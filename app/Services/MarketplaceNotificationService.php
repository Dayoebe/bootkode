<?php 

// app/Services/MarketplaceNotificationService.php
namespace App\Services;

use App\Models\Core\User;
use App\Models\Marketplace\MarketplaceItem;
use App\Models\Marketplace\MarketplaceOrder;
use Illuminate\Support\Facades\Notification;

class MarketplaceNotificationService
{
    public function notifyAdminsOfNewItem(MarketplaceItem $item)
    {
        $admins = User::whereIn('role', [User::ROLE_SUPER_ADMIN, User::ROLE_ACADEMY_ADMIN])->get();
        
        Notification::send($admins, new \App\Notifications\NewMarketplaceItemSubmitted($item));
    }

    public function notifyVendorOfOrderStatusChange(MarketplaceOrder $order, string $oldStatus)
    {
        if ($order->status !== $oldStatus) {
            $order->vendor->notify(new \App\Notifications\MarketplaceOrderStatusChanged($order, $oldStatus));
        }
    }

    public function notifyCustomerOfOrderStatusChange(MarketplaceOrder $order, string $oldStatus)
    {
        if ($order->status !== $oldStatus) {
            $order->customer->notify(new \App\Notifications\MarketplaceOrderStatusChanged($order, $oldStatus));
        }
    }

    public function sendWeeklyVendorReport(User $vendor)
    {
        $weekStart = now()->startOfWeek();
        
        $stats = [
            'new_orders' => MarketplaceOrder::byVendor($vendor->id)
                ->where('created_at', '>=', $weekStart)
                ->count(),
            'total_revenue' => MarketplaceOrder::byVendor($vendor->id)
                ->paid()
                ->where('paid_at', '>=', $weekStart)
                ->sum('vendor_earning'),
            'top_item' => MarketplaceItem::byVendor($vendor->id)
                ->orderBy('views_count', 'desc')
                ->first(),
        ];

        $vendor->notify(new \App\Notifications\WeeklyVendorReport($stats));
    }
}