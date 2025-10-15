<?php

namespace App\Livewire\Career;

use Livewire\Component;
use App\Models\MockInterview;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard')]
class InterviewResults extends Component
{
    public $interview;
    
    public function mount($interview)
    {
        $this->interview = is_numeric($interview)
            ? MockInterview::findOrFail($interview)
            : $interview;
            
        // Check access
        if ($this->interview->user_id !== Auth::id() && !Auth::user()->canManageCourses()) {
            abort(403, 'Unauthorized access to this interview.');
        }
        
        // Ensure interview is completed
        if (!$this->interview->isCompleted()) {
            return redirect()->route('user.interview')->with('error', 'Interview not completed yet.');
        }
    }
    
    public function retakeInterview()
    {
        if (!$this->interview->allow_retakes || $this->interview->retake_count >= $this->interview->max_retakes) {
            session()->flash('error', 'Retakes not allowed or maximum retakes reached.');
            return;
        }
        
        // Create retake
        $retakeData = $this->interview->toArray();
        unset($retakeData['id'], $retakeData['created_at'], $retakeData['updated_at']);
        $retakeData['title'] = $this->interview->title . ' (Retake ' . ($this->interview->retake_count + 1) . ')';
        $retakeData['original_interview_id'] = $this->interview->id;
        $retakeData['status'] = 'scheduled';
        $retakeData['started_at'] = null;
        $retakeData['completed_at'] = null;
        $retakeData['user_responses'] = null;
        $retakeData['overall_score'] = null;
        
        $retake = Auth::user()->mockInterviews()->create($retakeData);
        $this->interview->increment('retake_count');
        
        return redirect()->route('interview.take', $retake->id);
    }
    
    public function downloadReport()
    {
        // Generate PDF report
        session()->flash('message', 'Report download feature coming soon!');
    }
    
    public function render()
    {
        return view('livewire.career.interview-results');
    }
}