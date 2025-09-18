{{-- resources/views/livewire/marketplace/partials/product-detail.blade.php --}}
<section class="py-8 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <button wire:click="backToBrowse" class="hover:text-purple-600 transition-colors">Home</button>
        <i class="fas fa-chevron-right text-xs"></i>
        @if($selectedProduct->itemCategories && $selectedProduct->itemCategories->count() > 0)
            <button wire:click="viewCategory({{ $selectedProduct->itemCategories->first()->id }})" 
                    class="hover:text-purple-600 transition-colors">
                {{ $selectedProduct->itemCategories->first()->name }}
            </button>
            <i class="fas fa-chevron-right text-xs"></i>
        @endif
        <span class="text-gray-900 font-medium">{{ Str::limit($selectedProduct->title, 40) }}</span>
    </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Product Images -->
            <div class="lg:col-span-2">
                <div class="space-y-4">
                    <!-- Main Image -->
                    <div class="aspect-video bg-gray-200 rounded-xl overflow-hidden">
                        @if($selectedProduct->getPrimaryImage())
                            <img src="{{ Storage::url($selectedProduct->getPrimaryImage()) }}" 
                                 alt="{{ $selectedProduct->title }}" 
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-purple-100">
                                <i class="fas fa-book text-purple-400 text-6xl"></i>
                            </div>
                        @endif
                    </div>

                    <!-- Additional Images -->
                    @if($selectedProduct->getAllImages() && count($selectedProduct->getAllImages()) > 1)
                        <div class="grid grid-cols-4 gap-2">
                            @foreach($selectedProduct->getAllImages() as $index => $image)
                                @if($index > 0)
                                    <div class="aspect-video bg-gray-200 rounded-lg overflow-hidden">
                                        <img src="{{ Storage::url($image) }}" 
                                             alt="{{ $selectedProduct->title }}" 
                                             class="w-full h-full object-cover hover:scale-105 transition-transform cursor-pointer">
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Product Details Tabs -->
                <div class="mt-8" x-data="{ activeTab: 'description' }">
                    <div class="border-b border-gray-200">
                        <nav class="flex space-x-8">
                            <button @click="activeTab = 'description'" 
                                    :class="activeTab === 'description' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                                Description
                            </button>
                            @if($selectedProduct->reviews->count() > 0)
                                <button @click="activeTab = 'reviews'" 
                                        :class="activeTab === 'reviews' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                                    Reviews ({{ $selectedProduct->reviews_count }})
                                </button>
                            @endif
                            <button @click="activeTab = 'instructor'" 
                                    :class="activeTab === 'instructor' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                    class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                                Instructor
                            </button>
                        </nav>
                    </div>

                    <!-- Tab Content -->
                    <div class="py-6">
                        <!-- Description Tab -->
                        <div x-show="activeTab === 'description'" x-transition>
                            <div class="prose max-w-none text-gray-700">
                                {!! nl2br(e($selectedProduct->description)) !!}
                            </div>

                            @if($selectedProduct->duration_minutes)
                                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-clock text-purple-600"></i>
                                        <span class="font-medium text-gray-900">Duration:</span>
                                        <span class="text-gray-700">{{ $selectedProduct->getFormattedDuration() }}</span>
                                    </div>
                                </div>
                            @endif

                            @if($selectedProduct->files && count($selectedProduct->files) > 0)
                                <div class="mt-6">
                                    <h4 class="font-medium text-gray-900 mb-3">Included Files:</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        @foreach($selectedProduct->files as $file)
                                            <div class="flex items-center space-x-2 p-2 bg-gray-50 rounded">
                                                <i class="fas fa-file text-gray-400"></i>
                                                <span class="text-sm text-gray-700">{{ $file['name'] }}</span>
                                                <span class="text-xs text-gray-500 ml-auto">
                                                    {{ number_format($file['size'] / 1024, 1) }}KB
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Reviews Tab -->
                        @if($selectedProduct->reviews->count() > 0)
                            <div x-show="activeTab === 'reviews'" x-transition>
                                <div class="space-y-6">
                                    @foreach($selectedProduct->reviews->take(5) as $review)
                                        <div class="border-b border-gray-200 pb-6 last:border-b-0">
                                            <div class="flex items-start space-x-4">
                                                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                                    <span class="text-sm font-semibold text-purple-600">
                                                        {{ substr($review->user->name, 0, 2) }}
                                                    </span>
                                                </div>
                                                <div class="flex-1">
                                                    <div class="flex items-center justify-between">
                                                        <h5 class="font-medium text-gray-900">{{ $review->user->name }}</h5>
                                                        <span class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                                                    </div>
                                                    <div class="flex items-center space-x-1 mt-1">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i class="fas fa-star text-sm {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                                        @endfor
                                                    </div>
                                                    @if($review->title)
                                                        <h6 class="font-medium text-gray-900 mt-2">{{ $review->title }}</h6>
                                                    @endif
                                                    <p class="text-gray-700 mt-2">{{ $review->comment }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                @guest
                                    <div class="mt-6 p-4 bg-purple-50 rounded-lg text-center">
                                        <p class="text-purple-700 mb-2">Want to leave a review?</p>
                                        <button wire:click="requireLogin('review')" 
                                                class="text-purple-600 hover:text-purple-700 font-medium">
                                            Sign in to review this product
                                        </button>
                                    </div>
                                @endguest
                            </div>
                        @endif

                        <!-- Instructor Tab -->
                        <div x-show="activeTab === 'instructor'" x-transition>
                            <div class="flex items-start space-x-6">
                                <div class="w-20 h-20 bg-purple-100 rounded-full flex items-center justify-center">
                                    <span class="text-2xl font-bold text-purple-600">
                                        {{ substr($selectedProduct->vendor->name, 0, 2) }}
                                    </span>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-xl font-semibold text-gray-900 mb-2">{{ $selectedProduct->vendor->name }}</h4>
                                    @if($selectedProduct->vendor->bio)
                                        <p class="text-gray-700 mb-4">{{ $selectedProduct->vendor->bio }}</p>
                                    @endif
                                    
                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <span class="text-gray-500">Products:</span>
                                            <span class="font-medium text-gray-900 ml-2">
                                                {{ $selectedProduct->vendor->marketplaceItems()->published()->count() }}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="text-gray-500">Rating:</span>
                                            <span class="font-medium text-gray-900 ml-2">
                                                @php
                                                    $vendorAvgRating = $selectedProduct->vendor->marketplaceItems()
                                                        ->published()
                                                        ->whereNotNull('average_rating')
                                                        ->avg('average_rating');
                                                @endphp
                                                {{ number_format($vendorAvgRating ?? 0, 1) }}/5.0
                                            </span>
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <button wire:click="viewVendor({{ $selectedProduct->vendor->id }})" 
                                                class="text-purple-600 hover:text-purple-700 font-medium text-sm">
                                            View all products by {{ $selectedProduct->vendor->name }} →
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Purchase Card -->
                <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6 sticky top-8">
                    <!-- Price -->
                    <div class="mb-6">
                        @if($selectedProduct->hasDiscount())
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="text-3xl font-bold text-purple-600">{{ $selectedProduct->getFormattedPrice() }}</span>
                                <span class="text-lg text-gray-500 line-through">{{ $selectedProduct->getFormattedOriginalPrice() }}</span>
                            </div>
                            <div class="inline-flex items-center px-2 py-1 bg-red-100 text-red-800 text-sm font-medium rounded">
                                <i class="fas fa-tag mr-1"></i>
                                {{ $selectedProduct->getDiscountPercentage() }}% OFF
                            </div>
                        @else
                            <span class="text-3xl font-bold text-purple-600">{{ $selectedProduct->getFormattedPrice() }}</span>
                        @endif
                    </div>

                    <!-- Product Type & Categories -->
                    <div class="mb-6 space-y-2">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-cube text-gray-400"></i>
                            <span class="text-sm text-gray-700">Type: {{ $selectedProduct->type_name }}</span>
                        </div>
                        @if($selectedProduct->itemCategories->count() > 0)
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-tags text-gray-400"></i>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($selectedProduct->itemCategories as $category)
                                        <span class="text-xs bg-purple-100 text-purple-600 px-2 py-1 rounded">
                                            {{ $category->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="space-y-3">
                        @guest
                            <button wire:click="requireLogin('purchase')" 
                                    class="w-full bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition-colors font-semibold">
                                <i class="fas fa-shopping-cart mr-2"></i>Buy Now
                            </button>
                            <button wire:click="requireLogin('wishlist')" 
                                    class="w-full border border-purple-600 text-purple-600 px-6 py-2 rounded-lg hover:bg-purple-50 transition-colors font-medium">
                                <i class="fas fa-heart mr-2"></i>Add to Wishlist
                            </button>
                        @else
                            <a href="{{ route('marketplace.browse') }}" 
                               class="w-full bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition-colors font-semibold text-center block">
                                <i class="fas fa-shopping-cart mr-2"></i>Buy Now
                            </a>
                        @endguest
                    </div>

                    <!-- Product Stats -->
                    <div class="mt-6 pt-6 border-t border-gray-200 space-y-3 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Views</span>
                            <span class="font-medium text-gray-900">{{ number_format($selectedProduct->views_count) }}</span>
                        </div>
                        @if($selectedProduct->sales_count > 0)
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500">Sales</span>
                                <span class="font-medium text-gray-900">{{ number_format($selectedProduct->sales_count) }}</span>
                            </div>
                        @endif
                        @if($selectedProduct->average_rating > 0)
                            <div class="flex items-center justify-between">
                                <span class="text-gray-500">Rating</span>
                                <div class="flex items-center space-x-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star text-xs {{ $i <= $selectedProduct->average_rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                    @endfor
                                    <span class="font-medium text-gray-900 ml-1">{{ number_format($selectedProduct->average_rating, 1) }}</span>
                                </div>
                            </div>
                        @endif
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Last Updated</span>
                            <span class="font-medium text-gray-900">{{ $selectedProduct->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    <!-- Share -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <p class="text-sm text-gray-500 mb-3">Share this product:</p>
                        <div class="flex space-x-2">
                            <button class="p-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                                <i class="fab fa-facebook-f text-sm"></i>
                            </button>
                            <button class="p-2 bg-blue-400 text-white rounded hover:bg-blue-500 transition-colors">
                                <i class="fab fa-twitter text-sm"></i>
                            </button>
                            <button class="p-2 bg-blue-700 text-white rounded hover:bg-blue-800 transition-colors">
                                <i class="fab fa-linkedin-in text-sm"></i>
                            </button>
                            <button class="p-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition-colors">
                                <i class="fas fa-link text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Vendor Info Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">About the Instructor</h3>
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                            <span class="text-lg font-bold text-purple-600">
                                {{ substr($selectedProduct->vendor->name, 0, 2) }}
                            </span>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900">{{ $selectedProduct->vendor->name }}</h4>
                            <p class="text-sm text-gray-600">
                                {{ $selectedProduct->vendor->marketplaceItems()->published()->count() }} Products
                            </p>
                        </div>
                    </div>
                    
                    @if($selectedProduct->vendor->bio)
                        <p class="text-sm text-gray-700 mb-4 line-clamp-3">{{ $selectedProduct->vendor->bio }}</p>
                    @endif
                    
                    <button wire:click="viewVendor({{ $selectedProduct->vendor->id }})" 
                            class="w-full text-purple-600 border border-purple-600 px-4 py-2 rounded-lg hover:bg-purple-50 transition-colors font-medium text-sm">
                        View Profile
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Products -->
@if($relatedProducts->count() > 0)
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-8">You might also like</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedProducts as $item)
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
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif