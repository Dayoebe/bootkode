<?php

namespace App\Models\Learning;

use Illuminate\Database\Eloquent\Model;
use App\Models\Learning\Course;

class ReviewAnalytics extends Model
{
    protected $fillable = [
        'course_id',
        'date',
        'average_rating',
        'review_count',
        'response_count',
        'response_rate',
        'sentiment_score',
        'keyword_frequencies'
    ];

    protected $casts = [
        'date' => 'date',
        'average_rating' => 'decimal:2',
        'response_rate' => 'decimal:2',
        'sentiment_score' => 'decimal:2',
        'keyword_frequencies' => 'array'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}