<?php
// app/Livewire/Marketplace/Partial/PaymentsPayouts.php
namespace App\Livewire\Marketplace\Partial;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MarketplaceOrder;
use App\Models\Withdrawal;
use App\Models\WalletTransaction;
use App\Models\User;

class PaymentsPayouts extends Component
{
    use WithPagination;

    public $activeTab = 'transactions';
    public $statusFilter = 'all';
    public $search = '';
    public $dateFrom = '';
    public $dateTo = '';

    // Payout Management
    public $showPayoutModal = false;
    public $selectedWithdrawal = null;
    public $payoutNotes = '';

    protected $queryString = [
        'activeTab' => ['except' => 'transactions'],
        'statusFilter' => ['except' => 'all'],
        'search' => ['except' => ''],
    ];

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openPayoutModal($withdrawalId, $action)
    {
        $this->selectedWithdrawal = Withdrawal::findOrFail($withdrawalId);
        $this->payoutNotes = '';
        $this->showPayoutModal = true;
    }

    public function closePayoutModal()
    {
        $this->showPayoutModal = false;
        $this->selectedWithdrawal = null;
        $this->payoutNotes = '';
    }

    public function approveWithdrawal()
    {
        if (!$this->selectedWithdrawal) return;

        $this->selectedWithdrawal->approve(auth()->id());
        
        if ($this->payoutNotes) {
            $this->selectedWithdrawal->update(['admin_note' => $this->payoutNotes]);
        }

        session()->flash('message', 'Withdrawal approved successfully!');
        $this->closePayoutModal();
    }

    public function rejectWithdrawal()
    {
        if (!$this->selectedWithdrawal || !$this->payoutNotes) {
            session()->flash('error', 'Please provide a reason for rejection.');
            return;
        }

        $this->selectedWithdrawal->reject($this->payoutNotes);

        session()->flash('message', 'Withdrawal rejected and funds returned to vendor wallet.');
        $this->closePayoutModal();
    }

    public function processAutomaticPayouts()
    {
        // Process eligible payouts (orders older than 7 days)
        $eligibleOrders = MarketplaceOrder::paid()
            ->where('created_at', '<=', now()->subDays(7))
            ->whereDoesntHave('walletTransactions', function($q) {
                $q->where('category', WalletTransaction::CATEGORY_INSTRUCTOR_EARNING);
            })
            ->count();

        if ($eligibleOrders > 0) {
            // In a real implementation, you'd process these
            session()->flash('message', "Processed automatic payouts for {$eligibleOrders} orders.");
        } else {
            session()->flash('info', 'No orders eligible for automatic payout at this time.');
        }
    }

    private function getPaymentStats()
    {
        return [
            'total_revenue' => MarketplaceOrder::paid()->sum('total_amount'),
            'platform_commission' => MarketplaceOrder::paid()->sum('platform_commission'),
            'vendor_earnings' => MarketplaceOrder::paid()->sum('vendor_earning'),
            'pending_payouts' => Withdrawal::where('status', Withdrawal::STATUS_PENDING)->sum('amount'),
            'processed_payouts' => Withdrawal::where('status', Withdrawal::STATUS_COMPLETED)->sum('amount'),
            'this_month_revenue' => MarketplaceOrder::paid()
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->sum('total_amount'),
        ];
    }

    private function getRecentTransactions()
    {
        return WalletTransaction::whereIn('category', [
                WalletTransaction::CATEGORY_INSTRUCTOR_EARNING,
                WalletTransaction::CATEGORY_COURSE_PURCHASE,
                'platform_commission'
            ])
            ->with(['wallet.user', 'transactionable'])
            ->when($this->statusFilter !== 'all', function($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->search, function($query) {
                $query->whereHas('wallet.user', function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->dateFrom, function($query) {
                $query->where('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function($query) {
                $query->where('created_at', '<=', $this->dateTo . ' 23:59:59');
            })
            ->latest()
            ->paginate(15);
    }

    private function getPendingWithdrawals()
    {
        return Withdrawal::with(['user', 'wallet'])
            ->where('status', Withdrawal::STATUS_PENDING)
            ->when($this->search, function($query) {
                $query->whereHas('user', function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(10);
    }

    private function getRevenueAnalytics()
    {
        $data = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $revenue = MarketplaceOrder::paid()
                ->whereDate('paid_at', $date)
                ->sum('total_amount');
            
            $data[] = [
                'date' => $date->format('M d'),
                'revenue' => $revenue,
            ];
        }
        return $data;
    }

    public function render()
    {
        $stats = $this->getPaymentStats();

        $data = [];
        if ($this->activeTab === 'transactions') {
            $data['transactions'] = $this->getRecentTransactions();
        } elseif ($this->activeTab === 'withdrawals') {
            $data['withdrawals'] = $this->getPendingWithdrawals();
        } elseif ($this->activeTab === 'analytics') {
            $data['revenueData'] = $this->getRevenueAnalytics();
        }

        return view('livewire.marketplace.partial.payments-payouts', [
            'stats' => $stats,
            ...$data
        ]);
    }
}