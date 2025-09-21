<?php
namespace App\Livewire\Affiliate;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', ['title' => 'Apply for Affiliate Program'])]
class Apply extends Component
{
    public function mount()
    {
        $user = auth()->user();
        
        // Redirect if already an affiliate
        if ($user->isAffiliate()) {
            return redirect()->route('affiliate.dashboard');
        }
        
        // Redirect if not eligible
        if (!$user->canBecomeAffiliate()) {
            return redirect()->route('affiliate.not-eligible');
        }
    }

    public function applyForAffiliate()
    {
        $user = auth()->user();
        
        if (!$user->canBecomeAffiliate()) {
            session()->flash('error', 'You are not eligible for the affiliate program.');
            return;
        }

        if ($user->isAffiliate()) {
            session()->flash('error', 'You already have an affiliate account.');
            return;
        }

        $affiliate = $user->applyForAffiliate();
        
        if ($affiliate) {
            session()->flash('success', 'Affiliate application submitted successfully!');
            return redirect()->route('affiliate.dashboard');
        } else {
            session()->flash('error', 'Application failed. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.affiliate.apply');
    }
}