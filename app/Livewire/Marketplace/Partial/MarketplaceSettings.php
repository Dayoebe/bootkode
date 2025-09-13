<?php
// app/Livewire/Marketplace/Partial/MarketplaceSettings.php
namespace App\Livewire\Marketplace\Partial;

use Livewire\Component;
use Illuminate\Support\Facades\Cache;

class MarketplaceSettings extends Component
{
    // General Settings
    public $platformCommission = 20;
    public $autoApproveInstructors = true;
    public $requireAdminApproval = false;
    public $enableReviews = true;
    public $enablePromotions = true;
    
    // File Upload Settings
    public $maxThumbnailSize = 2048; // KB
    public $maxImageCount = 10;
    public $maxFileSize = 10240; // KB
    
    // Payment Settings
    public $defaultCurrency = 'NGN';
    public $minimumPrice = 100;
    public $maximumPrice = 1000000;
    
    // Notification Settings
    public $emailNewOrders = true;
    public $emailItemApprovals = true;
    public $emailPayouts = true;
    
    // SEO Settings
    public $marketplaceTitle = 'Bootkode Marketplace';
    public $marketplaceDescription = 'Discover courses, digital resources, and services from expert instructors';
    public $marketplaceKeywords = 'online courses, digital resources, programming, web development';
    
    // Allowed file types
    public $allowedFileTypes = ['pdf', 'zip', 'docx', 'pptx', 'xlsx'];
    public $newFileType = '';

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

    public function mount()
    {
        $this->loadSettings();
    }

    protected function loadSettings()
    {
        // Load settings from cache/config
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
        $this->maximumPrice = $settings['maximum_price'] ?? 1000000;
        
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
        
        // Log the settings change
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
        $this->allowedFileTypes = array_values($this->allowedFileTypes); // Re-index array
    }

    public function resetToDefaults()
    {
        Cache::forget('marketplace_settings');
        $this->loadSettings();
        session()->flash('message', 'Settings reset to defaults!');
    }

    public function getMarketplaceStats()
    {
        return [
            'total_items' => \App\Models\MarketplaceItem::count(),
            'published_items' => \App\Models\MarketplaceItem::published()->count(),
            'pending_items' => \App\Models\MarketplaceItem::where('status', 'pending')->count(),
            'total_vendors' => \App\Models\User::whereHas('marketplaceItems')->count(),
            'total_orders' => \App\Models\MarketplaceOrder::count(),
            'total_revenue' => \App\Models\MarketplaceOrder::where('payment_status', 'paid')->sum('total_amount'),
        ];
    }

    public function render()
    {
        return view('livewire.marketplace.partial.marketplace-settings', [
            'stats' => $this->getMarketplaceStats(),
            'currencies' => [
                'NGN' => 'Nigerian Naira (₦)',
                'USD' => 'US Dollar ($)',
                'EUR' => 'Euro (€)',
                'GBP' => 'British Pound (£)',
            ],
        ]);
    }
}