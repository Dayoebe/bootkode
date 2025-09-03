<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VideoLibrary extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'video_url',
        'video_type',
        'thumbnail',
        'duration_seconds',
        'course_id',
        'lesson_id',
        'uploaded_by',
        'category',
        'tags',
        'is_public',
        'quality',
        'file_size',
        'views_count',
        'likes_count',
        'status',
        'published_at',
        'featured',
        'captions_file',
        'transcript',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'featured' => 'boolean',
        'published_at' => 'datetime',
        'duration_seconds' => 'integer',
        'file_size' => 'integer',
        'views_count' => 'integer',
        'likes_count' => 'integer',
    ];

    const VIDEO_TYPES = [
        'upload' => 'Uploaded Video',
        'youtube' => 'YouTube',
        'vimeo' => 'Vimeo',
        'external' => 'External Link'
    ];

    const CATEGORIES = [
        'lecture' => 'Lecture',
        'tutorial' => 'Tutorial',
        'demo' => 'Demo',
        'webinar' => 'Webinar',
        'interview' => 'Interview',
        'presentation' => 'Presentation',
        'other' => 'Other'
    ];

    const QUALITIES = [
        '480p' => '480p',
        '720p' => '720p (HD)',
        '1080p' => '1080p (Full HD)',
        '1440p' => '1440p (2K)',
        '2160p' => '2160p (4K)'
    ];

    const STATUSES = [
        'processing' => 'Processing',
        'published' => 'Published',
        'draft' => 'Draft',
        'archived' => 'Archived',
        'private' => 'Private'
    ];

    // Relationships
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function views()
    {
        return $this->hasMany(VideoView::class);
    }

    public function likes()
    {
        return $this->hasMany(VideoLike::class);
    }

    public function comments()
    {
        return $this->hasMany(VideoComment::class);
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($video) {
            if (!$video->status) {
                $video->status = 'published';
            }
            
            if ($video->status === 'published' && !$video->published_at) {
                $video->published_at = now();
            }
        });

        static::updating(function ($video) {
            if ($video->isDirty('status') && $video->status === 'published' && !$video->published_at) {
                $video->published_at = now();
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

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByVideoType($query, $type)
    {
        return $query->where('video_type', $type);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where(function($q) use ($userId) {
            $q->where('uploaded_by', $userId)
              ->orWhere('is_public', true);
        });
    }

    // Accessors
    public function getFormattedDurationAttribute()
    {
        if (!$this->duration_seconds) {
            return 'Unknown';
        }

        $hours = floor($this->duration_seconds / 3600);
        $minutes = floor(($this->duration_seconds % 3600) / 60);
        $seconds = $this->duration_seconds % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }

        return sprintf('%02d:%02d', $minutes, $seconds);
    }

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

    public function getCategoryNameAttribute()
    {
        return self::CATEGORIES[$this->category] ?? ucfirst($this->category);
    }

    public function getVideoTypeNameAttribute()
    {
        return self::VIDEO_TYPES[$this->video_type] ?? ucfirst($this->video_type);
    }

    public function getStatusNameAttribute()
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    public function getTagsArrayAttribute()
    {
        return $this->tags ? array_map('trim', explode(',', $this->tags)) : [];
    }

    public function getEmbedUrlAttribute()
    {
        switch ($this->video_type) {
            case 'youtube':
                // Extract video ID from various YouTube URL formats
                preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $this->video_url, $matches);
                return isset($matches[1]) ? "https://www.youtube.com/embed/{$matches[1]}" : $this->video_url;
                
            case 'vimeo':
                // Extract video ID from Vimeo URL
                preg_match('/vimeo\.com\/(\d+)/', $this->video_url, $matches);
                return isset($matches[1]) ? "https://player.vimeo.com/video/{$matches[1]}" : $this->video_url;
                
            default:
                return $this->video_url;
        }
    }

    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail) {
            return $this->thumbnail;
        }

        // Generate default thumbnails for video platforms
        switch ($this->video_type) {
            case 'youtube':
                preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $this->video_url, $matches);
                return isset($matches[1]) ? "https://img.youtube.com/vi/{$matches[1]}/maxresdefault.jpg" : null;
                
            case 'vimeo':
                // For Vimeo, you'd need to make an API call to get the thumbnail
                // This is a placeholder
                return null;
                
            default:
                return null;
        }
    }

    public function getIsNewAttribute()
    {
        return $this->created_at->gt(now()->subDays(7));
    }

    public function getIsPopularAttribute()
    {
        return $this->views_count > 100 || $this->likes_count > 20;
    }

    // Helper methods
    public function incrementViewCount()
    {
        $this->increment('views_count');
    }

    public function incrementLikeCount()
    {
        $this->increment('likes_count');
    }

    public function decrementLikeCount()
    {
        $this->decrement('likes_count');
    }

    public function canBeEditedBy($user)
    {
        return $user->isSuperAdmin() || 
               $user->isAcademyAdmin() || 
               $this->uploaded_by === $user->id;
    }

    public function canBeDeletedBy($user)
    {
        return $user->isSuperAdmin() || 
               $user->isAcademyAdmin() || 
               $this->uploaded_by === $user->id;
    }

    public function canBeViewedBy($user)
    {
        if ($this->is_public || $this->uploaded_by === $user->id) {
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

    public function hasTranscript()
    {
        return !empty($this->transcript);
    }

    public function hasCaptions()
    {
        return !empty($this->captions_file);
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

    public function toggleFeatured()
    {
        $this->update(['featured' => !$this->featured]);
    }

    // Search functionality
    public static function search($query)
    {
        return static::where(function($q) use ($query) {
            $q->where('title', 'like', '%' . $query . '%')
              ->orWhere('description', 'like', '%' . $query . '%')
              ->orWhere('tags', 'like', '%' . $query . '%');
        });
    }

    // Get popular videos
    public static function getPopular($limit = 10)
    {
        return static::published()
            ->public()
            ->orderByRaw('(views_count + likes_count * 5) DESC')
            ->limit($limit)
            ->get();
    }

    // Get recent videos
    public static function getRecent($limit = 10)
    {
        return static::published()
            ->public()
            ->latest('published_at')
            ->limit($limit)
            ->get();
    }

    // Get featured videos
    public static function getFeatured($limit = 10)
    {
        return static::published()
            ->public()
            ->featured()
            ->latest('published_at')
            ->limit($limit)
            ->get();
    }

    // Get videos by course
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
    public static function getTotalViews()
    {
        return static::sum('views_count');
    }

    public static function getTotalLikes()
    {
        return static::sum('likes_count');
    }

    public static function getCategoryStatistics()
    {
        return static::selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category')
            ->toArray();
    }

    public static function getTypeStatistics()
    {
        return static::selectRaw('video_type, COUNT(*) as count')
            ->groupBy('video_type')
            ->pluck('count', 'video_type')
            ->toArray();
    }
}