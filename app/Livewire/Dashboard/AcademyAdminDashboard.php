<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class AcademyAdminDashboard extends Component
{
    public function render()
    {
        return view('livewire.dashboard.academy-admin-dashboard')
        ->layout('layouts.dashboard', ['title' => 'Academy Admin Dashboard']);
    }
}

