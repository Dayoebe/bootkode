<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'user_id',
        'rating',
        'comment',
        'is_approved',
        'instructor_reply',
        'replied_at'
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_approved' => 'boolean',
        'replied_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();
    
        static::created(function ($review) {
            $review->course->updateAverageRating();
            
            // Notify instructor
            if ($review->course->instructor) {
                $review->course->instructor->notify(
                    new \App\Notifications\CourseReviewNotification($review)
                );
            }
    
            // Trigger analytics generation
            dispatch(function() use ($review) {
                app(\App\Services\ReviewAnalyticsService::class)
                    ->generateDailyAnalytics($review->course);
            })->afterResponse();
        });
    
        static::updated(function ($review) {
            $review->course->updateAverageRating();
            
            // Regenerate analytics when review is updated
            dispatch(function() use ($review) {
                app(\App\Services\ReviewAnalyticsService::class)
                    ->generateDailyAnalytics($review->course);
            })->afterResponse();
        });
    
        static::deleted(function ($review) {
            $review->course->updateAverageRating();
            
            // Regenerate analytics when review is deleted
            dispatch(function() use ($review) {
                app(\App\Services\ReviewAnalyticsService::class)
                    ->generateDailyAnalytics($review->course);
            })->afterResponse();
        });
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function replies()
    {
        return $this->hasMany(ReviewReply::class);
    }

    // Accessors
    public function getReviewTextAttribute()
    {
        return $this->comment;
    }

    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Scope to get only approved reviews
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    /**
     * Check if review is from verified student (completed course)
     */
    public function isVerified(): bool
    {
        return $this->user->enrollments()
            ->where('course_id', $this->course_id)
            ->where('is_completed', true)
            ->exists();
    }

    /**
     * Check if student has meaningful progress
     */
    public function hasSubstantialProgress(): bool
    {
        $enrollment = $this->user->enrollments()
            ->where('course_id', $this->course_id)
            ->first();
            
        return $enrollment && $enrollment->progress_percentage >= 50;
    }

    /**
     * Get review quality score
     */
    public function getQualityScore(): int
    {
        $score = 0;
        
        // Length (max 40 points)
        $score += min((strlen($this->comment) / 200) * 40, 40);
        
        // Verified (30 points)
        $score += $this->isVerified() ? 30 : 0;
        
        // Has instructor reply (20 points - shows engagement)
        $score += $this->instructor_reply ? 20 : 0;
        
        // Substantial progress (10 points)
        $score += $this->hasSubstantialProgress() ? 10 : 0;
        
        return round($score);
    }

    /**
     * Check if review can be edited by user
     */
    public function getCanEditAttribute(): bool
    {
        if (!auth()->check()) {
            return false;
        }
        
        return $this->user_id === auth()->id();
    }

    /**
     * Scope for high quality reviews
     */
    public function scopeHighQuality($query)
    {
        return $query->whereRaw('LENGTH(comment) > 100')
            ->whereHas('user.enrollments', function($q) {
                $q->where('is_completed', true);
            });
    }

    /**
     * Scope for reviews with instructor replies
     */
    public function scopeWithReplies($query)
    {
        return $query->whereNotNull('instructor_reply');
    }

    /**
     * Get verified reviews count for a course
     */
    public static function getVerifiedCount($courseId): int
    {
        return static::where('course_id', $courseId)
            ->where('is_approved', true)
            ->whereHas('user.enrollments', function($q) use ($courseId) {
                $q->where('course_id', $courseId)
                  ->where('is_completed', true);
            })
            ->count();
    }

    /**
     * Get average response time for instructor
     */
    public static function getAverageResponseTime($courseId): ?float
    {
        $repliedReviews = static::where('course_id', $courseId)
            ->whereNotNull('instructor_reply')
            ->whereNotNull('replied_at')
            ->get();

        if ($repliedReviews->isEmpty()) {
            return null;
        }

        $totalHours = $repliedReviews->sum(function($review) {
            return $review->replied_at->diffInHours($review->created_at);
        });

        return round($totalHours / $repliedReviews->count(), 1);
    }
}