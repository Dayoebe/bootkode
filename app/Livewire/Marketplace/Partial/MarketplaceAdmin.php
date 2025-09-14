<?php
// app/Livewire/Marketplace/Partial/MarketplaceAdmin.php
namespace App\Livewire\Marketplace\Partial;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\MarketplaceItem;
use App\Models\MarketplaceOrder;
use App\Models\WalletTransaction;
use App\Models\Wallet;
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
            return [
                // Revenue Stats
                'total_revenue' => MarketplaceOrder::where('payment_status', 'paid')->sum('total_amount') ?? 0,
                'this_month_revenue' => MarketplaceOrder::where('payment_status', 'paid')
                    ->whereMonth('paid_at', now()->month)
                    ->whereYear('paid_at', now()->year)
                    ->sum('total_amount') ?? 0,
                'platform_earnings' => MarketplaceOrder::where('payment_status', 'paid')->sum('platform_commission') ?? 0,
                'vendor_earnings' => MarketplaceOrder::where('payment_status', 'paid')->sum('vendor_earning') ?? 0,
                
                // Order Stats
                'total_orders' => MarketplaceOrder::count() ?? 0,
                'pending_orders' => MarketplaceOrder::where('status', 'pending')->count() ?? 0,
                'completed_orders' => MarketplaceOrder::where('status', 'completed')->count() ?? 0,
                'failed_orders' => MarketplaceOrder::where('status', 'failed')->count() ?? 0,
                
                // Vendor Stats
                'total_vendors' => User::where('role', 'instructor')
                    ->orWhere('metadata', 'LIKE', '%"vendor_approved":true%')->count() ?? 0,
                'pending_applications' => User::where('role', 'student')
                    ->where(function($q) {
                        $q->whereNull('metadata')
                          ->orWhere('metadata', 'NOT LIKE', '%"vendor_approved"%')
                          ->orWhere('metadata', 'NOT LIKE', '%"vendor_rejected"%');
                    })->count() ?? 0,
                'suspended_vendors' => User::where('metadata', 'LIKE', '%"vendor_suspended":true%')->count() ?? 0,
                
                // Payout Stats
                'pending_payouts' => $this->getPendingPayouts(),
                'processed_payouts' => $this->getProcessedPayouts(),
                'payout_requests' => $this->getPendingPayoutRequests(),
                
                // Item Stats
                'total_items' => MarketplaceItem::count() ?? 0,
                'published_items' => MarketplaceItem::where('status', 'approved')->count() ?? 0,
                'pending_approval' => MarketplaceItem::where('status', 'pending')->count() ?? 0,
                'suspended_items' => MarketplaceItem::where('status', 'suspended')->count() ?? 0,
            ];
        });
    }

    private function getPendingPayouts()
    {
        if (class_exists('App\Models\Withdrawal')) {
            return \App\Models\Withdrawal::where('status', 'pending')->sum('amount') ?? 0;
        }
        return 0;
    }

    private function getProcessedPayouts()
    {
        if (class_exists('App\Models\Withdrawal')) {
            return \App\Models\Withdrawal::where('status', 'completed')->sum('amount') ?? 0;
        }
        return 0;
    }

    private function getPendingPayoutRequests()
    {
        if (class_exists('App\Models\Withdrawal')) {
            return \App\Models\Withdrawal::where('status', 'pending')->count() ?? 0;
        }
        return 0;
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

    public function viewVendorDetails($userId)
    {
        // This could open a modal or redirect to a detailed view
        session()->flash('info', 'Vendor details view coming soon.');
    }

    // === ITEM MANAGEMENT METHODS ===
    public function approveItem($itemId)
    {
        $item = MarketplaceItem::findOrFail($itemId);
        $item->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id()
        ]);
        
        session()->flash('message', 'Item approved successfully.');
        $this->loadDashboardStats();
    }

    public function rejectItem($itemId)
    {
        $item = MarketplaceItem::findOrFail($itemId);
        $item->update([
            'status' => 'rejected',
            'rejection_reason' => 'Rejected by admin',
            'approved_by' => auth()->id()
        ]);
        
        session()->flash('message', 'Item rejected successfully.');
        $this->loadDashboardStats();
    }

    public function suspendItem($itemId)
    {
        $item = MarketplaceItem::findOrFail($itemId);
        $item->update(['status' => 'suspended']);
        
        session()->flash('message', 'Item suspended successfully.');
        $this->loadDashboardStats();
    }

    public function toggleFeatureItem($itemId)
    {
        $item = MarketplaceItem::findOrFail($itemId);
        $item->update(['is_featured' => !$item->is_featured]);
        
        $status = $item->is_featured ? 'featured' : 'unfeatured';
        session()->flash('message', "Item {$status} successfully.");
    }

    public function viewItem($itemId)
    {
        session()->flash('info', 'Item details view coming soon.');
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
            
            // Update order status
            $order->update([
                'status' => 'refunded',
                'payment_status' => 'refunded',
                'admin_notes' => $refundReason,
            ]);

            // Process refund if wallet system exists
            if (class_exists('App\Models\Wallet')) {
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
                if (class_exists('App\Models\Wallet')) {
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

    public function exportData()
    {
        session()->flash('info', 'Export functionality coming soon.');
    }

    public function exportVendors()
    {
        session()->flash('info', 'Vendor export functionality coming soon.');
    }

    public function exportFinancialData()
    {
        session()->flash('info', 'Financial data export functionality coming soon.');
    }

    public function refreshStats()
    {
        Cache::forget('marketplace_admin_stats');
        $this->loadDashboardStats();
        session()->flash('message', 'Statistics refreshed successfully.');
    }

    public function viewTransaction($transactionId)
    {
        session()->flash('info', 'Transaction details view coming soon.');
    }

    public function markTransactionCompleted($transactionId)
    {
        if (class_exists('App\Models\WalletTransaction')) {
            $transaction = WalletTransaction::findOrFail($transactionId);
            $transaction->update(['status' => 'completed']);
            session()->flash('message', 'Transaction marked as completed.');
        }
    }

    // === HELPER METHODS ===
    private function getVendorApplications()
    {
        $query = User::query()
            ->whereIn('role', ['student', 'instructor', 'mentor'])
            ->with(['marketplaceItems', 'customerOrders', 'vendorOrders']);

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
        $query = MarketplaceItem::with(['vendor']);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
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
        if (!class_exists('App\Models\WalletTransaction')) {
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
        if (!class_exists('App\Models\Withdrawal')) {
            return collect()->paginate(10);
        }
        
        $query = \App\Models\Withdrawal::with(['user'])
            ->where('status', 'pending');

        if ($this->search) {
            $query->whereHas('user', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        return $query->latest()->paginate(10);
    }

    public function render()
    {
        $data = ['stats' => $this->stats];

        switch ($this->activeTab) {
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