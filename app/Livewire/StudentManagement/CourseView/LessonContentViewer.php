<?php

namespace App\Livewire\StudentManagement\CourseView;

use Livewire\Component;
use App\Models\Learning\Lesson;
use App\Models\Assessment\Assessment;
use App\Models\Assessment\StudentAnswer;
use App\Models\Learning\LessonProgress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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

    // NEW: Transition indicator
    public $isTransitioning = false;

    // NEW: Time tracking
    public $timeSpentSeconds = 0;
    public $lessonStartTime;
    public $estimatedTime;
    public $showTimeComparison = false;
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

        // NEW: Initialize time tracking
        $this->initializeTimeTracking();

        // NEW: Store last viewed lesson for "Continue Learning"
        $this->storeLastViewedLesson();
    }

    // NEW: Time tracking initialization
    protected function initializeTimeTracking()
    {
        $this->lessonStartTime = now();
        $this->estimatedTime = $this->lesson->estimated_duration_minutes ?? 0;

        // Load existing progress if any
        $progress = LessonProgress::where('user_id', Auth::id())
            ->where('lesson_id', $this->lesson->id)
            ->first();

        if ($progress) {
            $this->timeSpentSeconds = $progress->time_spent_seconds ?? 0;
        }

        $this->showTimeComparison = $this->estimatedTime > 0;
    }

    // NEW: Store last viewed lesson
    protected function storeLastViewedLesson()
    {
        Cache::put(
            'user_' . Auth::id() . '_last_lesson_' . $this->lesson->section->course_id,
            [
                'lesson_id' => $this->lesson->id,
                'section_id' => $this->lesson->section_id,
                'timestamp' => now()
            ],
            now()->addDays(30)
        );
    }

    // NEW: Update time spent (called via polling)
    public function updateTimeSpent()
    {
        if ($this->lessonStartTime) {
            $elapsed = now()->diffInSeconds($this->lessonStartTime);
            $this->timeSpentSeconds += $elapsed;
            $this->lessonStartTime = now();

            // Save to database
            LessonProgress::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'lesson_id' => $this->lesson->id,
                ],
                [
                    'time_spent_seconds' => $this->timeSpentSeconds,
                    'last_accessed_at' => now(),
                ]
            );
        }
    }

    // NEW: Get formatted time spent
    public function getFormattedTimeSpent()
    {
        $minutes = floor($this->timeSpentSeconds / 60);
        $seconds = $this->timeSpentSeconds % 60;
        
        if ($minutes > 60) {
            $hours = floor($minutes / 60);
            $minutes = $minutes % 60;
            return "{$hours}h {$minutes}m";
        }
        
        return "{$minutes}m {$seconds}s";
    }

    // NEW: Get time comparison
    public function getTimeComparison()
    {
        if (!$this->showTimeComparison) {
            return null;
        }

        $actualMinutes = floor($this->timeSpentSeconds / 60);
        $estimatedMinutes = $this->estimatedTime;

        return [
            'actual' => $actualMinutes,
            'estimated' => $estimatedMinutes,
            'percentage' => $estimatedMinutes > 0 ? round(($actualMinutes / $estimatedMinutes) * 100) : 0,
            'over_time' => $actualMinutes > $estimatedMinutes,
        ];
    }

    // NEW: Get assessment preview
    public function getAssessmentPreview()
    {
        if (!$this->hasAssessments) {
            return null;
        }

        $assessments = Assessment::where('lesson_id', $this->lesson->id)->get();
        
        $preview = [];
        foreach ($assessments as $assessment) {
            $questionTypes = $assessment->questions()
                ->select('question_type')
                ->get()
                ->pluck('question_type')
                ->unique()
                ->values();

            $difficultyDistribution = $assessment->questions()
                ->select('difficulty_level')
                ->get()
                ->pluck('difficulty_level')
                ->countBy()
                ->toArray();

            $preview[] = [
                'id' => $assessment->id,
                'title' => $assessment->title,
                'type' => $assessment->type,
                'question_count' => $assessment->questions->count(),
                'question_types' => $questionTypes,
                'difficulty_distribution' => $difficultyDistribution,
                'estimated_duration' => $assessment->estimated_duration_minutes,
                'pass_percentage' => $assessment->pass_percentage,
                'total_points' => $assessment->questions->sum('points'),
            ];
        }

        return $preview;
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
            // Save final time spent before completing
            $this->updateTimeSpent();

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
        // Save time before transitioning
        $this->updateTimeSpent();

        $prevLesson = $this->allLessons[$this->currentIndex - 1] ?? null;
        if ($prevLesson) {
            $this->isTransitioning = true;
            $lessonId = is_object($prevLesson) ? $prevLesson->id : $prevLesson['id'];
            
            $this->dispatch('lesson-selected', lessonId: $lessonId)
                ->to('student-management.course-view');
        }
    }

    public function goToNextLesson()
    {
        // Save time before transitioning
        $this->updateTimeSpent();

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
            $this->isTransitioning = true;
            $lessonId = is_object($nextLesson) ? $nextLesson->id : $nextLesson['id'];
            $sectionId = is_object($nextLesson) ? $nextLesson->section_id : $nextLesson['section_id'];

            if (in_array($sectionId, $this->unlockedSections)) {
                $this->dispatch('lesson-selected', lessonId: $lessonId)
                    ->to('student-management.course-view');
            } else {
                $this->isTransitioning = false;
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

        return redirect()->route('student.certificates.index', $this->lesson->section->course);
    }

    public function render()
    {
        return view('livewire.student-management.course-view.lesson-content-viewer');
    }
}