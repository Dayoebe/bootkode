{{-- resources/views/livewire/marketplace/marketplace-system.blade.php --}}
<div class="space-y-6" x-data="{ loading: false }">
    <!-- Header Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-colors duration-300">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between space-y-4 lg:space-y-0">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Marketplace Management System</h2>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Comprehensive marketplace administration dashboard</p>
            </div>
            
            <!-- Quick Actions -->
            <div class="flex flex-wrap gap-2">
                <button wire:click="$set('activeTab', 'overview')" 
                        class="px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors duration-300">
                    <i class="fas fa-chart-bar mr-2"></i>Overview
                </button>
                <button wire:click="$set('activeTab', 'settings')" 
                        class="px-4 py-2 bg-gray-600 dark:bg-gray-500 text-white rounded-lg hover:bg-gray-700 dark:hover:bg-gray-600 transition-colors duration-300">
                    <i class="fas fa-cogs mr-2"></i>Settings
                </button>
                <button wire:click="$refresh" 
                        class="px-4 py-2 bg-green-600 dark:bg-green-500 text-white rounded-lg hover:bg-green-700 dark:hover:bg-green-600 transition-colors duration-300">
                    <i class="fas fa-sync-alt mr-2"></i>Refresh
                </button>
            </div>
        </div>

        <!-- Stats Overview Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mt-6">
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 text-center transition-colors duration-300">
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['total_items'] ?? 0 }}</div>
                <div class="text-sm text-blue-500 dark:text-blue-400">Total Items</div>
            </div>
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 text-center transition-colors duration-300">
                <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['published_items'] ?? 0 }}</div>
                <div class="text-sm text-green-500 dark:text-green-400">Published</div>
            </div>
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 text-center transition-colors duration-300">
                <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['pending_items'] ?? 0 }}</div>
                <div class="text-sm text-yellow-500 dark:text-yellow-400">Pending</div>
            </div>
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 text-center transition-colors duration-300">
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['total_vendors'] ?? 0 }}</div>
                <div class="text-sm text-blue-500 dark:text-blue-400">Vendors</div>
            </div>
            <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-lg p-4 text-center transition-colors duration-300">
                <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $stats['total_orders'] ?? 0 }}</div>
                <div class="text-sm text-indigo-500 dark:text-indigo-400">Total Orders</div>
            </div>
            <div class="bg-pink-50 dark:bg-pink-900/20 border border-pink-200 dark:border-pink-800 rounded-lg p-4 text-center transition-colors duration-300">
                <div class="text-2xl font-bold text-pink-600 dark:text-pink-400">₦{{ number_format($stats['total_revenue'] ?? 0, 0) }}</div>
                <div class="text-sm text-pink-500 dark:text-pink-400">Total Revenue</div>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 transition-colors duration-300">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="flex space-x-8 px-6" aria-label="Tabs">
                <button wire:click="$set('activeTab', 'overview')" 
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-300 {{ $activeTab === 'overview' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }}">
                    <i class="fas fa-chart-bar mr-2"></i>Overview
                </button>
                <button wire:click="$set('activeTab', 'analytics')" 
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-300 {{ $activeTab === 'analytics' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }}">
                    <i class="fas fa-chart-line mr-2"></i>Analytics
                </button>
                <button wire:click="$set('activeTab', 'items')" 
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-300 {{ $activeTab === 'items' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }}">
                    <i class="fas fa-box mr-2"></i>Items Management
                    @if(($stats['pending_items'] ?? 0) > 0)
                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-300">
                            {{ $stats['pending_items'] }}
                        </span>
                    @endif
                </button>
                <button wire:click="$set('activeTab', 'orders')" 
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-300 {{ $activeTab === 'orders' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }}">
                    <i class="fas fa-shopping-cart mr-2"></i>Orders
                    @if(($stats['pending_orders'] ?? 0) > 0)
                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-300">
                            {{ $stats['pending_orders'] }}
                        </span>
                    @endif
                </button>
                <button wire:click="$set('activeTab', 'vendors')" 
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-300 {{ $activeTab === 'vendors' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }}">
                    <i class="fas fa-users mr-2"></i>Vendors
                </button>
                <button wire:click="$set('activeTab', 'settings')" 
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-300 {{ $activeTab === 'settings' ? 'border-blue-500 text-blue-600 dark:text-blue-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600' }}">
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
    <div wire:loading.flex class="fixed inset-0 bg-gray-600 bg-opacity-50 dark:bg-gray-900 dark:bg-opacity-60 z-50 items-center justify-center transition-colors duration-300">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-xl transition-colors duration-300">
            <div class="flex items-center space-x-3">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 dark:border-blue-400"></div>
                <span class="text-gray-700 dark:text-gray-300">Loading...</span>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if (session()->has('message'))
        <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform translate-y-2" x-init="setTimeout(() => show = false, 5000)" class="fixed top-4 right-4 z-50 max-w-sm">
            <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded shadow-lg transition-colors duration-300">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ session('message') }}
                    </div>
                    <button @click="show = false" class="text-green-500 dark:text-green-400 hover:text-green-700 dark:hover:text-green-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Error Messages -->
    @if (session()->has('error'))
        <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform translate-y-2" x-init="setTimeout(() => show = false, 5000)" class="fixed top-4 right-4 z-50 max-w-sm">
            <div class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded shadow-lg transition-colors duration-300">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        {{ session('error') }}
                    </div>
                    <button @click="show = false" class="text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>