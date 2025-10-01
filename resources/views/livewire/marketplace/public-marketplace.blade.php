{{-- resources/views/livewire/marketplace/public-marketplace.blade.php --}}
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-300" x-data="{ mobileFiltersOpen: false, mobileMenuOpen: false }">
    @if($activeView === 'browse')
        <!-- Hero Section -->
        <section class="bg-white dark:bg-gray-800 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <h1 class="text-4xl lg:text-6xl font-bold text-gray-900 dark:text-white leading-tight transition-colors duration-300">
                            Learn from the <span class="text-purple-600 dark:text-purple-400">best</span> instructors worldwide
                        </h1>
                        <p class="mt-6 text-xl text-gray-600 dark:text-gray-300 leading-relaxed transition-colors duration-300">
                            Discover high-quality courses, digital resources, and services from expert instructors. 
                            Build your skills with trusted content at affordable prices.
                        </p>
                        <div class="mt-8 flex flex-col sm:flex-row gap-4">
                            @guest
                                <button wire:click="redirectToRegister" 
                                        class="bg-purple-600 dark:bg-purple-500 text-white px-8 py-4 rounded-lg hover:bg-purple-700 dark:hover:bg-purple-600 transition-colors font-semibold text-lg">
                                    Start Learning Today
                                </button>
                                <button wire:click="redirectToLogin" 
                                        class="border-2 border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 px-8 py-4 rounded-lg hover:border-purple-600 dark:hover:border-purple-400 hover:text-purple-600 dark:hover:text-purple-400 transition-colors font-semibold text-lg">
                                    Sign In
                                </button>
                            @endguest
                        </div>
                        
                        <!-- Stats -->
                        <div class="mt-12 grid grid-cols-3 gap-8">
                            <div>
                                <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ number_format($stats['total_products']) }}+</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Quality Products</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ number_format($stats['total_vendors']) }}+</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Expert Instructors</div>
                            </div>
                            <div>
                                <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ number_format($stats['average_rating'], 1) }}/5</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">Average Rating</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Hero Image/Illustration -->
                    <div class="relative">
                        <div class="aspect-square bg-purple-100 dark:bg-purple-900 rounded-2xl overflow-hidden transition-colors duration-300">
                            <div class="h-full w-full bg-purple-200 dark:bg-purple-800 flex items-center justify-center transition-colors duration-300">
                                <i class="fas fa-graduation-cap text-purple-500 dark:text-purple-400 text-8xl"></i>
                            </div>
                        </div>
                        <!-- Floating cards -->
                        <div class="absolute -top-4 -left-4 bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4 border border-gray-200 dark:border-gray-700 transition-colors duration-300">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center transition-colors duration-300">
                                    <i class="fas fa-play text-green-600 dark:text-green-400 text-sm"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">25k+ Videos</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">High Quality</div>
                                </div>
                            </div>
                        </div>
                        <div class="absolute -bottom-4 -right-4 bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4 border border-gray-200 dark:border-gray-700 transition-colors duration-300">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center transition-colors duration-300">
                                    <i class="fas fa-certificate text-blue-600 dark:text-blue-400 text-sm"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">Certificates</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">On Completion</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Featured Products -->
        @if($featuredItems->count() > 0)
        <section class="py-16 bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white transition-colors duration-300">Featured Products</h2>
                    <p class="mt-4 text-lg text-gray-600 dark:text-gray-300 transition-colors duration-300">Hand-picked premium content from our top instructors</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($featuredItems as $item)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md dark:hover:shadow-gray-900/20 transition-all duration-200 group cursor-pointer" 
                             wire:click="viewProduct({{ $item->id }})">
                            <div class="aspect-video bg-gray-200 dark:bg-gray-700 overflow-hidden transition-colors duration-300">
                                @if($item->getPrimaryImage())
                                    <img src="{{ Storage::url($item->getPrimaryImage()) }}" 
                                         alt="{{ $item->title }}" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-purple-100 dark:bg-purple-900 transition-colors duration-300">
                                        <i class="fas fa-book text-purple-400 dark:text-purple-500 text-2xl"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-medium text-purple-600 dark:text-purple-400 bg-purple-100 dark:bg-purple-900 px-2 py-1 rounded transition-colors duration-300">
                                        {{ $item->type_name }}
                                    </span>
                                    @if($item->hasDiscount())
                                        <span class="text-xs font-medium text-red-600 dark:text-red-400 bg-red-100 dark:bg-red-900 px-2 py-1 rounded transition-colors duration-300">
                                            {{ $item->getDiscountPercentage() }}% OFF
                                        </span>
                                    @endif
                                </div>
                                <h3 class="font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">
                                    {{ $item->title }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3 line-clamp-2 transition-colors duration-300">{{ $item->short_description }}</p>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center transition-colors duration-300">
                                            <span class="text-xs font-semibold text-purple-600 dark:text-purple-400">
                                                {{ substr($item->vendor->name, 0, 2) }}
                                            </span>
                                        </div>
                                        <span class="text-sm text-gray-700 dark:text-gray-300 transition-colors duration-300">{{ $item->vendor->name }}</span>
                                    </div>
                                    <div class="text-right">
                                        @if($item->hasDiscount())
                                            <div class="text-xs text-gray-500 dark:text-gray-400 line-through transition-colors duration-300">{{ $item->getFormattedOriginalPrice() }}</div>
                                        @endif
                                        <div class="font-bold text-purple-600 dark:text-purple-400 transition-colors duration-300">{{ $item->getFormattedPrice() }}</div>
                                    </div>
                                </div>
                                @if($item->average_rating > 0)
                                    <div class="flex items-center mt-3 pt-3 border-t border-gray-100 dark:border-gray-700 transition-colors duration-300">
                                        <div class="flex items-center space-x-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star text-xs {{ $i <= $item->average_rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"></i>
                                            @endfor
                                        </div>
                                        <span class="text-sm text-gray-600 dark:text-gray-400 ml-2 transition-colors duration-300">({{ $item->reviews_count }})</span>
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
        <section class="py-16 bg-white dark:bg-gray-800 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white transition-colors duration-300">Popular Categories</h2>
                    <p class="mt-4 text-lg text-gray-600 dark:text-gray-300 transition-colors duration-300">Explore our most popular learning categories</p>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                    @foreach($popularCategories as $category)
                        <button wire:click="viewCategory({{ $category->id }})" 
                                class="group p-6 bg-gray-50 dark:bg-gray-700 rounded-xl hover:bg-purple-50 dark:hover:bg-purple-900 transition-colors duration-200 text-center">
                            <div class="w-12 h-12 bg-white dark:bg-gray-600 rounded-lg mx-auto mb-4 flex items-center justify-center shadow-sm group-hover:shadow-md transition-all duration-200">
                                <i class="{{ $category->icon ?? 'fas fa-tag' }} text-purple-600 dark:text-purple-400 text-xl"></i>
                            </div>
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-1 transition-colors duration-300">{{ $category->name }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 transition-colors duration-300">{{ $category->items_count }} items</p>
                        </button>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Top Vendors Section -->
        @if($topVendors->count() > 0)
        <section class="py-16 bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white transition-colors duration-300">Top Instructors</h2>
                    <p class="mt-4 text-lg text-gray-600 dark:text-gray-300 transition-colors duration-300">Learn from the best in the industry</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($topVendors as $vendor)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 text-center hover:shadow-md dark:hover:shadow-gray-900/20 transition-all duration-200 cursor-pointer"
                             wire:click="viewVendor({{ $vendor->id }})">
                            <div class="w-20 h-20 bg-purple-100 dark:bg-purple-900 rounded-full mx-auto mb-4 flex items-center justify-center transition-colors duration-300">
                                <span class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                                    {{ substr($vendor->name, 0, 2) }}
                                </span>
                            </div>
                            <h3 class="font-semibold text-gray-900 dark:text-white mb-1 transition-colors duration-300">{{ $vendor->name }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-3 transition-colors duration-300">{{ $vendor->published_items_count }} Products</p>
                            @if($vendor->avg_rating > 0)
                                <div class="flex items-center justify-center space-x-1 mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star text-sm {{ $i <= $vendor->avg_rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"></i>
                                    @endfor
                                    <span class="text-sm text-gray-600 dark:text-gray-400 ml-1 transition-colors duration-300">({{ number_format($vendor->avg_rating, 1) }})</span>
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
        <section class="py-16 bg-white dark:bg-gray-800 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white transition-colors duration-300">Trending This Week</h2>
                    <p class="mt-4 text-lg text-gray-600 dark:text-gray-300 transition-colors duration-300">Most popular products from the past 7 days</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($trendingItems as $item)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md dark:hover:shadow-gray-900/20 transition-all duration-200 group cursor-pointer" 
                             wire:click="viewProduct({{ $item->id }})">
                            <div class="aspect-video bg-gray-200 dark:bg-gray-700 overflow-hidden relative transition-colors duration-300">
                                @if($item->getPrimaryImage())
                                    <img src="{{ Storage::url($item->getPrimaryImage()) }}" 
                                         alt="{{ $item->title }}" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-purple-100 dark:bg-purple-900 transition-colors duration-300">
                                        <i class="fas fa-book text-purple-400 dark:text-purple-500 text-2xl"></i>
                                    </div>
                                @endif
                                <div class="absolute top-3 left-3">
                                    <span class="bg-red-500 dark:bg-red-600 text-white text-xs px-2 py-1 rounded-full font-medium">
                                        <i class="fas fa-fire mr-1"></i>Trending
                                    </span>
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-medium text-purple-600 dark:text-purple-400 bg-purple-100 dark:bg-purple-900 px-2 py-1 rounded transition-colors duration-300">
                                        {{ $item->type_name }}
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 transition-colors duration-300">{{ $item->views_count }} views</span>
                                </div>
                                <h3 class="font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">
                                    {{ $item->title }}
                                </h3>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center transition-colors duration-300">
                                            <span class="text-xs font-semibold text-purple-600 dark:text-purple-400">
                                                {{ substr($item->vendor->name, 0, 1) }}
                                            </span>
                                        </div>
                                        <span class="text-sm text-gray-700 dark:text-gray-300 transition-colors duration-300">{{ $item->vendor->name }}</span>
                                    </div>
                                    <div class="font-bold text-purple-600 dark:text-purple-400 transition-colors duration-300">{{ $item->getFormattedPrice() }}</div>
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
        <section class="py-8 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Breadcrumb -->
                <nav class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400 mb-6 transition-colors duration-300">
                    <button wire:click="backToBrowse" class="hover:text-purple-600 dark:hover:text-purple-400 transition-colors">Home</button>
                    <i class="fas fa-chevron-right text-xs"></i>
                    @if($activeView === 'search')
                        <span class="text-gray-900 dark:text-white font-medium transition-colors duration-300">Search Results</span>
                    @elseif($activeView === 'category' && $selectedCategory)
                        <span class="text-gray-900 dark:text-white font-medium transition-colors duration-300">{{ $selectedCategory->name }}</span>
                    @elseif($activeView === 'vendor' && $selectedVendor)
                        <span class="text-gray-900 dark:text-white font-medium transition-colors duration-300">{{ $selectedVendor->name }}'s Products</span>
                    @endif
                </nav>

                <!-- Header -->
                <div class="flex flex-col lg:flex-row lg:items-center justify-between mb-8">
                    <div>
                        @if($activeView === 'search')
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white transition-colors duration-300">
                                @if($searchTerm)
                                    Results for "{{ $searchTerm }}"
                                @else
                                    All Products
                                @endif
                            </h1>
                            <p class="text-gray-600 dark:text-gray-300 mt-1 transition-colors duration-300">{{ $items->total() }} products found</p>
                        @elseif($activeView === 'category' && $selectedCategory)
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white transition-colors duration-300">{{ $selectedCategory->name }}</h1>
                            <p class="text-gray-600 dark:text-gray-300 mt-1 transition-colors duration-300">{{ $selectedCategory->description ?? $items->total() . ' products available' }}</p>
                        @elseif($activeView === 'vendor' && $selectedVendor)
                            <div class="flex items-center space-x-4">
                                <div class="w-16 h-16 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center transition-colors duration-300">
                                    <span class="text-xl font-bold text-purple-600 dark:text-purple-400">
                                        {{ substr($selectedVendor->name, 0, 2) }}
                                    </span>
                                </div>
                                <div>
                                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white transition-colors duration-300">{{ $selectedVendor->name }}</h1>
                                    <p class="text-gray-600 dark:text-gray-300 transition-colors duration-300">{{ $items->total() }} products available</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Controls -->
                    <div class="flex items-center space-x-4 mt-6 lg:mt-0">
                        <button wire:click="toggleFilters" 
                                class="flex items-center space-x-2 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:border-purple-600 dark:hover:border-purple-400 hover:text-purple-600 dark:hover:text-purple-400 text-gray-700 dark:text-gray-300 transition-colors duration-300">
                            <i class="fas fa-filter"></i>
                            <span>Filters</span>
                            @if(count($selectedCategories) > 0 || count($selectedTypes) > 0 || $minRating > 0)
                                <span class="bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-400 text-xs px-2 py-1 rounded-full transition-colors duration-300">
                                    {{ count($selectedCategories) + count($selectedTypes) + ($minRating > 0 ? 1 : 0) }}
                                </span>
                            @endif
                        </button>
                        
                        <select wire:model.live="sortBy" class="border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-300">
                            <option value="featured">Featured</option>
                            <option value="latest">Latest</option>
                            <option value="price_low">Price: Low to High</option>
                            <option value="price_high">Price: High to Low</option>
                            <option value="popular">Most Popular</option>
                            <option value="rating">Highest Rated</option>
                        </select>

                        <button wire:click="toggleViewMode" class="p-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:border-purple-600 dark:hover:border-purple-400 hover:text-purple-600 dark:hover:text-purple-400 text-gray-700 dark:text-gray-300 transition-colors duration-300">
                            <i class="fas {{ $viewMode === 'grid' ? 'fa-list' : 'fa-th-large' }}"></i>
                        </button>
                    </div>
                </div>

                <!-- Filters Panel -->
                @if($showFilters)
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-8 border border-gray-200 dark:border-gray-600 transition-colors duration-300">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <!-- Categories -->
                            <div>
                                <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2 transition-colors duration-300">Categories</label>
                                <div class="space-y-2 max-h-40 overflow-y-auto">
                                    @foreach($allCategories as $category)
                                        <label class="flex items-center">
                                            <input type="checkbox" 
                                                   wire:model.live="selectedCategories" 
                                                   value="{{ $category->id }}"
                                                   class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-purple-600 focus:ring-purple-500">
                                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300 transition-colors duration-300">{{ $category->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Types -->
                            <div>
                                <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2 transition-colors duration-300">Product Type</label>
                                <div class="space-y-2">
                                    @foreach($itemTypes as $key => $label)
                                        <label class="flex items-center">
                                            <input type="checkbox" 
                                                   wire:model.live="selectedTypes" 
                                                   value="{{ $key }}"
                                                   class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-purple-600 focus:ring-purple-500">
                                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300 transition-colors duration-300">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Price Range -->
                            <div>
                                <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2 transition-colors duration-300">Price Range</label>
                                <div class="space-y-2">
                                    <div>
                                        <label class="block text-xs text-gray-600 dark:text-gray-400 transition-colors duration-300">Min Price (₦)</label>
                                        <input type="number" 
                                               wire:model.live.debounce.500ms="priceMin" 
                                               min="0" 
                                               class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-300">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-600 dark:text-gray-400 transition-colors duration-300">Max Price (₦)</label>
                                        <input type="number" 
                                               wire:model.live.debounce.500ms="priceMax" 
                                               min="0" 
                                               class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors duration-300">
                                    </div>
                                </div>
                            </div>

                            <!-- Rating -->
                            <div>
                                <label class="block text-sm font-medium text-gray-900 dark:text-white mb-2 transition-colors duration-300">Minimum Rating</label>
                                <div class="space-y-2">
                                    @for($i = 5; $i >= 1; $i--)
                                        <label class="flex items-center">
                                            <input type="radio" 
                                                   wire:model.live="minRating" 
                                                   value="{{ $i }}" 
                                                   name="rating"
                                                   class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-purple-600 focus:ring-purple-500">
                                            <div class="ml-2 flex items-center">
                                                @for($j = 1; $j <= 5; $j++)
                                                    <i class="fas fa-star text-sm {{ $j <= $i ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"></i>
                                                @endfor
                                                <span class="ml-1 text-sm text-gray-700 dark:text-gray-300 transition-colors duration-300">& up</span>
                                            </div>
                                        </label>
                                    @endfor
                                    <label class="flex items-center">
                                        <input type="radio" 
                                               wire:model.live="minRating" 
                                               value="0" 
                                               name="rating"
                                               class="rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-purple-600 focus:ring-purple-500">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300 transition-colors duration-300">All Ratings</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between mt-6 pt-6 border-t border-gray-200 dark:border-gray-600 transition-colors duration-300">
                            <button wire:click="clearFilters" class="text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 font-medium text-sm transition-colors duration-300">
                                Clear All Filters
                            </button>
                            <button wire:click="toggleFilters" class="bg-purple-600 dark:bg-purple-500 text-white px-4 py-2 rounded-lg hover:bg-purple-700 dark:hover:bg-purple-600 transition-colors text-sm">
                                Apply Filters
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </section>

        <!-- Products Grid/List -->
        <section class="py-8 bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                @if($items->count() > 0)
                    @if($viewMode === 'grid')
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            @foreach($items as $item)
                                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md dark:hover:shadow-gray-900/20 transition-all duration-200 group cursor-pointer" 
                                     wire:click="viewProduct({{ $item->id }})">
                                    <div class="aspect-video bg-gray-200 dark:bg-gray-700 overflow-hidden transition-colors duration-300">
                                        @if($item->getPrimaryImage())
                                            <img src="{{ Storage::url($item->getPrimaryImage()) }}" 
                                                 alt="{{ $item->title }}" 
                                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-purple-100 dark:bg-purple-900 transition-colors duration-300">
                                                <i class="fas fa-book text-purple-400 dark:text-purple-500 text-2xl"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="p-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-xs font-medium text-purple-600 dark:text-purple-400 bg-purple-100 dark:bg-purple-900 px-2 py-1 rounded transition-colors duration-300">
                                                {{ $item->type_name }}
                                            </span>
                                            @if($item->hasDiscount())
                                                <span class="text-xs font-medium text-red-600 dark:text-red-400 bg-red-100 dark:bg-red-900 px-2 py-1 rounded transition-colors duration-300">
                                                    {{ $item->getDiscountPercentage() }}% OFF
                                                </span>
                                            @endif
                                        </div>
                                        <h3 class="font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors text-sm">
                                            {{ $item->title }}
                                        </h3>
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <div class="w-6 h-6 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center transition-colors duration-300">
                                                    <span class="text-xs font-semibold text-purple-600 dark:text-purple-400">
                                                        {{ substr($item->vendor->name, 0, 1) }}
                                                    </span>
                                                </div>
                                                <span class="text-xs text-gray-700 dark:text-gray-300 truncate transition-colors duration-300">{{ $item->vendor->name }}</span>
                                            </div>
                                            <div class="text-right">
                                                @if($item->hasDiscount())
                                                    <div class="text-xs text-gray-500 dark:text-gray-400 line-through transition-colors duration-300">{{ $item->getFormattedOriginalPrice() }}</div>
                                                @endif
                                                <div class="font-bold text-purple-600 dark:text-purple-400 text-sm transition-colors duration-300">{{ $item->getFormattedPrice() }}</div>
                                            </div>
                                        </div>
                                        @if($item->average_rating > 0)
                                            <div class="flex items-center mt-2 pt-2 border-t border-gray-100 dark:border-gray-700 transition-colors duration-300">
                                                <div class="flex items-center space-x-1">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star text-xs {{ $i <= $item->average_rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"></i>
                                                    @endfor
                                                </div>
                                                <span class="text-xs text-gray-600 dark:text-gray-400 ml-2 transition-colors duration-300">({{ $item->reviews_count }})</span>
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
                                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 hover:shadow-md dark:hover:shadow-gray-900/20 transition-all duration-200 cursor-pointer"
                                     wire:click="viewProduct({{ $item->id }})">
                                    <div class="flex items-start space-x-4">
                                        <div class="w-24 h-24 bg-gray-200 dark:bg-gray-700 rounded-lg overflow-hidden flex-shrink-0 transition-colors duration-300">
                                            @if($item->getPrimaryImage())
                                                <img src="{{ Storage::url($item->getPrimaryImage()) }}" 
                                                     alt="{{ $item->title }}" 
                                                     class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center bg-purple-100 dark:bg-purple-900 transition-colors duration-300">
                                                    <i class="fas fa-book text-purple-400 dark:text-purple-500"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-start justify-between">
                                                <div>
                                                    <div class="flex items-center space-x-2 mb-2">
                                                        <span class="text-xs font-medium text-purple-600 dark:text-purple-400 bg-purple-100 dark:bg-purple-900 px-2 py-1 rounded transition-colors duration-300">
                                                            {{ $item->type_name }}
                                                        </span>
                                                        @if($item->hasDiscount())
                                                            <span class="text-xs font-medium text-red-600 dark:text-red-400 bg-red-100 dark:bg-red-900 px-2 py-1 rounded transition-colors duration-300">
                                                                {{ $item->getDiscountPercentage() }}% OFF
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 hover:text-purple-600 dark:hover:text-purple-400 transition-colors">
                                                        {{ $item->title }}
                                                    </h3>
                                                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-3 line-clamp-2 transition-colors duration-300">{{ $item->short_description }}</p>
                                                    <div class="flex items-center space-x-4">
                                                        <div class="flex items-center space-x-2">
                                                            <div class="w-6 h-6 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center transition-colors duration-300">
                                                                <span class="text-xs font-semibold text-purple-600 dark:text-purple-400">
                                                                    {{ substr($item->vendor->name, 0, 1) }}
                                                                </span>
                                                            </div>
                                                            <span class="text-sm text-gray-700 dark:text-gray-300 transition-colors duration-300">{{ $item->vendor->name }}</span>
                                                        </div>
                                                        @if($item->average_rating > 0)
                                                            <div class="flex items-center space-x-1">
                                                                @for($i = 1; $i <= 5; $i++)
                                                                    <i class="fas fa-star text-xs {{ $i <= $item->average_rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"></i>
                                                                @endfor
                                                                <span class="text-sm text-gray-600 dark:text-gray-400 ml-1 transition-colors duration-300">({{ $item->reviews_count }})</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    @if($item->hasDiscount())
                                                        <div class="text-sm text-gray-500 dark:text-gray-400 line-through transition-colors duration-300">{{ $item->getFormattedOriginalPrice() }}</div>
                                                    @endif
                                                    <div class="text-xl font-bold text-purple-600 dark:text-purple-400 transition-colors duration-300">{{ $item->getFormattedPrice() }}</div>
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
                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4 transition-colors duration-300">
                            <i class="fas fa-search text-gray-400 dark:text-gray-500 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2 transition-colors duration-300">No products found</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4 transition-colors duration-300">Try adjusting your search or filter criteria</p>
                        <button wire:click="clearFilters" class="text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 font-medium transition-colors duration-300">
                            Clear all filters
                        </button>
                    </div>
                @endif
            </div>
        </section>

    @elseif($activeView === 'product' && $selectedProduct)
        <!-- Product Detail View -->
        @include('livewire.marketplace.public.product-detail')
    @endif

    <!-- Login Modal -->
    @if($showLoginModal)
        <div class="fixed inset-0 bg-gray-600 dark:bg-gray-900 bg-opacity-50 dark:bg-opacity-75 flex items-center justify-center z-50 transition-colors duration-300" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-6 w-full max-w-md mx-4 transition-colors duration-300"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                
                <div class="text-center">
                    <div class="w-16 h-16 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mx-auto mb-4 transition-colors duration-300">
                        <i class="fas fa-lock text-purple-600 dark:text-purple-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 transition-colors duration-300">Sign in required</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6 transition-colors duration-300">
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
                                class="w-full bg-purple-600 dark:bg-purple-500 text-white px-4 py-3 rounded-lg hover:bg-purple-700 dark:hover:bg-purple-600 transition-colors font-medium">
                            Sign In
                        </button>
                        <button wire:click="redirectToRegister" 
                                class="w-full border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 px-4 py-3 rounded-lg hover:border-purple-600 dark:hover:border-purple-400 hover:text-purple-600 dark:hover:text-purple-400 transition-colors font-medium">
                            Create Account
                        </button>
                        <button wire:click="closeLoginModal" 
                                class="w-full text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 px-4 py-2 text-sm transition-colors">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>