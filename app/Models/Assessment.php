<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Assessment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_id', 'section_id', 'lesson_id', 'title', 'slug', 'description', 'type',
        'pass_percentage', 'estimated_duration_minutes', 'deadline', 'project_type',
        'required_skills', 'deliverables', 'resources', 'is_mandatory', 'weight',
        'allows_collaboration', 'evaluation_criteria', 'due_date', 'max_score', 'instructions', 'order'
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'due_date' => 'datetime',
        'required_skills' => 'array',
        'deliverables' => 'array',
        'resources' => 'array',
        'is_mandatory' => 'boolean',
        'allows_collaboration' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'assessment_id');
    }

    public function getIsQuizAttribute()
    {
        return $this->type === 'quiz';
    }
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($assessment) {
            $assessment->order = $assessment->order ?? $assessment->section->assessments()->count() + 1;
        });
        static::updating(function ($assessment) {
            if ($assessment->isDirty('title')) {
                $assessment->slug = Str::slug($assessment->title);
            }
        });
    }
}