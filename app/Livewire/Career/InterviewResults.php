<?php

namespace App\Livewire\Career;

use Livewire\Component;
use App\Models\Mentorship\MockInterview;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard')]
class InterviewResults extends Component
{
    public $interview;

    public function boot(): void
    {
        if ($this->interview instanceof MockInterview) {
            $this->authorizeViewingInterview();
        }
    }
    
    public function mount($interview)
    {
        $this->interview = is_numeric($interview)
            ? MockInterview::findOrFail($interview)
            : $interview;
            
        // Check access
        $this->authorizeViewingInterview();
        
        // Ensure interview is completed
        if (!$this->interview->isCompleted()) {
            return redirect()->route('user.interview')->with('error', 'Interview not completed yet.');
        }
    }
    
    public function retakeInterview()
    {
        abort_unless($this->interview->user_id === Auth::id(), 403);

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
        foreach (['overall_score', 'technical_score', 'communication_score', 'confidence_score', 'problem_solving_score', 'cultural_fit_score'] as $scoreField) {
            $retakeData[$scoreField] = null;
        }
        
        $retake = Auth::user()->mockInterviews()->create($retakeData);
        $this->interview->increment('retake_count');
        
        return redirect()->route('interview.take', $retake->id);
    }
    
    public function downloadReport()
    {
        $filename = 'interview_report_' . $this->interview->id . '_' . now()->format('Y_m_d_His') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Metric', 'Value']);
            fputcsv($handle, ['Title', $this->interview->title]);
            fputcsv($handle, ['Status', $this->interview->status]);
            fputcsv($handle, ['Type', $this->interview->type]);
            fputcsv($handle, ['Difficulty', $this->interview->difficulty_level]);
            fputcsv($handle, ['Overall Score', $this->interview->overall_score ?? 'Not assessed']);
            fputcsv($handle, ['Technical Score', $this->interview->technical_score ?? 'Not assessed']);
            fputcsv($handle, ['Communication Score', $this->interview->communication_score ?? 'Not assessed']);
            fputcsv($handle, ['Confidence Score', $this->interview->confidence_score ?? 'Not assessed']);
            fputcsv($handle, ['Problem Solving Score', $this->interview->problem_solving_score ?? 'Not assessed']);
            fputcsv($handle, ['Completed At', optional($this->interview->completed_at)->toDateTimeString()]);
            fputcsv($handle, []);

            foreach ([
                'Strengths' => $this->interview->strengths,
                'Weaknesses' => $this->interview->weaknesses,
                'Improvement Suggestions' => $this->interview->improvement_suggestions,
            ] as $section => $items) {
                fputcsv($handle, [$section, is_array($items) ? implode('; ', $items) : $items]);
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
    
    public function render()
    {
        return view('livewire.career.interview-results');
    }

    private function authorizeViewingInterview(): void
    {
        $user = Auth::user();

        abort_unless(
            $user && (
                $this->interview->user_id === $user->id
                || $user->hasAnyRole(['super_admin', 'academy_admin', 'mentor', 'instructor'])
            ),
            403,
            'Unauthorized access to this interview.'
        );
    }
}
