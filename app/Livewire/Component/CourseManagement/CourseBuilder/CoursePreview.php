<?php

namespace App\Livewire\Component\CourseManagement\CourseBuilder;

use App\Models\Course;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

#[Layout('layouts.dashboard', ['title' => 'Course Preview'])]
class CoursePreview extends Component
{
    public Course $course;
    public $highlightLesson = null;

    // public function mount(Course $course, $highlight = null)
    // {
    //     // Authorization check
    //     if (!Gate::allows('view', $course)) {
    //         abort(403);
    //     }

    //     // Eager load all necessary relationships
    //     $this->course = $course->load([
    //         'sections.lessons',
    //         'modules.lessons',
    //         'modules.quizzes.questions.options',
    //         'instructor',
    //         'category'
    //     ]);

    //     $this->highlightLesson = $highlight;
    // }

    // // ... rest of your component ...

    public function mount(Course $course, $highlight = null)
    {
        if (!Gate::allows('view', $course)) {
            abort(403);
        }
    
        $this->course = $course->load([
            'sections.lessons' => function($query) {
                $query->orderBy('order')->with('section');
            },
            'sections.lessons',
            'modules.lessons',
            'modules.quizzes.questions.options',
            'instructor',
            'category'
        ]);
    
        $this->highlightLesson = $highlight;
    }

 

    public function render()
    {
        return view('livewire.component.course-management.course-builder.course-preview');
    }
}