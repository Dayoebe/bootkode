{{-- resources/views/livewire/marketplace/admin/partials/overview-tab.blade.php --}}
<div class="space-y-6">
    <!-- Recent Activity Summary -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Quick Actions Column -->
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

                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 cursor-pointer hover:bg-purple-100 transition-colors"
                    wire:click="setActiveTab('vendors')">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-purple-800">Pending Vendors</p>
                            <p class="text-xl font-semibold text-purple-900">
                                {{ $stats['pending_applications'] ?? 0 }}
                            </p>
                        </div>
                        <div class="bg-purple-100 p-2 rounded-full">
                            <i class="fas fa-user-clock text-purple-600"></i>
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
                        <span class="font-semibold text-green-600">{{ $stats['conversion_rate'] ?? 0 }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ min($stats['conversion_rate'] ?? 0, 100) }}%"></div>
                    </div>
                </div>

                <div class="space-y-3 mt-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Avg Order Value</span>
                        <span class="font-semibold">₦{{ number_format($stats['avg_order_value'] ?? 0, 0) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Active Sessions</span>
                        <span class="font-semibold">{{ $stats['active_sessions'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">This Week Orders</span>
                        <span class="font-semibold text-blue-600">{{ $stats['this_week_orders'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="space-y-4">
            <h3 class="text-lg font-medium text-gray-900">Recent Activity</h3>

            <div class="bg-white border border-gray-200 rounded-lg p-4">
                @if(isset($recentActivity) && $recentActivity->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentActivity->take(5) as $activity)
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0 w-2 h-2 bg-{{ $activity['color'] }}-400 rounded-full"></div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs text-gray-500">{{ $activity['time'] }}</p>
                                    <p class="text-sm text-gray-900">{{ $activity['message'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-history text-gray-300 text-2xl mb-2"></i>
                        <p class="text-sm text-gray-500">No recent activity</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Top Vendors This Month -->
    @if(isset($topVendors) && $topVendors->count() > 0)
        <div class="bg-white border border-gray-200 rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Top Performing Vendors This Month</h3>
            <div class="space-y-3">
                @foreach($topVendors as $index => $vendor)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                {{ $index + 1 }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $vendor->name }}</p>
                                <p class="text-sm text-gray-500">{{ $vendor->monthly_sales ?? 0 }} sales this month</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-gray-900">₦{{ number_format($vendor->monthly_earnings ?? 0, 0) }}</p>
                            <p class="text-sm text-green-600">{{ $vendor->marketplaceItems->count() }} items</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

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
                <div class="w-3 h-3 bg-green-500 rounded-full mx-auto mb-2"></div>
                <p class="text-sm font-medium text-gray-900">File Storage</p>
                <p class="text-xs text-gray-500">Operational</p>
            </div>
            <div class="text-center">
                <div class="w-3 h-3 bg-green-500 rounded-full mx-auto mb-2"></div>
                <p class="text-sm font-medium text-gray-900">Database</p>
                <p class="text-xs text-gray-500">Operational</p>
            </div>
        </div>
    </div>
</div>