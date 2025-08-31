<?php

namespace App\Livewire\Cbt;

use App\Models\CbtExam;
use App\Models\CbtResult;
use App\Models\CbtAnswer;
use App\Models\Question;
use App\Models\Course;
use App\Models\UserAchievement;
use App\Notifications\CbtResultNotification;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

#[Layout('layouts.dashboard')]
class CbtCenter extends Component
{
    use WithPagination;

    // Main state
    public $activeTab = 'dashboard';
    public $selectedExam = null;
    public $currentSession = null;
    
    // Exam taking state
    public $examQuestions = [];
    public $currentQuestionIndex = 0;
    public $userAnswers = [];
    public $timeRemaining = 0;
    public $examStarted = false;
    public $examCompleted = false;
    public $flaggedQuestions = [];
    
    // Navigation and UI state
    public $showQuestionPalette = false;
    public $showInstructions = false;
    public $showConfirmSubmit = false;
    public $showResultDetail = null;
    
    // Filters and search
    public $searchTerm = '';
    public $filterType = 'all';
    public $filterDifficulty = 'all';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    // Exam management (for admins)
    public $showCreateExam = false;
    public $editingExam = null;
    
    protected $listeners = [
        'examTimerExpired' => 'handleTimerExpired',
        'refreshExams' => 'refreshComponent',
        'examCreated' => 'handleExamCreated',
    ];

    public function mount()
    {
        $this->loadInitialData();
    }

    public function loadInitialData()
    {
        // Check for any active sessions
        $activeSession = CbtResult::where('user_id', Auth::id())
            ->where('status', 'in_progress')
            ->with('exam')
            ->first();

        if ($activeSession) {
            $this->resumeSession($activeSession);
        }
    }

