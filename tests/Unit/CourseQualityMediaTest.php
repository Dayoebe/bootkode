<?php

namespace Tests\Unit;

use App\Models\Learning\Course;
use App\Models\Learning\Lesson;
use App\Models\Learning\Section;
use App\Services\CourseQualityService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CourseQualityMediaTest extends TestCase
{
    public function test_escaped_code_and_plain_text_examples_are_not_reported_as_missing_media(): void
    {
        Http::preventStrayRequests();
        $course = $this->course(
            '<pre><code>&lt;link href="styles.css"&gt; &lt;script src="script.js"&gt;</code></pre>',
            'A teaching example: href="styles.css" and src="script.js".'
        );
        $result = app(CourseQualityService::class)->evaluate($course);

        $this->assertSame([], $result['media_results']);
        $this->assertSame(0, $result['summary']['broken_media_count']);
        Http::assertNothingSent();
    }

    public function test_real_links_are_checked_with_whitespace_and_entities_preserved(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('files/guide.pdf', 'Practice guide');
        Http::preventStrayRequests();
        $course = $this->course('<a href = "/storage/files/guide.pdf">Guide</a>'
            .'<img src="/storage/files/missing.png" alt="Example">'
            .'<a href="https://example.com/reference?a=1&amp;b=2">Reference</a>');
        $result = app(CourseQualityService::class)->evaluate($course);
        $items = collect($result['media_results'])->keyBy('value');

        $this->assertSame('ok', $items['/storage/files/guide.pdf']['status']);
        $this->assertSame('broken', $items['/storage/files/missing.png']['status']);
        $this->assertSame('unchecked', $items['https://example.com/reference?a=1&b=2']['status']);
        $this->assertSame(1, $result['summary']['broken_media_count']);
        Http::assertNothingSent();
    }

    public function test_code_examples_do_not_hide_real_media_elsewhere_in_the_lesson(): void
    {
        Storage::fake('public');
        $course = $this->course('<pre><a href="example-only.pdf">Example markup</a></pre>'
            .'<p><a href="/storage/files/actually-missing.pdf">Required reading</a></p>');
        $result = app(CourseQualityService::class)->evaluate($course);

        $this->assertCount(1, $result['media_results']);
        $this->assertSame('/storage/files/actually-missing.pdf', $result['media_results'][0]['value']);
        $this->assertSame(1, $result['summary']['broken_media_count']);
    }

    private function course(string $html, string $text = ''): Course
    {
        $lesson = new Lesson(['title' => 'Original lesson', 'content' => $html, 'text_content' => $text]);
        $lesson->setRelation('assessments', collect());
        $section = new Section(['title' => 'Practice']);
        $section->setRelation('lessons', collect([$lesson]));
        $section->setRelation('assessments', collect());
        $course = new Course(['title' => 'Practice course']);
        $course->setRelation('sections', collect([$section]));
        $course->setRelation('directAssessments', collect());

        return $course;
    }
}
