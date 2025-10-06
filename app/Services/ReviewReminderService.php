<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseReview;
use App\Models\ReviewReminder;
use App\Models\User;
use App\Notifications\ReviewReminderNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ReviewReminderService
{
    /**
     * Process review reminders for eligible students
     */
    public function processReminders()
    {
        $eligibleEnrollments = $this->getEligibleEnrollments();
        $remindersSent = 0;

        foreach ($eligibleEnrollments as $enrollment) {
            try {
                $reminder = $this->getOrCreateReminder($enrollment);

                if ($reminder->shouldSendReminder()) {
                    $this->sendReminder($enrollment->user, $enrollment->course, $reminder);
                    $remindersSent++;
                }
            } catch (\Exception $e) {
                Log::error('Failed to send review reminder', [
                    'user_id' => $enrollment->user_id,
                    'course_id' => $enrollment->course_id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $remindersSent;
    }

    /**
     * Get enrollments eligible for review reminders
     */
    private function getEligibleEnrollments()
    {
        return CourseEnrollment::with(['user', 'course'])
            ->where('is_completed', true)
            ->where('completed_at', '<=', now()->subDays(7)) // Completed at least 7 days ago
            ->whereDoesntHave('course.reviews', function($query) {
                $query->whereColumn('course_reviews.user_id', 'course_enrollments.user_id');
            })
            ->get();
    }

    /**
     * Get or create reminder record
     */
    private function getOrCreateReminder(CourseEnrollment $enrollment): ReviewReminder
    {
        return ReviewReminder::firstOrCreate(
            [
                'user_id' => $enrollment->user_id,
                'course_id' => $enrollment->course_id
            ],
            [
                'reminder_count' => 0
            ]
        );
    }

    /**
     * Send reminder notification
     */
    private function sendReminder(User $user, Course $course, ReviewReminder $reminder)
    {
        $user->notify(new ReviewReminderNotification($course, $reminder));
        $reminder->markAsSent();

        Log::info('Review reminder sent', [
            'user_id' => $user->id,
            'course_id' => $course->id,
            'reminder_number' => $reminder->reminder_count
        ]);
    }

    /**
     * Mark reminder as completed when user leaves review
     */
    public function markReminderCompleted(User $user, Course $course)
    {
        $reminder = ReviewReminder::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($reminder) {
            $reminder->markAsCompleted();
        }
    }

    /**
     * Get reminder statistics
     */
    public function getReminderStats(Course $course = null)
    {
        $query = ReviewReminder::query();

        if ($course) {
            $query->where('course_id', $course->id);
        }

        return [
            'total_reminders_sent' => $query->sum('reminder_count'),
            'completed_reviews' => $query->whereNotNull('completed_at')->count(),
            'unsubscribed' => $query->where('unsubscribed', true)->count(),
            'pending' => $query->whereNull('completed_at')
                ->where('unsubscribed', false)
                ->where('reminder_count', '<', 3)
                ->count(),
            'conversion_rate' => $this->calculateConversionRate($query)
        ];
    }

    /**
     * Calculate conversion rate (reminders to reviews)
     */
    private function calculateConversionRate($query)
    {
        $total = $query->count();
        $completed = $query->whereNotNull('completed_at')->count();

        return $total > 0 ? round(($completed / $total) * 100, 2) : 0;
    }

    /**
     * Unsubscribe user from all review reminders
     */
    public function unsubscribeFromAll(User $user)
    {
        ReviewReminder::where('user_id', $user->id)
            ->where('unsubscribed', false)
            ->update([
                'unsubscribed' => true,
                'unsubscribed_at' => now()
            ]);

        $user->update([
            'review_reminder_preferences' => array_merge(
                $user->review_reminder_preferences ?? [],
                ['email_enabled' => false]
            )
        ]);
    }

    /**
     * Unsubscribe from specific course reminder
     */
    public function unsubscribeFromCourse(ReviewReminder $reminder)
    {
        $reminder->unsubscribe();
    }
}