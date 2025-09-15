{{-- resources/views/livewire/marketplace/admin/partials/orders-tab.blade.php --}}
<div class="space-y-4">
    <!-- Filters -->
    <div class="flex flex-wrap gap-3 items-center">
        <div class="relative">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search orders..."
                class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
        </div>

        <select wire:model.live="orderStatus"
            class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="confirmed">Confirmed</option>
            <option value="processing">Processing</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
        </select>

        <select wire:model.live="paymentStatus"
            class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
            <option value="">All Payments</option>
            <option value="paid">Paid</option>
            <option value="unpaid">Unpaid</option>
            <option value="refunded">Refunded</option>
        </select>

        <input wire:model.live="dateFrom" type="date"
            class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">

        <input wire:model.live="dateTo" type="date"
            class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
    </div>

    <!-- Orders Table -->
    @if(isset($orders) && $orders->count() > 0)
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Order</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Item</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Vendor</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Payment</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($orders as $order)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $order->order_number }}
                                        </div>
                                        <div class="text-sm text-gray-500">
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
                                                    class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-sm font-medium text-gray-700">
                                                    {{ substr($order->customer->name ?? 'U', 0, 1) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $order->customer->name ?? 'Unknown Customer' }}
                                            </div>
                                            <div class="text-sm text-gray-500">{{ $order->customer->email ?? '' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ Str::limit($order->item->title ?? 'Deleted Item', 30) }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ ucfirst($order->item->type ?? 'unknown') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $order->vendor->name ?? 'Unknown Vendor' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm font-semibold text-gray-900">
                                        ₦{{ number_format($order->total_amount, 2) }}</div>
                                    <div class="text-xs text-gray-500">
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
                                            class="text-purple-600 hover:text-purple-900" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if($order->status === 'pending')
                                            <button wire:click="confirmOrder({{ $order->id }})"
                                                class="text-green-600 hover:text-green-900" title="Confirm">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                        @if($order->status !== 'refunded' && $order->payment_status === 'paid')
                                            <button wire:click="processRefund({{ $order->id }})"
                                                onclick="confirm('Process refund for this order?') || event.stopImmediatePropagation()"
                                                class="text-red-600 hover:text-red-900" title="Process Refund">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-200">
                {{ $orders->links() }}
            </div>
        </div>
    @else
        <div class="text-center py-12 bg-white rounded-lg border border-gray-200">
            <i class="fas fa-clipboard-list text-gray-300 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No orders found</h3>
            <p class="text-gray-500">
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