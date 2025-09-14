{{-- resources/views/livewire/marketplace/partial/marketplace-admin.blade.php --}}
<div class="space-y-6">
    <!-- Admin Dashboard Header -->
    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-lg shadow-lg text-white p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between space-y-4 md:space-y-0">
            <div>
                <h2 class="text-2xl font-bold">Marketplace Administration</h2>
                <p class="text-purple-100">Manage vendors, orders, payments, and platform operations</p>
            </div>

            <div class="flex items-center space-x-4">
                <button wire:click="refreshStats"
                    class="inline-flex items-center px-4 py-2 bg-white/20 backdrop-blur-sm text-white rounded-lg hover:bg-white/30 transition-colors">
                    <i class="fas fa-sync-alt mr-2" wire:loading.class="animate-spin"></i>
                    Refresh Stats
                </button>

                <div class="text-right">
                    <div class="text-sm text-purple-100">Last updated</div>
                    <div class="text-white font-medium">{{ now()->format('M d, H:i') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Overview -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Revenue</p>
                    <p class="text-lg font-semibold text-green-600">
                        ₦{{ number_format($stats['total_revenue'] ?? 0, 0) }}</p>
                </div>
                <div class="bg-green-100 p-2 rounded-full">
                    <i class="fas fa-chart-line text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">This Month</p>
                    <p class="text-lg font-semibold text-blue-600">
                        ₦{{ number_format($stats['this_month_revenue'] ?? 0, 0) }}</p>
                </div>
                <div class="bg-blue-100 p-2 rounded-full">
                    <i class="fas fa-calendar text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Orders</p>
                    <p class="text-lg font-semibold text-purple-600">{{ number_format($stats['total_orders'] ?? 0) }}
                    </p>
                </div>
                <div class="bg-purple-100 p-2 rounded-full">
                    <i class="fas fa-shopping-cart text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Active Vendors</p>
                    <p class="text-lg font-semibold text-indigo-600">{{ number_format($stats['total_vendors'] ?? 0) }}
                    </p>
                </div>
                <div class="bg-indigo-100 p-2 rounded-full">
                    <i class="fas fa-store text-indigo-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Pending Payouts</p>
                    <p class="text-lg font-semibold text-yellow-600">
                        ₦{{ number_format($stats['pending_payouts'] ?? 0, 0) }}</p>
                </div>
                <div class="bg-yellow-100 p-2 rounded-full">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Platform Earnings</p>
                    <p class="text-lg font-semibold text-emerald-600">
                        ₦{{ number_format($stats['platform_earnings'] ?? 0, 0) }}</p>
                </div>
                <div class="bg-emerald-100 p-2 rounded-full">
                    <i class="fas fa-piggy-bank text-emerald-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="border-b border-gray-200">
            <nav class="flex space-x-1 px-6 overflow-x-auto">
                <button wire:click="setActiveTab('overview')"
                    class="py-4 px-3 border-b-2 font-medium text-sm whitespace-nowrap {{ $activeTab === 'overview' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    <i class="fas fa-tachometer-alt mr-2"></i>
                    Overview
                </button>

                <button wire:click="setActiveTab('vendors')"
                    class="py-4 px-3 border-b-2 font-medium text-sm whitespace-nowrap {{ $activeTab === 'vendors' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    <i class="fas fa-user-tie mr-2"></i>
                    Vendor Applications
                    @if(($stats['pending_applications'] ?? 0) > 0)
                        <span class="ml-2 bg-yellow-500 text-white text-xs px-2 py-1 rounded-full">
                            {{ $stats['pending_applications'] }}
                        </span>
                    @endif
                </button>

                <button wire:click="setActiveTab('items')"
                    class="py-4 px-3 border-b-2 font-medium text-sm whitespace-nowrap {{ $activeTab === 'items' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    <i class="fas fa-box mr-2"></i>
                    Item Management
                    @if(($stats['pending_approval'] ?? 0) > 0)
                        <span class="ml-2 bg-orange-500 text-white text-xs px-2 py-1 rounded-full">
                            {{ $stats['pending_approval'] }}
                        </span>
                    @endif
                </button>

                <button wire:click="setActiveTab('orders')"
                    class="py-4 px-3 border-b-2 font-medium text-sm whitespace-nowrap {{ $activeTab === 'orders' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    <i class="fas fa-clipboard-list mr-2"></i>
                    Order Management
                    @if(($stats['pending_orders'] ?? 0) > 0)
                        <span class="ml-2 bg-blue-500 text-white text-xs px-2 py-1 rounded-full">
                            {{ $stats['pending_orders'] }}
                        </span>
                    @endif
                </button>

                <button wire:click="setActiveTab('payments')"
                    class="py-4 px-3 border-b-2 font-medium text-sm whitespace-nowrap {{ $activeTab === 'payments' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    <i class="fas fa-credit-card mr-2"></i>
                    Payments & Payouts
                    @if(($stats['payout_requests'] ?? 0) > 0)
                        <span class="ml-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full">
                            {{ $stats['payout_requests'] }}
                        </span>
                    @endif
                </button>

                <button wire:click="setActiveTab('analytics')"
                    class="py-4 px-3 border-b-2 font-medium text-sm whitespace-nowrap {{ $activeTab === 'analytics' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                    <i class="fas fa-chart-bar mr-2"></i>
                    Analytics
                </button>
            </nav>
        </div>

        <div class="p-6">
            @if($activeTab === 'overview')
                <!-- Overview Dashboard -->
                <div class="space-y-6">
                    <!-- Recent Activity Summary -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Quick Stats Column -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>

                            <!-- Quick Action Cards -->
                            <div class="space-y-3">
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 cursor-pointer hover:bg-yellow-100 transition-colors"
                                    wire:click="setActiveTab('items')">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm text-yellow-800">Pending Items</p>
                                            <p class="text-xl font-semibold text-yellow-900">
                                                {{ $stats['pending_approval'] ?? 0 }}
                                            </p>
                                        </div>
                                        <div class="bg-yellow-100 p-2 rounded-full">
                                            <i class="fas fa-clock text-yellow-600"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 cursor-pointer hover:bg-blue-100 transition-colors"
                                    wire:click="setActiveTab('orders')">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm text-blue-800">New Orders</p>
                                            <p class="text-xl font-semibold text-blue-900">
                                                {{ $stats['pending_orders'] ?? 0 }}
                                            </p>
                                        </div>
                                        <div class="bg-blue-100 p-2 rounded-full">
                                            <i class="fas fa-shopping-bag text-blue-600"></i>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-green-50 border border-green-200 rounded-lg p-4 cursor-pointer hover:bg-green-100 transition-colors"
                                    wire:click="setActiveTab('payments')">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm text-green-800">Payout Requests</p>
                                            <p class="text-xl font-semibold text-green-900">
                                                {{ $stats['payout_requests'] ?? 0 }}
                                            </p>
                                        </div>
                                        <div class="bg-green-100 p-2 rounded-full">
                                            <i class="fas fa-money-bill-wave text-green-600"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Performance Metrics -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900">Performance</h3>

                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Conversion Rate</span>
                                        <span class="font-semibold text-green-600">12.5%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-600 h-2 rounded-full" style="width: 12.5%"></div>
                                    </div>
                                </div>

                                <div class="space-y-3 mt-4">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Avg Order Value</span>
                                        <span class="font-semibold">₦{{ number_format(25000, 0) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Active Sessions</span>
                                        <span class="font-semibold">{{ rand(45, 120) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Return Rate</span>
                                        <span class="font-semibold text-red-600">2.1%</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Activity -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-medium text-gray-900">Recent Activity</h3>

                            <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <div class="space-y-3">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0 w-2 h-2 bg-green-400 rounded-full"></div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs text-gray-500">5 minutes ago</p>
                                            <p class="text-sm text-gray-900">New order from John Doe</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0 w-2 h-2 bg-blue-400 rounded-full"></div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs text-gray-500">12 minutes ago</p>
                                            <p class="text-sm text-gray-900">Vendor approved: Sarah Wilson</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0 w-2 h-2 bg-yellow-400 rounded-full"></div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs text-gray-500">25 minutes ago</p>
                                            <p class="text-sm text-gray-900">Item pending review</p>
                                        </div>
                                    </div>

                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0 w-2 h-2 bg-purple-400 rounded-full"></div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs text-gray-500">1 hour ago</p>
                                            <p class="text-sm text-gray-900">Payment processed: ₦15,000</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- System Health Status -->
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">System Status</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="text-center">
                                <div class="w-3 h-3 bg-green-500 rounded-full mx-auto mb-2"></div>
                                <p class="text-sm font-medium text-gray-900">Payment Gateway</p>
                                <p class="text-xs text-gray-500">Operational</p>
                            </div>
                            <div class="text-center">
                                <div class="w-3 h-3 bg-green-500 rounded-full mx-auto mb-2"></div>
                                <p class="text-sm font-medium text-gray-900">Email Service</p>
                                <p class="text-xs text-gray-500">Operational</p>
                            </div>
                            <div class="text-center">
                                <div class="w-3 h-3 bg-yellow-500 rounded-full mx-auto mb-2"></div>
                                <p class="text-sm font-medium text-gray-900">File Storage</p>
                                <p class="text-xs text-gray-500">Degraded</p>
                            </div>
                            <div class="text-center">
                                <div class="w-3 h-3 bg-green-500 rounded-full mx-auto mb-2"></div>
                                <p class="text-sm font-medium text-gray-900">Database</p>
                                <p class="text-xs text-gray-500">Operational</p>
                            </div>
                        </div>
                    </div>
                </div>

            @elseif($activeTab === 'vendors')
                <!-- Vendor Applications -->
                <div class="space-y-4">
                    <!-- Filters -->
                    <div class="flex flex-wrap gap-3 items-center">
                        <div class="relative">
                            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search vendors..."
                                class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        </div>

                        <select wire:model.live="vendorStatus"
                            class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            <option value="all">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="approved">Approved</option>
                            <option value="rejected">Rejected</option>
                            <option value="suspended">Suspended</option>
                        </select>

                        <div class="ml-auto flex gap-2">
                            <button wire:click="exportVendors"
                                class="px-3 py-2 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700">
                                <i class="fas fa-download mr-2"></i>Export
                            </button>
                        </div>
                    </div>

                    <!-- Vendors Table -->
                    @if(isset($users) && $users->count() > 0)
                        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Vendor</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Role</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Items</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Sales</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Commission</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status</th>
                                            <th
                                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($users as $user)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        @if($user->profile_picture)
                                                            <div class="flex-shrink-0 h-10 w-10">
                                                                <img class="h-10 w-10 rounded-full object-cover"
                                                                    src="{{ asset('storage/' . $user->profile_picture) }}"
                                                                    alt="{{ $user->name }}">
                                                            </div>
                                                        @else
                                                            <div class="flex-shrink-0 h-10 w-10">
                                                                <div
                                                                    class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                                                                    <span
                                                                        class="text-purple-600 font-medium">{{ substr($user->name, 0, 2) }}</span>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        <div class="ml-4">
                                                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                                            <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $user->marketplaceItems->count() ?? 0 }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    ₦{{ number_format($user->vendorOrders->where('payment_status', 'paid')->sum('vendor_earning') ?? 0, 0) }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    @php
                                                        $metadata = $user->metadata ?? [];
                                                        $commission = $metadata['vendor_commission_rate'] ?? 80;
                                                    @endphp
                                                    {{ $commission }}%
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @php
                                                        $metadata = $user->metadata ?? [];
                                                        $isApproved = isset($metadata['vendor_approved']) && $metadata['vendor_approved'];
                                                        $isRejected = isset($metadata['vendor_rejected']) && $metadata['vendor_rejected'];
                                                        $isSuspended = isset($metadata['vendor_suspended']) && $metadata['vendor_suspended'];
                                                        $isPending = !$isApproved && !$isRejected && $user->role === 'student';
                                                    @endphp

                                                    @if($isSuspended)
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                            Suspended
                                                        </span>
                                                    @elseif($isApproved)
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            Approved
                                                        </span>
                                                    @elseif($isRejected)
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            Rejected
                                                        </span>
                                                    @elseif($isPending)
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                            Pending
                                                        </span>
                                                    @else
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                            Active
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <div class="flex items-center gap-2 justify-end">
                                                        <button wire:click="viewVendorDetails({{ $user->id }})"
                                                            class="text-blue-600 hover:text-blue-900" title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        @if($isPending)
                                                            <button wire:click="openApprovalModal({{ $user->id }})"
                                                                class="text-green-600 hover:text-green-900" title="Approve">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                            <button wire:click="openRejectionModal({{ $user->id }})"
                                                                class="text-red-600 hover:text-red-900" title="Reject">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        @elseif($isApproved && !$isSuspended)
                                                            <button wire:click="suspendVendor({{ $user->id }})"
                                                                onclick="confirm('Suspend this vendor?') || event.stopImmediatePropagation()"
                                                                class="text-orange-600 hover:text-orange-900" title="Suspend">
                                                                <i class="fas fa-pause"></i>
                                                            </button>
                                                        @elseif($isSuspended)
                                                            <button wire:click="reactivateVendor({{ $user->id }})"
                                                                onclick="confirm('Reactivate this vendor?') || event.stopImmediatePropagation()"
                                                                class="text-green-600 hover:text-green-900" title="Reactivate">
                                                                <i class="fas fa-play"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="px-6 py-4 border-t border-gray-200">
                                {{ $users->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-12 bg-white rounded-lg border border-gray-200">
                            <i class="fas fa-user-tie text-gray-300 text-6xl mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No vendors found</h3>
                            <p class="text-gray-500">Vendor applications will appear here.</p>
                        </div>
                    @endif
                </div>

            @elseif($activeTab === 'items')
                <!-- Item Management -->
                <div class="space-y-4">
                    <!-- Filters -->
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

                    <!-- Items Table -->
                    @if(isset($items) && $items->count() > 0)
                        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Item</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Vendor</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Type</th>
                                            <th
                                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Price</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Created</th>
                                            <th
                                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($items as $item)
                                            <tr class="hover:bg-gray-50">
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
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $item->vendor->name ?? 'Unknown' }}
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
                                                            Featured
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $item->created_at->format('M d, Y') }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <div class="flex items-center gap-2 justify-end">
                                                        <button wire:click="viewItem({{ $item->id }})"
                                                            class="text-blue-600 hover:text-blue-900" title="View">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        @if($item->status === 'pending')
                                                            <button wire:click="approveItem({{ $item->id }})"
                                                                class="text-green-600 hover:text-green-900" title="Approve">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                            <button wire:click="rejectItem({{ $item->id }})"
                                                                class="text-red-600 hover:text-red-900" title="Reject">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        @elseif($item->status === 'approved')
                                                            <button wire:click="toggleFeatureItem({{ $item->id }})"
                                                                class="text-purple-600 hover:text-purple-900"
                                                                title="{{ $item->is_featured ? 'Remove from Featured' : 'Add to Featured' }}">
                                                                <i
                                                                    class="fas fa-{{ $item->is_featured ? 'star-half-alt' : 'star' }}"></i>
                                                            </button>
                                                            <button wire:click="suspendItem({{ $item->id }})"
                                                                onclick="confirm('Suspend this item?') || event.stopImmediatePropagation()"
                                                                class="text-orange-600 hover:text-orange-900" title="Suspend">
                                                                <i class="fas fa-ban"></i>
                                                            </button>
                                                        @endif
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
                            <p class="text-gray-500">Marketplace items will appear here.</p>
                        </div>
                    @endif
                </div>

            @elseif($activeTab === 'orders')
                <!-- Orders Management -->
                <div class="space-y-4">
                    <!-- Filters -->
                    <div class="flex flex-wrap gap-3 items-center">
                        <div class="relative">
                            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search orders..."
                                class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        </div>

                        <select wire:model.live="orderStatus"
                            class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="processing">Processing</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>

                        <select wire:model.live="paymentStatus"
                            class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            <option value="">All Payments</option>
                            <option value="paid">Paid</option>
                            <option value="unpaid">Unpaid</option>
                            <option value="refunded">Refunded</option>
                        </select>

                        <input wire:model.live="dateFrom" type="date"
                            class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">

                        <input wire:model.live="dateTo" type="date"
                            class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <!-- Orders Table -->
                    @if(isset($orders) && $orders->count() > 0)
                        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Order</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Customer</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Item</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Vendor</th>
                                            <th
                                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Amount</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Payment</th>
                                            <th
                                                class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($orders as $order)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">{{ $order->order_number }}
                                                        </div>
                                                        <div class="text-sm text-gray-500">
                                                            {{ $order->created_at->format('M d, Y H:i') }}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-8 w-8">
                                                            <div
                                                                class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center text-sm font-medium text-gray-700">
                                                                {{ substr($order->customer->name ?? 'U', 0, 1) }}
                                                            </div>
                                                        </div>
                                                        <div class="ml-3">
                                                            <div class="text-sm font-medium text-gray-900">
                                                                {{ $order->customer->name ?? 'Unknown' }}
                                                            </div>
                                                            <div class="text-sm text-gray-500">{{ $order->customer->email ?? '' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ Str::limit($order->item->title ?? 'Deleted Item', 30) }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ ucfirst($order->item->type ?? 'unknown') }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $order->vendor->name ?? 'Unknown Vendor' }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                                    <div class="text-sm font-semibold text-gray-900">
                                                        ₦{{ number_format($order->total_amount, 2) }}</div>
                                                    <div class="text-xs text-gray-500">
                                                        ₦{{ number_format($order->vendor_earning, 2) }} vendor</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @php
                                                        $statusColors = [
                                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                                            'confirmed' => 'bg-blue-100 text-blue-800',
                                                            'processing' => 'bg-purple-100 text-purple-800',
                                                            'completed' => 'bg-green-100 text-green-800',
                                                            'cancelled' => 'bg-red-100 text-red-800',
                                                            'refunded' => 'bg-orange-100 text-orange-800',
                                                        ];
                                                    @endphp
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                        {{ ucfirst($order->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    @php
                                                        $paymentColors = [
                                                            'paid' => 'bg-green-100 text-green-800',
                                                            'unpaid' => 'bg-red-100 text-red-800',
                                                            'failed' => 'bg-red-100 text-red-800',
                                                            'refunded' => 'bg-orange-100 text-orange-800',
                                                        ];
                                                    @endphp
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $paymentColors[$order->payment_status] ?? 'bg-gray-100 text-gray-800' }}">
                                                        {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <div class="flex items-center gap-2 justify-end">
                                                        <button wire:click="viewOrder({{ $order->id }})"
                                                            class="text-purple-600 hover:text-purple-900" title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        @if($order->status === 'pending')
                                                            <button wire:click="confirmOrder({{ $order->id }})"
                                                                class="text-green-600 hover:text-green-900" title="Confirm">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        @endif
                                                        @if($order->status !== 'refunded' && $order->payment_status === 'paid')
                                                            <button wire:click="processRefund({{ $order->id }})"
                                                                onclick="confirm('Process refund for this order?') || event.stopImmediatePropagation()"
                                                                class="text-red-600 hover:text-red-900" title="Process Refund">
                                                                <i class="fas fa-undo"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="px-6 py-4 border-t border-gray-200">
                                {{ $orders->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-12 bg-white rounded-lg border border-gray-200">
                            <i class="fas fa-clipboard-list text-gray-300 text-6xl mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No orders found</h3>
                            <p class="text-gray-500">Orders will appear here as they are placed.</p>
                        </div>
                    @endif
                </div>

            @elseif($activeTab === 'payments')
                <!-- Payments & Payouts -->
                <div class="space-y-4">
                    <!-- Payments Header Actions -->
                    <div class="flex flex-wrap gap-3 items-center justify-between">
                        <div class="flex flex-wrap gap-3 items-center">
                            <div class="relative">
                                <input wire:model.live.debounce.300ms="transactionSearch" type="text"
                                    placeholder="Search transactions..."
                                    class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                            </div>

                            <select wire:model.live="transactionStatus"
                                class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                                <option value="all">All Transactions</option>
                                <option value="completed">Completed</option>
                                <option value="pending">Pending</option>
                                <option value="failed">Failed</option>
                            </select>

                            <input wire:model.live="transactionDateFrom" type="date"
                                class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                        </div>

                        <div class="flex gap-2">
                            <button wire:click="processAutomaticPayouts"
                                class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-play mr-2"></i>
                                Process Auto Payouts
                            </button>
                            <button wire:click="exportFinancialData"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-file-export mr-2"></i>
                                Export Data
                            </button>
                        </div>
                    </div>

                    <!-- Financial Overview Dashboard -->
                    @isset($paymentStats)
                        <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6">
                            <div class="p-4 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg text-white text-center">
                                <div class="text-2xl font-bold">₦{{ number_format($paymentStats['total_revenue'] ?? 0, 0) }}
                                </div>
                                <div class="text-xs opacity-90">Total Revenue</div>
                            </div>
                            <div class="p-4 bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg text-white text-center">
                                <div class="text-2xl font-bold">
                                    ₦{{ number_format($paymentStats['platform_commission'] ?? 0, 0) }}</div>
                                <div class="text-xs opacity-90">Platform Earnings</div>
                            </div>
                            <div class="p-4 bg-gradient-to-r from-green-500 to-green-600 rounded-lg text-white text-center">
                                <div class="text-2xl font-bold">₦{{ number_format($paymentStats['vendor_earnings'] ?? 0, 0) }}
                                </div>
                                <div class="text-xs opacity-90">Vendor Earnings</div>
                            </div>
                            <div class="p-4 bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg text-white text-center">
                                <div class="text-2xl font-bold">₦{{ number_format($paymentStats['pending_payouts'] ?? 0, 0) }}
                                </div>
                                <div class="text-xs opacity-90">Pending Payouts</div>
                            </div>
                            <div class="p-4 bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-lg text-white text-center">
                                <div class="text-2xl font-bold">₦{{ number_format($paymentStats['processed_payouts'] ?? 0, 0) }}
                                </div>
                                <div class="text-xs opacity-90">Paid Out</div>
                            </div>
                            <div class="p-4 bg-gradient-to-r from-pink-500 to-pink-600 rounded-lg text-white text-center">
                                <div class="text-2xl font-bold">
                                    ₦{{ number_format($paymentStats['this_month_revenue'] ?? 0, 0) }}</div>
                                <div class="text-xs opacity-90">This Month</div>
                            </div>
                        </div>
                    @endisset

                    <!-- Payment Sub-tabs -->
                    <div class="border-b border-gray-200">
                        <nav class="flex space-x-8">
                            <button wire:click="setPaymentTab('transactions')"
                                class="py-2 px-1 border-b-2 font-medium text-sm {{ $paymentTab === 'transactions' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                                Recent Transactions
                            </button>
                            <button wire:click="setPaymentTab('withdrawals')"
                                class="py-2 px-1 border-b-2 font-medium text-sm {{ $paymentTab === 'withdrawals' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                                Withdrawal Requests
                                @if(($paymentStats['pending_withdrawals'] ?? 0) > 0)
                                    <span class="ml-1 bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">
                                        {{ $paymentStats['pending_withdrawals'] }}
                                    </span>
                                @endif
                            </button>
                            <button wire:click="setPaymentTab('analytics')"
                                class="py-2 px-1 border-b-2 font-medium text-sm {{ $paymentTab === 'analytics' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
                                Financial Analytics
                            </button>
                        </nav>
                    </div>

                    <!-- Payment Content -->
                    <div class="mt-6">
                        @if($paymentTab === 'transactions')
                            <!-- Recent Transactions -->
                            @if(isset($transactions) && $transactions->count() > 0)
                                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                                    <div class="px-6 py-4 border-b border-gray-200">
                                        <h3 class="text-lg font-medium text-gray-900">Recent Transactions</h3>
                                    </div>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        User</th>
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Date</th>
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Type</th>
                                                    <th
                                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Amount</th>
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Status</th>
                                                    <th
                                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                        Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach($transactions as $transaction)
                                                    <tr class="hover:bg-gray-50">
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            <div class="flex items-center">
                                                                <div
                                                                    class="flex-shrink-0 h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                                                    <span class="text-xs font-medium text-gray-700">
                                                                        {{ strtoupper(substr($transaction->wallet->user->name ?? 'U', 0, 1)) }}
                                                                    </span>
                                                                </div>
                                                                <div class="ml-3">
                                                                    <div class="text-sm font-medium text-gray-900">
                                                                        {{ $transaction->wallet->user->name ?? 'Unknown' }}
                                                                    </div>
                                                                    <div class="text-xs text-gray-500">
                                                                        {{ $transaction->wallet->user->email ?? '' }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                            {{ $transaction->created_at->format('M d, Y H:i') }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                            {{ ucfirst(str_replace('_', ' ', $transaction->category)) }}
                                                        </td>
                                                        <td
                                                            class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-gray-900">
                                                            ₦{{ number_format($transaction->amount, 2) }}
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            @php
                                                                $status = strtolower($transaction->status ?? 'pending');
                                                                $badge = 'bg-yellow-100 text-yellow-800';
                                                                if ($status === 'completed')
                                                                    $badge = 'bg-green-100 text-green-800';
                                                                if ($status === 'failed')
                                                                    $badge = 'bg-red-100 text-red-800';
                                                            @endphp
                                                            <span
                                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge }}">
                                                                {{ ucfirst($status) }}
                                                            </span>
                                                        </td>
                                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                            <button wire:click="viewTransaction({{ $transaction->id }})"
                                                                class="text-purple-600 hover:text-purple-900 mr-3" title="View Details">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            @if(($transaction->status ?? 'pending') === 'pending')
                                                                <button wire:click="markTransactionCompleted({{ $transaction->id }})"
                                                                    class="text-green-600 hover:text-green-900" title="Mark Complete">
                                                                    <i class="fas fa-check"></i>
                                                                </button>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="px-6 py-4 border-t border-gray-200">
                                        {{ $transactions->links() }}
                                    </div>
                                </div>
                            @else
                                <div class="bg-white rounded-lg border border-gray-200 p-8 text-center">
                                    <i class="fas fa-credit-card text-gray-300 text-4xl mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No transactions found</h3>
                                    <p class="text-gray-500">Transaction data will appear here when available.</p>
                                </div>
                            @endif

                        @elseif($paymentTab === 'withdrawals')
                            <!-- Withdrawal Requests -->
                            @if(isset($withdrawals) && $withdrawals->count() > 0)
                                <div class="space-y-4">
                                    @foreach($withdrawals as $withdrawal)
                                        <div
                                            class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-md transition-all duration-200">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-4">
                                                    <div class="flex-shrink-0">
                                                        <div
                                                            class="w-12 h-12 bg-gradient-to-br from-green-400 to-blue-500 rounded-full flex items-center justify-center">
                                                            <span class="text-white font-medium">
                                                                {{ strtoupper(substr($withdrawal->user->name ?? 'V', 0, 2)) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="flex-1">
                                                        <h3 class="text-lg font-medium text-gray-900">
                                                            {{ $withdrawal->user->name ?? 'Vendor' }}
                                                        </h3>
                                                        <p class="text-sm text-gray-500">{{ $withdrawal->user->email ?? 'N/A' }}</p>
                                                        <div class="mt-1 flex items-center space-x-4 text-sm text-gray-600">
                                                            <span><i
                                                                    class="fas fa-calendar mr-1"></i>{{ $withdrawal->created_at->format('M d, Y') }}</span>
                                                            <span><i
                                                                    class="fas fa-wallet mr-1"></i>{{ ucfirst($withdrawal->method ?? 'bank') }}
                                                                Transfer</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="text-right">
                                                    <div class="text-2xl font-bold text-gray-900">
                                                        ₦{{ number_format($withdrawal->amount ?? 0, 0) }}</div>
                                                    <div class="mt-2 flex space-x-2">
                                                        <button onclick="alert('Withdrawal approval coming soon')"
                                                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors">
                                                            <i class="fas fa-check mr-2"></i>
                                                            Approve
                                                        </button>
                                                        <button onclick="alert('Withdrawal rejection coming soon')"
                                                            class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-colors">
                                                            <i class="fas fa-times mr-2"></i>
                                                            Decline
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                    <div class="mt-6">
                                        {{ $withdrawals->links() }}
                                    </div>
                                </div>
                            @else
                                <div class="bg-white rounded-lg border border-gray-200 p-8 text-center">
                                    <i class="fas fa-university text-gray-300 text-4xl mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No withdrawal requests</h3>
                                    <p class="text-gray-500">Vendor withdrawal requests will appear here when submitted.</p>
                                </div>
                            @endif

                        @elseif($paymentTab === 'analytics')
                            <!-- Financial Analytics Dashboard -->
                            <div class="space-y-6">
                                <!-- Revenue Trends Chart Placeholder -->
                                <div class="bg-white rounded-lg border border-gray-200 p-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Revenue Trends (Last 30 Days)</h3>
                                    <div
                                        class="h-64 bg-gradient-to-r from-blue-50 to-indigo-100 rounded-lg flex items-center justify-center">
                                        <div class="text-center">
                                            <i class="fas fa-chart-line text-6xl text-gray-300 mb-4"></i>
                                            <p class="text-gray-500">Revenue chart would be displayed here</p>
                                            <p class="text-sm text-gray-400">Integration with Chart.js or similar library needed
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Top Performers -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                                        <h3 class="text-lg font-medium text-gray-900 mb-4">Top Earning Vendors This Month</h3>
                                        <div class="space-y-3">
                                            @for($i = 1; $i <= 5; $i++)
                                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                                    <div class="flex items-center space-x-3">
                                                        <div
                                                            class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                                            {{ $i }}
                                                        </div>
                                                        <div>
                                                            <p class="font-medium text-gray-900">Sample Vendor {{ $i }}</p>
                                                            <p class="text-sm text-gray-500">{{ rand(15, 45) }} items sold</p>
                                                        </div>
                                                    </div>
                                                    <div class="text-right">
                                                        <p class="font-semibold text-gray-900">
                                                            ₦{{ number_format(rand(50000, 500000), 0) }}</p>
                                                        <p class="text-sm text-green-600">+{{ rand(10, 35) }}%</p>
                                                    </div>
                                                </div>
                                            @endfor
                                        </div>
                                    </div>

                                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                                        <h3 class="text-lg font-medium text-gray-900 mb-4">Platform Performance</h3>
                                        <div class="space-y-4">
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-600">Average Order Value</span>
                                                <span class="font-semibold">₦{{ number_format(rand(15000, 45000), 0) }}</span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-600">Conversion Rate</span>
                                                <span
                                                    class="font-semibold text-green-600">{{ rand(3, 8) }}.{{ rand(1, 9) }}%</span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-600">Active Vendors</span>
                                                <span class="font-semibold">{{ rand(25, 85) }}</span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-600">Pending Reviews</span>
                                                <span class="font-semibold text-yellow-600">{{ rand(5, 25) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

            @elseif($activeTab === 'analytics')
                <!-- Analytics Dashboard -->
                <div class="space-y-6">
                    <div class="mb-6">
                        <h2 class="text-xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-chart-bar mr-2 text-indigo-600"></i>
                            Advanced Analytics Dashboard
                        </h2>
                        <p class="text-gray-600">Comprehensive insights and performance metrics</p>
                    </div>

                    <!-- Key Metrics Overview -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-blue-100 text-sm">Total Users</p>
                                    <p class="text-3xl font-bold">{{ number_format(rand(500, 2000)) }}</p>
                                    <p class="text-blue-200 text-xs mt-1">+{{ rand(5, 15) }}% this month</p>
                                </div>
                                <i class="fas fa-users text-4xl text-blue-300"></i>
                            </div>
                        </div>

                        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-green-100 text-sm">Total Sales</p>
                                    <p class="text-3xl font-bold">₦{{ number_format(rand(1000000, 5000000)) }}</p>
                                    <p class="text-green-200 text-xs mt-1">+{{ rand(8, 25) }}% this month</p>
                                </div>
                                <i class="fas fa-chart-line text-4xl text-green-300"></i>
                            </div>
                        </div>

                        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-purple-100 text-sm">Active Orders</p>
                                    <p class="text-3xl font-bold">{{ rand(50, 200) }}</p>
                                    <p class="text-purple-200 text-xs mt-1">{{ rand(10, 30) }} pending</p>
                                </div>
                                <i class="fas fa-shopping-bag text-4xl text-purple-300"></i>
                            </div>
                        </div>

                        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-xl p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-orange-100 text-sm">Growth Rate</p>
                                    <p class="text-3xl font-bold">{{ rand(12, 35) }}%</p>
                                    <p class="text-orange-200 text-xs mt-1">Month over month</p>
                                </div>
                                <i class="fas fa-trending-up text-4xl text-orange-300"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Charts and Detailed Analytics -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Sales Performance</h3>
                            <div
                                class="h-64 bg-gradient-to-br from-blue-50 to-indigo-100 rounded-lg flex items-center justify-center">
                                <div class="text-center">
                                    <i class="fas fa-chart-area text-5xl text-gray-400 mb-3"></i>
                                    <p class="text-gray-600">Sales performance chart</p>
                                    <p class="text-xs text-gray-500">Chart.js integration needed</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">User Activity</h3>
                            <div
                                class="h-64 bg-gradient-to-br from-green-50 to-emerald-100 rounded-lg flex items-center justify-center">
                                <div class="text-center">
                                    <i class="fas fa-users text-5xl text-gray-400 mb-3"></i>
                                    <p class="text-gray-600">User activity heatmap</p>
                                    <p class="text-xs text-gray-500">Activity tracking needed</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Tables -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity Summary</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Metric
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Today
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">This
                                            Week</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">This
                                            Month</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Change
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">New Users</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">{{ rand(5, 25) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">{{ rand(35, 100) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">{{ rand(150, 400) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-green-600">
                                            +{{ rand(5, 20) }}%</td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Orders Placed</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">{{ rand(3, 15) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">{{ rand(25, 80) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">{{ rand(100, 300) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-green-600">
                                            +{{ rand(8, 18) }}%</td>
                                    </tr>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Revenue Generated</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                            ₦{{ number_format(rand(50000, 200000)) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                            ₦{{ number_format(rand(300000, 800000)) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                                            ₦{{ number_format(rand(1000000, 3000000)) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-green-600">
                                            +{{ rand(10, 25) }}%</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Enhanced Modals -->

    <!-- Vendor Approval Modal -->
    @if($showApprovalModal && $selectedUser)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-2xl transform transition-all">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-user-check mr-2 text-green-600"></i>
                        Approve Vendor
                    </h3>
                    <button wire:click="closeApprovalModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <div class="mb-6">
                    <div class="flex items-center space-x-4 mb-4">
                        @if($selectedUser->profile_picture)
                            <img src="{{ asset('storage/' . $selectedUser->profile_picture) }}" alt="{{ $selectedUser->name }}"
                                class="w-14 h-14 object-cover rounded-full ring-2 ring-green-200">
                        @else
                            <div
                                class="w-14 h-14 bg-gradient-to-br from-green-400 to-blue-500 rounded-full flex items-center justify-center ring-2 ring-green-200">
                                <span class="text-white font-bold text-lg">
                                    {{ strtoupper(substr($selectedUser->name, 0, 2)) }}
                                </span>
                            </div>
                        @endif
                        <div>
                            <p class="font-semibold text-gray-900 text-lg">{{ $selectedUser->name }}</p>
                            <p class="text-sm text-gray-500">{{ $selectedUser->email }}</p>
                        </div>
                    </div>

                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <p class="text-sm text-green-800">
                            <i class="fas fa-info-circle mr-2"></i>
                            This will grant {{ $selectedUser->name }} full vendor privileges to create and sell items on the
                            marketplace.
                        </p>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="commissionRate" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-percentage mr-1"></i>
                        Vendor Commission Rate (%)
                    </label>
                    <div class="relative">
                        <input wire:model="commissionRate" type="number" min="0" max="100" step="5" id="commissionRate"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                        <span class="absolute right-3 top-3 text-gray-400">%</span>
                    </div>
                    <div class="mt-2 flex items-center justify-between text-xs">
                        <span class="text-gray-500">Vendor gets {{ $commissionRate }}%</span>
                        <span class="text-gray-500">Platform gets {{ 100 - $commissionRate }}%</span>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <button wire:click="closeApprovalModal"
                        class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button wire:click="approveVendor"
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center">
                        <i class="fas fa-check mr-2"></i>
                        Approve Vendor
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Vendor Rejection Modal -->
    @if($showRejectionModal && $selectedUser)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl max-w-md w-full p-6 shadow-2xl transform transition-all">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-user-times mr-2 text-red-600"></i>
                        Reject Application
                    </h3>
                    <button wire:click="closeRejectionModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <div class="mb-6">
                    <div class="flex items-center space-x-4 mb-4">
                        @if($selectedUser->profile_picture)
                            <img src="{{ asset('storage/' . $selectedUser->profile_picture) }}" alt="{{ $selectedUser->name }}"
                                class="w-14 h-14 object-cover rounded-full ring-2 ring-red-200">
                        @else
                            <div
                                class="w-14 h-14 bg-gradient-to-br from-red-400 to-orange-500 rounded-full flex items-center justify-center ring-2 ring-red-200">
                                <span class="text-white font-bold text-lg">
                                    {{ strtoupper(substr($selectedUser->name, 0, 2)) }}
                                </span>
                            </div>
                        @endif
                        <div>
                            <p class="font-semibold text-gray-900 text-lg">{{ $selectedUser->name }}</p>
                            <p class="text-sm text-gray-500">{{ $selectedUser->email }}</p>
                        </div>
                    </div>

                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <p class="text-sm text-red-800">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            Please provide a clear reason for rejecting this vendor application. This message will be sent
                            to the applicant.
                        </p>
                    </div>
                </div>

                <div class="mb-6">
                    <label for="rejectionReason" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-comment mr-1"></i>
                        Rejection Reason *
                    </label>
                    <textarea wire:model="rejectionReason" id="rejectionReason" rows="4"
                        placeholder="Please explain why this application is being rejected..."
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 resize-none transition-colors"></textarea>
                    @error('rejectionReason')
                        <p class="text-red-600 text-sm mt-2 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="flex justify-end space-x-3">
                    <button wire:click="closeRejectionModal"
                        class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button wire:click="rejectVendor"
                        class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors flex items-center"
                        @if(empty($rejectionReason)) disabled
                        class="px-6 py-2 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed" @endif>
                        <i class="fas fa-times mr-2"></i>
                        Reject Application
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Order Details Modal -->
    @if($showOrderModal && $selectedOrder)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg max-w-2xl w-full p-6 max-h-screen overflow-y-auto">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Order Details</h3>
                    <button wire:click="closeOrderModal" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div class="space-y-6">
                    <!-- Order Information -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-2">Order Information</h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Order Number:</span>
                                <span class="font-medium">{{ $selectedOrder->order_number }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Date:</span>
                                <span class="font-medium">{{ $selectedOrder->created_at->format('M d, Y H:i') }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Status:</span>
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    {{ ucfirst($selectedOrder->status) }}
                                </span>
                            </div>
                            <div>
                                <span class="text-gray-500">Payment:</span>
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ ucfirst(str_replace('_', ' ', $selectedOrder->payment_status)) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Information -->
                    <div class="bg-blue-50 rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-2">Customer</h4>
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-blue-200 rounded-full flex items-center justify-center">
                                <span
                                    class="text-blue-800 font-medium">{{ substr($selectedOrder->customer->name ?? 'U', 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="font-medium">{{ $selectedOrder->customer->name ?? 'Unknown' }}</p>
                                <p class="text-sm text-gray-600">{{ $selectedOrder->customer->email ?? '' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Financial Breakdown -->
                    <div class="bg-green-50 rounded-lg p-4">
                        <h4 class="font-medium text-gray-900 mb-2">Financial Breakdown</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span>Total Amount:</span>
                                <span class="font-semibold">₦{{ number_format($selectedOrder->total_amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-purple-600">
                                <span>Platform Commission:</span>
                                <span
                                    class="font-medium">₦{{ number_format($selectedOrder->platform_commission, 2) }}</span>
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
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Close
                    </button>
                    @if($selectedOrder->status !== 'refunded' && $selectedOrder->payment_status === 'paid')
                        <button wire:click="processRefund({{ $selectedOrder->id }})"
                            onclick="return confirm('Are you sure you want to process a refund for this order?')"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-undo mr-2"></i>
                            Process Refund
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Loading State Overlay -->
    <div wire:loading class="fixed inset-0 bg-black bg-opacity-25 z-40 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 shadow-xl animate__animated animate__fadeIn mx-4">
            <div class="flex items-center space-x-4">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <span class="text-gray-700 font-medium">Processing admin action...</span>
            </div>
        </div>
    </div>
</div>