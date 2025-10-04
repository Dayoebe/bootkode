<?php
// App\Notifications\CertificateApproved.php

namespace App\Notifications;

use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CertificateApproved extends Notification implements ShouldQueue
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
            ->subject('🎉 Your Certificate is Ready!')
            ->greeting('Congratulations ' . $notifiable->name . '!')
            ->line('Your certificate request has been approved!')
            ->line('**Course:** ' . $this->certificate->course->title)
            ->line('**Certificate Number:** ' . $this->certificate->certificate_number)
            ->line('**Grade:** ' . ($this->certificate->grade ?: 'Pass'))
            ->action('Download Certificate', route('certificate.download', $this->certificate->verification_code))
            ->action('View Certificate', route('certificate.view', $this->certificate->verification_code))
            ->line('You can now download and share your official certificate!')
            ->line('**Verification Code:** ' . $this->certificate->verification_code);
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Certificate Approved! 🎉',
            'message' => 'Your certificate for ' . $this->certificate->course->title . ' has been approved and is ready for download.',
            'certificate_id' => $this->certificate->id,
            'certificate_number' => $this->certificate->certificate_number,
            'course_title' => $this->certificate->course->title,
            'grade' => $this->certificate->grade,
            'download_url' => route('certificate.download', $this->certificate->verification_code),
            'view_url' => route('certificate.view', $this->certificate->verification_code),
            'type' => 'certificate_approved',
        ];
    }
}
