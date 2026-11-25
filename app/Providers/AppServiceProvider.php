<?php

namespace App\Providers;

use App\Services\ObservabilityService;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->scoped(\App\Services\PublicCatalogService::class);
    }

    public function boot()
    {
        if (PHP_VERSION_ID >= 80600) {
            Session::extend('file', function ($app) {
                $config = $app['config']->get('session');
                return new \App\Session\FileSessionHandler(
                    $app['files'],
                    $config['files'],
                    $config['lifetime']
                );
            });
        }

        Gate::policy(\App\Models\Content\Portfolio::class, \App\Policies\PortfolioPolicy::class);
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

    }
}
