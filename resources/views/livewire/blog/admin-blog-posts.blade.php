{{-- resources/views/livewire/blog/admin-blog-posts.blade.php --}}
<div>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Blog Posts</h1>
            <p class="text-gray-600 dark:text-gray-400">Manage your blog posts and content</p>
        </div>
        <a href="{{ route('admin.blog.posts.create') }}" 
           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            <i class="fas fa-plus mr-2"></i>
            Create Post
        </a>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <div>
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       placeholder="Search posts..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>
            <div>
                <select wire:model.live="statusFilter" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="all">All Status</option>
                    <option value="published">Published</option>
                    <option value="draft">Draft</option>
                    <option value="scheduled">Scheduled</option>
                </select>
            </div>
            <div>
                <select wire:model.live="categoryFilter" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="all">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select wire:model.live="authorFilter" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="all">All Authors</option>
                    @foreach($authors as $author)
                        <option value="{{ $author->id }}">{{ $author->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select wire:model.live="sortBy" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="latest">Latest</option>
                    <option value="oldest">Oldest</option>
                    <option value="title">Title A-Z</option>
                    <option value="views">Most Viewed</option>
                    <option value="likes">Most Liked</option>
                    <option value="comments">Most Commented</option>
                </select>
            </div>
        </div>
        
        @if($search || $statusFilter !== 'all' || $categoryFilter !== 'all' || $authorFilter !== 'all')
            <div class="mt-4">
                <button wire:click="clearFilters" 
                        class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                    Clear Filters
                </button>
            </div>
        @endif
    </div>

    {{-- Bulk Actions --}}
    @if(count($bulkActions) > 0)
        <div class="bg-blue-50 dark:bg-blue-900 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <span class="text-blue-800 dark:text-blue-200">
                    {{ count($bulkActions) }} posts selected
                </span>
                <div class="flex items-center space-x-3">
                    <select wire:model="bulkAction" 
                            class="px-3 py-1 border border-blue-300 rounded focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Action</option>
                        <option value="publish">Publish</option>
                        <option value="draft">Move to Draft</option>
                        <option value="feature">Feature</option>
                        <option value="unfeature">Unfeature</option>
                        <option value="delete">Delete</option>
                    </select>
                    <button wire:click="applyBulkAction" 
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Apply
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Posts Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" 
                                   wire:model="selectAll"
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Author</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stats</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($posts as $post)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4">
                                <input type="checkbox" 
                                       wire:model="bulkActions" 
                                       value="{{ $post->id }}"
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-start space-x-3">
                                    @if($post->featured_image)
                                        <img src="{{ Storage::url($post->featured_image) }}" 
                                             alt="{{ $post->title }}"
                                             class="w-12 h-12 rounded object-cover">
                                    @else
                                        <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center">
                                            <i class="fas fa-image text-gray-400"></i>
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <h3 class="font-medium text-gray-900 dark:text-white">
                                            {{ $post->title }}
                                            @if($post->is_featured)
                                                <span class="ml-2 px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded">Featured</span>
                                            @endif
                                        </h3>
                                        <p class="text-sm text-gray-500 line-clamp-1">{{ $post->excerpt }}</p>
                                        <div class="flex items-center mt-1 text-xs text-gray-400 space-x-4">
                                            @if($post->tags)
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach(array_slice($post->tags, 0, 3) as $tag)
                                                        <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded">#{{ $tag }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <img src="{{ $post->author->profile_photo_url ?? asset('images/default-avatar.png') }}" 
                                         alt="{{ $post->author->name }}"
                                         class="w-8 h-8 rounded-full mr-2">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $post->author->name }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($post->category)
                                    <span class="px-2 py-1 text-xs font-medium rounded"
                                          style="background-color: {{ $post->category->color }}20; color: {{ $post->category->color }}">
                                        {{ $post->category->name }}
                                    </span>
                                @else
                                    <span class="text-gray-400">No category</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    @switch($post->status)
                                        @case('published')
                                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded">Published</span>
                                            @break
                                        @case('draft')
                                            <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded">Draft</span>
                                            @break
                                        @case('scheduled')
                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">Scheduled</span>
                                            @break
                                    @endswitch
                                    
                                    <button wire:click="togglePostStatus({{ $post->id }})" 
                                            class="text-xs text-blue-600 hover:text-blue-800">
                                        Toggle
                                    </button>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-4 text-sm text-gray-500">
                                    <span title="Views"><i class="fas fa-eye"></i> {{ number_format($post->views_count) }}</span>
                                    <span title="Likes"><i class="fas fa-heart"></i> {{ number_format($post->likes_count) }}</span>
                                    <span title="Comments"><i class="fas fa-comment"></i> {{ number_format($post->comments_count) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    @if($post->status === 'scheduled')
                                        <span class="text-blue-600">{{ $post->published_at?->format('M d, Y g:i A') }}</span>
                                    @else
                                        {{ $post->created_at->format('M d, Y') }}
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500">
                                    Updated: {{ $post->updated_at->format('M d') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('blog.show', $post->slug) }}" 
                                       target="_blank"
                                       class="text-blue-600 hover:text-blue-800" 
                                       title="View Post">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                    <a href="{{ route('admin.blog.posts.edit', $post->slug) }}" 
                                       class="text-green-600 hover:text-green-800" 
                                       title="Edit Post">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button wire:click="toggleFeatured({{ $post->id }})" 
                                            class="text-yellow-600 hover:text-yellow-800" 
                                            title="Toggle Featured">
                                        <i class="fas {{ $post->is_featured ? 'fa-star' : 'fa-star-o' }}"></i>
                                    </button>
                                    <button wire:click="confirmDelete({{ $post->id }})" 
                                            class="text-red-600 hover:text-red-800" 
                                            title="Delete Post">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-newspaper text-4xl mb-4"></i>
                                <p>No posts found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        <div class="px-6 py-3 border-t border-gray-200 dark:border-gray-700">
            {{ $posts->links() }}
        </div>
    </div>

    {{-- Delete Modal --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-sm mx-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Delete Post</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Are you sure you want to delete this post? This action cannot be undone.</p>
                <div class="flex justify-end space-x-4">
                    <button wire:click="$set('showDeleteModal', false)" 
                            class="px-4 py-2 text-gray-600 hover:text-gray-800">
                        Cancel
                    </button>
                    <button wire:click="deletePost" 
                            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
