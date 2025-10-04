<?php

namespace App\Notifications;

use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CertificateAwarded extends Notification
{
    use Queueable;

    public $certificate;

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
            ->greeting('Congratulations, ' . $notifiable->name . '!')
            ->line('You have successfully completed **' . $this->certificate->course->title . '**.')
            ->line('Your certificate of completion has been generated and is ready for download.')
            ->line('**Certificate Details:**')
            ->line('- Certificate Number: ' . $this->certificate->certificate_number)
            ->line('- Completion Date: ' . $this->certificate->completion_date->format('F j, Y'))
            ->line('- Grade: ' . ($this->certificate->grade ?? 'Pass'))
            ->action('Download Certificate', route('certificate.download', $this->certificate->verification_code))
            ->line('You can also view and verify your certificate at any time from your dashboard.')
            ->line('Share your achievement with the world! 🌟');
    }

    public function toArray($notifiable)
    {
        return [
            'certificate_id' => $this->certificate->id,
            'course_id' => $this->certificate->course_id,
            'course_title' => $this->certificate->course->title,
            'certificate_number' => $this->certificate->certificate_number,
            'verification_code' => $this->certificate->verification_code,
            'message' => 'Your certificate for ' . $this->certificate->course->title . ' is ready!'
        ];
    }
}