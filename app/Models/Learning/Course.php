<?php

namespace App\Models\Learning;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\HasRevenueSplit;
use App\Models\Core\User; // UPDATED
use App\Models\Learning\CourseReview; // UPDATED

class Course extends Model
{
    use HasFactory, HasRevenueSplit; // Use the trait

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
        'is_paid',
        'currency',
        'materials_included',
        'tags'
    ];

    protected $casts = [
        'is_premium' => 'boolean',
        'is_free' => 'boolean',
        'is_paid' => 'boolean',
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
        'materials_included' => 'array',
        'tags' => 'array',
    ];

    // Relationships
// UPDATED RELATIONSHIPS
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id'); // UPDATED
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
        return $this->hasManyThrough(\App\Models\Assessment\Assessment::class, Section::class) // UPDATED
            ->select('assessments.*')
            ->orderBy('sections.order')
            ->orderBy('assessments.order');
    }

    public function directAssessments()
    {
        return $this->hasMany(\App\Models\Assessment\Assessment::class, 'course_id'); // UPDATED
    }

    public function reviews()
    {
        return $this->hasMany(CourseReview::class); // UPDATED
    }

    public function sections()
    {
        return $this->hasMany(Section::class, 'course_id');
    }

    public function rejections()
    {
        return $this->hasMany(\App\Models\Admin\CourseRejection::class); // UPDATED
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
            // Only auto-update slug if title changed and slug wasn't manually set
            if ($course->isDirty('title') && !$course->isDirty('slug')) {
                $course->slug = $course->generateUniqueSlug($course->title);
            }
        });

        static::saved(function ($course) {
            if (!$course->wasRecentlyCreated) {
                try {
                    $sectionsCount = $course->sections()->count();
                    $lessonsCount = $sectionsCount > 0 ? $course->allLessons()->count() : 0;

                    $totalAssessments = $course->directAssessments()->count();
                    if ($totalAssessments === 0 && $sectionsCount > 0) {
                        $totalAssessments = $course->assessments()->count();
                    }

                    $projectsCount = $course->directAssessments()->where('type', 'project')->count();
                    if ($projectsCount === 0 && $sectionsCount > 0) {
                        $projectsCount = $course->assessments()->where('assessments.type', 'project')->count();
                    }

                    $course->updateQuietly([
                        'total_modules' => $sectionsCount,
                        'total_lessons' => $lessonsCount,
                        'total_projects' => $projectsCount,
                        'total_assessments' => $totalAssessments,
                        'has_projects' => $projectsCount > 0,
                        'has_assessments' => $totalAssessments > 0,
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Error updating course statistics', [
                        'course_id' => $course->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        });
    }

    public function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $count = 1;

        // Ensure we exclude the current course when checking for uniqueness
        $query = static::where('slug', $slug);
        if ($this->exists) {
            $query->where('id', '!=', $this->id);
        }

        while ($query->exists()) {
            $slug = $originalSlug . '-' . $count++;
            $query = static::where('slug', $slug);
            if ($this->exists) {
                $query->where('id', '!=', $this->id);
            }
        }

        return $slug;
    }

    // Media helper methods
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

    public function scopePaid($query)
    {
        return $query->where('is_paid', true);
    }
    public function updateAverageRating()
    {
        $avgRating = $this->reviews()->where('is_approved', true)->avg('rating');
        $this->update(['average_rating' => round($avgRating ?? 0, 2)]);
    }

    public function getReviewsCount()
    {
        return $this->reviews()->where('is_approved', true)->count();
    }

    public function hasReviewBy($userId)
    {
        return $this->reviews()->where('user_id', $userId)->exists();
    }
    public function getRatingDistribution()
    {
        return $this->reviews()
            ->where('is_approved', true)
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->pluck('count', 'rating')
            ->toArray();
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

    public function getFormattedPriceAttribute()
    {
        if ($this->is_free) {
            return 'Free';
        }

        return '₦' . number_format($this->price, 2); // Changed to Naira symbol
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
    /**
     * Get instructor response rate
     */
    public function getInstructorResponseRate()
    {
        $totalReviews = $this->reviews()->approved()->count();
        if ($totalReviews === 0) {
            return 0;
        }

        $repliedReviews = $this->reviews()->approved()->whereNotNull('instructor_reply')->count();
        return round(($repliedReviews / $totalReviews) * 100, 2);
    }

    /**
     * Get average response time in hours
     */
    public function getAverageResponseTime()
    {
        $repliedReviews = $this->reviews()
            ->approved()
            ->whereNotNull('instructor_reply')
            ->whereNotNull('replied_at')
            ->get();

        if ($repliedReviews->isEmpty()) {
            return null;
        }

        $totalHours = $repliedReviews->sum(function ($review) {
            return $review->replied_at->diffInHours($review->created_at);
        });

        return round($totalHours / $repliedReviews->count(), 1);
    }

    /**
     * Scope for verified reviews (from students who completed course)
     */
    public function scopeVerified($query)
    {
        return $query->whereHas('user.enrollments', function ($q) use ($query) {
            $q->where('course_id', $query->getModel()->course_id)
                ->where('is_completed', true);
        });
    }
    /**
 * Calculate instructor share based on revenue split
 */
public function calculateInstructorShare(float $amount): float
{
    // Check if course has custom revenue split
    if (method_exists($this, 'getInstructorShare')) {
        return $this->getInstructorShare($amount);
    }

    // Default to 70% instructor share
    return $amount * 0.70;
}
}