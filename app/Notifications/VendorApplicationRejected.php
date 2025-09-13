<?php

// app/Notifications/VendorApplicationRejected.php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VendorApplicationRejected extends Notification implements ShouldQueue
{
    use Queueable;

    protected $reason;

    public function __construct($reason)
    {
        $this->reason = $reason;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Marketplace Vendor Application Update')
            ->greeting('Hello!')
            ->line('Thank you for your interest in becoming a vendor on the Bootkode Marketplace.')
            ->line('After careful review, we are unable to approve your vendor application at this time.')
            ->line("Reason: {$this->reason}")
            ->line('Please don\'t be discouraged! You can address these concerns and apply again in the future.')
            ->line('Continue learning and growing on our platform, and we look forward to potentially working with you as a vendor in the future.')
            ->action('Continue Learning', route('marketplace.browse'))
            ->line('If you have any questions about this decision, please contact our support team.');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'vendor_application_rejected',
            'reason' => $this->reason,
            'message' => 'Your vendor application was not approved at this time.',
        ];
    }
}
