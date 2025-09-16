{{-- resources/views/livewire/marketplace/vendor/partials/analytics-dashboard.blade.php --}}

<!-- Analytics Overview Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <!-- Revenue Card -->
    <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm font-medium">Total Revenue</p>
                <p class="text-3xl font-bold">₦{{ number_format($analyticsData['total_revenue'] ?? 0, 0) }}</p>
                <p class="text-green-200 text-sm mt-1">All-time earnings</p>
            </div>
            <div class="bg-green-400/30 p-3 rounded-full">
                <i class="fas fa-money-bill-wave text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Sales Card -->
    <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm font-medium">Total Sales</p>
                <p class="text-3xl font-bold">{{ $analyticsData['total_sales'] ?? 0 }}</p>
                <p class="text-blue-200 text-sm mt-1">Items sold</p>
            </div>
            <div class="bg-blue-400/30 p-3 rounded-full">
                <i class="fas fa-shopping-cart text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Items Card -->
    <div class="bg-gradient-to-br from-purple-500 to-violet-600 rounded-xl p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-purple-100 text-sm font-medium">Published Items</p>
                <p class="text-3xl font-bold">{{ $analyticsData['published_items'] ?? 0 }}</p>
                <p class="text-purple-200 text-sm mt-1">of {{ $analyticsData['total_items'] ?? 0 }} total</p>
            </div>
            <div class="bg-purple-400/30 p-3 rounded-full">
                <i class="fas fa-box text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Rating Card -->
    <div class="bg-gradient-to-br from-yellow-500 to-orange-600 rounded-xl p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-yellow-100 text-sm font-medium">Average Rating</p>
                <p class="text-3xl font-bold">{{ number_format($analyticsData['avg_rating'] ?? 0, 1) }}</p>
                <p class="text-yellow-200 text-sm mt-1">Customer satisfaction</p>
            </div>
            <div class="bg-yellow-400/30 p-3 rounded-full">
                <i class="fas fa-star text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Performance -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- This Month Stats -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-calendar-alt mr-2 text-blue-600"></i>
            This Month Performance
        </h3>
        
        <div class="space-y-4">
            <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                <div class="flex items-center">
                    <div class="bg-green-500 p-2 rounded-full mr-3">
                        <i class="fas fa-dollar-sign text-white"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Monthly Revenue</p>
                        <p class="text-xl font-bold text-gray-900">₦{{ number_format($analyticsData['this_month_revenue'] ?? 0, 0) }}</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg">
                <div class="flex items-center">
                    <div class="bg-blue-500 p-2 rounded-full mr-3">
                        <i class="fas fa-shopping-bag text-white"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Monthly Sales</p>
                        <p class="text-xl font-bold text-gray-900">{{ $analyticsData['this_month_sales'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between p-4 bg-purple-50 rounded-lg">
                <div class="flex items-center">
                    <div class="bg-purple-500 p-2 rounded-full mr-3">
                        <i class="fas fa-eye text-white"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Views</p>
                        <p class="text-xl font-bold text-gray-900">{{ number_format($analyticsData['total_views'] ?? 0) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Item Status Breakdown -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-chart-pie mr-2 text-purple-600"></i>
            Item Status Breakdown
        </h3>
        
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-green-500 rounded-full mr-3"></div>
                    <span class="text-sm font-medium text-gray-700">Published</span>
                </div>
                <span class="font-bold text-gray-900">{{ $analyticsData['published_items'] ?? 0 }}</span>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-yellow-500 rounded-full mr-3"></div>
                    <span class="text-sm font-medium text-gray-700">Drafts</span>
                </div>
                <span class="font-bold text-gray-900">{{ $analyticsData['draft_items'] ?? 0 }}</span>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-blue-500 rounded-full mr-3"></div>
                    <span class="text-sm font-medium text-gray-700">Pending Review</span>
                </div>
                <span class="font-bold text-gray-900">{{ $analyticsData['pending_items'] ?? 0 }}</span>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-red-500 rounded-full mr-3"></div>
                    <span class="text-sm font-medium text-gray-700">Rejected</span>
                </div>
                <span class="font-bold text-gray-900">{{ $analyticsData['rejected_items'] ?? 0 }}</span>
            </div>
        </div>

        <!-- Progress Bars -->
        <div class="mt-6">
            @php
                $totalItems = $analyticsData['total_items'] ?? 1;
                $publishedPercent = ($analyticsData['published_items'] ?? 0) / $totalItems * 100;
                $draftPercent = ($analyticsData['draft_items'] ?? 0) / $totalItems * 100;
                $pendingPercent = ($analyticsData['pending_items'] ?? 0) / $totalItems * 100;
            @endphp
            
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="flex h-full rounded-full overflow-hidden">
                    <div class="bg-green-500" style="width: {{ $publishedPercent }}%"></div>
                    <div class="bg-yellow-500" style="width: {{ $draftPercent }}%"></div>
                    <div class="bg-blue-500" style="width: {{ $pendingPercent }}%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Performance Insights -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
        <i class="fas fa-lightbulb mr-2 text-yellow-500"></i>
        Performance Insights & Tips
    </h3>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Completion Rate -->
        <div class="p-4 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg border border-blue-200">
            <div class="flex items-center mb-3">
                <i class="fas fa-tasks text-blue-600 mr-2"></i>
                <h4 class="font-semibold text-gray-900">Completion Rate</h4>
            </div>
            <div class="text-2xl font-bold text-blue-600 mb-2">
                {{ $totalItems > 0 ? round(($analyticsData['published_items'] ?? 0) / $totalItems * 100) : 0 }}%
            </div>
            <p class="text-sm text-gray-600">
                @if($totalItems > 0 && ($analyticsData['published_items'] ?? 0) / $totalItems < 0.5)
                    Complete more drafts to increase visibility
                @else
                    Great job completing your listings!
                @endif
            </p>
        </div>

        <!-- Revenue per Item -->
        <div class="p-4 bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg border border-green-200">
            <div class="flex items-center mb-3">
                <i class="fas fa-calculator text-green-600 mr-2"></i>
                <h4 class="font-semibold text-gray-900">Revenue per Item</h4>
            </div>
            <div class="text-2xl font-bold text-green-600 mb-2">
                ₦{{ $analyticsData['published_items'] > 0 ? number_format(($analyticsData['total_revenue'] ?? 0) / $analyticsData['published_items'], 0) : 0 }}
            </div>
            <p class="text-sm text-gray-600">Average earnings per published item</p>
        </div>

        <!-- Quick Actions -->
        <div class="p-4 bg-gradient-to-br from-purple-50 to-violet-50 rounded-lg border border-purple-200">
            <div class="flex items-center mb-3">
                <i class="fas fa-rocket text-purple-600 mr-2"></i>
                <h4 class="font-semibold text-gray-900">Quick Actions</h4>
            </div>
            <div class="space-y-2">
                @if(($analyticsData['draft_items'] ?? 0) > 0)
                    <button wire:click="showListings" 
                            class="w-full text-left px-3 py-2 bg-white rounded border hover:bg-gray-50 text-sm transition-colors">
                        <i class="fas fa-edit mr-2 text-purple-600"></i>
                        Complete {{ $analyticsData['draft_items'] }} drafts
                    </button>
                @endif
                
                <button wire:click="showCreate" 
                        class="w-full text-left px-3 py-2 bg-white rounded border hover:bg-gray-50 text-sm transition-colors">
                    <i class="fas fa-plus mr-2 text-purple-600"></i>
                    Create new item
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Recommended Actions -->
@if(($analyticsData['draft_items'] ?? 0) > 0 || ($analyticsData['rejected_items'] ?? 0) > 0)
    <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-exclamation-triangle mr-2 text-yellow-600"></i>
            Action Required
        </h3>
        
        <div class="space-y-3">
            @if(($analyticsData['draft_items'] ?? 0) > 0)
                <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-yellow-200">
                    <div class="flex items-center">
                        <i class="fas fa-edit text-yellow-600 mr-3"></i>
                        <div>
                            <p class="font-medium text-gray-900">{{ $analyticsData['draft_items'] }} Draft Items</p>
                            <p class="text-sm text-gray-600">Complete and submit these items for review</p>
                        </div>
                    </div>
                    <button wire:click="showListings; $set('status', 'draft')" 
                            class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                        Review Drafts
                    </button>
                </div>
            @endif
            
            @if(($analyticsData['rejected_items'] ?? 0) > 0)
                <div class="flex items-center justify-between p-3 bg-white rounded-lg border border-red-200">
                    <div class="flex items-center">
                        <i class="fas fa-times-circle text-red-600 mr-3"></i>
                        <div>
                            <p class="font-medium text-gray-900">{{ $analyticsData['rejected_items'] }} Rejected Items</p>
                            <p class="text-sm text-gray-600">Review feedback and resubmit these items</p>
                        </div>
                    </div>
                    <button wire:click="showListings; $set('status', 'rejected')" 
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        Fix Issues
                    </button>
                </div>
            @endif
        </div>
    </div>
@endif

<!-- Success Milestones -->
@if(($analyticsData['total_sales'] ?? 0) > 0 || ($analyticsData['total_revenue'] ?? 0) > 0)
    <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <i class="fas fa-trophy mr-2 text-green-600"></i>
            Milestones Achieved
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @if(($analyticsData['total_sales'] ?? 0) >= 1)
                <div class="text-center p-4 bg-white rounded-lg border border-green-200">
                    <i class="fas fa-medal text-green-600 text-2xl mb-2"></i>
                    <p class="font-semibold text-gray-900">First Sale!</p>
                    <p class="text-sm text-gray-600">You've made your first marketplace sale</p>
                </div>
            @endif
            
            @if(($analyticsData['total_revenue'] ?? 0) >= 10000)
                <div class="text-center p-4 bg-white rounded-lg border border-green-200">
                    <i class="fas fa-star text-green-600 text-2xl mb-2"></i>
                    <p class="font-semibold text-gray-900">₦10K Earned!</p>
                    <p class="text-sm text-gray-600">You've earned over ₦10,000</p>
                </div>
            @endif
            
            @if(($analyticsData['published_items'] ?? 0) >= 5)
                <div class="text-center p-4 bg-white rounded-lg border border-green-200">
                    <i class="fas fa-boxes text-green-600 text-2xl mb-2"></i>
                    <p class="font-semibold text-gray-900">5+ Items Published!</p>
                    <p class="text-sm text-gray-600">You have a growing marketplace presence</p>
                </div>
            @endif
        </div>
    </div>
@endif