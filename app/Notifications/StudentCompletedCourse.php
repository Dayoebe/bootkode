<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\Course;
use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentCompletedCourse extends Notification
{
    use Queueable;

    public $student;
    public $course;
    public $certificate;

    public function __construct(User $student, Course $course, Certificate $certificate)
    {
        $this->student = $student;
        $this->course = $course;
        $this->certificate = $certificate;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Student Completed Your Course')
            ->greeting('Great News!')
            ->line($this->student->name . ' has successfully completed your course: **' . $this->course->title . '**')
            ->line('**Student Performance:**')
            ->line('- Final Grade: ' . ($this->certificate->grade ?? 'Pass'))
            ->line('- Completion Date: ' . $this->certificate->completion_date->format('F j, Y'))
            ->line('- Certificate #: ' . $this->certificate->certificate_number)
            ->action('View Student Progress', route('course-builder', $this->course->id))
            ->line('The certificate has been automatically generated and sent to the student.');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'student_completed_course',
            'student_id' => $this->student->id,
            'student_name' => $this->student->name,
            'course_id' => $this->course->id,
            'course_title' => $this->course->title,
            'certificate_id' => $this->certificate->id,
            'grade' => $this->certificate->grade,
            'message' => $this->student->name . ' completed ' . $this->course->title
        ];
    }
}