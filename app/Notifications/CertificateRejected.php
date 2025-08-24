<?php
// App\Notifications\CertificateRejected.php

namespace App\Notifications;

use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CertificateRejected extends Notification implements ShouldQueue
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
            ->subject('Certificate Request Update - ' . $this->certificate->course->title)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Unfortunately, your certificate request has been rejected.')
            ->line('**Course:** ' . $this->certificate->course->title)
            ->line('**Certificate Number:** ' . $this->certificate->certificate_number)
            ->line('**Reason:** ' . $this->certificate->rejection_reason)
            ->line('If you believe this is an error or have questions about the rejection, please contact your instructor or support team.')
            ->action('Contact Support', route('help.support'));
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Certificate Request Rejected',
            'message' => 'Your certificate request for ' . $this->certificate->course->title . ' has been rejected.',
            'certificate_id' => $this->certificate->id,
            'certificate_number' => $this->certificate->certificate_number,
            'course_title' => $this->certificate->course->title,
            'rejection_reason' => $this->certificate->rejection_reason,
            'contact_url' => route('help.support'),
            'type' => 'certificate_rejected',
        ];
    }
}
