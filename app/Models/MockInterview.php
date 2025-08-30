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

    // Premium features
    const PREMIUM_FEATURES = [
        'ai_feedback',
        'video_recording',
        'detailed_analytics',
        'custom_questions',
        'unlimited_retakes',
        'priority_scheduling',
        'industry_specific',
        'one_on_one_coaching'
    ];

    protected $fillable = [
        'user_id',
        'course_id',
        'interviewer_id',
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
        
        // Interview Configuration
        'questions',
        'custom_questions',
        'question_order',
        'time_per_question',
        'allow_retakes',
        'max_retakes',
        'retake_count',
        'auto_submit_timeout',
        
        // Premium Features
        'is_premium',
        'premium_features',
        'ai_feedback_enabled',
        'video_recording_enabled',
        'detailed_analytics_enabled',
        'custom_branding',
        
        // Responses & Results
        'user_responses',
        'response_times',
        'audio_recordings',
        'video_recordings',
        'screen_recordings',
        
        // Scoring & Feedback
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
        
        // Analytics
        'completion_rate',
        'avg_response_time',
        'pause_count',
        'revision_count',
        'confidence_metrics',
        'speech_analysis',
        'emotion_analysis',
        'eye_contact_score',
        'body_language_score',
        
        // Metadata
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

    protected $appends = [
        'type_label',
        'format_label',
        'status_label',
        'difficulty_label',
        'duration_formatted',
        'completion_percentage',
        'overall_rating',
        'is_accessible',
        'next_available_slot'
    ];

    // Boot method
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

        static::saved(function ($interview) {
            // Update related statistics
            if ($interview->status === self::STATUS_COMPLETED) {
                $interview->updateUserStats();
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

    // Premium feature methods
    public function hasPremiumFeature($feature)
    {
        if (!$this->is_premium) {
            return false;
        }

        return in_array($feature, $this->premium_features ?? []);
    }

    public function enablePremiumFeature($feature)
    {
        if (!in_array($feature, self::PREMIUM_FEATURES)) {
            return false;
        }

        $features = $this->premium_features ?? [];
        if (!in_array($feature, $features)) {
            $features[] = $feature;
            $this->premium_features = $features;
            $this->is_premium = true;
            $this->save();
        }

        return true;
    }

    public function disablePremiumFeature($feature)
    {
        $features = $this->premium_features ?? [];
        $features = array_diff($features, [$feature]);
        $this->premium_features = $features;
        
        if (empty($features)) {
            $this->is_premium = false;
        }
        
        $this->save();
        return true;
    }

    // Interview management methods
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
            'attempts_count' => $this->attempts_count + 1
        ];

        if ($responses) {
            $updateData['user_responses'] = $responses;
        }

        if ($scores) {
            $updateData = array_merge($updateData, $scores);
        }

        $this->update($updateData);
        $this->updateUserStats();

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
        $this->update([
            'status' => self::STATUS_MISSED
        ]);

        return $this;
    }

    // Question management
    public function addQuestion($question, $type = 'custom')
    {
        $questions = $this->custom_questions ?? [];
        $questions[] = [
            'id' => Str::uuid(),
            'question' => $question,
            'type' => $type,
            'created_at' => now()->toISOString()
        ];
        
        $this->custom_questions = $questions;
        $this->save();

        return $this;
    }

    public function removeQuestion($questionId)
    {
        $questions = $this->custom_questions ?? [];
        $questions = array_filter($questions, fn($q) => $q['id'] !== $questionId);
        
        $this->custom_questions = array_values($questions);
        $this->save();

        return $this;
    }

    // Response management
    public function addResponse($questionId, $response, $responseTime = null)
    {
        $responses = $this->user_responses ?? [];
        $responses[$questionId] = [
            'response' => $response,
            'timestamp' => now()->toISOString(),
            'response_time' => $responseTime
        ];

        $this->user_responses = $responses;
        $this->save();

        return $this;
    }

    // Scoring methods
    public function calculateOverallScore()
    {
        $scores = array_filter([
            $this->technical_score,
            $this->communication_score,
            $this->confidence_score,
            $this->problem_solving_score,
            $this->cultural_fit_score
        ]);

        if (empty($scores)) {
            return 0;
        }

        return array_sum($scores) / count($scores);
    }

    public function updateScores($scores)
    {
        $this->update(array_merge($scores, [
            'overall_score' => $this->calculateOverallScore()
        ]));

        return $this;
    }

    // Analytics methods
    public function updateUserStats()
    {
        // This would update user's interview statistics
        // Could be implemented as a job for better performance
        dispatch(function () {
            // Update user achievements, statistics, etc.
        });
    }

    public function getAnalytics()
    {
        return [
            'completion_rate' => $this->completion_rate,
            'avg_response_time' => $this->avg_response_time,
            'scores' => [
                'overall' => $this->overall_score,
                'technical' => $this->technical_score,
                'communication' => $this->communication_score,
                'confidence' => $this->confidence_score,
                'problem_solving' => $this->problem_solving_score,
                'cultural_fit' => $this->cultural_fit_score
            ],
            'metrics' => [
                'pause_count' => $this->pause_count,
                'revision_count' => $this->revision_count,
                'eye_contact_score' => $this->eye_contact_score,
                'body_language_score' => $this->body_language_score
            ]
        ];
    }

    // Accessors
    public function typeLabelAttribute(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->type) {
                self::TYPE_TECHNICAL => 'Technical Interview',
                self::TYPE_BEHAVIORAL => 'Behavioral Interview',
                self::TYPE_CASE_STUDY => 'Case Study',
                self::TYPE_SYSTEM_DESIGN => 'System Design',
                self::TYPE_CODING => 'Coding Interview',
                self::TYPE_HR => 'HR Interview',
                self::TYPE_CUSTOM => 'Custom Interview',
                default => 'Interview'
            }
        );
    }

    public function formatLabelAttribute(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->format) {
                self::FORMAT_TEXT => 'Text-based',
                self::FORMAT_VOICE => 'Voice Interview',
                self::FORMAT_VIDEO => 'Video Interview',
                self::FORMAT_MIXED => 'Mixed Format',
                default => 'Standard'
            }
        );
    }

    public function statusLabelAttribute(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->status) {
                self::STATUS_SCHEDULED => 'Scheduled',
                self::STATUS_IN_PROGRESS => 'In Progress',
                self::STATUS_COMPLETED => 'Completed',
                self::STATUS_CANCELLED => 'Cancelled',
                self::STATUS_MISSED => 'Missed',
                default => 'Unknown'
            }
        );
    }

    public function difficultyLabelAttribute(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->difficulty_level) {
                self::DIFFICULTY_BEGINNER => 'Beginner',
                self::DIFFICULTY_INTERMEDIATE => 'Intermediate',
                self::DIFFICULTY_ADVANCED => 'Advanced',
                self::DIFFICULTY_EXPERT => 'Expert',
                default => 'Not specified'
            }
        );
    }

    public function durationFormattedAttribute(): Attribute
    {
        return Attribute::make(
            get: function () {
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
        );
    }

    public function completionPercentageAttribute(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->questions || empty($this->questions)) {
                    return 0;
                }

                $totalQuestions = count($this->questions);
                $answeredQuestions = count($this->user_responses ?? []);

                return round(($answeredQuestions / $totalQuestions) * 100, 1);
            }
        );
    }

    public function overallRatingAttribute(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->overall_score) {
                    return 'Not rated';
                }

                if ($this->overall_score >= 90) return 'Excellent';
                if ($this->overall_score >= 80) return 'Very Good';
                if ($this->overall_score >= 70) return 'Good';
                if ($this->overall_score >= 60) return 'Average';
                return 'Needs Improvement';
            }
        );
    }

    public function isAccessibleAttribute(): Attribute
    {
        return Attribute::make(
            get: function () {
                // Check if user has access based on premium status, course enrollment, etc.
                if (!$this->is_premium) {
                    return true;
                }

                // Add your access logic here
                return auth()->user()?->hasRole(['premium', 'instructor', 'admin']) ?? false;
            }
        );
    }

    public function nextAvailableSlotAttribute(): Attribute
    {
        return Attribute::make(
            get: function () {
                // This would calculate next available interview slot
                // Simplified implementation
                return now()->addDays(1)->setHour(9)->setMinute(0);
            }
        );
    }

    // Helper methods
    public function getStatusColor()
    {
        return match ($this->status) {
            self::STATUS_SCHEDULED => 'blue',
            self::STATUS_IN_PROGRESS => 'yellow',
            self::STATUS_COMPLETED => 'green',
            self::STATUS_CANCELLED => 'gray',
            self::STATUS_MISSED => 'red',
            default => 'gray'
        };
    }

    public function getDifficultyColor()
    {
        return match ($this->difficulty_level) {
            self::DIFFICULTY_BEGINNER => 'green',
            self::DIFFICULTY_INTERMEDIATE => 'blue',
            self::DIFFICULTY_ADVANCED => 'orange',
            self::DIFFICULTY_EXPERT => 'red',
            default => 'gray'
        };
    }

    public function getTypeIcon()
    {
        return match ($this->type) {
            self::TYPE_TECHNICAL => '💻',
            self::TYPE_BEHAVIORAL => '🗣️',
            self::TYPE_CASE_STUDY => '📊',
            self::TYPE_SYSTEM_DESIGN => '🏗️',
            self::TYPE_CODING => '👨‍💻',
            self::TYPE_HR => '👔',
            self::TYPE_CUSTOM => '🎯',
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

    public function scopeForCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }
}