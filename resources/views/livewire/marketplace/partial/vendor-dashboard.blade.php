
{{-- resources/views/livewire/marketplace/partial/vendor-dashboard.blade.php --}}
<div class="space-y-6">
    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-list-alt text-blue-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Listings</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_listings'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Published</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['published_listings'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-shopping-cart text-purple-600"></i>
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
                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-money-bill-wave text-yellow-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Revenue</p>
                    <p class="text-2xl font-semibold text-gray-900">₦{{ number_format($stats['total_revenue'], 0) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Chart -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Revenue Trend</h3>
            <select wire:model.live="dateRange" class="text-sm border border-gray-300 rounded-lg px-3 py-1 focus:ring-purple-500 focus:border-purple-500">
                <option value="7">Last 7 days</option>
                <option value="30">Last 30 days</option>
                <option value="90">Last 90 days</option>
            </select>
        </div>
        
        <!-- Simple chart representation -->
        <div class="h-64 flex items-end space-x-1">
            @foreach($chartData as $data)
                <div class="flex-1 bg-purple-100 hover:bg-purple-200 transition-colors rounded-t relative group" 
                     style="height: {{ $data['revenue'] > 0 ? max(($data['revenue'] / collect($chartData)->max('revenue')) * 100, 10) : 5 }}%">
                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                        {{ $data['formatted_date'] }}<br>
                        ₦{{ number_format($data['revenue']) }}
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-2 flex justify-between text-xs text-gray-500">
            <span>{{ collect($chartData)->first()['formatted_date'] }}</span>
            <span>{{ collect($chartData)->last()['formatted_date'] }}</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Orders -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Orders</h3>
            
            @if($recentOrders->count() > 0)
                <div class="space-y-4">
                    @foreach($recentOrders->take(5) as $order)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900">{{ $order->item->title }}</p>
                                <p class="text-sm text-gray-600">{{ $order->customer->name }}</p>
                                <p class="text-xs text-gray-500">{{ $order->created_at->format('M d, Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-medium text-gray-900">{{ $order->formatted_vendor_earning }}</p>
                                <span class="inline-block px-2 py-1 bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-800 text-xs font-medium rounded-full">
                                    {{ $order->status }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">No recent orders</p>
            @endif
        </div>

        <!-- Top Performing Items -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Top Performing Items</h3>
            
            @if($topItems->count() > 0)
                <div class="space-y-4">
                    @foreach($topItems as $item)
                        <div class="flex items-center space-x-3">
                            @if($item->getPrimaryImage())
                                <img src="{{ asset('storage/' . $item->getPrimaryImage()) }}" 
                                     alt="{{ $item->title }}" 
                                     class="w-12 h-12 object-cover rounded-lg">
                            @else
                                <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400"></i>
                                </div>
                            @endif
                            
                            <div class="flex-1">
                                <p class="font-medium text-gray-900 line-clamp-1">{{ $item->title }}</p>
                                <p class="text-sm text-gray-600">{{ $item->sales_count }} sales</p>
                            </div>
                            
                            <div class="text-right">
                                <p class="font-medium text-gray-900">{{ $item->getFormattedPrice() }}</p>
                                @if($item->average_rating > 0)
                                    <div class="flex items-center space-x-1">
                                        <i class="fas fa-star text-yellow-400 text-xs"></i>
                                        <span class="text-xs text-gray-600">{{ number_format($item->average_rating, 1) }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">No items yet</p>
            @endif
        </div>
    </div>
</div>