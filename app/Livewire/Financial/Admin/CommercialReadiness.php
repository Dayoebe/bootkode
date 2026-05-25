<?php

namespace App\Livewire\Financial\Admin;

use App\Models\Commerce\CommercialDocument;
use App\Models\Commerce\PricingPackage;
use App\Models\Marketplace\PaystackTransaction;
use App\Services\CommercialReadinessService;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard', [
    'title' => 'Commercial Readiness',
    'description' => 'Receipts, invoices, refunds, payout audit trail, revenue reports, and public pricing packages',
    'icon' => 'fas fa-file-invoice-dollar',
    'active' => 'admin.commercial.readiness',
])]
class CommercialReadiness extends Component
{
    public string $activeTab = 'overview';
    public string $dateFrom = '';
    public string $dateTo = '';
    public string $refundTransactionId = '';
    public string $refundAmount = '';
    public string $refundReason = '';

    public function mount(): void
    {
        $this->dateFrom = now()->subDays(30)->toDateString();
        $this->dateTo = now()->toDateString();
    }

    public function setTab(string $tab): void
    {
        if (in_array($tab, ['overview', 'documents', 'refunds', 'payouts', 'packages'], true)) {
            $this->activeTab = $tab;
        }
    }

    public function generateMissingReceipts(): void
    {
        $service = app(CommercialReadinessService::class);
        $receipts = $service->generateMissingReceipts();
        $invoices = $service->generateMissingInvoices();
        session()->flash('message', "Generated {$receipts} missing receipt(s) and {$invoices} missing invoice(s).");
        $this->activeTab = 'documents';
    }

    public function seedPackages(): void
    {
        $count = app(CommercialReadinessService::class)->seedDefaultPackages();
        session()->flash('message', $count > 0 ? "Seeded {$count} public package(s)." : 'Public packages are already available.');
        $this->activeTab = 'packages';
    }

    public function issueDocument(int $transactionId): void
    {
        $transaction = PaystackTransaction::findOrFail($transactionId);
        app(CommercialReadinessService::class)->issueReceiptForTransaction($transaction);
        session()->flash('message', 'Receipt issued for transaction ' . $transaction->reference . '.');
        $this->activeTab = 'documents';
    }

    public function processRefund(): void
    {
        $this->validate([
            'refundTransactionId' => 'required|integer|exists:paystack_transactions,id',
            'refundAmount' => 'required|numeric|min:1',
            'refundReason' => 'required|string|min:5|max:255',
        ]);

        $transaction = PaystackTransaction::findOrFail((int) $this->refundTransactionId);
        $result = app(CommercialReadinessService::class)->processRefund(
            $transaction,
            (float) $this->refundAmount,
            $this->refundReason,
            auth()->user()
        );

        session()->flash($result['success'] ? 'message' : 'error', $result['message']);

        if ($result['success']) {
            $this->reset(['refundTransactionId', 'refundAmount', 'refundReason']);
        }

        $this->activeTab = 'refunds';
    }

    public function exportRevenueCsv()
    {
        $report = app(CommercialReadinessService::class)->revenueReport($this->dateFrom, $this->dateTo);
        $filename = 'bootkode_revenue_report_' . now()->format('Y_m_d_His') . '.csv';

        return response()->streamDownload(function () use ($report) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Metric', 'Value']);

            foreach ($report['totals'] as $metric => $value) {
                fputcsv($handle, [str($metric)->headline()->toString(), $value]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['Date', 'Payments', 'Marketplace Orders', 'Refunds', 'Net']);

            foreach ($report['breakdown'] as $row) {
                fputcsv($handle, [$row['date'], $row['payments'], $row['orders'], $row['refunds'], $row['net']]);
            }

            fclose($handle);
        }, $filename);
    }

    public function render()
    {
        $service = app(CommercialReadinessService::class);
        $transactions = Schema::hasTable('paystack_transactions')
            ? PaystackTransaction::where('status', PaystackTransaction::STATUS_SUCCESS)
                ->latest()
                ->limit(25)
                ->get()
                ->map(fn (PaystackTransaction $transaction) => [
                    'id' => $transaction->id,
                    'reference' => $transaction->reference,
                    'customer' => $transaction->customer_name ?: $transaction->customer_email,
                    'amount' => (float) $transaction->amount,
                    'formatted_amount' => $transaction->formatted_amount,
                    'refundable' => $service->refundableAmount($transaction),
                ])
            : collect();

        return view('livewire.financial.admin.commercial-readiness', [
            'tablesReady' => $service->tablesReady(),
            'report' => $service->revenueReport($this->dateFrom, $this->dateTo),
            'documents' => $service->recentDocuments(12),
            'refunds' => $service->recentRefunds(12),
            'payoutAudits' => $service->recentPayoutAudits(15),
            'packages' => Schema::hasTable('pricing_packages')
                ? PricingPackage::orderBy('sort_order')->orderBy('name')->get()
                : collect(),
            'publicPackages' => $service->publicPackages(),
            'transactions' => $transactions,
            'documentCounts' => Schema::hasTable('commercial_documents')
                ? CommercialDocument::selectRaw('type, count(*) as total')->groupBy('type')->pluck('total', 'type')->all()
                : [],
        ]);
    }
}
