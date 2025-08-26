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


    































































// Add these methods to your StudentAssessmentTaker.php Livewire component

/**
 * Clear all previous attempts for the current assessment
 */
public function clearPreviousAttempts($assessmentId = null)
{
    if ($assessmentId) {
        $this->currentAssessment = Assessment::findOrFail($assessmentId);
    }

    if (!$this->currentAssessment) {
        $this->dispatch('notify', [
            'message' => 'No assessment selected.',
            'type' => 'error'
        ]);
        return;
    }

    // Delete all previous attempts for this user and assessment
    $deletedCount = StudentAnswer::where('user_id', Auth::id())
        ->where('assessment_id', $this->currentAssessment->id)
        ->delete();

    // Reset component state
    $this->assessmentState = 'list';
    $this->currentAssessment = null;
    $this->questions = [];
    $this->answers = [];
    $this->results = [];
    $this->isSubmitted = false;
    $this->attemptStarted = false;
    $this->userAttempt = null;

    // Refresh assessments list to update status
    $this->loadAssessments();

    $this->dispatch('notify', [
        'message' => "Previous attempts cleared successfully. ({$deletedCount} records deleted)",
        'type' => 'success'
    ]);
}

/**
 * Fixed formatAnswerForDisplay method
 */
protected function formatAnswerForDisplay($question, $answer)
{
    switch ($question->question_type) {
        case 'multiple_choice':
            $options = json_decode($question->options, true) ?? [];
            
            // Handle JSON string answers
            if (is_string($answer) && $this->isJson($answer)) {
                $answer = json_decode($answer, true);
            }
            
            if (is_array($answer)) {
                // Multiple selections
                $formattedAnswers = [];
                foreach ($answer as $index) {
                    $index = (int) $index;
                    if (isset($options[$index])) {
                        $formattedAnswers[] = chr(65 + $index) . '. ' . $options[$index];
                    }
                }
                return !empty($formattedAnswers) ? implode(', ', $formattedAnswers) : 'No answer selected';
            } else {
                // Single selection
                $index = (int) $answer;
                return isset($options[$index]) ? chr(65 + $index) . '. ' . $options[$index] : 'Invalid selection';
            }

        case 'true_false':
            $options = json_decode($question->options, true) ?? ['True', 'False'];
            $index = (int) $answer;
            return isset($options[$index]) ? $options[$index] : 'Invalid selection';

        case 'short_answer':
        case 'fill_blank':
        case 'essay':
            return is_string($answer) ? $answer : (is_array($answer) ? implode(' ', $answer) : (string) $answer);

        default:
            return is_array($answer) ? json_encode($answer) : (string) $answer;
    }
}

/**
 * Helper method to check if string is valid JSON
 */
protected function isJson($string)
{
    if (!is_string($string)) {
        return false;
    }
    json_decode($string);
    return json_last_error() === JSON_ERROR_NONE;
}

/**
 * Enhanced calculateResults method with better answer handling
 */
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
    $totalQuestions = count($this->questions);
    $answersData = [];

    foreach ($this->questions as $question) {
        $maxPoints += $question->points;
        $studentAnswer = $studentAnswers->get($question->id);

        if ($studentAnswer) {
            $totalPoints += $studentAnswer->points_earned;
            if ($studentAnswer->is_correct) {
                $correctAnswers++;
            }

            // Create a copy of the student answer for manipulation
            $answerCopy = clone $studentAnswer;
            
            // Format the answer for display
            $answerCopy->formatted_answer = $this->formatAnswerForDisplay($question, $studentAnswer->answer);
            
            // Add formatted correct answer
            $question->formatted_correct_answer = $this->getFormattedCorrectAnswer($question);
            
            // Store the raw answer for option comparison
            $answerCopy->raw_answer = $studentAnswer->answer;
            
            $answersData[$question->id] = $answerCopy;
        } else {
            // Handle unanswered questions
            $dummyAnswer = new \stdClass();
            $dummyAnswer->formatted_answer = 'Not answered';
            $dummyAnswer->is_correct = false;
            $dummyAnswer->points_earned = 0;
            $dummyAnswer->raw_answer = null;
            $question->formatted_correct_answer = $this->getFormattedCorrectAnswer($question);
            $answersData[$question->id] = $dummyAnswer;
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

/**
 * Get formatted correct answer for display
 */
protected function getFormattedCorrectAnswer($question)
{
    $correctAnswers = json_decode($question->correct_answers, true) ?? [];
    
    if (empty($correctAnswers)) {
        return 'No correct answer set';
    }

    switch ($question->question_type) {
        case 'multiple_choice':
            $options = json_decode($question->options, true) ?? [];
            $formattedAnswers = [];
            
            foreach ($correctAnswers as $index) {
                $index = (int) $index;
                if (isset($options[$index])) {
                    $formattedAnswers[] = chr(65 + $index) . '. ' . $options[$index];
                }
            }
            
            return !empty($formattedAnswers) ? implode(', ', $formattedAnswers) : 'Invalid correct answer';

        case 'true_false':
            $options = json_decode($question->options, true) ?? ['True', 'False'];
            $index = (int) $correctAnswers[0];
            return isset($options[$index]) ? $options[$index] : 'Invalid correct answer';

        case 'short_answer':
        case 'fill_blank':
        case 'essay':
            return is_array($correctAnswers) ? implode(', ', $correctAnswers) : (string) $correctAnswers;

        default:
            return is_array($correctAnswers) ? implode(', ', $correctAnswers) : (string) $correctAnswers;
    }
}












































// Fixed saveAnswers method for StudentAssessmentTaker.php

protected function saveAnswers()
{
    foreach ($this->questions as $question) {
        $userAnswer = $this->answers[$question->id] ?? null;

        if ($userAnswer === null || $userAnswer === '') {
            continue;
        }

        // Store answer in consistent format for database
        $answerToStore = $userAnswer;
        
        // For multiple choice, always store as array in the answer column
        // The StudentAnswer model will cast it to JSON automatically
        if ($question->question_type === 'multiple_choice') {
            if (is_array($userAnswer)) {
                // Multiple selections - store as array of integers
                $answerToStore = array_map('intval', $userAnswer);
            } else {
                // Single selection - store as array with single integer
                $answerToStore = [(int) $userAnswer];
            }
        } elseif ($question->question_type === 'true_false') {
            // Store as single integer
            $answerToStore = (int) $userAnswer;
        }
        // For text answers, keep as string

        // Use the Question model's method to check correctness
        $isCorrect = $question->isCorrectAnswer($userAnswer);
        $pointsEarned = 0;

        if ($isCorrect === true) {
            $pointsEarned = $question->points;
        } elseif ($isCorrect === false) {
            $pointsEarned = 0;
        } else {
            // For partial credit (e.g., multiple correct answers)
            $pointsEarned = $question->calculatePartialCredit($userAnswer);
        }

        StudentAnswer::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'assessment_id' => $this->currentAssessment->id,
                'question_id' => $question->id,
                'attempt_number' => $this->userAttempt,
            ],
            [
                'answer' => $answerToStore,
                'is_correct' => $isCorrect === true,
                'points_earned' => $pointsEarned,
                'submitted_at' => now(),
            ]
        );
    }
}
    public function render()
    {
        return view('livewire.student-management.course-view.student-assessment-taker');
    }
}