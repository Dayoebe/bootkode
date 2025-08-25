<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use PharIo\Manifest\Author;
use Livewire\Attributes\Layout;

#[Layout('layouts.app', ['title' => 'Home', 'description' => "Empowering Africa's youth with digital skills, mentorship & careers.", 'developer' => 'Bootkode', 'developer_url' => 'https://bootkode.com'])]


class Guideline extends Component
{
    public function render()
    {
        return view('livewire.pages.guideline');
    }
}
