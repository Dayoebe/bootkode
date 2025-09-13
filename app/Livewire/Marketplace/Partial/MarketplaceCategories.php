<?php
// app/Livewire/Marketplace/Partial/MarketplaceCategories.php
namespace App\Livewire\Marketplace\Partial;

use Livewire\Component;
use App\Models\MarketplaceCategory;
use App\Models\MarketplaceItem;
use Livewire\WithPagination;

class MarketplaceCategories extends Component
{
    use WithPagination;

    public $showCreateForm = false;
    public $showEditForm = false;
    public $editingCategory = null;
    
    // Form fields
    public $name = '';
    public $description = '';
    public $icon = 'fas fa-folder';
    public $color = '#6366f1';
    public $is_featured = false;
    public $sort_order = 0;

    // Available icons for selection
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

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:500',
        'icon' => 'required|string|max:50',
        'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
        'is_featured' => 'boolean',
        'sort_order' => 'integer|min:0',
    ];

    public function openCreateForm()
    {
        $this->resetForm();
        $this->showCreateForm = true;
    }

    public function closeCreateForm()
    {
        $this->showCreateForm = false;
        $this->resetForm();
    }

    public function openEditForm($categoryId)
    {
        $category = MarketplaceCategory::findOrFail($categoryId);
        
        $this->editingCategory = $category;
        $this->name = $category->name;
        $this->description = $category->description;
        $this->icon = $category->icon;
        $this->color = $category->color;
        $this->is_featured = $category->is_featured;
        $this->sort_order = $category->sort_order;
        
        $this->showEditForm = true;
    }

    public function closeEditForm()
    {
        $this->showEditForm = false;
        $this->editingCategory = null;
        $this->resetForm();
    }

    public function createCategory()
    {
        $this->validate();

        MarketplaceCategory::create([
            'name' => $this->name,
            'description' => $this->description,
            'icon' => $this->icon,
            'color' => $this->color,
            'is_featured' => $this->is_featured,
            'sort_order' => $this->sort_order,
            'is_active' => true,
        ]);

        session()->flash('message', 'Category created successfully!');
        $this->closeCreateForm();
    }

    public function updateCategory()
    {
        $this->validate();

        if ($this->editingCategory) {
            $this->editingCategory->update([
                'name' => $this->name,
                'description' => $this->description,
                'icon' => $this->icon,
                'color' => $this->color,
                'is_featured' => $this->is_featured,
                'sort_order' => $this->sort_order,
            ]);

            session()->flash('message', 'Category updated successfully!');
            $this->closeEditForm();
        }
    }

    public function toggleActive($categoryId)
    {
        $category = MarketplaceCategory::findOrFail($categoryId);
        $category->update(['is_active' => !$category->is_active]);
        
        $status = $category->is_active ? 'activated' : 'deactivated';
        session()->flash('message', "Category {$status} successfully!");
    }

    public function toggleFeatured($categoryId)
    {
        $category = MarketplaceCategory::findOrFail($categoryId);
        $category->update(['is_featured' => !$category->is_featured]);
        
        $status = $category->is_featured ? 'featured' : 'unfeatured';
        session()->flash('message', "Category {$status} successfully!");
    }

    public function deleteCategory($categoryId)
    {
        $category = MarketplaceCategory::findOrFail($categoryId);
        
        // Check if category has items
        if ($category->items()->count() > 0) {
            session()->flash('error', 'Cannot delete category with existing items. Please move or delete the items first.');
            return;
        }

        $category->delete();
        session()->flash('message', 'Category deleted successfully!');
    }

    private function resetForm()
    {
        $this->name = '';
        $this->description = '';
        $this->icon = 'fas fa-folder';
        $this->color = '#6366f1';
        $this->is_featured = false;
        $this->sort_order = 0;
        $this->resetValidation();
    }

    public function render()
    {
        // Get all categories with item counts
        $categories = MarketplaceCategory::withCount(['items' => function($query) {
            $query->published();
        }])
        ->ordered()
        ->paginate(12);

        // Get featured categories for the top section
        $featuredCategories = MarketplaceCategory::featured()
            ->active()
            ->withCount(['items' => function($query) {
                $query->published();
            }])
            ->ordered()
            ->limit(8)
            ->get();

        return view('livewire.marketplace.partial.marketplace-categories', [
            'categories' => $categories,
            'featuredCategories' => $featuredCategories,
            'availableIcons' => $this->availableIcons,
            'canManage' => auth()->user() && (auth()->user()->isSuperAdmin() || auth()->user()->isAcademyAdmin()),
        ]);
    }
}