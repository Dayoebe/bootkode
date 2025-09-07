<?php

// app/Livewire/Financial/Admin/Partials/PaymentOverview.php
namespace App\Livewire\Financial\Admin\Partials;

use Livewire\Component;
use App\Models\PaystackTransaction;
use App\Models\Withdrawal;
use App\Services\PaystackService;
use Carbon\Carbon;

class PaymentOverview extends Component
{
    public $stats;

    protected $listeners = ['refreshOverview' => '$refresh'];

    public function mount($stats)
    {
        $this->stats = $stats;
    }

    public function verifySinglePayment($transactionId)
    {
        try {
            $paystackService = app(PaystackService::class);
            $transaction = PaystackTransaction::findOrFail($transactionId);

            $result = $paystackService->verifyTransaction($transaction->reference);

            if ($result['success']) {
                if ($result['status'] === 'success') {
                    $paystackService->processWalletFunding($transaction->reference);
                    session()->flash('success', 'Payment verified and processed successfully');
                } else {
                    $transaction->update(['status' => $result['status']]);
                    session()->flash('info', 'Payment status updated to: ' . $result['status']);
                }
            } else {
                session()->flash('error', 'Verification failed: ' . $result['message']);
            }

            $this->emit('refreshOverview');
        } catch (\Exception $e) {
            session()->flash('error', 'Verification error: ' . $e->getMessage());
        }
    }

    public function approveWithdrawal($withdrawalId)
    {
        try {
            $walletService = app(\App\Services\WalletService::class);
            $withdrawal = Withdrawal::findOrFail($withdrawalId);
            $admin = auth()->user();

            $result = $walletService->processWithdrawal($withdrawal, $admin);

            if ($result['success']) {
                session()->flash('success', 'Withdrawal approved and processed successfully');
                $this->emit('refreshOverview');
            } else {
                session()->flash('error', $result['message']);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Withdrawal processing failed: ' . $e->getMessage());
        }
    }

    public function rejectWithdrawal($withdrawalId)
    {
        try {
            $withdrawal = Withdrawal::findOrFail($withdrawalId);

            if ($withdrawal->reject('Rejected by admin')) {
                session()->flash('success', 'Withdrawal rejected successfully');
                $this->emit('refreshOverview');
            } else {
                session()->flash('error', 'Failed to reject withdrawal');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Withdrawal rejection failed: ' . $e->getMessage());
        }
    }

    private function getRecentFailedPayments()
    {
        return PaystackTransaction::where('status', 'failed')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    private function getPendingWithdrawals()
    {
        return Withdrawal::with(['user'])
            ->where('status', Withdrawal::STATUS_PENDING)
            ->latest()
            ->limit(5)
            ->get();
    }

    public function render()
    {
        $failedPayments = $this->getRecentFailedPayments();
        $pendingWithdrawals = $this->getPendingWithdrawals();

        return view('livewire.financial.admin.partials.payment-overview', [
            'failedPayments' => $failedPayments,
            'pendingWithdrawals' => $pendingWithdrawals,
        ]);
    }
}