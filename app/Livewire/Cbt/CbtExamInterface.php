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
    use \App\Livewire\Concerns\RequiresAuthenticatedAccount;

    #[\Livewire\Attributes\Locked]
    public ?int $examSessionId = null;

    public function boot(): void
    {
        if ($this->assessment) {
            $assessment = $this->assessment instanceof Assessment ? $this->assessment : Assessment::findOrFail($this->assessment);
            app(\App\Services\CbtAttemptService::class)->authorize($assessment, Auth::user());
        }
    }

    #[\Livewire\Attributes\Locked]
    public $assessment;
    #[\Livewire\Attributes\Locked]
    public $questions;
    public $currentQuestionIndex = 0;
    public $answers = [];
    #[\Livewire\Attributes\Locked]
    public $timeRemaining;
    #[\Livewire\Attributes\Locked]
    public $examStarted = false;
    #[\Livewire\Attributes\Locked]
    public $examCompleted = false;
    #[\Livewire\Attributes\Locked]
    public $results = null;
    public $flaggedQuestions = [];
    #[\Livewire\Attributes\Locked]
    public $attemptNumber;
    #[\Livewire\Attributes\Locked]
    public $startTime;
    public $securityViolations = [];
    public $showSubmitModal = false;
    public $isFullscreenForced = false;
    #[\Livewire\Attributes\Locked]
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
        app(\App\Services\CbtAttemptService::class)->authorize($this->assessment, Auth::user());
        
        if (!$this->assessment || $this->assessment->questions->count() === 0) {
            session()->flash('error', 'Invalid assessment.');
            return redirect()->route('cbt.exams');
        }

        $this->loadQuestions();
        $this->initializeExam();
        [$canTake] = $this->assessment->canUserTakeAssessment(Auth::id());
        if (!$canTake) {
            $previous = \App\Models\Assessment\LearningSession::where('user_id', Auth::id())->where('activity_type', 'assessment')
                ->where('metadata->assessment_id', $this->assessment->id)->whereNotNull('ended_at')->latest('id')->first();
            if ($previous && isset($previous->metadata['results'])) {
                $this->examSessionId = $previous->id;
                $this->results = $previous->metadata['results'];
                $this->examStarted = $this->examCompleted = true;
            }
        }
    }

    public function render()
    {
        return view('livewire.cbt.cbt-exam-interface');
    }

    protected function loadQuestions()
    {
        $this->questions = app(\App\Services\CbtAttemptService::class)->questions($this->assessment);
        $this->questionOrder = collect($this->questions)->pluck('id')->all();
    }

    protected function initializeExam()
    {
        $this->timeRemaining = $this->assessment->estimated_duration_minutes * 60;
        $this->attemptNumber = $this->assessment->getNextAttemptNumber(Auth::id());
        foreach ($this->questions as $question) {
            $this->answers[$question['id']] = null;
        }
    }

    public function saveAnswer($questionId, $answer)
    {
        if ($this->examCompleted) return;
        abort_unless($this->examStarted && $this->examSessionId, 422);
        $value = in_array($answer, [null, '', 'null'], true) ? null : $answer;
        app(\App\Services\CbtAttemptService::class)->saveAnswer(Auth::user(), $this->assessment, $this->examSessionId, (int) $questionId, $value);
        $this->answers[$questionId] = $value;
    }

    public function submitExam()
    {
        if ($this->examCompleted) return;
        abort_unless($this->examStarted && $this->examSessionId, 422, 'Start the exam before submitting.');
        $this->results = app(\App\Services\CbtAttemptService::class)->submit(Auth::user(), $this->assessment, $this->examSessionId, $this->answers);
        $this->showSubmitModal = false;
        $this->examCompleted = true;
        $this->dispatch('examCompleted');
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
        if ($this->examStarted || $this->examCompleted) return;
        $session = app(\App\Services\CbtAttemptService::class)->start(Auth::user(), $this->assessment);
        $this->examSessionId = $session->id;
        $this->startTime = $session->started_at;
        $this->attemptNumber = $session->metadata['attempt'];
        $this->questions = $session->metadata['questions'];
        $this->answers = $session->metadata['answers'] ?? array_fill_keys(collect($this->questions)->pluck('id')->all(), null);
        $this->questionOrder = collect($this->questions)->pluck('id')->all();
        $this->timeRemaining = max(0, $session->metadata['duration_seconds'] - (int) $session->started_at->diffInSeconds(now(), false));
        $this->examStarted = true;
        $this->isFullscreenForced = true;
        $this->startQuestionTimer();
        $this->dispatch('startTimer');
        $this->dispatch('markExamStarted');
    }

    public function showSubmitConfirmation()
    {
        $this->showSubmitModal = true;
    }

    public function cancelSubmission()
    {
        $this->showSubmitModal = false;
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
