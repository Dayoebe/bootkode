<?php

// app/Livewire/Marketplace/Partial/VendorWithdrawals.php
namespace App\Livewire\Marketplace\Partial;

use Livewire\Component;
use App\Models\Withdrawal;

class VendorWithdrawals extends Component
{
    public function render()
    {
        $withdrawals = Withdrawal::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('livewire.marketplace.partial.vendor-withdrawals', [
            'withdrawals' => $withdrawals
        ]);
    }
}
