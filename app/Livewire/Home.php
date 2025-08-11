<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use PharIo\Manifest\Author;
use Livewire\Attributes\Layout;

#[Layout('layouts.app', ['title' => 'Home', 'description' => "Empowering Africa's youth with digital skills, mentorship & careers.", 'developer' => 'Bootkode', 'developer_url' => 'https://bootkode.com'])]


class Home extends Component
{
    /**
     * Render the component view.
     */
    public function render()
    {
        return view('livewire.home');
    }
}
