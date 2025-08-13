<?php

namespace App\Notifications;

use App\Models\Announcement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class NewAnnouncementNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $announcement;

    public function __construct(Announcement $announcement)
    {
        $this->announcement = $announcement;
    }

    public function via($notifiable)
    {
        $via = ['database'];
        if ($notifiable->shouldReceiveEmailNotification('announcement')) {
            $via[] = 'mail';
        }
        return $via;
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Announcement: ' . $this->announcement->title)
            ->line('A new announcement has been published.')
            ->line('Title: ' . $this->announcement->title)
            ->line('Content: ' . Str::limit($this->announcement->content, 100))
            ->action('View Announcement', route('announcements'))
            ->line('Thank you for staying updated!');
    }

    public function toDatabase($notifiable)
    {
        return new DatabaseMessage([
            'type' => 'new_announcement',
            'message' => 'New announcement: ' . $this->announcement->title,
            'action_url' => route('announcements'),
            'icon' => 'fas fa-bullhorn',
        ]);
    }
}