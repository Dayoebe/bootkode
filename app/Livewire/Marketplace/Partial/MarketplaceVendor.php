<?php
// app/Livewire/Marketplace/Partial/MarketplaceVendor.php
namespace App\Livewire\Marketplace\Partial;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\MarketplaceItem;
use App\Models\MarketplaceCategory;
use Illuminate\Support\Facades\Storage;

class MarketplaceVendor extends Component
{
    use WithPagination, WithFileUploads;

    // Internal navigation state
    public $currentView = 'listings'; // create, listings, drafts
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
    
    // Admin modals
    public $showApproveModal = false;
    public $itemToApprove = null;
    public $showWithdrawModal = false;
    public $itemToWithdraw = null;

    protected $queryString = [
        'currentView' => ['except' => 'listings'],
        'status' => ['except' => ''],
        'search' => ['except' => ''],
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
        // Set default view based on existing items
        if (auth()->user()->marketplaceItems()->count() === 0) {
            $this->currentView = 'create';
        }
    }

    // Navigation methods
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

    public function showDrafts()
    {
        $this->currentView = 'drafts';
        $this->resetPage();
    }

    public function editItem($itemId)
    {
        $item = $this->getItem($itemId);
        
        $this->editingItemId = $itemId;
        $this->title = $item->title;
        $this->description = $item->description;
        $this->short_description = $item->short_description;
        $this->type = $item->type;
        $this->price = $item->price;
        $this->discount_price = $item->discount_price;
        $this->is_digital = $item->is_digital;
        $this->tags = $item->tags ?? [];
        $this->meta_title = $item->meta_title;
        $this->meta_description = $item->meta_description;
        $this->keywords = $item->keywords;
        $this->duration_minutes = $item->duration_minutes;
        $this->selectedCategories = $item->categories->pluck('id')->toArray();
        
        $this->currentView = 'create';
    }

    // Form management methods
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
        if (!empty($value)) {
            $tags = array_map('trim', explode(',', $value));
            $this->tags = array_unique(array_merge($this->tags, $tags));
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

    // Save methods
    public function save()
    {
        $this->validate();

        $thumbnailPath = null;
        if ($this->thumbnail) {
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
            // Update existing item
            $item = $this->getItem($this->editingItemId);
            $item->update($itemData);
            $message = 'Item updated successfully!';
        } else {
            // Create new item
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
        $this->save();
    }

    public function submitForReview()
    {
        // Additional validation for submission
        $this->validate([
            'thumbnail' => $this->editingItemId ? 'nullable' : 'required',
            'meta_title' => 'required',
            'meta_description' => 'required',
        ]);

        $item = $this->save();
        $item->submitForReview();

        session()->flash('message', 'Item submitted for review!');
    }

    // Listings management methods
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
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
    }

    public function duplicateItem($itemId)
    {
        $item = $this->getItem($itemId);
        
        $newItem = $item->replicate();
        $newItem->title = $item->title . ' (Copy)';
        $newItem->slug = null;
        $newItem->status = MarketplaceItem::STATUS_DRAFT;
        $newItem->save();

        // Copy categories
        $newItem->categories()->sync($item->categories->pluck('id'));

        session()->flash('message', 'Item duplicated successfully!');
    }

    public function submitForReviewFromList($itemId)
    {
        $item = $this->getItem($itemId);
        $item->submitForReview();
        
        session()->flash('message', 'Item submitted for review!');
    }

    public function withdrawSubmission($itemId)
    {
        $item = $this->getItem($itemId);
        
        if ($item->status !== MarketplaceItem::STATUS_PENDING) {
            session()->flash('error', 'Only pending items can be withdrawn from review.');
            return;
        }
        
        $item->update(['status' => MarketplaceItem::STATUS_DRAFT]);
        
        session()->flash('message', 'Item withdrawn from review and returned to draft status.');
    }

    // Admin methods
    public function openApproveModal($itemId)
    {
        $this->itemToApprove = MarketplaceItem::findOrFail($itemId);
        $this->showApproveModal = true;
    }

    public function closeApproveModal()
    {
        $this->showApproveModal = false;
        $this->itemToApprove = null;
    }

    public function approveItem()
    {
        if (!$this->itemToApprove) {
            return;
        }

        if (!auth()->user()->canManageCourses()) {
            session()->flash('error', 'You do not have permission to approve items.');
            return;
        }

        $this->itemToApprove->approve(auth()->id());
        $this->closeApproveModal();
        
        session()->flash('message', 'Item approved successfully!');
    }

    public function openWithdrawModal($itemId)
    {
        $this->itemToWithdraw = MarketplaceItem::findOrFail($itemId);
        $this->showWithdrawModal = true;
    }

    public function closeWithdrawModal()
    {
        $this->showWithdrawModal = false;
        $this->itemToWithdraw = null;
    }

    public function withdrawApproval()
    {
        if (!$this->itemToWithdraw) {
            return;
        }

        if (!auth()->user()->canManageCourses()) {
            session()->flash('error', 'You do not have permission to withdraw approval.');
            return;
        }

        if ($this->itemToWithdraw->status !== MarketplaceItem::STATUS_APPROVED) {
            session()->flash('error', 'Only approved items can have approval withdrawn.');
            return;
        }
        
        $this->itemToWithdraw->update([
            'status' => MarketplaceItem::STATUS_DRAFT,
            'approved_at' => null,
            'approved_by' => null,
            'rejection_reason' => null,
        ]);
        
        $this->closeWithdrawModal();
        session()->flash('message', 'Approval withdrawn. Item returned to draft status.');
    }

    // Helper methods
    private function getItem($itemId)
    {
        return auth()->user()->canManageCourses() 
            ? MarketplaceItem::findOrFail($itemId)
            : MarketplaceItem::byVendor(auth()->id())->findOrFail($itemId);
    }

    public function render()
    {
        $data = [
            'currentView' => $this->currentView,
            'isAdmin' => auth()->user()->canManageCourses(),
        ];

        if ($this->currentView === 'create') {
            $data['availableCategories'] = MarketplaceCategory::active()
                ->ordered()
                ->get()
                ->pluck('name', 'id')
                ->toArray();
            $data['types'] = MarketplaceItem::TYPES;
        } else {
            // For admin users, show all items. For vendors, show only their items
            $query = auth()->user()->canManageCourses() 
                ? MarketplaceItem::query()
                : MarketplaceItem::byVendor(auth()->id());

            if ($this->currentView === 'drafts') {
                $query = $query->where('status', MarketplaceItem::STATUS_DRAFT);
            }

            $items = $query
                ->when($this->status, fn($query) => $query->where('status', $this->status))
                ->when($this->search, function ($query) {
                    $query->where('title', 'like', '%' . $this->search . '%')
                          ->orWhere('description', 'like', '%' . $this->search . '%');
                })
                ->with(['orders' => fn($q) => $q->latest()->limit(3), 'vendor'])
                ->latest()
                ->paginate(10);

            $data['items'] = $items;
            $data['statuses'] = MarketplaceItem::STATUSES;
        }

        return view('livewire.marketplace.partial.marketplace-vendor', $data);
    }
}