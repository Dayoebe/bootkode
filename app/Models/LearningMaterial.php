<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class LearningMaterial extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'type',
        'course_id',
        'created_by',
        'content',
        'file_path',
        'file_size',
        'file_type',
        'original_filename',
        'tags',
        'is_public',
        'difficulty_level',
        'download_count',
        'view_count',
        'status',
        'published_at',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'published_at' => 'datetime',
        'file_size' => 'integer',
        'download_count' => 'integer',
        'view_count' => 'integer',
    ];

    const TYPES = [
        'document' => 'Document',
        'presentation' => 'Presentation',
        'worksheet' => 'Worksheet',
        'template' => 'Template',
        'guide' => 'Guide',
        'other' => 'Other'
    ];

    const DIFFICULTY_LEVELS = [
        'beginner' => 'Beginner',
        'intermediate' => 'Intermediate',
        'advanced' => 'Advanced',
        'expert' => 'Expert'
    ];

    const STATUSES = [
        'draft' => 'Draft',
        'published' => 'Published',
        'archived' => 'Archived'
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function reviews()
    {
        return $this->hasMany(MaterialReview::class);
    }

    public function downloads()
    {
        return $this->hasMany(MaterialDownload::class);
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($material) {
            if (!$material->status) {
                $material->status = 'published';
            }
            
            if ($material->status === 'published' && !$material->published_at) {
                $material->published_at = now();
            }
        });

        static::updating(function ($material) {
            if ($material->isDirty('status') && $material->status === 'published' && !$material->published_at) {
                $material->published_at = now();
            }
        });
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByDifficulty($query, $level)
    {
        return $query->where('difficulty_level', $level);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where(function($q) use ($userId) {
            $q->where('created_by', $userId)
              ->orWhere('is_public', true);
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

    // Accessors
    public function getFormattedFileSizeAttribute()
    {
        if (!$this->file_size) {
            return 'N/A';
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getTypeNameAttribute()
    {
        return self::TYPES[$this->type] ?? ucfirst($this->type);
    }

    public function getDifficultyNameAttribute()
    {
        return self::DIFFICULTY_LEVELS[$this->difficulty_level] ?? ucfirst($this->difficulty_level);
    }

    public function getStatusNameAttribute()
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function getTagsArrayAttribute()
    {
        return $this->tags ? array_map('trim', explode(',', $this->tags)) : [];
    }

    public function getFileExtensionAttribute()
    {
        if (!$this->original_filename) {
            return null;
        }
        
        return strtolower(pathinfo($this->original_filename, PATHINFO_EXTENSION));
    }

    public function getFileIconAttribute()
    {
        $extension = $this->file_extension;
        
        return match($extension) {
            'pdf' => 'fas fa-file-pdf text-red-500',
            'doc', 'docx' => 'fas fa-file-word text-blue-500',
            'xls', 'xlsx' => 'fas fa-file-excel text-green-500',
            'ppt', 'pptx' => 'fas fa-file-powerpoint text-orange-500',
            'jpg', 'jpeg', 'png', 'gif' => 'fas fa-file-image text-purple-500',
            'mp4', 'avi', 'mov' => 'fas fa-file-video text-red-500',
            'mp3', 'wav' => 'fas fa-file-audio text-yellow-500',
            'zip', 'rar', '7z' => 'fas fa-file-archive text-gray-500',
            'txt' => 'fas fa-file-alt text-gray-500',
            default => 'fas fa-file text-gray-400'
        };
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
        return $this->download_count > 10 || $this->view_count > 50;
    }

    // Helper methods
    public function incrementDownloadCount()
    {
        $this->increment('download_count');
    }

    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    public function canBeEditedBy($user)
    {
        return $user->isSuperAdmin() || 
               $user->isAcademyAdmin() || 
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
        if ($this->is_public || $this->created_by === $user->id) {
            return true;
        }

        if ($user->isSuperAdmin() || $user->isAcademyAdmin()) {
            return true;
        }

        // Check if user is enrolled in the associated course
        if ($this->course_id) {
            return $user->enrolledCourses()->where('course_id', $this->course_id)->exists();
        }

        return false;
    }

    public function hasFile()
    {
        return !empty($this->file_path);
    }

    public function isDocument()
    {
        return $this->type === 'document';
    }

    public function isPresentation()
    {
        return $this->type === 'presentation';
    }

    public function isWorksheet()
    {
        return $this->type === 'worksheet';
    }

    public function isTemplate()
    {
        return $this->type === 'template';
    }

    public function isGuide()
    {
        return $this->type === 'guide';
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

    public function makePublic()
    {
        $this->update(['is_public' => true]);
    }

    public function makePrivate()
    {
        $this->update(['is_public' => false]);
    }

    // Search functionality
    public static function search($query)
    {
        return static::where(function($q) use ($query) {
            $q->where('title', 'like', '%' . $query . '%')
              ->orWhere('description', 'like', '%' . $query . '%')
              ->orWhere('content', 'like', '%' . $query . '%')
              ->orWhere('tags', 'like', '%' . $query . '%');
        });
    }

    // Get popular materials
    public static function getPopular($limit = 10)
    {
        return static::published()
            ->public()
            ->orderByRaw('(download_count * 2 + view_count) DESC')
            ->limit($limit)
            ->get();
    }

    // Get recent materials
    public static function getRecent($limit = 10)
    {
        return static::published()
            ->public()
            ->latest('published_at')
            ->limit($limit)
            ->get();
    }

    // Get materials by course
    public static function getByCourse($courseId, $limit = null)
    {
        $query = static::published()
            ->where('course_id', $courseId)
            ->orderBy('created_at', 'desc');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    // Statistics methods
    public static function getTotalDownloads()
    {
        return static::sum('download_count');
    }

    public static function getTotalViews()
    {
        return static::sum('view_count');
    }

    public static function getTypeStatistics()
    {
        return static::selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();
    }

    public static function getDifficultyStatistics()
    {
        return static::selectRaw('difficulty_level, COUNT(*) as count')
            ->groupBy('difficulty_level')
            ->pluck('count', 'difficulty_level')
            ->toArray();
    }
}