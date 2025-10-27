<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CbtAchievementUnlocked extends Notification implements ShouldQueue
{
    use Queueable;

    protected $achievement;

    public function __construct($achievement)
    {
        $this->achievement = $achievement;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("Achievement Unlocked: {$this->achievement->achievement_name}")
            ->greeting("Congratulations {$notifiable->name}!")
            ->line("You've unlocked a new achievement:")
            ->line("**{$this->achievement->achievement_icon} {$this->achievement->achievement_name}**")
            ->line($this->achievement->achievement_description)
            ->line("Keep up the great work in your learning journey!")
            ->action('View All Achievements', route('dashboard'))
            ->salutation('Best regards, The CBT Team');
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'achievement_unlocked',
            'title' => 'Achievement Unlocked!',
            'message' => "You've earned: {$this->achievement->achievement_name}",
            'data' => [
                'achievement_id' => $this->achievement->id,
                'achievement_name' => $this->achievement->achievement_name,
                'achievement_icon' => $this->achievement->achievement_icon,
                'achievement_description' => $this->achievement->achievement_description,
            ],
            'action_url' => route('dashboard'),
        ];
    }
}