<?php

// app/Models/Referral.php
namespace App\Models\Marketplace;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Core\User;

class Referral extends Model
{
    protected $fillable = [
        'affiliate_id',
        'referred_user_id',
        'total_spent',
        'total_commission_earned',
        'courses_purchased',
        'first_purchase_at',
        'last_purchase_at',
        'status',
        'metadata'
    ];

    protected $casts = [
        'total_spent' => 'decimal:2',
        'total_commission_earned' => 'decimal:2',
        'first_purchase_at' => 'datetime',
        'last_purchase_at' => 'datetime',
        'metadata' => 'array'
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    // Relationships
    public function affiliate(): BelongsTo
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function referredUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(ReferralTransaction::class);
    }

    // Status methods
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function activate(): bool
    {
        if ($this->isPending()) {
            $updated = $this->update([
                'status' => self::STATUS_ACTIVE,
                'first_purchase_at' => $this->first_purchase_at ?: now()
            ]);
            
            if ($updated) {
                $this->affiliate->increment('active_referrals');
            }
            
            return $updated;
        }
        
        return false;
    }

    // Update purchase statistics
    public function recordPurchase(float $amount, float $commission): void
    {
        $this->increment('courses_purchased');
        $this->increment('total_spent', $amount);
        $this->increment('total_commission_earned', $commission);
        
        $this->update([
            'last_purchase_at' => now(),
            'first_purchase_at' => $this->first_purchase_at ?: now()
        ]);

        // Activate if this is the first purchase
        if ($this->isPending()) {
            $this->activate();
        }
    }

    // Get formatted amounts
    public function getFormattedTotalSpentAttribute(): string
    {
        return '₦' . number_format($this->total_spent, 2);
    }

    public function getFormattedCommissionEarnedAttribute(): string
    {
        return '₦' . number_format($this->total_commission_earned, 2);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }
}

