<?php

// app/Livewire/Marketplace/Partial/MarketplaceAnalytics.php
namespace App\Livewire\Marketplace\Partial;

use Livewire\Component;
use App\Models\MarketplaceItem;
use App\Models\MarketplaceOrder;
use App\Models\User;
use Carbon\Carbon;

class MarketplaceAnalytics extends Component
{
    public $dateRange = '30';
    public $selectedMetric = 'revenue';

    public function render()
    {
        $startDate = now()->subDays((int)$this->dateRange);

        // Overview stats
        $stats = [
            'total_items' => MarketplaceItem::count(),
            'published_items' => MarketplaceItem::published()->count(),
            'pending_items' => MarketplaceItem::where('status', MarketplaceItem::STATUS_PENDING)->count(),
            'total_vendors' => User::whereHas('marketplaceItems')->count(),
            'total_orders' => MarketplaceOrder::count(),
            'total_revenue' => MarketplaceOrder::paid()->sum('total_amount'),
            'platform_commission' => MarketplaceOrder::paid()->sum('platform_commission'),
            'vendor_earnings' => MarketplaceOrder::paid()->sum('vendor_earning'),
        ];

        // Top categories
        $topCategories = MarketplaceItem::published()
            ->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(categories, '$[0]')) as category, COUNT(*) as count, SUM(sales_count) as total_sales")
            ->groupBy('category')
            ->orderBy('total_sales', 'desc')
            ->limit(10)
            ->get();

        // Top vendors
        $topVendors = User::withCount(['marketplaceItems as total_listings'])
            ->withSum(['vendorOrders as total_earnings' => function($query) {
                $query->paid();
            }], 'vendor_earning')
            ->having('total_listings', '>', 0)
            ->orderBy('total_earnings', 'desc')
            ->limit(10)
            ->get();

        // Revenue trend
        $revenueTrend = MarketplaceOrder::paid()
            ->where('paid_at', '>=', $startDate)
            ->selectRaw('DATE(paid_at) as date, COUNT(*) as orders, SUM(total_amount) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Fill missing dates for chart
        $chartData = [];
        for ($date = $startDate->copy(); $date <= now(); $date->addDay()) {
            $dateKey = $date->format('Y-m-d');
            $data = $revenueTrend[$dateKey] ?? (object)['orders' => 0, 'revenue' => 0];
            
            $chartData[] = [
                'date' => $dateKey,
                'formatted_date' => $date->format('M d'),
                'orders' => $data->orders,
                'revenue' => $data->revenue,
            ];
        }

        return view('livewire.marketplace.partial.marketplace-analytics', [
            'stats' => $stats,
            'topCategories' => $topCategories,
            'topVendors' => $topVendors,
            'chartData' => $chartData,
        ]);
    }
}