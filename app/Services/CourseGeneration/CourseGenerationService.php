<?php

namespace App\Services\CourseGeneration;

use App\Models\Assessment\Assessment;
use App\Models\Assessment\Question;
use App\Models\Core\User;
use App\Models\Learning\Course;
use App\Models\Learning\CourseCategory;
use App\Models\Learning\Lesson;
use App\Models\Learning\Section;
use App\Services\CourseValidationService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Mews\Purifier\Facades\Purifier;

class CourseGenerationService
{
    public function __construct(
        protected CourseGenerationBlueprintBuilder $blueprintBuilder,
        protected CourseValidationService $validationService
    ) {
    }

    public function preview(array|CourseGenerationPayload $payload): array
    {
        $payload = $this->normalizePayload($payload);
        $this->validatePayload($payload);

        return $this->blueprintBuilder->build($payload);
    }

    public function generate(array|CourseGenerationPayload $payload): array
    {
        $payload = $this->normalizePayload($payload);
        $this->validatePayload($payload);

        $blueprint = $this->blueprintBuilder->build($payload);
        $warnings = $blueprint['warnings'] ?? [];
        $instructor = $this->resolveInstructor($payload, $warnings);
        [$category, $categoryCreated] = $this->resolveCategory($payload);
        $forceUniqueSlug = false;

        $duplicate = $this->findDuplicateCourse(
            title: $blueprint['course']['title'],
            slug: $blueprint['course']['slug'],
            instructorId: $instructor->id,
            categoryId: $category?->id
        );

        if ($duplicate) {
            if ($payload->get('duplicate_strategy') !== 'create_new') {
                return $this->handleDuplicate($payload, $duplicate, $warnings);
            }

            $forceUniqueSlug = true;
            $warnings[] = 'Matching course already exists. Proceeding with create_new, so a new record will be inserted with a unique slug.';
        }

        return DB::transaction(function () use ($payload, $blueprint, $warnings, $instructor, $category, $categoryCreated, $forceUniqueSlug) {
            $course = Course::create(
                $this->prepareCourseAttributes(
                    payload: $payload,
                    courseBlueprint: $blueprint['course'],
                    instructorId: $instructor->id,
                    categoryId: $category?->id,
                    forceUniqueSlug: $forceUniqueSlug
                )
            );
            $assessmentCount = 0;
            $projectCount = 0;

            foreach ($blueprint['sections'] as $sectionIndex => $sectionBlueprint) {
                $this->validationService->validateSection(
                    $sectionBlueprint['title'],
                    $sectionBlueprint['description'] ?? null
                );

                $section = Section::create([
                    'course_id' => $course->id,
                    'title' => $sectionBlueprint['title'],
                    'description' => $sectionBlueprint['description'] ?? null,
                    'order' => $sectionIndex + 1,
                    'type' => 'section',
                    'is_locked' => $sectionIndex > 0,
                ]);

                $lastLesson = null;

                foreach ($sectionBlueprint['lessons'] as $lessonIndex => $lessonBlueprint) {
                    $duration = max(1, (int) ($lessonBlueprint['duration_minutes'] ?? 1));
                    $contentType = $lessonBlueprint['content_type'] ?? 'text';

                    $this->validationService->validateLesson(
                        $lessonBlueprint['title'],
                        $lessonBlueprint['description'] ?? null,
                        $duration,
                        $contentType
                    );

                    $lastLesson = Lesson::create([
                        'section_id' => $section->id,
                        'title' => $lessonBlueprint['title'],
                        'description' => $lessonBlueprint['description'] ?? null,
                        'content' => $this->sanitizeLessonContent($lessonBlueprint['content'] ?? ''),
                        'text_content' => Str::limit(strip_tags($lessonBlueprint['content'] ?? ''), 65000, ''),
                        'content_type' => $contentType,
                        'video_url' => $lessonBlueprint['video_url'] ?? null,
                        'duration_minutes' => $duration,
                        'order' => $lessonIndex + 1,
                        'scheduled_publish_at' => $this->resolveLessonSchedule($payload),
                        'published_at' => $this->resolveLessonPublishDate($payload),
                        'completion_time_type' => $lessonBlueprint['completion_time_type'] ?? ($contentType === 'video' ? 'watching' : 'reading'),
                        'difficulty_level' => $lessonBlueprint['difficulty_level'] ?? $payload->get('skill_level'),
                        'external_links' => $lessonBlueprint['external_links'] ?? [],
                    ]);
                }

                if (!empty($sectionBlueprint['assessment']) && $lastLesson) {
                    $assessment = $this->createAssessmentFromBlueprint(
                        course: $course,
                        section: $section,
                        lesson: $lastLesson,
                        blueprint: $sectionBlueprint['assessment']
                    );

                    $assessmentCount++;
                    if ($assessment->type === 'project') {
                        $projectCount++;
                    }
                }
            }

            $this->refreshCourseMetrics($course, $blueprint['course'], $assessmentCount, $projectCount);

            return [
                'created' => true,
                'course' => $course->fresh(['category', 'instructor', 'sections.lessons']),
                'warnings' => $warnings,
                'category_created' => $categoryCreated,
            ];
        });
    }

