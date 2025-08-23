<?php

namespace App\Livewire\StudentManagement;

use Livewire\Component;
use App\Models\Course;
use App\Models\Section;
use App\Models\Lesson;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;

#[Layout('layouts.dashboard')]
#[Title('Course View')]
class CourseView extends Component
{
    public $course;
    public $currentLesson;
    public $currentSection;
    public $completedLessons = [];
    public $unlockedSections = [];
    public $sectionCompletionThreshold;

    public function mount($course)
    {
        // Set completion threshold from config
        $this->sectionCompletionThreshold = config('course.section_completion_threshold', 80);
        
        $this->course = Course::with(['sections.lessons', 'instructor', 'category'])
            ->where('slug', $course)
            ->where('is_published', true)
            ->where('is_approved', true)
            ->firstOrFail();

        // Check if user is enrolled
        if (!Auth::user()->courses()->where('course_id', $this->course->id)->exists()) {
            abort(403, 'You are not enrolled in this course.');
        }

        $this->loadUserProgress();
        $this->determineUnlockedSections();
        $this->setInitialLesson();
    }

    protected function loadUserProgress()
    {
        // Get completed lessons - make sure we get them as IDs
        $completedLessonsQuery = Auth::user()->completedLessons()
            ->whereIn('lesson_id', $this->getAllLessonIds())
            ->pluck('lesson_id');
        
        $this->completedLessons = $completedLessonsQuery->toArray();
    }

    protected function getAllLessonIds()
    {
        $lessonIds = [];
        foreach ($this->course->sections as $section) {
            foreach ($section->lessons as $lesson) {
                $lessonIds[] = $lesson->id;
            }
        }
        return $lessonIds;
    }

    protected function determineUnlockedSections()
    {
        $this->unlockedSections = [];
        
        foreach ($this->course->sections as $index => $section) {
            if ($index === 0) {
                // First section is always unlocked
                $this->unlockedSections[] = $section->id;
            } else {
                // Check if previous section meets completion threshold
                $previousSection = $this->course->sections[$index - 1];
                $previousProgress = $this->calculateSectionProgress($previousSection);
                
                if ($previousProgress >= $this->sectionCompletionThreshold) {
                    $this->unlockedSections[] = $section->id;
                }
            }
        }
    }

    protected function setInitialLesson()
    {
        // Find the first incomplete lesson in unlocked sections
        foreach ($this->course->sections as $section) {
            if (in_array($section->id, $this->unlockedSections)) {
                foreach ($section->lessons as $lesson) {
                    if (!in_array($lesson->id, $this->completedLessons)) {
                        $this->currentLesson = $lesson;
                        $this->currentSection = $section;
                        return;
                    }
                }
            }
        }

        // If all unlocked lessons are completed, set to first lesson of first unlocked section
        foreach ($this->course->sections as $section) {
            if (in_array($section->id, $this->unlockedSections) && $section->lessons->count() > 0) {
                $this->currentLesson = $section->lessons->first();
                $this->currentSection = $section;
                return;
            }
        }
    }

    #[On('lesson-selected')]
    public function setCurrentLesson($lessonId)
    {
        try {
            $lesson = Lesson::with('section')->findOrFail($lessonId);
            
            // Check if the section is unlocked
            if (!in_array($lesson->section_id, $this->unlockedSections)) {
                $sectionIndex = $this->getSectionIndex($lesson->section_id);
                $requiredProgress = $this->sectionCompletionThreshold;
                
                $this->dispatch('notify', [
                    'message' => "Complete at least {$requiredProgress}% of the previous section to unlock this lesson.",
                    'type' => 'warning'
                ]);
                return;
            }

            $this->currentLesson = $lesson;
            $this->currentSection = $lesson->section;
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Lesson not found.',
                'type' => 'error'
            ]);
        }
    }

    protected function getSectionIndex($sectionId)
    {
        foreach ($this->course->sections as $index => $section) {
            if ($section->id == $sectionId) {
                return $index;
            }
        }
        return 0;
    }

    #[On('lesson-completed')]
    public function handleLessonCompleted($lessonId)
    {
        if (!in_array($lessonId, $this->completedLessons)) {
            try {
                Auth::user()->completedLessons()->attach($lessonId, ['completed_at' => now()]);
                $this->completedLessons[] = $lessonId;
                
                $this->updateCourseProgress();
                $this->determineUnlockedSections();
                
                // Check if section progress now meets threshold to unlock next section
                $lesson = Lesson::find($lessonId);
                if ($lesson) {
                    $sectionProgress = $this->calculateSectionProgress($lesson->section);
                    if ($sectionProgress >= $this->sectionCompletionThreshold) {
                        $this->checkAndUnlockNextSection($lesson->section);
                    }
                }

                $this->dispatch('progress-updated');
            } catch (\Exception $e) {
                $this->dispatch('notify', [
                    'message' => 'Error marking lesson as completed.',
                    'type' => 'error'
                ]);
            }
        }
    }

    #[On('lesson-uncompleted')]
    public function handleLessonUncompleted($lessonId)
    {
        if (in_array($lessonId, $this->completedLessons)) {
            try {
                Auth::user()->completedLessons()->detach($lessonId);
                $this->completedLessons = array_values(array_diff($this->completedLessons, [$lessonId]));
                
                $this->updateCourseProgress();
                $this->determineUnlockedSections();
                $this->dispatch('progress-updated');
            } catch (\Exception $e) {
                $this->dispatch('notify', [
                    'message' => 'Error marking lesson as incomplete.',
                    'type' => 'error'
                ]);
            }
        }
    }

    protected function checkAndUnlockNextSection($currentSection)
    {
        $currentIndex = $this->getSectionIndex($currentSection->id);
        $nextSection = $this->course->sections[$currentIndex + 1] ?? null;
        
        if ($nextSection && !in_array($nextSection->id, $this->unlockedSections)) {
            $this->dispatch('section-unlocked', [
                'sectionId' => $nextSection->id,
                'sectionTitle' => $nextSection->title
            ]);
        }
    }

    public function isSectionUnlocked($sectionId)
    {
        return in_array($sectionId, $this->unlockedSections);
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

    public function calculateOverallProgress()
    {
        $totalLessons = $this->getAllLessonIds();
        $totalCount = count($totalLessons);
        $completedCount = count($this->completedLessons);
        
        return $totalCount > 0 ? round(($completedCount / $totalCount) * 100) : 0;
    }

    private function updateCourseProgress()
    {
        $progress = $this->calculateOverallProgress();
        
        try {
            // Update user's course progress
            Auth::user()->courses()->updateExistingPivot($this->course->id, [
                'progress' => $progress,
                'updated_at' => now()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating course progress: ' . $e->getMessage());
        }
    }

    public function getSectionRequirementText($section)
    {
        $sectionIndex = $this->getSectionIndex($section->id);
        if ($sectionIndex === 0) {
            return 'Available to start';
        }
        
        if (in_array($section->id, $this->unlockedSections)) {
            return 'Unlocked';
        }
        
        return "Complete {$this->sectionCompletionThreshold}% of previous section to unlock";
    }

    public function render()
    {
        return view('livewire.student-management.course-view');
    }
}