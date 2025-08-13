<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id',
        'course_id',
        'title',
        'description',
        'due_date',
        'max_score',
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    /**
     * Get the module that the assignment belongs to (if applicable).
     */
    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Get the course that the assignment belongs to (if applicable).
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }



//     <div class="flex items-center justify-between mb-4">
//     <h2 class="text-xl font-bold text-white">{{ $assignment->title }}</h2>
//     <livewire:component.common.bookmark-button 
//         resourceableType="App\Models\Assignment" 
//         resourceableId="{{ $assignment->id }}"
//         courseId="{{ $assignment->course->id }}"
//     />
// </div>
}