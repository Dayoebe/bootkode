<?php

namespace App\Providers;

use App\Services\ObservabilityService;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->scoped(\App\Services\PublicCatalogService::class);
    }

    public function boot()
    {
        \Livewire\Livewire::addPersistentMiddleware([\App\Http\Middleware\CheckRole::class]);
        \Illuminate\Support\Facades\View::composer('components.footer', function ($view) {
            $view->with('publicCatalogStats', app(\App\Services\PublicCatalogService::class)->statistics());
        });

        Queue::failing(function (JobFailed $event): void {
            app(ObservabilityService::class)->recordFailedJob($event);
        });

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
