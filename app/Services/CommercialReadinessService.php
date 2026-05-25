<?php

namespace App\Services;

use App\Models\Commerce\CommercialDocument;
use App\Models\Commerce\PayoutAudit;
use App\Models\Commerce\PricingPackage;
use App\Models\Commerce\RefundRequest;
use App\Models\Core\User;
use App\Models\Marketplace\Affiliate;
use App\Models\Marketplace\MarketplaceOrder;
use App\Models\Marketplace\PaystackTransaction;
use App\Models\Marketplace\ReferralTransaction;
use App\Models\Marketplace\Wallet;
use App\Models\Marketplace\WalletTransaction;
use App\Models\Marketplace\Withdrawal;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Throwable;

class CommercialReadinessService
{
    public function tablesReady(): bool
    {
        return Schema::hasTable('commercial_documents')
            && Schema::hasTable('refund_requests')
            && Schema::hasTable('payout_audits')
            && Schema::hasTable('pricing_packages');
    }

    public function publicPackages(): Collection
    {
        if (Schema::hasTable('pricing_packages')) {
            $packages = PricingPackage::publicActive()->get();

            if ($packages->isNotEmpty()) {
                return $packages->map(fn (PricingPackage $package) => $this->normalizePackage([
                    'name' => $package->name,
                    'slug' => $package->slug,
                    'audience' => $package->audience,
                    'description' => $package->description,
                    'price' => $package->price !== null ? (float) $package->price : null,
                    'currency' => $package->currency,
                    'interval' => $package->interval,
                    'features' => $package->features ?? [],
                    'limits' => $package->limits ?? [],
                    'cta_label' => $package->cta_label,
                    'cta_route' => $package->cta_route,
                    'is_featured' => $package->is_featured,
                ]));
            }
        }

        return collect(config('commercial.public_packages', []))
            ->map(fn (array $package) => $this->normalizePackage($package));
    }

    public function seedDefaultPackages(): int
    {
        if (! Schema::hasTable('pricing_packages')) {
            return 0;
        }

        $created = 0;

        foreach (config('commercial.public_packages', []) as $index => $package) {
            $model = PricingPackage::firstOrNew(['slug' => $package['slug']]);
            $model->fill([
                'name' => $package['name'],
                'audience' => $package['audience'] ?? null,
                'description' => $package['description'] ?? null,
                'price' => $package['price'] ?? null,
                'currency' => $package['currency'] ?? config('commercial.currency', 'NGN'),
                'interval' => $package['interval'] ?? 'one-time',
                'features' => $package['features'] ?? [],
                'limits' => $package['limits'] ?? [],
                'cta_label' => $package['cta_label'] ?? 'Get started',
                'cta_route' => $package['cta_route'] ?? 'register',
                'sort_order' => $index + 1,
                'is_public' => true,
                'is_featured' => (bool) ($package['is_featured'] ?? false),
                'status' => PricingPackage::STATUS_ACTIVE,
            ]);

            if (! $model->exists) {
                $created++;
            }

            $model->save();
        }

        return $created;
    }

