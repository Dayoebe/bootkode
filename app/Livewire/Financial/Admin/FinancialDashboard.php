<?php

// Livewire/Financial/AdminFinancialDashboard.php
namespace App\Livewire\Financial\Admin;

use Livewire\Component;
use App\Models\Withdrawal;
use App\Services\WalletService;
use App\Services\PaystackService;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard')]
class FinancialDashboard extends Component
{
    use WithPagination;

    public $selectedTab = 'overview';
    public $dateRange = 30;

    private WalletService $walletService;
    private PaystackService $paystackService;

    public function boot(WalletService $walletService, PaystackService $paystackService)
    {
        $this->walletService = $walletService;
        $this->paystackService = $paystackService;
    }

    public function setTab($tab)
    {
        $this->selectedTab = $tab;
    }

    public function approveWithdrawal($withdrawalId)
    {
        try {
            $withdrawal = Withdrawal::findOrFail($withdrawalId);
            $admin = auth()->user();

            $result = $this->walletService->processWithdrawal($withdrawal, $admin);

            if ($result['success']) {
                session()->flash('success', 'Withdrawal approved and processed successfully');
            } else {
                session()->flash('error', $result['message']);
            }
        } catch (\Exception $e) {
            \Log::error('Withdrawal approval error: ' . $e->getMessage());
            session()->flash('error', 'Failed to process withdrawal: ' . $e->getMessage());
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
            \Log::error('Withdrawal rejection error: ' . $e->getMessage());
            session()->flash('error', 'Failed to reject withdrawal: ' . $e->getMessage());
        }
    }

    public function render()
    {
        try {
            // Create proper date range
            $startDate = now()->subDays($this->dateRange);
            $endDate = now();

            // Create DatePeriod object for the service
            $period = new \DatePeriod(
                $startDate->startOfDay(),
                new \DateInterval('P1D'),
                $endDate->endOfDay()
            );

            $revenueAnalytics = $this->walletService->getRevenueAnalytics($period);
            $paystackBalance = $this->paystackService->getBalance();

            $pendingWithdrawals = Withdrawal::with(['user', 'wallet'])
                ->where('status', Withdrawal::STATUS_PENDING)
                ->paginate(10);

            return view('livewire.financial.admin.financial-dashboard', [
                'revenueAnalytics' => $revenueAnalytics,
                'paystackBalance' => $paystackBalance,
                'pendingWithdrawals' => $pendingWithdrawals
            ]);

        } catch (\Exception $e) {
            \Log::error('Admin Financial Dashboard Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'user_id' => auth()->id()
            ]);

            // Return a safe fallback
            return view('livewire.financial.admin.financial-dashboard', [
                'revenueAnalytics' => [
                    'total_course_sales' => 0,
                    'instructor_earnings' => 0,
                    'platform_commission' => 0,
                    'total_withdrawals' => 0,
                    'pending_withdrawals' => 0,
                    'formatted' => [
                        'total_course_sales' => '₦0.00',
                        'instructor_earnings' => '₦0.00',
                        'platform_commission' => '₦0.00',
                        'total_withdrawals' => '₦0.00',
                        'pending_withdrawals' => '₦0.00'
                    ]
                ],
                'paystackBalance' => ['success' => false, 'balances' => []],
                'pendingWithdrawals' => collect([])
            ]);
        }
    }
}