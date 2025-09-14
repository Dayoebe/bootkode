{{-- resources/views/livewire/marketplace/marketplace-system.blade.php --}}
<div class="space-y-6" x-data="{ loading: false }">
    <!-- Header Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between space-y-4 lg:space-y-0">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Marketplace Management System</h2>
                <p class="text-gray-600 mt-1">Comprehensive marketplace administration dashboard</p>
            </div>
            
            <!-- Quick Actions -->
            <div class="flex flex-wrap gap-2">
                <button wire:click="$set('activeTab', 'overview')" 
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    <i class="fas fa-chart-bar mr-2"></i>Overview
                </button>
                <button wire:click="$set('activeTab', 'settings')" 
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-cogs mr-2"></i>Settings
                </button>
                <button wire:click="$refresh" 
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-sync-alt mr-2"></i>Refresh
                </button>
            </div>
        </div>

        <!-- Stats Overview Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mt-6">
            <div class="bg-purple-50 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-purple-600">{{ $stats['total_items'] ?? 0 }}</div>
                <div class="text-sm text-purple-500">Total Items</div>
            </div>
            <div class="bg-green-50 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-green-600">{{ $stats['published_items'] ?? 0 }}</div>
                <div class="text-sm text-green-500">Published</div>
            </div>
            <div class="bg-yellow-50 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending_items'] ?? 0 }}</div>
                <div class="text-sm text-yellow-500">Pending</div>
            </div>
            <div class="bg-blue-50 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $stats['total_vendors'] ?? 0 }}</div>
                <div class="text-sm text-blue-500">Vendors</div>
            </div>
            <div class="bg-indigo-50 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-indigo-600">{{ $stats['total_orders'] ?? 0 }}</div>
                <div class="text-sm text-indigo-500">Total Orders</div>
            </div>
            <div class="bg-pink-50 rounded-lg p-4 text-center">
                <div class="text-2xl font-bold text-pink-600">₦{{ number_format($stats['total_revenue'] ?? 0, 0) }}</div>
                <div class="text-sm text-pink-500">Total Revenue</div>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="border-b border-gray-200">
            <nav class="flex space-x-8 px-6" aria-label="Tabs">
                <button wire:click="$set('activeTab', 'overview')" 
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'overview' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="fas fa-chart-bar mr-2"></i>Overview
                </button>
                <button wire:click="$set('activeTab', 'analytics')" 
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'analytics' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="fas fa-chart-line mr-2"></i>Analytics
                </button>
                <button wire:click="$set('activeTab', 'items')" 
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'items' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="fas fa-box mr-2"></i>Items Management
                    @if(($stats['pending_items'] ?? 0) > 0)
                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            {{ $stats['pending_items'] }}
                        </span>
                    @endif
                </button>
                <button wire:click="$set('activeTab', 'orders')" 
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'orders' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="fas fa-shopping-cart mr-2"></i>Orders
                    @if(($stats['pending_orders'] ?? 0) > 0)
                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            {{ $stats['pending_orders'] }}
                        </span>
                    @endif
                </button>
                <button wire:click="$set('activeTab', 'vendors')" 
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'vendors' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="fas fa-users mr-2"></i>Vendors
                </button>
                <button wire:click="$set('activeTab', 'settings')" 
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'settings' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="fas fa-cogs mr-2"></i>Settings
                </button>
            </nav>
        </div>

        <!-- Content Area with Loading Animation -->
        <div class="px-6 py-6 overflow-x-auto" wire:loading.class="opacity-50 pointer-events-none">
            <div class="animate__animated animate__fadeIn w-full min-w-0">
                @if($activeTab === 'overview')
                    @include('livewire.marketplace.partial.system.overview-tab')
                @elseif($activeTab === 'analytics')
                    @include('livewire.marketplace.partial.system.analytics-tab')
                @elseif($activeTab === 'items')
                    @include('livewire.marketplace.partial.system.items-tab')
                @elseif($activeTab === 'orders')
                    @include('livewire.marketplace.partial.system.orders-tab')
                @elseif($activeTab === 'vendors')
                    @include('livewire.marketplace.partial.system.vendors-tab')
                @elseif($activeTab === 'settings')
                    @include('livewire.marketplace.partial.system.settings-tab')
                @endif
            </div>
        </div>
    </div>

    <!-- Modals -->
    @include('livewire.marketplace.partial.system.system-modals')

    <!-- Loading Indicator -->
    <div wire:loading.flex class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 items-center justify-center">
        <div class="bg-white rounded-lg p-6 shadow-xl">
            <div class="flex items-center space-x-3">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-purple-600"></div>
                <span class="text-gray-700">Loading...</span>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform translate-y-2" x-init="setTimeout(() => show = false, 5000)" class="fixed top-4 right-4 z-50 max-w-sm">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded shadow-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('message') }}
                    </div>
                    <button @click="show = false" class="text-green-500 hover:text-green-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Error Messages -->
    @if (session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform translate-y-2" x-init="setTimeout(() => show = false, 5000)" class="fixed top-4 right-4 z-50 max-w-sm">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded shadow-lg">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        {{ session('error') }}
                    </div>
                    <button @click="show = false" class="text-red-500 hover:text-red-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>