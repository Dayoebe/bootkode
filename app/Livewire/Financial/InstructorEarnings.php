<?php

// app/Livewire/Financial/InstructorEarnings.php
namespace App\Livewire\Financial;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\WalletTransaction;
use App\Models\Course;
use App\Models\Withdrawal;
use App\Services\WalletService;
use Livewire\Attributes\Layout;
use Carbon\Carbon;

#[Layout('layouts.dashboard')]
class InstructorEarnings extends Component
{
    use WithPagination;

    public $dateRange = '30';
    public $selectedPeriod = 'last_30_days';
    public $showWithdrawalModal = false;
    
    // Withdrawal form data
    public $withdrawalAmount = '';
    public $selectedBankCode = '';
    public $accountNumber = '';
    public $accountName = '';
    
    public $isResolvingAccount = false;
    public $banks = [];

    private WalletService $walletService;

    public function boot(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    public function mount()
    {
        $this->loadBanks();
    }

    public function updatedSelectedPeriod()
    {
        $this->dateRange = match($this->selectedPeriod) {
            'last_7_days' => '7',
            'last_30_days' => '30',
            'last_90_days' => '90',
            'last_365_days' => '365',
            default => '30'
        };
    }

    public function updatedAccountNumber()
    {
        if (strlen($this->accountNumber) === 10 && $this->selectedBankCode) {
            $this->resolveAccount();
        }
    }

    private function loadBanks()
    {
        $result = app(\App\Services\PaystackService::class)->getBanks();
        if ($result['success']) {
            $this->banks = $result['banks'];
        }
    }

    private function resolveAccount()
    {
        $this->isResolvingAccount = true;

        $result = app(\App\Services\PaystackService::class)->resolveAccountNumber(
            $this->accountNumber, 
            $this->selectedBankCode
        );

        if ($result['success']) {
            $this->accountName = $result['account_name'];
        } else {
            $this->accountName = '';
            session()->flash('error', 'Could not resolve account details');
        }

        $this->isResolvingAccount = false;
    }

    public function requestWithdrawal()
    {
        $this->validate([
            'withdrawalAmount' => 'required|numeric|min:1000|max:500000',
            'selectedBankCode' => 'required|string',
            'accountNumber' => 'required|string|size:10',
            'accountName' => 'required|string|min:3'
        ]);

        $user = auth()->user();
        
        $bankDetails = [
            'bank_code' => $this->selectedBankCode,
            'account_number' => $this->accountNumber,
            'account_name' => $this->accountName
        ];

        $result = $this->walletService->requestWithdrawal($user, (float) $this->withdrawalAmount, $bankDetails);

        if ($result['success']) {
            $this->reset(['withdrawalAmount', 'selectedBankCode', 'accountNumber', 'accountName', 'showWithdrawalModal']);
            session()->flash('success', 'Withdrawal request submitted successfully');
        } else {
            session()->flash('error', $result['message']);
        }
    }

    public function openWithdrawalModal()
    {
        $this->showWithdrawalModal = true;
    }

    public function closeWithdrawalModal()
    {
        $this->showWithdrawalModal = false;
        $this->reset(['withdrawalAmount', 'selectedBankCode', 'accountNumber', 'accountName']);
    }

    private function getEarningsData()
    {
        $user = auth()->user();
        $startDate = Carbon::now()->subDays((int) $this->dateRange);
        
        // Get instructor wallet
        $instructorWallet = $user->instructorWallet;
        $currentBalance = $instructorWallet ? $instructorWallet->balance : 0;
        
        // Get earnings in date range
        $earnings = WalletTransaction::where('wallet_id', $instructorWallet?->id)
            ->where('category', WalletTransaction::CATEGORY_INSTRUCTOR_EARNING)
            ->whereBetween('created_at', [$startDate, now()])
            ->with('transactionable')
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate totals
        $totalEarnings = $earnings->sum('amount');
        $coursesSold = $earnings->count();
        $uniqueCourses = $earnings->pluck('transactionable_id')->unique()->count();

        // Get pending withdrawals
        $pendingWithdrawals = Withdrawal::where('user_id', $user->id)
            ->where('status', Withdrawal::STATUS_PENDING)
            ->sum('amount');

        // Get top performing courses
        $topCourses = $earnings->groupBy('transactionable_id')
            ->map(function ($courseEarnings) {
                $course = $courseEarnings->first()->transactionable;
                return [
                    'course' => $course,
                    'total_earnings' => $courseEarnings->sum('amount'),
                    'sales_count' => $courseEarnings->count(),
                    'average_per_sale' => $courseEarnings->avg('amount')
                ];
            })
            ->sortByDesc('total_earnings')
            ->take(5)
            ->values();

        // Daily earnings for chart
        $dailyEarnings = $earnings->groupBy(function($transaction) {
                return $transaction->created_at->format('Y-m-d');
            })
            ->map(function ($dayEarnings) {
                return $dayEarnings->sum('amount');
            })
            ->sortKeys();

        return [
            'current_balance' => $currentBalance,
            'total_earnings' => $totalEarnings,
            'courses_sold' => $coursesSold,
            'unique_courses' => $uniqueCourses,
            'pending_withdrawals' => $pendingWithdrawals,
            'available_for_withdrawal' => $currentBalance,
            'top_courses' => $topCourses,
            'daily_earnings' => $dailyEarnings,
            'recent_earnings' => $earnings->take(10)
        ];
    }

    public function render()
    {
        $earningsData = $this->getEarningsData();
        
        return view('livewire.financial.instructor-earnings', [
            'earningsData' => $earningsData,
            'banks' => $this->banks
        ]);
    }
}