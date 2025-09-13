<?php

// app/Livewire/Marketplace/Partial/VendorDashboard.php
namespace App\Livewire\Marketplace\Partial;

use Livewire\Component;
use App\Models\MarketplaceItem;
use App\Models\MarketplaceOrder;
use Carbon\Carbon;

class VendorDashboard extends Component
{
    public $dateRange = '30';
    
    public function render()
    {
        $vendorId = auth()->id();
        $startDate = now()->subDays((int)$this->dateRange);

        // Basic stats
        $stats = [
            'total_listings' => MarketplaceItem::byVendor($vendorId)->count(),
            'published_listings' => MarketplaceItem::byVendor($vendorId)->published()->count(),
            'pending_listings' => MarketplaceItem::byVendor($vendorId)->where('status', MarketplaceItem::STATUS_PENDING)->count(),
            'total_orders' => MarketplaceOrder::byVendor($vendorId)->count(),
            'total_revenue' => MarketplaceOrder::byVendor($vendorId)->paid()->sum('vendor_earning'),
            'pending_orders' => MarketplaceOrder::byVendor($vendorId)->where('status', MarketplaceOrder::STATUS_PENDING)->count(),
        ];

        // Recent orders
        $recentOrders = MarketplaceOrder::byVendor($vendorId)
            ->with(['customer', 'item'])
            ->latest()
            ->limit(10)
            ->get();

        // Top performing items
        $topItems = MarketplaceItem::byVendor($vendorId)
            ->published()
            ->orderBy('sales_count', 'desc')
            ->limit(5)
            ->get();

        // Revenue chart data (last 30 days)
        $revenueData = MarketplaceOrder::byVendor($vendorId)
            ->paid()
            ->where('paid_at', '>=', $startDate)
            ->selectRaw('DATE(paid_at) as date, SUM(vendor_earning) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Fill missing dates
        $chartData = [];
        for ($date = $startDate->copy(); $date <= now(); $date->addDay()) {
            $dateKey = $date->format('Y-m-d');
            $chartData[] = [
                'date' => $dateKey,
                'revenue' => $revenueData[$dateKey]->revenue ?? 0,
                'formatted_date' => $date->format('M d'),
            ];
        }

        return view('livewire.marketplace.partial.vendor-dashboard', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'topItems' => $topItems,
            'chartData' => $chartData,
        ]);
    }
}
