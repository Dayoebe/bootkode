<?php

// app/Traits/HasRevenueSplit.php
namespace App\Traits;

use App\Models\RevenueSplit;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;

trait HasRevenueSplit
{
    public function revenueSplit()
    {
        return $this->hasOne(RevenueSplit::class);
    }

    // Get or create revenue split for course
    public function getOrCreateRevenueSplit(): RevenueSplit
    {
        return $this->revenueSplit ?: $this->revenueSplit()->create([
            'instructor_id' => $this->instructor_id,
            'instructor_percentage' => 80.00,
            'platform_percentage' => 20.00,
            'is_active' => true
        ]);
    }

    // Process course purchase with revenue split
    public function processPurchase(User $student, Wallet $studentWallet): array
    {
        if (!$studentWallet->hasSufficientBalance($this->price)) {
            throw new \Exception('Insufficient wallet balance');
        }

        $revenueSplit = $this->getOrCreateRevenueSplit();
        $splitAmounts = $revenueSplit->calculateSplit($this->price);

        // Debit student wallet
        $studentWallet->debit(
            $this->price,
            WalletTransaction::CATEGORY_COURSE_PURCHASE,
            "Course purchase: {$this->title}",
            $this
        );

        // Credit instructor wallet
        $instructorWallet = $this->instructor->getOrCreateInstructorWallet();
        $instructorWallet->credit(
            $splitAmounts['instructor_amount'],
            WalletTransaction::CATEGORY_INSTRUCTOR_EARNING,
            "Course sale: {$this->title}",
            $this,
            ['student_id' => $student->id, 'split_percentage' => $revenueSplit->instructor_percentage]
        );

        // Credit platform wallet (you can implement this if needed)
        // $platformWallet = Wallet::getOrCreateWallet(1, Wallet::TYPE_PLATFORM);
        // $platformWallet->credit(...);

        return $splitAmounts;
    }
}