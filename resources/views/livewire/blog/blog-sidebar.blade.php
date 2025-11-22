{{-- resources/views/livewire/blog/blog-sidebar.blade.php --}}
<div class="space-y-8">
    {{-- Newsletter Signup --}}
    <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl p-6 text-white">
        <h3 class="text-lg font-semibold mb-2">Stay Updated!</h3>
        <p class="text-blue-100 text-sm mb-4">Subscribe to our newsletter for the latest posts and insights.</p>

        {{-- Success/Error Messages --}}
        @if($subscribeSuccess)
            <div class="mb-4 bg-green-500/20 border border-green-300 text-green-100 px-4 py-3 rounded-lg">
                <i class="fas fa-check-circle mr-2"></i>
                Thank you for subscribing to our newsletter!
            </div>
        @endif

        @if($subscribeError)
            <div class="mb-4 bg-red-500/20 border border-red-300 text-red-100 px-4 py-3 rounded-lg">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ $subscribeError }}
            </div>
        @endif

        <form class="space-y-3" wire:submit.prevent="subscribe">
            <input type="email" wire:model="subscribeEmail" placeholder="Enter your email"
                class="w-full px-4 py-2 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-white"
                required>
            <button type="submit"
                class="w-full px-4 py-2 bg-white text-blue-600 font-medium rounded-lg hover:bg-gray-100 transition-colors flex items-center justify-center">
                <i class="fas fa-envelope mr-2"></i>
                Subscribe
            </button>
        </form>
        <p class="text-xs text-blue-200 mt-3 text-center">
            <i class="fas fa-shield-alt mr-1"></i>
            We respect your privacy. Unsubscribe at any time.
        </p>
    </div>

    {{-- Categories --}}
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4 flex items-center">
            <i class="fas fa-folder-open text-blue-600 mr-2"></i>
            Categories
        </h3>
        <div class="space-y-2">
            @foreach($categories as $category)
                <a href="{{ route('blog.category', $category->slug) }}"
                    class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50 transition-colors
                              {{ $currentCategory && $currentCategory->id === $category->id ? 'bg-blue-50 text-blue-700' : 'text-gray-700' }}">
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full mr-3" style="background-color: {{ $category->color }}"></div>
                        <span>{{ $category->name }}</span>
                    </div>
                    <span class="text-sm text-gray-500 bg-gray-100 px-2 py-1 rounded">
                        {{ $category->published_posts_count }}
                    </span>
                </a>
            @endforeach
        </div>
    </div>

    {{-- Popular Posts --}}
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4 flex items-center">
            <i class="fas fa-fire text-red-500 mr-2"></i>
            Popular Posts
        </h3>
        <div class="space-y-4">
            @foreach($popularPosts as $post)
                <article class="flex space-x-3">
                    <div class="flex-shrink-0 text-lg font-bold text-gray-400">
                        {{ $loop->iteration }}
                    </div>
                    <div class="flex-1">
                        <h4 class="font-medium text-sm leading-tight mb-1">
                            <a href="{{ route('blog.show', $post->slug) }}"
                                class="hover:text-blue-600 transition-colors line-clamp-2">
                                {{ $post->title }}
                            </a>
                        </h4>
                        <div class="flex items-center text-xs text-gray-500">
                            <span>{{ $post->published_at->format('M d') }}</span>
                            <span class="mx-2">•</span>
                            <span>{{ number_format($post->views_count) }} views</span>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>

    {{-- Recent Posts --}}
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4 flex items-center">
            <i class="fas fa-clock text-green-500 mr-2"></i>
            Recent Posts
        </h3>
        <div class="space-y-4">
            @foreach($recentPosts as $post)
                <article>
                    <h4 class="font-medium text-sm leading-tight mb-1">
                        <a href="{{ route('blog.show', $post->slug) }}"
                            class="hover:text-blue-600 transition-colors line-clamp-2">
                            {{ $post->title }}
                        </a>
                    </h4>
                    <time class="text-xs text-gray-500">{{ $post->published_at->format('M d, Y') }}</time>
                </article>
            @endforeach
        </div>
    </div>

    {{-- Popular Tags --}}
    @if(count($popularTags) > 0)
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold mb-4 flex items-center">
                <i class="fas fa-hashtag text-purple-500 mr-2"></i>
                Popular Tags
            </h3>
            <div class="flex flex-wrap gap-2">
                @foreach($popularTags as $tag)
                    @if(is_string($tag))
                        <a href="{{ route('blog.tag', $tag) }}"
                            class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-blue-100 hover:text-blue-700 transition-colors">
                            #{{ $tag }}
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    @endif
</div>