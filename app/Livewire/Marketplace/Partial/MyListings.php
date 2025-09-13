<?php
// app/Livewire/Marketplace/Partial/MyListings.php
namespace App\Livewire\Marketplace\Partial;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MarketplaceItem;
use App\Models\User;

class MyListings extends Component
{
    use WithPagination;

    public $status = '';
    public $search = '';
    public $showApproveModal = false;
    public $itemToApprove = null;
    public $showWithdrawModal = false;
    public $itemToWithdraw = null;

    protected $queryString = [
        'status' => ['except' => ''],
        'search' => ['except' => ''],
    ];

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

        session()->flash('message', 'Item duplicated successfully!');
    }

    public function submitForReview($itemId)
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

    // Admin approval methods
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

    // Withdraw approval methods
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
        
        // Update item status to draft and clear approval info
        $this->itemToWithdraw->update([
            'status' => MarketplaceItem::STATUS_DRAFT,
            'approved_at' => null,
            'approved_by' => null,
            'rejection_reason' => null,
        ]);
        
        $this->closeWithdrawModal();
        session()->flash('message', 'Approval withdrawn. Item returned to draft status.');
        
        // Force refresh to update UI state
        $this->dispatch('refreshComponent');
    }

    // Helper method to get items with proper authorization
    private function getItem($itemId)
    {
        return auth()->user()->canManageCourses() 
            ? MarketplaceItem::findOrFail($itemId)
            : MarketplaceItem::byVendor(auth()->id())->findOrFail($itemId);
    }

    public function render()
    {
        // For admin users, show all items. For vendors, show only their items
        $query = auth()->user()->canManageCourses() 
            ? MarketplaceItem::query()
            : MarketplaceItem::byVendor(auth()->id());

        $items = $query
            ->when($this->status, fn($query) => $query->where('status', $this->status))
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->with(['orders' => fn($q) => $q->latest()->limit(3), 'vendor'])
            ->latest()
            ->paginate(10);

        return view('livewire.marketplace.partial.my-listings', [
            'items' => $items,
            'statuses' => MarketplaceItem::STATUSES,
            'isAdmin' => auth()->user()->canManageCourses(),
        ]);
    }
}