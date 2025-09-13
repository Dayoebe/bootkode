<?php 

// app/Notifications/VendorApplicationPending.php (for when users apply)
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VendorApplicationPending extends Notification implements ShouldQueue
{
    use Queueable;

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Vendor Application Received')
            ->greeting('Thank you for your application!')
            ->line('We have received your application to become a vendor on the Bootkode Marketplace.')
            ->line('Our team will review your application and get back to you within 2-3 business days.')
            ->line('What happens next:')
            ->line('• We\'ll review your profile and experience')
            ->line('• Check your previous activity on the platform')
            ->line('• Verify your information')
            ->line('• Send you a decision via email')
            ->line('In the meantime, continue exploring our marketplace and learning resources.')
            ->action('Browse Marketplace', route('marketplace.browse'))
            ->line('Thank you for your interest in joining our vendor community!');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'vendor_application_pending',
            'message' => 'Your vendor application has been submitted and is under review.',
        ];
    }
}