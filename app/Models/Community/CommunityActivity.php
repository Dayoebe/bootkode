<?php 

// app/Models/Community/CommunityActivity.php
namespace App\Models\Community;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;

class CommunityActivity extends Model
{
    use HasFactory;

    protected $table = 'community_activities';

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

    public function scopeStudyGroups(Builder $query)
    {
        return $query->where('type', 'study_group');
    }

    public function scopeCodeChallenges(Builder $query)
    {
        return $query->where('type', 'code_challenge');
    }

    public function scopeLiveEvents(Builder $query)
    {
        return $query->where('type', 'live_event');
    }

    public function scopeActive(Builder $query)
    {
        return $query->where('status', 'active');
    }

    public function scopeUpcoming(Builder $query)
    {
        return $query->where('start_date', '>', now());
    }

    public function canJoin($userId = null)
    {
        $userId = $userId ?? auth()->id();
        
        if ($this->status !== 'active') return false;
        if ($this->max_participants && $this->participants_count >= $this->max_participants) return false;
        if ($this->participants()->where('user_id', $userId)->exists()) return false;
        
        return true;
    }

    public function join($userId = null)
    {
        $userId = $userId ?? auth()->id();
        
        if (!$this->canJoin($userId)) {
            return false;
        }

        ActivityParticipant::create([
            'activity_id' => $this->id,
            'user_id' => $userId,
            'status' => 'joined',
            'joined_at' => now(),
        ]);

        $this->increment('participants_count');
        return true;
    }

    public function leave($userId = null)
    {
        $userId = $userId ?? auth()->id();
        
        $participant = $this->participants()->where('user_id', $userId)->first();
        
        if ($participant) {
            $participant->delete();
            $this->decrement('participants_count');
            return true;
        }
        
        return false;
    }

    public function isExpired()
    {
        return $this->end_date && $this->end_date->isPast();
    }
}
