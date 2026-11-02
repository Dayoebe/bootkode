<?php

namespace App\Services;

use App\Models\Assessment\Assessment;
use App\Models\Assessment\LearningSession;
use App\Models\Core\User;
use App\Models\Credentials\UserAchievement;
use App\Models\Credentials\UserBadge;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LearningNotificationService
{
    public function record(User $recipient, string $event, array $payload, string $preference): ?DatabaseNotification
    {
        if (DB::transactionLevel() < 1) {
            throw new \LogicException('Learning delivery requires its source transaction.');
        }
        $recipient = User::whereKey($recipient->id)->lockForUpdate()->firstOrFail();
        if (! $recipient->is_active || ! $recipient->email_verified_at) {
            return null;
        }
        abort_unless(Schema::hasTable('learning_notification_receipts'), 503, 'Learning notifications need the reviewed forward migration.');
        $key = hash('sha256', $event);
        $receipt = DB::table('learning_notification_receipts')->where('user_id', $recipient->id)->where('event_key', $key)->first();
        if ($receipt) {
            return null;
        }
        $notice = app(PortalNotificationService::class)->record($recipient, $event, $payload, $preference);
        DB::table('learning_notification_receipts')->insert(['user_id' => $recipient->id, 'event_key' => $key, 'family' => $payload['type'], 'notification_id' => $notice->id, 'created_at' => now()]);

        return $notice;
    }

    public function result(LearningSession $session, string $event, string $type = 'assessment_result', ?string $requestId = null): void
    {
        $payload = ['type' => $type, 'title' => $type === 'assessment_result' ? 'Assessment result available' : 'Assessment review updated',
            'message' => $type === 'assessment_result' ? 'Your reviewed assessment result is available privately.' : 'A decision on your recorded assessment mark is available privately.',
            'action_url' => route('cbt.viewer'), 'icon' => 'fas fa-clipboard-check', 'session_id' => $session->id];
        if ($requestId) {
            $payload['request_id'] = $requestId;
        }
        $this->record($session->user, 'assessment:'.$session->id.':'.$event, $payload, 'assessment_review');
    }

    public function reviewRequested(LearningSession $session, array $request): void
    {
        $assessment = Assessment::find($session->metadata['assessment_id'] ?? 0);
        $owner = ($assessment?->course ?? $assessment?->lesson?->section?->course ?? $assessment?->section?->course)?->instructor;
        if (! $owner || $owner->id === $session->user_id || ! $owner->hasAnyRole(['instructor', 'super_admin', 'academy_admin'])) {
            return;
        }
        $this->record($owner, 'assessment:'.$session->id.':request:'.$request['id'],
            ['type' => 'assessment_review_request', 'title' => 'Assessment mark review requested', 'message' => 'A learner requested reconsideration of a recorded assessment mark.',
                'action_url' => route('assessment.reviews'), 'icon' => 'fas fa-clipboard-check', 'session_id' => $session->id, 'request_id' => $request['id']], 'assessment_review');
    }

    public function achievement(User $user, UserAchievement|UserBadge $achievement): bool
    {
        return DB::transaction(function () use ($user, $achievement) {
            $owner = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            $record = $achievement->newQuery()->where('user_id', $owner->id)->find($achievement->id);
            if (! $record || ! $record->exists || ! $owner->is_active || ! $owner->email_verified_at) {
                return false;
            }
            $kind = $record instanceof UserBadge ? 'badge' : 'achievement';

            return $this->record($owner, $kind.':'.$record->id,
                ['type' => 'achievement_unlocked', 'title' => 'Learning achievement recorded', 'message' => 'A learning achievement has been recorded in your private rewards history.',
                    'action_url' => route('gamification.badges'), 'icon' => 'fas fa-award', 'achievement_kind' => $kind, 'achievement_id' => $record->id], 'achievement') !== null;
        });
    }

    /** Recheck queued email against current source records; never trust the queued role or owner. */
    public function sourceAvailable(User $user, array $payload): bool
    {
        if (($payload['type'] ?? '') === 'achievement_unlocked') {
            $model = match ($payload['achievement_kind'] ?? '') {
                'badge' => new UserBadge, 'achievement' => new UserAchievement, default => null
            };

            return $model && $model->where('user_id', $user->id)->whereKey($payload['achievement_id'] ?? 0)->exists();
        }
        if (($payload['type'] ?? '') === 'cbt_reminder') {
            return app(CbtReminderService::class)->eligible($user, Assessment::find($payload['assessment_id'] ?? 0), $payload['deadline'] ?? null);
        }
        if (($payload['type'] ?? '') === 'course_review_reminder') {
            return app(ReviewReminderService::class)->emailEligible($user, $payload);
        }
        $session = LearningSession::where('activity_type', 'assessment')->whereNotNull('ended_at')->find($payload['session_id'] ?? 0);
        if (! $session || ($session->metadata['results']['pending_review'] ?? true) !== false) {
            return false;
        }
        if (($payload['type'] ?? '') === 'assessment_review_request') {
            $assessment = Assessment::find($session->metadata['assessment_id'] ?? 0);
            $user->unsetRelation('roles');
            $course = $assessment?->course ?? $assessment?->lesson?->section?->course ?? $assessment?->section?->course;

            return $course && (int) $course->instructor_id === (int) $user->id && $user->hasAnyRole(['instructor', 'super_admin', 'academy_admin'])
                && collect($session->metadata['mark_reviews'] ?? [])->contains(fn ($request) => $request['id'] === ($payload['request_id'] ?? '') && $request['status'] === 'open');
        }

        return in_array($payload['type'] ?? '', ['assessment_result', 'assessment_review_decision'], true) && (int) $session->user_id === (int) $user->id;
    }
}
