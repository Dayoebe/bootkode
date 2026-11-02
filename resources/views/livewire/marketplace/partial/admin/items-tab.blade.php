{{-- resources/views/livewire/marketplace/admin/partials/items-tab.blade.php --}}
<div class="space-y-4">
    <!-- Filters and Bulk Actions -->
    <div class="flex flex-wrap gap-3 items-center justify-between">
        <div class="flex flex-wrap gap-3 items-center">
            <div class="relative">
                <input aria-label="Search items" wire:model.live.debounce.300ms="search" type="text" placeholder="Search items..."
                    class="w-64 pl-10 pr-4 py-2 border border-line rounded-lg focus:ring-brand/30 focus:border-brand">
                <i aria-hidden="true" class="fas fa-search absolute left-3 top-3 text-content-muted"></i>
            </div>

            <select aria-label="Item status" wire:model.live="itemStatus"
                class="px-3 py-2 border border-line rounded-lg focus:ring-brand/30 focus:border-brand">
                <option value="">All Status</option>
                <option value="draft">Draft</option>
                <option value="pending">Pending Review</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
                <option value="suspended">Suspended</option>
            </select>

            <select aria-label="Item type" wire:model.live="itemType"
                class="px-3 py-2 border border-line rounded-lg focus:ring-brand/30 focus:border-brand">
                <option value="">All Types</option>
                <option value="course">Course</option>
                <option value="resource">Digital Resource</option>
                <option value="service">Service</option>
            </select>
        </div>

        <!-- Bulk Actions -->
        @if($selectedItems && count($selectedItems) > 0)
            <div class="flex gap-2 items-center">
                <span class="text-sm text-content-muted">{{ count($selectedItems) }} selected</span>
                <select aria-label="Bulk action" wire:model="bulkAction"
                    class="px-3 py-2 border border-line rounded-lg focus:ring-brand/30 focus:border-brand text-sm">
                    <option value="">Bulk Actions</option>
                    <option value="approve">Approve Selected</option>
                    <option value="reject">Reject Selected</option>
                    <option value="suspend">Suspend Selected</option>
                    <option value="feature">Feature Selected</option>
                    <option value="unfeature">Remove Feature</option>
                </select>
                <button wire:click="executeBulkAction"
                    class="px-4 py-2 bg-action text-action-foreground text-sm rounded-lg hover:bg-action transition-colors"
                    @if(!$bulkAction) disabled class="px-4 py-2 bg-gray-300 text-content-muted rounded-lg cursor-not-allowed" @endif>
                    Execute
                </button>
            </div>
        @endif
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
        <div class="bg-brand/10 border border-brand rounded-lg p-3 text-center">
            <div class="text-lg font-bold text-brand">{{ $stats['total_items'] ?? 0 }}</div>
            <div class="text-xs text-brand">Total Items</div>
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
        <div class="bg-brand/10 border border-brand rounded-lg p-3 text-center">
            @php
                $featuredCount = isset($items) ? $items->where('is_featured', true)->count() : 0;
            @endphp
            <div class="text-lg font-bold text-brand">{{ $featuredCount }}</div>
            <div class="text-xs text-brand">Featured</div>
        </div>
        <div class="bg-surface-muted border border-line rounded-lg p-3 text-center">
            @php
                $rejectedCount = isset($items) ? $items->where('status', 'rejected')->count() : 0;
            @endphp
            <div class="text-lg font-bold text-content-muted">{{ $rejectedCount }}</div>
            <div class="text-xs text-content-muted">Rejected</div>
        </div>
    </div>

    <!-- Items Table -->
    @if(isset($items) && $items->count() > 0)
        <div class="bg-surface border border-line rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <x-ui.table label="Items Tab records"><table class="min-w-full divide-y divide-line">
                    <thead class="bg-surface-muted">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left">
                                <input aria-label="Select all" type="checkbox" wire:model="selectAll" wire:click="toggleSelectAll"
                                    class="rounded border-line text-brand focus:ring-brand/30">
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">
                                Item</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">
                                Vendor</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">
                                Type</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-content-muted uppercase tracking-wider">
                                Price</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">
                                Sales</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">
                                Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">
                                Created</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-content-muted uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-surface divide-y divide-line">
                        @foreach($items as $item)
                            <tr class="hover:bg-surface-muted {{ in_array($item->id, $selectedItems) ? 'bg-blue-50' : '' }}">
                                <td class="px-6 py-4">
                                    <input aria-label="Selected items" type="checkbox" wire:model="selectedItems" value="{{ $item->id }}"
                                        class="rounded border-line text-brand focus:ring-brand/30">
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
                                                class="flex-shrink-0 h-12 w-12 bg-surface-muted rounded-lg flex items-center justify-center">
                                                <i aria-hidden="true" class="fas fa-image text-content-muted"></i>
                                            </div>
                                        @endif
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-content">
                                                {{ Str::limit($item->title, 40) }}
                                            </div>
                                            <div class="text-sm text-content-muted">
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
                                                <span class="text-xs font-medium text-content-muted">
                                                    {{ $item->vendor ? substr($item->vendor->name, 0, 1) : 'U' }}
                                                </span>
                                            </div>
                                        @endif
                                        <div class="text-sm text-content">{{ $item->vendor->name ?? 'Unknown' }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-content">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-surface-muted text-content">
                                        {{ ucfirst($item->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-content">
                                    <div>
                                        @if($item->discount_price && $item->discount_price < $item->price)
                                            <span class="font-semibold">₦{{ number_format($item->discount_price, 2) }}</span>
                                            <div class="text-xs text-content-muted line-through">
                                                ₦{{ number_format($item->price, 2) }}
                                            </div>
                                        @else
                                            <span class="font-semibold">₦{{ number_format($item->price, 2) }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-content">
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
                                            class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-brand/10 text-brand">
                                            <i aria-hidden="true" class="fas fa-star mr-1"></i>Featured
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-content-muted">
                                    {{ $item->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center gap-1 justify-end">
                                        <!-- View Item -->
                                        <button wire:click="viewItemDetails({{ $item->id }})"
                                            class="text-brand hover:text-brand p-1" title="View Details">
                                            <i aria-hidden="true" class="fas fa-eye"></i>
                                        </button>

                                        @if($item->status === 'pending')
                                            <!-- Approve -->
                                            <button wire:click="approveItem({{ $item->id }})"
                                                class="text-green-600 hover:text-green-900 p-1" title="Approve">
                                                <i aria-hidden="true" class="fas fa-check"></i>
                                            </button>
                                            <!-- Reject -->
                                            <button wire:click="rejectItem({{ $item->id }})"
                                                onclick="confirm('Reject this item?') || event.stopImmediatePropagation()"
                                                class="text-red-600 hover:text-red-900 p-1" title="Reject">
                                                <i aria-hidden="true" class="fas fa-times"></i>
                                            </button>
                                        @endif

                                        @if($item->status === 'approved')
                                            <!-- Feature/Unfeature -->
                                            <button wire:click="toggleFeatureItem({{ $item->id }})"
                                                class="text-brand hover:text-brand p-1"
                                                title="{{ $item->is_featured ? 'Remove from Featured' : 'Add to Featured' }}">
                                                <i aria-hidden="true" class="fas fa-{{ $item->is_featured ? 'star-half-alt' : 'star' }}"></i>
                                            </button>
                                            <!-- Suspend -->
                                            <button wire:click="suspendItem({{ $item->id }})"
                                                onclick="confirm('Suspend this item?') || event.stopImmediatePropagation()"
                                                class="text-orange-600 hover:text-orange-900 p-1" title="Suspend">
                                                <i aria-hidden="true" class="fas fa-ban"></i>
                                            </button>
                                        @endif

                                        @if($item->status === 'suspended')
                                            <!-- Reactivate -->
                                            <button wire:click="reactivateItem({{ $item->id }})"
                                                class="text-green-600 hover:text-green-900 p-1" title="Reactivate">
                                                <i aria-hidden="true" class="fas fa-play"></i>
                                            </button>
                                        @endif

                                        @if($item->status === 'rejected')
                                            <!-- Re-approve -->
                                            <button wire:click="approveItem({{ $item->id }})"
                                                class="text-green-600 hover:text-green-900 p-1" title="Approve">
                                                <i aria-hidden="true" class="fas fa-check"></i>
                                            </button>
                                        @endif

                                        <!-- Delete -->
                                        <button wire:click="deleteItem({{ $item->id }})"
                                            onclick="confirm('Permanently delete this item?') || event.stopImmediatePropagation()"
                                            class="text-red-600 hover:text-red-900 p-1" title="Delete">
                                            <i aria-hidden="true" class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table></x-ui.table>
            </div>

            <div class="px-6 py-4 border-t border-line">
                {{ $items->links() }}
            </div>
        </div>
    @else
        <div class="text-center py-12 bg-surface rounded-lg border border-line">
            <i aria-hidden="true" class="fas fa-box text-gray-300 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-content mb-2">No items found</h3>
            <p class="text-content-muted">
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

    @if($showItemDetailsModal && $selectedMarketplaceItem)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4 py-6" x-trap.inert.noscroll="true" role="dialog" aria-modal="true" aria-label="Item details modal" tabindex="-1" x-data="{}" @keydown.escape.stop="$wire.set(&#x27;showItemDetailsModal&#x27;, false)">
            <div class="w-full max-w-3xl overflow-hidden rounded-lg bg-surface shadow-sm">
                <div class="flex items-start justify-between border-b border-line px-6 py-4">
                    <div>
                        <h3 class="text-lg font-semibold text-content">{{ $selectedMarketplaceItem->title }}</h3>
                        <p class="mt-1 text-sm text-content-muted">
                            {{ $selectedMarketplaceItem->type_name }} by {{ $selectedMarketplaceItem->vendor?->name ?? 'Unknown vendor' }}
                        </p>
                    </div>
                    <button type="button" wire:click="closeItemDetails" class="text-content-muted hover:text-content-muted" aria-label="Close">
                        <i aria-hidden="true" class="fas fa-times"></i>
                    </button>
                </div>

                <div class="max-h-[70vh] overflow-y-auto px-6 py-5">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
                        <div class="rounded-lg border border-line p-4">
                            <p class="text-xs font-semibold uppercase text-content-muted">Status</p>
                            <p class="mt-1 font-semibold text-content">{{ $selectedMarketplaceItem->status_name }}</p>
                        </div>
                        <div class="rounded-lg border border-line p-4">
                            <p class="text-xs font-semibold uppercase text-content-muted">Price</p>
                            <p class="mt-1 font-semibold text-content">{{ $selectedMarketplaceItem->getFormattedPrice() }}</p>
                        </div>
                        <div class="rounded-lg border border-line p-4">
                            <p class="text-xs font-semibold uppercase text-content-muted">Orders</p>
                            <p class="mt-1 font-semibold text-content">{{ number_format($selectedMarketplaceItem->orders_count ?? 0) }}</p>
                        </div>
                        <div class="rounded-lg border border-line p-4">
                            <p class="text-xs font-semibold uppercase text-content-muted">Paid Revenue</p>
                            <p class="mt-1 font-semibold text-content">₦{{ number_format((float) ($selectedMarketplaceItem->paid_revenue ?? 0), 2) }}</p>
                        </div>
                    </div>

                    <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <p class="text-sm font-semibold text-content">Description</p>
                            <p class="mt-2 whitespace-pre-line text-sm leading-6 text-content-muted">{{ $selectedMarketplaceItem->description ?: $selectedMarketplaceItem->short_description ?: 'No description provided.' }}</p>
                        </div>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm font-semibold text-content">Approval</p>
                                <p class="mt-1 text-sm text-content-muted">
                                    {{ $selectedMarketplaceItem->approved_at ? $selectedMarketplaceItem->approved_at->format('M d, Y H:i') : 'Not approved yet' }}
                                    @if($selectedMarketplaceItem->approver)
                                        by {{ $selectedMarketplaceItem->approver->name }}
                                    @endif
                                </p>
                            </div>
                            @if($selectedMarketplaceItem->rejection_reason)
                                <div>
                                    <p class="text-sm font-semibold text-content">Review Note</p>
                                    <p class="mt-1 text-sm text-content-muted">{{ $selectedMarketplaceItem->rejection_reason }}</p>
                                </div>
                            @endif
                            <div>
                                <p class="text-sm font-semibold text-content">Tags</p>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @forelse((array) ($selectedMarketplaceItem->tags ?? []) as $tag)
                                        <span class="rounded-full bg-surface-muted px-2.5 py-1 text-xs font-medium text-content">{{ $tag }}</span>
                                    @empty
                                        <span class="text-sm text-content-muted">No tags</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end border-t border-line px-6 py-4">
                    <button type="button" wire:click="closeItemDetails" class="rounded-lg border border-line px-4 py-2 text-sm font-semibold text-content hover:bg-surface-muted">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
