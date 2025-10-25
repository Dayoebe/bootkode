<?php

namespace App\Notifications;

use App\Models\Learning\Course;
use App\Models\ReviewReminder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class ReviewReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $course;
    protected $reminder;
    protected $reminderNumber;

    public function __construct(Course $course, ReviewReminder $reminder)
    {
        $this->course = $course;
        $this->reminder = $reminder;
        $this->reminderNumber = $reminder->reminder_count + 1;
    }

    public function via($notifiable)
    {
        $preferences = $notifiable->review_reminder_preferences ?? [];
        $emailEnabled = $preferences['email_enabled'] ?? true;
        
        $channels = ['database'];
        
        if ($emailEnabled && !$this->reminder->unsubscribed) {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    public function toMail($notifiable)
    {
        $unsubscribeUrl = URL::signedRoute('review-reminder.unsubscribe', [
            'reminder' => $this->reminder->id
        ]);

        $subject = match($this->reminderNumber) {
            1 => 'Share Your Experience with ' . $this->course->title,
            2 => 'We\'d Love Your Feedback on ' . $this->course->title,
            3 => 'Last Chance: Review ' . $this->course->title,
            default => 'Review Reminder'
        };

        $greeting = match($this->reminderNumber) {
            1 => 'We hope you enjoyed the course!',
            2 => 'Your feedback matters to us',
            3 => 'One final reminder',
            default => 'Hello!'
        };

        $message = match($this->reminderNumber) {
            1 => "You recently completed **{$this->course->title}**. We'd love to hear about your experience! Your review helps other students and enables us to improve the course.",
            2 => "We noticed you haven't left a review for **{$this->course->title}** yet. Your insights are valuable and only take a minute to share.",
            3 => "This is our last reminder about reviewing **{$this->course->title}**. If you have a moment, we'd really appreciate your feedback.",
            default => "Please consider leaving a review for {$this->course->title}."
        };

        return (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line($message)
            ->line('**Course:** ' . $this->course->title)
            ->line('**Instructor:** ' . $this->course->instructor->name)
            ->action('Write Your Review', route('course.view', $this->course->slug) . '#review')
            ->line('Your honest feedback takes less than 2 minutes and helps thousands of students.')
            ->line('---')
            ->line('Don\'t want these reminders? [Unsubscribe from course review reminders](' . $unsubscribeUrl . ')');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'review_reminder',
            'course_id' => $this->course->id,
            'course_title' => $this->course->title,
            'reminder_number' => $this->reminderNumber,
            'message' => "Share your experience with {$this->course->title} - Your feedback helps others!",
            'action_url' => route('course.view', $this->course->slug),
            'icon' => 'fas fa-comment-dots'
        ];
    }
}