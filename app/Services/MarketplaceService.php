<?php
// app/Services/MarketplaceService.php
namespace App\Services;

use App\Models\Marketplace\MarketplaceItem;
use App\Models\Marketplace\MarketplaceOrder;
use App\Models\Core\User;
use App\Models\Marketplace\Wallet;
use App\Models\Marketplace\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Notifications\MarketplaceItemApproved;
use App\Notifications\MarketplaceItemRejected;
use App\Notifications\MarketplaceOrderConfirmed;

class MarketplaceService
{
    public function processItemApproval(MarketplaceItem $item, User $approver)
    {
        DB::beginTransaction();
        
        try {
            $item->approve($approver->id);
            
            // Send notification to vendor
            $item->vendor->notify(new MarketplaceItemApproved($item));
            
            // Log activity
            activity()
                ->causedBy($approver)
                ->performedOn($item)
                ->log("Marketplace item '{$item->title}' was approved");
            
            DB::commit();
            
            return ['success' => true, 'message' => 'Item approved successfully'];
            
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => 'Failed to approve item: ' . $e->getMessage()];
        }
    }

    public function processItemRejection(MarketplaceItem $item, string $reason, User $rejecter)
    {
        DB::beginTransaction();
        
        try {
            $item->reject($reason, $rejecter->id);
            
            // Send notification to vendor
            $item->vendor->notify(new MarketplaceItemRejected($item, $reason));
            
            // Log activity
            activity()
                ->causedBy($rejecter)
                ->performedOn($item)
                ->withProperties(['reason' => $reason])
                ->log("Marketplace item '{$item->title}' was rejected");
            
            DB::commit();
            
            return ['success' => true, 'message' => 'Item rejected successfully'];
            
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => 'Failed to reject item: ' . $e->getMessage()];
        }
    }

    public function processOrderPayment(MarketplaceOrder $order, array $paymentDetails = [])
    {
        DB::beginTransaction();
        
        try {
            // Mark order as paid
            $order->markAsPaid($paymentDetails);
            
            // Update item sales count
            $order->item->increment('sales_count');
            
            // Send notifications
            $order->customer->notify(new MarketplaceOrderConfirmed($order));
            $order->vendor->notify(new \App\Notifications\MarketplaceOrderReceived($order));
            
            // Log activity
            activity()
                ->causedBy($order->customer)
                ->performedOn($order)
                ->withProperties($paymentDetails)
                ->log("Payment processed for order {$order->order_number}");
            
            DB::commit();
            
            return ['success' => true, 'message' => 'Payment processed successfully'];
            
        } catch (\Exception $e) {
            DB::rollBack();
            return ['success' => false, 'message' => 'Failed to process payment: ' . $e->getMessage()];
        }
    }

    public function calculateRevenueSplit(float $amount, float $platformCommissionRate = 20.0): array
    {
        $platformCommission = ($amount * $platformCommissionRate) / 100;
        $vendorEarning = $amount - $platformCommission;
        
        return [
            'total_amount' => $amount,
            'platform_commission' => round($platformCommission, 2),
            'vendor_earning' => round($vendorEarning, 2),
            'commission_rate' => $platformCommissionRate,
        ];
    }

    public function getMarketplaceAnalytics(int $days = 30): array
    {
        $startDate = now()->subDays($days);
        
        return [
            'overview' => [
                'total_items' => MarketplaceItem::count(),
                'published_items' => MarketplaceItem::published()->count(),
                'pending_items' => MarketplaceItem::where('status', MarketplaceItem::STATUS_PENDING)->count(),
                'total_vendors' => User::whereHas('marketplaceItems')->count(),
                'total_orders' => MarketplaceOrder::count(),
                'total_revenue' => MarketplaceOrder::paid()->sum('total_amount'),
                'platform_earnings' => MarketplaceOrder::paid()->sum('platform_commission'),
                'vendor_earnings' => MarketplaceOrder::paid()->sum('vendor_earning'),
            ],
            'period_stats' => [
                'new_items' => MarketplaceItem::where('created_at', '>=', $startDate)->count(),
                'new_orders' => MarketplaceOrder::where('created_at', '>=', $startDate)->count(),
                'period_revenue' => MarketplaceOrder::paid()->where('paid_at', '>=', $startDate)->sum('total_amount'),
                'avg_order_value' => MarketplaceOrder::paid()->where('paid_at', '>=', $startDate)->avg('total_amount'),
            ],
            'top_performers' => [
                'items' => MarketplaceItem::published()
                    ->orderBy('sales_count', 'desc')
                    ->limit(10)
                    ->get(['id', 'title', 'sales_count', 'price']),
                'vendors' => User::withSum(['vendorOrders as earnings' => fn($q) => $q->paid()], 'vendor_earning')
                    ->having('earnings', '>', 0)
                    ->orderBy('earnings', 'desc')
                    ->limit(10)
                    ->get(['id', 'name', 'earnings']),
            ],
        ];
    }

    public function processAutomaticPayouts()
    {
        // Process automatic payouts for vendors (could be run via scheduler)
        $eligibleOrders = MarketplaceOrder::paid()
            ->where('created_at', '<=', now()->subDays(7)) // 7 day holding period
            ->whereDoesntHave('walletTransactions', function($q) {
                $q->where('category', WalletTransaction::CATEGORY_INSTRUCTOR_EARNING);
            })
            ->get();

        $payoutResults = ['processed' => 0, 'failed' => 0, 'total_amount' => 0];

        foreach ($eligibleOrders as $order) {
            try {
                $order->processVendorPayment();
                $payoutResults['processed']++;
                $payoutResults['total_amount'] += $order->vendor_earning;
            } catch (\Exception $e) {
                $payoutResults['failed']++;
                \Log::error("Failed to process payout for order {$order->id}: " . $e->getMessage());
            }
        }

        return $payoutResults;
    }
}
