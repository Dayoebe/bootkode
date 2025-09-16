<!-- Enhanced Filters and Controls -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between space-y-4 lg:space-y-0">
        <!-- Search and Filters -->
        <div class="flex flex-wrap items-center gap-3">
            <div class="relative">
                <input wire:model.live.debounce.300ms="search" 
                       type="text" 
                       placeholder="Search your items..." 
                       class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>
            
            <select wire:model.live="status" 
                    class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                <option value="">All Status</option>
                @isset($statuses)
                    @foreach($statuses as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                @endisset
            </select>

            <select wire:model.live="sortBy" 
                    class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                <option value="latest">Latest First</option>
                <option value="oldest">Oldest First</option>
                <option value="title">Title A-Z</option>
                <option value="price_low">Price Low to High</option>
                <option value="price_high">Price High to Low</option>
                <option value="status">Status</option>
                <option value="sales">Best Selling</option>
            </select>

            @if($search || $status)
                <button wire:click="$set('search', ''); $set('status', '')" 
                        class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-times mr-1"></i>
                    Clear
                </button>
            @endif
        </div>
        
        <!-- Action Buttons -->
        <div class="flex items-center space-x-3">
            @if($selectedItems && count($selectedItems) > 0)
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-600">{{ count($selectedItems) }} selected</span>
                    <select wire:model="bulkAction" 
                            class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                        <option value="">Bulk Actions</option>
                        <option value="submit_review">Submit for Review</option>
                        <option value="duplicate">Duplicate Selected</option>
                        <option value="delete">Delete Selected</option>
                    </select>
                    <button wire:click="executeBulkAction" 
                            class="px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors"
                            @if(!$bulkAction) disabled class="px-3 py-1.5 bg-gray-300 text-gray-500 text-sm rounded-lg cursor-not-allowed" @endif>
                        Execute
                    </button>
                </div>
            @endif

            <button wire:click="showCreate" 
                    class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Create New Item
            </button>
        </div>
    </div>
    
    <!-- Quick Stats Row -->
    <div class="mt-6 grid grid-cols-2 md:grid-cols-6 gap-4">
        <div class="text-center p-3 bg-gray-50 rounded-lg">
            <div class="text-lg font-bold text-gray-900">{{ $analyticsData['total_items'] ?? 0 }}</div>
            <div class="text-xs text-gray-600">Total Items</div>
        </div>
        <div class="text-center p-3 bg-green-50 rounded-lg">
            <div class="text-lg font-bold text-green-600">{{ $analyticsData['published_items'] ?? 0 }}</div>
            <div class="text-xs text-green-600">Published</div>
        </div>
        <div class="text-center p-3 bg-yellow-50 rounded-lg">
            <div class="text-lg font-bold text-yellow-600">{{ $analyticsData['draft_items'] ?? 0 }}</div>
            <div class="text-xs text-yellow-600">Drafts</div>
        </div>
        <div class="text-center p-3 bg-blue-50 rounded-lg">
            <div class="text-lg font-bold text-blue-600">{{ $analyticsData['pending_items'] ?? 0 }}</div>
            <div class="text-xs text-blue-600">Pending</div>
        </div>
        <div class="text-center p-3 bg-red-50 rounded-lg">
            <div class="text-lg font-bold text-red-600">{{ $analyticsData['rejected_items'] ?? 0 }}</div>
            <div class="text-xs text-red-600">Rejected</div>
        </div>
        <div class="text-center p-3 bg-purple-50 rounded-lg">
            <div class="text-lg font-bold text-purple-600">{{ number_format($analyticsData['avg_rating'] ?? 0, 1) }}</div>
            <div class="text-xs text-purple-600">Avg Rating</div>
        </div>
    </div>
</div>

<!-- Enhanced Items Grid -->
@isset($items)
    @if($items->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <!-- Bulk Select Header -->
            <div class="p-4 border-b border-gray-200 bg-gray-50">
                <label class="flex items-center">
                    <input type="checkbox" wire:model="selectAll" wire:click="toggleSelectAll"
                           class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                    <span class="ml-2 text-sm text-gray-700">Select All</span>
                </label>
            </div>

            <!-- Items Cards Grid -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($items as $item)
                        <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-all duration-200 {{ in_array($item->id, $selectedItems) ? 'ring-2 ring-purple-500 bg-purple-50' : 'hover:border-purple-300' }}">
                            <!-- Card Header with Selection -->
                            <div class="relative">
                                @if($item->getPrimaryImage())
                                    <img src="{{ asset('storage/' . $item->getPrimaryImage()) }}" 
                                         alt="{{ $item->title }}" 
                                         class="w-full h-48 object-cover">
                                @else
                                    <div class="w-full h-48 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400 text-3xl"></i>
                                    </div>
                                @endif
                                
                                <!-- Selection Checkbox -->
                                <div class="absolute top-3 left-3">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" wire:model="selectedItems" value="{{ $item->id }}"
                                               class="rounded border-gray-300 text-purple-600 focus:ring-purple-500 bg-white shadow-sm">
                                    </label>
                                </div>

                                <!-- Status Badge -->
                                <div class="absolute top-3 right-3">
                                    @php
                                        $statusColors = [
                                            'draft' => 'bg-gray-100 text-gray-800',
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'approved' => 'bg-green-100 text-green-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                            'suspended' => 'bg-orange-100 text-orange-800',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $statusColors[$item->status] ?? 'bg-gray-100 text-gray-800' }} backdrop-blur-sm">
                                        <i class="fas fa-{{ $item->status === 'approved' ? 'check' : ($item->status === 'pending' ? 'clock' : ($item->status === 'rejected' ? 'times' : 'edit')) }} mr-1"></i>
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </div>

                         <!-- Price Tag -->
<div class="absolute bottom-3 right-3">
    <div class="bg-white/90 backdrop-blur-sm rounded-lg px-2 py-1">
        @if($item->hasDiscount())
            <span class="text-lg font-bold text-gray-900">₦{{ number_format($item->discount_price, 0) }}</span>
            <div class="text-sm text-gray-500 line-through">₦{{ number_format($item->price, 0) }}</div>
        @else
            <span class="text-lg font-bold text-gray-900">₦{{ number_format($item->price, 0) }}</span>
        @endif
    </div>
</div>
                            </div>

                            <!-- Card Content -->
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900 text-lg mb-2 line-clamp-2">{{ $item->title }}</h3>
                                <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $item->short_description }}</p>
                                
                                <!-- Item Stats -->
                                <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                                    <div class="flex items-center space-x-3">
                                        <span class="flex items-center">
                                            <i class="fas fa-eye mr-1"></i>
                                            {{ number_format($item->views_count ?? 0) }}
                                        </span>
                                        <span class="flex items-center">
                                            <i class="fas fa-shopping-cart mr-1"></i>
                                            {{ $item->orders->count() ?? 0 }}
                                        </span>
                                        <span class="flex items-center">
                                            <i class="fas fa-star mr-1 text-yellow-400"></i>
                                            {{ number_format($item->average_rating ?? 0, 1) }}
                                        </span>
                                    </div>
                                    <span class="text-xs">{{ $item->updated_at->diffForHumans() }}</span>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex items-center gap-2 flex-wrap">
                                    <!-- Edit Button (only for drafts and rejected) -->
                                    @if(in_array($item->status, ['draft', 'rejected']))
                                        <button wire:click="editItem({{ $item->id }})"
                                                class="flex-1 min-w-0 px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center">
                                            <i class="fas fa-edit mr-1"></i>
                                            Edit
                                        </button>
                                    @endif

                                    <!-- Submit for Review (only for drafts) -->
                                    @if($item->status === 'draft')
                                        <button wire:click="submitForReviewFromList({{ $item->id }})"
                                                class="flex-1 min-w-0 px-3 py-1.5 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center">
                                            <i class="fas fa-paper-plane mr-1"></i>
                                            Submit
                                        </button>
                                    @endif

                                    <!-- Duplicate Button -->
                                    <button wire:click="duplicateItem({{ $item->id }})"
                                            class="px-3 py-1.5 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700 transition-colors">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                    
                                    <!-- Delete Button (only if no orders) -->
                                    @if(!$item->orders()->exists())
                                        <button wire:click="deleteItem({{ $item->id }})"
                                                onclick="confirm('Are you sure you want to delete this item?') || event.stopImmediatePropagation()"
                                                class="px-3 py-1.5 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif

                                    <!-- View Public (only for approved items) -->
                                    @if($item->status === 'approved')
                                        <a href="#" target="_blank"
                                           class="px-3 py-1.5 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    @endif
                                </div>

                                <!-- Recent Orders Preview -->
                                @if($item->orders && $item->orders->count() > 0)
                                    <div class="mt-3 pt-3 border-t border-gray-100">
                                        <h5 class="text-xs font-medium text-gray-700 mb-2 flex items-center">
                                            <i class="fas fa-shopping-cart mr-1"></i>
                                            Recent Orders:
                                        </h5>
                                        <div class="space-y-1">
                                            @foreach($item->orders->take(2) as $order)
                                                <div class="flex justify-between text-xs">
                                                    <span class="text-gray-600">{{ Str::limit($order->customer->name, 20) }}</span>
                                                    <span class="text-{{ $order->status === 'completed' ? 'green' : 'yellow' }}-600 font-medium">
                                                        {{ ucfirst($order->status) }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Item Type Badge -->
                                <div class="mt-3 pt-3 border-t border-gray-100">
                                    <div class="flex items-center justify-between">
                                        <span class="inline-flex items-center px-2 py-1 bg-purple-100 text-purple-800 text-xs font-medium rounded-full">
                                            <i class="fas fa-{{ $item->type === 'course' ? 'graduation-cap' : ($item->type === 'service' ? 'handshake' : 'file-alt') }} mr-1"></i>
                                            {{ ucfirst($item->type) }}
                                        </span>
                                        @if($item->duration_minutes)
                                            <span class="text-xs text-gray-500">{{ $item->getFormattedDuration() }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $items->links() }}
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="text-center py-16">
                <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-store text-3xl text-purple-600"></i>
                </div>
                
                <h3 class="text-xl font-medium text-gray-900 mb-2">
                    @if($search || $status)
                        No matching items found
                    @else
                        No items yet
                    @endif
                </h3>
                <p class="text-gray-500 mb-8 max-w-md mx-auto">
                    @if($search || $status)
                        Try adjusting your search criteria or filters to find what you're looking for.
                    @else
                        Ready to start selling? Create your first marketplace item and reach thousands of potential customers.
                    @endif
                </p>
                
                <div class="space-y-3">
                    @if($search || $status)
                        <button wire:click="$set('search', ''); $set('status', '')" 
                                class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-times mr-2"></i>
                            Clear Filters
                        </button>
                        <br>
                    @endif
                    
                    <button wire:click="showCreate" 
                            class="inline-flex items-center px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Create Your First Item
                    </button>
                </div>
            </div>
        </div>
    @endif
@endisset