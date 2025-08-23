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
        'file_path',
        'scheduled_publish_at',
        'published_at',
        'images',
        'documents',
        'audios',
        'videos',
        'external_links',
        'completion_time_type',
        'difficulty_level',
        'views_count',
        'likes_count',
    ];

    protected $casts = [
        'scheduled_publish_at' => 'datetime',
        'published_at' => 'datetime',
        'images' => 'array',
        'documents' => 'array',
        'audios' => 'array',
        'videos' => 'array',
        'external_links' => 'array',
    ];

    // FIX: Remove any accessors/mutators for content to let it be stored as raw text
    // Do NOT add getContentAttribute or setContentAttribute methods

    // Relationships
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

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($lesson) {
            if (!$lesson->slug && $lesson->title) {
                $lesson->generateUniqueSlug();
            }

            if (!$lesson->order && $lesson->section) {
                $lesson->order = $lesson->section->lessons()->count() + 1;
            }

            if ($lesson->scheduled_publish_at && $lesson->scheduled_publish_at->isPast()) {
                $lesson->published_at = $lesson->scheduled_publish_at;
            }
        });

        static::updating(function ($lesson) {
            if ($lesson->isDirty('title')) {
                $lesson->generateUniqueSlug();
            }

            if ($lesson->isDirty('scheduled_publish_at')) {
                if ($lesson->scheduled_publish_at && $lesson->scheduled_publish_at->isPast()) {
                    $lesson->published_at = $lesson->scheduled_publish_at;
                } elseif (!$lesson->scheduled_publish_at) {
                    $lesson->published_at = now();
                }
            }
        });
    }

    protected function generateUniqueSlug()
    {
        $courseTitle = $this->section?->course?->title ?? '';
        $baseSlug = $courseTitle ? Str::slug($courseTitle . '-' . $this->title) : Str::slug($this->title);

        $slug = $baseSlug;
        $counter = 1;
        while (static::where('slug', $slug)->where('id', '!=', $this->id ?? 0)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        $this->slug = $slug;
    }

    // Media helper methods
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

    // Publishing methods
    public function isPublished()
    {
        if ($this->scheduled_publish_at) {
            return $this->scheduled_publish_at->isPast();
        }
        return !is_null($this->published_at);
    }

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

        return round($totalSize / (1024 * 1024), 2);
    }

    // Scopes
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

    public function hasMediaContent()
    {
        return $this->hasImage() ||
            $this->hasAudio() ||
            $this->hasVideo() ||
            $this->hasDocuments() ||
            $this->hasExternalLinks();
    }

    public function getPrimaryImageAttribute()
    {
        if ($this->image_path) {
            return $this->image_path;
        }

        $images = $this->getImagesArray();
        if (!empty($images)) {
            return $images[0]['path'] ?? null;
        }

        return null;
    }

    public function getEstimatedCompletionTimeAttribute()
    {
        if ($this->duration_minutes) {
            return $this->duration_minutes;
        }

        $wordCount = str_word_count(strip_tags($this->content ?? ''));
        $readingTime = ceil($wordCount / 200);

        $mediaTime = 0;
        if ($this->video_url || !empty($this->getVideosArray())) {
            $mediaTime += 10;
        }

        if ($this->hasAudio()) {
            $mediaTime += 5;
        }

        return max($readingTime + $mediaTime, 1);
    }

    public function getFormattedDurationAttribute()
    {
        if (!$this->duration_minutes) {
            return 'N/A';
        }

        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;

        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }

        return $minutes . 'm';
    }
}