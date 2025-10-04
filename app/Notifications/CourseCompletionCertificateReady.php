<?php

namespace App\Notifications;

use App\Models\Course;
use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CourseCompletionCertificateReady extends Notification
{
    use Queueable;

    public $course;
    public $certificate;
    public $completionPercentage;

    public function __construct(Course $course, Certificate $certificate, $completionPercentage = 100)
    {
        $this->course = $course;
        $this->certificate = $certificate;
        $this->completionPercentage = $completionPercentage;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('🎓 Course Completed - Certificate Available!')
            ->greeting('Congratulations on Completing Your Course!')
            ->line('Dear ' . $notifiable->name . ',')
            ->line('We are thrilled to inform you that you have successfully completed **' . $this->course->title . '**!')
            ->line('**Your Achievement:**')
            ->line('✅ Course Progress: ' . $this->completionPercentage . '%')
            ->line('✅ Lessons Completed: All required lessons')
            ->line('✅ Assessments: All passed')
            ->line('✅ Final Grade: ' . ($this->certificate->grade ?? 'Pass'))
            ->line('')
            ->line('**Your Certificate:**')
            ->line('We have automatically generated your certificate of completion.')
            ->line('Certificate #: ' . $this->certificate->certificate_number)
            ->action('View & Download Certificate', route('certificate.view', $this->certificate->verification_code))
            ->line('')
            ->line('**What\'s Next?**')
            ->line('• Share your achievement on LinkedIn and social media')
            ->line('• Add this certificate to your portfolio')
            ->line('• Explore more courses to continue your learning journey')
            ->line('')
            ->line('Keep up the excellent work! We look forward to seeing you in more courses.')
            ->salutation('Best regards, The ' . config('app.name') . ' Team');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'course_completion',
            'course_id' => $this->course->id,
            'course_title' => $this->course->title,
            'certificate_id' => $this->certificate->id,
            'certificate_number' => $this->certificate->certificate_number,
            'verification_code' => $this->certificate->verification_code,
            'completion_percentage' => $this->completionPercentage,
            'grade' => $this->certificate->grade,
            'message' => 'Congratulations! You completed ' . $this->course->title
        ];
    }
}