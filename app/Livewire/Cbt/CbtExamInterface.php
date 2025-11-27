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

            if ($this->assessment->shuffle_options && !empty($questionArray['options'])) {
                $originalOptions = $questionArray['options'];
                $originalCorrectAnswers = $questionArray['correct_answers'] ?? [];
                
                $indexedOptions = [];
                foreach ($originalOptions as $index => $option) {
                    $indexedOptions[] = [
                        'original_index' => $index,
                        'text' => $option
                    ];
                }
                
                shuffle($indexedOptions);
                
                $indexMapping = [];
                $newOptions = [];
                foreach ($indexedOptions as $newIndex => $item) {
                    $indexMapping[$item['original_index']] = $newIndex;
                    $newOptions[$newIndex] = $item['text'];
                }
                
                $newCorrectAnswers = [];
                foreach ($originalCorrectAnswers as $correctAnswer) {
                    if (isset($indexMapping[$correctAnswer])) {
                        $newCorrectAnswers[] = $indexMapping[$correctAnswer];
                    }
                }
                
                $questionArray['options'] = $newOptions;
                $questionArray['correct_answers'] = $newCorrectAnswers;
                $questionArray['original_correct_answers'] = $originalCorrectAnswers;
                $questionArray['option_mapping'] = $indexMapping;
            }

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

            return $questionArray;
        })->values()->toArray();
        
        $this->questionOrder = collect($this->questions)->pluck('id')->toArray();
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

                $studentAnswer = StudentAnswer::create([
                    'user_id' => Auth::id(),
                    'assessment_id' => $this->assessment->id,
                    'question_id' => $questionId,
                    'attempt_number' => $this->attemptNumber,
                    'answer' => $userAnswer,
                    'time_spent_seconds' => $timeSpent,
                    'submitted_at' => now(),
                    'question_order' => $this->questionOrder,
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