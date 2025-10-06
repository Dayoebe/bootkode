<?php

namespace App\Notifications;

use App\Models\CourseReview;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CoursereviewNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $review;

    public function __construct(CourseReview $review)
    {
        $this->review = $review;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $stars = str_repeat('⭐', $this->review->rating);
        
        return (new MailMessage)
            ->subject('New Review on Your Course')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('You received a new **' . $this->review->rating . '-star** review on your course.')
            ->line('**Course:** ' . $this->review->course->title)
            ->line('**Rating:** ' . $stars)
            ->line('**Reviewer:** ' . $this->review->user->name)
            ->line('**Review:** "' . \Str::limit($this->review->review_text, 200) . '"')
            ->action('View Reviews', route('course-reviews'))  
            ->line('Thank you for creating great content!');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'course_review',
            'review_id' => $this->review->id,
            'course_id' => $this->review->course_id,
            'course_title' => $this->review->course->title,
            'reviewer_name' => $this->review->user->name,
            'rating' => $this->review->rating,
            'message' => $this->review->user->name . ' left a ' . $this->review->rating . '-star review on "' . $this->review->course->title . '"',
            'action_url' => route('course-reviews'),  
        ];
    }
}