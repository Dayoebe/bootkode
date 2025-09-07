<?php

// app/Providers/FinancialServiceProvider.php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\PaystackService;
use App\Services\WalletService;

class FinancialServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(PaystackService::class, function ($app) {
            return new PaystackService();
        });

        $this->app->singleton(WalletService::class, function ($app) {
            return new WalletService($app->make(PaystackService::class));
        });
    }

    public function boot()
    {
        //
    }
}
