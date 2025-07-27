<?php

namespace App\Livewire;

use Livewire\Component;

class Home extends Component
{
    /**
     * Render the component view.
     */
    public function render()
    {
        return view('livewire.home')
            ->layout('layouts.app', [
                'title' => 'Home',
                'description' => "Empowering Africa's youth with digital skills, mentorship & careers.",
            ]);
    }
}
