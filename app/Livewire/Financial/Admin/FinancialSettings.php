<?php


// app/Livewire/Financial/Admin/FinancialSettings.php
namespace App\Livewire\Financial\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard')]
class FinancialSettings extends Component
{
    public $commissionRate = 20;
    public $minimumWithdrawal = 1000;
    public $maximumWithdrawal = 500000;
    public $autoApproveWithdrawals = false;
    
    public function save()
    {
        // Save settings logic
        session()->flash('success', 'Settings saved successfully');
    }
    
    public function render()
    {
        return view('livewire.financial.admin.financial-settings');
    }
}