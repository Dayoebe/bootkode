<?php

// Model 2: MarketplaceOrder.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class MarketplaceOrder extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REFUNDED = 'refunded';
    const STATUS_FAILED = 'failed';

    const PAYMENT_STATUS_UNPAID = 'unpaid';
    const PAYMENT_STATUS_PAID = 'paid';
    const PAYMENT_STATUS_PARTIALLY_REFUNDED = 'partially_refunded';
    const PAYMENT_STATUS_REFUNDED = 'refunded';
    const PAYMENT_STATUS_FAILED = 'failed';

    const STATUSES = [
        self::STATUS_PENDING => 'Pending',
        self::STATUS_CONFIRMED => 'Confirmed',
        self::STATUS_PROCESSING => 'Processing',
        self::STATUS_COMPLETED => 'Completed',
        self::STATUS_CANCELLED => 'Cancelled',
        self::STATUS_REFUNDED => 'Refunded',
        self::STATUS_FAILED => 'Failed',
    ];

    const PAYMENT_STATUSES = [
        self::PAYMENT_STATUS_UNPAID => 'Unpaid',
        self::PAYMENT_STATUS_PAID => 'Paid',
        self::PAYMENT_STATUS_PARTIALLY_REFUNDED => 'Partially Refunded',
        self::PAYMENT_STATUS_REFUNDED => 'Refunded',
        self::PAYMENT_STATUS_FAILED => 'Failed',
    ];

    protected $fillable = [
        'order_number', 'customer_id', 'vendor_id', 'item_id',
        'status', 'payment_status', 'item_price', 'discount_amount',
        'total_amount', 'currency', 'platform_commission_rate',
        'platform_commission', 'vendor_earning', 'payment_method',
        'payment_reference', 'transaction_id', 'payment_details',
        'details', 'scheduled_at', 'completed_at', 'customer_details',
        'customer_notes', 'vendor_notes', 'admin_notes',
        'is_delivered', 'delivered_at', 'delivery_details',
        'confirmed_at', 'paid_at'
    ];

    protected $casts = [
        'item_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'platform_commission_rate' => 'decimal:2',
        'platform_commission' => 'decimal:2',
        'vendor_earning' => 'decimal:2',
        'payment_details' => 'array',
        'details' => 'array',
        'customer_details' => 'array',
        'delivery_details' => 'array',
        'is_delivered' => 'boolean',
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
        'delivered_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (!$order->order_number) {
                $order->order_number = $order->generateOrderNumber();
            }

            // Calculate commission and vendor earning
            $order->calculateCommissions();
        });
    }

    // Relationships
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function item()
    {
        return $this->belongsTo(MarketplaceItem::class, 'item_id');
    }

    public function walletTransactions()
    {
        return $this->morphMany(WalletTransaction::class, 'transactionable');
    }

    // Helper Methods
    public function generateOrderNumber()
    {
        do {
            $number = 'MP-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        } while (static::where('order_number', $number)->exists());

        return $number;
    }

    public function calculateCommissions()
    {
        $this->platform_commission = ($this->total_amount * $this->platform_commission_rate) / 100;
        $this->vendor_earning = $this->total_amount - $this->platform_commission;
    }

    // Status Methods
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isConfirmed()
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    public function isProcessing()
    {
        return $this->status === self::STATUS_PROCESSING;
    }

    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isCancelled()
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isRefunded()
    {
        return $this->status === self::STATUS_REFUNDED;
    }

    public function isPaid()
    {
        return $this->payment_status === self::PAYMENT_STATUS_PAID;
    }

    // Action Methods
    public function confirm()
    {
        $this->update([
            'status' => self::STATUS_CONFIRMED,
            'confirmed_at' => now(),
        ]);
    }

    public function markAsPaid($paymentDetails = [])
    {
        $this->update([
            'payment_status' => self::PAYMENT_STATUS_PAID,
            'paid_at' => now(),
            'payment_details' => $paymentDetails,
        ]);

        // Process vendor payment
        $this->processVendorPayment();
    }

    public function complete($deliveryDetails = [])
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now(),
            'is_delivered' => true,
            'delivered_at' => now(),
            'delivery_details' => $deliveryDetails,
        ]);
    }

    public function cancel($reason = null)
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'admin_notes' => $reason,
        ]);
    }

    public function refund($amount = null, $reason = null)
    {
        $refundAmount = $amount ?? $this->total_amount;
        
        $this->update([
            'status' => self::STATUS_REFUNDED,
            'payment_status' => self::PAYMENT_STATUS_REFUNDED,
            'admin_notes' => $reason,
        ]);

        // Process refund to customer wallet
        $this->processRefund($refundAmount, $reason);
    }

    // Payment Processing
    public function processVendorPayment()
    {
        if (!$this->isPaid()) {
            return false;
        }

        // Credit vendor wallet
        $vendorWallet = Wallet::getOrCreateWallet($this->vendor_id, Wallet::TYPE_INSTRUCTOR);
        $vendorWallet->credit(
            $this->vendor_earning,
            WalletTransaction::CATEGORY_INSTRUCTOR_EARNING,
            "Marketplace sale: {$this->item->title}",
            $this,
            ['order_number' => $this->order_number]
        );

        // Credit platform wallet
        $platformWallet = Wallet::getOrCreateWallet(1, Wallet::TYPE_PLATFORM); // Assuming platform user ID is 1
        $platformWallet->credit(
            $this->platform_commission,
            'platform_commission',
            "Platform commission: {$this->item->title}",
            $this,
            ['order_number' => $this->order_number]
        );

        return true;
    }

    public function processRefund($amount, $reason = null)
    {
        // Credit customer wallet
        $customerWallet = Wallet::getOrCreateWallet($this->customer_id);
        $customerWallet->credit(
            $amount,
            WalletTransaction::CATEGORY_REFUND,
            "Refund for order: {$this->order_number}",
            $this,
            ['reason' => $reason]
        );
    }

    // Scopes
    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeByVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', self::PAYMENT_STATUS_PAID);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    // Accessors
    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_CONFIRMED => 'blue',
            self::STATUS_PROCESSING => 'purple',
            self::STATUS_COMPLETED => 'green',
            self::STATUS_CANCELLED => 'red',
            self::STATUS_REFUNDED => 'orange',
            self::STATUS_FAILED => 'red',
            default => 'gray',
        };
    }

    public function getPaymentStatusColorAttribute()
    {
        return match ($this->payment_status) {
            self::PAYMENT_STATUS_UNPAID => 'red',
            self::PAYMENT_STATUS_PAID => 'green',
            self::PAYMENT_STATUS_PARTIALLY_REFUNDED => 'yellow',
            self::PAYMENT_STATUS_REFUNDED => 'orange',
            self::PAYMENT_STATUS_FAILED => 'red',
            default => 'gray',
        };
    }

    public function getFormattedTotalAttribute()
    {
        return '₦' . number_format($this->total_amount, 2);
    }

    public function getFormattedVendorEarningAttribute()
    {
        return '₦' . number_format($this->vendor_earning, 2);
    }

    public function getFormattedCommissionAttribute()
    {
        return '₦' . number_format($this->platform_commission, 2);
    }
}
