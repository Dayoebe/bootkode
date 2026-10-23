<div class="space-y-6">
    <!-- Header & Controls -->
    <div class="bg-surface dark:bg-gray-800 rounded-lg shadow-sm p-6 transition-colors duration-300">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
            <div>
                <h2 class="text-2xl font-bold text-content dark:text-white">Video Library</h2>
                <p class="text-content-muted dark:text-content-muted mt-1">Manage educational videos and multimedia content</p>
            </div>

            <button
                wire:click="openCreateModal"
                class="bg-action dark:bg-action text-action-foreground px-4 py-2 rounded-lg hover:bg-action dark:hover:bg-action transition-colors flex items-center space-x-2"
            >
                <i class="fas fa-plus" aria-hidden="true"></i>
                <span>Add New Video</span>
            </button>
        </div>

        <!-- Filters -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <input
 aria-label="Search videos"                    wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="Search videos..."
                    class="w-full px-3 py-2 border border-line dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand/30 focus:border-transparent bg-surface dark:bg-gray-700 text-content dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500"
                >
            </div>

            <div>
                <select aria-label="Selected category" wire:model.live="selectedCategory" class="w-full px-3 py-2 border border-line dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand/30 bg-surface dark:bg-gray-700 text-content dark:text-gray-100">
                    <option value="">All Categories</option>
                    @foreach($categories as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <select aria-label="Selected course" wire:model.live="selectedCourse" class="w-full px-3 py-2 border border-line dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand/30 bg-surface dark:bg-gray-700 text-content dark:text-gray-100">
                    <option value="">All Courses</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <select aria-label="Sort by" wire:model.live="sortBy" class="w-full px-3 py-2 border border-line dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand/30 bg-surface dark:bg-gray-700 text-content dark:text-gray-100">
                    <option value="created_at">Sort by Date</option>
                    <option value="title">Sort by Title</option>
                    <option value="views_count">Sort by Views</option>
                    <option value="likes_count">Sort by Likes</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Videos Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($videos as $video)
            <div class="bg-surface dark:bg-gray-800 rounded-lg shadow-sm  transition-all duration-300 overflow-hidden">
                <!-- Video Thumbnail -->
                <div class="relative h-48 bg-gray-200 dark:bg-gray-700">
                    @if($video->thumbnail_url)
                        <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}" class="w-full h-full object-cover">
                    @else
                        <div class="flex items-center justify-center h-full">
                            <i class="fas fa-video text-6xl text-content-muted dark:text-content-muted" aria-hidden="true"></i>
                        </div>
                    @endif

                    <!-- Video Type Badge -->
                    <div class="absolute top-2 left-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-black bg-opacity-75 text-white">
                            <i class="fas {{ $video->video_type === 'youtube' ? 'fa-youtube' : ($video->video_type === 'vimeo' ? 'fa-vimeo' : 'fa-video') }} mr-1" aria-hidden="true"></i>
                            {{ $videoTypes[$video->video_type] ?? $video->video_type }}
                        </span>
                    </div>

                    <!-- Duration (if available) -->
                    @if($video->formatted_duration)
                        <div class="absolute bottom-2 right-2">
                            <span class="bg-black bg-opacity-75 text-white text-xs px-2 py-1 rounded">
                                {{ $video->formatted_duration }}
                            </span>
                        </div>
                    @endif

                    <!-- Actions Dropdown -->
                    <div class="absolute top-2 right-2">
                        <div class="relative inline-block text-left">
                            <button type="button" class="p-1 bg-black bg-opacity-50 text-white rounded-full hover:bg-opacity-75" onclick="toggleVideoDropdown({{ $video->id }})">
                                <i class="fas fa-ellipsis-v" aria-hidden="true"></i>
                            </button>
                            <div id="video-dropdown-{{ $video->id }}" class="hidden absolute right-0 z-10 mt-2 w-48 bg-surface dark:bg-gray-800 rounded-md shadow-sm ring-1 ring-black ring-opacity-5 dark:ring-gray-700">
                                <div class="py-1">
                                    <button wire:click="openEditModal({{ $video->id }})" class="w-full text-left px-4 py-2 text-sm text-content dark:text-gray-300 hover:bg-surface-muted dark:hover:bg-gray-700 flex items-center">
                                        <i class="fas fa-edit mr-2" aria-hidden="true"></i> Edit
                                    </button>
                                    <button wire:click="openDeleteModal({{ $video->id }})" class="w-full text-left px-4 py-2 text-sm text-red-700 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center">
                                        <i class="fas fa-trash mr-2" aria-hidden="true"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Video Info -->
                <div class="p-4">
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $video->category === 'lecture' ? 'bg-blue-100 dark:bg-action/30 text-brand dark:text-blue-300' : '' }}
                                    {{ $video->category === 'tutorial' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : '' }}
                                    {{ $video->category === 'demo' ? 'bg-purple-100 dark:bg-action/30 text-brand dark:text-purple-300' : '' }}
                                    {{ $video->category === 'webinar' ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300' : '' }}
                                    {{ $video->category === 'interview' ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' : '' }}
                                    {{ $video->category === 'presentation' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300' : '' }}
                                    {{ $video->category === 'other' ? 'bg-gray-100 dark:bg-gray-700 text-content dark:text-gray-300' : '' }}
                                ">
                                    {{ $categories[$video->category] ?? $video->category }}
                                </span>

                                @if(!$video->is_public)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-surface-muted dark:bg-gray-700 text-content dark:text-gray-300">
                                        <i class="fas fa-lock mr-1" aria-hidden="true"></i> Private
                                    </span>
                                @endif
                            </div>

                            <h3 class="text-lg font-semibold text-content dark:text-white mb-1">{{ $video->title }}</h3>
                            <p class="text-sm text-content-muted dark:text-content-muted line-clamp-2">{{ $video->description ?: 'No description available' }}</p>
                        </div>
                    </div>

                    <!-- Video Stats -->
                    <div class="flex items-center justify-between text-sm text-content-muted dark:text-content-muted mb-3">
                        <div class="flex items-center space-x-4">
                            @if($video->views_count > 0)
                                <span class="flex items-center">
                                    <i class="fas fa-eye mr-1" aria-hidden="true"></i>
                                    {{ $video->views_count }}
                                </span>
                            @endif
                            @if($video->likes_count > 0)
                                <span class="flex items-center">
                                    <i class="fas fa-thumbs-up mr-1" aria-hidden="true"></i>
                                    {{ $video->likes_count }}
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center">
                            <span>{{ $video->created_at->format('M j, Y') }}</span>
                        </div>
                    </div>

                    <!-- Course Info -->
                    @if($video->course)
                        <div class="flex items-center text-sm text-content-muted dark:text-content-muted mb-3">
                            <i class="fas fa-book mr-2" aria-hidden="true"></i>
                            <span>{{ $video->course->title }}</span>
                        </div>
                    @endif

                    <!-- Tags -->
                    @if($video->tags)
                        <div class="flex flex-wrap gap-1 mb-3">
                            @foreach($video->tags_array as $tag)
                                <span class="inline-block bg-surface-muted dark:bg-gray-700 text-content dark:text-gray-300 text-xs px-2 py-1 rounded">
                                    {{ $tag }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    <!-- Footer -->
                    <div class="flex items-center justify-between pt-3 border-t border-line dark:border-gray-700 text-sm text-content-muted dark:text-content-muted">
                        <div class="flex items-center">
                            <i class="fas fa-user mr-1" aria-hidden="true"></i>
                            <span>{{ $video->uploader->name ?? 'Unknown' }}</span>
                        </div>

                        <a href="{{ $video->video_url }}" target="_blank" class="text-brand dark:text-brand hover:text-brand dark:hover:text-brand font-medium">
                            <i class="fas fa-external-link-alt mr-1" aria-hidden="true"></i>
                            Watch
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="text-center py-12">
                    <i class="fas fa-video text-6xl text-gray-300 dark:text-content-muted mb-4" aria-hidden="true"></i>
                    <h3 class="text-lg font-medium text-content dark:text-white mb-2">No videos found</h3>
                    <p class="text-content-muted dark:text-content-muted mb-4">
                        @if($search || $selectedCategory || $selectedCourse)
                            Try adjusting your filters or search terms.
                        @else
                            Get started by adding your first video.
                        @endif
                    </p>
                    <button wire:click="openCreateModal" class="bg-action dark:bg-action text-action-foreground px-4 py-2 rounded-lg hover:bg-action dark:hover:bg-action">
                        Add Video
                    </button>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($videos->hasPages())
        <div class="mt-8">
            {{ $videos->links() }}
        </div>
    @endif

    <!-- Create Video Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" x-trap.inert.noscroll="true" role="dialog" aria-modal="true" aria-label="Add New Video" tabindex="-1" x-data="{}" @keydown.escape.stop="$wire.set(&#x27;showCreateModal&#x27;, false)">
            <div class="bg-surface dark:bg-gray-800 rounded-lg max-w-2xl w-full max-h-screen overflow-y-auto transition-colors duration-300">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-content dark:text-white">Add New Video</h3>
                        <button wire:click="closeCreateModal" class="text-content-muted dark:text-content-muted hover:text-content-muted dark:hover:text-gray-300" aria-label="Close">
                            <i class="fas fa-times" aria-hidden="true"></i>
                        </button>
                    </div>

                    <form wire:submit="save" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label for="title" class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Title *</label>
                                <input wire:model="title" type="text" id="title" class="w-full px-3 py-2 border border-line dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand/30 bg-surface dark:bg-gray-700 text-content dark:text-gray-100" required>
                                @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="video_type" class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Video Type *</label>
                                <select wire:model="video_type" id="video_type" class="w-full px-3 py-2 border border-line dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand/30 bg-surface dark:bg-gray-700 text-content dark:text-gray-100" required>
                                    @foreach($videoTypes as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('video_type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="category" class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Category *</label>
                                <select wire:model="category" id="category" class="w-full px-3 py-2 border border-line dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand/30 bg-surface dark:bg-gray-700 text-content dark:text-gray-100" required>
                                    @foreach($categories as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('category') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="video_url" class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Video URL *</label>
                                <input wire:model="video_url" type="url" id="video_url" class="w-full px-3 py-2 border border-line dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand/30 bg-surface dark:bg-gray-700 text-content dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500" placeholder="https://" required>
                                @error('video_url') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="course_id" class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Associated Course</label>
                                <select wire:model="course_id" id="course_id" class="w-full px-3 py-2 border border-line dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand/30 bg-surface dark:bg-gray-700 text-content dark:text-gray-100">
                                    <option value="">No Course</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Description</label>
                                <textarea wire:model="description" id="description" rows="3" class="w-full px-3 py-2 border border-line dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand/30 bg-surface dark:bg-gray-700 text-content dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500" placeholder="Brief description of the video"></textarea>
                                @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="tags" class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Tags</label>
                                <input wire:model="tags" type="text" id="tags" class="w-full px-3 py-2 border border-line dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand/30 bg-surface dark:bg-gray-700 text-content dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500" placeholder="Comma-separated tags">
                            </div>

                            <div class="md:col-span-2">
                                <label class="flex items-center">
                                    <input wire:model="is_public" type="checkbox" class="rounded border-line dark:border-gray-600 text-brand shadow-sm focus:ring-brand/30">
                                    <span class="ml-2 text-sm text-content dark:text-gray-300">Make this video public</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 pt-6 border-t border-line dark:border-gray-700">
                            <button type="button" wire:click="closeCreateModal" class="px-4 py-2 text-content dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-action dark:bg-action text-action-foreground rounded-lg hover:bg-action dark:hover:bg-action flex items-center">
                                <span wire:loading.remove wire:target="save">Add Video</span>
                                <span wire:loading wire:target="save" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Adding...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Edit Video Modal -->
    @if($showEditModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" x-trap.inert.noscroll="true" role="dialog" aria-modal="true" aria-label="Edit Video" tabindex="-1" x-data="{}" @keydown.escape.stop="$wire.set(&#x27;showEditModal&#x27;, false)">
            <div class="bg-surface dark:bg-gray-800 rounded-lg max-w-2xl w-full max-h-screen overflow-y-auto transition-colors duration-300">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-content dark:text-white">Edit Video</h3>
                        <button wire:click="closeEditModal" class="text-content-muted dark:text-content-muted hover:text-content-muted dark:hover:text-gray-300" aria-label="Close">
                            <i class="fas fa-times" aria-hidden="true"></i>
                        </button>
                    </div>

                    <form wire:submit="update" class="space-y-4">
                        <!-- Same form fields as create modal -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label for="edit_title" class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Title *</label>
                                <input wire:model="title" type="text" id="edit_title" class="w-full px-3 py-2 border border-line dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand/30 bg-surface dark:bg-gray-700 text-content dark:text-gray-100" required>
                                @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="edit_video_type" class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Video Type *</label>
                                <select wire:model="video_type" id="edit_video_type" class="w-full px-3 py-2 border border-line dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand/30 bg-surface dark:bg-gray-700 text-content dark:text-gray-100" required>
                                    @foreach($videoTypes as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('video_type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="edit_category" class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Category *</label>
                                <select wire:model="category" id="edit_category" class="w-full px-3 py-2 border border-line dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand/30 bg-surface dark:bg-gray-700 text-content dark:text-gray-100" required>
                                    @foreach($categories as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('category') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="edit_video_url" class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Video URL *</label>
                                <input wire:model="video_url" type="url" id="edit_video_url" class="w-full px-3 py-2 border border-line dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand/30 bg-surface dark:bg-gray-700 text-content dark:text-gray-100" required>
                                @error('video_url') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="edit_course_id" class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Associated Course</label>
                                <select wire:model="course_id" id="edit_course_id" class="w-full px-3 py-2 border border-line dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand/30 bg-surface dark:bg-gray-700 text-content dark:text-gray-100">
                                    <option value="">No Course</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label for="edit_description" class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Description</label>
                                <textarea wire:model="description" id="edit_description" rows="3" class="w-full px-3 py-2 border border-line dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand/30 bg-surface dark:bg-gray-700 text-content dark:text-gray-100"></textarea>
                                @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="edit_tags" class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Tags</label>
                                <input wire:model="tags" type="text" id="edit_tags" class="w-full px-3 py-2 border border-line dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand/30 bg-surface dark:bg-gray-700 text-content dark:text-gray-100">
                            </div>

                            <div class="md:col-span-2">
                                <label class="flex items-center">
                                    <input wire:model="is_public" type="checkbox" class="rounded border-line dark:border-gray-600 text-brand shadow-sm focus:ring-brand/30">
                                    <span class="ml-2 text-sm text-content dark:text-gray-300">Make this video public</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 pt-6 border-t border-line dark:border-gray-700">
                            <button type="button" wire:click="closeEditModal" class="px-4 py-2 text-content dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-action dark:bg-action text-action-foreground rounded-lg hover:bg-action dark:hover:bg-action flex items-center">
                                <span wire:loading.remove wire:target="update">Update Video</span>
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
    @if($showDeleteModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" x-trap.inert.noscroll="true" role="dialog" aria-modal="true" aria-label="Delete Video" tabindex="-1" x-data="{}" @keydown.escape.stop="$wire.set(&#x27;showDeleteModal&#x27;, false)">
            <div class="bg-surface dark:bg-gray-800 rounded-lg max-w-md w-full transition-colors duration-300">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30">
                            <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="text-center">
                        <h3 class="text-lg font-medium text-content dark:text-white mb-2">Delete Video</h3>
                        <p class="text-sm text-content-muted dark:text-content-muted mb-6">
                            Are you sure you want to delete "{{ $selectedVideo->title ?? '' }}"? This action cannot be undone.
                        </p>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button wire:click="closeDeleteModal" class="px-4 py-2 text-content dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                            Cancel
                        </button>
                        <button wire:click="delete" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 flex items-center">
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
function toggleVideoDropdown(id) {
    const dropdown = document.getElementById('video-dropdown-' + id);
    const allDropdowns = document.querySelectorAll('[id^="video-dropdown-"]');

    // Close all other dropdowns
    allDropdowns.forEach(d => {
        if (d.id !== 'video-dropdown-' + id) {
            d.classList.add('hidden');
        }
    });

    // Toggle current dropdown
    dropdown.classList.toggle('hidden');
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('[onclick^="toggleVideoDropdown"]') && !e.target.closest('[id^="video-dropdown-"]')) {
        document.querySelectorAll('[id^="video-dropdown-"]').forEach(d => d.classList.add('hidden'));
    }
});
</script>
