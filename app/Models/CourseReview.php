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
        'helpful_count',
        'instructor_reply',
        'replied_at'
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_approved' => 'boolean',
        'helpful_count' => 'integer',
        'replied_at' => 'datetime'
    ];
    protected static function boot()
    {
        parent::boot();
    
        static::created(function ($review) {
            $review->course->updateAverageRating();
            
            // Notify instructor
              $review->course->instructor->notify(
                new \App\Notifications\CourseReviewNotification($review)
            );
    
            // Mark review reminder as completed
            app(\App\Services\ReviewReminderService::class)
                ->markReminderCompleted($review->user, $review->course);
    
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

    // Accessor to support both field names
    public function getReviewTextAttribute()
    {
        return $this->comment;
    }
}