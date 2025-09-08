<?php 
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AffiliateService;
use App\Services\WalletService;

class AffiliateServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(AffiliateService::class, function ($app) {
            return new AffiliateService($app->make(WalletService::class));
        });
    }

    public function boot()
    {
        // Register custom wallet transaction categories
        if (!defined('WalletTransaction::CATEGORY_REFERRAL_COMMISSION')) {
            define('App\Models\WalletTransaction::CATEGORY_REFERRAL_COMMISSION', 'referral_commission');
        }
    }
}