<?php

namespace App\Models\Assessment\Assessment;

use Illuminate\Database\Eloquent\Model;
use App\Models\Core\User; // UPDATED
use App\Models\Learning\Course; // UPDATED
use App\Models\Learning\Lesson;

class UserProgress extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'lesson_id',
        'assessment_id',
        'is_completed',
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

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }
}