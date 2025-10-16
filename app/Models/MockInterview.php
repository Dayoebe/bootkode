<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MockInterview extends Model
{
    use HasFactory, SoftDeletes;

// Interview types
const TYPE_TECHNICAL = 'technical';
const TYPE_BEHAVIORAL = 'behavioral';
const TYPE_CASE_STUDY = 'case_study';
const TYPE_SYSTEM_DESIGN = 'system_design';
const TYPE_CODING = 'coding';
const TYPE_HR = 'hr';
const TYPE_CUSTOM = 'custom';

// Interview formats
const FORMAT_TEXT = 'text';
const FORMAT_VOICE = 'voice';
const FORMAT_VIDEO = 'video';
const FORMAT_MIXED = 'mixed';

// Interview statuses
const STATUS_SCHEDULED = 'scheduled';
const STATUS_IN_PROGRESS = 'in_progress';
const STATUS_COMPLETED = 'completed';
const STATUS_CANCELLED = 'cancelled';
const STATUS_MISSED = 'missed';

// Difficulty levels
const DIFFICULTY_BEGINNER = 'beginner';
const DIFFICULTY_INTERMEDIATE = 'intermediate';
const DIFFICULTY_ADVANCED = 'advanced';
const DIFFICULTY_EXPERT = 'expert';

protected $fillable = [
    'user_id',
    'course_id',
    'interviewer_id',
    'question_set_id',
    'original_interview_id',
    'title',
    'slug',
    'description',
    'type',
    'format',
    'status',
    'difficulty_level',
    'industry',
    'job_role',
    'company_type',
    'estimated_duration_minutes',
    'scheduled_at',
    'started_at',
    'completed_at',
    'cancelled_at',
    'cancellation_reason',
    'questions',
    'custom_questions',
    'question_order',
    'time_per_question',
    'allow_retakes',
    'max_retakes',
    'retake_count',
    'auto_submit_timeout',
    'is_premium',
    'premium_features',
    'ai_feedback_enabled',
    'video_recording_enabled',
    'detailed_analytics_enabled',
    'custom_branding',
    'user_responses',
    'response_times',
    'audio_recordings',
    'video_recordings',
    'screen_recordings',
    'overall_score',
    'technical_score',
    'communication_score',
    'confidence_score',
    'problem_solving_score',
    'cultural_fit_score',
    'ai_feedback',
    'interviewer_feedback',
    'improvement_suggestions',
    'strengths',
    'weaknesses',
    'completion_rate',
    'avg_response_time',
    'pause_count',
    'revision_count',
    'confidence_metrics',
    'speech_analysis',
    'emotion_analysis',
    'eye_contact_score',
    'body_language_score',
    'metadata',
    'settings',
    'tags',
    'is_practice',
    'is_featured',
    'is_public',
    'views_count',
    'attempts_count',
    'success_rate'
];

protected $casts = [
    'scheduled_at' => 'datetime',
    'started_at' => 'datetime',
    'completed_at' => 'datetime',
    'cancelled_at' => 'datetime',
    'questions' => 'array',
    'custom_questions' => 'array',
    'question_order' => 'array',
    'user_responses' => 'array',
    'response_times' => 'array',
    'audio_recordings' => 'array',
    'video_recordings' => 'array',
    'screen_recordings' => 'array',
    'ai_feedback' => 'array',
    'interviewer_feedback' => 'array',
    'improvement_suggestions' => 'array',
    'strengths' => 'array',
    'weaknesses' => 'array',
    'confidence_metrics' => 'array',
    'speech_analysis' => 'array',
    'emotion_analysis' => 'array',
    'metadata' => 'array',
    'settings' => 'array',
    'tags' => 'array',
    'premium_features' => 'array',
    'is_premium' => 'boolean',
    'ai_feedback_enabled' => 'boolean',
    'video_recording_enabled' => 'boolean',
    'detailed_analytics_enabled' => 'boolean',
    'allow_retakes' => 'boolean',
    'is_practice' => 'boolean',
    'is_featured' => 'boolean',
    'is_public' => 'boolean',
    'overall_score' => 'decimal:2',
    'technical_score' => 'decimal:2',
    'communication_score' => 'decimal:2',
    'confidence_score' => 'decimal:2',
    'problem_solving_score' => 'decimal:2',
    'cultural_fit_score' => 'decimal:2',
    'completion_rate' => 'decimal:2',
    'avg_response_time' => 'decimal:2',
    'eye_contact_score' => 'decimal:2',
    'body_language_score' => 'decimal:2',
    'success_rate' => 'decimal:2'
];

protected static function boot()
{
    parent::boot();

    static::creating(function ($interview) {
        if (empty($interview->slug)) {
            $interview->slug = $interview->generateUniqueSlug($interview->title);
        }
    });

    static::updating(function ($interview) {
        if ($interview->isDirty('title')) {
            $interview->slug = $interview->generateUniqueSlug($interview->title);
        }
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

public function interviewer()
{
    return $this->belongsTo(User::class, 'interviewer_id');
}

public function questionSet()
{
    return $this->belongsTo(InterviewQuestionSet::class, 'question_set_id');
}

public function retakes()
{
    return $this->hasMany(MockInterview::class, 'original_interview_id');
}

public function originalInterview()
{
    return $this->belongsTo(MockInterview::class, 'original_interview_id');
}

// Slug generation
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

// Status methods
public function isScheduled()
{
    return $this->status === self::STATUS_SCHEDULED;
}

public function isInProgress()
{
    return $this->status === self::STATUS_IN_PROGRESS;
}

public function isCompleted()
{
    return $this->status === self::STATUS_COMPLETED;
}

public function isCancelled()
{
    return $this->status === self::STATUS_CANCELLED;
}

public function isMissed()
{
    return $this->status === self::STATUS_MISSED;
}

public function start()
{
    $this->update([
        'status' => self::STATUS_IN_PROGRESS,
        'started_at' => now()
    ]);
    return $this;
}

public function complete($responses = null, $scores = null)
{
    $updateData = [
        'status' => self::STATUS_COMPLETED,
        'completed_at' => now(),
    ];

    if ($responses) {
        $updateData['user_responses'] = $responses;
    }

    if ($scores) {
        $updateData = array_merge($updateData, $scores);
    }

    $this->update($updateData);
    return $this;
}

public function cancel($reason = null)
{
    $this->update([
        'status' => self::STATUS_CANCELLED,
        'cancelled_at' => now(),
        'cancellation_reason' => $reason
    ]);

    return $this;
}

public function markAsMissed()
{
    $this->update(['status' => self::STATUS_MISSED]);
    return $this;
}

// Accessors (using proper Laravel accessor methods)
public function getTypeLabelAttribute()
{
    return match ($this->type) {
        self::TYPE_TECHNICAL => 'Technical Interview',
        self::TYPE_BEHAVIORAL => 'Behavioral Interview',
        self::TYPE_CASE_STUDY => 'Case Study',
        self::TYPE_SYSTEM_DESIGN => 'System Design',
        self::TYPE_CODING => 'Coding Interview',
        self::TYPE_HR => 'HR Interview',
        self::TYPE_CUSTOM => 'Custom Interview',
        default => 'Interview'
    };
}

public function getFormatLabelAttribute()
{
    return match ($this->format) {
        self::FORMAT_TEXT => 'Text-based',
        self::FORMAT_VOICE => 'Voice Interview',
        self::FORMAT_VIDEO => 'Video Interview',
        self::FORMAT_MIXED => 'Mixed Format',
        default => 'Standard'
    };
}

public function getStatusLabelAttribute()
{
    return match ($this->status) {
        self::STATUS_SCHEDULED => 'Scheduled',
        self::STATUS_IN_PROGRESS => 'In Progress',
        self::STATUS_COMPLETED => 'Completed',
        self::STATUS_CANCELLED => 'Cancelled',
        self::STATUS_MISSED => 'Missed',
        default => 'Unknown'
    };
}

public function getDifficultyLabelAttribute()
{
    return match ($this->difficulty_level) {
        self::DIFFICULTY_BEGINNER => 'Beginner',
        self::DIFFICULTY_INTERMEDIATE => 'Intermediate',
        self::DIFFICULTY_ADVANCED => 'Advanced',
        self::DIFFICULTY_EXPERT => 'Expert',
        default => 'Not specified'
    };
}

public function getDurationFormattedAttribute()
{
    if (!$this->estimated_duration_minutes) {
        return 'Variable duration';
    }

    $hours = floor($this->estimated_duration_minutes / 60);
    $minutes = $this->estimated_duration_minutes % 60;

    if ($hours > 0) {
        return $hours . 'h ' . $minutes . 'm';
    }

    return $minutes . ' minutes';
}

public function getCompletionPercentageAttribute()
{
    if (!$this->questions || empty($this->questions)) {
        return 0;
    }

    $totalQuestions = count($this->questions);
    $answeredQuestions = count($this->user_responses ?? []);

    return round(($answeredQuestions / $totalQuestions) * 100, 1);
}

public function getOverallRatingAttribute()
{
    if (!$this->overall_score) {
        return 'Not rated';
    }

    if ($this->overall_score >= 90) return 'Excellent';
    if ($this->overall_score >= 80) return 'Very Good';
    if ($this->overall_score >= 70) return 'Good';
    if ($this->overall_score >= 60) return 'Average';
    return 'Needs Improvement';
}

// Helper methods
public function getStatusColor()
{
    return match ($this->status) {
        'scheduled' => 'blue',
        'in_progress' => 'yellow',
        'completed' => 'green',
        'cancelled' => 'gray',
        'missed' => 'red',
        default => 'gray'
    };
}

public function getDifficultyColor()
{
    return match ($this->difficulty_level) {
        'beginner' => 'green',
        'intermediate' => 'blue',
        'advanced' => 'orange',
        'expert' => 'red',
        default => 'gray'
    };
}

public function getTypeIcon()
{
    return match ($this->type) {
        'technical' => '💻',
        'behavioral' => '🗣️',
        'case_study' => '📊',
        'system_design' => '🏗️',
        'coding' => '👨‍💻',
        'hr' => '👔',
        'custom' => '🎯',
        default => '📝'
    };
}

// Scopes
public function scopeForUser($query, $userId)
{
    return $query->where('user_id', $userId);
}

public function scopeByType($query, $type)
{
    return $query->where('type', $type);
}

public function scopeByStatus($query, $status)
{
    return $query->where('status', $status);
}

public function scopeCompleted($query)
{
    return $query->where('status', self::STATUS_COMPLETED);
}

public function scopeUpcoming($query)
{
    return $query->where('status', self::STATUS_SCHEDULED)
        ->where('scheduled_at', '>', now());
}

public function scopePremium($query)
{
    return $query->where('is_premium', true);
}

public function scopeFeatured($query)
{
    return $query->where('is_featured', true);
}

public function scopePublic($query)
{
    return $query->where('is_public', true);
}
}