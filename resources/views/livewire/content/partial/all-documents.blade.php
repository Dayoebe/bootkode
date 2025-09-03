<div class="space-y-6">
    <!-- Header & Controls -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">All Documents</h2>
                <p class="text-gray-600 mt-1">Browse and manage all documents in the system</p>
            </div>
            
            <div class="flex items-center space-x-3">
                <button 
                    wire:click="toggleFilters"
                    class="text-gray-700 bg-gray-100 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors flex items-center space-x-2"
                >
                    <i class="fas fa-filter"></i>
                    <span>{{ $showFilters ? 'Hide' : 'Show' }} Filters</span>
                </button>
                
                @if($search || $selectedType || $selectedCategory || $selectedStatus || $selectedVisibility)
                    <button 
                        wire:click="clearFilters"
                        class="text-red-600 bg-red-50 px-4 py-2 rounded-lg hover:bg-red-100 transition-colors flex items-center space-x-2"
                    >
                        <i class="fas fa-times"></i>
                        <span>Clear Filters</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- Document Stats -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="bg-blue-50 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-file-alt text-blue-600 text-xl mr-3"></i>
                    <div>
                        <p class="text-2xl font-bold text-blue-600">{{ $documents->total() }}</p>
                        <p class="text-sm text-blue-700">Total Documents</p>
                    </div>
                </div>
            </div>
            <div class="bg-green-50 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-600 text-xl mr-3"></i>
                    <div>
                        <p class="text-2xl font-bold text-green-600">{{ $documents->where('status', 'published')->count() }}</p>
                        <p class="text-sm text-green-700">Published</p>
                    </div>
                </div>
            </div>
            <div class="bg-orange-50 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-clock text-orange-600 text-xl mr-3"></i>
                    <div>
                        <p class="text-2xl font-bold text-orange-600">{{ $documents->where('status', 'pending_review')->count() }}</p>
                        <p class="text-sm text-orange-700">Pending Review</p>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-edit text-gray-600 text-xl mr-3"></i>
                    <div>
                        <p class="text-2xl font-bold text-gray-600">{{ $documents->where('status', 'draft')->count() }}</p>
                        <p class="text-sm text-gray-700">Drafts</p>
                    </div>
                </div>
            </div>
            <div class="bg-purple-50 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-star text-purple-600 text-xl mr-3"></i>
                    <div>
                        <p class="text-2xl font-bold text-purple-600">{{ $documents->where('featured', true)->count() }}</p>
                        <p class="text-sm text-purple-700">Featured</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Filters -->
    @if($showFilters)
        <div class="bg-white rounded-lg shadow-sm p-6 transition-all duration-300">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Advanced Filters</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input 
                        wire:model.live.debounce.300ms="search" 
                        type="text" 
                        id="search"
                        placeholder="Search documents..." 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    >
                </div>
                
                <div>
                    <label for="selectedType" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select wire:model.live="selectedType" id="selectedType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="">All Types</option>
                        @foreach($types as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="selectedCategory" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select wire:model.live="selectedCategory" id="selectedCategory" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="selectedStatus" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select wire:model.live="selectedStatus" id="selectedStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="">All Statuses</option>
                        @foreach($statuses as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="selectedVisibility" class="block text-sm font-medium text-gray-700 mb-1">Visibility</label>
                    <select wire:model.live="selectedVisibility" id="selectedVisibility" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="">All Visibility</option>
                        @foreach($visibilities as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="sortBy" class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                    <select wire:model.live="sortBy" id="sortBy" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="created_at">Date Created</option>
                        <option value="updated_at">Last Updated</option>
                        <option value="title">Title</option>
                        <option value="views_count">Most Viewed</option>
                    </select>
                </div>
            </div>
        </div>
    @endif

    <!-- Documents Table -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        @if($documents->count() > 0)
            <!-- Table Header -->
            <div class="bg-gray-50 px-6 py-3 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Documents ({{ $documents->total() }} total)
                    </h3>
                    <div class="flex items-center space-x-2 text-sm">
                        <button wire:click="sortBy('title')" class="flex items-center space-x-1 text-gray-600 hover:text-gray-900">
                            <span>Title</span>
                            <i class="fas fa-sort {{ $sortBy === 'title' ? ($sortDirection === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : '' }}"></i>
                        </button>
                        <span class="text-gray-300">|</span>
                        <button wire:click="sortBy('created_at')" class="flex items-center space-x-1 text-gray-600 hover:text-gray-900">
                            <span>Date</span>
                            <i class="fas fa-sort {{ $sortBy === 'created_at' ? ($sortDirection === 'asc' ? 'fa-sort-up' : 'fa-sort-down') : '' }}"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Documents List -->
            <div class="divide-y divide-gray-200">
                @foreach($documents as $document)
                    <div class="p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <h4 class="text-lg font-medium text-gray-900 hover:text-indigo-600 cursor-pointer">
                                        {{ $document->title }}
                                    </h4>
                                    
                                    <!-- Document Type Badge -->
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $types[$document->type] ?? $document->type }}
                                    </span>
                                    
                                    <!-- Status Badge -->
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $document->status === 'published' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $document->status === 'draft' ? 'bg-gray-100 text-gray-800' : '' }}
                                        {{ $document->status === 'pending_review' ? 'bg-orange-100 text-orange-800' : '' }}
                                        {{ $document->status === 'archived' ? 'bg-blue-100 text-blue-800' : '' }}
                                    ">
                                        {{ $statuses[$document->status] ?? $document->status }}
                                    </span>
                                    
                                    <!-- Visibility Badge -->
                                    @if($document->visibility !== 'public')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-lock mr-1"></i>
                                            {{ $visibilities[$document->visibility] ?? $document->visibility }}
                                        </span>
                                    @endif
                                    
                                    <!-- Featured Badge -->
                                    @if($document->featured)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            <i class="fas fa-star mr-1"></i>
                                            Featured
                                        </span>
                                    @endif
                                </div>
                                
                                @if($document->excerpt)
                                    <p class="text-gray-600 text-sm line-clamp-2 mb-3">{{ $document->excerpt }}</p>
                                @endif
                                
                                <div class="flex items-center space-x-6 text-xs text-gray-500 mb-3">
                                    <span>
                                        <i class="fas fa-user mr-1"></i>
                                        {{ $document->creator->name ?? 'Unknown Author' }}
                                    </span>
                                    <span>
                                        <i class="fas fa-calendar mr-1"></i>
                                        {{ $document->created_at->format('M j, Y') }}
                                    </span>
                                    @if($document->updated_at && $document->updated_at->ne($document->created_at))
                                        <span>
                                            <i class="fas fa-edit mr-1"></i>
                                            Updated {{ $document->updated_at->diffForHumans() }}
                                        </span>
                                    @endif
                                    @if($document->category)
                                        <span>
                                            <i class="fas fa-tag mr-1"></i>
                                            {{ $document->category->name }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Document Metrics -->
                                <div class="flex items-center space-x-6 text-xs text-gray-500">
                                    @if($document->views_count ?? 0 > 0)
                                        <span>
                                            <i class="fas fa-eye mr-1"></i>
                                            {{ number_format($document->views_count) }} views
                                        </span>
                                    @endif
                                    @if($document->word_count ?? 0 > 0)
                                        <span>
                                            <i class="fas fa-file-word mr-1"></i>
                                            {{ number_format($document->word_count) }} words
                                        </span>
                                    @endif
                                    @if($document->reading_time ?? 0 > 0)
                                        <span>
                                            <i class="fas fa-clock mr-1"></i>
                                            {{ $document->reading_time }} min read
                                        </span>
                                    @endif
                                    <span>
                                        <i class="fas fa-signal mr-1"></i>
                                        {{ ucfirst($document->difficulty_level ?? 'beginner') }}
                                    </span>
                                </div>

                                <!-- Tags -->
                                @if($document->tags)
                                    <div class="flex flex-wrap gap-1 mt-2">
                                        @foreach(explode(',', $document->tags) as $tag)
                                            <span class="inline-block bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">
                                                {{ trim($tag) }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            
                            <div class="ml-6 flex items-center space-x-2">
                                <!-- Quick Actions -->
                                <div class="flex items-center space-x-1">
                                    <button class="text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 p-2 rounded transition-colors" title="View Document">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    
                                    <button class="text-green-600 hover:text-green-800 bg-green-50 hover:bg-green-100 p-2 rounded transition-colors" title="Edit Document">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    
                                    <!-- More Actions Dropdown -->
                                    <div class="relative inline-block text-left">
                                        <button 
                                            type="button" 
                                            class="text-gray-400 hover:text-gray-600 p-2 rounded hover:bg-gray-50"
                                            onclick="toggleDocumentDropdown({{ $document->id }})"
                                        >
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div id="document-dropdown-{{ $document->id }}" class="hidden absolute right-0 z-10 mt-2 w-48 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5">
                                            <div class="py-1">
                                                <button class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                                    <i class="fas fa-copy mr-2"></i> 
                                                    Duplicate
                                                </button>
                                                <button class="w-full text-left px-4 py-2 text-sm text-blue-700 hover:bg-blue-50 flex items-center">
                                                    <i class="fas fa-download mr-2"></i> 
                                                    Export
                                                </button>
                                                <button class="w-full text-left px-4 py-2 text-sm text-orange-700 hover:bg-orange-50 flex items-center">
                                                    <i class="fas fa-archive mr-2"></i> 
                                                    Archive
                                                </button>
                                                <button class="w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50 flex items-center">
                                                    <i class="fas fa-trash mr-2"></i> 
                                                    Delete
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($documents->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $documents->links() }}
                </div>
            @endif
        @else
            <div class="p-12 text-center">
                <i class="fas fa-file-alt text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No documents found</h3>
                <p class="text-gray-500 mb-4">
                    @if($search || $selectedType || $selectedCategory || $selectedStatus || $selectedVisibility)
                        No documents match your current filters. Try adjusting your search criteria.
                    @else
                        No documents have been created yet.
                    @endif
                </p>
                @if($search || $selectedType || $selectedCategory || $selectedStatus || $selectedVisibility)
                    <button wire:click="clearFilters" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                        Clear Filters
                    </button>
                @endif
            </div>
        @endif
    </div>
</div>

<script>
function toggleDocumentDropdown(id) {
    const dropdown = document.getElementById('document-dropdown-' + id);
    const allDropdowns = document.querySelectorAll('[id^="document-dropdown-"]');
    
    // Close all other dropdowns
    allDropdowns.forEach(d => {
        if (d.id !== 'document-dropdown-' + id) {
            d.classList.add('hidden');
        }
    });
    
    // Toggle current dropdown
    dropdown.classList.toggle('hidden');
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('[onclick^="toggleDocumentDropdown"]') && !e.target.closest('[id^="document-dropdown-"]')) {
        document.querySelectorAll('[id^="document-dropdown-"]').forEach(d => d.classList.add('hidden'));
    }
});
</script>