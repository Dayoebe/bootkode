<?php

// App/Notifications/CbtResultNotification.php
namespace App\Notifications;

use App\Models\CbtResult;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class CbtResultNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $cbtResult;

    public function __construct(CbtResult $cbtResult)
    {
        $this->cbtResult = $cbtResult;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $exam = $this->cbtResult->exam;
        $subject = $this->cbtResult->passed 
            ? "🎉 Congratulations! You passed: {$exam->title}"
            : "📊 Your exam results are ready: {$exam->title}";

        $mail = (new MailMessage)
            ->subject($subject)
            ->greeting("Hello {$notifiable->name}!")
            ->line("Your exam results for **{$exam->title}** are now available.");

        if ($this->cbtResult->passed) {
            $mail->line("🎉 **Congratulations!** You have successfully passed the exam with a score of **{$this->cbtResult->percentage_score}%**");
            
            if ($exam->exam_type === 'certification') {
                $mail->line("✅ You are now eligible for certification. Your certificate will be processed within 24-48 hours.");
            }
        } else {
            $mail->line("📊 Your score: **{$this->cbtResult->percentage_score}%** (Passing score: {$exam->pass_percentage}%)");
            
            $remainingAttempts = $exam->max_attempts - $this->cbtResult->attempt_number;
            if ($remainingAttempts > 0) {
                $mail->line("💪 Don't worry! You have **{$remainingAttempts}** more attempt(s) remaining. Review the material and try again.");
            }
        }

        $mail->line("**Exam Details:**")
            ->line("- **Score:** {$this->cbtResult->percentage_score}%")
            ->line("- **Grade:** {$this->cbtResult->grade}")
            ->line("- **Correct Answers:** {$this->cbtResult->correct_answers}/{$this->cbtResult->total_questions}")
            ->line("- **Time Spent:** " . $this->formatDuration($this->cbtResult->time_spent_seconds))
            ->line("- **Completed:** " . $this->cbtResult->completed_at->format('M j, Y \a\t g:i A'));

        if ($exam->show_correct_answers || $exam->show_explanations) {
            $mail->action('View Detailed Results', route('cbt.result', $this->cbtResult->session_id));
        } else {
            $mail->action('View Results', route('cbt.results'));
        }

        $mail->line('Thank you for using our CBT platform!')
            ->salutation('Best regards, The CBT Team');

        return $mail;
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'cbt_result',
            'title' => $this->cbtResult->passed ? 'Exam Passed!' : 'Exam Results Available',
            'message' => "Your results for {$this->cbtResult->exam->title} are ready.",
            'data' => [
                'cbt_result_id' => $this->cbtResult->id,
                'exam_title' => $this->cbtResult->exam->title,
                'score' => $this->cbtResult->percentage_score,
                'passed' => $this->cbtResult->passed,
                'grade' => $this->cbtResult->grade,
                'attempt_number' => $this->cbtResult->attempt_number,
            ],
            'action_url' => route('cbt.result', $this->cbtResult->session_id),
        ];
    }

    private function formatDuration($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        
        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }
        return "{$minutes}m";
    }
}
