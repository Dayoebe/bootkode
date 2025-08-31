<?php

namespace App\Livewire\Cbt;

use App\Models\CbtExam;
use App\Models\CbtResult;
use App\Models\CbtAnswer;
use App\Models\Question;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

#[Layout('layouts.exam')]
class CbtExamViewer extends Component
{
    public $exam;
    public $currentSession;
    public $examQuestions = [];
    public $currentQuestionIndex = 0;
    public $userAnswers = [];
    public $timeRemaining = 0;
    public $examState = 'pre_start'; // pre_start, in_progress, completed, expired
    public $flaggedQuestions = [];
    public $showQuestionNavigation = false;
    public $showSubmitConfirmation = false;
    public $showResultModal = false;
    public $showReviewModal = false;
    
    // Security tracking
    public $tabSwitchCount = 0;
    public $securityWarnings = [];
    public $browserInfo = [];
    public $startTime = null;

    protected $listeners = [
        'examTimerTick' => 'updateTimer',
        'examTimerExpired' => 'autoSubmitExam',
        'saveAnswer' => 'saveAnswer',
        'toggleQuestionFlag' => 'toggleQuestionFlag',
        'visibilityChanged' => 'handleVisibilityChange',
        'beforeUnload' => 'handleBeforeUnload'
    ];

    public function mount($exam)
    {
        $this->exam = CbtExam::with(['questions' => function($query) {
            $query->orderByPivot('order');
        }, 'course'])->findOrFail($exam);

        // Security check - ensure user can take exam
        [$canTake, $reason] = $this->exam->canUserTake(Auth::user());
        if (!$canTake) {
            abort(403, $reason);
        }

        // Check for existing active session
        $activeSession = CbtResult::where('cbt_exam_id', $this->exam->id)
            ->where('user_id', Auth::id())
            ->where('status', 'in_progress')
            ->first();

        if ($activeSession) {
            $this->loadExistingSession($activeSession);
        } else {
            $this->examState = 'pre_start';
        }

        // Collect browser info for security
        $this->browserInfo = [
            'user_agent' => request()->userAgent(),
            'ip_address' => request()->ip(),
            'screen_resolution' => null, // Will be filled by JS
            'timezone' => null, // Will be filled by JS
        ];
    }

    private function loadExistingSession($session)
    {
        $this->currentSession = $session;
        $this->examState = 'in_progress';
        
        // Calculate remaining time
        $elapsed = now()->diffInSeconds($session->started_at);
        $totalTime = $this->exam->duration_minutes * 60;
        $this->timeRemaining = max(0, $totalTime - $elapsed);
        
        if ($this->timeRemaining <= 0) {
            $this->autoSubmitExam();
            return;
        }

        // Load questions and answers
        $this->loadExamQuestions();
        $this->loadUserAnswers();
        $this->loadFlaggedQuestions();
    }

    public function startExam()
    {
        DB::transaction(function () {
            // Get next attempt number
            $attemptNumber = CbtResult::where('cbt_exam_id', $this->exam->id)
                ->where('user_id', Auth::id())
                ->max('attempt_number') + 1;

            // Create new session
            $this->currentSession = CbtResult::create([
                'cbt_exam_id' => $this->exam->id,
                'user_id' => Auth::id(),
                'session_id' => Str::uuid(),
                'attempt_number' => $attemptNumber,
                'started_at' => now(),
                'total_questions' => $this->exam->total_questions,
                'status' => 'in_progress',
                'browser_info' => $this->browserInfo,
                'ip_address' => request()->ip(),
            ]);

            // Set timer
            $this->timeRemaining = $this->exam->duration_minutes * 60;
            $this->startTime = now();
            
            // Load questions
            $this->loadExamQuestions();
            
            $this->examState = 'in_progress';
        });

        $this->dispatch('startExamTimer', ['timeRemaining' => $this->timeRemaining]);
    }

    private function loadExamQuestions()
    {
        $questions = $this->exam->questions()->get();
        
        // Randomize if enabled
        if ($this->exam->randomize_questions) {
            $questions = $questions->shuffle();
        }

        $this->examQuestions = $questions->map(function ($question) {
            $questionData = [
                'id' => $question->id,
                'text' => $question->question_text,
                'type' => $question->question_type,
                'points' => $question->pivot->points ?? 1,
                'difficulty' => $question->difficulty_level,
                'media_url' => $question->media_url,
                'media_type' => $question->media_type,
            ];

            // Add options for multiple choice questions
            if ($question->question_type === 'multiple_choice') {
                $options = $question->options ?? [];
                
                // Randomize options if enabled
                if ($this->exam->randomize_options && !empty($options)) {
                    // Preserve correct answer mapping
                    $correctAnswer = $question->correct_answer;
                    $optionPairs = array_map(fn($i) => ['index' => $i, 'text' => $options[$i]], array_keys($options));
                    shuffle($optionPairs);
                    
                    $questionData['options'] = array_column($optionPairs, 'text');
                    $questionData['option_mapping'] = array_column($optionPairs, 'index');
                } else {
                    $questionData['options'] = $options;
                }
            }

            return $questionData;
        })->toArray();
    }

