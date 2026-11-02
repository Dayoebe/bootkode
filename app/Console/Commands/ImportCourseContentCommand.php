<?php

namespace App\Console\Commands;

use App\Exceptions\CourseContentException;
use App\Models\Core\User;
use App\Models\Learning\CourseCategory;
use App\Services\CourseImport\CourseContentSourceRegistry;
use App\Services\CourseImport\CourseImportService;
use Illuminate\Console\Command;
use Illuminate\Validation\ValidationException;
use Throwable;

class ImportCourseContentCommand extends Command
{
    protected $signature = 'bootkode:courses-import
        {--source=freecodecamp : Configured licensed source}
        {--path= : Source curriculum block path}
        {--category-id= : Existing BootKode course category ID}
        {--instructor-id= : Authorised course editor user ID}
        {--limit=12 : Maximum lesson files to inspect (1-12)}
        {--dry-run : Inspect metadata and preview without database writes}
        {--list-sources : List supported configured sources}
        {--new-revision : Create a separate draft if the source revision changed}';

    protected $description = 'Preview or import a bounded, attributed curriculum subset as an editable course draft';

    public function handle(CourseContentSourceRegistry $sources, CourseImportService $imports): int
    {
        if ($this->option('list-sources')) {
            foreach ($imports->sourceList() as $source) {
                $this->line($source['key'].' — '.$source['name']);
            }

            return self::SUCCESS;
        }

        $sourceKey = (string) $this->option('source');
        if (! isset($sources->all()[$sourceKey])) {
            $this->error("Unknown source [{$sourceKey}]. Use --list-sources to view configured sources.");

            return self::FAILURE;
        }

        $path = (string) $this->option('path');
        $limit = filter_var($this->option('limit'), FILTER_VALIDATE_INT);
        if ($path === '' || $limit === false || $limit < 1 || $limit > 12) {
            $this->error('Provide --path and a --limit between 1 and 12.');

            return self::FAILURE;
        }

        try {
            $document = $imports->preview($sourceKey, $path, $limit);
            $this->table(['Source', 'Title', 'Revision', 'Licence', 'Lessons', 'Attribution'], [[
                $sourceKey,
                $document['original_title'],
                substr($document['revision'], 0, 12),
                $document['license_id'],
                count($document['lessons']),
                $document['attribution'],
            ]]);

            if ($this->option('dry-run')) {
                $this->line('Dry run only: no course or import record was created.');
                foreach ($document['lessons'] as $lesson) {
                    $this->line('- '.$lesson['title']);
                }

                return self::SUCCESS;
            }

            $category = CourseCategory::query()->find($this->option('category-id'));
            $editor = User::query()->find($this->option('instructor-id'));
            if (! $category || ! $editor || ! $editor->hasAnyRole(['super_admin', 'academy_admin', 'instructor'])) {
                $this->error('A valid --category-id and --instructor-id with a course-authoring role are required.');

                return self::FAILURE;
            }

            $result = $imports->import($editor, $sourceKey, $path, $category->id, $limit, (bool) $this->option('new-revision'), $document['revision']);
            $this->info("Import status: {$result['run']->status}");
            if ($result['run']->course) {
                $this->line("Draft course: {$result['run']->course->title} (ID {$result['run']->course_id})");
                $this->line('The course remains unpublished and unapproved.');
            }
            foreach ($result['warnings'] as $warning) {
                $this->warn($warning);
            }

            return in_array($result['run']->status, ['completed', 'partial', 'skipped'], true) ? self::SUCCESS : self::FAILURE;
        } catch (Throwable $exception) {
            if ($exception instanceof CourseContentException || $exception instanceof ValidationException) {
                $this->error($exception->getMessage());
            } else {
                \Illuminate\Support\Facades\Log::warning('Course import command failed.', ['exception_type' => $exception::class]);
                $this->error('The import could not be completed. Review the configuration and application logs.');
            }

            return self::FAILURE;
        }
    }
}
