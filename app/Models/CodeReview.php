<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CodeReview extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_PENDING = 'pending';
    const STATUS_IN_REVIEW = 'in_review';
    const STATUS_COMPLETED = 'completed';
    const STATUS_DECLINED = 'declined';

    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    protected $fillable = [
        'mentorship_id',
        'requested_by',
        'reviewed_by',
        'title',
        'description',
        'status',
        'priority',
        'technologies',
        'repository_url',
        'branch_name',
        'pull_request_url',
        'files_to_review',
        'specific_questions',
        'code_snippets',
        'review_feedback',
        'suggestions',
        'code_quality_score',
        'improvement_areas',
        'requested_at',
        'started_review_at',
        'completed_at',
        'estimated_review_time',
        'actual_review_time',
        'attachments',
        'is_urgent'
    ];

    protected $casts = [
        'technologies' => 'array',
        'files_to_review' => 'array',
        'code_snippets' => 'array',
        'suggestions' => 'array',
        'improvement_areas' => 'array',
        'attachments' => 'array',
        'is_urgent' => 'boolean',
        'code_quality_score' => 'decimal:2',
        'requested_at' => 'datetime',
        'started_review_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    public function mentorship()
    {
        return $this->belongsTo(Mentorship::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function startReview($reviewerId = null)
    {
        $this->update([
            'status' => self::STATUS_IN_REVIEW,
            'reviewed_by' => $reviewerId ?? auth()->id(),
            'started_review_at' => now()
        ]);

        return $this;
    }

    public function complete($feedback, $suggestions = [], $score = null)
    {
        $reviewTime = $this->started_review_at 
            ? $this->started_review_at->diffInMinutes(now())
            : null;

        $this->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now(),
            'review_feedback' => $feedback,
            'suggestions' => $suggestions,
            'code_quality_score' => $score,
            'actual_review_time' => $reviewTime
        ]);

        return $this;
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeUrgent($query)
    {
        return $query->where('is_urgent', true)->orWhere('priority', self::PRIORITY_URGENT);
    }
}