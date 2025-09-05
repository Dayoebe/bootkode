<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// Live Event Models
class LiveEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'host_id', 'title', 'description', 'event_type', 'scheduled_at',
        'duration_minutes', 'meeting_link', 'meeting_password', 
        'max_attendees', 'status', 'resources'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'resources' => 'array',
    ];

    public function host()
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    public function attendees()
    {
        return $this->belongsToMany(User::class, 'event_attendees')
            ->withPivot('registered_at', 'attended_at')
            ->withTimestamps();
    }

    public function getAttendeesCountAttribute()
    {
        return $this->attendees()->count();
    }

    public function isFull()
    {
        return $this->max_attendees && $this->attendees_count >= $this->max_attendees;
    }

    public function isRegistered($userId)
    {
        return $this->attendees()->where('user_id', $userId)->exists();
    }
}
