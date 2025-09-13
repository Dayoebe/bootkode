<?php

// app/Livewire/Marketplace/Partial/AllOrders.php (Admin)
namespace App\Livewire\Marketplace\Partial;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MarketplaceOrder;

class AllOrders extends Component
{
    use WithPagination;

    public $status = '';
    public $paymentStatus = '';
    public $search = '';

    protected $queryString = [
        'status' => ['except' => ''],
        'paymentStatus' => ['except' => ''],
        'search' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function processRefund($orderId, $reason = 'Admin refund')
    {
        $order = MarketplaceOrder::findOrFail($orderId);
        $order->refund($order->total_amount, $reason);
        
        session()->flash('message', 'Refund processed successfully!');
    }

    public function render()
    {
        $orders = MarketplaceOrder::with(['customer', 'vendor', 'item'])
            ->when($this->status, fn($query) => $query->byStatus($this->status))
            ->when($this->paymentStatus, fn($query) => $query->where('payment_status', $this->paymentStatus))
            ->when($this->search, function ($query) {
                $query->where('order_number', 'like', '%' . $this->search . '%')
                      ->orWhereHas('customer', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                      ->orWhereHas('vendor', fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                      ->orWhereHas('item', fn($q) => $q->where('title', 'like', '%' . $this->search . '%'));
            })
            ->latest()
            ->paginate(15);

        return view('livewire.marketplace.partial.all-orders', [
            'orders' => $orders,
            'statuses' => MarketplaceOrder::STATUSES,
            'paymentStatuses' => MarketplaceOrder::PAYMENT_STATUSES,
        ]);
    }
}