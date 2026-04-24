<?php

namespace App\Livewire\StudentManagement\CourseView;

use Livewire\Component;
use App\Models\Learning\Course;
use Livewire\Attributes\On;

class CourseProgressHeader extends Component
{
    public $course;
    public $overallProgress;
    public $currentSection;
    public $completedLessons;
    public $unlockedSections;
    public $sectionCompletionThreshold;

    #[On('progress-updated')]
    public function updateProgress()
    {
        $this->dispatch('$refresh');
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
        $totalLessons = $this->course->sections->flatMap->lessons->count();
        $completedCount = count($this->completedLessons ?? []);
        
        return [
            'completed' => $completedCount,
            'total' => $totalLessons,
            'percentage' => $this->overallProgress
        ];
    }

    public function calculateSectionProgress($section): int
    {
        $totalLessons = $section->lessons->count();

        if ($totalLessons === 0) {
            return 0;
        }

        $completedCount = $section->lessons
            ->filter(fn($lesson) => in_array($lesson->id, $this->completedLessons ?? [], true))
            ->count();

        return (int) round(($completedCount / $totalLessons) * 100);
    }

    public function getCompletedSectionsCount(): int
    {
        return $this->course->sections
            ->filter(fn($section) => $section->lessons->count() > 0 && $this->calculateSectionProgress($section) === 100)
            ->count();
    }

    public function getNextUnlockMilestone(): ?array
    {
        $sections = $this->course->sections->values();

        foreach ($sections as $index => $section) {
            if (in_array($section->id, $this->unlockedSections ?? [], true)) {
                continue;
            }

            $previousSection = $sections->get($index - 1);

            if (!$previousSection) {
                return null;
            }

            $completedCount = $previousSection->lessons
                ->filter(fn($lesson) => in_array($lesson->id, $this->completedLessons ?? [], true))
                ->count();

            $requiredCount = max(1, (int) ceil($previousSection->lessons->count() * ($this->sectionCompletionThreshold / 100)));

            return [
                'section_title' => $section->title,
                'remaining_lessons' => max($requiredCount - $completedCount, 0),
                'required_lessons' => $requiredCount,
                'completed_lessons' => $completedCount,
            ];
        }

        return null;
    }

    public function render()
    {
        return view('livewire.student-management.course-view.course-progress-header');
    }
}
