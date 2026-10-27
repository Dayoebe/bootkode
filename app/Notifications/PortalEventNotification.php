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
        return (new MailMessage)->subject($this->payload['title'])->line($this->payload['message'])
            ->action('Open BootKode', $this->payload['action_url']);
    }
}
