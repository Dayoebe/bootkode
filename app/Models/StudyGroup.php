<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// Study Group Models
class StudyGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'creator_id', 'course_id', 'name', 'description', 'max_members', 
        'meeting_schedule', 'meeting_link', 'status'
    ];

    protected $casts = [
        'meeting_schedule' => 'array',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'study_group_members')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    public function getMembersCountAttribute()
    {
        return $this->members()->count();
    }

    public function isFull()
    {
        return $this->members_count >= $this->max_members;
    }

    public function isMember($userId)
    {
        return $this->members()->where('user_id', $userId)->exists();
    }
}
