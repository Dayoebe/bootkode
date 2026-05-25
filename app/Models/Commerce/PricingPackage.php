<?php

namespace App\Models\Commerce;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PricingPackage extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_ARCHIVED = 'archived';

    protected $fillable = [
        'name',
        'slug',
        'audience',
        'description',
        'price',
        'currency',
        'interval',
        'features',
        'limits',
        'cta_label',
        'cta_route',
        'sort_order',
        'is_public',
        'is_featured',
        'status',
        'metadata',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'array',
        'limits' => 'array',
        'is_public' => 'boolean',
        'is_featured' => 'boolean',
        'metadata' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (PricingPackage $package) {
            if (! $package->slug && $package->name) {
                $package->slug = Str::slug($package->name);
            }
        });
    }

    public function scopePublicActive($query)
    {
        return $query->where('is_public', true)
            ->where('status', self::STATUS_ACTIVE)
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderBy('name');
    }

    public function getFormattedPriceAttribute(): string
    {
        if ($this->price === null) {
            return match ($this->interval) {
                'custom' => 'Custom',
                'commission' => 'Commission based',
                default => 'Contact sales',
            };
        }

        if ((float) $this->price === 0.0) {
            return 'Free';
        }

        $symbol = $this->currency === 'NGN' ? '₦' : $this->currency . ' ';

        return $symbol . number_format((float) $this->price, 0);
    }
}
