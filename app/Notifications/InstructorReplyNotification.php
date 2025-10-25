<?php

namespace App\Notifications;

use App\Models\Learning\CourseReview;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InstructorReplyNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $review;

    public function __construct(CourseReview $review)
    {
        $this->review = $review;
    }

    public function via($notifiable)
    {
        $channels = ['database']; // Always store in database
        
        // Only send email if user has enabled this notification type
        if ($notifiable->shouldReceiveEmailNotification('instructor_reply')) {
            $channels[] = 'mail';
        }
        
        return $channels;
    }

    public function toMail($notifiable)
    {
        $instructorName = $this->review->course->instructor->name;
        
        return (new MailMessage)
            ->subject('Instructor Responded to Your Review')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('**' . $instructorName . '** has responded to your review on **' . $this->review->course->title . '**.')
            ->line('---')
            ->line('**Your Review:**')
            ->line('"' . $this->review->review_text . '"')
            ->line('**Your Rating:** ' . str_repeat('⭐', $this->review->rating) . ' (' . $this->review->rating . '/5)')
            ->line('---')
            ->line('**Instructor\'s Response:**')
            ->line('"' . $this->review->instructor_reply . '"')
            ->line('---')
            ->action('View Course', route('course.view', $this->review->course->slug))
            ->line('Thank you for your feedback and continued engagement with the course!');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'instructor_reply',
            'review_id' => $this->review->id,
            'course_id' => $this->review->course_id,
            'course_title' => $this->review->course->title,
            'instructor_name' => $this->review->course->instructor->name,
            'message' => $this->review->course->instructor->name . ' replied to your review on "' . $this->review->course->title . '"',
            'action_url' => route('course.view', $this->review->course->slug),
            'icon' => 'fas fa-reply'
        ];
    }
}