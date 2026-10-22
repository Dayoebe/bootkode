<?php

namespace App\Livewire\Financial\Admin;

use App\Livewire\Concerns\AuthorizesFinancialAdministration;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard', ['title' => 'Payment configuration'])]
class PaystackSettings extends Component
{
    use AuthorizesFinancialAdministration;

    public function render()
    {
        $secret = (string) config('services.paystack.secret_key');

        return view('livewire.financial.admin.paystack-settings', [
            'configured' => filled($secret) && filled(config('services.paystack.public_key')),
            'testMode' => str_starts_with($secret, 'sk_test_'),
            'webhookUrl' => route('paystack.webhook'),
        ]);
    }
}
