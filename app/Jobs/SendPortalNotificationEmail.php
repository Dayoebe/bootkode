<?php

namespace App\Jobs;

use App\Models\Core\User;
use App\Notifications\PortalEventNotification;
use App\Services\PortalNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class SendPortalNotificationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 4;

    public int $timeout = 60;

    public function __construct(public string $notificationId)
    {
        $this->afterCommit();
    }

    public function backoff(): array
    {
        return [60, 300];
    }

    public function handle(): void
    {
        $payload = DB::transaction(function () {
            $record = DatabaseNotification::whereKey($this->notificationId)->lockForUpdate()->first();
            if (! $record) {
                return null;
            }
            if (($record->data['mail_state'] ?? '') === 'processing') {
                $expires = \Illuminate\Support\Carbon::parse($record->data['processing_until'] ?? now());
                if ($expires->isFuture()) {
                    return ['defer' => max(1, (int) now()->diffInSeconds($expires))];
                }
            } elseif (! in_array($record->data['mail_state'] ?? '', ['pending', 'not_queued', 'retry'], true)) {
                return null;
            }
            $user = $record->notifiable;
            if (! $user instanceof User || ! $user->is_active || ! $user->email_verified_at || ! $user->shouldReceiveEmailNotification($record->data['preference'])) {
                $record->update(['data' => array_merge($record->data, ['mail_state' => 'disabled'])]);

                return null;
            }
            $source = match ($record->data['type'] ?? '') {
                'community_feedback_response' => \App\Models\Community\CommunityFeedback::whereKey($record->data['feedback_id'] ?? 0)->where('user_id', $user->id)->whereNotNull('admin_response')->exists(),
                'job_alert' => (bool) \App\Models\Career\JobAlert::whereKey($record->data['job_alert_id'] ?? 0)->where('user_id', $user->id)->where('is_active', true)->first()
                    ?->matchingJobs(false)->whereIn('id', $record->data['job_ids'] ?? [])->exists(),
                default => app(\App\Services\LearningNotificationService::class)->sourceAvailable($user, $record->data),
            };
            if (! $source) {
                $record->update(['data' => array_merge($record->data, ['mail_state' => 'cancelled'])]);

                return null;
            }
            if (! app(PortalNotificationService::class)->emailAvailable()) {
                $record->update(['data' => array_merge($record->data, ['mail_state' => 'unavailable'])]);

                return null;
            }
            if ($window = app(PortalNotificationService::class)->nextEmailWindow($user)) {
                $record->update(['data' => array_merge($record->data, ['mail_state' => 'pending', 'available_at' => $window->toISOString()])]);

                return ['defer' => max(1, (int) now()->diffInSeconds($window))];
            }
            $record->update(['data' => array_merge($record->data, ['mail_state' => 'processing', 'processing_until' => now()->addSeconds(120)->toISOString()])]);

            return [$user, $record->data];
        });
        if (! $payload) {
            return;
        }
        if (isset($payload['defer'])) {
            $this->release($payload['defer']);

            return;
        }
        try {
            Notification::sendNow($payload[0], new PortalEventNotification($payload[1]), ['mail']);
            $this->state('transport_accepted');
        } catch (\Throwable) {
            $this->state('retry');
            throw new \RuntimeException('Notification email transport failed.');
        }
    }

    private function state(string $state, array $expected = ['processing']): void
    {
        DB::transaction(function () use ($state, $expected) {
            $record = DatabaseNotification::whereKey($this->notificationId)->lockForUpdate()->first();
            if ($record && in_array($record->data['mail_state'] ?? '', $expected, true)) {
                $record->update(['data' => array_merge($record->data, ['mail_state' => $state])]);
            }
        });
    }

    public function failed(?\Throwable $exception): void
    {
        $this->state('failed', ['pending', 'not_queued', 'processing', 'retry']);
    }
}
