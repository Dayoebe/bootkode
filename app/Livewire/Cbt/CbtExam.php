<?php

namespace App\Livewire\Cbt;

use Livewire\Component;
use App\Models\Assessment;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', ['title' => 'CBT Exams', 'description' => 'CBT examination system', 'icon' => 'fas fa-laptop-code'])]
class CbtExam extends Component
{
    public $assessmentId;

    public function mount($assessmentId = null)
    {
        // If specific assessment ID is provided, redirect to exam interface
        if ($assessmentId) {
            $assessment = Assessment::with('questions')->find($assessmentId);
            
            if (!$assessment) {
                session()->flash('error', 'Assessment not found.');
                return redirect()->route('cbt.exams');
            }

            if ($assessment->questions->count() === 0) {
                session()->flash('error', 'This assessment has no questions.');
                return redirect()->route('cbt.exams');
            }

            // Check if user can take the exam
            $userResult = $assessment->getStudentResults(Auth::id());
            if ($userResult && $userResult['passed']) {
                session()->flash('warning', 'You have already passed this assessment.');
                return redirect()->route('cbt.exams');
            }

            // Redirect to secure exam interface
            return redirect()->route('cbt.exam.take', ['assessment' => $assessmentId]);
        }

        // Otherwise redirect to selection page
        return redirect()->route('cbt.exams');
    }

    public function render()
    {
        // This component now just handles redirects
        // The actual views are handled by the new components
        return view('livewire.cbt.cbt-exam-redirect');
    }
}