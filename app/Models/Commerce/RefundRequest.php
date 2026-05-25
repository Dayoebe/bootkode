<?php

namespace App\Models\Commerce;

use App\Models\Core\User;
use App\Models\Marketplace\MarketplaceOrder;
use App\Models\Marketplace\PaystackTransaction;
use App\Models\Marketplace\WalletTransaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RefundRequest extends Model
{
    use HasFactory;

    public const STATUS_REQUESTED = 'requested';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_PROCESSED = 'processed';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'refund_number',
        'user_id',
        'requested_by',
        'processed_by',
        'paystack_transaction_id',
        'marketplace_order_id',
        'wallet_transaction_id',
        'status',
        'method',
        'amount',
        'currency',
        'reason',
        'failure_reason',
        'provider_reference',
        'provider_response',
        'metadata',
        'requested_at',
        'approved_at',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'provider_response' => 'array',
        'metadata' => 'array',
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (RefundRequest $refund) {
            if (! $refund->refund_number) {
                $refund->refund_number = 'RFN-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
            }

            $refund->requested_at ??= now();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function paystackTransaction()
    {
        return $this->belongsTo(PaystackTransaction::class);
    }

    public function marketplaceOrder()
    {
        return $this->belongsTo(MarketplaceOrder::class);
    }

    public function walletTransaction()
    {
        return $this->belongsTo(WalletTransaction::class);
    }

    public function getFormattedAmountAttribute(): string
    {
        $symbol = $this->currency === 'NGN' ? '₦' : $this->currency . ' ';

        return $symbol . number_format((float) $this->amount, 2);
    }
}
