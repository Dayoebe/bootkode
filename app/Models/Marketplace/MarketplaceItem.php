<?php
namespace App\Models\Marketplace;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use App\Models\Core\User;

class MarketplaceItem extends Model
{
    use HasFactory, SoftDeletes, Searchable;

    const TYPE_COURSE = 'course';
    const TYPE_RESOURCE = 'resource';
    const TYPE_SERVICE = 'service';

    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';
    const STATUS_SUSPENDED = 'suspended';

    const TYPES = [
        self::TYPE_COURSE => 'Course',
        self::TYPE_RESOURCE => 'Digital Resource',
        self::TYPE_SERVICE => 'Service',
    ];

    const STATUSES = [
        self::STATUS_DRAFT => 'Draft',
        self::STATUS_PENDING => 'Pending Review',
        self::STATUS_APPROVED => 'Approved',
        self::STATUS_REJECTED => 'Rejected',
        self::STATUS_SUSPENDED => 'Suspended',
    ];

    protected $fillable = [
        'vendor_id',
        'title',
        'slug',
        'description',
        'short_description',
        'type',
        'status',
        'price',
        'discount_price',
        'currency',
        'is_digital',
        'is_featured',
        'thumbnail',
        'images',
        'files',
        'categories',
        'tags',
        'metadata',
        'meta_title',
        'meta_description',
        'keywords',
        'duration_minutes',
        'availability',
        'approved_at',
        'approved_by',
        'rejection_reason'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'average_rating' => 'decimal:2',
        'is_digital' => 'boolean',
        'is_featured' => 'boolean',
        'images' => 'array',
        'files' => 'array',
        'categories' => 'array',
        'tags' => 'array',
        'metadata' => 'array',
        'availability' => 'array',
        'approved_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            if (!$item->slug) {
                $item->slug = $item->generateUniqueSlug($item->title);
            }
        });

        static::updating(function ($item) {
            if ($item->isDirty('title') && !$item->isDirty('slug')) {
                $item->slug = $item->generateUniqueSlug($item->title);
            }
        });
    }
    public function categories()
    {
        return $this->belongsToMany(MarketplaceCategory::class, 'marketplace_item_categories');
    }
    public function itemCategories()
{
    return $this->belongsToMany(MarketplaceCategory::class, 'marketplace_item_categories');
}
    
    // Add this scope method:
    public function scopeInCategories($query, $categoryIds)
    {
        if (is_array($categoryIds)) {
            return $query->whereHas('categories', function ($q) use ($categoryIds) {
                $q->whereIn('marketplace_categories.id', $categoryIds);
            });
        }
        
        return $query->whereHas('categories', function ($q) use ($categoryIds) {
            $q->where('marketplace_categories.id', $categoryIds);
        });
    }
    
    // Add helper method to get category names
    public function getCategoryNames()
    {
        return $this->categories->pluck('name')->toArray();
    }
    
    public function getCategoryColors()
    {
        return $this->categories->pluck('color', 'name')->toArray();
    }
    // Relationships


    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function orders()
    {
        return $this->hasMany(MarketplaceOrder::class, 'item_id');
    }
    public function reviews()
    {
        return $this->morphMany(ProductReview::class, 'reviewable');
    }

    // Helper Methods
    public function generateUniqueSlug($title)
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->where('id', '!=', $this->id ?? 0)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }

    // Status Methods
    public function isDraft()
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function isSuspended()
    {
        return $this->status === self::STATUS_SUSPENDED;
    }

    public function isPublished()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function approve($approverId = null)
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'approved_at' => now(),
            'approved_by' => $approverId ?? auth()->id(),
            'rejection_reason' => null,
        ]);
    }

    public function reject($reason, $rejecterId = null)
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'rejection_reason' => $reason,
            'approved_at' => null,
            'approved_by' => $rejecterId ?? auth()->id(),
        ]);
    }

    public function suspend($reason = null)
    {
        $this->update([
            'status' => self::STATUS_SUSPENDED,
            'rejection_reason' => $reason,
        ]);
    }

    public function submitForReview()
    {
        $this->update(['status' => self::STATUS_PENDING]);
    }

    // Pricing Methods
    public function getEffectivePrice()
    {
        return $this->discount_price ?? $this->price;
    }

    public function hasDiscount()
    {
        return $this->discount_price && $this->discount_price < $this->price;
    }

    public function getDiscountPercentage()
    {
        if (!$this->hasDiscount()) {
            return 0;
        }

        return round((($this->price - $this->discount_price) / $this->price) * 100);
    }

    public function getFormattedPrice()
    {
        return '₦' . number_format($this->getEffectivePrice(), 2);
    }

    public function getFormattedOriginalPrice()
    {
        return '₦' . number_format($this->price, 2);
    }

    // Media Methods
    public function getPrimaryImage()
    {
        if ($this->thumbnail) {
            return $this->thumbnail;
        }

        $images = $this->images ?? [];
        return $images[0]['path'] ?? null;
    }

    public function getAllImages()
    {
        $images = [];

        if ($this->thumbnail) {
            $images[] = $this->thumbnail;
        }

        foreach ($this->images ?? [] as $image) {
            $images[] = $image['path'];
        }

        return array_unique($images);
    }

    // Analytics Methods
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function updateRating()
    {
        $reviews = $this->reviews()->where('is_approved', true);
        $this->update([
            'average_rating' => $reviews->avg('rating') ?? 0,
            'reviews_count' => $reviews->count(),
        ]);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInCategory($query, $category)
    {
        return $query->whereJsonContains('categories', $category);
    }

    public function scopeWithTag($query, $tag)
    {
        return $query->whereJsonContains('tags', $tag);
    }

    public function scopePriceRange($query, $min, $max)
    {
        return $query->whereBetween('price', [$min, $max]);
    }

    // Search Configuration
    public function toSearchableArray()
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'keywords' => $this->keywords,
            'categories' => $this->categories,
            'tags' => $this->tags,
            'type' => $this->type,
            'vendor_name' => $this->vendor->name ?? '',
        ];
    }

    // Accessors
    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'gray',
            self::STATUS_PENDING => 'yellow',
            self::STATUS_APPROVED => 'green',
            self::STATUS_REJECTED => 'red',
            self::STATUS_SUSPENDED => 'orange',
            default => 'gray',
        };
    }

    public function getTypeNameAttribute()
    {
        return self::TYPES[$this->type] ?? ucfirst($this->type);
    }

    public function getStatusNameAttribute()
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function getIsAvailableAttribute()
    {
        return $this->isPublished() && !$this->isSuspended();
    }

    // Service-specific methods
    public function isService()
    {
        return $this->type === self::TYPE_SERVICE;
    }

    public function isCourse()
    {
        return $this->type === self::TYPE_COURSE;
    }

    public function isResource()
    {
        return $this->type === self::TYPE_RESOURCE;
    }

    public function getFormattedDuration()
    {
        if (!$this->duration_minutes) {
            return null;
        }

        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;

        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }

        return $minutes . 'm';
    }
}
