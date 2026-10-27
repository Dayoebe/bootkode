<?php

namespace App\Console\Commands;

use App\Notifications\PortalEventNotification;
use Illuminate\Console\Command;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\DB;

class RetryPortalNotificationEmails extends Command
{
    protected $signature = 'bootkode:notification-email {--id= : Limit to a recorded portal notification UUID} {--send : Queue eligible recorded emails; otherwise report only}';

    protected $description = 'Inspect or retry recorded optional portal emails without recreating portal notifications';

    public function handle(): int
    {
        $query = DatabaseNotification::where('type', PortalEventNotification::class)->whereIn('data->mail_state', ['pending', 'not_queued', 'retry', 'failed', 'unavailable']);
        if ($this->option('id')) {
            $query->whereKey($this->option('id'));
        }
        if (! $this->option('send')) {
            $this->info($query->count().' recorded email(s) may need review; no messages queued.');

            return self::SUCCESS;
        }
        $queued = 0;
        $failed = 0;
        $query->orderBy('id')->chunkById(100, function ($records) use (&$queued, &$failed) {
            foreach ($records as $record) {
                $id = DB::transaction(function () use ($record) {
                    $row = DatabaseNotification::whereKey($record->id)->lockForUpdate()->first();
                    if (! $row || ! in_array($row->data['mail_state'] ?? '', ['pending', 'not_queued', 'retry', 'failed', 'unavailable'], true)) {
                        return null;
                    }
                    $row->update(['data' => array_merge($row->data, ['mail_state' => 'pending'])]);

                    return $row->id;
                });
                if ($id) {
                    if (app(\App\Services\PortalNotificationService::class)->queueEmail($id)) {
                        $queued++;
                    } else {
                        $failed++;
                    }
                }
            }
        });
        $this->info($queued.' recorded email(s) queued for current preference, source and transport checks.');

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
