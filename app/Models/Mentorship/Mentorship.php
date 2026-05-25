<?php

namespace App\Models\Mentorship;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\Core\User;

class Mentorship extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_PENDING = 'pending';
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'mentor_id',
        'mentee_id',
        'status',
        'request_message',
        'rejection_reason',
        'goals',
        'expectations',
        'duration_weeks',
        'hourly_rate',
        'is_paid',
        'started_at',
        'completed_at',
        'requested_at',
        'accepted_at',
        'rejected_at',
        'metadata'
    ];

    protected $casts = [
        'goals' => 'array',
        'expectations' => 'array',
        'metadata' => 'array',
        'is_paid' => 'boolean',
        'hourly_rate' => 'decimal:2',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'requested_at' => 'datetime',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime'
    ];

    protected $appends = [
        'status_label',
        'status_color',
        'progress_percentage',
        'goal_completion_percentage',
        'goals_with_progress',
        'duration_formatted',
        'is_active',
        'next_session'
    ];

    // Relationships
    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function mentee()
    {
        return $this->belongsTo(User::class, 'mentee_id');
    }

    public function sessions()
    {
        return $this->hasMany(MentorshipSession::class);
    }

    public function codeReviews()
    {
        return $this->hasMany(CodeReview::class);
    }

    public function reviews()
    {
        return $this->hasMany(MentorshipReview::class);
    }

    // Status methods
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function accept()
    {
        $this->update([
            'status' => self::STATUS_ACTIVE,
            'accepted_at' => now(),
            'started_at' => now()
        ]);

        // Update mentor's current mentees count
        $this->mentor->mentorProfile?->increment('current_mentees');

        return $this;
    }

    public function reject($reason = null)
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'rejected_at' => now(),
            'rejection_reason' => $reason
        ]);

        return $this;
    }

    public function complete()
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'completed_at' => now()
        ]);

        // Update mentor stats
        $mentorProfile = $this->mentor->mentorProfile;
        if ($mentorProfile) {
            $mentorProfile->decrement('current_mentees');
            $mentorProfile->increment('total_mentees');
        }

        return $this;
    }

    public function markGoal(int $index, bool $completed = true): self
    {
        $goals = collect($this->goals ?? [])->values();

        if (! $goals->has($index)) {
            return $this;
        }

        $metadata = $this->metadata ?? [];
        $goalProgress = data_get($metadata, 'goal_progress', []);
        $goalProgress[$index] = [
            'completed' => $completed,
            'updated_at' => now()->toIso8601String(),
        ];

        data_set($metadata, 'goal_progress', $goalProgress);

        $this->forceFill(['metadata' => $metadata])->save();

        return $this->refresh();
    }

    // Accessors
    public function statusLabelAttribute(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->status) {
                self::STATUS_PENDING => 'Pending Approval',
                self::STATUS_ACTIVE => 'Active Mentorship',
                self::STATUS_COMPLETED => 'Completed',
                self::STATUS_CANCELLED => 'Cancelled',
                self::STATUS_REJECTED => 'Rejected',
                default => 'Unknown'
            }
        );
    }

    public function statusColorAttribute(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->status) {
                self::STATUS_PENDING => 'yellow',
                self::STATUS_ACTIVE => 'green',
                self::STATUS_COMPLETED => 'blue',
                self::STATUS_CANCELLED => 'gray',
                self::STATUS_REJECTED => 'red',
                default => 'gray'
            }
        );
    }

    public function progressPercentageAttribute(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->isCompleted()) {
                    return 100;
                }

                if (!$this->isActive()) {
                    return 0;
                }

                $timeProgress = 0;
                if ($this->duration_weeks && $this->started_at) {
                    $startDate = $this->started_at;
                    $endDate = $startDate->copy()->addWeeks($this->duration_weeks);
                    $now = now();

                    if ($now >= $endDate) {
                        $timeProgress = 100;
                    } else {
                        $totalDays = max(1, $startDate->diffInDays($endDate));
                        $passedDays = max(0, $startDate->diffInDays($now));
                        $timeProgress = min(100, round(($passedDays / $totalDays) * 100));
                    }
                }

                $targetSessions = max(1, (int) ($this->duration_weeks ?: 4));
                $completedSessions = $this->sessions()
                    ->where('status', MentorshipSession::STATUS_COMPLETED)
                    ->count();
                $sessionProgress = min(100, round(($completedSessions / $targetSessions) * 100));

                return (int) round(($timeProgress * 0.25) + ($sessionProgress * 0.45) + ($this->goal_completion_percentage * 0.30));
            }
        );
    }

    public function goalCompletionPercentageAttribute(): Attribute
    {
        return Attribute::make(
            get: function () {
                $goals = collect($this->goals ?? [])->filter()->values();

                if ($goals->isEmpty()) {
                    return 0;
                }

                $goalProgress = data_get($this->metadata ?? [], 'goal_progress', []);
                $completed = $goals
                    ->keys()
                    ->filter(fn ($index) => (bool) data_get($goalProgress, "{$index}.completed", false))
                    ->count();

                return (int) round(($completed / $goals->count()) * 100);
            }
        );
    }

    public function goalsWithProgressAttribute(): Attribute
    {
        return Attribute::make(
            get: function () {
                $goalProgress = data_get($this->metadata ?? [], 'goal_progress', []);

                return collect($this->goals ?? [])
                    ->values()
                    ->map(fn ($goal, $index) => [
                        'index' => $index,
                        'text' => $goal,
                        'completed' => (bool) data_get($goalProgress, "{$index}.completed", false),
                        'updated_at' => data_get($goalProgress, "{$index}.updated_at"),
                    ])
                    ->all();
            }
        );
    }

    public function durationFormattedAttribute(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->duration_weeks) {
                    return 'Ongoing';
                }

                return $this->duration_weeks . ' week' . ($this->duration_weeks > 1 ? 's' : '');
            }
        );
    }

    public function isActiveAttribute(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->status === self::STATUS_ACTIVE
        );
    }

    public function nextSessionAttribute(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->sessions()
                ->where('status', 'scheduled')
                ->where('scheduled_at', '>', now())
                ->orderBy('scheduled_at')
                ->first()
        );
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->where('mentor_id', $userId)->orWhere('mentee_id', $userId);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }
}
