{{-- resources/views/livewire/blog/public-blog-post.blade.php --}}
<div>
    {{-- Article Header --}}
    <article class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <header class="mb-8">
            @if($post->category)
                <div class="mb-4">
                    <a href="{{ route('blog.category', $post->category->slug) }}"
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                        style="background-color: {{ $post->category->color }}20; color: {{ $post->category->color }}">
                        {{ $post->category->name }}
                    </a>
                </div>
            @endif

            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">{{ $post->title }}</h1>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-gray-200 pb-6">
                <div class="flex items-center mb-4 sm:mb-0">
                    <div class="flex-shrink-0">
                        <img src="{{ $post->author->profile_photo_url ?? asset('images/default-avatar.png') }}"
                            alt="{{ $post->author->name }}" class="w-12 h-12 rounded-full mr-4">
                        <div>
                            <p class="font-medium text-gray-900">{{ $post->author->name }}</p>
                            <div class="flex items-center text-sm text-gray-500">
                                <time>{{ $post->published_at->format('M d, Y') }}</time>
                                @if($post->read_time > 0)
                                    <span class="mx-2">•</span>
                                    <span>{{ $post->read_time }} min read</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="flex items-center text-sm text-gray-500">
                            <i class="fas fa-eye mr-1"></i>
                            {{ number_format($post->views_count) }} views
                        </div>

                        {{-- Reaction Buttons --}}
                        <div class="flex items-center space-x-2">
                            <button wire:click="toggleReaction('like')"
                                class="flex items-center px-3 py-1 rounded-full transition-colors
                                       {{ $userHasLiked ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-600 hover:bg-red-50 hover:text-red-600' }}">
                                <i class="fas fa-heart mr-1"></i>
                                {{ number_format($post->likes_count) }}
                            </button>

                            @auth
                                <button wire:click="toggleReaction('bookmark')"
                                    class="flex items-center px-3 py-1 rounded-full transition-colors
                                               {{ $userHasBookmarked ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-600 hover:bg-blue-50 hover:text-blue-600' }}">
                                    <i class="fas fa-bookmark mr-1"></i>
                                    {{ $userHasBookmarked ? 'Saved' : 'Save' }}
                                </button>
                            @endauth
                        </div>
                    </div>
                </div>
        </header>

        {{-- Featured Image --}}
        <div class="max-w-4xl mx-auto">
            @if($post->featured_image)
                <div class="aspect-video mb-8 rounded-xl overflow-hidden">
                    <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}"
                        class="w-full h-full object-cover">
                </div>
            @endif
        </div>

        {{-- Article Content --}}
        <div class="prose prose-lg max-w-none mb-12">
            {!! $post->content !!}
        </div>

        {{-- Tags --}}
        @if($post->tags && count($post->tags) > 0)
            <div class="flex flex-wrap gap-2 mb-8">
                <span class="text-sm font-medium text-gray-500 mr-2">Tags:</span>
                @foreach($post->tags as $tag)
                    <a href="{{ route('blog.tag', $tag) }}"
                        class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm hover:bg-gray-200 transition-colors">
                        #{{ $tag }}
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Social Sharing --}}
        <div class="border-t border-b border-gray-200 py-6 my-8">
            <div class="flex items-center justify-between">
                <span class="text-lg font-medium text-gray-900">Share this post</span>
                <div class="flex items-center space-x-4">
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($post->title) }}"
                        target="_blank"
                        class="flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                        <i class="fab fa-twitter mr-2"></i>
                        Twitter
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}"
                        target="_blank"
                        class="flex items-center px-4 py-2 bg-blue-800 text-white rounded-lg hover:bg-blue-900 transition-colors">
                        <i class="fab fa-facebook mr-2"></i>
                        Facebook
                    </a>
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->url()) }}"
                        target="_blank"
                        class="flex items-center px-4 py-2 bg-blue-700 text-white rounded-lg hover:bg-blue-800 transition-colors">
                        <i class="fab fa-linkedin mr-2"></i>
                        LinkedIn
                    </a>
                </div>
            </div>
        </div>

        {{-- Comments Section --}}
        @if($post->allow_comments)
            <section id="comments" class="mb-12">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-2xl font-bold text-gray-900">
                        Comments ({{ number_format($post->comments_count) }})
                    </h3>
                    <button wire:click="toggleCommentForm"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-comment mr-2"></i>
                        Leave a Comment
                    </button>
                </div>

                {{-- Comment Form --}}
                @if($showCommentForm)
                    <div class="bg-gray-50 rounded-xl p-6 mb-8">
                        <h4 class="text-lg font-medium mb-4">
                            @if($replyTo)
                                Reply to Comment
                                <button wire:click="cancelReply" class="ml-2 text-sm text-gray-500 hover:text-gray-700">
                                    (Cancel)
                                </button>
                            @else
                                Leave a Comment
                            @endif
                        </h4>

                        <form wire:submit.prevent="submitComment">
                            @if($errors->has('guestInfo'))
                                <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-700 rounded-lg">
                                    {{ $errors->first('guestInfo') }}
                                </div>
                            @endif

                            @guest
                                <div class="grid md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Name (optional)</label>
                                        <input type="text" wire:model="guestName"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                            placeholder="Your name">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Email (optional)</label>
                                        <input type="email" wire:model="guestEmail"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                            placeholder="your@email.com">
                                        @error('guestEmail')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                            @endguest

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Comment</label>
                                <textarea wire:model="newComment" rows="4"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                                    placeholder="Share your thoughts..."></textarea>
                                @error('newComment')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                            </div>

                            <div class="flex items-center justify-between">
                                <p class="text-sm text-gray-500">
                                    @auth
                                        Commenting as {{ auth()->user()->name }}
                                    @else
                                        You can comment as a guest or <a href="{{ route('login') }}"
                                            class="text-blue-600 hover:underline">login</a>
                                    @endauth
                                </p>
                                <div class="space-x-3">
                                    <button type="button" wire:click="toggleCommentForm"
                                        class="px-4 py-2 text-gray-600 hover:text-gray-800">
                                        Cancel
                                    </button>
                                    <button type="submit"
                                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                        Post Comment
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif

                {{-- Comments List --}}
                @if($comments->count() > 0)
                    <div class="space-y-6">
                        @foreach($comments as $comment)
                            @livewire('blog.blog-comment-item', ['comment' => $comment], key($comment->id))
                        @endforeach
                    </div>

                    {{-- Comments Pagination --}}
                    <div class="mt-8">
                        {{ $comments->links() }}
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-comments text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">No comments yet. Be the first to share your thoughts!</p>
                    </div>
                @endif
            </section>
        @endif
    </article>

    {{-- Related Posts --}}
    @if($relatedPosts->count() > 0)
        <section class="bg-gray-50 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h3 class="text-3xl font-bold text-gray-900 mb-8 text-center">Related Posts</h3>
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($relatedPosts as $relatedPost)
                        <article class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition-shadow">
                            @if($relatedPost->featured_image)
                                <div class="aspect-video overflow-hidden">
                                    <img src="{{ Storage::url($relatedPost->featured_image) }}" alt="{{ $relatedPost->title }}"
                                        class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                                </div>
                            @endif
                            <div class="p-4">
                                <h4 class="font-medium mb-2 line-clamp-2">
                                    <a href="{{ route('blog.show', $relatedPost->slug) }}"
                                        class="hover:text-blue-600 transition-colors">
                                        {{ $relatedPost->title }}
                                    </a>
                                </h4>
                                <p class="text-sm text-gray-600 line-clamp-2">{{ $relatedPost->excerpt }}</p>
                                <div class="flex items-center text-xs text-gray-500 mt-2">
                                    <span>{{ $relatedPost->published_at->format('M d') }}</span>
                                    <span class="mx-2">•</span>
                                    <span>{{ number_format($relatedPost->views_count) }} views</span>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</div>