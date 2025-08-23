<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'instructor_id',
        'category_id',
        'title',
        'subtitle',
        'slug',
        'description',
        'thumbnail',
        'difficulty_level',
        'estimated_duration_minutes',
        'price',
        'is_premium',
        'is_free',
        'has_offline_content',
        'is_published',
        'is_approved',
        'scheduled_publish_at',
        'published_at',
        'target_audience',
        'learning_outcomes',
        'prerequisites',
        'syllabus_overview',
        'total_modules',
        'total_lessons',
        'total_projects',
        'total_assessments',
        'faqs',
        'certificate_template',
        'has_projects',
        'has_assessments',
        'completion_rate_threshold',
        'images',
        'documents',
        'videos',
        'external_links',
        'views_count',
        'likes_count',
        'average_rating',
    ];

    protected $casts = [
        'is_premium' => 'boolean',
        'is_free' => 'boolean',
        'is_published' => 'boolean',
        'is_approved' => 'boolean',
        'has_offline_content' => 'boolean',
        'has_projects' => 'boolean',
        'has_assessments' => 'boolean',
        'scheduled_publish_at' => 'datetime',
        'published_at' => 'datetime',
        'learning_outcomes' => 'array',
        'prerequisites' => 'array',
        'faqs' => 'array',
        'images' => 'array',
        'documents' => 'array',
        'videos' => 'array',
        'external_links' => 'array',
        'price' => 'decimal:2',
        'average_rating' => 'decimal:2',
    ];

    // Relationships
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function category()
    {
        return $this->belongsTo(CourseCategory::class);
    }

    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function allLessons()
    {
        return $this->hasManyThrough(Lesson::class, Section::class)
            ->select('lessons.*')
            ->orderBy('sections.order')
            ->orderBy('lessons.order');
    }

    public function assessments()
    {
        return $this->hasManyThrough(Assessment::class, Section::class)
            ->select('assessments.*')
            ->orderBy('sections.order')
            ->orderBy('assessments.order');
    }

    public function reviews()
    {
        return $this->hasMany(CourseReview::class);
    }
    public function sections()
    {
        return $this->hasMany(Section::class, 'course_id');
    }
    public function rejections()
    {
        return $this->hasMany(CourseRejection::class);
    }

    // Boot method
    
    protected static function boot()
    {
        parent::boot();
    
        static::creating(function ($course) {
            if (empty($course->slug)) {
                $course->slug = $course->generateUniqueSlug($course->title);
            }
        });
    
        static::updating(function ($course) {
            if ($course->isDirty('title')) {
                $course->slug = $course->generateUniqueSlug($course->title);
            }
        });
    
        // Only update relationship counts for existing courses with relationships
        static::saved(function ($course) {
            // Skip if this is a new course being created
            if (!$course->wasRecentlyCreated) {
                try {
                    // Fix the ambiguous 'type' column by specifying the table
                    $course->total_modules = $course->sections()->count();
                    $course->total_lessons = $course->allLessons()->count();
                    $course->total_projects = $course->assessments()->where('assessments.type', 'project')->count(); // Fixed here
                    $course->total_assessments = $course->assessments()->count();
                    $course->has_projects = $course->total_projects > 0;
                    $course->has_assessments = $course->total_assessments > 0;
    
                    // Use updateQuietly to avoid triggering events again
                    $course->updateQuietly([
                        'total_modules' => $course->total_modules,
                        'total_lessons' => $course->total_lessons,
                        'total_projects' => $course->total_projects,
                        'total_assessments' => $course->total_assessments,
                        'has_projects' => $course->has_projects,
                        'has_assessments' => $course->has_assessments,
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Error updating course statistics', [
                        'course_id' => $course->id,
                        'error' => $e->getMessage()
                    ]);
                    // Don't rethrow the exception to prevent breaking the save operation
                }
            }
        });
    }
    public function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->where('id', '!=', $this->id ?? 0)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        return $slug;
    }

    // Media helper methods (same as lessons)
    public function getImagesArray()
    {
        return is_string($this->images) ? json_decode($this->images, true) : ($this->images ?? []);
    }

    public function getDocumentsArray()
    {
        return is_string($this->documents) ? json_decode($this->documents, true) : ($this->documents ?? []);
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
        return !is_null($this->published_at) && $this->is_published;
    }

    public function isApproved()
    {
        return $this->is_approved;
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->where(function ($q) {
                $q->whereNotNull('published_at')
                    ->orWhere(function ($subq) {
                        $subq->whereNotNull('scheduled_publish_at')
                            ->where('scheduled_publish_at', '<=', now());
                    });
            });
    }

    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }

    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }

    // Accessors
    public function getFormattedLearningOutcomesAttribute()
    {
        return collect($this->learning_outcomes)->map(fn($outcome) => "- $outcome")->join("\n");
    }

    public function getTotalStorageAttribute()
    {
        return $this->allLessons()->sum('size_mb');
    }

    // public function getFormattedDurationAttribute()
    // {
    //     if (!$this->estimated_duration_minutes) {
    //         return 'Not specified';
    //     }

    //     $hours = floor($this->estimated_duration_minutes / 60);
    //     $minutes = $this->estimated_duration_minutes % 60;

    //     if ($hours > 0) {
    //         return $hours . 'h ' . $minutes . 'm';
    //     }

    //     return $minutes . ' minutes';
    // }

    public function getFormattedPriceAttribute()
    {
        if ($this->is_free) {
            return 'Free';
        }

        return '$' . number_format($this->price, 2);
    }
    public function getFormattedDurationAttribute()
{
    if (!$this->estimated_duration_minutes) {
        return 'Self-paced';
    }

    $hours = floor($this->estimated_duration_minutes / 60);
    $minutes = $this->estimated_duration_minutes % 60;

    if ($hours > 0) {
        return $hours . 'h ' . $minutes . 'm';
    }

    return $minutes . 'm';
}
}

