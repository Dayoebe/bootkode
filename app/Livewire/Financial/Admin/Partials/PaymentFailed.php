<?php

// app/Livewire/Financial/Admin/Partials/PaymentFailed.php
namespace App\Livewire\Financial\Admin\Partials;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Marketplace\PaystackTransaction;
use App\Services\PaystackService;
use Carbon\Carbon;

class PaymentFailed extends Component
{
    use WithPagination;

    public $failedDateRange = 'week';
    public $failureReason = 'all';
    public $failedAmountRange = 'all';
    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function generateFailureReport()
    {
        try {
            $failures = $this->getFailedTransactionsQuery()->get();
            
            $failureAnalysis = $failures->groupBy('gateway_response')->map(function ($group) use ($failures) {
                return [
                    'count' => $group->count(),
                    'total_amount' => $group->sum('amount'),
                    'percentage' => $failures->count() > 0 ? round(($group->count() / $failures->count()) * 100, 2) : 0
                ];
            });

            session()->flash('success', 'Failure analysis completed. Found ' . $failures->count() . ' failed transactions with ' . $failureAnalysis->count() . ' different failure reasons.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to generate failure report: ' . $e->getMessage());
        }
    }

    public function retryAllFailedPayments()
    {
        try {
            $paystackService = app(PaystackService::class);
            $failedTransactions = $this->getFailedTransactionsQuery()->limit(20)->get();

            $retryCount = 0;
            $recoveredCount = 0;

            foreach ($failedTransactions as $transaction) {
                $result = $paystackService->verifyTransaction($transaction->reference);
                if ($result['success']) {
                    if ($result['status'] === 'success') {
                        $paystackService->processWalletFunding($transaction->reference);
                        $recoveredCount++;
                    }
                    $retryCount++;
                }
            }

            session()->flash('success', "Retried {$retryCount} payments. Successfully recovered {$recoveredCount} payments.");
        } catch (\Exception $e) {
            session()->flash('error', 'Bulk retry failed: ' . $e->getMessage());
        }
    }

