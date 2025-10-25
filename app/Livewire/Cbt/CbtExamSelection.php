<?php

namespace App\Livewire\Cbt;

use Livewire\Component;
use App\Models\Assessment\Assessment;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', ['title' => 'CBT Exams', 'description' => 'Select a CBT exam', 'icon' => 'fas fa-laptop-code'])]
class CbtExamSelection extends Component
{
    public function render()
    {
        $availableAssessments = Assessment::where('type', 'quiz')
            ->with(['questions', 'course'])
            ->whereHas('questions') // Only show assessments that have questions
            ->get()
            ->map(function($assessment) {
                // Check if user has attempted this assessment
                $userResult = $assessment->getStudentResults(Auth::id());
                $assessment->user_result = $userResult;
                $assessment->can_retake = !$userResult || !$userResult['passed'];
                $assessment->attempts_count = $assessment->studentAnswers()
                    ->where('user_id', Auth::id())
                    ->distinct('attempt_number')
                    ->count();
                
                return $assessment;
            });

        return view('livewire.cbt.cbt-exam-selection', compact('availableAssessments'));
    }

    public function startExam($assessmentId)
    {
        $assessment = Assessment::with('questions')->find($assessmentId);
        
        if (!$assessment || $assessment->questions->count() === 0) {
            session()->flash('error', 'Assessment not found or has no questions.');
            return;
        }

        // Check if user can take the exam
        $userResult = $assessment->getStudentResults(Auth::id());
        if ($userResult && $userResult['passed']) {
            session()->flash('warning', 'You have already passed this assessment.');
            return;
        }

        // Redirect to secure exam interface
        return redirect()->route('cbt.exam.take', ['assessment' => $assessmentId]);
    }

    public function viewResults($assessmentId)
    {
        $assessment = Assessment::find($assessmentId);
        if (!$assessment) {
            session()->flash('error', 'Assessment not found.');
            return;
        }

        $userResult = $assessment->getStudentResults(Auth::id());
        if (!$userResult) {
            session()->flash('error', 'No results found for this assessment.');
            return;
        }

        // You can redirect to a results page or show a modal
        session()->flash('results', $userResult);
    }
}