    protected function normalizePayload(array|CourseGenerationPayload $payload): CourseGenerationPayload
    {
        return $payload instanceof CourseGenerationPayload
            ? $payload
            : CourseGenerationPayload::fromArray($payload);
    }

    protected function validatePayload(CourseGenerationPayload $payload): void
    {
        $validator = Validator::make($payload->all(), [
            'topic' => ['required', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'regex:/^[a-z0-9\-]+$/', 'max:255'],
            'target_audience' => ['nullable', 'string', 'max:500'],
            'skill_level' => ['required', 'in:beginner,intermediate,advanced,expert'],
            'course_goals' => ['nullable', 'array', 'max:8'],
            'course_goals.*' => ['nullable', 'string', 'max:255'],
            'section_count' => ['required_without:sections', 'integer', 'min:1', 'max:12'],
            'lessons_per_section' => ['required_without:sections', 'integer', 'min:1', 'max:12'],
            'video_lessons_per_section' => ['nullable', 'integer', 'min:0', 'max:4'],
            'include_faqs' => ['boolean'],
            'include_resources' => ['boolean'],
            'include_quizzes' => ['boolean'],
            'create_category_if_missing' => ['boolean'],
            'duplicate_strategy' => ['required', 'in:return_existing,fail,create_new'],
            'publish' => ['boolean'],
            'approve' => ['boolean'],
            'scheduled_publish_at' => ['nullable', 'date'],
            'price' => ['numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'is_premium' => ['boolean'],
            'completion_rate_threshold' => ['integer', 'between:1,100'],
            'estimated_duration_minutes' => ['nullable', 'integer', 'min:1'],
            'learning_outcomes' => ['nullable', 'array', 'max:10'],
            'learning_outcomes.*' => ['nullable', 'string', 'max:500'],
            'prerequisites' => ['nullable', 'array', 'max:10'],
            'prerequisites.*' => ['nullable', 'string', 'max:1000'],
            'materials_included' => ['nullable', 'array', 'max:10'],
            'materials_included.*' => ['nullable', 'string', 'max:255'],
            'tags' => ['nullable', 'array', 'max:10'],
            'tags.*' => ['nullable', 'string', 'max:100'],
            'syllabus_overview' => ['nullable', 'string', 'max:1500'],
            'faqs' => ['nullable', 'array', 'max:10'],
            'faqs.*.question' => ['required_with:faqs.*.answer', 'string', 'max:255'],
            'faqs.*.answer' => ['required_with:faqs.*.question', 'string', 'max:1000'],
            'video_references' => ['nullable', 'array'],
            'video_references.*.url' => ['required', 'url'],
            'resource_links' => ['nullable', 'array'],
            'resource_links.*.url' => ['required', 'url'],
            'resource_links.*.title' => ['nullable', 'string', 'max:255'],
            'sections' => ['nullable', 'array', 'max:12'],
            'sections.*.title' => ['nullable', 'string', 'max:255'],
            'sections.*.description' => ['nullable', 'string', 'max:1000'],
            'sections.*.lessons' => ['nullable', 'array', 'max:12'],
            'sections.*.lessons.*.title' => ['nullable', 'string', 'max:255'],
            'sections.*.lessons.*.description' => ['nullable', 'string', 'max:1000'],
            'sections.*.lessons.*.content' => ['nullable', 'string'],
            'sections.*.lessons.*.content_type' => ['nullable', 'in:text,video,file'],
            'sections.*.lessons.*.video_url' => ['nullable', 'url'],
            'sections.*.lessons.*.duration_minutes' => ['nullable', 'integer', 'min:1', 'max:600'],
            'sections.*.lessons.*.completion_time_type' => ['nullable', 'in:reading,watching,practice,total'],
            'sections.*.lessons.*.difficulty_level' => ['nullable', 'in:beginner,intermediate,advanced,expert'],
        ]);

        $validator->after(function ($validator) use ($payload) {
            $category = $payload->get('category');

            if (!$category) {
                $validator->errors()->add('category', 'A category reference is required.');
            }

            foreach ($payload->get('video_references', []) as $index => $reference) {
                if (!$this->isYouTubeUrl($reference['url'] ?? null)) {
                    $validator->errors()->add("video_references.{$index}.url", 'Video references must be verified YouTube URLs.');
                }
            }

            foreach ($payload->get('sections', []) as $sectionIndex => $section) {
                if (empty($section['title'])) {
                    $validator->errors()->add("sections.{$sectionIndex}.title", 'Each provided section needs a title.');
                }

                if (empty($section['lessons'])) {
                    $validator->errors()->add("sections.{$sectionIndex}.lessons", 'Each provided section needs at least one lesson.');
                    continue;
                }

                foreach ($section['lessons'] as $lessonIndex => $lesson) {
                    if (empty($lesson['title'])) {
                        $validator->errors()->add("sections.{$sectionIndex}.lessons.{$lessonIndex}.title", 'Each provided lesson needs a title.');
                    }

                    if (!empty($lesson['video_url']) && !$this->isYouTubeUrl($lesson['video_url'])) {
                        $validator->errors()->add("sections.{$sectionIndex}.lessons.{$lessonIndex}.video_url", 'Lesson video URLs must be verified YouTube URLs.');
                    }
                }
            }
        });

        $validator->validate();
    }

    protected function resolveInstructor(CourseGenerationPayload $payload, array &$warnings): User
    {
        $reference = $payload->get('instructor');

        if ($reference['id'] ?? null) {
            return User::query()->findOrFail($reference['id']);
        }

        if ($reference['email'] ?? null) {
            return User::query()->where('email', $reference['email'])->firstOrFail();
        }

        if ($reference['name'] ?? null) {
            $user = User::query()->where('name', $reference['name'])->first();

            if ($user) {
                return $user;
            }
        }

        $fallback = User::query()
            ->whereIn('role', [User::ROLE_INSTRUCTOR, User::ROLE_ACADEMY_ADMIN, User::ROLE_SUPER_ADMIN])
            ->orderBy('id')
            ->first() ?? User::query()->orderBy('id')->first();

        if (!$fallback) {
            throw ValidationException::withMessages([
                'instructor' => 'No instructor was supplied and no fallback user exists in the database.',
            ]);
        }

        $warnings[] = "No instructor was supplied. Assigned the course to {$fallback->email}.";

        return $fallback;
    }

    protected function resolveCategory(CourseGenerationPayload $payload): array
    {
        $reference = $payload->get('category');

        if (!$reference) {
            throw ValidationException::withMessages([
                'category' => 'A category reference is required.',
            ]);
        }

        if ($reference['id'] ?? null) {
            return [CourseCategory::query()->findOrFail($reference['id']), false];
        }

        $query = CourseCategory::query();

        if ($reference['slug'] ?? null) {
            $category = (clone $query)->where('slug', Str::slug($reference['slug']))->first();
            if ($category) {
                return [$category, false];
            }
        }

        if ($reference['name'] ?? null) {
            $name = $reference['name'];
            $category = (clone $query)
                ->whereRaw('LOWER(name) = ?', [Str::lower($name)])
                ->orWhere('slug', Str::slug($name))
                ->first();

            if ($category) {
                return [$category, false];
            }

            if ($payload->get('create_category_if_missing')) {
                return [CourseCategory::create([
                    'name' => $name,
                    'description' => $reference['description'] ?? "Course category for {$name}.",
                ]), true];
            }
        }

        throw ValidationException::withMessages([
            'category' => 'The requested course category could not be resolved.',
        ]);
    }

    protected function findDuplicateCourse(string $title, ?string $slug, int $instructorId, ?int $categoryId): ?Course
    {
        $slug = $slug ?: Str::slug($title);

        return Course::query()
            ->where(function ($query) use ($title, $slug, $instructorId, $categoryId) {
                $query->where('slug', $slug)
                    ->orWhere(function ($subQuery) use ($title, $instructorId, $categoryId) {
                        $subQuery->where('title', $title)
                            ->where('instructor_id', $instructorId);

                        if ($categoryId) {
                            $subQuery->where('category_id', $categoryId);
                        }
                    });
            })
            ->first();
    }

    protected function handleDuplicate(CourseGenerationPayload $payload, Course $duplicate, array $warnings): array
    {
        return match ($payload->get('duplicate_strategy')) {
            'return_existing' => [
                'created' => false,
                'course' => $duplicate->fresh(['category', 'instructor', 'sections.lessons']),
                'warnings' => array_merge($warnings, ['Matching course already exists. Returned the existing record without changes.']),
                'category_created' => false,
            ],
            'fail' => throw ValidationException::withMessages([
                'duplicate' => 'A matching course already exists. Use duplicate_strategy=create_new if you want another copy.',
            ]),
            default => throw ValidationException::withMessages([
                'duplicate_strategy' => 'Unsupported duplicate strategy.',
            ]),
        };
    }

    protected function prepareCourseAttributes(
        CourseGenerationPayload $payload,
        array $courseBlueprint,
        int $instructorId,
        ?int $categoryId,
        bool $forceUniqueSlug = false
    ): array {
        $price = (float) $payload->get('price', 0);
        $isFree = $price <= 0;
        $scheduledPublishAt = $this->resolveCourseSchedule($payload);
        $publishNow = (bool) $payload->get('publish') && !$scheduledPublishAt;

        return [
            'instructor_id' => $instructorId,
            'category_id' => $categoryId,
            'title' => $courseBlueprint['title'],
            'subtitle' => $courseBlueprint['subtitle'] ?? null,
            'slug' => $forceUniqueSlug ? null : ($courseBlueprint['slug'] ?? null),
            'description' => $courseBlueprint['description'],
            'difficulty_level' => $payload->get('skill_level'),
            'estimated_duration_minutes' => $courseBlueprint['estimated_duration_minutes'] ?? null,
            'price' => $price,
            'is_paid' => !$isFree,
            'currency' => $payload->get('currency'),
            'is_premium' => !$isFree && (bool) $payload->get('is_premium'),
            'is_free' => $isFree,
            'has_offline_content' => (bool) ($courseBlueprint['has_offline_content'] ?? false),
            'is_published' => $publishNow,
            'is_approved' => (bool) $payload->get('approve'),
            'scheduled_publish_at' => $scheduledPublishAt,
            'published_at' => $publishNow ? now() : null,
            'target_audience' => $courseBlueprint['target_audience'] ?? null,
            'learning_outcomes' => $courseBlueprint['learning_outcomes'] ?? [],
            'prerequisites' => $courseBlueprint['prerequisites'] ?? [],
            'syllabus_overview' => $courseBlueprint['syllabus_overview'] ?? null,
            'faqs' => $courseBlueprint['faqs'] ?? [],
            'completion_rate_threshold' => $payload->get('completion_rate_threshold'),
            'videos' => $courseBlueprint['videos'] ?? [],
            'external_links' => $courseBlueprint['external_links'] ?? [],
            'materials_included' => $courseBlueprint['materials_included'] ?? [],
            'tags' => $courseBlueprint['tags'] ?? [],
        ];
    }

    protected function resolveCourseSchedule(CourseGenerationPayload $payload): ?Carbon
    {
        $scheduled = $payload->get('scheduled_publish_at');

        if (!$scheduled) {
            return null;
        }

        return Carbon::parse($scheduled);
    }

    protected function resolveLessonSchedule(CourseGenerationPayload $payload): ?Carbon
    {
        return $payload->get('publish') ? null : $this->resolveCourseSchedule($payload);
    }

    protected function resolveLessonPublishDate(CourseGenerationPayload $payload): ?Carbon
    {
        return $payload->get('publish') ? now() : null;
    }

    protected function createAssessmentFromBlueprint(Course $course, Section $section, Lesson $lesson, array $blueprint): Assessment
    {
        $title = $blueprint['title'] ?? ($section->title . ' Knowledge Check');
        $description = $blueprint['description'] ?? 'Short quiz for the section.';
        $type = $blueprint['type'] ?? 'quiz';
        $duration = max(1, (int) ($blueprint['estimated_duration_minutes'] ?? 10));

        $this->validationService->validateAssessment(
            $title,
            $description,
            $type,
            $duration,
            null
        );

        $assessment = Assessment::create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'lesson_id' => $lesson->id,
            'title' => $title,
            'description' => $description,
            'type' => $type,
            'pass_percentage' => (int) ($blueprint['pass_percentage'] ?? 70),
            'estimated_duration_minutes' => $duration,
            'is_mandatory' => true,
            'weight' => 1,
            'max_score' => 0,
            'max_attempts' => null,
        ]);