    public function retryFailedPayment($transactionId)
    {
        try {
            $paystackService = app(PaystackService::class);
            $transaction = PaystackTransaction::findOrFail($transactionId);
            
            $result = $paystackService->verifyTransaction($transaction->reference);
            
            if ($result['success']) {
                if ($result['status'] === 'success') {
                    $paystackService->processWalletFunding($transaction->reference);
                    session()->flash('success', 'Payment successfully recovered!');
                } else {
                    $transaction->update(['status' => $result['status']]);
                    session()->flash('info', 'Payment status updated to: ' . $result['status']);
                }
            } else {
                session()->flash('error', 'Retry failed: ' . $result['message']);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Retry error: ' . $e->getMessage());
        }
    }

    public function investigateFailure($transactionId)
    {
        try {
            $paystackService = app(PaystackService::class);
            $transaction = PaystackTransaction::findOrFail($transactionId);
            
            $result = $paystackService->verifyTransaction($transaction->reference);
            
            if ($result['success']) {
                \Log::info('Transaction Investigation', [
                    'transaction_id' => $transactionId,
                    'reference' => $transaction->reference,
                    'paystack_data' => $result['data'],
                    'investigated_by' => auth()->id(),
                    'investigated_at' => now()
                ]);
                
                session()->flash('info', 'Investigation logged. Transaction details have been recorded for review.');
            } else {
                session()->flash('error', 'Could not retrieve transaction details from Paystack');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Investigation failed: ' . $e->getMessage());
        }
    }

    public function markAsIrrecoverable($transactionId)
    {
        try {
            $transaction = PaystackTransaction::findOrFail($transactionId);
            
            $transaction->update([
                'status' => 'abandoned',
                'gateway_response' => 'Marked as irrecoverable by admin - ' . now()->format('Y-m-d H:i:s')
            ]);
            
            session()->flash('success', 'Transaction marked as irrecoverable');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update transaction: ' . $e->getMessage());
        }
    }

    public function exportFailedTransactions()
    {
        try {
            $failedTransactions = $this->getFailedTransactionsQuery()->get();
            
            $filename = 'failed_transactions_' . now()->format('Y_m_d_H_i_s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];
            
            $callback = function () use ($failedTransactions) {
                $file = fopen('php://output', 'w');
                fputcsv($file, [
                    'Reference', 'Customer Name', 'Customer Email', 'Amount', 
                    'Status', 'Failure Reason', 'Created At', 'Last Updated'
                ]);
                
                foreach ($failedTransactions as $transaction) {
                    fputcsv($file, [
                        $transaction->reference,
                        $transaction->customer_name,
                        $transaction->customer_email,
                        $transaction->amount,
                        $transaction->status,
                        $transaction->gateway_response ?? 'Unknown',
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

    private function getFailedTransactionsQuery()
    {
        $query = PaystackTransaction::where('status', 'failed');
        
        // Apply date range filter
        switch ($this->failedDateRange) {
            case 'today':
                $query->whereDate('created_at', today());
                break;
            case 'yesterday':
                $query->whereDate('created_at', yesterday());
                break;
            case 'week':
                $query->where('created_at', '>=', now()->subWeek());
                break;
            case 'month':
                $query->where('created_at', '>=', now()->subMonth());
                break;
        }
        
        // Apply failure reason filter
        if ($this->failureReason !== 'all') {
            $query->where('gateway_response', 'like', '%' . $this->failureReason . '%');
        }

        // Apply amount range filter
        if ($this->failedAmountRange !== 'all') {
            switch ($this->failedAmountRange) {
                case '0-1000':
                    $query->whereBetween('amount', [0, 1000]);
                    break;
                case '1000-5000':
                    $query->whereBetween('amount', [1000, 5000]);
                    break;
                case '5000-10000':
                    $query->whereBetween('amount', [5000, 10000]);
                    break;
                case '10000+':
                    $query->where('amount', '>', 10000);
                    break;
            }
        }

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('reference', 'like', '%' . $this->search . '%')
                  ->orWhere('customer_email', 'like', '%' . $this->search . '%')
                  ->orWhere('customer_name', 'like', '%' . $this->search . '%');
            });
        }
        
        return $query->orderBy('created_at', 'desc');
    }

    private function getFailureStats()
    {
        $failedTransactions = $this->getFailedTransactionsQuery()->get();
        
        $stats = [
            'total_failed' => $failedTransactions->count(),
            'total_amount_lost' => $failedTransactions->sum('amount'),
            'most_common_reason' => $failedTransactions->groupBy('gateway_response')
                ->sortByDesc->count()
                ->keys()
                ->first() ?? 'Unknown',
            'retry_success_rate' => $this->calculateRetrySuccessRate(),
            'daily_average' => $failedTransactions->count() > 0 && $this->failedDateRange === 'week' 
                ? round($failedTransactions->count() / 7, 1)
                : 0,
        ];

        return $stats;
    }

    private function calculateRetrySuccessRate()
    {
        // This is a simplified calculation - in real implementation,
        // you'd track retry attempts and their outcomes
        $totalRetries = PaystackTransaction::where('status', 'failed')
            ->where('updated_at', '>', 'created_at')
            ->count();
            
        $successfulRetries = PaystackTransaction::where('status', 'success')
            ->where('updated_at', '>', 'created_at')
            ->count();

        return $totalRetries > 0 ? round(($successfulRetries / $totalRetries) * 100, 1) : 0;
    }

    public function render()
    {
        $failedTransactions = $this->getFailedTransactionsQuery()->paginate(15);
        $stats = $this->getFailureStats();

        return view('livewire.financial.admin.partials.payment-failed', [
            'failedTransactions' => $failedTransactions,
            'stats' => $stats,
        ]);
    }
}