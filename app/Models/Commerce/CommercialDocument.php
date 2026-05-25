<?php

namespace App\Models\Commerce;

use App\Models\Core\User;
use App\Models\Marketplace\MarketplaceOrder;
use App\Models\Marketplace\PaystackTransaction;
use App\Models\Marketplace\WalletTransaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CommercialDocument extends Model
{
    use HasFactory;

    public const TYPE_INVOICE = 'invoice';
    public const TYPE_RECEIPT = 'receipt';
    public const TYPE_CREDIT_NOTE = 'credit_note';

    public const STATUS_DRAFT = 'draft';
    public const STATUS_ISSUED = 'issued';
    public const STATUS_PAID = 'paid';
    public const STATUS_REFUNDED = 'refunded';
    public const STATUS_VOID = 'void';

    protected $fillable = [
        'document_number',
        'type',
        'status',
        'user_id',
        'documentable_type',
        'documentable_id',
        'paystack_transaction_id',
        'marketplace_order_id',
        'wallet_transaction_id',
        'customer_name',
        'customer_email',
        'currency',
        'subtotal',
        'discount_total',
        'tax_total',
        'total',
        'amount_paid',
        'amount_refunded',
        'issued_on',
        'due_on',
        'paid_at',
        'refunded_at',
        'line_items',
        'metadata',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount_total' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'total' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'amount_refunded' => 'decimal:2',
        'issued_on' => 'date',
        'due_on' => 'date',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
        'line_items' => 'array',
        'metadata' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (CommercialDocument $document) {
            if (! $document->document_number) {
                $prefix = match ($document->type) {
                    self::TYPE_INVOICE => 'INV',
                    self::TYPE_CREDIT_NOTE => 'CRN',
                    default => 'RCT',
                };

                $document->document_number = $prefix . '-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
            }

            $document->issued_on ??= now()->toDateString();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function documentable()
    {
        return $this->morphTo();
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

    public function scopeReceipts($query)
    {
        return $query->where('type', self::TYPE_RECEIPT);
    }

    public function scopeInvoices($query)
    {
        return $query->where('type', self::TYPE_INVOICE);
    }

    public function getFormattedTotalAttribute(): string
    {
        return $this->formatMoney((float) $this->total);
    }

    public function getFormattedPaidAttribute(): string
    {
        return $this->formatMoney((float) $this->amount_paid);
    }

    public function formatMoney(float $amount): string
    {
        $symbol = $this->currency === 'NGN' ? '₦' : $this->currency . ' ';

        return $symbol . number_format($amount, 2);
    }
}
