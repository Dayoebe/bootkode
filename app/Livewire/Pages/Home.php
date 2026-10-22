<?php

namespace App\Livewire\Pages;

use App\Services\PublicCatalogService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Learn. Build. Grow.', 'description' => 'Practical technology education for learners in Nigeria and beyond.'])]
class Home extends Component
{
    public function render(PublicCatalogService $catalog)
    {
        return view('livewire.pages.home', [
            'stats' => $catalog->statistics(),
            'featuredCourses' => $catalog->courses()->latest('id')->limit(3)->get(),
        ]);
    }
}
