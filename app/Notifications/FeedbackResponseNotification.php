<?php

namespace App\Notifications;

use App\Models\Feedback;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class FeedbackResponseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $feedback;

    public function __construct(Feedback $feedback)
    {
        $this->feedback = $feedback;
    }

    public function via($notifiable)
    {
        $via = ['database'];
        if ($notifiable->shouldReceiveEmailNotification('feedback_response')) {
            $via[] = 'mail';
        }
        return $via;
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Response to Your Feedback')
            ->line('Your feedback has been responded to.')
            ->line('Response: ' . ($this->feedback->response ?? 'No response text provided.'))
            ->action('View Feedback', route('feedback'))
            ->line('Thank you for your input!');
    }

    public function toDatabase($notifiable)
    {
        return new DatabaseMessage([
            'type' => 'feedback_response',
            'message' => 'Your feedback on ' . ($this->feedback->course ? $this->feedback->course->title : $this->feedback->category) . ' has been responded to.',
            'action_url' => route('feedback'),
            'icon' => 'fas fa-comment-dots',
        ]);
    }
}