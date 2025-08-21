<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Lesson extends Model
{
    protected $fillable = [
        'section_id',
        'title',
        'slug',
        'description',
        'content',
        'content_type',
        'video_url',
        'duration_minutes',
        'order',
        'text_content',
        'size_mb',
        'image_path',
        'audio_path',
        'is_free',
        'file_path',
        'is_premium',
        'price',
        'scheduled_publish_at',
        'images',
        'documents',
        'audios',
        'videos', // Added missing field
        'external_links',
        'completion_time_type',
        'difficulty_level',
        'published_at',
        'views_count', // Added missing field
        'likes_count', // Added missing field
    ];

    protected $casts = [
        'content' => 'array',
        'is_free' => 'boolean',
        'is_premium' => 'boolean',
        'price' => 'decimal:2',
        'scheduled_publish_at' => 'datetime',
        'published_at' => 'datetime',
        'images' => 'array',
        'documents' => 'array',
        'audios' => 'array',
        'videos' => 'array', // Added missing cast
        'external_links' => 'array',
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function course()
    {
        return $this->hasOneThrough(Course::class, Section::class, 'id', 'id', 'section_id', 'course_id');
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($lesson) {
            // Generate unique slug with course title
            if (!$lesson->slug && $lesson->title) {
                $lesson->generateUniqueSlug();
            }

            // Set order if not provided - Fixed potential null reference
            if (!$lesson->order && $lesson->section) {
                $lesson->order = $lesson->section->lessons()->count() + 1;
            }

            // Set published_at if scheduled
            if ($lesson->scheduled_publish_at && $lesson->scheduled_publish_at->isPast()) {
                $lesson->published_at = $lesson->scheduled_publish_at;
            }
        });

        static::updating(function ($lesson) {
            if ($lesson->isDirty('title')) {
                $lesson->generateUniqueSlug();
            }

            // Update published_at based on schedule
            if ($lesson->isDirty('scheduled_publish_at')) {
                if ($lesson->scheduled_publish_at && $lesson->scheduled_publish_at->isPast()) {
                    $lesson->published_at = $lesson->scheduled_publish_at;
                } elseif (!$lesson->scheduled_publish_at) {
                    $lesson->published_at = now();
                }
            }
        });
    }

    /**
     * Generate a unique slug for the lesson including course title
     */
    protected function generateUniqueSlug()
    {
        // Added null safety checks
        $courseTitle = $this->section?->course?->title ?? '';
        $baseSlug = $courseTitle ? Str::slug($courseTitle . '-' . $this->title) : Str::slug($this->title);

        // Ensure uniqueness
        $slug = $baseSlug;
        $counter = 1;
        while (static::where('slug', $slug)->where('id', '!=', $this->id ?? 0)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        $this->slug = $slug;
    }

    // Helper methods for media management
    public function hasImage()
    {
        return !empty($this->image_path) || !empty($this->images);
    }

    public function hasAudio()
    {
        return !empty($this->audio_path) || !empty($this->audios);
    }

    public function hasVideo()
    {
        return !empty($this->video_url) || !empty($this->videos);
    }

    public function hasDocuments()
    {
        return !empty($this->documents);
    }

    public function hasExternalLinks()
    {
        return !empty($this->external_links);
    }

    public function getImagesArray()
    {
        return is_string($this->images) ? json_decode($this->images, true) : ($this->images ?? []);
    }

    public function getDocumentsArray()
    {
        return is_string($this->documents) ? json_decode($this->documents, true) : ($this->documents ?? []);
    }

    public function getAudiosArray()
    {
        return is_string($this->audios) ? json_decode($this->audios, true) : ($this->audios ?? []);
    }

    public function getVideosArray()
    {
        return is_string($this->videos) ? json_decode($this->videos, true) : ($this->videos ?? []);
    }

    public function getExternalLinksArray()
    {
        return is_string($this->external_links) ? json_decode($this->external_links, true) : ($this->external_links ?? []);
    }

    /**
     * Check if lesson is published
     */
    public function isPublished()
    {
        if ($this->scheduled_publish_at) {
            return $this->scheduled_publish_at->isPast();
        }

        return !is_null($this->published_at);
    }

    /**
     * Get total file size in MB
     */
    public function getTotalFileSizeAttribute()
    {
        $totalSize = 0;

        foreach ($this->getImagesArray() as $image) {
            $totalSize += $image['size'] ?? 0;
        }

        foreach ($this->getDocumentsArray() as $document) {
            $totalSize += $document['size'] ?? 0;
        }

        foreach ($this->getAudiosArray() as $audio) {
            $totalSize += $audio['size'] ?? 0;
        }

        foreach ($this->getVideosArray() as $video) {
            $totalSize += $video['size'] ?? 0;
        }

        return round($totalSize / (1024 * 1024), 2); // Convert to MB
    }

    /**
     * Get formatted duration
     */
    public function getFormattedDurationAttribute()
    {
        if (!$this->duration_minutes) {
            return 'Not specified';
        }

        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;

        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }

        return $minutes . ' minutes';
    }

    /**
     * Scope for published lessons
     */
    public function scopePublished($query)
    {
        return $query->where(function ($q) {
            $q->whereNotNull('published_at')
                ->orWhere(function ($subq) {
                    $subq->whereNotNull('scheduled_publish_at')
                        ->where('scheduled_publish_at', '<=', now());
                });
        });
    }

    /**
     * Scope for free lessons
     */
    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }

    /**
     * Scope for premium lessons
     */
    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }

    /**
     * Check if lesson has any media content
     */
    public function hasMediaContent()
    {
        return $this->hasImage() ||
            $this->hasAudio() ||
            $this->hasVideo() ||
            $this->hasDocuments() ||
            $this->hasExternalLinks();
    }

    /**
     * Get the primary image for the lesson
     */
    public function getPrimaryImageAttribute()
    {
        // First check for legacy image_path
        if ($this->image_path) {
            return $this->image_path;
        }

        // Then check for images array
        $images = $this->getImagesArray();
        if (!empty($images)) {
            return $images[0]['path'] ?? null;
        }

        return null;
    }

    /**
     * Get estimated reading/completion time based on content
     */
    public function getEstimatedCompletionTimeAttribute()
    {
        if ($this->duration_minutes) {
            return $this->duration_minutes;
        }

        // Estimate based on content length (rough calculation)
        $wordCount = str_word_count(strip_tags($this->content ?? ''));
        $readingTime = ceil($wordCount / 200); // Average reading speed: 200 words per minute

        // Add time for media content
        $mediaTime = 0;
        if ($this->video_url || !empty($this->getVideosArray())) {
            $mediaTime += 10; // Assume 10 minutes for video content
        }

        if ($this->hasAudio()) {
            $mediaTime += 5; // Assume 5 minutes for audio content
        }

        return max($readingTime + $mediaTime, 1); // At least 1 minute
    }
}