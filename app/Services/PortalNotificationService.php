<?php

namespace App\Services;

use App\Jobs\SendPortalNotificationEmail;
use App\Models\Core\User;
use App\Notifications\PortalEventNotification;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class PortalNotificationService
{
    public function emailAvailable(): bool
    {
        $driver = config('mail.default');

        return in_array(config('mail.mailers.'.$driver.'.transport'), ['smtp', 'sendmail', 'ses', 'ses-v2', 'postmark', 'mailgun', 'resend'], true);
    }

    public function nextEmailWindow(User $user): ?\Illuminate\Support\Carbon
    {
        $savedZone = $user->getAttributes()['timezone'] ?? null;
        $zone = in_array($savedZone, timezone_identifiers_list(), true) ? $savedZone : config('app.timezone', 'UTC');
        $now = now($zone);
        $target = $now->copy();
        $preferences = $user->notification_preferences ?? [];
        $weekends = $preferences['weekend_notifications'] ?? true;
        $start = $preferences['quiet_hours_start'] ?? '22:00';
        $end = $preferences['quiet_hours_end'] ?? '07:00';
        $valid = is_string($start) && is_string($end) && preg_match('/^(?:[01][0-9]|2[0-3]):[0-5][0-9]$/', $start) && preg_match('/^(?:[01][0-9]|2[0-3]):[0-5][0-9]$/', $end);
        for ($day = 0; $day < 8; $day++) {
            if (! $weekends && $target->isWeekend()) {
                $target->next(\Carbon\Carbon::MONDAY)->startOfDay();

                continue;
            }
            $time = $target->format('H:i');
            if ($valid && $start !== $end) {
                if ($start > $end && $time >= $start) {
                    $target->addDay()->setTimeFromTimeString($end);

                    continue;
                }
                if (($start > $end && $time < $end) || ($start < $end && $time >= $start && $time < $end)) {
                    $target->setTimeFromTimeString($end);

                    continue;
                }
            }
            break;
        }

        return $target->gt($now) ? $target : null;
    }

    public function setEmailPreference(User $user, string $type, bool $enabled): void
    {
        abort_unless((int) auth()->id() === (int) $user->id, 403);
        $keys = ['job_alert' => 'email_job_alerts', 'community_feedback_response' => 'email_community_feedback_replies'];
        abort_unless(isset($keys[$type]), 422);
        DB::transaction(function () use ($user, $type, $enabled, $keys) {
            $record = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            abort_unless($record->is_active && $record->email_verified_at, 403);
            abort_unless($record->notification_preferences === null || is_array($record->notification_preferences), 422, 'Existing notification preferences need review.');
            $record->update(['notification_preferences' => array_merge($record->notification_preferences ?? [], [$keys[$type] => $enabled])]);
        });
    }

    /** Call within the source transaction so a portal alert cannot outlive a failed source write. */
    public function record(User $recipient, string $event, array $payload, string $preference): DatabaseNotification
    {
        if (DB::transactionLevel() < 1) {
            throw new \LogicException('Record portal notifications inside their source transaction.');
        }
        $target = parse_url($payload['action_url'] ?? '');
        $configuredHost = parse_url((string) config('app.url'), PHP_URL_HOST);
        $hosts = array_filter([$configuredHost]);
        $loopbackHosts = ['localhost', '127.0.0.1', '::1'];
        if (app()->environment(['local', 'testing']) && in_array($configuredHost, $loopbackHosts, true)) {
            $hosts = array_merge($hosts, $loopbackHosts);
        }
        abort_unless(is_array($target) && in_array($target['scheme'] ?? '', ['http', 'https'], true) && in_array($target['host'] ?? '', $hosts, true), 422, 'Use an application notification link.');
        $payload['title'] = strip_tags((string) $payload['title']);
        $payload['message'] = strip_tags((string) $payload['message']);
        $id = Uuid::uuid5(Uuid::NAMESPACE_URL, 'bootkode:'.$recipient->id.':'.$event)->toString();
        $existing = $recipient->notifications()->find($id);
        if ($existing) {
            return $existing;
        }
        $mail = ! $recipient->is_active || ! $recipient->email_verified_at || ! $recipient->shouldReceiveEmailNotification($preference)
            ? 'disabled' : ($this->emailAvailable() ? 'pending' : 'unavailable');
        $record = $recipient->notifications()->create(['id' => $id, 'type' => PortalEventNotification::class,
            'data' => array_merge($payload, ['preference' => $preference, 'mail_state' => $mail])]);
        if ($mail === 'pending') {
            DB::afterCommit(fn () => $this->queueEmail($id));
        }

        return $record;
    }

    public function queueEmail(string $id): bool
    {
        try {
            SendPortalNotificationEmail::dispatch($id);

            return true;
        } catch (\Throwable) {
            $record = DatabaseNotification::find($id);
            if ($record && ($record->data['mail_state'] ?? '') !== 'transport_accepted') {
                $record->update(['data' => array_merge($record->data, ['mail_state' => 'not_queued'])]);
            }
            Log::warning('Optional portal notification email could not be queued.', ['notification_id' => $id]);

            return false;
        }
    }
}
