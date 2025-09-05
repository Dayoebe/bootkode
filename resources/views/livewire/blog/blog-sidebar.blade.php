{{-- resources/views/livewire/blog/blog-sidebar.blade.php --}}
<div class="space-y-8">
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
                    <a href="{{ route('blog.tag', $tag) }}" 
                       class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-blue-100 hover:text-blue-700 transition-colors">
                        #{{ $tag }}
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Newsletter Signup --}}
    <div class="bg-gray-800 rounded-xl p-6 text-white">
        <h3 class="text-lg font-semibold mb-2">Stay Updated!</h3>
        <p class="text-blue-100 text-sm mb-4">Subscribe to our newsletter for the latest posts and insights.</p>
        <form class="space-y-3" wire:submit.prevent="subscribe">
            <input type="email" 
                   placeholder="Enter your email"
                   class="w-full px-4 py-2 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-white">
            <button type="submit" 
                    class="w-full px-4 py-2 bg-white text-blue-600 font-medium rounded-lg hover:bg-gray-100 transition-colors">
                Subscribe
            </button>
        </form>
    </div>
</div>