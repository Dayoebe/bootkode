<?php

// app/Livewire/Affiliate/NotEligible.php
namespace App\Livewire\Affiliate;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', ['title' => 'Affiliate Program - Not Eligible'])]
class NotEligible extends Component
{
    public function render()
    {
        $user = auth()->user();
        
        // Redirect if already an affiliate
        if ($user->isAffiliate()) {
            return redirect()->route('affiliate.dashboard');
        }
        
        // Redirect if eligible to apply
        if ($user->canBecomeAffiliate()) {
            return redirect()->route('affiliate.apply');
        }
        
        return view('livewire.affiliate.not-eligible');
    }
}