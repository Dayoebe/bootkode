<?php

namespace App\Notifications;

use App\Models\SystemStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;
use \App\Notifications\Str;

class SystemStatusUpdateNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $systemStatus;

    public function __construct(SystemStatus $systemStatus)
    {
        $this->systemStatus = $systemStatus;
    }

    public function via($notifiable)
    {
        $via = ['database'];
        if ($notifiable->shouldReceiveEmailNotification('system_status')) {
            $via[] = 'mail';
        }
        return $via;
    }

    public function toMail($notifiable)
    {
        $message = $this->systemStatus->resolved_at
            ? 'System Status Resolved: ' . $this->systemStatus->title
            : 'System Status Update: ' . $this->systemStatus->title;

        return (new MailMessage)
            ->subject($message)
            ->line($message)
            ->line('Service: ' . ucfirst($this->systemStatus->service))
            ->line('Status: ' . ucfirst($this->systemStatus->status))
            ->line('Description: ' . Str::limit($this->systemStatus->description, 100))
            ->action('View System Status', route('system-status'))
            ->line('Thank you for your patience!');
    }

    public function toDatabase($notifiable)
    {
        return new DatabaseMessage([
            'type' => 'system_status_update',
            'message' => ($this->systemStatus->resolved_at ? 'Resolved: ' : 'New Issue: ') . $this->systemStatus->title,
            'action_url' => route('system-status'),
            'icon' => 'fas fa-server',
        ]);
    }
}