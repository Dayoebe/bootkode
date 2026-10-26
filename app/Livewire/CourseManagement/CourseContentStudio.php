<?php

namespace App\Livewire\CourseManagement;

use App\Jobs\GenerateCourseWithAi;
use App\Jobs\ImportCourseContent;
use App\Livewire\Concerns\AuthorizesCourseAuthoring;
use App\Models\Learning\CourseCategory;
use App\Models\Learning\CourseContentRun;
use App\Services\Ai\AiManager;
use App\Services\CourseAuthoringAccess;
use App\Services\CourseGeneration\AiCourseGenerationService;
use App\Services\CourseImport\CourseImportService;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use RuntimeException;

#[Layout('layouts.dashboard', ['title' => 'Course Content Studio', 'description' => 'Import licensed learning material or generate original course drafts', 'active' => 'course-content-studio'])]
class CourseContentStudio extends Component
{
    use AuthorizesCourseAuthoring;

    #[Locked]
    public array $categories = [];

    #[Locked]
    public string $notice = '';

    public string $importPath = 'curriculum/challenges/english/blocks/algorithms';

    public string $importCategoryId = '';

    public int $importLimit = 12;

    public bool $createSourceRevision = false;

    #[Locked]
    public array $importPreview = [];

    #[Locked]
    public string $previewedImportPath = '';

    #[Locked]
    public int $previewedImportLimit = 0;

    public string $generationTopic = '';

    public string $generationCategoryId = '';

    public string $targetAudience = '';

    public string $skillLevel = 'beginner';

    public array $learningObjectives = ['', ''];

    public int $durationMinutes = 180;

    public int $moduleCount = 3;

    public int $lessonsPerModule = 3;

    public string $teachingStyle = 'Clear explanations with practical examples';

    public string $practicalRequirements = '';

    public string $assessmentRequirements = '';

    public bool $includeAssessments = true;

    public string $language = 'English';

    public string $additionalInstructions = '';

    public function mount(): void
    {
        $this->categories = CourseCategory::query()->orderBy('name')->get(['id', 'name'])->toArray();
    }

    public function addObjective(): void
    {
        $this->authorizeRole();
        if (count($this->learningObjectives) < 8) {
            $this->learningObjectives[] = '';
        }
    }

    public function removeObjective(int $index): void
    {
        $this->authorizeRole();
        if (count($this->learningObjectives) > 2 && array_key_exists($index, $this->learningObjectives)) {
            unset($this->learningObjectives[$index]);
            $this->learningObjectives = array_values($this->learningObjectives);
        }
    }

