<?php

// app/Livewire/Marketplace/Partial/CartCheckout.php
namespace App\Livewire\Marketplace\Partial;

use Livewire\Component;
use App\Models\MarketplaceItem;
use App\Models\Wallet;

class CartCheckout extends Component
{
    public $cartItems = [];
    public $paymentMethod = 'wallet';
    public $customerNotes = '';

    public function mount()
    {
        $this->loadCart();
    }

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
                ];
            }
        }
    }

    public function removeFromCart($itemId)
    {
        $cart = session()->get('marketplace_cart', []);
        unset($cart[$itemId]);
        session()->put('marketplace_cart', $cart);
        
        $this->loadCart();
        session()->flash('message', 'Item removed from cart!');
    }

    public function getTotal()
    {
        return collect($this->cartItems)->sum('price');
    }

    public function checkout()
    {
        if (empty($this->cartItems)) {
            session()->flash('error', 'Your cart is empty.');
            return;
        }

        if ($this->paymentMethod === 'wallet') {
            $wallet = Wallet::getOrCreateWallet(auth()->id());
            $total = $this->getTotal();
            
            if (!$wallet->hasSufficientBalance($total)) {
                session()->flash('error', 'Insufficient wallet balance. Please fund your wallet first.');
                return;
            }
        }

        // Process checkout via controller
        $response = app('App\Http\Controllers\MarketplaceController')->checkout(request()->merge([
            'payment_method' => $this->paymentMethod,
            'items' => collect($this->cartItems)->map(fn($item) => ['item_id' => $item['item']->id])->toArray(),
            'customer_notes' => $this->customerNotes,
        ]));

        if ($response->getData()->success) {
            return redirect()->route('marketplace.purchases');
        }
    }

    public function render()
    {
        return view('livewire.marketplace.partial.cart-checkout', [
            'total' => $this->getTotal(),
            'walletBalance' => auth()->user() ? Wallet::getOrCreateWallet(auth()->id())->balance : 0,
        ]);
    }
}
