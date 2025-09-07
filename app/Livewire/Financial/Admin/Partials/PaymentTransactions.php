<?php

// app/Livewire/Financial/Admin/Partials/PaymentTransactions.php
namespace App\Livewire\Financial\Admin\Partials;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PaystackTransaction;
use App\Services\PaystackService;
use Carbon\Carbon;

class PaymentTransactions extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    public $typeFilter = 'all';
    public $dateFrom = '';
    public $dateTo = '';

    // Bulk operations
    public $selectedTransactions = [];
    public $selectAll = false;
    public $bulkAction = '';

    // Refund modal
    public $showRefundModal = false;
    public $refundTransactionId = null;
    public $refundAmount = '';
    public $refundReason = '';

    public function mount()
    {
        $this->dateFrom = Carbon::now()->subDays(7)->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedTypeFilter()
    {
        $this->resetPage();
    }

    public function updatedSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedTransactions = $this->getTransactionQuery()->pluck('id')->toArray();
        } else {
            $this->selectedTransactions = [];
        }
    }

    public function processBulkAction()
    {
        if (empty($this->selectedTransactions) || empty($this->bulkAction)) {
            session()->flash('error', 'Please select transactions and an action');
            return;
        }

        try {
            switch ($this->bulkAction) {
                case 'verify':
                    $this->bulkVerifyPayments();
                    break;
                case 'retry':
                    $this->bulkRetryPayments();
                    break;
                case 'mark_failed':
                    $this->bulkMarkAsFailed();
                    break;
                default:
                    session()->flash('error', 'Invalid bulk action');
                    return;
            }

            $this->reset(['selectedTransactions', 'selectAll', 'bulkAction']);

        } catch (\Exception $e) {
            session()->flash('error', 'Bulk operation failed: ' . $e->getMessage());
        }
    }

    private function bulkVerifyPayments()
    {
        $paystackService = app(PaystackService::class);
        $transactions = PaystackTransaction::whereIn('id', $this->selectedTransactions)->get();
        $verified = 0;

        foreach ($transactions as $transaction) {
            if ($transaction->status === 'pending') {
                $result = $paystackService->verifyTransaction($transaction->reference);
                if ($result['success'] && $result['status'] === 'success') {
                    $paystackService->processWalletFunding($transaction->reference);
                    $verified++;
                }
            }
        }

        session()->flash('success', "Verified {$verified} payments successfully");
    }

    private function bulkRetryPayments()
    {
        $paystackService = app(PaystackService::class);
        $transactions = PaystackTransaction::whereIn('id', $this->selectedTransactions)->get();
        $retried = 0;

        foreach ($transactions as $transaction) {
            if ($transaction->status === 'failed') {
                $result = $paystackService->verifyTransaction($transaction->reference);
                if ($result['success']) {
                    if ($result['status'] === 'success') {
                        $paystackService->processWalletFunding($transaction->reference);
                        $retried++;
                    } else {
                        $transaction->update(['status' => $result['status']]);
                    }
                }
            }
        }

        session()->flash('success', "Retried payments - {$retried} successfully recovered");
    }

    private function bulkMarkAsFailed()
    {
        $updated = PaystackTransaction::whereIn('id', $this->selectedTransactions)
            ->where('status', 'pending')
            ->update(['status' => 'failed']);

        session()->flash('success', "Marked {$updated} transactions as failed");
    }

    public function openRefundModal($transactionId)
    {
        $transaction = PaystackTransaction::findOrFail($transactionId);
        $this->refundTransactionId = $transactionId;
        $this->refundAmount = $transaction->amount;
        $this->showRefundModal = true;
    }

    public function processRefund()
    {
        $this->validate([
            'refundAmount' => 'required|numeric|min:1',
            'refundReason' => 'required|string|min:5|max:255'
        ]);

        try {
            $transaction = PaystackTransaction::findOrFail($this->refundTransactionId);

            // Create refund transaction record
            PaystackTransaction::create([
                'reference' => 'RF_' . time() . '_' . uniqid(),
                'amount' => $this->refundAmount,
                'currency' => $transaction->currency,
                'status' => 'success',
                'customer_email' => $transaction->customer_email,
                'customer_name' => $transaction->customer_name,
                'transaction_type' => 'refund',
                'transactionable_type' => $transaction->transactionable_type,
                'transactionable_id' => $transaction->transactionable_id,
                'paid_at' => now(),
                'gateway_response' => 'Manual refund: ' . $this->refundReason
            ]);

            session()->flash('success', 'Refund processed successfully');

        } catch (\Exception $e) {
            session()->flash('error', 'Refund processing error: ' . $e->getMessage());
        }

        $this->closeRefundModal();
    }

    public function closeRefundModal()
    {
        $this->showRefundModal = false;
        $this->reset(['refundTransactionId', 'refundAmount', 'refundReason']);
    }

    public function verifySinglePayment($transactionId)
    {
        try {
            $paystackService = app(PaystackService::class);
            $transaction = PaystackTransaction::findOrFail($transactionId);

            $result = $paystackService->verifyTransaction($transaction->reference);

            if ($result['success']) {
                if ($result['status'] === 'success') {
                    $paystackService->processWalletFunding($transaction->reference);
                    session()->flash('success', 'Payment verified and processed successfully');
                } else {
                    $transaction->update(['status' => $result['status']]);
                    session()->flash('info', 'Payment status updated to: ' . $result['status']);
                }
            } else {
                session()->flash('error', 'Verification failed: ' . $result['message']);
            }

        } catch (\Exception $e) {
            session()->flash('error', 'Verification error: ' . $e->getMessage());
        }
    }

    public function exportTransactions()
    {
        try {
            $transactions = $this->getTransactionQuery()->get();
            
            $filename = 'transactions_export_' . now()->format('Y_m_d_H_i_s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];
            
            $callback = function () use ($transactions) {
                $file = fopen('php://output', 'w');
                fputcsv($file, [
                    'Reference', 'Paystack Reference', 'Customer Name', 'Customer Email', 
                    'Amount', 'Currency', 'Status', 'Type', 'Created At', 'Updated At'
                ]);
                
                foreach ($transactions as $transaction) {
                    fputcsv($file, [
                        $transaction->reference,
                        $transaction->paystack_reference,
                        $transaction->customer_name,
                        $transaction->customer_email,
                        $transaction->amount,
                        $transaction->currency,
                        $transaction->status,
                        $transaction->transaction_type,
                        $transaction->created_at->format('Y-m-d H:i:s'),
                        $transaction->updated_at->format('Y-m-d H:i:s')
                    ]);
                }
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            session()->flash('error', 'Export failed: ' . $e->getMessage());
        }
    }

    private function getTransactionQuery()
    {
        $query = PaystackTransaction::with(['transactionable']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('reference', 'like', '%' . $this->search . '%')
                    ->orWhere('paystack_reference', 'like', '%' . $this->search . '%')
                    ->orWhere('customer_email', 'like', '%' . $this->search . '%')
                    ->orWhere('customer_name', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->typeFilter !== 'all') {
            $query->where('transaction_type', $this->typeFilter);
        }

        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        return $query->orderBy('created_at', 'desc');
    }

    private function getTransactionStats()
    {
        $query = $this->getTransactionQuery();
        
        return [
            'total_transactions' => $query->count(),
            'successful_transactions' => $query->where('status', 'success')->count(),
            'pending_transactions' => $query->where('status', 'pending')->count(),
            'failed_transactions' => $query->where('status', 'failed')->count(),
            'total_amount' => $query->where('status', 'success')->sum('amount'),
        ];
    }

    public function render()
    {
        $transactions = $this->getTransactionQuery()->paginate(20);
        $stats = $this->getTransactionStats();

        return view('livewire.financial.admin.partials.payment-transactions', [
            'transactions' => $transactions,
            'stats' => $stats,
        ]);
    }
}