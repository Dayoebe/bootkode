{{-- resources/views/livewire/marketplace/partial/marketplace-shopping.blade.php --}}
<div class="space-y-6">
    <x-ui.flash :messages="$feedback" />

    <!-- Internal Navigation Tabs -->
    <div class="bg-surface dark:bg-gray-800 rounded-lg shadow-sm border border-line dark:border-gray-700 p-4 transition-colors duration-300">
        <div class="flex min-w-0 flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <nav aria-label="Purchases and cart" class="flex max-w-full gap-2 overflow-x-auto">
                <button wire:click="showCart"
                        class="{{ $currentView === 'cart' ? 'bg-blue-100 dark:bg-action text-brand dark:text-brand border-brand dark:border-blue-700' : 'text-gray-600 dark:text-content-muted hover:text-content dark:hover:text-white' }} min-h-11 shrink-0 px-4 py-2 rounded-lg border transition-colors duration-300 flex items-center">
                    <i class="fas fa-shopping-cart mr-2" aria-hidden="true"></i>
                    Shopping Cart
                    @if(count($cartItems ?? []) > 0)
                        <span class="ml-2 bg-action dark:bg-action text-action-foreground text-xs px-2 py-1 rounded-full">
                            {{ $cartItemCount ?? 0 }}
                        </span>
                    @endif
                </button>

                <button wire:click="showPurchases"
                        class="{{ $currentView === 'purchases' ? 'bg-blue-100 dark:bg-action text-brand dark:text-brand border-brand dark:border-blue-700' : 'text-gray-600 dark:text-content-muted hover:text-content dark:hover:text-white' }} min-h-11 shrink-0 px-4 py-2 rounded-lg border transition-colors duration-300 flex items-center">
                    <i class="fas fa-shopping-bag mr-2" aria-hidden="true"></i>
                    My Purchases
                </button>
            </nav>

            <!-- Cart Actions (only show on cart view) -->
            @if($currentView === 'cart' && count($cartItems ?? []) > 0)
                <div class="flex space-x-2">
                    <button wire:click="clearCart"
                            onclick="confirm('Are you sure you want to clear your cart?') || event.stopImmediatePropagation()"
                            class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 text-sm px-3 py-1 border border-red-200 dark:border-red-800 rounded hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-300">
                        <i class="fas fa-trash mr-1" aria-hidden="true"></i>
                        Clear Cart
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Cart View -->
    @if($currentView === 'cart')
        @if(count($cartItems ?? []) > 0)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Cart Items -->
                <div class="lg:col-span-2">
                    <div class="bg-surface dark:bg-gray-800 rounded-lg shadow-sm border border-line dark:border-gray-700 transition-colors duration-300">
                        <div class="p-6 border-b border-line dark:border-gray-700">
                            <h3 class="text-lg font-medium text-content dark:text-white flex items-center transition-colors duration-300">
                                <i class="fas fa-shopping-cart mr-2 text-brand dark:text-brand" aria-hidden="true"></i>
                                Items ({{ $cartItemCount }})
                            </h3>
                        </div>

                        <div class="divide-y divide-line dark:divide-gray-700">
                            @foreach($cartItems as $index => $cartItem)
                                <div class="p-6">
                                    <div class="flex flex-col gap-4 sm:flex-row">
                                        @if($cartItem['item']->getPrimaryImage())
                                            <img src="{{ asset('storage/' . $cartItem['item']->getPrimaryImage()) }}"
                                                 alt="{{ $cartItem['item']->title }}"
                                                 class="w-20 h-20 object-cover rounded-lg">
                                        @else
                                            <div class="w-20 h-20 bg-surface-muted dark:bg-gray-700 rounded-lg flex items-center justify-center transition-colors duration-300">
                                                <i class="fas fa-image text-content-muted dark:text-content-muted" aria-hidden="true"></i>
                                            </div>
                                        @endif

                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-lg font-medium text-content dark:text-white transition-colors duration-300">{{ $cartItem['item']->title }}</h4>
                                            <p class="text-sm text-content-muted dark:text-content-muted mt-1 line-clamp-2 transition-colors duration-300">{{ $cartItem['item']->short_description }}</p>
                                            <p class="text-sm text-content-muted dark:text-content-muted mt-1">by {{ $cartItem['item']->vendor->name }}</p>

                                            <!-- Type and category badges -->
                                            <div class="flex items-center space-x-2 mt-2">
                                                <span class="px-2 py-1 bg-{{ $cartItem['item']->type === 'course' ? 'blue' : ($cartItem['item']->type === 'resource' ? 'green' : 'orange') }}-100 dark:bg-{{ $cartItem['item']->type === 'course' ? 'blue' : ($cartItem['item']->type === 'resource' ? 'green' : 'orange') }}-900 text-{{ $cartItem['item']->type === 'course' ? 'blue' : ($cartItem['item']->type === 'resource' ? 'green' : 'orange') }}-800 dark:text-{{ $cartItem['item']->type === 'course' ? 'blue' : ($cartItem['item']->type === 'resource' ? 'green' : 'orange') }}-300 text-xs font-medium rounded-full transition-colors duration-300">
                                                    {{ $cartItem['item']->type_name }}
                                                </span>
                                                @if($cartItem['item']->is_digital)
                                                    <span class="px-2 py-1 bg-surface-muted dark:bg-gray-700 text-content dark:text-gray-300 text-xs rounded-full transition-colors duration-300">
                                                        Digital
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="mt-4 flex flex-wrap items-center justify-between gap-3">
                                                <div class="flex flex-wrap items-center gap-3">
                                                    <!-- Quantity Controls -->
                                                    <div class="flex items-center space-x-2">
                                                        <label class="text-sm text-content-muted dark:text-content-muted">Qty:</label>
                                                        <div class="flex items-center border border-line dark:border-gray-600 rounded transition-colors duration-300">
                                                            <button wire:click="updateCartQuantity({{ $cartItem['item']->id }}, {{ $cartItem['quantity'] - 1 }})"
                                                                    aria-label="Decrease quantity" class="min-h-11 min-w-11 px-2 py-1 text-content-muted dark:text-content-muted hover:text-content dark:hover:text-gray-300 transition-colors duration-300">
                                                                <i class="fas fa-minus text-xs" aria-hidden="true"></i>
                                                            </button>
                                                            <span class="px-3 py-1 text-sm text-content dark:text-white">{{ $cartItem['quantity'] }}</span>
                                                            <button wire:click="updateCartQuantity({{ $cartItem['item']->id }}, {{ $cartItem['quantity'] + 1 }})"
                                                                    aria-label="Increase quantity" class="min-h-11 min-w-11 px-2 py-1 text-content-muted dark:text-content-muted hover:text-content dark:hover:text-gray-300 transition-colors duration-300">
                                                                <i class="fas fa-plus text-xs" aria-hidden="true"></i>
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <!-- Price -->
                                                    <div>
                                                        @if($cartItem['item']->hasDiscount())
                                                            <div class="flex items-center space-x-2">
                                                                <span class="text-lg font-bold text-brand dark:text-brand transition-colors duration-300">₦{{ number_format($cartItem['price'], 2) }}</span>
                                                                <span class="text-sm text-content-muted dark:text-content-muted line-through transition-colors duration-300">₦{{ number_format($cartItem['item']->price, 2) }}</span>
                                                                <span class="text-xs bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-300 px-1 py-0.5 rounded transition-colors duration-300">
                                                                    {{ $cartItem['item']->getDiscountPercentage() }}% OFF
                                                                </span>
                                                            </div>
                                                        @else
                                                            <span class="text-lg font-bold text-brand dark:text-brand transition-colors duration-300">₦{{ number_format($cartItem['price'], 2) }}</span>
                                                        @endif
                                                        @if($cartItem['quantity'] > 1)
                                                            <p class="text-sm text-content-muted dark:text-content-muted">₦{{ number_format($cartItem['price'] * $cartItem['quantity'], 2) }} total</p>
                                                        @endif
                                                    </div>
                                                </div>

                                                <button wire:click="removeFromCart({{ $cartItem['item']->id }})"
                                                        class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 text-sm px-3 py-1 border border-red-200 dark:border-red-800 rounded hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-300">
                                                    <i class="fas fa-trash mr-1" aria-hidden="true"></i>
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
                    <div class="bg-surface dark:bg-gray-800 rounded-lg shadow-sm border border-line dark:border-gray-700 p-6 sticky top-6 transition-colors duration-300">
                        <h3 class="text-lg font-medium text-content dark:text-white mb-4 flex items-center transition-colors duration-300">
                            <i class="fas fa-receipt mr-2 text-green-600 dark:text-green-400" aria-hidden="true"></i>
                            Order Summary
                        </h3>

                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-content-muted dark:text-content-muted">Subtotal ({{ $cartItemCount }} items)</span>
                                <span class="font-medium text-content dark:text-white">₦{{ number_format($cartTotal, 2) }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-content-muted dark:text-content-muted">Platform Fee</span>
                                <span class="font-medium text-content dark:text-white">₦0.00</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-content-muted dark:text-content-muted">Processing Fee</span>
                                <span class="font-medium text-content dark:text-white">₦0.00</span>
                            </div>

                            <div class="border-t border-line dark:border-gray-700 pt-3">
                                <div class="flex justify-between">
                                    <span class="text-lg font-medium text-content dark:text-white">Total</span>
                                    <span class="text-lg font-bold text-brand dark:text-brand">₦{{ number_format($cartTotal, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-content dark:text-gray-300 mb-3">Payment Method</label>

                            <div class="space-y-3">
                                <label class="flex items-center p-3 border border-line dark:border-gray-700 rounded-lg hover:bg-surface-muted dark:hover:bg-gray-700 cursor-pointer transition-colors duration-300">
                                    <input wire:model="paymentMethod"
                                           type="radio"
                                           value="wallet"
                                           class="mr-3 text-brand focus:ring-brand/30">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <span class="font-medium flex items-center text-content dark:text-white">
                                                <i class="fas fa-wallet mr-2 text-brand dark:text-brand" aria-hidden="true"></i>
                                                Wallet
                                            </span>
                                        </div>
                                        <p class="text-sm text-content-muted dark:text-content-muted">Balance: ₦{{ number_format($walletBalance, 2) }}</p>
                                        @if($walletBalance < $cartTotal)
                                            <p class="text-xs text-red-600 dark:text-red-400 mt-1 flex items-center">
                                                <i class="fas fa-exclamation-triangle mr-1" aria-hidden="true"></i>
                                                Insufficient balance
                                            </p>
                                        @endif
                                    </div>
                                </label>

                                <label class="flex items-center p-3 border border-line dark:border-gray-700 rounded-lg hover:bg-surface-muted dark:hover:bg-gray-700 cursor-pointer transition-colors duration-300">
                                    <input wire:model="paymentMethod"
                                           type="radio"
                                           value="paystack"
                                           class="mr-3 text-brand focus:ring-brand/30">
                                    <div class="flex-1">
                                        <span class="font-medium flex items-center text-content dark:text-white">
                                            <i class="fas fa-credit-card mr-2 text-brand dark:text-brand" aria-hidden="true"></i>
                                            Card Payment
                                        </span>
                                        <p class="text-sm text-content-muted dark:text-content-muted">Pay with debit/credit card via Paystack</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Customer Notes -->
                        <div class="mt-6">
                            <label for="customerNotes" class="block text-sm font-medium text-content dark:text-gray-300 mb-2">
                                Order Notes (Optional)
                            </label>
                            <textarea wire:model="customerNotes"
                                      id="customerNotes"
                                      rows="3"
                                      placeholder="Any special instructions or notes..."
                                      class="w-full px-3 py-2 border border-line dark:border-gray-600 bg-surface dark:bg-gray-700 text-content dark:text-white rounded-lg focus:ring-brand/30 focus:border-brand text-sm transition-colors duration-300"></textarea>
                        </div>

                        <!-- Checkout Button -->
                        <button wire:click="checkout"
                                @if($paymentMethod === 'wallet' && $walletBalance < $cartTotal) disabled @endif
                                class="w-full mt-6 px-4 py-3 bg-action dark:bg-action text-action-foreground rounded-lg hover:bg-action dark:hover:bg-action transition-colors duration-300 disabled:bg-gray-300 dark:disabled:bg-gray-700 disabled:cursor-not-allowed flex items-center justify-center">
                            <i class="fas fa-lock mr-2" aria-hidden="true"></i>
                            Complete Purchase
                            <span class="ml-2 font-bold">₦{{ number_format($cartTotal, 2) }}</span>
                        </button>

                        @if($paymentMethod === 'wallet' && $walletBalance < $cartTotal)
                            <div class="mt-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg transition-colors duration-300">
                                <p class="text-sm text-red-700 dark:text-red-400 text-center flex items-center justify-center">
                                    <i class="fas fa-info-circle mr-2" aria-hidden="true"></i>
                                    Please fund your wallet to complete this purchase
                                </p>
                                <a href="{{ route('wallet.index') }}"
                                   class="block mt-2 text-center text-sm text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 underline">
                                    Fund Wallet
                                </a>
                            </div>
                        @endif

                        <p class="text-xs text-content-muted dark:text-content-muted mt-3 text-center">
                            <i class="fas fa-shield-alt mr-1" aria-hidden="true"></i>
                            Secured by SSL encryption
                        </p>
                    </div>
                </div>
            </div>
        @else
            <!-- Empty Cart -->
            <div class="bg-surface dark:bg-gray-800 rounded-lg shadow-sm border border-line dark:border-gray-700 transition-colors duration-300">
                <div class="text-center py-16">
                    <i class="fas fa-shopping-cart text-gray-300 dark:text-content-muted text-6xl mb-4" aria-hidden="true"></i>
                    <h3 class="text-xl font-medium text-content dark:text-white mb-2 transition-colors duration-300">Your cart is empty</h3>
                    <p class="text-content-muted dark:text-content-muted mb-8 transition-colors duration-300">Browse our marketplace to find great courses, resources, and services.</p>
                    <div class="space-y-3">
                        <a href="{{ route('marketplace.browse') }}"
                           class="inline-flex items-center px-6 py-3 bg-action dark:bg-action text-action-foreground rounded-lg hover:bg-action dark:hover:bg-action transition-colors duration-300">
                            <i class="fas fa-search mr-2" aria-hidden="true"></i>
                            Browse Marketplace
                        </a>
                        <br>
                        <button wire:click="showPurchases"
                                class="inline-flex items-center px-4 py-2 border border-line dark:border-gray-600 text-content dark:text-gray-300 rounded-lg hover:bg-surface-muted dark:hover:bg-gray-700 transition-colors duration-300">
                            <i class="fas fa-shopping-bag mr-2" aria-hidden="true"></i>
                            View My Purchases
                        </button>
                    </div>
                </div>
            </div>
        @endif

    <!-- Purchases View -->
    @else
        <!-- Header with Filters -->
        <div class="bg-surface dark:bg-gray-800 rounded-lg shadow-sm border border-line dark:border-gray-700 p-6 transition-colors duration-300">
            <div class="flex flex-col md:flex-row md:items-center justify-between space-y-4 md:space-y-0">
                <div>
                    <h2 class="text-xl font-bold text-content dark:text-white flex items-center transition-colors duration-300">
                        <i class="fas fa-shopping-bag mr-2 text-brand dark:text-brand" aria-hidden="true"></i>
                        My Purchases
                    </h2>
                    <p class="text-content-muted dark:text-content-muted transition-colors duration-300">Track your orders and access purchased items</p>
                </div>

                <div class="flex space-x-3">
                    <!-- Search -->
                    <div class="relative">
                        <input aria-label="Search purchases" wire:model.live.debounce.300ms="search"
                               type="text"
                               placeholder="Search purchases..."
                               class="w-64 pl-10 pr-4 py-2 border border-line dark:border-gray-600 bg-surface dark:bg-gray-700 text-content dark:text-white rounded-lg focus:ring-brand/30 focus:border-brand transition-colors duration-300">
                        <i class="fas fa-search absolute left-3 top-3 text-content-muted dark:text-content-muted" aria-hidden="true"></i>
                        @if($search)
                            <button wire:click="$set('search', '')" class="absolute right-3 top-3 text-content-muted dark:text-content-muted hover:text-content-muted dark:hover:text-content-muted" aria-label="Close">
                                <i class="fas fa-times" aria-hidden="true"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="mt-4 flex flex-wrap gap-3">
                <select aria-label="Status" wire:model.live="status" class="px-3 py-2 border border-line dark:border-gray-600 bg-surface dark:bg-gray-700 text-content dark:text-white rounded-lg focus:ring-brand/30 focus:border-brand transition-colors duration-300">
                    <option value="">All Status</option>
                    @isset($statuses)
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    @endisset
                </select>

                <select aria-label="Type" wire:model.live="type" class="px-3 py-2 border border-line dark:border-gray-600 bg-surface dark:bg-gray-700 text-content dark:text-white rounded-lg focus:ring-brand/30 focus:border-brand transition-colors duration-300">
                    <option value="">All Types</option>
                    @isset($types)
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    @endisset
                </select>

                <!-- Clear Filters -->
                @if($status || $type || $search)
                    <button wire:click="$set('status', ''); $set('type', ''); $set('search', '')"
                            class="px-3 py-2 bg-surface-muted dark:bg-gray-700 text-content dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-300">
                        <i class="fas fa-times mr-1" aria-hidden="true"></i>
                        Clear Filters
                    </button>
                @endif
            </div>
        </div>

        <!-- Orders List -->
        <div class="bg-surface dark:bg-gray-800 rounded-lg shadow-sm border border-line dark:border-gray-700 transition-colors duration-300">
            @isset($orders)
                @if($orders->count() > 0)
                    <div class="divide-y divide-line dark:divide-gray-700">
                        @foreach($orders as $order)
                            <div class="p-6 hover:bg-surface-muted dark:hover:bg-gray-700 transition-colors duration-300">
                                <div class="flex flex-col gap-4 sm:flex-row">
                                    <!-- Item Image -->
                                    <div class="flex-shrink-0">
                                        @if($order->item->getPrimaryImage())
                                            <img src="{{ asset('storage/' . $order->item->getPrimaryImage()) }}"
                                                 alt="{{ $order->item->title }}"
                                                 class="w-20 h-20 object-cover rounded-lg border border-line dark:border-gray-700">
                                        @else
                                            <div class="w-20 h-20 bg-surface-muted dark:bg-gray-700 rounded-lg flex items-center justify-center border border-line dark:border-gray-700 transition-colors duration-300">
                                                <i class="fas fa-image text-content-muted dark:text-content-muted" aria-hidden="true"></i>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Order Details -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                            <div class="flex-1">
                                                <h3 class="text-lg font-medium text-content dark:text-white hover:text-brand dark:hover:text-brand transition-colors duration-300">
                                                    {{ $order->item->title }}
                                                </h3>
                                                <div class="mt-1 flex flex-wrap items-center gap-2 text-sm text-content-muted dark:text-content-muted">
                                                    <span class="flex items-center">
                                                        <i class="fas fa-hashtag mr-1" aria-hidden="true"></i>
                                                        {{ $order->order_number }}
                                                    </span>
                                                    <span class="flex items-center">
                                                        <i class="fas fa-user mr-1" aria-hidden="true"></i>
                                                        {{ $order->vendor->name }}
                                                    </span>
                                                    <span class="flex items-center">
                                                        <i class="fas fa-calendar mr-1" aria-hidden="true"></i>
                                                        {{ $order->created_at->format('M d, Y') }}
                                                    </span>
                                                </div>

                                                @if($order->item->short_description)
                                                    <p class="text-sm text-content-muted dark:text-content-muted mt-2 line-clamp-2">{{ $order->item->short_description }}</p>
                                                @endif
                                            </div>

                                            <!-- Price and Status -->
                                            <div class="text-left sm:ml-4 sm:text-right">
                                                <p class="text-lg font-semibold text-content dark:text-white">
                                                    {{ $order->formatted_total }}
                                                </p>
                                                <div class="flex flex-col space-y-1 mt-1">
                                                    <span class="inline-flex items-center px-2 py-1 bg-{{ $order->status_color }}-100 dark:bg-{{ $order->status_color }}-900 text-{{ $order->status_color }}-800 dark:text-{{ $order->status_color }}-300 text-xs font-medium rounded-full">
                                                        <i class="fas fa-{{ $order->status === 'completed' ? 'check' : ($order->status === 'pending' ? 'clock' : 'times') }} mr-1" aria-hidden="true"></i>
                                                        {{ ucfirst($order->status) }}
                                                    </span>
                                                    <span class="inline-flex items-center px-2 py-1 bg-{{ $order->payment_status_color }}-100 dark:bg-{{ $order->payment_status_color }}-900 text-{{ $order->payment_status_color }}-800 dark:text-{{ $order->payment_status_color }}-300 text-xs font-medium rounded-full">
                                                        <i class="fas fa-{{ $order->payment_status === 'paid' ? 'check-circle' : 'clock' }} mr-1" aria-hidden="true"></i>
                                                        {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Order Actions -->
                                        <div class="mt-4 flex flex-wrap items-center gap-2">
                                            @if($order->isPaid() && $order->item->is_digital)
                                                <button wire:click="downloadItem({{ $order->id }})"
                                                        class="inline-flex min-h-11 items-center px-3 py-1.5 bg-green-600 dark:bg-green-500 text-white text-sm rounded-lg hover:bg-green-700 dark:hover:bg-green-600 transition-colors duration-300">
                                                    <i class="fas fa-download mr-1" aria-hidden="true"></i>
                                                    Download
                                                </button>
                                            @endif

                                            @if($order->isPaid() && $order->isCompleted())
                                                <a href="{{ route('marketplace.item.public', $order->item->slug) }}"
                                                   class="inline-flex min-h-11 items-center px-3 py-1.5 bg-action dark:bg-action text-action-foreground text-sm rounded-lg hover:bg-action dark:hover:bg-action transition-colors duration-300">
                                                    <i class="fas fa-star mr-1" aria-hidden="true"></i>
                                                    View reviews
                                                </a>
                                            @endif

                                            @if($order->isPaid() && !$order->isRefunded() && !$order->isCancelled())
                                                <button wire:click="requestRefund({{ $order->id }})"
                                                        onclick="confirm('Are you sure you want to request a refund? This action cannot be undone.') || event.stopImmediatePropagation()"
                                                        class="inline-flex min-h-11 items-center px-3 py-1.5 bg-orange-600 dark:bg-orange-500 text-white text-sm rounded-lg hover:bg-orange-700 dark:hover:bg-orange-600 transition-colors duration-300">
                                                    <i class="fas fa-undo mr-1" aria-hidden="true"></i>
                                                    Request Refund
                                                </button>
                                            @endif

                                            <button wire:click="reorderItem({{ $order->id }})"
                                                    class="inline-flex min-h-11 items-center px-3 py-1.5 bg-action dark:bg-action text-action-foreground text-sm rounded-lg hover:bg-action dark:hover:bg-action transition-colors duration-300">
                                                <i class="fas fa-redo mr-1" aria-hidden="true"></i>
                                                Reorder
                                            </button>

                                            <a href="{{ route('marketplace.item.public', $order->item->slug) }}"
                                               class="inline-flex min-h-11 items-center px-3 py-1.5 border border-line dark:border-gray-600 text-content dark:text-gray-300 text-sm rounded-lg hover:bg-surface-muted dark:hover:bg-gray-700 transition-colors duration-300">
                                                <i class="fas fa-eye mr-1" aria-hidden="true"></i>
                                                View Item
                                            </a>
                                        </div>

                                        <!-- Order Notes -->
                                        @if($order->customer_notes || $order->vendor_notes || $order->admin_notes)
                                            <div class="mt-3 p-3 bg-surface-muted dark:bg-gray-700 rounded-lg transition-colors duration-300">
                                                @if($order->customer_notes)
                                                    <p class="text-sm text-content-muted dark:text-content-muted">
                                                        <span class="font-medium flex items-center text-content dark:text-white">
                                                            <i class="fas fa-user mr-1" aria-hidden="true"></i>
                                                            Your notes:
                                                        </span>
                                                        {{ $order->customer_notes }}
                                                    </p>
                                                @endif
                                                @if($order->vendor_notes)
                                                    <p class="text-sm text-content-muted dark:text-content-muted mt-1">
                                                        <span class="font-medium flex items-center text-content dark:text-white">
                                                            <i class="fas fa-store mr-1" aria-hidden="true"></i>
                                                            Vendor notes:
                                                        </span>
                                                        {{ $order->vendor_notes }}
                                                    </p>
                                                @endif

                                            </div>
                                        @endif

                                        <!-- Delivery Details -->
                                        @if($order->is_delivered && $order->delivered_at)
                                            <div class="mt-3 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg transition-colors duration-300">
                                                <p class="text-sm text-green-800 dark:text-green-400 flex items-center">
                                                    <i class="fas fa-check-circle mr-2" aria-hidden="true"></i>
                                                    <span class="font-medium">Delivered on {{ $order->delivered_at->format('M d, Y \a\t g:i A') }}</span>
                                                </p>
                                                @if($order->delivery_details && isset($order->delivery_details['notes']))
                                                    <p class="text-sm text-green-700 dark:text-green-400 mt-1">{{ $order->delivery_details['notes'] }}</p>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-line dark:border-gray-700 bg-surface-muted dark:bg-gray-800 transition-colors duration-300">
                        {{ $orders->links() }}
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="text-center py-16">
                        <i class="fas fa-shopping-bag text-gray-300 dark:text-content-muted text-6xl mb-4" aria-hidden="true"></i>
                        <h3 class="text-xl font-medium text-content dark:text-white mb-2 transition-colors duration-300">
                            @if($search || $status || $type)
                                No matching purchases found
                            @else
                                No purchases yet
                            @endif
                        </h3>
                        <p class="text-content-muted dark:text-content-muted mb-8 transition-colors duration-300">
                            @if($search || $status || $type)
                                Try adjusting your filters or search terms.
                            @else
                                Browse the marketplace to find courses, resources, and services.
                            @endif
                        </p>

                        <div class="space-y-3">
                            @if($search || $status || $type)
                                <button wire:click="$set('status', ''); $set('type', ''); $set('search', '')"
                                        class="inline-flex items-center px-6 py-3 bg-action dark:bg-action text-action-foreground rounded-lg hover:bg-action dark:hover:bg-action transition-colors duration-300">
                                    <i class="fas fa-times mr-2" aria-hidden="true"></i>
                                    Clear Filters
                                </button>
                                <br>
                            @endif

                            <a href="{{ route('marketplace.browse') }}"
                               class="inline-flex items-center px-6 py-3 bg-action dark:bg-action text-action-foreground rounded-lg hover:bg-action dark:hover:bg-action transition-colors duration-300">
                                <i class="fas fa-search mr-2" aria-hidden="true"></i>
                                Browse Marketplace
                            </a>

                            @if(!($search || $status || $type))
                                <br>
                                <button wire:click="showCart"
                                        class="inline-flex items-center px-4 py-2 border border-line dark:border-gray-600 text-content dark:text-gray-300 rounded-lg hover:bg-surface-muted dark:hover:bg-gray-700 transition-colors duration-300">
                                    <i class="fas fa-shopping-cart mr-2" aria-hidden="true"></i>
                                    View Shopping Cart
                                </button>
                            @endif
                        </div>
                    </div>
                @endif
            @endisset
        </div>
    @endif
</div