    public function revenueReport(?string $dateFrom = null, ?string $dateTo = null): array
    {
        [$start, $end] = $this->dateRange($dateFrom, $dateTo);

        $successfulPayments = Schema::hasTable('paystack_transactions')
            ? PaystackTransaction::where('status', PaystackTransaction::STATUS_SUCCESS)
                ->whereBetween('created_at', [$start, $end])
                ->sum('amount')
            : 0;

        $refunds = Schema::hasTable('refund_requests')
            ? RefundRequest::whereIn('status', [RefundRequest::STATUS_APPROVED, RefundRequest::STATUS_PROCESSED])
                ->whereBetween('created_at', [$start, $end])
                ->sum('amount')
            : $this->fallbackRefundTotal($start, $end);

        $walletCourseSales = Schema::hasTable('wallet_transactions')
            ? WalletTransaction::where('category', WalletTransaction::CATEGORY_COURSE_PURCHASE)
                ->whereBetween('created_at', [$start, $end])
                ->sum('amount')
            : 0;

        $marketplaceRevenue = Schema::hasTable('marketplace_orders')
            ? MarketplaceOrder::whereIn('payment_status', [
                    MarketplaceOrder::PAYMENT_STATUS_PAID,
                    MarketplaceOrder::PAYMENT_STATUS_PARTIALLY_REFUNDED,
                ])
                ->whereBetween('created_at', [$start, $end])
                ->sum('total_amount')
            : 0;

        $platformCommission = $this->platformCommission($start, $end);
        $instructorEarnings = $this->instructorEarnings($start, $end);
        $affiliateCommission = $this->affiliateCommission($start, $end);
        $completedPayouts = $this->completedPayouts($start, $end);
        $pendingPayouts = Schema::hasTable('withdrawals')
            ? Withdrawal::where('status', Withdrawal::STATUS_PENDING)->sum('amount')
            : 0;

        $gross = (float) $successfulPayments + (float) $walletCourseSales + (float) $marketplaceRevenue;
        $net = max(0, $gross - (float) $refunds);

        return [
            'period' => [
                'from' => $start->toDateString(),
                'to' => $end->toDateString(),
            ],
            'totals' => [
                'gross_revenue' => $gross,
                'net_revenue' => $net,
                'successful_payments' => (float) $successfulPayments,
                'wallet_course_sales' => (float) $walletCourseSales,
                'marketplace_revenue' => (float) $marketplaceRevenue,
                'refunds' => (float) $refunds,
                'platform_commission' => (float) $platformCommission,
                'instructor_earnings' => (float) $instructorEarnings,
                'affiliate_commission' => (float) $affiliateCommission,
                'completed_payouts' => (float) $completedPayouts,
                'pending_payouts' => (float) $pendingPayouts,
                'documents_issued' => Schema::hasTable('commercial_documents')
                    ? CommercialDocument::whereBetween('created_at', [$start, $end])->count()
                    : 0,
                'refund_requests' => Schema::hasTable('refund_requests')
                    ? RefundRequest::whereBetween('created_at', [$start, $end])->count()
                    : 0,
            ],
            'formatted' => [
                'gross_revenue' => $this->money($gross),
                'net_revenue' => $this->money($net),
                'successful_payments' => $this->money((float) $successfulPayments),
                'wallet_course_sales' => $this->money((float) $walletCourseSales),
                'marketplace_revenue' => $this->money((float) $marketplaceRevenue),
                'refunds' => $this->money((float) $refunds),
                'platform_commission' => $this->money((float) $platformCommission),
                'instructor_earnings' => $this->money((float) $instructorEarnings),
                'affiliate_commission' => $this->money((float) $affiliateCommission),
                'completed_payouts' => $this->money((float) $completedPayouts),
                'pending_payouts' => $this->money((float) $pendingPayouts),
            ],
            'breakdown' => $this->dailyBreakdown($start, $end),
        ];
    }

