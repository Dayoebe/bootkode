<?php

namespace App\Services\CourseImport;

use App\Exceptions\CourseContentException;
use App\Models\Core\User;
use App\Models\Learning\CourseContentRun;
use App\Services\CourseGeneration\CourseGenerationService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class CourseImportService
{
    public function __construct(
        private readonly CourseContentSourceRegistry $sources,
        private readonly CourseGenerationService $courses,
    ) {}

    public function sourceList(): array
    {
        return collect($this->sources->all())
            ->map(fn ($source) => ['key' => $source->key(), 'name' => $source->name()])
            ->values()
            ->all();
    }

    public function preview(string $sourceKey, string $path, int $limit = 12): array
    {
        return $this->sources->get($sourceKey)->inspect($path, $limit);
    }

    public function queue(User $user, string $sourceKey, string $path, int $categoryId, int $limit = 12, bool $newRevision = false, ?string $expectedRevision = null): CourseContentRun
    {
        abort_unless($user->hasAnyRole(['super_admin', 'academy_admin', 'instructor']), 403);
        Validator::make(['category_id' => $categoryId], ['category_id' => ['required', 'integer', 'exists:course_categories,id']])->validate();
        $source = $this->sources->get($sourceKey);
        $path = $source->validatePath($path);
        $limit = min(max($limit, 1), 12);
        // Preserve the identity of already-recorded imports when upgrading this pipeline.
        $previous = CourseContentRun::query()->where('user_id', $user->id)->where('source_type', 'repository')
            ->where(fn ($query) => $query->whereNull('course_id')->orWhereHas('course', fn ($course) => $course->where('instructor_id', $user->id)))
            ->where('metadata->source', $sourceKey)->where('metadata->path', $path)->where('metadata->limit', $limit)->oldest('id')->first();
        $sourceKeyHash = $previous?->source_key ?? hash('sha256', $source->key().':'.$path.':'.$user->id.':'.$limit);

        return DB::transaction(function () use ($user, $sourceKeyHash, $sourceKey, $path, $categoryId, $limit, $newRevision, $expectedRevision) {
            $run = CourseContentRun::create([
                'source_key' => $sourceKeyHash,
                'revision' => null,
                'source_type' => 'repository',
                'user_id' => $user->id,
                'status' => 'queued',
                'metadata' => [
                    'source' => $sourceKey,
                    'path' => $path,
                    'category_id' => $categoryId,
                    'limit' => min(max($limit, 1), 12),
                    'new_revision' => $newRevision,
                    'expected_revision' => $expectedRevision,
                    'requested_at' => now()->toIso8601String(),
                ],
            ]);

            return $run;
        });
    }

    public function import(User $user, string $sourceKey, string $path, int $categoryId, int $limit = 12, bool $newRevision = false, ?string $expectedRevision = null): array
    {
        $run = $this->queue($user, $sourceKey, $path, $categoryId, $limit, $newRevision, $expectedRevision);

        return $this->process($run->id);
    }

    public function process(int $runId): array
    {
        $run = CourseContentRun::query()->where('source_type', 'repository')->findOrFail($runId);
        $lock = Cache::lock('course-import:'.$run->source_key, 900);
        if (! $lock->get()) {
            throw new CourseContentException('This source import is already processing. Retry after it finishes.');
        }
        try {
            return $this->processLocked($runId);
        } finally {
            $lock->release();
        }
    }

    private function processLocked(int $runId): array
    {
        $run = CourseContentRun::query()->findOrFail($runId);
        if (in_array($run->status, ['completed', 'partial', 'skipped'], true) && $run->course_id) {
            return ['run' => $run->load('course'), 'created' => false, 'warnings' => []];
        }
        $metadata = $run->metadata ?? [];
        $run->forceFill(['status' => 'processing', 'failure_reason' => null])->save();

        try {
            $editor = User::query()->find($run->user_id);
            if (! $editor || ! $editor->hasAnyRole(['super_admin', 'academy_admin', 'instructor'])) {
                throw new CourseContentException('The original course editor is no longer authorised to import content.');
            }

            $source = $this->sources->get((string) ($metadata['source'] ?? ''));
            $document = $source->inspect((string) ($metadata['path'] ?? ''), (int) ($metadata['limit'] ?? 12), $metadata['expected_revision'] ?? null);
            $revision = (string) $document['revision'];
            $existingRevision = CourseContentRun::query()
                ->where('source_key', $run->source_key)
                ->where('revision', $revision)
                ->whereIn('status', ['completed', 'partial'])
                ->whereNotNull('course_id')
                ->first();

            if ($existingRevision) {
                $run->forceFill([
                    'status' => 'skipped',
                    'revision' => null,
                    'course_id' => $existingRevision->course_id,
                    'metadata' => array_merge($metadata, $document, ['duplicate_of_run_id' => $existingRevision->id]),
                ])->save();

                return ['run' => $run->fresh(['course']), 'created' => false, 'warnings' => ['This source revision is already imported. No duplicate course was created.']];
            }

            $latest = CourseContentRun::query()
                ->where('source_key', $run->source_key)
                ->whereIn('status', ['completed', 'partial'])
                ->whereNotNull('course_id')
                ->latest('id')
                ->first();

            if ($latest && $latest->revision !== $revision && ! ($metadata['new_revision'] ?? false)) {
                $run->forceFill([
                    'status' => 'conflict',
                    'metadata' => array_merge($metadata, $document, ['latest_course_id' => $latest->course_id]),
                    'failure_reason' => 'The source changed since the last import. Review the existing draft and explicitly request a new draft revision to import this version.',
                ])->save();

                return ['run' => $run->fresh(['course']), 'created' => false, 'warnings' => [$run->failure_reason]];
            }

            $categoryId = (int) ($metadata['category_id'] ?? 0);
            if ($categoryId < 1) {
                throw new CourseContentException('Select an existing course category before importing.');
            }

            $importedLessons = [];
            foreach ($document['lessons'] as $index => $lesson) {
                $content = \Illuminate\Support\Str::markdown($lesson['content'])
                    ."\n\n<p><strong>Original source:</strong> <a href=\"".e($lesson['source_url']).'">'.e($lesson['source_url']).'</a></p>';
                // Individual lesson/offline exports also need the complete redistribution notice.
                $content .= "\n<hr><h3>Copyright and licence notice</h3><pre>".e($document['license_text']).'</pre>';

                $importedLessons[] = [
                    'title' => $lesson['title'],
                    'description' => 'Imported challenge description. Review source context and expand with BootKode teaching activities before approval.',
                    'content' => $content,
                    'content_type' => 'text',
                    'duration_minutes' => $lesson['duration_minutes'],
                    'external_links' => [['title' => 'Original freeCodeCamp challenge', 'url' => $lesson['source_url']]],
                ];
            }

            $sections = [[
                'title' => 'Imported curriculum challenges',
                'description' => 'Selected freeCodeCamp challenge descriptions. Interactive tests, seed code, solutions, images and external media are not included.',
                'lessons' => $importedLessons,
            ]];

            $result = DB::transaction(function () use ($run, $document, $revision, $categoryId, $sections) {
                $existing = CourseContentRun::query()
                    ->where('source_key', $run->source_key)
                    ->where('revision', $revision)
                    ->whereIn('status', ['completed', 'partial'])
                    ->whereNotNull('course_id')
                    ->lockForUpdate()
                    ->first();

                if ($existing) {
                    return ['course' => $existing->course, 'created' => false, 'existing_run' => $existing];
                }

                $courseResult = $this->courses->generate([
                    'topic' => $document['original_title'],
                    'title' => $document['original_title'].' — freeCodeCamp',
                    'description' => 'Draft import of selected freeCodeCamp curriculum challenge descriptions. This is not a complete course: tests, starter code, solutions and external media are intentionally excluded. An editor must review and add learning activities and assessments before approval.',
                    'target_audience' => 'Learners studying '.$document['original_title'],
                    'skill_level' => 'beginner',
                    'category_id' => $categoryId,
                    'instructor_id' => $run->user_id,
                    'section_count' => 1,
                    'lessons_per_section' => count($sections[0]['lessons']),
                    'sections' => $sections,
                    'learning_outcomes' => [],
                    'course_goals' => [],
                    'include_quizzes' => false,
                    'include_faqs' => false,
                    'include_resources' => false,
                    'create_category_if_missing' => false,
                    'price' => 0,
                    'currency' => 'NGN',
                    'publish' => false,
                    'approve' => false,
                    'duplicate_strategy' => 'create_new',
                ]);

                $importMetadata = array_merge($document, [
                    'importer_version' => '2',
                    'imported_at' => now()->toIso8601String(),
                    'module_count' => count($sections),
                    'lesson_count' => count($document['lessons']),
                    'manual_review' => true,
                    'review_flags' => [
                        'This source subset includes challenge descriptions only.',
                        'Interactive tests, seed code, solutions, images and external media are excluded.',
                        'Create BootKode-specific learning outcomes, activities and assessments before approval.',
                    ],
                    'assessment_count' => 0,
                ]);

                $run->forceFill([
                    'revision' => $revision,
                    'course_id' => $courseResult['course']->id,
                    'status' => ($document['skipped_files'] ?? 0) > 0 ? 'partial' : 'completed',
                    'metadata' => array_merge($run->metadata ?? [], $importMetadata),
                    'failure_reason' => null,
                ])->save();

                return ['course' => $courseResult['course'], 'created' => true, 'existing_run' => null];
            });

            if (! $result['created'] && $result['existing_run']) {
                $run->forceFill([
                    'status' => 'skipped',
                    'revision' => null,
                    'course_id' => $result['course']?->id,
                    'metadata' => array_merge($metadata, $document, ['duplicate_of_run_id' => $result['existing_run']->id]),
                ])->save();

                return ['run' => $run->fresh(['course']), 'created' => false, 'warnings' => ['This source revision is already imported. No duplicate course was created.']];
            }

            return [
                'run' => $run->fresh(['course']),
                'created' => $result['created'],
                'warnings' => ($document['skipped_files'] ?? 0) > 0
                    ? ["{$document['skipped_files']} source file(s) were not supported and were skipped."]
                    : [],
            ];
        } catch (QueryException $exception) {
            $existingRevision = isset($revision)
                ? CourseContentRun::query()
                    ->where('source_key', $run->source_key)
                    ->where('revision', $revision)
                    ->whereIn('status', ['completed', 'partial'])
                    ->whereNotNull('course_id')
                    ->first()
                : null;

            if ($existingRevision) {
                $run->refresh()->forceFill([
                    'status' => 'skipped',
                    'revision' => null,
                    'course_id' => $existingRevision->course_id,
                    'metadata' => array_merge($run->metadata ?? [], $document ?? [], ['duplicate_of_run_id' => $existingRevision->id]),
                    'failure_reason' => null,
                ])->save();

                return ['run' => $run->fresh(['course']), 'created' => false, 'warnings' => ['This source revision is already imported. No duplicate course was created.']];
            }

            $run->refresh()->forceFill([
                'status' => 'failed',
                'failure_reason' => 'The import could not be completed. Retry the import or contact support.',
            ])->save();
            \Illuminate\Support\Facades\Log::warning('Course import failed.', ['run_id' => $run->id, 'exception_type' => $exception::class]);
            throw new CourseContentException('The import could not be completed. Retry the import or contact support.');
        } catch (Throwable $exception) {
            $run->refresh()->forceFill([
                'status' => 'failed',
                'failure_reason' => $exception instanceof CourseContentException
                    ? $exception->getMessage()
                    : 'The import could not be completed. Retry the import or contact support.',
            ])->save();

            if ($exception instanceof CourseContentException) {
                throw $exception;
            }
            throw new CourseContentException('The import could not be completed. Retry the import or contact support.');
        }
    }

    public function retry(CourseContentRun $run, User $user): CourseContentRun
    {
        abort_unless($user->hasAnyRole(['super_admin', 'academy_admin', 'instructor']), 403);
        abort_unless($run->source_type === 'repository' && in_array($run->status, ['failed', 'conflict'], true), 422);
        abort_unless((int) $run->user_id === (int) $user->id || $user->hasAnyRole(['super_admin', 'academy_admin']), 403);

        $run->forceFill(['status' => 'queued', 'failure_reason' => null])->save();

        return $run;
    }
}
