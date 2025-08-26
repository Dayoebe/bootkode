<?php

namespace App\Livewire\StudentManagement\CourseView;

use Livewire\Component;
use App\Models\Lesson;
use App\Models\Assessment;
use App\Models\StudentAnswer;
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
    public $hasAssessments = false;
    public $allAssessmentsPassed = false;

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
        
        // Check for assessments
        $this->checkAssessments();
    }

    protected function checkAssessments()
    {
        // Check if lesson has assessments
        $assessments = Assessment::where('lesson_id', $this->lesson->id)->get();
        $this->hasAssessments = $assessments->count() > 0;
        
        if ($this->hasAssessments) {
            $this->allAssessmentsPassed = $this->checkAllAssessmentsPassed($assessments);
        } else {
            $this->allAssessmentsPassed = true; // If no assessments, consider passed
        }
    }

    protected function checkAllAssessmentsPassed($assessments)
    {
        foreach ($assessments as $assessment) {
            // Get the latest attempt for this assessment
            $latestAttempt = StudentAnswer::where('user_id', Auth::id())
                ->where('assessment_id', $assessment->id)
                ->orderBy('attempt_number', 'desc')
                ->first();

            if (!$latestAttempt) {
                return false; // No attempt made
            }

            // Calculate score for the latest attempt
            $totalPoints = StudentAnswer::where('user_id', Auth::id())
                ->where('assessment_id', $assessment->id)
                ->where('attempt_number', $latestAttempt->attempt_number)
                ->sum('points_earned');

            $maxPoints = $assessment->questions->sum('points');
            $percentage = $maxPoints > 0 ? round(($totalPoints / $maxPoints) * 100, 1) : 0;

            // Check if passed
            if ($percentage < $assessment->pass_percentage) {
                return false;
            }
        }

        return true; // All assessments passed
    }

    #[On('progress-updated')]
    public function refreshProgress()
    {
        $this->isCompleted = in_array($this->lesson->id, $this->completedLessons);
        $this->checkAssessments(); // Re-check assessments
    }

    #[On('assessment-completed')]
    public function handleAssessmentCompleted()
    {
        $this->checkAssessments(); // Re-check assessments when one is completed
    }

    public function markAsCompleted()
    {
        // Check if assessments are required and passed
        if ($this->hasAssessments && !$this->allAssessmentsPassed) {
            $this->dispatch('notify', [
                'message' => 'You must pass all assessments in this lesson before marking it as complete.',
                'type' => 'warning'
            ]);
            return;
        }

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
        // Check if current lesson has assessments that need to be passed
        if ($this->hasAssessments && !$this->allAssessmentsPassed) {
            $this->dispatch('notify', [
                'message' => 'You must pass all assessments in this lesson before proceeding to the next lesson.',
                'type' => 'warning'
            ]);
            return;
        }

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
        // Check if current lesson has assessments that need to be passed
        if ($this->hasAssessments && !$this->allAssessmentsPassed) {
            $this->dispatch('notify', [
                'message' => 'You must pass all assessments in this lesson before completing the course.',
                'type' => 'warning'
            ]);
            return;
        }

        // Mark current lesson as completed if not already
        if (!$this->isCompleted) {
            $this->markAsCompleted();
        }

        $this->dispatch('notify', [
            'message' => 'Congratulations! You have completed this course!',
            'type' => 'success'
        ]);

        // You could redirect to a course completion page or certificate page
        return redirect()->route('certificates.index', $this->lesson->section->course);
    }

    public function render()
    {
        return view('livewire.student-management.course-view.lesson-content-viewer');
    }
}