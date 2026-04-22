<?php

namespace Tests\Feature\CourseGeneration;

use App\Models\Assessment\Assessment;
use App\Models\Learning\Course;
use App\Models\Learning\CourseCategory;
use App\Services\CourseGeneration\CourseGenerationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CourseGenerationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_generates_a_complete_course_from_high_level_input(): void
    {
        $instructorId = $this->createInstructor();
        $category = CourseCategory::create([
            'name' => 'Web Development',
            'description' => 'Web development courses.',
        ]);

        $result = app(CourseGenerationService::class)->generate([
            'topic' => 'Laravel API Delivery',
            'category' => ['id' => $category->id],
            'instructor' => ['id' => $instructorId],
            'skill_level' => 'intermediate',
            'course_goals' => [
                'Design a cleaner API workflow',
                'Build maintainable feature delivery habits',
                'Ship work with stronger review discipline',
            ],
            'section_count' => 3,
            'lessons_per_section' => 3,
            'include_faqs' => true,
            'include_resources' => true,
            'include_quizzes' => true,
            'publish' => true,
            'approve' => true,
        ]);

        $this->assertTrue($result['created']);
        $this->assertNotEmpty($result['warnings']);

        $course = $result['course'];
        $sections = $course->sections->sortBy('order')->values();

        $this->assertSame($instructorId, $course->instructor_id);
        $this->assertSame($category->id, $course->category_id);
        $this->assertCount(3, $sections);
        $this->assertSame(9, $sections->sum(fn ($section) => $section->lessons->count()));
        $this->assertFalse((bool) $sections[0]->is_locked);
        $this->assertTrue((bool) $sections[1]->is_locked);
        $this->assertNotEmpty($course->learning_outcomes);
        $this->assertNotEmpty($course->materials_included);
        $this->assertSame(3, Assessment::count());

        $videoLesson = $sections
            ->flatMap(fn ($section) => $section->lessons)
            ->first(fn ($lesson) => $lesson->content_type === 'video');

        $this->assertNotNull($videoLesson);
        $this->assertNull($videoLesson->video_url);
        $this->assertStringContainsString('Manual review required', $videoLesson->content);
    }

    public function test_it_reuses_existing_course_when_duplicate_strategy_is_return_existing(): void
    {
        $instructorId = $this->createInstructor();
        $category = CourseCategory::create([
            'name' => 'Data Analysis',
            'description' => 'Data analysis courses.',
        ]);

        $payload = [
            'topic' => 'Applied Data Analysis',
            'category' => ['id' => $category->id],
            'instructor' => ['id' => $instructorId],
            'skill_level' => 'beginner',
            'course_goals' => [
                'Ask better data questions',
                'Clean data more reliably',
                'Communicate insights clearly',
            ],
            'section_count' => 2,
            'lessons_per_section' => 2,
            'duplicate_strategy' => 'return_existing',
        ];

        $first = app(CourseGenerationService::class)->generate($payload);
        $second = app(CourseGenerationService::class)->generate($payload);

        $this->assertTrue($first['created']);
        $this->assertFalse($second['created']);
        $this->assertSame($first['course']->id, $second['course']->id);
        $this->assertSame(1, Course::count());
    }

    protected function createInstructor(): int
    {
        return DB::table('users')->insertGetId([
            'name' => 'Course Instructor',
            'email' => 'instructor@example.com',
            'password' => Hash::make('password'),
            'role' => 'instructor',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
