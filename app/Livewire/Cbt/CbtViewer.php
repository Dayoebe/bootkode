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
        // Get ONLY standalone CBT assessments where user has attempted
        $userAssessments = Assessment::where('type', 'quiz')
            ->whereNull('section_id')
            ->whereNull('lesson_id')
            ->whereHas('studentAnswers', function($query) {
                $query->where('user_id', Auth::id())
                      ->whereNotNull('submitted_at');
            })
            ->with(['studentAnswers' => function($query) {
                $query->where('user_id', Auth::id())
                      ->whereNotNull('submitted_at')
                      ->with('question');
            }, 'questions'])
            ->paginate(10);

        \Log::info('CbtViewer render', [
            'user_id' => Auth::id(),
            'assessments_count' => $userAssessments->total(),
            'assessments' => $userAssessments->pluck('id', 'title')
        ]);

        return view('livewire.cbt.cbt-viewer', compact('userAssessments'));
    }

    public function viewAssessmentDetails($assessmentId)
    {
        \Log::info('Viewing assessment details', [
            'user_id' => Auth::id(),
            'assessment_id' => $assessmentId
        ]);

        $this->selectedAssessment = Assessment::with([
            'studentAnswers' => function($query) {
                $query->where('user_id', Auth::id())
                      ->whereNotNull('submitted_at')
                      ->with('question')
                      ->orderBy('attempt_number', 'desc');
            },
            'questions'
        ])->findOrFail($assessmentId);

        $this->viewDetails = true;

        \Log::info('Assessment loaded', [
            'assessment_id' => $this->selectedAssessment->id,
            'student_answers_count' => $this->selectedAssessment->studentAnswers->count(),
            'attempts' => $this->selectedAssessment->studentAnswers->pluck('attempt_number')->unique()->values()
        ]);
    }

    public function viewAttemptDetails($attemptNumber)
    {
        \Log::info('Viewing attempt details', [
            'user_id' => Auth::id(),
            'assessment_id' => $this->selectedAssessment->id,
            'attempt_number' => $attemptNumber
        ]);

        $this->selectedAttempt = $this->selectedAssessment->getStudentResults(Auth::id(), $attemptNumber);

        if (!$this->selectedAttempt) {
            \Log::warning('No results found for attempt', [
                'assessment_id' => $this->selectedAssessment->id,
                'attempt_number' => $attemptNumber
            ]);
            session()->flash('error', 'No results found for this attempt.');
        } else {
            \Log::info('Attempt details loaded', [
                'total_questions' => $this->selectedAttempt['total_questions'],
                'correct_answers' => $this->selectedAttempt['correct_answers'],
                'percentage' => $this->selectedAttempt['percentage']
            ]);
        }
    }

    public function closeDetails()
    {
        $this->viewDetails = false;
        $this->selectedAssessment = null;
        $this->selectedAttempt = null;
    }

    /**
     * Better attempt aggregation with proper grouping
     */
    public function getAttemptsForAssessment($assessment)
    {
        $attempts = $assessment->studentAnswers
            ->where('user_id', Auth::id())
            ->whereNotNull('submitted_at')
            ->groupBy('attempt_number')
            ->map(function($answers, $attemptNumber) use ($assessment) {
                // Calculate totals for this attempt
                $totalPoints = $answers->sum('points_earned');
                $correctAnswersCount = $answers->where('is_correct', true)->count();
                
                // Get max possible points
                $maxPoints = $assessment->questions->sum('points') ?: $assessment->max_score ?: 100;
                
                // Calculate percentage
                $percentage = $maxPoints > 0 ? round(($totalPoints / $maxPoints) * 100, 1) : 0;
                $passed = $percentage >= $assessment->pass_percentage;

                \Log::info('Processing attempt', [
                    'assessment_id' => $assessment->id,
                    'attempt_number' => $attemptNumber,
                    'total_points' => $totalPoints,
                    'max_points' => $maxPoints,
                    'percentage' => $percentage,
                    'correct_answers' => $correctAnswersCount,
                    'total_answers' => $answers->count()
                ]);

                return [
                    'attempt_number' => $attemptNumber,
                    'total_points' => $totalPoints,
                    'max_points' => $maxPoints,
                    'percentage' => $percentage,
                    'passed' => $passed,
                    'submitted_at' => $answers->first()->submitted_at,
                    'answers_count' => $answers->count(),
                    'correct_answers' => $correctAnswersCount
                ];
            })
            ->sortByDesc('attempt_number')
            ->values();

        \Log::info('Attempts aggregated', [
            'assessment_id' => $assessment->id,
            'attempts_count' => $attempts->count(),
            'attempts_data' => $attempts
        ]);

        return $attempts;
    }
}