<?php 
// app/Notifications/VendorApplicationApproved.php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VendorApplicationApproved extends Notification implements ShouldQueue
{
    use Queueable;

    protected $commissionRate;

    public function __construct($commissionRate)
    {
        $this->commissionRate = $commissionRate;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('🎉 Welcome to the Bootkode Marketplace!')
            ->greeting('Congratulations!')
            ->line("Your application to become a vendor on the Bootkode Marketplace has been approved!")
            ->line("You can now create and sell courses, digital resources, and services on our platform.")
            ->line("Commission Structure:")
            ->line("• You earn: {$this->commissionRate}% of each sale")
            ->line("• Platform fee: " . (100 - $this->commissionRate) . "% of each sale")
            ->action('Start Selling', route('marketplace.seller.create'))
            ->line('We\'re excited to have you as part of our marketplace community!')
            ->line('If you have any questions, please don\'t hesitate to contact our support team.');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'vendor_application_approved',
            'commission_rate' => $this->commissionRate,
            'message' => 'Your vendor application has been approved! You can now start selling on the marketplace.',
        ];
    }
}
