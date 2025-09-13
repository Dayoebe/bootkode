<?php
// app/Livewire/Marketplace/Partial/VendorOrders.php
namespace App\Livewire\Marketplace\Partial;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MarketplaceOrder;

class VendorOrders extends Component
{
    use WithPagination;

    public $status = '';
    public $search = '';
    public $showNoteModal = false;
    public $selectedOrder = null;
    public $vendorNotes = '';

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

    public function fulfillOrder($orderId)
    {
        $order = MarketplaceOrder::byVendor(auth()->id())->findOrFail($orderId);
        
        if ($order->isPaid()) {
            $order->complete([
                'fulfilled_by' => auth()->user()->name,
                'fulfilled_at' => now(),
                'fulfillment_method' => 'manual'
            ]);
            session()->flash('message', 'Order fulfilled successfully!');
        } else {
            session()->flash('error', 'Order must be paid before fulfillment.');
        }
    }

    public function openNoteModal($orderId)
    {
        $order = MarketplaceOrder::byVendor(auth()->id())->findOrFail($orderId);
        $this->selectedOrder = $order;
        $this->vendorNotes = $order->vendor_notes ?? '';
        $this->showNoteModal = true;
    }

    public function closeNoteModal()
    {
        $this->showNoteModal = false;
        $this->selectedOrder = null;
        $this->vendorNotes = '';
    }

    public function saveNotes()
    {
        if ($this->selectedOrder) {
            $this->selectedOrder->update([
                'vendor_notes' => $this->vendorNotes
            ]);
            
            session()->flash('message', 'Notes saved successfully!');
            $this->closeNoteModal();
        }
    }

    public function provideDigitalAccess($orderId)
    {
        $order = MarketplaceOrder::byVendor(auth()->id())->findOrFail($orderId);
        
        if (!$order->isPaid()) {
            session()->flash('error', 'Order must be paid before providing access.');
            return;
        }

        if (!$order->item->is_digital) {
            session()->flash('error', 'This is not a digital product.');
            return;
        }

        // Mark as completed and provide access details
        $order->complete([
            'fulfilled_by' => auth()->user()->name,
            'fulfilled_at' => now(),
            'fulfillment_method' => 'digital_access',
            'access_provided' => true,
        ]);

        // Here you could send an email with download links
        // or update the order with access credentials
        
        session()->flash('message', 'Digital access provided successfully!');
    }

    public function exportOrders($format = 'csv')
    {
        // Get all orders for this vendor
        $orders = MarketplaceOrder::byVendor(auth()->id())
            ->with(['customer', 'item'])
            ->when($this->status, fn($query) => $query->byStatus($this->status))
            ->get();

        if ($orders->isEmpty()) {
            session()->flash('error', 'No orders to export.');
            return;
        }

        $filename = 'vendor-orders-' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Order Number', 'Customer Name', 'Customer Email', 'Item Title',
                'Status', 'Payment Status', 'Order Amount', 'Your Earning',
                'Order Date', 'Payment Date', 'Completion Date'
            ]);

            // CSV data
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->customer->name,
                    $order->customer->email,
                    $order->item->title,
                    ucfirst($order->status),
                    ucfirst(str_replace('_', ' ', $order->payment_status)),
                    number_format($order->total_amount, 2),
                    number_format($order->vendor_earning, 2),
                    $order->created_at->format('Y-m-d H:i:s'),
                    $order->paid_at ? $order->paid_at->format('Y-m-d H:i:s') : 'Not paid',
                    $order->completed_at ? $order->completed_at->format('Y-m-d H:i:s') : 'Not completed',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function getOrderStats()
    {
        $vendorId = auth()->id();
        
        return [
            'total_orders' => MarketplaceOrder::byVendor($vendorId)->count(),
            'pending_orders' => MarketplaceOrder::byVendor($vendorId)->where('status', 'pending')->count(),
            'completed_orders' => MarketplaceOrder::byVendor($vendorId)->where('status', 'completed')->count(),
            'total_earnings' => MarketplaceOrder::byVendor($vendorId)->where('payment_status', 'paid')->sum('vendor_earning'),
            'this_month_earnings' => MarketplaceOrder::byVendor($vendorId)
                ->where('payment_status', 'paid')
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->sum('vendor_earning'),
        ];
    }

    public function render()
    {
        $orders = MarketplaceOrder::byVendor(auth()->id())
            ->with(['customer', 'item'])
            ->when($this->status, fn($query) => $query->byStatus($this->status))
            ->when($this->search, function ($query) {
                $query->whereHas('customer', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                })->orWhere('order_number', 'like', '%' . $this->search . '%')
                  ->orWhereHas('item', function ($q) {
                      $q->where('title', 'like', '%' . $this->search . '%');
                  });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.marketplace.partial.vendor-orders', [
            'orders' => $orders,
            'statuses' => MarketplaceOrder::STATUSES,
            'stats' => $this->getOrderStats(),
        ]);
    }
}