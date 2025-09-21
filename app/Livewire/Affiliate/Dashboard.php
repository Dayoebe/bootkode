<?php

// app/Livewire/Affiliate/Dashboard.php
namespace App\Livewire\Affiliate;

use Livewire\Component;
use App\Services\AffiliateService;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', ['title' => 'Affiliate Dashboard'])]
class Dashboard extends Component
{
    public $selectedPeriod = '30';
    
    private AffiliateService $affiliateService;

    public function boot(AffiliateService $affiliateService)
    {
        $this->affiliateService = $affiliateService;
    }

    public function mount()
    {
        $user = auth()->user();
        
        // Handle redirects in mount() method
        if (!$user->isAffiliate()) {
            if (!$user->canBecomeAffiliate()) {
                return redirect()->route('affiliate.not-eligible');
            }
            return redirect()->route('affiliate.apply');
        }
    }

    public function updatedSelectedPeriod()
    {
        // Refresh the component when period changes
        $this->render();
    }

    public function applyForAffiliate()
    {
        $user = auth()->user();
        
        if (!$user->canBecomeAffiliate()) {
            session()->flash('error', 'You are not eligible for the affiliate program.');
            return;
        }

        $affiliate = $user->applyForAffiliate();
        
        if ($affiliate) {
            session()->flash('success', 'Affiliate application submitted successfully!');
        } else {
            session()->flash('error', 'You already have an affiliate account or application failed.');
        }
    }

    public function render()
    {
        $user = auth()->user();
        $affiliate = $user->affiliate;
        $affiliateStats = $user->getAffiliateStats();
        $analytics = $this->affiliateService->getAffiliateAnalytics($affiliate, (int) $this->selectedPeriod);
        $monthlyPerformance = $user->getMonthlyAffiliatePerformance(6);
        $recentCommissions = $user->getCommissionTransactions(10);
        $referredActivity = $user->getReferredUsersActivity(5);

        return view('livewire.affiliate.dashboard', [
            'affiliateStats' => $affiliateStats,
            'analytics' => $analytics,
            'monthlyPerformance' => $monthlyPerformance,
            'recentCommissions' => $recentCommissions,
            'referredActivity' => $referredActivity
        ]);
    }
}