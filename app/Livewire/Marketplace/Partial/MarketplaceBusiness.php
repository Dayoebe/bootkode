<?php
// app/Livewire/Marketplace/Partial/MarketplaceBusiness.php
namespace App\Livewire\Marketplace\Partial;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MarketplaceItem;
use App\Models\MarketplaceOrder;
use App\Models\Withdrawal;
use App\Models\Wallet;
use App\Services\PaystackService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MarketplaceBusiness extends Component
{
    use WithPagination;

    // Internal navigation state
    public $currentView = 'dashboard'; // dashboard, orders, withdrawals
    
    // Dashboard properties
    public $dateRange = '30';
    
    // Orders properties
    public $orderStatus = '';
    public $orderSearch = '';
    public $showNoteModal = false;
    public $selectedOrder = null;
    public $vendorNotes = '';
    
    // Withdrawals properties
    public $showWithdrawalModal = false;
    public $withdrawalAmount = '';
    public $selectedBankId = '';
    public $availableBanks = [];

    protected $queryString = [
        'currentView' => ['except' => 'dashboard'],
        'orderStatus' => ['except' => ''],
        'orderSearch' => ['except' => ''],
        'dateRange' => ['except' => '30'],
    ];

    protected $rules = [
        'withdrawalAmount' => 'required|numeric|min:1000|max:500000',
        'selectedBankId' => 'required',
    ];

    public function mount()
    {
        $this->loadBanks();
    }

    // Navigation methods
    public function showDashboard()
    {
        $this->currentView = 'dashboard';
    }

    public function showOrders()
    {
        $this->currentView = 'orders';
        $this->resetPage();
    }

    public function showWithdrawals()
    {
        $this->currentView = 'withdrawals';
        $this->resetPage();
    }

    // Dashboard methods
    public function getVendorStats()
    {
        $vendorId = auth()->id();
        
        return [
            'total_listings' => MarketplaceItem::byVendor($vendorId)->count(),
            'published_listings' => MarketplaceItem::byVendor($vendorId)->published()->count(),
            'pending_listings' => MarketplaceItem::byVendor($vendorId)->where('status', MarketplaceItem::STATUS_PENDING)->count(),
            'draft_listings' => MarketplaceItem::byVendor($vendorId)->where('status', MarketplaceItem::STATUS_DRAFT)->count(),
            'total_orders' => MarketplaceOrder::byVendor($vendorId)->count(),
            'total_revenue' => MarketplaceOrder::byVendor($vendorId)->paid()->sum('vendor_earning'),
            'pending_orders' => MarketplaceOrder::byVendor($vendorId)->where('status', MarketplaceOrder::STATUS_PENDING)->count(),
            'completed_orders' => MarketplaceOrder::byVendor($vendorId)->where('status', MarketplaceOrder::STATUS_COMPLETED)->count(),
            'this_month_revenue' => MarketplaceOrder::byVendor($vendorId)
                ->paid()
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->sum('vendor_earning'),
            'available_balance' => $this->getAvailableBalance(),
        ];
    }

    public function getRecentOrders()
    {
        return MarketplaceOrder::byVendor(auth()->id())
            ->with(['customer', 'item'])
            ->latest()
            ->limit(10)
            ->get();
    }

    public function getTopItems()
    {
        return MarketplaceItem::byVendor(auth()->id())
            ->published()
            ->orderBy('sales_count', 'desc')
            ->limit(5)
            ->get();
    }

    public function getChartData()
    {
        $vendorId = auth()->id();
        $startDate = now()->subDays((int)$this->dateRange);

        $revenueData = MarketplaceOrder::byVendor($vendorId)
            ->paid()
            ->where('paid_at', '>=', $startDate)
            ->selectRaw('DATE(paid_at) as date, SUM(vendor_earning) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $chartData = [];
        for ($date = $startDate->copy(); $date <= now(); $date->addDay()) {
            $dateKey = $date->format('Y-m-d');
            $chartData[] = [
                'date' => $dateKey,
                'revenue' => $revenueData[$dateKey]->revenue ?? 0,
                'formatted_date' => $date->format('M d'),
            ];
        }

        return $chartData;
    }

    // Orders methods
    public function updatingOrderSearch()
    {
        $this->resetPage();
    }

    public function updatingOrderStatus()
    {
        $this->resetPage();
    }

    public function fulfillOrder($orderId)
    {
        $order = MarketplaceOrder::byVendor(auth()->id())->findOrFail($orderId);
        
        if (!$order->isPaid()) {
            session()->flash('error', 'Order must be paid before fulfillment.');
            return;
        }

        if ($order->isCompleted()) {
            session()->flash('error', 'Order is already completed.');
            return;
        }

        $order->complete([
            'fulfilled_by' => auth()->user()->name,
            'fulfilled_at' => now(),
            'fulfillment_method' => 'manual'
        ]);
        
        session()->flash('message', 'Order fulfilled successfully!');
    }

    public function provideDigitalAccess($orderId)
    {
        $order = MarketplaceOrder::byVendor(auth()->id())->findOrFail($orderId);
        
        if (!$order->isPaid()) {
            session()->flash('error', 'Order must be paid before providing access.');
            return;
        }

        if (!$order->item->is_digital) {
            session()->flash('error', 'This is not a digital product.');
            return;
        }

        $order->complete([
            'fulfilled_by' => auth()->user()->name,
            'fulfilled_at' => now(),
            'fulfillment_method' => 'digital_access',
            'access_provided' => true,
        ]);
        
        session()->flash('message', 'Digital access provided successfully!');
    }

    public function openNoteModal($orderId)
    {
        $order = MarketplaceOrder::byVendor(auth()->id())->findOrFail($orderId);
        $this->selectedOrder = $order;
        $this->vendorNotes = $order->vendor_notes ?? '';
        $this->showNoteModal = true;
    }

    public function closeNoteModal()
    {
        $this->showNoteModal = false;
        $this->selectedOrder = null;
        $this->vendorNotes = '';
    }

    public function saveNotes()
    {
        if ($this->selectedOrder) {
            $this->selectedOrder->update([
                'vendor_notes' => $this->vendorNotes
            ]);
            
            session()->flash('message', 'Notes saved successfully!');
            $this->closeNoteModal();
        }
    }

    public function exportOrders()
    {
        $orders = MarketplaceOrder::byVendor(auth()->id())
            ->with(['customer', 'item'])
            ->when($this->orderStatus, fn($query) => $query->byStatus($this->orderStatus))
            ->get();

        if ($orders->isEmpty()) {
            session()->flash('error', 'No orders to export.');
            return;
        }

        $filename = 'vendor-orders-' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, [
                'Order Number', 'Customer Name', 'Customer Email', 'Item Title',
                'Status', 'Payment Status', 'Order Amount', 'Your Earning',
                'Order Date', 'Payment Date', 'Completion Date'
            ]);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->customer->name,
                    $order->customer->email,
                    $order->item->title,
                    ucfirst($order->status),
                    ucfirst(str_replace('_', ' ', $order->payment_status)),
                    number_format($order->total_amount, 2),
                    number_format($order->vendor_earning, 2),
                    $order->created_at->format('Y-m-d H:i:s'),
                    $order->paid_at ? $order->paid_at->format('Y-m-d H:i:s') : 'Not paid',
                    $order->completed_at ? $order->completed_at->format('Y-m-d H:i:s') : 'Not completed',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Withdrawals methods
    public function getAvailableBalance()
    {
        $instructorWallet = auth()->user()->instructorWallet;
        return $instructorWallet ? $instructorWallet->balance : 0;
    }

    public function loadBanks()
    {
        try {
            $paystackService = app(PaystackService::class);
            $response = $paystackService->getBanks();
            
            if ($response['success']) {
                $this->availableBanks = $response['banks'];
            }
        } catch (\Exception $e) {
            $this->availableBanks = [];
        }
    }

    public function openWithdrawalModal()
    {
        $balance = $this->getAvailableBalance();
        
        if ($balance < 1000) {
            session()->flash('error', 'Minimum withdrawal amount is ₦1,000. Your current balance is ₦' . number_format($balance, 2));
            return;
        }

        $this->withdrawalAmount = '';
        $this->selectedBankId = '';
        $this->showWithdrawalModal = true;
    }

    public function closeWithdrawalModal()
    {
        $this->showWithdrawalModal = false;
        $this->withdrawalAmount = '';
        $this->selectedBankId = '';
        $this->resetValidation();
    }

    public function requestWithdrawal()
    {
        $this->validate();
        
        $balance = $this->getAvailableBalance();
        
        if ($this->withdrawalAmount > $balance) {
            session()->flash('error', 'Insufficient balance for withdrawal.');
            return;
        }

        try {
            DB::beginTransaction();

            $user = auth()->user();
            $selectedBank = collect($this->availableBanks)->firstWhere('code', $this->selectedBankId);

            if (!$selectedBank) {
                throw new \Exception('Invalid bank selected.');
            }

            // Create withdrawal record
            $withdrawal = Withdrawal::create([
                'user_id' => $user->id,
                'amount' => $this->withdrawalAmount,
                'bank_code' => $selectedBank['code'],
                'bank_name' => $selectedBank['name'],
                'account_number' => $user->account_number,
                'account_name' => $user->account_name,
                'status' => Withdrawal::STATUS_PENDING,
            ]);

            // Debit from instructor wallet
            $instructorWallet = $user->getOrCreateInstructorWallet();
            $instructorWallet->debit(
                $this->withdrawalAmount,
                'withdrawal_request',
                "Withdrawal request #{$withdrawal->id}",
                $withdrawal
            );

            DB::commit();

            session()->flash('message', 'Withdrawal request submitted successfully! You will receive payment within 1-3 business days.');
            $this->closeWithdrawalModal();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to process withdrawal request: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $data = [
            'currentView' => $this->currentView,
            'availableBalance' => $this->getAvailableBalance(),
        ];

        switch ($this->currentView) {
            case 'dashboard':
                $data = array_merge($data, [
                    'stats' => $this->getVendorStats(),
                    'recentOrders' => $this->getRecentOrders(),
                    'topItems' => $this->getTopItems(),
                    'chartData' => $this->getChartData(),
                ]);
                break;

            case 'orders':
                $orders = MarketplaceOrder::byVendor(auth()->id())
                    ->with(['customer', 'item'])
                    ->when($this->orderStatus, fn($query) => $query->byStatus($this->orderStatus))
                    ->when($this->orderSearch, function ($query) {
                        $query->whereHas('customer', function ($q) {
                            $q->where('name', 'like', '%' . $this->orderSearch . '%')
                              ->orWhere('email', 'like', '%' . $this->orderSearch . '%');
                        })->orWhere('order_number', 'like', '%' . $this->orderSearch . '%')
                          ->orWhereHas('item', function ($q) {
                              $q->where('title', 'like', '%' . $this->orderSearch . '%');
                          });
                    })
                    ->latest()
                    ->paginate(10);

                $data = array_merge($data, [
                    'orders' => $orders,
                    'orderStatuses' => MarketplaceOrder::STATUSES,
                    'orderStats' => [
                        'total_orders' => MarketplaceOrder::byVendor(auth()->id())->count(),
                        'pending_orders' => MarketplaceOrder::byVendor(auth()->id())->where('status', 'pending')->count(),
                        'completed_orders' => MarketplaceOrder::byVendor(auth()->id())->where('status', 'completed')->count(),
                        'total_earnings' => MarketplaceOrder::byVendor(auth()->id())->where('payment_status', 'paid')->sum('vendor_earning'),
                    ],
                ]);
                break;

            case 'withdrawals':
                $withdrawals = Withdrawal::where('user_id', auth()->id())
                    ->latest()
                    ->paginate(10);

                $data = array_merge($data, [
                    'withdrawals' => $withdrawals,
                    'withdrawalStats' => [
                        'total_withdrawn' => Withdrawal::where('user_id', auth()->id())
                            ->where('status', Withdrawal::STATUS_COMPLETED)
                            ->sum('amount'),
                        'pending_withdrawals' => Withdrawal::where('user_id', auth()->id())
                            ->where('status', Withdrawal::STATUS_PENDING)
                            ->sum('amount'),
                        'withdrawal_count' => Withdrawal::where('user_id', auth()->id())->count(),
                    ],
                ]);
                break;
        }

        return view('livewire.marketplace.partial.marketplace-business', $data);
    }
}