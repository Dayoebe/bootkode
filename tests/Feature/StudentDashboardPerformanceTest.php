<?php

namespace Tests\Feature;

use App\Livewire\Dashboard\StudentDashboard;
use App\Models\Assessment\LearningSession;
use App\Models\Core\User;
use App\Models\Learning\Course;
use App\Models\Learning\CourseEnrollment;
use App\Models\Learning\Lesson;
use App\Models\Learning\Section;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StudentDashboardPerformanceTest extends TestCase
{
    use RefreshDatabase;

    private function student(): User
    {
        Role::findOrCreate(User::ROLE_STUDENT, 'web');
        $student = User::factory()->create(['role' => User::ROLE_STUDENT]);
        $student->assignRole(User::ROLE_STUDENT);

        return $student;
    }

    private function enrollWithPublishedLessons(User $student, int $courseNumber, int $lessonCount = 4): array
    {
        $course = Course::withoutEvents(fn () => Course::create([
            'instructor_id' => $student->id,
            'title' => "Course {$courseNumber}",
            'slug' => "performance-course-{$courseNumber}",
            'is_published' => true,
            'is_approved' => true,
            'estimated_duration_minutes' => 240,
        ]));
        $section = Section::create([
            'course_id' => $course->id,
            'title' => "Section {$courseNumber}",
            'order' => 1,
        ]);
        $lessons = collect();

        for ($lessonNumber = 1; $lessonNumber <= $lessonCount; $lessonNumber++) {
            $lessons->push(Lesson::withoutEvents(fn () => Lesson::create([
                'section_id' => $section->id,
                'title' => "Course {$courseNumber} lesson {$lessonNumber}",
                'slug' => "performance-course-{$courseNumber}-lesson-{$lessonNumber}",
                'order' => $lessonNumber,
                'published_at' => now(),
            ])));
        }

        CourseEnrollment::create([
            'course_id' => $course->id,
            'user_id' => $student->id,
            'enrolled_at' => now(),
            'progress_percentage' => 25,
            'is_completed' => false,
        ]);

        DB::table('lesson_user')->insert([
            'user_id' => $student->id,
            'lesson_id' => $lessons->first()->id,
            'completed_at' => now(),
            'time_spent_minutes' => 10,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [$course, $lessons];
    }

    public function test_learning_progress_query_count_does_not_grow_per_visible_enrollment(): void
    {
        $student = $this->student();
        for ($courseNumber = 1; $courseNumber <= 6; $courseNumber++) {
            $this->enrollWithPublishedLessons($student, $courseNumber);
        }
        $this->actingAs($student);

        $queries = [];
        DB::listen(function ($query) use (&$queries): void {
            $queries[] = $query->sql;
        });

        $progress = app(StudentDashboard::class)->learningProgress();

        $this->assertCount(6, $progress);
        $this->assertSame(6, count($queries), 'Visible course progress should use a fixed query graph.');
        $this->assertSame([1, 1, 1, 1, 1, 1], $progress->pluck('completed_lessons')->values()->all());
        $this->assertSame([4, 4, 4, 4, 4, 4], $progress->pluck('total_lessons')->values()->all());
        $this->assertTrue($progress->every(fn ($course) => str_ends_with($course['next_lesson']['title'], 'lesson 2')));
    }

    public function test_dashboard_read_does_not_create_gamification_state_and_only_chart_views_request_chart_assets(): void
    {
        $student = $this->student();
        $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true, flags: JSON_THROW_ON_ERROR);
        $chartAsset = is_file(public_path('hot'))
            ? 'resources/js/charts.js'
            : $manifest['resources/js/charts.js']['file'];

        $this->assertDatabaseCount('gamification_data', 0);
        $this->actingAs($student)->get(route('profile.view'))
            ->assertOk()
            ->assertDontSee($chartAsset, false);
        Livewire::flushState();
        $this->get(route('student.dashboard'))
            ->assertOk()
            ->assertSee($chartAsset, false);
        $this->assertDatabaseCount('gamification_data', 0);
    }

    public function test_authenticated_marketing_analytics_respects_saved_privacy_consent(): void
    {
        $student = $this->student();

        $this->actingAs($student)->get(route('student.dashboard'))
            ->assertOk()
            ->assertDontSee('googletagmanager.com', false);

        $student->update(['privacy_settings' => [
            'data_collection_consent' => true,
            'analytics_tracking' => true,
            'marketing_analytics' => true,
            'third_party_integrations' => true,
        ]]);

        $this->get(route('student.dashboard'))
            ->assertOk()
            ->assertSee('googletagmanager.com', false);
    }

    public function test_secondary_widget_failure_keeps_the_learning_workspace_available(): void
    {
        $student = $this->student();
        DB::listen(function ($query): void {
            if (str_contains($query->sql, 'system_statuses')) {
                throw new \RuntimeException('Synthetic secondary widget failure.');
            }
        });

        Livewire::actingAs($student)->test(StudentDashboard::class)
            ->assertSee('Your learning workspace')
            ->assertSee('System status is temporarily unavailable')
            ->assertSee('No learning data was changed');
    }

    public function test_study_streak_uses_one_bounded_activity_date_query(): void
    {
        $student = $this->student();
        $this->travelTo(now()->startOfDay()->addHours(18));

        foreach ([0, 1, 2, 4] as $daysAgo) {
            LearningSession::create([
                'user_id' => $student->id,
                'started_at' => now()->subDays($daysAgo)->subHours(2),
                'ended_at' => now()->subDays($daysAgo)->subHour(),
                'duration_minutes' => 60,
                'activity_type' => 'lesson',
            ]);
        }

        $queryCount = 0;
        DB::listen(function () use (&$queryCount): void {
            $queryCount++;
        });

        $this->assertSame(3, LearningSession::getStudyStreak($student->id));
        $this->assertSame(1, $queryCount);
    }
}
