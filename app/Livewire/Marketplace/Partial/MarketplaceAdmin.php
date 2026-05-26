<?php
// app/Livewire/Marketplace/Partial/MarketplaceAdmin.php
namespace App\Livewire\Marketplace\Partial;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Core\User;
use App\Models\Marketplace\MarketplaceItem;
use App\Models\Marketplace\MarketplaceOrder;
use App\Models\Marketplace\WalletTransaction;
use App\Models\Marketplace\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class MarketplaceAdmin extends Component
{
    use WithPagination;

    // Main Tab Management
    public $activeTab = 'overview';
    
    // Common Filters
    public $search = '';
    public $statusFilter = 'all';
    public $dateFrom = '';
    public $dateTo = '';
    
    // Vendor Applications
    public $vendorStatus = 'all';
    public $showApprovalModal = false;
    public $showRejectionModal = false;
    public $selectedUser = null;
    public $rejectionReason = '';
    public $commissionRate = 80;
    
    // Item Management
    public $itemStatus = '';
    public $itemType = '';
    public $selectedItems = [];
    public $bulkAction = '';
    public $selectAll = false;
    public $selectedMarketplaceItem = null;
    public $showItemDetailsModal = false;
    
    // Orders Management
    public $orderStatus = '';
    public $paymentStatus = '';
    public $selectedOrder = null;
    public $showOrderModal = false;
    public $refundReason = '';
    
    // Payments & Payouts
    public $selectedWithdrawal = null;
    public $showPayoutModal = false;
    public $payoutAction = '';
    public $payoutNotes = '';
    public $transactionStatus = 'all';
    public $transactionSearch = '';
    public $transactionDateFrom = '';
    public $paymentTab = 'transactions';
    
    // Analytics Cache
    public $stats = [];

    protected $listeners = [
        'refreshStats' => 'loadDashboardStats',
        'orderProcessed' => 'refreshOrders',
    ];

    public function mount()
    {
        $this->loadDashboardStats();
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
        $this->resetFilters();
    }

    public function setPaymentTab($tab)
    {
        $this->paymentTab = $tab;
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->statusFilter = 'all';
        $this->selectedItems = [];
        $this->selectAll = false;
    }

    public function loadDashboardStats()
    {
        $this->stats = Cache::remember('marketplace_admin_stats', 300, function() {
            try {
                return [
                    // Real Revenue Stats from Orders
                    'total_revenue' => MarketplaceOrder::where('payment_status', 'paid')->sum('total_amount') ?? 0,
                    'this_month_revenue' => MarketplaceOrder::where('payment_status', 'paid')
                        ->whereMonth('paid_at', now()->month)
                        ->whereYear('paid_at', now()->year)
                        ->sum('total_amount') ?? 0,
                    'platform_earnings' => MarketplaceOrder::where('payment_status', 'paid')->sum('platform_commission') ?? 0,
                    'vendor_earnings' => MarketplaceOrder::where('payment_status', 'paid')->sum('vendor_earning') ?? 0,
                    
                    // Real Order Stats
                    'total_orders' => MarketplaceOrder::count() ?? 0,
                    'pending_orders' => MarketplaceOrder::where('status', 'pending')->count() ?? 0,
                    'completed_orders' => MarketplaceOrder::where('status', 'completed')->count() ?? 0,
                    'failed_orders' => MarketplaceOrder::where('status', 'failed')->count() ?? 0,
                    'this_week_orders' => MarketplaceOrder::where('created_at', '>=', now()->startOfWeek())->count() ?? 0,
                    
                    // Real Vendor Stats from Database
                    'total_vendors' => User::where(function($query) {
                        $query->where('role', 'instructor')
                              ->orWhere('metadata', 'LIKE', '%"vendor_approved":true%');
                    })->count() ?? 0,
                    
                    'pending_applications' => User::where('role', 'student')
                        ->where(function($q) {
                            $q->whereNull('metadata')
                              ->orWhere('metadata', 'NOT LIKE', '%"vendor_approved"%')
                              ->orWhere('metadata', 'NOT LIKE', '%"vendor_rejected"%');
                        })->count() ?? 0,
                        
                    'suspended_vendors' => User::where('metadata', 'LIKE', '%"vendor_suspended":true%')->count() ?? 0,
                    
                    // Real Payout Stats
                    'pending_payouts' => $this->calculatePendingPayouts(),
                    'processed_payouts' => $this->calculateProcessedPayouts(),
                    'payout_requests' => $this->getPendingPayoutRequests(),
                    
                    // Real Item Stats
                    'total_items' => MarketplaceItem::count() ?? 0,
                    'published_items' => MarketplaceItem::where('status', 'approved')->count() ?? 0,
                    'pending_approval' => MarketplaceItem::where('status', 'pending')->count() ?? 0,
                    'suspended_items' => MarketplaceItem::where('status', 'suspended')->count() ?? 0,
                    
                    // Additional Real Analytics
                    'avg_order_value' => MarketplaceOrder::where('payment_status', 'paid')->avg('total_amount') ?? 0,
                    'conversion_rate' => $this->calculateConversionRate(),
                    'active_sessions' => $this->getActiveUserSessions(),
                ];
            } catch (\Exception $e) {
                // If any database error occurs, return default values
                \Log::error('MarketplaceAdmin stats error: ' . $e->getMessage());
                return [
                    'total_revenue' => 0,
                    'this_month_revenue' => 0,
                    'platform_earnings' => 0,
                    'vendor_earnings' => 0,
                    'total_orders' => 0,
                    'pending_orders' => 0,
                    'completed_orders' => 0,
                    'failed_orders' => 0,
                    'this_week_orders' => 0,
                    'total_vendors' => 0,
                    'pending_applications' => 0,
                    'suspended_vendors' => 0,
                    'pending_payouts' => 0,
                    'processed_payouts' => 0,
                    'payout_requests' => 0,
                    'total_items' => 0,
                    'published_items' => 0,
                    'pending_approval' => 0,
                    'suspended_items' => 0,
                    'avg_order_value' => 0,
                    'conversion_rate' => 0,
                    'active_sessions' => 0,
                ];
            }
        });
    }

    private function calculatePendingPayouts()
    {
        try {
            return MarketplaceOrder::where('payment_status', 'paid')
                ->where('status', '!=', 'refunded')
                ->whereDoesntHave('walletTransactions', function($query) {
                    $query->where('category', 'instructor_earning');
                })
                ->sum('vendor_earning') ?? 0;
        } catch (\Exception $e) {
            // If walletTransactions relationship doesn't exist, calculate differently
            return MarketplaceOrder::where('payment_status', 'paid')
                ->where('status', '!=', 'refunded')
                ->where('created_at', '<=', now()->subDays(7)) // Orders older than 7 days
                ->sum('vendor_earning') ?? 0;
        }
    }

    private function calculateProcessedPayouts()
    {
        try {
            if (class_exists('App\Models\Marketplace\WalletTransaction')) {
                return WalletTransaction::where('category', 'instructor_earning')
                    ->where('type', 'credit')
                    ->sum('amount') ?? 0;
            }
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getPendingPayoutRequests()
    {
        try {
            if (class_exists('App\Models\Marketplace\Withdrawal')) {
                return \App\Models\Marketplace\Withdrawal::where('status', 'pending')->count() ?? 0;
            }
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function calculateConversionRate()
    {
        try {
            $totalVisits = MarketplaceItem::sum('views_count') ?: 1;
            $totalOrders = MarketplaceOrder::count();
            return $totalOrders > 0 ? round(($totalOrders / $totalVisits) * 100, 2) : 0;
        } catch (\Exception $e) {
            // Fallback calculation if views_count doesn't exist
            $totalItems = MarketplaceItem::count() ?: 1;
            $totalOrders = MarketplaceOrder::count();
            return round(($totalOrders / $totalItems) * 10, 2); // Different calculation
        }
    }

    private function getActiveUserSessions()
    {
        try {
            // Check if last_activity column exists, fallback to updated_at or count recent users
            if (\Schema::hasColumn('users', 'last_activity')) {
                return User::where('last_activity', '>=', now()->subMinutes(30))->count() ?? 0;
            } elseif (\Schema::hasColumn('users', 'last_seen_at')) {
                return User::where('last_seen_at', '>=', now()->subMinutes(30))->count() ?? 0;
            } else {
                // Fallback: count users updated in last 30 minutes
                return User::where('updated_at', '>=', now()->subMinutes(30))->count() ?? 0;
            }
        } catch (\Exception $e) {
            // If any database error occurs, return 0
            return 0;
        }
    }

    // === REAL DATA METHODS FOR OVERVIEW ===
    public function getRecentActivity()
    {
        return collect([
            // Recent Orders
            ...MarketplaceOrder::with(['customer', 'item'])
                ->latest()
                ->take(3)
                ->get()
                ->map(fn($order) => [
                    'type' => 'order',
                    'message' => "New order from {$order->customer->name}",
                    'time' => $order->created_at->diffForHumans(),
                    'color' => 'green',
                    'icon' => 'fa-shopping-cart'
                ]),
            
            // Recent Vendor Approvals
            ...User::where('metadata', 'LIKE', '%"vendor_approved":true%')
                ->latest('updated_at')
                ->take(2)
                ->get()
                ->map(fn($user) => [
                    'type' => 'vendor',
                    'message' => "Vendor approved: {$user->name}",
                    'time' => $user->updated_at->diffForHumans(),
                    'color' => 'blue',
                    'icon' => 'fa-user-check'
                ]),
            
            // Recent Item Submissions
            ...MarketplaceItem::where('status', 'pending')
                ->with('vendor')
                ->latest()
                ->take(2)
                ->get()
                ->map(fn($item) => [
                    'type' => 'item',
                    'message' => "Item pending review: {$item->title}",
                    'time' => $item->created_at->diffForHumans(),
                    'color' => 'yellow',
                    'icon' => 'fa-clock'
                ]),
                
            // Recent Payments
            ...MarketplaceOrder::where('payment_status', 'paid')
                ->latest('paid_at')
                ->take(2)
                ->get()
                ->map(fn($order) => [
                    'type' => 'payment',
                    'message' => "Payment processed: ₦" . number_format($order->total_amount, 0),
                    'time' => $order->paid_at ? $order->paid_at->diffForHumans() : $order->created_at->diffForHumans(),
                    'color' => 'purple',
                    'icon' => 'fa-credit-card'
                ]),
        ])->sortByDesc(function($item) {
            return now(); // You might want to sort by actual timestamps here
        })->take(6);
    }

    public function getTopVendors()
    {
        return User::whereHas('vendorOrders', function($query) {
                $query->where('payment_status', 'paid')
                      ->whereMonth('paid_at', now()->month)
                      ->whereYear('paid_at', now()->year);
            })
            ->withSum(['vendorOrders as monthly_earnings' => function($query) {
                $query->where('payment_status', 'paid')
                      ->whereMonth('paid_at', now()->month)
                      ->whereYear('paid_at', now()->year);
            }], 'vendor_earning')
            ->withCount(['vendorOrders as monthly_sales' => function($query) {
                $query->where('payment_status', 'paid')
                      ->whereMonth('paid_at', now()->month)
                      ->whereYear('paid_at', now()->year);
            }])
            ->orderByDesc('monthly_earnings')
            ->take(5)
            ->get();
    }

    // === VENDOR APPLICATIONS METHODS ===
    public function openApprovalModal($userId)
    {
        $this->selectedUser = User::findOrFail($userId);
        $this->commissionRate = 80;
        $this->showApprovalModal = true;
    }

    public function closeApprovalModal()
    {
        $this->showApprovalModal = false;
        $this->selectedUser = null;
        $this->commissionRate = 80;
    }

    public function approveVendor()
    {
        if (!$this->selectedUser) return;

        try {
            DB::beginTransaction();

            if ($this->selectedUser->role === 'student') {
                $this->selectedUser->update(['role' => 'instructor']);
            }

            $metadata = $this->selectedUser->metadata ?? [];
            $metadata['vendor_approved'] = true;
            $metadata['vendor_approved_at'] = now();
            $metadata['vendor_approved_by'] = auth()->id();
            $metadata['vendor_commission_rate'] = $this->commissionRate;
            unset($metadata['vendor_rejected'], $metadata['vendor_rejection_reason']);
            
            $this->selectedUser->update(['metadata' => $metadata]);

            DB::commit();

            $this->loadDashboardStats();
            session()->flash('message', "Vendor approved successfully! Commission rate set to {$this->commissionRate}%");
            $this->closeApprovalModal();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to approve vendor: ' . $e->getMessage());
        }
    }

    public function openRejectionModal($userId)
    {
        $this->selectedUser = User::findOrFail($userId);
        $this->rejectionReason = '';
        $this->showRejectionModal = true;
    }

    public function closeRejectionModal()
    {
        $this->showRejectionModal = false;
        $this->selectedUser = null;
        $this->rejectionReason = '';
    }

    public function rejectVendor()
    {
        if (!$this->selectedUser || !$this->rejectionReason) {
            session()->flash('error', 'Please provide a rejection reason.');
            return;
        }

        try {
            $metadata = $this->selectedUser->metadata ?? [];
            $metadata['vendor_rejected'] = true;
            $metadata['vendor_rejected_at'] = now();
            $metadata['vendor_rejected_by'] = auth()->id();
            $metadata['vendor_rejection_reason'] = $this->rejectionReason;
            unset($metadata['vendor_approved'], $metadata['vendor_commission_rate']);
            
            $this->selectedUser->update(['metadata' => $metadata]);

            $this->loadDashboardStats();
            session()->flash('message', 'Vendor application rejected successfully.');
            $this->closeRejectionModal();

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to reject application: ' . $e->getMessage());
        }
    }

    public function suspendVendor($userId)
    {
        $user = User::findOrFail($userId);
        
        $metadata = $user->metadata ?? [];
        $metadata['vendor_suspended'] = true;
        $metadata['vendor_suspended_at'] = now();
        $metadata['vendor_suspended_by'] = auth()->id();
        
        $user->update(['metadata' => $metadata]);

        MarketplaceItem::where('vendor_id', $userId)
            ->where('status', 'approved')
            ->update(['status' => 'suspended']);

        $this->loadDashboardStats();
        session()->flash('message', 'Vendor suspended successfully.');
    }

    public function reactivateVendor($userId)
    {
        $user = User::findOrFail($userId);
        
        $metadata = $user->metadata ?? [];
        unset($metadata['vendor_suspended'], $metadata['vendor_suspended_at'], $metadata['vendor_suspended_by']);
        
        $user->update(['metadata' => $metadata]);

        $this->loadDashboardStats();
        session()->flash('message', 'Vendor reactivated successfully.');
    }
    // === ORDER MANAGEMENT METHODS ===
    public function viewOrder($orderId)
    {
        $this->selectedOrder = MarketplaceOrder::with(['customer', 'vendor', 'item'])->findOrFail($orderId);
        $this->showOrderModal = true;
    }

    public function closeOrderModal()
    {
        $this->showOrderModal = false;
        $this->selectedOrder = null;
        $this->refundReason = '';
    }

    public function confirmOrder($orderId)
    {
        $order = MarketplaceOrder::findOrFail($orderId);
        $order->update([
            'status' => 'confirmed',
            'confirmed_at' => now()
        ]);
        
        session()->flash('message', 'Order confirmed successfully.');
        $this->loadDashboardStats();
    }

    public function processRefund($orderId, $reason = null)
    {
        $order = MarketplaceOrder::findOrFail($orderId);
        
        try {
            DB::beginTransaction();
            
            $refundReason = $reason ?? $this->refundReason ?? 'Admin refund';
            
            $order->update([
                'status' => 'refunded',
                'payment_status' => 'refunded',
                'admin_notes' => $refundReason,
            ]);

            if (class_exists('App\Models\Marketplace\Wallet')) {
                $customerWallet = Wallet::where('user_id', $order->customer_id)->first();
                if ($customerWallet) {
                    $customerWallet->increment('balance', $order->total_amount);
                }
            }
            
            DB::commit();
            
            $this->loadDashboardStats();
            session()->flash('message', 'Refund processed successfully!');
            
            if ($this->showOrderModal) {
                $this->closeOrderModal();
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to process refund: ' . $e->getMessage());
        }
    }

    // === PAYMENT METHODS ===
    public function processAutomaticPayouts()
    {
        try {
            $eligibleOrders = MarketplaceOrder::where('payment_status', 'paid')
                ->where('created_at', '<=', now()->subDays(7))
                ->whereDoesntHave('walletTransactions')
                ->get();

            $processedCount = 0;
            
            DB::beginTransaction();
            
            foreach ($eligibleOrders as $order) {
                if (class_exists('App\Models\Marketplace\Wallet')) {
                    $vendorWallet = Wallet::where('user_id', $order->vendor_id)->first();
                    if (!$vendorWallet) {
                        $vendorWallet = Wallet::create([
                            'user_id' => $order->vendor_id,
                            'type' => 'instructor',
                            'balance' => 0
                        ]);
                    }
                    
                    $vendorWallet->increment('balance', $order->vendor_earning);
                    $processedCount++;
                }
            }
            
            DB::commit();

            $this->loadDashboardStats();
            
            if ($processedCount > 0) {
                session()->flash('message', "Processed automatic payouts for {$processedCount} orders.");
            } else {
                session()->flash('info', 'No orders eligible for automatic payout at this time.');
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Automatic payout processing failed: ' . $e->getMessage());
        }
    }

    public function refreshStats()
    {
        Cache::forget('marketplace_admin_stats');
        $this->loadDashboardStats();
        session()->flash('message', 'Statistics refreshed successfully.');
    }

    // === HELPER METHODS FOR DATA RETRIEVAL ===
    private function getVendorApplications()
    {
        $query = User::query()
            ->with(['marketplaceItems', 'customerOrders', 'vendorOrders'])
            ->withCount([
                'marketplaceItems',
                'vendorOrders as total_sales' => function($q) {
                    $q->where('payment_status', 'paid');
                }
            ])
            ->withSum([
                'vendorOrders as total_earnings' => function($q) {
                    $q->where('payment_status', 'paid');
                }
            ], 'vendor_earning');

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        switch ($this->vendorStatus) {
            case 'pending':
                $query->where('role', 'student')
                      ->where(function($q) {
                          $q->whereNull('metadata')
                            ->orWhere('metadata', 'NOT LIKE', '%"vendor_approved"%')
                            ->orWhere('metadata', 'NOT LIKE', '%"vendor_rejected"%');
                      });
                break;
            case 'approved':
                $query->where(function($q) {
                    $q->where('role', 'instructor')
                      ->orWhere('metadata', 'LIKE', '%"vendor_approved":true%');
                });
                break;
            case 'rejected':
                $query->where('metadata', 'LIKE', '%"vendor_rejected":true%');
                break;
            case 'suspended':
                $query->where('metadata', 'LIKE', '%"vendor_suspended":true%');
                break;
        }

        return $query->latest()->paginate(10);
    }

    private function getItems()
    {
        $query = MarketplaceItem::with(['vendor'])
            ->withCount(['orders as total_sales' => function($q) {
                $q->where('status', 'completed');
            }])
            ->withSum(['orders as total_revenue' => function($q) {
                $q->where('payment_status', 'paid');
            }], 'total_amount');

        if ($this->search) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhereHas('vendor', function($vendorQuery) {
                      $vendorQuery->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->itemStatus) {
            $query->where('status', $this->itemStatus);
        }

        if ($this->itemType) {
            $query->where('type', $this->itemType);
        }

        return $query->latest()->paginate(15);
    }

    private function getOrders()
    {
        $query = MarketplaceOrder::with(['customer', 'vendor', 'item']);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('order_number', 'like', '%' . $this->search . '%')
                  ->orWhereHas('customer', fn($sq) => $sq->where('name', 'like', '%' . $this->search . '%'))
                  ->orWhereHas('vendor', fn($sq) => $sq->where('name', 'like', '%' . $this->search . '%'))
                  ->orWhereHas('item', fn($sq) => $sq->where('title', 'like', '%' . $this->search . '%'));
            });
        }

        if ($this->orderStatus) {
            $query->where('status', $this->orderStatus);
        }

        if ($this->paymentStatus) {
            $query->where('payment_status', $this->paymentStatus);
        }

        if ($this->dateFrom) {
            $query->where('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->where('created_at', '<=', $this->dateTo . ' 23:59:59');
        }

        return $query->latest()->paginate(15);
    }

    private function getPaymentTransactions()
    {
        if (!class_exists('App\Models\Marketplace\WalletTransaction')) {
            return collect()->paginate(15);
        }
        
        $query = WalletTransaction::with(['wallet.user']);

        if ($this->transactionSearch) {
            $query->whereHas('wallet.user', function($q) {
                $q->where('name', 'like', '%' . $this->transactionSearch . '%')
                  ->orWhere('email', 'like', '%' . $this->transactionSearch . '%');
            });
        }

        if ($this->transactionStatus !== 'all') {
            $query->where('status', $this->transactionStatus);
        }

        if ($this->transactionDateFrom) {
            $query->where('created_at', '>=', $this->transactionDateFrom);
        }

        return $query->latest()->paginate(15);
    }

    private function getWithdrawalRequests()
    {
        if (!class_exists('App\Models\Marketplace\Withdrawal')) {
            return collect()->paginate(10);
        }
        
        $query = \App\Models\Marketplace\Withdrawal::with(['user'])
            ->where('status', 'pending');

        if ($this->search) {
            $query->whereHas('user', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        return $query->latest()->paginate(10);
    }
// === ENHANCED ITEM MANAGEMENT METHODS ===
public function approveItem($itemId)
{
    $item = MarketplaceItem::findOrFail($itemId);
    $item->update([
        'status' => 'approved',
        'approved_at' => now(),
        'approved_by' => auth()->id(),
        'rejection_reason' => null
    ]);
    
    session()->flash('message', 'Item approved successfully.');
    $this->loadDashboardStats();
}

public function rejectItem($itemId)
{
    $item = MarketplaceItem::findOrFail($itemId);
    $item->update([
        'status' => 'rejected',
        'rejection_reason' => 'Rejected by admin - does not meet marketplace standards',
        'approved_by' => auth()->id(),
        'approved_at' => null
    ]);
    
    session()->flash('message', 'Item rejected successfully.');
    $this->loadDashboardStats();
}

public function suspendItem($itemId)
{
    $item = MarketplaceItem::findOrFail($itemId);
    $item->update([
        'status' => 'suspended',
        'rejection_reason' => 'Suspended by admin for policy violation'
    ]);
    
    session()->flash('message', 'Item suspended successfully.');
    $this->loadDashboardStats();
}

public function reactivateItem($itemId)
{
    $item = MarketplaceItem::findOrFail($itemId);
    $item->update([
        'status' => 'approved',
        'rejection_reason' => null,
        'approved_at' => now(),
        'approved_by' => auth()->id()
    ]);
    
    session()->flash('message', 'Item reactivated successfully.');
    $this->loadDashboardStats();
}

public function toggleFeatureItem($itemId)
{
    $item = MarketplaceItem::findOrFail($itemId);
    $item->update(['is_featured' => !$item->is_featured]);
    
    $status = $item->is_featured ? 'featured' : 'unfeatured';
    session()->flash('message', "Item {$status} successfully.");
}

public function deleteItem($itemId)
{
    $item = MarketplaceItem::findOrFail($itemId);
    
    // Check if item has orders
    if ($item->orders()->count() > 0) {
        session()->flash('error', 'Cannot delete item with existing orders. Consider suspending instead.');
        return;
    }
    
    $item->delete();
    session()->flash('message', 'Item deleted successfully.');
    $this->loadDashboardStats();
}

public function viewItemDetails($itemId)
{
    $this->selectedMarketplaceItem = MarketplaceItem::with(['vendor', 'approver'])
        ->withCount('orders')
        ->withSum(['orders as paid_revenue' => fn ($query) => $query->where('payment_status', 'paid')], 'total_amount')
        ->findOrFail($itemId);

    $this->showItemDetailsModal = true;
}

public function closeItemDetails(): void
{
    $this->showItemDetailsModal = false;
    $this->selectedMarketplaceItem = null;
}

// === BULK ACTIONS FOR ITEMS ===
public function toggleSelectAll()
{
    if ($this->selectAll) {
        $this->selectedItems = [];
        $this->selectAll = false;
    } else {
        $this->selectedItems = $this->getItems()->pluck('id')->toArray();
        $this->selectAll = true;
    }
}

public function executeBulkAction()
{
    if (!$this->bulkAction || empty($this->selectedItems)) {
        session()->flash('error', 'Please select items and an action.');
        return;
    }

    $count = count($this->selectedItems);
    
    try {
        switch ($this->bulkAction) {
            case 'approve':
                MarketplaceItem::whereIn('id', $this->selectedItems)->update([
                    'status' => 'approved',
                    'approved_at' => now(),
                    'approved_by' => auth()->id(),
                    'rejection_reason' => null
                ]);
                session()->flash('message', "{$count} items approved successfully.");
                break;

            case 'reject':
                MarketplaceItem::whereIn('id', $this->selectedItems)->update([
                    'status' => 'rejected',
                    'rejection_reason' => 'Bulk rejection by admin',
                    'approved_by' => auth()->id(),
                    'approved_at' => null
                ]);
                session()->flash('message', "{$count} items rejected successfully.");
                break;

            case 'suspend':
                MarketplaceItem::whereIn('id', $this->selectedItems)->update([
                    'status' => 'suspended',
                    'rejection_reason' => 'Bulk suspension by admin'
                ]);
                session()->flash('message', "{$count} items suspended successfully.");
                break;

            case 'feature':
                MarketplaceItem::whereIn('id', $this->selectedItems)->update(['is_featured' => true]);
                session()->flash('message', "{$count} items featured successfully.");
                break;

            case 'unfeature':
                MarketplaceItem::whereIn('id', $this->selectedItems)->update(['is_featured' => false]);
                session()->flash('message', "{$count} items unfeatured successfully.");
                break;

            default:
                session()->flash('error', 'Invalid bulk action.');
                return;
        }

        // Reset selections
        $this->selectedItems = [];
        $this->selectAll = false;
        $this->bulkAction = '';
        $this->loadDashboardStats();

    } catch (\Exception $e) {
        session()->flash('error', 'Bulk action failed: ' . $e->getMessage());
    }
}
    public function render()
    {
        $data = ['stats' => $this->stats];

        switch ($this->activeTab) {
            case 'overview':
                $data['recentActivity'] = $this->getRecentActivity();
                $data['topVendors'] = $this->getTopVendors();
                break;
            case 'vendors':
                $data['users'] = $this->getVendorApplications();
                break;
            case 'items':
                $data['items'] = $this->getItems();
                break;
            case 'orders':
                $data['orders'] = $this->getOrders();
                break;
            case 'payments':
                $data['transactions'] = $this->getPaymentTransactions();
                $data['withdrawals'] = $this->getWithdrawalRequests();
                $data['paymentStats'] = [
                    'total_revenue' => $this->stats['total_revenue'],
                    'platform_commission' => $this->stats['platform_earnings'],
                    'vendor_earnings' => $this->stats['vendor_earnings'],
                    'pending_payouts' => $this->stats['pending_payouts'],
                    'processed_payouts' => $this->stats['processed_payouts'],
                    'this_month_revenue' => $this->stats['this_month_revenue'],
                    'pending_withdrawals' => $this->stats['payout_requests']
                ];
                break;
        }

        return view('livewire.marketplace.partial.marketplace-admin', $data);
    }
}
