<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'instructor_id',
        'category_id',
        'title',
        'slug',
        'description',
        'thumbnail',
        'difficulty_level',
        'estimated_duration_minutes',
        'price',
        'is_premium',
        'is_published',
        'is_approved',
    ];

    protected $casts = [
        'is_premium' => 'boolean',
        'is_published' => 'boolean',
        'is_approved' => 'boolean',
    ];

    /**
     * Get the instructor that owns the course.
     */
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    /**
     * Get the category that the course belongs to.
     */
    public function category()
    {
        return $this->belongsTo(CourseCategory::class, 'category_id');
    }

    /**
     * Get the modules for the course.
     */
    public function modules()
    {
        return $this->hasMany(Module::class)->orderBy('order');
    }

    /**
     * Get the reviews for the course.
     */
    public function reviews()
    {
        return $this->hasMany(CourseReview::class);
    }

    /**
     * Get the assignments for the course.
     */



public function sections()
{
    return $this->hasMany(CourseSection::class)->orderBy('order');
}

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    /**
     * Automatically generate slug when saving.
     */
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
    }
}