<?php

namespace App\Models\Content;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\Core\User;

class BlogPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'status',
        'published_at',
        'author_id',
        'category_id',
        'views_count',
        'likes_count',
        'comments_count',
        'read_time',
        'allow_comments',
        'is_featured',
        'tags'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'meta_keywords' => 'array',
        'allow_comments' => 'boolean',
        'is_featured' => 'boolean',
        'tags' => 'array',
        'views_count' => 'integer',
        'likes_count' => 'integer',
        'comments_count' => 'integer',
        'read_time' => 'integer'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($post) {
            if (!$post->slug) {
                $post->slug = Str::slug($post->title);
            }
            $post->read_time = self::calculateReadTime($post->content);
        });
        
        static::updating(function ($post) {
            if ($post->isDirty('title') && !$post->isDirty('slug')) {
                $post->slug = Str::slug($post->title);
            }
            if ($post->isDirty('content')) {
                $post->read_time = self::calculateReadTime($post->content);
            }
        });
    }

    // NEW: Get all categories
    public function getCategoriesAttribute()
    {
        $tags = $this->attributes['tags'] ?? null;
        
        if (is_string($tags)) {
            $tags = json_decode($tags, true);
        }
        
        $categoryIds = $tags['categories'] ?? [];
        
        // Always include primary category first
        if ($this->category_id && !in_array($this->category_id, $categoryIds)) {
            array_unshift($categoryIds, $this->category_id);
        }
        
        if (empty($categoryIds)) {
            return collect();
        }
        
        return BlogCategory::whereIn('id', array_unique($categoryIds))->get();
    }

    // NEW: Get all category IDs
    public function getAllCategoryIdsAttribute()
    {
        $tags = $this->attributes['tags'] ?? null;
        
        if (is_string($tags)) {
            $tags = json_decode($tags, true);
        }
        
        $categoryIds = $tags['categories'] ?? [];
        
        // Always include primary category
        if ($this->category_id && !in_array($this->category_id, $categoryIds)) {
            array_unshift($categoryIds, $this->category_id);
        }
        
        return array_values(array_unique(array_filter($categoryIds)));
    }

    // NEW: Get user tags only (excluding categories)
    public function getUserTagsAttribute()
    {
        $tags = $this->attributes['tags'] ?? null;
        
        if (is_string($tags)) {
            $tags = json_decode($tags, true);
        }
        
        // Return user tags array, or empty array if not set
        return $tags['tags'] ?? [];
    }

    // Relationships
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    public function comments()
    {
        return $this->hasMany(BlogComment::class, 'post_id');
    }

    public function approvedComments()
    {
        return $this->comments()->where('status', 'approved')->whereNull('parent_id');
    }

    public function reactions()
    {
        return $this->morphMany(BlogReaction::class, 'reactable');
    }

    public function likes()
    {
        return $this->reactions()->where('type', 'like');
    }

    public function bookmarks()
    {
        return $this->reactions()->where('type', 'bookmark');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published')->where('published_at', '<=', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByCategory($query, $categorySlug)
    {
        $category = BlogCategory::where('slug', $categorySlug)->first();
        
        if (!$category) {
            return $query->whereRaw('1 = 0');
        }
        
        return $query->where(function($q) use ($category) {
            $q->where('category_id', $category->id)
              ->orWhereJsonContains('tags->categories', $category->id);
        });
    }

    public function scopeByTag($query, $tag)
    {
        return $query->whereJsonContains('tags->tags', $tag);
    }

    public function scopeSearch($query, $term)
    {
        return $query->whereFullText(['title', 'content', 'excerpt'], $term);
    }

    // Helper methods
    public static function calculateReadTime($content)
    {
        $wordCount = str_word_count(strip_tags($content));
        return max(1, round($wordCount / 200));
    }

    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function isPublished()
    {
        return $this->status === 'published' && $this->published_at <= now();
    }

    public function getExcerptAttribute($value)
    {
        return $value ?: Str::limit(strip_tags($this->content), 200);
    }
    public function getFeaturedImageUrlAttribute()
{
    return $this->featured_image ?: asset('img/2.png');
}
}