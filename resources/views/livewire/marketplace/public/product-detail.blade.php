{{-- resources/views/livewire/marketplace/partials/product-detail.blade.php --}}
<section class="py-8 bg-surface">
    <div class=" px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <button wire:click="backToBrowse" class="hover:text-brand transition-colors">Home</button>
        <i class="fas fa-chevron-right text-xs"></i>
        @if($selectedProduct->itemCategories && $selectedProduct->itemCategories->count() > 0)
            <button wire:click="viewCategory({{ $selectedProduct->itemCategories->first()->id }})"
                    class="hover:text-brand transition-colors">
                {{ $selectedProduct->itemCategories->first()->name }}
            </button>
            <i class="fas fa-chevron-right text-xs"></i>
        @endif
        <span class="text-content font-medium">{{ Str::limit($selectedProduct->title, 40) }}</span>
    </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Product Images -->
            <div class="lg:col-span-2">
                <div class="space-y-4">
                    <!-- Main Image -->
                    <div class="aspect-video bg-gray-200 rounded-lg overflow-hidden">
                        @if($selectedProduct->getPrimaryImage())
                            <img src="{{ Storage::url($selectedProduct->getPrimaryImage()) }}"
                                 alt="{{ $selectedProduct->title }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-brand/10">
                                <i class="fas fa-book text-brand text-6xl"></i>
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
                                             class="w-full h-full object-cover  transition-transform cursor-pointer">
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Product Details Tabs -->
                <div class="mt-8" x-data="{ activeTab: 'description' }">
                    <div class="border-b border-line">
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
                            <div class="prose max-w-none text-content">
                                {!! nl2br(e($selectedProduct->description)) !!}
                            </div>

                            @if($selectedProduct->duration_minutes)
                                <div class="mt-6 p-4 bg-surface-muted rounded-lg">
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-clock text-brand"></i>
                                        <span class="font-medium text-content">Duration:</span>
                                        <span class="text-content">{{ $selectedProduct->getFormattedDuration() }}</span>
                                    </div>
                                </div>
                            @endif

                            @if($selectedProduct->files && count($selectedProduct->files) > 0)
                                <div class="mt-6">
                                    <h4 class="font-medium text-content mb-3">Included Files:</h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        @foreach($selectedProduct->files as $file)
                                            <div class="flex items-center space-x-2 p-2 bg-surface-muted rounded">
                                                <i class="fas fa-file text-content-muted"></i>
                                                <span class="text-sm text-content">{{ $file['name'] ?? 'Downloadable file' }}</span>
                                                <span class="text-xs text-content-muted ml-auto">
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
                                        <div class="border-b border-line pb-6 last:border-b-0">
                                            <div class="flex items-start space-x-4">
                                                <div class="w-10 h-10 bg-brand/10 rounded-full flex items-center justify-center">
                                                    <span class="text-sm font-semibold text-brand">
                                                        {{ substr($review->user->name, 0, 2) }}
                                                    </span>
                                                </div>
                                                <div class="flex-1">
                                                    <div class="flex items-center justify-between">
                                                        <h5 class="font-medium text-content">{{ $review->user->name }}</h5>
                                                        <span class="text-sm text-content-muted">{{ $review->created_at->diffForHumans() }}</span>
                                                    </div>
                                                    <div class="flex items-center space-x-1 mt-1">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i class="fas fa-star text-sm {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                                        @endfor
                                                    </div>
                                                    @if($review->title)
                                                        <h6 class="font-medium text-content mt-2">{{ $review->title }}</h6>
                                                    @endif
                                                    <p class="text-content mt-2">{{ $review->comment }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                @guest
                                    <div class="mt-6 p-4 bg-brand/10 rounded-lg text-center">
                                        <p class="text-brand mb-2">Want to leave a review?</p>
                                        <button wire:click="requireLogin('review')"
                                                class="text-brand hover:text-brand font-medium">
                                            Sign in to review this product
                                        </button>
                                    </div>
                                @endguest
                            </div>
                        @endif

                        <!-- Instructor Tab -->
                        <div x-show="activeTab === 'instructor'" x-transition>
                            <div class="flex items-start space-x-6">
                                <div class="w-20 h-20 bg-brand/10 rounded-full flex items-center justify-center">
                                    <span class="text-2xl font-bold text-brand">
                                        {{ substr($selectedProduct->vendor->name, 0, 2) }}
                                    </span>
                                </div>
                                <div class="flex-1">
                                    <h4 class="text-xl font-semibold text-content mb-2">{{ $selectedProduct->vendor->name }}</h4>
                                    @if($selectedProduct->vendor->bio)
                                        <p class="text-content mb-4">{{ $selectedProduct->vendor->bio }}</p>
                                    @endif

                                    <div class="grid grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <span class="text-content-muted">Products:</span>
                                            <span class="font-medium text-content ml-2">
                                                {{ $selectedProduct->vendor->marketplaceItems()->published()->count() }}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="text-content-muted">Rating:</span>
                                            <span class="font-medium text-content ml-2">
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
                                                class="text-brand hover:text-brand font-medium text-sm">
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
                <div class="bg-surface rounded-lg shadow-sm border border-line p-6 sticky top-8">
                    <!-- Price -->
                    <div class="mb-6">
                        @if($selectedProduct->hasDiscount())
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="text-3xl font-bold text-brand">{{ $selectedProduct->getFormattedPrice() }}</span>
                                <span class="text-lg text-content-muted line-through">{{ $selectedProduct->getFormattedOriginalPrice() }}</span>
                            </div>
                            <div class="inline-flex items-center px-2 py-1 bg-red-100 text-red-800 text-sm font-medium rounded">
                                <i class="fas fa-tag mr-1"></i>
                                {{ $selectedProduct->getDiscountPercentage() }}% OFF
                            </div>
                        @else
                            <span class="text-3xl font-bold text-brand">{{ $selectedProduct->getFormattedPrice() }}</span>
                        @endif
                    </div>

                    <!-- Product Type & Categories -->
                    <div class="mb-6 space-y-2">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-cube text-content-muted"></i>
                            <span class="text-sm text-content">Type: {{ $selectedProduct->type_name }}</span>
                        </div>
                        @if($selectedProduct->itemCategories->count() > 0)
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-tags text-content-muted"></i>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($selectedProduct->itemCategories as $category)
                                        <span class="text-xs bg-brand/10 text-brand px-2 py-1 rounded">
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
                                    class="w-full bg-action text-action-foreground px-6 py-3 rounded-lg hover:bg-action transition-colors font-semibold">
                                <i class="fas fa-shopping-cart mr-2"></i>Buy Now
                            </button>
                            <button wire:click="requireLogin('wishlist')"
                                    class="w-full border border-brand text-brand px-6 py-2 rounded-lg hover:bg-brand/10 transition-colors font-medium">
                                <i class="fas fa-heart mr-2"></i>Add to Wishlist
                            </button>
                        @else
                            <a href="{{ route('marketplace.browse') }}"
                               class="w-full bg-action text-action-foreground px-6 py-3 rounded-lg hover:bg-action transition-colors font-semibold text-center block">
                                <i class="fas fa-shopping-cart mr-2"></i>Buy Now
                            </a>
                        @endguest
                    </div>

                    <!-- Product Stats -->
                    <div class="mt-6 pt-6 border-t border-line space-y-3 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-content-muted">Views</span>
                            <span class="font-medium text-content">{{ number_format($selectedProduct->views_count) }}</span>
                        </div>
                        @if($selectedProduct->sales_count > 0)
                            <div class="flex items-center justify-between">
                                <span class="text-content-muted">Sales</span>
                                <span class="font-medium text-content">{{ number_format($selectedProduct->sales_count) }}</span>
                            </div>
                        @endif
                        @if($selectedProduct->average_rating > 0)
                            <div class="flex items-center justify-between">
                                <span class="text-content-muted">Rating</span>
                                <div class="flex items-center space-x-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star text-xs {{ $i <= $selectedProduct->average_rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                    @endfor
                                    <span class="font-medium text-content ml-1">{{ number_format($selectedProduct->average_rating, 1) }}</span>
                                </div>
                            </div>
                        @endif
                        <div class="flex items-center justify-between">
                            <span class="text-content-muted">Last Updated</span>
                            <span class="font-medium text-content">{{ $selectedProduct->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    <!-- Share -->
                    <div class="mt-6 pt-6 border-t border-line">
                        <p class="text-sm text-content-muted mb-3">Share this product:</p>
                        <div class="flex space-x-2">
                            <button class="p-2 bg-action text-action-foreground rounded hover:bg-action transition-colors">
                                <i class="fab fa-facebook-f text-sm"></i>
                            </button>
                            <button class="p-2 bg-blue-400 text-action-foreground rounded hover:bg-action transition-colors">
                                <i class="fab fa-twitter text-sm"></i>
                            </button>
                            <button class="p-2 bg-action text-action-foreground rounded hover:bg-action transition-colors">
                                <i class="fab fa-linkedin-in text-sm"></i>
                            </button>
                            <button class="p-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition-colors">
                                <i class="fas fa-link text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Vendor Info Card -->
                <div class="bg-surface rounded-lg shadow-sm border border-line p-6">
                    <h3 class="font-semibold text-content mb-4">About the Instructor</h3>
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-12 h-12 bg-brand/10 rounded-full flex items-center justify-center">
                            <span class="text-lg font-bold text-brand">
                                {{ substr($selectedProduct->vendor->name, 0, 2) }}
                            </span>
                        </div>
                        <div>
                            <h4 class="font-medium text-content">{{ $selectedProduct->vendor->name }}</h4>
                            <p class="text-sm text-content-muted">
                                {{ $selectedProduct->vendor->marketplaceItems()->published()->count() }} Products
                            </p>
                        </div>
                    </div>

                    @if($selectedProduct->vendor->bio)
                        <p class="text-sm text-content mb-4 line-clamp-3">{{ $selectedProduct->vendor->bio }}</p>
                    @endif

                    <button wire:click="viewVendor({{ $selectedProduct->vendor->id }})"
                            class="w-full text-brand border border-brand px-4 py-2 rounded-lg hover:bg-brand/10 transition-colors font-medium text-sm">
                        View Profile
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Related Products -->
@if($relatedProducts->count() > 0)
    <section class="py-16 bg-surface-muted">
        <div class=" px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-content mb-8">You might also like</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedProducts as $item)
                    <div class="bg-surface rounded-lg shadow-sm border border-line overflow-hidden  transition-shadow duration-200 group cursor-pointer"
                         wire:click="viewProduct({{ $item->id }})">
                        <div class="aspect-video bg-gray-200 overflow-hidden">
                            @if($item->getPrimaryImage())
                                <img src="{{ Storage::url($item->getPrimaryImage()) }}"
                                     alt="{{ $item->title }}"
                                     class="w-full h-full object-cover  transition-transform duration-200">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-brand/10">
                                    <i class="fas fa-book text-brand text-2xl"></i>
                                </div>
                            @endif
                        </div>
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-medium text-brand bg-brand/10 px-2 py-1 rounded">
                                    {{ $item->type_name }}
                                </span>
                                @if($item->hasDiscount())
                                    <span class="text-xs font-medium text-red-600 bg-red-100 px-2 py-1 rounded">
                                        {{ $item->getDiscountPercentage() }}% OFF
                                    </span>
                                @endif
                            </div>
                            <h3 class="font-semibold text-content mb-2 line-clamp-2 group-hover:text-brand transition-colors text-sm">
                                {{ $item->title }}
                            </h3>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <div class="w-6 h-6 bg-brand/10 rounded-full flex items-center justify-center">
                                        <span class="text-xs font-semibold text-brand">
                                            {{ substr($item->vendor->name, 0, 1) }}
                                        </span>
                                    </div>
                                    <span class="text-xs text-content truncate">{{ $item->vendor->name }}</span>
                                </div>
                                <div class="text-right">
                                    @if($item->hasDiscount())
                                        <div class="text-xs text-content-muted line-through">{{ $item->getFormattedOriginalPrice() }}</div>
                                    @endif
                                    <div class="font-bold text-brand text-sm">{{ $item->getFormattedPrice() }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
