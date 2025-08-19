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
    public $activeContentType = null; // 'lesson' or 'quiz'
    public $activeSectionId = null;

    public function mount(Course $course)
    {
        $this->course = $course;
    }

    #[On('lesson-selected')]
    public function selectLesson($lessonId)
    {
        // Only update if it's a different lesson
        if ($this->activeContentId !== $lessonId) {
            $this->activeContentId = $lessonId;
            $this->activeContentType = 'lesson';
            $this->activeSectionId = Lesson::find($lessonId)->section_id;
            
            // Tell the editor to reload its content
            $this->dispatch('lesson-changed', lessonId: $lessonId)
                 ->to('component.course-management.course-builder.lesson-editor');
        }
    }

    #[On('section-expanded')]
    public function expandSection($sectionId)
    {
        $this->activeSectionId = $sectionId;
    }

    public function render()
    {
        return view('livewire.component.course-management.course-builder');
    }
}