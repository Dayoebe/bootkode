<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'has_offline_content',
        'is_published',
        'is_approved',
    ];

    protected $casts = [
        'is_premium' => 'boolean',
        'is_published' => 'boolean',
        'is_approved' => 'boolean',
        'has_offline_content' => 'boolean',
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

// In app/Models/Course.php
public function enrollments()
{
    return $this->belongsToMany(User::class, 'course_user')->withTimestamps();
}
public function sections()
{
    return $this->hasMany(CourseSection::class)->orderBy('order');
}

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function wishlistedBy()
    {
        return $this->belongsToMany(User::class, 'wishlists');
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
    protected $appends = ['offline_size_mb', 'offline_content_types'];

public function getOfflineSizeMbAttribute()
{
    // Calculate total size of offline content (lessons, pdfs, etc.)
    return $this->lessons()->sum('size_mb') + $this->pdfResources()->sum('size_mb');
}

public function getOfflineContentTypesAttribute()
{
    $types = [];
    
    if ($this->lessons()->exists()) $types[] = 'lesson';
    if ($this->pdfResources()->exists()) $types[] = 'pdf';
    if ($this->audioResources()->exists()) $types[] = 'audio';
    
    return $types;
}

public function certificates()
{
    return $this->hasMany(Certificate::class);
}
public function feedbacks()
    {
        return $this->hasMany(Feedback::class);
    }

    public function getAverageRatingAttribute()
    {
        return $this->feedbacks()->avg('rating') ?: 0;
    }

    public function getRatingCountAttribute()
    {
        return $this->feedbacks()->count();
    }
}