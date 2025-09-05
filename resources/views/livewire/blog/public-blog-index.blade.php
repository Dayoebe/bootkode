<div>
    {{-- Hero Section --}}
    @if($featuredPosts->count() > 0 && !$search && !$category && !$tag)
        <section class="bg-gray-700 text-white py-16 mb-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12">
                    <h1 class="text-4xl md:text-6xl font-bold mb-4">Our Blog</h1>
                    <p class="text-xl md:text-2xl text-blue-100">Discover insights, stories, and knowledge</p>
                </div>

                {{-- Featured Posts --}}
                <div class="grid md:grid-cols-3 gap-8">
                    @foreach($featuredPosts as $post)
                        <article
                            class="bg-white/10 backdrop-blur-lg rounded-xl p-6 hover:bg-white/20 transition-all duration-300">
                            @if($post->featured_image)
                                <div class="aspect-video mb-4 rounded-lg overflow-hidden">
                                    <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}"
                                        class="w-full h-full object-cover">
                                </div>
                            @endif
                            <div class="flex items-center text-sm text-blue-200 mb-2">
                                <i class="fas fa-calendar mr-2"></i>
                                {{ $post->published_at->format('M d, Y') }}
                                @if($post->category)
                                    <span class="mx-2">•</span>
                                    <span class="px-2 py-1 bg-white/20 rounded">{{ $post->category->name }}</span>
                                @endif
                            </div>
                            <h3 class="text-xl font-bold mb-2 line-clamp-2">
                                <a href="{{ route('blog.show', $post->slug) }}" class="hover:text-blue-200 transition-colors">
                                    {{ $post->title }}
                                </a>
                            </h3>
                            <p class="text-blue-100 line-clamp-3 mb-4">{{ $post->excerpt }}</p>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center text-sm text-blue-200">
                                    <i class="fas fa-eye mr-1"></i>
                                    {{ number_format($post->views_count) }}
                                    <i class="fas fa-heart ml-3 mr-1"></i>
                                    {{ number_format($post->likes_count) }}
                                </div>
                                <a href="{{ route('blog.show', $post->slug) }}"
                                    class="text-white hover:text-blue-200 font-medium">
                                    Read More →
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Main Content --}}
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-8">
            {{-- Main Content --}}
            <main class="lg:w-2/3">
                {{-- Search & Filters --}}
                <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
                    <div class="flex flex-col md:flex-row gap-4 items-end">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search Posts</label>
                            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search for posts..."
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="md:w-48">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Sort by</label>
                            <select wire:model.live="sortBy"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="latest">Latest</option>
                                <option value="popular">Popular</option>
                                <option value="trending">Trending</option>
                                <option value="oldest">Oldest</option>
                            </select>
                        </div>
                        @if($search || $category || $tag)
                            <button wire:click="clearFilters"
                                class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                                Clear Filters
                            </button>
                        @endif
                    </div>

                    {{-- Active Filters --}}
                    @if($search || $category || $tag)
                        <div class="mt-4 flex flex-wrap gap-2">
                            @if($search)
                                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                                    Search: "{{ $search }}"
                                </span>
                            @endif
                            @if($category)
                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                                    Category: {{ $category->name }}
                                </span>
                            @endif
                            @if($tag)
                                <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm">
                                    Tag: {{ $tag }}
                                </span>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Posts Grid --}}
                @if($posts->count() > 0)
                    <div class="grid md:grid-cols-2 gap-8 mb-8">
                        @foreach($posts as $post)
                            <article
                                class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition-shadow duration-300">
                                @if($post->featured_image)
                                    <div class="aspect-video overflow-hidden">
                                        <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}"
                                            class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                                    </div>
                                @endif

                                <div class="p-6">
                                    <div class="flex items-center text-sm text-gray-500 mb-3">
                                        <img src="{{ $post->author->profile_photo_url ?? asset('images/default-avatar.png') }}"
                                            alt="{{ $post->author->name }}" class="w-6 h-6 rounded-full mr-2">
                                        <span>{{ $post->author->name }}</span>
                                        <span class="mx-2">•</span>
                                        <time>{{ $post->published_at->format('M d, Y') }}</time>
                                        @if($post->category)
                                            <span class="mx-2">•</span>
                                            <a href="{{ route('blog.category', $post->category->slug) }}"
                                                class="px-2 py-1 rounded text-xs font-medium"
                                                style="background-color: {{ $post->category->color }}20; color: {{ $post->category->color }}">
                                                {{ $post->category->name }}
                                            </a>
                                        @endif
                                    </div>

                                    <h2 class="text-xl font-bold mb-3 line-clamp-2">
                                        <a href="{{ route('blog.show', $post->slug) }}"
                                            class="hover:text-blue-600 transition-colors">
                                            {{ $post->title }}
                                        </a>
                                    </h2>

                                    <p class="text-gray-600 mb-4 line-clamp-3">{{ $post->excerpt }}</p>

                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center text-sm text-gray-500 space-x-4">
                                            <span><i class="fas fa-eye mr-1"></i>{{ number_format($post->views_count) }}</span>
                                            <span><i
                                                    class="fas fa-heart mr-1"></i>{{ number_format($post->likes_count) }}</span>
                                            <span><i
                                                    class="fas fa-comment mr-1"></i>{{ number_format($post->comments_count) }}</span>
                                            @if($post->read_time > 0)
                                                <span><i class="fas fa-clock mr-1"></i>{{ $post->read_time }} min read</span>
                                            @endif
                                        </div>
                                        <a href="{{ route('blog.show', $post->slug) }}"
                                            class="text-blue-600 hover:text-blue-800 font-medium">
                                            Read More →
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    {{ $posts->links() }}
                @else
                    <div class="text-center py-12">
                        <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-xl font-medium text-gray-900 mb-2">No posts found</h3>
                        <p class="text-gray-500 mb-6">We couldn't find any posts matching your criteria.</p>
                        @if($search || $category || $tag)
                            <button wire:click="clearFilters"
                                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                Clear Filters
                            </button>
                        @endif
                    </div>
                @endif
            </main>

            {{-- Sidebar --}}
            <aside class="lg:w-1/3">
                @livewire('blog.blog-sidebar', ['currentCategory' => $category])
            </aside>
        </div>
    </div>
</div>