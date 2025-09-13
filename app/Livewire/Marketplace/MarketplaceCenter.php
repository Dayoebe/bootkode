<?php
// app/Livewire/Marketplace/MarketplaceCenter.php
namespace App\Livewire\Marketplace;

use Livewire\Component;
use App\Models\User;
use App\Models\MarketplaceItem;
use App\Models\MarketplaceOrder;
use Illuminate\Support\Facades\Route;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', [
    'title' => 'Marketplace', 
    'description' => 'Browse, buy and sell courses, resources and services', 
    'icon' => 'fas fa-store', 
    'active' => 'marketplace'
])]

class MarketplaceCenter extends Component
{
    public $activeTab = 'browse';
    public $user;

    // Statistics
    public $stats = [
        'total_items' => 0,
        'my_orders' => 0,
        'my_listings' => 0,
        'total_earnings' => 0,
    ];

    public function mount()
    {
        $this->user = auth()->user();

        // Set active tab based on route
        $currentRoute = Route::currentRouteName();
        $this->activeTab = match ($currentRoute) {
            'marketplace.browse' => 'browse',
            'marketplace.categories' => 'categories',
            'marketplace.product.show' => 'product-details',
            'marketplace.checkout' => 'checkout',
            'marketplace.purchases' => 'purchases',
            'marketplace.seller.create' => 'create-listing',
            'marketplace.seller.listings' => 'my-listings',
            'marketplace.seller.drafts' => 'drafts',
            'marketplace.vendor.dashboard' => 'vendor-dashboard',
            'marketplace.vendor.orders' => 'vendor-orders',
            'marketplace.vendor.withdrawals' => 'withdrawals',
            'marketplace.vendor.applications' => 'vendor-applications',
            'marketplace.orders' => 'all-orders',
            'marketplace.payments' => 'payments',
            'marketplace.promotions' => 'promotions',
            'marketplace.reviews' => 'reviews',
            'marketplace.analytics' => 'analytics',
            'marketplace.settings' => 'settings',
            'marketplace.integrations' => 'integrations',
            default => 'browse'
        };

        $this->loadStatistics();
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->loadStatistics();
    }

    protected function loadStatistics()
    {
        try {
            $this->stats['total_items'] = MarketplaceItem::published()->count();
            
            if ($this->user) {
                $this->stats['my_orders'] = MarketplaceOrder::byCustomer($this->user->id)->count();
                $this->stats['my_listings'] = MarketplaceItem::byVendor($this->user->id)->count();
                
                // Calculate total earnings for vendors
                $earnings = MarketplaceOrder::byVendor($this->user->id)
                    ->where('payment_status', MarketplaceOrder::PAYMENT_STATUS_PAID)
                    ->sum('vendor_earning');
                $this->stats['total_earnings'] = $earnings;
            }
        } catch (\Exception $e) {
            // Fallback if there are any issues
            $this->stats = [
                'total_items' => 0,
                'my_orders' => 0,
                'my_listings' => 0,
                'total_earnings' => 0,
            ];
        }
    }

    public function refreshStats()
    {
        $this->loadStatistics();
        session()->flash('message', 'Statistics refreshed successfully!');
    }

    public function render()
    {
        return view('livewire.marketplace.marketplace-center', [
            'user' => $this->user,
            'stats' => $this->stats,
        ]);
    }
}