{{-- resources/views/livewire/marketplace/partial/vendor-orders.blade.php --}}
<div class="space-y-6">
    <!-- Header with Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between space-y-4 md:space-y-0">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Customer Orders</h2>
                <p class="text-gray-600">Manage orders for your marketplace items</p>
            </div>

            <!-- Quick Stats -->
            <div class="flex items-center space-x-4 text-sm">
                <div class="text-center">
                    <div class="text-lg font-semibold text-blue-600">{{ $stats['pending_orders'] }}</div>
                    <div class="text-gray-500">Pending</div>
                </div>
                <div class="text-center">
                    <div class="text-lg font-semibold text-green-600">{{ $stats['completed_orders'] }}</div>
                    <div class="text-gray-500">Completed</div>
                </div>
                <div class="text-center">
                    <div class="text-lg font-semibold text-purple-600">₦{{ number_format($stats['total_earnings'], 0) }}
                    </div>
                    <div class="text-gray-500">Total Earnings</div>
                </div>
            </div>
        </div>

        <!-- Filters and Actions -->
        <div class="mt-4 flex flex-wrap gap-3 items-center justify-between">
            <div class="flex flex-wrap gap-3">
                <div class="relative">
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search orders..."
                        class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>

                <select wire:model.live="status"
                    class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    <option value="">All Status</option>
                    @foreach($statuses as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            @if($orders->count() > 0)
                <button wire:click="exportOrders"
                    class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-download mr-2"></i>
                    Export CSV
                </button>
            @endif
        </div>
    </div>

    <!-- Orders List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        @if($orders->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($orders as $order)
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start space-x-4 flex-1">
                                <!-- Item Image -->
                                <div class="flex-shrink-0">
                                    @if($order->item->getPrimaryImage())
                                        <img src="{{ asset('storage/' . $order->item->getPrimaryImage()) }}"
                                            alt="{{ $order->item->title }}" class="w-16 h-16 object-cover rounded-lg">
                                    @else
                                        <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center">
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
                                            <div class="mt-2 space-y-1 text-sm text-gray-500">
                                                <p><span class="font-medium">Customer:</span> {{ $order->customer->name }}
                                                    ({{ $order->customer->email }})</p>
                                                <p><span class="font-medium">Ordered:</span>
                                                    {{ $order->created_at->format('M d, Y \a\t g:i A') }}</p>
                                                @if($order->isPaid())
                                                    <p><span class="font-medium">Paid:</span>
                                                        {{ $order->paid_at->format('M d, Y \a\t g:i A') }}</p>
                                                @endif
                                                @if($order->isCompleted())
                                                    <p><span class="font-medium">Completed:</span>
                                                        {{ $order->completed_at->format('M d, Y \a\t g:i A') }}</p>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Order Value and Status -->
                                        <div class="text-right ml-4">
                                            <div class="space-y-2">
                                                <div>
                                                    <p class="text-lg font-semibold text-gray-900">{{ $order->formatted_total }}
                                                    </p>
                                                    <p class="text-sm text-green-600 font-medium">Your earning:
                                                        {{ $order->formatted_vendor_earning }}</p>
                                                </div>

                                                <div class="flex flex-col space-y-1">
                                                    <span
                                                        class="inline-block px-2 py-1 bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-800 text-xs font-medium rounded-full text-center">
                                                        {{ ucfirst($order->status) }}
                                                    </span>
                                                    <span
                                                        class="inline-block px-2 py-1 bg-{{ $order->payment_status_color }}-100 text-{{ $order->payment_status_color }}-800 text-xs font-medium rounded-full text-center">
                                                        {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Customer Notes -->
                                    @if($order->customer_notes)
                                        <div class="mt-3 p-3 bg-blue-50 rounded-lg">
                                            <p class="text-sm text-blue-900">
                                                <span class="font-medium">Customer notes:</span> {{ $order->customer_notes }}
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Vendor Notes -->
                                    @if($order->vendor_notes)
                                        <div class="mt-3 p-3 bg-green-50 rounded-lg">
                                            <p class="text-sm text-green-900">
                                                <span class="font-medium">Your notes:</span> {{ $order->vendor_notes }}
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Order Actions -->
                                    <div class="mt-4 flex flex-wrap items-center gap-2">
                                        @if($order->isPaid() && !$order->isCompleted() && !$order->isCancelled())
                                            <button wire:click="fulfillOrder({{ $order->id }})"
                                                onclick="confirm('Are you sure you want to mark this order as fulfilled?') || event.stopImmediatePropagation()"
                                                class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors">
                                                <i class="fas fa-check mr-2"></i>
                                                Mark as Fulfilled
                                            </button>
                                        @endif

                                        @if($order->item->is_digital && $order->isPaid() && !$order->isCompleted())
                                            <button wire:click="provideDigitalAccess({{ $order->id }})"
                                                class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                                <i class="fas fa-download mr-2"></i>
                                                Provide Access
                                            </button>
                                        @endif

                                        <button wire:click="openNoteModal({{ $order->id }})"
                                            class="inline-flex items-center px-3 py-2 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700 transition-colors">
                                            <i class="fas fa-sticky-note mr-2"></i>
                                            {{ $order->vendor_notes ? 'Edit Notes' : 'Add Notes' }}
                                        </button>

                                        <button
                                            class="inline-flex items-center px-3 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition-colors"
                                            onclick="window.open('mailto:{{ $order->customer->email }}?subject=Regarding Order {{ $order->order_number }}', '_blank')">
                                            <i class="fas fa-envelope mr-2"></i>
                                            Contact Customer
                                        </button>

                                        <a href="{{ route('marketplace.item.public', $order->item->slug) }}" target="_blank"
                                            class="inline-flex items-center px-3 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-external-link-alt mr-2"></i>
                                            View Item
                                        </a>
                                    </div>
                                </div>
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
                <h3 class="text-lg font-medium text-gray-900 mb-2">No orders yet</h3>
                <p class="text-gray-500 mb-6">
                    @if($status || $search)
                        No orders match your current filters.
                    @else
                        You haven't received any orders for your marketplace items yet.
                    @endif
                </p>
                @if($status || $search)
                    <button wire:click="$set('status', '')"
                        class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        Clear Filters
                    </button>
                @else
                    <a href="{{ route('marketplace.browse') }}"
                        class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-store mr-2"></i>
                        Browse Marketplace
                    </a>
                @endif
            </div>
        @endif
    </div>

    <!-- Notes Modal -->
    @if($showNoteModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg max-w-md w-full p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        Notes for Order #{{ $selectedOrder->order_number }}
                    </h3>
                    <button wire:click="closeNoteModal" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form wire:submit.prevent="saveNotes">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Your Notes</label>
                        <textarea wire:model="vendorNotes" rows="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                            placeholder="Add private notes about this order (only visible to you)..."></textarea>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" wire:click="closeNoteModal"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                            Save Notes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif