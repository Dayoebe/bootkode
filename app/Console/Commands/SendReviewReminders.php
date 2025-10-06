<?php

namespace App\Console\Commands;

use App\Services\ReviewReminderService;
use Illuminate\Console\Command;

class SendReviewReminders extends Command
{
    protected $signature = 'reviews:send-reminders';
    protected $description = 'Send review reminders to eligible students';

    public function handle(ReviewReminderService $reminderService)
    {
        $this->info('Processing review reminders...');

        $remindersSent = $reminderService->processReminders();

        $this->info("Successfully sent {$remindersSent} review reminders!");

        return Command::SUCCESS;
    }
}