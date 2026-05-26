<?php

// app/Livewire/Financial/TransactionHistory.php
namespace App\Livewire\Financial;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Marketplace\WalletTransaction;
use App\Models\Marketplace\Wallet;
use Livewire\Attributes\Layout;
use Carbon\Carbon;

#[Layout('layouts.dashboard')]
class TransactionHistory extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryFilter = 'all';
    public $typeFilter = 'all';
    public $walletFilter = 'all';
    public $dateFrom = '';
    public $dateTo = '';
    public $showFilters = false;

    // Export functionality
    public $showExportModal = false;
    public $exportFormat = 'csv';
    public $exportDateFrom = '';
    public $exportDateTo = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'categoryFilter' => ['except' => 'all'],
        'typeFilter' => ['except' => 'all'],
        'walletFilter' => ['except' => 'all'],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
    ];

    public function mount()
    {
        // Set default export dates
        $this->exportDateFrom = Carbon::now()->subDays(30)->format('Y-m-d');
        $this->exportDateTo = Carbon::now()->format('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatedTypeFilter()
    {
        $this->resetPage();
    }

    public function updatedWalletFilter()
    {
        $this->resetPage();
    }

    public function updatedDateFrom()
    {
        $this->resetPage();
    }

    public function updatedDateTo()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset([
            'search', 'categoryFilter', 'typeFilter', 'walletFilter', 
            'dateFrom', 'dateTo'
        ]);
        $this->resetPage();
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function openExportModal()
    {
        $this->showExportModal = true;
    }

    public function closeExportModal()
    {
        $this->showExportModal = false;
    }

    public function exportTransactions()
    {
        $this->validate([
            'exportFormat' => 'required|in:csv',
            'exportDateFrom' => 'required|date',
            'exportDateTo' => 'required|date|after_or_equal:exportDateFrom'
        ]);

        $user = auth()->user();
        
        // Get user's wallets
        $userWallet = $user->wallet;
        $instructorWallet = $user->instructorWallet;
        $walletIds = array_filter([$userWallet?->id, $instructorWallet?->id]);

        if (empty($walletIds)) {
            session()->flash('error', 'No wallet transactions found');
            return;
        }

        $transactions = WalletTransaction::whereIn('wallet_id', $walletIds)
            ->whereBetween('created_at', [
                Carbon::parse($this->exportDateFrom)->startOfDay(),
                Carbon::parse($this->exportDateTo)->endOfDay()
            ])
            ->with(['wallet.user', 'transactionable'])
            ->orderBy('created_at', 'desc')
            ->get();

        if ($transactions->isEmpty()) {
            session()->flash('error', 'No transactions found for the selected date range');
            return;
        }

        return $this->exportToCsv($transactions);
    }

    private function exportToCsv($transactions)
    {
        $filename = 'transactions_' . now()->format('Y_m_d_H_i_s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'Date', 'Type', 'Category', 'Amount', 'Balance Before', 
                'Balance After', 'Description', 'Reference', 'Status', 'Wallet Type'
            ]);

            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->created_at->format('Y-m-d H:i:s'),
                    ucfirst($transaction->type),
                    ucfirst(str_replace('_', ' ', $transaction->category)),
                    $transaction->amount,
                    $transaction->balance_before,
                    $transaction->balance_after,
                    $transaction->description,
                    $transaction->reference,
                    ucfirst($transaction->status),
                    ucfirst($transaction->wallet->wallet_type)
                ]);
            }
            
            fclose($file);
        };

        $this->closeExportModal();
        return response()->stream($callback, 200, $headers);
    }

    private function getTransactionQuery()
    {
        $user = auth()->user();
        
        // Get user's wallets
        $userWallet = $user->wallet;
        $instructorWallet = $user->instructorWallet;
        $walletIds = array_filter([$userWallet?->id, $instructorWallet?->id]);
    
        // Always return a query builder, even when no wallets exist
        $query = WalletTransaction::query()->with(['wallet.user', 'transactionable']);
    
        if (empty($walletIds)) {
            // Return empty results if no wallets exist
            $query->whereRaw('1 = 0');
        } else {
            $query->whereIn('wallet_id', $walletIds);
        }
    
        // Apply filters
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('description', 'like', '%' . $this->search . '%')
                  ->orWhere('reference', 'like', '%' . $this->search . '%')
                  ->orWhere('transaction_id', 'like', '%' . $this->search . '%');
            });
        }
    
        if ($this->categoryFilter !== 'all') {
            $query->where('category', $this->categoryFilter);
        }
    
        if ($this->typeFilter !== 'all') {
            $query->where('type', $this->typeFilter);
        }
    
        if ($this->walletFilter !== 'all') {
            $walletType = $this->walletFilter === 'user' ? Wallet::TYPE_USER : Wallet::TYPE_INSTRUCTOR;
            $query->whereHas('wallet', function ($q) use ($walletType) {
                $q->where('wallet_type', $walletType);
            });
        }
    
        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }
    
        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }
    
        return $query->orderBy('created_at', 'desc');
    }
    private function getTransactionSummary()
{
    $user = auth()->user();
    
    // Get user's wallets
    $userWallet = $user->wallet;
    $instructorWallet = $user->instructorWallet;

    $userBalance = $userWallet ? $userWallet->balance : 0;
    $instructorBalance = $instructorWallet ? $instructorWallet->balance : 0;

    // Get filtered transactions for summary using the same query logic
    $query = $this->getTransactionQuery();
    
    // Remove the orderBy for counting/summing operations
    $query->getQuery()->orders = [];
    
    $transactions = $query->get();
    
    $totalCredits = $transactions->where('type', 'credit')->sum('amount');
    $totalDebits = $transactions->where('type', 'debit')->sum('amount');
    $transactionCount = $transactions->count();

    // Group by category
    $categorySummary = $transactions->groupBy('category')
        ->map(function ($categoryTransactions) {
            return [
                'count' => $categoryTransactions->count(),
                'total_amount' => $categoryTransactions->sum('amount'),
                'credits' => $categoryTransactions->where('type', 'credit')->sum('amount'),
                'debits' => $categoryTransactions->where('type', 'debit')->sum('amount'),
            ];
        });

    return [
        'current_balances' => [
            'user_wallet' => $userBalance,
            'instructor_wallet' => $instructorBalance,
            'total' => $userBalance + $instructorBalance,
        ],
        'transaction_summary' => [
            'total_transactions' => $transactionCount,
            'total_credits' => $totalCredits,
            'total_debits' => $totalDebits,
            'net_amount' => $totalCredits - $totalDebits,
        ],
        'category_summary' => $categorySummary,
    ];
}

    public function render()
    {
        $transactions = $this->getTransactionQuery()->paginate(15);
        $summary = $this->getTransactionSummary();
        
        // Available filter options
        $categories = WalletTransaction::CATEGORY_FUNDING ? [
            'funding' => 'Wallet Funding',
            'course_purchase' => 'Course Purchase',
            'instructor_earning' => 'Instructor Earning',
            'withdrawal' => 'Withdrawal',
            'refund' => 'Refund',
        ] : [];

        $walletTypes = [
            'user' => 'User Wallet',
            'instructor' => 'Instructor Wallet',
        ];

        return view('livewire.financial.transaction-history', [
            'transactions' => $transactions,
            'summary' => $summary,
            'categories' => $categories,
            'walletTypes' => $walletTypes,
        ]);
    }
}
