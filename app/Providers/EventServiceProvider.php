<?php 

// app/Providers/EventServiceProvider.php (Add this to your existing file)
namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\CoursePurchased;
use App\Listeners\ProcessAffiliateCommission;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        // Add to your existing events
        CoursePurchased::class => [
            ProcessAffiliateCommission::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}


