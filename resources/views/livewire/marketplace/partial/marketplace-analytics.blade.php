
{{-- resources/views/livewire/marketplace/partial/marketplace-analytics.blade.php --}}
<div class="space-y-6">
    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-store text-purple-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Items</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_items'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-shopping-cart text-green-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Orders</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_orders'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-money-bill-wave text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Revenue</p>
                    <p class="text-2xl font-semibold text-gray-900">₦{{ number_format($stats['total_revenue'], 0) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-yellow-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Active Vendors</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_vendors'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue Trend Chart -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Revenue Trend</h3>
                <select wire:model.live="dateRange" class="text-sm border border-gray-300 rounded px-3 py-1">
                    <option value="7">Last 7 days</option>
                    <option value="30">Last 30 days</option>
                    <option value="90">Last 90 days</option>
                </select>
            </div>
            
            <div class="h-64 flex items-end space-x-1">
                @foreach($chartData as $data)
                    <div class="flex-1 bg-purple-100 hover:bg-purple-200 transition-colors rounded-t relative group" 
                         style="height: {{ $data['revenue'] > 0 ? max(($data['revenue'] / collect($chartData)->max('revenue')) * 100, 5) : 5 }}%">
                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                            {{ $data['formatted_date'] }}<br>
                            ₦{{ number_format($data['revenue']) }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Top Categories -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Top Categories</h3>
            
            @if($topCategories->count() > 0)
                <div class="space-y-3">
                    @foreach($topCategories->take(5) as $category)
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-medium text-gray-900">{{ ucfirst(str_replace('-', ' ', $category->category)) }}</p>
                                <p class="text-sm text-gray-500">{{ $category->count }} items</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-purple-600">{{ $category->total_sales }}</p>
                                <p class="text-xs text-gray-500">sales</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">No category data available</p>
            @endif
        </div>
    </div>

    <!-- Top Vendors Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Top Performing Vendors</h3>
        
        @if($topVendors->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Listings</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Earnings</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($topVendors->take(10) as $vendor)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                <span class="text-sm font-medium text-gray-700">
                                                    {{ substr($vendor->name, 0, 2) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $vendor->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $vendor->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $vendor->total_listings }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ₦{{ number_format($vendor->total_earnings ?? 0, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('marketplace.vendor.public', $vendor) }}" 
                                       class="text-purple-600 hover:text-purple-900">View Profile</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 text-center py-8">No vendor data available</p>
        @endif
    </div>
</div>