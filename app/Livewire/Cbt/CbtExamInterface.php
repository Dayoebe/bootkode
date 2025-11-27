<?php

namespace App\Livewire\Cbt;

use Livewire\Component;
use App\Models\Assessment\Assessment;
use App\Models\Assessment\Question;
use App\Models\Assessment\StudentAnswer;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use App\Jobs\SendExamResultsEmail;
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
    public $questionOrder = [];
    
    public $progressTracking = [
        'question_start_times' => [],
        'question_end_times' => [],
        'question_durations' => [],
        'total_active_time' => 0,
        'pause_count' => 0,
        'navigation_count' => 0
    ];

    public function mount($assessment)
    {
        $this->assessment = Assessment::with('questions')->findOrFail($assessment);
        
        if (!$this->assessment || $this->assessment->questions->count() === 0) {
            session()->flash('error', 'Invalid assessment or no questions available.');
            return redirect()->route('cbt.exams');
        }

        $this->loadQuestions();
        $this->initializeExam();
    }

    public function render()
    {
        return view('livewire.cbt.cbt-exam-interface');
    }
    protected function loadQuestions()
{
    $questionCollection = $this->assessment->questions;
    
    if ($this->assessment->shuffle_questions) {
        $questionCollection = $questionCollection->shuffle();
    }
    
    $this->questions = $questionCollection->map(function ($question) {
        $questionArray = $question->toArray();

        // Ensure options are properly formatted
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

        // Ensure correct_answers are properly formatted
        if (!isset($questionArray['correct_answers'])) {
            $correctAnswers = $question->correct_answers;
            if (is_string($correctAnswers)) {
                $decodedAnswers = json_decode($correctAnswers, true);
                $questionArray['correct_answers'] = is_array($decodedAnswers) ? $decodedAnswers : [];
            } elseif (!is_array($correctAnswers)) {
                $questionArray['correct_answers'] = [$correctAnswers];
            } else {
                $questionArray['correct_answers'] = $correctAnswers;
            }
        }

        // Store original correct answers for grading reference
        $questionArray['original_correct_answers'] = $questionArray['correct_answers'];
        
        // Handle option shuffling with proper mapping
        if ($this->assessment->shuffle_options && !empty($questionArray['options']) && $questionArray['question_type'] === 'multiple_choice') {
            $originalOptions = $questionArray['options'];
            $originalCorrectAnswers = $questionArray['correct_answers'];
            
            // Create indexed options with original positions
            $indexedOptions = [];
            foreach ($originalOptions as $index => $option) {
                $indexedOptions[] = [
                    'original_index' => $index,
                    'text' => $option
                ];
            }
            
            // Shuffle the options
            shuffle($indexedOptions);
            
            // Create mapping between new and original indices
            $indexMapping = [];
            $newOptions = [];
            $newCorrectAnswers = [];
            
            foreach ($indexedOptions as $newIndex => $item) {
                $originalIndex = $item['original_index'];
                $indexMapping[$newIndex] = $originalIndex; // Map: new_index => original_index
                $newOptions[$newIndex] = $item['text'];
                
                // If this original index was correct, mark the new index as correct
                if (in_array($originalIndex, $originalCorrectAnswers)) {
                    $newCorrectAnswers[] = $newIndex;
                }
            }
            
            // Update the question with shuffled data
            $questionArray['options'] = $newOptions;
            $questionArray['correct_answers'] = $newCorrectAnswers;
            $questionArray['original_correct_answers'] = $originalCorrectAnswers;
            $questionArray['option_mapping'] = $indexMapping; // For display to original mapping
            $questionArray['inverse_mapping'] = array_flip($indexMapping); // For original to display mapping (useful for grading)
            $questionArray['was_shuffled'] = true;
        } else {
            // No shuffling, ensure consistent data structure
            $questionArray['option_mapping'] = null;
            $questionArray['inverse_mapping'] = null;
            $questionArray['was_shuffled'] = false;
        }

        // For true/false questions, ensure correct_answers is properly formatted
        if ($questionArray['question_type'] === 'true_false') {
            if (is_array($questionArray['correct_answers'])) {
                // Ensure we're using integers
                $questionArray['correct_answers'] = array_map('intval', $questionArray['correct_answers']);
            } else {
                $questionArray['correct_answers'] = [intval($questionArray['correct_answers'])];
            }
        }

        // Ensure points is set and is numeric
        if (!isset($questionArray['points']) || !is_numeric($questionArray['points'])) {
            $questionArray['points'] = 1;
        } else {
            $questionArray['points'] = floatval($questionArray['points']);
        }

        return $questionArray;
    })->values()->toArray();
    
    $this->questionOrder = collect($this->questions)->pluck('id')->toArray();
    
    // Log for debugging
    \Log::info('Questions loaded for assessment', [
        'assessment_id' => $this->assessment->id,
        'total_questions' => count($this->questions),
        'shuffle_questions' => $this->assessment->shuffle_questions,
        'shuffle_options' => $this->assessment->shuffle_options,
        'questions_sample' => collect($this->questions)->take(2)->map(function($q) {
            return [
                'id' => $q['id'],
                'type' => $q['question_type'],
                'correct_answers' => $q['correct_answers'],
                'original_correct_answers' => $q['original_correct_answers'] ?? null,
                'was_shuffled' => $q['was_shuffled'] ?? false,
                'options_count' => count($q['options'] ?? [])
            ];
        })->toArray()
    ]);
}

 
    protected function initializeExam()
    {
        $this->timeRemaining = $this->assessment->estimated_duration_minutes * 60;
        $this->attemptNumber = $this->assessment->getNextAttemptNumber(Auth::id());

        foreach ($this->questions as $question) {
            $this->answers[$question['id']] = null;
        }

        $this->progressTracking = [
            'question_start_times' => [],
            'question_end_times' => [],
            'question_durations' => [],
            'total_active_time' => 0,
            'pause_count' => 0,
            'navigation_count' => 0,
            'current_question_start' => null
        ];
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
        try {
            $this->examStarted = true;
            $this->isFullscreenForced = true;
            $this->startTime = Carbon::now();
            $this->startQuestionTimer();
            
            $this->dispatch('startTimer');
            $this->dispatch('markExamStarted');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to start exam. Please try again.');
        }
    }

    public function saveAnswer($questionId, $answer)
    {
        try {
            if ($answer === null || $answer === '' || $answer === 'null') {
                $this->answers[$questionId] = null;
                return;
            }
            
            $answer = (int)$answer;
            $this->answers[$questionId] = $answer;
            $this->dispatch('answerSaved', questionId: $questionId, answer: $answer);
            
        } catch (\Exception $e) {
            // Silent fail - don't disrupt student experience
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

    $this->trackQuestionTime($this->currentQuestionIndex);
    $this->showSubmitModal = false;

    try {
        $timeSpent = 0;
        if ($this->startTime) {
            $timeSpent = abs($this->startTime->diffInSeconds(Carbon::now(), false));
        }

        $totalPoints = 0;
        $correctAnswers = 0;
        $totalQuestions = count($this->questions);
        $answeredQuestions = 0;

        foreach ($this->questions as $questionData) {
            $questionId = $questionData['id'];
            $userAnswer = $this->answers[$questionId] ?? null;

            if ($userAnswer === null || $userAnswer === '' || $userAnswer === 'null') {
                continue;
            }

            $answeredQuestions++;

            // Store exam data for grading
            $examData = [
                'was_shuffled' => $questionData['was_shuffled'] ?? false,
                'option_mapping' => $questionData['option_mapping'] ?? null,
                'inverse_mapping' => $questionData['inverse_mapping'] ?? null,
                'original_correct_answers' => $questionData['original_correct_answers'] ?? null,
                'shuffled_correct_answers' => $questionData['correct_answers'] ?? null,
                'question_index' => array_search($questionId, $this->questionOrder)
            ];

            $studentAnswer = StudentAnswer::create([
                'user_id' => Auth::id(),
                'assessment_id' => $this->assessment->id,
                'question_id' => $questionId,
                'attempt_number' => $this->attemptNumber,
                'answer' => $userAnswer,
                'time_spent_seconds' => $timeSpent,
                'submitted_at' => now(),
                'question_order' => $this->questionOrder,
                'exam_data' => $examData, // Store shuffling metadata
            ]);

            $graded = $studentAnswer->autoGrade();
            
            if ($graded) {
                $studentAnswer->refresh();

                if ($studentAnswer->is_correct) {
                    $correctAnswers++;
                }
                $totalPoints += $studentAnswer->points_earned ?? 0;
            }
        }

        $maxPoints = collect($this->questions)->sum('points') ?: $this->assessment->max_score ?: 100;
        $percentage = $maxPoints > 0 ? round(($totalPoints / $maxPoints) * 100, 1) : 0;
        $passed = $percentage >= $this->assessment->pass_percentage;

        $this->results = [
            'total_questions' => $totalQuestions,
            'answered_questions' => $answeredQuestions,
            'unanswered_questions' => $totalQuestions - $answeredQuestions,
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
            'navigation_count' => $this->progressTracking['navigation_count'],
            'questions_shuffled' => $this->assessment->shuffle_questions,
            'options_shuffled' => $this->assessment->shuffle_options,
        ];

        $this->examCompleted = true;
        $this->isFullscreenForced = false;
        $this->dispatch('examCompleted');
        $this->dispatch('allowFullscreenExit');
        
    } catch (\Exception $e) {
        session()->flash('error', 'Failed to submit exam. Please contact support.');
        
        $this->results = [
            'total_questions' => count($this->questions),
            'answered_questions' => 0,
            'unanswered_questions' => count($this->questions),
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

    public function formatTimeSpent($seconds)
    {
        $seconds = abs($seconds);
        
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
        }
        return sprintf('%02d:%02d', $minutes, $secs);
    }

    public function showSummary()
    {
        return view('livewire.cbt.exam.cbt-exam-summary', [
            'questions' => $this->questions,
            'answers' => $this->answers,
            'currentQuestionIndex' => $this->currentQuestionIndex,
            'flaggedQuestions' => $this->flaggedQuestions,
            'timeRemaining' => $this->timeRemaining,
        ]);
    }

    protected function sendResultsEmail()
    {
        try {
            SendExamResultsEmail::dispatch(
                Auth::user(),
                $this->assessment,
                $this->attemptNumber,
                $this->results
            );
            
            session()->flash('message', 'Results will be sent to your email shortly!');
            
        } catch (\Exception $e) {
            // Silent fail - email not critical
        }
    }
}