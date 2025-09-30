<?php

namespace App\Livewire\CourseManagement;

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
    
    // Polling control
    public $enablePolling = true;
    public $lastActivity = null;
    
    // Cache for detecting changes
    public $lastCourseHash = null;

    public function mount(Course $course)
    {
        $this->course = $course;
        $this->lastActivity = now()->timestamp;
        $this->lastCourseHash = $this->generateCourseHash();
        
        // Auto-select first lesson if none is selected
        if (!$this->activeContentId && $this->course->sections->count() > 0) {
            $firstSection = $this->course->sections->first();
            if ($firstSection && $firstSection->lessons->count() > 0) {
                $firstLesson = $firstSection->lessons->first();
                $this->selectLesson($firstLesson->id);
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
            $this->updateActivity();
            
            // Notify outline to update its active state
            $this->dispatch('lesson-selected', lessonId: $lessonId)
                ->to('course-management.course-builder.course-outline');
        }
    }

    #[On('lesson-deselected')]
    public function deselectLesson()
    {
        $this->activeContentId = null;
        $this->activeContentType = null;
        $this->activeSectionId = null;
    }
    
    #[On('outline-updated')]
    #[On('course-updated')]
    public function refreshCourse()
    {
        $this->course->refresh();
        $this->lastCourseHash = $this->generateCourseHash();
        
        // If current lesson no longer exists, reset selection
        if ($this->activeContentId && $this->activeContentType === 'lesson') {
            if (!Lesson::find($this->activeContentId)) {
                $this->deselectLesson();
            }
        }
    }

    public function pollForUpdates()
    {
        // Don't poll if user is actively working
        if ($this->lastActivity && (now()->timestamp - $this->lastActivity) < 30) {
            return;
        }

        $currentHash = $this->generateCourseHash();
        if ($currentHash !== $this->lastCourseHash) {
            $this->refreshCourse();
            $this->dispatch('course-data-updated');
        }
    }

    #[On('user-activity')]
    public function updateActivity()
    {
        $this->lastActivity = now()->timestamp;
    }

    private function generateCourseHash()
    {
        $this->course->refresh();
        $data = [
            'course_updated_at' => $this->course->updated_at?->timestamp,
            'sections_count' => $this->course->sections()->count(),
            'lessons_count' => $this->course->sections()->withCount('lessons')->get()->sum('lessons_count'),
            'course_published' => $this->course->is_published,
        ];
        
        return md5(json_encode($data));
    }

    #[On('toggle-polling')]
    public function togglePolling($enable = null)
    {
        $this->enablePolling = $enable ?? !$this->enablePolling;
    }

    public function render()
    {
        return view('livewire.course-management.course-builder');
    }
}