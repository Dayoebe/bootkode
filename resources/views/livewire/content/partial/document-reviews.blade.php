<div class="space-y-6">
    <!-- Header & Controls -->
    <div class="bg-surface dark:bg-gray-800 rounded-lg shadow-sm p-6 transition-colors duration-300">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
            <div>
                <h2 class="text-2xl font-bold text-content dark:text-white">Document Reviews</h2>
                <p class="text-content-muted dark:text-content-muted mt-1">Review and approve submitted documents</p>
            </div>

            <div class="flex items-center space-x-3">
                <div class="flex items-center space-x-2 text-sm">
                    <span class="text-content-muted dark:text-content-muted">Pending Reviews:</span>
                    <span class="bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200 px-2 py-1 rounded-full font-medium">
                        {{ $documents->where('status', 'pending_review')->count() }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-4 transition-colors duration-300">
                <div class="flex items-center">
                    <i class="fas fa-clock text-orange-600 dark:text-orange-400 text-xl mr-3" aria-hidden="true"></i>
                    <div>
                        <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ $documents->where('status', 'pending_review')->count() }}</p>
                        <p class="text-sm text-orange-700 dark:text-orange-300">Pending Review</p>
                    </div>
                </div>
            </div>
            <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4 transition-colors duration-300">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl mr-3" aria-hidden="true"></i>
                    <div>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $documents->where('status', 'published')->count() }}</p>
                        <p class="text-sm text-green-700 dark:text-green-300">Published</p>
                    </div>
                </div>
            </div>
            <div class="bg-surface-muted dark:bg-gray-700 rounded-lg p-4 transition-colors duration-300">
                <div class="flex items-center">
                    <i class="fas fa-edit text-content-muted dark:text-content-muted text-xl mr-3" aria-hidden="true"></i>
                    <div>
                        <p class="text-2xl font-bold text-content-muted dark:text-gray-300">{{ $documents->where('status', 'draft')->count() }}</p>
                        <p class="text-sm text-content dark:text-content-muted">Draft</p>
                    </div>
                </div>
            </div>
            <div class="bg-brand/10 dark:bg-action/20 rounded-lg p-4 transition-colors duration-300">
                <div class="flex items-center">
                    <i class="fas fa-archive text-brand dark:text-brand text-xl mr-3" aria-hidden="true"></i>
                    <div>
                        <p class="text-2xl font-bold text-brand dark:text-brand">{{ $documents->where('status', 'archived')->count() }}</p>
                        <p class="text-sm text-brand dark:text-brand">Archived</p>
                    </div>
                </div>
            </div>
            <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4 transition-colors duration-300">
                <div class="flex items-center">
                    <i class="fas fa-times-circle text-red-600 dark:text-red-400 text-xl mr-3" aria-hidden="true"></i>
                    <div>
                        <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $documents->where('status', 'deprecated')->count() }}</p>
                        <p class="text-sm text-red-700 dark:text-red-300">Deprecated</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-surface dark:bg-gray-800 rounded-lg shadow-sm p-6 transition-colors duration-300">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Search Documents</label>
                <input
                    wire:model.live.debounce.300ms="search"
                    type="text"
                    id="search"
                    placeholder="Search by title, content, or author..."
                    class="w-full px-3 py-2 border border-line dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand/30 bg-surface dark:bg-gray-700 text-content dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500"
                >
            </div>

            <div>
                <label for="selectedStatus" class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Status</label>
                <select wire:model.live="selectedStatus" id="selectedStatus" class="w-full px-3 py-2 border border-line dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand/30 bg-surface dark:bg-gray-700 text-content dark:text-gray-100">
                    @foreach($statuses as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="sortBy" class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Sort By</label>
                <select wire:model.live="sortBy" id="sortBy" class="w-full px-3 py-2 border border-line dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand/30 bg-surface dark:bg-gray-700 text-content dark:text-gray-100">
                    <option value="updated_at">Last Updated</option>
                    <option value="created_at">Date Created</option>
                    <option value="title">Title</option>
                </select>
            </div>

            <div class="flex items-end">
                <button
                    wire:click="$refresh"
                    class="w-full bg-surface-muted dark:bg-gray-700 text-content dark:text-gray-300 px-4 py-2 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors flex items-center justify-center space-x-2"
                >
                    <i class="fas fa-sync-alt" aria-hidden="true"></i>
                    <span>Refresh</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Documents List -->
    <div class="bg-surface dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden transition-colors duration-300">
        @if($documents->count() > 0)
            <div class="divide-y divide-line dark:divide-gray-700">
                @foreach($documents as $document)
                    <div class="p-6 hover:bg-surface-muted dark:hover:bg-gray-700 transition-colors">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <h4 class="text-lg font-medium text-content dark:text-white">{{ $document->title }}</h4>

                                    <!-- Status Badge -->
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $document->status === 'published' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : '' }}
                                        {{ $document->status === 'draft' ? 'bg-gray-100 dark:bg-gray-700 text-content dark:text-gray-300' : '' }}
                                        {{ $document->status === 'pending_review' ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300' : '' }}
                                        {{ $document->status === 'archived' ? 'bg-blue-100 dark:bg-action/30 text-brand dark:text-blue-300' : '' }}
                                        {{ $document->status === 'deprecated' ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' : '' }}
                                    ">
                                        @switch($document->status)
                                            @case('published')
                                                <i class="fas fa-check-circle mr-1" aria-hidden="true"></i>
                                                Published
                                                @break
                                            @case('draft')
                                                <i class="fas fa-edit mr-1" aria-hidden="true"></i>
                                                Draft
                                                @break
                                            @case('pending_review')
                                                <i class="fas fa-clock mr-1" aria-hidden="true"></i>
                                                Pending Review
                                                @break
                                            @case('archived')
                                                <i class="fas fa-archive mr-1" aria-hidden="true"></i>
                                                Archived
                                                @break
                                            @case('deprecated')
                                                <i class="fas fa-times-circle mr-1" aria-hidden="true"></i>
                                                Deprecated
                                                @break
                                            @default
                                                {{ ucfirst($document->status) }}
                                        @endswitch
                                    </span>

                                    <!-- Priority for pending review -->
                                    @if($document->status === 'pending_review' && $document->created_at->diffInDays() > 3)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                                            <i class="fas fa-exclamation-triangle mr-1" aria-hidden="true"></i>
                                            Overdue
                                        </span>
                                    @endif
                                </div>

                                @if($document->excerpt)
                                    <p class="text-content-muted dark:text-content-muted text-sm line-clamp-2 mb-3">
                                        {{ $document->excerpt }}
                                    </p>
                                @endif

                                <div class="flex items-center space-x-4 text-xs text-content-muted dark:text-content-muted mb-3">
                                    <span>
                                        <i class="fas fa-user mr-1" aria-hidden="true"></i>
                                        {{ $document->creator->name ?? 'Unknown Author' }}
                                    </span>
                                    <span>
                                        <i class="fas fa-calendar mr-1" aria-hidden="true"></i>
                                        Created {{ $document->created_at->format('M j, Y') }}
                                    </span>
                                    @if($document->updated_at && $document->updated_at->ne($document->created_at))
                                        <span>
                                            <i class="fas fa-edit mr-1" aria-hidden="true"></i>
                                            Updated {{ $document->updated_at->diffForHumans() }}
                                        </span>
                                    @endif
                                    @if($document->category)
                                        <span>
                                            <i class="fas fa-tag mr-1" aria-hidden="true"></i>
                                            {{ $document->category->name }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Review History -->
                                @if($document->reviewer)
                                    <div class="bg-brand/10 dark:bg-action/20 rounded-lg p-3 text-sm transition-colors duration-300">
                                        <div class="flex items-center space-x-2">
                                            <i class="fas fa-history text-brand dark:text-brand" aria-hidden="true"></i>
                                            <span class="font-medium text-brand dark:text-brand">Review History:</span>
                                        </div>
                                        <p class="text-brand dark:text-brand mt-1">
                                            Last reviewed by {{ $document->reviewer->name }}
                                            on {{ $document->reviewed_at->format('M j, Y g:i A') }}
                                        </p>
                                        @if($document->review_comments)
                                            <p class="text-brand dark:text-brand mt-1">{{ $document->review_comments }}</p>
                                        @endif
                                    </div>
                                @endif

                                <!-- Document Metrics -->
                                <div class="flex items-center space-x-4 text-xs text-content-muted dark:text-content-muted mt-3">
                                    @if($document->word_count ?? false)
                                        <span>
                                            <i class="fas fa-file-word mr-1" aria-hidden="true"></i>
                                            {{ number_format($document->word_count) }} words
                                        </span>
                                    @endif
                                    @if($document->reading_time ?? false)
                                        <span>
                                            <i class="fas fa-clock mr-1" aria-hidden="true"></i>
                                            {{ $document->reading_time }} min read
                                        </span>
                                    @endif
                                    @if($document->views_count ?? 0 > 0)
                                        <span>
                                            <i class="fas fa-eye mr-1" aria-hidden="true"></i>
                                            {{ number_format($document->views_count) }} views
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="ml-6 flex items-center space-x-2">
                                <!-- Quick Actions for pending review -->
                                @if($document->status === 'pending_review')
                                    <button
                                        wire:click="quickApprove({{ $document->id }})"
                                        class="text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/30 px-3 py-1 rounded text-xs font-medium transition-colors"
                                        title="Quick Approve"
                                    >
                                        <i class="fas fa-check mr-1" aria-hidden="true"></i>
                                        Quick Approve
                                    </button>
                                @endif

                                <!-- Review Actions -->
                                <div class="flex items-center space-x-1">
                                    <button
                                        wire:click="openReviewModal({{ $document->id }}, 'approve')"
                                        class="text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/30 p-2 rounded transition-colors"
                                        title="Approve Document"
                                    >
                                        <i class="fas fa-thumbs-up" aria-hidden="true"></i>
                                    </button>

                                    <button
                                        wire:click="openReviewModal({{ $document->id }}, 'reject')"
                                        class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 p-2 rounded transition-colors"
                                        title="Reject Document"
                                    >
                                        <i class="fas fa-thumbs-down" aria-hidden="true"></i>
                                    </button>

                                    <!-- More Actions Dropdown -->
                                    <div class="relative inline-block text-left">
                                        <button
                                            type="button"
                                            class="text-content-muted dark:text-content-muted hover:text-content-muted dark:hover:text-gray-300 p-2 rounded hover:bg-surface-muted dark:hover:bg-gray-700"
                                            onclick="toggleReviewDropdown({{ $document->id }})"
                                        >
                                            <i class="fas fa-ellipsis-v" aria-hidden="true"></i>
                                        </button>
                                        <div id="review-dropdown-{{ $document->id }}" class="hidden absolute right-0 z-10 mt-2 w-48 bg-surface dark:bg-gray-800 rounded-md shadow-sm ring-1 ring-black ring-opacity-5 dark:ring-gray-700">
                                            <div class="py-1">
                                                <button class="w-full text-left px-4 py-2 text-sm text-content dark:text-gray-300 hover:bg-surface-muted dark:hover:bg-gray-700 flex items-center">
                                                    <i class="fas fa-eye mr-2" aria-hidden="true"></i>
                                                    Preview Document
                                                </button>
                                                <button class="w-full text-left px-4 py-2 text-sm text-brand dark:text-brand hover:bg-brand/10 dark:hover:bg-action/20 flex items-center">
                                                    <i class="fas fa-edit mr-2" aria-hidden="true"></i>
                                                    Edit Document
                                                </button>
                                                <button class="w-full text-left px-4 py-2 text-sm text-orange-700 dark:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-900/20 flex items-center">
                                                    <i class="fas fa-history mr-2" aria-hidden="true"></i>
                                                    View History
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
                <div class="px-6 py-4 border-t border-line dark:border-gray-700">
                    {{ $documents->links() }}
                </div>
            @endif
        @else
            <div class="p-12 text-center">
                <i class="fas fa-star text-6xl text-gray-300 dark:text-content-muted mb-4" aria-hidden="true"></i>
                <h3 class="text-lg font-medium text-content dark:text-white mb-2">No documents found</h3>
                <p class="text-content-muted dark:text-content-muted">
                    @if($search)
                        No documents match your search criteria.
                    @elseif($selectedStatus === 'pending_review')
                        All documents have been reviewed. Great work!
                    @else
                        No documents with the selected status.
                    @endif
                </p>
            </div>
        @endif
    </div>

    <!-- Review Modal -->
    @if($showReviewModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" x-trap.inert.noscroll="true" role="dialog" aria-modal="true" aria-label="Review modal" tabindex="-1" x-data="{}" @keydown.escape.stop="$wire.set(&#x27;showReviewModal&#x27;, false)">
            <div class="bg-surface dark:bg-gray-800 rounded-lg max-w-3xl w-full max-h-screen overflow-y-auto transition-colors duration-300">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-content dark:text-white">
                                {{ ucfirst($reviewAction) }} Document
                            </h3>
                            <p class="text-sm text-content-muted dark:text-content-muted mt-1">
                                {{ $selectedDocument->title ?? '' }}
                            </p>
                        </div>
                        <button wire:click="closeReviewModal" class="text-content-muted dark:text-content-muted hover:text-content-muted dark:hover:text-gray-300" aria-label="Close">
                            <i class="fas fa-times text-xl" aria-hidden="true"></i>
                        </button>
                    </div>

                    <!-- Document Preview -->
                    <div class="bg-surface-muted dark:bg-gray-700 rounded-lg p-6 mb-6 transition-colors duration-300">
                        <div class="space-y-4">
                            <div>
                                <h4 class="font-medium text-content dark:text-white text-lg">{{ $selectedDocument->title ?? '' }}</h4>
                                <div class="flex items-center space-x-4 text-sm text-content-muted dark:text-content-muted mt-2">
                                    <span>
                                        <i class="fas fa-user mr-1" aria-hidden="true"></i>
                                        {{ $selectedDocument->creator->name ?? 'Unknown' }}
                                    </span>
                                    <span>
                                        <i class="fas fa-calendar mr-1" aria-hidden="true"></i>
                                        {{ $selectedDocument->created_at->format('M j, Y g:i A') }}
                                    </span>
                                    @if($selectedDocument->category)
                                        <span>
                                            <i class="fas fa-tag mr-1" aria-hidden="true"></i>
                                            {{ $selectedDocument->category->name }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if($selectedDocument->excerpt ?? false)
                                <div>
                                    <label class="text-sm font-medium text-content dark:text-gray-300">Excerpt:</label>
                                    <p class="text-content dark:text-gray-200 mt-1">{{ $selectedDocument->excerpt }}</p>
                                </div>
                            @endif

                            <div>
                                <label class="text-sm font-medium text-content dark:text-gray-300">Content Preview:</label>
                                <div class="text-content dark:text-gray-200 mt-1 max-h-32 overflow-y-auto bg-surface dark:bg-gray-800 p-3 rounded border border-line dark:border-gray-600">
                                    {{ Str::limit($selectedDocument->content ?? '', 500) }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <form wire:submit="submitReview" class="space-y-4">
                        <div>
                            <label for="reviewComments" class="block text-sm font-medium text-content dark:text-gray-300 mb-1">
                                Review Comments
                                @if($reviewAction === 'reject')
                                    <span class="text-red-500">*</span>
                                @endif
                            </label>
                            <textarea
                                wire:model="reviewComments"
                                id="reviewComments"
                                rows="4"
                                class="w-full px-3 py-2 border border-line dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand/30 bg-surface dark:bg-gray-700 text-content dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500"
                                placeholder="{{ $reviewAction === 'approve' ? 'Optional feedback for the author...' : 'Please explain why this document is being rejected...' }}"
                                {{ $reviewAction === 'reject' ? 'required' : '' }}
                            ></textarea>
                            @error('reviewComments')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="flex justify-end space-x-3 pt-6 border-t border-line dark:border-gray-700">
                            <button
                                type="button"
                                wire:click="closeReviewModal"
                                class="px-4 py-2 text-content dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="px-4 py-2
                                    {{ $reviewAction === 'approve' ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700' }}
                                    text-white rounded-lg transition-colors flex items-center"
                            >
                                <span wire:loading.remove wire:target="submitReview">
                                    <i class="fas {{ $reviewAction === 'approve' ? 'fa-check' : 'fa-times' }} mr-2" aria-hidden="true"></i>
                                    {{ ucfirst($reviewAction) }} Document
                                </span>
                                <span wire:loading wire:target="submitReview" class="flex items-center">
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
function toggleReviewDropdown(id) {
    const dropdown = document.getElementById('review-dropdown-' + id);
    const allDropdowns = document.querySelectorAll('[id^="review-dropdown-"]');

    // Close all other dropdowns
    allDropdowns.forEach(d => {
        if (d.id !== 'review-dropdown-' + id) {
            d.classList.add('hidden');
        }
    });

    // Toggle current dropdown
    dropdown.classList.toggle('hidden');
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('[onclick^="toggleReviewDropdown"]') && !e.target.closest('[id^="review-dropdown-"]')) {
        document.querySelectorAll('[id^="review-dropdown-"]').forEach(d => d.classList.add('hidden'));
    }
});
</script>
