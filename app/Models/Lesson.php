<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id',
        'title',
        'description',
        'type',
        'content_url',
        'text_content',
        'duration_minutes',
        'order',
        'slug', // Make sure 'slug' is here
        'course_section_id',
        'content_type',
        'content',
    ];


    protected $casts = [
        'content' => 'array',
    ];
    
    /**
     * Get the module that the lesson belongs to.
     */
    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Get the quizzes for the lesson.
     */
    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

//     <div class="flex items-center gap-4 mb-4">
//     <h1 class="text-2xl font-bold text-white">{{ $lesson->title }}</h1>
//     <livewire:component.common.bookmark-button 
//         resourceableType="App\Models\Lesson" 
//         resourceableId="{{ $lesson->id }}"
//         courseId="{{ $lesson->course->id }}"
//         showText="true"
//     />
// </div>
}

    