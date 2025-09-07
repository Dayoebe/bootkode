<?php

// app/Livewire/Financial/Admin/Partials/PaymentWithdrawals.php
namespace App\Livewire\Financial\Admin\Partials;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Withdrawal;
use App\Services\WalletService;

class PaymentWithdrawals extends Component
{
    use WithPagination;

    public $statusFilter = 'pending';
    public $search = '';
    public $dateFrom = '';
    public $dateTo = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function approveWithdrawal($withdrawalId)
    {
        try {
            $walletService = app(WalletService::class);
            $withdrawal = Withdrawal::findOrFail($withdrawalId);
            $admin = auth()->user();

            $result = $walletService->processWithdrawal($withdrawal, $admin);

            if ($result['success']) {
                session()->flash('success', 'Withdrawal approved and processed successfully');
            } else {
                session()->flash('error', $result['message']);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Withdrawal processing failed: ' . $e->getMessage());
        }
    }

    public function rejectWithdrawal($withdrawalId, $reason = 'Rejected by admin')
    {
        try {
            $withdrawal = Withdrawal::findOrFail($withdrawalId);

            if ($withdrawal->reject($reason)) {
                session()->flash('success', 'Withdrawal rejected successfully');
            } else {
                session()->flash('error', 'Failed to reject withdrawal');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Withdrawal rejection failed: ' . $e->getMessage());
        }
    }

    public function bulkApprove($withdrawalIds)
    {
        try {
            $walletService = app(WalletService::class);
            $admin = auth()->user();
            $processed = 0;

            foreach ($withdrawalIds as $withdrawalId) {
                $withdrawal = Withdrawal::find($withdrawalId);
                if ($withdrawal && $withdrawal->canBeApproved()) {
                    $result = $walletService->processWithdrawal($withdrawal, $admin);
                    if ($result['success']) {
                        $processed++;
                    }
                }
            }

            session()->flash('success', "Processed {$processed} withdrawals successfully");
        } catch (\Exception $e) {
            session()->flash('error', 'Bulk processing failed: ' . $e->getMessage());
        }
    }

    private function getWithdrawalQuery()
    {
        $query = Withdrawal::with(['user', 'wallet']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('user', function ($userQuery) {
                    $userQuery->where('name', 'like', '%' . $this->search . '%')
                             ->orWhere('email', 'like', '%' . $this->search . '%');
                })
                ->orWhere('account_name', 'like', '%' . $this->search . '%')
                ->orWhere('account_number', 'like', '%' . $this->search . '%')
                ->orWhere('withdrawal_id', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->dateFrom) {
            $query->whereDate('requested_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('requested_at', '<=', $this->dateTo);
        }

        return $query->orderBy('requested_at', 'desc');
    }

    private function getWithdrawalStats()
    {
        return [
            'pending_count' => Withdrawal::where('status', Withdrawal::STATUS_PENDING)->count(),
            'pending_amount' => Withdrawal::where('status', Withdrawal::STATUS_PENDING)->sum('amount'),
            'processing_count' => Withdrawal::where('status', Withdrawal::STATUS_PROCESSING)->count(),
            'processing_amount' => Withdrawal::where('status', Withdrawal::STATUS_PROCESSING)->sum('amount'),
            'completed_today' => Withdrawal::where('status', Withdrawal::STATUS_COMPLETED)
                ->whereDate('completed_at', today())->count(),
            'completed_today_amount' => Withdrawal::where('status', Withdrawal::STATUS_COMPLETED)
                ->whereDate('completed_at', today())->sum('amount'),
        ];
    }

    public function render()
    {
        $withdrawals = $this->getWithdrawalQuery()->paginate(15);
        $stats = $this->getWithdrawalStats();

        return view('livewire.financial.admin.partials.payment-withdrawals', [
            'withdrawals' => $withdrawals,
            'stats' => $stats,
        ]);
    }
}