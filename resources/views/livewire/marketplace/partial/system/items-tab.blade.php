{{-- resources/views/livewire/marketplace/partial/system/items-tab.blade.php --}}
<div class="space-y-6">
    <!-- Filters and Search -->
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <!-- Search -->
            <div class="flex-1 max-w-lg">
                <div class="relative">
                    <input type="text" 
                           wire:model.live.debounce.300ms="searchTerm"
                           placeholder="Search items by title or description..."
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
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                    <option value="suspended">Suspended</option>
                </select>

                <select wire:model.live="typeFilter" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">All Types</option>
                    <option value="course">Course</option>
                    <option value="resource">Digital Resource</option>
                    <option value="service">Service</option>
                </select>

                <select wire:model.live="vendorFilter" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                    <option value="">All Vendors</option>
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                    @endforeach
                </select>

                <button wire:click="$set('searchTerm', '')" class="px-3 py-2 text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i> Clear
                </button>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Marketplace Items</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <button wire:click="$set('sortBy', 'title')" class="flex items-center space-x-1 hover:text-gray-700">
                                <span>Item</span>
                                <i class="fas fa-sort text-xs"></i>
                            </button>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <button wire:click="$set('sortBy', 'type')" class="flex items-center space-x-1 hover:text-gray-700">
                                <span>Type</span>
                                <i class="fas fa-sort text-xs"></i>
                            </button>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <button wire:click="$set('sortBy', 'price')" class="flex items-center space-x-1 hover:text-gray-700">
                                <span>Price</span>
                                <i class="fas fa-sort text-xs"></i>
                            </button>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <button wire:click="$set('sortBy', 'created_at')" class="flex items-center space-x-1 hover:text-gray-700">
                                <span>Created</span>
                                <i class="fas fa-sort text-xs"></i>
                            </button>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12">
                                        <img class="h-12 w-12 rounded-lg object-cover" 
                                             src="{{asset('storage/' .   $item->getPrimaryImage()) ?? 'https://via.placeholder.com/48x48' }}" 
                                             alt="{{ $item->title }}">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ Str::limit($item->title, 50) }}</div>
                                        <div class="text-sm text-gray-500">
                                            @if($item->categories->count() > 0)
                                                @foreach($item->categories->take(2) as $category)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 mr-1">
                                                        {{ $category->name }}
                                                    </span>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $item->vendor->name }}</div>
                                <div class="text-sm text-gray-500">{{ $item->vendor->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $item->type === 'course' ? 'bg-blue-100 text-blue-800' : 
                                       ($item->type === 'resource' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800') }}">
                                    {{ $item->type_name }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($item->hasDiscount())
                                    <div>
                                        <span class="line-through text-gray-500">₦{{ number_format($item->price, 0) }}</span>
                                        <span class="text-green-600 font-medium">₦{{ number_format($item->discount_price, 0) }}</span>
                                    </div>
                                @else
                                    <span class="font-medium">₦{{ number_format($item->price, 0) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    bg-{{ $item->status_color }}-100 text-{{ $item->status_color }}-800">
                                    {{ $item->status_name }}
                                </span>
                                @if($item->isRejected() && $item->rejection_reason)
                                    <div class="text-xs text-red-600 mt-1" title="{{ $item->rejection_reason }}">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        {{ Str::limit($item->rejection_reason, 30) }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div>{{ $item->created_at->format('M d, Y') }}</div>
                                <div class="text-xs">{{ $item->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <button wire:click="viewItem({{ $item->id }})" 
                                            class="text-indigo-600 hover:text-indigo-900" 
                                            title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    @if($item->isPending())
                                        <button wire:click="approveItem({{ $item->id }})" 
                                                class="text-green-600 hover:text-green-900" 
                                                title="Approve Item">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button wire:click="viewItem({{ $item->id }})" 
                                                class="text-red-600 hover:text-red-900" 
                                                title="Reject Item">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif

                                    @if($item->isApproved())
                                        <button wire:click="suspendItem({{ $item->id }})" 
                                                class="text-orange-600 hover:text-orange-900" 
                                                title="Suspend Item">
                                            <i class="fas fa-pause"></i>
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
                                                    <i class="fas fa-edit mr-2"></i>Edit Item
                                                </a>
                                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <i class="fas fa-chart-bar mr-2"></i>View Analytics
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
                            <td colspan="7" class="px-6 py-8 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-box-open text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">No items found</p>
                                    <p class="text-sm">Try adjusting your search or filter criteria</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($items->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $items->links() }}
            </div>
        @endif
    </div>

    <!-- Bulk Actions -->
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <h4 class="text-sm font-medium text-gray-900">Bulk Actions</h4>
                <div class="flex space-x-2">
                    <button class="px-3 py-1 text-sm bg-green-100 text-green-800 rounded hover:bg-green-200">
                        <i class="fas fa-check mr-1"></i>Approve Selected
                    </button>
                    <button class="px-3 py-1 text-sm bg-red-100 text-red-800 rounded hover:bg-red-200">
                        <i class="fas fa-times mr-1"></i>Reject Selected
                    </button>
                    <button class="px-3 py-1 text-sm bg-orange-100 text-orange-800 rounded hover:bg-orange-200">
                        <i class="fas fa-pause mr-1"></i>Suspend Selected
                    </button>
                </div>
            </div>
            
            <div class="text-sm text-gray-500">
                Showing {{ $items->firstItem() ?? 0 }} to {{ $items->lastItem() ?? 0 }} of {{ $items->total() }} items
            </div>
        </div>
    </div>
</div>