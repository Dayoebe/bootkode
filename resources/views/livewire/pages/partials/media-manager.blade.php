{{-- Media Manager Partial --}}
<div class="space-y-6">
    <!-- Media Header & Stats -->
    <div class="bg-surface rounded-lg shadow-sm p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <div>
                <h2 class="text-xl font-semibold text-content">Media Manager</h2>
                <p class="text-content-muted mt-1">Upload, organize, and manage your media files</p>
            </div>

            <div class="flex items-center space-x-4">
                <button wire:click="$set('showMediaUpload', true)"
                        class="bg-action text-action-foreground px-4 py-2 rounded-lg hover:bg-action transition-colors">
                    <i class="fas fa-cloud-upload-alt mr-2"></i>Upload Files
                </button>

                <button wire:click="optimizeAllImages"
                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-compress-alt mr-2"></i>Optimize All
                </button>

                <button wire:click="cleanupUnusedMedia"
                        wire:confirm="Are you sure you want to delete all unused media files?"
                        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-trash mr-2"></i>Cleanup
                </button>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mt-6">
            <div class="text-center">
                <div class="text-2xl font-bold text-brand">{{ number_format($mediaStats['total_files']) }}</div>
                <div class="text-sm text-content-muted">Total Files</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-brand">{{ $this->getFormattedSize($mediaStats['total_size']) }}</div>
                <div class="text-sm text-content-muted">Total Size</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600">{{ number_format($mediaStats['images_count']) }}</div>
                <div class="text-sm text-content-muted">Images</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-brand">{{ number_format($mediaStats['videos_count']) }}</div>
                <div class="text-sm text-content-muted">Videos</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-yellow-600">{{ number_format($mediaStats['documents_count']) }}</div>
                <div class="text-sm text-content-muted">Documents</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-red-600">{{ number_format($mediaStats['unused_files']) }}</div>
                <div class="text-sm text-content-muted">Unused</div>
            </div>
        </div>
    </div>

    <!-- Filters & Controls -->
    <div class="bg-surface rounded-lg shadow-sm p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <!-- Search & Filters -->
            <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4">
                <div class="relative">
                    <input aria-label="Search media files" type="text"
                           wire:model.live.debounce.300ms="mediaSearch"
                           placeholder="Search media files..."
                           class="pl-10 pr-4 py-2 border border-line rounded-lg focus:ring-brand/30 focus:border-brand">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-content-muted"></i>
                    </div>
                </div>

                <select aria-label="Media filter" wire:model.live="mediaFilter"
                        class="border border-line rounded-lg px-3 py-2 focus:ring-brand/30 focus:border-brand">
                    <option value="">All Files</option>
                    <option value="image">Images</option>
                    <option value="video">Videos</option>
                    <option value="document">Documents</option>
                    <option value="unused">Unused Files</option>
                    <option value="recent">Recent (7 days)</option>
                    <option value="large">Large Files</option>
                    <option value="unoptimized">Unoptimized</option>
                </select>
            </div>

            <!-- View Controls -->
            <div class="flex items-center space-x-4">
                <div class="flex items-center bg-surface-muted rounded-lg p-1">
                    <button wire:click="$set('viewMode', 'grid')"
                            class="{{ $viewMode === 'grid' ? 'bg-white shadow-sm' : '' }} p-2 rounded-md transition-colors">
                        <i class="fas fa-th text-content-muted"></i>
                    </button>
                    <button wire:click="$set('viewMode', 'list')"
                            class="{{ $viewMode === 'list' ? 'bg-white shadow-sm' : '' }} p-2 rounded-md transition-colors">
                        <i class="fas fa-list text-content-muted"></i>
                    </button>
                </div>

                <select aria-label="Sort by" wire:model.live="sortBy"
                        class="border border-line rounded-lg px-3 py-2 text-sm focus:ring-brand/30 focus:border-brand">
                    <option value="created_at">Date Added</option>
                    <option value="original_name">Name</option>
                    <option value="file_size">File Size</option>
                    <option value="media_type">Type</option>
                </select>
            </div>
        </div>

        <!-- Bulk Actions -->
        @if($showBulkActions)
            <div class="mt-4 p-4 bg-brand/10 border border-brand rounded-lg">
                <div class="flex items-center justify-between">
                    <span class="text-brand font-medium">{{ count($selectedMedia) }} files selected</span>
                    <div class="flex items-center space-x-3">
                        <select aria-label="Bulk action" wire:model="bulkAction"
                                class="border border-brand rounded px-3 py-1 text-sm">
                            <option value="">Bulk Actions...</option>
                            <option value="delete">Delete Selected</option>
                            <option value="optimize">Optimize Images</option>
                            <option value="add_tags">Add Tags</option>
                        </select>
                        <button wire:click="executeBulkAction"
                                class="bg-action text-action-foreground px-3 py-1 rounded text-sm hover:bg-action">
                            Apply
                        </button>
                        <button wire:click="deselectAllMedia"
                                class="text-brand hover:text-brand text-sm">
                            Clear Selection
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Media Grid/List -->
    @if($viewMode === 'grid')
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-6">
            @forelse($mediaFiles as $media)
                <div class="bg-surface rounded-lg shadow-sm border  transition-shadow">
                    <!-- Selection Checkbox -->
                    <div class="p-3 pb-0">
                        <input type="checkbox"
                               @if(in_array($media->id, $selectedMedia)) checked @endif
                               wire:click="toggleMediaSelection({{ $media->id }})"
                               class="rounded border-line text-brand">
                    </div>

                    <!-- Media Preview -->
                    <div class="px-3 pb-3">
                        <div class="aspect-square bg-surface-muted rounded-lg overflow-hidden mb-3">
                            @if($media->isImage())
                                <img src="{{ $media->getThumbnailUrl('medium') ?: $media->getUrl() }}"
                                     alt="{{ $media->alt_text ?: $media->original_name }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="{{ $media->getIconClass() }} text-4xl"></i>
                                </div>
                            @endif
                        </div>

                        <!-- Media Info -->
                        <div class="space-y-1">
                            <h4 class="text-sm font-medium text-content truncate"
                                title="{{ $media->original_name }}">
                                {{ Str::limit($media->original_name, 20) }}
                            </h4>
                            <p class="text-xs text-content-muted">{{ $media->getFormattedSize() }}</p>
                            <p class="text-xs text-content-muted">{{ $media->created_at->format('M j, Y') }}</p>

                            @if($media->pages()->count() > 0)
                                <p class="text-xs text-green-600">
                                    <i class="fas fa-link text-xs mr-1"></i>
                                    Used in {{ $media->pages()->count() }} page(s)
                                </p>
                            @else
                                <p class="text-xs text-content-muted">
                                    <i class="fas fa-unlink text-xs mr-1"></i>
                                    Not used
                                </p>
                            @endif
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-between mt-3">
                            <button wire:click="editMedia({{ $media->id }})"
                                    class="text-brand hover:text-brand text-xs">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </button>

                            <div class="flex space-x-1">
                                <a href="{{ $media->getUrl() }}"
                                   target="_blank"
                                   class="text-content-muted hover:text-content-muted text-xs">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                                <button wire:click="deleteMedia({{ $media->id }})"
                                        wire:confirm="Are you sure you want to delete this file?"
                                        class="text-red-400 hover:text-red-600 text-xs">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="text-center py-12">
                        <i class="fas fa-photo-video text-gray-300 text-6xl mb-4"></i>
                        <h3 class="text-lg font-medium text-content mb-2">No media files found</h3>
                        <p class="text-content-muted mb-6">Upload your first media file to get started</p>
                        <button wire:click="$set('showMediaUpload', true)"
                                class="bg-action text-action-foreground px-4 py-2 rounded-lg hover:bg-action">
                            Upload Files
                        </button>
                    </div>
                </div>
            @endforelse
        </div>
    @else
        <!-- List View -->
        <div class="bg-surface rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <x-ui.table label="Media Manager.Blade records"><table class="min-w-full divide-y divide-line">
                    <thead class="bg-surface-muted">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left">
                                <input type="checkbox"
                                       @if(count($selectedMedia) === $mediaFiles->count() && $mediaFiles->count() > 0) checked @endif
                                       wire:click="{{ count($selectedMedia) === $mediaFiles->count() ? 'deselectAllMedia' : 'selectAllMedia' }}"
                                       class="rounded border-line">
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">
                                Preview
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider cursor-pointer"
                                wire:click="sortBy('original_name')">
                                Name
                                @if($sortBy === 'original_name')
                                    <i class="ml-1 fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">
                                Type
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider cursor-pointer"
                                wire:click="sortBy('file_size')">
                                Size
                                @if($sortBy === 'file_size')
                                    <i class="ml-1 fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">
                                Usage
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider cursor-pointer"
                                wire:click="sortBy('created_at')">
                                Date
                                @if($sortBy === 'created_at')
                                    <i class="ml-1 fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-content-muted uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-surface divide-y divide-line">
                        @forelse($mediaFiles as $media)
                            <tr class="hover:bg-surface-muted">
                                <td class="px-6 py-4">
                                    <input type="checkbox"
                                           @if(in_array($media->id, $selectedMedia)) checked @endif
                                           wire:click="toggleMediaSelection({{ $media->id }})"
                                           class="rounded border-line">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="w-12 h-12 bg-surface-muted rounded-lg overflow-hidden">
                                        @if($media->isImage())
                                            <img src="{{ $media->getThumbnailUrl('small') ?: $media->getUrl() }}"
                                                 alt="{{ $media->alt_text ?: $media->original_name }}"
                                                 class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <i class="{{ $media->getIconClass() }} text-lg"></i>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-content">{{ $media->original_name }}</div>
                                    @if($media->alt_text)
                                        <div class="text-sm text-content-muted">{{ $media->alt_text }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-content">
                                    <span class="px-2 py-1 text-xs rounded-full bg-surface-muted">
                                        {{ strtoupper($media->media_type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-content">
                                    {{ $media->getFormattedSize() }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($media->pages()->count() > 0)
                                        <span class="text-green-600">
                                            <i class="fas fa-link mr-1"></i>
                                            {{ $media->pages()->count() }} page(s)
                                        </span>
                                    @else
                                        <span class="text-content-muted">
                                            <i class="fas fa-unlink mr-1"></i>
                                            Not used
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-content-muted">
                                    {{ $media->created_at->format('M j, Y') }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <button wire:click="editMedia({{ $media->id }})"
                                                class="text-brand hover:text-brand">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="{{ $media->getUrl() }}"
                                           target="_blank"
                                           class="text-content-muted hover:text-content-muted">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                        <button wire:click="deleteMedia({{ $media->id }})"
                                                wire:confirm="Are you sure you want to delete this file?"
                                                class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <i class="fas fa-photo-video text-gray-300 text-4xl mb-4"></i>
                                    <p class="text-content-muted">No media files found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table></x-ui.table>
            </div>
        </div>
    @endif

    <!-- Pagination -->
    @if($mediaFiles->hasPages())
        <div class="mt-6">
            {{ $mediaFiles->links() }}
        </div>
    @endif

    <!-- Upload Modal -->
    @if($showMediaUpload)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" x-trap.inert.noscroll="true" role="dialog" aria-modal="true" aria-label="Upload Media Files" tabindex="-1" x-data="{}" @keydown.escape.stop="$wire.set(&#x27;showMediaUpload&#x27;, false)">
            <div class="bg-surface rounded-lg shadow-sm max-w-2xl w-full max-h-screen overflow-y-auto">
                <div class="p-6 border-b border-line">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-content">Upload Media Files</h3>
                        <button wire:click="$set('showMediaUpload', false)"
                                class="text-content-muted hover:text-content-muted" aria-label="Close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <div class="border-2 border-dashed border-line rounded-lg p-8 text-center">
                        <input aria-label="Bulk files" type="file"
                               wire:model="bulkFiles"
                               multiple
                               accept="{{ implode(',', array_map(fn($type) => $type, $allowedTypes)) }}"
                               class="hidden"
                               id="bulk_upload">

                        <label for="bulk_upload" class="cursor-pointer">
                            <i class="fas fa-cloud-upload-alt text-content-muted text-6xl mb-4"></i>
                            <p class="text-lg text-content-muted mb-2">Drop files here or click to upload</p>
                            <p class="text-sm text-content-muted mb-4">
                                Supports: JPEG, PNG, GIF, WebP, MP4, PDF<br>
                                Maximum file size: {{ $maxFileSize }}MB
                            </p>
                        </label>

                        @if(count($bulkFiles) > 0)
                            <div class="mt-4 space-y-2">
                                @foreach($bulkFiles as $file)
                                    <div class="flex items-center justify-between bg-surface-muted p-2 rounded">
                                        <span class="text-sm text-content">{{ $file->getClientOriginalName() }}</span>
                                        <span class="text-sm text-content-muted">{{ $this->getFormattedSize($file->getSize()) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    @error('bulkFiles.*')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="px-6 py-4 bg-surface-muted border-t border-line flex items-center justify-between">
                    <p class="text-sm text-content-muted">
                        {{ count($bulkFiles) }} file(s) selected
                    </p>

                    <div class="flex space-x-3">
                        <button wire:click="$set('showMediaUpload', false)"
                                class="px-4 py-2 text-content bg-surface border border-line rounded-lg hover:bg-surface-muted">
                            Cancel
                        </button>
                        <button wire:click="uploadFiles"
                                @if(count($bulkFiles) === 0) disabled @endif
                                class="px-4 py-2 bg-action text-action-foreground rounded-lg hover:bg-action disabled:opacity-50 disabled:cursor-not-allowed">
                            Upload Files
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Edit Media Modal -->
    @if($editingMedia)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-surface rounded-lg shadow-sm max-w-lg w-full">
                <div class="p-6 border-b border-line">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-content">Edit Media</h3>
                        <button wire:click="cancelEdit"
                                class="text-content-muted hover:text-content-muted" aria-label="Close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-content mb-1">Alt Text</label>
                        <input aria-label="Describe this image for accessibility" type="text"
                               wire:model="editingAltText"
                               class="w-full border border-line rounded-lg px-3 py-2 focus:ring-brand/30 focus:border-brand"
                               placeholder="Describe this image for accessibility">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-content mb-1">Description</label>
                        <textarea aria-label="Optional description of this file" wire:model="editingDescription"
                                  rows="3"
                                  class="w-full border border-line rounded-lg px-3 py-2 focus:ring-brand/30 focus:border-brand"
                                  placeholder="Optional description of this file"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-content mb-1">Tags</label>
                        <input aria-label="Enter tags separated by commas" type="text"
                               wire:model="editingTags"
                               class="w-full border border-line rounded-lg px-3 py-2 focus:ring-brand/30 focus:border-brand"
                               placeholder="Enter tags separated by commas">
                    </div>
                </div>

                <div class="px-6 py-4 bg-surface-muted border-t border-line flex items-center justify-between">
                    <button wire:click="cancelEdit"
                            class="px-4 py-2 text-content bg-surface border border-line rounded-lg hover:bg-surface-muted">
                        Cancel
                    </button>
                    <button wire:click="updateMedia"
                            class="px-4 py-2 bg-action text-action-foreground rounded-lg hover:bg-action">
                        Update Media
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
