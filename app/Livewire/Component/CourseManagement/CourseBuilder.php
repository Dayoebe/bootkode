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
    }

    #[On('lesson-selected')]
    public function selectLesson($lessonId)
    {
        $this->activeContentId = $lessonId;
        $this->activeContentType = 'lesson';
        $this->activeSectionId = Lesson::find($lessonId)->section_id;
    }
    #[On('outline-updated')]
    #[On('course-updated')]
    public function refreshCourse()
    {
        $this->course->refresh();
    }
    public function render()
    {
        return view('livewire.component.course-management.course-builder');
    }
}