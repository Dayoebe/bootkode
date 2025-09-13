{{-- resources/views/livewire/marketplace/partial/browse-marketplace.blade.php --}}
<div class="space-y-6">
    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <div class="relative">
                    <input wire:model.live.debounce.300ms="search" 
                           type="text" 
                           placeholder="Search courses, resources, services..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                <select wire:model.live="type" class="w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    <option value="">All Types</option>
                    @foreach($types as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                <select wire:model.live="sortBy" class="w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    <option value="created_at">Newest</option>
                    <option value="price">Price: Low to High</option>
                    <option value="price,desc">Price: High to Low</option>
                    <option value="average_rating">Highest Rated</option>
                    <option value="sales_count">Best Selling</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Featured Items -->
    @if($featuredItems->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-star text-yellow-500 mr-2"></i>
                Featured Items
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($featuredItems as $item)
                    <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-lg p-4 border border-purple-200">
                        @if($item->getPrimaryImage())
                            <img src="{{ asset('storage/' . $item->getPrimaryImage()) }}" 
                                 alt="{{ $item->title }}" 
                                 class="w-full h-32 object-cover rounded-lg mb-3">
                        @else
                            <div class="w-full h-32 bg-gray-200 rounded-lg mb-3 flex items-center justify-center">
                                <i class="fas fa-image text-gray-400 text-2xl"></i>
                            </div>
                        @endif
                        
                        <div class="space-y-2">
                            <span class="inline-block px-2 py-1 bg-purple-100 text-purple-800 text-xs font-medium rounded-full">
                                {{ $item->type_name }}
                            </span>
                            <h3 class="font-semibold text-gray-900 line-clamp-2">{{ $item->title }}</h3>
                            <p class="text-sm text-gray-600 line-clamp-2">{{ $item->short_description }}</p>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    @if($item->hasDiscount())
                                        <span class="text-lg font-bold text-purple-600">{{ $item->getFormattedPrice() }}</span>
                                        <span class="text-sm text-gray-500 line-through">{{ $item->getFormattedOriginalPrice() }}</span>
                                    @else
                                        <span class="text-lg font-bold text-purple-600">{{ $item->getFormattedPrice() }}</span>
                                    @endif
                                </div>
                                <button class="bg-purple-600 text-white px-3 py-1 rounded-lg text-sm hover:bg-purple-700 transition-colors">
                                    View Details
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Main Items Grid -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">
                All Items ({{ $items->total() }})
            </h2>
        </div>

        @if($items->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($items as $item)
                    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
                        @if($item->getPrimaryImage())
                            <img src="{{ asset('storage/' . $item->getPrimaryImage()) }}" 
                                 alt="{{ $item->title }}" 
                                 class="w-full h-40 object-cover">
                        @else
                            <div class="w-full h-40 bg-gray-100 flex items-center justify-center">
                                <i class="fas fa-image text-gray-400 text-3xl"></i>
                            </div>
                        @endif
                        
                        <div class="p-4 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="px-2 py-1 bg-{{ $item->type === 'course' ? 'blue' : ($item->type === 'resource' ? 'green' : 'orange') }}-100 text-{{ $item->type === 'course' ? 'blue' : ($item->type === 'resource' ? 'green' : 'orange') }}-800 text-xs font-medium rounded-full">
                                    {{ $item->type_name }}
                                </span>
                                @if($item->average_rating > 0)
                                    <div class="flex items-center space-x-1">
                                        <i class="fas fa-star text-yellow-400 text-xs"></i>
                                        <span class="text-xs text-gray-600">{{ number_format($item->average_rating, 1) }}</span>
                                    </div>
                                @endif
                            </div>
                            
                            <h3 class="font-semibold text-gray-900 line-clamp-2">{{ $item->title }}</h3>
                            <p class="text-sm text-gray-600 line-clamp-2">{{ $item->short_description }}</p>
                            
                            <div class="flex items-center text-xs text-gray-500 space-x-3">
                                <span><i class="fas fa-user mr-1"></i>{{ $item->vendor->name }}</span>
                                <span><i class="fas fa-eye mr-1"></i>{{ $item->views_count }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between pt-2">
                                <div>
                                    @if($item->hasDiscount())
                                        <div class="flex items-center space-x-2">
                                            <span class="text-lg font-bold text-purple-600">{{ $item->getFormattedPrice() }}</span>
                                            <span class="text-sm text-gray-500 line-through">{{ $item->getFormattedOriginalPrice() }}</span>
                                        </div>
                                    @else
                                        <span class="text-lg font-bold text-purple-600">{{ $item->getFormattedPrice() }}</span>
                                    @endif
                                </div>
                                <button class="bg-purple-600 text-white px-3 py-1 rounded text-sm hover:bg-purple-700 transition-colors">
                                    View
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-6">
                {{ $items->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-search text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No items found</h3>
                <p class="text-gray-500">Try adjusting your search criteria or browse our categories.</p>
            </div>
        @endif
    </div>
</div>
