<?php

namespace App\Livewire\Cbt;

use Livewire\Component;
use App\Models\Assessment\Assessment;
use App\Models\Assessment\Question;
use App\Models\Assessment\StudentAnswer;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Carbon\Carbon;

#[Layout('layouts.exam', ['title' => 'CBT Exam', 'description' => 'Secure CBT exam interface'])]
class CbtExamInterface extends Component
{
    public $assessment;
    public $questions;
    public $currentQuestionIndex = 0;
    public $answers = [];
    public $timeRemaining;
    public $examStarted = false;
    public $examCompleted = false;
    public $results = null;
    public $flaggedQuestions = [];
    public $attemptNumber;
    public $startTime;
    public $securityViolations = [];
    public $showSubmitModal = false;
    public $isFullscreenForced = false;
    
    // Progress tracking properties
    public $progressTracking = [
        'question_start_times' => [],
        'question_end_times' => [],
        'question_durations' => [],
        'total_active_time' => 0,
        'pause_count' => 0,
        'navigation_count' => 0
    ];

    public function mount(Assessment $assessment)
    {
        \Log::info('CbtExamInterface mount called', [
            'assessment_id' => $assessment->id,
            'user_id' => Auth::id(),
            'questions_count' => $assessment->questions->count()
        ]);

        if (!$assessment || $assessment->questions->count() === 0) {
            \Log::error('Invalid assessment or no questions', [
                'assessment_id' => $assessment->id ?? 'null',
                'questions_count' => $assessment->questions->count() ?? 0
            ]);

            session()->flash('error', 'Invalid assessment or no questions available.');
            return redirect()->route('cbt.exams');
        }

        $this->assessment = $assessment->load('questions');
        $this->loadQuestions();
        $this->initializeExam();
    }

    public function render()
    {
        \Log::info('CbtExamInterface render called', [
            'examStarted' => $this->examStarted,
            'examCompleted' => $this->examCompleted,
            'questions_count' => count($this->questions ?? [])
        ]);

        return view('livewire.cbt.cbt-exam-interface');
    }

    protected function loadQuestions()
    {
        $this->questions = $this->assessment->questions->map(function ($question) {
            $questionArray = $question->toArray();

            // Handle options field
            if (isset($questionArray['options'])) {
                if (is_string($questionArray['options'])) {
                    $decodedOptions = json_decode($questionArray['options'], true);
                    $questionArray['options'] = is_array($decodedOptions) ? $decodedOptions : [];
                } elseif (!is_array($questionArray['options'])) {
                    $questionArray['options'] = [];
                }
            } else {
                $questionArray['options'] = [];
            }

            // Handle correct_answers field
            if (isset($questionArray['correct_answers'])) {
                if (is_string($questionArray['correct_answers'])) {
                    $decodedAnswers = json_decode($questionArray['correct_answers'], true);
                    $questionArray['correct_answers'] = is_array($decodedAnswers) ? $decodedAnswers : [];
                } elseif (!is_array($questionArray['correct_answers'])) {
                    $questionArray['correct_answers'] = [$questionArray['correct_answers']];
                }
            } else {
                $questionArray['correct_answers'] = [];
            }

            return $questionArray;
        })->toArray();
    }

    protected function initializeExam()
    {
        $this->timeRemaining = $this->assessment->estimated_duration_minutes * 60;
        $this->attemptNumber = $this->assessment->getNextAttemptNumber(Auth::id());

        // Initialize answers array
        foreach ($this->questions as $question) {
            $this->answers[$question['id']] = null;
        }

        // Initialize progress tracking
        $this->progressTracking = [
            'question_start_times' => [],
            'question_end_times' => [],
            'question_durations' => [],
            'total_active_time' => 0,
            'pause_count' => 0,
            'navigation_count' => 0,
            'current_question_start' => null
        ];

        \Log::info('Exam initialized', [
            'timeRemaining' => $this->timeRemaining,
            'attemptNumber' => $this->attemptNumber,
            'answers_initialized' => count($this->answers)
        ]);
    }

    public function nextQuestion()
    {
        if ($this->currentQuestionIndex < count($this->questions) - 1) {
            $this->trackQuestionTime($this->currentQuestionIndex);
            $previousIndex = $this->currentQuestionIndex;
            $this->currentQuestionIndex++;
            $this->progressTracking['navigation_count']++;
            $this->startQuestionTimer();
            
            $this->dispatch('questionChanged', 
                previousIndex: $previousIndex, 
                currentIndex: $this->currentQuestionIndex
            );
            
            \Log::info('Moved to next question', ['index' => $this->currentQuestionIndex]);
        }
    }

