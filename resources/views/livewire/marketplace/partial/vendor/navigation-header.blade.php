{{-- resources/views/livewire/marketplace/vendor/partials/navigation-header.blade.php --}}
<div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-lg shadow-lg text-white p-6">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between space-y-4 lg:space-y-0">
        <!-- Header Info -->
        <div>
            <h1 class="text-2xl font-bold">
                @if($currentView === 'create')
                    {{ $editingItemId ? 'Edit Item' : 'Create New Listing' }}
                @elseif($currentView === 'analytics')
                    Vendor Analytics
                @else
                    My Marketplace
                @endif
            </h1>
            <p class="text-purple-100 mt-1">
                @if($currentView === 'create')
                    {{ $editingItemId ? 'Update your marketplace item' : 'Add a new item to the marketplace' }}
                @elseif($currentView === 'analytics')
                    Track your performance and earnings
                @else
                    Manage your marketplace listings and performance
                @endif
            </p>
        </div>

        <!-- Quick Stats -->
        @if($currentView !== 'create')
            <div class="flex items-center space-x-6">
                <div class="text-center">
                    <div class="text-2xl font-bold">{{ $analyticsData['total_items'] ?? 0 }}</div>
                    <div class="text-sm text-purple-200">Total Items</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold">{{ $analyticsData['published_items'] ?? 0 }}</div>
                    <div class="text-sm text-purple-200">Published</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold">₦{{ number_format($analyticsData['total_revenue'] ?? 0, 0) }}</div>
                    <div class="text-sm text-purple-200">Total Earned</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold">{{ $analyticsData['total_sales'] ?? 0 }}</div>
                    <div class="text-sm text-purple-200">Sales</div>
                </div>
            </div>
        @endif
    </div>

    <!-- Navigation Tabs -->
    <div class="mt-6 border-t border-purple-400 pt-4">
        <nav class="flex space-x-1 overflow-x-auto">
            <button wire:click="showListings" 
                    class="px-4 py-2 rounded-lg font-medium text-sm whitespace-nowrap transition-colors {{ $currentView === 'listings' ? 'bg-white/20 text-white' : 'text-purple-200 hover:text-white hover:bg-white/10' }} flex items-center">
                <i class="fas fa-list-alt mr-2"></i>
                My Listings
                @if(isset($analyticsData['draft_items']) && $analyticsData['draft_items'] > 0)
                    <span class="ml-2 bg-yellow-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $analyticsData['draft_items'] }}</span>
                @endif
            </button>
            
            <button wire:click="showCreate" 
                    class="px-4 py-2 rounded-lg font-medium text-sm whitespace-nowrap transition-colors {{ $currentView === 'create' ? 'bg-white/20 text-white' : 'text-purple-200 hover:text-white hover:bg-white/10' }} flex items-center">
                <i class="fas fa-{{ $editingItemId ? 'edit' : 'plus' }} mr-2"></i>
                {{ $editingItemId ? 'Edit Item' : 'Create New' }}
            </button>

            <button wire:click="showAnalytics" 
                    class="px-4 py-2 rounded-lg font-medium text-sm whitespace-nowrap transition-colors {{ $currentView === 'analytics' ? 'bg-white/20 text-white' : 'text-purple-200 hover:text-white hover:bg-white/10' }} flex items-center">
                <i class="fas fa-chart-line mr-2"></i>
                Analytics
            </button>

            @if($currentView === 'create' && $editingItemId)
                <button wire:click="showCreate" 
                        class="px-4 py-2 rounded-lg font-medium text-sm whitespace-nowrap transition-colors text-purple-200 hover:text-white hover:bg-white/10 flex items-center">
                    <i class="fas fa-times mr-2"></i>
                    Cancel Edit
                </button>
            @endif
        </nav>
    </div>
</div>

<!-- Notification Alerts -->
@if(session()->has('message'))
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 flex items-center">
        <i class="fas fa-check-circle text-green-600 mr-3"></i>
        <div class="text-green-800 font-medium">{{ session('message') }}</div>
    </div>
@endif

@if(session()->has('error'))
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 flex items-center">
        <i class="fas fa-exclamation-circle text-red-600 mr-3"></i>
        <div class="text-red-800 font-medium">{{ session('error') }}</div>
    </div>
@endif

@if(session()->has('info'))
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-center">
        <i class="fas fa-info-circle text-blue-600 mr-3"></i>
        <div class="text-blue-800 font-medium">{{ session('info') }}</div>
    </div>
@endif