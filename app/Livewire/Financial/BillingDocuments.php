<?php

namespace App\Livewire\Financial;

use App\Models\Marketplace\PaystackTransaction;
use App\Models\Marketplace\Wallet;
use App\Services\CommercialReadinessService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard', [
    'title' => 'Receipts & Invoices',
    'description' => 'Download your BootKode billing documents',
    'icon' => 'fas fa-receipt',
    'active' => 'billing.documents',
])]
class BillingDocuments extends Component
{
    public function generateReceipts(): void
    {
        if (! Schema::hasTable('commercial_documents') || ! Schema::hasTable('paystack_transactions')) {
            session()->flash('error', 'Billing document tables are not migrated yet.');
            return;
        }

        $user = Auth::user();
        $walletIds = Wallet::where('user_id', $user->id)->pluck('id');
        $service = app(CommercialReadinessService::class);
        $count = 0;

        PaystackTransaction::where('status', PaystackTransaction::STATUS_SUCCESS)
            ->where(function ($query) use ($user, $walletIds) {
                $query->where('customer_email', $user->email)
                    ->orWhere(function ($walletQuery) use ($walletIds) {
                        $walletQuery->where('transactionable_type', Wallet::class)
                            ->whereIn('transactionable_id', $walletIds);
                    });
            })
            ->latest()
            ->limit(50)
            ->get()
            ->each(function (PaystackTransaction $transaction) use ($service, &$count) {
                if ($service->issueReceiptForTransaction($transaction)) {
                    $count++;
                }
            });

        session()->flash('message', "Updated {$count} receipt(s).");
    }

    public function render()
    {
        $service = app(CommercialReadinessService::class);

        return view('livewire.financial.billing-documents', [
            'tablesReady' => $service->tablesReady(),
            'documents' => $service->documentsForUser(Auth::user()),
        ]);
    }
}
