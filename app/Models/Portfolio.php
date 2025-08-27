<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class Portfolio extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'category',
        'status',
        'project_url',
        'client_name',
        'technologies',
        'start_date',
        'end_date',
        'image_path',
        'additional_images',
        'views_count',
        'likes_count',
        'meta_description',
        'tags',
        'is_featured',
        'is_public',
        'sort_order',
    ];

    protected $casts = [
        'additional_images' => 'array',
        'tags' => 'array',
        'start_date' => 'date:Y-m-d',
        'end_date' => 'date:Y-m-d',
        'is_featured' => 'boolean',
        'is_public' => 'boolean',
        'views_count' => 'integer',
        'likes_count' => 'integer',
        'sort_order' => 'integer',
    ];

    protected $appends = [
        'image_url',
        'thumbnail_url',
        'formatted_technologies',
        'duration',
        'status_label',
        'category_label'
    ];

    public function setStartDateAttribute($value)
    {
        $this->attributes['start_date'] = empty($value) ? null : $value;
    }

    public function setEndDateAttribute($value)
    {
        $this->attributes['end_date'] = empty($value) ? null : $value;
    }
    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessors
    public function imageUrl(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->image_path
            ? Storage::url($this->image_path)
            : 'https://via.placeholder.com/800x600/f3f4f6/9ca3af?text=No+Image'
        );
    }

    public function thumbnailUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->image_path) {
                    return 'https://via.placeholder.com/300x300/f3f4f6/9ca3af?text=No+Image';
                }
                
                // Check if thumbnail exists
                $thumbnailPath = str_replace('/main/', '/thumbs/', $this->image_path);
                if (Storage::disk('public')->exists($thumbnailPath)) {
                    return Storage::url($thumbnailPath);
                }
                
                // Fallback to main image if thumbnail doesn't exist
                return Storage::url($this->image_path);
            }
        );
    }
    public function formattedTechnologies(): Attribute
    {
        return Attribute::make(
            get: fn() => collect(explode(',', $this->technologies))
                ->map(fn($tech) => trim($tech))
                ->filter()
                ->toArray()
        );
    }

    public function duration(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->start_date)
                    return 'Duration not specified';

                $start = $this->start_date;
                $end = $this->end_date ?? now();

                $months = $start->diffInMonths($end);

                if ($months == 0)
                    return '1 month';
                if ($months < 12)
                    return $months . ' months';

                $years = floor($months / 12);
                $remainingMonths = $months % 12;

                if ($remainingMonths == 0) {
                    return $years . ' ' . ($years == 1 ? 'year' : 'years');
                }

                return $years . ' ' . ($years == 1 ? 'year' : 'years') .
                    ', ' . $remainingMonths . ' months';
            }
        );
    }

    public function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->status) {
                'completed' => 'Completed',
                'in-progress' => 'In Progress',
                'planning' => 'Planning',
                'on-hold' => 'On Hold',
                default => 'Unknown'
            }
        );
    }

    public function categoryLabel(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->category) {
                'web-development' => 'Web Development',
                'mobile-app' => 'Mobile App',
                'ui-ux-design' => 'UI/UX Design',
                'graphic-design' => 'Graphic Design',
                'branding' => 'Branding',
                'photography' => 'Photography',
                'video-editing' => 'Video Editing',
                'data-analysis' => 'Data Analysis',
                'machine-learning' => 'Machine Learning',
                'other' => 'Other',
                default => 'Uncategorized'
            }
        );
    }

    // Scopes
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Helper methods
    public function getStatusColor()
    {
        return match ($this->status) {
            'completed' => 'green',
            'in-progress' => 'blue',
            'planning' => 'yellow',
            'on-hold' => 'red',
            default => 'gray'
        };
    }

    public function getCategoryIcon()
    {
        return match ($this->category) {
            'web-development' => '💻',
            'mobile-app' => '📱',
            'ui-ux-design' => '🎨',
            'graphic-design' => '🖼️',
            'branding' => '🎯',
            'photography' => '📸',
            'video-editing' => '🎬',
            'data-analysis' => '📊',
            'machine-learning' => '🤖',
            'other' => '🔧',
            default => '📁'
        };
    }

    public function getShareableUrl()
    {
        return route('portfolio', $this->slug);
    }

    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function toggleLike()
    {
        // This would typically check if user has already liked
        $this->increment('likes_count');
    }
}