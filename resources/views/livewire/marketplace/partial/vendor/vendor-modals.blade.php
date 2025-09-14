{{-- resources/views/livewire/marketplace/partial/modals/vendor-modals.blade.php --}}

<!-- Approval Modal -->
@if($showApproveModal && $itemToApprove)
    <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="fas fa-check-circle mr-2 text-green-600"></i>
                    Approve Listing
                </h3>
                <button wire:click="closeApproveModal" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="mb-6">
                <div class="flex items-center space-x-3 mb-4">
                    @if($itemToApprove->getPrimaryImage())
                        <img src="{{ asset('storage/' . $itemToApprove->getPrimaryImage()) }}" 
                             alt="{{ $itemToApprove->title }}" 
                             class="w-16 h-16 object-cover rounded-lg border border-gray-200">
                    @else
                        <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center border border-gray-200">
                            <i class="fas fa-image text-gray-400"></i>
                        </div>
                    @endif
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900">{{ $itemToApprove->title }}</h4>
                        <p class="text-sm text-gray-600">by {{ $itemToApprove->vendor->name }}</p>
                        <p class="text-sm text-gray-500">{{ $itemToApprove->getFormattedPrice() }}</p>
                    </div>
                </div>
                
                <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                    <p class="text-sm text-green-800 flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        Are you sure you want to approve this listing? It will become publicly visible in the marketplace.
                    </p>
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <button wire:click="closeApproveModal"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button wire:click="approveItem"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center">
                    <i class="fas fa-check mr-2"></i>
                    Approve Listing
                </button>
            </div>
        </div>
    </div>
@endif

<!-- Withdraw Approval Modal -->
@if($showWithdrawModal && $itemToWithdraw)
    <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2 text-orange-600"></i>
                    Withdraw Approval
                </h3>
                <button wire:click="closeWithdrawModal" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="mb-6">
                <div class="flex items-center space-x-3 mb-4">
                    @if($itemToWithdraw->getPrimaryImage())
                        <img src="{{ asset('storage/' . $itemToWithdraw->getPrimaryImage()) }}" 
                             alt="{{ $itemToWithdraw->title }}" 
                             class="w-16 h-16 object-cover rounded-lg border border-gray-200">
                    @else
                        <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center border border-gray-200">
                            <i class="fas fa-image text-gray-400"></i>
                        </div>
                    @endif
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900">{{ $itemToWithdraw->title }}</h4>
                        <p class="text-sm text-gray-600">by {{ $itemToWithdraw->vendor->name }}</p>
                        <p class="text-sm text-gray-500">{{ $itemToWithdraw->getFormattedPrice() }}</p>
                    </div>
                </div>
                
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-3">
                    <p class="text-sm text-orange-800 flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Are you sure you want to withdraw approval for this listing? It will no longer be publicly visible and will return to draft status.
                    </p>
                </div>
                
                @if($itemToWithdraw->orders && $itemToWithdraw->orders->count() > 0)
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mt-3">
                        <p class="text-sm text-yellow-800 flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Warning:</strong> This item has {{ $itemToWithdraw->orders->count() }} existing orders. Withdrawing approval may affect customer access.
                        </p>
                    </div>
                @endif
            </div>

            <div class="flex justify-end space-x-3">
                <button wire:click="closeWithdrawModal"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button wire:click="withdrawApproval"
                        class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors flex items-center">
                    <i class="fas fa-times mr-2"></i>
                    Withdraw Approval
                </button>
            </div>
        </div>
    </div>
@endif