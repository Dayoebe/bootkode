
{{-- resources/views/livewire/marketplace/partial/cart-checkout.blade.php --}}
<div class="space-y-6">
    <!-- Cart Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-xl font-bold text-gray-900">Shopping Cart</h2>
        <p class="text-gray-600">Review your items and checkout</p>
    </div>

    @if(count($cartItems) > 0)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Cart Items -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Items ({{ count($cartItems) }})</h3>
                    </div>
                    
                    <div class="divide-y divide-gray-200">
                        @foreach($cartItems as $cartItem)
                            <div class="p-6">
                                <div class="flex items-start space-x-4">
                                    @if($cartItem['item']->getPrimaryImage())
                                        <img src="{{ asset('storage/' . $cartItem['item']->getPrimaryImage()) }}" 
                                             alt="{{ $cartItem['item']->title }}" 
                                             class="w-16 h-16 object-cover rounded-lg">
                                    @else
                                        <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-image text-gray-400"></i>
                                        </div>
                                    @endif
                                    
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-lg font-medium text-gray-900">{{ $cartItem['item']->title }}</h4>
                                        <p class="text-sm text-gray-600 mt-1">{{ $cartItem['item']->short_description }}</p>
                                        <p class="text-sm text-gray-500 mt-1">by {{ $cartItem['item']->vendor->name }}</p>
                                        
                                        <div class="flex items-center justify-between mt-3">
                                            <div>
                                                @if($cartItem['item']->hasDiscount())
                                                    <span class="text-lg font-bold text-purple-600">₦{{ number_format($cartItem['price'], 2) }}</span>
                                                    <span class="text-sm text-gray-500 line-through ml-2">₦{{ number_format($cartItem['item']->price, 2) }}</span>
                                                @else
                                                    <span class="text-lg font-bold text-purple-600">₦{{ number_format($cartItem['price'], 2) }}</span>
                                                @endif
                                            </div>
                                            
                                            <button wire:click="removeFromCart({{ $cartItem['item']->id }})"
                                                    class="text-red-600 hover:text-red-700 text-sm">
                                                <i class="fas fa-trash mr-1"></i>
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            
            <!-- Checkout Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Order Summary</h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal ({{ count($cartItems) }} items)</span>
                            <span class="font-medium">₦{{ number_format($total, 2) }}</span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span class="text-gray-600">Processing Fee</span>
                            <span class="font-medium">₦0.00</span>
                        </div>
                        
                        <div class="border-t border-gray-200 pt-3">
                            <div class="flex justify-between">
                                <span class="text-lg font-medium text-gray-900">Total</span>
                                <span class="text-lg font-bold text-purple-600">₦{{ number_format($total, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Method -->
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-3">Payment Method</label>
                        
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input wire:model="paymentMethod" 
                                       type="radio" 
                                       value="wallet" 
                                       class="mr-3 text-purple-600 focus:ring-purple-500">
                                <div class="flex-1">
                                    <span class="font-medium">Wallet</span>
                                    <p class="text-sm text-gray-500">Balance: ₦{{ number_format($walletBalance, 2) }}</p>
                                    @if($walletBalance < $total)
                                        <p class="text-xs text-red-600 mt-1">Insufficient balance</p>
                                    @endif
                                </div>
                            </label>
                            
                            <label class="flex items-center">
                                <input wire:model="paymentMethod" 
                                       type="radio" 
                                       value="paystack" 
                                       class="mr-3 text-purple-600 focus:ring-purple-500">
                                <div class="flex-1">
                                    <span class="font-medium">Card Payment</span>
                                    <p class="text-sm text-gray-500">Pay with debit/credit card</p>
                                </div>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Customer Notes -->
                    <div class="mt-6">
                        <label for="customerNotes" class="block text-sm font-medium text-gray-700 mb-2">
                            Order Notes (Optional)
                        </label>
                        <textarea wire:model="customerNotes" 
                                  id="customerNotes"
                                  rows="3"
                                  placeholder="Any special instructions..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"></textarea>
                    </div>
                    
                    <!-- Checkout Button -->
                    <button wire:click="checkout"
                            @if($paymentMethod === 'wallet' && $walletBalance < $total) disabled @endif
                            class="w-full mt-6 px-4 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed">
                        <i class="fas fa-lock mr-2"></i>
                        Complete Purchase
                    </button>
                    
                    @if($paymentMethod === 'wallet' && $walletBalance < $total)
                        <p class="text-sm text-red-600 mt-2 text-center">
                            Please fund your wallet to complete this purchase
                        </p>
                    @endif
                    
                    <p class="text-xs text-gray-500 mt-3 text-center">
                        By completing your purchase, you agree to our terms and conditions
                    </p>
                </div>
            </div>
        </div>
    @else
        <!-- Empty Cart -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="text-center py-12">
                <i class="fas fa-shopping-cart text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Your cart is empty</h3>
                <p class="text-gray-500 mb-6">Browse our marketplace to find great courses and resources.</p>
                <a href="{{ route('marketplace.browse') }}" 
                   class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    <i class="fas fa-search mr-2"></i>
                    Browse Marketplace
                </a>
            </div>
        </div>
    @endif
</div>
