{{-- resources/views/livewire/marketplace/admin/partials/modals.blade.php --}}

<!-- Vendor Approval Modal -->
@if($showApprovalModal && $selectedUser)
    <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" x-trap.inert.noscroll="true" role="dialog" aria-modal="true" aria-label="Approve Vendor" tabindex="-1" x-data="{}" @keydown.escape.stop="$wire.set(&#x27;showApprovalModal&#x27;, false)">
        <div class="bg-surface rounded-lg max-w-md w-full p-6 shadow-sm transform transition-all">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-content flex items-center">
                    <i aria-hidden="true" class="fas fa-user-check mr-2 text-green-600"></i>
                    Approve Vendor
                </h3>
                <button wire:click="closeApprovalModal" class="text-content-muted hover:text-content-muted transition-colors" aria-label="Close">
                    <i aria-hidden="true" class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="mb-6">
                <div class="flex items-center space-x-4 mb-4">
                    @if($selectedUser->profile_picture)
                        <img src="{{ asset('storage/' . $selectedUser->profile_picture) }}" alt="{{ $selectedUser->name }}"
                            class="w-14 h-14 object-cover rounded-full ring-2 ring-green-200">
                    @else
                        <div
                            class="w-14 h-14 bg-surface-muted   rounded-full flex items-center justify-center ring-2 ring-green-200">
                            <span class="text-white font-bold text-lg">
                                {{ strtoupper(substr($selectedUser->name, 0, 2)) }}
                            </span>
                        </div>
                    @endif
                    <div>
                        <p class="font-semibold text-content text-lg">{{ $selectedUser->name }}</p>
                        <p class="text-sm text-content-muted">{{ $selectedUser->email }}</p>
                    </div>
                </div>

                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <p class="text-sm text-green-800">
                        <i aria-hidden="true" class="fas fa-info-circle mr-2"></i>
                        This will grant {{ $selectedUser->name }} full vendor privileges to create and sell items on the
                        marketplace.
                    </p>
                </div>
            </div>

            <div class="mb-6">
                <label for="commissionRate" class="block text-sm font-medium text-content mb-2">
                    <i aria-hidden="true" class="fas fa-percentage mr-1"></i>
                    Vendor Commission Rate (%)
                </label>
                <div class="relative">
                    <input wire:model="commissionRate" type="number" min="0" max="100" step="5" id="commissionRate"
                        class="w-full px-4 py-3 border border-line rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                    <span class="absolute right-3 top-3 text-content-muted">%</span>
                </div>
                <div class="mt-2 flex items-center justify-between text-xs">
                    <span class="text-content-muted">Vendor gets {{ $commissionRate }}%</span>
                    <span class="text-content-muted">Platform gets {{ 100 - $commissionRate }}%</span>
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <button wire:click="closeApprovalModal"
                    class="px-6 py-2 border border-line rounded-lg text-content hover:bg-surface-muted transition-colors">
                    Cancel
                </button>
                <button wire:click="approveVendor"
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center">
                    <i aria-hidden="true" class="fas fa-check mr-2"></i>
                    Approve Vendor
                </button>
            </div>
        </div>
    </div>
@endif

<!-- Vendor Rejection Modal -->
@if($showRejectionModal && $selectedUser)
    <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" x-trap.inert.noscroll="true" role="dialog" aria-modal="true" aria-label="Reject Application" tabindex="-1" x-data="{}" @keydown.escape.stop="$wire.set(&#x27;showRejectionModal&#x27;, false)">
        <div class="bg-surface rounded-lg max-w-md w-full p-6 shadow-sm transform transition-all">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-content flex items-center">
                    <i aria-hidden="true" class="fas fa-user-times mr-2 text-red-600"></i>
                    Reject Application
                </h3>
                <button wire:click="closeRejectionModal" class="text-content-muted hover:text-content-muted transition-colors" aria-label="Close">
                    <i aria-hidden="true" class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="mb-6">
                <div class="flex items-center space-x-4 mb-4">
                    @if($selectedUser->profile_picture)
                        <img src="{{ asset('storage/' . $selectedUser->profile_picture) }}" alt="{{ $selectedUser->name }}"
                            class="w-14 h-14 object-cover rounded-full ring-2 ring-red-200">
                    @else
                        <div
                            class="w-14 h-14 bg-surface-muted   rounded-full flex items-center justify-center ring-2 ring-red-200">
                            <span class="text-white font-bold text-lg">
                                {{ strtoupper(substr($selectedUser->name, 0, 2)) }}
                            </span>
                        </div>
                    @endif
                    <div>
                        <p class="font-semibold text-content text-lg">{{ $selectedUser->name }}</p>
                        <p class="text-sm text-content-muted">{{ $selectedUser->email }}</p>
                    </div>
                </div>

                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <p class="text-sm text-red-800">
                        <i aria-hidden="true" class="fas fa-exclamation-triangle mr-2"></i>
                        Please provide a clear reason for rejecting this vendor application. This message will be sent
                        to the applicant.
                    </p>
                </div>
            </div>

            <div class="mb-6">
                <label for="rejectionReason" class="block text-sm font-medium text-content mb-2">
                    <i aria-hidden="true" class="fas fa-comment mr-1"></i>
                    Rejection Reason *
                </label>
                <textarea wire:model="rejectionReason" id="rejectionReason" rows="4"
                    placeholder="Please explain why this application is being rejected..."
                    class="w-full px-4 py-3 border border-line rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 resize-none transition-colors"></textarea>
                @error('rejectionReason')
                    <p class="text-red-600 text-sm mt-2 flex items-center">
                        <i aria-hidden="true" class="fas fa-exclamation-circle mr-1"></i>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="flex justify-end space-x-3">
                <button wire:click="closeRejectionModal"
                    class="px-6 py-2 border border-line rounded-lg text-content hover:bg-surface-muted transition-colors">
                    Cancel
                </button>
                <button wire:click="rejectVendor"
                    class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center"
                    @if(empty($rejectionReason)) disabled
                    class="px-6 py-2 bg-gray-300 text-content-muted rounded-lg cursor-not-allowed" @endif>
                    <i aria-hidden="true" class="fas fa-times mr-2"></i>
                    Reject Application
                </button>
            </div>
        </div>
    </div>
@endif

<!-- Order Details Modal -->
@if($showOrderModal && $selectedOrder)
    <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" x-trap.inert.noscroll="true" role="dialog" aria-modal="true" aria-label="Order Details" tabindex="-1" x-data="{}" @keydown.escape.stop="$wire.set(&#x27;showOrderModal&#x27;, false)">
        <div class="bg-surface rounded-lg max-w-2xl w-full p-6 max-h-screen overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-content">Order Details</h3>
                <button wire:click="closeOrderModal" class="text-content-muted hover:text-content-muted" aria-label="Close">
                    <i aria-hidden="true" class="fas fa-times"></i>
                </button>
            </div>

            <div class="space-y-6">
                <!-- Order Information -->
                <div class="bg-surface-muted rounded-lg p-4">
                    <h4 class="font-medium text-content mb-2">Order Information</h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-content-muted">Order Number:</span>
                            <span class="font-medium">{{ $selectedOrder->order_number }}</span>
                        </div>
                        <div>
                            <span class="text-content-muted">Date:</span>
                            <span class="font-medium">{{ $selectedOrder->created_at->format('M d, Y H:i') }}</span>
                        </div>
                        <div>
                            <span class="text-content-muted">Status:</span>
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                {{ ucfirst($selectedOrder->status) }}
                            </span>
                        </div>
                        <div>
                            <span class="text-content-muted">Payment:</span>
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ ucfirst(str_replace('_', ' ', $selectedOrder->payment_status)) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Customer Information -->
                <div class="bg-brand/10 rounded-lg p-4">
                    <h4 class="font-medium text-content mb-2">Customer</h4>
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-200 rounded-full flex items-center justify-center">
                            <span
                                class="text-brand font-medium">{{ substr($selectedOrder->customer->name ?? 'U', 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="font-medium">{{ $selectedOrder->customer->name ?? 'Unknown' }}</p>
                            <p class="text-sm text-content-muted">{{ $selectedOrder->customer->email ?? '' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Item Information -->
                <div class="bg-brand/10 rounded-lg p-4">
                    <h4 class="font-medium text-content mb-2">Item Details</h4>
                    <div class="flex items-center space-x-3">
                        @if($selectedOrder->item && $selectedOrder->item->thumbnail)
                            <img src="{{ asset('storage/' . $selectedOrder->item->thumbnail) }}"
                                alt="{{ $selectedOrder->item->title }}"
                                class="w-10 h-10 rounded-lg object-cover">
                        @else
                            <div class="w-10 h-10 bg-purple-200 rounded-lg flex items-center justify-center">
                                <i aria-hidden="true" class="fas fa-box text-brand"></i>
                            </div>
                        @endif
                        <div>
                            <p class="font-medium">{{ $selectedOrder->item->title ?? 'Deleted Item' }}</p>
                            <p class="text-sm text-content-muted">{{ ucfirst($selectedOrder->item->type ?? 'unknown') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Vendor Information -->
                <div class="bg-brand/10 rounded-lg p-4">
                    <h4 class="font-medium text-content mb-2">Vendor</h4>
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-indigo-200 rounded-full flex items-center justify-center">
                            <span class="text-brand font-medium">{{ substr($selectedOrder->vendor->name ?? 'V', 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="font-medium">{{ $selectedOrder->vendor->name ?? 'Unknown Vendor' }}</p>
                            <p class="text-sm text-content-muted">{{ $selectedOrder->vendor->email ?? '' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Financial Breakdown -->
                <div class="bg-green-50 rounded-lg p-4">
                    <h4 class="font-medium text-content mb-2">Financial Breakdown</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>Total Amount:</span>
                            <span class="font-semibold">₦{{ number_format($selectedOrder->total_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-brand">
                            <span>Platform Commission:</span>
                            <span class="font-medium">₦{{ number_format($selectedOrder->platform_commission, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-green-600">
                            <span>Vendor Earning:</span>
                            <span class="font-medium">₦{{ number_format($selectedOrder->vendor_earning, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t">
                <button wire:click="closeOrderModal"
                    class="px-4 py-2 border border-line rounded-lg text-content hover:bg-surface-muted transition-colors">
                    Close
                </button>
                @if($selectedOrder->status !== 'refunded' && $selectedOrder->payment_status === 'paid')
                    <button wire:click="processRefund({{ $selectedOrder->id }})"
                        onclick="return confirm('Are you sure you want to process a refund for this order?')"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        <i aria-hidden="true" class="fas fa-undo mr-2"></i>
                        Process Refund
                    </button>
                @endif
            </div>
        </div>
    </div>
@endif
