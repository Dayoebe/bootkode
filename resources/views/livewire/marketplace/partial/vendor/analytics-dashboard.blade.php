{{-- resources/views/livewire/marketplace/vendor/partials/analytics-dashboard.blade.php --}}

<!-- Analytics Overview Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <!-- Revenue Card -->
    <div class="bg-brand-ink   rounded-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm font-medium">Total Revenue</p>
                <p class="text-3xl font-bold">₦{{ number_format($analyticsData['total_revenue'] ?? 0, 0) }}</p>
                <p class="text-green-200 text-sm mt-1">All-time earnings</p>
            </div>
            <div class="bg-green-400/30 p-3 rounded-full">
                <i aria-hidden="true" class="fas fa-money-bill-wave text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Sales Card -->
    <div class="bg-brand-ink   rounded-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-brand text-sm font-medium">Total Sales</p>
                <p class="text-3xl font-bold">{{ $analyticsData['total_sales'] ?? 0 }}</p>
                <p class="text-brand text-sm mt-1">Items sold</p>
            </div>
            <div class="bg-blue-400/30 p-3 rounded-full">
                <i aria-hidden="true" class="fas fa-shopping-cart text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Items Card -->
    <div class="bg-brand-ink   rounded-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-brand text-sm font-medium">Published Items</p>
                <p class="text-3xl font-bold">{{ $analyticsData['published_items'] ?? 0 }}</p>
                <p class="text-brand text-sm mt-1">of {{ $analyticsData['total_items'] ?? 0 }} total</p>
            </div>
            <div class="bg-purple-400/30 p-3 rounded-full">
                <i aria-hidden="true" class="fas fa-box text-2xl"></i>
            </div>
        </div>
    </div>

    <!-- Rating Card -->
    <div class="bg-brand-ink   rounded-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-yellow-100 text-sm font-medium">Average Rating</p>
                <p class="text-3xl font-bold">{{ number_format($analyticsData['avg_rating'] ?? 0, 1) }}</p>
                <p class="text-yellow-200 text-sm mt-1">Customer satisfaction</p>
            </div>
            <div class="bg-yellow-400/30 p-3 rounded-full">
                <i aria-hidden="true" class="fas fa-star text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Performance -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- This Month Stats -->
    <div class="bg-surface rounded-lg shadow-sm border border-line p-6">
        <h3 class="text-lg font-semibold text-content mb-4 flex items-center">
            <i aria-hidden="true" class="fas fa-calendar-alt mr-2 text-brand"></i>
            This Month Performance
        </h3>

        <div class="space-y-4">
            <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                <div class="flex items-center">
                    <div class="bg-green-500 p-2 rounded-full mr-3">
                        <i aria-hidden="true" class="fas fa-dollar-sign text-white"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-content-muted">Monthly Revenue</p>
                        <p class="text-xl font-bold text-content">₦{{ number_format($analyticsData['this_month_revenue'] ?? 0, 0) }}</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between p-4 bg-brand/10 rounded-lg">
                <div class="flex items-center">
                    <div class="bg-action p-2 rounded-full mr-3">
                        <i aria-hidden="true" class="fas fa-shopping-bag text-action-foreground"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-content-muted">Monthly Sales</p>
                        <p class="text-xl font-bold text-content">{{ $analyticsData['this_month_sales'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between p-4 bg-brand/10 rounded-lg">
                <div class="flex items-center">
                    <div class="bg-action p-2 rounded-full mr-3">
                        <i aria-hidden="true" class="fas fa-eye text-action-foreground"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-content-muted">Total Views</p>
                        <p class="text-xl font-bold text-content">{{ number_format($analyticsData['total_views'] ?? 0) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Item Status Breakdown -->
    <div class="bg-surface rounded-lg shadow-sm border border-line p-6">
        <h3 class="text-lg font-semibold text-content mb-4 flex items-center">
            <i aria-hidden="true" class="fas fa-chart-pie mr-2 text-brand"></i>
            Item Status Breakdown
        </h3>

        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-green-500 rounded-full mr-3"></div>
                    <span class="text-sm font-medium text-content">Published</span>
                </div>
                <span class="font-bold text-content">{{ $analyticsData['published_items'] ?? 0 }}</span>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-yellow-500 rounded-full mr-3"></div>
                    <span class="text-sm font-medium text-content">Drafts</span>
                </div>
                <span class="font-bold text-content">{{ $analyticsData['draft_items'] ?? 0 }}</span>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-action rounded-full mr-3"></div>
                    <span class="text-sm font-medium text-content">Pending Review</span>
                </div>
                <span class="font-bold text-content">{{ $analyticsData['pending_items'] ?? 0 }}</span>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-red-500 rounded-full mr-3"></div>
                    <span class="text-sm font-medium text-content">Rejected</span>
                </div>
                <span class="font-bold text-content">{{ $analyticsData['rejected_items'] ?? 0 }}</span>
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
                    <div class="bg-action" style="width: {{ $pendingPercent }}%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Performance Insights -->
<div class="bg-surface rounded-lg shadow-sm border border-line p-6">
    <h3 class="text-lg font-semibold text-content mb-6 flex items-center">
        <i aria-hidden="true" class="fas fa-lightbulb mr-2 text-yellow-500"></i>
        Performance Insights & Tips
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Completion Rate -->
        <div class="p-4 bg-surface-muted   rounded-lg border border-brand">
            <div class="flex items-center mb-3">
                <i aria-hidden="true" class="fas fa-tasks text-brand mr-2"></i>
                <h4 class="font-semibold text-content">Completion Rate</h4>
            </div>
            <div class="text-2xl font-bold text-brand mb-2">
                {{ $totalItems > 0 ? round(($analyticsData['published_items'] ?? 0) / $totalItems * 100) : 0 }}%
            </div>
            <p class="text-sm text-content-muted">
                @if($totalItems > 0 && ($analyticsData['published_items'] ?? 0) / $totalItems < 0.5)
                    Complete more drafts to increase visibility
                @else
                    Great job completing your listings!
                @endif
            </p>
        </div>

        <!-- Revenue per Item -->
        <div class="p-4 bg-surface-muted   rounded-lg border border-green-200">
            <div class="flex items-center mb-3">
                <i aria-hidden="true" class="fas fa-calculator text-green-600 mr-2"></i>
                <h4 class="font-semibold text-content">Revenue per Item</h4>
            </div>
            <div class="text-2xl font-bold text-green-600 mb-2">
                ₦{{ $analyticsData['published_items'] > 0 ? number_format(($analyticsData['total_revenue'] ?? 0) / $analyticsData['published_items'], 0) : 0 }}
            </div>
            <p class="text-sm text-content-muted">Average earnings per published item</p>
        </div>

        <!-- Quick Actions -->
        <div class="p-4 bg-surface-muted   rounded-lg border border-brand">
            <div class="flex items-center mb-3">
                <i aria-hidden="true" class="fas fa-rocket text-brand mr-2"></i>
                <h4 class="font-semibold text-content">Quick Actions</h4>
            </div>
            <div class="space-y-2">
                @if(($analyticsData['draft_items'] ?? 0) > 0)
                    <button wire:click="showListings"
                            class="w-full text-left px-3 py-2 bg-surface rounded border hover:bg-surface-muted text-sm transition-colors">
                        <i aria-hidden="true" class="fas fa-edit mr-2 text-brand"></i>
                        Complete {{ $analyticsData['draft_items'] }} drafts
                    </button>
                @endif

                <button wire:click="showCreate"
                        class="w-full text-left px-3 py-2 bg-surface rounded border hover:bg-surface-muted text-sm transition-colors">
                    <i aria-hidden="true" class="fas fa-plus mr-2 text-brand"></i>
                    Create new item
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Recommended Actions -->
@if(($analyticsData['draft_items'] ?? 0) > 0 || ($analyticsData['rejected_items'] ?? 0) > 0)
    <div class="bg-surface-muted   border border-yellow-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-content mb-4 flex items-center">
            <i aria-hidden="true" class="fas fa-exclamation-triangle mr-2 text-yellow-600"></i>
            Action Required
        </h3>

        <div class="space-y-3">
            @if(($analyticsData['draft_items'] ?? 0) > 0)
                <div class="flex items-center justify-between p-3 bg-surface rounded-lg border border-yellow-200">
                    <div class="flex items-center">
                        <i aria-hidden="true" class="fas fa-edit text-yellow-600 mr-3"></i>
                        <div>
                            <p class="font-medium text-content">{{ $analyticsData['draft_items'] }} Draft Items</p>
                            <p class="text-sm text-content-muted">Complete and submit these items for review</p>
                        </div>
                    </div>
                    <button wire:click="showListings; $set('status', 'draft')"
                            class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                        Review Drafts
                    </button>
                </div>
            @endif

            @if(($analyticsData['rejected_items'] ?? 0) > 0)
                <div class="flex items-center justify-between p-3 bg-surface rounded-lg border border-red-200">
                    <div class="flex items-center">
                        <i aria-hidden="true" class="fas fa-times-circle text-red-600 mr-3"></i>
                        <div>
                            <p class="font-medium text-content">{{ $analyticsData['rejected_items'] }} Rejected Items</p>
                            <p class="text-sm text-content-muted">Review feedback and resubmit these items</p>
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
    <div class="bg-surface-muted   border border-green-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-content mb-4 flex items-center">
            <i aria-hidden="true" class="fas fa-trophy mr-2 text-green-600"></i>
            Milestones Achieved
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @if(($analyticsData['total_sales'] ?? 0) >= 1)
                <div class="text-center p-4 bg-surface rounded-lg border border-green-200">
                    <i aria-hidden="true" class="fas fa-medal text-green-600 text-2xl mb-2"></i>
                    <p class="font-semibold text-content">First Sale!</p>
                    <p class="text-sm text-content-muted">You've made your first marketplace sale</p>
                </div>
            @endif

            @if(($analyticsData['total_revenue'] ?? 0) >= 10000)
                <div class="text-center p-4 bg-surface rounded-lg border border-green-200">
                    <i aria-hidden="true" class="fas fa-star text-green-600 text-2xl mb-2"></i>
                    <p class="font-semibold text-content">₦10K Earned!</p>
                    <p class="text-sm text-content-muted">You've earned over ₦10,000</p>
                </div>
            @endif

            @if(($analyticsData['published_items'] ?? 0) >= 5)
                <div class="text-center p-4 bg-surface rounded-lg border border-green-200">
                    <i aria-hidden="true" class="fas fa-boxes text-green-600 text-2xl mb-2"></i>
                    <p class="font-semibold text-content">5+ Items Published!</p>
                    <p class="text-sm text-content-muted">You have a growing marketplace presence</p>
                </div>
            @endif
        </div>
    </div>
@endif
