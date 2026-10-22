<?php

// app/Livewire/Affiliate/Settings.php
namespace App\Livewire\Affiliate;

use Livewire\Component;
use App\Models\Marketplace\Affiliate;
use App\Models\Core\User;
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

    public function boot(): void
    {
        abort_unless(auth()->user()?->hasRole('super_admin'), 403);
    }

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

        session()->flash('info', 'Global defaults are managed in server configuration. This form does not save them.');
    }

    public function updateAffiliateStatus($affiliateId, $status)
    {
        validator(['status' => $status], ['status' => 'required|in:active,inactive,suspended'])->validate();
        $affiliate = Affiliate::findOrFail($affiliateId);
        $affiliate->update(['status' => $status]);
        
        session()->flash('success', "Affiliate status updated to {$status}");
    }

    public function updateCommissionRate($affiliateId, $rate)
    {
        validator(['rate' => $rate], ['rate' => 'required|numeric|between:0,100'])->validate();
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

        $this->validate(['bulkAction' => 'required|in:activate,deactivate,suspend', 'selectedAffiliates' => 'array|max:100', 'selectedAffiliates.*' => 'integer|exists:affiliates,id']);
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