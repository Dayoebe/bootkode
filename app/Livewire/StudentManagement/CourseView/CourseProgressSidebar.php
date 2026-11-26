<?php

namespace App\Livewire\StudentManagement\CourseView;

use App\Livewire\Concerns\RequiresAuthenticatedAccount;
use App\Models\Learning\Course;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class CourseProgressSidebar extends Component
{
    use RequiresAuthenticatedAccount;

    #[Locked]
    public $course;

    #[Locked]
    public $sections;

    #[Locked]
    public $currentLesson;

    #[Locked]
    public $completedLessons;

    #[Locked]
    public $unlockedSections;

    #[Locked]
    public $sectionCompletionThreshold;

    public function boot(): void
    {
        if ($this->course instanceof Course) {
            abort_unless(
                Auth::user()->enrollments()->where('course_id', $this->course->id)->exists()
                || Auth::user()->courses()->where('courses.id', $this->course->id)->exists(),
                403
            );
        }
    }

    #[On('progress-updated')]
    public function refreshProgress()
    {
        // The parent component will pass updated data
        $this->dispatch('$refresh');
    }

    public function selectLesson($lessonId, $sectionId)
    {
        $lesson = $this->course->sections
            ->firstWhere('id', (int) $sectionId)?->lessons
            ->firstWhere('id', (int) $lessonId);

        abort_unless($lesson, 404);

        // Check if section is unlocked
        if (!in_array((int) $sectionId, $this->unlockedSections, true)) {
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
            if (in_array($lesson->id, $this->completedLessons, true)) {
                $completed++;
            }
        }
        
        return round(($completed / $totalLessons) * 100);
    }

    public function isSectionUnlocked($sectionId)
    {
        return in_array($sectionId, $this->unlockedSections, true);
    }

    public function isSectionCompleted($section)
    {
        foreach ($section->lessons as $lesson) {
            if (!in_array($lesson->id, $this->completedLessons, true)) {
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
