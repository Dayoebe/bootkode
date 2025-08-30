<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MentorshipReview extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'mentorship_id',
        'reviewer_id',
        'reviewee_id',
        'type',
        'session_id',
        'overall_rating',
        'communication_rating',
        'expertise_rating',
        'helpfulness_rating',
        'professionalism_rating',
        'review_text',
        'pros',
        'cons',
        'would_recommend',
        'is_public',
        'tags'
    ];

    protected $casts = [
        'pros' => 'array',
        'cons' => 'array',
        'tags' => 'array',
        'would_recommend' => 'boolean',
        'is_public' => 'boolean',
        'overall_rating' => 'decimal:2',
        'communication_rating' => 'decimal:2',
        'expertise_rating' => 'decimal:2',
        'helpfulness_rating' => 'decimal:2',
        'professionalism_rating' => 'decimal:2'
    ];

    public function mentorship()
    {
        return $this->belongsTo(Mentorship::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function reviewee()
    {
        return $this->belongsTo(User::class, 'reviewee_id');
    }

    public function session()
    {
        return $this->belongsTo(MentorshipSession::class, 'session_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($review) {
            // Update mentor profile rating
            if ($review->reviewee->mentorProfile) {
                $profile = $review->reviewee->mentorProfile;
                $avgRating = MentorshipReview::where('reviewee_id', $review->reviewee_id)
                    ->avg('overall_rating');
                $totalReviews = MentorshipReview::where('reviewee_id', $review->reviewee_id)
                    ->count();

                $profile->update([
                    'rating' => round($avgRating, 2),
                    'total_reviews' => $totalReviews
                ]);
            }
        });
    }
}