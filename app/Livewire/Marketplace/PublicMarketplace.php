<?php
// app/Livewire/Marketplace/PublicMarketplace.php
namespace App\Livewire\Marketplace;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Marketplace\MarketplaceItem;
use App\Models\Marketplace\MarketplaceCategory;
use App\Models\Core\User;
use App\Services\MarketplaceSearchService;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;

#[Layout('layouts.app', [
    'title' => 'Marketplace', 
    'description' => 'Browse, buy and sell courses, resources and services', 
    'icon' => 'fas fa-store', 
    'active' => 'marketplace'
])]


class PublicMarketplace extends Component
{
    use WithPagination;

    public $activeView = 'browse'; // browse, product, category, vendor, search
    public $selectedProduct = null;
    public $selectedCategory = null;
    public $selectedVendor = null;
    
    // Search and Filter Properties
    public $searchTerm = '';
    public $selectedCategories = [];
    public $selectedTypes = [];
    public $priceMin = 0;
    public $priceMax = 1000000100;
    public $sortBy = 'featured'; // featured, latest, price_low, price_high, popular, rating
    public $minRating = 0;
    
    // UI Properties
    public $viewMode = 'grid'; // grid, list
    public $itemsPerPage = 12;
    public $showFilters = false;
    public $showLoginModal = false;
    public $loginAction = ''; // purchase, wishlist, review
    
    // Featured Content
    public $featuredItems = [];
    public $popularCategories = [];
    public $topVendors = [];
    
    protected $queryString = [
        'searchTerm' => ['except' => ''],
        'selectedCategories' => ['except' => []],
        'selectedTypes' => ['except' => []],
        'sortBy' => ['except' => 'featured'],
        'activeView' => ['except' => 'browse'],
    ];

    protected $listeners = [
        'productSelected' => 'viewProduct',
        'categorySelected' => 'viewCategory',
        'vendorSelected' => 'viewVendor',
        'searchUpdated' => 'updateSearch',
    ];

    public function mount($slug = null, $type = null)
    {
        // Handle direct URL navigation
        if ($slug && $type) {
            switch ($type) {
                case 'product':
                    $this->viewProductBySlug($slug);
                    break;
                case 'category':
                    $this->viewCategoryBySlug($slug);
                    break;
                case 'vendor':
                    $this->viewVendorBySlug($slug);
                    break;
            }
        }
        
        $this->loadFeaturedContent();
    }

    public function loadFeaturedContent()
    {
        $this->featuredItems = Cache::remember('public_marketplace_featured', now()->addHours(2), function () {
            return MarketplaceItem::published()
                ->featured()
                ->with(['vendor', 'categories'])
                ->limit(8)
                ->get();
        });

        $this->popularCategories = Cache::remember('public_marketplace_categories', now()->addHours(4), function () {
            return MarketplaceCategory::active()
                ->withCount(['items' => function ($query) {
                    $query->published();
                }])
                ->having('items_count', '>', 0)
                ->orderBy('items_count', 'desc')
                ->limit(6)
                ->get();
        });

        $this->topVendors = Cache::remember('public_marketplace_vendors', now()->addHours(6), function () {
            return User::whereHas('marketplaceItems', function ($query) {
                $query->published();
            })
            ->withCount(['marketplaceItems as published_items_count' => function ($query) {
                $query->published();
            }])
            ->withAvg(['marketplaceItems as avg_rating' => function ($query) {
                $query->published();
            }], 'average_rating')
            ->having('published_items_count', '>', 0)
            ->orderBy('avg_rating', 'desc')
            ->orderBy('published_items_count', 'desc')
            ->limit(6)
            ->get();
        });
    }

    public function updatedSearchTerm()
    {
        $this->resetPage();
        $this->activeView = 'search';
    }

    public function updatedSelectedCategories()
    {
        $this->resetPage();
    }

    public function updatedSortBy()
    {
        $this->resetPage();
    }

    public function viewProduct($productId)
    {
        $this->selectedProduct = MarketplaceItem::published()
            ->with(['vendor', 'itemCategories', 'reviews.user']) // Changed to itemCategories
            ->findOrFail($productId);
        
        // Increment view count
        $this->selectedProduct->incrementViews();
        
        $this->activeView = 'product';
        $this->dispatch('product-viewed', $this->selectedProduct->id);
    }

    public function viewProductBySlug($slug)
    {
        $product = MarketplaceItem::published()
            ->where('slug', $slug)
            ->with(['vendor', 'categories', 'reviews.user'])
            ->first();
            
        if ($product) {
            $this->selectedProduct = $product;
            $product->incrementViews();
            $this->activeView = 'product';
        }
    }

    public function viewCategory($categoryId)
    {
        $this->selectedCategory = MarketplaceCategory::active()->findOrFail($categoryId);
        $this->selectedCategories = [$categoryId];
        $this->activeView = 'category';
        $this->resetPage();
    }

    public function viewCategoryBySlug($slug)
    {
        $category = MarketplaceCategory::active()->where('slug', $slug)->first();
        if ($category) {
            $this->viewCategory($category->id);
        }
    }

