{{-- resources/views/livewire/marketplace/partial/system/vendors-tab.blade.php --}}
<div class="space-y-6">
    <!-- Filters and Search -->
    <div class="bg-surface rounded-lg border border-line p-4">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <!-- Search -->
            <div class="flex-1 max-w-lg">
                <div class="relative">
                    <input aria-label="Search vendors by name or email" type="text"
                           wire:model.live.debounce.300ms="searchTerm"
                           placeholder="Search vendors by name or email..."
                           class="w-full pl-10 pr-4 py-2 border border-line rounded-lg focus:ring-brand/30 focus:border-brand">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-content-muted"></i>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="flex flex-wrap items-center gap-3">
                <select aria-label="Sort by" wire:model.live="sortBy" class="border border-line rounded-lg px-3 py-2 text-sm">
                    <option value="name">Sort by Name</option>
                    <option value="created_at">Sort by Join Date</option>
                    <option value="total_earnings">Sort by Earnings</option>
                    <option value="total_items">Sort by Items</option>
                </select>

                <button wire:click="$set('searchTerm', '')" class="px-3 py-2 text-content-muted hover:text-content">
                    <i class="fas fa-times"></i> Clear
                </button>
            </div>
        </div>
    </div>

    <!-- Vendor Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-surface rounded-lg border border-line p-6">
            <div class="flex items-center">
                <div class="p-2 bg-brand/10 rounded-lg">
                    <i class="fas fa-users text-brand"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-content-muted">Total Vendors</p>
                    <p class="text-2xl font-bold text-content">{{ $stats['total_vendors'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-surface rounded-lg border border-line p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <i class="fas fa-user-check text-green-600"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-content-muted">Active Vendors</p>
                    <p class="text-2xl font-bold text-content">{{ $vendors->where('is_active', true)->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-surface rounded-lg border border-line p-6">
            <div class="flex items-center">
                <div class="p-2 bg-brand/10 rounded-lg">
                    <i class="fas fa-box text-brand"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-content-muted">Total Items</p>
                    <p class="text-2xl font-bold text-content">{{ $stats['published_items'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-surface rounded-lg border border-line p-6">
            <div class="flex items-center">
                <div class="p-2 bg-brand/10 rounded-lg">
                    <i class="fas fa-dollar-sign text-brand"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-content-muted">Total Earnings</p>
                    <p class="text-2xl font-bold text-content">₦{{ number_format($stats['vendor_earnings'], 0) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Vendors Table -->
    <div class="bg-surface rounded-lg border border-line overflow-hidden">
        <div class="px-6 py-4 border-b border-line">
            <h3 class="text-lg font-semibold text-content">Vendor Management</h3>
        </div>

        <div class="overflow-x-auto">
            <x-ui.table label="Vendors Tab.Blade records"><table class="min-w-full divide-y divide-line">
                <thead class="bg-surface-muted">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Vendor</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Contact</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Items</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Earnings</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Orders</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Joined</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-surface divide-y divide-line">
                    @forelse($vendors as $vendor)
                        @php
                            $vendorStats = $vendor->getVendorOrderStats();
                        @endphp
                        <tr class="hover:bg-surface-muted">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full"
                                             src="{{asset('storage/' .   $vendor->profile_picture) ?? 'https://ui-avatars.com/api/?name=' . urlencode($vendor->name) }}"
                                             alt="{{ $vendor->name }}">
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-content">{{ $vendor->name }}</div>
                                        <div class="text-sm text-content-muted">{{ $vendor->role }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-content">{{ $vendor->email }}</div>
                                <div class="text-sm text-content-muted">{{ $vendor->phone_number ?? 'No phone' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-content">
                                <div class="flex flex-col space-y-1">
                                    <span class="font-medium">{{ $vendor->marketplaceItems->count() }} total</span>
                                    <span class="text-xs text-green-600">{{ $vendor->marketplaceItems->where('status', 'approved')->count() }} published</span>
                                    @if($vendor->marketplaceItems->where('status', 'pending')->count() > 0)
                                        <span class="text-xs text-yellow-600">{{ $vendor->marketplaceItems->where('status', 'pending')->count() }} pending</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-content">
                                <div class="font-medium">₦{{ number_format($vendorStats['total_earnings'], 0) }}</div>
                                <div class="text-xs text-content-muted">All time</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-content">
                                <div class="flex flex-col space-y-1">
                                    <span class="font-medium">{{ $vendorStats['total_orders'] }} total</span>
                                    <span class="text-xs text-green-600">{{ $vendorStats['completed_orders'] }} completed</span>
                                    @if($vendorStats['pending_orders'] > 0)
                                        <span class="text-xs text-yellow-600">{{ $vendorStats['pending_orders'] }} pending</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $vendor->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $vendor->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-content-muted">
                                <div>{{ $vendor->created_at->format('M d, Y') }}</div>
                                <div class="text-xs">{{ $vendor->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <button wire:click="viewVendor({{ $vendor->id }})"
                                            class="text-brand hover:text-brand"
                                            title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    @if($vendor->is_active)
                                        <button wire:click="deactivateVendor({{ $vendor->id }})"
                                                class="text-red-600 hover:text-red-900"
                                                title="Deactivate Vendor">
                                            <i class="fas fa-user-slash"></i>
                                        </button>
                                    @else
                                        <button wire:click="activateVendor({{ $vendor->id }})"
                                                class="text-green-600 hover:text-green-900"
                                                title="Activate Vendor">
                                            <i class="fas fa-user-check"></i>
                                        </button>
                                    @endif

                                    <div class="relative" x-data="{ open: false }">
                                        <button @click="open = !open" class="text-content-muted hover:text-content-muted" aria-label="More actions">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div x-show="open" @click.away="open = false"
                                             class="absolute right-0 mt-2 w-48 bg-surface rounded-md shadow-sm z-10 border border-line">
                                            <div class="py-1">
                                                <a href="#" class="block px-4 py-2 text-sm text-content hover:bg-surface-muted">
                                                    <i class="fas fa-chart-bar mr-2"></i>View Analytics
                                                </a>
                                                <a href="#" class="block px-4 py-2 text-sm text-content hover:bg-surface-muted">
                                                    <i class="fas fa-envelope mr-2"></i>Send Email
                                                </a>
                                                <a href="#" class="block px-4 py-2 text-sm text-content hover:bg-surface-muted">
                                                    <i class="fas fa-history mr-2"></i>View History
                                                </a>
                                                <a href="#" class="block px-4 py-2 text-sm text-content hover:bg-surface-muted">
                                                    <i class="fas fa-money-bill mr-2"></i>Payout History
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center">
                                <div class="text-content-muted">
                                    <i class="fas fa-users text-4xl mb-4"></i>
                                    <p class="text-lg font-medium">No vendors found</p>
                                    <p class="text-sm">Try adjusting your search criteria</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table></x-ui.table>
        </div>

        <!-- Pagination -->
        @if(method_exists($vendors, 'hasPages') && $vendors->hasPages())
            <div class="px-6 py-4 border-t border-line">
                {{ $vendors->links() }}
            </div>
        @endif
    </div>

    <!-- Top Performers -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-surface rounded-lg border border-line p-6">
            <h3 class="text-lg font-semibold text-content mb-4">Top Earning Vendors</h3>
            <div class="space-y-4">
                @foreach($topVendors->take(5) as $index => $vendor)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="text-sm font-medium text-content-muted">#{{ $index + 1 }}</div>
                            <img class="h-8 w-8 rounded-full"
                                 src="{{asset('storage/' .   $vendor->profile_picture) ?? 'https://ui-avatars.com/api/?name=' . urlencode($vendor->name) }}"
                                 alt="{{ $vendor->name }}">
                            <div>
                                <p class="text-sm font-medium text-content">{{ $vendor->name }}</p>
                                <p class="text-xs text-content-muted">{{ $vendor->total_listings }} items</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-content">₦{{ number_format($vendor->total_earnings ?? 0, 0) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-surface rounded-lg border border-line p-6">
            <h3 class="text-lg font-semibold text-content mb-4">Most Productive Vendors</h3>
            <div class="space-y-4">
                @foreach($vendors->sortByDesc(function($vendor) { return $vendor->marketplaceItems->count(); })->take(5) as $index => $vendor)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="text-sm font-medium text-content-muted">#{{ $index + 1 }}</div>
                            <img class="h-8 w-8 rounded-full"
                                 src="{{asset('storage/' .   $vendor->profile_picture) ?? 'https://ui-avatars.com/api/?name=' . urlencode($vendor->name) }}"
                                 alt="{{ $vendor->name }}">
                            <div>
                                <p class="text-sm font-medium text-content">{{ $vendor->name }}</p>
                                <p class="text-xs text-content-muted">{{ $vendor->created_at->format('M Y') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-content">{{ $vendor->marketplaceItems->count() }} items</p>
                            <p class="text-xs text-content-muted">{{ $vendor->marketplaceItems->where('status', 'approved')->count() }} published</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
