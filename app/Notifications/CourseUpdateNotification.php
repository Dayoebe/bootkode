<?php

namespace App\Notifications;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class CourseUpdateNotification extends Notification
{
    use Queueable;

    public $course;

    public function __construct(Course $course)
    {
        $this->course = $course;
    }

    public function via($notifiable)
    {
        $via = ['database'];
        if ($notifiable->shouldReceiveEmailNotification('course_update')) {
            $via[] = 'mail';
        }
        return $via;
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Course Update: ' . $this->course->title)
            ->line('A new course update is available: ' . $this->course->title)
            ->action('View Course', route('courses.show', $this->course->slug))
            ->line('Thank you for using our platform!');
    }

    public function toDatabase($notifiable)
    {
        return new DatabaseMessage([
            'type' => 'course_update',
            'message' => 'New update for course: ' . $this->course->title,
            'action_url' => route('courses.show', $this->course->slug),
            'icon' => 'fas fa-book-open',
        ]);
    }
}