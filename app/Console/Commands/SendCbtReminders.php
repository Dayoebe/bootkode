<?php

namespace App\Console\Commands;

use App\Services\CbtReminderService;
use Illuminate\Console\Command;

class SendCbtReminders extends Command
{
    protected $signature = 'bootkode:cbt-reminders {--dry-run : Count eligible upcoming reminders without creating records}';

    protected $description = 'Record eligible course CBT deadline reminders in private learner portals';

    public function handle(CbtReminderService $service): int
    {
        $count = 0;
        $service->candidates()->orderBy('id')->chunkById(100, function ($assessments) use ($service, &$count) {
            foreach ($assessments as $assessment) {
                $service->recipients($assessment)->orderBy('id')->chunkById(100, function ($users) use ($service, $assessment, &$count) {
                    foreach ($users as $user) {
                        if ($this->option('dry-run') ? $service->eligible($user, $assessment) : $service->deliver($user, $assessment)) {
                            $count++;
                        }
                    }
                });
            }
        });
        $this->info($count.($this->option('dry-run') ? ' eligible reminder(s); no records changed.' : ' new portal reminder(s) recorded. Email follows current preferences and transport availability.'));

        return self::SUCCESS;
    }
}
