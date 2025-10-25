<?php
// app/Livewire/Marketplace/Partial/MarketplaceBrowse.php
namespace App\Livewire\Marketplace\Partial;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Marketplace\MarketplaceItem;
use App\Models\Marketplace\MarketplaceCategory;

class MarketplaceBrowse extends Component
{
    use WithPagination;

    // Internal navigation state
    public $currentView = 'browse'; // browse, categories, product-details
    public $selectedItemId = null;
    
    // Browse filters
    public $search = '';
    public $type = '';
    public $category = '';
    public $sortBy = 'created_at';
    public $sortOrder = 'desc';
    public $minPrice = '';
    public $maxPrice = '';

    // Category management (for admins)
    public $showCreateCategoryForm = false;
    public $showEditCategoryForm = false;
    public $editingCategory = null;
    
    // Category form fields
    public $categoryName = '';
    public $categoryDescription = '';
    public $categoryIcon = 'fas fa-folder';
    public $categoryColor = '#6366f1';
    public $categoryIsFeatured = false;
    public $categorySortOrder = 0;

    protected $queryString = [
        'search' => ['except' => ''],
        'type' => ['except' => ''],
        'category' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortOrder' => ['except' => 'desc'],
        'currentView' => ['except' => 'browse'],
    ];

    protected $categoryRules = [
        'categoryName' => 'required|string|max:255',
        'categoryDescription' => 'nullable|string|max:500',
        'categoryIcon' => 'required|string|max:50',
        'categoryColor' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        'categoryIsFeatured' => 'boolean',
        'categorySortOrder' => 'integer|min:0',
    ];

    // Available icons for category selection
    public $availableIcons = [
        'fas fa-code' => 'Programming',
        'fas fa-paint-brush' => 'Design',
        'fas fa-briefcase' => 'Business',
        'fas fa-bullhorn' => 'Marketing',
        'fas fa-chart-bar' => 'Analytics',
        'fas fa-globe' => 'Web',
        'fas fa-mobile-alt' => 'Mobile',
        'fas fa-server' => 'Server',
        'fas fa-database' => 'Database',
        'fas fa-shield-alt' => 'Security',
        'fas fa-robot' => 'AI/ML',
        'fas fa-gamepad' => 'Gaming',
        'fas fa-camera' => 'Photography',
        'fas fa-music' => 'Music',
        'fas fa-book' => 'Education',
        'fas fa-heart' => 'Health',
        'fas fa-car' => 'Automotive',
        'fas fa-home' => 'Lifestyle',
    ];

    public function mount()
    {
        // Set initial view based on route or parameters
        $this->setInitialView();
    }

    // View navigation methods
    public function showBrowse()
    {
        $this->currentView = 'browse';
        $this->resetPage();
    }

    public function showCategories()
    {
        $this->currentView = 'categories';
        $this->resetPage();
    }

    public function showProductDetails($itemId = null)
    {
        $this->currentView = 'product-details';
        $this->selectedItemId = $itemId;
    }

    public function backToBrowse()
    {
        $this->currentView = 'browse';
        $this->selectedItemId = null;
    }

    // Search and filter methods
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingType()
    {
        $this->resetPage();
    }

    public function updatingCategory()
    {
        $this->resetPage();
    }

    public function selectCategory($categoryId)
    {
        $this->category = $categoryId;
        $this->showBrowse();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->type = '';
        $this->category = '';
        $this->minPrice = '';
        $this->maxPrice = '';
        $this->resetPage();
    }

    // Category management methods
    public function openCreateCategoryForm()
    {
        $this->resetCategoryForm();
        $this->showCreateCategoryForm = true;
    }

    public function closeCreateCategoryForm()
    {
        $this->showCreateCategoryForm = false;
        $this->resetCategoryForm();
    }

    public function openEditCategoryForm($categoryId)
    {
        $category = MarketplaceCategory::findOrFail($categoryId);
        
        $this->editingCategory = $category;
        $this->categoryName = $category->name;
        $this->categoryDescription = $category->description;
        $this->categoryIcon = $category->icon;
        $this->categoryColor = $category->color;
        $this->categoryIsFeatured = $category->is_featured;
        $this->categorySortOrder = $category->sort_order;
        
        $this->showEditCategoryForm = true;
    }

    public function closeEditCategoryForm()
    {
        $this->showEditCategoryForm = false;
        $this->editingCategory = null;
        $this->resetCategoryForm();
    }

    public function createCategory()
    {
        $this->validate($this->categoryRules);

        MarketplaceCategory::create([
            'name' => $this->categoryName,
            'description' => $this->categoryDescription,
            'icon' => $this->categoryIcon,
            'color' => $this->categoryColor,
            'is_featured' => $this->categoryIsFeatured,
            'sort_order' => $this->categorySortOrder,
            'is_active' => true,
        ]);

        session()->flash('message', 'Category created successfully!');
        $this->closeCreateCategoryForm();
    }

