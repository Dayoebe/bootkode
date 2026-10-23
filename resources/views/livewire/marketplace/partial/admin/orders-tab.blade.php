{{-- resources/views/livewire/marketplace/admin/partials/orders-tab.blade.php --}}
<div class="space-y-4">
    <!-- Filters -->
    <div class="flex flex-wrap gap-3 items-center">
        <div class="relative">
            <input aria-label="Search orders" wire:model.live.debounce.300ms="search" type="text" placeholder="Search orders..."
                class="w-64 pl-10 pr-4 py-2 border border-line rounded-lg focus:ring-brand/30 focus:border-brand">
            <i class="fas fa-search absolute left-3 top-3 text-content-muted" aria-hidden="true"></i>
        </div>

        <select aria-label="Order status" wire:model.live="orderStatus"
            class="px-3 py-2 border border-line rounded-lg focus:ring-brand/30 focus:border-brand">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="confirmed">Confirmed</option>
            <option value="processing">Processing</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
        </select>

        <select aria-label="Payment status" wire:model.live="paymentStatus"
            class="px-3 py-2 border border-line rounded-lg focus:ring-brand/30 focus:border-brand">
            <option value="">All Payments</option>
            <option value="paid">Paid</option>
            <option value="unpaid">Unpaid</option>
            <option value="refunded">Refunded</option>
        </select>

        <input aria-label="Date from" wire:model.live="dateFrom" type="date"
            class="px-3 py-2 border border-line rounded-lg focus:ring-brand/30 focus:border-brand">

        <input aria-label="Date to" wire:model.live="dateTo" type="date"
            class="px-3 py-2 border border-line rounded-lg focus:ring-brand/30 focus:border-brand">
    </div>

    <!-- Orders Table -->
    @if(isset($orders) && $orders->count() > 0)
        <div class="bg-surface border border-line rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <x-ui.table label="Orders Tab records"><table class="min-w-full divide-y divide-line">
                    <thead class="bg-surface-muted">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">
                                Order</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">
                                Customer</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">
                                Item</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">
                                Vendor</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-content-muted uppercase tracking-wider">
                                Amount</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">
                                Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">
                                Payment</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-content-muted uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-surface divide-y divide-line">
                        @foreach($orders as $order)
                            <tr class="hover:bg-surface-muted">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-content">{{ $order->order_number }}
                                        </div>
                                        <div class="text-sm text-content-muted">
                                            {{ $order->created_at->format('M d, Y H:i') }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-8 w-8">
                                            @if($order->customer && $order->customer->profile_picture)
                                                <img class="h-8 w-8 rounded-full object-cover"
                                                    src="{{ asset('storage/' . $order->customer->profile_picture) }}"
                                                    alt="{{ $order->customer->name }}">
                                            @else
                                                <div
                                                    class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-sm font-medium text-content">
                                                    {{ substr($order->customer->name ?? 'U', 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-content">
                                                {{ $order->customer->name ?? 'Unknown Customer' }}
                                            </div>
                                            <div class="text-sm text-content-muted">{{ $order->customer->email ?? '' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-content">
                                        {{ Str::limit($order->item->title ?? 'Deleted Item', 30) }}
                                    </div>
                                    <div class="text-sm text-content-muted">
                                        {{ ucfirst($order->item->type ?? 'unknown') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-content">
                                    {{ $order->vendor->name ?? 'Unknown Vendor' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm font-semibold text-content">
                                        ₦{{ number_format($order->total_amount, 2) }}</div>
                                    <div class="text-xs text-content-muted">
                                        ₦{{ number_format($order->vendor_earning, 2) }} vendor</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'confirmed' => 'bg-blue-100 text-blue-800',
                                            'processing' => 'bg-purple-100 text-purple-800',
                                            'completed' => 'bg-green-100 text-green-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                            'refunded' => 'bg-orange-100 text-orange-800',
                                        ];
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $paymentColors = [
                                            'paid' => 'bg-green-100 text-green-800',
                                            'unpaid' => 'bg-red-100 text-red-800',
                                            'failed' => 'bg-red-100 text-red-800',
                                            'refunded' => 'bg-orange-100 text-orange-800',
                                        ];
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $paymentColors[$order->payment_status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center gap-2 justify-end">
                                        <button wire:click="viewOrder({{ $order->id }})"
                                            class="text-brand hover:text-brand" title="View Details">
                                            <i class="fas fa-eye" aria-hidden="true"></i>
                                        </button>
                                        @if($order->status === 'pending')
                                            <button wire:click="confirmOrder({{ $order->id }})"
                                                class="text-green-600 hover:text-green-900" title="Confirm">
                                                <i class="fas fa-check" aria-hidden="true"></i>
                                            </button>
                                        @endif
                                        @if($order->status !== 'refunded' && $order->payment_status === 'paid')
                                            <button wire:click="processRefund({{ $order->id }})"
                                                onclick="confirm('Process refund for this order?') || event.stopImmediatePropagation()"
                                                class="text-red-600 hover:text-red-900" title="Process Refund">
                                                <i class="fas fa-undo" aria-hidden="true"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table></x-ui.table>
            </div>

            <div class="px-6 py-4 border-t border-line">
                {{ $orders->links() }}
            </div>
        </div>
    @else
        <div class="text-center py-12 bg-surface rounded-lg border border-line">
            <i class="fas fa-clipboard-list text-gray-300 text-6xl mb-4" aria-hidden="true"></i>
            <h3 class="text-lg font-medium text-content mb-2">No orders found</h3>
            <p class="text-content-muted">
                @if($orderStatus)
                    No {{ strtolower($orderStatus) }} orders found.
                @elseif($search)
                    No orders match your search criteria.
                @else
                    Orders will appear here as they are placed.
                @endif
            </p>
        </div>
    @endif
</div>
