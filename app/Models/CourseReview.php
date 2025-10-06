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

    protected static function boot()
    {
        parent::boot();

        static::created(function ($review) {
            $review->course->updateAverageRating();
            
            // Notify instructor
            $review->course->instructor->notify(
                new \App\Notifications\CoursereviewNotification($review)
            );
        });

        static::updated(function ($review) {
            $review->course->updateAverageRating();
        });

        static::deleted(function ($review) {
            $review->course->updateAverageRating();
        });
    }
}