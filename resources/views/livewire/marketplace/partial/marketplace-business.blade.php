{{-- resources/views/livewire/marketplace/partial/marketplace-business.blade.php --}}
<div class="space-y-6">
    
    <!-- Internal Navigation Tabs -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex items-center justify-between">
            <nav class="flex space-x-4">
                <button wire:click="showDashboard" 
                        class="{{ $currentView === 'dashboard' ? 'bg-blue-100 text-blue-700 border-blue-200' : 'text-gray-600 hover:text-gray-900' }} px-4 py-2 rounded-lg border transition-colors flex items-center">
                    <i class="fas fa-chart-line mr-2"></i>
                    Dashboard
                </button>
                
                <button wire:click="showOrders" 
                        class="{{ $currentView === 'orders' ? 'bg-purple-100 text-purple-700 border-purple-200' : 'text-gray-600 hover:text-gray-900' }} px-4 py-2 rounded-lg border transition-colors flex items-center">
                    <i class="fas fa-box mr-2"></i>
                    Orders
                    @isset($orderStats)
                        @if($orderStats['pending_orders'] > 0)
                            <span class="ml-2 bg-purple-500 text-white text-xs px-2 py-1 rounded-full">
                                {{ $orderStats['pending_orders'] }}
                            </span>
                        @endif
                    @endisset
                </button>
                
                <button wire:click="showWithdrawals" 
                        class="{{ $currentView === 'withdrawals' ? 'bg-green-100 text-green-700 border-green-200' : 'text-gray-600 hover:text-gray-900' }} px-4 py-2 rounded-lg border transition-colors flex items-center">
                    <i class="fas fa-money-bill-wave mr-2"></i>
                    Withdrawals
                </button>
            </nav>
            
            <!-- Available Balance Display -->
            <div class="text-right">
                <div class="text-sm text-gray-600">Available Balance</div>
                <div class="text-lg font-bold text-green-600">₦{{ number_format($availableBalance, 2) }}</div>
            </div>
        </div>
    </div>

    <!-- Dashboard View -->
    @if($currentView === 'dashboard')
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-list-alt text-blue-600 text-lg"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Listings</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_listings'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $stats['published_listings'] }} published</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-shopping-cart text-purple-600 text-lg"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Orders</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_orders'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $stats['pending_orders'] }} pending</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-money-bill-wave text-green-600 text-lg"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Revenue</p>
                        <p class="text-2xl font-semibold text-gray-900">₦{{ number_format($stats['total_revenue'], 0) }}</p>
                        <p class="text-xs text-gray-500 mt-1">All time earnings</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i class="fas fa-calendar-month text-orange-600 text-lg"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">This Month</p>
                        <p class="text-2xl font-semibold text-gray-900">₦{{ number_format($stats['this_month_revenue'], 0) }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ now()->format('M Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Chart -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-medium text-gray-900 flex items-center">
                    <i class="fas fa-chart-area mr-2 text-blue-500"></i>
                    Revenue Trend
                </h3>
                <select wire:model.live="dateRange" class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-purple-500 focus:border-purple-500">
                    <option value="7">Last 7 days</option>
                    <option value="30">Last 30 days</option>
                    <option value="90">Last 90 days</option>
                </select>
            </div>
            
            <!-- Simple chart representation -->
            <div class="h-64 flex items-end justify-between space-x-1">
                @foreach($chartData as $data)
                    <div class="flex-1 bg-gradient-to-t from-purple-200 to-purple-100 hover:from-purple-300 hover:to-purple-200 transition-colors rounded-t relative group cursor-pointer" 
                         style="height: {{ $data['revenue'] > 0 ? max((collect($chartData)->max('revenue') > 0 ? ($data['revenue'] / collect($chartData)->max('revenue')) * 100 : 0), 5) : 5 }}%">
                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 bg-gray-800 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap z-10">
                            <div class="text-center">
                                <div class="font-medium">{{ $data['formatted_date'] }}</div>
                                <div class="text-green-400">₦{{ number_format($data['revenue']) }}</div>
                            </div>
                            <div class="absolute top-full left-1/2 transform -translate-x-1/2 border-4 border-transparent border-t-gray-800"></div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-4 flex justify-between text-xs text-gray-500 px-1">
                <span>{{ collect($chartData)->first()['formatted_date'] ?? '' }}</span>
                <span class="text-center">Revenue: ₦{{ number_format(collect($chartData)->sum('revenue')) }}</span>
                <span>{{ collect($chartData)->last()['formatted_date'] ?? '' }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Orders -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <i class="fas fa-clock mr-2 text-blue-500"></i>
                        Recent Orders
                    </h3>
                    <button wire:click="showOrders" class="text-purple-600 hover:text-purple-700 text-sm">
                        View All →
                    </button>
                </div>
                
                @if($recentOrders->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentOrders->take(5) as $order)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex items-center space-x-3">
                                    @if($order->item->getPrimaryImage())
                                        <img src="{{ asset('storage/' . $order->item->getPrimaryImage()) }}" 
                                             alt="{{ $order->item->title }}" 
                                             class="w-10 h-10 object-cover rounded">
                                    @else
                                        <div class="w-10 h-10 bg-gray-200 rounded flex items-center justify-center">
                                            <i class="fas fa-image text-gray-400 text-sm"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-gray-900 text-sm line-clamp-1">{{ $order->item->title }}</p>
                                        <p class="text-xs text-gray-600">{{ $order->customer->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $order->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-medium text-gray-900 text-sm">{{ $order->formatted_vendor_earning }}</p>
                                    <span class="inline-block px-2 py-0.5 bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-800 text-xs font-medium rounded-full">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-shopping-cart text-gray-300 text-3xl mb-2"></i>
                        <p class="text-gray-500 text-sm">No recent orders</p>
                    </div>
                @endif
            </div>

            <!-- Top Performing Items -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 flex items-center">
                        <i class="fas fa-trophy mr-2 text-yellow-500"></i>
                        Top Performing Items
                    </h3>
                    <a href="{{ route('marketplace.seller.listings') }}" class="text-purple-600 hover:text-purple-700 text-sm">
                        Manage →
                    </a>
                </div>
                
                @if($topItems->count() > 0)
                    <div class="space-y-3">
                        @foreach($topItems as $index => $item)
                            <div class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-50 transition-colors">
                                <div class="flex-shrink-0 text-sm font-medium text-gray-500 w-6">
                                    #{{ $index + 1 }}
                                </div>
                                @if($item->getPrimaryImage())
                                    <img src="{{ asset('storage/' . $item->getPrimaryImage()) }}" 
                                         alt="{{ $item->title }}" 
                                         class="w-12 h-12 object-cover rounded-lg">
                                @else
                                    <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                @endif
                                
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-gray-900 line-clamp-1 text-sm">{{ $item->title }}</p>
                                    <p class="text-xs text-gray-600">{{ $item->sales_count }} sales</p>
                                    @if($item->average_rating > 0)
                                        <div class="flex items-center space-x-1 mt-1">
                                            <i class="fas fa-star text-yellow-400 text-xs"></i>
                                            <span class="text-xs text-gray-600">{{ number_format($item->average_rating, 1) }}</span>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="text-right">
                                    <p class="font-medium text-gray-900 text-sm">{{ $item->getFormattedPrice() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-chart-bar text-gray-300 text-3xl mb-2"></i>
                        <p class="text-gray-500 text-sm">No items to display</p>
                    </div>
                @endif
            </div>
        </div>

    <!-- Orders View -->
    @elseif($currentView === 'orders')
        <!-- Header with Filters -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between space-y-4 md:space-y-0">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-box mr-2 text-purple-600"></i>
                        Customer Orders
                    </h2>
                    <p class="text-gray-600">Manage orders for your marketplace items</p>
                </div>

                <!-- Quick Stats -->
                <div class="flex items-center space-x-6 text-sm">
                    <div class="text-center">
                        <div class="text-lg font-semibold text-yellow-600">{{ $orderStats['pending_orders'] }}</div>
                        <div class="text-gray-500">Pending</div>
                    </div>
                    <div class="text-center">
                        <div class="text-lg font-semibold text-green-600">{{ $orderStats['completed_orders'] }}</div>
                        <div class="text-gray-500">Completed</div>
                    </div>
                    <div class="text-center">
                        <div class="text-lg font-semibold text-purple-600">₦{{ number_format($orderStats['total_earnings'], 0) }}</div>
                        <div class="text-gray-500">Total Earnings</div>
                    </div>
                </div>
            </div>

            <!-- Filters and Actions -->
            <div class="mt-4 flex flex-wrap gap-3 items-center justify-between">
                <div class="flex flex-wrap gap-3">
                    <div class="relative">
                        <input wire:model.live.debounce.300ms="orderSearch" type="text" placeholder="Search orders..."
                            class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        @if($orderSearch)
                            <button wire:click="$set('orderSearch', '')" class="absolute right-3 top-3 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    </div>

                    <select wire:model.live="orderStatus"
                        class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                        <option value="">All Status</option>
                        @foreach($orderStatuses as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    
                    @if($orderSearch || $orderStatus)
                        <button wire:click="$set('orderSearch', ''); $set('orderStatus', '')" 
                                class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            <i class="fas fa-times mr-1"></i>
                            Clear
                        </button>
                    @endif
                </div>

                @if($orders->count() > 0)
                    <button wire:click="exportOrders"
                        class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-download mr-2"></i>
                        Export CSV
                    </button>
                @endif
            </div>
        </div>

        <!-- Orders List -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            @if($orders->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($orders as $order)
                        <div class="p-6 hover:bg-gray-50 transition-colors">
                            <div class="flex items-start space-x-4">
                                <!-- Item Image -->
                                <div class="flex-shrink-0">
                                    @if($order->item->getPrimaryImage())
                                        <img src="{{ asset('storage/' . $order->item->getPrimaryImage()) }}"
                                            alt="{{ $order->item->title }}" class="w-16 h-16 object-cover rounded-lg">
                                    @else
                                        <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-image text-gray-400"></i>
                                        </div>
                                    @endif
                                </div>

                                <!-- Order Details -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h3 class="text-lg font-medium text-gray-900">
                                                {{ $order->item->title }}
                                            </h3>
                                            <p class="text-sm text-gray-600 mt-1">
                                                <i class="fas fa-hashtag mr-1"></i>
                                                Order #{{ $order->order_number }}
                                            </p>
                                            <div class="mt-2 space-y-1 text-sm text-gray-500">
                                                <p class="flex items-center">
                                                    <i class="fas fa-user mr-2 w-4"></i>
                                                    <span class="font-medium">Customer:</span> 
                                                    <span class="ml-1">{{ $order->customer->name }} ({{ $order->customer->email }})</span>
                                                </p>
                                                <p class="flex items-center">
                                                    <i class="fas fa-calendar mr-2 w-4"></i>
                                                    <span class="font-medium">Ordered:</span> 
                                                    <span class="ml-1">{{ $order->created_at->format('M d, Y \a\t g:i A') }}</span>
                                                </p>
                                                @if($order->isPaid())
                                                    <p class="flex items-center">
                                                        <i class="fas fa-check-circle mr-2 w-4 text-green-500"></i>
                                                        <span class="font-medium">Paid:</span> 
                                                        <span class="ml-1">{{ $order->paid_at->format('M d, Y \a\t g:i A') }}</span>
                                                    </p>
                                                @endif
                                                @if($order->isCompleted())
                                                    <p class="flex items-center">
                                                        <i class="fas fa-flag-checkered mr-2 w-4 text-blue-500"></i>
                                                        <span class="font-medium">Completed:</span> 
                                                        <span class="ml-1">{{ $order->completed_at->format('M d, Y \a\t g:i A') }}</span>
                                                    </p>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Order Value and Status -->
                                        <div class="text-right ml-4">
                                            <div class="space-y-2">
                                                <div>
                                                    <p class="text-lg font-semibold text-gray-900">{{ $order->formatted_total }}</p>
                                                    <p class="text-sm text-green-600 font-medium">Your earning: {{ $order->formatted_vendor_earning }}</p>
                                                </div>

                                                <div class="flex flex-col space-y-1">
                                                    <span class="inline-flex items-center px-2 py-1 bg-{{ $order->status_color }}-100 text-{{ $order->status_color }}-800 text-xs font-medium rounded-full text-center">
                                                        <i class="fas fa-{{ $order->status === 'completed' ? 'check' : ($order->status === 'pending' ? 'clock' : 'times') }} mr-1"></i>
                                                        {{ ucfirst($order->status) }}
                                                    </span>
                                                    <span class="inline-flex items-center px-2 py-1 bg-{{ $order->payment_status_color }}-100 text-{{ $order->payment_status_color }}-800 text-xs font-medium rounded-full text-center">
                                                        <i class="fas fa-{{ $order->payment_status === 'paid' ? 'check-circle' : 'clock' }} mr-1"></i>
                                                        {{ ucfirst(str_replace('_', ' ', $order->payment_status)) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Customer Notes -->
                                    @if($order->customer_notes)
                                        <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                            <p class="text-sm text-blue-900 flex items-start">
                                                <i class="fas fa-comment mr-2 mt-0.5 flex-shrink-0"></i>
                                                <span><span class="font-medium">Customer notes:</span> {{ $order->customer_notes }}</span>
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Vendor Notes -->
                                    @if($order->vendor_notes)
                                        <div class="mt-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                                            <p class="text-sm text-green-900 flex items-start">
                                                <i class="fas fa-sticky-note mr-2 mt-0.5 flex-shrink-0"></i>
                                                <span><span class="font-medium">Your notes:</span> {{ $order->vendor_notes }}</span>
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Order Actions -->
                                    <div class="mt-4 flex flex-wrap items-center gap-2">
                                        @if($order->isPaid() && !$order->isCompleted() && !$order->isCancelled())
                                            <button wire:click="fulfillOrder({{ $order->id }})"
                                                onclick="confirm('Are you sure you want to mark this order as fulfilled?') || event.stopImmediatePropagation()"
                                                class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors">
                                                <i class="fas fa-check mr-1"></i>
                                                Mark as Fulfilled
                                            </button>
                                        @endif

                                        @if($order->item->is_digital && $order->isPaid() && !$order->isCompleted())
                                            <button wire:click="provideDigitalAccess({{ $order->id }})"
                                                class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                                <i class="fas fa-download mr-1"></i>
                                                Provide Access
                                            </button>
                                        @endif

                                        <button wire:click="openNoteModal({{ $order->id }})"
                                            class="inline-flex items-center px-3 py-1.5 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700 transition-colors">
                                            <i class="fas fa-sticky-note mr-1"></i>
                                            {{ $order->vendor_notes ? 'Edit Notes' : 'Add Notes' }}
                                        </button>

                                        <a href="mailto:{{ $order->customer->email }}?subject=Regarding Order {{ $order->order_number }}"
                                            class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-envelope mr-1"></i>
                                            Contact Customer
                                        </a>

                                        <a href="{{ route('marketplace.item.public', $order->item->slug) }}" target="_blank"
                                            class="inline-flex items-center px-3 py-1.5 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-external-link-alt mr-1"></i>
                                            View Item
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $orders->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-16">
                    <i class="fas fa-shopping-bag text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-xl font-medium text-gray-900 mb-2">
                        @if($orderStatus || $orderSearch)
                            No matching orders found
                        @else
                            No orders yet
                        @endif
                    </h3>
                    <p class="text-gray-500 mb-8">
                        @if($orderStatus || $orderSearch)
                            No orders match your current filters.
                        @else
                            You haven't received any orders for your marketplace items yet.
                        @endif
                    </p>
                    @if($orderStatus || $orderSearch)
                        <button wire:click="$set('orderStatus', ''); $set('orderSearch', '')"
                            class="inline-flex items-center px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                            <i class="fas fa-times mr-2"></i>
                            Clear Filters
                        </button>
                    @else
                        <a href="{{ route('marketplace.browse') }}"
                            class="inline-flex items-center px-6 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                            <i class="fas fa-store mr-2"></i>
                            Browse Marketplace
                        </a>
                    @endif
                </div>
            @endif
        </div>

    <!-- Withdrawals View -->
    @else
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between space-y-4 md:space-y-0">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-money-bill-wave mr-2 text-green-600"></i>
                        Withdrawals
                    </h2>
                    <p class="text-gray-600">Manage your earnings withdrawals</p>
                </div>
                
                <div class="flex items-center space-x-4">
                    <!-- Stats -->
                    @isset($withdrawalStats)
                        <div class="flex items-center space-x-6 text-sm">
                            <div class="text-center">
                                <div class="text-lg font-semibold text-green-600">₦{{ number_format($withdrawalStats['total_withdrawn'], 0) }}</div>
                                <div class="text-gray-500">Total Withdrawn</div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-semibold text-orange-600">₦{{ number_format($withdrawalStats['pending_withdrawals'], 0) }}</div>
                                <div class="text-gray-500">Pending</div>
                            </div>
                        </div>
                    @endisset
                    
                    <button wire:click="openWithdrawalModal"
                        @if($availableBalance < 1000) disabled title="Minimum withdrawal amount is ₦1,000" @endif
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:bg-gray-300 disabled:cursor-not-allowed">
                        <i class="fas fa-plus mr-2"></i>
                        Request Withdrawal
                    </button>
                </div>
            </div>
            
            @if($availableBalance < 1000)
                <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-sm text-yellow-800 flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        Minimum withdrawal amount is ₦1,000. Your current available balance is ₦{{ number_format($availableBalance, 2) }}.
                    </p>
                </div>
            @endif
        </div>

        <!-- Withdrawals List -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            @isset($withdrawals)
                @if($withdrawals->count() > 0)
                    <div class="divide-y divide-gray-200">
                        @foreach($withdrawals as $withdrawal)
                            <div class="p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-money-bill-wave text-green-600"></i>
                                            </div>
                                            <div>
                                                <h3 class="font-medium text-gray-900">₦{{ number_format($withdrawal->amount, 2) }}</h3>
                                                <p class="text-sm text-gray-600">{{ $withdrawal->bank_name }} - {{ $withdrawal->account_number }}</p>
                                                <p class="text-xs text-gray-500">Requested {{ $withdrawal->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="text-right">
                                        <span class="inline-block px-3 py-1 bg-{{ $withdrawal->status === 'completed' ? 'green' : ($withdrawal->status === 'pending' ? 'yellow' : 'red') }}-100 text-{{ $withdrawal->status === 'completed' ? 'green' : ($withdrawal->status === 'pending' ? 'yellow' : 'red') }}-800 text-sm font-medium rounded-full">
                                            {{ ucfirst($withdrawal->status) }}
                                        </span>
                                        @if($withdrawal->completed_at)
                                            <p class="text-xs text-gray-500 mt-1">Completed {{ $withdrawal->completed_at->diffForHumans() }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                        {{ $withdrawals->links() }}
                    </div>
                @else
                    <div class="text-center py-16">
                        <i class="fas fa-money-bill-wave text-gray-300 text-6xl mb-4"></i>
                        <h3 class="text-xl font-medium text-gray-900 mb-2">No Withdrawals Yet</h3>
                        <p class="text-gray-500 mb-8">Your withdrawal history will appear here</p>
                        @if($availableBalance >= 1000)
                            <button wire:click="openWithdrawalModal"
                                class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-plus mr-2"></i>
                                Request Your First Withdrawal
                            </button>
                        @endif
                    </div>
                @endif
            @endisset
        </div>
    @endif

    <!-- Modals -->
    @include('livewire.marketplace.partial.business.business-modals')
</div>