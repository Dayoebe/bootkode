<?php

// app/Traits/HasWallet.php
namespace App\Traits;

use App\Models\Wallet;
use App\Models\Withdrawal;

trait HasWallet
{
    public function wallet()
    {
        return $this->hasOne(Wallet::class)->where('wallet_type', Wallet::TYPE_USER);
    }

    public function instructorWallet()
    {
        return $this->hasOne(Wallet::class)->where('wallet_type', Wallet::TYPE_INSTRUCTOR);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class)->orderBy('created_at', 'desc');
    }

    // Get or create user wallet
    public function getOrCreateWallet(): Wallet
    {
        return Wallet::getOrCreateWallet($this->id, Wallet::TYPE_USER);
    }

    // Get or create instructor wallet
    public function getOrCreateInstructorWallet(): Wallet
    {
        return Wallet::getOrCreateWallet($this->id, Wallet::TYPE_INSTRUCTOR);
    }

    // Get total wallet balance
    public function getTotalWalletBalance(): float
    {
        $userBalance = $this->wallet?->balance ?? 0;
        $instructorBalance = $this->instructorWallet?->balance ?? 0;
        
        return $userBalance + $instructorBalance;
    }

    // Check if user can make withdrawal
    public function canWithdraw(float $amount): bool
    {
        return $this->instructorWallet?->hasSufficientBalance($amount) ?? false;
    }
}