    public function recentDocuments(int $limit = 10): Collection
    {
        if (! Schema::hasTable('commercial_documents')) {
            return collect();
        }

        return CommercialDocument::with(['user', 'paystackTransaction', 'marketplaceOrder'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function recentRefunds(int $limit = 10): Collection
    {
        if (! Schema::hasTable('refund_requests')) {
            return collect();
        }

        return RefundRequest::with(['user', 'requester', 'processor', 'paystackTransaction'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function recentPayoutAudits(int $limit = 12): Collection
    {
        if (! Schema::hasTable('payout_audits')) {
            return collect();
        }

        return PayoutAudit::with(['withdrawal', 'user', 'actor'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function generateMissingReceipts(int $limit = 50): int
    {
        if (! Schema::hasTable('commercial_documents') || ! Schema::hasTable('paystack_transactions')) {
            return 0;
        }

        $documentedIds = CommercialDocument::whereNotNull('paystack_transaction_id')
            ->pluck('paystack_transaction_id');

        return PaystackTransaction::where('status', PaystackTransaction::STATUS_SUCCESS)
            ->whereNotIn('id', $documentedIds)
            ->latest()
            ->limit($limit)
            ->get()
            ->sum(function (PaystackTransaction $transaction) {
                return $this->issueReceiptForTransaction($transaction) ? 1 : 0;
            });
    }

    public function generateMissingInvoices(int $limit = 50): int
    {
        if (! Schema::hasTable('commercial_documents') || ! Schema::hasTable('marketplace_orders')) {
            return 0;
        }

        $documentedIds = CommercialDocument::whereNotNull('marketplace_order_id')
            ->where('type', CommercialDocument::TYPE_INVOICE)
            ->pluck('marketplace_order_id');

        return MarketplaceOrder::with(['customer', 'item'])
            ->whereIn('payment_status', [
                MarketplaceOrder::PAYMENT_STATUS_PAID,
                MarketplaceOrder::PAYMENT_STATUS_PARTIALLY_REFUNDED,
            ])
            ->whereNotIn('id', $documentedIds)
            ->latest()
            ->limit($limit)
            ->get()
            ->sum(function (MarketplaceOrder $order) {
                return $this->issueInvoiceForOrder($order) ? 1 : 0;
            });
    }

    public function issueReceiptForTransaction(PaystackTransaction $transaction): ?CommercialDocument
    {
        if (! Schema::hasTable('commercial_documents')) {
            return null;
        }

        $user = $this->userForTransaction($transaction);
        $total = (float) $transaction->amount;

        return CommercialDocument::updateOrCreate(
            [
                'paystack_transaction_id' => $transaction->id,
                'type' => CommercialDocument::TYPE_RECEIPT,
            ],
            [
                'status' => $transaction->status === PaystackTransaction::STATUS_SUCCESS
                    ? CommercialDocument::STATUS_PAID
                    : CommercialDocument::STATUS_ISSUED,
                'user_id' => $user?->id,
                'documentable_type' => $transaction->transactionable_type,
                'documentable_id' => $transaction->transactionable_id,
                'customer_name' => $transaction->customer_name ?? $user?->name,
                'customer_email' => $transaction->customer_email ?? $user?->email,
                'currency' => $transaction->currency ?: config('commercial.currency', 'NGN'),
                'subtotal' => $total,
                'discount_total' => 0,
                'tax_total' => 0,
                'total' => $total,
                'amount_paid' => $transaction->status === PaystackTransaction::STATUS_SUCCESS ? $total : 0,
                'paid_at' => $transaction->paid_at,
                'line_items' => [[
                    'description' => Str::headline($transaction->transaction_type ?: 'payment'),
                    'quantity' => 1,
                    'unit_price' => $total,
                    'total' => $total,
                ]],
                'metadata' => [
                    'reference' => $transaction->reference,
                    'paystack_reference' => $transaction->paystack_reference,
                    'gateway_response' => $transaction->gateway_response,
                ],
            ]
        );
    }

    public function issueInvoiceForOrder(MarketplaceOrder $order): ?CommercialDocument
    {
        if (! Schema::hasTable('commercial_documents')) {
            return null;
        }

        return CommercialDocument::updateOrCreate(
            [
                'marketplace_order_id' => $order->id,
                'type' => CommercialDocument::TYPE_INVOICE,
            ],
            [
                'status' => $order->payment_status === MarketplaceOrder::PAYMENT_STATUS_PAID
                    ? CommercialDocument::STATUS_PAID
                    : CommercialDocument::STATUS_ISSUED,
                'user_id' => $order->customer_id,
                'documentable_type' => MarketplaceOrder::class,
                'documentable_id' => $order->id,
                'customer_name' => $order->customer?->name,
                'customer_email' => $order->customer?->email,
                'currency' => $order->currency ?: config('commercial.currency', 'NGN'),
                'subtotal' => (float) $order->item_price,
                'discount_total' => (float) $order->discount_amount,
                'tax_total' => 0,
                'total' => (float) $order->total_amount,
                'amount_paid' => $order->payment_status === MarketplaceOrder::PAYMENT_STATUS_PAID ? (float) $order->total_amount : 0,
                'paid_at' => $order->paid_at,
                'line_items' => [[
                    'description' => $order->item?->title ?? 'Marketplace order',
                    'quantity' => 1,
                    'unit_price' => (float) $order->item_price,
                    'discount' => (float) $order->discount_amount,
                    'total' => (float) $order->total_amount,
                ]],
                'metadata' => [
                    'order_number' => $order->order_number,
                    'payment_reference' => $order->payment_reference,
                    'vendor_earning' => $order->vendor_earning,
                    'platform_commission' => $order->platform_commission,
                ],
            ]
        );
    }

    public function processRefund(PaystackTransaction $transaction, float $amount, string $reason, ?User $actor = null): array
    {
        if (! Schema::hasTable('refund_requests')) {
            return ['success' => false, 'message' => 'Refund tables are not migrated yet.'];
        }

        if (! $transaction->isSuccessful()) {
            return ['success' => false, 'message' => 'Only successful payments can be refunded.'];
        }

        $amount = round($amount, 2);
        $refundable = $this->refundableAmount($transaction);

        if ($amount <= 0 || $amount > $refundable) {
            return ['success' => false, 'message' => 'Refund amount exceeds the remaining refundable balance.'];
        }

        try {
            return DB::transaction(function () use ($transaction, $amount, $reason, $actor) {
                $user = $this->userForTransaction($transaction);
                $usePaystack = (bool) config('commercial.refunds.use_paystack_api', false)
                    && filled($transaction->paystack_reference);
                $method = $usePaystack ? 'paystack_api' : 'manual_ledger';

                $refund = RefundRequest::create([
                    'user_id' => $user?->id,
                    'requested_by' => $actor?->id,
                    'processed_by' => $actor?->id,
                    'paystack_transaction_id' => $transaction->id,
                    'status' => RefundRequest::STATUS_APPROVED,
                    'method' => $method,
                    'amount' => $amount,
                    'currency' => $transaction->currency ?: config('commercial.currency', 'NGN'),
                    'reason' => $reason,
                    'approved_at' => now(),
                    'metadata' => [
                        'original_reference' => $transaction->reference,
                        'original_type' => $transaction->transaction_type,
                    ],
                ]);

                $providerResponse = null;
                $providerReference = null;

                if ($usePaystack) {
                    $result = app(PaystackService::class)->initiateRefund($transaction, $amount, $reason);

                    if (! ($result['success'] ?? false)) {
                        $refund->update([
                            'status' => RefundRequest::STATUS_FAILED,
                            'failure_reason' => $result['message'] ?? 'Paystack refund failed.',
                            'provider_response' => $result,
                        ]);

                        return ['success' => false, 'message' => $refund->failure_reason, 'refund' => $refund];
                    }

                    $providerResponse = $result['data'] ?? $result;
                    $providerReference = data_get($providerResponse, 'reference') ?: data_get($providerResponse, 'transaction_reference');
                }

                $walletDebit = $this->reverseWalletFundingIfPossible($transaction, $amount, $refund);

                $refundTransaction = PaystackTransaction::create([
                    'reference' => 'RF_' . now()->format('YmdHis') . '_' . Str::upper(Str::random(6)),
                    'paystack_reference' => $providerReference,
                    'amount' => $amount,
                    'currency' => $transaction->currency ?: config('commercial.currency', 'NGN'),
                    'status' => PaystackTransaction::STATUS_SUCCESS,
                    'customer_email' => $transaction->customer_email,
                    'customer_name' => $transaction->customer_name,
                    'transaction_type' => 'refund',
                    'transactionable_type' => $transaction->transactionable_type,
                    'transactionable_id' => $transaction->transactionable_id,
                    'paid_at' => now(),
                    'gateway_response' => 'Refund processed: ' . $reason,
                    'paystack_response' => [
                        'method' => $method,
                        'provider' => $providerResponse,
                        'wallet_debit_id' => $walletDebit?->id,
                    ],
                ]);

                $refund->update([
                    'status' => RefundRequest::STATUS_PROCESSED,
                    'provider_reference' => $providerReference ?: $refundTransaction->reference,
                    'provider_response' => $providerResponse,
                    'wallet_transaction_id' => $walletDebit?->id,
                    'processed_at' => now(),
                ]);

                $this->issueCreditNote($transaction, $refund, $refundTransaction);
                $this->refreshDocumentRefundTotals($transaction);

                return [
                    'success' => true,
                    'message' => 'Refund recorded with audit trail.',
                    'refund' => $refund->fresh(),
                    'refund_transaction' => $refundTransaction,
                ];
            });
        } catch (Throwable $exception) {
            report($exception);

            return ['success' => false, 'message' => $exception->getMessage()];
        }
    }

    public function refundableAmount(PaystackTransaction $transaction): float
    {
        if (Schema::hasTable('refund_requests')) {
            $processed = RefundRequest::where('paystack_transaction_id', $transaction->id)
                ->whereIn('status', [RefundRequest::STATUS_APPROVED, RefundRequest::STATUS_PROCESSED])
                ->sum('amount');

            return max(0, round((float) $transaction->amount - (float) $processed, 2));
        }

        $legacy = Schema::hasTable('paystack_transactions')
            ? PaystackTransaction::where('transaction_type', 'refund')
                ->where('transactionable_type', $transaction->transactionable_type)
                ->where('transactionable_id', $transaction->transactionable_id)
                ->where('created_at', '>=', $transaction->created_at)
                ->sum('amount')
            : 0;

        return max(0, round((float) $transaction->amount - (float) $legacy, 2));
    }

    public function recordPayoutAudit(
        Withdrawal $withdrawal,
        string $event,
        ?string $statusFrom = null,
        ?string $statusTo = null,
        ?User $actor = null,
        array $metadata = [],
        ?string $notes = null
    ): ?PayoutAudit {
        if (! Schema::hasTable('payout_audits')) {
            return null;
        }

        return PayoutAudit::create([
            'withdrawal_id' => $withdrawal->id,
            'user_id' => $withdrawal->user_id,
            'actor_id' => $actor?->id,
            'event' => $event,
            'status_from' => $statusFrom,
            'status_to' => $statusTo,
            'amount' => (float) $withdrawal->amount,
            'currency' => $withdrawal->wallet?->currency ?: config('commercial.currency', 'NGN'),
            'provider' => $withdrawal->paystack_transfer_code ? 'paystack' : null,
            'provider_reference' => $withdrawal->paystack_transfer_code,
            'notes' => $notes,
            'metadata' => $metadata,
        ]);
    }

    public function documentQuery(?User $user = null): Builder
    {
        return CommercialDocument::query()
            ->with(['user', 'paystackTransaction', 'marketplaceOrder'])
            ->when($user, fn (Builder $query) => $query->where('user_id', $user->id))
            ->latest();
    }

    public function documentsForUser(User $user, int $perPage = 12): LengthAwarePaginator|Collection
    {
        if (! Schema::hasTable('commercial_documents')) {
            return collect();
        }

        return $this->documentQuery($user)->paginate($perPage);
    }

    public function money(float $amount, ?string $currency = null): string
    {
        $currency ??= config('commercial.currency', 'NGN');
        $symbol = $currency === 'NGN' ? '₦' : $currency . ' ';

        return $symbol . number_format($amount, 2);
    }

    private function normalizePackage(array $package): array
    {
        $price = $package['price'] ?? null;
        $currency = $package['currency'] ?? config('commercial.currency', 'NGN');
        $interval = $package['interval'] ?? 'one-time';
        $route = $package['cta_route'] ?? 'register';

        return [
            'name' => $package['name'] ?? 'Package',
            'slug' => $package['slug'] ?? Str::slug($package['name'] ?? 'package'),
            'audience' => $package['audience'] ?? null,
            'description' => $package['description'] ?? null,
            'price' => $price,
            'currency' => $currency,
            'interval' => $interval,
            'formatted_price' => $this->packagePrice($price, $currency, $interval),
            'features' => $package['features'] ?? [],
            'limits' => $package['limits'] ?? [],
            'cta_label' => $package['cta_label'] ?? 'Get started',
            'cta_url' => Route::has($route) ? route($route) : url('/'),
            'is_featured' => (bool) ($package['is_featured'] ?? false),
        ];
    }

    private function packagePrice(mixed $price, string $currency, string $interval): string
    {
        if ($price === null) {
            return match ($interval) {
                'custom' => 'Custom',
                'commission' => 'Commission based',
                default => 'Contact sales',
            };
        }

        if ((float) $price === 0.0) {
            return 'Free';
        }

        $symbol = $currency === 'NGN' ? '₦' : $currency . ' ';

        return $symbol . number_format((float) $price, 0);
    }

    private function dateRange(?string $dateFrom, ?string $dateTo): array
    {
        $start = $dateFrom ? Carbon::parse($dateFrom)->startOfDay() : now()->subDays(30)->startOfDay();
        $end = $dateTo ? Carbon::parse($dateTo)->endOfDay() : now()->endOfDay();

        if ($start->gt($end)) {
            return [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        return [$start, $end];
    }

    private function fallbackRefundTotal(Carbon $start, Carbon $end): float
    {
        if (! Schema::hasTable('paystack_transactions')) {
            return 0;
        }

        return (float) PaystackTransaction::where('transaction_type', 'refund')
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');
    }

    private function platformCommission(Carbon $start, Carbon $end): float
    {
        $walletCommission = Schema::hasTable('wallet_transactions')
            ? WalletTransaction::where('category', 'platform_commission')
                ->whereBetween('created_at', [$start, $end])
                ->sum('amount')
            : 0;

        $marketplaceCommission = Schema::hasTable('marketplace_orders')
            ? MarketplaceOrder::whereIn('payment_status', [
                    MarketplaceOrder::PAYMENT_STATUS_PAID,
                    MarketplaceOrder::PAYMENT_STATUS_PARTIALLY_REFUNDED,
                ])
                ->whereBetween('created_at', [$start, $end])
                ->sum('platform_commission')
            : 0;

        return (float) $walletCommission + (float) $marketplaceCommission;
    }

    private function instructorEarnings(Carbon $start, Carbon $end): float
    {
        return Schema::hasTable('wallet_transactions')
            ? (float) WalletTransaction::where('category', WalletTransaction::CATEGORY_INSTRUCTOR_EARNING)
                ->whereBetween('created_at', [$start, $end])
                ->sum('amount')
            : 0;
    }

    private function affiliateCommission(Carbon $start, Carbon $end): float
    {
        return Schema::hasTable('referral_transactions')
            ? (float) ReferralTransaction::whereBetween('created_at', [$start, $end])->sum('commission_amount')
            : 0;
    }

    private function completedPayouts(Carbon $start, Carbon $end): float
    {
        return Schema::hasTable('withdrawals')
            ? (float) Withdrawal::where('status', Withdrawal::STATUS_COMPLETED)
                ->whereBetween('completed_at', [$start, $end])
                ->sum('amount')
            : 0;
    }

    private function dailyBreakdown(Carbon $start, Carbon $end): array
    {
        $days = [];
        $cursor = $start->copy();

        while ($cursor->lte($end)) {
            $dayStart = $cursor->copy()->startOfDay();
            $dayEnd = $cursor->copy()->endOfDay();

            $payments = Schema::hasTable('paystack_transactions')
                ? (float) PaystackTransaction::where('status', PaystackTransaction::STATUS_SUCCESS)
                    ->whereBetween('created_at', [$dayStart, $dayEnd])
                    ->sum('amount')
                : 0;

            $refunds = Schema::hasTable('refund_requests')
                ? (float) RefundRequest::whereIn('status', [RefundRequest::STATUS_APPROVED, RefundRequest::STATUS_PROCESSED])
                    ->whereBetween('created_at', [$dayStart, $dayEnd])
                    ->sum('amount')
                : 0;

            $orders = Schema::hasTable('marketplace_orders')
                ? (float) MarketplaceOrder::whereIn('payment_status', [
                        MarketplaceOrder::PAYMENT_STATUS_PAID,
                        MarketplaceOrder::PAYMENT_STATUS_PARTIALLY_REFUNDED,
                    ])
                    ->whereBetween('created_at', [$dayStart, $dayEnd])
                    ->sum('total_amount')
                : 0;

            $days[] = [
                'date' => $cursor->toDateString(),
                'payments' => $payments,
                'orders' => $orders,
                'refunds' => $refunds,
                'net' => max(0, $payments + $orders - $refunds),
            ];

            $cursor->addDay();
        }

        return $days;
    }

    private function userForTransaction(PaystackTransaction $transaction): ?User
    {
        $transactionable = $transaction->transactionable;

        if ($transactionable instanceof Wallet) {
            return $transactionable->user;
        }

        if ($transaction->customer_email) {
            return User::where('email', $transaction->customer_email)->first();
        }

        return null;
    }

    private function reverseWalletFundingIfPossible(PaystackTransaction $transaction, float $amount, RefundRequest $refund): ?WalletTransaction
    {
        $transactionable = $transaction->transactionable;

        if (! $transactionable instanceof Wallet) {
            return null;
        }

        if (! $transactionable->hasSufficientBalance($amount)) {
            throw new \RuntimeException('The wallet does not have enough available balance to reverse this funding refund safely.');
        }

        return $transactionable->debit(
            $amount,
            WalletTransaction::CATEGORY_REFUND,
            'Refund reversal for ' . $transaction->reference,
            $refund,
            [
                'original_paystack_transaction_id' => $transaction->id,
                'refund_number' => $refund->refund_number,
            ]
        );
    }

    private function issueCreditNote(PaystackTransaction $transaction, RefundRequest $refund, PaystackTransaction $refundTransaction): ?CommercialDocument
    {
        if (! Schema::hasTable('commercial_documents')) {
            return null;
        }

        return CommercialDocument::create([
            'type' => CommercialDocument::TYPE_CREDIT_NOTE,
            'status' => CommercialDocument::STATUS_REFUNDED,
            'user_id' => $refund->user_id,
            'documentable_type' => RefundRequest::class,
            'documentable_id' => $refund->id,
            'paystack_transaction_id' => $refundTransaction->id,
            'customer_name' => $transaction->customer_name,
            'customer_email' => $transaction->customer_email,
            'currency' => $refund->currency,
            'subtotal' => (float) $refund->amount,
            'total' => (float) $refund->amount,
            'amount_refunded' => (float) $refund->amount,
            'refunded_at' => now(),
            'line_items' => [[
                'description' => 'Refund for ' . $transaction->reference,
                'quantity' => 1,
                'unit_price' => (float) $refund->amount,
                'total' => (float) $refund->amount,
            ]],
            'metadata' => [
                'refund_number' => $refund->refund_number,
                'original_reference' => $transaction->reference,
                'refund_transaction_reference' => $refundTransaction->reference,
            ],
            'notes' => $refund->reason,
        ]);
    }

    private function refreshDocumentRefundTotals(PaystackTransaction $transaction): void
    {
        if (! Schema::hasTable('commercial_documents')) {
            return;
        }

        $refunded = RefundRequest::where('paystack_transaction_id', $transaction->id)
            ->whereIn('status', [RefundRequest::STATUS_APPROVED, RefundRequest::STATUS_PROCESSED])
            ->sum('amount');

        CommercialDocument::where('paystack_transaction_id', $transaction->id)
            ->whereIn('type', [CommercialDocument::TYPE_RECEIPT, CommercialDocument::TYPE_INVOICE])
            ->update([
                'amount_refunded' => $refunded,
                'status' => $refunded >= (float) $transaction->amount
                    ? CommercialDocument::STATUS_REFUNDED
                    : CommercialDocument::STATUS_PAID,
                'refunded_at' => $refunded > 0 ? now() : null,
            ]);
    }
}
