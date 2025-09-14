{{-- resources/views/livewire/marketplace/public-marketplace.blade.php --}}
<div class="min-h-screen bg-gray-50" x-data="{ mobileFiltersOpen: false, mobileMenuOpen: false }">
    @if($activeView === 'browse')
        <!-- Hero Section -->
        <section class="bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <h1 class="text-4xl lg:text-6xl font-bold text-gray-900 leading-tight">
                            Learn from the <span class="text-purple-600">best</span> instructors worldwide
                        </h1>
                        <p class="mt-6 text-xl text-gray-600 leading-relaxed">
                            Discover high-quality courses, digital resources, and services from expert instructors. 
                            Build your skills with trusted content at affordable prices.
                        </p>
                        <div class="mt-8 flex flex-col sm:flex-row gap-4">
                            @guest
                                <button wire:click="redirectToRegister" 
                                        class="bg-purple-600 text-white px-8 py-4 rounded-lg hover:bg-purple-700 transition-colors font-semibold text-lg">
                                    Start Learning Today
                                </button>
                                <button wire:click="redirectToLogin" 
                                        class="border-2 border-gray-300 text-gray-700 px-8 py-4 rounded-lg hover:border-purple-600 hover:text-purple-600 transition-colors font-semibold text-lg">
                                    Sign In
                                </button>
                            @endguest
                        </div>
                        
                        <!-- Stats -->
                        <div class="mt-12 grid grid-cols-3 gap-8">
                            <div>
                                <div class="text-2xl font-bold text-purple-600">{{ number_format($stats['total_products']) }}+</div>
                                <div class="text-sm text-gray-600 mt-1">Quality Products</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-purple-600">{{ number_format($stats['total_vendors']) }}+</div>
                                <div class="text-sm text-gray-600 mt-1">Expert Instructors</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-purple-600">{{ number_format($stats['average_rating'], 1) }}/5</div>
                                <div class="text-sm text-gray-600 mt-1">Average Rating</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Hero Image/Illustration -->
                    <div class="relative">
                        <div class="aspect-square bg-purple-100 rounded-2xl overflow-hidden">
                            <div class="h-full w-full bg-purple-200 flex items-center justify-center">
                                <i class="fas fa-graduation-cap text-purple-500 text-8xl"></i>
                            </div>
                        </div>
                        <!-- Floating cards -->
                        <div class="absolute -top-4 -left-4 bg-white rounded-lg shadow-lg p-4 border border-gray-200">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-play text-green-600 text-sm"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">25k+ Videos</div>
                                    <div class="text-xs text-gray-500">High Quality</div>
                                </div>
                            </div>
                        </div>
                        <div class="absolute -bottom-4 -right-4 bg-white rounded-lg shadow-lg p-4 border border-gray-200">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-certificate text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">Certificates</div>
                                    <div class="text-xs text-gray-500">On Completion</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Products -->
        @if($featuredItems->count() > 0)
        <section class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900">Featured Products</h2>
                    <p class="mt-4 text-lg text-gray-600">Hand-picked premium content from our top instructors</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($featuredItems as $item)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200 group cursor-pointer" 
                             wire:click="viewProduct({{ $item->id }})">
                            <div class="aspect-video bg-gray-200 overflow-hidden">
                                @if($item->getPrimaryImage())
                                    <img src="{{ Storage::url($item->getPrimaryImage()) }}" 
                                         alt="{{ $item->title }}" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-purple-100">
                                        <i class="fas fa-book text-purple-400 text-2xl"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-medium text-purple-600 bg-purple-100 px-2 py-1 rounded">
                                        {{ $item->type_name }}
                                    </span>
                                    @if($item->hasDiscount())
                                        <span class="text-xs font-medium text-red-600 bg-red-100 px-2 py-1 rounded">
                                            {{ $item->getDiscountPercentage() }}% OFF
                                        </span>
                                    @endif
                                </div>
                                <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2 group-hover:text-purple-600 transition-colors">
                                    {{ $item->title }}
                                </h3>
                                <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $item->short_description }}</p>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-semibold text-purple-600">
                                                {{ substr($item->vendor->name, 0, 2) }}
                                            </span>
                                        </div>
                                        <span class="text-sm text-gray-700">{{ $item->vendor->name }}</span>
                                    </div>
                                    <div class="text-right">
                                        @if($item->hasDiscount())
                                            <div class="text-xs text-gray-500 line-through">{{ $item->getFormattedOriginalPrice() }}</div>
                                        @endif
                                        <div class="font-bold text-purple-600">{{ $item->getFormattedPrice() }}</div>
                                    </div>
                                </div>
                                @if($item->average_rating > 0)
                                    <div class="flex items-center mt-3 pt-3 border-t border-gray-100">
                                        <div class="flex items-center space-x-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star text-xs {{ $i <= $item->average_rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                            @endfor
                                        </div>
                                        <span class="text-sm text-gray-600 ml-2">({{ $item->reviews_count }})</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        <!-- Categories Section -->
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900">Popular Categories</h2>
                    <p class="mt-4 text-lg text-gray-600">Explore our most popular learning categories</p>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                    @foreach($popularCategories as $category)
                        <button wire:click="viewCategory({{ $category->id }})" 
                                class="group p-6 bg-gray-50 rounded-xl hover:bg-purple-50 transition-colors duration-200 text-center">
                            <div class="w-12 h-12 bg-white rounded-lg mx-auto mb-4 flex items-center justify-center shadow-sm group-hover:shadow-md transition-shadow">
                                <i class="{{ $category->icon ?? 'fas fa-tag' }} text-purple-600 text-xl"></i>
                            </div>
                            <h3 class="font-semibold text-gray-900 mb-1">{{ $category->name }}</h3>
                            <p class="text-sm text-gray-600">{{ $category->items_count }} items</p>
                        </button>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Top Vendors Section -->
        @if($topVendors->count() > 0)
        <section class="py-16 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900">Top Instructors</h2>
                    <p class="mt-4 text-lg text-gray-600">Learn from the best in the industry</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($topVendors as $vendor)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center hover:shadow-md transition-shadow cursor-pointer"
                             wire:click="viewVendor({{ $vendor->id }})">
                            <div class="w-20 h-20 bg-purple-100 rounded-full mx-auto mb-4 flex items-center justify-center">
                                <span class="text-2xl font-bold text-purple-600">
                                    {{ substr($vendor->name, 0, 2) }}
                                </span>
                            </div>
                            <h3 class="font-semibold text-gray-900 mb-1">{{ $vendor->name }}</h3>
                            <p class="text-sm text-gray-600 mb-3">{{ $vendor->published_items_count }} Products</p>
                            @if($vendor->avg_rating > 0)
                                <div class="flex items-center justify-center space-x-1 mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star text-sm {{ $i <= $vendor->avg_rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                    @endfor
                                    <span class="text-sm text-gray-600 ml-1">({{ number_format($vendor->avg_rating, 1) }})</span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        <!-- Trending Products -->
        @if($trendingItems->count() > 0)
        <section class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900">Trending This Week</h2>
                    <p class="mt-4 text-lg text-gray-600">Most popular products from the past 7 days</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($trendingItems as $item)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200 group cursor-pointer" 
                             wire:click="viewProduct({{ $item->id }})">
                            <div class="aspect-video bg-gray-200 overflow-hidden relative">
                                @if($item->getPrimaryImage())
                                    <img src="{{ Storage::url($item->getPrimaryImage()) }}" 
                                         alt="{{ $item->title }}" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-purple-100">
                                        <i class="fas fa-book text-purple-400 text-2xl"></i>
                                    </div>
                                @endif
                                <div class="absolute top-3 left-3">
                                    <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full font-medium">
                                        <i class="fas fa-fire mr-1"></i>Trending
                                    </span>
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-medium text-purple-600 bg-purple-100 px-2 py-1 rounded">
                                        {{ $item->type_name }}
                                    </span>
                                    <span class="text-xs text-gray-500">{{ $item->views_count }} views</span>
                                </div>
                                <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2 group-hover:text-purple-600 transition-colors">
                                    {{ $item->title }}
                                </h3>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 bg-purple-100 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-semibold text-purple-600">
                                                {{ substr($item->vendor->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <span class="text-sm text-gray-700">{{ $item->vendor->name }}</span>
                                    </div>
                                    <div class="font-bold text-purple-600">{{ $item->getFormattedPrice() }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

    @elseif($activeView === 'search' || $activeView === 'category' || $activeView === 'vendor')
        <!-- Search/Filter Results -->
        <section class="py-8 bg-white border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Breadcrumb -->
                <nav class="flex items-center space-x-2 text-sm text-gray-600 mb-6">
                    <button wire:click="backToBrowse" class="hover:text-purple-600 transition-colors">Home</button>
                    <i class="fas fa-chevron-right text-xs"></i>
                    @if($activeView === 'search')
                        <span class="text-gray-900 font-medium">Search Results</span>
                    @elseif($activeView === 'category' && $selectedCategory)
                        <span class="text-gray-900 font-medium">{{ $selectedCategory->name }}</span>
                    @elseif($activeView === 'vendor' && $selectedVendor)
                        <span class="text-gray-900 font-medium">{{ $selectedVendor->name }}'s Products</span>
                    @endif
                </nav>

                <!-- Header -->
                <div class="flex flex-col lg:flex-row lg:items-center justify-between mb-8">
                    <div>
                        @if($activeView === 'search')
                            <h1 class="text-2xl font-bold text-gray-900">
                                @if($searchTerm)
                                    Results for "{{ $searchTerm }}"
                                @else
                                    All Products
                                @endif
                            </h1>
                            <p class="text-gray-600 mt-1">{{ $items->total() }} products found</p>
                        @elseif($activeView === 'category' && $selectedCategory)
                            <h1 class="text-2xl font-bold text-gray-900">{{ $selectedCategory->name }}</h1>
                            <p class="text-gray-600 mt-1">{{ $selectedCategory->description ?? $items->total() . ' products available' }}</p>
                        @elseif($activeView === 'vendor' && $selectedVendor)
                            <div class="flex items-center space-x-4">
                                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center">
                                    <span class="text-xl font-bold text-purple-600">
                                        {{ substr($selectedVendor->name, 0, 2) }}
                                    </span>
                                </div>
                                <div>
                                    <h1 class="text-2xl font-bold text-gray-900">{{ $selectedVendor->name }}</h1>
                                    <p class="text-gray-600">{{ $items->total() }} products available</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Controls -->
                    <div class="flex items-center space-x-4 mt-6 lg:mt-0">
                        <button wire:click="toggleFilters" 
                                class="flex items-center space-x-2 px-4 py-2 border border-gray-300 rounded-lg hover:border-purple-600 hover:text-purple-600 transition-colors">
                            <i class="fas fa-filter"></i>
                            <span>Filters</span>
                            @if(count($selectedCategories) > 0 || count($selectedTypes) > 0 || $minRating > 0)
                                <span class="bg-purple-100 text-purple-600 text-xs px-2 py-1 rounded-full">
                                    {{ count($selectedCategories) + count($selectedTypes) + ($minRating > 0 ? 1 : 0) }}
                                </span>
                            @endif
                        </button>
                        
                        <select wire:model.live="sortBy" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            <option value="featured">Featured</option>
                            <option value="latest">Latest</option>
                            <option value="price_low">Price: Low to High</option>
                            <option value="price_high">Price: High to Low</option>
                            <option value="popular">Most Popular</option>
                            <option value="rating">Highest Rated</option>
                        </select>

                        <button wire:click="toggleViewMode" class="p-2 border border-gray-300 rounded-lg hover:border-purple-600 hover:text-purple-600 transition-colors">
                            <i class="fas {{ $viewMode === 'grid' ? 'fa-list' : 'fa-th-large' }}"></i>
                        </button>
                    </div>
                </div>

                <!-- Filters Panel -->
                @if($showFilters)
                    <div class="bg-gray-50 rounded-lg p-6 mb-8 border border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <!-- Categories -->
                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-2">Categories</label>
                                <div class="space-y-2 max-h-40 overflow-y-auto">
                                    @foreach($allCategories as $category)
                                        <label class="flex items-center">
                                            <input type="checkbox" 
                                                   wire:model.live="selectedCategories" 
                                                   value="{{ $category->id }}"
                                                   class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                            <span class="ml-2 text-sm text-gray-700">{{ $category->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Types -->
                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-2">Product Type</label>
                                <div class="space-y-2">
                                    @foreach($itemTypes as $key => $label)
                                        <label class="flex items-center">
                                            <input type="checkbox" 
                                                   wire:model.live="selectedTypes" 
                                                   value="{{ $key }}"
                                                   class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                            <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Price Range -->
                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-2">Price Range</label>
                                <div class="space-y-2">
                                    <div>
                                        <label class="block text-xs text-gray-600">Min Price (₦)</label>
                                        <input type="number" 
                                               wire:model.live.debounce.500ms="priceMin" 
                                               min="0" 
                                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600">Max Price (₦)</label>
                                        <input type="number" 
                                               wire:model.live.debounce.500ms="priceMax" 
                                               min="0" 
                                               class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                    </div>
                                </div>
                            </div>

                            <!-- Rating -->
                            <div>
                                <label class="block text-sm font-medium text-gray-900 mb-2">Minimum Rating</label>
                                <div class="space-y-2">
                                    @for($i = 5; $i >= 1; $i--)
                                        <label class="flex items-center">
                                            <input type="radio" 
                                                   wire:model.live="minRating" 
                                                   value="{{ $i }}" 
                                                   name="rating"
                                                   class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                            <div class="ml-2 flex items-center">
                                                @for($j = 1; $j <= 5; $j++)
                                                    <i class="fas fa-star text-sm {{ $j <= $i ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                                @endfor
                                                <span class="ml-1 text-sm text-gray-700">& up</span>
                                            </div>
                                        </label>
                                    @endfor
                                    <label class="flex items-center">
                                        <input type="radio" 
                                               wire:model.live="minRating" 
                                               value="0" 
                                               name="rating"
                                               class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                        <span class="ml-2 text-sm text-gray-700">All Ratings</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between mt-6 pt-6 border-t border-gray-200">
                            <button wire:click="clearFilters" class="text-purple-600 hover:text-purple-700 font-medium text-sm">
                                Clear All Filters
                            </button>
                            <button wire:click="toggleFilters" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors text-sm">
                                Apply Filters
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </section>

        <!-- Products Grid/List -->
        <section class="py-8 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                @if($items->count() > 0)
                    @if($viewMode === 'grid')
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            @foreach($items as $item)
                                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200 group cursor-pointer" 
                                     wire:click="viewProduct({{ $item->id }})">
                                    <div class="aspect-video bg-gray-200 overflow-hidden">
                                        @if($item->getPrimaryImage())
                                            <img src="{{ Storage::url($item->getPrimaryImage()) }}" 
                                                 alt="{{ $item->title }}" 
                                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-purple-100">
                                                <i class="fas fa-book text-purple-400 text-2xl"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="p-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-xs font-medium text-purple-600 bg-purple-100 px-2 py-1 rounded">
                                                {{ $item->type_name }}
                                            </span>
                                            @if($item->hasDiscount())
                                                <span class="text-xs font-medium text-red-600 bg-red-100 px-2 py-1 rounded">
                                                    {{ $item->getDiscountPercentage() }}% OFF
                                                </span>
                                            @endif
                                        </div>
                                        <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2 group-hover:text-purple-600 transition-colors text-sm">
                                            {{ $item->title }}
                                        </h3>
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <div class="w-6 h-6 bg-purple-100 rounded-full flex items-center justify-center">
                                                    <span class="text-xs font-semibold text-purple-600">
                                                        {{ substr($item->vendor->name, 0, 1) }}
                                                    </span>
                                                </div>
                                                <span class="text-xs text-gray-700 truncate">{{ $item->vendor->name }}</span>
                                            </div>
                                            <div class="text-right">
                                                @if($item->hasDiscount())
                                                    <div class="text-xs text-gray-500 line-through">{{ $item->getFormattedOriginalPrice() }}</div>
                                                @endif
                                                <div class="font-bold text-purple-600 text-sm">{{ $item->getFormattedPrice() }}</div>
                                            </div>
                                        </div>
                                        @if($item->average_rating > 0)
                                            <div class="flex items-center mt-2 pt-2 border-t border-gray-100">
                                                <div class="flex items-center space-x-1">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star text-xs {{ $i <= $item->average_rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                                    @endfor
                                                </div>
                                                <span class="text-xs text-gray-600 ml-2">({{ $item->reviews_count }})</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <!-- List View -->
                        <div class="space-y-4">
                            @foreach($items as $item)
                                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200 cursor-pointer"
                                     wire:click="viewProduct({{ $item->id }})">
                                    <div class="flex items-start space-x-4">
                                        <div class="w-24 h-24 bg-gray-200 rounded-lg overflow-hidden flex-shrink-0">
                                            @if($item->getPrimaryImage())
                                                <img src="{{ Storage::url($item->getPrimaryImage()) }}" 
                                                     alt="{{ $item->title }}" 
                                                     class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center bg-purple-100">
                                                    <i class="fas fa-book text-purple-400"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-start justify-between">
                                                <div>
                                                    <div class="flex items-center space-x-2 mb-2">
                                                        <span class="text-xs font-medium text-purple-600 bg-purple-100 px-2 py-1 rounded">
                                                            {{ $item->type_name }}
                                                        </span>
                                                        @if($item->hasDiscount())
                                                            <span class="text-xs font-medium text-red-600 bg-red-100 px-2 py-1 rounded">
                                                                {{ $item->getDiscountPercentage() }}% OFF
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <h3 class="text-lg font-semibold text-gray-900 mb-2 hover:text-purple-600 transition-colors">
                                                        {{ $item->title }}
                                                    </h3>
                                                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $item->short_description }}</p>
                                                    <div class="flex items-center space-x-4">
                                                        <div class="flex items-center space-x-2">
                                                            <div class="w-6 h-6 bg-purple-100 rounded-full flex items-center justify-center">
                                                                <span class="text-xs font-semibold text-purple-600">
                                                                    {{ substr($item->vendor->name, 0, 1) }}
                                                                </span>
                                                            </div>
                                                            <span class="text-sm text-gray-700">{{ $item->vendor->name }}</span>
                                                        </div>
                                                        @if($item->average_rating > 0)
                                                            <div class="flex items-center space-x-1">
                                                                @for($i = 1; $i <= 5; $i++)
                                                                    <i class="fas fa-star text-xs {{ $i <= $item->average_rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                                                @endfor
                                                                <span class="text-sm text-gray-600 ml-1">({{ $item->reviews_count }})</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    @if($item->hasDiscount())
                                                        <div class="text-sm text-gray-500 line-through">{{ $item->getFormattedOriginalPrice() }}</div>
                                                    @endif
                                                    <div class="text-xl font-bold text-purple-600">{{ $item->getFormattedPrice() }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $items->links() }}
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-search text-gray-400 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No products found</h3>
                        <p class="text-gray-600 mb-4">Try adjusting your search or filter criteria</p>
                        <button wire:click="clearFilters" class="text-purple-600 hover:text-purple-700 font-medium">
                            Clear all filters
                        </button>
                    </div>
                @endif
            </div>
        </section>

    @elseif($activeView === 'product' && $selectedProduct)
        <!-- Product Detail View -->
        @include('livewire.marketplace.partials.product-detail')
    @endif

    <!-- Login Modal -->
    @if($showLoginModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md mx-4"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-lock text-purple-600 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Sign in required</h3>
                    <p class="text-gray-600 mb-6">
                        @if($loginAction === 'purchase')
                            Please sign in to purchase this product and access exclusive content.
                        @elseif($loginAction === 'wishlist')
                            Please sign in to add items to your wishlist.
                        @elseif($loginAction === 'review')
                            Please sign in to leave a review.
                        @else
                            Please sign in to continue with this action.
                        @endif
                    </p>
                    
                    <div class="space-y-3">
                        <button wire:click="redirectToLogin" 
                                class="w-full bg-purple-600 text-white px-4 py-3 rounded-lg hover:bg-purple-700 transition-colors font-medium">
                            Sign In
                        </button>
                        <button wire:click="redirectToRegister" 
                                class="w-full border border-gray-300 text-gray-700 px-4 py-3 rounded-lg hover:border-purple-600 hover:text-purple-600 transition-colors font-medium">
                            Create Account
                        </button>
                        <button wire:click="closeLoginModal" 
                                class="w-full text-gray-500 hover:text-gray-700 px-4 py-2 text-sm transition-colors">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>