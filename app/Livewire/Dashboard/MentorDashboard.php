<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class MentorDashboard extends Component
{
    public function render()
    {
        return view('livewire.dashboard.mentor-dashboard')
        ->layout('layouts.dashboard', ['title' => 'Mentor Dashboard']);
    }
}
