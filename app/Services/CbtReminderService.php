<?php

namespace App\Services;

use App\Models\Assessment\Assessment;
use App\Models\Assessment\LearningSession;
use App\Models\Core\User;
use App\Models\Learning\Course;
use Illuminate\Support\Facades\DB;

class CbtReminderService
{
    public function candidates()
    {
        // Standalone global quizzes have no recipient assignment; never infer a mailing list.
        return Assessment::where('type', 'quiz')->whereNull('lesson_id')->whereNull('section_id')->whereNotNull('course_id')
            ->where(fn ($q) => $q->whereBetween('deadline', [now(), now()->addDays(3)])->orWhere(fn ($q) => $q->whereNull('deadline')->whereBetween('due_date', [now(), now()->addDays(3)])));
    }

    public function deadline(Assessment $assessment): ?string
    {
        return ($assessment->deadline ?? $assessment->due_date)?->toISOString();
    }

    public function eligible(User $user, ?Assessment $assessment, ?string $deadline = null): bool
    {
        if (! $user->is_active || ! $user->email_verified_at || ! $assessment || $assessment->type !== 'quiz' || $assessment->lesson_id || $assessment->section_id || ! $assessment->course_id) {
            return false;
        }
        $due = $assessment->deadline ?? $assessment->due_date;
        $frequency = $user->notification_preferences['reminder_frequency'] ?? '24_hours';
        $hours = match ($frequency) {
            'never' => 0, '1_hour' => 1, '6_hours' => 6, '3_days' => 72, default => 24
        };
        if (! $hours || ! $due || ! $due->isFuture() || $due->gt(now()->addHours($hours)) || ($deadline !== null && $this->deadline($assessment) !== $deadline)) {
            return false;
        }
        if (! Course::publiclyAvailable()->whereKey($assessment->course_id)->exists()) {
            return false;
        }
        $enrolled = $user->enrollments()->where('course_id', $assessment->course_id)->exists() || $user->courses()->where('courses.id', $assessment->course_id)->exists();
        if (! $enrolled || ! $assessment->questions()->exists() || $assessment->questions()->whereNotIn('question_type', ['multiple_choice', 'true_false'])->exists()) {
            return false;
        }
        if (! is_numeric($assessment->pass_percentage) || $assessment->pass_percentage < 1 || $assessment->pass_percentage > 100) {
            return false;
        }
        foreach (app(CbtAttemptService::class)->questions($assessment) as $question) {
            if ($question['allows_multiple'] || empty($question['display_to_original_map'])) {
                return false;
            }
        }
        if (! $assessment->canUserTakeAssessment($user->id)[0]) {
            return false;
        }
        if ($assessment->studentAnswers()->where('user_id', $user->id)->whereNotNull('submitted_at')->exists()) {
            return false;
        }

        return ! LearningSession::where('user_id', $user->id)->where('activity_type', 'assessment')->where('metadata->assessment_id', $assessment->id)->exists();
    }

    public function recipients(Assessment $assessment)
    {
        return User::where('is_active', true)->whereNotNull('email_verified_at')
            ->where(fn ($q) => $q->whereHas('enrollments', fn ($q) => $q->where('course_id', $assessment->course_id))
                ->orWhereHas('courses', fn ($q) => $q->where('courses.id', $assessment->course_id)));
    }

    public function deliver(User $user, Assessment $assessment): bool
    {
        return DB::transaction(function () use ($user, $assessment) {
            $user = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            $assessment = Assessment::whereKey($assessment->id)->lockForUpdate()->firstOrFail();
            if (! $this->eligible($user, $assessment)) {
                return false;
            }
            $due = $assessment->deadline ?? $assessment->due_date;
            $window = $due->lte(now()->addHour()) ? 'within the next hour.' : ($due->lte(now()->addDay()) ? 'within the next day.' : 'within the next three days.');

            return app(LearningNotificationService::class)->record($user, 'cbt:'.$assessment->id.':'.$this->deadline($assessment),
                ['type' => 'cbt_reminder', 'title' => 'CBT deadline reminder', 'message' => 'An available course quiz has a scheduled deadline '.$window,
                    'action_url' => route('cbt.exam.take', ['assessment' => $assessment->id]), 'icon' => 'fas fa-clock', 'assessment_id' => $assessment->id, 'deadline' => $this->deadline($assessment)], 'cbt_reminder') !== null;
        });
    }
}