    public function previousQuestion()
    {
        if ($this->currentQuestionIndex > 0) {
            $this->trackQuestionTime($this->currentQuestionIndex);
            $previousIndex = $this->currentQuestionIndex;
            $this->currentQuestionIndex--;
            $this->progressTracking['navigation_count']++;
            $this->startQuestionTimer();
            
            $this->dispatch('questionChanged', 
                previousIndex: $previousIndex, 
                currentIndex: $this->currentQuestionIndex
            );
            
            \Log::info('Moved to previous question', ['index' => $this->currentQuestionIndex]);
        }
    }

    public function goToQuestion($index)
    {
        if ($index >= 0 && $index < count($this->questions)) {
            $this->trackQuestionTime($this->currentQuestionIndex);
            $previousIndex = $this->currentQuestionIndex;
            $this->currentQuestionIndex = $index;
            $this->progressTracking['navigation_count']++;
            $this->startQuestionTimer();
            
            $this->dispatch('questionChanged', 
                previousIndex: $previousIndex, 
                currentIndex: $this->currentQuestionIndex
            );
            
            \Log::info('Moved to question', ['index' => $index]);
        }
    }

    protected function startQuestionTimer()
    {
        $this->progressTracking['current_question_start'] = microtime(true);
    }

    protected function trackQuestionTime($questionIndex)
    {
        if (isset($this->progressTracking['current_question_start'])) {
            $startTime = $this->progressTracking['current_question_start'];
            $endTime = microtime(true);
            $duration = $endTime - $startTime;
            
            $this->progressTracking['question_durations'][$questionIndex] = $duration;
            $this->progressTracking['total_active_time'] += $duration;
            
            \Log::info('Question time tracked', [
                'question_index' => $questionIndex,
                'duration' => $duration,
                'total_active_time' => $this->progressTracking['total_active_time']
            ]);
        }
    }

    public function toggleFlag($questionIndex)
    {
        if (!isset($this->questions[$questionIndex])) {
            return;
        }

        $questionId = $this->questions[$questionIndex]['id'];

        if (in_array($questionId, $this->flaggedQuestions)) {
            $this->flaggedQuestions = array_values(array_filter($this->flaggedQuestions, function ($id) use ($questionId) {
                return $id !== $questionId;
            }));
        } else {
            $this->flaggedQuestions[] = $questionId;
        }

        \Log::info('Question flag toggled', [
            'questionId' => $questionId,
            'flagged' => in_array($questionId, $this->flaggedQuestions)
        ]);
    }

    public function retakeExam()
    {
        return redirect()->route('cbt.exams');
    }

    public function handleSecurityViolation($type, $details = null)
    {
        if (!$this->examStarted || $this->examCompleted) {
            return;
        }

        $violation = [
            'type' => $type,
            'details' => $details,
            'timestamp' => Carbon::now(),
            'question_index' => $this->currentQuestionIndex,
            'time_remaining' => $this->timeRemaining
        ];

        $this->securityViolations[] = $violation;

        \Log::warning('CBT Security Violation', [
            'user_id' => Auth::id(),
            'assessment_id' => $this->assessment->id,
            'violation' => $violation
        ]);

        // Track pause for security violations
        if ($type === 'app_switch' || $type === 'visibility_change') {
            $this->progressTracking['pause_count']++;
        }

        // Enhanced security - force fullscreen on violations
        if ($type === 'fullscreen_exit') {
            $this->dispatch('forceFullscreen');
        }

        // Auto-submit if too many violations
        if (count($this->securityViolations) >= 10) {
            session()->flash('error', 'Too many security violations detected. Exam auto-submitted.');
            $this->submitExam();
        }
    }

    public function handleVisibilityChange($visible)
    {
        if (!$visible && $this->examStarted && !$this->examCompleted) {
            $this->handleSecurityViolation('visibility_change', 'Tab switched or window minimized');
            $this->dispatch('forceFullscreen');
        }
    }

    public function handleBeforeUnload()
    {
        if ($this->examStarted && !$this->examCompleted) {
            $this->handleSecurityViolation('navigation_attempt', 'User attempted to leave page');
        }
    }

