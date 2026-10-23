{{-- resources/views/livewire/marketplace/partial/system/system-modals.blade.php --}}

<!-- Item Details Modal -->
@if($showItemModal && $selectedItem)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" x-data="{ show: @entangle('showItemModal') }" x-trap.inert.noscroll="true" role="dialog" aria-modal="true" aria-label="Item Details" tabindex="-1" @keydown.escape.stop="$wire.set(&#x27;showItemModal&#x27;, false)">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-sm rounded-md bg-surface">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b">
                <h3 class="text-lg font-semibold text-content">Item Details</h3>
                <button wire:click="closeItemModal" class="text-content-muted hover:text-content-muted" aria-label="Close">
                    <i class="fas fa-times text-xl" aria-hidden="true"></i>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="mt-6 space-y-6">
                <!-- Item Info -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <img src="{{asset('storage/' .   $selectedItem->getPrimaryImage()) ?? 'https://via.placeholder.com/400x300' }}"
                             alt="{{ $selectedItem->title }}"
                             class="w-full h-64 object-cover rounded-lg">
                    </div>

                    <div>
                        <h4 class="text-xl font-bold text-content mb-2">{{ $selectedItem->title }}</h4>

                        <div class="flex items-center space-x-2 mb-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                bg-{{ $selectedItem->status_color }}-100 text-{{ $selectedItem->status_color }}-800">
                                {{ $selectedItem->status_name }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                bg-brand/10 text-brand">
                                {{ $selectedItem->type_name }}
                            </span>
                        </div>

                        <div class="space-y-2 text-sm">
                            <p><span class="font-medium">Vendor:</span> {{ $selectedItem->vendor->name }}</p>
                            <p><span class="font-medium">Price:</span>
                                @if($selectedItem->hasDiscount())
                                    <span class="line-through text-content-muted">₦{{ number_format($selectedItem->price, 0) }}</span>
                                    <span class="text-green-600 font-medium ml-2">₦{{ number_format($selectedItem->discount_price, 0) }}</span>
                                @else
                                    <span class="font-medium">₦{{ number_format($selectedItem->price, 0) }}</span>
                                @endif
                            </p>
                            <p><span class="font-medium">Created:</span> {{ $selectedItem->created_at->format('M d, Y') }}</p>
                            @if($selectedItem->approved_at)
                                <p><span class="font-medium">Approved:</span> {{ $selectedItem->approved_at->format('M d, Y') }}</p>
                            @endif
                        </div>

                        <!-- Categories -->
                        @if($selectedItem->itemCategories->count() > 0)
                            <div class="mt-4">
                                <p class="text-sm font-medium text-content mb-2">Categories:</p>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($selectedItem->itemCategories as $category)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-surface-muted text-content">
                                            {{ $category->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <h5 class="font-medium text-content mb-2">Description</h5>
                    <p class="text-content-muted text-sm">{{ $selectedItem->description }}</p>
                </div>

                <!-- Rejection Reason (if applicable) -->
                @if($selectedItem->isRejected() && $selectedItem->rejection_reason)
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <h5 class="font-medium text-red-900 mb-2">Rejection Reason</h5>
                        <p class="text-red-700 text-sm">{{ $selectedItem->rejection_reason }}</p>
                    </div>
                @endif

                <!-- Action Form for Rejection -->
                @if($selectedItem->isPending())
                    <div class="bg-surface-muted rounded-lg p-4">
                        <h5 class="font-medium text-content mb-3">Admin Actions</h5>
                        <div class="space-y-3">
                            <button wire:click="approveItem({{ $selectedItem->id }})"
                                    class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                <i class="fas fa-check mr-2" aria-hidden="true"></i>Approve Item
                            </button>

                            <div>
                                <textarea aria-label="Enter rejection reason" wire:model="rejectionReason"
                                          placeholder="Enter rejection reason..."
                                          rows="3"
                                          class="w-full border border-line rounded-lg px-3 py-2 focus:ring-red-500 focus:border-red-500"></textarea>
                                <button wire:click="rejectItem({{ $selectedItem->id }})"
                                        class="w-full mt-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                                        :disabled="!$wire.rejectionReason">
                                    <i class="fas fa-times mr-2" aria-hidden="true"></i>Reject Item
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end pt-6 border-t mt-6">
                <button wire:click="closeItemModal"
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    Close
                </button>
            </div>
        </div>
    </div>
@endif

<!-- Order Details Modal -->
@if($showOrderModal && $selectedOrder)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" x-trap.inert.noscroll="true" role="dialog" aria-modal="true" aria-label="Order Details" tabindex="-1" x-data="{}" @keydown.escape.stop="$wire.set(&#x27;showOrderModal&#x27;, false)">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-3xl shadow-sm rounded-md bg-surface">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b">
                <h3 class="text-lg font-semibold text-content">Order Details</h3>
                <button wire:click="closeOrderModal" class="text-content-muted hover:text-content-muted" aria-label="Close">
                    <i class="fas fa-times text-xl" aria-hidden="true"></i>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="mt-6 space-y-6">
                <!-- Order Summary -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-semibold text-content mb-3">Order Information</h4>
                        <div class="space-y-2 text-sm">
                            <p><span class="font-medium">Order Number:</span> {{ $selectedOrder->order_number }}</p>
                            <p><span class="font-medium">Status:</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                    bg-{{ $selectedOrder->status_color }}-100 text-{{ $selectedOrder->status_color }}-800">
                                    {{ ucfirst($selectedOrder->status) }}
                                </span>
                            </p>
                            <p><span class="font-medium">Payment Status:</span>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                    bg-{{ $selectedOrder->payment_status_color }}-100 text-{{ $selectedOrder->payment_status_color }}-800">
                                    {{ ucfirst($selectedOrder->payment_status) }}
                                </span>
                            </p>
                            <p><span class="font-medium">Created:</span> {{ $selectedOrder->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>

                    <div>
                        <h4 class="font-semibold text-content mb-3">Payment Details</h4>
                        <div class="space-y-2 text-sm">
                            <p><span class="font-medium">Total Amount:</span> {{ $selectedOrder->formatted_total }}</p>
                            <p><span class="font-medium">Vendor Earning:</span> {{ $selectedOrder->formatted_vendor_earning }}</p>
                            <p><span class="font-medium">Platform Commission:</span> {{ $selectedOrder->formatted_commission }}</p>
                            @if($selectedOrder->payment_method)
                                <p><span class="font-medium">Payment Method:</span> {{ ucfirst($selectedOrder->payment_method) }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Customer & Vendor Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-semibold text-content mb-3">Customer</h4>
                        <div class="space-y-2 text-sm">
                            <p><span class="font-medium">Name:</span> {{ $selectedOrder->customer->name }}</p>
                            <p><span class="font-medium">Email:</span> {{ $selectedOrder->customer->email }}</p>
                        </div>
                    </div>

                    <div>
                        <h4 class="font-semibold text-content mb-3">Vendor</h4>
                        <div class="space-y-2 text-sm">
                            <p><span class="font-medium">Name:</span> {{ $selectedOrder->vendor->name }}</p>
                            <p><span class="font-medium">Email:</span> {{ $selectedOrder->vendor->email }}</p>
                        </div>
                    </div>
                </div>

                <!-- Item Details -->
                <div>
                    <h4 class="font-semibold text-content mb-3">Item Details</h4>
                    <div class="flex items-center space-x-4 p-4 border rounded-lg">
                        <img src="{{asset('storage/' .   $selectedOrder->item->getPrimaryImage()) ?? 'https://via.placeholder.com/60x60' }}"
                             alt="{{ $selectedOrder->item->title }}"
                             class="w-15 h-15 object-cover rounded">
                        <div class="flex-1">
                            <p class="font-medium">{{ $selectedOrder->item->title }}</p>
                            <p class="text-sm text-content-muted">{{ $selectedOrder->item->type_name }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-medium">{{ $selectedOrder->formatted_total }}</p>
                        </div>
                    </div>
                </div>

                <!-- Admin Actions -->
                @if($selectedOrder->isPending() || $selectedOrder->isConfirmed())
                    <div class="bg-surface-muted rounded-lg p-4">
                        <h5 class="font-medium text-content mb-3">Admin Actions</h5>
                        <div class="flex flex-wrap gap-2">
                            @if($selectedOrder->isPending())
                                <button wire:click="updateOrderStatus({{ $selectedOrder->id }}, 'confirmed')"
                                        class="px-3 py-1 bg-action text-action-foreground rounded hover:bg-action">
                                    <i class="fas fa-check mr-1" aria-hidden="true"></i>Confirm
                                </button>
                                <button wire:click="updateOrderStatus({{ $selectedOrder->id }}, 'cancelled')"
                                        class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                                    <i class="fas fa-times mr-1" aria-hidden="true"></i>Cancel
                                </button>
                            @endif

                            @if($selectedOrder->isConfirmed())
                                <button wire:click="updateOrderStatus({{ $selectedOrder->id }}, 'completed')"
                                        class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700">
                                    <i class="fas fa-check-double mr-1" aria-hidden="true"></i>Complete
                                </button>
                            @endif

                            @if($selectedOrder->isPaid())
                                <button wire:click="refundOrder({{ $selectedOrder->id }})"
                                        class="px-3 py-1 bg-orange-600 text-white rounded hover:bg-orange-700">
                                    <i class="fas fa-undo mr-1" aria-hidden="true"></i>Refund
                                </button>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end pt-6 border-t mt-6">
                <button wire:click="closeOrderModal"
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    Close
                </button>
            </div>
        </div>
    </div>
@endif

<!-- Vendor Details Modal -->
@if($showVendorModal && $selectedVendor)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" x-trap.inert.noscroll="true" role="dialog" aria-modal="true" aria-label="Vendor Details" tabindex="-1" x-data="{}" @keydown.escape.stop="$wire.set(&#x27;showVendorModal&#x27;, false)">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-sm rounded-md bg-surface">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b">
                <h3 class="text-lg font-semibold text-content">Vendor Details</h3>
                <button wire:click="closeVendorModal" class="text-content-muted hover:text-content-muted" aria-label="Close">
                    <i class="fas fa-times text-xl" aria-hidden="true"></i>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="mt-6 space-y-6">
                <!-- Vendor Profile -->
                <div class="flex items-start space-x-6">
                    <img src="{{asset('storage/' .   $selectedVendor->profile_picture) ?? 'https://ui-avatars.com/api/?name=' . urlencode($selectedVendor->name) }}"
                         alt="{{ $selectedVendor->name }}"
                         class="w-24 h-24 rounded-full">

                    <div class="flex-1">
                        <h4 class="text-xl font-bold text-content mb-2">{{ $selectedVendor->name }}</h4>
                        <p class="text-content-muted mb-3">{{ $selectedVendor->email }}</p>

                        <div class="flex items-center space-x-4 text-sm">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $selectedVendor->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $selectedVendor->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            <span class="text-content-muted">Joined {{ $selectedVendor->created_at->format('M d, Y') }}</span>
                        </div>

                        @if($selectedVendor->bio)
                            <p class="mt-3 text-sm text-content-muted">{{ $selectedVendor->bio }}</p>
                        @endif
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-brand/10 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-brand">{{ $selectedVendor->marketplaceItems->count() }}</div>
                        <div class="text-sm text-brand">Total Items</div>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $selectedVendor->marketplaceItems->where('status', 'approved')->count() }}</div>
                        <div class="text-sm text-green-500">Published</div>
                    </div>
                    <div class="bg-brand/10 rounded-lg p-4 text-center">
                        @php $vendorStats = $selectedVendor->getVendorOrderStats(); @endphp
                        <div class="text-2xl font-bold text-brand">{{ $vendorStats['total_orders'] }}</div>
                        <div class="text-sm text-brand">Total Orders</div>
                    </div>
                    <div class="bg-brand/10 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-brand">₦{{ number_format($vendorStats['total_earnings'], 0) }}</div>
                        <div class="text-sm text-brand">Total Earnings</div>
                    </div>
                </div>

                <!-- Recent Items -->
                <div>
                    <h5 class="font-semibold text-content mb-3">Recent Items</h5>
                    <div class="space-y-3">
                        @forelse($selectedVendor->marketplaceItems->take(5) as $item)
                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <img src="{{asset('storage/' .   $item->getPrimaryImage()) ?? 'https://via.placeholder.com/40x40' }}"
                                         alt="{{ $item->title }}"
                                         class="w-10 h-10 object-cover rounded">
                                    <div>
                                        <p class="font-medium text-sm">{{ Str::limit($item->title, 40) }}</p>
                                        <p class="text-xs text-content-muted">{{ $item->created_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                        bg-{{ $item->status_color }}-100 text-{{ $item->status_color }}-800">
                                        {{ $item->status_name }}
                                    </span>
                                    <p class="text-sm font-medium mt-1">₦{{ number_format($item->price, 0) }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-content-muted text-sm">No items yet</p>
                        @endforelse
                    </div>
                </div>

                <!-- Contact Information -->
                <div>
                    <h5 class="font-semibold text-content mb-3">Contact Information</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p><span class="font-medium">Email:</span> {{ $selectedVendor->email }}</p>
                            @if($selectedVendor->phone_number)
                                <p><span class="font-medium">Phone:</span> {{ $selectedVendor->phone_number }}</p>
                            @endif
                        </div>
                        <div>
                            @if($selectedVendor->address_city || $selectedVendor->address_country)
                                <p><span class="font-medium">Location:</span>
                                    {{ collect([$selectedVendor->address_city, $selectedVendor->address_country])->filter()->join(', ') }}
                                </p>
                            @endif
                            <p><span class="font-medium">Role:</span> {{ ucfirst(str_replace('_', ' ', $selectedVendor->role)) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Admin Actions -->
                <div class="bg-surface-muted rounded-lg p-4">
                    <h5 class="font-medium text-content mb-3">Admin Actions</h5>
                    <div class="flex flex-wrap gap-2">
                        @if($selectedVendor->is_active)
                            <button wire:click="deactivateVendor({{ $selectedVendor->id }})"
                                    class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                                <i class="fas fa-user-slash mr-1" aria-hidden="true"></i>Deactivate
                            </button>
                        @else
                            <button wire:click="activateVendor({{ $selectedVendor->id }})"
                                    class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700">
                                <i class="fas fa-user-check mr-1" aria-hidden="true"></i>Activate
                            </button>
                        @endif

                        <button class="px-3 py-1 bg-action text-action-foreground rounded hover:bg-action">
                            <i class="fas fa-envelope mr-1" aria-hidden="true"></i>Send Email
                        </button>

                        <button class="px-3 py-1 bg-action text-action-foreground rounded hover:bg-action">
                            <i class="fas fa-chart-bar mr-1" aria-hidden="true"></i>View Analytics
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex justify-end pt-6 border-t mt-6">
                <button wire:click="closeVendorModal"
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    Close
                </button>
            </div>
        </div>
    </div>
@endif

<!-- Confirmation Modal -->
<div x-data="{ show: false, message: '', action: null }"
     x-on:confirm-action.window="show = true; message = $event.detail.message; action = $event.detail.action"
     x-show="show"
     x-cloak
     class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" x-trap.inert.noscroll="show" role="dialog" aria-modal="true" aria-label="Dialog" tabindex="-1" @keydown.escape.stop="show = false">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-sm rounded-md bg-surface">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                <i class="fas fa-exclamation-triangle text-yellow-600" aria-hidden="true"></i>
            </div>
            <h3 class="text-lg font-medium text-content mt-4" x-text="message"></h3>
            <div class="mt-6 flex justify-center space-x-3">
                <button @click="show = false"
                        class="px-4 py-2 bg-gray-300 text-content rounded-lg hover:bg-gray-400">
                    Cancel
                </button>
                <button @click="if(action) { $wire.call(action); } show = false"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Confirm
                </button>
            </div>
        </div>
    </div>
</div>
