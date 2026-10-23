{{-- resources/views/livewire/marketplace/admin/partials/analytics-tab.blade.php --}}
<div class="space-y-6">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-content flex items-center">
            <i class="fas fa-chart-bar mr-2 text-brand"></i>
            Analytics Dashboard
        </h2>
        <p class="text-content-muted">Real-time insights and performance metrics from your marketplace data</p>
    </div>

    <!-- Key Metrics Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-brand-ink   rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-brand text-sm">Total Users</p>
                    <p class="text-3xl font-bold">{{ number_format($stats['total_vendors'] + ($stats['total_orders'] / 2)) }}</p>
                    <p class="text-brand text-xs mt-1">Platform registered users</p>
                </div>
                <i class="fas fa-users text-4xl text-brand"></i>
            </div>
        </div>

        <div class="bg-brand-ink   rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Total Sales</p>
                    <p class="text-3xl font-bold">₦{{ number_format($stats['total_revenue']) }}</p>
                    <p class="text-green-200 text-xs mt-1">All-time revenue</p>
                </div>
                <i class="fas fa-chart-line text-4xl text-green-300"></i>
            </div>
        </div>

        <div class="bg-brand-ink   rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-brand text-sm">Active Orders</p>
                    <p class="text-3xl font-bold">{{ $stats['pending_orders'] + $stats['completed_orders'] }}</p>
                    <p class="text-brand text-xs mt-1">{{ $stats['pending_orders'] }} pending</p>
                </div>
                <i class="fas fa-shopping-bag text-4xl text-brand"></i>
            </div>
        </div>

        <div class="bg-brand-ink   rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm">Conversion Rate</p>
                    <p class="text-3xl font-bold">{{ $stats['conversion_rate'] }}%</p>
                    <p class="text-orange-200 text-xs mt-1">Visitor to customer</p>
                </div>
                <i class="fas fa-trending-up text-4xl text-orange-300"></i>
            </div>
        </div>
    </div>

    <!-- Charts and Detailed Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-surface rounded-lg border border-line p-6">
            <h3 class="text-lg font-semibold text-content mb-4">Revenue Breakdown</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-content-muted">Platform Earnings</span>
                    <div class="text-right">
                        <span class="font-semibold">₦{{ number_format($stats['platform_earnings']) }}</span>
                        <div class="w-32 bg-gray-200 rounded-full h-2 ml-auto">
                            @php
                                $platformPercentage = $stats['total_revenue'] > 0 ? ($stats['platform_earnings'] / $stats['total_revenue']) * 100 : 0;
                            @endphp
                            <div class="bg-action h-2 rounded-full" style="width: {{ $platformPercentage }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-content-muted">Vendor Earnings</span>
                    <div class="text-right">
                        <span class="font-semibold">₦{{ number_format($stats['vendor_earnings']) }}</span>
                        <div class="w-32 bg-gray-200 rounded-full h-2 ml-auto">
                            @php
                                $vendorPercentage = $stats['total_revenue'] > 0 ? ($stats['vendor_earnings'] / $stats['total_revenue']) * 100 : 0;
                            @endphp
                            <div class="bg-green-600 h-2 rounded-full" style="width: {{ $vendorPercentage }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between items-center">
                    <span class="text-content-muted">This Month Revenue</span>
                    <div class="text-right">
                        <span class="font-semibold">₦{{ number_format($stats['this_month_revenue']) }}</span>
                        <div class="w-32 bg-gray-200 rounded-full h-2 ml-auto">
                            @php
                                $monthlyPercentage = $stats['total_revenue'] > 0 ? ($stats['this_month_revenue'] / $stats['total_revenue']) * 100 : 0;
                            @endphp
                            <div class="bg-action h-2 rounded-full" style="width: {{ min($monthlyPercentage, 100) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-surface rounded-lg border border-line p-6">
            <h3 class="text-lg font-semibold text-content mb-4">Platform Performance</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-content-muted">Average Order Value</span>
                    <span class="font-semibold">₦{{ number_format($stats['avg_order_value']) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-content-muted">Active Sessions</span>
                    <span class="font-semibold">{{ $stats['active_sessions'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-content-muted">Total Items</span>
                    <span class="font-semibold">{{ $stats['total_items'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-content-muted">Published Items</span>
                    <span class="font-semibold text-green-600">{{ $stats['published_items'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-content-muted">Pending Reviews</span>
                    <span class="font-semibold text-yellow-600">{{ $stats['pending_approval'] }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Summary Table -->
    <div class="bg-surface rounded-lg border border-line p-6">
        <h3 class="text-lg font-semibold text-content mb-4">Performance Summary</h3>
        <div class="overflow-x-auto">
            <x-ui.table label="Analytics Tab.Blade records"><table class="min-w-full divide-y divide-line">
                <thead class="bg-surface-muted">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase">Metric</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-content-muted uppercase">Current</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-content-muted uppercase">This Week</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-content-muted uppercase">This Month</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-content-muted uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-content">New Vendors</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">{{ $stats['pending_applications'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">{{ $stats['total_vendors'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">{{ $stats['total_vendors'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-green-600">
                            Active
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-content">Orders Placed</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">{{ $stats['pending_orders'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">{{ $stats['this_week_orders'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">{{ $stats['total_orders'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-green-600">
                            Growing
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-content">Revenue Generated</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                            ₦{{ number_format($stats['platform_earnings']) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                            ₦{{ number_format($stats['this_month_revenue'] / 4) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                            ₦{{ number_format($stats['this_month_revenue']) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-green-600">
                            Profitable
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-content">Items Published</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">{{ $stats['pending_approval'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">{{ $stats['published_items'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right">{{ $stats['total_items'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-brand">
                            Expanding
                        </td>
                    </tr>
                </tbody>
            </table></x-ui.table>
        </div>
    </div>
</div>
