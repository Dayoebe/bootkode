<?php

namespace App\Livewire\Financial\Admin;

use App\Livewire\Concerns\AuthorizesFinancialAdministration;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard', ['title' => 'Financial rules'])]
class FinancialSettings extends Component
{
    use AuthorizesFinancialAdministration;

    public function render()
    {
        return view('livewire.financial.admin.financial-settings', [
            'minimumWithdrawal' => config('financial.minimum_withdrawal'),
            'maximumWithdrawal' => config('financial.maximum_withdrawal'),
            'platformPercentage' => 100 - config('financial.default_instructor_percentage'),
        ]);
    }
}
