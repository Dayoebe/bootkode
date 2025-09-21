<?php

// app/Livewire/Affiliate/Settings.php
namespace App\Livewire\Affiliate;

use Livewire\Component;
use App\Models\Affiliate;
use App\Models\User;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', ['title' => 'Affiliate Settings'])]


class Settings extends Component
{
    public $globalCommissionRate = 30.00;
    public $minimumWithdrawal = 1000.00;
    public $autoApproveInstructors = true;
    public $selectedAffiliate = null;
    public $bulkAction = '';
    public $selectedAffiliates = [];

    public function mount()
    {
        // Load current settings
        $this->globalCommissionRate = config('affiliate.default_commission_rate', 30.00);
        $this->minimumWithdrawal = config('affiliate.minimum_withdrawal', 1000.00);
        $this->autoApproveInstructors = config('affiliate.auto_approve_instructors', true);
    }

    public function saveGlobalSettings()
    {
        $this->validate([
            'globalCommissionRate' => 'required|numeric|min:0|max:100',
            'minimumWithdrawal' => 'required|numeric|min:100'
        ]);

        session()->flash('success', 'Global settings updated successfully');
    }

    public function updateAffiliateStatus($affiliateId, $status)
    {
        $affiliate = Affiliate::findOrFail($affiliateId);
        $affiliate->update(['status' => $status]);
        
        session()->flash('success', "Affiliate status updated to {$status}");
    }

    public function updateCommissionRate($affiliateId, $rate)
    {
        $affiliate = Affiliate::findOrFail($affiliateId);
        $affiliate->update(['commission_rate' => $rate]);
        
        session()->flash('success', 'Commission rate updated successfully');
    }

    public function bulkUpdateAffiliates()
    {
        if (empty($this->selectedAffiliates) || empty($this->bulkAction)) {
            session()->flash('error', 'Please select affiliates and an action');
            return;
        }

        $count = 0;
        foreach ($this->selectedAffiliates as $affiliateId) {
            $affiliate = Affiliate::find($affiliateId);
            if ($affiliate) {
                match($this->bulkAction) {
                    'activate' => $affiliate->update(['status' => Affiliate::STATUS_ACTIVE]),
                    'deactivate' => $affiliate->update(['status' => Affiliate::STATUS_INACTIVE]),
                    'suspend' => $affiliate->update(['status' => Affiliate::STATUS_SUSPENDED]),
                    default => null
                };
                $count++;
            }
        }

        $this->selectedAffiliates = [];
        session()->flash('success', "Updated {$count} affiliates successfully");
    }

    public function render()
    {
        $user = auth()->user();
        
        if (!$user->hasRole(User::ROLE_SUPER_ADMIN)) {
            abort(403, 'Unauthorized access');
        }

        $affiliates = Affiliate::with('user')
            ->orderBy('total_earned', 'desc')
            ->paginate(20);

        $systemStats = [
            'total_affiliates' => Affiliate::count(),
            'active_affiliates' => Affiliate::where('status', Affiliate::STATUS_ACTIVE)->count(),
            'pending_approval' => Affiliate::where('status', Affiliate::STATUS_INACTIVE)->count(),
            'total_commissions_paid' => Affiliate::sum('total_earned'),
        ];

        return view('livewire.affiliate.settings', [
            'affiliates' => $affiliates,
            'systemStats' => $systemStats
        ]);
    }
}