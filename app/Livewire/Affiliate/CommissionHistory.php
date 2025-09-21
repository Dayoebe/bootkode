<?php

// app/Livewire/Affiliate/CommissionHistory.php
namespace App\Livewire\Affiliate;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ReferralTransaction;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', ['title' => 'Affiliate Commission History'])]
class CommissionHistory extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    public $dateRange = 'all';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    public function mount()
    {
        $user = auth()->user();
        
        if (!$user->isAffiliate()) {
            return redirect()->route('affiliate.dashboard');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedDateRange()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'desc';
        }
    }

    public function render()
    {
        $user = auth()->user();

        $query = ReferralTransaction::query()
            ->whereHas('referral', function($q) use ($user) {
                $q->where('affiliate_id', $user->affiliate->id);
            })
            ->with(['referral.referredUser', 'course']);

        // Apply filters
        if ($this->search) {
            $query->whereHas('course', function($q) {
                $q->where('title', 'like', '%' . $this->search . '%');
            })->orWhereHas('referral.referredUser', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->dateRange !== 'all') {
            $days = match($this->dateRange) {
                'today' => 1,
                'week' => 7,
                'month' => 30,
                'quarter' => 90,
                default => null
            };
            
            if ($days) {
                $query->where('created_at', '>=', now()->subDays($days));
            }
        }

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        $transactions = $query->paginate(15);

        // Calculate summary stats
        $totalCommissions = ReferralTransaction::whereHas('referral', function($q) use ($user) {
                $q->where('affiliate_id', $user->affiliate->id);
            })
            ->where('status', ReferralTransaction::STATUS_PAID)
            ->sum('commission_amount');

        $pendingCommissions = ReferralTransaction::whereHas('referral', function($q) use ($user) {
                $q->where('affiliate_id', $user->affiliate->id);
            })
            ->where('status', ReferralTransaction::STATUS_PENDING)
            ->sum('commission_amount');

        return view('livewire.affiliate.commission-history', [
            'transactions' => $transactions,
            'totalCommissions' => $totalCommissions,
            'pendingCommissions' => $pendingCommissions
        ]);
    }
}