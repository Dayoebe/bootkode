<?php

namespace App\Models\Learning;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Models\Core\User;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'type',
        'category_id',
        'created_by',
        'updated_by',
        'status',
        'visibility',
        'featured',
        'meta_title',
        'meta_description',
        'tags',
        'version',
        'parent_id',
        'order',
        'view_count',
        'download_count',
        'published_at',
        'last_reviewed_at',
        'reviewed_by',
        'attachments',
        'related_documents',
        'language',
        'difficulty_level',
        'estimated_reading_time',
        'table_of_contents',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'published_at' => 'datetime',
        'last_reviewed_at' => 'datetime',
        'attachments' => 'array',
        'related_documents' => 'array',
        'table_of_contents' => 'array',
        'view_count' => 'integer',
        'download_count' => 'integer',
        'order' => 'integer',
        'version' => 'decimal:2',
    ];

    const TYPES = [
        'guide' => 'Guide',
        'manual' => 'Manual',
        'tutorial' => 'Tutorial',
        'reference' => 'Reference',
        'policy' => 'Policy',
        'procedure' => 'Procedure',
        'faq' => 'FAQ',
        'article' => 'Article',
        'whitepaper' => 'Whitepaper',
        'case_study' => 'Case Study',
        'other' => 'Other'
    ];

    const STATUSES = [
        'draft' => 'Draft',
        'pending_review' => 'Pending Review',
        'published' => 'Published',
        'archived' => 'Archived',
        'deprecated' => 'Deprecated'
    ];

    const VISIBILITY_LEVELS = [
        'public' => 'Public',
        'private' => 'Private',
        'restricted' => 'Restricted',
        'internal' => 'Internal'
    ];

    const DIFFICULTY_LEVELS = [
        'beginner' => 'Beginner',
        'intermediate' => 'Intermediate',
        'advanced' => 'Advanced',
        'expert' => 'Expert'
    ];

    // Relationships
    public function category()
    {
        return $this->belongsTo(DocumentCategory::class, 'category_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by'); // UPDATED
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by'); // UPDATED
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by'); // UPDATED
    }

    public function parent()
    {
        return $this->belongsTo(Document::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Document::class, 'parent_id')->orderBy('order');
    }

    public function versions()
    {
        return $this->hasMany(\App\Models\Learning\DocumentVersion::class); // UPDATED
    }

    public function reviews()
    {
        return $this->hasMany(\App\Models\Learning\DocumentReview::class); // UPDATED
    }

    public function comments()
    {
        return $this->hasMany(\App\Models\Learning\DocumentComment::class); // UPDATED
    }

    public function views()
    {
        return $this->hasMany(\App\Models\Learning\DocumentView::class); // UPDATED
    }

    public function bookmarks()
    {
        return $this->hasMany(\App\Models\Learning\DocumentBookmark::class); // UPDATED
    }
    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($document) {
            if (!$document->slug) {
                $document->slug = $document->generateUniqueSlug($document->title);
            }

            if (!$document->status) {
                $document->status = 'draft';
            }

            if (!$document->visibility) {
                $document->visibility = 'public';
            }

            if (!$document->language) {
                $document->language = 'en';
            }

            if (!$document->version) {
                $document->version = 1.0;
            }

            // Auto-generate excerpt if not provided
            if (!$document->excerpt && $document->content) {
                $document->excerpt = Str::limit(strip_tags($document->content), 200);
            }

            // Calculate estimated reading time
            if (!$document->estimated_reading_time && $document->content) {
                $wordCount = str_word_count(strip_tags($document->content));
                $document->estimated_reading_time = max(1, ceil($wordCount / 200)); // 200 words per minute
            }

            if ($document->status === 'published' && !$document->published_at) {
                $document->published_at = now();
            }
        });

        static::updating(function ($document) {
            if ($document->isDirty('title') && !$document->isDirty('slug')) {
                $document->slug = $document->generateUniqueSlug($document->title);
            }

            if ($document->isDirty('status') && $document->status === 'published' && !$document->published_at) {
                $document->published_at = now();
            }

            // Update excerpt if content changed
            if ($document->isDirty('content') && !$document->isDirty('excerpt')) {
                $document->excerpt = Str::limit(strip_tags($document->content), 200);
            }

            // Update reading time if content changed
            if ($document->isDirty('content') && !$document->isDirty('estimated_reading_time')) {
                $wordCount = str_word_count(strip_tags($document->content));
                $document->estimated_reading_time = max(1, ceil($wordCount / 200));
            }

            // Increment version on content changes
            if ($document->isDirty('content')) {
                $document->version = $document->version + 0.1;
            }
        });

        static::saved(function ($document) {
            // Create a version record when content is updated
            if ($document->isDirty('content') && !$document->wasRecentlyCreated) {
                $document->versions()->create([
                    'version' => $document->version,
                    'content' => $document->content,
                    'updated_by' => auth()->id(),
                    'changes_summary' => 'Content updated'
                ]);
            }
        });
    }

    // Generate unique slug
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

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopePublic($query)
    {
        return $query->where('visibility', 'public');
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('created_by', $userId)
                ->orWhere('visibility', 'public');
        });
    }

    public function scopeSearchByTitle($query, $search)
    {
        return $query->where('title', 'like', '%' . $search . '%');
    }

    public function scopeWithTags($query, $tags)
    {
        if (is_array($tags)) {
            $tags = implode(',', $tags);
        }

        return $query->where('tags', 'like', '%' . $tags . '%');
    }

    public function scopeOrderByPopularity($query)
    {
        return $query->orderByRaw('(view_count * 2 + download_count * 3) DESC');
    }

    // Accessors
    public function getTypeNameAttribute()
    {
        return self::TYPES[$this->type] ?? ucfirst($this->type);
    }

    public function getStatusNameAttribute()
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function getVisibilityNameAttribute()
    {
        return self::VISIBILITY_LEVELS[$this->visibility] ?? ucfirst($this->visibility);
    }

    public function getDifficultyNameAttribute()
    {
        return self::DIFFICULTY_LEVELS[$this->difficulty_level] ?? ucfirst($this->difficulty_level);
    }

    public function getTagsArrayAttribute()
    {
        return $this->tags ? array_map('trim', explode(',', $this->tags)) : [];
    }

    public function getFormattedReadingTimeAttribute()
    {
        if (!$this->estimated_reading_time) {
            return 'Unknown';
        }

        if ($this->estimated_reading_time < 60) {
            return $this->estimated_reading_time . ' min read';
        }

        $hours = floor($this->estimated_reading_time / 60);
        $minutes = $this->estimated_reading_time % 60;

        if ($minutes > 0) {
            return $hours . 'h ' . $minutes . 'm read';
        }

        return $hours . 'h read';
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }

    public function getTotalReviewsAttribute()
    {
        return $this->reviews()->count();
    }

    public function getIsNewAttribute()
    {
        return $this->created_at->gt(now()->subDays(7));
    }

    public function getIsPopularAttribute()
    {
        return $this->view_count > 50 || $this->download_count > 20;
    }

    public function getIsOutdatedAttribute()
    {
        return $this->updated_at->lt(now()->subMonths(6));
    }

    public function getWordCountAttribute()
    {
        return str_word_count(strip_tags($this->content ?? ''));
    }

    public function getCharacterCountAttribute()
    {
        return strlen(strip_tags($this->content ?? ''));
    }

    public function getAttachmentsCountAttribute()
    {
        return count($this->attachments ?? []);
    }

    public function getRelatedDocumentsCountAttribute()
    {
        return count($this->related_documents ?? []);
    }

    public function getCanonicalUrlAttribute()
    {
        return route('documents.show', $this->slug);
    }

    // Helper methods
    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    public function incrementDownloadCount()
    {
        $this->increment('download_count');
    }

    public function canBeEditedBy($user)
    {
        return $user->isSuperAdmin() ||
            $user->isAcademyAdmin() ||
            $user->isContentEditor() ||
            $this->created_by === $user->id;
    }

    public function canBeDeletedBy($user)
    {
        return $user->isSuperAdmin() ||
            $user->isAcademyAdmin() ||
            $this->created_by === $user->id;
    }

    public function canBeViewedBy($user)
    {
        switch ($this->visibility) {
            case 'public':
                return true;
            case 'private':
                return $this->created_by === $user->id ||
                    $user->isSuperAdmin() ||
                    $user->isAcademyAdmin();
            case 'restricted':
                return $user->isContentEditor() ||
                    $user->isAcademyAdmin() ||
                    $user->isSuperAdmin() ||
                    $this->created_by === $user->id;
            case 'internal':
                return !$user->isStudent();
            default:
                return false;
        }
    }

    public function canBeReviewedBy($user)
    {
        return ($user->isContentEditor() || $user->isAcademyAdmin() || $user->isSuperAdmin()) &&
            $this->created_by !== $user->id;
    }

    public function hasAttachments()
    {
        return !empty($this->attachments);
    }

    public function hasRelatedDocuments()
    {
        return !empty($this->related_documents);
    }

    public function hasTableOfContents()
    {
        return !empty($this->table_of_contents);
    }

    public function isPublished()
    {
        return $this->status === 'published';
    }

    public function isDraft()
    {
        return $this->status === 'draft';
    }

    public function isPendingReview()
    {
        return $this->status === 'pending_review';
    }

    public function isArchived()
    {
        return $this->status === 'archived';
    }

    public function isDeprecated()
    {
        return $this->status === 'deprecated';
    }

    public function isPublic()
    {
        return $this->visibility === 'public';
    }

    public function isPrivate()
    {
        return $this->visibility === 'private';
    }

    public function publish()
    {
        $this->update([
            'status' => 'published',
            'published_at' => now()
        ]);
    }

    public function archive()
    {
        $this->update(['status' => 'archived']);
    }

    public function deprecate()
    {
        $this->update(['status' => 'deprecated']);
    }

    public function submitForReview()
    {
        $this->update(['status' => 'pending_review']);
    }

    public function approve($reviewerId)
    {
        $this->update([
            'status' => 'published',
            'published_at' => now(),
            'last_reviewed_at' => now(),
            'reviewed_by' => $reviewerId
        ]);
    }

    public function reject($reviewerId, $reason = null)
    {
        $this->update([
            'status' => 'draft',
            'last_reviewed_at' => now(),
            'reviewed_by' => $reviewerId
        ]);

        if ($reason) {
            $this->reviews()->create([
                'user_id' => $reviewerId,
                'type' => 'rejection',
                'comments' => $reason,
                'reviewed_at' => now()
            ]);
        }
    }

    public function makePublic()
    {
        $this->update(['visibility' => 'public']);
    }

    public function makePrivate()
    {
        $this->update(['visibility' => 'private']);
    }

    public function toggleFeatured()
    {
        $this->update(['featured' => !$this->featured]);
    }

    public function addAttachment($filePath, $originalName, $size = null)
    {
        $attachments = $this->attachments ?? [];
        $attachments[] = [
            'file_path' => $filePath,
            'original_name' => $originalName,
            'size' => $size,
            'uploaded_at' => now()->toISOString()
        ];

        $this->update(['attachments' => $attachments]);
    }

    public function removeAttachment($index)
    {
        $attachments = $this->attachments ?? [];
        if (isset($attachments[$index])) {
            unset($attachments[$index]);
            $this->update(['attachments' => array_values($attachments)]);
        }
    }

    public function addRelatedDocument($documentId)
    {
        $related = $this->related_documents ?? [];
        if (!in_array($documentId, $related)) {
            $related[] = $documentId;
            $this->update(['related_documents' => $related]);
        }
    }

    public function removeRelatedDocument($documentId)
    {
        $related = $this->related_documents ?? [];
        $related = array_filter($related, fn($id) => $id != $documentId);
        $this->update(['related_documents' => array_values($related)]);
    }

    public function generateTableOfContents()
    {
        if (!$this->content) {
            return [];
        }

        // Extract headings from content (assuming HTML content)
        preg_match_all('/<h([1-6])[^>]*>(.*?)<\/h[1-6]>/i', $this->content, $matches, PREG_SET_ORDER);

        $toc = [];
        foreach ($matches as $match) {
            $level = (int) $match[1];
            $text = strip_tags($match[2]);
            $id = Str::slug($text);

            $toc[] = [
                'level' => $level,
                'text' => $text,
                'id' => $id
            ];
        }

        $this->update(['table_of_contents' => $toc]);
        return $toc;
    }

    // Search functionality
    public static function search($query)
    {
        return static::where(function ($q) use ($query) {
            $q->where('title', 'like', '%' . $query . '%')
                ->orWhere('content', 'like', '%' . $query . '%')
                ->orWhere('excerpt', 'like', '%' . $query . '%')
                ->orWhere('tags', 'like', '%' . $query . '%');
        });
    }

    // Get popular documents
    public static function getPopular($limit = 10)
    {
        return static::published()
            ->public()
            ->orderByRaw('(view_count * 2 + download_count * 3) DESC')
            ->limit($limit)
            ->get();
    }

    // Get recent documents
    public static function getRecent($limit = 10)
    {
        return static::published()
            ->public()
            ->latest('published_at')
            ->limit($limit)
            ->get();
    }

    // Get featured documents
    public static function getFeatured($limit = 10)
    {
        return static::published()
            ->public()
            ->featured()
            ->latest('published_at')
            ->limit($limit)
            ->get();
    }

    // Get documents by category
    public static function getByCategory($categoryId, $limit = null)
    {
        $query = static::published()
            ->where('category_id', $categoryId)
            ->orderBy('created_at', 'desc');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    // Get documents pending review
    public static function getPendingReview($limit = null)
    {
        $query = static::where('status', 'pending_review')
            ->latest('updated_at');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    // Statistics methods
    public static function getTotalViews()
    {
        return static::sum('view_count');
    }

    public static function getTotalDownloads()
    {
        return static::sum('download_count');
    }

    public static function getTypeStatistics()
    {
        return static::selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();
    }

    public static function getStatusStatistics()
    {
        return static::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }

    public static function getVisibilityStatistics()
    {
        return static::selectRaw('visibility, COUNT(*) as count')
            ->groupBy('visibility')
            ->pluck('count', 'visibility')
            ->toArray();
    }

    public static function getMonthlyCreationStats($months = 12)
    {
        return static::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths($months))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('count', 'month')
            ->toArray();
    }
}