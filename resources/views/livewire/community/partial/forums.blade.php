<div>
    <!-- Search and Actions Bar -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6 animate__animated animate__fadeInUp">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <div class="flex-1 max-w-lg">
                <div class="relative">
                    <input type="text" 
                           wire:model.live.debounce.300ms="search"
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent"
                           placeholder="Search discussions...">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400 dark:text-gray-500"></i>
                </div>
            </div>
            
            <div class="flex items-center space-x-3">
                @if(auth()->user()->canManageCourses())
                    <button wire:click="$set('showCreateCategory', true)" 
                            class="bg-gray-600 dark:bg-gray-700 hover:bg-gray-700 dark:hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                        <i class="fas fa-plus mr-2"></i>Category
                    </button>
                @endif
                
                <button wire:click="$set('showCreateThread', true)" 
                        class="bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                    <i class="fas fa-plus mr-2"></i>New Discussion
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Categories Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-6 animate__animated animate__fadeInLeft">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <i class="fas fa-folder-open mr-2 text-blue-500 dark:text-blue-400"></i>
                    Categories
                </h2>
                
                <div class="space-y-2">
                    <button wire:click="selectCategory(null)" 
                            class="{{ $selectedCategory === null ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 border-blue-200 dark:border-blue-700' : 'hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300' }} w-full text-left p-3 rounded-lg border border-gray-200 dark:border-gray-700 transition-colors duration-200">
                        <div class="flex items-center justify-between">
                            <span class="font-medium">All Categories</span>
                            <span class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 px-2 py-1 rounded-full text-xs">
                                {{ $threads->total() ?? 0 }}
                            </span>
                        </div>
                    </button>
                    
                    @foreach($categories as $category)
                        <button wire:click="selectCategory({{ $category->id }})" 
                                class="{{ $selectedCategory == $category->id ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 border-blue-200 dark:border-blue-700' : 'hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300' }} w-full text-left p-3 rounded-lg border border-gray-200 dark:border-gray-700 transition-colors duration-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="{{ $category->icon }} mr-3" style="color: {{ $category->color }}"></i>
                                    <span class="font-medium">{{ $category->name }}</span>
                                </div>
                                <span class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 px-2 py-1 rounded-full text-xs">
                                    {{ $category->threads_count }}
                                </span>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-3">
            <!-- Thread List -->
            <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm animate__animated animate__fadeInUp">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $selectedCategory ? $categories->find($selectedCategory)?->name : 'All Discussions' }}
                    </h2>
                </div>
                
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($threads as $thread)
                        <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                            <div class="flex items-start space-x-4">
                                <img src="{{ $thread->user->profile_picture ?? 'https://ui-avatars.com/api/?name=' . urlencode($thread->user->name) }}" 
                                     alt="{{ $thread->user->name }}" 
                                     class="w-10 h-10 rounded-full">
                                
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-2 mb-1">
                                        @if($thread->is_pinned)
                                            <i class="fas fa-thumbtack text-red-500 dark:text-red-400"></i>
                                        @endif
                                        @if($thread->is_locked)
                                            <i class="fas fa-lock text-gray-500 dark:text-gray-400"></i>
                                        @endif
                                        <a href="{{ route('community.thread.view', $thread) }}" 
                                           class="font-semibold text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition-colors duration-200">
                                            {{ $thread->title }}
                                        </a>
                                    </div>
                                    
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                                        {{ Str::limit(strip_tags($thread->content), 120) }}
                                    </p>
                                    
                                    <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                                        <div class="flex items-center space-x-4">
                                            <span>By {{ $thread->user->name }}</span>
                                            <span>{{ $thread->created_at->diffForHumans() }}</span>
                                            <span class="flex items-center">
                                                <i class="fas fa-eye mr-1"></i>{{ $thread->views }}
                                            </span>
                                        </div>
                                        
                                        <div class="flex items-center space-x-4">
                                            <span class="flex items-center">
                                                <i class="fas fa-comments mr-1"></i>{{ $thread->replies_count }}
                                            </span>
                                            @if($thread->lastReplyUser)
                                                <span>Last by {{ $thread->lastReplyUser->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center">
                            <i class="fas fa-comments text-gray-300 dark:text-gray-600 text-4xl mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No discussions yet</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">Be the first to start a conversation!</p>
                            <button wire:click="$set('showCreateThread', true)" 
                                    class="bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                                Start Discussion
                            </button>
                        </div>
                    @endforelse
                </div>
                
                @if($threads->hasPages())
                    <div class="p-6 border-t border-gray-100 dark:border-gray-700">
                        {{ $threads->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Create Thread Modal -->
    @if($showCreateThread)
        <div class="fixed inset-0 bg-black bg-opacity-50 dark:bg-opacity-70 z-50 flex items-center justify-center p-4 animate__animated animate__fadeIn">
            <div class="bg-white dark:bg-gray-800 rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto animate__animated animate__zoomIn">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Start New Discussion</h3>
                        <button wire:click="$set('showCreateThread', false)" 
                                class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-200">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <form wire:submit.prevent="createThread" class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
                            <select wire:model="threadCategoryId" 
                                    class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent">
                                <option value="">Select a category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('threadCategoryId') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Title</label>
                            <input type="text" wire:model="threadTitle" 
                                   class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent"
                                   placeholder="Enter discussion title">
                            @error('threadTitle') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Content</label>
                            <textarea wire:model="threadContent" rows="6"
                                      class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent"
                                      placeholder="Share your thoughts or ask a question..."></textarea>
                            @error('threadContent') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-end space-x-3 mt-6">
                        <button type="button" wire:click="$set('showCreateThread', false)" 
                                class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg font-medium transition-colors duration-200">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600 text-white rounded-lg font-medium transition-colors duration-200">
                            Create Discussion
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Create Category Modal -->
    @if($showCreateCategory)
        <div class="fixed inset-0 bg-black bg-opacity-50 dark:bg-opacity-70 z-50 flex items-center justify-center p-4 animate__animated animate__fadeIn">
            <div class="bg-white dark:bg-gray-800 rounded-lg max-w-lg w-full animate__animated animate__zoomIn">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Create Category</h3>
                        <button wire:click="$set('showCreateCategory', false)" 
                                class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-200">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>
                
                <form wire:submit.prevent="createCategory" class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name</label>
                            <input type="text" wire:model="categoryName" 
                                   class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent"
                                   placeholder="Category name">
                            @error('categoryName') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                            <textarea wire:model="categoryDescription" rows="3"
                                      class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent"
                                      placeholder="Brief description"></textarea>
                            @error('categoryDescription') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Icon</label>
                                <input type="text" wire:model="categoryIcon" 
                                       class="w-full border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent"
                                       placeholder="fas fa-folder">
                                @error('categoryIcon') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Color</label>
                                <input type="color" wire:model="categoryColor" 
                                       class="w-full h-10 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent">
                                @error('categoryColor') <span class="text-red-500 dark:text-red-400 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-end space-x-3 mt-6">
                        <button type="button" wire:click="$set('showCreateCategory', false)" 
                                class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 rounded-lg font-medium transition-colors duration-200">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 dark:bg-blue-500 hover:bg-blue-700 dark:hover:bg-blue-600 text-white rounded-lg font-medium transition-colors duration-200">
                            Create Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>