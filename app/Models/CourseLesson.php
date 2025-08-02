<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CourseSection;

class CourseLesson extends Model
{
    protected $fillable = ['course_section_id', 'title', 'slug', 'content', 'video_url', 'order'];

    public function section()
    {
        return $this->belongsTo(CourseSection::class);
    }
}