    public function updateCategory()
    {
        $this->validate($this->categoryRules);

        if ($this->editingCategory) {
            $this->editingCategory->update([
                'name' => $this->categoryName,
                'description' => $this->categoryDescription,
                'icon' => $this->categoryIcon,
                'color' => $this->categoryColor,
                'is_featured' => $this->categoryIsFeatured,
                'sort_order' => $this->categorySortOrder,
            ]);

            session()->flash('message', 'Category updated successfully!');
            $this->closeEditCategoryForm();
        }
    }

    public function toggleCategoryActive($categoryId)
    {
        $category = MarketplaceCategory::findOrFail($categoryId);
        $category->update(['is_active' => !$category->is_active]);
        
        $status = $category->is_active ? 'activated' : 'deactivated';
        session()->flash('message', "Category {$status} successfully!");
    }

    public function toggleCategoryFeatured($categoryId)
    {
        $category = MarketplaceCategory::findOrFail($categoryId);
        $category->update(['is_featured' => !$category->is_featured]);
        
        $status = $category->is_featured ? 'featured' : 'unfeatured';
        session()->flash('message', "Category {$status} successfully!");
    }

    public function deleteCategory($categoryId)
    {
        $category = MarketplaceCategory::findOrFail($categoryId);
        
        if ($category->items()->count() > 0) {
            session()->flash('error', 'Cannot delete category with existing items. Please move or delete the items first.');
            return;
        }

        $category->delete();
        session()->flash('message', 'Category deleted successfully!');
    }

    // Helper methods
    private function setInitialView()
    {
        // You can set logic here based on route parameters if needed
        // For now, defaults to 'browse'
    }

    private function resetCategoryForm()
    {
        $this->categoryName = '';
        $this->categoryDescription = '';
        $this->categoryIcon = 'fas fa-folder';
        $this->categoryColor = '#6366f1';
        $this->categoryIsFeatured = false;
        $this->categorySortOrder = 0;
        $this->resetValidation();
    }

    protected function getCategories()
    {
        return MarketplaceCategory::active()->ordered()->get();
    }

    public function render()
    {
        $data = [];

        // Get data based on current view
        switch ($this->currentView) {
            case 'browse':
                // Map the sortBy to the correct database column
                $sortColumn = match($this->sortBy) {
                    'price' => 'price',
                    'average_rating' => 'average_rating',
                    'sales_count' => 'sales_count', // Changed from 'sales'
                    default => 'created_at'
                };
                
                $items = MarketplaceItem::published()
                    ->when($this->search, function ($query) {
                        $query->where(function ($q) {
                            $q->where('title', 'like', '%' . $this->search . '%')
                                ->orWhere('description', 'like', '%' . $this->search . '%')
                                ->orWhere('keywords', 'like', '%' . $this->search . '%');
                        });
                    })
                    ->when($this->type, fn($query) => $query->byType($this->type))
                    ->when($this->category, function ($query) {
                        if (is_numeric($this->category)) {
                            $query->inCategories([$this->category]);
                        } else {
                            $query->inCategory($this->category);
                        }
                    })
                    ->when($this->minPrice, fn($query) => $query->where('price', '>=', $this->minPrice))
                    ->when($this->maxPrice, fn($query) => $query->where('price', '<=', $this->maxPrice))
                    ->orderBy($sortColumn, $this->sortOrder) // Use the mapped column
                    ->paginate(12);
    
                $featuredItems = MarketplaceItem::published()->featured()->limit(6)->get();
    
                $data = [
                    'items' => $items,
                    'featuredItems' => $featuredItems,
                    'types' => MarketplaceItem::TYPES,
                    'categories' => $this->getCategories(),
                ];
                
                break;

            case 'categories':
                $categories = MarketplaceCategory::withCount(['items' => function($query) {
                    $query->published();
                }])->ordered()->paginate(12);

                $featuredCategories = MarketplaceCategory::featured()
                    ->active()
                    ->withCount(['items' => function($query) {
                        $query->published();
                    }])
                    ->ordered()
                    ->limit(8)
                    ->get();

                $data = [
                    'categories' => $categories,
                    'featuredCategories' => $featuredCategories,
                ];
                break;

            case 'product-details':
                $selectedItem = null;
                if ($this->selectedItemId) {
                    $selectedItem = MarketplaceItem::published()
                        ->with(['vendor', 'reviews' => fn($q) => $q->approved()->latest()->limit(5)])
                        ->find($this->selectedItemId);
                }

                $data = [
                    'selectedItem' => $selectedItem,
                ];
                break;
        }

        $data['currentView'] = $this->currentView;
        $data['canManageCategories'] = auth()->user() && (auth()->user()->isSuperAdmin() || auth()->user()->isAcademyAdmin());
        $data['availableIcons'] = $this->availableIcons;

        return view('livewire.marketplace.partial.marketplace-browse', $data);
    }
}