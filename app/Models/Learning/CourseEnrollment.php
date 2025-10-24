<?php

namespace App\Models\Learning;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Core\User; 

class CourseEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id', 'user_id', 'enrolled_at', 'progress_percentage', 'is_completed', 'completed_at'
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
        'is_completed' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class); // UPDATED
    }
}