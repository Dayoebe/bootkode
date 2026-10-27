<?php

namespace Tests\Feature;

use App\Jobs\SendPortalNotificationEmail;
use App\Livewire\Career\JobPortal as JobPortalComponent;
use App\Livewire\Community\CommunityCenter;
use App\Models\Career\JobAlert;
use App\Models\Career\JobPortal;
use App\Models\Community\CommunityFeedback;
use App\Models\Core\User;
use App\Notifications\PortalEventNotification;
use App\Services\CommunityFeedbackResponseService;
use App\Services\JobAlertService;
use App\Services\PortalNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class NotificationDeliveryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.timezone' => 'UTC']);
        $this->travelTo(\Illuminate\Support\Carbon::parse('2026-10-27 12:00:00', 'UTC'));
        Notification::fake();
        Queue::fake([SendPortalNotificationEmail::class]);
        config(['mail.default' => 'array']);
    }

    protected function tearDown(): void
    {
        $this->travelBack();
        parent::tearDown();
    }

    private function fixture(): array
    {
        Role::findOrCreate('super_admin', 'web');
        Role::findOrCreate('student', 'web');
        $admin = User::factory()->create(['role' => 'super_admin']);
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $feedback = CommunityFeedback::create(['user_id' => $owner->id, 'subject' => 'Private community request', 'message' => 'PRIVATE REQUEST CONTENT', 'status' => 'open']);
        $this->actingAs($owner);

        return compact('admin', 'owner', 'other', 'feedback');
    }

    private function respond(array $f, string $text = 'PRIVATE RETAINED STAFF REPLY'): CommunityFeedback
    {
        return app(CommunityFeedbackResponseService::class)->respond($f['admin'], $f['feedback']->id, $text);
    }

    private function job(array $f, array $attributes = []): JobPortal
    {
        return JobPortal::create(array_merge(['posted_by' => $f['admin']->id, 'title' => 'Laravel developer opening', 'description' => 'A genuine fixture description for controlled career tests.', 'company_name' => 'Isolated test employer', 'category' => 'engineering', 'location' => 'Lagos', 'status' => 'active', 'is_public' => true, 'application_method' => 'internal', 'employment_type' => 'full-time', 'work_type' => 'remote', 'experience_level' => 'mid', 'salary_currency' => 'NGN', 'salary_min' => 100000, 'salary_max' => 200000, 'hide_salary' => false], $attributes));
    }

    private function alert(array $f, array $criteria = []): JobAlert
    {
        return app(JobAlertService::class)->create($f['owner'], array_merge(['title' => 'My saved jobs', 'keywords' => ['Laravel'], 'frequency' => 'daily'], $criteria));
    }

    private function denied(callable $action, int $status): void
    {
        try {
            $action();
            $this->fail('Expected refusal');
        } catch (HttpException $e) {
            $this->assertSame($status, $e->getStatusCode());
        }
    }

    public function test_reply_and_real_portal_notification_are_atomic_owned_and_replay_safe(): void
    {
        $f = $this->fixture();
        $reply = $this->respond($f);
        $this->respond($f);
        $this->assertSame($f['admin']->id, $reply->responded_by);
        $this->assertNotNull($reply->responded_at);
        $this->assertSame('in_progress', $reply->status);
        $this->assertSame(1, $f['owner']->notifications()->count());
        $this->assertSame(0, $f['other']->notifications()->count());
        $notice = $f['owner']->notifications()->first();
        $this->assertSame('disabled', $notice->data['mail_state']);
        $this->assertStringNotContainsString('PRIVATE', json_encode($notice->data));
        $this->denied(fn () => $this->respond($f, 'A changed replacement cannot erase the recorded response.'), 409);
        Queue::assertNothingPushed();
    }

    public function test_reply_and_notification_roll_back_when_notification_storage_fails(): void
    {
        $f = $this->fixture();
        DatabaseNotification::creating(fn () => throw new \RuntimeException('Simulated write failure'));
        try {
            $this->respond($f);
            $this->fail('Expected rollback');
        } catch (\RuntimeException $e) {
            $this->assertSame('Simulated write failure', $e->getMessage());
        } finally {
            DatabaseNotification::flushEventListeners();
        }
        $this->assertNull($f['feedback']->fresh()->admin_response);
        $this->assertNull($f['feedback']->fresh()->responded_at);
        $this->assertSame(0, $f['owner']->notifications()->count());
    }

    public function test_feedback_replies_are_admin_only_and_current_roles_are_rechecked(): void
    {
        $f = $this->fixture();
        $this->denied(fn () => app(CommunityFeedbackResponseService::class)->respond($f['other'], $f['feedback']->id, 'An unauthorized forged reply.'), 403);
        $f['admin']->syncRoles([]);
        $f['admin']->update(['role' => 'student']);
        $this->denied(fn () => $this->respond($f), 403);
        $this->assertNull($f['feedback']->fresh()->admin_response);
    }

    public function test_requester_sees_the_recorded_reply_but_other_users_do_not(): void
    {
        $f = $this->fixture();
        $this->respond($f);
        Livewire::actingAs($f['owner'])->test(CommunityCenter::class)->call('setTab', 'feedback')->assertSee('PRIVATE RETAINED STAFF REPLY');
        Livewire::actingAs($f['other'])->test(CommunityCenter::class)->call('setTab', 'feedback')->assertDontSee('PRIVATE RETAINED STAFF REPLY')->assertDontSee('PRIVATE REQUEST CONTENT');
    }

    public function test_real_reply_form_resets_context_and_revoked_staff_cannot_render_it(): void
    {
        $f = $this->fixture();
        $component = Livewire::actingAs($f['admin'])->test(CommunityCenter::class)->call('respondToFeedback', $f['feedback']->id)
            ->assertSet('feedbackReplyId', $f['feedback']->id)->set('feedbackReplyText', 'A real retained response through the native component.')->call('saveFeedbackReply')
            ->assertSet('showFeedbackReplyModal', false)->assertSet('feedbackReplyId', null);
        $this->assertSame('A real retained response through the native component.', $f['feedback']->fresh()->admin_response);
        $component->call('respondToFeedback', $f['feedback']->id);
        $f['admin']->syncRoles([]);
        $f['admin']->update(['role' => 'student']);
        $component->set('search', 'revoked')->assertForbidden();
    }

    public function test_feedback_resolution_does_not_fabricate_a_response_timestamp(): void
    {
        $f = $this->fixture();
        Livewire::actingAs($f['admin'])->test(CommunityCenter::class)->call('resolveFeedback', $f['feedback']->id);
        $this->assertSame('resolved', $f['feedback']->fresh()->status);
        $this->assertNull($f['feedback']->fresh()->responded_at);
    }

    public function test_opt_in_without_real_mail_transport_has_truthful_unavailable_state(): void
    {
        $f = $this->fixture();
        app(PortalNotificationService::class)->setEmailPreference($f['owner'], 'community_feedback_response', true);
        $this->respond($f);
        $this->assertSame('unavailable', $f['owner']->notifications()->first()->data['mail_state']);
        Queue::assertNothingPushed();
        Notification::assertNothingSent();
    }

    public function test_queued_mail_checks_current_preferences_and_does_not_resend_accepted_transport(): void
    {
        $f = $this->fixture();
        config(['mail.default' => 'smtp']);
        app(PortalNotificationService::class)->setEmailPreference($f['owner'], 'community_feedback_response', true);
        $this->respond($f);
        $notice = $f['owner']->notifications()->first();
        $this->assertSame('pending', $notice->data['mail_state']);
        Queue::assertPushed(SendPortalNotificationEmail::class);
        (new SendPortalNotificationEmail($notice->id))->handle();
        (new SendPortalNotificationEmail($notice->id))->handle();
        Notification::assertSentToTimes($f['owner'], PortalEventNotification::class, 1);
        $this->assertSame('transport_accepted', $notice->fresh()->data['mail_state']);
    }

    public function test_revoked_email_preference_and_removed_source_cancel_pending_mail(): void
    {
        $f = $this->fixture();
        config(['mail.default' => 'smtp']);
        app(PortalNotificationService::class)->setEmailPreference($f['owner'], 'community_feedback_response', true);
        $this->respond($f);
        $notice = $f['owner']->notifications()->first();
        app(PortalNotificationService::class)->setEmailPreference($f['owner'], 'community_feedback_response', false);
        (new SendPortalNotificationEmail($notice->id))->handle();
        $this->assertSame('disabled', $notice->fresh()->data['mail_state']);
        Notification::assertNothingSent();
    }

    public function test_mail_failure_is_retryable_and_does_not_expose_transport_credentials(): void
    {
        $f = $this->fixture();
        config(['mail.default' => 'smtp']);
        app(PortalNotificationService::class)->setEmailPreference($f['owner'], 'community_feedback_response', true);
        $this->respond($f);
        $notice = $f['owner']->notifications()->first();
        Notification::shouldReceive('sendNow')->once()->andThrow(new \RuntimeException('PRIVATE SMTP PASSWORD and provider body'));
        $job = new SendPortalNotificationEmail($notice->id);
        try {
            $job->handle();
            $this->fail('Expected transport failure');
        } catch (\RuntimeException $e) {
            $this->assertSame('Notification email transport failed.', $e->getMessage());
            $this->assertNull($e->getPrevious());
        }
        $this->assertSame('retry', $notice->fresh()->data['mail_state']);
        $this->assertStringNotContainsString('PRIVATE', json_encode($notice->fresh()->data));
        $job->failed(null);
        $this->assertSame('failed', $notice->fresh()->data['mail_state']);
    }

    public function test_new_mail_preferences_merge_without_overwriting_other_settings(): void
    {
        $f = $this->fixture();
        $f['owner']->update(['notification_preferences' => ['email_course_updates' => false]]);
        app(PortalNotificationService::class)->setEmailPreference($f['owner'], 'job_alert', true);
        $this->assertSame(['email_course_updates' => false, 'email_job_alerts' => true], $f['owner']->fresh()->notification_preferences);
        $this->denied(fn () => app(PortalNotificationService::class)->setEmailPreference($f['other'], 'job_alert', true), 403);
    }

    public function test_alert_creation_uses_actual_owner_and_reuses_equivalent_filters(): void
    {
        $f = $this->fixture();
        $a = $this->alert($f, ['user_id' => $f['other']->id]);
        $b = $this->alert($f, ['title' => 'A different display name']);
        $this->assertSame($f['owner']->id, $a->user_id);
        $this->assertSame($a->id, $b->id);
        $this->assertDatabaseCount('job_alerts', 1);
        $this->assertNull($a->last_sent_at);
        $this->assertSame(0, $f['owner']->notifications()->count());
    }

    public function test_public_filters_currency_and_hidden_salary_are_enforced_before_recording_matches(): void
    {
        $f = $this->fixture();
        $good = $this->job($f);
        $this->job($f, ['is_public' => false]);
        $this->job($f, ['status' => 'draft']);
        $this->job($f, ['expires_at' => now()->subDay()]);
        $this->job($f, ['application_deadline' => now()->subDay()]);
        $this->job($f, ['hide_salary' => true]);
        $this->job($f, ['salary_currency' => 'USD']);
        $a = $this->alert($f, ['salary_min' => 0, 'salary_max' => 300000, 'salary_currency' => 'NGN']);
        $this->assertSame(1, app(JobAlertService::class)->checkOwn($f['owner'], $a->id));
        $notice = $f['owner']->notifications()->first();
        $this->assertSame([$good->id], $notice->data['job_ids']);
        $this->assertSame(1, DB::table('job_alert_deliveries')->count());
        $this->assertNotNull($a->fresh()->last_sent_at);
        $this->assertSame(0, app(JobAlertService::class)->checkOwn($f['owner'], $a->id));
        $this->assertSame(1, $f['owner']->notifications()->count());
    }

    public function test_empty_matches_do_not_claim_delivery_or_advance_the_clock(): void
    {
        $f = $this->fixture();
        $a = $this->alert($f);
        $this->assertSame(0, app(JobAlertService::class)->checkOwn($f['owner'], $a->id));
        $this->assertNull($a->fresh()->last_sent_at);
        $this->assertDatabaseCount('job_alert_deliveries', 0);
        $this->assertSame(0, $f['owner']->notifications()->count());
    }

    public function test_alert_receipts_prevent_duplicates_after_a_notification_is_opened(): void
    {
        $f = $this->fixture();
        $this->job($f);
        $a = $this->alert($f);
        app(JobAlertService::class)->checkOwn($f['owner'], $a->id);
        $f['owner']->notifications()->delete();
        $this->assertSame(0, app(JobAlertService::class)->checkOwn($f['owner'], $a->id));
        $this->assertDatabaseCount('job_alert_deliveries', 1);
        $this->assertSame(0, $f['owner']->notifications()->count());
    }

    public function test_later_publication_of_an_old_draft_is_not_lost_by_a_timestamp_cursor(): void
    {
        $f = $this->fixture();
        $draft = $this->job($f, ['status' => 'draft']);
        $draft->forceFill(['created_at' => now()->subMonth()])->save();
        $this->assertTrue($draft->fresh()->created_at->lt(now()->subWeek()));
        $this->job($f);
        $a = $this->alert($f);
        app(JobAlertService::class)->checkOwn($f['owner'], $a->id);
        $draft->update(['status' => 'active']);
        $this->assertSame(1, app(JobAlertService::class)->checkOwn($f['owner'], $a->id));
        $this->assertDatabaseCount('job_alert_deliveries', 2);
    }

    public function test_paused_inactive_and_other_owners_cannot_trigger_saved_alert_delivery(): void
    {
        $f = $this->fixture();
        $a = $this->alert($f);
        $this->job($f);
        app(JobAlertService::class)->pause($f['owner'], $a->id, false);
        $this->assertSame(0, app(JobAlertService::class)->checkOwn($f['owner'], $a->id));
        try {
            app(JobAlertService::class)->checkOwn($f['other'], $a->id);
            $this->fail('Other owner accepted');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->assertSame(JobAlert::class, $e->getModel());
        }
        $f['owner']->update(['is_active' => false]);
        $this->denied(fn () => app(JobAlertService::class)->checkOwn($f['owner'], $a->id), 403);
    }

    public function test_delivery_rolls_back_receipts_clock_and_notification_together(): void
    {
        $f = $this->fixture();
        $a = $this->alert($f);
        $this->job($f);
        JobAlert::updating(fn () => throw new \RuntimeException('Simulated alert write failure'));
        try {
            app(JobAlertService::class)->checkOwn($f['owner'], $a->id);
            $this->fail('Expected rollback');
        } catch (\RuntimeException $e) {
            $this->assertSame('Simulated alert write failure', $e->getMessage());
        } finally {
            JobAlert::flushEventListeners();
        }
        $this->assertNull($a->fresh()->last_sent_at);
        $this->assertDatabaseCount('job_alert_deliveries', 0);
        $this->assertSame(0, $f['owner']->notifications()->count());
    }

    public function test_job_portal_refuses_raw_sort_injection_and_loads_only_owned_saved_filters(): void
    {
        $f = $this->fixture();
        $a = $this->alert($f);
        Livewire::actingAs($f['owner'])->test(JobPortalComponent::class)->call('loadSavedAlert', $a->id)->assertSet('searchTerm', 'Laravel')
            ->set('sortDirection', 'desc, (select 1)')->assertHasErrors('sortDirection')->assertSet('sortDirection', 'desc');
        Livewire::actingAs($f['other'])->test(JobPortalComponent::class)->call('loadSavedAlert', $a->id)->assertSet('searchTerm', '');
    }

    public function test_dispatch_dry_run_and_default_email_recovery_do_not_change_records(): void
    {
        $f = $this->fixture();
        $a = $this->alert($f);
        $this->job($f);
        $this->artisan('bootkode:job-alerts', ['--dry-run' => true])->assertSuccessful();
        $this->assertNull($a->fresh()->last_sent_at);
        $this->assertDatabaseCount('notifications', 0);
        $this->artisan('bootkode:notification-email')->assertSuccessful();
        Queue::assertNothingPushed();
    }

    public function test_bounded_delivery_keeps_remaining_matches_for_later_checks(): void
    {
        $f = $this->fixture();
        for ($i = 0; $i < 51; $i++) {
            $this->job($f, ['title' => 'Laravel opening '.$i]);
        }$a = $this->alert($f);
        $this->assertSame(50, app(JobAlertService::class)->checkOwn($f['owner'], $a->id));
        $first = $f['owner']->notifications()->first();
        $this->assertSame(50, $first->data['match_count']);
        $this->assertSame(1, $first->data['remaining_count']);
        $this->assertSame(1, app(JobAlertService::class)->checkOwn($f['owner'], $a->id));
        $this->assertSame(0, app(JobAlertService::class)->checkOwn($f['owner'], $a->id));
        $this->assertDatabaseCount('job_alert_deliveries', 51);
        $this->assertSame(2, $f['owner']->notifications()->count());
    }

    public function test_expired_processing_lease_can_recover_without_repeating_an_accepted_mail(): void
    {
        $f = $this->fixture();
        config(['mail.default' => 'smtp']);
        app(PortalNotificationService::class)->setEmailPreference($f['owner'], 'community_feedback_response', true);
        $this->respond($f);
        $notice = $f['owner']->notifications()->first();
        $notice->update(['data' => array_merge($notice->data, ['mail_state' => 'processing', 'processing_until' => now()->subMinute()->toISOString()])]);
        (new SendPortalNotificationEmail($notice->id))->handle();
        $this->assertSame('transport_accepted', $notice->fresh()->data['mail_state']);
        Notification::assertSentToTimes($f['owner'], PortalEventNotification::class, 1);
    }

    public function test_new_notification_links_must_stay_on_the_application_origin(): void
    {
        $f = $this->fixture();
        $this->denied(fn () => DB::transaction(fn () => app(PortalNotificationService::class)->record($f['owner'], 'forged',
            ['title' => 'Forged', 'message' => 'Forged external action', 'action_url' => 'https://external.example.test/private'], 'job_alert')), 422);
        $this->assertSame(0, $f['owner']->notifications()->count());
    }

    public function test_job_alert_limit_and_criteria_validation_preserve_existing_records(): void
    {
        $f = $this->fixture();
        for ($i = 0; $i < 10; $i++) {
            $this->alert($f, ['keywords' => ['term'.$i]]);
        }
        $this->denied(fn () => $this->alert($f, ['keywords' => ['one more']]), 422);
        $this->assertDatabaseCount('job_alerts', 10);
        try {
            $this->alert($f, ['salary_min' => 200, 'salary_max' => 100, 'salary_currency' => 'NGN']);
            $this->fail('Invalid range accepted');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->assertArrayHasKey('salary_max', $e->errors());
        }
        $this->assertDatabaseCount('job_alerts', 10);
    }

    public function test_optional_email_waits_for_quiet_hours_and_weekend_preferences(): void
    {
        $f = $this->fixture();
        config(['mail.default' => 'smtp']);
        $f['owner']->update(['notification_preferences' => ['email_community_feedback_replies' => true, 'quiet_hours_start' => '22:00', 'quiet_hours_end' => '07:00', 'weekend_notifications' => false]]);
        $this->travelTo(\Illuminate\Support\Carbon::parse('2026-10-24 23:00:00', 'UTC'));
        $this->respond($f);
        $notice = $f['owner']->notifications()->first();
        (new SendPortalNotificationEmail($notice->id))->handle();
        Notification::assertNothingSent();
        $this->assertSame('pending', $notice->fresh()->data['mail_state']);
        $this->assertSame('2026-10-26T07:00:00.000000Z', $notice->fresh()->data['available_at']);
        $this->travelTo(\Illuminate\Support\Carbon::parse('2026-10-26 08:00:00', 'UTC'));
        (new SendPortalNotificationEmail($notice->id))->handle();
        Notification::assertSentToTimes($f['owner'], PortalEventNotification::class, 1);
        $this->assertSame('transport_accepted', $notice->fresh()->data['mail_state']);
    }

    public function test_alert_frequency_changes_and_removal_remain_owned(): void
    {
        $f = $this->fixture();
        $a = $this->alert($f);
        Livewire::actingAs($f['owner'])->test(JobPortalComponent::class)->call('setJobAlertFrequency', $a->id, 'weekly')->assertSee('Saved alert frequency updated');
        $this->assertSame('weekly', $a->fresh()->frequency);
        try {
            app(JobAlertService::class)->remove($f['other'], $a->id);
            $this->fail('Other owner removed alert');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->assertSame(JobAlert::class, $e->getModel());
        }
        app(JobAlertService::class)->remove($f['owner'], $a->id);
        $this->assertDatabaseMissing('job_alerts', ['id' => $a->id]);
    }

    public function test_queued_job_mail_rechecks_recorded_matches_against_the_current_deadline(): void
    {
        $f = $this->fixture();
        config(['mail.default' => 'smtp']);
        app(PortalNotificationService::class)->setEmailPreference($f['owner'], 'job_alert', true);
        $alert = $this->alert($f);
        $job = $this->job($f);
        app(JobAlertService::class)->checkOwn($f['owner'], $alert->id);
        $notice = $f['owner']->notifications()->first();
        $job->update(['application_deadline' => now()->subMinute()]);
        (new SendPortalNotificationEmail($notice->id))->handle();
        $this->assertSame('cancelled', $notice->fresh()->data['mail_state']);
        Notification::assertNothingSent();
        $this->assertDatabaseCount('job_alert_deliveries', 1);
    }

    public function test_central_preferences_load_local_opt_ins_and_preserve_unrelated_current_preferences(): void
    {
        $f = $this->fixture();
        $f['owner']->update(['notification_preferences' => ['email_job_alerts' => true, 'email_community_feedback_replies' => true, 'email_instructor_replies' => false, 'unrelated' => 'retained']]);
        $component = Livewire::actingAs($f['owner'])->test(\App\Livewire\SystemManagement\NotificationPreferences::class)
            ->assertSet('email_job_alerts', true)->assertSet('email_community_feedback_replies', true)->assertSet('email_instructor_replies', false);
        $f['owner']->update(['notification_preferences' => array_merge($f['owner']->notification_preferences, ['concurrent' => 'retained'])]);
        $component->call('disableAllEmail')->call('saveEmailNotifications')->assertHasNoErrors();
        $preferences = $f['owner']->fresh()->notification_preferences;
        $this->assertFalse($preferences['email_job_alerts']);
        $this->assertFalse($preferences['email_community_feedback_replies']);
        $this->assertFalse($preferences['email_instructor_replies']);
        $this->assertSame('retained', $preferences['concurrent']);
        $this->assertSame('retained', $preferences['unrelated']);
    }

    public function test_native_frequency_form_validates_times_and_retains_email_choices(): void
    {
        $f = $this->fixture();
        $f['owner']->update(['notification_preferences' => ['email_job_alerts' => true]]);
        $component = Livewire::actingAs($f['owner'])->test(\App\Livewire\SystemManagement\NotificationPreferences::class)
            ->set('quiet_hours_start', '29:00')->call('saveFrequencySettings')->assertHasErrors('quiet_hours_start');
        $component->set('quiet_hours_start', '21:00')->set('quiet_hours_end', '08:00')->set('weekend_notifications', false)->call('saveFrequencySettings')->assertHasNoErrors();
        $preferences = $f['owner']->fresh()->notification_preferences;
        $this->assertSame('21:00', $preferences['quiet_hours_start']);
        $this->assertFalse($preferences['weekend_notifications']);
        $this->assertTrue($preferences['email_job_alerts']);
        $component->call('testNotification', 'email')->assertDispatched('notify', 'Test delivery is unavailable. No notification was sent.', 'error');
        Notification::assertNothingSent();
        $f['owner']->update(['is_active' => false]);
        $component->call('saveEmailNotifications')->assertForbidden();
    }
}
