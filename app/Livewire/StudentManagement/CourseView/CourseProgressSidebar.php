<?php

namespace App\Livewire\StudentManagement\CourseView;

use Livewire\Component;
use App\Models\Course;
use Livewire\Attributes\On;

class CourseProgressSidebar extends Component
{
    public $course;
    public $sections;
    public $currentLesson;
    public $completedLessons;
    public $unlockedSections;
    public $sectionCompletionThreshold;

    public function mount(
        Course $course, 
        $sections, 
        $currentLesson = null, 
        $completedLessons = [], 
        $unlockedSections = [],
        $sectionCompletionThreshold = 80
    ) {
        $this->course = $course;
        $this->sections = $sections;
        $this->currentLesson = $currentLesson;
        $this->completedLessons = $completedLessons ?? [];
        $this->unlockedSections = $unlockedSections ?? [];
        $this->sectionCompletionThreshold = $sectionCompletionThreshold;
    }

    #[On('progress-updated')]
    public function refreshProgress()
    {
        // The parent component will pass updated data
        $this->dispatch('$refresh');
    }

    public function selectLesson($lessonId, $sectionId)
    {
        // Check if section is unlocked
        if (!in_array($sectionId, $this->unlockedSections)) {
            $this->dispatch('notify', [
                'message' => "Complete at least {$this->sectionCompletionThreshold}% of the previous section to unlock this lesson.",
                'type' => 'warning'
            ]);
            return;
        }

        $this->dispatch('lesson-selected', lessonId: $lessonId)->to('student-management.course-view');
    }

    public function calculateSectionProgress($section)
    {
        $totalLessons = $section->lessons->count();
        if ($totalLessons === 0) return 0;
        
        $completed = 0;
        foreach ($section->lessons as $lesson) {
            if (in_array($lesson->id, $this->completedLessons)) {
                $completed++;
            }
        }
        
        return round(($completed / $totalLessons) * 100);
    }

    public function isSectionUnlocked($sectionId)
    {
        return in_array($sectionId, $this->unlockedSections);
    }

    public function isSectionCompleted($section)
    {
        foreach ($section->lessons as $lesson) {
            if (!in_array($lesson->id, $this->completedLessons)) {
                return false;
            }
        }
        return $section->lessons->count() > 0;
    }

    public function render()
    {
        return view('livewire.student-management.course-view.course-progress-sidebar');
    }
}