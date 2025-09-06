<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Institution;

class LicenseExpiring extends Notification implements ShouldQueue
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
        $daysUntilExpiry = $this->institution->getDaysUntilExpiry();
        
        return (new MailMessage)
            ->subject('License Renewal Required - ' . $this->institution->name)
            ->greeting('License Renewal Notice')
            ->line('Your institution license for "' . $this->institution->name . '" is expiring soon.')
            ->line('License expires on: ' . $this->institution->license_end_date->format('F j, Y'))
            ->line('Days remaining: ' . $daysUntilExpiry)
            ->line('To avoid service interruption, please renew your license before the expiration date.')
            ->action('Contact Support', config('app.url') . '/support')
            ->line('If you have already renewed your license, please disregard this notice.')
            ->salutation('Best regards, The ' . config('app.name') . ' Team');
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'License Renewal Required',
            'message' => 'License for "' . $this->institution->name . '" expires on ' . $this->institution->license_end_date->format('M j, Y'),
            'institution_id' => $this->institution->id,
            'days_remaining' => $this->institution->getDaysUntilExpiry(),
            'type' => 'license_expiring'
        ];
    }
}