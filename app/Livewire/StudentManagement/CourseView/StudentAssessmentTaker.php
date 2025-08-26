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
    public $results = [];
    public $timeRemaining = null;
    public $attemptStarted = false;
    public $userAttempt = null;
    
    // Assessment states: 'list', 'taking', 'results'
    public $assessmentState = 'list';

    public function mount($lesson)
    {
        $this->lesson = $lesson;
        $this->loadAssessments();
    }

    protected function loadAssessments()
    {
        $this->assessments = Assessment::where('lesson_id', $this->lesson->id)
            ->with(['questions' => function($query) {
                $query->orderBy('order');
            }])
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
        
        // If showing results instead of starting
        if ($existingAttempt) {
            $this->showAssessmentResults($existingAttempt);
            return;
        }

        $this->initializeAssessment();
    }

    public function retakeAssessment($assessmentId = null)
    {
        if ($assessmentId) {
            $this->currentAssessment = Assessment::with(['questions' => function($query) {
                $query->orderBy('order');
            }])->findOrFail($assessmentId);
        }
        
        $this->initializeAssessment();
    }

    protected function initializeAssessment()
    {
        $this->questions = $this->currentAssessment->questions;
        $this->currentQuestionIndex = 0;
        $this->answers = [];
        $this->isSubmitted = false;
        $this->results = [];
        $this->timeRemaining = $this->currentAssessment->time_limit ? $this->currentAssessment->time_limit * 60 : null;
        $this->attemptStarted = true;
        
        $latestAttempt = $this->getUserLatestAttempt();
        $this->userAttempt = $latestAttempt ? $latestAttempt->attempt_number + 1 : 1;
        
        $this->assessmentState = 'taking';
        $this->dispatch('assessment-started');
    }

    protected function showAssessmentResults($latestAttempt)
    {
        $this->userAttempt = $latestAttempt->attempt_number;
        $this->calculateResults();
        $this->assessmentState = 'results';
    }

    public function backToAssessmentList()
    {
        $this->assessmentState = 'list';
        $this->currentAssessment = null;
        $this->questions = [];
        $this->answers = [];
        $this->results = [];
        $this->isSubmitted = false;
        $this->attemptStarted = false;
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

    public function getCurrentQuestion()
    {
        return $this->questions[$this->currentQuestionIndex] ?? null;
    }

    protected function getUserLatestAttempt()
    {
        return StudentAnswer::where('user_id', Auth::id())
            ->where('assessment_id', $this->currentAssessment->id)
            ->orderBy('attempt_number', 'desc')
            ->first();
    }

    public function getQuestionProgress()
    {
        if ($this->questions->isEmpty()) {
            return 0;
        }
        return round((($this->currentQuestionIndex + 1) / count($this->questions)) * 100);
    }

    public function isQuestionAnswered($questionId)
    {
        return isset($this->answers[$questionId]) && 
               $this->answers[$questionId] !== null && 
               $this->answers[$questionId] !== '';
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

    public function submitAssessment()
    {
        if ($this->isSubmitted) {
            return;
        }

        $this->isSubmitted = true;
        $this->saveAnswers();
        $this->calculateResults();
        $this->assessmentState = 'results';

        // Notify parent component about assessment completion
        $this->dispatch('assessment-completed')->to('student-management.course-view.lesson-content-viewer');
        
        $score = $this->results['percentage'] ?? 0;
        $passed = $this->results['passed'] ?? false;
        
        if ($passed) {
            $this->dispatch('notify', [
                'message' => "Assessment completed successfully! Your score: {$score}%",
                'type' => 'success'
            ]);
        } else {
            $this->dispatch('notify', [
                'message' => "Assessment completed. Score: {$score}%. You can retake this assessment.",
                'type' => 'warning'
            ]);
        }
    }

    protected function saveAnswers()
    {
        foreach ($this->questions as $question) {
            $userAnswer = $this->answers[$question->id] ?? null;
            
            if ($userAnswer === null || $userAnswer === '') {
                continue;
            }

            $isCorrect = $this->checkAnswer($question, $userAnswer);
            $pointsEarned = $isCorrect ? $question->points : 0;

            StudentAnswer::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'assessment_id' => $this->currentAssessment->id,
                    'question_id' => $question->id,
                    'attempt_number' => $this->userAttempt,
                ],
                [
                    'answer' => is_array($userAnswer) ? json_encode($userAnswer) : $userAnswer,
                    'is_correct' => $isCorrect,
                    'points_earned' => $pointsEarned,
                    'submitted_at' => now(),
                ]
            );
        }
    }

    protected function checkAnswer($question, $userAnswer)
    {
        switch ($question->question_type) {
            case 'multiple_choice':
                if ($question->hasMultipleCorrectAnswers()) {
                    // Handle multiple correct answers
                    $correctAnswers = is_array($question->correct_answer) 
                        ? $question->correct_answer 
                        : json_decode($question->correct_answer, true);
                    $userAnswers = is_array($userAnswer) ? $userAnswer : [$userAnswer];
                    
                    return empty(array_diff($correctAnswers, $userAnswers)) && 
                           empty(array_diff($userAnswers, $correctAnswers));
                } else {
                    return $userAnswer == $question->correct_answer;
                }
                
            case 'true_false':
                return $userAnswer == $question->correct_answer;
                
            case 'short_answer':
            case 'fill_blank':
                return strtolower(trim($userAnswer)) === strtolower(trim($question->correct_answer));
                
            case 'essay':
                // Essays need manual grading - return false for now
                return false;
                
            default:
                return false;
        }
    }

    protected function calculateResults()
    {
        $studentAnswers = StudentAnswer::where('user_id', Auth::id())
            ->where('assessment_id', $this->currentAssessment->id)
            ->where('attempt_number', $this->userAttempt)
            ->with('question')
            ->get()
            ->keyBy('question_id');

        $totalPoints = 0;
        $maxPoints = 0;
        $correctAnswers = 0;
        $totalQuestions = $this->questions->count();
        $answersData = [];

        foreach ($this->questions as $question) {
            $maxPoints += $question->points;
            $studentAnswer = $studentAnswers->get($question->id);
            
            if ($studentAnswer) {
                $totalPoints += $studentAnswer->points_earned;
                if ($studentAnswer->is_correct) {
                    $correctAnswers++;
                }
                
                // Format the answer for display
                $formattedAnswer = $this->formatAnswerForDisplay($question, $studentAnswer->answer);
                $studentAnswer->formatted_answer = $formattedAnswer;
                $answersData[$question->id] = $studentAnswer;
            }
        }

        $percentage = $maxPoints > 0 ? round(($totalPoints / $maxPoints) * 100, 1) : 0;
        $passed = $percentage >= $this->currentAssessment->pass_percentage;

        $this->results = [
            'passed' => $passed,
            'percentage' => $percentage,
            'correct_answers' => $correctAnswers,
            'total_questions' => $totalQuestions,
            'total_points' => $totalPoints,
            'max_points' => $maxPoints,
            'answers' => $answersData,
        ];
    }

    protected function formatAnswerForDisplay($question, $answer)
    {
        switch ($question->question_type) {
            case 'multiple_choice':
            case 'true_false':
                $options = json_decode($question->options, true) ?? [];
                if (is_array($answer)) {
                    $formattedAnswers = [];
                    foreach (json_decode($answer, true) as $index) {
                        if (isset($options[$index])) {
                            $formattedAnswers[] = chr(65 + $index) . '. ' . $options[$index];
                        }
                    }
                    return implode(', ', $formattedAnswers);
                } else {
                    return isset($options[$answer]) ? chr(65 + $answer) . '. ' . $options[$answer] : $answer;
                }
                
            default:
                return $answer;
        }
    }

    public function render()
    {
        return view('livewire.student-management.course-view.student-assessment-taker');
    }
}