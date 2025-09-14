{{-- resources/views/livewire/marketplace/partial/system/system-modals.blade.php --}}

<!-- Item Details Modal -->
@if($showItemModal && $selectedItem)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" x-data="{ show: @entangle('showItemModal') }">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Item Details</h3>
                <button wire:click="closeItemModal" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
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
                        <h4 class="text-xl font-bold text-gray-900 mb-2">{{ $selectedItem->title }}</h4>
                        
                        <div class="flex items-center space-x-2 mb-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                bg-{{ $selectedItem->status_color }}-100 text-{{ $selectedItem->status_color }}-800">
                                {{ $selectedItem->status_name }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                bg-blue-100 text-blue-800">
                                {{ $selectedItem->type_name }}
                            </span>
                        </div>

                        <div class="space-y-2 text-sm">
                            <p><span class="font-medium">Vendor:</span> {{ $selectedItem->vendor->name }}</p>
                            <p><span class="font-medium">Price:</span> 
                                @if($selectedItem->hasDiscount())
                                    <span class="line-through text-gray-500">₦{{ number_format($selectedItem->price, 0) }}</span>
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
                        @if($selectedItem->categories->count() > 0)
                            <div class="mt-4">
                                <p class="text-sm font-medium text-gray-700 mb-2">Categories:</p>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($selectedItem->categories as $category)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
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
                    <h5 class="font-medium text-gray-900 mb-2">Description</h5>
                    <p class="text-gray-600 text-sm">{{ $selectedItem->description }}</p>
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
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h5 class="font-medium text-gray-900 mb-3">Admin Actions</h5>
                        <div class="space-y-3">
                            <button wire:click="approveItem({{ $selectedItem->id }})" 
                                    class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                <i class="fas fa-check mr-2"></i>Approve Item
                            </button>
                            
                            <div>
                                <textarea wire:model="rejectionReason" 
                                          placeholder="Enter rejection reason..."
                                          rows="3"
                                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-red-500 focus:border-red-500"></textarea>
                                <button wire:click="rejectItem({{ $selectedItem->id }})" 
                                        class="w-full mt-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
                                        :disabled="!$wire.rejectionReason">
                                    <i class="fas fa-times mr-2"></i>Reject Item
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
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-3xl shadow-lg rounded-md bg-white">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Order Details</h3>
                <button wire:click="closeOrderModal" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="mt-6 space-y-6">
                <!-- Order Summary -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-3">Order Information</h4>
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
                        <h4 class="font-semibold text-gray-900 mb-3">Payment Details</h4>
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
                        <h4 class="font-semibold text-gray-900 mb-3">Customer</h4>
                        <div class="space-y-2 text-sm">
                            <p><span class="font-medium">Name:</span> {{ $selectedOrder->customer->name }}</p>
                            <p><span class="font-medium">Email:</span> {{ $selectedOrder->customer->email }}</p>
                        </div>
                    </div>

                    <div>
                        <h4 class="font-semibold text-gray-900 mb-3">Vendor</h4>
                        <div class="space-y-2 text-sm">
                            <p><span class="font-medium">Name:</span> {{ $selectedOrder->vendor->name }}</p>
                            <p><span class="font-medium">Email:</span> {{ $selectedOrder->vendor->email }}</p>
                        </div>
                    </div>
                </div>

                <!-- Item Details -->
                <div>
                    <h4 class="font-semibold text-gray-900 mb-3">Item Details</h4>
                    <div class="flex items-center space-x-4 p-4 border rounded-lg">
                        <img src="{{asset('storage/' .   $selectedOrder->item->getPrimaryImage()) ?? 'https://via.placeholder.com/60x60' }}" 
                             alt="{{ $selectedOrder->item->title }}" 
                             class="w-15 h-15 object-cover rounded">
                        <div class="flex-1">
                            <p class="font-medium">{{ $selectedOrder->item->title }}</p>
                            <p class="text-sm text-gray-600">{{ $selectedOrder->item->type_name }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-medium">{{ $selectedOrder->formatted_total }}</p>
                        </div>
                    </div>
                </div>

                <!-- Admin Actions -->
                @if($selectedOrder->isPending() || $selectedOrder->isConfirmed())
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h5 class="font-medium text-gray-900 mb-3">Admin Actions</h5>
                        <div class="flex flex-wrap gap-2">
                            @if($selectedOrder->isPending())
                                <button wire:click="updateOrderStatus({{ $selectedOrder->id }}, 'confirmed')" 
                                        class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
                                    <i class="fas fa-check mr-1"></i>Confirm
                                </button>
                                <button wire:click="updateOrderStatus({{ $selectedOrder->id }}, 'cancelled')" 
                                        class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                                    <i class="fas fa-times mr-1"></i>Cancel
                                </button>
                            @endif
                            
                            @if($selectedOrder->isConfirmed())
                                <button wire:click="updateOrderStatus({{ $selectedOrder->id }}, 'completed')" 
                                        class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700">
                                    <i class="fas fa-check-double mr-1"></i>Complete
                                </button>
                            @endif

                            @if($selectedOrder->isPaid())
                                <button wire:click="refundOrder({{ $selectedOrder->id }})" 
                                        class="px-3 py-1 bg-orange-600 text-white rounded hover:bg-orange-700">
                                    <i class="fas fa-undo mr-1"></i>Refund
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
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
            <!-- Modal Header -->
            <div class="flex items-center justify-between pb-4 border-b">
                <h3 class="text-lg font-semibold text-gray-900">Vendor Details</h3>
                <button wire:click="closeVendorModal" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
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
                        <h4 class="text-xl font-bold text-gray-900 mb-2">{{ $selectedVendor->name }}</h4>
                        <p class="text-gray-600 mb-3">{{ $selectedVendor->email }}</p>
                        
                        <div class="flex items-center space-x-4 text-sm">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $selectedVendor->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $selectedVendor->is_active ? 'Active' : 'Inactive' }}
                            </span>
                            <span class="text-gray-500">Joined {{ $selectedVendor->created_at->format('M d, Y') }}</span>
                        </div>

                        @if($selectedVendor->bio)
                            <p class="mt-3 text-sm text-gray-600">{{ $selectedVendor->bio }}</p>
                        @endif
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-blue-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ $selectedVendor->marketplaceItems->count() }}</div>
                        <div class="text-sm text-blue-500">Total Items</div>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $selectedVendor->marketplaceItems->where('status', 'approved')->count() }}</div>
                        <div class="text-sm text-green-500">Published</div>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-4 text-center">
                        @php $vendorStats = $selectedVendor->getVendorOrderStats(); @endphp
                        <div class="text-2xl font-bold text-purple-600">{{ $vendorStats['total_orders'] }}</div>
                        <div class="text-sm text-purple-500">Total Orders</div>
                    </div>
                    <div class="bg-indigo-50 rounded-lg p-4 text-center">
                        <div class="text-2xl font-bold text-indigo-600">₦{{ number_format($vendorStats['total_earnings'], 0) }}</div>
                        <div class="text-sm text-indigo-500">Total Earnings</div>
                    </div>
                </div>

                <!-- Recent Items -->
                <div>
                    <h5 class="font-semibold text-gray-900 mb-3">Recent Items</h5>
                    <div class="space-y-3">
                        @forelse($selectedVendor->marketplaceItems->take(5) as $item)
                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <img src="{{asset('storage/' .   $item->getPrimaryImage()) ?? 'https://via.placeholder.com/40x40' }}" 
                                         alt="{{ $item->title }}" 
                                         class="w-10 h-10 object-cover rounded">
                                    <div>
                                        <p class="font-medium text-sm">{{ Str::limit($item->title, 40) }}</p>
                                        <p class="text-xs text-gray-500">{{ $item->created_at->format('M d, Y') }}</p>
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
                            <p class="text-gray-500 text-sm">No items yet</p>
                        @endforelse
                    </div>
                </div>

                <!-- Contact Information -->
                <div>
                    <h5 class="font-semibold text-gray-900 mb-3">Contact Information</h5>
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
                <div class="bg-gray-50 rounded-lg p-4">
                    <h5 class="font-medium text-gray-900 mb-3">Admin Actions</h5>
                    <div class="flex flex-wrap gap-2">
                        @if($selectedVendor->is_active)
                            <button wire:click="deactivateVendor({{ $selectedVendor->id }})" 
                                    class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700">
                                <i class="fas fa-user-slash mr-1"></i>Deactivate
                            </button>
                        @else
                            <button wire:click="activateVendor({{ $selectedVendor->id }})" 
                                    class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700">
                                <i class="fas fa-user-check mr-1"></i>Activate
                            </button>
                        @endif
                        
                        <button class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">
                            <i class="fas fa-envelope mr-1"></i>Send Email
                        </button>
                        
                        <button class="px-3 py-1 bg-purple-600 text-white rounded hover:bg-purple-700">
                            <i class="fas fa-chart-bar mr-1"></i>View Analytics
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
     class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                <i class="fas fa-exclamation-triangle text-yellow-600"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-4" x-text="message"></h3>
            <div class="mt-6 flex justify-center space-x-3">
                <button @click="show = false" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
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