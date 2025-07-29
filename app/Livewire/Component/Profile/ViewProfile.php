<?php

namespace App\Livewire\Component\Profile;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ViewProfile extends Component
{
    public $user;

    public function mount()
    {
        $this->user = Auth::user();
    }

    public function render()
    {
        return view('livewire.component.profile.view')
            ->layout('layouts.dashboard', [
                'title' => 'View Profile'
            ]);
    }
}