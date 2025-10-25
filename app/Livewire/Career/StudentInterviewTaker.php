<?php

namespace App\Livewire\Career;

use Livewire\Component;
use App\Models\Mentorship\Mentorship\MockInterview;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard')]
class StudentInterviewTaker extends Component
{
    public $interview;
    public $currentQuestionIndex = 0;
    public $currentAnswer = '';
    public $responses = [];
    public $timeRemaining;
    public $startTime;
    
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
        
        $this->startTime = now();
        $this->timeRemaining = $this->interview->estimated_duration_minutes * 60;
        
        // Load existing responses if any
        $this->responses = $this->interview->user_responses ?? [];
    }
    
    public function submitAnswer()
    {
        if (!isset($this->interview->questions[$this->currentQuestionIndex])) {
            return;
        }
        
        $question = $this->interview->questions[$this->currentQuestionIndex];
        $responseTime = now()->diffInSeconds($this->startTime);
        
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
    
    public function completeInterview()
    {
        $scores = $this->calculateScores();
        
        $this->interview->complete($this->responses, $scores);
        
        session()->flash('message', 'Interview completed successfully!');
        
        return redirect()->route('interview.results', $this->interview->id);
    }
    
    private function calculateScores()
    {
        $totalQuestions = count($this->interview->questions);
        $answeredQuestions = count($this->responses);
        
        $completionRate = ($answeredQuestions / $totalQuestions) * 100;
        $averageResponseTime = collect($this->responses)->avg('response_time') ?? 0;
        
        // Basic scoring - can be enhanced
        $technicalScore = min(100, $completionRate * 0.8 + (120 - min($averageResponseTime, 120)) / 120 * 20);
        $communicationScore = rand(70, 95); // Placeholder
        $confidenceScore = rand(65, 90); // Placeholder
        
        return [
            'overall_score' => ($technicalScore + $communicationScore + $confidenceScore) / 3,
            'technical_score' => $technicalScore,
            'communication_score' => $communicationScore,
            'confidence_score' => $confidenceScore,
            'completion_rate' => $completionRate,
            'avg_response_time' => $averageResponseTime,
        ];
    }
    
    public function render()
    {
        return view('livewire.career.student-interview-taker');
    }
}