    public function previewImport(CourseImportService $imports): void
    {
        $this->authorizeRole();
        if (! $this->allowRequest()) {
            return;
        }
        $this->validate([
            'importPath' => ['required', 'string', 'max:180', 'regex:/^curriculum\/challenges\/english\/blocks\/[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'importLimit' => ['required', 'integer', 'between:1,12'],
        ]);

        try {
            $document = $imports->preview('freecodecamp', $this->importPath, $this->importLimit);
            $this->importPreview = collect($document)
                ->except(['lessons', 'license_text'])
                ->put('lessons', collect($document['lessons'])->map(fn ($lesson) => [
                    'title' => $lesson['title'],
                    'source_url' => $lesson['source_url'],
                    'preview' => \Illuminate\Support\Str::limit(strip_tags($lesson['content']), 240),
                ])->all())
                ->all();
            $this->previewedImportPath = $this->importPath;
            $this->previewedImportLimit = $this->importLimit;
        } catch (RuntimeException $exception) {
            $this->importPreview = [];
            $this->previewedImportPath = '';
            $this->addError('source', $exception->getMessage());
        }
    }

    public function startImport(CourseImportService $imports): void
    {
        $this->authorizeRole();
        if ($this->previewedImportPath !== $this->importPath || $this->previewedImportLimit !== $this->importLimit || $this->importPreview === []) {
            $this->addError('import', 'Preview the current source path and licence before starting the import.');

            return;
        }

        $this->validate([
            'importPath' => ['required', 'string', 'max:180', 'regex:/^curriculum\/challenges\/english\/blocks\/[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'importCategoryId' => ['required', 'integer', 'exists:course_categories,id'],
            'importLimit' => ['required', 'integer', 'between:1,12'],
            'createSourceRevision' => ['boolean'],
        ]);

        if (! $this->allowRequest()) {
            return;
        }
        $run = $imports->queue(auth()->user(), 'freecodecamp', $this->importPath, (int) $this->importCategoryId, $this->importLimit, $this->createSourceRevision, $this->importPreview['revision']);
        ImportCourseContent::dispatch($run->id)->afterCommit();
        $this->notice = 'Licensed-source import queued. The result will remain a draft for editorial review.';
    }

    public function startGeneration(AiCourseGenerationService $generation, AiManager $manager): void
    {
        $this->authorizeRole();
        if (! $manager->status()['ready']) {
            $this->addError('generation', 'AI course generation is unavailable. Ask an administrator to configure the enabled provider, model and server-side key.');

            return;
        }

        $input = [
            'topic' => $this->generationTopic,
            'category_id' => $this->generationCategoryId,
            'target_audience' => $this->targetAudience,
            'skill_level' => $this->skillLevel,
            'learning_objectives' => $this->learningObjectives,
            'duration_minutes' => $this->durationMinutes,
            'module_count' => $this->moduleCount,
            'lessons_per_module' => $this->lessonsPerModule,
            'teaching_style' => $this->teachingStyle,
            'practical_requirements' => $this->practicalRequirements,
            'assessment_requirements' => $this->assessmentRequirements,
            'include_assessments' => $this->includeAssessments,
            'language' => $this->language,
            'additional_instructions' => $this->additionalInstructions,
        ];

        $this->validate([
            'generationTopic' => ['required', 'string', 'min:3', 'max:160'],
            'generationCategoryId' => ['required', 'integer', 'exists:course_categories,id'],
            'targetAudience' => ['required', 'string', 'max:500'],
            'skillLevel' => ['required', 'in:beginner,intermediate,advanced'],
            'learningObjectives' => ['required', 'array', 'min:2', 'max:8'],
            'learningObjectives.*' => ['required', 'string', 'max:255'],
            'durationMinutes' => ['required', 'integer', 'min:30', 'max:2400'],
            'moduleCount' => ['required', 'integer', 'between:2,5'],
            'lessonsPerModule' => ['required', 'integer', 'between:2,4'],
            'teachingStyle' => ['required', 'string', 'max:160'],
            'practicalRequirements' => ['nullable', 'string', 'max:1000'],
            'assessmentRequirements' => ['nullable', 'string', 'max:500'],
            'includeAssessments' => ['boolean'],
            'language' => ['required', 'string', 'max:80'],
            'additionalInstructions' => ['nullable', 'string', 'max:1000'],
        ]);

        if (! $this->allowRequest()) {
            return;
        }
        $run = $generation->queue(auth()->user(), $input);
        GenerateCourseWithAi::dispatch($run->id)->afterCommit();
        $this->notice = 'AI course generation queued. Completed stages are retained for safe retry; the resulting course remains a draft.';
    }

    public function retryRun(int $runId): void
    {
        $this->authorizeRole();
        $run = CourseContentRun::query()->findOrFail($runId);
        abort_unless(
            (int) $run->user_id === (int) auth()->id() || auth()->user()->hasAnyRole(['super_admin', 'academy_admin']),
            403
        );
        abort_unless($run->status === 'failed', 422);

        if (! $this->allowRequest()) {
            return;
        }
        if ($run->source_type === 'repository') {
            app(CourseImportService::class)->retry($run, auth()->user());
        } else {
            abort_unless($run->source_type === 'ai', 422);
            $run->forceFill(['status' => 'queued', 'failure_reason' => null])->save();
        }
        if ($run->source_type === 'ai') {
            GenerateCourseWithAi::dispatch($run->id)->afterCommit();
        } else {
            ImportCourseContent::dispatch($run->id)->afterCommit();
        }

        $this->notice = 'Retry queued. Existing successful AI stages will be reused.';
    }

    public function render()
    {
        $user = auth()->user();
        $runs = CourseContentRun::query()
            ->with(['course:id,title,slug,instructor_id'])
            ->when(! $user->hasAnyRole(['super_admin', 'academy_admin']), fn ($query) => $query->where('user_id', $user->id))
            ->latest('id')
            ->limit(20)
            ->get();

        return view('livewire.course-management.course-content-studio', [
            'runs' => $runs,
            'aiStatus' => app(AiManager::class)->status(),
        ]);
    }

    private function allowRequest(): bool
    {
        $key = 'course-content-requests:'.auth()->id();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            $this->addError('request', 'Too many course-content requests. Wait a minute before trying again.');

            return false;
        }
        RateLimiter::hit($key, 60);

        return true;
    }

    private function authorizeRole(): void
    {
        app(CourseAuthoringAccess::class)->authorizeRole();
    }
}
