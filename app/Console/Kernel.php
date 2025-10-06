<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     *
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('gamification:reset-daily-quests')->dailyAt('00:01');
        $schedule->command('withdrawals:process')->everyFifteenMinutes();
        $schedule->command('reviews:generate-analytics')->daily();
        $schedule->command('reviews:send-reminders')->dailyAt('10:00');

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    protected $commands = [
        \App\Console\Commands\ResetDailyQuests::class,
    
    ];
}
    