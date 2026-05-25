<?php

// Withdrawal.php
namespace App\Models\Marketplace;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Core\User;

class Withdrawal extends Model
{
    protected $fillable = [
        'withdrawal_id',
        'user_id',
        'wallet_id',
        'amount',
        'bank_code',
        'account_number',
        'account_name',
        'status',
        'paystack_transfer_code',
        'paystack_recipient_code',
        'admin_note',
        'failure_reason',
        'approved_by',
        'requested_at',
        'approved_at',
        'processed_at',
        'completed_at'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_REJECTED = 'rejected';

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($withdrawal) {
            if (!$withdrawal->withdrawal_id) {
                $withdrawal->withdrawal_id = \Str::uuid();
            }
            if (!$withdrawal->requested_at) {
                $withdrawal->requested_at = now();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Status methods
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function canBeApproved(): bool
    {
        return $this->isPending();
    }

    // Approve withdrawal
    public function approve(int $approverId): bool
    {
        if (!$this->canBeApproved()) {
            return false;
        }

        return $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_by' => $approverId,
            'approved_at' => now()
        ]);
    }

    // Reject withdrawal
    public function reject(string $reason): bool
    {
        if (!$this->canBeApproved()) {
            return false;
        }

        $previousStatus = $this->status;

        // Refund the amount back to wallet
        $this->wallet->credit(
            $this->amount,
            WalletTransaction::CATEGORY_REFUND,
            'Withdrawal request rejected: ' . $reason,
            $this
        );

        $updated = $this->update([
            'status' => self::STATUS_REJECTED,
            'failure_reason' => $reason
        ]);

        if ($updated) {
            app(\App\Services\CommercialReadinessService::class)->recordPayoutAudit(
                $this->fresh(),
                'withdrawal_rejected',
                $previousStatus,
                self::STATUS_REJECTED,
                auth()->user(),
                [],
                $reason
            );
        }

        return $updated;
    }

    // Get status color for UI
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'yellow',
            self::STATUS_APPROVED => 'blue',
            self::STATUS_PROCESSING => 'purple',
            self::STATUS_COMPLETED => 'green',
            self::STATUS_FAILED => 'red',
            self::STATUS_REJECTED => 'red',
            default => 'gray'
        };
    }

    // Get formatted amount
    public function getFormattedAmountAttribute(): string
    {
        return '₦' . number_format($this->amount, 2);
    }
}
