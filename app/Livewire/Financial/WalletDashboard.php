<?php

// Livewire/Financial/WalletDashboard.php
namespace App\Livewire\Financial;

use Livewire\Component;
use App\Services\WalletService;
use App\Services\PaystackService;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard')]
class WalletDashboard extends Component
{
    public $fundAmount = '';
    public $showFundingModal = false;
    
    private WalletService $walletService;
    private PaystackService $paystackService;

    public function boot(WalletService $walletService, PaystackService $paystackService)
    {
        $this->walletService = $walletService;
        $this->paystackService = $paystackService;
    }

    public function fundWallet()
    {
        $this->validate([
            'fundAmount' => 'required|numeric|min:100|max:500000'
        ]);

        $user = auth()->user();
        $result = $this->walletService->initiateFunding($user, (float) $this->fundAmount);

        if ($result['success']) {
            // Redirect to Paystack payment page
            return redirect($result['data']['authorization_url']);
        }

        session()->flash('error', $result['message']);
    }

    public function openFundingModal()
    {
        $this->showFundingModal = true;
        $this->fundAmount = '';
    }

    public function closeFundingModal()
    {
        $this->showFundingModal = false;
        $this->fundAmount = '';
    }

    public function render()
    {
        $user = auth()->user();
        $walletStats = $this->walletService->getWalletStats($user);

        return view('livewire.financial.wallet-dashboard', [
            'walletStats' => $walletStats,
            'paystackPublicKey' => $this->paystackService->getPublicKey()
        ]);
    }
}
