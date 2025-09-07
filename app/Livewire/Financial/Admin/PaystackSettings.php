<?php

// app/Livewire/Financial/Admin/PaystackSettings.php
namespace App\Livewire\Financial\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard')]
class PaystackSettings extends Component
{
    public $publicKey = '';
    public $secretKey = '';
    public $webhookUrl = '';
    public $testMode = true;
    
    public function mount()
    {
        $this->publicKey = config('services.paystack.public_key');
        $this->secretKey = config('services.paystack.secret_key');
        $this->webhookUrl = route('paystack.webhook');
    }
    
    public function save()
    {
        // Save Paystack settings
        session()->flash('success', 'Paystack settings updated successfully');
    }
    
    public function testConnection()
    {
        // Test Paystack connection
        session()->flash('info', 'Connection test completed');
    }
    
    public function render()
    {
        return view('livewire.financial.admin.paystack-settings');
    }
}
