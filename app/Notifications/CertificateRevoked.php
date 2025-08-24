<?php
// App\Notifications\CertificateRevoked.php

namespace App\Notifications;

use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CertificateRevoked extends Notification implements ShouldQueue
{
    use Queueable;

    protected $certificate;

    public function __construct(Certificate $certificate)
    {
        $this->certificate = $certificate;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Important: Certificate Revoked - ' . $this->certificate->course->title)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('This is to inform you that your certificate has been revoked.')
            ->line('**Course:** ' . $this->certificate->course->title)
            ->line('**Certificate Number:** ' . $this->certificate->certificate_number)
            ->line('**Reason:** ' . $this->certificate->revocation_reason)
            ->line('**Revocation Date:** ' . $this->certificate->revoked_at->format('M j, Y'))
            ->line('The certificate is no longer valid and cannot be verified.')
            ->line('If you have questions about this revocation, please contact support immediately.')
            ->action('Contact Support', route('help.support'));
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Certificate Revoked',
            'message' => 'Your certificate for ' . $this->certificate->course->title . ' has been revoked.',
            'certificate_id' => $this->certificate->id,
            'certificate_number' => $this->certificate->certificate_number,
            'course_title' => $this->certificate->course->title,
            'revocation_reason' => $this->certificate->revocation_reason,
            'revoked_at' => $this->certificate->revoked_at,
            'contact_url' => route('help.support'),
            'type' => 'certificate_revoked',
        ];
    }
}