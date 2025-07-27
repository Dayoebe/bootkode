<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class ContentEditorDashboard extends Component
{
    public function render()
    {
        return view('livewire.dashboard.content-editor-dashboard')
        ->layout('layouts.dashboard', ['title' => 'Content Editor Dashboard']);
    }
}
