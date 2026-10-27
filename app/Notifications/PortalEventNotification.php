<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PortalEventNotification extends Notification
{
    public function __construct(public array $payload) {}

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $message = (new MailMessage)->subject($this->payload['title'])->line($this->payload['message'])
            ->action('Open BootKode', $this->payload['action_url']);
        if (($this->payload['type'] ?? '') === 'course_review_reminder') {
            $reminder = \App\Models\Learning\ReviewReminder::where('user_id', $notifiable->id)->find($this->payload['reminder_id']);
            if ($reminder) {
                $message->line('[Stop reminders for this course]('.app(\App\Services\ReviewReminderService::class)->unsubscribeUrl($reminder).')');
            }
        }

        return $message;
    }
}
