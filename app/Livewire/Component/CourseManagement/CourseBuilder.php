<?php

namespace App\Livewire\Component\CourseManagement;

use App\Models\Course;
use App\Models\Lesson;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

#[Layout('layouts.dashboard')]
class CourseBuilder extends Component
{
    public Course $course;
    public $activeContentId = null;
    public $activeContentType = null;
    public $activeSectionId = null;

    public function mount(Course $course)
    {
        $this->course = $course;
        
        // Auto-select first lesson if none is selected
        if (!$this->activeContentId && $this->course->sections->count() > 0) {
            $firstSection = $this->course->sections->first();
            if ($firstSection && $firstSection->lessons->count() > 0) {
                $firstLesson = $firstSection->lessons->first();
                $this->activeContentId = $firstLesson->id;
                $this->activeContentType = 'lesson';
                $this->activeSectionId = $firstSection->id;
            }
        }
    }

    #[On('lesson-selected')]
    public function selectLesson($lessonId)
    {
        $lesson = Lesson::find($lessonId);
        if ($lesson) {
            $this->activeContentId = $lessonId;
            $this->activeContentType = 'lesson';
            $this->activeSectionId = $lesson->section_id;
        }
    }
    
    #[On('outline-updated')]
    #[On('course-updated')]
    public function refreshCourse()
    {
        $this->course->refresh();
        
        // If current lesson no longer exists, reset selection
        if ($this->activeContentId && $this->activeContentType === 'lesson') {
            if (!Lesson::find($this->activeContentId)) {
                $this->activeContentId = null;
                $this->activeContentType = null;
                $this->activeSectionId = null;
            }
        }
    }

    public function render()
    {
        return view('livewire.component.course-management.course-builder');
    }
}