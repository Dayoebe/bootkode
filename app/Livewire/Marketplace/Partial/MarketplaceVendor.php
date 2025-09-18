<?php
// app/Livewire/Marketplace/Partial/MarketplaceVendor.php
namespace App\Livewire\Marketplace\Partial;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\MarketplaceItem;
use App\Models\MarketplaceCategory;
use App\Models\MarketplaceOrder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class MarketplaceVendor extends Component
{
    use WithPagination, WithFileUploads;

    // Internal navigation state
    public $currentView = 'listings'; // create, listings, analytics
    public $editingItemId = null;
    
    // Listing form properties
    public $title = '';
    public $description = '';
    public $short_description = '';
    public $type = 'course';
    public $price = 0;
    public $discount_price = null;
    public $is_digital = true;
    public $tags = [];
    public $thumbnail;
    public $images = [];
    public $files = [];
    public $meta_title = '';
    public $meta_description = '';
    public $keywords = '';
    public $duration_minutes = null;
    public $selectedCategories = [];
    public $tagInput = '';

    // Listings filters
    public $status = '';
    public $search = '';
    public $sortBy = 'latest';
    
    // Analytics properties
    public $analyticsData = [];
    public $selectedPeriod = '30_days';

    // Bulk operations
    public $selectedItems = [];
    public $bulkAction = '';
    public $selectAll = false;

    protected $queryString = [
        'currentView' => ['except' => 'listings'],
        'status' => ['except' => ''],
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'latest'],
    ];

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'required|string|min:100',
        'short_description' => 'required|string|max:160',
        'type' => 'required|in:course,resource,service',
        'price' => 'required|numeric|min:0',
        'discount_price' => 'nullable|numeric|lt:price',
        'thumbnail' => 'nullable|image|max:2048',
        'selectedCategories' => 'required|array|min:1',
        'selectedCategories.*' => 'exists:marketplace_categories,id',
        'tags' => 'array',
    ];

    public function mount()
    {
        if (auth()->user()->marketplaceItems()->count() === 0) {
            $this->currentView = 'create';
        }
        $this->loadAnalytics();
    }

    // Enhanced Navigation methods
    public function showCreate()
    {
        $this->currentView = 'create';
        $this->resetForm();
        $this->editingItemId = null;
    }

    public function showListings()
    {
        $this->currentView = 'listings';
        $this->resetPage();
    }

    public function showAnalytics()
    {
        $this->currentView = 'analytics';
        $this->loadAnalytics();
    }

    public function editItem($itemId)
    {
        $item = $this->getItem($itemId);
        
        // Check if item is approved - vendors can't edit approved items
        if (!auth()->user()->canManageCourses() && $item->status === 'approved') {
            session()->flash('error', 'Cannot edit approved items. Please duplicate and create a new version.');
            return;
        }
        
        $this->editingItemId = $itemId;
        $this->title = $item->title ?? '';
        $this->description = $item->description ?? '';
        $this->short_description = $item->short_description ?? '';
        $this->type = $item->type ?? 'course';
        $this->price = $item->price ?? 0;
        $this->discount_price = $item->discount_price;
        $this->is_digital = $item->is_digital ?? true;
        $this->tags = $item->tags ?? [];
        $this->meta_title = $item->meta_title ?? '';
        $this->meta_description = $item->meta_description ?? '';
        $this->keywords = $item->keywords ?? '';
        $this->duration_minutes = $item->duration_minutes;
        
        // Safely get categories - handle null relationship
        try {
            $this->selectedCategories = $item->categories ? $item->categories->pluck('id')->toArray() : [];
        } catch (\Exception $e) {
            $this->selectedCategories = [];
        }
        
        $this->currentView = 'create';
    }

    // Enhanced Form management methods
    public function resetForm()
    {
        $this->title = '';
        $this->description = '';
        $this->short_description = '';
        $this->type = 'course';
        $this->price = 0;
        $this->discount_price = null;
        $this->is_digital = true;
        $this->tags = [];
        $this->thumbnail = null;
        $this->images = [];
        $this->files = [];
        $this->meta_title = '';
        $this->meta_description = '';
        $this->keywords = '';
        $this->duration_minutes = null;
        $this->selectedCategories = [];
        $this->tagInput = '';
        $this->resetValidation();
    }

    // Tag management
    public function updatedTagInput($value)
    {
        if (!empty($value) && str_contains($value, ',')) {
            $tags = array_map('trim', explode(',', $value));
            $this->tags = array_unique(array_merge($this->tags, array_filter($tags)));
            $this->tagInput = '';
        }
    }

    public function addTag()
    {
        if (!empty($this->tagInput)) {
            $tag = trim($this->tagInput);
            if (!in_array($tag, $this->tags)) {
                $this->tags[] = $tag;
            }
            $this->tagInput = '';
        }
    }

    public function removeTag($index)
    {
        if (isset($this->tags[$index])) {
            unset($this->tags[$index]);
            $this->tags = array_values($this->tags);
        }
    }

    // Enhanced Save methods
    public function save()
    {
        $this->validate();

        $thumbnailPath = null;
        if ($this->thumbnail) {
            // Delete old thumbnail if editing
            if ($this->editingItemId) {
                $oldItem = $this->getItem($this->editingItemId);
                if ($oldItem->thumbnail) {
                    Storage::disk('public')->delete($oldItem->thumbnail);
                }
            }
            $thumbnailPath = $this->thumbnail->store('marketplace/thumbnails', 'public');
        }

        $itemData = [
            'vendor_id' => auth()->id(),
            'title' => $this->title,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'type' => $this->type,
            'price' => $this->price,
            'discount_price' => $this->discount_price,
            'is_digital' => $this->is_digital,
            'tags' => $this->tags,
            'meta_title' => $this->meta_title ?: $this->title,
            'meta_description' => $this->meta_description ?: $this->short_description,
            'keywords' => $this->keywords,
            'duration_minutes' => $this->duration_minutes,
            'status' => MarketplaceItem::STATUS_DRAFT,
        ];

        if ($thumbnailPath) {
            $itemData['thumbnail'] = $thumbnailPath;
        }

        if ($this->editingItemId) {
            $item = $this->getItem($this->editingItemId);
            $item->update($itemData);
            $message = 'Item updated successfully!';
        } else {
            $item = MarketplaceItem::create($itemData);
            $message = 'Item created successfully!';
        }

        // Sync categories
        $item->categories()->sync($this->selectedCategories);

        session()->flash('message', $message);
        $this->showListings();
        
        return $item;
    }

    public function saveDraft()
    {
        $item = $this->save();
        session()->flash('message', 'Draft saved successfully!');
    }

    public function submitForReview()
    {
        // Additional validation for submission
        $this->validate([
            'thumbnail' => $this->editingItemId ? 'nullable' : 'required|image|max:2048',
            'meta_title' => 'required|string|max:60',
            'meta_description' => 'required|string|max:160',
        ]);

        $item = $this->save();
        $item->update(['status' => MarketplaceItem::STATUS_PENDING]);

        session()->flash('message', 'Item submitted for review! You will be notified once it\'s approved.');
        $this->resetForm();
        $this->showListings();
    }

    // Enhanced Listings management methods
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingSortBy()
    {
        $this->resetPage();
    }

    public function deleteItem($itemId)
    {
        $item = $this->getItem($itemId);
        
        if ($item->orders()->exists()) {
            session()->flash('error', 'Cannot delete item with existing orders.');
            return;
        }

        // Delete associated files
        if ($item->thumbnail) {
            Storage::disk('public')->delete($item->thumbnail);
        }

        $item->delete();
        session()->flash('message', 'Item deleted successfully!');
        $this->loadAnalytics(); // Refresh analytics
    }

    public function duplicateItem($itemId)
    {
        $item = $this->getItem($itemId);
        
        $newItem = $item->replicate();
        $newItem->title = $item->title . ' (Copy)';
        $newItem->slug = null;
        $newItem->status = MarketplaceItem::STATUS_DRAFT;
        $newItem->approved_at = null;
        $newItem->approved_by = null;
        $newItem->save();

        // Copy categories
        $newItem->categories()->sync($item->categories->pluck('id'));

        session()->flash('message', 'Item duplicated successfully! You can now edit the copy.');
    }

    public function submitForReviewFromList($itemId)
    {
        $item = $this->getItem($itemId);
        
        if ($item->status !== MarketplaceItem::STATUS_DRAFT) {
            session()->flash('error', 'Only draft items can be submitted for review.');
            return;
        }
        
        // Check if item has required fields
        if (!$item->thumbnail || !$item->meta_title || !$item->meta_description) {
            session()->flash('error', 'Please complete all required fields (thumbnail, meta title, meta description) before submitting.');
            return;
        }
        
        $item->update(['status' => MarketplaceItem::STATUS_PENDING]);
        session()->flash('message', 'Item submitted for review!');
        $this->loadAnalytics(); // Refresh analytics
    }

    public function quickEdit($itemId, $field, $value)
    {
        $item = $this->getItem($itemId);
        
        // Only allow quick edit for draft items
        if ($item->status !== MarketplaceItem::STATUS_DRAFT) {
            session()->flash('error', 'Can only quick edit draft items.');
            return;
        }
        
        $allowedFields = ['title', 'price', 'discount_price', 'is_featured'];
        
        if (in_array($field, $allowedFields)) {
            $item->update([$field => $value]);
            session()->flash('message', 'Item updated successfully!');
        }
    }

    // Bulk Operations
    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedItems = [];
            $this->selectAll = false;
        } else {
            $items = $this->getItems();
            $this->selectedItems = $items->pluck('id')->toArray();
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
            DB::beginTransaction();
            
            $items = MarketplaceItem::whereIn('id', $this->selectedItems)
                ->where('vendor_id', auth()->id())
                ->get();
            
            switch ($this->bulkAction) {
                case 'submit_review':
                    $submitted = 0;
                    foreach ($items as $item) {
                        if ($item->status === MarketplaceItem::STATUS_DRAFT && $item->thumbnail && $item->meta_title) {
                            $item->update(['status' => MarketplaceItem::STATUS_PENDING]);
                            $submitted++;
                        }
                    }
                    session()->flash('message', "{$submitted} items submitted for review.");
                    break;

                case 'delete':
                    $deleted = 0;
                    foreach ($items as $item) {
                        if (!$item->orders()->exists()) {
                            if ($item->thumbnail) {
                                Storage::disk('public')->delete($item->thumbnail);
                            }
                            $item->delete();
                            $deleted++;
                        }
                    }
                    session()->flash('message', "{$deleted} items deleted successfully.");
                    break;

                case 'duplicate':
                    $duplicated = 0;
                    foreach ($items as $item) {
                        $newItem = $item->replicate();
                        $newItem->title = $item->title . ' (Copy)';
                        $newItem->slug = null;
                        $newItem->status = MarketplaceItem::STATUS_DRAFT;
                        $newItem->save();
                        $newItem->categories()->sync($item->categories->pluck('id'));
                        $duplicated++;
                    }
                    session()->flash('message', "{$duplicated} items duplicated successfully.");
                    break;
            }

            DB::commit();
            $this->selectedItems = [];
            $this->selectAll = false;
            $this->bulkAction = '';
            $this->loadAnalytics();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Bulk action failed: ' . $e->getMessage());
        }
    }

    // Analytics Methods
    public function loadAnalytics()
    {
        $vendorId = auth()->id();
        
        $this->analyticsData = [
            'total_items' => MarketplaceItem::where('vendor_id', $vendorId)->count(),
            'published_items' => MarketplaceItem::where('vendor_id', $vendorId)->where('status', 'approved')->count(),
            'draft_items' => MarketplaceItem::where('vendor_id', $vendorId)->where('status', 'draft')->count(),
            'pending_items' => MarketplaceItem::where('vendor_id', $vendorId)->where('status', 'pending')->count(),
            'rejected_items' => MarketplaceItem::where('vendor_id', $vendorId)->where('status', 'rejected')->count(),
            
            'total_sales' => MarketplaceOrder::whereHas('item', function($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })->where('payment_status', 'paid')->count(),
            
            'total_revenue' => MarketplaceOrder::whereHas('item', function($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })->where('payment_status', 'paid')->sum('vendor_earning'),
            
            'this_month_sales' => MarketplaceOrder::whereHas('item', function($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })->where('payment_status', 'paid')
              ->whereMonth('paid_at', now()->month)
              ->count(),
            
            'this_month_revenue' => MarketplaceOrder::whereHas('item', function($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })->where('payment_status', 'paid')
              ->whereMonth('paid_at', now()->month)
              ->sum('vendor_earning'),
              
            'avg_rating' => MarketplaceItem::where('vendor_id', $vendorId)->avg('average_rating'),
            'total_views' => MarketplaceItem::where('vendor_id', $vendorId)->sum('views_count'),
        ];
    }

    // Helper methods
    private function getItem($itemId)
    {
        return MarketplaceItem::where('vendor_id', auth()->id())->findOrFail($itemId);
    }

    private function getItems()
    {
        $query = MarketplaceItem::where('vendor_id', auth()->id());

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // Apply sorting
        switch ($this->sortBy) {
            case 'title':
                $query->orderBy('title');
                break;
            case 'price_low':
                $query->orderBy('price');
                break;
            case 'price_high':
                $query->orderByDesc('price');
                break;
            case 'status':
                $query->orderBy('status')->orderByDesc('created_at');
                break;
            case 'sales':
                $query->withCount('orders')->orderByDesc('orders_count');
                break;
            case 'oldest':
                $query->orderBy('created_at');
                break;
            default: // latest
                $query->latest();
        }

        return $query->with(['orders' => fn($q) => $q->latest()->limit(3)])
                    ->paginate(12);
    }

    public function render()
    {
        $data = [
            'currentView' => $this->currentView,
            'analyticsData' => $this->analyticsData,
        ];

        if ($this->currentView === 'create') {
            $data['availableCategories'] = MarketplaceCategory::active()
                ->ordered()
                ->get();
            $data['types'] = MarketplaceItem::TYPES;
        } else if ($this->currentView === 'listings') {
            $data['items'] = $this->getItems();
            $data['statuses'] = MarketplaceItem::STATUSES;
        }

        return view('livewire.marketplace.partial.marketplace-vendor', $data);
    }
}