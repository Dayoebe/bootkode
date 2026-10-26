<?php

namespace App\Console\Commands;

use App\Exceptions\AiProviderException;
use App\Models\Core\User;
use App\Services\CourseGeneration\AiCourseExpansionService;
use Illuminate\Console\Command;
use Illuminate\Validation\ValidationException;
use Throwable;

class ExpandImportedCourseCommand extends Command
{
    protected $signature = 'bootkode:courses-expand
        {--course-ids= : Comma-separated IDs of imported freeCodeCamp draft courses}
        {--editor-id= : Authorised editor user ID}
        {--restore-attribution-only : Append the verified full notice to legacy draft lessons without AI requests}';

    protected $description = 'Expand imported freeCodeCamp course drafts with detailed lessons and section quizzes';

    public function handle(AiCourseExpansionService $expansion): int
    {
        $courseIds = collect(explode(',', (string) $this->option('course-ids')))
            ->map(fn ($id) => filter_var(trim($id), FILTER_VALIDATE_INT))
            ->filter(fn ($id) => $id !== false && $id > 0)
            ->unique()
            ->values();
        $editor = User::query()->find($this->option('editor-id'));

        if ($courseIds->isEmpty() || ! $editor || ! $editor->hasAnyRole(['super_admin', 'academy_admin', 'instructor'])) {
            $this->error('Provide --course-ids and an authorised --editor-id.');

            return self::FAILURE;
        }

        try {
            foreach ($courseIds as $courseId) {
                if ($this->option('restore-attribution-only')) {
                    $count = $expansion->restoreAttribution($editor, (int) $courseId);
                    $this->info('Licence notices restored in '.$count.' lessons.');

                    continue;
                }
                $result = $expansion->expand($editor, (int) $courseId);
                $course = $result['run']->course;
                $this->info("Expanded draft: {$course->title} (ID {$course->id})");
                $this->line('Sections: '.$result['summary']['sections'].'; lessons: '.$result['summary']['lessons'].'; quizzes: '.$result['summary']['quizzes'].'; quiz questions: '.$result['summary']['questions']);
                $this->line('The course remains unpublished and unapproved for instructor review.');
            }

            return self::SUCCESS;
        } catch (Throwable $exception) {
            if ($exception instanceof AiProviderException || $exception instanceof ValidationException || $exception instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
                $this->error($exception->getMessage() ?: 'Expansion is not permitted for this draft.');
            } else {
                \Illuminate\Support\Facades\Log::warning('Expansion command failed.', ['exception_type' => $exception::class]);
                $this->error('Expansion could not be completed. Review the run status.');
            }

            return self::FAILURE;
        }
    }
}
