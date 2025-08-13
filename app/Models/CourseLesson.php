<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CourseSection;

class CourseLesson extends Model
{
    protected $fillable = ['course_section_id', 'title', 'slug', 'content', 'video_url', 'order'];

    public function section()
{
    return $this->belongsTo(CourseSection::class, 'course_section_id');
}


// <div class="flex justify-between items-center mb-6">
//     <h2 class="text-xl font-bold text-white">{{ $currentLesson->title }}</h2>
//     <livewire:component.common.bookmark-button 
//         resourceableType="App\Models\Lesson" 
//         resourceableId="{{ $currentLesson->id }}"
//         courseId="{{ $course->id }}"
//     />
// </div>
}


