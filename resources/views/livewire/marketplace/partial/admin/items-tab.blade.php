{{-- resources/views/livewire/marketplace/admin/partials/items-tab.blade.php --}}
<div class="space-y-4">
    <!-- Filters and Bulk Actions -->
    <div class="flex flex-wrap gap-3 items-center justify-between">
        <div class="flex flex-wrap gap-3 items-center">
            <div class="relative">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search items..."
                    class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>

            <select wire:model.live="itemStatus"
                class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                <option value="">All Status</option>
                <option value="draft">Draft</option>
                <option value="pending">Pending Review</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
                <option value="suspended">Suspended</option>
            </select>

            <select wire:model.live="itemType"
                class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                <option value="">All Types</option>
                <option value="course">Course</option>
                <option value="resource">Digital Resource</option>
                <option value="service">Service</option>
            </select>
        </div>

        <!-- Bulk Actions -->
        @if($selectedItems && count($selectedItems) > 0)
            <div class="flex gap-2 items-center">
                <span class="text-sm text-gray-600">{{ count($selectedItems) }} selected</span>
                <select wire:model="bulkAction" 
                    class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 text-sm">
                    <option value="">Bulk Actions</option>
                    <option value="approve">Approve Selected</option>
                    <option value="reject">Reject Selected</option>
                    <option value="suspend">Suspend Selected</option>
                    <option value="feature">Feature Selected</option>
                    <option value="unfeature">Remove Feature</option>
                </select>
                <button wire:click="executeBulkAction" 
                    class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors"
                    @if(!$bulkAction) disabled class="px-4 py-2 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed" @endif>
                    Execute
                </button>
            </div>
        @endif
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-center">
            <div class="text-lg font-bold text-blue-600">{{ $stats['total_items'] ?? 0 }}</div>
            <div class="text-xs text-blue-600">Total Items</div>
        </div>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-center">
            <div class="text-lg font-bold text-yellow-600">{{ $stats['pending_approval'] ?? 0 }}</div>
            <div class="text-xs text-yellow-600">Pending</div>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-lg p-3 text-center">
            <div class="text-lg font-bold text-green-600">{{ $stats['published_items'] ?? 0 }}</div>
            <div class="text-xs text-green-600">Approved</div>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-center">
            <div class="text-lg font-bold text-red-600">{{ $stats['suspended_items'] ?? 0 }}</div>
            <div class="text-xs text-red-600">Suspended</div>
        </div>
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-3 text-center">
            @php
                $featuredCount = isset($items) ? $items->where('is_featured', true)->count() : 0;
            @endphp
            <div class="text-lg font-bold text-purple-600">{{ $featuredCount }}</div>
            <div class="text-xs text-purple-600">Featured</div>
        </div>
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 text-center">
            @php
                $rejectedCount = isset($items) ? $items->where('status', 'rejected')->count() : 0;
            @endphp
            <div class="text-lg font-bold text-gray-600">{{ $rejectedCount }}</div>
            <div class="text-xs text-gray-600">Rejected</div>
        </div>
    </div>

    <!-- Items Table -->
    @if(isset($items) && $items->count() > 0)
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">
                                <input type="checkbox" wire:model="selectAll" wire:click="toggleSelectAll"
                                    class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Item</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Vendor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Sales</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Created</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($items as $item)
                            <tr class="hover:bg-gray-50 {{ in_array($item->id, $selectedItems) ? 'bg-blue-50' : '' }}">
                                <td class="px-6 py-4">
                                    <input type="checkbox" wire:model="selectedItems" value="{{ $item->id }}"
                                        class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        @if($item->thumbnail)
                                            <div class="flex-shrink-0 h-12 w-12">
                                                <img class="h-12 w-12 rounded-lg object-cover"
                                                    src="{{ asset('storage/' . $item->thumbnail) }}"
                                                    alt="{{ $item->title }}">
                                            </div>
                                        @else
                                            <div
                                                class="flex-shrink-0 h-12 w-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-image text-gray-400"></i>
                                            </div>
                                        @endif
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ Str::limit($item->title, 40) }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ Str::limit($item->short_description ?? '', 60) }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($item->vendor && $item->vendor->profile_picture)
                                            <img class="h-8 w-8 rounded-full object-cover mr-2" 
                                                src="{{ asset('storage/' . $item->vendor->profile_picture) }}"
                                                alt="{{ $item->vendor->name }}">
                                        @else
                                            <div class="h-8 w-8 bg-gray-300 rounded-full flex items-center justify-center mr-2">
                                                <span class="text-xs font-medium text-gray-600">
                                                    {{ $item->vendor ? substr($item->vendor->name, 0, 1) : 'U' }}
                                                </span>
                                            </div>
                                        @endif
                                        <div class="text-sm text-gray-900">{{ $item->vendor->name ?? 'Unknown' }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ ucfirst($item->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-900">
                                    <div>
                                        <span class="font-semibold">₦{{ number_format($item->price, 2) }}</span>
                                        @if($item->discount_price && $item->discount_price < $item->price)
                                            <div class="text-xs text-gray-500 line-through">
                                                ₦{{ number_format($item->price, 2) }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div>
                                        <span class="font-medium">{{ $item->total_sales ?? 0 }}</span>
                                        @if($item->total_revenue)
                                            <div class="text-xs text-green-600">₦{{ number_format($item->total_revenue, 0) }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'draft' => 'bg-gray-100 text-gray-800',
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'approved' => 'bg-green-100 text-green-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                            'suspended' => 'bg-orange-100 text-orange-800',
                                        ];
                                    @endphp
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$item->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                    @if($item->is_featured)
                                        <span
                                            class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                            <i class="fas fa-star mr-1"></i>Featured
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $item->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center gap-1 justify-end">
                                        <!-- View Item -->
                                        <button wire:click="viewItemDetails({{ $item->id }})"
                                            class="text-blue-600 hover:text-blue-900 p-1" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        @if($item->status === 'pending')
                                            <!-- Approve -->
                                            <button wire:click="approveItem({{ $item->id }})"
                                                class="text-green-600 hover:text-green-900 p-1" title="Approve">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <!-- Reject -->
                                            <button wire:click="rejectItem({{ $item->id }})"
                                                onclick="confirm('Reject this item?') || event.stopImmediatePropagation()"
                                                class="text-red-600 hover:text-red-900 p-1" title="Reject">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif

                                        @if($item->status === 'approved')
                                            <!-- Feature/Unfeature -->
                                            <button wire:click="toggleFeatureItem({{ $item->id }})"
                                                class="text-purple-600 hover:text-purple-900 p-1"
                                                title="{{ $item->is_featured ? 'Remove from Featured' : 'Add to Featured' }}">
                                                <i class="fas fa-{{ $item->is_featured ? 'star-half-alt' : 'star' }}"></i>
                                            </button>
                                            <!-- Suspend -->
                                            <button wire:click="suspendItem({{ $item->id }})"
                                                onclick="confirm('Suspend this item?') || event.stopImmediatePropagation()"
                                                class="text-orange-600 hover:text-orange-900 p-1" title="Suspend">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        @endif

                                        @if($item->status === 'suspended')
                                            <!-- Reactivate -->
                                            <button wire:click="reactivateItem({{ $item->id }})"
                                                class="text-green-600 hover:text-green-900 p-1" title="Reactivate">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        @endif

                                        @if($item->status === 'rejected')
                                            <!-- Re-approve -->
                                            <button wire:click="approveItem({{ $item->id }})"
                                                class="text-green-600 hover:text-green-900 p-1" title="Approve">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif

                                        <!-- Edit/Modify -->
                                        <button wire:click="editItem({{ $item->id }})"
                                            class="text-indigo-600 hover:text-indigo-900 p-1" title="Edit Item">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <!-- Delete -->
                                        <button wire:click="deleteItem({{ $item->id }})"
                                            onclick="confirm('Permanently delete this item?') || event.stopImmediatePropagation()"
                                            class="text-red-600 hover:text-red-900 p-1" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-200">
                {{ $items->links() }}
            </div>
        </div>
    @else
        <div class="text-center py-12 bg-white rounded-lg border border-gray-200">
            <i class="fas fa-box text-gray-300 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No items found</h3>
            <p class="text-gray-500">
                @if($itemStatus)
                    No {{ strtolower($itemStatus) }} items found.
                @elseif($search)
                    No items match your search criteria.
                @else
                    Marketplace items will appear here.
                @endif
            </p>
        </div>
    @endif
</div>