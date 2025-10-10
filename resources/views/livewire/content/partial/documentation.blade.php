<div class="space-y-6">
    <!-- Header & Controls -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 transition-colors duration-300">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Documentation Management</h2>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Create and manage technical documentation</p>
            </div>
            
            <button 
                wire:click="openCreateModal"
                class="bg-indigo-600 dark:bg-indigo-500 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-colors flex items-center space-x-2"
            >
                <i class="fas fa-plus"></i>
                <span>Create Document</span>
            </button>
        </div>

        <!-- Quick Actions -->
        <div class="mt-6 flex flex-wrap gap-2">
            <button class="px-3 py-1 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-full text-sm hover:bg-blue-200 dark:hover:bg-blue-900/40 transition-colors">
                <i class="fas fa-file-alt mr-1"></i>
                New Guide
            </button>
            <button class="px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-full text-sm hover:bg-green-200 dark:hover:bg-green-900/40 transition-colors">
                <i class="fas fa-question-circle mr-1"></i>
                New FAQ
            </button>
            <button class="px-3 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded-full text-sm hover:bg-purple-200 dark:hover:bg-purple-900/40 transition-colors">
                <i class="fas fa-code mr-1"></i>
                API Documentation
            </button>
            <button class="px-3 py-1 bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 rounded-full text-sm hover:bg-orange-200 dark:hover:bg-orange-900/40 transition-colors">
                <i class="fas fa-book mr-1"></i>
                Tutorial
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 transition-colors duration-300">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                <input 
                    wire:model.live.debounce.300ms="search" 
                    type="text" 
                    id="search"
                    placeholder="Search documents..." 
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 transition-colors duration-300"
                >
            </div>
            
            <div>
                <label for="selectedType" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                <select wire:model.live="selectedType" id="selectedType" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 transition-colors duration-300">
                    <option value="">All Types</option>
                    @foreach($types as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="selectedCategory" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                <select wire:model.live="selectedCategory" id="selectedCategory" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 transition-colors duration-300">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="selectedStatus" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                <select wire:model.live="selectedStatus" id="selectedStatus" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 transition-colors duration-300">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="sortBy" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sort By</label>
                <select wire:model.live="sortBy" id="sortBy" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 transition-colors duration-300">
                    <option value="created_at">Date Created</option>
                    <option value="updated_at">Last Updated</option>
                    <option value="title">Title</option>
                    <option value="views_count">Most Viewed</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Documents Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($documents as $document)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
                <!-- Document Header -->
                <div class="p-6">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                <!-- Type Icon -->
                                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/30">
                                    @switch($document->type)
                                        @case('guide')
                                            <i class="fas fa-book text-indigo-600 dark:text-indigo-400"></i>
                                            @break
                                        @case('faq')
                                            <i class="fas fa-question-circle text-indigo-600 dark:text-indigo-400"></i>
                                            @break
                                        @case('api')
                                            <i class="fas fa-code text-indigo-600 dark:text-indigo-400"></i>
                                            @break
                                        @case('tutorial')
                                            <i class="fas fa-graduation-cap text-indigo-600 dark:text-indigo-400"></i>
                                            @break
                                        @default
                                            <i class="fas fa-file-alt text-indigo-600 dark:text-indigo-400"></i>
                                    @endswitch
                                </div>
                                
                                <!-- Status Badge -->
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $document->status === 'published' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : '' }}
                                    {{ $document->status === 'draft' ? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300' : '' }}
                                    {{ $document->status === 'pending_review' ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300' : '' }}
                                    {{ $document->status === 'archived' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' : '' }}
                                ">
                                    {{ $statuses[$document->status] ?? $document->status }}
                                </span>

                                @if($document->featured)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">
                                        <i class="fas fa-star mr-1"></i>
                                        Featured
                                    </span>
                                @endif
                            </div>
                            
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1 line-clamp-2">{{ $document->title }}</h3>
                            
                            @if($document->excerpt)
                                <p class="text-gray-600 dark:text-gray-400 text-sm line-clamp-3 mb-3">{{ $document->excerpt }}</p>
                            @endif
                        </div>
                        
                        <!-- Actions Dropdown -->
                        <div class="relative inline-block text-left">
                            <button 
                                type="button" 
                                class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 p-1 transition-colors"
                                onclick="toggleDocDropdown({{ $document->id }})"
                            >
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div id="doc-dropdown-{{ $document->id }}" class="hidden absolute right-0 z-10 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 dark:ring-gray-700">
                                <div class="py-1">
                                    <button wire:click="openEditModal({{ $document->id }})" class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 flex items-center transition-colors">
                                        <i class="fas fa-edit mr-2"></i> Edit
                                    </button>
                                    <button wire:click="toggleFeatured({{ $document->id }})" class="w-full text-left px-4 py-2 text-sm text-yellow-700 dark:text-yellow-400 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 flex items-center transition-colors">
                                        <i class="fas {{ $document->featured ? 'fa-star-half-alt' : 'fa-star' }} mr-2"></i> 
                                        {{ $document->featured ? 'Unfeature' : 'Feature' }}
                                    </button>
                                    <button wire:click="changeStatus({{ $document->id }}, 'published')" class="w-full text-left px-4 py-2 text-sm text-green-700 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 flex items-center transition-colors">
                                        <i class="fas fa-check mr-2"></i> Publish
                                    </button>
                                    <button wire:click="changeStatus({{ $document->id }}, 'archived')" class="w-full text-left px-4 py-2 text-sm text-blue-700 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 flex items-center transition-colors">
                                        <i class="fas fa-archive mr-2"></i> Archive
                                    </button>
                                    <button wire:click="openDeleteModal({{ $document->id }})" class="w-full text-left px-4 py-2 text-sm text-red-700 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center transition-colors">
                                        <i class="fas fa-trash mr-2"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Document Metadata -->
                    <div class="space-y-2 text-xs text-gray-500 dark:text-gray-400">
                        <div class="flex items-center justify-between">
                            <span>
                                <i class="fas fa-user mr-1"></i>
                                {{ $document->creator->name ?? 'Unknown' }}
                            </span>
                            @if($document->difficulty_level)
                                <span class="px-2 py-1 rounded text-xs font-medium
                                    {{ $document->difficulty_level === 'beginner' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300' : '' }}
                                    {{ $document->difficulty_level === 'intermediate' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300' : '' }}
                                    {{ $document->difficulty_level === 'advanced' ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300' : '' }}
                                ">
                                    {{ ucfirst($document->difficulty_level) }}
                                </span>
                            @endif
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span>
                                <i class="fas fa-calendar mr-1"></i>
                                {{ $document->created_at->format('M j, Y') }}
                            </span>
                            @if($document->views_count ?? 0 > 0)
                                <span>
                                    <i class="fas fa-eye mr-1"></i>
                                    {{ number_format($document->views_count) }}
                                </span>
                            @endif
                        </div>

                        @if($document->category)
                            <div class="flex items-center">
                                <i class="fas fa-tag mr-1"></i>
                                <span>{{ $document->category->name }}</span>
                            </div>
                        @endif

                        @if($document->updated_at && $document->updated_at->ne($document->created_at))
                            <div class="flex items-center">
                                <i class="fas fa-edit mr-1"></i>
                                <span>Updated {{ $document->updated_at->diffForHumans() }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Tags -->
                    @if($document->tags)
                        <div class="flex flex-wrap gap-1 mt-3">
                            @foreach(explode(',', $document->tags) as $tag)
                                <span class="inline-block bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs px-2 py-1 rounded">
                                    {{ trim($tag) }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Document Footer -->
                <div class="px-6 py-3 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between transition-colors duration-300">
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $types[$document->type] ?? ucfirst($document->type) }}
                        @if($document->visibility !== 'public')
                            • {{ $visibilities[$document->visibility] ?? $document->visibility }}
                        @endif
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <button class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-xs font-medium transition-colors">
                            View
                        </button>
                        <button wire:click="openEditModal({{ $document->id }})" class="text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 text-xs font-medium transition-colors">
                            Edit
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="text-center py-12">
                    <i class="fas fa-file-alt text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No documentation found</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">
                        @if($search || $selectedType || $selectedCategory || $selectedStatus)
                            No documents match your current filters.
                        @else
                            Start building your documentation library.
                        @endif
                    </p>
                    <button wire:click="openCreateModal" class="bg-indigo-600 dark:bg-indigo-500 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-colors">
                        Create First Document
                    </button>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($documents->hasPages())
        <div class="mt-8">
            {{ $documents->links() }}
        </div>
    @endif

    <!-- Create Document Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg max-w-4xl w-full max-h-screen overflow-y-auto transition-colors duration-300">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Create New Document</h3>
                        <button wire:click="closeCreateModal" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <form wire:submit="save" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title *</label>
                                <input 
                                    wire:model="title" 
                                    type="text" 
                                    id="title" 
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 transition-colors duration-300" 
                                    required
                                >
                                @error('title') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type *</label>
                                <select wire:model="type" id="type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 transition-colors duration-300" required>
                                    <option value="">Select Type</option>
                                    @foreach($types as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('type') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                                <select wire:model="category_id" id="category_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 transition-colors duration-300">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="difficulty_level" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Difficulty Level</label>
                                <select wire:model="difficulty_level" id="difficulty_level" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 transition-colors duration-300">
                                    @foreach($difficultyLevels as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                                <select wire:model="status" id="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 transition-colors duration-300">
                                    @foreach($statuses as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label for="excerpt" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Excerpt</label>
                                <textarea 
                                    wire:model="excerpt" 
                                    id="excerpt" 
                                    rows="3" 
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 transition-colors duration-300"
                                    placeholder="Brief description or summary..."
                                ></textarea>
                                @error('excerpt') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Content *</label>
                                <textarea 
                                    wire:model="content" 
                                    id="content" 
                                    rows="10" 
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 transition-colors duration-300" 
                                    placeholder="Write your documentation content here..."
                                    required
                                ></textarea>
                                @error('content') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="tags" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tags</label>
                                <input 
                                    wire:model="tags" 
                                    type="text" 
                                    id="tags" 
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 transition-colors duration-300"
                                    placeholder="tag1, tag2, tag3"
                                >
                            </div>

                            <div>
                                <label for="visibility" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Visibility</label>
                                <select wire:model="visibility" id="visibility" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 transition-colors duration-300">
                                    @foreach($visibilities as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex items-end">
                                <label class="flex items-center">
                                    <input wire:model="featured" type="checkbox" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500 bg-white dark:bg-gray-700">
                                    <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Featured document</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <button type="button" wire:click="closeCreateModal" class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 dark:bg-indigo-500 text-white rounded-lg hover:bg-indigo-700 dark:hover:bg-indigo-600 flex items-center transition-colors">
                                <span wire:loading.remove wire:target="save">Create Document</span>
                                <span wire:loading wire:target="save" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Creating...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Edit Document Modal -->
    @if($showEditModal && $selectedDocument)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg max-w-4xl w-full max-h-screen overflow-y-auto transition-colors duration-300">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Edit Document</h3>
                        <button wire:click="closeEditModal" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <form wire:submit="update" class="space-y-6">
                        <!-- Same form fields as create modal -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label for="edit_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title *</label>
                                <input 
                                    wire:model="title" 
                                    type="text" 
                                    id="edit_title" 
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 transition-colors duration-300" 
                                    required
                                >
                                @error('title') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="edit_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type *</label>
                                <select wire:model="type" id="edit_type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 transition-colors duration-300" required>
                                    @foreach($types as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('type') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="edit_category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
                                <select wire:model="category_id" id="edit_category_id" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 transition-colors duration-300">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="edit_difficulty_level" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Difficulty Level</label>
                                <select wire:model="difficulty_level" id="edit_difficulty_level" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 transition-colors duration-300">
                                    @foreach($difficultyLevels as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="edit_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                                <select wire:model="status" id="edit_status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 transition-colors duration-300">
                                    @foreach($statuses as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label for="edit_excerpt" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Excerpt</label>
                                <textarea 
                                    wire:model="excerpt" 
                                    id="edit_excerpt" 
                                    rows="3" 
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 transition-colors duration-300"
                                ></textarea>
                                @error('excerpt') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="edit_content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Content *</label>
                                <textarea 
                                    wire:model="content" 
                                    id="edit_content" 
                                    rows="10" 
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 transition-colors duration-300" 
                                    required
                                ></textarea>
                                @error('content') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="edit_tags" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tags</label>
                                <input 
                                    wire:model="tags" 
                                    type="text" 
                                    id="edit_tags" 
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 transition-colors duration-300"
                                >
                            </div>

                            <div>
                                <label for="edit_visibility" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Visibility</label>
                                <select wire:model="visibility" id="edit_visibility" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 transition-colors duration-300">
                                    @foreach($visibilities as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex items-end">
                                <label class="flex items-center">
                                    <input wire:model="featured" type="checkbox" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500 bg-white dark:bg-gray-700">
                                    <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Featured document</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <button type="button" wire:click="closeEditModal" class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 dark:bg-indigo-500 text-white rounded-lg hover:bg-indigo-700 dark:hover:bg-indigo-600 flex items-center transition-colors">
                                <span wire:loading.remove wire:target="update">Update Document</span>
                                <span wire:loading wire:target="update" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Updating...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal && $selectedDocument)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg max-w-md w-full transition-colors duration-300">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30">
                            <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400"></i>
                        </div>
                    </div>
                    <div class="text-center">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Delete Document</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                            Are you sure you want to delete "{{ $selectedDocument->title }}"? This action cannot be undone.
                        </p>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button wire:click="closeDeleteModal" class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                            Cancel
                        </button>
                        <button wire:click="delete" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 flex items-center transition-colors">
                            <span wire:loading.remove wire:target="delete">Delete</span>
                            <span wire:loading wire:target="delete" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Deleting...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
function toggleDocDropdown(id) {
    const dropdown = document.getElementById('doc-dropdown-' + id);
    const allDropdowns = document.querySelectorAll('[id^="doc-dropdown-"]');
    
    // Close all other dropdowns
    allDropdowns.forEach(d => {
        if (d.id !== 'doc-dropdown-' + id) {
            d.classList.add('hidden');
        }
    });
    
    // Toggle current dropdown
    dropdown.classList.toggle('hidden');
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('[onclick^="toggleDocDropdown"]') && !e.target.closest('[id^="doc-dropdown-"]')) {
        document.querySelectorAll('[id^="doc-dropdown-"]').forEach(d => d.classList.add('hidden'));
    }
});
</script>