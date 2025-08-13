<?php

namespace App\Notifications;

use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class CertificateUpdateNotification extends Notification
{
    use Queueable;

    public $certificate;

    public function __construct(Certificate $certificate)
    {
        $this->certificate = $certificate;
    }

    public function via($notifiable)
    {
        $via = ['database'];
        if ($notifiable->shouldReceiveEmailNotification('certificate_update')) {
            $via[] = 'mail';
        }
        return $via;
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Certificate Update: ' . $this->certificate->course->title)
            ->line('Your certificate for ' . $this->certificate->course->title . ' is now ' . $this->certificate->status . '.')
            ->action('View Certificate', route('certificates.show', $this->certificate->uuid))
            ->line('Thank you for your progress!');
    }

    public function toDatabase($notifiable)
    {
        return new DatabaseMessage([
            'type' => 'certificate_update',
            'message' => 'Certificate for ' . $this->certificate->course->title . ' is ' . $this->certificate->status,
            'action_url' => route('certificates.show', $this->certificate->uuid),
            'icon' => 'fas fa-certificate',
        ]);
    }
}