    public function startExam()
    {
        \Log::info('startExam method called', [
            'user_id' => Auth::id(),
            'assessment_id' => $this->assessment->id,
            'current_time' => now()->toDateTimeString()
        ]);

        try {
            $this->examStarted = true;
            $this->isFullscreenForced = true;
            $this->startTime = Carbon::now();
            $this->startQuestionTimer(); // Start tracking first question
            
            \Log::info('Exam started successfully', [
                'examStarted' => $this->examStarted,
                'startTime' => $this->startTime->toDateTimeString()
            ]);
            
            $this->dispatch('startTimer');
            $this->dispatch('markExamStarted');
            
        } catch (\Exception $e) {
            \Log::error('Error starting CBT exam', [
                'user_id' => Auth::id(),
                'assessment_id' => $this->assessment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            session()->flash('error', 'Failed to start exam. Please try again. Error: ' . $e->getMessage());
        }
    }

    public function saveAnswer($questionId, $answer)
    {
        try {
            if (is_numeric($answer)) {
                $answer = (int) $answer;
            }
            
            $this->answers[$questionId] = $answer;
            
            \Log::info('Answer saved', [
                'user_id' => Auth::id(),
                'question_id' => $questionId,
                'answer' => $answer
            ]);
            
            $this->dispatch('answerSaved', questionId: $questionId, answer: $answer);
            
        } catch (\Exception $e) {
            \Log::error('Error saving answer', [
                'user_id' => Auth::id(),
                'question_id' => $questionId,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function showSubmitConfirmation()
    {
        $this->showSubmitModal = true;
    }

    public function cancelSubmission()
    {
        $this->showSubmitModal = false;
    }

    public function submitExam()
    {
        if ($this->examCompleted) {
            return;
        }

        // Track final question time
        $this->trackQuestionTime($this->currentQuestionIndex);

        $this->showSubmitModal = false;

        \Log::info('Submitting exam', [
            'user_id' => Auth::id(),
            'assessment_id' => $this->assessment->id,
            'answers_count' => count(array_filter($this->answers, fn($a) => $a !== null))
        ]);

        try {
            $totalPoints = 0;
            $correctAnswers = 0;
            $totalQuestions = count($this->questions);
            $timeSpent = $this->startTime ? Carbon::now()->diffInSeconds($this->startTime) : 0;

            // Create detailed submission data
            $submissionData = [
                'progress_tracking' => $this->progressTracking,
                'security_violations' => $this->securityViolations,
                'flagged_questions' => $this->flaggedQuestions,
                'navigation_pattern' => $this->getNavigationPattern(),
                'time_analytics' => $this->getTimeAnalytics()
            ];

            foreach ($this->questions as $question) {
                $answer = $this->answers[$question['id']] ?? null;
                
                $studentAnswer = StudentAnswer::create([
                    'user_id' => Auth::id(),
                    'assessment_id' => $this->assessment->id,
                    'question_id' => $question['id'],
                    'attempt_number' => $this->attemptNumber,
                    'answer' => $answer,
                    'time_spent' => $timeSpent,
                    'submitted_at' => now(),
                    // Store additional tracking data
                    'question_time_spent' => $this->progressTracking['question_durations'][$this->getQuestionIndex($question['id'])] ?? 0,
                    'was_flagged' => in_array($question['id'], $this->flaggedQuestions)
                ]);

                // Auto-grade the answer
                $studentAnswer->autoGrade();
                $studentAnswer->refresh();

                if ($studentAnswer->is_correct) {
                    $correctAnswers++;
                }
                $totalPoints += $studentAnswer->points_earned ?? 0;
            }

            $maxPoints = collect($this->questions)->sum('points') ?: 100;
            $percentage = $maxPoints > 0 ? round(($totalPoints / $maxPoints) * 100, 1) : 0;
            $passed = $percentage >= $this->assessment->pass_percentage;

            $this->results = [
                'total_questions' => $totalQuestions,
                'correct_answers' => $correctAnswers,
                'total_points' => $totalPoints,
                'max_points' => $maxPoints,
                'percentage' => $percentage,
                'passed' => $passed,
                'attempt_number' => $this->attemptNumber,
                'time_spent' => $timeSpent,
                'security_violations' => count($this->securityViolations),
                'active_time' => $this->progressTracking['total_active_time'],
                'pause_count' => $this->progressTracking['pause_count'],
                'navigation_count' => $this->progressTracking['navigation_count']
            ];

            // Store detailed results for analytics
            $this->storeDetailedResults($submissionData);

            $this->examCompleted = true;
            $this->isFullscreenForced = false;
            $this->dispatch('examCompleted');
            $this->dispatch('allowFullscreenExit');
            
            \Log::info('Exam submitted successfully', [
                'results' => $this->results
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error submitting CBT exam', [
                'user_id' => Auth::id(),
                'assessment_id' => $this->assessment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            session()->flash('error', 'Failed to submit exam. Please contact support.');
            
            // Fallback results
            $this->results = [
                'total_questions' => count($this->questions),
                'correct_answers' => 0,
                'total_points' => 0,
                'max_points' => collect($this->questions)->sum('points') ?: 100,
                'percentage' => 0,
                'passed' => false,
                'attempt_number' => $this->attemptNumber,
                'time_spent' => 0,
                'security_violations' => count($this->securityViolations)
            ];
            
            $this->examCompleted = true;
            $this->isFullscreenForced = false;
            $this->dispatch('allowFullscreenExit');
        }
    }

    protected function getQuestionIndex($questionId)
    {
        foreach ($this->questions as $index => $question) {
            if ($question['id'] == $questionId) {
                return $index;
            }
        }
        return 0;
    }

    protected function getNavigationPattern()
    {
        // Return pattern of question navigation for analysis
        return [
            'total_navigations' => $this->progressTracking['navigation_count'],
            'back_navigations' => 0, // Could track this separately
            'jump_navigations' => 0, // Could track this separately
        ];
    }

    protected function getTimeAnalytics()
    {
        $durations = array_values($this->progressTracking['question_durations']);
        
        if (empty($durations)) {
            return [
                'avg_time_per_question' => 0,
                'min_time' => 0,
                'max_time' => 0,
                'total_active_time' => 0
            ];
        }

        return [
            'avg_time_per_question' => array_sum($durations) / count($durations),
            'min_time' => min($durations),
            'max_time' => max($durations),
            'total_active_time' => $this->progressTracking['total_active_time'],
            'question_times' => $this->progressTracking['question_durations']
        ];
    }

    protected function storeDetailedResults($submissionData)
    {
        // Store additional analytics data
        // This could be stored in a separate table for detailed analytics
        \Log::info('Detailed submission data', [
            'user_id' => Auth::id(),
            'assessment_id' => $this->assessment->id,
            'submission_data' => $submissionData
        ]);
    }

    // Progress tracking helper methods
    public function getProgressStats()
    {
        $answered = $this->getAnsweredQuestionsCount();
        $total = count($this->questions);
        $remaining = $total - $answered;
        
        $avgTimePerQuestion = 0;
        if ($this->progressTracking['total_active_time'] > 0 && $answered > 0) {
            $avgTimePerQuestion = $this->progressTracking['total_active_time'] / $answered;
        }
        
        $estimatedTimeRemaining = $remaining * $avgTimePerQuestion;
        
        return [
            'answered' => $answered,
            'total' => $total,
            'remaining' => $remaining,
            'percentage' => $total > 0 ? ($answered / $total) * 100 : 0,
            'avg_time_per_question' => $avgTimePerQuestion,
            'estimated_time_remaining' => $estimatedTimeRemaining,
            'total_active_time' => $this->progressTracking['total_active_time']
        ];
    }

    // Helper methods (existing)
    public function getCurrentQuestion()
    {
        if ($this->currentQuestionIndex >= 0 && $this->currentQuestionIndex < count($this->questions)) {
            return $this->questions[$this->currentQuestionIndex];
        }
        return null;
    }

    public function getAnsweredQuestionsCount()
    {
        return count(array_filter($this->answers, function ($answer) {
            return $answer !== null && $answer !== '';
        }));
    }

    public function isQuestionFlagged($questionIndex)
    {
        if (!isset($this->questions[$questionIndex])) {
            return false;
        }

        $questionId = $this->questions[$questionIndex]['id'];
        return in_array($questionId, $this->flaggedQuestions);
    }

    public function getProgressPercentage()
    {
        return count($this->questions) > 0 ? (($this->currentQuestionIndex + 1) / count($this->questions)) * 100 : 0;
    }

    public function canGoNext()
    {
        return $this->currentQuestionIndex < count($this->questions) - 1;
    }

    public function canGoPrevious()
    {
        return $this->currentQuestionIndex > 0;
    }

    public function isLastQuestion()
    {
        return $this->currentQuestionIndex === count($this->questions) - 1;
    }

    // Accessibility helper methods
    public function getEstimatedCompletionTime()
    {
        $stats = $this->getProgressStats();
        if ($stats['estimated_time_remaining'] > 0) {
            $finishTime = now()->addSeconds($stats['estimated_time_remaining']);
            return $finishTime->format('H:i');
        }
        return null;
    }

    public function getTimePerRemainingQuestion()
    {
        $remaining = count($this->questions) - $this->getAnsweredQuestionsCount();
        if ($remaining > 0 && $this->timeRemaining > 0) {
            return floor($this->timeRemaining / $remaining);
        }
        return 0;
    }
}