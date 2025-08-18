<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Lesson extends Model
{
    protected $fillable = [
        'section_id', 'title', 'slug', 'description', 'content', 'content_type',
        'video_url', 'duration_minutes', 'order', 'text_content', 'size_mb'
    ];

    protected $casts = ['content' => 'array'];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($lesson) {
            $lesson->slug = Str::slug($lesson->title);
        });
        static::updating(function ($lesson) {
            if ($lesson->isDirty('title')) {
                $lesson->slug = Str::slug($lesson->title);
            }
        });
    }
}