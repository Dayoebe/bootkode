<?php

namespace App\Services;

use App\Models\Core\User;
use App\Models\Learning\Course;
use App\Models\Learning\CourseEnrollment;
use App\Models\Learning\ReviewReminder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReviewReminderService
{
    public function unsubscribeUrl(ReviewReminder $reminder): string
    {
        $expires = now()->addDays(30);

        return \Illuminate\Support\Facades\URL::temporarySignedRoute('review-reminder.unsubscribe', $expires,
            ['reminder' => $reminder->id, 'token' => $this->unsubscribeToken($reminder->id, $expires->timestamp)]);
    }

    public function unsubscribeToken(int $reminderId, int $expires): string
    {
        return hash_hmac('sha256', 'course-review-unsubscribe:'.$reminderId.':'.$expires, (string) config('app.key'));
    }

    public function eligibleEnrollments()
    {
        return CourseEnrollment::where('is_completed', true)->whereNotNull('completed_at')->where('completed_at', '<=', now())
            ->whereHas('course', fn ($q) => $q->publiclyAvailable())
            ->whereDoesntHave('course.reviews', fn ($q) => $q->whereColumn('course_reviews.user_id', 'course_enrollments.user_id'));
    }

    public function nextDue(User $user, CourseEnrollment $enrollment, ?ReviewReminder $reminder): ?Carbon
    {
        $prefs = $user->review_reminder_preferences ?? [];
        if (! $user->is_active || ! $user->email_verified_at || ! is_array($prefs) || ($prefs['unsubscribed_all'] ?? false)
            || ! $enrollment->is_completed || ! $enrollment->completed_at || $enrollment->completed_at->isFuture()
            || $reminder?->unsubscribed || $reminder?->completed_at) {
            return null;
        }
        $frequency = $prefs['frequency'] ?? 'standard';
        $schedule = match ($frequency) {
            'frequent' => [3, 7, 14], 'minimal' => [14], default => [7, 14, 30]
        };
        $number = $reminder?->reminder_count ?? 0;
        if ($number < 0 || ! isset($schedule[$number])) {
            return null;
        }

        return $number > 0 && $reminder?->last_reminded_at
            ? Carbon::parse($reminder->last_reminded_at)->addDays($schedule[$number])
            : Carbon::parse($enrollment->completed_at)->addDays($schedule[$number]);
    }

    public function processReminders(bool $dryRun = false): int
    {
        $count = 0;
        $this->eligibleEnrollments()->orderBy('id')->chunkById(100, function ($enrollments) use (&$count, $dryRun) {
            foreach ($enrollments as $enrollment) {
                if ($dryRun) {
                    $user = $enrollment->user;
                    $reminder = ReviewReminder::where('user_id', $enrollment->user_id)->where('course_id', $enrollment->course_id)->first();
                    $due = $user ? $this->nextDue($user, $enrollment, $reminder) : null;
                    if ($due && $due->lte(now())) {
                        $count++;
                    }
                } elseif ($this->deliver($enrollment->id)) {
                    $count++;
                }
            }
        });

        return $count;
    }

    public function deliver(int $enrollmentId): bool
    {
        $entry = CourseEnrollment::findOrFail($enrollmentId);

        return DB::transaction(function () use ($entry) {
            $user = User::whereKey($entry->user_id)->lockForUpdate()->firstOrFail();
            $enrollment = $this->eligibleEnrollments()->whereKey($entry->id)->lockForUpdate()->first();
            if (! $enrollment) {
                return false;
            }
            $reminder = ReviewReminder::where('user_id', $user->id)->where('course_id', $enrollment->course_id)->lockForUpdate()->first();
            $due = $this->nextDue($user, $enrollment, $reminder);
            if (! $due || $due->isFuture()) {
                return false;
            }
            $reminder ??= ReviewReminder::create(['user_id' => $user->id, 'course_id' => $enrollment->course_id, 'reminder_count' => 0]);
            $notice = app(LearningNotificationService::class)->record($user, 'course-review:'.$reminder->id.':'.($reminder->reminder_count + 1),
                ['type' => 'course_review_reminder', 'title' => 'Share your course feedback', 'message' => 'You can leave an honest review of a course you completed.',
                    'action_url' => route('course.view', $enrollment->course->slug).'#review', 'icon' => 'fas fa-comment-dots', 'reminder_id' => $reminder->id, 'reminder_number' => $reminder->reminder_count + 1], 'course_review_reminder');
            if (! $notice) {
                return false;
            }
            $reminder->markAsSent();

            return true;
        });
    }

    public function emailEligible(User $user, array $payload): bool
    {
        $reminder = ReviewReminder::where('user_id', $user->id)->find($payload['reminder_id'] ?? 0);
        if (! $reminder || $reminder->unsubscribed || $reminder->completed_at || ($user->review_reminder_preferences['unsubscribed_all'] ?? false)) {
            return false;
        }
        $number = (int) ($payload['reminder_number'] ?? $reminder->reminder_count);
        $limit = ($user->review_reminder_preferences['frequency'] ?? 'standard') === 'minimal' ? 1 : 3;
        if ($number < 1 || $number !== $reminder->reminder_count || $number > $limit) {
            return false;
        }

        return $this->eligibleEnrollments()->where('user_id', $user->id)->where('course_id', $reminder->course_id)->exists();
    }

    public function markReminderCompleted(User $user, Course $course): void
    {
        // An actual review is the completion boundary, not a client-supplied flag.
        if ($course->reviews()->where('user_id', $user->id)->exists()) {
            ReviewReminder::where('user_id', $user->id)->where('course_id', $course->id)->update(['completed_at' => now()]);
        }
    }

    public function getReminderStats(?Course $course = null): array
    {
        $query = ReviewReminder::when($course, fn ($q) => $q->where('course_id', $course->id));
        $total = (clone $query)->count();
        $completed = (clone $query)->whereNotNull('completed_at')->count();

        return ['total_reminders_sent' => (clone $query)->sum('reminder_count'), 'completed_reviews' => $completed,
            'unsubscribed' => (clone $query)->where('unsubscribed', true)->count(),
            'pending' => (clone $query)->whereNull('completed_at')->where('unsubscribed', false)->where('reminder_count', '<', 3)->count(),
            'conversion_rate' => $total ? round($completed / $total * 100, 2) : 0];
    }

    public function unsubscribeFromAll(User $user): void
    {
        abort_unless((int) auth()->id() === (int) $user->id, 403);
        DB::transaction(function () use ($user) {
            $owner = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            abort_unless($owner->review_reminder_preferences === null || is_array($owner->review_reminder_preferences), 422);
            ReviewReminder::where('user_id', $owner->id)->where('unsubscribed', false)->update(['unsubscribed' => true, 'unsubscribed_at' => now()]);
            $owner->update(['review_reminder_preferences' => array_merge($owner->review_reminder_preferences ?? [], ['email_enabled' => false, 'unsubscribed_all' => true])]);
        });
    }

    // Called only after a valid signed link and CSRF-protected confirmation.
    public function unsubscribeFromCourse(ReviewReminder $reminder): void
    {
        DB::transaction(function () use ($reminder) {
            User::whereKey($reminder->user_id)->lockForUpdate()->firstOrFail();
            $record = ReviewReminder::whereKey($reminder->id)->lockForUpdate()->firstOrFail();
            if (! $record->unsubscribed) {
                $record->unsubscribe();
            }
        });
    }
}
