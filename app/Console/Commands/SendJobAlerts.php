<?php

namespace App\Console\Commands;

use App\Models\Career\JobAlert;
use App\Services\JobAlertService;
use Illuminate\Console\Command;

class SendJobAlerts extends Command
{
    protected $signature = 'bootkode:job-alerts {--dry-run : Count due alerts without delivery or timestamp changes}';

    protected $description = 'Record due public job matches in each subscriber portal and queue opted-in email';

    public function handle(JobAlertService $service): int
    {
        if (! $service->ready()) {
            $this->error('Saved alert storage needs the reviewed forward migration.');

            return self::FAILURE;
        }
        if ($this->option('dry-run')) {
            $this->info(JobAlert::due()->count().' due saved alerts; no records changed.');

            return self::SUCCESS;
        }
        $delivered = 0;
        JobAlert::due()->orderBy('id')->chunkById(100, function ($alerts) use ($service, &$delivered) {
            foreach ($alerts as $alert) {
                if ($service->deliver($alert->id) > 0) {
                    $delivered++;
                }
            }
        });
        $this->info($delivered.' saved alert(s) recorded in subscriber portals. Email follows preferences and transport availability.');

        return self::SUCCESS;
    }
}
