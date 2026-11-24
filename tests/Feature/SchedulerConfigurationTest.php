<?php

namespace Tests\Feature;

use Illuminate\Console\Scheduling\Schedule;
use Tests\TestCase;

class SchedulerConfigurationTest extends TestCase
{
    public function test_laravel_12_active_schedule_contains_every_operational_task(): void
    {
        $commands = collect(app(Schedule::class)->events())
            ->map(fn ($event): string => (string) $event->command);

        foreach ([
            'newsletter:send-scheduled',
            'gamification:reset-daily-quests',
            'reviews:generate-analytics',
            'withdrawals:process',
            'bootkode:job-alerts',
            'bootkode:cbt-reminders',
            'reviews:send-reminders',
        ] as $signature) {
            $this->assertTrue($commands->contains(fn (string $command): bool => str_contains($command, $signature)), $signature);
        }

        $this->assertFileDoesNotExist(app_path('Console/Kernel.php'));
        $this->assertFalse(config('delivery.withdrawal_processing_schedule_enabled'));
    }
}