    // Tab Management
    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPagination();
        $this->resetExamState();
    }

    // Exam Discovery and Selection
    public function getAvailableExams()
    {
        $query = CbtExam::available()
            ->with(['course', 'creator'])
            ->withCount(['questions', 'results']);

        // Apply filters
        if ($this->searchTerm) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('exam_code', 'like', '%' . $this->searchTerm . '%');
            });
        }

        if ($this->filterType !== 'all') {
            $query->where('exam_type', $this->filterType);
        }

        if ($this->filterDifficulty !== 'all') {
            $query->where('difficulty_level', $this->filterDifficulty);
        }

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate(12);
    }

    public function getUserResults()
    {
        return CbtResult::where('user_id', Auth::id())
            ->with(['exam'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    // Exam Taking Methods
    public function startExam($examId)
    {
        $exam = CbtExam::with('questions.options')->find($examId);
        
        if (!$exam) {
            $this->addError('exam', 'Exam not found.');
            return;
        }

        [$canTake, $message] = $exam->canUserTake(Auth::user());
        
        if (!$canTake) {
            $this->addError('exam', $message);
            return;
        }

        DB::transaction(function () use ($exam) {
            // Create new session
            $this->currentSession = CbtResult::create([
                'cbt_exam_id' => $exam->id,
                'user_id' => Auth::id(),
                'session_id' => \Str::uuid(),
                'attempt_number' => $this->getNextAttemptNumber($exam->id),
                'started_at' => now(),
                'status' => 'in_progress',
                'total_questions' => $exam->questions->count(),
                'browser_info' => request()->header('User-Agent'),
                'ip_address' => request()->ip(),
            ]);

            // Prepare questions
            $questions = $exam->questions;
            if ($exam->randomize_questions) {
                $questions = $questions->shuffle();
            }

            $this->examQuestions = $questions->map(function ($question, $index) use ($exam) {
                $options = $question->options;
                if ($exam->randomize_options && is_array($options)) {
                    $options = collect($options)->shuffle()->values()->toArray();
                }

                return [
                    'id' => $question->id,
                    'text' => $question->question_text,
                    'type' => $question->question_type,
                    'options' => $options,
                    'points' => $question->pivot->points ?? $question->points,
                    'order' => $index + 1,
                    'explanation' => $question->explanation,
                ];
            })->toArray();

            // Initialize state
            $this->selectedExam = $exam;
            $this->timeRemaining = $exam->duration_minutes * 60;
            $this->examStarted = true;
            $this->currentQuestionIndex = 0;
            $this->userAnswers = [];
            $this->flaggedQuestions = [];
            
            // Initialize answers array
            foreach ($this->examQuestions as $question) {
                $this->userAnswers[$question['id']] = null;
            }
        });

        $this->dispatch('startExamTimer', $this->timeRemaining);
    }

    public function resumeSession($session)
    {
        $this->currentSession = $session;
        $this->selectedExam = $session->exam;
        $this->examStarted = true;
        $this->examCompleted = false;

        // Calculate remaining time
        $elapsed = now()->diffInSeconds($session->started_at);
        $totalTime = $this->selectedExam->duration_minutes * 60;
        $this->timeRemaining = max(0, $totalTime - $elapsed);

        if ($this->timeRemaining <= 0) {
            $this->handleTimerExpired();
            return;
        }

        // Load existing answers
        $existingAnswers = $session->answers()->with('question')->get();
        $this->userAnswers = $existingAnswers->pluck('selected_answer', 'question_id')->toArray();
        $this->flaggedQuestions = $existingAnswers->where('flagged_for_review', true)
            ->pluck('question_id')->toArray();

        // Prepare questions (maintaining original order for resume)
        $this->examQuestions = $this->selectedExam->questions->map(function ($question, $index) {
            return [
                'id' => $question->id,
                'text' => $question->question_text,
                'type' => $question->question_type,
                'options' => $question->options,
                'points' => $question->pivot->points ?? $question->points,
                'order' => $index + 1,
                'explanation' => $question->explanation,
            ];
        })->toArray();

        $this->dispatch('resumeExamTimer', $this->timeRemaining);
    }

    public function saveAnswer($questionId, $answer)
    {
        if (!$this->currentSession) {
            return;
        }

        $this->userAnswers[$questionId] = $answer;

        // Save to database
        CbtAnswer::updateOrCreate(
            [
                'cbt_result_id' => $this->currentSession->id,
                'question_id' => $questionId,
            ],
            [
                'selected_answer' => is_array($answer) ? $answer : [$answer],
                'answered_at' => now(),
                'time_spent_seconds' => 0, // Will be calculated later
                'answer_sequence' => $this->currentQuestionIndex + 1,
            ]
        );

        // Auto-advance if configured
        if (!$this->selectedExam->allow_navigation) {
            $this->nextQuestion();
        }
    }

    public function toggleFlag($questionId = null)
    {
        $questionId = $questionId ?? $this->examQuestions[$this->currentQuestionIndex]['id'];
        
        if (in_array($questionId, $this->flaggedQuestions)) {
            $this->flaggedQuestions = array_diff($this->flaggedQuestions, [$questionId]);
        } else {
            $this->flaggedQuestions[] = $questionId;
        }

        // Update in database
        CbtAnswer::where('cbt_result_id', $this->currentSession->id)
            ->where('question_id', $questionId)
            ->update(['flagged_for_review' => in_array($questionId, $this->flaggedQuestions)]);
    }

    public function navigateToQuestion($index)
    {
        if (!$this->selectedExam->allow_navigation) {
            return;
        }

        $this->currentQuestionIndex = max(0, min($index, count($this->examQuestions) - 1));
    }

    public function nextQuestion()
    {
        if ($this->currentQuestionIndex < count($this->examQuestions) - 1) {
            $this->currentQuestionIndex++;
        }
    }

    public function previousQuestion()
    {
        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
        }
    }

    public function submitExam()
    {
        if (!$this->currentSession) {
            return;
        }

        DB::transaction(function () {
            $this->calculateResults();
            $this->finalizeExam();
            $this->examStarted = false;
            $this->examCompleted = true;
        });
    }

    public function handleTimerExpired()
    {
        if ($this->currentSession && $this->examStarted) {
            DB::transaction(function () {
                $this->currentSession->update(['auto_submitted' => true]);
                $this->calculateResults();
                $this->finalizeExam();
            });

            $this->examStarted = false;
            $this->examCompleted = true;
            
            session()->flash('message', 'Exam time expired and has been auto-submitted.');
        }
    }

    private function calculateResults()
    {
        $totalQuestions = count($this->examQuestions);
        $answeredQuestions = 0;
        $correctAnswers = 0;
        $totalPoints = 0;
        $earnedPoints = 0;

        foreach ($this->examQuestions as $questionData) {
            $questionId = $questionData['id'];
            $userAnswer = $this->userAnswers[$questionId] ?? null;
            
            $totalPoints += $questionData['points'];

            if ($userAnswer !== null) {
                $answeredQuestions++;
                
                // Get the actual question model for correctness check
                $question = Question::find($questionId);
                if ($question) {
                    $isCorrect = $question->isCorrectAnswer($userAnswer);
                    $pointsAwarded = $isCorrect ? $questionData['points'] : 0;

                    if ($isCorrect) {
                        $correctAnswers++;
                    }
                    $earnedPoints += $pointsAwarded;

                    // Update the answer record
                    CbtAnswer::where('cbt_result_id', $this->currentSession->id)
                        ->where('question_id', $questionId)
                        ->update([
                            'is_correct' => $isCorrect,
                            'points_awarded' => $pointsAwarded,
                        ]);
                }
            }
        }

        $percentageScore = $totalPoints > 0 ? ($earnedPoints / $totalPoints) * 100 : 0;
        $passed = $percentageScore >= $this->selectedExam->pass_percentage;

        // Update session results
        $this->currentSession->update([
            'completed_at' => now(),
            'status' => 'completed',
            'total_questions' => $totalQuestions,
            'answered_questions' => $answeredQuestions,
            'correct_answers' => $correctAnswers,
            'wrong_answers' => $answeredQuestions - $correctAnswers,
            'unanswered_questions' => $totalQuestions - $answeredQuestions,
            'total_points' => $totalPoints,
            'points_earned' => $earnedPoints,
            'percentage_score' => $percentageScore,
            'passed' => $passed,
            'grade' => $this->currentSession->calculateGrade(),
            'time_spent_seconds' => ($this->selectedExam->duration_minutes * 60) - $this->timeRemaining,
            'certificate_eligible' => $passed && $this->selectedExam->exam_type === 'certification',
        ]);
    }

    private function finalizeExam()
    {
        // Update exam statistics
        $this->selectedExam->increment('attempts_count');
        
        // Update average score
        $avgScore = $this->selectedExam->results()
            ->where('status', 'completed')
            ->avg('percentage_score');
        $this->selectedExam->update(['average_score' => $avgScore]);

        // Award achievements
        $this->currentSession->updateAchievements();

        // Send email notification if enabled
        if ($this->selectedExam->email_results) {
            try {
                $this->currentSession->user->notify(new CbtResultNotification($this->currentSession));
                $this->currentSession->update([
                    'result_emailed' => true,
                    'result_emailed_at' => now(),
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send CBT result email: ' . $e->getMessage());
            }
        }

        $this->dispatch('examCompleted', $this->currentSession->id);
    }

    private function getNextAttemptNumber($examId)
    {
        return CbtResult::where('cbt_exam_id', $examId)
            ->where('user_id', Auth::id())
            ->max('attempt_number') + 1;
    }

    // Results and Analytics
    public function viewResult($resultId)
    {
        $result = CbtResult::with(['exam', 'answers.question'])
            ->find($resultId);

        if (!$result || ($result->user_id !== Auth::id() && !Auth::user()->hasRole(['super_admin', 'academy_admin', 'instructor']))) {
            $this->addError('result', 'Result not found or access denied.');
            return;
        }

        $this->showResultDetail = $result;

        // Mark as viewed
        if ($result->user_id === Auth::id() && !$result->result_viewed) {
            $result->update([
                'result_viewed' => true,
                'result_viewed_at' => now(),
            ]);
        }
    }

    public function closeResult()
    {
        $this->showResultDetail = null;
    }

    // Utility Methods
    public function resetExamState()
    {
        $this->selectedExam = null;
        $this->currentSession = null;
        $this->examQuestions = [];
        $this->currentQuestionIndex = 0;
        $this->userAnswers = [];
        $this->timeRemaining = 0;
        $this->examStarted = false;
        $this->examCompleted = false;
        $this->flaggedQuestions = [];
        $this->showQuestionPalette = false;
        $this->showInstructions = false;
        $this->showConfirmSubmit = false;
    }

    public function refreshComponent()
    {
        $this->resetPagination();
        $this->dispatch('$refresh');
    }

    // Computed Properties
    public function getProgressPercentage()
    {
        if (empty($this->examQuestions)) {
            return 0;
        }

        $answered = collect($this->userAnswers)->filter()->count();
        return ($answered / count($this->examQuestions)) * 100;
    }

    public function getQuestionStatus($questionId)
    {
        if (!isset($this->userAnswers[$questionId]) || $this->userAnswers[$questionId] === null) {
            return 'unanswered';
        }

        if (in_array($questionId, $this->flaggedQuestions)) {
            return 'flagged';
        }

        return 'answered';
    }

    public function getDashboardStats()
    {
        $user = Auth::user();
        
        return [
            'total_exams_taken' => CbtResult::where('user_id', $user->id)
                ->where('status', 'completed')->count(),
            'exams_passed' => CbtResult::where('user_id', $user->id)
                ->where('passed', true)->count(),
            'average_score' => round(CbtResult::where('user_id', $user->id)
                ->where('status', 'completed')->avg('percentage_score') ?? 0, 1),
            'total_study_time' => CbtResult::where('user_id', $user->id)
                ->sum('time_spent_seconds'),
            'available_exams' => CbtExam::available()->count(),
            'recent_achievements' => UserAchievement::getRecentAchievements($user->id, 5),
        ];
    }

    public function render()
    {
        $data = [
            'availableExams' => $this->activeTab === 'exams' ? $this->getAvailableExams() : collect(),
            'userResults' => $this->activeTab === 'results' ? $this->getUserResults() : collect(),
            'dashboardStats' => $this->activeTab === 'dashboard' ? $this->getDashboardStats() : [],
        ];

        return view('livewire.c-b-t.cbt-center', $data);
    }
}