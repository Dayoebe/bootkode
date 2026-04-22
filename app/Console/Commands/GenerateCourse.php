<?php

namespace App\Console\Commands;

use App\Services\CourseGeneration\CourseGenerationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateCourse extends Command
{
    protected $signature = 'bootkode:generate-course
        {--payload= : Path to a JSON payload file}
        {--topic= : Course topic}
        {--category= : Category id, slug, or name}
        {--instructor= : Instructor id or email}
        {--audience= : Target audience}
        {--level=beginner : Difficulty level}
        {--goal=* : One or more course goals}
        {--sections=5 : Number of sections}
        {--lessons=4 : Lessons per section}
        {--video-lessons=1 : Video lessons per section}
        {--video-reference=* : Verified YouTube URLs}
        {--price=0 : Course price}
        {--currency=NGN : Currency code}
        {--publish : Publish immediately}
        {--approve : Mark as approved}
        {--with-faqs : Include FAQs}
        {--with-resources : Include resource placeholders}
        {--with-quizzes : Include generated quizzes}
        {--duplicate=return_existing : Duplicate handling strategy}
        {--create-category : Create the category if it does not exist}
        {--dry-run : Preview the course without inserting it}';

    protected $description = 'Generate a structured Bootkode course from a JSON payload or CLI options';

    public function handle(CourseGenerationService $service): int
    {
        $payload = $this->buildPayload();

        if ($this->option('dry-run')) {
            $preview = $service->preview($payload);
            $this->renderPreview($preview);

            return self::SUCCESS;
        }

        $result = $service->generate($payload);
        $created = $result['created'] ?? false;
        $course = $result['course'];

        $this->line('');
        $this->info(($created ? 'Created' : 'Reused') . " course: {$course->title}");
        $this->line("Course ID: {$course->id}");
        $this->line("Slug: {$course->slug}");
        $this->line("Sections: {$course->sections->count()}");
        $this->line("Lessons: {$course->sections->sum(fn ($section) => $section->lessons->count())}");

        foreach (($result['warnings'] ?? []) as $warning) {
            $this->warn($warning);
        }

        return self::SUCCESS;
    }

    protected function buildPayload(): array
    {
        if ($path = $this->option('payload')) {
            $resolvedPath = str_starts_with($path, DIRECTORY_SEPARATOR) ? $path : base_path($path);

            if (!File::exists($resolvedPath)) {
                $this->fail("Payload file not found: {$resolvedPath}");
            }

            $decoded = json_decode(File::get($resolvedPath), true);

            if (!is_array($decoded)) {
                $this->fail('Payload file must contain a JSON object.');
            }

            return $decoded;
        }

        $topic = $this->option('topic');
        $category = $this->option('category');

        if (!$topic || !$category) {
            $this->fail('Either --payload or both --topic and --category are required.');
        }

        $instructor = $this->option('instructor');

        $payload = array_filter([
            'topic' => $topic,
            'category' => $category,
            'instructor' => $instructor,
            'target_audience' => $this->option('audience'),
            'skill_level' => $this->option('level'),
            'course_goals' => $this->option('goal'),
            'section_count' => (int) $this->option('sections'),
            'lessons_per_section' => (int) $this->option('lessons'),
            'video_lessons_per_section' => (int) $this->option('video-lessons'),
            'video_references' => array_map(
                fn (string $url) => ['url' => $url],
                $this->option('video-reference')
            ),
            'price' => (float) $this->option('price'),
            'currency' => $this->option('currency'),
            'duplicate_strategy' => $this->option('duplicate'),
        ], fn (mixed $value) => !is_null($value));

        if ($this->option('publish')) {
            $payload['publish'] = true;
        }

        if ($this->option('approve')) {
            $payload['approve'] = true;
        }

        if ($this->option('with-faqs')) {
            $payload['include_faqs'] = true;
        }

        if ($this->option('with-resources')) {
            $payload['include_resources'] = true;
        }

        if ($this->option('with-quizzes')) {
            $payload['include_quizzes'] = true;
        }

        if ($this->option('create-category')) {
            $payload['create_category_if_missing'] = true;
        }

        return $payload;
    }

    protected function renderPreview(array $preview): void
    {
        $course = $preview['course'];
        $sections = $preview['sections'] ?? [];

        $this->line('');
        $this->info("Preview: {$course['title']}");
        $this->line($course['subtitle'] ?? '');
        $this->line('Estimated Duration: ' . ($course['estimated_duration_minutes'] ?? 0) . ' minutes');
        $this->line('Sections: ' . count($sections));

        foreach ($sections as $index => $section) {
            $this->line('');
            $this->line(($index + 1) . '. ' . $section['title']);

            foreach (($section['lessons'] ?? []) as $lessonIndex => $lesson) {
                $this->line('   - ' . ($lessonIndex + 1) . '. ' . $lesson['title'] . ' [' . ($lesson['content_type'] ?? 'text') . ']');
            }
        }

        foreach (($preview['warnings'] ?? []) as $warning) {
            $this->warn($warning);
        }
    }
}
