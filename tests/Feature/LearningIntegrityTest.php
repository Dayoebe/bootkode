<?php

namespace Tests\Feature;

use App\Models\Assessment\UserProgress;
use App\Models\Core\User;
use App\Models\Learning\Course;
use App\Models\Learning\Lesson;
use App\Models\Learning\Section;
use App\Services\CertificateService;
use App\Services\ProgressService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LearningIntegrityTest extends TestCase
{
    use RefreshDatabase;

    private function fixture(): array
    {
        $user = User::factory()->create();
        $course = Course::withoutEvents(fn () => Course::create([
            'title' => 'Learning fixture', 'slug' => 'learning-fixture', 'instructor_id' => $user->id,
        ]));
        $first = Section::create(['course_id' => $course->id, 'title' => 'First', 'order' => 1, 'is_locked' => true]);
        $next = Section::create(['course_id' => $course->id, 'title' => 'Next', 'order' => 2, 'is_locked' => true]);
        $lesson = Lesson::withoutEvents(fn () => Lesson::create(['section_id' => $first->id, 'title' => 'Practice', 'slug' => 'practice-fixture']));
        $user->courses()->attach($course->id);
        return [$user, $course, $next, $lesson];
    }

    public function test_completing_a_lesson_unlocks_only_the_current_learners_path(): void
    {
        [$user, $course, $next, $lesson] = $this->fixture();
        $service = app(ProgressService::class);
        $this->actingAs($user);
        $this->assertFalse($service->canAccessSection($course, $next));
        $service->completeLesson($course, $lesson);
        $this->assertTrue($service->canAccessSection($course, $next));
        $this->assertTrue((bool) $next->fresh()->is_locked);
        $this->assertNotNull(UserProgress::first()->completed_at);
        $other = User::factory()->create();
        $other->courses()->attach($course->id);
        $this->actingAs($other);
        $this->assertFalse($service->canAccessSection($course, $next));
    }

    public function test_certificate_eligibility_requires_enrollment_and_completed_lessons(): void
    {
        [$user, $course, , $lesson] = $this->fixture();
        $service = app(CertificateService::class);
        $this->assertFalse($service->canRequestCertificate(User::factory()->create()->id, $course->id)['can_request']);
        $this->assertFalse($service->canRequestCertificate($user->id, $course->id)['can_request']);
        $user->completedLessons()->attach($lesson->id, ['completed_at' => now()]);
        $this->assertTrue($service->canRequestCertificate($user->id, $course->id)['can_request']);
    }

    public function test_progress_write_rejects_a_learner_without_enrollment(): void
    {
        [, $course, , $lesson] = $this->fixture();
        $this->actingAs(User::factory()->create());
        try {
            app(ProgressService::class)->completeLesson($course, $lesson);
            $this->fail('Progress was accepted without enrollment');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        }
        $this->assertDatabaseCount('user_progress', 0);
    }

    public function test_certificate_request_rechecks_completion_when_client_changes_the_flag(): void
    {
        [$user, $course] = $this->fixture();
        \Livewire\Livewire::actingAs($user)->test(\App\Livewire\CertificateManagement\CertificateRequest::class, ['courseId' => $course->id])
            ->set('canRequestCertificate', true)->set('completionPercentage', 100)
            ->call('requestCertificate')->assertSet('canRequestCertificate', false);
        $this->assertDatabaseCount('certificates', 0);
    }
}
