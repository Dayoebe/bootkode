<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonProgress extends Model
{
    use HasFactory;

    protected $table = 'lesson_progress';

    protected $fillable = [
        'user_id',
        'lesson_id',
        'time_spent_seconds',
        'last_accessed_at',
    ];

    protected $casts = [
        'last_accessed_at' => 'datetime',
        'time_spent_seconds' => 'integer',
    ];

    /**
     * Get the user that owns the progress.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the lesson that this progress belongs to.
     */
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Get formatted time spent
     */
    public function getFormattedTimeSpentAttribute()
    {
        $minutes = floor($this->time_spent_seconds / 60);
        $seconds = $this->time_spent_seconds % 60;
        
        if ($minutes > 60) {
            $hours = floor($minutes / 60);
            $minutes = $minutes % 60;
            return "{$hours}h {$minutes}m";
        }
        
        return $minutes > 0 ? "{$minutes}m {$seconds}s" : "{$seconds}s";
    }

    /**
     * Scope to get progress for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get progress for a specific lesson
     */
    public function scopeForLesson($query, $lessonId)
    {
        return $query->where('lesson_id', $lessonId);
    }

    /**
     * Get the last accessed lesson for a user in a course
     */
    public static function getLastAccessedLesson($userId, $courseId)
    {
        return static::where('user_id', $userId)
            ->whereHas('lesson.section', function ($query) use ($courseId) {
                $query->where('course_id', $courseId);
            })
            ->orderBy('last_accessed_at', 'desc')
            ->first();
    }
}