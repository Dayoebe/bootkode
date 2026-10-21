<?php
// app/Livewire/Marketplace/Partial/MarketplaceShopping.php
namespace App\Livewire\Marketplace\Partial;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Marketplace\MarketplaceItem;
use App\Models\Marketplace\MarketplaceOrder;
use App\Models\Marketplace\Wallet;
use Illuminate\Support\Facades\DB;

class MarketplaceShopping extends Component
{
    use \App\Livewire\Concerns\HasActionFeedback;

    use WithPagination;

    // Internal navigation state
    public $currentView = 'cart'; // cart, purchases
    
    // Cart properties
    public $cartItems = [];
    public $paymentMethod = 'wallet';
    public $customerNotes = '';
    
    // Purchase filters
    public $status = '';
    public $type = '';
    public $search = '';

    protected $queryString = [
        'currentView' => ['except' => 'cart'],
        'status' => ['except' => ''],
        'type' => ['except' => ''],
        'search' => ['except' => ''],
    ];

    public function boot(): void
    {
        abort_unless(auth()->check(), 403);
    }

    public function mount()
    {
        $this->loadCart();
        
        // Set initial view based on cart contents
        if (request()->route()?->getName() === 'marketplace.purchases' || empty($this->cartItems)) {
            $this->currentView = 'purchases';
        }
    }

    // Navigation methods
    public function showCart()
    {
        $this->currentView = 'cart';
        $this->loadCart();
    }

    public function showPurchases()
    {
        $this->currentView = 'purchases';
        $this->resetPage();
    }

    // Cart management methods
    protected function loadCart()
    {
        $cart = session()->get('marketplace_cart', []);
        $this->cartItems = [];
        
        foreach ($cart as $itemData) {
            $item = MarketplaceItem::find($itemData['item_id']);
            if ($item && $item->isPublished()) {
                $this->cartItems[] = [
                    'item' => $item,
                    'price' => $item->getEffectivePrice(),
                    'quantity' => $itemData['quantity'] ?? 1,
                ];
            }
        }
    }

    public function addToCart($itemId, $quantity = 1)
    {
        abort_unless(filter_var($quantity, FILTER_VALIDATE_INT) !== false && $quantity >= 1 && $quantity <= 10, 422);
        $item = MarketplaceItem::published()->findOrFail($itemId);
        abort_if($item->vendor_id === auth()->id(), 422);
        
        $cart = session()->get('marketplace_cart', []);
        
        // Check if item already in cart
        if (isset($cart[$itemId])) {
            $cart[$itemId]['quantity'] += $quantity;
        } else {
            $cart[$itemId] = [
                'item_id' => $itemId,
                'quantity' => $quantity,
                'added_at' => now()->toDateTimeString(),
            ];
        }
        
        session()->put('marketplace_cart', $cart);
        session()->forget('marketplace_checkout_key');
        $this->loadCart();
        
        $this->flashFeedback('message', "Added {$item->title} to cart!");
    }

    public function updateCartQuantity($itemId, $quantity)
    {
        abort_unless(filter_var($quantity, FILTER_VALIDATE_INT) !== false && $quantity >= 0 && $quantity <= 10, 422);
        if ($quantity <= 0) {
            $this->removeFromCart($itemId);
            return;
        }

        $cart = session()->get('marketplace_cart', []);
        if (isset($cart[$itemId])) {
            $cart[$itemId]['quantity'] = $quantity;
            session()->put('marketplace_cart', $cart);
        session()->forget('marketplace_checkout_key');
            $this->loadCart();
        }
    }

    public function removeFromCart($itemId)
    {
        $cart = session()->get('marketplace_cart', []);
        $itemTitle = '';
        
        // Get item title for message
        foreach ($this->cartItems as $cartItem) {
            if ($cartItem['item']->id == $itemId) {
                $itemTitle = $cartItem['item']->title;
                break;
            }
        }
        
        unset($cart[$itemId]);
        session()->put('marketplace_cart', $cart);
        session()->forget('marketplace_checkout_key');
        
        $this->loadCart();
        $this->flashFeedback('message', "Removed {$itemTitle} from cart!");
    }

    public function clearCart()
    {
        session()->forget('marketplace_cart');
        $this->cartItems = [];
        $this->flashFeedback('message', 'Cart cleared!');
    }

