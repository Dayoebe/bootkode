<?php

// WalletTransaction.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    protected $fillable = [
        'transaction_id',
        'wallet_id',
        'type',
        'category',
        'amount',
        'balance_before',
        'balance_after',
        'reference',
        'description',
        'metadata',
        'status',
        'transactionable_type',
        'transactionable_id'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
        'metadata' => 'array'
    ];

    const TYPE_CREDIT = 'credit';
    const TYPE_DEBIT = 'debit';

    const CATEGORY_FUNDING = 'funding';
    const CATEGORY_COURSE_PURCHASE = 'course_purchase';
    const CATEGORY_INSTRUCTOR_EARNING = 'instructor_earning';
    const CATEGORY_WITHDRAWAL = 'withdrawal';
    const CATEGORY_REFUND = 'refund';

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function transactionable()
    {
        return $this->morphTo();
    }

    // Get formatted amount with sign
    public function getFormattedAmountAttribute(): string
    {
        $sign = $this->type === self::TYPE_CREDIT ? '+' : '-';
        return $sign . '₦' . number_format($this->amount, 2);
    }

    // Get status color for UI
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'completed' => 'green',
            'pending' => 'yellow',
            'failed' => 'red',
            default => 'gray'
        };
    }

    // Scopes
    public function scopeCredits($query)
    {
        return $query->where('type', self::TYPE_CREDIT);
    }

    public function scopeDebits($query)
    {
        return $query->where('type', self::TYPE_DEBIT);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}
