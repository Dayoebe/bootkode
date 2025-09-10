<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Carbon\Carbon;

class Page extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'status',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_image',
        'no_index',
        'template',
        'custom_css',
        'custom_js',
        'page_blocks',
        'shortcodes',
        'settings',
        'published_at',
        'scheduled_at',
        'expires_at',
        'created_by',
        'updated_by',
        'last_reviewed_at',
        'reviewed_by',
    ];

    protected $casts = [
        'custom_css' => 'array',
        'custom_js' => 'array',
        'page_blocks' => 'array',
        'shortcodes' => 'array',
        'settings' => 'array',
        'published_at' => 'datetime',
        'scheduled_at' => 'datetime',
        'expires_at' => 'datetime',
        'last_reviewed_at' => 'datetime',
        'no_index' => 'boolean',
    ];

    // Constants
    const STATUS_DRAFT = 'draft';
    const STATUS_PUBLISHED = 'published';
    const STATUS_ARCHIVED = 'archived';

    const TEMPLATE_DEFAULT = 'default';
    const TEMPLATE_LANDING = 'landing';
    const TEMPLATE_BLOG = 'blog';
    const TEMPLATE_FULL_WIDTH = 'full-width';
    const TEMPLATE_MINIMAL = 'minimal';

    // Boot method for auto-generating slugs
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($page) {
            if (empty($page->slug)) {
                $page->slug = $page->generateUniqueSlug($page->title);
            }
            $page->created_by = auth()->id();
            
            // Auto-generate meta fields if empty
            if (empty($page->meta_title)) {
                $page->meta_title = Str::limit($page->title, 55);
            }
            if (empty($page->meta_description) && $page->excerpt) {
                $page->meta_description = Str::limit($page->excerpt, 155);
            }
        });

        static::updating(function ($page) {
            $page->updated_by = auth()->id();
            
            // Update slug if title changed and slug wasn't manually set
            if ($page->isDirty('title') && !$page->isDirty('slug')) {
                $page->slug = $page->generateUniqueSlug($page->title, $page->id);
            }
        });

        static::saved(function ($page) {
            // Handle scheduled publishing
            if ($page->scheduled_at && $page->status === self::STATUS_DRAFT && $page->scheduled_at <= now()) {
                $page->update(['status' => self::STATUS_PUBLISHED, 'published_at' => now()]);
            }
        });
    }

    // Relationships
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function media(): BelongsToMany
    {
        return $this->belongsToMany(PageMedia::class, 'page_media_attachments', 'page_id', 'media_id')
            ->withPivot(['context', 'sort_order'])
            ->withTimestamps()
            ->orderBy('pivot_sort_order');
    }

    public function featuredMedia(): BelongsToMany
    {
        return $this->media()->wherePivot('context', 'featured');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED)
            ->where(function ($q) {
                $q->whereNull('published_at')
                  ->orWhere('published_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', self::STATUS_DRAFT)
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expires_at')
            ->where('expires_at', '<=', now());
    }

    public function scopeByTemplate($query, $template)
    {
        return $query->where('template', $template);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('content', 'like', "%{$search}%")
              ->orWhere('meta_description', 'like', "%{$search}%");
        });
    }

    // Helper Methods
    public function generateUniqueSlug($title, $excludeId = null): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;

        while ($this->slugExists($slug, $excludeId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function slugExists($slug, $excludeId = null): bool
    {
        $query = static::where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        return $query->exists();
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED 
            && ($this->published_at === null || $this->published_at <= now())
            && ($this->expires_at === null || $this->expires_at > now());
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isScheduled(): bool
    {
        return $this->isDraft() && $this->scheduled_at && $this->scheduled_at > now();
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at <= now();
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_PUBLISHED => $this->isExpired() ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800',
            self::STATUS_DRAFT => $this->isScheduled() ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800',
            self::STATUS_ARCHIVED => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusLabel(): string
    {
        if ($this->isExpired()) return 'Expired';
        if ($this->isScheduled()) return 'Scheduled';
        return ucfirst($this->status);
    }

    public function getUrl(): string
    {
        return url('/' . $this->slug);
    }

    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    // SEO Helpers
    public function getMetaTitle(): string
    {
        return $this->meta_title ?: $this->title;
    }

    public function getMetaDescription(): string
    {
        return $this->meta_description ?: Str::limit(strip_tags($this->content), 155);
    }

    public function getOgImage(): ?string
    {
        if ($this->og_image) {
            return $this->og_image;
        }

        $featuredImage = $this->featuredMedia()->first();
        return $featuredImage ? asset($featuredImage->file_path) : null;
    }

    // Content Processing
    public function getProcessedContent(): string
    {
        $content = $this->content;
        
        // Process shortcodes
        $content = $this->processShortcodes($content);
        
        // Process page blocks if they exist
        if ($this->page_blocks) {
            $content = $this->processPageBlocks($content);
        }
        
        return $content;
    }

    private function processShortcodes(string $content): string
    {
        // Process common shortcodes
        $shortcodes = [
            '/\[date\]/' => date('Y-m-d'),
            '/\[year\]/' => date('Y'),
            '/\[site_name\]/' => config('app.name'),
            '/\[current_user\]/' => auth()->user()?->name ?? 'Guest',
        ];

        // Add custom shortcodes from the page settings
        if ($this->shortcodes) {
            foreach ($this->shortcodes as $shortcode => $replacement) {
                $shortcodes["/\[{$shortcode}\]/"] = $replacement;
            }
        }

        return preg_replace(array_keys($shortcodes), array_values($shortcodes), $content);
    }

    private function processPageBlocks(string $content): string
    {
        // Process blocks like [block:hero], [block:testimonials], etc.
        return preg_replace_callback('/\[block:(\w+)\]/', function ($matches) {
            $blockName = $matches[1];
            $blockData = $this->page_blocks[$blockName] ?? null;
            
            if (!$blockData) return $matches[0];
            
            // Return processed block content
            return view("pages.blocks.{$blockName}", ['data' => $blockData])->render();
        }, $content);
    }

    // Analytics
    public function getAnalyticsData(): array
    {
        return [
            'views' => $this->view_count,
            'avg_time_on_page' => $this->avg_time_on_page,
            'bounce_rate' => $this->bounce_rate,
            'last_30_days_views' => $this->getRecentViewsCount(30),
            'performance_score' => $this->calculatePerformanceScore(),
        ];
    }

    private function getRecentViewsCount(int $days): int
    {
        // This would typically query an analytics table
        // For now, we'll return a placeholder
        return rand(10, 100);
    }

    private function calculatePerformanceScore(): int
    {
        $score = 0;
        
        // SEO completeness
        $score += $this->meta_title ? 20 : 0;
        $score += $this->meta_description ? 20 : 0;
        $score += $this->og_image ? 15 : 0;
        
        // Content quality
        $wordCount = str_word_count(strip_tags($this->content));
        $score += min($wordCount / 10, 25); // Max 25 points for content length
        
        // Performance metrics
        if ($this->avg_time_on_page > 30) $score += 10;
        if ($this->bounce_rate < 50) $score += 10;
        
        return min($score, 100);
    }

    // Template helpers
    public static function getAvailableTemplates(): array
    {
        return [
            self::TEMPLATE_DEFAULT => 'Default Template',
            self::TEMPLATE_LANDING => 'Landing Page',
            self::TEMPLATE_BLOG => 'Blog Style',
            self::TEMPLATE_FULL_WIDTH => 'Full Width',
            self::TEMPLATE_MINIMAL => 'Minimal',
        ];
    }

    public function getTemplateView(): string
    {
        return "pages.templates.{$this->template}";
    }

    // Activity Logging
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('page')
            ->logOnly(['title', 'slug', 'status', 'published_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        return "Page '{$this->title}' has been {$eventName}";
    }
}