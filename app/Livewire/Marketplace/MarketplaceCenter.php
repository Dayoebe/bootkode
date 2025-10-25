<?php
// app/Livewire/Marketplace/MarketplaceCenter.php
namespace App\Livewire\Marketplace;

use Livewire\Component;
use App\Models\Core\User;
use App\Models\Marketplace\MarketplaceItem;
use App\Models\Marketplace\MarketplaceOrder;
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

        // Set active tab based on route - Updated for consolidated structure
        $currentRoute = Route::currentRouteName();
        $this->activeTab = match ($currentRoute) {
            'marketplace.browse', 'marketplace.categories', 'marketplace.product.show' => 'browse',
            'marketplace.checkout', 'marketplace.purchases' => 'shopping',
            'marketplace.seller.create', 'marketplace.seller.listings', 'marketplace.seller.drafts' => 'vendor',
            'marketplace.vendor.dashboard', 'marketplace.vendor.orders', 'marketplace.vendor.withdrawals' => 'business',
            'marketplace.vendor.applications', 'marketplace.orders', 'marketplace.payments' => 'admin',
            'marketplace.promotions', 'marketplace.reviews' => 'content',
            'marketplace.analytics', 'marketplace.settings' => 'system',
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