<?php

// app/Livewire/Marketplace/Partial/MyPurchases.php
namespace App\Livewire\Marketplace\Partial;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MarketplaceOrder;

class MyPurchases extends Component
{
    use WithPagination;

    public $status = '';
    public $type = '';
    public $search = '';

    protected $queryString = [
        'status' => ['except' => ''],
        'type' => ['except' => ''],
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

    public function updatingType()
    {
        $this->resetPage();
    }

    public function downloadItem($orderId)
    {
        $order = MarketplaceOrder::byCustomer(auth()->id())
            ->with('item')
            ->findOrFail($orderId);

        if (!$order->isPaid() || !$order->item->is_digital) {
            session()->flash('error', 'This item is not available for download.');
            return;
        }

        // Logic to generate/serve download
        session()->flash('message', 'Download started!');
    }

    public function requestRefund($orderId)
    {
        $order = MarketplaceOrder::byCustomer(auth()->id())->findOrFail($orderId);
        
        if (!$order->isPaid()) {
            session()->flash('error', 'Cannot request refund for unpaid order.');
            return;
        }

        // Logic to create refund request
        session()->flash('message', 'Refund request submitted for review.');
    }

    public function render()
    {
        $orders = MarketplaceOrder::byCustomer(auth()->id())
            ->with(['item', 'vendor'])
            ->when($this->status, fn($query) => $query->byStatus($this->status))
            ->when($this->type, function ($query) {
                $query->whereHas('item', fn($q) => $q->byType($this->type));
            })
            ->when($this->search, function ($query) {
                $query->whereHas('item', function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.marketplace.partial.my-purchases', [
            'orders' => $orders,
            'statuses' => MarketplaceOrder::STATUSES,
            'types' => MarketplaceItem::TYPES,
        ]);
    }
}
