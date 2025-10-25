<?php

namespace App\Livewire\Cbt;

use Livewire\Component;
use App\Models\Assessment\Assessment;
use App\Models\Assessment\StudentAnswer;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', ['title' => 'View CBT Exam', 'description' => 'View CBT exams', 'icon' => 'fas fa-microphone-alt'])]
class CbtViewer extends Component
{
    use WithPagination;

    public $selectedAssessment = null;
    public $selectedAttempt = null;
    public $viewDetails = false;

    public function render()
    {
        // Get assessments the user has attempted
        $userAssessments = Assessment::where('type', 'quiz')
            ->whereHas('studentAnswers', function($query) {
                $query->where('user_id', Auth::id());
            })
            ->with(['studentAnswers' => function($query) {
                $query->where('user_id', Auth::id());
            }])
            ->paginate(10);

        return view('livewire.cbt.cbt-viewer', compact('userAssessments'));
    }

    public function viewAssessmentDetails($assessmentId)
    {
        $this->selectedAssessment = Assessment::with([
            'studentAnswers' => function($query) {
                $query->where('user_id', Auth::id())
                      ->with('question')
                      ->orderBy('attempt_number', 'desc');
            },
            'questions'
        ])->findOrFail($assessmentId);

        $this->viewDetails = true;
    }

    public function viewAttemptDetails($attemptNumber)
    {
        $this->selectedAttempt = $this->selectedAssessment->getStudentResults(Auth::id(), $attemptNumber);
    }

    public function closeDetails()
    {
        $this->viewDetails = false;
        $this->selectedAssessment = null;
        $this->selectedAttempt = null;
    }

    public function getAttemptsForAssessment($assessment)
    {
        return $assessment->studentAnswers
            ->groupBy('attempt_number')
            ->map(function($answers, $attemptNumber) {
                $totalPoints = $answers->sum('points_earned');
                $maxPoints = $answers->first()->assessment->max_score;
                $percentage = $maxPoints > 0 ? round(($totalPoints / $maxPoints) * 100, 1) : 0;
                $passed = $percentage >= $answers->first()->assessment->pass_percentage;

                return [
                    'attempt_number' => $attemptNumber,
                    'total_points' => $totalPoints,
                    'max_points' => $maxPoints,
                    'percentage' => $percentage,
                    'passed' => $passed,
                    'submitted_at' => $answers->first()->submitted_at,
                    'answers_count' => $answers->count()
                ];
            })
            ->sortByDesc('attempt_number')
            ->values();
    }
}