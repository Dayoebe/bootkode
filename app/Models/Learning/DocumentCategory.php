<?php

namespace App\Models\Learning;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DocumentCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function documents()
    {
        return $this->hasMany(Document::class, 'category_id');
    }

    public function publishedDocuments()
    {
        return $this->hasMany(Document::class, 'category_id')
            ->where('status', 'published');
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (!$category->slug) {
                $category->slug = $category->generateUniqueSlug($category->name);
            }
            
            if (!$category->sort_order) {
                $category->sort_order = static::max('sort_order') + 1;
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && !$category->isDirty('slug')) {
                $category->slug = $category->generateUniqueSlug($category->name);
            }
        });
    }

    // Generate unique slug
    public function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->where('id', '!=', $this->id ?? 0)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function scopeWithDocumentCounts($query)
    {
        return $query->withCount([
            'documents',
            'publishedDocuments'
        ]);
    }

    // Accessors
    public function getDocumentsCountAttribute()
    {
        return $this->documents()->count();
    }

    public function getPublishedDocumentsCountAttribute()
    {
        return $this->publishedDocuments()->count();
    }

    public function getColorStyleAttribute()
    {
        return "background-color: {$this->color}; color: " . $this->getContrastColor($this->color) . ";";
    }

    public function getBadgeClassAttribute()
    {
        $lightness = $this->getColorLightness($this->color);
        
        if ($lightness > 0.7) {
            return 'text-gray-800';
        } elseif ($lightness > 0.4) {
            return 'text-gray-900';
        } else {
            return 'text-white';
        }
    }

    // Helper methods
    public function activate()
    {
        $this->update(['is_active' => true]);
    }

    public function deactivate()
    {
        $this->update(['is_active' => false]);
    }

    public function moveUp()
    {
        $previousCategory = static::where('sort_order', '<', $this->sort_order)
            ->orderBy('sort_order', 'desc')
            ->first();

        if ($previousCategory) {
            $tempOrder = $this->sort_order;
            $this->update(['sort_order' => $previousCategory->sort_order]);
            $previousCategory->update(['sort_order' => $tempOrder]);
        }
    }

    public function moveDown()
    {
        $nextCategory = static::where('sort_order', '>', $this->sort_order)
            ->orderBy('sort_order', 'asc')
            ->first();

        if ($nextCategory) {
            $tempOrder = $this->sort_order;
            $this->update(['sort_order' => $nextCategory->sort_order]);
            $nextCategory->update(['sort_order' => $tempOrder]);
        }
    }

    public function canBeDeleted()
    {
        return $this->documents()->count() === 0;
    }

    // Color utility methods
    private function getContrastColor($hexColor)
    {
        // Remove # if present
        $hexColor = ltrim($hexColor, '#');
        
        // Convert to RGB
        $r = hexdec(substr($hexColor, 0, 2));
        $g = hexdec(substr($hexColor, 2, 2));
        $b = hexdec(substr($hexColor, 4, 2));
        
        // Calculate relative luminance
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
        
        // Return white or black based on luminance
        return $luminance > 0.5 ? '#000000' : '#FFFFFF';
    }

    private function getColorLightness($hexColor)
    {
        // Remove # if present
        $hexColor = ltrim($hexColor, '#');
        
        // Convert to RGB
        $r = hexdec(substr($hexColor, 0, 2)) / 255;
        $g = hexdec(substr($hexColor, 2, 2)) / 255;
        $b = hexdec(substr($hexColor, 4, 2)) / 255;
        
        // Calculate lightness
        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        
        return ($max + $min) / 2;
    }

    // Static methods
    public static function getActiveCategories()
    {
        return static::active()->ordered()->get();
    }

    public static function getCategoriesWithCounts()
    {
        return static::active()
            ->withDocumentCounts()
            ->ordered()
            ->get();
    }

    public static function reorderCategories(array $categoryIds)
    {
        foreach ($categoryIds as $index => $categoryId) {
            static::where('id', $categoryId)->update(['sort_order' => $index + 1]);
        }
    }
}