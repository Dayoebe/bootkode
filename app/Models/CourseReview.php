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
        'review_text',
        'is_approved'
    ];
    protected $casts = [
        'rating' => 'integer',
        'is_approved' => 'boolean'
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

    public function getReviewTextAttribute()
    {
        return $this->comment;
    }
}