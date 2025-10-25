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

    public function mount()
    {
        $this->loadCart();
        
        // Set initial view based on cart contents
        if (empty($this->cartItems)) {
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
        $item = MarketplaceItem::published()->findOrFail($itemId);
        
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
        $this->loadCart();
        
        session()->flash('message', "Added {$item->title} to cart!");
    }

    public function updateCartQuantity($itemId, $quantity)
    {
        if ($quantity <= 0) {
            $this->removeFromCart($itemId);
            return;
        }

        $cart = session()->get('marketplace_cart', []);
        if (isset($cart[$itemId])) {
            $cart[$itemId]['quantity'] = $quantity;
            session()->put('marketplace_cart', $cart);
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
        
        $this->loadCart();
        session()->flash('message', "Removed {$itemTitle} from cart!");
    }

    public function clearCart()
    {
        session()->forget('marketplace_cart');
        $this->cartItems = [];
        session()->flash('message', 'Cart cleared!');
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
        if (empty($this->cartItems)) {
            session()->flash('error', 'Your cart is empty.');
            return;
        }

        $total = $this->getCartTotal();

        if ($this->paymentMethod === 'wallet') {
            $wallet = Wallet::getOrCreateWallet(auth()->id());
            
            if (!$wallet->hasSufficientBalance($total)) {
                session()->flash('error', 'Insufficient wallet balance. Please fund your wallet first.');
                return;
            }
        }

        try {
            DB::beginTransaction();

            $orders = [];
            foreach ($this->cartItems as $cartItem) {
                $item = $cartItem['item'];
                $quantity = $cartItem['quantity'];
                
                for ($i = 0; $i < $quantity; $i++) {
                    $order = MarketplaceOrder::create([
                        'customer_id' => auth()->id(),
                        'vendor_id' => $item->vendor_id,
                        'item_id' => $item->id,
                        'status' => MarketplaceOrder::STATUS_PENDING,
                        'payment_status' => MarketplaceOrder::PAYMENT_STATUS_UNPAID,
                        'item_price' => $item->price,
                        'discount_amount' => $item->price - $item->getEffectivePrice(),
                        'total_amount' => $item->getEffectivePrice(),
                        'currency' => 'NGN',
                        'platform_commission_rate' => 20, // 20% platform commission
                        'payment_method' => $this->paymentMethod,
                        'customer_notes' => $this->customerNotes,
                    ]);
                    
                    $orders[] = $order;
                }
            }

            // Process payment
            if ($this->paymentMethod === 'wallet') {
                foreach ($orders as $order) {
                    $wallet = Wallet::getOrCreateWallet(auth()->id());
                    $wallet->debit(
                        $order->total_amount,
                        'course_purchase',
                        "Purchase: {$order->item->title}",
                        $order
                    );
                    
                    $order->markAsPaid([
                        'payment_method' => 'wallet',
                        'wallet_transaction_id' => $wallet->transactions()->latest()->first()->id,
                    ]);
                }
            } else {
                // For card payments, redirect to Paystack
                // This would be handled differently in real implementation
                session()->flash('info', 'Redirecting to payment gateway...');
            }

            // Clear cart
            session()->forget('marketplace_cart');
            $this->cartItems = [];

            DB::commit();

            session()->flash('message', 'Purchase completed successfully!');
            $this->showPurchases();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Purchase failed: ' . $e->getMessage());
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
            session()->flash('error', 'This item is not available for download.');
            return;
        }

        // In a real implementation, this would serve the actual file
        // For now, we'll just show a success message
        session()->flash('message', 'Download started for: ' . $order->item->title);
        
        // Log the download
        activity()
            ->causedBy(auth()->user())
            ->performedOn($order->item)
            ->log('Downloaded purchased item');
    }

    public function requestRefund($orderId)
    {
        $order = MarketplaceOrder::byCustomer(auth()->id())->findOrFail($orderId);
        
        if (!$order->isPaid()) {
            session()->flash('error', 'Cannot request refund for unpaid order.');
            return;
        }

        if ($order->isRefunded()) {
            session()->flash('error', 'This order has already been refunded.');
            return;
        }

        // In a real implementation, this would create a refund request
        // For now, we'll update the order status
        $order->update([
            'status' => MarketplaceOrder::STATUS_CANCELLED,
            'admin_notes' => 'Refund requested by customer on ' . now()->format('Y-m-d H:i:s'),
        ]);

        session()->flash('message', 'Refund request submitted for review.');
    }

    public function reorderItem($orderId)
    {
        $order = MarketplaceOrder::byCustomer(auth()->id())
            ->with('item')
            ->findOrFail($orderId);

        if (!$order->item->isPublished()) {
            session()->flash('error', 'This item is no longer available.');
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
                    $query->whereHas('item', function ($q) {
                        $q->where('title', 'like', '%' . $this->search . '%');
                    })->orWhere('order_number', 'like', '%' . $this->search . '%');
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