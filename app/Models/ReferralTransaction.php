<?php
// app/Models/ReferralTransaction.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralTransaction extends Model
{
    protected $fillable = [
        'referral_id',
        'course_id',
        'wallet_transaction_id',
        'course_price',
        'platform_share',
        'commission_rate',
        'commission_amount',
        'status',
        'paid_at',
        'metadata'
    ];

    protected $casts = [
        'course_price' => 'decimal:2',
        'platform_share' => 'decimal:2',
        'commission_rate' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'metadata' => 'array'
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_PAID = 'paid';
    const STATUS_CANCELLED = 'cancelled';

    // Relationships
    public function referral(): BelongsTo
    {
        return $this->belongsTo(Referral::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function walletTransaction(): BelongsTo
    {
        return $this->belongsTo(WalletTransaction::class);
    }

    // Status methods
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function markAsPaid(): bool
    {
        return $this->update([
            'status' => self::STATUS_PAID,
            'paid_at' => now()
        ]);
    }

    public function cancel(): bool
    {
        return $this->update(['status' => self::STATUS_CANCELLED]);
    }

    // Get formatted amounts
    public function getFormattedCommissionAttribute(): string
    {
        return '₦' . number_format($this->commission_amount, 2);
    }

    public function getFormattedCoursePriceAttribute(): string
    {
        return '₦' . number_format($this->course_price, 2);
    }

    // Scopes
    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }
}