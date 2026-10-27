<?php

namespace App\Console\Commands;

use App\Services\ReviewReminderService;
use Illuminate\Console\Command;

class SendReviewReminders extends Command
{
    protected $signature = 'reviews:send-reminders {--dry-run : Count eligible reminders without creating records}';

    protected $description = 'Send review reminders to eligible students';

    public function handle(ReviewReminderService $reminderService)
    {
        $count = $reminderService->processReminders((bool) $this->option('dry-run'));
        $this->info($count.($this->option('dry-run') ? ' eligible review reminder(s); no records changed.' : ' new portal review reminder(s) recorded. Email follows current preferences and transport availability.'));

        return Command::SUCCESS;
    }
}
