<?php

namespace Tests\Feature;

use App\Jobs\SendPortalNotificationEmail;
use App\Livewire\Gamification\GamificationDashboard;
use App\Livewire\SystemManagement\NotificationPreferences;
use App\Models\Assessment\Assessment;
use App\Models\Assessment\Question;
use App\Models\Assessment\StudentAnswer;
use App\Models\Core\User;
use App\Models\Credentials\UserAchievement;
use App\Models\Credentials\UserBadge;
use App\Models\Learning\Course;
use App\Models\Learning\CourseEnrollment;
use App\Models\Learning\ReviewReminder;
use App\Services\AssessmentReviewService;
use App\Services\CbtAttemptService;
use App\Services\CbtReminderService;
use App\Services\GamificationService;
use App\Services\ReviewReminderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LearningNotificationDeliveryTest extends TestCase
{
    use RefreshDatabase;

    public function test_forged_request_host_cannot_authorize_an_external_notification_link(): void
    {
        $f = $this->fixture();
        request()->headers->set('host', 'attacker.example.test');
        try {
            DB::transaction(fn () => app(\App\Services\PortalNotificationService::class)->record($f['learner'], 'forged-host',
                ['title' => 'Untrusted destination', 'message' => 'An attacker-controlled host is not application authority.', 'action_url' => 'https://attacker.example.test/private'], 'assessment_review'));
            $this->fail('A request host must not authorize a notification destination.');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $exception) {
            $this->assertSame(422, $exception->getStatusCode());
        }
        $this->assertDatabaseCount('notifications', 0);
    }

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.timezone' => 'UTC', 'mail.default' => 'array']);
        $this->travelTo(\Illuminate\Support\Carbon::parse('2026-10-27 12:00:00', 'UTC'));
        Notification::fake();
        Queue::fake([SendPortalNotificationEmail::class]);
    }

    protected function tearDown(): void
    {
        $this->travelBack();
        parent::tearDown();
    }

    private function fixture(bool $written = false): array
    {
        Role::findOrCreate('instructor', 'web');
        Role::findOrCreate('student', 'web');
        $teacher = User::factory()->create(['role' => 'instructor']);
        $learner = User::factory()->create();
        $other = User::factory()->create();
        $course = Course::withoutEvents(fn () => Course::create(['title' => 'Learning delivery course', 'slug' => 'delivery-'.Str::uuid(), 'instructor_id' => $teacher->id, 'is_published' => true, 'is_approved' => true]));
        $enrollment = CourseEnrollment::create(['course_id' => $course->id, 'user_id' => $learner->id, 'enrolled_at' => now()->subMonth(), 'is_completed' => false]);
        $assessment = Assessment::create(['course_id' => $course->id, 'title' => 'Delivery assessment', 'type' => 'quiz', 'pass_percentage' => 70, 'estimated_duration_minutes' => 10, 'max_attempts' => 2, 'deadline' => now()->addHours(12)]);
        $question = Question::create(['assessment_id' => $assessment->id, 'question_type' => $written ? 'essay' : 'multiple_choice', 'question_text' => 'Describe the tested approach', 'options' => $written ? [] : ['First', 'Second'], 'correct_answers' => $written ? ['PRIVATE KEY'] : [0], 'explanation' => 'PRIVATE RUBRIC', 'points' => 10, 'order' => 1]);
        $learner->getOrCreateGamificationData()->update(['daily_quests' => [], 'last_quest_reset' => now()]);
        $this->actingAs($learner);

        return compact('teacher', 'learner', 'other', 'course', 'enrollment', 'assessment', 'question');
    }

    private function submitted(array $f): array
    {
        $session = app(CbtAttemptService::class)->start($f['learner'], $f['assessment'], true);
        app(CbtAttemptService::class)->submit($f['learner'], $f['assessment'], $session->id, [$f['question']->id => 'PRIVATE ORIGINAL RESPONSE']);

        return [$session->fresh(), StudentAnswer::where('assessment_id', $f['assessment']->id)->where('user_id', $f['learner']->id)->firstOrFail()];
    }

    private function finalized(array $f): array
    {
        [$session,$answer] = $this->submitted($f);
        app(AssessmentReviewService::class)->grade($f['teacher'], $session->id, $answer->id, 5, 'PRIVATE LEARNER FEEDBACK');

        return [$session->fresh(), $answer->fresh()];
    }

    private function notice(User $user, string $type): DatabaseNotification
    {
        return $user->notifications()->where('data->type', $type)->firstOrFail();
    }

    public function test_final_manual_mark_and_notice_are_atomic_private_and_replay_safe(): void
    {
        $f = $this->fixture(true);
        [$session,$answer] = $this->submitted($f);
        $service = app(AssessmentReviewService::class);
        $service->grade($f['teacher'], $session->id, $answer->id, 5, 'PRIVATE LEARNER FEEDBACK');
        $service->grade($f['teacher'], $session->id, $answer->id, 5, 'PRIVATE LEARNER FEEDBACK');
        $notice = $this->notice($f['learner'], 'assessment_result');
        $this->assertSame('disabled', $notice->data['mail_state']);
        $this->assertStringNotContainsString('PRIVATE', json_encode($notice->data));
        $this->assertSame(1, $f['learner']->notifications()->count());
        $this->assertSame(0, $f['other']->notifications()->count());
        $this->assertDatabaseCount('learning_notification_receipts', 1);
        $notice->delete();
        $service->grade($f['teacher'], $session->id, $answer->id, 5, 'PRIVATE LEARNER FEEDBACK');
        $this->assertSame(0, $f['learner']->notifications()->count());
    }

    public function test_failed_notice_storage_rolls_back_the_official_mark_and_result(): void
    {
        $f = $this->fixture(true);
        [$session,$answer] = $this->submitted($f);
        DatabaseNotification::creating(fn () => throw new \RuntimeException('Forced notice failure'));
        try {
            app(AssessmentReviewService::class)->grade($f['teacher'], $session->id, $answer->id, 5);
            $this->fail('Expected rollback');
        } catch (\RuntimeException $e) {
            $this->assertSame('Forced notice failure', $e->getMessage());
        } finally {
            DatabaseNotification::flushEventListeners();
        }
        $this->assertNull($answer->fresh()->graded_at);
        $this->assertTrue($session->fresh()->metadata['results']['pending_review']);
        $this->assertDatabaseCount('learning_notification_receipts', 0);
    }

    public function test_review_request_notifies_actual_course_owner_and_upheld_decision_notifies_only_learner(): void
    {
        $f = $this->fixture(true);
        [$session,$answer] = $this->finalized($f);
        $service = app(AssessmentReviewService::class);
        $request = $service->requestMarkReview($f['learner'], $f['assessment'], 1, $answer->id, 'Please reconsider the original recorded mark.');
        $service->requestMarkReview($f['learner'], $f['assessment'], 1, $answer->id, 'Please reconsider the original recorded mark.');
        $this->assertSame(1, $f['teacher']->notifications()->where('data->type', 'assessment_review_request')->count());
        $revision = $service->revision($session->fresh());
        $operation = (string) Str::uuid();
        $service->upholdMark($f['teacher'], $session->id, $request['id'], 'The original mark follows the recorded rubric.', $revision, $operation);
        $service->upholdMark($f['teacher'], $session->id, $request['id'], 'The original mark follows the recorded rubric.', $revision, $operation);
        $this->assertSame(1, $f['learner']->notifications()->where('data->type', 'assessment_review_decision')->count());
        $this->assertEquals(5, $answer->fresh()->points_earned);
        $this->assertSame(0, $f['other']->notifications()->count());
        $this->assertStringNotContainsString('rubric', json_encode($this->notice($f['learner'], 'assessment_review_decision')->data));
    }

    public function test_correction_records_notice_without_changing_existing_rewards_and_replays_do_not_repeat(): void
    {
        $f = $this->fixture(true);
        [$session,$answer] = $this->finalized($f);
        $service = app(AssessmentReviewService::class);
        $revision = $service->revision($session->fresh());
        $operation = (string) Str::uuid();
        $service->correct($f['teacher'], $session->id, $answer->id, 6, 'Private updated feedback', 'A documented credit correction.', $revision, $operation);
        $service->correct($f['teacher'], $session->id, $answer->id, 6, 'Private updated feedback', 'A documented credit correction.', $revision, $operation);
        $this->assertSame(1, $f['learner']->notifications()->where('data->type', 'assessment_review_decision')->count());
        $this->assertDatabaseCount('gamification_transactions', 0);
        $this->assertEquals(60, $session->fresh()->metadata['results']['percentage']);
    }

    public function test_queued_review_request_email_rechecks_current_course_owner_and_open_request(): void
    {
        $f = $this->fixture(true);
        [$session,$answer] = $this->finalized($f);
        config(['mail.default' => 'smtp']);
        $f['teacher']->update(['notification_preferences' => ['email_assessment_reviews' => true]]);
        $request = app(AssessmentReviewService::class)->requestMarkReview($f['learner'], $f['assessment'], 1, $answer->id, 'Please reconsider the original recorded mark.');
        $notice = $this->notice($f['teacher'], 'assessment_review_request');
        $this->assertSame('pending', $notice->data['mail_state']);
        $f['course']->update(['instructor_id' => $f['other']->id]);
        (new SendPortalNotificationEmail($notice->id))->handle();
        $this->assertSame('cancelled', $notice->fresh()->data['mail_state']);
        Notification::assertNothingSent();
    }

    public function test_revoked_reviewer_cannot_publish_a_mark_or_notification(): void
    {
        $f = $this->fixture(true);
        [$session,$answer] = $this->submitted($f);
        $f['teacher']->update(['is_active' => false]);
        try {
            app(AssessmentReviewService::class)->grade($f['teacher'], $session->id, $answer->id, 5);
            $this->fail('Inactive reviewer accepted');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
        }
        $this->assertNull($answer->fresh()->graded_at);
        $this->assertDatabaseCount('notifications', 0);
    }

    public function test_cbt_reminders_require_real_enrollment_available_course_and_untaken_supported_quiz(): void
    {
        $f = $this->fixture();
        $service = app(CbtReminderService::class);
        $this->assertFalse($service->deliver($f['other'], $f['assessment']));
        $this->assertTrue($service->deliver($f['learner'], $f['assessment']));
        $this->assertFalse($service->deliver($f['learner'], $f['assessment']));
        $notice = $this->notice($f['learner'], 'cbt_reminder');
        $this->assertSame(route('cbt.exam.take', ['assessment' => $f['assessment']->id]), $notice->data['action_url']);
        $notice->delete();
        $this->assertFalse($service->deliver($f['learner'], $f['assessment']));
        $this->assertDatabaseCount('learning_notification_receipts', 1);
        $f['course']->update(['is_published' => false]);
        $this->assertFalse($service->eligible($f['learner'], $f['assessment']->fresh()));
        $f['course']->update(['is_published' => true]);
        $f['assessment']->update(['course_id' => null]);
        $this->assertFalse($service->eligible($f['learner'], $f['assessment']->fresh()));
    }

    public function test_cbt_reminders_require_takeable_questions_and_recheck_them_before_email(): void
    {
        $f = $this->fixture();
        $service = app(CbtReminderService::class);
        $f['question']->update(['correct_answers' => [0, 1]]);
        $this->assertFalse($service->deliver($f['learner'], $f['assessment']));
        $f['question']->update(['correct_answers' => [0], 'options' => []]);
        $this->assertFalse($service->deliver($f['learner'], $f['assessment']));
        $f['question']->update(['options' => ['First', 'Second']]);
        $f['assessment']->update(['pass_percentage' => 0]);
        $this->assertFalse($service->deliver($f['learner'], $f['assessment']));
        $this->assertDatabaseCount('notifications', 0);
        $f['assessment']->update(['pass_percentage' => 70]);
        config(['mail.default' => 'smtp']);
        $f['learner']->update(['notification_preferences' => ['email_assignment_reminders' => true]]);
        $this->assertTrue($service->deliver($f['learner'], $f['assessment']));
        $notice = $this->notice($f['learner'], 'cbt_reminder');
        $f['question']->update(['correct_answers' => [0, 1]]);
        (new SendPortalNotificationEmail($notice->id))->handle();
        $this->assertSame('cancelled', $notice->fresh()->data['mail_state']);
        Notification::assertNothingSent();
    }

    public function test_cbt_timing_preferences_deadline_changes_and_active_attempts_are_respected(): void
    {
        $f = $this->fixture();
        $service = app(CbtReminderService::class);
        $f['learner']->update(['notification_preferences' => ['reminder_frequency' => 'never']]);
        $this->assertFalse($service->deliver($f['learner'], $f['assessment']));
        $f['learner']->update(['notification_preferences' => ['reminder_frequency' => '1_hour']]);
        $this->assertFalse($service->deliver($f['learner'], $f['assessment']));
        $f['assessment']->update(['deadline' => now()->addMinutes(30)]);
        $this->assertTrue($service->deliver($f['learner'], $f['assessment']));
        app(CbtAttemptService::class)->start($f['learner'], $f['assessment']);
        $this->assertFalse($service->eligible($f['learner'], $f['assessment']->fresh()));
        $this->assertDatabaseCount('notifications', 1);
    }

    public function test_queued_cbt_email_cancels_after_enrollment_or_deadline_changes(): void
    {
        $f = $this->fixture();
        config(['mail.default' => 'smtp']);
        $f['learner']->update(['notification_preferences' => ['email_assignment_reminders' => true]]);
        app(CbtReminderService::class)->deliver($f['learner'], $f['assessment']);
        $notice = $this->notice($f['learner'], 'cbt_reminder');
        $f['assessment']->update(['deadline' => now()->addDays(10)]);
        (new SendPortalNotificationEmail($notice->id))->handle();
        $this->assertSame('cancelled', $notice->fresh()->data['mail_state']);
        Notification::assertNothingSent();
    }

    public function test_course_review_reminder_uses_completed_enrollment_actual_due_time_and_no_duplicate_clock(): void
    {
        $f = $this->fixture();
        $service = app(ReviewReminderService::class);
        $this->assertFalse($service->deliver($f['enrollment']->id));
        $f['enrollment']->update(['is_completed' => true, 'completed_at' => now()->subDays(8)]);
        $this->assertTrue($service->deliver($f['enrollment']->id));
        $reminder = ReviewReminder::firstOrFail();
        $clock = $reminder->getRawOriginal('last_reminded_at');
        $this->assertFalse($service->deliver($f['enrollment']->id));
        $this->assertSame(1, $reminder->fresh()->reminder_count);
        $this->assertSame($clock, $reminder->fresh()->getRawOriginal('last_reminded_at'));
        $this->assertSame('disabled', $this->notice($f['learner'], 'course_review_reminder')->data['mail_state']);
    }

    public function test_course_review_frequency_and_existing_reviews_prevent_unwanted_reminders(): void
    {
        $f = $this->fixture();
        $service = app(ReviewReminderService::class);
        $f['learner']->update(['review_reminder_preferences' => ['frequency' => 'minimal']]);
        $f['enrollment']->update(['is_completed' => true, 'completed_at' => now()->subDays(8)]);
        $this->assertFalse($service->deliver($f['enrollment']->id));
        $f['enrollment']->update(['completed_at' => now()->subDays(15)]);
        $this->assertTrue($service->deliver($f['enrollment']->id));
        $this->travel(100)->days();
        $this->assertFalse($service->deliver($f['enrollment']->id));
        DB::table('course_reviews')->insert(['course_id' => $f['course']->id, 'user_id' => $f['learner']->id, 'rating' => 4, 'comment' => 'Actual fixture feedback', 'created_at' => now(), 'updated_at' => now()]);
        $this->assertFalse($service->emailEligible($f['learner'], ['reminder_id' => ReviewReminder::first()->id]));
    }

    public function test_course_reminder_storage_failure_rolls_back_count_notice_and_new_row(): void
    {
        $f = $this->fixture();
        $f['enrollment']->update(['is_completed' => true, 'completed_at' => now()->subDays(8)]);
        DatabaseNotification::creating(fn () => throw new \RuntimeException('Forced notice failure'));
        try {
            app(ReviewReminderService::class)->deliver($f['enrollment']->id);
            $this->fail('Expected rollback');
        } catch (\RuntimeException $e) {
            $this->assertSame('Forced notice failure', $e->getMessage());
        } finally {
            DatabaseNotification::flushEventListeners();
        }
        $this->assertDatabaseCount('review_reminders', 0);
        $this->assertDatabaseCount('learning_notification_receipts', 0);
    }

    public function test_signed_unsubscribe_requires_expiry_record_bound_token_and_post_confirmation(): void
    {
        $f = $this->fixture();
        $reminder = ReviewReminder::create(['user_id' => $f['learner']->id, 'course_id' => $f['course']->id]);
        $service = app(ReviewReminderService::class);
        $url = $service->unsubscribeUrl($reminder);
        $this->get($url)->assertOk()->assertSee('Stop these reminders')->assertHeader('Cache-Control', 'no-store, private');
        $this->assertFalse($reminder->fresh()->unsubscribed);
        $this->post($url)->assertOk()->assertSee('Course review reminders stopped');
        $at = $reminder->fresh()->getRawOriginal('unsubscribed_at');
        $this->post($url)->assertOk();
        $this->assertSame($at, $reminder->fresh()->getRawOriginal('unsubscribed_at'));
        $invalid = URL::temporarySignedRoute('review-reminder.unsubscribe', now()->addDay(), ['reminder' => $reminder->id, 'token' => str_repeat('a', 64)]);
        $this->post($invalid)->assertForbidden();
        $this->get(route('review-reminder.unsubscribe', $reminder))->assertForbidden();
        $this->travel(31)->days();
        $this->post($url)->assertForbidden();
    }

    public function test_unsubscribe_all_is_owner_scoped_and_applies_to_future_course_reminders(): void
    {
        $f = $this->fixture();
        $f['learner']->update(['review_reminder_preferences' => ['frequency' => 'minimal', 'unrelated' => 'preserved']]);
        $this->get(route('review-reminders.unsubscribe-all'))->assertOk();
        $this->assertArrayNotHasKey('unsubscribed_all', $f['learner']->fresh()->review_reminder_preferences);
        $this->post(route('review-reminders.unsubscribe-all.confirm'))->assertOk();
        $prefs = $f['learner']->fresh()->review_reminder_preferences;
        $this->assertTrue($prefs['unsubscribed_all']);
        $this->assertSame('preserved', $prefs['unrelated']);
        $this->assertNull($f['other']->fresh()->review_reminder_preferences);
        $f['enrollment']->update(['is_completed' => true, 'completed_at' => now()->subDays(100)]);
        $this->assertFalse(app(ReviewReminderService::class)->deliver($f['enrollment']->id));
    }

    public function test_recorded_achievement_delivery_rejects_fabricated_or_foreign_records_and_is_durable(): void
    {
        $f = $this->fixture();
        $achievement = UserAchievement::create(['user_id' => $f['learner']->id, 'achievement_type' => 'lesson_completion', 'achievement_name' => 'Retained achievement', 'achievement_description' => 'A retained actual fixture record', 'achievement_value' => 10, 'earned_at' => now()]);
        $service = app(GamificationService::class);
        $this->assertFalse($service->broadcastAchievement($f['other'], $achievement));
        $this->assertFalse($service->broadcastAchievement($f['learner'], ['achievement_id' => $achievement->id]));
        $this->assertTrue($service->broadcastAchievement($f['learner'], $achievement));
        $this->notice($f['learner'], 'achievement_unlocked')->delete();
        $this->assertFalse($service->broadcastAchievement($f['learner'], $achievement));
        $this->assertDatabaseCount('learning_notification_receipts', 1);
        $this->assertDatabaseCount('gamification_transactions', 0);
        Livewire::actingAs($f['learner'])->test(GamificationDashboard::class, ['activeTab' => 'badges'])->assertSee('Retained achievement');
        Livewire::actingAs($f['other'])->test(GamificationDashboard::class, ['activeTab' => 'badges'])->assertDontSee('Retained achievement');
    }

    public function test_badge_award_is_atomic_and_cannot_override_its_owner(): void
    {
        $f = $this->fixture();
        $data = ['user_id' => $f['other']->id, 'badge_type' => 'learning', 'badge_name' => 'Actual awarded badge', 'badge_description' => 'Recorded in the existing badge model', 'badge_icon' => 'fas fa-award'];
        $badge = UserBadge::awardBadge($f['learner']->id, 'delivery-check', $data);
        UserBadge::awardBadge($f['learner']->id, 'delivery-check', $data);
        $this->assertSame($f['learner']->id, $badge->user_id);
        $this->assertDatabaseCount('user_badges', 1);
        $this->assertSame(1, $f['learner']->notifications()->count());
        $this->assertSame(0, $f['other']->notifications()->count());
    }

    public function test_milestones_use_recorded_completion_instead_of_bare_progress_and_do_not_award_currency(): void
    {
        $f = $this->fixture();
        $f['enrollment']->update(['progress_percentage' => 100]);
        $this->assertSame([], UserAchievement::checkAndAwardAchievements($f['learner']->id));
        $f['enrollment']->update(['is_completed' => true, 'completed_at' => now()]);
        $awarded = UserAchievement::checkAndAwardAchievements($f['learner']->id);
        $this->assertCount(1, $awarded);
        $this->assertSame('First Steps', $awarded[0]->achievement_name);
        $this->assertSame([], UserAchievement::checkAndAwardAchievements($f['learner']->id));
        $this->assertDatabaseCount('gamification_transactions', 0);
        $this->assertDatabaseCount('user_achievements', 1);
    }

    public function test_native_preferences_preserve_current_keys_and_validate_course_review_frequency(): void
    {
        $f = $this->fixture();
        $f['learner']->update(['review_reminder_preferences' => ['unrelated' => 'retained', 'unsubscribed_all' => true]]);
        $component = Livewire::actingAs($f['learner'])->test(NotificationPreferences::class)->assertSet('email_assessment_reviews', false)->assertSet('email_assignment_reminders', false)->assertSet('email_achievement_notifications', false)
            ->set('email_assessment_reviews', true)->call('saveEmailNotifications')->assertHasNoErrors();
        $this->assertTrue($f['learner']->fresh()->shouldReceiveEmailNotification('assessment_review'));
        $component->set('review_reminder_frequency', 'invalid')->call('saveReviewReminderPreferences')->assertHasErrors('review_reminder_frequency');
        $component->set('review_reminder_frequency', 'minimal')->set('review_reminders_enabled', true)->call('saveReviewReminderPreferences')->assertHasNoErrors();
        $prefs = $f['learner']->fresh()->review_reminder_preferences;
        $this->assertSame('retained', $prefs['unrelated']);
        $this->assertFalse($prefs['unsubscribed_all']);
        $this->assertTrue($prefs['email_enabled']);
    }

    public function test_reminder_dry_runs_are_read_only_and_repaired_notification_classes_load_without_output(): void
    {
        $f = $this->fixture();
        $f['enrollment']->update(['is_completed' => true, 'completed_at' => now()->subDays(10)]);
        $this->artisan('bootkode:cbt-reminders', ['--dry-run' => true])->assertSuccessful();
        $this->artisan('reviews:send-reminders', ['--dry-run' => true])->assertSuccessful();
        $this->assertDatabaseCount('notifications', 0);
        $this->assertDatabaseCount('review_reminders', 0);
        $this->assertDatabaseCount('learning_notification_receipts', 0);
        ob_start();
        $loaded = class_exists(\App\Notifications\CbtExamReminder::class) && class_exists(\App\Notifications\CbtAchievementUnlocked::class) && class_exists(\App\Notifications\ReviewReminderNotification::class);
        $output = ob_get_clean();
        $this->assertTrue($loaded);
        $this->assertSame('', $output);
    }

    public function test_partial_manual_grading_does_not_announce_a_final_result(): void
    {
        $f = $this->fixture(true);
        $second = Question::create(['assessment_id' => $f['assessment']->id, 'question_type' => 'essay', 'question_text' => 'Explain another tradeoff', 'points' => 10, 'order' => 2]);
        $session = app(CbtAttemptService::class)->start($f['learner'], $f['assessment'], true);
        app(CbtAttemptService::class)->submit($f['learner'], $f['assessment'], $session->id, [$f['question']->id => 'First original answer', $second->id => 'Second original answer']);
        $answers = StudentAnswer::where('assessment_id', $f['assessment']->id)->orderBy('id')->get();
        $service = app(AssessmentReviewService::class);
        $service->grade($f['teacher'], $session->id, $answers[0]->id, 5);
        $this->assertDatabaseCount('notifications', 0);
        $this->assertTrue($session->fresh()->metadata['results']['pending_review']);
        $service->grade($f['teacher'], $session->id, $answers[1]->id, 5);
        $this->assertSame(1, $f['learner']->notifications()->where('data->type', 'assessment_result')->count());
    }

    public function test_new_review_result_email_uses_the_shared_transport_state_and_does_not_repeat(): void
    {
        $f = $this->fixture(true);
        config(['mail.default' => 'smtp']);
        $f['learner']->update(['notification_preferences' => ['email_assessment_reviews' => true]]);
        [$session,$answer] = $this->finalized($f);
        $notice = $this->notice($f['learner'], 'assessment_result');
        $this->assertSame('pending', $notice->data['mail_state']);
        Queue::assertPushed(SendPortalNotificationEmail::class);
        (new SendPortalNotificationEmail($notice->id))->handle();
        (new SendPortalNotificationEmail($notice->id))->handle();
        $this->assertSame('transport_accepted', $notice->fresh()->data['mail_state']);
        Notification::assertSentToTimes($f['learner'], \App\Notifications\PortalEventNotification::class, 1);
        $this->assertEquals(5, $answer->fresh()->points_earned);
    }

    public function test_a_real_automatic_passing_result_awards_existing_score_milestones_only_once(): void
    {
        $f = $this->fixture();
        $session = app(CbtAttemptService::class)->start($f['learner'], $f['assessment']);
        $result = app(CbtAttemptService::class)->submit($f['learner'], $f['assessment'], $session->id, [$f['question']->id => '0']);
        $this->assertEquals(100, $result['percentage']);
        $this->assertDatabaseCount('user_achievements', 5);
        $this->assertSame(5, $f['learner']->notifications()->where('data->type', 'achievement_unlocked')->count());
        app(GamificationService::class)->handleQuizCompletion($f['learner'], $f['assessment']);
        $this->assertDatabaseCount('user_achievements', 5);
        $this->assertSame(10, (int) $f['learner']->gamificationTransactions()->where('source', 'quiz_completion')->where('currency_type', 'coins')->sum('amount'));
        $this->assertSame(50, (int) $f['learner']->gamificationTransactions()->where('source', 'quiz_completion')->where('currency_type', 'points')->sum('amount'));
    }

    public function test_badge_and_receipt_roll_back_when_notice_storage_fails(): void
    {
        $f = $this->fixture();
        DatabaseNotification::creating(fn () => throw new \RuntimeException('Forced notice failure'));
        try {
            UserBadge::awardBadge($f['learner']->id, 'rollback-badge', ['badge_type' => 'learning', 'badge_name' => 'Actual badge', 'badge_description' => 'Actual award', 'badge_icon' => 'fas fa-award']);
            $this->fail('Expected rollback');
        } catch (\RuntimeException $e) {
            $this->assertSame('Forced notice failure', $e->getMessage());
        } finally {
            DatabaseNotification::flushEventListeners();
        }
        $this->assertDatabaseCount('user_badges', 0);
        $this->assertDatabaseCount('learning_notification_receipts', 0);
    }

    public function test_native_course_review_saves_comment_completes_reminder_and_refuses_foreign_deletion(): void
    {
        $f = $this->fixture();
        $reminder = ReviewReminder::create(['user_id' => $f['learner']->id, 'course_id' => $f['course']->id]);
        $component = Livewire::actingAs($f['learner'])->test(\App\Livewire\StudentManagement\CourseView\Review::class, ['course' => $f['course']])
            ->set('rating', 4)->set('comment', 'My actual retained course review comment.')->call('submitReview')->assertHasNoErrors()->assertSee('My actual retained course review comment.');
        $review = \App\Models\Learning\CourseReview::where('user_id', $f['learner']->id)->firstOrFail();
        $this->assertSame('My actual retained course review comment.', $review->comment);
        $this->assertNotNull($reminder->fresh()->completed_at);
        Livewire::actingAs($f['other'])->test(\App\Livewire\StudentManagement\CourseView\Review::class, ['course' => $f['course']])->call('deleteReview', $review->id);
        $this->assertDatabaseHas('course_reviews', ['id' => $review->id]);
        $this->expectException(\Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException::class);
        $component->set('editingReview', $review->id);
    }

    public function test_course_reminder_model_returns_a_future_due_date_without_mutating_the_saved_clock(): void
    {
        $f = $this->fixture();
        $f['enrollment']->update(['is_completed' => true, 'completed_at' => now()->subDays(20)]);
        $reminder = ReviewReminder::create(['user_id' => $f['learner']->id, 'course_id' => $f['course']->id, 'reminder_count' => 1, 'last_reminded_at' => now()]);
        $before = $reminder->last_reminded_at->toISOString();
        $this->assertFalse($reminder->shouldSendReminder());
        $this->assertSame(now()->addDays(14)->toISOString(), $reminder->getNextReminderDate()->toISOString());
        $this->assertSame($before, $reminder->last_reminded_at->toISOString());
    }

    public function test_review_email_cancels_superseded_reminders_and_honors_a_new_minimal_preference(): void
    {
        $f = $this->fixture();
        config(['mail.default' => 'smtp']);
        $f['learner']->update(['review_reminder_preferences' => ['email_enabled' => true, 'frequency' => 'standard']]);
        $f['enrollment']->update(['is_completed' => true, 'completed_at' => now()->subDays(8)]);
        $service = app(ReviewReminderService::class);
        $this->assertTrue($service->deliver($f['enrollment']->id));
        $first = $this->notice($f['learner'], 'course_review_reminder');
        $this->travel(15)->days();
        $this->assertTrue($service->deliver($f['enrollment']->id));
        $last = $f['learner']->notifications()->where('data->type', 'course_review_reminder')->where('data->reminder_number', 2)->firstOrFail();
        (new SendPortalNotificationEmail($first->id))->handle();
        $this->assertSame('cancelled', $first->fresh()->data['mail_state']);
        $f['learner']->update(['review_reminder_preferences' => ['email_enabled' => true, 'frequency' => 'minimal']]);
        (new SendPortalNotificationEmail($last->id))->handle();
        $this->assertSame('cancelled', $last->fresh()->data['mail_state']);
        Notification::assertNothingSent();
        $this->assertSame(2, ReviewReminder::first()->reminder_count);
    }
}
