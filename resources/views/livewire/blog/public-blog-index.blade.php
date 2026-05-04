<div class="bk-edge-to-edge bg-slate-50">
    @if($featuredPosts->count() > 0 && !$search && !$category && !$tag)
        <section class="bg-slate-950 text-white">
            <div class="bk-shell py-12 sm:py-16 lg:py-20">
                <span class="bk-eyebrow border-white/15 bg-white/10 text-white">BootKode Blog</span>
                <h1 class="bk-display mt-4 max-w-4xl text-3xl font-black leading-tight text-white sm:text-5xl">
                    Practical notes for learners, builders, and mentors
                </h1>
                <p class="mt-4 max-w-2xl text-base leading-7 text-slate-300">
                    Read field notes, course updates, learning guidance, and career advice from the BootKode ecosystem.
                </p>

                <div class="mt-8 grid gap-4 md:grid-cols-3">
                    @foreach($featuredPosts as $post)
                        <article class="overflow-hidden rounded-3xl border border-white/10 bg-white/10 backdrop-blur">
                            @if($post->featured_image)
                                <a href="{{ route('blog.show', $post->slug) }}" class="block aspect-video overflow-hidden">
                                    <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="h-full w-full object-cover transition duration-300 hover:scale-105">
                                </a>
                            @endif
                            <div class="p-5">
                                <div class="flex flex-wrap items-center gap-2 text-xs font-bold text-slate-300">
                                    <time>{{ $post->published_at->format('M d, Y') }}</time>
                                    @if($post->categories->count() > 0)
                                        @foreach($post->categories->take(1) as $postCategory)
                                            <span class="rounded-full bg-white/10 px-2 py-1">{{ $postCategory->name }}</span>
                                        @endforeach
                                    @elseif($post->category)
                                        <span class="rounded-full bg-white/10 px-2 py-1">{{ $post->category->name }}</span>
                                    @endif
                                </div>
                                <h2 class="mt-3 line-clamp-2 text-xl font-black leading-snug text-white">
                                    <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
                                </h2>
                                <p class="mt-2 line-clamp-3 text-sm leading-6 text-slate-300">{{ $post->excerpt }}</p>
                                <a href="{{ route('blog.show', $post->slug) }}" class="mt-4 inline-flex items-center gap-2 text-sm font-black text-teal-200">
                                    Read article <i class="fas fa-arrow-right text-xs"></i>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="py-10 sm:py-14 lg:py-16">
        <div class="bk-shell">
            <div class="grid gap-8 lg:grid-cols-[0.68fr_0.32fr]">
                <main>
                    <div class="bk-card p-4 sm:p-5">
                        <div class="grid gap-4 md:grid-cols-[1fr_180px_auto] md:items-end">
                            <div>
                                <label class="text-sm font-black text-slate-800">Search posts</label>
                                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search articles..." class="bk-input mt-2">
                            </div>
                            <div>
                                <label class="text-sm font-black text-slate-800">Sort by</label>
                                <select wire:model.live="sortBy" class="bk-input mt-2">
                                    <option value="latest">Latest</option>
                                    <option value="popular">Popular</option>
                                    <option value="trending">Trending</option>
                                    <option value="oldest">Oldest</option>
                                </select>
                            </div>
                            @if($search || $category || $tag)
                                <button wire:click="clearFilters" class="bk-secondary-btn">Clear</button>
                            @endif
                        </div>

                        @if($search || $category || $tag)
                            <div class="mt-4 flex flex-wrap gap-2">
                                @if($search)
                                    <span class="rounded-full bg-sky-50 px-3 py-1 text-sm font-bold text-sky-800">Search: "{{ $search }}"</span>
                                @endif
                                @if($category)
                                    <span class="rounded-full bg-teal-50 px-3 py-1 text-sm font-bold text-teal-800">Category: {{ $category->name }}</span>
                                @endif
                                @if($tag)
                                    <span class="rounded-full bg-rose-50 px-3 py-1 text-sm font-bold text-rose-800">Tag: {{ $tag }}</span>
                                @endif
                            </div>
                        @endif
                    </div>

                    @if($posts->count() > 0)
                        <div class="mt-6 grid gap-4 md:grid-cols-2">
                            @foreach($posts as $post)
                                <article class="bk-card overflow-hidden">
                                    @if($post->featured_image)
                                        <a href="{{ route('blog.show', $post->slug) }}" class="block aspect-video overflow-hidden bg-slate-100">
                                            <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="h-full w-full object-cover transition duration-300 hover:scale-105">
                                        </a>
                                    @endif

                                    <div class="p-5">
                                        <div class="flex items-center gap-3 text-xs font-bold text-slate-500">
                                            <img src="{{ $post->author->profile_photo_url ?? asset('images/default-avatar.png') }}" alt="{{ $post->author->name }}" class="h-8 w-8 rounded-full object-cover">
                                            <span class="truncate">{{ $post->author->name }}</span>
                                            <span>{{ $post->published_at->format('M d, Y') }}</span>
                                        </div>

                                        @if($post->categories->count() > 0)
                                            <div class="mt-4 flex flex-wrap gap-2">
                                                @foreach($post->categories->take(3) as $postCategory)
                                                    <a href="{{ route('blog.category', $postCategory->slug) }}" class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-black text-slate-700">
                                                        {{ $postCategory->name }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @elseif($post->category)
                                            <div class="mt-4">
                                                <a href="{{ route('blog.category', $post->category->slug) }}" class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-black text-slate-700">
                                                    {{ $post->category->name }}
                                                </a>
                                            </div>
                                        @endif

                                        <h2 class="mt-3 line-clamp-2 text-xl font-black leading-snug text-slate-950">
                                            <a href="{{ route('blog.show', $post->slug) }}" class="hover:text-teal-700">{{ $post->title }}</a>
                                        </h2>
                                        <p class="mt-2 line-clamp-3 text-sm leading-6 text-slate-600">{{ $post->excerpt }}</p>

                                        <div class="mt-4 flex items-center justify-between gap-3 border-t border-slate-100 pt-4">
                                            <div class="flex items-center gap-3 text-xs font-bold text-slate-500">
                                                <span><i class="fas fa-eye mr-1"></i>{{ number_format($post->views_count) }}</span>
                                                <span><i class="fas fa-heart mr-1"></i>{{ number_format($post->likes_count) }}</span>
                                                <span><i class="fas fa-comment mr-1"></i>{{ number_format($post->comments_count) }}</span>
                                            </div>
                                            <a href="{{ route('blog.show', $post->slug) }}" class="text-sm font-black text-teal-700">Read</a>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        <div class="mt-8">
                            {{ $posts->links() }}
                        </div>
                    @else
                        <div class="bk-card mt-6 p-10 text-center">
                            <i class="fas fa-search text-4xl text-slate-300"></i>
                            <h2 class="mt-4 text-xl font-black text-slate-950">No posts found</h2>
                            <p class="mt-2 text-sm text-slate-600">Try a different search term or clear your filters.</p>
                            @if($search || $category || $tag)
                                <button wire:click="clearFilters" class="bk-primary-btn mt-5">Clear filters</button>
                            @endif
                        </div>
                    @endif
                </main>

                <aside class="lg:sticky lg:top-24 lg:self-start">
                    @livewire('blog.blog-sidebar', ['currentCategory' => $category])
                </aside>
            </div>
        </div>
    </section>
</div>
