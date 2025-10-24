<?php

namespace App\Models\Marketplace;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use App\Models\Core\User;

class DiscountCode extends Model
{
    use HasFactory, SoftDeletes;

    const TYPE_PERCENTAGE = 'percentage';
    const TYPE_FIXED = 'fixed';

    const TYPES = [
        self::TYPE_PERCENTAGE => 'Percentage',
        self::TYPE_FIXED => 'Fixed Amount',
    ];

    protected $fillable = [
        'code',
        'type',
        'value',
        'min_amount',
        'max_uses',
        'uses_per_user',
        'used_count',
        'valid_from',
        'valid_until',
        'is_active',
        'description',
        'created_by',
        'metadata'
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_amount' => 'decimal:2',
        'is_active' => 'boolean',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
        'metadata' => 'array',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function orders()
    {
        return $this->hasMany(MarketplaceOrder::class, 'discount_code_id');
    }

    // Helper Methods
    public function isValid()
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();

        if ($this->valid_from && $this->valid_from > $now) {
            return false;
        }

        if ($this->valid_until && $this->valid_until < $now) {
            return false;
        }

        if ($this->max_uses && $this->used_count >= $this->max_uses) {
            return false;
        }

        return true;
    }

    public function isExpired()
    {
        return $this->valid_until && $this->valid_until < now();
    }

    public function canBeUsedBy($userId, $orderAmount = 0)
    {
        if (!$this->isValid()) {
            return false;
        }

        // Check minimum amount
        if ($this->min_amount && $orderAmount < $this->min_amount) {
            return false;
        }

        // Check user usage limit
        if ($this->uses_per_user > 0) {
            $userUsage = $this->orders()
                ->where('customer_id', $userId)
                ->where('payment_status', MarketplaceOrder::PAYMENT_STATUS_PAID)
                ->count();

            if ($userUsage >= $this->uses_per_user) {
                return false;
            }
        }

        return true;
    }

    public function calculateDiscount($amount)
    {
        if ($this->type === self::TYPE_PERCENTAGE) {
            return min(($amount * $this->value) / 100, $amount);
        }

        return min($this->value, $amount);
    }

    public function incrementUsage()
    {
        $this->increment('used_count');
    }

    public function getUsagePercentage()
    {
        if (!$this->max_uses) {
            return 0;
        }

        return min(($this->used_count / $this->max_uses) * 100, 100);
    }

    public function getRemainingUses()
    {
        if (!$this->max_uses) {
            return null; // Unlimited
        }

        return max(0, $this->max_uses - $this->used_count);
    }

    public function getTimeUntilExpiry()
    {
        if (!$this->valid_until) {
            return null; // No expiry
        }

        return $this->valid_until->diffForHumans();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeValid($query)
    {
        $now = now();
        return $query->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('valid_from')
                  ->orWhere('valid_from', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('valid_until')
                  ->orWhere('valid_until', '>=', $now);
            })
            ->where(function ($q) {
                $q->whereNull('max_uses')
                  ->orWhereRaw('used_count < max_uses');
            });
    }

    public function scopeExpired($query)
    {
        return $query->where('valid_until', '<', now());
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCode($query, $code)
    {
        return $query->where('code', strtoupper($code));
    }

    // Accessors
    public function getFormattedValueAttribute()
    {
        if ($this->type === self::TYPE_PERCENTAGE) {
            return $this->value . '%';
        }

        return '₦' . number_format($this->value, 0);
    }

    public function getFormattedMinAmountAttribute()
    {
        return $this->min_amount ? '₦' . number_format($this->min_amount, 0) : null;
    }

    public function getStatusAttribute()
    {
        if (!$this->is_active) {
            return 'inactive';
        }

        if ($this->isExpired()) {
            return 'expired';
        }

        if ($this->max_uses && $this->used_count >= $this->max_uses) {
            return 'exhausted';
        }

        return 'active';
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'active' => 'green',
            'inactive' => 'gray',
            'expired' => 'red',
            'exhausted' => 'orange',
            default => 'gray',
        };
    }

    // Static Methods
    public static function findValidCode($code)
    {
        return static::byCode($code)->valid()->first();
    }

    public static function generateUniqueCode($prefix = 'SAVE', $length = 6)
    {
        do {
            $code = $prefix . strtoupper(\Str::random($length));
        } while (static::where('code', $code)->exists());

        return $code;
    }
}