<div class="space-y-6">

    <!-- Internal Navigation Tabs -->
    <div class="bg-surface dark:bg-gray-800 rounded-lg shadow-sm border border-line dark:border-gray-700 p-4 transition-colors duration-300">
        <nav class="flex space-x-4">
            <button wire:click="showBrowse"
                    class="{{ $currentView === 'browse' ? 'bg-purple-100 dark:bg-action text-brand dark:text-brand border-brand dark:border-purple-700' : 'text-gray-600 dark:text-content-muted hover:text-content dark:hover:text-white' }} px-4 py-2 rounded-lg border transition-colors duration-300 flex items-center">
                <i class="fas fa-search mr-2"></i>
                Browse Items
            </button>

            <button wire:click="showCategories"
                    class="{{ $currentView === 'categories' ? 'bg-purple-100 dark:bg-action text-brand dark:text-brand border-brand dark:border-purple-700' : 'text-gray-600 dark:text-content-muted hover:text-content dark:hover:text-white' }} px-4 py-2 rounded-lg border transition-colors duration-300 flex items-center">
                <i class="fas fa-tags mr-2"></i>
                Categories
            </button>

            @if($currentView === 'product-details')
                <button wire:click="backToBrowse"
                        class="text-content-muted dark:text-content-muted hover:text-content dark:hover:text-white px-4 py-2 rounded-lg border border-line dark:border-gray-700 transition-colors duration-300 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Browse
                </button>
                <div class="px-4 py-2 bg-brand/10 dark:bg-action text-brand dark:text-brand rounded-lg border border-brand dark:border-brand flex items-center transition-colors duration-300">
                    <i class="fas fa-box-open mr-2"></i>
                    Product Details
                </div>
            @endif
        </nav>
    </div>

    <!-- Browse View -->
    @if($currentView === 'browse')
        <!-- Search and Filters -->
        <div class="bg-surface dark:bg-gray-800 rounded-lg shadow-sm border border-line dark:border-gray-700 p-6 transition-colors duration-300">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-content dark:text-gray-300 mb-2 transition-colors duration-300">Search</label>
                    <div class="relative">
                        <input aria-label="Search courses, resources, services" wire:model.live.debounce.300ms="search"
                               type="text"
                               placeholder="Search courses, resources, services..."
                               class="w-full pl-10 pr-4 py-2 border border-line dark:border-gray-600 bg-surface dark:bg-gray-700 text-content dark:text-white rounded-lg focus:ring-brand/30 focus:border-brand transition-colors duration-300">
                        <i class="fas fa-search absolute left-3 top-3 text-content-muted dark:text-content-muted"></i>
                        @if($search)
                            <button wire:click="$set('search', '')" class="absolute right-3 top-3 text-content-muted dark:text-content-muted hover:text-content-muted dark:hover:text-content-muted transition-colors duration-300" aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-content dark:text-gray-300 mb-2 transition-colors duration-300">Type</label>
                    <select aria-label="Type" wire:model.live="type" class="w-full py-2 px-3 border border-line dark:border-gray-600 bg-surface dark:bg-gray-700 text-content dark:text-white rounded-lg focus:ring-brand/30 focus:border-brand transition-colors duration-300">
                        <option value="">All Types</option>
                        @isset($types)
                            @foreach($types as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        @endisset
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-content dark:text-gray-300 mb-2 transition-colors duration-300">Sort By</label>
                    <select aria-label="Sort by" wire:model.live="sortBy" class="w-full py-2 px-3 border border-line dark:border-gray-600 bg-surface dark:bg-gray-700 text-content dark:text-white rounded-lg focus:ring-brand/30 focus:border-brand transition-colors duration-300">
                        <option value="created_at">Newest</option>
                        <option value="price">Price: Low to High</option>
                        <option value="price,desc">Price: High to Low</option>
                        <option value="average_rating">Highest Rated</option>
                        <option value="sales_count">Best Selling</option>
                    </select>
                </div>
            </div>

            <!-- Active Filters -->
            @if($search || $type || $category || $minPrice || $maxPrice)
                <div class="mt-4 pt-4 border-t border-line dark:border-gray-700 transition-colors duration-300">
                    <div class="flex items-center justify-between">
                        <div class="flex flex-wrap gap-2">
                            @if($search)
                                <span class="inline-flex items-center px-3 py-1 bg-brand/10 dark:bg-action text-brand dark:text-brand text-sm rounded-full transition-colors duration-300">
                                    Search: "{{ $search }}"
                                    <button wire:click="$set('search', '')" class="ml-2 hover:text-brand dark:hover:text-brand transition-colors duration-300" aria-label="Close">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                </span>
                            @endif
                            @if($type)
                                <span class="inline-flex items-center px-3 py-1 bg-brand/10 dark:bg-action text-brand dark:text-brand text-sm rounded-full transition-colors duration-300">
                                    Type: {{ $types[$type] ?? $type }}
                                    <button wire:click="$set('type', '')" class="ml-2 hover:text-brand dark:hover:text-brand transition-colors duration-300" aria-label="Close">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                </span>
                            @endif
                            @if($category)
                                <span class="inline-flex items-center px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-300 text-sm rounded-full transition-colors duration-300">
                                    Category
                                    <button wire:click="$set('category', '')" class="ml-2 hover:text-green-600 dark:hover:text-green-400 transition-colors duration-300" aria-label="Close">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                </span>
                            @endif
                        </div>
                        <button wire:click="clearFilters" class="text-content-muted dark:text-content-muted hover:text-content dark:hover:text-gray-300 text-sm transition-colors duration-300">
                            Clear All
                        </button>
                    </div>
                </div>
            @endif
        </div>

        <!-- Featured Items -->
        @isset($featuredItems)
            @if($featuredItems->count() > 0)
                <div class="bg-surface dark:bg-gray-800 rounded-lg shadow-sm border border-line dark:border-gray-700 p-6 transition-colors duration-300">
                    <h2 class="text-xl font-bold text-content dark:text-white mb-4 flex items-center transition-colors duration-300">
                        <i class="fas fa-star text-yellow-500 mr-2"></i>
                        Featured Items
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($featuredItems as $item)
                            <div class="bg-brand-ink     rounded-lg p-4 border border-brand dark:border-brand transition-colors duration-300">
                                @if($item->getPrimaryImage())
                                    <img src="{{ asset('storage/' . $item->getPrimaryImage()) }}"
                                         alt="{{ $item->title }}"
                                         class="w-full h-32 object-cover rounded-lg mb-3">
                                @else
                                    <div class="w-full h-32 bg-gray-200 dark:bg-gray-700 rounded-lg mb-3 flex items-center justify-center transition-colors duration-300">
                                        <i class="fas fa-image text-content-muted dark:text-content-muted text-2xl"></i>
                                    </div>
                                @endif

                                <div class="space-y-2">
                                    <span class="inline-block px-2 py-1 bg-brand/10 dark:bg-action text-brand dark:text-brand text-xs font-medium rounded-full transition-colors duration-300">
                                        {{ $item->type_name }}
                                    </span>
                                    <h3 class="font-semibold text-content dark:text-white line-clamp-2 transition-colors duration-300">{{ $item->title }}</h3>
                                    <p class="text-sm text-content-muted dark:text-content-muted line-clamp-2 transition-colors duration-300">{{ $item->short_description }}</p>
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2">
                                            @if($item->hasDiscount())
                                                <span class="text-lg font-bold text-brand dark:text-brand transition-colors duration-300">{{ $item->getFormattedPrice() }}</span>
                                                <span class="text-sm text-content-muted dark:text-content-muted line-through transition-colors duration-300">{{ $item->getFormattedOriginalPrice() }}</span>
                                            @else
                                                <span class="text-lg font-bold text-brand dark:text-brand transition-colors duration-300">{{ $item->getFormattedPrice() }}</span>
                                            @endif
                                        </div>
                                        <button wire:click="showProductDetails({{ $item->id }})"
                                                class="bg-action dark:bg-action text-action-foreground px-3 py-1 rounded-lg text-sm hover:bg-action dark:hover:bg-action transition-colors duration-300">
                                            View Details
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endisset

        <!-- Main Items Grid -->
        <div class="bg-surface dark:bg-gray-800 rounded-lg shadow-sm border border-line dark:border-gray-700 p-6 transition-colors duration-300">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-content dark:text-white transition-colors duration-300">
                    @isset($items)
                        All Items ({{ $items->total() }})
                    @else
                        All Items
                    @endisset
                </h2>
            </div>

            @isset($items)
                @if($items->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach($items as $item)
                            <div class="bg-surface dark:bg-gray-800 rounded-lg border border-line dark:border-gray-700 overflow-hidden  dark:hover:shadow-gray-900/20 transition-all duration-300">
                                @if($item->getPrimaryImage())
                                    <img src="{{ asset('storage/' . $item->getPrimaryImage()) }}"
                                         alt="{{ $item->title }}"
                                         class="w-full h-40 object-cover">
                                @else
                                    <div class="w-full h-40 bg-surface-muted dark:bg-gray-700 flex items-center justify-center transition-colors duration-300">
                                        <i class="fas fa-image text-content-muted dark:text-content-muted text-3xl"></i>
                                    </div>
                                @endif

                                <div class="p-4 space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="px-2 py-1 bg-{{ $item->type === 'course' ? 'blue' : ($item->type === 'resource' ? 'green' : 'orange') }}-100 dark:bg-{{ $item->type === 'course' ? 'blue' : ($item->type === 'resource' ? 'green' : 'orange') }}-900 text-{{ $item->type === 'course' ? 'blue' : ($item->type === 'resource' ? 'green' : 'orange') }}-800 dark:text-{{ $item->type === 'course' ? 'blue' : ($item->type === 'resource' ? 'green' : 'orange') }}-300 text-xs font-medium rounded-full transition-colors duration-300">
                                            {{ $item->type_name }}
                                        </span>
                                        @if($item->average_rating > 0)
                                            <div class="flex items-center space-x-1">
                                                <i class="fas fa-star text-yellow-400 text-xs"></i>
                                                <span class="text-xs text-content-muted dark:text-content-muted transition-colors duration-300">{{ number_format($item->average_rating, 1) }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <h3 class="font-semibold text-content dark:text-white line-clamp-2 transition-colors duration-300">{{ $item->title }}</h3>
                                    <p class="text-sm text-content-muted dark:text-content-muted line-clamp-2 transition-colors duration-300">{{ $item->short_description }}</p>

                                    <div class="flex items-center text-xs text-content-muted dark:text-content-muted space-x-3 transition-colors duration-300">
                                        <span><i class="fas fa-user mr-1"></i>{{ $item->vendor->name }}</span>
                                        <span><i class="fas fa-eye mr-1"></i>{{ $item->views_count }}</span>
                                    </div>

                                    <div class="flex items-center justify-between pt-2">
                                        <div>
                                            @if($item->hasDiscount())
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-lg font-bold text-brand dark:text-brand transition-colors duration-300">{{ $item->getFormattedPrice() }}</span>
                                                    <span class="text-sm text-content-muted dark:text-content-muted line-through transition-colors duration-300">{{ $item->getFormattedOriginalPrice() }}</span>
                                                </div>
                                            @else
                                                <span class="text-lg font-bold text-brand dark:text-brand transition-colors duration-300">{{ $item->getFormattedPrice() }}</span>
                                            @endif
                                        </div>
                                        <button wire:click="showProductDetails({{ $item->id }})"
                                                class="bg-action dark:bg-action text-action-foreground px-3 py-1 rounded text-sm hover:bg-action dark:hover:bg-action transition-colors duration-300">
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
                        <i class="fas fa-search text-gray-300 dark:text-content-muted text-6xl mb-4"></i>
                        <h3 class="text-lg font-medium text-content dark:text-white mb-2 transition-colors duration-300">No items found</h3>
                        <p class="text-content-muted dark:text-content-muted mb-4 transition-colors duration-300">Try adjusting your search criteria or browse our categories.</p>
                        <button wire:click="showCategories"
                                class="inline-flex items-center px-4 py-2 bg-action dark:bg-action text-action-foreground rounded-lg hover:bg-action dark:hover:bg-action transition-colors duration-300">
                            <i class="fas fa-tags mr-2"></i>
                            Browse Categories
                        </button>
                    </div>
                @endif
            @endisset
        </div>

    <!-- Categories View -->
    @elseif($currentView === 'categories')
        <!-- Category Header -->
        <div class="bg-surface dark:bg-gray-800 rounded-lg shadow-sm border border-line dark:border-gray-700 p-6 transition-colors duration-300">
            <div class="flex flex-col md:flex-row md:items-center justify-between space-y-4 md:space-y-0">
                <div>
                    <h2 class="text-xl font-bold text-content dark:text-white transition-colors duration-300">Browse Categories</h2>
                    <p class="text-content-muted dark:text-content-muted transition-colors duration-300">Discover items by category</p>
                </div>

                @if($canManageCategories)
                    <button wire:click="openCreateCategoryForm"
                            class="inline-flex items-center px-4 py-2 bg-action dark:bg-action text-action-foreground rounded-lg hover:bg-action dark:hover:bg-action transition-colors duration-300">
                        <i class="fas fa-plus mr-2"></i>
                        Add Category
                    </button>
                @endif
            </div>
        </div>

        <!-- Featured Categories -->
        @isset($featuredCategories)
            @if($featuredCategories->count() > 0)
                <div class="bg-surface dark:bg-gray-800 rounded-lg shadow-sm border border-line dark:border-gray-700 p-6 transition-colors duration-300">
                    <h3 class="text-lg font-medium text-content dark:text-white mb-4 flex items-center transition-colors duration-300">
                        <i class="fas fa-star text-yellow-500 mr-2"></i>
                        Featured Categories
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4">
                        @foreach($featuredCategories as $category)
                            <button wire:click="selectCategory({{ $category->id }})"
                                    class="group p-4 bg-surface-muted dark:bg-gray-700 rounded-lg hover:bg-brand/10 dark:hover:bg-action transition-colors duration-300 text-center">
                                <div class="w-12 h-12 mx-auto mb-2 rounded-lg flex items-center justify-center group-hover:bg-purple-200 dark:group-hover:bg-action transition-colors duration-300"
                                     style="background-color: {{ $category->color }}20">
                                    <i class="{{ $category->icon }} text-xl" style="color: {{ $category->color }}"></i>
                                </div>
                                <h4 class="font-medium text-content dark:text-white text-sm transition-colors duration-300">{{ $category->name }}</h4>
                                <p class="text-xs text-content-muted dark:text-content-muted transition-colors duration-300">{{ $category->items_count }} items</p>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif
        @endisset

        <!-- All Categories -->
        <div class="bg-surface dark:bg-gray-800 rounded-lg shadow-sm border border-line dark:border-gray-700 p-6 transition-colors duration-300">
            <h3 class="text-lg font-medium text-content dark:text-white mb-4 transition-colors duration-300">All Categories</h3>

            @isset($categories)
                @if($categories->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($categories as $category)
                            <div class="group relative border border-line dark:border-gray-700 rounded-lg p-4 hover:border-brand dark:hover:border-brand transition-colors duration-300">
                                <!-- Category Link -->
                                <button wire:click="selectCategory({{ $category->id }})" class="block w-full text-left">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0 w-12 h-12 rounded-lg flex items-center justify-center transition-colors duration-300"
                                             style="background-color: {{ $category->color }}20">
                                            <i class="{{ $category->icon }} text-xl" style="color: {{ $category->color }}"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h4 class="font-medium text-content dark:text-white group-hover:text-brand dark:group-hover:text-brand transition-colors duration-300">
                                                {{ $category->name }}
                                                @if($category->is_featured)
                                                    <i class="fas fa-star text-yellow-500 text-xs ml-1"></i>
                                                @endif
                                            </h4>
                                            @if($category->description)
                                                <p class="text-sm text-content-muted dark:text-content-muted line-clamp-2 transition-colors duration-300">{{ $category->description }}</p>
                                            @endif
                                            <div class="flex items-center mt-2 space-x-3 text-xs text-content-muted dark:text-content-muted transition-colors duration-300">
                                                <span><i class="fas fa-box mr-1"></i>{{ $category->items_count }} items</span>
                                                <span class="px-2 py-1 bg-{{ $category->is_active ? 'green' : 'red' }}-100 dark:bg-{{ $category->is_active ? 'green' : 'red' }}-900 text-{{ $category->is_active ? 'green' : 'red' }}-800 dark:text-{{ $category->is_active ? 'green' : 'red' }}-300 rounded-full transition-colors duration-300">
                                                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </button>

                                <!-- Admin Actions -->
                                @if($canManageCategories)
                                    <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <div class="flex space-x-1">
                                            <button wire:click="openEditCategoryForm({{ $category->id }})"
                                                    class="p-1 bg-action dark:bg-action text-action-foreground rounded text-xs hover:bg-action dark:hover:bg-action transition-colors duration-300">
                                                <i class="fas fa-edit"></i>
                                            </button>

                                            <button wire:click="toggleCategoryFeatured({{ $category->id }})"
                                                    class="p-1 bg-{{ $category->is_featured ? 'yellow' : 'gray' }}-600 text-white rounded text-xs hover:bg-{{ $category->is_featured ? 'yellow' : 'gray' }}-700 transition-colors duration-300">
                                                <i class="fas fa-star"></i>
                                            </button>

                                            <button wire:click="toggleCategoryActive({{ $category->id }})"
                                                    class="p-1 bg-{{ $category->is_active ? 'red' : 'green' }}-600 text-white rounded text-xs hover:bg-{{ $category->is_active ? 'red' : 'green' }}-700 transition-colors duration-300">
                                                <i class="fas fa-{{ $category->is_active ? 'eye-slash' : 'eye' }}"></i>
                                            </button>

                                            @if($category->items_count === 0)
                                                <button wire:click="deleteCategory({{ $category->id }})"
                                                        onclick="confirm('Are you sure you want to delete this category?') || event.stopImmediatePropagation()"
                                                        class="p-1 bg-red-600 dark:bg-red-500 text-white rounded text-xs hover:bg-red-700 dark:hover:bg-red-600 transition-colors duration-300">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $categories->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-folder-open text-gray-300 dark:text-content-muted text-6xl mb-4"></i>
                        <h3 class="text-lg font-medium text-content dark:text-white mb-2 transition-colors duration-300">No categories yet</h3>
                        <p class="text-content-muted dark:text-content-muted mb-6 transition-colors duration-300">Categories help organize marketplace items.</p>
                        @if($canManageCategories)
                            <button wire:click="openCreateCategoryForm"
                                    class="inline-flex items-center px-4 py-2 bg-action dark:bg-action text-action-foreground rounded-lg hover:bg-action dark:hover:bg-action transition-colors duration-300">
                                <i class="fas fa-plus mr-2"></i>
                                Create First Category
                            </button>
                        @endif
                    </div>
                @endif
            @endisset
        </div>

    <!-- Product Details View -->
    @elseif($currentView === 'product-details')
        @isset($selectedItem)
            @if($selectedItem)
                <!-- Product Details Content -->
                <div class="bg-surface dark:bg-gray-800 rounded-lg shadow-sm border border-line dark:border-gray-700 p-6 transition-colors duration-300">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Product Images -->
                        <div class="lg:col-span-1">
                            @if($selectedItem->getPrimaryImage())
                                <img src="{{ asset('storage/' . $selectedItem->getPrimaryImage()) }}"
                                     alt="{{ $selectedItem->title }}"
                                     class="w-full h-64 object-cover rounded-lg">
                            @else
                                <div class="w-full h-64 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center transition-colors duration-300">
                                    <i class="fas fa-image text-content-muted dark:text-content-muted text-4xl"></i>
                                </div>
                            @endif

                            <!-- Additional Images -->
                            @if(count($selectedItem->getAllImages()) > 1)
                                <div class="grid grid-cols-3 gap-2 mt-4">
                                    @foreach(array_slice($selectedItem->getAllImages(), 1, 3) as $image)
                                        <img src="{{ asset('storage/' . $image) }}"
                                             alt="Product image"
                                             class="w-full h-20 object-cover rounded">
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Product Info -->
                        <div class="lg:col-span-2 space-y-6">
                            <!-- Header -->
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="px-3 py-1 bg-{{ $selectedItem->type === 'course' ? 'blue' : ($selectedItem->type === 'resource' ? 'green' : 'orange') }}-100 dark:bg-{{ $selectedItem->type === 'course' ? 'blue' : ($selectedItem->type === 'resource' ? 'green' : 'orange') }}-900 text-{{ $selectedItem->type === 'course' ? 'blue' : ($selectedItem->type === 'resource' ? 'green' : 'orange') }}-800 dark:text-{{ $selectedItem->type === 'course' ? 'blue' : ($selectedItem->type === 'resource' ? 'green' : 'orange') }}-300 text-sm font-medium rounded-full transition-colors duration-300">
                                        {{ $selectedItem->type_name }}
                                    </span>
                                    @if($selectedItem->is_featured)
                                        <span class="px-3 py-1 bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-300 text-sm font-medium rounded-full transition-colors duration-300">
                                            <i class="fas fa-star mr-1"></i>Featured
                                        </span>
                                    @endif
                                </div>

                                <h2 class="text-2xl font-bold text-content dark:text-white mb-2 transition-colors duration-300">{{ $selectedItem->title }}</h2>
                                <p class="text-content-muted dark:text-content-muted text-lg transition-colors duration-300">{{ $selectedItem->short_description }}</p>
                            </div>

                            <!-- Stats -->
                            <div class="flex items-center space-x-6 text-sm text-content-muted dark:text-content-muted transition-colors duration-300">
                                <div class="flex items-center">
                                    <i class="fas fa-user mr-2"></i>
                                    <span>{{ $selectedItem->vendor->name }}</span>
                                </div>
                                @if($selectedItem->average_rating > 0)
                                    <div class="flex items-center">
                                        <i class="fas fa-star text-yellow-400 mr-2"></i>
                                        <span>{{ number_format($selectedItem->average_rating, 1) }} ({{ $selectedItem->reviews_count }} reviews)</span>
                                    </div>
                                @endif
                                <div class="flex items-center">
                                    <i class="fas fa-eye mr-2"></i>
                                    <span>{{ number_format($selectedItem->views_count) }} views</span>
                                </div>
                                @if($selectedItem->duration_minutes)
                                    <div class="flex items-center">
                                        <i class="fas fa-clock mr-2"></i>
                                        <span>{{ $selectedItem->getFormattedDuration() }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Pricing -->
                            <div class="bg-surface-muted dark:bg-gray-700 rounded-lg p-4 transition-colors duration-300">
                                <div class="flex items-center justify-between">
                                    <div>
                                        @if($selectedItem->hasDiscount())
                                            <div class="flex items-center space-x-3">
                                                <span class="text-3xl font-bold text-brand dark:text-brand transition-colors duration-300">{{ $selectedItem->getFormattedPrice() }}</span>
                                                <span class="text-xl text-content-muted dark:text-content-muted line-through transition-colors duration-300">{{ $selectedItem->getFormattedOriginalPrice() }}</span>
                                                <span class="px-2 py-1 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-300 text-sm font-medium rounded transition-colors duration-300">
                                                    {{ $selectedItem->getDiscountPercentage() }}% OFF
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-3xl font-bold text-brand dark:text-brand transition-colors duration-300">{{ $selectedItem->getFormattedPrice() }}</span>
                                        @endif
                                    </div>
                                    <div class="space-x-2">
                                        <button class="bg-action dark:bg-action text-action-foreground px-6 py-3 rounded-lg hover:bg-action dark:hover:bg-action transition-colors duration-300">
                                            <i class="fas fa-shopping-cart mr-2"></i>
                                            Add to Cart
                                        </button>
                                        <button class="border border-line dark:border-gray-600 text-content dark:text-gray-300 px-6 py-3 rounded-lg hover:bg-surface-muted dark:hover:bg-gray-700 transition-colors duration-300">
                                            <i class="fas fa-heart mr-2"></i>
                                            Wishlist
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            <div>
                                <h3 class="text-lg font-medium text-content dark:text-white mb-3 transition-colors duration-300">Description</h3>
                                <div class="prose max-w-none text-content-muted dark:text-content-muted transition-colors duration-300">
                                    {!! nl2br(e($selectedItem->description)) !!}
                                </div>
                            </div>

                            <!-- Categories and Tags -->
                            @if($selectedItem->getCategoryNames() || $selectedItem->tags)
                                <div>
                                    <h3 class="text-lg font-medium text-content dark:text-white mb-3 transition-colors duration-300">Categories & Tags</h3>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($selectedItem->getCategoryNames() as $categoryName)
                                            <span class="px-3 py-1 bg-brand/10 dark:bg-action text-brand dark:text-brand text-sm rounded-full transition-colors duration-300">
                                                {{ $categoryName }}
                                            </span>
                                        @endforeach
                                        @if($selectedItem->tags)
                                            @foreach($selectedItem->tags as $tag)
                                                <span class="px-3 py-1 bg-surface-muted dark:bg-gray-700 text-content dark:text-gray-300 text-sm rounded-full transition-colors duration-300">
                                                    #{{ $tag }}
                                                </span>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Reviews Section -->
                @if($selectedItem->reviews && $selectedItem->reviews->count() > 0)
                    <div class="bg-surface dark:bg-gray-800 rounded-lg shadow-sm border border-line dark:border-gray-700 p-6 transition-colors duration-300">
                        <h3 class="text-lg font-medium text-content dark:text-white mb-4 transition-colors duration-300">
                            Reviews ({{ $selectedItem->reviews_count }})
                        </h3>
                        <div class="space-y-4">
                            @foreach($selectedItem->reviews as $review)
                                <div class="border-b border-line dark:border-gray-700 pb-4 last:border-b-0 last:pb-0 transition-colors duration-300">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center space-x-2">
                                            <span class="font-medium text-content dark:text-white transition-colors duration-300">{{ $review->user->name }}</span>
                                            <div class="flex">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star text-{{ $i <= $review->rating ? 'yellow' : 'gray' }}-400 text-sm"></i>
                                                @endfor
                                            </div>
                                        </div>
                                        <span class="text-sm text-content-muted dark:text-content-muted transition-colors duration-300">{{ $review->created_at->diffForHumans() }}</span>
                                    </div>
                                    @if($review->title)
                                        <h4 class="font-medium text-content dark:text-white mb-1 transition-colors duration-300">{{ $review->title }}</h4>
                                    @endif
                                    <p class="text-content-muted dark:text-content-muted transition-colors duration-300">{{ $review->comment }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @else
                <!-- Item not found -->
                <div class="bg-surface dark:bg-gray-800 rounded-lg shadow-sm border border-line dark:border-gray-700 p-6 transition-colors duration-300">
                    <div class="text-center py-12">
                        <i class="fas fa-exclamation-triangle text-yellow-400 text-6xl mb-4"></i>
                        <h3 class="text-lg font-medium text-content dark:text-white mb-2 transition-colors duration-300">Item Not Found</h3>
                        <p class="text-content-muted dark:text-content-muted mb-6 transition-colors duration-300">The requested item could not be found or is no longer available.</p>
                        <button wire:click="backToBrowse"
                                class="inline-flex items-center px-4 py-2 bg-action dark:bg-action text-action-foreground rounded-lg hover:bg-action dark:hover:bg-action transition-colors duration-300">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to Browse
                        </button>
                    </div>
                </div>
            @endif
        @else
            <!-- No item selected -->
            <div class="bg-surface dark:bg-gray-800 rounded-lg shadow-sm border border-line dark:border-gray-700 p-6 transition-colors duration-300">
                <div class="text-center py-12">
                    <i class="fas fa-box-open text-gray-300 dark:text-content-muted text-6xl mb-4"></i>
                    <h3 class="text-lg font-medium text-content dark:text-white mb-2 transition-colors duration-300">Select an Item</h3>
                    <p class="text-content-muted dark:text-content-muted mb-6 transition-colors duration-300">Choose an item from the marketplace to view its details.</p>
                    <button wire:click="backToBrowse"
                            class="inline-flex items-center px-4 py-2 bg-action dark:bg-action text-action-foreground rounded-lg hover:bg-action dark:hover:bg-action transition-colors duration-300">
                        <i class="fas fa-search mr-2"></i>
                        Browse Items
                    </button>
                </div>
            </div>
        @endisset
    @endif

    <!-- Category Management Modals -->
    @include('livewire.marketplace.partial.browse.category-modals')
</div>
