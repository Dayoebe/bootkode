<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectSubmission extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_id',
        'user_id',
        'course_id',
        'section_id',
        'title',
        'description',
        'files',
        'status',
        'grade',
        'feedback',
        'is_featured',
    ];

    protected $casts = [
        'files' => 'array',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function section()
    {
        return $this->belongsTo(CourseSection::class, 'section_id');
    }
}