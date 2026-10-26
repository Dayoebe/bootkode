<?php

namespace App\Models\Learning;

use App\Models\Core\User;
use Illuminate\Database\Eloquent\Model;

class CourseContentRun extends Model
{
    protected $hidden = ['stage_data'];

    protected $fillable = [
        'source_key',
        'revision',
        'source_type',
        'user_id',
        'course_id',
        'status',
        'metadata',
        'stage_data',
        'failure_reason',
    ];

    protected $casts = [
        'metadata' => 'array',
        'stage_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
