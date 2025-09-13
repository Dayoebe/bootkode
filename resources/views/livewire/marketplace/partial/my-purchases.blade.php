{{-- resources/views/livewire/marketplace/partial/my-purchases.blade.php --}}
<div class="space-y-6">
    <!-- Header with Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between space-y-4 md:space-y-0">
            <div>
                <h2 class="text-xl font-bold text-gray-900">My Purchases</h2>
                <p class="text-gray-600">Track your orders and download purchased items</p>
            </div>
            
            <div class="flex space-x-3">
                <!-- Search -->
                <div class="relative">
                    <input wire:model.live.debounce.300ms="search" 
                           type="text" 
                           placeholder="Search purchases..." 
                           class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>
        </div>
        
        <!-- Filters -->
        <div class="mt-4 flex flex-wrap gap-3">
            <select wire:model.live="status" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Status</option>
                @foreach($statuses as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
            
            <select wire:model.live="type" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Types</option>
                @foreach($types as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Orders List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        @if($orders->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($orders as $order)
                    <div class="p-6">
                        <div class="flex items-start space-x-4">
                            <!-- Item Image -->
                            <div class="flex-shrink-0">
                                @if($order->item->getPrimaryImage())
                                    <img src="{{ asset('storage/' . $order->item->getPrimaryImage()) }}" 
                                         alt="{{ $order->item->title }}" 
                                         class="w-20 h-20 object-cover rounded-lg">
                                @else
                                    <div class="w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Order Details -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900">
                                            {{ $order->item->title }}
                                        </h3>
                                        <p class="text-sm text-gray-600 mt-1">
                                            Order #{{ $order->order_number }}
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            Vendor: {{ $order->vendor->name }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            Ordered on {{ $order->created_at->format('M d, Y \a\t g:i A') }}
                                        </p>
                                    </div>
                                    
                                    <!-- Price and Status -->
                                    <div class="text-right">
                                        <p class="text-lg font-semibold text-gray-900">
                                            {{ $order->formatted_total }}
                                        </p>
                                        <div class="flex items-center space-x-2 mt-1">
                                            <span class="inline-block px-2 py-1 bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-800 text-xs font-medium rounded-full">
                                                {{ $order->status }}
                                            </span>
                                            <span class="inline-block px-2 py-1 bg-{{ $order->payment_status_color }}-100 text-{{ $order->payment_status_color }}-800 text-xs font-medium rounded-full">
                                                {{ $order->payment_status }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Order Actions -->
                                <div class="mt-4 flex items-center space-x-3">
                                    @if($order->isPaid() && $order->item->is_digital)
                                        <button wire:click="downloadItem({{ $order->id }})"
                                                class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors">
                                            <i class="fas fa-download mr-2"></i>
                                            Download
                                        </button>
                                    @endif
                                    
                                    @if($order->isPaid() && $order->isCompleted())
                                        <a href="{{ route('marketplace.item.public', $order->item->slug) }}" 
                                           class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                            <i class="fas fa-star mr-2"></i>
                                            Leave Review
                                        </a>
                                    @endif
                                    
                                    @if($order->isPaid() && !$order->isRefunded())
                                        <button wire:click="requestRefund({{ $order->id }})"
                                                onclick="confirm('Are you sure you want to request a refund?') || event.stopImmediatePropagation()"
                                                class="inline-flex items-center px-3 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-colors">
                                            <i class="fas fa-undo mr-2"></i>
                                            Request Refund
                                        </button>
                                    @endif
                                    
                                    <a href="{{ route('marketplace.item.public', $order->item->slug) }}" 
                                       class="inline-flex items-center px-3 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition-colors">
                                        <i class="fas fa-eye mr-2"></i>
                                        View Item
                                    </a>
                                </div>
                                
                                <!-- Order Notes -->
                                @if($order->customer_notes || $order->vendor_notes)
                                    <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                                        @if($order->customer_notes)
                                            <p class="text-sm text-gray-600">
                                                <span class="font-medium">Your notes:</span> {{ $order->customer_notes }}
                                            </p>
                                        @endif
                                        @if($order->vendor_notes)
                                            <p class="text-sm text-gray-600 mt-1">
                                                <span class="font-medium">Vendor notes:</span> {{ $order->vendor_notes }}
                                            </p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $orders->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <i class="fas fa-shopping-bag text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No purchases yet</h3>
                <p class="text-gray-500 mb-6">Browse the marketplace to find courses, resources, and services.</p>
                <a href="{{ route('marketplace.browse') }}" 
                   class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    <i class="fas fa-search mr-2"></i>
                    Browse Marketplace
                </a>
            </div>
        @endif
    </div>
</div>