{{-- resources/views/livewire/marketplace/partial/my-listings.blade.php --}}
<div class="space-y-6" wire:poll.visible="1000ms">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between space-y-4 md:space-y-0">
            <div>
                <h2 class="text-xl font-bold text-gray-900">
                    {{ $isAdmin ? 'All Listings' : 'My Listings' }}
                </h2>
                <p class="text-gray-600">
                    {{ $isAdmin ? 'Manage all marketplace items' : 'Manage your marketplace items' }}
                </p>
            </div>
            
            <a href="{{ route('marketplace.seller.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Create New Item
            </a>
        </div>
        
        <!-- Filters -->
        <div class="mt-4 flex flex-wrap gap-3">
            <input wire:model.live.debounce.300ms="search" 
                   type="text" 
                   placeholder="Search items..." 
                   class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
            
            <select wire:model.live="status" 
                    class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                <option value="">All Status</option>
                @foreach($statuses as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Items List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        @if($items->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($items as $item)
                    <div class="p-6">
                        <div class="flex items-start space-x-4">
                            <!-- Item Image -->
                            <div class="flex-shrink-0">
                                @if($item->getPrimaryImage())
                                    <img src="{{ asset('storage/' . $item->getPrimaryImage()) }}" 
                                         alt="{{ $item->title }}" 
                                         class="w-20 h-20 object-cover rounded-lg">
                                @else
                                    <div class="w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Item Details -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900">
                                            {{ $item->title }}
                                        </h3>
                                        
                                        @if($isAdmin)
                                            <p class="text-sm text-gray-600 mt-1">
                                                Vendor: {{ $item->vendor->name }}
                                            </p>
                                        @endif
                                        
                                        <p class="text-sm text-gray-600 mt-1 line-clamp-2">
                                            {{ $item->short_description }}
                                        </p>
                                        <div class="flex items-center space-x-4 mt-2 text-sm text-gray-500">
                                            <span><i class="fas fa-eye mr-1"></i>{{ $item->views_count }} views</span>
                                            <span><i class="fas fa-shopping-cart mr-1"></i>{{ $item->sales_count }} sales</span>
                                            <span><i class="fas fa-star mr-1"></i>{{ number_format($item->average_rating, 1) }}</span>
                                        </div>
                                    </div>
                                    
                                    <!-- Price and Status -->
                                    <div class="text-right">
                                        <p class="text-lg font-semibold text-gray-900">
                                            {{ $item->getFormattedPrice() }}
                                        </p>
                                        <span class="inline-block px-2 py-1 bg-{{ $item->status_color }}-100 text-{{ $item->status_color }}-800 text-xs font-medium rounded-full mt-1">
                                            {{ $item->status_name }}
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Actions -->
                                <div class="mt-4 flex items-center space-x-3 flex-wrap gap-2">
                                    <a href="{{ route('marketplace.items.update', $item) }}" 
                                       class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors">
                                        <i class="fas fa-edit mr-1"></i>
                                        Edit
                                    </a>
                                    
                                    @if(!$isAdmin)
                                        @if($item->isDraft())
                                            <button wire:click="submitForReview({{ $item->id }})"
                                                    class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition-colors">
                                                <i class="fas fa-paper-plane mr-1"></i>
                                                Submit for Review
                                            </button>
                                        @endif
                                        
                                        @if($item->isPending())
                                            <button wire:click="withdrawSubmission({{ $item->id }})"
                                                    class="inline-flex items-center px-3 py-1 bg-yellow-600 text-white text-sm rounded hover:bg-yellow-700 transition-colors">
                                                <i class="fas fa-undo mr-1"></i>
                                                Withdraw Submission
                                            </button>
                                        @endif
                                    @endif
                                    
                                    @if($isAdmin && $item->isPending())
                                        <button wire:click="openApproveModal({{ $item->id }})"
                                                class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition-colors">
                                            <i class="fas fa-check mr-1"></i>
                                            Approve
                                        </button>
                                    @endif
                                    
                                    @if($isAdmin && $item->isApproved())
                                        <button wire:click="openWithdrawModal({{ $item->id }})"
                                                class="inline-flex items-center px-3 py-1 bg-orange-600 text-white text-sm rounded hover:bg-orange-700 transition-colors">
                                            <i class="fas fa-times mr-1"></i>
                                            Withdraw Approval
                                        </button>
                                    @endif
                                    
                                    <button wire:click="duplicateItem({{ $item->id }})"
                                            class="inline-flex items-center px-3 py-1 bg-gray-600 text-white text-sm rounded hover:bg-gray-700 transition-colors">
                                        <i class="fas fa-copy mr-1"></i>
                                        Duplicate
                                    </button>
                                    
                                    @if(!$item->orders()->exists())
                                        <button wire:click="deleteItem({{ $item->id }})"
                                                onclick="confirm('Are you sure you want to delete this item?') || event.stopImmediatePropagation()"
                                                class="inline-flex items-center px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700 transition-colors">
                                            <i class="fas fa-trash mr-1"></i>
                                            Delete
                                        </button>
                                    @endif
                                    
                                    @if($item->isPublished())
                                        <a href="{{ route('marketplace.item.public', $item->slug) }}" 
                                           target="_blank"
                                           class="inline-flex items-center px-3 py-1 border border-gray-300 text-gray-700 text-sm rounded hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-external-link-alt mr-1"></i>
                                            View Public
                                        </a>
                                    @endif
                                </div>
                                
                                <!-- Recent Orders -->
                                @if($item->orders->count() > 0)
                                    <div class="mt-3 p-3 bg-gray-50 rounded-lg">
                                        <h5 class="text-sm font-medium text-gray-700 mb-2">Recent Orders:</h5>
                                        <div class="space-y-1">
                                            @foreach($item->orders->take(3) as $order)
                                                <div class="flex justify-between text-sm">
                                                    <span>{{ $order->customer->name }}</span>
                                                    <span class="text-{{ $order->status_color }}-600">{{ $order->status }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $items->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-box-open text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No items found</h3>
                <p class="text-gray-500 mb-6">
                    {{ $isAdmin ? 'No marketplace items found.' : 'You haven\'t created any marketplace items yet.' }}
                </p>
                @if(!$isAdmin)
                    <a href="{{ route('marketplace.seller.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Create Your First Item
                    </a>
                @endif
            </div>
        @endif
    </div>

    <!-- Approval Modal -->
    @if($showApproveModal && $itemToApprove)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg max-w-md w-full p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Approve Listing</h3>
                    <button wire:click="closeApproveModal" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="mb-4">
                    <div class="flex items-center space-x-3 mb-3">
                        @if($itemToApprove->getPrimaryImage())
                            <img src="{{ asset('storage/' . $itemToApprove->getPrimaryImage()) }}" 
                                 alt="{{ $itemToApprove->title }}" 
                                 class="w-12 h-12 object-cover rounded-lg">
                        @else
                            <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-image text-gray-400"></i>
                            </div>
                        @endif
                        <div>
                            <p class="font-medium text-gray-900">{{ $itemToApprove->title }}</p>
                            <p class="text-sm text-gray-500">by {{ $itemToApprove->vendor->name }}</p>
                        </div>
                    </div>
                    
                    <p class="text-sm text-gray-600">
                        Are you sure you want to approve this listing? It will become publicly visible in the marketplace.
                    </p>
                </div>

                <div class="flex justify-end space-x-3">
                    <button wire:click="closeApproveModal"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button wire:click="approveItem"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
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
                    <h3 class="text-lg font-medium text-gray-900">Withdraw Approval</h3>
                    <button wire:click="closeWithdrawModal" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="mb-4">
                    <div class="flex items-center space-x-3 mb-3">
                        @if($itemToWithdraw->getPrimaryImage())
                            <img src="{{ asset('storage/' . $itemToWithdraw->getPrimaryImage()) }}" 
                                 alt="{{ $itemToWithdraw->title }}" 
                                 class="w-12 h-12 object-cover rounded-lg">
                        @else
                            <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-image text-gray-400"></i>
                            </div>
                        @endif
                        <div>
                            <p class="font-medium text-gray-900">{{ $itemToWithdraw->title }}</p>
                            <p class="text-sm text-gray-500">by {{ $itemToWithdraw->vendor->name }}</p>
                        </div>
                    </div>
                    
                    <p class="text-sm text-gray-600">
                        Are you sure you want to withdraw approval for this listing? It will no longer be publicly visible and will return to draft status.
                    </p>
                </div>

                <div class="flex justify-end space-x-3">
                    <button wire:click="closeWithdrawModal"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button wire:click="withdrawApproval"
                            class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700">
                        Withdraw Approval
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>