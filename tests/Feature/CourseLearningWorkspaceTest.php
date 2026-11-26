<?php

namespace Tests\Feature;

use App\Livewire\StudentManagement\CourseView\LessonToolsPanel;
use App\Models\Core\User;
use App\Models\Learning\Course;
use App\Models\Learning\CourseEnrollment;
use App\Models\Learning\Lesson;
use App\Models\Learning\SavedResource;
use App\Models\Learning\Section;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CourseLearningWorkspaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_enrolled_student_sees_the_dedicated_learning_workspace_and_real_lesson_content(): void
    {
        [$student, $course, $lesson] = $this->createEnrolledCourse();

        $this->actingAs($student)
            ->get(route('course.view', ['course' => $course->slug]))
            ->assertOk()
            ->assertSee('BootKode Learning')
            ->assertSee($course->title)
            ->assertSee($lesson->title)
            ->assertSee('Course Map')
            ->assertSee('Private note')
            ->assertSee('enhanceBootKodeLesson', escape: false);
    }

    public function test_unenrolled_student_cannot_open_the_learning_workspace(): void
    {
        [, $course] = $this->createEnrolledCourse();
        $otherStudent = User::factory()->create();

        $this->actingAs($otherStudent)
            ->get(route('course.view', ['course' => $course->slug]))
            ->assertForbidden();
    }

    public function test_private_notes_and_bookmarks_are_scoped_to_the_authenticated_student(): void
    {
        [$student, , $lesson] = $this->createEnrolledCourse();

        Livewire::actingAs($student)
            ->test(LessonToolsPanel::class, ['lesson' => $lesson])
            ->set('lessonNotes', 'Review dependency injection before the assessment.')
            ->call('saveNotes')
            ->assertSet('statusMessage', 'Private note saved.')
            ->call('toggleBookmark')
            ->assertSet('isBookmarked', true);

        $this->assertDatabaseHas('saved_resources', [
            'user_id' => $student->id,
            'resourceable_type' => Lesson::class,
            'resourceable_id' => $lesson->id,
            'type' => 'note',
            'notes' => 'Review dependency injection before the assessment.',
        ]);
        $this->assertDatabaseHas('saved_resources', [
            'user_id' => $student->id,
            'resourceable_type' => Lesson::class,
            'resourceable_id' => $lesson->id,
            'type' => 'lesson',
        ]);

        $otherStudent = User::factory()->create();
        CourseEnrollment::create([
            'course_id' => $lesson->section->course_id,
            'user_id' => $otherStudent->id,
            'enrolled_at' => now(),
        ]);

        Livewire::actingAs($otherStudent)
            ->test(LessonToolsPanel::class, ['lesson' => $lesson])
            ->assertSet('lessonNotes', '')
            ->assertSet('isBookmarked', false);

        $this->assertSame(2, SavedResource::where('user_id', $student->id)->count());
        $this->assertSame(0, SavedResource::where('user_id', $otherStudent->id)->count());
    }

    private function createEnrolledCourse(): array
    {
        $instructor = User::factory()->create();
        $student = User::factory()->create();
        $course = Course::create([
            'instructor_id' => $instructor->id,
            'title' => 'Practical Laravel Foundations',
            'subtitle' => 'Build a reliable application one lesson at a time.',
            'slug' => 'practical-laravel-foundations',
            'description' => 'A structured technical course.',
            'difficulty_level' => 'beginner',
            'is_published' => true,
            'is_approved' => true,
            'is_free' => true,
            'published_at' => now(),
        ]);
        $section = Section::create([
            'course_id' => $course->id,
            'title' => 'Application foundations',
            'order' => 1,
            'is_locked' => false,
        ]);
        $lesson = Lesson::create([
            'section_id' => $section->id,
            'title' => 'Understand the service container',
            'slug' => 'practical-laravel-foundations-understand-the-service-container',
            'description' => 'Learn how Laravel resolves application dependencies.',
            'content' => '<h2>Resolve dependencies</h2><p>Use the container deliberately.</p><pre><code class="language-php">app(Service::class);</code></pre>',
            'duration_minutes' => 12,
            'order' => 1,
            'published_at' => now(),
        ]);
        CourseEnrollment::create([
            'course_id' => $course->id,
            'user_id' => $student->id,
            'enrolled_at' => now(),
        ]);

        return [$student, $course, $lesson];
    }
}
