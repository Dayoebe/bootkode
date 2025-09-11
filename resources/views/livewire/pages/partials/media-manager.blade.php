{{-- Media Manager Partial --}}
<div class="space-y-6">
    <!-- Media Header & Stats -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Media Manager</h2>
                <p class="text-gray-600 mt-1">Upload, organize, and manage your media files</p>
            </div>
            
            <div class="flex items-center space-x-4">
                <button wire:click="$set('showMediaUpload', true)"
                        class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
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
                <div class="text-2xl font-bold text-indigo-600">{{ number_format($mediaStats['total_files']) }}</div>
                <div class="text-sm text-gray-500">Total Files</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $this->getFormattedSize($mediaStats['total_size']) }}</div>
                <div class="text-sm text-gray-500">Total Size</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600">{{ number_format($mediaStats['images_count']) }}</div>
                <div class="text-sm text-gray-500">Images</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-purple-600">{{ number_format($mediaStats['videos_count']) }}</div>
                <div class="text-sm text-gray-500">Videos</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-yellow-600">{{ number_format($mediaStats['documents_count']) }}</div>
                <div class="text-sm text-gray-500">Documents</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-red-600">{{ number_format($mediaStats['unused_files']) }}</div>
                <div class="text-sm text-gray-500">Unused</div>
            </div>
        </div>
    </div>

    <!-- Filters & Controls -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <!-- Search & Filters -->
            <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4">
                <div class="relative">
                    <input type="text" 
                           wire:model.live.debounce.300ms="mediaSearch"
                           placeholder="Search media files..."
                           class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
                
                <select wire:model.live="mediaFilter" 
                        class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
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
                <div class="flex items-center bg-gray-100 rounded-lg p-1">
                    <button wire:click="$set('viewMode', 'grid')"
                            class="{{ $viewMode === 'grid' ? 'bg-white shadow-sm' : '' }} p-2 rounded-md transition-colors">
                        <i class="fas fa-th text-gray-600"></i>
                    </button>
                    <button wire:click="$set('viewMode', 'list')"
                            class="{{ $viewMode === 'list' ? 'bg-white shadow-sm' : '' }} p-2 rounded-md transition-colors">
                        <i class="fas fa-list text-gray-600"></i>
                    </button>
                </div>
                
                <select wire:model.live="sortBy" 
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="created_at">Date Added</option>
                    <option value="original_name">Name</option>
                    <option value="file_size">File Size</option>
                    <option value="media_type">Type</option>
                </select>
            </div>
        </div>

        <!-- Bulk Actions -->
        @if($showBulkActions)
            <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-center justify-between">
                    <span class="text-blue-700 font-medium">{{ count($selectedMedia) }} files selected</span>
                    <div class="flex items-center space-x-3">
                        <select wire:model="bulkAction" 
                                class="border border-blue-300 rounded px-3 py-1 text-sm">
                            <option value="">Bulk Actions...</option>
                            <option value="delete">Delete Selected</option>
                            <option value="optimize">Optimize Images</option>
                            <option value="add_tags">Add Tags</option>
                        </select>
                        <button wire:click="executeBulkAction"
                                class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                            Apply
                        </button>
                        <button wire:click="deselectAllMedia"
                                class="text-blue-600 hover:text-blue-700 text-sm">
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
                <div class="bg-white rounded-lg shadow-sm border hover:shadow-md transition-shadow">
                    <!-- Selection Checkbox -->
                    <div class="p-3 pb-0">
                        <input type="checkbox" 
                               @if(in_array($media->id, $selectedMedia)) checked @endif
                               wire:click="toggleMediaSelection({{ $media->id }})"
                               class="rounded border-gray-300 text-indigo-600">
                    </div>

                    <!-- Media Preview -->
                    <div class="px-3 pb-3">
                        <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden mb-3">
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
                            <h4 class="text-sm font-medium text-gray-900 truncate" 
                                title="{{ $media->original_name }}">
                                {{ Str::limit($media->original_name, 20) }}
                            </h4>
                            <p class="text-xs text-gray-500">{{ $media->getFormattedSize() }}</p>
                            <p class="text-xs text-gray-500">{{ $media->created_at->format('M j, Y') }}</p>
                            
                            @if($media->pages()->count() > 0)
                                <p class="text-xs text-green-600">
                                    <i class="fas fa-link text-xs mr-1"></i>
                                    Used in {{ $media->pages()->count() }} page(s)
                                </p>
                            @else
                                <p class="text-xs text-gray-400">
                                    <i class="fas fa-unlink text-xs mr-1"></i>
                                    Not used
                                </p>
                            @endif
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-between mt-3">
                            <button wire:click="editMedia({{ $media->id }})"
                                    class="text-indigo-600 hover:text-indigo-700 text-xs">
                                <i class="fas fa-edit mr-1"></i>Edit
                            </button>
                            
                            <div class="flex space-x-1">
                                <a href="{{ $media->getUrl() }}" 
                                   target="_blank"
                                   class="text-gray-400 hover:text-gray-600 text-xs">
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
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No media files found</h3>
                        <p class="text-gray-500 mb-6">Upload your first media file to get started</p>
                        <button wire:click="$set('showMediaUpload', true)"
                                class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                            Upload Files
                        </button>
                    </div>
                </div>
            @endforelse
        </div>
    @else
        <!-- List View -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">
                                <input type="checkbox" 
                                       @if(count($selectedMedia) === $mediaFiles->count() && $mediaFiles->count() > 0) checked @endif
                                       wire:click="{{ count($selectedMedia) === $mediaFiles->count() ? 'deselectAllMedia' : 'selectAllMedia' }}"
                                       class="rounded border-gray-300">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Preview
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                wire:click="sortBy('original_name')">
                                Name
                                @if($sortBy === 'original_name')
                                    <i class="ml-1 fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                wire:click="sortBy('file_size')">
                                Size
                                @if($sortBy === 'file_size')
                                    <i class="ml-1 fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Usage
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                wire:click="sortBy('created_at')">
                                Date
                                @if($sortBy === 'created_at')
                                    <i class="ml-1 fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($mediaFiles as $media)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <input type="checkbox" 
                                           @if(in_array($media->id, $selectedMedia)) checked @endif
                                           wire:click="toggleMediaSelection({{ $media->id }})"
                                           class="rounded border-gray-300">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="w-12 h-12 bg-gray-100 rounded-lg overflow-hidden">
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
                                    <div class="text-sm font-medium text-gray-900">{{ $media->original_name }}</div>
                                    @if($media->alt_text)
                                        <div class="text-sm text-gray-500">{{ $media->alt_text }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100">
                                        {{ strtoupper($media->media_type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $media->getFormattedSize() }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($media->pages()->count() > 0)
                                        <span class="text-green-600">
                                            <i class="fas fa-link mr-1"></i>
                                            {{ $media->pages()->count() }} page(s)
                                        </span>
                                    @else
                                        <span class="text-gray-400">
                                            <i class="fas fa-unlink mr-1"></i>
                                            Not used
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $media->created_at->format('M j, Y') }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <button wire:click="editMedia({{ $media->id }})"
                                                class="text-indigo-600 hover:text-indigo-900">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <a href="{{ $media->getUrl() }}" 
                                           target="_blank"
                                           class="text-gray-400 hover:text-gray-600">
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
                                    <p class="text-gray-500">No media files found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
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
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-screen overflow-y-auto">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Upload Media Files</h3>
                        <button wire:click="$set('showMediaUpload', false)" 
                                class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                        <input type="file" 
                               wire:model="bulkFiles"
                               multiple
                               accept="{{ implode(',', array_map(fn($type) => $type, $allowedTypes)) }}"
                               class="hidden"
                               id="bulk_upload">
                        
                        <label for="bulk_upload" class="cursor-pointer">
                            <i class="fas fa-cloud-upload-alt text-gray-400 text-6xl mb-4"></i>
                            <p class="text-lg text-gray-600 mb-2">Drop files here or click to upload</p>
                            <p class="text-sm text-gray-500 mb-4">
                                Supports: JPEG, PNG, GIF, WebP, MP4, PDF<br>
                                Maximum file size: {{ $maxFileSize }}MB
                            </p>
                        </label>

                        @if(count($bulkFiles) > 0)
                            <div class="mt-4 space-y-2">
                                @foreach($bulkFiles as $file)
                                    <div class="flex items-center justify-between bg-gray-50 p-2 rounded">
                                        <span class="text-sm text-gray-700">{{ $file->getClientOriginalName() }}</span>
                                        <span class="text-sm text-gray-500">{{ $this->getFormattedSize($file->getSize()) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    @error('bulkFiles.*')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                    <p class="text-sm text-gray-500">
                        {{ count($bulkFiles) }} file(s) selected
                    </p>
                    
                    <div class="flex space-x-3">
                        <button wire:click="$set('showMediaUpload', false)"
                                class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            Cancel
                        </button>
                        <button wire:click="uploadFiles"
                                @if(count($bulkFiles) === 0) disabled @endif
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed">
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
            <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Edit Media</h3>
                        <button wire:click="cancelEdit" 
                                class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Alt Text</label>
                        <input type="text" 
                               wire:model="editingAltText"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Describe this image for accessibility">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea wire:model="editingDescription"
                                  rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Optional description of this file"></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tags</label>
                        <input type="text" 
                               wire:model="editingTags"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Enter tags separated by commas">
                    </div>
                </div>
                
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                    <button wire:click="cancelEdit"
                            class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                    <button wire:click="updateMedia"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Update Media
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>