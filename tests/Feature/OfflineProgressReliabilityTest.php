<?php

namespace Tests\Feature;

use App\Models\Assessment\Assessment;
use App\Models\Core\User;
use App\Models\Learning\Course;
use App\Models\Learning\CourseEnrollment;
use App\Models\Learning\Lesson;
use App\Models\Learning\LessonProgress;
use App\Models\Learning\Section;
use App\Services\OfflineLearningPackService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OfflineProgressReliabilityTest extends TestCase
{
    use RefreshDatabase;

    private function fixture(): array
    {
        $user = User::factory()->create();
        $instructor = User::factory()->create();
        $course = Course::withoutEvents(fn () => Course::create(['title' => 'Offline practice', 'slug' => 'offline-practice',
            'instructor_id' => $instructor->id, 'is_published' => true, 'is_approved' => true]));
        $section = Section::create(['course_id' => $course->id, 'title' => 'Practice', 'order' => 1]);
        $lesson = Lesson::withoutEvents(fn () => Lesson::create(['section_id' => $section->id, 'title' => 'First practice', 'slug' => 'first-practice', 'order' => 1,
            'content' => '<p>Original practice text.</p><script>badScript()</script>', 'published_at' => now()]));
        $user->courses()->attach($course->id);
        CourseEnrollment::create(['user_id' => $user->id, 'course_id' => $course->id, 'enrolled_at' => now()]);
        $this->actingAs($user);

        return [$user, $course, $lesson, $section];
    }

    private function event(Lesson $lesson, string $id = 'event-one', int $seconds = 20): array
    {
        return ['event_id' => $id, 'lesson_id' => $lesson->id, 'type' => 'lesson_completed', 'time_spent_seconds' => $seconds, 'completed_at' => now()->addYear()->toIso8601String()];
    }

    public function test_offline_replay_keeps_original_completion_dates_maximum_time_and_rewards(): void
    {
        [$user, $course, $lesson] = $this->fixture();
        $service = app(OfflineLearningPackService::class);
        $first = $service->syncProgress($user, $course, [$this->event($lesson)]);
        $date = DB::table('lesson_user')->where('lesson_id', $lesson->id)->value('completed_at');
        $courseDate = $user->enrollments()->first()->completed_at;
        $points = $user->getOrCreateGamificationData()->points;
        $this->travel(2)->hours();
        $replay = $service->syncProgress($user, $course, [$this->event($lesson, 'event-two', 1)]);
        $this->assertSame(['event-one'], $first['accepted_event_ids']);
        $this->assertSame(['event-two'], $replay['accepted_event_ids']);
        $this->assertSame($date, DB::table('lesson_user')->where('lesson_id', $lesson->id)->value('completed_at'));
        $this->assertTrue($courseDate->equalTo($user->enrollments()->first()->completed_at));
        $this->assertSame(20, LessonProgress::first()->time_spent_seconds);
        $this->assertSame($points, $user->fresh()->getOrCreateGamificationData()->points);
        $this->assertDatabaseCount('lesson_user', 1);
        $this->assertDatabaseCount('lesson_progress', 1);
        $this->travelBack();
    }

    public function test_offline_completion_cannot_bypass_an_unpassed_lesson_assessment(): void
    {
        [$user, $course, $lesson] = $this->fixture();
        Assessment::create(['course_id' => $course->id, 'lesson_id' => $lesson->id, 'title' => 'Required reviewed quiz', 'type' => 'quiz', 'pass_percentage' => 70]);
        $result = app(OfflineLearningPackService::class)->syncProgress($user, $course, [$this->event($lesson)]);
        $this->assertSame([], $result['accepted_event_ids']);
        $this->assertSame([$lesson->id], $result['blocked_lesson_ids']);
        $this->assertDatabaseCount('lesson_user', 0);
        $this->assertDatabaseCount('lesson_progress', 0);
    }

    public function test_offline_completion_cannot_skip_a_locked_section_or_a_foreign_lesson(): void
    {
        [$user, $course] = $this->fixture();
        $next = Section::create(['course_id' => $course->id, 'title' => 'Later practice', 'order' => 2, 'is_locked' => true]);
        $lesson = Lesson::withoutEvents(fn () => Lesson::create(['section_id' => $next->id, 'title' => 'Later lesson', 'slug' => 'later-lesson', 'published_at' => now()]));
        $result = app(OfflineLearningPackService::class)->syncProgress($user, $course, [$this->event($lesson), ['event_id' => 'foreign', 'lesson_id' => 999999]]);
        $this->assertSame([], $result['accepted_event_ids']);
        $this->assertSame([$lesson->id], $result['blocked_lesson_ids']);
        $this->assertSame(1, $result['ignored']);
        $this->assertDatabaseCount('lesson_user', 0);
    }

    public function test_sync_owner_binding_refuses_another_accounts_old_local_queue(): void
    {
        [$user, $course, $lesson] = $this->fixture();
        $this->postJson(route('offline-learning.sync', $course->slug), ['owner_id' => $user->id + 1, 'events' => [$this->event($lesson)]])->assertForbidden();
        $this->assertDatabaseCount('lesson_user', 0);
        $this->postJson(route('offline-learning.sync', $course->slug), ['events' => [$this->event($lesson)]])->assertUnprocessable()->assertJsonValidationErrors('owner_id');
    }

    public function test_fresh_sync_session_is_private_and_rechecks_authentication(): void
    {
        [$user] = $this->fixture();
        $this->getJson(route('offline-learning.session'))->assertOk()->assertJsonPath('user_id', $user->id)->assertJsonStructure(['csrf_token'])->assertHeader('Cache-Control', 'no-store, private');
        auth()->logout();
        $this->getJson(route('offline-learning.session'))->assertUnauthorized();
    }

    public function test_manifest_is_owner_bound_sanitized_and_never_lists_authenticated_html_for_cache(): void
    {
        [$user, $course] = $this->fixture();
        $manifest = app(OfflineLearningPackService::class)->buildManifest($user, $course, ['lessons']);
        $this->assertSame($user->id, $manifest['user_id']);
        $this->assertStringNotContainsString('<script', $manifest['sections'][0]['lessons'][0]['content']);
        foreach ($manifest['cache_urls'] as $url) {
            $this->assertStringNotContainsString('/course/', $url);
        }
    }

    public function test_revoked_enrollment_refuses_sync_without_modifying_owned_progress(): void
    {
        [$user, $course, $lesson] = $this->fixture();
        $user->courses()->detach($course->id);
        $user->enrollments()->delete();
        $this->postJson(route('offline-learning.sync', $course->slug), ['owner_id' => $user->id, 'events' => [$this->event($lesson)]])->assertForbidden();
        $this->assertDatabaseCount('lesson_user', 0);
    }

    public function test_library_paginates_owned_summaries_without_loading_full_lesson_manifests(): void
    {
        [$user] = $this->fixture();
        for ($i = 0; $i < 11; $i++) {
            $course = Course::withoutEvents(fn () => Course::create(['title' => 'Owned pack '.$i, 'slug' => 'owned-pack-'.$i, 'instructor_id' => $user->id]));
            \App\Models\Learning\DownloadableContent::create(['user_id' => $user->id, 'course_id' => $course->id, 'downloaded_at' => now(), 'size_mb' => 0.01,
                'manifest' => ['course' => ['lesson_count' => 3], 'sections' => [['private_body' => str_repeat('Private lesson body. ', 100)]]]]);
        }
        $other = User::factory()->create();
        $foreign = Course::withoutEvents(fn () => Course::create(['title' => 'Foreign private pack', 'slug' => 'foreign-private-pack', 'instructor_id' => $other->id]));
        \App\Models\Learning\DownloadableContent::create(['user_id' => $other->id, 'course_id' => $foreign->id, 'downloaded_at' => now(), 'size_mb' => 0.01]);
        $component = \Livewire\Livewire::test(\App\Livewire\StudentManagement\OfflineLearningLibrary::class)->assertDontSee('Foreign private pack');
        $packs = $component->viewData('packs');
        $this->assertSame(11, $packs->total());
        $this->assertCount(10, $packs);
        $this->assertArrayNotHasKey('manifest', $packs->first()->getAttributes());
        $this->assertSame(3, (int) $packs->first()->offline_lesson_count);
    }

    public function test_logout_requests_browser_cache_and_storage_cleanup_after_confirmed_logout(): void
    {
        $this->actingAs(User::factory()->create());
        $this->post(route('logout'))->assertRedirect('/')->assertHeader('Clear-Site-Data', '"cache", "storage"');
        $this->assertGuest();
    }
}
