<?php

// app/Livewire/Financial/Admin/PaymentProcessing.php
namespace App\Livewire\Financial\Admin;

use Livewire\Component;
use App\Models\Marketplace\PaystackTransaction;
use App\Models\Marketplace\Withdrawal;
use App\Services\PaystackService;
use App\Services\WalletService;
use Livewire\Attributes\Layout;
use Carbon\Carbon;

#[Layout('layouts.dashboard')]
class PaymentProcessing extends Component
{
    public $selectedTab = 'overview';
    
    private PaystackService $paystackService;
    private WalletService $walletService;

    public function boot(PaystackService $paystackService, WalletService $walletService)
    {
        $this->paystackService = $paystackService;
        $this->walletService = $walletService;
    }

    public function setTab($tab)
    {
        $this->selectedTab = $tab;
    }

    private function getPaymentStats()
    {
        $startDate = Carbon::now()->subDays(7);
        $endDate = Carbon::now();

        $stats = [
            'total_transactions' => PaystackTransaction::whereBetween('created_at', [$startDate, $endDate])->count(),
            'successful_payments' => PaystackTransaction::where('status', 'success')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'pending_payments' => PaystackTransaction::where('status', 'pending')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'failed_payments' => PaystackTransaction::where('status', 'failed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count(),
            'total_amount' => PaystackTransaction::where('status', 'success')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount'),
            'pending_withdrawals' => Withdrawal::where('status', Withdrawal::STATUS_PENDING)->count(),
            'processing_withdrawals' => Withdrawal::where('status', Withdrawal::STATUS_PROCESSING)->count(),
        ];

        // Calculate success rate
        $stats['success_rate'] = $stats['total_transactions'] > 0
            ? round(($stats['successful_payments'] / $stats['total_transactions']) * 100, 2)
            : 0;

        return $stats;
    }

    public function render()
    {
        $stats = $this->getPaymentStats();

        return view('livewire.financial.admin.payment-processing', [
            'stats' => $stats,
        ]);
    }
}