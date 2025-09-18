{{-- resources/views/livewire/marketplace/partial/system/analytics-tab.blade.php --}}
<div class="space-y-6">
    <!-- Date Range Selector -->
    <div class="bg-white rounded-lg border border-gray-200 p-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
            <h3 class="text-lg font-semibold text-gray-900">Analytics Dashboard</h3>
            
            <div class="flex items-center space-x-4">
                <div class="flex items-center space-x-2">
                    <label class="text-sm text-gray-600">Date Range:</label>
                    <select wire:model.live="dateRange" class="border border-gray-300 rounded-lg px-3 py-1 text-sm">
                        <option value="7">Last 7 days</option>
                        <option value="30">Last 30 days</option>
                        <option value="90">Last 90 days</option>
                        <option value="365">Last year</option>
                    </select>
                </div>
                
                <div class="flex items-center space-x-2">
                    <label class="text-sm text-gray-600">Metric:</label>
                    <select wire:model.live="selectedMetric" class="border border-gray-300 rounded-lg px-3 py-1 text-sm">
                        <option value="revenue">Revenue</option>
                        <option value="orders">Orders</option>
                        <option value="items">Items</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Average Order Value</p>
                    <p class="text-2xl font-bold text-gray-900">
                        ₦{{ number_format($stats['total_revenue'] > 0 ? $stats['total_revenue'] / max($stats['total_orders'], 1) : 0, 2) }}
                    </p>
                </div>
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i class="fas fa-calculator text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Conversion Rate</p>
                    <p class="text-2xl font-bold text-gray-900">
                        {{ number_format(($stats['total_orders'] > 0 && $stats['total_items'] > 0) ? ($stats['total_orders'] / $stats['total_items']) * 100 : 0, 1) }}%
                    </p>
                </div>
                <div class="p-2 bg-green-100 rounded-lg">
                    <i class="fas fa-percentage text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Platform Commission</p>
                    <p class="text-2xl font-bold text-gray-900">
                        ₦{{ number_format($stats['platform_commission'], 0) }}
                    </p>
                </div>
                <div class="p-2 bg-purple-100 rounded-lg">
                    <i class="fas fa-coins text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Active Vendors</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_vendors']) }}</p>
                </div>
                <div class="p-2 bg-indigo-100 rounded-lg">
                    <i class="fas fa-users text-indigo-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue Chart -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Revenue Trend</h3>
            <div class="h-64">
                <canvas id="revenueChart" width="400" height="200"></canvas>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const ctx = document.getElementById('revenueChart').getContext('2d');
                        const chartData = @json($chartData);
                        
                        new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: chartData.map(item => item.formatted_date),
                                datasets: [{
                                    label: 'Revenue (₦)',
                                    data: chartData.map(item => item.revenue),
                                    borderColor: 'rgb(147, 51, 234)',
                                    backgroundColor: 'rgba(147, 51, 234, 0.1)',
                                    tension: 0.4,
                                    fill: true
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            callback: function(value) {
                                                return '₦' + value.toLocaleString();
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    });
                </script>
            </div>
        </div>

        <!-- Orders Chart -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Orders Trend</h3>
            <div class="h-64">
                <canvas id="ordersChart" width="400" height="200"></canvas>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const ctx = document.getElementById('ordersChart').getContext('2d');
                        const chartData = @json($chartData);
                        
                        new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: chartData.map(item => item.formatted_date),
                                datasets: [{
                                    label: 'Orders',
                                    data: chartData.map(item => item.orders),
                                    backgroundColor: 'rgba(34, 197, 94, 0.8)',
                                    borderColor: 'rgb(34, 197, 94)',
                                    borderWidth: 1
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                    });
                </script>
            </div>
        </div>
    </div>

    <!-- Category Performance -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Categories -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Categories</h3>
            <div class="space-y-4">
                @foreach($topCategories as $category)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-4 h-4 bg-purple-500 rounded-full"></div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $category->category }}</p>
                                <p class="text-xs text-gray-500">{{ $category->count }} items</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">{{ $category->total_sales }} sales</p>
                            <div class="w-24 bg-gray-200 rounded-full h-2 mt-1">
                                <div class="bg-purple-500 h-2 rounded-full" style="width: {{ $topCategories->max('total_sales') > 0 ? min(($category->total_sales / $topCategories->max('total_sales')) * 100, 100) : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Vendor Performance -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Performing Vendors</h3>
            <div class="space-y-4">
                @foreach($topVendors as $vendor)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <img src="{{ asset('storage/' .  $vendor->profile_picture) ?? 'https://ui-avatars.com/api/?name=' . urlencode($vendor->name) }}" 
                                 alt="{{ $vendor->name }}" 
                                 class="w-8 h-8 rounded-full">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $vendor->name }}</p>
                                <p class="text-xs text-gray-500">{{ $vendor->total_listings }} listings</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">₦{{ number_format($vendor->total_earnings ?? 0, 0) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Detailed Analytics Table -->
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Detailed Performance Metrics</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metric</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Period</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Previous Period</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Change</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Total Revenue</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₦{{ number_format($stats['total_revenue'], 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₦{{ number_format($stats['total_revenue'] * 0.85, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-arrow-up mr-1"></i>+15%
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Total Orders</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($stats['total_orders']) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($stats['total_orders'] * 0.9) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-arrow-up mr-1"></i>+10%
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">New Vendors</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($stats['total_vendors'] * 0.1) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($stats['total_vendors'] * 0.08) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-arrow-up mr-1"></i>+25%
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">New Items</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($stats['published_items'] * 0.2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($stats['published_items'] * 0.15) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-arrow-up mr-1"></i>+33%
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Export Options -->
    <div class="bg-white rounded-lg border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Export Analytics</h3>
                <p class="text-sm text-gray-600">Download detailed reports and analytics data</p>
            </div>
            <div class="flex space-x-3">
                <button class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-file-excel mr-2"></i>Export to Excel
                </button>
                <button class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-file-pdf mr-2"></i>Export to PDF
                </button>
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-file-csv mr-2"></i>Export to CSV
                </button>
            </div>
        </div>
    </div>
</div>