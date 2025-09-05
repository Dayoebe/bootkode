
{{-- resources/views/livewire/blog/admin-blog-comments.blade.php --}}
<div>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Comments Moderation</h1>
            <p class="text-gray-600 dark:text-gray-400">Manage and moderate blog comments</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" 
                   wire:model.live.debounce.300ms="search" 
                   placeholder="Search comments..."
                   class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            
            <select wire:model.live="statusFilter" 
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="all">All Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
            </select>
            
            <select wire:model.live="postFilter" 
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="all">All Posts</option>
                @foreach($posts as $post)
                    <option value="{{ $post->id }}">{{ Str::limit($post->title, 50) }}</option>
                @endforeach
            </select>
            
            <select wire:model.live="sortBy" 
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="latest">Latest</option>
                <option value="oldest">Oldest</option>
                <option value="likes">Most Liked</option>
            </select>
        </div>
        
        @if($search || $statusFilter !== 'all' || $postFilter !== 'all')
            <div class="mt-4">
                <button wire:click="clearFilters" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                    Clear Filters
                </button>
            </div>
        @endif
    </div>

    {{-- Bulk Actions --}}
    @if(count($bulkActions) > 0)
        <div class="bg-blue-50 dark:bg-blue-900 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <span class="text-blue-800 dark:text-blue-200">{{ count($bulkActions) }} comments selected</span>
                <div class="flex items-center space-x-3">
                    <select wire:model="bulkAction" class="px-3 py-1 border rounded">
                        <option value="">Select Action</option>
                        <option value="approve">Approve</option>
                        <option value="reject">Reject</option>
                        <option value="delete">Delete</option>
                    </select>
                    <button wire:click="applyBulkAction" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Apply
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Comments List --}}
    <div class="space-y-4">
        @forelse($comments as $comment)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                <div class="flex items-start justify-between">
                    <div class="flex items-start space-x-4 flex-1">
                        <input type="checkbox" 
                               wire:model="bulkActions" 
                               value="{{ $comment->id }}"
                               class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        
                        <img src="{{ $comment->user ? $comment->user->profile_photo_url : asset('images/default-avatar.png') }}" 
                             alt="{{ $comment->author_display_name }}" 
                             class="w-10 h-10 rounded-full">
                        
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                <h4 class="font-medium text-gray-900 dark:text-white">
                                    {{ $comment->author_display_name }}
                                </h4>
                                @if($comment->user)
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">Registered</span>
                                @else
                                    <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded">Guest</span>
                                @endif
                                <span class="px-2 py-1 text-xs rounded
                                    {{ $comment->status === 'approved' ? 'bg-green-100 text-green-800' : 
                                       ($comment->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ ucfirst($comment->status) }}
                                </span>
                            </div>
                            
                            <p class="text-gray-700 dark:text-gray-300 mb-2">{{ $comment->content }}</p>
                            
                            <div class="text-sm text-gray-500 space-x-4">
                                <span>On: <a href="{{ route('blog.show', $comment->post->slug) }}" 
                                           class="text-blue-600 hover:underline">{{ $comment->post->title }}</a></span>
                                <span>{{ $comment->created_at->diffForHumans() }}</span>
                                <span>{{ $comment->likes_count }} likes</span>
                                @if($comment->parent_id)
                                    <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded">Reply</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-2 ml-4">
                        <button wire:click="viewComment({{ $comment->id }})" 
                                class="text-blue-600 hover:text-blue-800 p-2">
                            <i class="fas fa-eye"></i>
                        </button>
                        @if($comment->status !== 'approved')
                            <button wire:click="approveComment({{ $comment->id }})" 
                                    class="text-green-600 hover:text-green-800 p-2">
                                <i class="fas fa-check"></i>
                            </button>
                        @endif
                        @if($comment->status !== 'rejected')
                            <button wire:click="rejectComment({{ $comment->id }})" 
                                    class="text-red-600 hover:text-red-800 p-2">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                        <button wire:click="deleteComment({{ $comment->id }})" 
                                class="text-red-600 hover:text-red-800 p-2">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-12 text-center">
                <i class="fas fa-comments text-4xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">No comments found.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $comments->links() }}
    </div>

    {{-- View Modal --}}
    @if($showModal && $selectedComment)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-2xl mx-4 w-full max-h-96 overflow-y-auto">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Comment Details</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="space-y-4">
                    <div class="flex items-center space-x-3">
                        <img src="{{ $selectedComment->user ? $selectedComment->user->profile_photo_url : asset('images/default-avatar.png') }}" 
                             alt="{{ $selectedComment->author_display_name }}" 
                             class="w-12 h-12 rounded-full">
                        <div>
                            <h4 class="font-medium">{{ $selectedComment->author_display_name }}</h4>
                            <p class="text-sm text-gray-500">{{ $selectedComment->created_at->format('M d, Y g:i A') }}</p>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <p class="text-gray-700 dark:text-gray-300">{{ $selectedComment->content }}</p>
                    </div>
                    
                    <div class="text-sm text-gray-500 space-y-1">
                        <p><strong>Post:</strong> {{ $selectedComment->post->title }}</p>
                        <p><strong>Status:</strong> {{ ucfirst($selectedComment->status) }}</p>
                        <p><strong>IP Address:</strong> {{ $selectedComment->ip_address }}</p>
                        @if($selectedComment->parent)
                            <p><strong>Reply to:</strong> {{ $selectedComment->parent->author_display_name }}</p>
                        @endif
                    </div>
                </div>
                
                <div class="flex justify-end space-x-4 mt-6">
                    <button wire:click="rejectComment({{ $selectedComment->id }})" 
                            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        Reject
                    </button>
                    <button wire:click="approveComment({{ $selectedComment->id }})" 
                            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        Approve
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
