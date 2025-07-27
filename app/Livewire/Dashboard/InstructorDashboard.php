<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class InstructorDashboard extends Component
{
    public function render()
    {
        return view('livewire.dashboard.instructor-dashboard')
        ->layout('layouts.dashboard', ['title' => 'Instructor Dashboard']);
    }
}
