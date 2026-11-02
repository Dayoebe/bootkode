<?php

namespace App\Console\Commands;

use App\Exceptions\AiProviderException;
use App\Models\Core\User;
use App\Services\CourseGeneration\AiCourseGenerationService;
use Illuminate\Console\Command;
use Illuminate\Validation\ValidationException;
use Throwable;

class GenerateCourseWithAiCommand extends Command
{
    protected $signature = 'bootkode:courses-generate
        {--topic= : Course topic or working title}
        {--category-id= : Existing BootKode course category ID}
        {--instructor-id= : Authorised course editor user ID}
        {--level=beginner : beginner, intermediate, or advanced}
        {--audience= : Target learner audience}
        {--objectives= : At least two objectives separated by semicolons}
        {--modules=3 : Number of modules (2-5)}
        {--lessons-per-module=3 : Number of lessons per module (2-4)}
        {--duration=180 : Expected course duration in minutes}
        {--teaching-style=Clear explanations with practical examples : Preferred teaching style}
        {--practical-requirements= : Capstone instructions to guide project generation}
        {--assessment-requirements= : Assessment guidance}
        {--instructions= : Additional course requirements}
        {--language=English : Course language}
        {--request-id= : Stable idempotency key; use a new value to intentionally create another draft}
        {--with-assessments : Generate module quizzes}';

    protected $description = 'Generate a structured AI course and save it as an editable draft';

    public function handle(AiCourseGenerationService $generation): int
    {
        $editor = User::query()->find($this->option('instructor-id'));
        $objectives = array_values(array_filter(array_map('trim', explode(';', (string) $this->option('objectives')))));
        if (
            ! $editor
            || ! $editor->hasAnyRole(['super_admin', 'academy_admin', 'instructor'])
            || ! $this->option('topic')
            || ! $this->option('category-id')
            || ! $this->option('audience')
            || count($objectives) < 2
        ) {
            $this->error('Provide --topic, --category-id, an authorised --instructor-id, --audience and at least two semicolon-separated --objectives.');

            return self::FAILURE;
        }

        try {
            $input = [
                'topic' => (string) $this->option('topic'),
                'category_id' => (int) $this->option('category-id'),
                'target_audience' => (string) $this->option('audience'),
                'skill_level' => (string) $this->option('level'),
                'learning_objectives' => $objectives,
                'duration_minutes' => (int) $this->option('duration'),
                'module_count' => (int) $this->option('modules'),
                'lessons_per_module' => (int) $this->option('lessons-per-module'),
                'teaching_style' => (string) $this->option('teaching-style'),
                'practical_requirements' => (string) $this->option('practical-requirements'),
                'assessment_requirements' => (string) $this->option('assessment-requirements'),
                'include_assessments' => (bool) $this->option('with-assessments'),
                'language' => (string) $this->option('language'),
                'additional_instructions' => (string) $this->option('instructions'),
            ];
            $requestId = $this->option('request-id') ?: null;
            $result = $generation->generate($editor, $input, $requestId);

            $this->info('Generation status: '.$result['run']->status);
            if ($result['run']->course) {
                $this->line("Draft course: {$result['run']->course->title} (ID {$result['run']->course_id})");
                $this->line('Modules: '.$result['summary']['module_count'].'; lessons: '.$result['summary']['lesson_count'].'; assessments: '.$result['summary']['assessment_count']);
                $this->line('Human subject-matter review is required; the course is not published.');
            }

            return $result['run']->status === 'completed' ? self::SUCCESS : self::FAILURE;
        } catch (Throwable $exception) {
            if ($exception instanceof AiProviderException || $exception instanceof ValidationException) {
                $this->error($exception->getMessage());
            } else {
                \Illuminate\Support\Facades\Log::warning('Course generation command failed.', ['exception_type' => $exception::class]);
                $this->error('AI course generation could not be completed. Review configuration and application logs.');
            }

            return self::FAILURE;
        }
    }
}
