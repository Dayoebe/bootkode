<?php

namespace Tests\Feature;

use App\Jobs\ProcessBulkEnrollment;
use App\Jobs\SendNewsletterBatch;
use App\Jobs\SendNewsletterCampaign;
use App\Mail\NewsletterMail;
use App\Models\Admin\BulkEnrollmentBatch;
use App\Models\Admin\InstitutionUser;
use App\Models\Admin\NewsletterCampaign;
use App\Models\Admin\NewsletterInteraction;
use App\Models\Admin\NewsletterSubscriber;
use App\Models\Core\Institution;
use App\Models\Core\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BackgroundWorkloadTest extends TestCase
{
    use RefreshDatabase;

    private function batch(array $lines): BulkEnrollmentBatch
    {
        Storage::fake('local');
        Notification::fake();
        Bus::fake();
        Role::findOrCreate('academy_admin', 'web');
        $editor = User::factory()->create(['role' => 'academy_admin']);
        $institution = Institution::create(['created_by' => $editor->id, 'admin_user_id' => $editor->id,
            'name' => 'Synthetic institution', 'email' => 'institution@example.test', 'status' => 'active',
            'license_type' => 'basic', 'max_users' => 200, 'license_start_date' => now()->subDay(), 'license_end_date' => now()->addYear()]);
        Storage::disk('local')->put('bulk-enrollments/probe.csv', "name,email\n".implode("\n", $lines)."\n");

        return BulkEnrollmentBatch::create(['institution_id' => $institution->id, 'created_by' => $editor->id,
            'name' => 'Synthetic batch', 'file_path' => 'bulk-enrollments/probe.csv', 'original_filename' => 'probe.csv',
            'courses' => [], 'total_records' => count($lines), 'settings' => []]);
    }

    public function test_bulk_slices_checkpoint_and_resume_without_duplicate_memberships_or_completion(): void
    {
        $lines = [];
        for ($i = 0; $i < 101; $i++) {
            $lines[] = 'Fixture '.$i.',fixture'.$i.'@example.test';
        }
        $batch = $this->batch($lines);
        $job = new ProcessBulkEnrollment($batch);
        $this->assertSame('bulk-enrollment', $job->connection);
        $this->assertGreaterThan($job->timeout, config('queue.connections.bulk-enrollment.retry_after'));
        $job->handle();
        $batch->refresh();
        $this->assertGreaterThan(0, $batch->processed_records);
        $this->assertLessThan(101, $batch->processed_records);
        $this->assertSame($batch->processed_records, $batch->settings['resume_row']);
        Bus::assertDispatched(ProcessBulkEnrollment::class);
        for ($i = 0; $i < 20 && $batch->refresh()->status !== 'completed'; $i++) {
            (new ProcessBulkEnrollment($batch->fresh()))->handle();
        }
        $this->assertSame('completed', $batch->fresh()->status);
        $this->assertSame(101, $batch->fresh()->successful_enrollments);
        $this->assertSame(101, InstitutionUser::where('institution_id', $batch->institution_id)->count());
        $notifications = Notification::sent($batch->creator, \App\Notifications\BulkEnrollmentCompleted::class)->count();
        (new ProcessBulkEnrollment($batch->fresh()))->handle();
        $this->assertSame(101, InstitutionUser::where('institution_id', $batch->institution_id)->count());
        $this->assertSame($notifications, Notification::sent($batch->creator, \App\Notifications\BulkEnrollmentCompleted::class)->count());
    }

    public function test_bulk_invalid_row_is_checkpointed_without_storing_its_private_values(): void
    {
        $batch = $this->batch(['Good,good@example.test', 'Private,not-an-email,extra-column']);
        (new ProcessBulkEnrollment($batch))->handle();
        $batch->refresh();
        $this->assertSame(2, $batch->processed_records);
        $this->assertSame(1, $batch->successful_enrollments);
        $this->assertSame(1, $batch->failed_enrollments);
        $this->assertStringNotContainsString('not-an-email', json_encode($batch->errors));
        $this->assertSame(2, $batch->settings['resume_row']);
    }

    public function test_bulk_resumption_refuses_changed_source_and_preserves_cancelled_status(): void
    {
        $batch = $this->batch(['Good,good@example.test']);
        $batch->update(['settings' => ['resume_file_sha256' => hash('sha256', 'different original file')]]);
        try {
            (new ProcessBulkEnrollment($batch))->handle();
            $this->fail('Changed CSV resumed');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('CSV changed', $exception->getMessage());
        }
        $this->assertSame(0, $batch->fresh()->processed_records);
        $batch->update(['status' => 'cancelled']);
        $job = new ProcessBulkEnrollment($batch);
        $job->handle();
        $job->failed(new \RuntimeException('Simulated late failure'));
        $this->assertSame('cancelled', $batch->fresh()->status);
    }

    public function test_bulk_rechecks_the_original_administrators_current_permission(): void
    {
        $batch = $this->batch(['Good,good@example.test']);
        $batch->creator->syncRoles(['student']);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('no longer authorised');
        (new ProcessBulkEnrollment($batch->fresh()))->handle();
    }

    private function campaign(): NewsletterCampaign
    {
        return NewsletterCampaign::create(['name' => 'Synthetic campaign', 'subject' => 'Fixture message', 'html_content' => '<p>A synthetic message.</p>',
            'from_name' => 'Synthetic sender', 'from_email' => 'sender@example.test',
            'type' => 'campaign', 'status' => 'scheduled', 'created_by' => User::factory()->create()->id]);
    }

    private function subscriber(string $email = 'subscriber@example.test'): NewsletterSubscriber
    {
        return NewsletterSubscriber::create(['email' => $email, 'name' => 'Synthetic subscriber', 'status' => 'active']);
    }

    public function test_newsletter_replay_reuses_interactions_and_never_double_counts_a_delivery(): void
    {
        Bus::fake();
        Mail::fake();
        $campaign = $this->campaign();
        $subscriber = $this->subscriber();
        (new SendNewsletterCampaign($campaign))->handle();
        (new SendNewsletterCampaign($campaign))->handle();
        $this->assertDatabaseCount('newsletter_interactions', 1);
        $send = NewsletterInteraction::firstOrFail();
        $job = new SendNewsletterBatch($campaign, [$send->id]);
        $job->handle();
        $job->handle();
        Mail::assertSent(NewsletterMail::class, 1);
        $this->assertSame(1, (int) $campaign->fresh()->sent_count);
        $this->assertSame('completed', $send->fresh()->status);
        $this->assertSame('sent', $campaign->fresh()->status);
    }

    public function test_newsletter_rechecks_unsubscription_before_sending(): void
    {
        Mail::fake();
        $campaign = $this->campaign();
        $subscriber = $this->subscriber();
        $send = NewsletterInteraction::createSendRecord($campaign->id, $subscriber->id);
        $subscriber->update(['status' => 'unsubscribed']);
        (new SendNewsletterBatch($campaign, [$send->id]))->handle();
        Mail::assertNothingSent();
        $this->assertSame('cancelled', $send->fresh()->status);
    }

    public function test_newsletter_transport_failure_requires_reconciliation_and_is_never_resent_automatically(): void
    {
        $campaign = $this->campaign();
        $send = NewsletterInteraction::createSendRecord($campaign->id, $this->subscriber()->id);
        Mail::shouldReceive('to')->once()->andThrow(new \RuntimeException('private-transport-credential-marker'));
        $job = new SendNewsletterBatch($campaign, [$send->id]);
        foreach ([1, 2] as $attempt) {
            try {
                $job->handle();
                $this->fail('Uncertain SMTP state silently accepted');
            } catch (\RuntimeException $exception) {
                $this->assertStringNotContainsString('private-transport-credential-marker', $exception->getMessage());
            }
        }
        $this->assertSame('delivery_unknown', $send->fresh()->status);
        $this->assertStringNotContainsString('private-transport-credential-marker', $send->fresh()->error_message);
        $this->assertSame(0, (int) $campaign->fresh()->sent_count);
    }

    public function test_legacy_multi_recipient_job_is_split_before_any_smtp_calls(): void
    {
        Bus::fake();
        Mail::fake();
        $job = new SendNewsletterBatch($this->campaign(), [1, 2, 3]);
        $job->handle();
        Bus::assertDispatchedTimes(SendNewsletterBatch::class, 3);
        Mail::assertNothingSent();
    }
}