    public function getCartTotal()
    {
        return collect($this->cartItems)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });
    }

    public function getCartItemCount()
    {
        return collect($this->cartItems)->sum('quantity');
    }

    // Checkout methods
    public function checkout()
    {
        $this->validate(['paymentMethod' => 'required|in:wallet', 'customerNotes' => 'nullable|string|max:2000']);
        $rows = session('marketplace_cart', []);
        if (!$rows) {
            $this->flashFeedback('error', 'Your cart is empty.');
            return;
        }
        $key = session('marketplace_checkout_key');
        if (!$key) {
            $key = (string) \Illuminate\Support\Str::uuid();
            session()->put('marketplace_checkout_key', $key);
        }
        try {
            app(\App\Services\MarketplaceService::class)->checkoutWallet(auth()->user(), array_values($rows), $key, $this->customerNotes);
            session()->forget('marketplace_cart');
            $this->cartItems = [];
            $this->flashFeedback('message', 'Wallet purchase completed. Your invoice is available in billing documents.');
            $this->showPurchases();
        } catch (\Throwable $exception) {
            \Log::warning('Marketplace checkout failed', ['exception' => get_class($exception)]);
            $this->flashFeedback('error', $exception instanceof \DomainException ? $exception->getMessage() : 'Purchase could not be completed. Please review your cart and try again.');
            $this->loadCart();
        }
    }

    // Purchase history methods
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
            $this->flashFeedback('error', 'This item is not available for download.');
            return;
        }

        if (empty($order->item->files)) {
            $this->flashFeedback('error', 'The vendor has not attached a downloadable file to this item.');
            return;
        }
        return redirect()->route('marketplace.order.download', $order);
    }

    public function requestRefund($orderId)
    {
        $order = MarketplaceOrder::byCustomer(auth()->id())->findOrFail($orderId);
        
        if (!$order->isPaid()) {
            $this->flashFeedback('error', 'Cannot request refund for unpaid order.');
            return;
        }

        if ($order->isRefunded()) {
            $this->flashFeedback('error', 'This order has already been refunded.');
            return;
        }

        // In a real implementation, this would create a refund request
        // For now, we'll update the order status
        $order->update([
            'status' => MarketplaceOrder::STATUS_CANCELLED,
            'admin_notes' => 'Refund requested by customer on ' . now()->format('Y-m-d H:i:s'),
        ]);

        $this->flashFeedback('message', 'Refund request submitted for review.');
    }

    public function reorderItem($orderId)
    {
        $order = MarketplaceOrder::byCustomer(auth()->id())
            ->with('item')
            ->findOrFail($orderId);

        if (!$order->item->isPublished()) {
            $this->flashFeedback('error', 'This item is no longer available.');
            return;
        }

        $this->addToCart($order->item->id);
        $this->showCart();
    }

    public function render()
    {
        $data = [
            'currentView' => $this->currentView,
        ];

        if ($this->currentView === 'cart') {
            $data = array_merge($data, [
                'cartItems' => $this->cartItems,
                'cartTotal' => $this->getCartTotal(),
                'cartItemCount' => $this->getCartItemCount(),
                'walletBalance' => auth()->user() ? Wallet::getOrCreateWallet(auth()->id())->balance : 0,
            ]);
        } else {
            // Purchases view
            $orders = MarketplaceOrder::byCustomer(auth()->id())
                ->with(['item', 'vendor'])
                ->when($this->status, fn($query) => $query->byStatus($this->status))
                ->when($this->type, function ($query) {
                    $query->whereHas('item', fn($q) => $q->byType($this->type));
                })
                ->when($this->search, function ($query) {
                    $query->where(function ($owned) {
                        $owned->whereHas('item', function ($q) {
                            $q->where('title', 'like', '%' . $this->search . '%');
                        })->orWhere('order_number', 'like', '%' . $this->search . '%');
                    });
                })
                ->latest()
                ->paginate(10);

            $data = array_merge($data, [
                'orders' => $orders,
                'statuses' => MarketplaceOrder::STATUSES,
                'types' => MarketplaceItem::TYPES,
            ]);
        }

        return view('livewire.marketplace.partial.marketplace-shopping', $data);
    }
}