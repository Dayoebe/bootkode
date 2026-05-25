<?php

namespace App\Livewire\Pages;

use App\Services\CommercialReadinessService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app', [
    'title' => 'Pricing & Packages - BootKode',
    'description' => 'BootKode pricing for learners, career builders, institutions, and marketplace vendors.',
    'developer' => 'Bootkode',
    'developer_url' => 'https://bootkode.com',
])]
class Pricing extends Component
{
    public function render()
    {
        return view('livewire.pages.pricing', [
            'packages' => app(CommercialReadinessService::class)->publicPackages(),
        ]);
    }
}
