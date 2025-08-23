<?php

namespace App\Livewire\StudentManagement\CourseView;

use Livewire\Component;
use App\Models\Lesson;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class LessonContentViewer extends Component
{
    public $lesson;
    public $allLessons;
    public $currentIndex;
    public $isCompleted = false;
    public $completedLessons;
    public $unlockedSections;

    public function mount($lesson, $allLessons, $completedLessons = [], $unlockedSections = [])
    {
        $this->lesson = $lesson;
        
        // Ensure allLessons is a collection and handle both arrays and objects
        if (is_array($allLessons)) {
            $this->allLessons = collect($allLessons)->map(function ($lessonData) {
                // If it's already an object, return as is
                if (is_object($lessonData)) {
                    return $lessonData;
                }
                // If it's an array, convert to object-like structure
                return (object) $lessonData;
            });
        } else {
            $this->allLessons = collect($allLessons);
        }
        
        $this->completedLessons = $completedLessons ?? [];
        $this->unlockedSections = $unlockedSections ?? [];
        
        $this->currentIndex = $this->allLessons->search(function($l) {
            $lessonId = is_object($l) ? $l->id : $l['id'];
            return $lessonId == $this->lesson->id;
        });
        
        if ($this->currentIndex === false) {
            $this->currentIndex = 0;
        }
        
        $this->isCompleted = in_array($this->lesson->id, $this->completedLessons);
    }

    #[On('progress-updated')]
    public function refreshProgress()
    {
        $this->isCompleted = in_array($this->lesson->id, $this->completedLessons);
    }

    public function markAsCompleted()
    {
        if (!$this->isCompleted) {
            $this->dispatch('lesson-completed', lessonId: $this->lesson->id)
                ->to('student-management.course-view');
            
            $this->isCompleted = true;
            
            $this->dispatch('notify', [
                'message' => 'Lesson marked as completed!',
                'type' => 'success'
            ]);
        }
    }

    public function markAsIncomplete()
    {
        if ($this->isCompleted) {
            $this->dispatch('lesson-uncompleted', lessonId: $this->lesson->id)
                ->to('student-management.course-view');
            
            $this->isCompleted = false;
            
            $this->dispatch('notify', [
                'message' => 'Lesson marked as incomplete.',
                'type' => 'info'
            ]);
        }
    }

    public function goToPreviousLesson()
    {
        $prevLesson = $this->allLessons[$this->currentIndex - 1] ?? null;
        if ($prevLesson) {
            $lessonId = is_object($prevLesson) ? $prevLesson->id : $prevLesson['id'];
            $this->dispatch('lesson-selected', lessonId: $lessonId)
                ->to('student-management.course-view');
        }
    }

    public function goToNextLesson()
    {
        $nextLesson = $this->allLessons[$this->currentIndex + 1] ?? null;
        if ($nextLesson) {
            $lessonId = is_object($nextLesson) ? $nextLesson->id : $nextLesson['id'];
            $sectionId = is_object($nextLesson) ? $nextLesson->section_id : $nextLesson['section_id'];
            
            // Check if next lesson's section is unlocked
            if (in_array($sectionId, $this->unlockedSections)) {
                $this->dispatch('lesson-selected', lessonId: $lessonId)
                    ->to('student-management.course-view');
            } else {
                $this->dispatch('notify', [
                    'message' => 'Complete the current section to unlock the next lesson.',
                    'type' => 'warning'
                ]);
            }
        }
    }

    public function getPreviousLesson()
    {
        return $this->allLessons[$this->currentIndex - 1] ?? null;
    }

    public function getNextLesson()
    {
        return $this->allLessons[$this->currentIndex + 1] ?? null;
    }

    public function isNextLessonUnlocked()
    {
        $nextLesson = $this->getNextLesson();
        if (!$nextLesson) return false;
        
        $sectionId = is_object($nextLesson) ? $nextLesson->section_id : $nextLesson['section_id'];
        return in_array($sectionId, $this->unlockedSections);
    }

    public function completeCourse()
    {
        // Mark current lesson as completed if not already
        if (!$this->isCompleted) {
            $this->markAsCompleted();
        }

        $this->dispatch('notify', [
            'message' => 'Congratulations! You have completed this course!',
            'type' => 'success'
        ]);

        // You could redirect to a course completion page or certificate page
        // return redirect()->route('course.completed', $this->lesson->section->course);
    }

    public function render()
    {
        return view('livewire.student-management.course-view.lesson-content-viewer');
    }
}