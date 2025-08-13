<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SocialLoginDataCollectionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $provider;

    public function __construct($provider)
    {
        $this->provider = ucfirst($provider);
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }
    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject('Welcome! About the data we collected')
            ->line("Thank you for signing up with {$this->provider}!")
            ->line('We collected the following information from your profile:')
            ->line('- Your name and email address')
            ->line('- Profile picture (if available)');
        
        if ($this->provider === 'Google') {
            $mail->line('- Birthday (for age verification)')
                 ->line('- Address information (if provided)');
        } elseif ($this->provider === 'Facebook') {
            $mail->line('- Birthday (if provided)')
                 ->line('- Location information (if provided)')
                 ->line('- Gender (if provided)');
        }
        
        return $mail->line('This information helps us provide a better experience.')
            ->action('Update Your Profile', route('profile.edit'))
            ->line('You can update or remove any of this information in your profile settings.');
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "Collected profile data from {$this->provider} login",
            'link' => route('profile.edit'),
        ];
    }



// Privacy Policies page 
// <div class="privacy-section">
//     <h3>Social Login Data Collection</h3>
//     <p>When you sign up using Google authentication, with your permission we may collect:</p>
//     <ul>
//         <li>Basic profile information (name, email)</li>
//         <li>Profile picture</li>
//         <li>Birth date (for age verification)</li>
//         <li>Address information (if available)</li>
//         <li>Phone number (if available)</li>
//     </ul>
//     <p>You can review and modify this information at any time in your account settings.</p>
// </div>


// <div class="privacy-section">
//     <h3>Facebook Login Data Collection</h3>
//     <p>When you sign up using Facebook authentication, with your permission we may collect:</p>
//     <ul>
//         <li>Basic profile information (name, email)</li>
//         <li>Profile picture</li>
//         <li>Birth date (if provided)</li>
//         <li>Location and hometown information (if available)</li>
//         <li>Gender (if provided)</li>
//     </ul>
//     <p>This information helps us personalize your experience and comply with age restrictions.</p>
// </div>


}