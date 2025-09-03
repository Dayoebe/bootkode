<div class="space-y-6">
    <!-- Header & Controls -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Content Moderation</h2>
                <p class="text-gray-600 mt-1">Review and moderate user-generated content</p>
            </div>
            
            <div class="flex items-center space-x-3">
                <div class="flex items-center space-x-2 text-sm">
                    <span class="text-gray-500">Quick Actions:</span>
                    <button class="text-green-600 hover:text-green-800 font-medium">Approve All</button>
                    <span class="text-gray-300">|</span>
                    <button class="text-red-600 hover:text-red-800 font-medium">Bulk Review</button>
                </div>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-red-50 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-flag text-red-600 text-xl mr-3"></i>
                    <div>
                        <p class="text-2xl font-bold text-red-600">{{ $content->where('status', 'flagged')->count() }}</p>
                        <p class="text-sm text-red-700">Flagged Content</p>
                    </div>
                </div>
            </div>
            <div class="bg-yellow-50 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-clock text-yellow-600 text-xl mr-3"></i>
                    <div>
                        <p class="text-2xl font-bold text-yellow-600">{{ $content->where('status', 'pending_review')->count() }}</p>
                        <p class="text-sm text-yellow-700">Pending Review</p>
                    </div>
                </div>
            </div>
            <div class="bg-green-50 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-600 text-xl mr-3"></i>
                    <div>
                        <p class="text-2xl font-bold text-green-600">{{ $content->where('status', 'published')->count() }}</p>
                        <p class="text-sm text-green-700">Published</p>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-archive text-gray-600 text-xl mr-3"></i>
                    <div>
                        <p class="text-2xl font-bold text-gray-600">{{ $content->where('status', 'archived')->count() }}</p>
                        <p class="text-sm text-gray-700">Archived</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Content</label>
                <input 
                    wire:model.live.debounce.300ms="search" 
                    type="text" 
                    id="search"
                    placeholder="Search content..." 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                >
            </div>
            
            <div>
                <label for="selectedContentType" class="block text-sm font-medium text-gray-700 mb-1">Content Type</label>
                <select wire:model.live="selectedContentType" id="selectedContentType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    @foreach($contentTypes as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="selectedStatus" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select wire:model.live="selectedStatus" id="selectedStatus" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    @foreach($statuses as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="sortBy" class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                <select wire:model.live="sortBy" id="sortBy" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="updated_at">Last Updated</option>
                    <option value="created_at">Date Created</option>
                    <option value="title">Title</option>
                    <option value="status">Status</option>
                </select>
            </div>
            
            <div class="flex items-end">
                <button 
                    wire:click="$refresh" 
                    class="w-full bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors flex items-center justify-center space-x-2"
                >
                    <i class="fas fa-sync-alt"></i>
                    <span>Refresh</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Content List -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        @if($content->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($content as $item)
                    <div class="p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <h4 class="text-lg font-medium text-gray-900">{{ $item->title }}</h4>
                                    
                                    <!-- Content Type Badge -->
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $item->content_type === 'document' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $item->content_type === 'material' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $item->content_type === 'video' ? 'bg-purple-100 text-purple-800' : '' }}
                                    ">
                                        <i class="fas {{ $item->content_type === 'document' ? 'fa-file-alt' : ($item->content_type === 'material' ? 'fa-book' : 'fa-video') }} mr-1"></i>
                                        {{ ucfirst($item->content_type) }}
                                    </span>
                                    
                                    <!-- Status Badge -->
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $item->status === 'published' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ $item->status === 'draft' ? 'bg-gray-100 text-gray-800' : '' }}
                                        {{ $item->status === 'pending_review' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $item->status === 'flagged' ? 'bg-red-100 text-red-800' : '' }}
                                        {{ $item->status === 'archived' ? 'bg-gray-100 text-gray-600' : '' }}
                                    ">
                                        @switch($item->status)
                                            @case('published')
                                                <i class="fas fa-check-circle mr-1"></i>
                                                Published
                                                @break
                                            @case('draft')
                                                <i class="fas fa-edit mr-1"></i>
                                                Draft
                                                @break
                                            @case('pending_review')
                                                <i class="fas fa-clock mr-1"></i>
                                                Pending Review
                                                @break
                                            @case('flagged')
                                                <i class="fas fa-flag mr-1"></i>
                                                Flagged
                                                @break
                                            @case('archived')
                                                <i class="fas fa-archive mr-1"></i>
                                                Archived
                                                @break
                                            @default
                                                {{ ucfirst($item->status) }}
                                        @endswitch
                                    </span>
                                </div>
                                
                                @if($item->description ?? $item->excerpt ?? $item->content)
                                    <p class="text-gray-600 text-sm line-clamp-2 mb-3">
                                        {{ Str::limit($item->description ?? $item->excerpt ?? $item->content, 200) }}
                                    </p>
                                @endif
                                
                                <div class="flex items-center space-x-4 text-xs text-gray-500">
                                    <span>
                                        <i class="fas fa-user mr-1"></i>
                                        {{ $item->creator->name ?? $item->uploader->name ?? 'Unknown' }}
                                    </span>
                                    <span>
                                        <i class="fas fa-calendar mr-1"></i>
                                        {{ $item->created_at->format('M j, Y') }}
                                    </span>
                                    <span>
                                        <i class="fas fa-clock mr-1"></i>
                                        {{ $item->updated_at->diffForHumans() }}
                                    </span>
                                    @if($item->category ?? $item->type)
                                        <span>
                                            <i class="fas fa-tag mr-1"></i>
                                            {{ $item->category->name ?? $item->type ?? 'Uncategorized' }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="ml-6 flex items-center space-x-2">
                                <!-- Quick Actions for non-flagged content -->
                                @if($item->status !== 'published')
                                    <button 
                                        wire:click="quickApprove({{ $item->id }}, '{{ $item->content_type }}')"
                                        class="text-green-600 hover:text-green-800 bg-green-50 hover:bg-green-100 px-3 py-1 rounded text-xs font-medium transition-colors"
                                        title="Quick Approve"
                                    >
                                        <i class="fas fa-check mr-1"></i>
                                        Approve
                                    </button>
                                @endif
                                
                                <!-- Moderation Actions -->
                                <div class="relative inline-block text-left">
                                    <button 
                                        type="button" 
                                        class="text-gray-400 hover:text-gray-600 p-1"
                                        onclick="toggleModerationDropdown({{ $item->id }})"
                                    >
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div id="moderation-dropdown-{{ $item->id }}" class="hidden absolute right-0 z-10 mt-2 w-48 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5">
                                        <div class="py-1">
                                            <button 
                                                wire:click="openModerationModal({{ $item->id }}, '{{ $item->content_type }}', 'approve')" 
                                                class="w-full text-left px-4 py-2 text-sm text-green-700 hover:bg-green-50 flex items-center"
                                            >
                                                <i class="fas fa-check mr-2"></i> 
                                                Approve
                                            </button>
                                            <button 
                                                wire:click="openModerationModal({{ $item->id }}, '{{ $item->content_type }}', 'reject')" 
                                                class="w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50 flex items-center"
                                            >
                                                <i class="fas fa-times mr-2"></i> 
                                                Reject
                                            </button>
                                            <button 
                                                wire:click="openModerationModal({{ $item->id }}, '{{ $item->content_type }}', 'flag')" 
                                                class="w-full text-left px-4 py-2 text-sm text-orange-700 hover:bg-orange-50 flex items-center"
                                            >
                                                <i class="fas fa-flag mr-2"></i> 
                                                Flag Content
                                            </button>
                                            <button 
                                                wire:click="openModerationModal({{ $item->id }}, '{{ $item->content_type }}', 'archive')" 
                                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 flex items-center"
                                            >
                                                <i class="fas fa-archive mr-2"></i> 
                                                Archive
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-12 text-center">
                <i class="fas fa-shield-alt text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No content found</h3>
                <p class="text-gray-500">
                    @if($search)
                        No content matches your search and filter criteria.
                    @else
                        All content has been moderated. Great work!
                    @endif
                </p>
            </div>
        @endif
    </div>

    <!-- Moderation Modal -->
    @if($showModerationModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg max-w-2xl w-full max-h-screen overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ ucfirst($moderationAction) }} Content
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">
                                {{ ucfirst($contentType) }}: {{ $selectedContent->title ?? '' }}
                            </p>
                        </div>
                        <button wire:click="closeModerationModal" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <!-- Content Preview -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <h4 class="font-medium text-gray-900 mb-2">Content Preview</h4>
                        <div class="space-y-2">
                            <div>
                                <label class="text-sm font-medium text-gray-700">Title:</label>
                                <p class="text-gray-900">{{ $selectedContent->title ?? '' }}</p>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700">Content:</label>
                                <p class="text-gray-900 text-sm line-clamp-3">
                                    {{ Str::limit($selectedContent->content ?? $selectedContent->description ?? '', 300) }}
                                </p>
                            </div>
                            <div class="flex items-center space-x-4 text-sm text-gray-600">
                                <span>
                                    <i class="fas fa-user mr-1"></i>
                                    {{ $selectedContent->creator->name ?? $selectedContent->uploader->name ?? 'Unknown' }}
                                </span>
                                <span>
                                    <i class="fas fa-calendar mr-1"></i>
                                    {{ $selectedContent->created_at->format('M j, Y g:i A') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <form wire:submit="submitModeration" class="space-y-4">
                        <div>
                            <label for="moderationReason" class="block text-sm font-medium text-gray-700 mb-1">
                                Reason for {{ $moderationAction }} *
                            </label>
                            <textarea 
                                wire:model="moderationReason" 
                                id="moderationReason" 
                                rows="4" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" 
                                placeholder="Please provide a reason for this moderation action..."
                                required
                            ></textarea>
                            @error('moderationReason') 
                                <span class="text-red-500 text-sm">{{ $message }}</span> 
                            @enderror
                        </div>

                        <div class="flex justify-end space-x-3 pt-6 border-t">
                            <button 
                                type="button" 
                                wire:click="closeModerationModal" 
                                class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300"
                            >
                                Cancel
                            </button>
                            <button 
                                type="submit" 
                                class="px-4 py-2 
                                    {{ $moderationAction === 'approve' ? 'bg-green-600 hover:bg-green-700' : '' }}
                                    {{ $moderationAction === 'reject' ? 'bg-red-600 hover:bg-red-700' : '' }}
                                    {{ $moderationAction === 'flag' ? 'bg-orange-600 hover:bg-orange-700' : '' }}
                                    {{ $moderationAction === 'archive' ? 'bg-gray-600 hover:bg-gray-700' : '' }}
                                    text-white rounded-lg transition-colors flex items-center"
                            >
                                <span wire:loading.remove wire:target="submitModeration">
                                    {{ ucfirst($moderationAction) }} Content
                                </span>
                                <span wire:loading wire:target="submitModeration" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Processing...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
function toggleModerationDropdown(id) {
    const dropdown = document.getElementById('moderation-dropdown-' + id);
    const allDropdowns = document.querySelectorAll('[id^="moderation-dropdown-"]');
    
    // Close all other dropdowns
    allDropdowns.forEach(d => {
        if (d.id !== 'moderation-dropdown-' + id) {
            d.classList.add('hidden');
        }
    });
    
    // Toggle current dropdown
    dropdown.classList.toggle('hidden');
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('[onclick^="toggleModerationDropdown"]') && !e.target.closest('[id^="moderation-dropdown-"]')) {
        document.querySelectorAll('[id^="moderation-dropdown-"]').forEach(d => d.classList.add('hidden'));
    }
});
</script>