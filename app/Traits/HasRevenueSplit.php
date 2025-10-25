<?php
namespace App\Traits;

use App\Models\RevenueSplit;
use App\Models\Core\User;
use App\Models\Marketplace\Wallet;
use App\Models\Marketplace\WalletTransaction;
use App\Events\CoursePurchased;

trait HasRevenueSplit
{
    public function revenueSplit()
    {
        return $this->hasOne(RevenueSplit::class);
    }

    public function getOrCreateRevenueSplit(): RevenueSplit
    {
        return $this->revenueSplit ?: $this->revenueSplit()->create([
            'instructor_id' => $this->instructor_id,
            'instructor_percentage' => 80.00,
            'platform_percentage' => 20.00,
            'is_active' => true
        ]);
    }

    public function processPurchase(User $student, Wallet $studentWallet): array
    {
        if (!$studentWallet->hasSufficientBalance($this->price)) {
            throw new \Exception('Insufficient wallet balance');
        }

        $revenueSplit = $this->getOrCreateRevenueSplit();
        $splitAmounts = $revenueSplit->calculateSplit($this->price);

        // Debit student wallet
        $purchaseTransaction = $studentWallet->debit(
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

        // Fire purchase event for affiliate processing
        event(new CoursePurchased($student, $this, $this->price, $purchaseTransaction));

        return $splitAmounts;
    }
}