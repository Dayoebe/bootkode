<?php

// app/Models/Affiliate.php
namespace App\Models\Marketplace;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Models\Core\User;

class Affiliate extends Model
{
    protected $fillable = [
        'user_id',
        'referral_code',
        'commission_rate',
        'status',
        'total_earned',
        'total_referrals',
        'active_referrals',
        'approved_at',
        'approved_by',
        'metadata'
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
        'total_earned' => 'decimal:2',
        'approved_at' => 'datetime',
        'metadata' => 'array'
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_SUSPENDED = 'suspended';

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($affiliate) {
            if (!$affiliate->referral_code) {
                $affiliate->referral_code = $affiliate->generateUniqueReferralCode();
            }
        });
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(ReferralTransaction::class, 'referral_id');
    }

    // Status methods
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isSuspended(): bool
    {
        return $this->status === self::STATUS_SUSPENDED;
    }

    public function activate(): bool
    {
        return $this->update(['status' => self::STATUS_ACTIVE]);
    }

    public function suspend(): bool
    {
        return $this->update(['status' => self::STATUS_SUSPENDED]);
    }

    // Generate unique referral code
    private function generateUniqueReferralCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (self::where('referral_code', $code)->exists());

        return $code;
    }

    // Get referral link
    public function getReferralLinkAttribute(): string
    {
        return url('/register?ref=' . $this->referral_code);
    }

    // Get formatted total earned
    public function getFormattedTotalEarnedAttribute(): string
    {
        return '₦' . number_format($this->total_earned, 2);
    }

    // Update statistics
    public function updateStats(): void
    {
        $this->update([
            'total_referrals' => $this->referrals()->count(),
            'active_referrals' => $this->referrals()->where('status', 'active')->count(),
            'total_earned' => $this->transactions()
                ->where('status', 'paid')
                ->sum('commission_amount')
        ]);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeTopPerformers($query, $limit = 10)
    {
        return $query->orderBy('total_earned', 'desc')
                    ->orderBy('total_referrals', 'desc')
                    ->limit($limit);
    }
}

