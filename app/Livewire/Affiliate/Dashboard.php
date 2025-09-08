<?php

// app/Livewire/Affiliate/Dashboard.php
namespace App\Livewire\Affiliate;

use Livewire\Component;
use App\Services\AffiliateService;

class Dashboard extends Component
{
    public $selectedPeriod = '30';
    
    private AffiliateService $affiliateService;

    public function boot(AffiliateService $affiliateService)
    {
        $this->affiliateService = $affiliateService;
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
        
        // Check if user is affiliate or can become one
        if (!$user->isAffiliate()) {
            if (!$user->canBecomeAffiliate()) {
                return view('livewire.affiliate.not-eligible')->layout('layouts.dashboard');
            }
            return view('livewire.affiliate.apply')->layout('layouts.dashboard');
        }

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
        ])->layout('layouts.dashboard');
    }
}