    private function loadUserAnswers()
    {
        if (!$this->currentSession) return;

        $answers = CbtAnswer::where('cbt_result_id', $this->currentSession->id)->get();
        
        foreach ($answers as $answer) {
            $this->userAnswers[$answer->question_id] = $answer->selected_answer ?? $answer->text_answer;
        }
    }

    private function loadFlaggedQuestions()
    {
        if (!$this->currentSession) return;

        $flagged = CbtAnswer::where('cbt_result_id', $this->currentSession->id)
            ->where('flagged_for_review', true)
            ->pluck('question_id')
            ->toArray();

        $this->flaggedQuestions = $flagged;
    }

    public function saveAnswer($questionId, $answer)
    {
        if ($this->examState !== 'in_progress' || !$this->currentSession) {
            return;
        }

        DB::transaction(function () use ($questionId, $answer) {
            CbtAnswer::updateOrCreate(
                [
                    'cbt_result_id' => $this->currentSession->id,
                    'question_id' => $questionId
                ],
                [
                    'selected_answer' => is_string($answer) ? null : $answer,
                    'text_answer' => is_string($answer) ? $answer : null,
                    'answered_at' => now(),
                    'answer_sequence' => $this->getAnswerSequence(),
                ]
            );

            // Update session progress
            $this->currentSession->update([
                'answered_questions' => $this->getAnsweredCount(),
                'time_spent_seconds' => now()->diffInSeconds($this->currentSession->started_at),
            ]);
        });

        $this->userAnswers[$questionId] = $answer;
    }

    public function toggleQuestionFlag($questionId)
    {
        if ($this->examState !== 'in_progress' || !$this->currentSession) {
            return;
        }

        if (in_array($questionId, $this->flaggedQuestions)) {
            $this->flaggedQuestions = array_diff($this->flaggedQuestions, [$questionId]);
            $flagged = false;
        } else {
            $this->flaggedQuestions[] = $questionId;
            $flagged = true;
        }

        // Update in database
        CbtAnswer::updateOrCreate(
            [
                'cbt_result_id' => $this->currentSession->id,
                'question_id' => $questionId
            ],
            [
                'flagged_for_review' => $flagged,
                'answered_at' => now(),
            ]
        );
    }

    public function updateTimer($timeRemaining)
    {
        $this->timeRemaining = $timeRemaining;
        
        if ($this->currentSession) {
            $this->currentSession->update([
                'time_remaining_seconds' => $timeRemaining,
                'time_spent_seconds' => now()->diffInSeconds($this->currentSession->started_at),
            ]);
        }
    }

    public function submitExam()
    {
        if ($this->examState !== 'in_progress' || !$this->currentSession) {
            return;
        }

        $this->showSubmitConfirmation = false;

        DB::transaction(function () {
            $this->calculateResults();
            
            $this->currentSession->update([
                'submitted_at' => now(),
                'completed_at' => now(),
                'status' => 'completed',
                'time_spent_seconds' => now()->diffInSeconds($this->currentSession->started_at),
                'time_remaining_seconds' => $this->timeRemaining,
                'auto_submitted' => false,
            ]);
        });

        $this->examState = 'completed';
        $this->dispatch('examCompleted');

        if ($this->exam->show_results_immediately) {
            $this->showResultModal = true;
        }
    }

    public function autoSubmitExam()
    {
        if ($this->examState !== 'in_progress' || !$this->currentSession) {
            return;
        }

        DB::transaction(function () {
            $this->calculateResults();
            
            $this->currentSession->update([
                'submitted_at' => now(),
                'completed_at' => now(),
                'status' => 'completed',
                'time_spent_seconds' => $this->exam->duration_minutes * 60,
                'time_remaining_seconds' => 0,
                'auto_submitted' => true,
            ]);
        });

        $this->timeRemaining = 0;
        $this->examState = 'expired';
        
        if ($this->exam->show_results_immediately) {
            $this->showResultModal = true;
        }
    }