    public function viewVendor($vendorId)
    {
        $this->selectedVendor = User::whereHas('marketplaceItems', function ($query) {
            $query->published();
        })->findOrFail($vendorId);
        
        $this->activeView = 'vendor';
        $this->resetPage();
    }

    public function backToBrowse()
    {
        $this->activeView = 'browse';
        $this->selectedProduct = null;
        $this->selectedCategory = null;
        $this->selectedVendor = null;
        $this->searchTerm = '';
        $this->selectedCategories = [];
        $this->resetPage();
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function clearFilters()
    {
        $this->selectedCategories = [];
        $this->selectedTypes = [];
        $this->priceMin = 0;
        $this->priceMax = 1000000100;
        $this->minRating = 0;
        $this->sortBy = 'featured';
        $this->resetPage();
    }

    public function toggleViewMode()
    {
        $this->viewMode = $this->viewMode === 'grid' ? 'list' : 'grid';
    }

    // Authentication Required Actions
    public function requireLogin($action, $context = null)
    {
        $this->loginAction = $action;
        $this->showLoginModal = true;
        
        // Store context for after login
        session()->put('marketplace_redirect_context', [
            'action' => $action,
            'context' => $context,
            'current_url' => request()->url(),
        ]);
    }

    public function closeLoginModal()
    {
        $this->showLoginModal = false;
        $this->loginAction = '';
    }

    public function redirectToLogin()
    {
        return redirect()->route('login');
    }

    public function redirectToRegister()
    {
        return redirect()->route('register');
    }

    // Data Methods
    public function getItemsQuery()
    {
        $searchService = app(MarketplaceSearchService::class);
        
        $filters = [
            'search' => $this->searchTerm,
            'categories' => $this->selectedCategories,
            'type' => $this->selectedTypes,
            'min_price' => $this->priceMin > 0 ? $this->priceMin : null,
            'max_price' => $this->priceMax < 1000000 ? $this->priceMax : null,
            'min_rating' => $this->minRating > 0 ? $this->minRating : null,
            'featured_only' => $this->sortBy === 'featured',
            'sort_by' => $this->sortBy === 'featured' ? 'created_at' : $this->sortBy,
            'sort_order' => 'desc',
        ];

        // Add vendor filter for vendor view
        if ($this->activeView === 'vendor' && $this->selectedVendor) {
            $filters['vendor_id'] = $this->selectedVendor->id;
        }

        return $searchService->search($filters);
    }

    public function getRelatedProducts()
    {
        if (!$this->selectedProduct) {
            return collect();
        }

        return MarketplaceItem::published()
            ->where('id', '!=', $this->selectedProduct->id)
            ->where(function ($query) {
                $query->where('type', $this->selectedProduct->type)
                      ->orWhere('vendor_id', $this->selectedProduct->vendor_id);
                
                // Only add category filter if categories are loaded
                if ($this->selectedProduct->itemCategories && $this->selectedProduct->itemCategories->count() > 0) {
                    $query->orWhereHas('categories', function ($catQuery) {
                        $catQuery->whereIn('marketplace_categories.id', 
                            $this->selectedProduct->itemCategories->pluck('id'));
                    });
                }
            })
            ->with(['vendor', 'categories'])
            ->inRandomOrder()
            ->limit(4)
            ->get();
    }

    public function getMarketplaceStats()
    {
        return Cache::remember('public_marketplace_stats', now()->addHours(1), function () {
            return [
                'total_products' => MarketplaceItem::published()->count(),
                'total_vendors' => User::whereHas('marketplaceItems', function ($query) {
                    $query->published();
                })->count(),
                'total_categories' => MarketplaceCategory::active()->count(),
                'total_reviews' => \App\Models\Marketplace\ProductReview::approved()->count(),
                'average_rating' => MarketplaceItem::published()
                    ->whereNotNull('average_rating')
                    ->avg('average_rating'),
            ];
        });
    }

    public function getTrendingItems()
    {
        return Cache::remember('trending_items', now()->addMinutes(30), function () {
            return MarketplaceItem::published()
                ->where('created_at', '>=', now()->subDays(7))
                ->orderBy('views_count', 'desc')
                ->orderBy('sales_count', 'desc')
                ->with(['vendor', 'categories'])
                ->limit(6)
                ->get();
        });
    }

    public function getPopularSearches()
    {
        $searchService = app(MarketplaceSearchService::class);
        return $searchService->getPopularSearches(8);
    }

    public function render()
    {
        $items = collect();
        $stats = $this->getMarketplaceStats();
        
        if (in_array($this->activeView, ['browse', 'search', 'category', 'vendor'])) {
            $items = $this->getItemsQuery()->paginate($this->itemsPerPage);
        }

        $relatedProducts = $this->activeView === 'product' ? $this->getRelatedProducts() : collect();
        $trendingItems = $this->getTrendingItems();
        $popularSearches = $this->getPopularSearches();

        return view('livewire.marketplace.public-marketplace', [
            'items' => $items,
            'stats' => $stats,
            'relatedProducts' => $relatedProducts,
            'trendingItems' => $trendingItems,
            'popularSearches' => $popularSearches,
            'allCategories' => MarketplaceCategory::active()->ordered()->get(),
            'itemTypes' => MarketplaceItem::TYPES,
        ]);
    }
}