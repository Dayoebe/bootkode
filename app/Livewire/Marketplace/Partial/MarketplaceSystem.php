<?php
// app/Livewire/Marketplace/MarketplaceSystem.php
namespace App\Livewire\Marketplace\Partial;

use Livewire\Component;
use App\Models\MarketplaceItem;
use App\Models\MarketplaceOrder;
use App\Models\MarketplaceCategory;
use App\Models\User;
use App\Models\ProductReview;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class MarketplaceSystem extends Component
{
    public $activeTab = 'overview';
    
    // Settings Properties
    public $platformCommission = 20;
    public $autoApproveInstructors = true;
    public $requireAdminApproval = false;
    public $enableReviews = true;
    public $enablePromotions = true;
    public $maxThumbnailSize = 2048;
    public $maxImageCount = 10;
    public $maxFileSize = 10240;
    public $defaultCurrency = 'NGN';
    public $minimumPrice = 100;
    public $maximumPrice = 1000000100;
    public $emailNewOrders = true;
    public $emailItemApprovals = true;
    public $emailPayouts = true;
    public $marketplaceTitle = 'Bootkode Marketplace';
    public $marketplaceDescription = 'Discover courses, digital resources, and services from expert instructors';
    public $marketplaceKeywords = 'online courses, digital resources, programming, web development';
    public $allowedFileTypes = ['pdf', 'zip', 'docx', 'pptx', 'xlsx'];
    public $newFileType = '';

    // Analytics Properties
    public $dateRange = '30';
    public $selectedMetric = 'revenue';

    // Management Properties
    public $searchTerm = '';
    public $statusFilter = '';
    public $typeFilter = '';
    public $vendorFilter = '';
    public $sortBy = 'created_at';
    public $sortOrder = 'desc';
    
    // Modal Properties
    public $showItemModal = false;
    public $showOrderModal = false;
    public $showVendorModal = false;
    public $selectedItem = null;
    public $selectedOrder = null;
    public $selectedVendor = null;
    public $rejectionReason = '';

    protected $rules = [
        'platformCommission' => 'required|numeric|min:0|max:100',
        'maxThumbnailSize' => 'required|integer|min:100|max:10240',
        'maxImageCount' => 'required|integer|min:1|max:50',
        'maxFileSize' => 'required|integer|min:100|max:102400',
        'minimumPrice' => 'required|numeric|min:0',
        'maximumPrice' => 'required|numeric|min:100',
        'marketplaceTitle' => 'required|string|max:255',
        'marketplaceDescription' => 'required|string|max:500',
        'marketplaceKeywords' => 'required|string|max:1000',
    ];

    protected $listeners = [
        'refreshComponent' => '$refresh',
        'itemStatusChanged' => 'handleItemStatusChange',
        'orderStatusChanged' => 'handleOrderStatusChange',
    ];

    public function mount()
    {
        $this->loadSettings();
    }

    public function updatedActiveTab()
    {
        // Reset filters when switching tabs
        $this->reset(['searchTerm', 'statusFilter', 'typeFilter', 'vendorFilter']);
    }

    // Settings Methods
    protected function loadSettings()
    {
        $settings = Cache::get('marketplace_settings', []);
        
        $this->platformCommission = $settings['platform_commission'] ?? config('marketplace.commission_rate', 20);
        $this->autoApproveInstructors = $settings['auto_approve_instructors'] ?? config('marketplace.auto_approve_instructors', true);
        $this->requireAdminApproval = $settings['require_admin_approval'] ?? config('marketplace.require_admin_approval', false);
        $this->enableReviews = $settings['enable_reviews'] ?? true;
        $this->enablePromotions = $settings['enable_promotions'] ?? true;
        $this->maxThumbnailSize = $settings['max_thumbnail_size'] ?? config('marketplace.max_thumbnail_size', 2048);
        $this->maxImageCount = $settings['max_image_count'] ?? config('marketplace.max_image_count', 10);
        $this->maxFileSize = $settings['max_file_size'] ?? config('marketplace.max_file_size', 10240);
        $this->defaultCurrency = $settings['default_currency'] ?? config('marketplace.default_currency', 'NGN');
        $this->minimumPrice = $settings['minimum_price'] ?? 100;
        $this->maximumPrice = $settings['maximum_price'] ?? 1000000100;
        $this->emailNewOrders = $settings['email_new_orders'] ?? true;
        $this->emailItemApprovals = $settings['email_item_approvals'] ?? true;
        $this->emailPayouts = $settings['email_payouts'] ?? true;
        $this->marketplaceTitle = $settings['marketplace_title'] ?? 'Bootkode Marketplace';
        $this->marketplaceDescription = $settings['marketplace_description'] ?? 'Discover courses, digital resources, and services from expert instructors';
        $this->marketplaceKeywords = $settings['marketplace_keywords'] ?? 'online courses, digital resources, programming, web development';
        $this->allowedFileTypes = $settings['allowed_file_types'] ?? config('marketplace.allowed_file_types', ['pdf', 'zip', 'docx', 'pptx', 'xlsx']);
    }

    public function saveSettings()
    {
        $this->validate();

        $settings = [
            'platform_commission' => $this->platformCommission,
            'auto_approve_instructors' => $this->autoApproveInstructors,
            'require_admin_approval' => $this->requireAdminApproval,
            'enable_reviews' => $this->enableReviews,
            'enable_promotions' => $this->enablePromotions,
            'max_thumbnail_size' => $this->maxThumbnailSize,
            'max_image_count' => $this->maxImageCount,
            'max_file_size' => $this->maxFileSize,
            'default_currency' => $this->defaultCurrency,
            'minimum_price' => $this->minimumPrice,
            'maximum_price' => $this->maximumPrice,
            'email_new_orders' => $this->emailNewOrders,
            'email_item_approvals' => $this->emailItemApprovals,
            'email_payouts' => $this->emailPayouts,
            'marketplace_title' => $this->marketplaceTitle,
            'marketplace_description' => $this->marketplaceDescription,
            'marketplace_keywords' => $this->marketplaceKeywords,
            'allowed_file_types' => $this->allowedFileTypes,
            'updated_at' => now(),
            'updated_by' => auth()->id(),
        ];

        Cache::put('marketplace_settings', $settings, now()->addDays(30));
        
        activity()
            ->causedBy(auth()->user())
            ->withProperties($settings)
            ->log('Updated marketplace settings');

        session()->flash('message', 'Marketplace settings saved successfully!');
    }

    public function addFileType()
    {
        if (!empty($this->newFileType) && !in_array($this->newFileType, $this->allowedFileTypes)) {
            $this->allowedFileTypes[] = trim($this->newFileType);
            $this->newFileType = '';
        }
    }

    public function removeFileType($type)
    {
        $this->allowedFileTypes = array_filter($this->allowedFileTypes, fn($t) => $t !== $type);
        $this->allowedFileTypes = array_values($this->allowedFileTypes);
    }

    public function resetToDefaults()
    {
        Cache::forget('marketplace_settings');
        $this->loadSettings();
        session()->flash('message', 'Settings reset to defaults!');
    }

    // Item Management Methods
    public function approveItem($itemId)
    {
        $item = MarketplaceItem::findOrFail($itemId);
        $item->approve(auth()->id());
        
        activity()
            ->causedBy(auth()->user())
            ->performedOn($item)
            ->log("Marketplace item '{$item->title}' was approved");

        session()->flash('message', 'Item approved successfully!');
        $this->dispatch('itemStatusChanged');
    }

    public function rejectItem($itemId)
    {
        if (empty($this->rejectionReason)) {
            session()->flash('error', 'Please provide a rejection reason.');
            return;
        }

        $item = MarketplaceItem::findOrFail($itemId);
        $item->reject($this->rejectionReason, auth()->id());
        
        activity()
            ->causedBy(auth()->user())
            ->performedOn($item)
            ->withProperties(['reason' => $this->rejectionReason])
            ->log("Marketplace item '{$item->title}' was rejected");

        session()->flash('message', 'Item rejected successfully!');
        $this->rejectionReason = '';
        $this->showItemModal = false;
        $this->dispatch('itemStatusChanged');
    }

    public function suspendItem($itemId)
    {
        $item = MarketplaceItem::findOrFail($itemId);
        $item->suspend();
        
        session()->flash('message', 'Item suspended successfully!');
        $this->dispatch('itemStatusChanged');
    }

    public function viewItem($itemId)
    {
        $this->selectedItem = MarketplaceItem::with(['vendor', 'reviews', 'orders'])->findOrFail($itemId);
        $this->showItemModal = true;
    }

    public function closeItemModal()
    {
        $this->showItemModal = false;
        $this->selectedItem = null;
        $this->rejectionReason = '';
    }

    // Order Management Methods
    public function viewOrder($orderId)
    {
        $this->selectedOrder = MarketplaceOrder::with(['customer', 'vendor', 'item'])->findOrFail($orderId);
        $this->showOrderModal = true;
    }

    public function updateOrderStatus($orderId, $status)
    {
        $order = MarketplaceOrder::findOrFail($orderId);
        $oldStatus = $order->status;
        
        $order->update(['status' => $status]);
        
        if ($status === MarketplaceOrder::STATUS_COMPLETED) {
            $order->complete();
        }
        
        session()->flash('message', 'Order status updated successfully!');
        $this->dispatch('orderStatusChanged');
    }

    public function refundOrder($orderId)
    {
        $order = MarketplaceOrder::findOrFail($orderId);
        $order->refund(null, 'Refunded by admin');
        
        session()->flash('message', 'Order refunded successfully!');
        $this->dispatch('orderStatusChanged');
    }

    public function closeOrderModal()
    {
        $this->showOrderModal = false;
        $this->selectedOrder = null;
    }

    // Vendor Management Methods
    public function viewVendor($vendorId)
    {
        $this->selectedVendor = User::with(['marketplaceItems', 'vendorOrders'])->findOrFail($vendorId);
        $this->showVendorModal = true;
    }

    public function closeVendorModal()
    {
        $this->showVendorModal = false;
        $this->selectedVendor = null;
    }

    // Data Methods
    public function getMarketplaceStats()
    {
        return Cache::remember('marketplace_stats', now()->addMinutes(10), function () {
            return [
                'total_items' => MarketplaceItem::count(),
                'published_items' => MarketplaceItem::published()->count(),
                'pending_items' => MarketplaceItem::where('status', MarketplaceItem::STATUS_PENDING)->count(),
                'total_vendors' => User::whereHas('marketplaceItems')->count(),
                'total_orders' => MarketplaceOrder::count(),
                'total_revenue' => MarketplaceOrder::paid()->sum('total_amount'),
                'platform_commission' => MarketplaceOrder::paid()->sum('platform_commission'),
                'vendor_earnings' => MarketplaceOrder::paid()->sum('vendor_earning'),
                'pending_orders' => MarketplaceOrder::where('status', MarketplaceOrder::STATUS_PENDING)->count(),
                'completed_orders' => MarketplaceOrder::where('status', MarketplaceOrder::STATUS_COMPLETED)->count(),
            ];
        });
    }

    public function getAnalyticsData()
    {
        $startDate = now()->subDays((int)$this->dateRange);

        // Top categories
        $topCategories = MarketplaceItem::published()
            ->join('marketplace_item_categories', 'marketplace_items.id', '=', 'marketplace_item_categories.marketplace_item_id')
            ->join('marketplace_categories', 'marketplace_item_categories.marketplace_category_id', '=', 'marketplace_categories.id')
            ->selectRaw('marketplace_categories.name as category, COUNT(*) as count, SUM(marketplace_items.sales_count) as total_sales')
            ->groupBy('marketplace_categories.id', 'marketplace_categories.name')
            ->orderBy('total_sales', 'desc')
            ->limit(10)
            ->get();

        // Top vendors
        $topVendors = User::withCount(['marketplaceItems as total_listings'])
            ->withSum(['vendorOrders as total_earnings' => function($query) {
                $query->paid();
            }], 'vendor_earning')
            ->having('total_listings', '>', 0)
            ->orderBy('total_earnings', 'desc')
            ->limit(10)
            ->get();

        // Revenue trend
        $revenueTrend = MarketplaceOrder::paid()
            ->where('paid_at', '>=', $startDate)
            ->selectRaw('DATE(paid_at) as date, COUNT(*) as orders, SUM(total_amount) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Fill missing dates for chart
        $chartData = [];
        for ($date = $startDate->copy(); $date <= now(); $date->addDay()) {
            $dateKey = $date->format('Y-m-d');
            $data = $revenueTrend[$dateKey] ?? (object)['orders' => 0, 'revenue' => 0];
            
            $chartData[] = [
                'date' => $dateKey,
                'formatted_date' => $date->format('M d'),
                'orders' => $data->orders,
                'revenue' => $data->revenue,
            ];
        }

        return [
            'topCategories' => $topCategories,
            'topVendors' => $topVendors,
            'chartData' => $chartData,
        ];
    }

    public function getItemsQuery()
    {
        $query = MarketplaceItem::with(['vendor', 'categories'])
            ->when($this->searchTerm, function ($q) {
                $q->where('title', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $this->searchTerm . '%');
            })
            ->when($this->statusFilter, function ($q) {
                $q->where('status', $this->statusFilter);
            })
            ->when($this->typeFilter, function ($q) {
                $q->where('type', $this->typeFilter);
            })
            ->when($this->vendorFilter, function ($q) {
                $q->where('vendor_id', $this->vendorFilter);
            });

        return $query->orderBy($this->sortBy, $this->sortOrder);
    }

    public function getOrdersQuery()
    {
        $query = MarketplaceOrder::with(['customer', 'vendor', 'item'])
            ->when($this->searchTerm, function ($q) {
                $q->where('order_number', 'like', '%' . $this->searchTerm . '%')
                  ->orWhereHas('item', function ($subQ) {
                      $subQ->where('title', 'like', '%' . $this->searchTerm . '%');
                  });
            })
            ->when($this->statusFilter, function ($q) {
                $q->where('status', $this->statusFilter);
            });

        return $query->orderBy($this->sortBy, $this->sortOrder);
    }

    public function getRecentActivity()
    {
        return Cache::remember('marketplace_recent_activity', now()->addMinutes(5), function () {
            $activities = [];
            
            // Recent items
            $recentItems = MarketplaceItem::with('vendor')
                ->latest()
                ->limit(5)
                ->get()
                ->map(function ($item) {
                    return [
                        'type' => 'item',
                        'action' => 'created',
                        'subject' => $item->title,
                        'user' => $item->vendor->name,
                        'time' => $item->created_at,
                        'status' => $item->status,
                    ];
                });

            // Recent orders
            $recentOrders = MarketplaceOrder::with(['customer', 'item'])
                ->latest()
                ->limit(5)
                ->get()
                ->map(function ($order) {
                    return [
                        'type' => 'order',
                        'action' => 'placed',
                        'subject' => $order->item->title,
                        'user' => $order->customer->name,
                        'time' => $order->created_at,
                        'amount' => $order->total_amount,
                    ];
                });

            return $recentItems->merge($recentOrders)
                ->sortByDesc('time')
                ->take(10)
                ->values()
                ->toArray();
        });
    }

    public function handleItemStatusChange()
    {
        $this->dispatch('refreshComponent');
    }

    public function handleOrderStatusChange()
    {
        $this->dispatch('refreshComponent');
    }

    public function render()
    {
        $stats = $this->getMarketplaceStats();
        $analytics = $this->getAnalyticsData();
        $recentActivity = $this->getRecentActivity();

        return view('livewire.marketplace.partial.marketplace-system', [
            'stats' => $stats,
            'topCategories' => $analytics['topCategories'],
            'topVendors' => $analytics['topVendors'],
            'chartData' => $analytics['chartData'],
            'recentActivity' => $recentActivity,
            'items' => $this->getItemsQuery()->paginate(10, ['*'], 'items_page'),
            'orders' => $this->getOrdersQuery()->paginate(10, ['*'], 'orders_page'),
            'currencies' => [
                'NGN' => 'Nigerian Naira (₦)',
                'USD' => 'US Dollar ($)',
                'EUR' => 'Euro (€)',
                'GBP' => 'British Pound (£)',
            ],
            'categories' => MarketplaceCategory::active()->ordered()->get(),
            'vendors' => User::whereHas('marketplaceItems')->get(),
        ]);
    }
}