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

        if ($type === 'app_switch' || $type === 'visibility_change') {
            $this->progressTracking['pause_count']++;
        }

        if ($type === 'fullscreen_exit') {
            $this->dispatch('forceFullscreen');
        }

        if (count($this->securityViolations) >= 10) {
            session()->flash('error', 'Too many security violations detected. Exam auto-submitted.');
            $this->submitExam();
        }
    }

    public function startExam()
    {
        \Log::info('startExam method called', [
            'user_id' => Auth::id(),
            'assessment_id' => $this->assessment->id
        ]);

        try {
            $this->examStarted = true;
            $this->isFullscreenForced = true;
            $this->startTime = Carbon::now();
            $this->startQuestionTimer();
            
            $this->dispatch('startTimer');
            $this->dispatch('markExamStarted');
            
            \Log::info('Exam started successfully', [
                'start_time' => $this->startTime,
                'user_id' => Auth::id()
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error starting CBT exam', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            session()->flash('error', 'Failed to start exam. Please try again.');
        }
    }

    /**
     * Save answer when user selects it
     */
    public function saveAnswer($questionId, $answer)
    {
        try {
            // Convert to integer
            $answer = (int)$answer;
            
            // Store in component state
            $this->answers[$questionId] = $answer;
            
            \Log::info('Answer saved', [
                'question_id' => $questionId,
                'answer' => $answer,
                'answer_type' => gettype($answer)
            ]);
            
            // Dispatch event
            $this->dispatch('answerSaved', questionId: $questionId, answer: $answer);
            
        } catch (\Exception $e) {
            \Log::error('Error saving answer', [
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

    /**
     * FIXED: Submit the exam with proper time tracking and grading
     */
    public function submitExam()
    {
        if ($this->examCompleted) {
            \Log::warning('Exam already completed', ['user_id' => Auth::id()]);
            return;
        }

        $this->trackQuestionTime($this->currentQuestionIndex);
        $this->showSubmitModal = false;

        try {
            // FIXED: Calculate actual time spent
            $timeSpent = 0;
            if ($this->startTime) {
                $timeSpent = Carbon::now()->diffInSeconds($this->startTime);
            }

            \Log::info('Starting exam submission', [
                'user_id' => Auth::id(),
                'assessment_id' => $this->assessment->id,
                'attempt_number' => $this->attemptNumber,
                'start_time' => $this->startTime,
                'time_spent_seconds' => $timeSpent,
                'answers_count' => count(array_filter($this->answers, fn($a) => $a !== null))
            ]);

            $totalPoints = 0;
            $correctAnswers = 0;
            $totalQuestions = count($this->questions);

            // Process each question
            foreach ($this->questions as $questionData) {
                $questionId = $questionData['id'];
                $userAnswer = $this->answers[$questionId] ?? null;

                \Log::info('Processing question', [
                    'question_id' => $questionId,
                    'user_answer' => $userAnswer,
                    'user_answer_type' => gettype($userAnswer)
                ]);

                // FIXED: Create student answer with proper data
                $studentAnswer = StudentAnswer::create([
                    'user_id' => Auth::id(),
                    'assessment_id' => $this->assessment->id,
                    'question_id' => $questionId,
                    'attempt_number' => $this->attemptNumber,
                    'answer' => $userAnswer, // Store as integer directly
                    'time_spent_seconds' => max(0, $timeSpent), // Ensure non-negative
                    'submitted_at' => now(),
                ]);

                \Log::info('StudentAnswer created', [
                    'id' => $studentAnswer->id,
                    'answer_stored' => $studentAnswer->answer,
                    'answer_type' => gettype($studentAnswer->answer)
                ]);

                // FIXED: Auto-grade the answer
                $graded = $studentAnswer->autoGrade();
                
                if ($graded) {
                    $studentAnswer->refresh();
                    
                    \Log::info('Question graded', [
                        'question_id' => $questionId,
                        'is_correct' => $studentAnswer->is_correct,
                        'points_earned' => $studentAnswer->points_earned
                    ]);

                    if ($studentAnswer->is_correct) {
                        $correctAnswers++;
                    }
                    $totalPoints += $studentAnswer->points_earned ?? 0;
                } else {
                    \Log::warning('Question not graded', [
                        'question_id' => $questionId,
                        'reason' => 'autoGrade returned false'
                    ]);
                }
            }

            // Calculate results
            $maxPoints = collect($this->questions)->sum('points') ?: $this->assessment->max_score ?: 100;
            $percentage = $maxPoints > 0 ? round(($totalPoints / $maxPoints) * 100, 1) : 0;
            $passed = $percentage >= $this->assessment->pass_percentage;

            \Log::info('Exam grading completed', [
                'user_id' => Auth::id(),
                'total_questions' => $totalQuestions,
                'correct_answers' => $correctAnswers,
                'total_points' => $totalPoints,
                'max_points' => $maxPoints,
                'percentage' => $percentage,
                'passed' => $passed,
                'time_spent' => $timeSpent
            ]);

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

            $this->examCompleted = true;
            $this->isFullscreenForced = false;
            $this->dispatch('examCompleted');
            $this->dispatch('allowFullscreenExit');
            
            \Log::info('Exam submission completed successfully', [
                'user_id' => Auth::id(),
                'results' => $this->results
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error submitting exam', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            session()->flash('error', 'Failed to submit exam: ' . $e->getMessage());
            
            $this->results = [
                'total_questions' => count($this->questions),
                'correct_answers' => 0,
                'total_points' => 0,
                'max_points' => 100,
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
        return [
            'total_navigations' => $this->progressTracking['navigation_count'],
            'back_navigations' => 0,
            'jump_navigations' => 0,
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