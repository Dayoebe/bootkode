<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CommunityActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'creator_id', 'type', 'title', 'description', 'tags', 'max_participants',
        'start_date', 'end_date', 'location', 'requirements', 'metadata', 
        'status', 'participants_count'
    ];

    protected $casts = [
        'tags' => 'array',
        'metadata' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function participants()
    {
        return $this->hasMany(ActivityParticipant::class, 'activity_id');
    }

    public function activeParticipants()
    {
        return $this->hasMany(ActivityParticipant::class, 'activity_id')
            ->whereIn('status', ['joined', 'completed']);
    }

    // Activity Type Scopes
    public function scopeStudyGroups($query)
    {
        return $query->where('type', 'study_group');
    }

    public function scopeCodeChallenges($query)
    {
        return $query->where('type', 'code_challenge');
    }

    public function scopeLiveEvents($query)
    {
        return $query->where('type', 'live_event');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }

    public function scopeOngoing($query)
    {
        return $query->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    // Helper Methods
    public function isStudyGroup()
    {
        return $this->type === 'study_group';
    }

    public function isCodeChallenge()
    {
        return $this->type === 'code_challenge';
    }

    public function isLiveEvent()
    {
        return $this->type === 'live_event';
    }

    public function canJoin($userId = null)
    {
        $userId = $userId ?? auth()->id();
        
        if ($this->status !== 'active') return false;
        if ($this->max_participants && $this->participants_count >= $this->max_participants) return false;
        if ($this->participants()->where('user_id', $userId)->exists()) return false;
        
        return true;
    }

    public function getUserParticipation($userId = null)
    {
        $userId = $userId ?? auth()->id();
        return $this->participants()->where('user_id', $userId)->first();
    }

    public function join($userId = null)
    {
        $userId = $userId ?? auth()->id();
        
        if (!$this->canJoin($userId)) {
            return false;
        }

        $participant = ActivityParticipant::create([
            'activity_id' => $this->id,
            'user_id' => $userId,
            'joined_at' => now(),
        ]);

        $this->increment('participants_count');
        return $participant;
    }

    public function leave($userId = null)
    {
        $userId = $userId ?? auth()->id();
        
        $participant = $this->participants()->where('user_id', $userId)->first();
        
        if ($participant) {
            $participant->update(['status' => 'left']);
            $this->decrement('participants_count');
            return true;
        }
        
        return false;
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'active' => 'green',
            'completed' => 'blue',
            'cancelled' => 'red',
            default => 'gray'
        };
    }

    public function getTypeIconAttribute()
    {
        return match($this->type) {
            'study_group' => 'fas fa-user-friends',
            'code_challenge' => 'fas fa-trophy',
            'live_event' => 'fas fa-video',
            default => 'fas fa-circle'
        };
    }

    public function getFormattedDateAttribute()
    {
        if ($this->start_date && $this->end_date) {
            return $this->start_date->format('M j, Y') . ' - ' . $this->end_date->format('M j, Y');
        } elseif ($this->start_date) {
            return $this->start_date->format('M j, Y \a\t g:i A');
        }
        return 'No date set';
    }

    public function isExpired()
    {
        return $this->end_date && $this->end_date->isPast();
    }

    public function isUpcoming()
    {
        return $this->start_date && $this->start_date->isFuture();
    }

    public function isOngoing()
    {
        return $this->start_date && $this->start_date->isPast() 
            && (!$this->end_date || $this->end_date->isFuture());
    }
}

// ActivityParticipant Model (part of same file to reduce models)
class ActivityParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_id', 'user_id', 'status', 'submission_data', 'score', 
        'joined_at', 'completed_at'
    ];

    protected $casts = [
        'submission_data' => 'array',
        'joined_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function activity()
    {
        return $this->belongsTo(CommunityActivity::class, 'activity_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function complete($score = null, $submissionData = null)
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'score' => $score,
            'submission_data' => $submissionData,
        ]);
    }

    public function host()
{
    return $this->belongsTo(User::class, 'creator_id');
}
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['joined', 'completed']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeWithHost($query)
{
    return $query->with(['host' => function($q) {
        $q->withTrashed(); // Include soft-deleted users if any
    }]);
}

// Ensure these scopes exist
public function scopeStudyGroups($query)
{
    return $query->where('type', 'study_group');
}

public function scopeCodeChallenges($query)
{
    return $query->where('type', 'code_challenge');
}

public function scopeLiveEvents($query)
{
    return $query->where('type', 'live_event');
}
}