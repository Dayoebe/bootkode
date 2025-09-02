<?php

namespace App\Livewire\Cbt;

use Livewire\Component;
use App\Models\Assessment;
use App\Models\Question;
use App\Models\StudentAnswer;
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

    public function mount(Assessment $assessment)
    {
        // Verify assessment exists and has questions
        if (!$assessment || $assessment->questions->count() === 0) {
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
        // Process questions and ensure options are properly formatted
        $this->questions = $this->assessment->questions->map(function($question) {
            $questionArray = $question->toArray();
            
            // Handle options - ensure it's always an array
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
    }

    public function startExam()
    {
        $this->examStarted = true;
        $this->startTime = Carbon::now();
        $this->dispatch('startTimer');
        $this->dispatch('markExamStarted'); // For JavaScript security monitoring
    }

    public function saveAnswer($questionId, $answer)
    {
        $this->answers[$questionId] = $answer;
        
        // Auto-save progress
        $this->dispatch('answerSaved', $questionId, $answer);
    }

    public function nextQuestion()
    {
        if ($this->currentQuestionIndex < count($this->questions) - 1) {
            $this->currentQuestionIndex++;
        }
    }

    public function previousQuestion()
    {
        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
        }
    }

    public function goToQuestion($index)
    {
        if ($index >= 0 && $index < count($this->questions)) {
            $this->currentQuestionIndex = $index;
        }
    }

    public function toggleFlag($questionIndex)
    {
        if (!isset($this->questions[$questionIndex])) {
            return;
        }
        
        $questionId = $this->questions[$questionIndex]['id'];
        
        if (in_array($questionId, $this->flaggedQuestions)) {
            $this->flaggedQuestions = array_filter($this->flaggedQuestions, function($id) use ($questionId) {
                return $id !== $questionId;
            });
        } else {
            $this->flaggedQuestions[] = $questionId;
        }
    }

    public function submitExam()
    {
        if ($this->examCompleted) {
            return;
        }

        $totalPoints = 0;
        $correctAnswers = 0;
        $totalQuestions = count($this->questions);
        $timeSpent = $this->startTime ? Carbon::now()->diffInSeconds($this->startTime) : 0;

        foreach ($this->questions as $question) {
            $answer = $this->answers[$question['id']] ?? null;
            
            // Create student answer record
            $studentAnswer = StudentAnswer::create([
                'user_id' => Auth::id(),
                'assessment_id' => $this->assessment->id,
                'question_id' => $question['id'],
                'attempt_number' => $this->attemptNumber,
                'answer' => $answer,
                'time_spent' => $timeSpent,
                'submitted_at' => now(),
            ]);

            // Auto-grade the answer
            $studentAnswer->autoGrade();
            $studentAnswer->refresh();

            if ($studentAnswer->is_correct) {
                $correctAnswers++;
            }
            $totalPoints += $studentAnswer->points_earned;
        }

        $maxPoints = collect($this->questions)->sum('points');
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
            'security_violations' => count($this->securityViolations)
        ];

        $this->examCompleted = true;
        $this->dispatch('examCompleted');
    }

    public function retakeExam()
    {
        return redirect()->route('cbt.exams');
    }

    public function handleSecurityViolation($type, $details = null)
    {
        $violation = [
            'type' => $type,
            'details' => $details,
            'timestamp' => Carbon::now(),
            'question_index' => $this->currentQuestionIndex
        ];

        $this->securityViolations[] = $violation;

        // Log security violation (you can save to database if needed)
        \Log::warning('CBT Security Violation', [
            'user_id' => Auth::id(),
            'assessment_id' => $this->assessment->id,
            'violation' => $violation
        ]);

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
        }
    }

    public function handleBeforeUnload()
    {
        if ($this->examStarted && !$this->examCompleted) {
            // Save current progress
            $this->handleSecurityViolation('navigation_attempt', 'User attempted to leave page');
        }
    }

    // Helper methods
    public function getCurrentQuestion()
    {
        if ($this->currentQuestionIndex >= 0 && $this->currentQuestionIndex < count($this->questions)) {
            return $this->questions[$this->currentQuestionIndex];
        }
        return null;
    }

    public function getAnsweredQuestionsCount()
    {
        return count(array_filter($this->answers, function($answer) {
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
}