        $totalPoints = 0;

        foreach (($blueprint['questions'] ?? []) as $index => $questionBlueprint) {
            $points = (float) ($questionBlueprint['points'] ?? 5);
            $totalPoints += $points;

            Question::create([
                'assessment_id' => $assessment->id,
                'question_text' => $questionBlueprint['question_text'],
                'question_type' => $questionBlueprint['question_type'] ?? 'multiple_choice',
                'options' => $questionBlueprint['options'] ?? [],
                'correct_answers' => $questionBlueprint['correct_answers'] ?? [0],
                'points' => $points,
                'explanation' => $questionBlueprint['explanation'] ?? null,
                'is_required' => true,
                'order' => $index + 1,
                'difficulty_level' => $questionBlueprint['difficulty_level'] ?? 'medium',
                'tags' => $questionBlueprint['tags'] ?? [],
            ]);
        }

        if ($totalPoints > 0) {
            $assessment->updateQuietly([
                'max_score' => $totalPoints,
            ]);
        }

        return $assessment;
    }

    protected function refreshCourseMetrics(Course $course, array $courseBlueprint, int $assessmentCount, int $projectCount): void
    {
        $sectionCount = $course->sections()->count();
        $lessonCount = $course->allLessons()->count();

        $course->updateQuietly([
            'total_modules' => $sectionCount,
            'total_lessons' => $lessonCount,
            'total_assessments' => $assessmentCount,
            'total_projects' => $projectCount,
            'has_assessments' => $assessmentCount > 0,
            'has_projects' => $projectCount > 0,
            'estimated_duration_minutes' => $courseBlueprint['estimated_duration_minutes'] ?? $course->allLessons()->sum('duration_minutes'),
        ]);
    }

    protected function sanitizeLessonContent(string $content): string
    {
        return Purifier::clean($content, [
            'HTML.Allowed' => 'h1,h2,h3,h4,h5,h6,p,br,strong,em,u,s,ul,ol,li,a[href|target],blockquote,pre,code,div,span',
            'AutoFormat.RemoveEmpty' => false,
        ]);
    }

    protected function isYouTubeUrl(?string $url): bool
    {
        if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }

        return (bool) preg_match('/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be)\//i', $url);
    }
}
