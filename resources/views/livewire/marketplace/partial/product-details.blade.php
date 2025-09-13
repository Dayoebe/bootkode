
{{-- resources/views/livewire/marketplace/partial/product-details.blade.php --}}
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="text-center py-12">
            <i class="fas fa-box-open text-gray-300 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Product Details</h3>
            <p class="text-gray-500 mb-6">Select an item from the marketplace to view its details.</p>
            <a href="{{ route('marketplace.browse') }}" 
               class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                <i class="fas fa-search mr-2"></i>
                Browse Items
            </a>
        </div>
    </div>
</div>
