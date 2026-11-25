<?php

namespace App\Livewire\Career;

use Livewire\Component;
use App\Models\Mentorship\MockInterview;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use App\Services\Career\MockInterviewCompletionService;

#[Layout('layouts.dashboard')]
class StudentInterviewTaker extends Component
{
    public $interview;
    public $currentQuestionIndex = 0;
    public $currentAnswer = '';
    public $responses = [];
    public $timeRemaining;
    #[Locked]
    public ?int $startTime = null;
    
    public function mount($interview)
    {
        // Accept either ID or model
        $this->interview = is_numeric($interview) 
            ? MockInterview::findOrFail($interview)
            : $interview;
            
        // Check access
        if ($this->interview->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this interview.');
        }
        
        // Start interview if not started
        if (!$this->interview->started_at) {
            $this->interview->start();
        }
        
        $this->startTime = now()->timestamp;
        $this->timeRemaining = $this->interview->estimated_duration_minutes * 60;
        
        // Load existing responses if any
        $this->responses = $this->interview->user_responses ?? [];
    }
    
    public function submitAnswer()
    {
        $this->interview = MockInterview::where('user_id', Auth::id())
            ->findOrFail($this->interview->id);

        if (!isset($this->interview->questions[$this->currentQuestionIndex])) {
            return;
        }
        
        $question = $this->interview->questions[$this->currentQuestionIndex];
        $responseTime = $this->startTime !== null ? max(0, now()->timestamp - $this->startTime) : null;
        
        $this->responses[$question['id']] = [
            'question_id' => $question['id'],
            'answer' => $this->currentAnswer,
            'response_time' => $responseTime,
            'timestamp' => now()->toISOString(),
        ];
        
        // Save progress
        $this->interview->update(['user_responses' => $this->responses]);
        
        // Move to next question
        $this->currentQuestionIndex++;
        $this->currentAnswer = '';
        $this->startTime = now()->timestamp;
        
        // If last question, complete interview
        if ($this->currentQuestionIndex >= count($this->interview->questions)) {
            $this->completeInterview();
        }
    }
    
    public function previousQuestion()
    {
        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
            
            // Load previous answer if exists
            $question = $this->interview->questions[$this->currentQuestionIndex];
            $this->currentAnswer = $this->responses[$question['id']]['answer'] ?? '';
        }
    }
    
    public function completeInterview(MockInterviewCompletionService $completionService)
    {
        $this->interview = $completionService->completeForUser(
            $this->interview,
            (int) Auth::id(),
            $this->responses
        );
        
        session()->flash('message', 'Interview completed successfully!');
        
        return redirect()->route('interview.results', $this->interview->id);
    }
    
    public function render()
    {
        return view('livewire.career.student-interview-taker');
    }
}
