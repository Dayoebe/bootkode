<?php

namespace App\Models\Assessment;

use App\Models\Core\User;
use App\Models\Learning\Course;
use App\Models\Learning\Lesson; // UPDATED
use Illuminate\Database\Eloquent\Factories\HasFactory; // UPDATED
use Illuminate\Database\Eloquent\Model;

class LearningSession extends Model
{
    use HasFactory;

    protected $hidden = ['metadata'];

    protected $fillable = [
        'user_id',
        'course_id',
        'lesson_id',
        'started_at',
        'ended_at',
        'duration_minutes',
        'activity_type',
        'metadata',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    /**
     * Start a new learning session
     */
    public static function startSession($userId, $courseId = null, $lessonId = null, $activityType = 'lesson')
    {
        return static::create([
            'user_id' => $userId,
            'course_id' => $courseId,
            'lesson_id' => $lessonId,
            'started_at' => now(),
            'activity_type' => $activityType,
        ]);
    }

    /**
     * End the current session
     */
    public function endSession()
    {
        $this->update([
            'ended_at' => now(),
            'duration_minutes' => $this->started_at->diffInMinutes(now()),
        ]);

        return $this;
    }

    /**
     * Get active session for user
     */
    public static function getActiveSession($userId)
    {
        return static::where('user_id', $userId)
            ->whereNull('ended_at')
            ->latest()
            ->first();
    }

    /**
     * Get daily study time for user
     */
    public static function getDailyStudyTime($userId, $date = null)
    {
        $date = $date ?? now();

        return static::where('user_id', $userId)
            ->whereDate('started_at', $date)
            ->whereNotNull('ended_at')
            ->sum('duration_minutes');
    }

    /**
     * Get weekly study time for user
     */
    public static function getWeeklyStudyTime($userId, $startOfWeek = null)
    {
        $startOfWeek = $startOfWeek ?? now()->startOfWeek();

        return static::where('user_id', $userId)
            ->whereBetween('started_at', [$startOfWeek, $startOfWeek->copy()->endOfWeek()])
            ->whereNotNull('ended_at')
            ->sum('duration_minutes');
    }

    /**
     * Get study streak for user
     */
    public static function getStudyStreak($userId)
    {
        $currentDate = now()->startOfDay();
        $activeDates = static::where('user_id', $userId)
            ->where('started_at', '>=', $currentDate->copy()->subDays(364))
            ->whereNotNull('ended_at')
            ->where('ended_at', '<=', now())
            ->selectRaw('DATE(started_at) as activity_date')
            ->distinct()
            ->pluck('activity_date')
            ->mapWithKeys(fn ($date) => [(string) $date => true]);

        $streak = 0;
        for ($offset = 0; $offset < 365; $offset++) {
            if (! isset($activeDates[$currentDate->copy()->subDays($offset)->toDateString()])) {
                break;
            }

            $streak++;
        }

        return $streak;
    }

    /**
     * Scope for completed sessions
     */
    public function scopeCompleted($query)
    {
        return $query->whereNotNull('ended_at');
    }

    /**
     * Scope for active sessions
     */
    public function scopeActive($query)
    {
        return $query->whereNull('ended_at');
    }

    /**
     * Get formatted duration
     */
    public function getFormattedDurationAttribute()
    {
        if (! $this->duration_minutes) {
            return 'Active';
        }

        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;

        if ($hours > 0) {
            return $hours.'h '.$minutes.'m';
        }

        return $minutes.'m';
    }
}
