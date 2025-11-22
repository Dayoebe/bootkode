<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class UserCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $temporaryPassword;
    protected $createdBy;
    protected $needsVerification;

    public function __construct($temporaryPassword, $createdBy, $needsVerification = false)
    {
        $this->temporaryPassword = $temporaryPassword;
        $this->createdBy = $createdBy;
        $this->needsVerification = $needsVerification;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $loginUrl = route('login');
        $appName = config('app.name');

        $mail = (new MailMessage)
            ->subject("Welcome to {$appName}!")
            ->greeting("Hello {$notifiable->name}!")
            ->line("Your account has been created by {$this->createdBy}.")
            ->line("Here are your login credentials:")
            ->line("**Email:** {$notifiable->email}")
            ->line("**Temporary Password:** {$this->temporaryPassword}")
            ->line("**Important:** Please change your password after your first login for security reasons.");

        if ($this->needsVerification) {
            $verificationUrl = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(60),
                ['id' => $notifiable->id, 'hash' => sha1($notifiable->email)]
            );

            $mail->action('Verify Email & Login', $verificationUrl)
                 ->line('Please verify your email address first, then you can login.');
        } else {
            $mail->action('Login Now', $loginUrl)
                 ->line('Your email has been pre-verified. You can login immediately.');
        }

        $mail->line('If you did not expect this account creation, please contact support immediately.')
             ->line('Thank you for joining us!');

        return $mail;
    }
}