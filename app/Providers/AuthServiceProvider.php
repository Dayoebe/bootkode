<?php

// Add this to app/Providers/AuthServiceProvider.php

use App\Models\Marketplace\MarketplaceItem;
use App\Models\Marketplace\MarketplaceOrder;
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