<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;

class AffiliateAmbassadorDashboard extends Component
{
    public function render()
    {
        return view('livewire.dashboard.affiliate-ambassador-dashboard')
        ->layout('layouts.dashboard', ['title' => 'Affiliate/Ambassador Dashboard']);
    }
}
