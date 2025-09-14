{{-- resources/views/livewire/marketplace/partial/system/orders-tab.blade.php --}}
<div class="space-y-6">
    <!-- Filters and Search -->
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <!-- Search -->
            <div class="flex-1 max-w-lg">
                <div class="relative">
                    <input type="text" 
                           wire:model.live.debounce.300ms="searchTerm"
                           placeholder="Search by order number or item name..."
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="flex flex-wrap items-center gap-3">
                <select wire:model.live="statusFilter" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="processing">Processing</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="refunded">Refunded</option>
                </select>

                <select wire:model.live="sortBy" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="created_at">Sort by Date</option>
                    <option value="total_amount">Sort by Amount</option>
                    <option value="order_number">Sort by Order #</option>
                </select>

                <button wire:click="$set('searchTerm', '')" class="px-3 py-2 text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i> Clear
                </button>
            </div>
        </div>
    </div>

    <!-- Orders Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Pending Orders</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_orders'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i class="fas fa-sync text-blue-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Processing</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $orders->where('status', 'processing')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <i class="fas fa-check text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Completed</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['completed_orders'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    <i class="fas fa-exclamation-triangle text-red-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-600">Issues</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $orders->whereIn('status', ['cancelled', 'refunded', 'failed'])->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Marketplace Orders</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $order->order_number }}</div>
                                <div class="text-sm text-gray-500">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium 
                                        bg-{{ $order->payment_status_color }}-100 text-{{ $order->payment_status_color }}-800">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $order->customer->name }}</div>
                                <div class="text-sm text-gray-500">{{ $order->customer->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $order->vendor->name }}</div>
                                <div class="text-sm text-gray-500">{{ $order->vendor->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <img class="h-8 w-8 rounded object-cover mr-3" 
                                         src="{{asset('storage/' .  $order->item->getPrimaryImage()) ?? 'https://via.placeholder.com/32x32' }}" 
                                         alt="{{ $order->item->title }}">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ Str::limit($order->item->title, 30) }}</div>
                                        <div class="text-sm text-gray-500">{{ $order->item->type_name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $order->formatted_total }}</div>
                                <div class="text-xs text-gray-500">
                                    Vendor: {{ $order->formatted_vendor_earning }}<br>
                                    Platform: {{ $order->formatted_commission }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-800">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div>{{ $order->created_at->format('M d, Y') }}</div>
                                <div class="text-xs">{{ $order->created_at->format('h:i A') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <button wire:click="viewOrder({{ $order->id }})" 
                                            class="text-indigo-600 hover:text-indigo-900" 
                                            title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    @if($order->isPending())
                                        <button wire:click="updateOrderStatus({{ $order->id }}, 'confirmed')" 
                                                class="text-green-600 hover:text-green-900" 
                                                title="Confirm Order">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button wire:click="updateOrderStatus({{ $order->id }}, 'cancelled')" 
                                                class="text-red-600 hover:text-red-900" 
                                                title="Cancel Order">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif

                                    @if($order->isConfirmed())
                                        <button wire:click="updateOrderStatus({{ $order->id }}, 'completed')" 
                                                class="text-blue-600 hover:text-blue-900" 
                                                title="Mark as Completed">
                                            <i class="fas fa-check-double"></i>
                                        </button>
                                    @endif

                                    @if($order->isPaid() && !$order->isRefunded())
                                        <button wire:click="refundOrder({{ $order->id }})" 
                                                class="text-orange-600 hover:text-orange-900" 
                                                title="Process Refund">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                    @endif

                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open" class="text-gray-400 hover:text-gray-600">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div x-show="open" @click.away="open = false" 
                                             class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 border border-gray-200">
                                            <div class="py-1">
                                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <i class="fas fa-file-invoice mr-2"></i>View Invoice
                                                </a>
                                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <i class="fas fa-envelope mr-2"></i>Send Email
                                                </a>
                                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <i class="fas fa-history mr-2"></i>View History
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-shopping-cart text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">No orders found</p>
                                    <p class="text-sm">Try adjusting your search or filter criteria</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($orders->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    <!-- Quick Stats -->
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Statistics</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="text-3xl font-bold text-blue-600">
                    ₦{{ number_format($orders->where('payment_status', 'paid')->sum('total_amount'), 0) }}
                </div>
                <div class="text-sm text-gray-600">Total Paid Amount</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-green-600">
                    ₦{{ number_format($orders->where('payment_status', 'paid')->sum('vendor_earning'), 0) }}
                </div>
                <div class="text-sm text-gray-600">Vendor Earnings</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-purple-600">
                    ₦{{ number_format($orders->where('payment_status', 'paid')->sum('platform_commission'), 0) }}
                </div>
                <div class="text-sm text-gray-600">Platform Commission</div>
            </div>
        </div>
    </div>

    <!-- Export Options -->
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <div class="flex items-center justify-between">
            <h4 class="text-sm font-medium text-gray-900">Export Orders</h4>
            <div class="flex space-x-2">
                <button class="px-3 py-1 text-sm bg-green-100 text-green-800 rounded hover:bg-green-200">
                    <i class="fas fa-file-excel mr-1"></i>Export to Excel
                </button>
                <button class="px-3 py-1 text-sm bg-blue-100 text-blue-800 rounded hover:bg-blue-200">
                    <i class="fas fa-file-csv mr-1"></i>Export to CSV
                </button>
            </div>
        </div>
    </div>
</div>