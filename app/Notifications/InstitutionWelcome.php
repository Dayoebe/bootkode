<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Institution;

class InstitutionWelcome extends Notification implements ShouldQueue
{
    use Queueable;

    protected $institution;

    public function __construct(Institution $institution)
    {
        $this->institution = $institution;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Welcome to ' . config('app.name') . ' - Institution Partnership')
            ->greeting('Welcome to ' . config('app.name') . '!')
            ->line('Your institution "' . $this->institution->name . '" has been successfully registered as a partner.')
            ->line('You now have access to our institutional management portal where you can:')
            ->line('• Manage your users and enrollments')
            ->line('• Track learning progress and analytics')
            ->line('• Generate certificates for your learners')
            ->line('• Access bulk enrollment tools')
            ->action('Access Portal', route('institution.portal'))
            ->line('If you have any questions, please don\'t hesitate to contact our support team.')
            ->salutation('Best regards, The ' . config('app.name') . ' Team');
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Institution Partnership Welcome',
            'message' => 'Your institution "' . $this->institution->name . '" has been successfully registered as a partner.',
            'institution_id' => $this->institution->id,
            'type' => 'institution_welcome'
        ];
    }
}