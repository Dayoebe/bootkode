<?php

namespace App\Livewire\Component\CBT;

use Livewire\Component;
use App\Models\CbtResult;
use Illuminate\Support\Facades\Auth;

class ViewCbtResults extends Component
{
    public $results = [];

    public function mount()
    {
        if (!Auth::user()->hasRole(User::ROLE_STUDENT)) {
            abort(403, 'Unauthorized access to CBT Results.');
        }

        $this->loadResults();
    }

    public function loadResults()
    {
        $this->results = CbtResult::where('user_id', Auth::id())
            ->with('exam')
            ->orderBy('completed_at', 'desc')
            ->get()
            ->map(function ($result) {
                return [
                    'exam_title' => $result->exam->title,
                    'score' => $result->score,
                    'total_marks' => $result->total_marks,
                    'percentage' => ($result->total_marks > 0) ? ($result->score / $result->total_marks * 100) : 0,
                    'passed' => $result->passed,
                    'completed_at' => $result->completed_at ? $result->completed_at->format('M d, Y h:i A') : 'N/A',
                ];
            })->toArray();
    }

    public function render()
    {
        return view('livewire.component.cbt.view-cbt-results')
            ->layout('layouts.student', [
                'title' => 'View CBT Results',
                'description' => 'Review your CBT exam results',
                'icon' => 'fas fa-chart-bar',
                'active' => 'cbt.results',
            ]);
    }
}