@php
    $imageFor = function ($item) {
        if ($item && method_exists($item, 'getPrimaryImage') && $item->getPrimaryImage()) {
            return \Illuminate\Support\Facades\Storage::url($item->getPrimaryImage());
        }

        return asset('images/default-course.png');
    };

    $title = match ($activeView) {
        'search' => 'Search results',
        'category' => $selectedCategory?->name ?? 'Category',
        'vendor' => $selectedVendor?->name ?? 'Instructor',
        default => 'BootKode Marketplace',
    };
@endphp

<div class="bk-edge-to-edge bg-surface-muted" x-data="{ filtersOpen: false }">
    @if($activeView === 'product' && $selectedProduct)
        <section class="bg-slate-950 text-white">
            <div class="bk-shell py-10 sm:py-14 lg:py-16">
                <button wire:click="backToBrowse" class="inline-flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 text-sm font-bold text-white transition hover:bg-white/15">
                    <i class="fas fa-arrow-left text-xs"></i>
                    Marketplace
                </button>
                <div class="mt-6 grid gap-8 lg:grid-cols-[1fr_380px] lg:items-start">
                    <div>
                        <div class="flex flex-wrap gap-2">
                            <span class="rounded-full bg-teal-400 px-3 py-1 text-xs font-bold text-content">{{ $selectedProduct->type_name }}</span>
                            @if($selectedProduct->itemCategories && $selectedProduct->itemCategories->count() > 0)
                                @foreach($selectedProduct->itemCategories->take(3) as $productCategory)
                                    <button wire:click="viewCategory({{ $productCategory->id }})" class="rounded-full bg-white/10 px-3 py-1 text-xs font-bold text-white">
                                        {{ $productCategory->name }}
                                    </button>
                                @endforeach
                            @endif
                        </div>
                        <h1 class="bk-display mt-4  font-bold leading-tight text-white  bk-page-title">{{ $selectedProduct->title }}</h1>
                        <p class="mt-4 max-w-3xl text-base leading-7 text-slate-300">{{ $selectedProduct->short_description ?: \Illuminate\Support\Str::limit(strip_tags($selectedProduct->description), 220) }}</p>
                    </div>

                    <aside class="rounded-lg border border-white/10 bg-white/10 p-5 ">
                        <p class="text-sm font-bold text-slate-300">Price</p>
                        <div class="mt-2">
                            @if($selectedProduct->hasDiscount())
                                <p class="text-3xl font-bold text-white">{{ $selectedProduct->getFormattedPrice() }}</p>
                                <p class="mt-1 text-sm font-bold text-content-muted line-through">{{ $selectedProduct->getFormattedOriginalPrice() }}</p>
                            @else
                                <p class="text-3xl font-bold text-white">{{ $selectedProduct->getFormattedPrice() }}</p>
                            @endif
                        </div>
                        @guest
                            <button wire:click="requireLogin('purchase')" class="mt-5 w-full rounded-lg bg-surface px-4 py-3 text-sm font-bold text-content">Buy now</button>
                            <button wire:click="requireLogin('wishlist')" class="mt-2 w-full rounded-lg border border-white/20 px-4 py-3 text-sm font-bold text-white">Add to wishlist</button>
                        @else
                            <a href="{{ route('marketplace.browse') }}" class="mt-5 flex w-full items-center justify-center rounded-lg bg-surface px-4 py-3 text-sm font-bold text-content">Buy now</a>
                        @endguest
                    </aside>
                </div>
            </div>
        </section>

        <section class="py-10 sm:py-14 lg:py-16">
            <div class="bk-shell grid gap-8 lg:grid-cols-[1fr_340px]">
                <div class="space-y-6">
                    <div class="bk-card overflow-hidden">
                        <img src="{{ $imageFor($selectedProduct) }}" alt="{{ $selectedProduct->title }}" class="h-[260px] w-full object-cover sm:h-[420px]">
                    </div>

                    <div class="bk-card p-5 sm:p-7">
                        <h2 class="bk-display text-3xl font-bold text-content">What is included</h2>
                        <div class="prose prose-slate mt-5 max-w-none prose-headings:font-bold prose-a:text-brand">
                            {!! nl2br(e($selectedProduct->description)) !!}
                        </div>

                        @if($selectedProduct->files && count($selectedProduct->files) > 0)
                            <div class="mt-6 grid gap-3 sm:grid-cols-2">
                                @foreach($selectedProduct->files as $file)
                                    <div class="rounded-lg bg-surface-muted p-4 text-sm font-bold text-content">
                                        <i class="fas fa-file mr-2 text-brand"></i>{{ $file['name'] ?? 'Downloadable file' }}
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <aside class="space-y-4 lg:sticky lg:top-24 lg:self-start">
                    <div class="bk-card p-5">
                        <h3 class="font-bold text-content">Instructor</h3>
                        <div class="mt-4 flex items-center gap-3">
                            <span class="grid h-12 w-12 place-items-center rounded-full bg-action text-sm font-bold text-action-foreground">
                                {{ substr($selectedProduct->vendor->name, 0, 2) }}
                            </span>
                            <div class="min-w-0">
                                <p class="truncate font-bold text-content">{{ $selectedProduct->vendor->name }}</p>
                                <p class="text-sm font-semibold text-content-muted">{{ $selectedProduct->vendor->marketplaceItems()->published()->count() }} products</p>
                            </div>
                        </div>
                        @if($selectedProduct->vendor->bio)
                            <p class="mt-4 line-clamp-4 text-sm leading-6 text-content-muted">{{ $selectedProduct->vendor->bio }}</p>
                        @endif
                        <button wire:click="viewVendor({{ $selectedProduct->vendor->id }})" class="bk-secondary-btn mt-5 w-full">View instructor</button>
                    </div>

                    <div class="bk-soft-card p-5">
                        <div class="grid grid-cols-2 gap-2">
                            <div class="rounded-lg bg-surface p-3">
                                <p class="font-bold text-content">{{ number_format($selectedProduct->views_count) }}</p>
                                <p class="text-xs font-bold text-content-muted">Views</p>
                            </div>
                            <div class="rounded-lg bg-surface p-3">
                                <p class="font-bold text-content">{{ number_format($selectedProduct->sales_count) }}</p>
                                <p class="text-xs font-bold text-content-muted">Sales</p>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </section>

        @if($relatedProducts->count() > 0)
            <section class="bg-surface py-12 sm:py-16">
                <div class="bk-shell">
                    <h2 class="bk-display text-3xl font-bold text-content">Related products</h2>
                    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach($relatedProducts as $item)
                            <article wire:click="viewProduct({{ $item->id }})" class="bk-card cursor-pointer overflow-hidden">
                                <img src="{{ $imageFor($item) }}" alt="{{ $item->title }}" class="h-40 w-full object-cover">
                                <div class="p-4">
                                    <h3 class="line-clamp-2 font-bold text-content">{{ $item->title }}</h3>
                                    <p class="mt-2 text-sm font-bold text-brand">{{ $item->getFormattedPrice() }}</p>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    @else
        <section class="bg-slate-950 text-white">
            <div class="bk-shell py-12 sm:py-16 lg:py-20">
                <div class="grid gap-8 lg:grid-cols-[1fr_360px] lg:items-end">
                    <div>
                        <span class="bk-eyebrow border-white/15 bg-white/10 text-white">Marketplace</span>
                        <h1 class="bk-display mt-4 max-w-4xl  font-bold leading-tight text-white  bk-page-title">
                            {{ $title }}
                        </h1>
                        <p class="mt-4 max-w-2xl text-base leading-7 text-slate-300">
                            Courses, resources, templates, and services from BootKode instructors and creators.
                        </p>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <div class="rounded-lg border border-white/10 bg-white/10 p-3">
                            <p class="text-2xl font-bold text-white">{{ number_format($stats['total_products']) }}</p>
                            <p class="text-xs font-bold text-slate-300">Products</p>
                        </div>
                        <div class="rounded-lg border border-white/10 bg-white/10 p-3">
                            <p class="text-2xl font-bold text-white">{{ number_format($stats['total_vendors']) }}</p>
                            <p class="text-xs font-bold text-slate-300">Instructors</p>
                        </div>
                        <div class="rounded-lg border border-white/10 bg-white/10 p-3">
                            <p class="text-2xl font-bold text-white">{{ number_format($stats['average_rating'] ?? 0, 1) }}</p>
                            <p class="text-xs font-bold text-slate-300">Rating</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @if($activeView === 'browse')
            <section class="border-b border-line bg-surface">
                <div class="bk-shell grid gap-3 py-4 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach($popularCategories->take(4) as $category)
                        <button wire:click="viewCategory({{ $category->id }})" class="flex items-center gap-3 rounded-lg border border-line p-4 text-left transition hover:border-brand hover:bg-brand/10">
                            <span class="grid h-11 w-11 place-items-center rounded-lg bg-surface-muted text-brand">
                                <i class="{{ $category->icon ?? 'fas fa-tag' }}"></i>
                            </span>
                            <span>
                                <span class="block font-bold text-content">{{ $category->name }}</span>
                                <span class="text-sm font-semibold text-content-muted">{{ $category->items_count }} items</span>
                            </span>
                        </button>
                    @endforeach
                </div>
            </section>
        @endif

        <section class="py-10 sm:py-14 lg:py-16">
            <div class="bk-shell grid gap-6 lg:grid-cols-[280px_1fr]">
                <aside class="lg:sticky lg:top-24 lg:self-start">
                    <div class="bk-card p-4">
                        <div class="flex items-center justify-between lg:hidden">
                            <h2 class="font-bold text-content">Filters</h2>
                            <button type="button" class="rounded-full bg-surface-muted px-3 py-2 text-sm font-bold text-content" @click="filtersOpen = !filtersOpen">Toggle</button>
                        </div>

                        <div class="mt-4 space-y-5 lg:mt-0" x-show="filtersOpen || window.innerWidth >= 1024" x-cloak>
                            <div>
                                <label class="text-sm font-bold text-content">Search</label>
                                <input aria-label="Search products" wire:model.live.debounce.300ms="searchTerm" type="text" class="bk-input mt-2" placeholder="Search products">
                            </div>

                            <div>
                                <label class="text-sm font-bold text-content">Sort</label>
                                <select aria-label="Sort by" wire:model.live="sortBy" class="bk-input mt-2">
                                    <option value="featured">Featured</option>
                                    <option value="latest">Latest</option>
                                    <option value="price_low">Price low</option>
                                    <option value="price_high">Price high</option>
                                    <option value="popular">Popular</option>
                                    <option value="rating">Rating</option>
                                </select>
                            </div>

                            <div>
                                <p class="text-sm font-bold text-content">Categories</p>
                                <div class="mt-3 max-h-64 space-y-2 overflow-y-auto pr-1">
                                    @foreach($allCategories as $category)
                                        <label class="flex items-center gap-3 rounded-lg bg-surface-muted px-3 py-3 text-sm font-bold text-content">
                                            <input type="checkbox" wire:model.live="selectedCategories" value="{{ $category->id }}" class="rounded border-line text-brand focus:ring-brand/30">
                                            <span class="min-w-0 flex-1 truncate">{{ $category->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <button wire:click="clearFilters" class="bk-secondary-btn w-full">Clear filters</button>
                        </div>
                    </div>
                </aside>

                <main>
                    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="bk-display text-3xl font-bold text-content">{{ $title }}</h2>
                            @if(method_exists($items, 'total'))
                                <p class="mt-1 text-sm font-semibold text-content-muted">{{ number_format($items->total()) }} products found</p>
                            @endif
                        </div>
                        <button wire:click="backToBrowse" class="bk-secondary-btn w-full sm:w-auto">Marketplace home</button>
                    </div>

                    @if($items->count() > 0)
                        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                            @foreach($items as $item)
                                <article wire:click="viewProduct({{ $item->id }})" class="bk-card cursor-pointer overflow-hidden transition hover:-translate-y-0.5 ">
                                    <div class="aspect-video bg-surface-muted">
                                        <img src="{{ $imageFor($item) }}" alt="{{ $item->title }}" class="h-full w-full object-cover">
                                    </div>
                                    <div class="p-4">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="rounded-full bg-brand/10 px-2.5 py-1 text-xs font-bold text-brand">{{ $item->type_name }}</span>
                                            @if($item->hasDiscount())
                                                <span class="rounded-full bg-rose-50 px-2.5 py-1 text-xs font-bold text-rose-700">{{ $item->getDiscountPercentage() }}% off</span>
                                            @endif
                                        </div>
                                        <h3 class="mt-3 line-clamp-2 text-lg font-bold leading-snug text-content">{{ $item->title }}</h3>
                                        <p class="mt-2 line-clamp-2 text-sm leading-6 text-content-muted">{{ $item->short_description }}</p>
                                        <div class="mt-4 flex items-center justify-between gap-3 border-t border-line pt-4">
                                            <div class="min-w-0 text-sm font-bold text-content-muted">
                                                <span class="block truncate">{{ $item->vendor->name }}</span>
                                                @if($item->average_rating > 0)
                                                    <span class="text-xs text-amber-600"><i class="fas fa-star"></i> {{ number_format($item->average_rating, 1) }}</span>
                                                @endif
                                            </div>
                                            <p class="shrink-0 text-sm font-bold text-brand">{{ $item->getFormattedPrice() }}</p>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        @if(method_exists($items, 'links'))
                            <div class="mt-8">{{ $items->links() }}</div>
                        @endif
                    @else
                        <div class="bk-card p-10 text-center">
                            <i class="fas fa-store text-4xl text-slate-300"></i>
                            <h2 class="mt-4 text-xl font-bold text-content">No products found</h2>
                            <p class="mt-2 text-sm text-content-muted">Try changing the search or category filters.</p>
                            <button wire:click="clearFilters" class="bk-primary-btn mt-5">Clear filters</button>
                        </div>
                    @endif
                </main>
            </div>
        </section>
    @endif

    @if($showLoginModal)
        <div class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-950/55 p-4 " x-trap.inert.noscroll="true" role="dialog" aria-modal="true" aria-label="Sign in required" tabindex="-1" x-data="{}" @keydown.escape.stop="$wire.set(&#x27;showLoginModal&#x27;, false)">
            <div class="w-full max-w-md rounded-lg bg-surface p-6 shadow-sm">
                <div class="mx-auto grid h-14 w-14 place-items-center rounded-full bg-slate-950 text-white">
                    <i class="fas fa-lock"></i>
                </div>
                <h2 class="mt-4 text-center text-xl font-bold text-content">Sign in required</h2>
                <p class="mt-2 text-center text-sm leading-6 text-content-muted">Create an account or sign in to continue with this marketplace action.</p>
                <div class="mt-6 space-y-2">
                    <button wire:click="redirectToLogin" class="bk-primary-btn w-full">Sign in</button>
                    <button wire:click="redirectToRegister" class="bk-secondary-btn w-full">Create account</button>
                    <button wire:click="closeLoginModal" class="w-full rounded-lg px-4 py-3 text-sm font-bold text-content-muted">Cancel</button>
                </div>
            </div>
        </div>
    @endif
</div>