    private function calculateResults()
    {
        $answers = CbtAnswer::where('cbt_result_id', $this->currentSession->id)->get();
        $correctAnswers = 0;
        $wrongAnswers = 0;
        $pointsEarned = 0;
        $totalPoints = 0;

        foreach ($this->examQuestions as $examQuestion) {
            $question = Question::find($examQuestion['id']);
            $userAnswer = $answers->where('question_id', $question->id)->first();
            $questionPoints = $examQuestion['points'];
            $totalPoints += $questionPoints;

            if ($userAnswer) {
                $isCorrect = $this->checkAnswerCorrectness($question, $userAnswer);
                
                $userAnswer->update([
                    'is_correct' => $isCorrect,
                    'points_awarded' => $isCorrect ? $questionPoints : 0,
                ]);

                if ($isCorrect) {
                    $correctAnswers++;
                    $pointsEarned += $questionPoints;
                } else {
                    $wrongAnswers++;
                }
            } else {
                // Create empty answer record for unanswered questions
                CbtAnswer::create([
                    'cbt_result_id' => $this->currentSession->id,
                    'question_id' => $question->id,
                    'is_correct' => false,
                    'points_awarded' => 0,
                ]);
            }
        }

        $unansweredQuestions = $this->exam->total_questions - ($correctAnswers + $wrongAnswers);
        $percentageScore = $totalPoints > 0 ? ($pointsEarned / $totalPoints) * 100 : 0;
        $passed = $percentageScore >= $this->exam->pass_percentage;

        $this->currentSession->update([
            'correct_answers' => $correctAnswers,
            'wrong_answers' => $wrongAnswers,
            'unanswered_questions' => $unansweredQuestions,
            'total_points' => $totalPoints,
            'points_earned' => $pointsEarned,
            'percentage_score' => $percentageScore,
            'passed' => $passed,
            'grade' => $this->calculateGrade($percentageScore),
            'answer_sequence' => $this->getCompleteAnswerSequence(),
        ]);
    }

    private function checkAnswerCorrectness($question, $userAnswer)
    {
        $userResponse = $userAnswer->selected_answer ?? $userAnswer->text_answer;
        
        switch ($question->question_type) {
            case 'multiple_choice':
                return $userResponse == $question->correct_answer;
                
            case 'true_false':
                return $userResponse === $question->correct_answer;
                
            case 'essay':
            case 'fill_blank':
                // For now, mark as incorrect - these should be manually graded
                return false;
                
            default:
                return false;
        }
    }

    private function calculateGrade($score)
    {
        if ($score >= 90) return 'A';
        if ($score >= 80) return 'B';
        if ($score >= 70) return 'C';
        if ($score >= 60) return 'D';
        return 'F';
    }

    public function handleVisibilityChange($isVisible)
    {
        if (!$isVisible && $this->examState === 'in_progress') {
            $this->tabSwitchCount++;
            $this->securityWarnings[] = [
                'type' => 'tab_switch',
                'timestamp' => now(),
                'count' => $this->tabSwitchCount
            ];

            if ($this->tabSwitchCount >= 3) {
                $this->addSecurityWarning('Multiple tab switches detected. Exam may be terminated.');
            }
        }
    }

    public function handleBeforeUnload()
    {
        if ($this->examState === 'in_progress' && $this->currentSession) {
            // Save current progress
            $this->currentSession->update([
                'time_spent_seconds' => now()->diffInSeconds($this->currentSession->started_at),
                'time_remaining_seconds' => $this->timeRemaining,
            ]);
        }
    }

    private function addSecurityWarning($message)
    {
        $this->securityWarnings[] = [
            'message' => $message,
            'timestamp' => now(),
        ];
    }

    public function getAnsweredCount()
    {
        return count(array_filter($this->userAnswers, fn($answer) => $answer !== null && $answer !== ''));
    }

    private function getAnswerSequence()
    {
        return $this->currentQuestionIndex + 1;
    }

    private function getCompleteAnswerSequence()
    {
        $sequence = [];
        foreach ($this->userAnswers as $questionId => $answer) {
            if ($answer !== null && $answer !== '') {
                $sequence[] = $questionId;
            }
        }
        return $sequence;
    }

    public function finishExam()
    {
        return redirect()->route('cbt.exams');
    }

    public function closeResultModal()
    {
        $this->showResultModal = false;
    }

    public function render()
    {
        return view('livewire.c-b-t.cbt-exam-viewer');
    }
}