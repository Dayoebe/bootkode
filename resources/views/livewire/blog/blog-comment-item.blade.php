{{-- resources/views/livewire/blog/blog-comment-item.blade.php --}}
<div class="comment-item">
    <div class="flex space-x-4">
        <div class="flex-shrink-0">
            <img src="{{ $comment->user ? $comment->user->profile_photo_url : asset('images/default-avatar.png') }}" 
                 alt="{{ $comment->author_display_name }}"
                 class="w-10 h-10 rounded-full">
        </div>
        
        <div class="flex-1">
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center space-x-2">
                        <h4 class="font-medium text-gray-900">{{ $comment->author_display_name }}</h4>
                        @if($comment->user)
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">Verified</span>
                        @endif
                    </div>
                    <time class="text-sm text-gray-500">{{ $comment->created_at->diffForHumans() }}</time>
                </div>
                
                <p class="text-gray-700 leading-relaxed">{{ $comment->content }}</p>
            </div>
            
            <div class="flex items-center space-x-4 mt-2">
                <button wire:click="toggleLike" 
                        class="flex items-center text-sm transition-colors
                               {{ $userHasLiked ? 'text-red-600' : 'text-gray-500 hover:text-red-600' }}">
                    <i class="fas fa-heart mr-1"></i>
                    {{ $comment->likes_count > 0 ? $comment->likes_count : 'Like' }}
                </button>
                
                <button wire:click="toggleReplyForm" 
                        class="text-sm text-gray-500 hover:text-blue-600 transition-colors">
                    <i class="fas fa-reply mr-1"></i>
                    Reply
                </button>
                
                <span class="text-sm text-gray-400">
                    {{ $comment->created_at->format('M d, Y \a\t g:i A') }}
                </span>
            </div>
            
            {{-- Reply Form --}}
            @if($showReplyForm)
                <div class="mt-4 bg-white rounded-lg border p-4">
                    <form wire:submit.prevent="submitReply">
                        @if($errors->has('guestInfo'))
                            <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-700 rounded">
                                {{ $errors->first('guestInfo') }}
                            </div>
                        @endif
                        
                        @guest
                            <div class="grid md:grid-cols-2 gap-4 mb-4">
                                <input type="text" 
                                       wire:model="guestName" 
                                       class="px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500"
                                       placeholder="Your name (optional)">
                                <input type="email" 
                                       wire:model="guestEmail" 
                                       class="px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500"
                                       placeholder="Email (optional)">
                            </div>
                        @endguest
                        
                        <textarea wire:model="replyContent" 
                                  rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 mb-3"
                                  placeholder="Write your reply..."></textarea>
                        @error('replyContent')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                        
                        <div class="flex justify-end space-x-3">
                            <button type="button" 
                                    wire:click="toggleReplyForm"
                                    class="px-4 py-2 text-gray-600 hover:text-gray-800">
                                Cancel
                            </button>
                            <button type="submit" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                                Post Reply
                            </button>
                        </div>
                    </form>
                </div>
            @endif
            
            {{-- Replies --}}
            @if($comment->replies->count() > 0)
                <div class="mt-4 space-y-4">
                    @foreach($comment->replies as $reply)
                        @livewire('blog.blog-comment-item', ['comment' => $reply], key('reply-' . $reply->id))
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
