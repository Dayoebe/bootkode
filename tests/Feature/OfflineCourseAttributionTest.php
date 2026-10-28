<?php

namespace Tests\Feature;

use App\Models\Core\User;
use App\Models\Learning\{Course, Lesson, Section};
use App\Services\OfflineLearningPackService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfflineCourseAttributionTest extends TestCase
{
    use RefreshDatabase;

    private function fixture(): array
    {
        $user = User::factory()->create();
        $course = Course::create(['title' => 'Original teaching with source credits', 'instructor_id' => $user->id]);
        $section = Section::create(['course_id' => $course->id, 'title' => 'Practice', 'order' => 1]);
        Lesson::create([
            'section_id' => $section->id, 'title' => 'Useful lesson', 'content' => '<h2>Learn the concept</h2><p>Original teaching example.</p>',
            'published_at' => now(), 'order' => 1,
            'documents' => [
                ['name' => 'Source credits', 'path' => 'course-materials/source-notices.txt', 'size' => 1600, 'type' => 'txt', 'required_attribution' => true],
                ['name' => 'Practice workbook', 'path' => 'course-materials/workbook.pdf', 'size' => 2000, 'type' => 'pdf'],
            ],
        ]);
        $user->courses()->attach($course->id);

        return [$user, $course];
    }

    public function test_lessons_only_download_keeps_source_notices_out_of_reading_text_and_in_the_pack(): void
    {
        [$user, $course] = $this->fixture();
        $manifest = app(OfflineLearningPackService::class)->buildManifest($user, $course, ['lessons']);
        $lesson = $manifest['sections'][0]['lessons'][0];
        $this->assertStringContainsString('Original teaching example', $lesson['content']);
        $this->assertStringNotContainsString('Source credits', $lesson['content']);
        $this->assertCount(1, $lesson['resources']);
        $this->assertSame('Source credits', $lesson['resources'][0]['name']);
        $this->assertContains(asset('storage/course-materials/source-notices.txt'), $manifest['cache_urls']);
        $this->assertNotContains(asset('storage/course-materials/workbook.pdf'), $manifest['cache_urls']);
    }

    public function test_optional_document_selection_keeps_both_required_and_optional_materials(): void
    {
        [$user, $course] = $this->fixture();
        $manifest = app(OfflineLearningPackService::class)->buildManifest($user, $course, ['lessons', 'documents']);
        $this->assertCount(2, $manifest['sections'][0]['lessons'][0]['resources']);
        $this->assertContains(asset('storage/course-materials/workbook.pdf'), $manifest['cache_urls']);
    }

    public function test_notice_handling_does_not_bypass_course_enrollment(): void
    {
        [, $course] = $this->fixture();
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        app(OfflineLearningPackService::class)->buildManifest(User::factory()->create(), $course, ['lessons']);
    }
}
