<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MentorshipSession extends Model
{
    use HasFactory, SoftDeletes;

    const TYPE_GENERAL = 'general';
    const TYPE_CODE_REVIEW = 'code_review';
    const TYPE_PROJECT_GUIDANCE = 'project_guidance';
    const TYPE_CAREER_ADVICE = 'career_advice';

    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_MISSED = 'missed';

    protected $fillable = [
        'mentorship_id',
        'title',
        'description',
        'type',
        'format',
        'status',
        'scheduled_at',
        'started_at',
        'ended_at',
        'duration_minutes',
        'agenda',
        'materials',
        'session_notes',
        'action_items',
        'mentor_feedback',
        'mentee_feedback',
        'mentor_rating',
        'mentee_rating',
        'meeting_link',
        'recording_url',
        'attachments',
        'is_billable',
        'session_cost',
        'payment_status',
        'metadata'
    ];

    protected $casts = [
        'materials' => 'array',
        'action_items' => 'array',
        'attachments' => 'array',
        'metadata' => 'array',
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'is_billable' => 'boolean',
        'session_cost' => 'decimal:2',
        'mentor_rating' => 'decimal:2',
        'mentee_rating' => 'decimal:2'
    ];

    public function mentorship()
    {
        return $this->belongsTo(Mentorship::class);
    }

    public function start()
    {
        $this->update([
            'status' => self::STATUS_IN_PROGRESS,
            'started_at' => now()
        ]);

        return $this;
    }

    public function complete()
    {
        $duration = $this->started_at ? $this->started_at->diffInMinutes(now()) : 0;

        $this->update([
            'status' => self::STATUS_COMPLETED,
            'ended_at' => now(),
            'duration_minutes' => $duration
        ]);

        // Update mentor stats
        $this->mentorship->mentor->mentorProfile?->increment('total_sessions');

        return $this;
    }
}