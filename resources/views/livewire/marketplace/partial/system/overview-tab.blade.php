{{-- resources/views/livewire/marketplace/partial/system/overview-tab.blade.php --}}
<div class="space-y-6">
    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Revenue Card -->
        <div class="bg-brand-ink   rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold">Total Revenue</h3>
                    <p class="text-3xl font-bold mt-2">₦{{ number_format($stats['total_revenue'], 0) }}</p>
                    <p class="text-brand text-sm mt-1">Platform Earnings: ₦{{ number_format($stats['platform_commission'], 0) }}</p>
                </div>
                <div class="bg-purple-400 p-3 rounded-full">
                    <i aria-hidden="true" class="fas fa-chart-line text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Orders Card -->
        <div class="bg-brand-ink   rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold">Total Orders</h3>
                    <p class="text-3xl font-bold mt-2">{{ number_format($stats['total_orders']) }}</p>
                    <p class="text-green-100 text-sm mt-1">Completed: {{ number_format($stats['completed_orders']) }}</p>
                </div>
                <div class="bg-green-400 p-3 rounded-full">
                    <i aria-hidden="true" class="fas fa-shopping-cart text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Vendors Card -->
        <div class="bg-brand-ink   rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold">Active Vendors</h3>
                    <p class="text-3xl font-bold mt-2">{{ number_format($stats['total_vendors']) }}</p>
                    <p class="text-brand text-sm mt-1">Vendor Earnings: ₦{{ number_format($stats['vendor_earnings'], 0) }}</p>
                </div>
                <div class="bg-blue-400 p-3 rounded-full">
                    <i aria-hidden="true" class="fas fa-users text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue Chart -->
        <div class="bg-surface rounded-lg border border-line p-6">
            <h3 class="text-lg font-semibold text-content mb-4">Revenue Trend (Last 30 Days)</h3>
            <div class="h-64 flex items-center justify-center text-content-muted">
                <div class="text-center">
                    <i aria-hidden="true" class="fas fa-chart-area text-4xl mb-2"></i>
                    <p>Chart visualization would go here</p>
                    <p class="text-sm">Integration with Chart.js or similar library needed</p>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-surface rounded-lg border border-line p-6">
            <h3 class="text-lg font-semibold text-content mb-4">Recent Activity</h3>
            <div class="space-y-4">
                @forelse($recentActivity as $activity)
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            @if($activity['type'] === 'item')
                                <div class="w-8 h-8 bg-brand/10 rounded-full flex items-center justify-center">
                                    <i aria-hidden="true" class="fas fa-box text-brand text-sm"></i>
                                </div>
                            @elseif($activity['type'] === 'order')
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <i aria-hidden="true" class="fas fa-shopping-cart text-green-600 text-sm"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-content">
                                <span class="font-medium">{{ $activity['user'] }}</span>
                                {{ $activity['action'] }}
                                <span class="font-medium">{{ Str::limit($activity['subject'], 30) }}</span>
                            </p>
                            <p class="text-xs text-content-muted">{{ $activity['time']->diffForHumans() }}</p>
                        </div>
                        <div class="flex-shrink-0">
                            @if(isset($activity['status']))
                                @php
                                    $statusColor = match($activity['status']) {
                                        'pending' => 'yellow',
                                        'approved' => 'green',
                                        'rejected' => 'red',
                                        default => 'gray'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800">
                                    {{ ucfirst($activity['status']) }}
                                </span>
                            @elseif(isset($activity['amount']))
                                <span class="text-sm font-medium text-content">₦{{ number_format($activity['amount'], 0) }}</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-content-muted">
                        <i aria-hidden="true" class="fas fa-history text-2xl mb-2"></i>
                        <p>No recent activity</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Top Performers -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Items -->
        <div class="bg-surface rounded-lg border border-line p-6">
            <h3 class="text-lg font-semibold text-content mb-4">Top Selling Items</h3>
            <div class="space-y-4">
                @foreach($topCategories as $index => $category)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="text-sm font-medium text-content-muted">#{{ $index + 1 }}</div>
                            <div>
                                <p class="text-sm font-medium text-content">{{ $category->category }}</p>
                                <p class="text-xs text-content-muted">{{ $category->count }} items</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-content">{{ $category->total_sales }} sales</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Top Vendors -->
        <div class="bg-surface rounded-lg border border-line p-6">
            <h3 class="text-lg font-semibold text-content mb-4">Top Vendors</h3>
            <div class="space-y-4">
                @foreach($topVendors as $index => $vendor)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="text-sm font-medium text-content-muted">#{{ $index + 1 }}</div>
                            <div>
                                <p class="text-sm font-medium text-content">{{ $vendor->name }}</p>
                                <p class="text-xs text-content-muted">{{ $vendor->total_listings }} listings</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-content">₦{{ number_format($vendor->total_earnings ?? 0, 0) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- System Status -->
    <div class="bg-surface rounded-lg border border-line p-6">
        <h3 class="text-lg font-semibold text-content mb-4">System Status</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                <span class="text-sm text-content-muted">Payment Gateway: Online</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                <span class="text-sm text-content-muted">File Storage: Active</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                <span class="text-sm text-content-muted">Email Service: Operational</span>
            </div>
            <div class="flex items-center space-x-2">
                <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                <span class="text-sm text-content-muted">Backup: Scheduled</span>
            </div>
        </div>
    </div>
</div>
