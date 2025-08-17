<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'instructor_id', 'category_id', 'title', 'slug', 'description', 'thumbnail',
        'difficulty_level', 'estimated_duration_minutes', 'price', 'is_premium',
        'has_offline_content', 'is_published', 'is_approved', 'target_audience',
        'learning_outcomes', 'prerequisites', 'syllabus_overview', 'total_modules',
        'total_projects', 'total_assessments', 'faqs', 'certificate_template',
        'has_projects', 'has_assessments', 'completion_rate_threshold',
    ];

    protected $casts = [
        'is_premium' => 'boolean',
        'is_published' => 'boolean',
        'is_approved' => 'boolean',
        'has_offline_content' => 'boolean',
        'has_projects' => 'boolean',
        'has_assessments' => 'boolean',
        'learning_outcomes' => 'array',
        'prerequisites' => 'array',
        'faqs' => 'array',
    ];

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function category()
    {
        return $this->belongsTo(CourseCategory::class);
    }
    public function enrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function allLessons()
    {
        return $this->hasManyThrough(Lesson::class, Section::class, 'course_id', 'section_id', 'id', 'id')
            ->orderBy('sections.order')
            ->orderBy('lessons.order');
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }

    public function reviews()
    {
        return $this->hasMany(CourseReview::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($course) {
            $course->slug = Str::slug($course->title);
        });
        static::updating(function ($course) {
            if ($course->isDirty('title')) {
                $course->slug = Str::slug($course->title);
            }
        });
        static::saving(function ($course) {
            $course->total_modules = $course->sections()->count();
            $course->total_projects = $course->assessments()->where('type', 'project')->count();
            $course->total_assessments = $course->assessments()->count();
            $course->has_projects = $course->total_projects > 0;
            $course->has_assessments = $course->total_assessments > 0;
        });
    }

    public function getFormattedLearningOutcomesAttribute()
    {
        return collect($this->learning_outcomes)->map(fn($outcome) => "- $outcome")->join("\n");
    }

    public function getTotalStorageAttribute()
    {
        return $this->allLessons()->sum('size_mb');
    }
}