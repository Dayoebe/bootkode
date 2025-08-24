<?php

namespace App\Livewire\StudentManagement\CourseView;

use Livewire\Component;
use App\Models\Assessment;
use App\Models\Question;
use App\Models\StudentAnswer;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class StudentAssessmentTaker extends Component
{
    public $lesson;
    public $assessments = [];
    public $currentAssessment = null;
    public $questions = [];
    public $answers = [];
    public $currentQuestionIndex = 0;
    public $isSubmitted = false;
    public $showResults = false;
    public $results = [];
    public $timeRemaining = null;
    public $attemptStarted = false;
    public $userAttempt = null;
    
    // Assessment states: 'list', 'taking', 'results', 'completed'
    public $assessmentState = 'list';

    public function mount($lesson)
    {
        $this->lesson = $lesson;
        $this->loadAssessments();
    }

    protected function loadAssessments()
    {
        $this->assessments = Assessment::where('lesson_id', $this->lesson->id)
            ->with(['questions'])
            ->orderBy('order')
            ->get();
    }

    public function startAssessment($assessmentId)
    {
        $this->currentAssessment = Assessment::with(['questions' => function($query) {
            $query->orderBy('order');
        }])->findOrFail($assessmentId);

        // Check if user has already completed this assessment
        $existingAttempt = $this->getUserLatestAttempt();
        
        if ($existingAttempt && $this->currentAssessment->type === 'quiz') {
            $this->userAttempt = $existingAttempt;
            $this->loadExistingAnswers();
            $this->showResults = true;
            $this->assessmentState = 'results';
            return;
        }

        $this->questions = $this->currentAssessment->questions;
        $this->initializeAnswers();
        $this->currentQuestionIndex = 0;
        $this->attemptStarted = true;
        $this->isSubmitted = false;
        $this->showResults = false;
        $this->assessmentState = 'taking';
        
        // Set time limit if applicable
        if ($this->currentAssessment->estimated_duration_minutes) {
            $this->timeRemaining = $this->currentAssessment->estimated_duration_minutes * 60;
        }

        $this->dispatch('assessment-started');
    }

    protected function initializeAnswers()
    {
        $this->answers = [];
        foreach ($this->questions as $question) {
            $this->answers[$question->id] = null;
        }
    }

    protected function getUserLatestAttempt()
    {
        if (!$this->currentAssessment) return null;

        return StudentAnswer::where('user_id', Auth::id())
            ->where('assessment_id', $this->currentAssessment->id)
            ->orderBy('attempt_number', 'desc')
            ->first();
    }

    protected function loadExistingAnswers()
    {
        $studentAnswers = StudentAnswer::where('user_id', Auth::id())
            ->where('assessment_id', $this->currentAssessment->id)
            ->where('attempt_number', $this->userAttempt->attempt_number)
            ->with('question')
            ->get();

        $this->results = [
            'total_questions' => $this->currentAssessment->questions->count(),
            'correct_answers' => $studentAnswers->where('is_correct', true)->count(),
            'total_points' => $studentAnswers->sum('points_earned'),
            'max_points' => $this->currentAssessment->questions->sum('points'),
            'percentage' => 0,
            'passed' => false,
            'answers' => $studentAnswers->keyBy('question_id')
        ];

        if ($this->results['max_points'] > 0) {
            $this->results['percentage'] = round(($this->results['total_points'] / $this->results['max_points']) * 100, 1);
            $this->results['passed'] = $this->results['percentage'] >= $this->currentAssessment->pass_percentage;
        }
    }

    public function answerQuestion($questionId, $answer)
    {
        $this->answers[$questionId] = $answer;
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

    public function submitAssessment()
    {
        if (!$this->currentAssessment || $this->isSubmitted) {
            return;
        }

        $attemptNumber = $this->getNextAttemptNumber();
        $submittedAt = now();

        // Save all answers
        foreach ($this->questions as $question) {
            $answer = $this->answers[$question->id] ?? null;
            
            $studentAnswer = StudentAnswer::create([
                'user_id' => Auth::id(),
                'assessment_id' => $this->currentAssessment->id,
                'question_id' => $question->id,
                'attempt_number' => $attemptNumber,
                'answer' => is_array($answer) ? $answer : [$answer],
                'submitted_at' => $submittedAt,
                'time_spent' => $this->calculateTimeSpent()
            ]);

            // Auto-grade if possible
            $studentAnswer->autoGrade();
        }

        $this->isSubmitted = true;
        $this->userAttempt = StudentAnswer::where('user_id', Auth::id())
            ->where('assessment_id', $this->currentAssessment->id)
            ->where('attempt_number', $attemptNumber)
            ->first();

        $this->loadExistingAnswers();
        $this->showResults = true;
        $this->assessmentState = 'results';

        $this->dispatch('notify', [
            'message' => 'Assessment submitted successfully!',
            'type' => 'success'
        ]);

        $this->dispatch('assessment-completed', [
            'assessmentId' => $this->currentAssessment->id,
            'passed' => $this->results['passed']
        ]);
    }

    protected function getNextAttemptNumber()
    {
        $lastAttempt = StudentAnswer::where('user_id', Auth::id())
            ->where('assessment_id', $this->currentAssessment->id)
            ->max('attempt_number');

        return ($lastAttempt ?? 0) + 1;
    }

    protected function calculateTimeSpent()
    {
        if ($this->currentAssessment->estimated_duration_minutes && $this->timeRemaining) {
            return ($this->currentAssessment->estimated_duration_minutes * 60) - $this->timeRemaining;
        }
        return 0;
    }

    public function retakeAssessment()
    {
        // Reset state for retake
        $this->initializeAnswers();
        $this->currentQuestionIndex = 0;
        $this->isSubmitted = false;
        $this->showResults = false;
        $this->assessmentState = 'taking';
        $this->userAttempt = null;
        
        if ($this->currentAssessment->estimated_duration_minutes) {
            $this->timeRemaining = $this->currentAssessment->estimated_duration_minutes * 60;
        }
    }

    public function backToAssessmentList()
    {
        $this->currentAssessment = null;
        $this->questions = [];
        $this->answers = [];
        $this->currentQuestionIndex = 0;
        $this->isSubmitted = false;
        $this->showResults = false;
        $this->assessmentState = 'list';
        $this->userAttempt = null;
    }

    public function getCurrentQuestion()
    {
        return $this->questions[$this->currentQuestionIndex] ?? null;
    }

    public function getQuestionProgress()
    {
        if (empty($this->questions)) return 0;
        return round((($this->currentQuestionIndex + 1) / count($this->questions)) * 100);
    }

    public function isQuestionAnswered($questionId)
    {
        return isset($this->answers[$questionId]) && $this->answers[$questionId] !== null && $this->answers[$questionId] !== '';
    }

    public function getAnsweredQuestionsCount()
    {
        return collect($this->answers)->filter(function($answer) {
            return $answer !== null && $answer !== '';
        })->count();
    }

    public function canSubmitAssessment()
    {
        // For mandatory questions, all must be answered
        $requiredQuestions = $this->questions->where('is_required', true);
        foreach ($requiredQuestions as $question) {
            if (!$this->isQuestionAnswered($question->id)) {
                return false;
            }
        }
        return true;
    }

    #[On('timer-ended')]
    public function handleTimerEnd()
    {
        if ($this->assessmentState === 'taking' && !$this->isSubmitted) {
            $this->submitAssessment();
            $this->dispatch('notify', [
                'message' => 'Time limit reached. Assessment submitted automatically.',
                'type' => 'warning'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.student-management.course-view.student-assessment-taker');
    }
}