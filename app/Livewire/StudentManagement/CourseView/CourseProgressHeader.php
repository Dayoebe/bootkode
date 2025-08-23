<?php

namespace App\Livewire\StudentManagement\CourseView;

use Livewire\Component;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class CourseProgressHeader extends Component
{
    public $course;
    public $overallProgress;
    public $currentSection;
    public $completedLessons;
    public $totalLessons;

    public function mount(Course $course, $overallProgress = 0, $currentSection = null)
    {
        $this->course = $course;
        $this->overallProgress = $overallProgress;
        $this->currentSection = $currentSection;
        $this->totalLessons = $course->sections->flatMap->lessons->count();
    }

    #[On('progress-updated')]
    public function updateProgress($overallProgress = null, $currentSection = null)
    {
        if ($overallProgress !== null) {
            $this->overallProgress = $overallProgress;
        }
        
        if ($currentSection !== null) {
            $this->currentSection = $currentSection;
        }
    }

    #[On('section-completed')]
    public function handleSectionCompleted($sectionId)
    {
        $this->dispatch('notify', [
            'message' => 'Section completed! Next section unlocked.',
            'type' => 'success'
        ]);
    }

    public function getProgressStats()
    {
        $totalLessons = $this->totalLessons;
        $completedCount = count($this->completedLessons ?? []);
        
        return [
            'completed' => $completedCount,
            'total' => $totalLessons,
            'percentage' => $this->overallProgress
        ];
    }

    public function render()
    {
        return view('livewire.student-management.course-view.course-progress-header');
    }
}