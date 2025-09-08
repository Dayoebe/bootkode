<?php

// app/Events/CoursePurchased.php
namespace App\Events;

use App\Models\User;
use App\Models\Course;
use App\Models\WalletTransaction;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CoursePurchased
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public User $buyer,
        public Course $course,
        public float $amount,
        public WalletTransaction $purchaseTransaction
    ) {}
}
