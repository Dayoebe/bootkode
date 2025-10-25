<?php


// Livewire/Financial/WithdrawalManager.php
namespace App\Livewire\Financial;

use Livewire\Component;
use App\Models\Marketplace\Withdrawal;
use App\Services\WalletService;
use App\Services\PaystackService;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard')]
class WithdrawalManager extends Component
{
    use WithPagination;

    public $amount = '';
    public $bankCode = '';
    public $accountNumber = '';
    public $accountName = '';
    public $showWithdrawalForm = false;
    public $banks = [];
    public $isResolvingAccount = false;

    private WalletService $walletService;
    private PaystackService $paystackService;

    public function boot(WalletService $walletService, PaystackService $paystackService)
    {
        $this->walletService = $walletService;
        $this->paystackService = $paystackService;
    }

    public function mount()
    {
        $this->loadBanks();
    }

    public function loadBanks()
    {
        $result = $this->paystackService->getBanks();
        if ($result['success']) {
            $this->banks = $result['banks'];
        }
    }

    public function updatedAccountNumber()
    {
        if (strlen($this->accountNumber) === 10 && $this->bankCode) {
            $this->resolveAccount();
        }
    }

    public function resolveAccount()
    {
        if (!$this->accountNumber || !$this->bankCode) {
            return;
        }

        $this->isResolvingAccount = true;

        $result = $this->paystackService->resolveAccountNumber($this->accountNumber, $this->bankCode);

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
            'amount' => 'required|numeric|min:1000|max:500000',
            'bankCode' => 'required|string',
            'accountNumber' => 'required|string|size:10',
            'accountName' => 'required|string|min:3'
        ]);

        $user = auth()->user();
        
        $bankDetails = [
            'bank_code' => $this->bankCode,
            'account_number' => $this->accountNumber,
            'account_name' => $this->accountName
        ];

        $result = $this->walletService->requestWithdrawal($user, (float) $this->amount, $bankDetails);

        if ($result['success']) {
            $this->reset(['amount', 'bankCode', 'accountNumber', 'accountName', 'showWithdrawalForm']);
            session()->flash('success', 'Withdrawal request submitted successfully');
        } else {
            session()->flash('error', $result['message']);
        }
    }

    public function openWithdrawalForm()
    {
        $this->showWithdrawalForm = true;
    }

    public function closeWithdrawalForm()
    {
        $this->showWithdrawalForm = false;
        $this->reset(['amount', 'bankCode', 'accountNumber', 'accountName']);
    }

    public function render()
    {
        $user = auth()->user();
        $withdrawals = $user->withdrawals()->paginate(10);
        $instructorWallet = $user->instructorWallet;
        $availableBalance = $instructorWallet ? $instructorWallet->balance : 0;

        return view('livewire.financial.withdrawal-manager', [
            'withdrawals' => $withdrawals,
            'availableBalance' => $availableBalance,
            'formattedBalance' => '₦' . number_format($availableBalance, 2)
        ]);
    }
}
