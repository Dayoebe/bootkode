<?php 
// App/Console/Commands/SendExamReminders.php
namespace App\Console\Commands;

use App\Models\CbtExam;
use App\Models\User;
use App\Notifications\CbtExamReminder;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendExamReminders extends Command
{
    protected $signature = 'cbt:send-reminders';
    protected $description = 'Send exam reminders to students';

    public function handle()
    {
        $this->info('Sending exam reminders...');

        // Upcoming exams (24 hours before start)
        $upcomingExams = CbtExam::where('is_published', true)
            ->where('is_active', true)
            ->whereNotNull('start_date')
            ->whereBetween('start_date', [
                now()->addHours(23),
                now()->addHours(25)
            ])
            ->get();

        foreach ($upcomingExams as $exam) {
            $this->sendRemindersForExam($exam, 'upcoming');
        }

        // Deadline approaching (24 hours before end)
        $deadlineExams = CbtExam::where('is_published', true)
            ->where('is_active', true)
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [
                now()->addHours(23),
                now()->addHours(25)
            ])
            ->get();

        foreach ($deadlineExams as $exam) {
            $this->sendRemindersForExam($exam, 'deadline_approaching');
        }

        // Last chance (2 hours before end)
        $lastChanceExams = CbtExam::where('is_published', true)
            ->where('is_active', true)
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [
                now()->addHours(1),
                now()->addHours(3)
            ])
            ->get();

        foreach ($lastChanceExams as $exam) {
            $this->sendRemindersForExam($exam, 'last_chance');
        }

        $this->info('Exam reminders sent successfully.');
    }

    private function sendRemindersForExam($exam, $type)
    {
        // Get eligible users (enrolled in course, haven't completed exam)
        $eligibleUsers = User::whereHas('enrolledCourses', function($q) use ($exam) {
            $q->where('courses.id', $exam->course_id);
        })
        ->whereDoesntHave('cbtResults', function($q) use ($exam) {
            $q->where('cbt_exam_id', $exam->id)
              ->where('status', 'completed');
        })
        ->get();

        foreach ($eligibleUsers as $user) {
            $user->notify(new CbtExamReminder($exam, $type));
        }

        $this->line("Sent {$type} reminders for '{$exam->title}' to {$eligibleUsers->count()} users");
    }
}

