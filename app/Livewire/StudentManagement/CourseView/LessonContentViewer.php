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

    // Polling management
    public $lastAssessmentCheck;
    public $shouldPoll = false;

    public function mount($lesson, $allLessons, $completedLessons = [], $unlockedSections = [])
    {
        $this->lesson = $lesson;

        if (is_array($allLessons)) {
            $this->allLessons = collect($allLessons)->map(function ($lessonData) {
                if (is_object($lessonData)) {
                    return $lessonData;
                }
                return (object) $lessonData;
            });
        } else {
            $this->allLessons = collect($allLessons);
        }

        $this->completedLessons = $completedLessons ?? [];
        $this->unlockedSections = $unlockedSections ?? [];

        $this->currentIndex = $this->allLessons->search(function ($l) {
            $lessonId = is_object($l) ? $l->id : $l['id'];
            return $lessonId == $this->lesson->id;
        });

        if ($this->currentIndex === false) {
            $this->currentIndex = 0;
        }

        $this->isCompleted = in_array($this->lesson->id, $this->completedLessons);
        $this->checkAssessments();
        $this->lastAssessmentCheck = now();
    }

    protected function checkAssessments()
    {
        $assessments = Assessment::where('lesson_id', $this->lesson->id)->get();
        $this->hasAssessments = $assessments->count() > 0;

        if ($this->hasAssessments) {
            $this->allAssessmentsPassed = $this->checkAllAssessmentsPassed($assessments);
            $this->shouldPoll = !$this->allAssessmentsPassed;
        } else {
            $this->allAssessmentsPassed = true;
            $this->shouldPoll = false;
        }
    }

    protected function checkAllAssessmentsPassed($assessments)
    {
        foreach ($assessments as $assessment) {
            $latestAttempt = StudentAnswer::where('user_id', Auth::id())
                ->where('assessment_id', $assessment->id)
                ->orderBy('attempt_number', 'desc')
                ->first();

            if (!$latestAttempt) {
                return false;
            }

            $totalPoints = StudentAnswer::where('user_id', Auth::id())
                ->where('assessment_id', $assessment->id)
                ->where('attempt_number', $latestAttempt->attempt_number)
                ->sum('points_earned');

            $maxPoints = $assessment->questions->sum('points');
            $percentage = $maxPoints > 0 ? round(($totalPoints / $maxPoints) * 100, 1) : 0;

            if ($percentage < $assessment->pass_percentage) {
                return false;
            }
        }

        return true;
    }

    #[On('progress-updated')]
    public function refreshProgress()
    {
        $this->isCompleted = in_array($this->lesson->id, $this->completedLessons);
        $this->checkAssessments();
    }

    #[On('assessment-completed')]
    public function handleAssessmentCompleted()
    {
        $this->checkAssessments();
        $this->lastAssessmentCheck = now();

        if ($this->allAssessmentsPassed) {
            $this->shouldPoll = false;
        }
    }

    public function pollAssessmentStatus()
    {
        if (!$this->shouldPoll) {
            return;
        }

        if ($this->lastAssessmentCheck && $this->lastAssessmentCheck->diffInSeconds(now()) < 10) {
            return;
        }

        $previousState = $this->allAssessmentsPassed;
        $this->checkAssessments();
        $this->lastAssessmentCheck = now();

        if ($previousState !== $this->allAssessmentsPassed && $this->allAssessmentsPassed) {
            $this->dispatch('notify', [
                'message' => 'All assessments completed! You can now proceed.',
                'type' => 'success'
            ]);

            $this->dispatch('assessment-status-changed', [
                'lessonId' => $this->lesson->id,
                'allPassed' => true
            ])->to('student-management.course-view');
        }
    }

    public function markAsCompleted()
    {
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
        // AUTO-COMPLETE CURRENT LESSON if it has no assessments OR all assessments are passed
        if (!$this->isCompleted && (!$this->hasAssessments || $this->allAssessmentsPassed)) {
            $this->markAsCompleted();
        }

        // Check if current lesson requirements are met
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
        if (!$nextLesson)
            return false;

        $sectionId = is_object($nextLesson) ? $nextLesson->section_id : $nextLesson['section_id'];
        return in_array($sectionId, $this->unlockedSections);
    }

    public function canProceedToNext()
    {
        return !$this->hasAssessments || $this->allAssessmentsPassed;
    }

    public function completeCourse()
    {
        if ($this->hasAssessments && !$this->allAssessmentsPassed) {
            $this->dispatch('notify', [
                'message' => 'You must pass all assessments in this lesson before completing the course.',
                'type' => 'warning'
            ]);
            return;
        }

        if (!$this->isCompleted) {
            $this->markAsCompleted();
        }

        $this->dispatch('notify', [
            'message' => 'Congratulations! You have completed this course!',
            'type' => 'success'
        ]);

        return redirect()->route('certificates.index', $this->lesson->section->course);
    }

    public function render()
    {
        return view('livewire.student-management.course-view.lesson-content-viewer');
    }
}