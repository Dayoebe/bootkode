<?php

namespace App\Livewire\Component;

use Livewire\Component;
use App\Models\Course; // Example

class DashboardOverview extends Component
{
    public function render()
    {
        $user = auth()->user();
        $enrolledCourses = $user->courses()->count(); // Example stat
        return view('livewire.component.dashboard-overview', compact('enrolledCourses'))
            ->layout('layouts.dashboard');
    }
}