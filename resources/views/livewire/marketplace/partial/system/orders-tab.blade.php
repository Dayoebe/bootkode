{{-- resources/views/livewire/marketplace/partial/system/orders-tab.blade.php --}}
<div class="space-y-6">
    <!-- Filters and Search -->
    <div class="bg-surface rounded-lg border border-line p-4">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <!-- Search -->
            <div class="flex-1 max-w-lg">
                <div class="relative">
                    <input aria-label="Search by order number or item name" type="text"
                           wire:model.live.debounce.300ms="searchTerm"
                           placeholder="Search by order number or item name..."
                           class="w-full pl-10 pr-4 py-2 border border-line rounded-lg focus:ring-brand/30 focus:border-brand">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-content-muted" aria-hidden="true"></i>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="flex flex-wrap items-center gap-3">
                <select aria-label="Status filter" wire:model.live="statusFilter" class="border border-line rounded-lg px-3 py-2 text-sm">
                    <option value="">All Statuses</option>
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="processing">Processing</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="refunded">Refunded</option>
                </select>

                <select aria-label="Sort by" wire:model.live="sortBy" class="border border-line rounded-lg px-3 py-2 text-sm">
                    <option value="created_at">Sort by Date</option>
                    <option value="total_amount">Sort by Amount</option>
                    <option value="order_number">Sort by Order #</option>
                </select>

                <button wire:click="$set('searchTerm', '')" class="px-3 py-2 text-content-muted hover:text-content">
                    <i class="fas fa-times" aria-hidden="true"></i> Clear
                </button>
            </div>
        </div>
    </div>

    <!-- Orders Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-surface rounded-lg border border-line p-6">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <i class="fas fa-clock text-yellow-600" aria-hidden="true"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-content-muted">Pending Orders</p>
                    <p class="text-2xl font-bold text-content">{{ $stats['pending_orders'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-surface rounded-lg border border-line p-6">
            <div class="flex items-center">
                <div class="p-2 bg-brand/10 rounded-lg">
                    <i class="fas fa-sync text-brand" aria-hidden="true"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-content-muted">Processing</p>
                    <p class="text-2xl font-bold text-content">{{ $orders->where('status', 'processing')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-surface rounded-lg border border-line p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <i class="fas fa-check text-green-600" aria-hidden="true"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-content-muted">Completed</p>
                    <p class="text-2xl font-bold text-content">{{ $stats['completed_orders'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-surface rounded-lg border border-line p-6">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    <i class="fas fa-exclamation-triangle text-red-600" aria-hidden="true"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-content-muted">Issues</p>
                    <p class="text-2xl font-bold text-content">{{ $orders->whereIn('status', ['cancelled', 'refunded', 'failed'])->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-surface rounded-lg border border-line overflow-hidden">
        <div class="px-6 py-4 border-b border-line">
            <h3 class="text-lg font-semibold text-content">Marketplace Orders</h3>
        </div>

        <div class="overflow-x-auto">
            <x-ui.table label="Orders Tab records"><table class="min-w-full divide-y divide-line">
                <thead class="bg-surface-muted">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Order</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Customer</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Vendor</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Item</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Amount</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-surface divide-y divide-line">
                    @forelse($orders as $order)
                        <tr class="hover:bg-surface-muted">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-content">{{ $order->order_number }}</div>
                                <div class="text-sm text-content-muted">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                        bg-{{ $order->payment_status_color }}-100 text-{{ $order->payment_status_color }}-800">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-content">{{ $order->customer->name }}</div>
                                <div class="text-sm text-content-muted">{{ $order->customer->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-content">{{ $order->vendor->name }}</div>
                                <div class="text-sm text-content-muted">{{ $order->vendor->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <img class="h-8 w-8 rounded object-cover mr-3"
                                         src="{{asset('storage/' .  $order->item->getPrimaryImage()) ?? 'https://via.placeholder.com/32x32' }}"
                                         alt="{{ $order->item->title }}">
                                    <div>
                                        <div class="text-sm font-medium text-content">{{ Str::limit($order->item->title, 30) }}</div>
                                        <div class="text-sm text-content-muted">{{ $order->item->type_name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-content">{{ $order->formatted_total }}</div>
                                <div class="text-xs text-content-muted">
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-content-muted">
                                <div>{{ $order->created_at->format('M d, Y') }}</div>
                                <div class="text-xs">{{ $order->created_at->format('h:i A') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <button wire:click="viewOrder({{ $order->id }})"
                                            class="text-brand hover:text-brand"
                                            title="View Details">
                                        <i class="fas fa-eye" aria-hidden="true"></i>
                                    </button>

                                    @if($order->isPending())
                                        <button wire:click="updateOrderStatus({{ $order->id }}, 'confirmed')"
                                                class="text-green-600 hover:text-green-900"
                                                title="Confirm Order">
                                            <i class="fas fa-check" aria-hidden="true"></i>
                                        </button>
                                        <button wire:click="updateOrderStatus({{ $order->id }}, 'cancelled')"
                                                class="text-red-600 hover:text-red-900"
                                                title="Cancel Order">
                                            <i class="fas fa-times" aria-hidden="true"></i>
                                        </button>
                                    @endif

                                    @if($order->isConfirmed())
                                        <button wire:click="updateOrderStatus({{ $order->id }}, 'completed')"
                                                class="text-brand hover:text-brand"
                                                title="Mark as Completed">
                                            <i class="fas fa-check-double" aria-hidden="true"></i>
                                        </button>
                                    @endif

                                    @if($order->isPaid() && !$order->isRefunded())
                                        <button wire:click="refundOrder({{ $order->id }})"
                                                class="text-orange-600 hover:text-orange-900"
                                                title="Process Refund">
                                            <i class="fas fa-undo" aria-hidden="true"></i>
                                        </button>
                                    @endif

                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open" class="text-content-muted hover:text-content-muted" aria-label="More actions">
                                            <i class="fas fa-ellipsis-v" aria-hidden="true"></i>
                                        </button>
                                        <div x-show="open" @click.away="open = false"
                                             class="absolute right-0 mt-2 w-48 bg-surface rounded-md shadow-sm z-10 border border-line">
                                            <div class="py-1">
                                                <a href="#" class="block px-4 py-2 text-sm text-content hover:bg-surface-muted">
                                                    <i class="fas fa-file-invoice mr-2" aria-hidden="true"></i>View Invoice
                                                </a>
                                                <a href="#" class="block px-4 py-2 text-sm text-content hover:bg-surface-muted">
                                                    <i class="fas fa-envelope mr-2" aria-hidden="true"></i>Send Email
                                                </a>
                                                <a href="#" class="block px-4 py-2 text-sm text-content hover:bg-surface-muted">
                                                    <i class="fas fa-history mr-2" aria-hidden="true"></i>View History
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
                                <div class="text-content-muted">
                                    <i class="fas fa-shopping-cart text-4xl mb-4" aria-hidden="true"></i>
                                    <p class="text-lg font-medium">No orders found</p>
                                    <p class="text-sm">Try adjusting your search or filter criteria</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table></x-ui.table>
        </div>

        <!-- Pagination -->
        @if($orders->hasPages())
            <div class="px-6 py-4 border-t border-line">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    <!-- Quick Stats -->
    <div class="bg-surface rounded-lg border border-line p-6">
        <h3 class="text-lg font-semibold text-content mb-4">Order Statistics</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="text-3xl font-bold text-brand">
                    ₦{{ number_format($orders->where('payment_status', 'paid')->sum('total_amount'), 0) }}
                </div>
                <div class="text-sm text-content-muted">Total Paid Amount</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-green-600">
                    ₦{{ number_format($orders->where('payment_status', 'paid')->sum('vendor_earning'), 0) }}
                </div>
                <div class="text-sm text-content-muted">Vendor Earnings</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-brand">
                    ₦{{ number_format($orders->where('payment_status', 'paid')->sum('platform_commission'), 0) }}
                </div>
                <div class="text-sm text-content-muted">Platform Commission</div>
            </div>
        </div>
    </div>

    <!-- Export Options -->
    <div class="bg-surface rounded-lg border border-line p-4">
        <div class="flex items-center justify-between">
            <h4 class="text-sm font-medium text-content">Export Orders</h4>
            <div class="flex space-x-2">
                <button class="px-3 py-1 text-sm bg-green-100 text-green-800 rounded hover:bg-green-200">
                    <i class="fas fa-file-excel mr-1" aria-hidden="true"></i>Export to Excel
                </button>
                <button class="px-3 py-1 text-sm bg-brand/10 text-brand rounded hover:bg-blue-200">
                    <i class="fas fa-file-csv mr-1" aria-hidden="true"></i>Export to CSV
                </button>
            </div>
        </div>
    </div>
</div>
