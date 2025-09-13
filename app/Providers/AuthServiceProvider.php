<?php

// Add this to app/Providers/AuthServiceProvider.php

use App\Models\MarketplaceItem;
use App\Models\MarketplaceOrder;
use App\Policies\MarketplaceItemPolicy;
use App\Policies\MarketplaceOrderPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // ... existing policies
        MarketplaceItem::class => MarketplaceItemPolicy::class,
        MarketplaceOrder::class => MarketplaceOrderPolicy::class,
    ];
}