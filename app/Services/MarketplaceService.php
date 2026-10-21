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
    public function checkoutWallet(User $customer, array $rows, string $checkoutKey, string $notes = ''): array
    {
        return DB::transaction(function () use ($customer, $rows, $checkoutKey, $notes) {
            User::whereKey($customer->id)->lockForUpdate()->firstOrFail();
            $existing = MarketplaceOrder::byCustomer($customer->id)->where('payment_details->checkout_key', $checkoutKey)->get();
            if ($existing->isNotEmpty()) {
                return $existing->all();
            }
            if (!$rows || count($rows) > 30 || !\Illuminate\Support\Str::isUuid($checkoutKey)) {
                throw new \DomainException('Please review your cart before checkout.');
            }
            $wallet = Wallet::getOrCreateWallet($customer->id);
            $wallet = Wallet::lockForUpdate()->findOrFail($wallet->id);
            if (!$wallet->is_active || $wallet->currency !== 'NGN') {
                throw new \DomainException('A current NGN wallet is required.');
            }
            $orders = [];
            foreach ($rows as $row) {
                $quantity = filter_var($row['quantity'] ?? 1, FILTER_VALIDATE_INT);
                if ($quantity === false || $quantity < 1 || $quantity > 10) {
                    throw new \DomainException('Quantity must be between 1 and 10.');
                }
                $item = MarketplaceItem::published()->lockForUpdate()->findOrFail($row['item_id'] ?? null);
                if ($item->vendor_id === $customer->id || ($item->currency ?: 'NGN') !== 'NGN') {
                    throw new \DomainException('This item cannot be purchased with your wallet.');
                }
                $amount = (float) $item->getEffectivePrice();
                if (!is_finite($amount) || $amount < 0 || $amount > (float) $item->price) {
                    throw new \DomainException('This item has an invalid price.');
                }
                if ($item->is_digital && MarketplaceOrder::byCustomer($customer->id)->paid()->where('item_id', $item->id)->exists()) {
                    throw new \DomainException('You already own this digital item.');
                }
                $settings = \Illuminate\Support\Facades\Cache::get('marketplace_settings', []);
                $commission = (float) ($settings['platform_commission'] ?? config('marketplace.commission_rate', 20));
                if ($commission < 0 || $commission > 100) {
                    throw new \DomainException('Marketplace commission is not configured correctly.');
                }
                for ($i = 0; $i < $quantity; $i++) {
                    $order = MarketplaceOrder::create([
                        'customer_id' => $customer->id, 'vendor_id' => $item->vendor_id, 'item_id' => $item->id,
                        'item_price' => $item->price, 'discount_amount' => (float) $item->price - $amount,
                        'total_amount' => $amount, 'currency' => 'NGN', 'platform_commission_rate' => $commission,
                        'payment_method' => 'wallet', 'status' => MarketplaceOrder::STATUS_PENDING,
                        'payment_status' => MarketplaceOrder::PAYMENT_STATUS_UNPAID, 'customer_notes' => $notes,
                    ]);
                    $debit = $amount > 0 ? $wallet->debit($amount, WalletTransaction::CATEGORY_COURSE_PURCHASE,
                        'Marketplace purchase: '.$item->title, $order) : null;
                    $this->settleWalletOrder($order, ['wallet_transaction_id' => $debit?->id, 'checkout_key' => $checkoutKey]);
                    $orders[] = $order->fresh();
                }
            }

            return $orders;
        });
    }

    public function settleWalletOrder(MarketplaceOrder $order, array $details = []): void
    {
        DB::transaction(function () use ($order, $details) {
            $locked = MarketplaceOrder::lockForUpdate()->findOrFail($order->id);
            if ($locked->isPaid()) {
                return;
            }
            if ($locked->payment_status !== MarketplaceOrder::PAYMENT_STATUS_UNPAID || $locked->isCancelled()) {
                throw new \DomainException('This order cannot accept payment.');
            }
            $this->assertWalletPayment($locked, $details['wallet_transaction_id'] ?? null);
            $locked->update([
                'payment_status' => MarketplaceOrder::PAYMENT_STATUS_PAID, 'paid_at' => now(),
                'payment_method' => 'wallet', 'payment_details' => array_merge($details, ['payment_method' => 'wallet']),
                'status' => MarketplaceOrder::STATUS_CONFIRMED, 'confirmed_at' => now(),
            ]);
            $this->payoutOrder($locked);
            $locked->item->increment('sales_count');
            app(CommercialReadinessService::class)->issueInvoiceForOrder($locked->fresh(['customer', 'item']));
        });
        $order->refresh();
    }

    private function assertWalletPayment(MarketplaceOrder $order, $transactionId): void
    {
        if ((float) $order->total_amount === 0.0) {
            return;
        }
        $debit = WalletTransaction::with('wallet')->find($transactionId);
        if (!$debit || $debit->type !== 'debit' || $debit->status !== 'completed'
            || $debit->transactionable_type !== $order->getMorphClass() || (int) $debit->transactionable_id !== $order->id
            || (int) $debit->wallet->user_id !== $order->customer_id || $debit->wallet->currency !== $order->currency
            || (int) round((float) $debit->amount * 100) !== (int) round((float) $order->total_amount * 100)) {
            throw new \DomainException('A matching verified wallet debit is required.');
        }
    }

    public function payoutOrder(MarketplaceOrder $order): bool
    {
        return DB::transaction(function () use ($order) {
            $order = MarketplaceOrder::lockForUpdate()->findOrFail($order->id);
            if (!$order->isPaid()) {
                return false;
            }
            $this->assertWalletPayment($order, $order->payment_details['wallet_transaction_id'] ?? null);
            if ((float) $order->vendor_earning > 0 && !$order->walletTransactions()->where('type', 'credit')->where('category', WalletTransaction::CATEGORY_INSTRUCTOR_EARNING)->exists()) {
                $this->settlementWallet($order->vendor_id, Wallet::TYPE_INSTRUCTOR, $order->currency)->credit(
                    (float) $order->vendor_earning, WalletTransaction::CATEGORY_INSTRUCTOR_EARNING,
                    'Marketplace sale: '.$order->order_number, $order);
            }
            if ((float) $order->platform_commission > 0 && !$order->walletTransactions()->where('type', 'credit')->where('category', 'platform_commission')->exists()) {
                $owner = config('marketplace.platform_user_id')
                    ? User::find(config('marketplace.platform_user_id'))
                    : User::role(User::ROLE_SUPER_ADMIN)->orderBy('id')->first();
                if (!$owner || !$owner->hasRole(User::ROLE_SUPER_ADMIN)) {
                    throw new \DomainException('A platform wallet owner must be configured.');
                }
                $this->settlementWallet($owner->id, Wallet::TYPE_PLATFORM, $order->currency)->credit(
                    (float) $order->platform_commission, 'platform_commission',
                    'Marketplace commission: '.$order->order_number, $order);
            }

            return true;
        });
    }

    private function settlementWallet(int $userId, string $type, string $currency): Wallet
    {
        $wallet = Wallet::getOrCreateWallet($userId, $type);
        $wallet = Wallet::lockForUpdate()->findOrFail($wallet->id);
        if (!$wallet->is_active || $wallet->currency !== $currency) {
            throw new \DomainException('Settlement requires an active wallet in the order currency.');
        }

        return $wallet;
    }

    public function refundOrder(MarketplaceOrder $order, ?float $amount = null, ?string $reason = null): void
    {
        abort_unless(auth()->user()?->hasAnyRole(['super_admin', 'academy_admin']), 403);
        DB::transaction(function () use ($order, $amount, $reason) {
            $order = MarketplaceOrder::lockForUpdate()->findOrFail($order->id);
            if ($order->isRefunded()) {
                return;
            }
            if (!$order->isPaid() || ($amount !== null && (int) round($amount * 100) !== (int) round((float) $order->total_amount * 100))) {
                throw new \DomainException('Only a full refund of a paid wallet order is supported here.');
            }
            $this->assertWalletPayment($order, $order->payment_details['wallet_transaction_id'] ?? null);
            // Recover the actual credited wallets, including an older configured owner.
            foreach ($order->walletTransactions()->where('type', 'credit')->whereIn('category', [WalletTransaction::CATEGORY_INSTRUCTOR_EARNING, 'platform_commission'])->get() as $credit) {
                $credit->wallet->debit((float) $credit->amount, WalletTransaction::CATEGORY_REFUND,
                    'Reverse marketplace payout: '.$order->order_number, $order);
            }
            if ((float) $order->total_amount > 0) {
                $this->settlementWallet($order->customer_id, Wallet::TYPE_USER, $order->currency)->credit((float) $order->total_amount,
                    WalletTransaction::CATEGORY_REFUND, 'Marketplace refund: '.$order->order_number, $order, ['reason' => $reason]);
            }
            $order->update(['status' => MarketplaceOrder::STATUS_REFUNDED, 'payment_status' => MarketplaceOrder::PAYMENT_STATUS_REFUNDED, 'admin_notes' => $reason]);
            \App\Models\Commerce\CommercialDocument::where('marketplace_order_id', $order->id)->where('type', 'invoice')->update([
                'status' => 'refunded', 'amount_refunded' => $order->total_amount, 'refunded_at' => now(),
            ]);
        });
        $order->refresh();
    }

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
        try {
            $this->settleWalletOrder($order, $paymentDetails);
            return ['success' => true, 'message' => 'Verified wallet payment processed.'];
        } catch (\Throwable $exception) {
            return ['success' => false, 'message' => 'Payment could not be verified.'];
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
