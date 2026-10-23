{{-- resources/views/livewire/marketplace/partial/marketplace-admin.blade.php --}}
<div class="space-y-6">
    <!-- Admin Dashboard Header -->
    <div class="border border-line bg-surface rounded-lg shadow-sm text-content p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between space-y-4 md:space-y-0">
            <div>
                <h2 class="text-2xl font-bold">Marketplace Administration</h2>
                <p class="text-brand">Manage vendors, orders, payments, and platform operations</p>
            </div>

            <div class="flex items-center space-x-4">
                <button wire:click="refreshStats"
                    class="inline-flex items-center px-4 py-2 bg-white/20  text-content rounded-lg hover:bg-white/30 transition-colors">
                    <i class="fas fa-sync-alt mr-2" wire:loading.class="animate-spin"></i>
                    Refresh Stats
                </button>

                <div class="text-right">
                    <div class="text-sm text-brand">Last updated</div>
                    <div class="text-content font-medium">{{ now()->format('M d, H:i') }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Overview -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        <div class="bg-surface rounded-lg shadow-sm border border-line p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-content-muted">Total Revenue</p>
                    <p class="text-lg font-semibold text-green-600">
                        ₦{{ number_format($stats['total_revenue'] ?? 0, 0) }}</p>
                </div>
                <div class="bg-green-100 p-2 rounded-full">
                    <i class="fas fa-chart-line text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-surface rounded-lg shadow-sm border border-line p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-content-muted">This Month</p>
                    <p class="text-lg font-semibold text-brand">
                        ₦{{ number_format($stats['this_month_revenue'] ?? 0, 0) }}</p>
                </div>
                <div class="bg-brand/10 p-2 rounded-full">
                    <i class="fas fa-calendar text-brand"></i>
                </div>
            </div>
        </div>

        <div class="bg-surface rounded-lg shadow-sm border border-line p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-content-muted">Total Orders</p>
                    <p class="text-lg font-semibold text-brand">{{ number_format($stats['total_orders'] ?? 0) }}</p>
                </div>
                <div class="bg-brand/10 p-2 rounded-full">
                    <i class="fas fa-shopping-cart text-brand"></i>
                </div>
            </div>
        </div>

        <div class="bg-surface rounded-lg shadow-sm border border-line p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-content-muted">Active Vendors</p>
                    <p class="text-lg font-semibold text-brand">{{ number_format($stats['total_vendors'] ?? 0) }}</p>
                </div>
                <div class="bg-brand/10 p-2 rounded-full">
                    <i class="fas fa-store text-brand"></i>
                </div>
            </div>
        </div>

        <div class="bg-surface rounded-lg shadow-sm border border-line p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-content-muted">Pending Payouts</p>
                    <p class="text-lg font-semibold text-yellow-600">
                        ₦{{ number_format($stats['pending_payouts'] ?? 0, 0) }}</p>
                </div>
                <div class="bg-yellow-100 p-2 rounded-full">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-surface rounded-lg shadow-sm border border-line p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-content-muted">Platform Earnings</p>
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
    <div class="bg-surface rounded-lg shadow-sm border border-line">
        <div class="border-b border-line">
            <nav class="flex space-x-1 px-6 overflow-x-auto">
                <button wire:click="setActiveTab('overview')"
                    class="py-4 px-3 border-b-2 font-medium text-sm whitespace-nowrap {{ $activeTab === 'overview' ? 'border-purple-500 text-purple-600' : 'border-transparent text-content-muted hover:text-gray-700' }}">
                    <i class="fas fa-tachometer-alt mr-2"></i>
                    Overview
                </button>

                <button wire:click="setActiveTab('vendors')"
                    class="py-4 px-3 border-b-2 font-medium text-sm whitespace-nowrap {{ $activeTab === 'vendors' ? 'border-purple-500 text-purple-600' : 'border-transparent text-content-muted hover:text-gray-700' }}">
                    <i class="fas fa-user-tie mr-2"></i>
                    Vendor Applications
                    @if(($stats['pending_applications'] ?? 0) > 0)
                        <span class="ml-2 bg-yellow-500 text-white text-xs px-2 py-1 rounded-full">
                            {{ $stats['pending_applications'] }}
                        </span>
                    @endif
                </button>

                <button wire:click="setActiveTab('items')"
                    class="py-4 px-3 border-b-2 font-medium text-sm whitespace-nowrap {{ $activeTab === 'items' ? 'border-purple-500 text-purple-600' : 'border-transparent text-content-muted hover:text-gray-700' }}">
                    <i class="fas fa-box mr-2"></i>
                    Item Management
                    @if(($stats['pending_approval'] ?? 0) > 0)
                        <span class="ml-2 bg-orange-500 text-white text-xs px-2 py-1 rounded-full">
                            {{ $stats['pending_approval'] }}
                        </span>
                    @endif
                </button>

                <button wire:click="setActiveTab('orders')"
                    class="py-4 px-3 border-b-2 font-medium text-sm whitespace-nowrap {{ $activeTab === 'orders' ? 'border-purple-500 text-purple-600' : 'border-transparent text-content-muted hover:text-gray-700' }}">
                    <i class="fas fa-clipboard-list mr-2"></i>
                    Order Management
                    @if(($stats['pending_orders'] ?? 0) > 0)
                        <span class="ml-2 bg-action text-action-foreground text-xs px-2 py-1 rounded-full">
                            {{ $stats['pending_orders'] }}
                        </span>
                    @endif
                </button>

                <button wire:click="setActiveTab('payments')"
                    class="py-4 px-3 border-b-2 font-medium text-sm whitespace-nowrap {{ $activeTab === 'payments' ? 'border-purple-500 text-purple-600' : 'border-transparent text-content-muted hover:text-gray-700' }}">
                    <i class="fas fa-credit-card mr-2"></i>
                    Payments & Payouts
                    @if(($stats['payout_requests'] ?? 0) > 0)
                        <span class="ml-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full">
                            {{ $stats['payout_requests'] }}
                        </span>
                    @endif
                </button>

                <button wire:click="setActiveTab('analytics')"
                    class="py-4 px-3 border-b-2 font-medium text-sm whitespace-nowrap {{ $activeTab === 'analytics' ? 'border-purple-500 text-purple-600' : 'border-transparent text-content-muted hover:text-gray-700' }}">
                    <i class="fas fa-chart-bar mr-2"></i>
                    Analytics
                </button>
            </nav>
        </div>

        <div class="p-6">
            @if($activeTab === 'overview')
                @include('livewire.marketplace.partial.admin.overview-tab')
            @elseif($activeTab === 'vendors')
                @include('livewire.marketplace.partial.admin.vendors-tab')
            @elseif($activeTab === 'items')
                @include('livewire.marketplace.partial.admin.items-tab')
            @elseif($activeTab === 'orders')
                @include('livewire.marketplace.partial.admin.orders-tab')
            @elseif($activeTab === 'payments')
                @include('livewire.marketplace.partial.admin.payments-tab')
            @elseif($activeTab === 'analytics')
                @include('livewire.marketplace.partial.admin.analytics-tab')
            @endif
        </div>
    </div>

    <!-- Modals -->
    @include('livewire.marketplace.partial.admin.modals')

    <!-- Loading State Overlay -->
    <div wire:loading class="fixed inset-0 bg-black bg-opacity-25 z-40 flex items-center justify-center">
        <div class="bg-surface rounded-lg p-6 shadow-sm   mx-4">
            <div class="flex items-center space-x-4">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-brand"></div>
                <span class="text-content font-medium">Processing admin action...</span>
            </div>
        </div>
    </div>
</div>
