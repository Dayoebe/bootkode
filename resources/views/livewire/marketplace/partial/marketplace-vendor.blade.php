{{-- resources/views/livewire/marketplace/partial/marketplace-vendor.blade.php --}}
<div class="space-y-6">
    
    <!-- Navigation Header -->
    @include('livewire.marketplace.partial.vendor.navigation-header')

    <!-- Content Based on Current View -->
    @if($currentView === 'create')
        @include('livewire.marketplace.partial.vendor.create-form')
    @elseif($currentView === 'analytics')
        @include('livewire.marketplace.partial.vendor.analytics-dashboard')
    @else
        @include('livewire.marketplace.partial.vendor.listings-view')
    @endif

    <!-- Loading Overlay -->
    <div wire:loading class="fixed inset-0 bg-black dark:bg-gray-900 bg-opacity-25 dark:bg-opacity-75 z-50 flex items-center justify-center transition-colors duration-300">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-xl transition-colors duration-300">
            <div class="flex items-center space-x-3">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-purple-600 dark:border-purple-400"></div>
                <span class="text-gray-700 dark:text-gray-300 font-medium transition-colors duration-300">Processing...</span>
            </div>
        </div>
    </div>
</div>