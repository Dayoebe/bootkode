
// App/Notifications/CbtExamReminder.php
class CbtExamReminder extends Notification implements ShouldQueue
{
    use Queueable;

    protected $exam;
    protected $reminderType;

    public function __construct(CbtExam $exam, $reminderType = 'upcoming')
    {
        $this->exam = $exam;
        $this->reminderType = $reminderType;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $mail = (new MailMessage);

        switch ($this->reminderType) {
            case 'upcoming':
                $mail->subject("⏰ Upcoming Exam: {$this->exam->title}")
                    ->greeting("Hello {$notifiable->name}!")
                    ->line("This is a reminder that you have an upcoming exam:")
                    ->line("**{$this->exam->title}**")
                    ->line("**Course:** {$this->exam->course->title}")
                    ->line("**Duration:** {$this->exam->formatted_duration}")
                    ->line("**Available from:** " . $this->exam->start_date->format('M j, Y \a\t g:i A'));
                break;

            case 'deadline_approaching':
                $mail->subject("⚠️ Exam Deadline Approaching: {$this->exam->title}")
                    ->greeting("Hello {$notifiable->name}!")
                    ->line("⚠️ **Important:** The deadline for your exam is approaching!")
                    ->line("**{$this->exam->title}**")
                    ->line("**Deadline:** " . $this->exam->end_date->format('M j, Y \a\t g:i A'))
                    ->line("**Time remaining:** " . $this->exam->end_date->diffForHumans());
                break;

            case 'last_chance':
                $mail->subject("🚨 Last Chance: {$this->exam->title}")
                    ->greeting("Hello {$notifiable->name}!")
                    ->line("🚨 **Final Notice:** This is your last chance to take this exam!")
                    ->line("**{$this->exam->title}**")
                    ->line("**Deadline:** " . $this->exam->end_date->format('M j, Y \a\t g:i A'))
                    ->line("**Time remaining:** " . $this->exam->end_date->diffForHumans());
                break;
        }

        $mail->action('Take Exam Now', route('cbt.exam', $this->exam->slug))
            ->line('Good luck with your exam!')
            ->salutation('Best regards, The CBT Team');

        return $mail;
    }

    public function toDatabase($notifiable)
    {
        $messages = [
            'upcoming' => "Upcoming exam: {$this->exam->title}",
            'deadline_approaching' => "Exam deadline approaching: {$this->exam->title}",
            'last_chance' => "Last chance to take: {$this->exam->title}"
        ];

        return [
            'type' => 'exam_reminder',
            'title' => 'Exam Reminder',
            'message' => $messages[$this->reminderType],
            'data' => [
                'exam_id' => $this->exam->id,
                'exam_title' => $this->exam->title,
                'reminder_type' => $this->reminderType,
                'deadline' => $this->exam->end_date,
            ],
            'action_url' => route('cbt.exam', $this->exam->slug),
        ];
    }
}