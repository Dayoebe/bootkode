<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Register newsletter commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Console\Commands\SendScheduledNewsletters::class,
            ]);
        }

        // Only schedule tasks when the Schedule class is available
        if (class_exists(Schedule::class) && $this->app->bound(Schedule::class)) {
            $this->app->booted(function () {
                $schedule = $this->app->make(Schedule::class);

                // Check for scheduled campaigns every minute
                $schedule->command('newsletter:send-scheduled')
                    ->everyMinute()
                    ->withoutOverlapping();
            });
        }
    }
}
