<?php

namespace App\Notifications;

use App\Models\Learning\Course;
use App\Models\Learning\ReviewReminder;
use App\Services\PortalNotificationService;
use App\Services\ReviewReminderService;

class ReviewReminderNotification extends PortalEventNotification
{
    public function __construct(Course $course, ReviewReminder $reminder)
    {
        abort_unless((int) $reminder->course_id === (int) $course->id, 422);
        parent::__construct(['type' => 'course_review_reminder', 'title' => 'Share your course feedback', 'message' => 'You can leave an honest review of a course you completed.',
            'action_url' => route('course.view', $course->slug).'#review', 'reminder_id' => $reminder->id]);
    }

    public function via($notifiable): array
    {
        $user = $notifiable->fresh();

        return $user && $user->is_active && $user->email_verified_at && $user->shouldReceiveEmailNotification('course_review_reminder') && app(PortalNotificationService::class)->emailAvailable()
            && app(ReviewReminderService::class)->emailEligible($user, $this->payload) ? ['mail'] : [];
    }
}
