{{-- resources/views/livewire/blog/admin-blog-posts.blade.php --}}
<div class="p-4 md:p-6">
    {{-- Success/Error Messages --}}
    @if (session()->has('message'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-start">
            <i class="fas fa-check-circle mr-2 mt-0.5"></i>
            <span class="flex-1">{{ session('message') }}</span>
            <button onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-start">
            <i class="fas fa-exclamation-circle mr-2 mt-0.5"></i>
            <span class="flex-1">{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">Blog Posts</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Manage your blog posts and content</p>
        </div>
        <a href="{{ route('admin.blog.posts.create') }}" 
           class="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium whitespace-nowrap">
            <i class="fas fa-plus mr-2"></i>
            Create Post
        </a>
    </div>

    {{-- Filters - Now Fully Responsive --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 mb-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3">
            <div class="w-full">
                <input type="text" 
                       wire:model.live.debounce.300ms="search" 
                       placeholder="Search posts..."
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>
            <div class="w-full">
                <select wire:model.live="statusFilter" 
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="all">All Status</option>
                    <option value="published">Published</option>
                    <option value="draft">Draft</option>
                    <option value="scheduled">Scheduled</option>
                </select>
            </div>
            <div class="w-full">
                <select wire:model.live="categoryFilter" 
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="all">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full">
                <select wire:model.live="authorFilter" 
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="all">All Authors</option>
                    @foreach($authors as $author)
                        <option value="{{ $author->id }}">{{ $author->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full">
                <select wire:model.live="sortBy" 
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
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
            <div class="mt-3">
                <button wire:click="clearFilters" 
                        class="px-3 py-1.5 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors text-sm">
                    <i class="fas fa-times mr-1"></i>
                    Clear Filters
                </button>
            </div>
        @endif
    </div>

    {{-- Bulk Actions --}}
    @if(count($bulkActions) > 0)
        <div class="bg-blue-50 dark:bg-blue-900 rounded-lg p-3 mb-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <span class="text-sm text-blue-800 dark:text-blue-200 font-medium">
                    {{ count($bulkActions) }} post(s) selected
                </span>
                <div class="flex items-center gap-2">
                    <select wire:model="bulkAction" 
                            class="px-3 py-1.5 text-sm border border-blue-300 rounded focus:ring-2 focus:ring-blue-500 dark:bg-blue-800 dark:border-blue-600">
                        <option value="">Select Action</option>
                        <option value="publish">Publish</option>
                        <option value="draft">Move to Draft</option>
                        <option value="feature">Feature</option>
                        <option value="unfeature">Unfeature</option>
                        <option value="delete">Delete</option>
                    </select>
                    <button wire:click="applyBulkAction" 
                            class="px-4 py-1.5 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-medium whitespace-nowrap">
                        Apply
                    </button>
                </div>
            </div>
        </div>
    @endif>

    {{-- Desktop Table View (hidden on mobile) --}}
    <div class="hidden lg:block bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left w-10">
                            <input type="checkbox" 
                                   wire:model.live="selectAll"
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stats</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($posts as $post)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3">
                                <input type="checkbox" 
                                       wire:model.live="bulkActions" 
                                       value="{{ $post->id }}"
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-start space-x-3 max-w-md">
                                    @if($post->featured_image)
                                        <img src="{{ Storage::url($post->featured_image) }}" 
                                             alt="{{ $post->title }}"
                                             class="w-12 h-12 rounded object-cover flex-shrink-0">
                                    @else
                                        <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-image text-gray-400"></i>
                                        </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-medium text-gray-900 dark:text-white text-sm truncate">
                                            {{ $post->title }}
                                            @if($post->is_featured)
                                                <span class="ml-1 px-2 py-0.5 bg-yellow-100 text-yellow-800 text-xs rounded">Featured</span>
                                            @endif
                                        </h3>
                                        <p class="text-xs text-gray-500 line-clamp-1">{{ $post->excerpt }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center">
                                    <img src="{{ $post->author->profile_photo_url ?? asset('images/default-avatar.png') }}" 
                                         alt="{{ $post->author->name }}"
                                         class="w-6 h-6 rounded-full mr-2">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white truncate max-w-[100px]">
                                        {{ $post->author->name }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($post->category)
                                    <span class="px-2 py-1 text-xs font-medium rounded whitespace-nowrap"
                                          style="background-color: {{ $post->category->color }}20; color: {{ $post->category->color }}">
                                        {{ $post->category->name }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">No category</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    @switch($post->status)
                                        @case('published')
                                            <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded whitespace-nowrap">Published</span>
                                            @break
                                        @case('draft')
                                            <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded whitespace-nowrap">Draft</span>
                                            @break
                                        @case('scheduled')
                                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded whitespace-nowrap">Scheduled</span>
                                            @break
                                    @endswitch
                                    
                                    <button wire:click="togglePostStatus({{ $post->id }})" 
                                            class="text-xs text-blue-600 hover:text-blue-800 whitespace-nowrap">
                                        Toggle
                                    </button>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-3 text-xs text-gray-500">
                                    <span title="Views"><i class="fas fa-eye"></i> {{ number_format($post->views_count) }}</span>
                                    <span title="Likes"><i class="fas fa-heart"></i> {{ number_format($post->likes_count) }}</span>
                                    <span title="Comments"><i class="fas fa-comment"></i> {{ number_format($post->comments_count) }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-xs text-gray-900 dark:text-white">
                                    @if($post->status === 'scheduled')
                                        <span class="text-blue-600">{{ $post->published_at?->format('M d, Y') }}</span>
                                    @else
                                        {{ $post->created_at->format('M d, Y') }}
                                    @endif
                                </div>
                                <div class="text-xs text-gray-500">
                                    Updated: {{ $post->updated_at->format('M d') }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('blog.show', $post->slug) }}" 
                                       target="_blank"
                                       class="text-blue-600 hover:text-blue-800 p-1" 
                                       title="View Post">
                                        <i class="fas fa-external-link-alt text-sm"></i>
                                    </a>
                                    <a href="{{ route('admin.blog.posts.edit', $post->slug) }}" 
                                       class="text-green-600 hover:text-green-800 p-1" 
                                       title="Edit Post">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                    <button wire:click="toggleFeatured({{ $post->id }})" 
                                            class="text-yellow-600 hover:text-yellow-800 p-1" 
                                            title="Toggle Featured">
                                        <i class="fas {{ $post->is_featured ? 'fa-star' : 'fa-star-o' }} text-sm"></i>
                                    </button>
                                    <button wire:click="confirmDelete({{ $post->id }})" 
                                            class="text-red-600 hover:text-red-800 p-1" 
                                            title="Delete Post">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-gray-500">
                                <i class="fas fa-newspaper text-4xl mb-4"></i>
                                <p>No posts found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Mobile Card View (visible only on mobile/tablet) --}}
    <div class="lg:hidden space-y-4">
        @forelse($posts as $post)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4">
                {{-- Checkbox and Featured Image --}}
                <div class="flex items-start gap-3 mb-3">
                    <input type="checkbox" 
                           wire:model.live="bulkActions" 
                           value="{{ $post->id }}"
                           class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    
                    @if($post->featured_image)
                        <img src="{{ Storage::url($post->featured_image) }}" 
                             alt="{{ $post->title }}"
                             class="w-20 h-20 rounded object-cover flex-shrink-0">
                    @else
                        <div class="w-20 h-20 bg-gray-200 rounded flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-image text-gray-400"></i>
                        </div>
                    @endif
                    
                    <div class="flex-1 min-w-0">
                        <h3 class="font-medium text-gray-900 dark:text-white text-sm mb-1">
                            {{ Str::limit($post->title, 50) }}
                        </h3>
                        @if($post->is_featured)
                            <span class="inline-block px-2 py-0.5 bg-yellow-100 text-yellow-800 text-xs rounded mb-1">Featured</span>
                        @endif
                        <p class="text-xs text-gray-500 line-clamp-2">{{ $post->excerpt }}</p>
                    </div>
                </div>

                {{-- Meta Info --}}
                <div class="grid grid-cols-2 gap-2 mb-3 text-xs">
                    <div>
                        <span class="text-gray-500">Author:</span>
                        <span class="font-medium text-gray-900 dark:text-white ml-1">{{ $post->author->name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Date:</span>
                        <span class="font-medium text-gray-900 dark:text-white ml-1">{{ $post->created_at->format('M d, Y') }}</span>
                    </div>
                    @if($post->category)
                        <div class="col-span-2">
                            <span class="text-gray-500">Category:</span>
                            <span class="inline-block px-2 py-0.5 text-xs font-medium rounded ml-1"
                                  style="background-color: {{ $post->category->color }}20; color: {{ $post->category->color }}">
                                {{ $post->category->name }}
                            </span>
                        </div>
                    @endif
                </div>

                {{-- Status and Stats --}}
                <div class="flex items-center justify-between mb-3 pb-3 border-b border-gray-200 dark:border-gray-700">
                    <div>
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
                    </div>
                    <div class="flex items-center gap-3 text-xs text-gray-500">
                        <span><i class="fas fa-eye"></i> {{ number_format($post->views_count) }}</span>
                        <span><i class="fas fa-heart"></i> {{ number_format($post->likes_count) }}</span>
                        <span><i class="fas fa-comment"></i> {{ number_format($post->comments_count) }}</span>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('blog.show', $post->slug) }}" 
                           target="_blank"
                           class="px-3 py-1.5 text-xs bg-blue-100 text-blue-700 rounded hover:bg-blue-200">
                            <i class="fas fa-external-link-alt mr-1"></i> View
                        </a>
                        <a href="{{ route('admin.blog.posts.edit', $post->slug) }}" 
                           class="px-3 py-1.5 text-xs bg-green-100 text-green-700 rounded hover:bg-green-200">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </a>
                    </div>
                    <div class="flex items-center gap-2">
                        <button wire:click="toggleFeatured({{ $post->id }})" 
                                class="p-1.5 text-yellow-600 hover:bg-yellow-50 rounded">
                            <i class="fas {{ $post->is_featured ? 'fa-star' : 'fa-star-o' }}"></i>
                        </button>
                        <button wire:click="togglePostStatus({{ $post->id }})" 
                                class="p-1.5 text-blue-600 hover:bg-blue-50 rounded">
                            <i class="fas fa-sync"></i>
                        </button>
                        <button wire:click="confirmDelete({{ $post->id }})" 
                                class="p-1.5 text-red-600 hover:bg-red-50 rounded">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-12 text-center text-gray-500">
                <i class="fas fa-newspaper text-4xl mb-4"></i>
                <p>No posts found.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $posts->links() }}
    </div>

    {{-- Delete Modal --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-sm w-full mx-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Delete Post</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Are you sure you want to delete this post? This action cannot be undone.</p>
                <div class="flex flex-col sm:flex-row justify-end gap-3">
                    <button wire:click="$set('showDeleteModal', false)" 
                            class="px-4 py-2 text-gray-600 hover:text-gray-800 border border-gray-300 rounded hover:bg-gray-50">
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