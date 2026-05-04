@php
    $featuredImage = null;
    if ($post->featured_image) {
        $featuredImage = \Illuminate\Support\Str::startsWith($post->featured_image, ['http://', 'https://'])
            ? $post->featured_image
            : \Illuminate\Support\Facades\Storage::url($post->featured_image);
    }
    $userTags = is_array($post->user_tags) ? $post->user_tags : [];
@endphp

<div class="bk-edge-to-edge bg-slate-50">
    <article>
        <header class="bg-slate-950 text-white">
            <div class="bk-shell py-10 sm:py-14 lg:py-20">
                <div class="flex flex-wrap gap-2">
                    @if($post->categories->count() > 0)
                        @foreach($post->categories as $postCategory)
                            <a href="{{ route('blog.category', $postCategory->slug) }}" class="rounded-full bg-white/10 px-3 py-1 text-sm font-black text-white">
                                {{ $postCategory->name }}
                            </a>
                        @endforeach
                    @elseif($post->category)
                        <a href="{{ route('blog.category', $post->category->slug) }}" class="rounded-full bg-white/10 px-3 py-1 text-sm font-black text-white">
                            {{ $post->category->name }}
                        </a>
                    @endif
                </div>

                <h1 class="bk-display mt-5 max-w-4xl text-3xl font-black leading-tight text-white sm:text-5xl lg:text-6xl">
                    {{ $post->title }}
                </h1>

                <div class="mt-6 flex flex-col gap-5 border-t border-white/10 pt-5 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3">
                        <img src="{{ $post->author->profile_photo_url ?? asset('images/default-avatar.png') }}" alt="{{ $post->author->name }}" class="h-12 w-12 rounded-full object-cover">
                        <div>
                            <p class="font-black text-white">{{ $post->author->name }}</p>
                            <div class="mt-1 flex flex-wrap items-center gap-2 text-sm font-semibold text-slate-300">
                                <time>{{ $post->published_at->format('M d, Y') }}</time>
                                @if($post->read_time > 0)
                                    <span>{{ $post->read_time }} min read</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <span class="rounded-full bg-white/10 px-3 py-2 text-sm font-bold text-slate-200">
                            <i class="fas fa-eye mr-1"></i>{{ number_format($post->views_count) }}
                        </span>
                        <button wire:click="toggleReaction('like')" class="rounded-full px-3 py-2 text-sm font-black transition {{ $userHasLiked ? 'bg-rose-500 text-white' : 'bg-white/10 text-slate-200 hover:bg-white/15' }}">
                            <i class="fas fa-heart mr-1"></i>{{ number_format($post->likes_count) }}
                        </button>
                        @auth
                            <button wire:click="toggleReaction('bookmark')" class="rounded-full px-3 py-2 text-sm font-black transition {{ $userHasBookmarked ? 'bg-sky-500 text-white' : 'bg-white/10 text-slate-200 hover:bg-white/15' }}">
                                <i class="fas fa-bookmark mr-1"></i>{{ $userHasBookmarked ? 'Saved' : 'Save' }}
                            </button>
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        @if($featuredImage)
            <div class="bk-shell -mt-8 sm:-mt-10">
                <div class="overflow-hidden rounded-[2rem] border-4 border-white bg-slate-100 shadow-2xl shadow-slate-900/10">
                    <img src="{{ $featuredImage }}" alt="{{ $post->title }}" loading="lazy" class="h-[260px] w-full object-cover sm:h-[420px]">
                </div>
            </div>
        @endif

        <div class="bk-shell py-10 sm:py-14">
            <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_280px]">
                <div class="bk-card p-5 sm:p-7 lg:p-9">
                    <div class="prose prose-slate max-w-none prose-headings:font-black prose-a:text-teal-700 prose-img:rounded-2xl">
                        {!! $post->content !!}
                    </div>

                    @if(count($userTags) > 0)
                        <div class="mt-8 flex flex-wrap gap-2 border-t border-slate-100 pt-6">
                            @foreach($userTags as $tag)
                                @if(is_string($tag))
                                    <a href="{{ route('blog.tag', $tag) }}" class="rounded-full bg-slate-100 px-3 py-1 text-sm font-bold text-slate-700">
                                        #{{ $tag }}
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>

                <aside class="space-y-4 lg:sticky lg:top-24 lg:self-start">
                    <div class="bk-card p-5">
                        <h2 class="font-black text-slate-950">Share</h2>
                        <div class="mt-4 grid gap-2">
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($post->title) }}" target="_blank" rel="noopener" class="flex items-center gap-3 rounded-2xl bg-slate-50 px-3 py-3 text-sm font-black text-slate-700">
                                <i class="fab fa-x-twitter text-slate-950"></i>X
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank" rel="noopener" class="flex items-center gap-3 rounded-2xl bg-slate-50 px-3 py-3 text-sm font-black text-slate-700">
                                <i class="fab fa-facebook text-blue-700"></i>Facebook
                            </a>
                            <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->url()) }}" target="_blank" rel="noopener" class="flex items-center gap-3 rounded-2xl bg-slate-50 px-3 py-3 text-sm font-black text-slate-700">
                                <i class="fab fa-linkedin text-sky-700"></i>LinkedIn
                            </a>
                        </div>
                    </div>

                    <div class="bk-soft-card p-5">
                        <p class="text-sm font-black text-slate-950">Article activity</p>
                        <div class="mt-4 grid grid-cols-3 gap-2 text-center">
                            <div class="rounded-2xl bg-white p-3">
                                <p class="font-black text-slate-950">{{ number_format($post->views_count) }}</p>
                                <p class="text-[11px] font-bold text-slate-500">Views</p>
                            </div>
                            <div class="rounded-2xl bg-white p-3">
                                <p class="font-black text-slate-950">{{ number_format($post->likes_count) }}</p>
                                <p class="text-[11px] font-bold text-slate-500">Likes</p>
                            </div>
                            <div class="rounded-2xl bg-white p-3">
                                <p class="font-black text-slate-950">{{ number_format($post->comments_count) }}</p>
                                <p class="text-[11px] font-bold text-slate-500">Comments</p>
                            </div>
                        </div>
                    </div>
                </aside>
            </div>

            @if($post->allow_comments)
                <section id="comments" class="mt-10">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <h2 class="bk-display text-3xl font-black text-slate-950">Comments ({{ number_format($post->comments_count) }})</h2>
                        <button wire:click="toggleCommentForm" class="bk-primary-btn w-full sm:w-auto">
                            <i class="fas fa-comment text-sm"></i>
                            Leave a comment
                        </button>
                    </div>

                    @if($showCommentForm)
                        <div class="bk-card mt-6 p-5 sm:p-6">
                            <h3 class="text-lg font-black text-slate-950">
                                @if($replyTo)
                                    Reply to comment
                                    <button wire:click="cancelReply" class="ml-2 text-sm font-bold text-slate-500">(cancel)</button>
                                @else
                                    Leave a comment
                                @endif
                            </h3>

                            <form wire:submit.prevent="submitComment" class="mt-5 space-y-4">
                                @if($errors->has('guestInfo'))
                                    <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800">
                                        {{ $errors->first('guestInfo') }}
                                    </div>
                                @endif

                                @guest
                                    <div class="grid gap-4 md:grid-cols-2">
                                        <div>
                                            <label class="text-sm font-black text-slate-800">Name</label>
                                            <input type="text" wire:model="guestName" class="bk-input mt-2" placeholder="Your name">
                                        </div>
                                        <div>
                                            <label class="text-sm font-black text-slate-800">Email</label>
                                            <input type="email" wire:model="guestEmail" class="bk-input mt-2" placeholder="your@email.com">
                                            @error('guestEmail')<p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                                        </div>
                                    </div>
                                @endguest

                                <div>
                                    <label class="text-sm font-black text-slate-800">Comment</label>
                                    <textarea wire:model="newComment" rows="4" class="bk-input mt-2 resize-none" placeholder="Share your thoughts"></textarea>
                                    @error('newComment')<p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                                </div>

                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <p class="text-sm text-slate-500">
                                        @auth
                                            Commenting as {{ auth()->user()->name }}
                                        @else
                                            You can comment as a guest or <a href="{{ route('login') }}" class="font-bold text-teal-700">log in</a>.
                                        @endauth
                                    </p>
                                    <div class="flex gap-2">
                                        <button type="button" wire:click="toggleCommentForm" class="bk-secondary-btn">Cancel</button>
                                        <button type="submit" class="bk-primary-btn">Post</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @endif

                    @if($comments->count() > 0)
                        <div class="mt-6 space-y-4">
                            @foreach($comments as $comment)
                                @livewire('blog.blog-comment-item', ['comment' => $comment], key($comment->id))
                            @endforeach
                        </div>
                        <div class="mt-8">{{ $comments->links() }}</div>
                    @else
                        <div class="bk-card mt-6 p-8 text-center">
                            <i class="fas fa-comments text-4xl text-slate-300"></i>
                            <p class="mt-3 text-sm font-semibold text-slate-600">No comments yet.</p>
                        </div>
                    @endif
                </section>
            @endif
        </div>
    </article>

    @if($relatedPosts->count() > 0)
        <section class="bg-white py-12 sm:py-16">
            <div class="bk-shell">
                <h2 class="bk-display text-3xl font-black text-slate-950">Related posts</h2>
                <div class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    @foreach($relatedPosts as $relatedPost)
                        @php
                            $relatedImage = null;
                            if ($relatedPost->featured_image) {
                                $relatedImage = \Illuminate\Support\Str::startsWith($relatedPost->featured_image, ['http://', 'https://'])
                                    ? $relatedPost->featured_image
                                    : \Illuminate\Support\Facades\Storage::url($relatedPost->featured_image);
                            }
                        @endphp
                        <article class="bk-card overflow-hidden">
                            @if($relatedImage)
                                <a href="{{ route('blog.show', $relatedPost->slug) }}" class="block aspect-video overflow-hidden bg-slate-100">
                                    <img src="{{ $relatedImage }}" alt="{{ $relatedPost->title }}" class="h-full w-full object-cover transition hover:scale-105">
                                </a>
                            @endif
                            <div class="p-4">
                                <h3 class="line-clamp-2 font-black text-slate-950">
                                    <a href="{{ route('blog.show', $relatedPost->slug) }}" class="hover:text-teal-700">{{ $relatedPost->title }}</a>
                                </h3>
                                <p class="mt-2 line-clamp-2 text-sm text-slate-600">{{ $relatedPost->excerpt }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</div>
