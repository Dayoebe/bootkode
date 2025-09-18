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
    <div wire:loading class="fixed inset-0 bg-black bg-opacity-25 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 shadow-xl animate-pulse">
            <div class="flex items-center space-x-3">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-purple-600"></div>
                <span class="text-gray-700 font-medium">Processing...</span>
            </div>
        </div>
    </div>
</div>