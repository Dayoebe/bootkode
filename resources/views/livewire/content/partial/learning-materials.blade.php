<div class="space-y-6">
    <!-- Header & Controls -->
    <div class="bg-surface dark:bg-gray-800 rounded-lg shadow-sm p-6 transition-colors duration-300">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
            <div>
                <h2 class="text-2xl font-bold text-content dark:text-white">Learning Materials</h2>
                <p class="text-content-muted dark:text-content-muted mt-1">Manage educational resources and materials</p>
            </div>

            <button
                wire:click="openCreateModal"
                class="bg-action dark:bg-action text-action-foreground px-4 py-2 rounded-lg hover:bg-action dark:hover:bg-action transition-colors flex items-center space-x-2"
            >
                <i class="fas fa-plus" aria-hidden="true"></i>
                <span>Add New Material</span>
            </button>
        </div>

        <!-- Filters -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <input
 aria-label="Search materials"                    wire:model.live.debounce.300ms="search"
                    type="text"
                    placeholder="Search materials..."
                    class="w-full px-3 py-2 border border-line dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand/30 focus:border-transparent bg-surface dark:bg-gray-700 text-content dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 transition-colors duration-300"
                >
            </div>

            <div>
                <select aria-label="Selected type" wire:model.live="selectedType" class="w-full px-3 py-2 border border-line dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand/30 bg-surface dark:bg-gray-700 text-content dark:text-gray-100 transition-colors duration-300">
                    <option value="">All Types</option>
                    @foreach($types as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <select aria-label="Selected course" wire:model.live="selectedCourse" class="w-full px-3 py-2 border border-line dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand/30 bg-surface dark:bg-gray-700 text-content dark:text-gray-100 transition-colors duration-300">
                    <option value="">All Courses</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <select aria-label="Sort by" wire:model.live="sortBy" class="w-full px-3 py-2 border border-line dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand/30 bg-surface dark:bg-gray-700 text-content dark:text-gray-100 transition-colors duration-300">
                    <option value="created_at">Sort by Date</option>
                    <option value="title">Sort by Title</option>
                    <option value="type">Sort by Type</option>
                    <option value="download_count">Sort by Downloads</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Materials Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($materials as $material)
            <div class="bg-surface dark:bg-gray-800 rounded-lg shadow-sm  transition-all duration-300 p-6">
                <!-- Material Header -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $material->type === 'document' ? 'bg-blue-100 dark:bg-action/30 text-brand dark:text-blue-300' : '' }}
                                {{ $material->type === 'presentation' ? 'bg-purple-100 dark:bg-action/30 text-brand dark:text-purple-300' : '' }}
                                {{ $material->type === 'worksheet' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : '' }}
                                {{ $material->type === 'template' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300' : '' }}
                                {{ $material->type === 'guide' ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' : '' }}
                                {{ $material->type === 'other' ? 'bg-gray-100 dark:bg-gray-700 text-content dark:text-gray-300' : '' }}
                            ">
                                {{ $types[$material->type] ?? $material->type }}
                            </span>

                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $material->difficulty_level === 'beginner' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : '' }}
                                {{ $material->difficulty_level === 'intermediate' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300' : '' }}
                                {{ $material->difficulty_level === 'advanced' ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300' : '' }}
                                {{ $material->difficulty_level === 'expert' ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' : '' }}
                            ">
                                {{ ucfirst($material->difficulty_level) }}
                            </span>
                        </div>

                        <h3 class="text-lg font-semibold text-content dark:text-white mb-1">{{ $material->title }}</h3>
                        <p class="text-sm text-content-muted dark:text-content-muted line-clamp-2">{{ $material->description ?: 'No description available' }}</p>
                    </div>

                    <div class="ml-4">
                        <div class="relative inline-block text-left">
                            <button type="button" class="p-1 text-content-muted dark:text-content-muted hover:text-content-muted dark:hover:text-gray-300 transition-colors" onclick="toggleDropdown({{ $material->id }})">
                                <i class="fas fa-ellipsis-v" aria-hidden="true"></i>
                            </button>
                            <div id="dropdown-{{ $material->id }}" class="hidden absolute right-0 z-10 mt-2 w-48 bg-surface dark:bg-gray-800 rounded-md shadow-sm ring-1 ring-black ring-opacity-5 dark:ring-gray-700">
                                <div class="py-1">
                                    @if($material->file_path)
                                        <button wire:click="downloadFile({{ $material->id }})" class="w-full text-left px-4 py-2 text-sm text-content dark:text-gray-300 hover:bg-surface-muted dark:hover:bg-gray-700 flex items-center transition-colors">
                                            <i class="fas fa-download mr-2" aria-hidden="true"></i> Download
                                        </button>
                                    @endif

                                    <button wire:click="openEditModal({{ $material->id }})" class="w-full text-left px-4 py-2 text-sm text-content dark:text-gray-300 hover:bg-surface-muted dark:hover:bg-gray-700 flex items-center transition-colors">
                                        <i class="fas fa-edit mr-2" aria-hidden="true"></i> Edit
                                    </button>
                                    <button wire:click="openDeleteModal({{ $material->id }})" class="w-full text-left px-4 py-2 text-sm text-red-700 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center transition-colors">
                                        <i class="fas fa-trash mr-2" aria-hidden="true"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Material Content -->
                <div class="mb-4">
                    @if($material->file_path)
                        <div class="flex items-center space-x-2 text-sm text-content-muted dark:text-content-muted mb-2">
                            <i class="fas fa-paperclip" aria-hidden="true"></i>
                            <span>{{ $material->original_filename }}</span>
                            @if($material->file_size)
                                <span class="text-content-muted dark:text-content-muted">• {{ number_format($material->file_size / 1024 / 1024, 1) }} MB</span>
                            @endif
                        </div>
                    @endif

                    @if($material->course)
                        <div class="flex items-center space-x-2 text-sm text-content-muted dark:text-content-muted mb-2">
                            <i class="fas fa-book" aria-hidden="true"></i>
                            <span>{{ $material->course->title }}</span>
                        </div>
                    @endif

                    @if($material->tags)
                        <div class="flex flex-wrap gap-1 mb-2">
                            @foreach(explode(',', $material->tags) as $tag)
                                <span class="inline-block bg-surface-muted dark:bg-gray-700 text-content dark:text-gray-300 text-xs px-2 py-1 rounded">
                                    {{ trim($tag) }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Material Footer -->
                <div class="flex items-center justify-between text-sm text-content-muted dark:text-content-muted pt-4 border-t border-line dark:border-gray-700">
                    <div class="flex items-center space-x-4">
                        <span>
                            <i class="fas fa-user mr-1" aria-hidden="true"></i>
                            {{ $material->creator->name ?? 'Unknown' }}
                        </span>
                        @if($material->download_count > 0)
                            <span>
                                <i class="fas fa-download mr-1" aria-hidden="true"></i>
                                {{ $material->download_count }}
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center space-x-2">
                        @if($material->is_public)
                            <i class="fas fa-globe text-green-500 dark:text-green-400" title="Public"></i>
                        @else
                            <i class="fas fa-lock text-content-muted dark:text-content-muted" title="Private"></i>
                        @endif
                        <span>{{ $material->created_at->format('M j, Y') }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="text-center py-12">
                    <i class="fas fa-book-open text-6xl text-gray-300 dark:text-content-muted mb-4" aria-hidden="true"></i>
                    <h3 class="text-lg font-medium text-content dark:text-white mb-2">No materials found</h3>
                    <p class="text-content-muted dark:text-content-muted mb-4">
                        @if($search || $selectedType || $selectedCourse)
                            Try adjusting your filters or search terms.
                        @else
                            Get started by creating your first learning material.
                        @endif
                    </p>
                    <button wire:click="openCreateModal" class="bg-action dark:bg-action text-action-foreground px-4 py-2 rounded-lg hover:bg-action dark:hover:bg-action transition-colors">
                        Create Material
                    </button>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($materials->hasPages())
        <div class="mt-8">
            {{ $materials->links() }}
        </div>
    @endif

    <!-- Create Material Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" x-trap.inert.noscroll="true" role="dialog" aria-modal="true" aria-label="Create New Learning Material" tabindex="-1" x-data="{}" @keydown.escape.stop="$wire.set(&#x27;showCreateModal&#x27;, false)">
            <div class="bg-surface dark:bg-gray-800 rounded-lg max-w-2xl w-full max-h-screen overflow-y-auto transition-colors duration-300">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-content dark:text-white">Create New Learning Material</h3>
                        <button wire:click="closeCreateModal" class="text-content-muted dark:text-content-muted hover:text-content-muted dark:hover:text-gray-300 transition-colors" aria-label="Close">
                            <i class="fas fa-times" aria-hidden="true"></i>
                        </button>
                    </div>

                    <form wire:submit="save" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label for="title" class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Title *</label>
                                <input wire:model="title" type="text" id="title" class="w-full px-3 py-2 border border-line dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand/30 bg-surface dark:bg-gray-700 text-content dark:text-gray-100 transition-colors duration-300" required>
                                @error('title') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="type" class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Type *</label>
                                <select wire:model="type" id="type" class="w-full px-3 py-2 border border-line dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand/30 bg-surface dark:bg-gray-700 text-content dark:text-gray-100 transition-colors duration-300" required>
                                    <option value="">Select Type</option>
                                    @foreach($types as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('type') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="difficulty_level" class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Difficulty Level</label>
                                <select wire:model="difficulty_level" id="difficulty_level" class="w-full px-3 py-2 border border-line dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand/30 bg-surface dark:bg-gray-700 text-content dark:text-gray-100 transition-colors duration-300">
                                    <option value="beginner">Beginner</option>
                                    <option value="intermediate">Intermediate</option>
                                    <option value="advanced">Advanced</option>
                                    <option value="expert">Expert</option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label for="course_id" class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Associated Course</label>
                                <select wire:model="course_id" id="course_id" class="w-full px-3 py-2 border border-line dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand/30 bg-surface dark:bg-gray-700 text-content dark:text-gray-100 transition-colors duration-300">
                                    <option value="">No Course</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Description</label>
                                <textarea wire:model="description" id="description" rows="3" class="w-full px-3 py-2 border border-line dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand/30 bg-surface dark:bg-gray-700 text-content dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 transition-colors duration-300" placeholder="Brief description of the material"></textarea>
                                @error('description') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="content" class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Content</label>
                                <textarea wire:model="content" id="content" rows="5" class="w-full px-3 py-2 border border-line dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand/30 bg-surface dark:bg-gray-700 text-content dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 transition-colors duration-300" placeholder="Text content or notes about this material"></textarea>
                            </div>

                            <div class="md:col-span-2">
                                <label for="file_upload" class="block text-sm font-medium text-content dark:text-gray-300 mb-1">File Upload</label>
                                <input wire:model="file_upload" type="file" id="file_upload" class="w-full px-3 py-2 border border-line dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand/30 bg-surface dark:bg-gray-700 text-content dark:text-gray-100 transition-colors duration-300">
                                <p class="text-sm text-content-muted dark:text-content-muted mt-1">Maximum file size: 50MB</p>
                                @error('file_upload') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="tags" class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Tags</label>
                                <input wire:model="tags" type="text" id="tags" class="w-full px-3 py-2 border border-line dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand/30 bg-surface dark:bg-gray-700 text-content dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 transition-colors duration-300" placeholder="Comma-separated tags">
                            </div>

                            <div class="md:col-span-2">
                                <label class="flex items-center">
                                    <input wire:model="is_public" type="checkbox" class="rounded border-line dark:border-gray-600 text-brand shadow-sm focus:ring-brand/30 bg-surface dark:bg-gray-700">
                                    <span class="ml-2 text-sm text-content dark:text-gray-300">Make this material public</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 pt-6 border-t border-line dark:border-gray-700">
                            <button type="button" wire:click="closeCreateModal" class="px-4 py-2 text-content dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-action dark:bg-action text-action-foreground rounded-lg hover:bg-action dark:hover:bg-action flex items-center transition-colors">
                                <span wire:loading.remove wire:target="save">Create Material</span>
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

    <!-- Edit Material Modal -->
    @if($showEditModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" x-trap.inert.noscroll="true" role="dialog" aria-modal="true" aria-label="Edit Learning Material" tabindex="-1" x-data="{}" @keydown.escape.stop="$wire.set(&#x27;showEditModal&#x27;, false)">
            <div class="bg-surface dark:bg-gray-800 rounded-lg max-w-2xl w-full max-h-screen overflow-y-auto transition-colors duration-300">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-content dark:text-white">Edit Learning Material</h3>
                        <button wire:click="closeEditModal" class="text-content-muted dark:text-content-muted hover:text-content-muted dark:hover:text-gray-300 transition-colors" aria-label="Close">
                            <i class="fas fa-times" aria-hidden="true"></i>
                        </button>
                    </div>

                    <form wire:submit="update" class="space-y-4">
                        <!-- Same form fields as create modal with dark mode -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label for="edit_title" class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Title *</label>
                                <input wire:model="title" type="text" id="edit_title" class="w-full px-3 py-2 border border-line dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand/30 bg-surface dark:bg-gray-700 text-content dark:text-gray-100 transition-colors duration-300" required>
                                @error('title') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="edit_type" class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Type *</label>
                                <select wire:model="type" id="edit_type" class="w-full px-3 py-2 border border-line dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand/30 bg-surface dark:bg-gray-700 text-content dark:text-gray-100 transition-colors duration-300" required>
                                    <option value="">Select Type</option>
                                    @foreach($types as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('type') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="edit_difficulty_level" class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Difficulty Level</label>
                                <select wire:model="difficulty_level" id="edit_difficulty_level" class="w-full px-3 py-2 border border-line dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand/30 bg-surface dark:bg-gray-700 text-content dark:text-gray-100 transition-colors duration-300">
                                    <option value="beginner">Beginner</option>
                                    <option value="intermediate">Intermediate</option>
                                    <option value="advanced">Advanced</option>
                                    <option value="expert">Expert</option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label for="edit_course_id" class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Associated Course</label>
                                <select wire:model="course_id" id="edit_course_id" class="w-full px-3 py-2 border border-line dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand/30 bg-surface dark:bg-gray-700 text-content dark:text-gray-100 transition-colors duration-300">
                                    <option value="">No Course</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label for="edit_description" class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Description</label>
                                <textarea wire:model="description" id="edit_description" rows="3" class="w-full px-3 py-2 border border-line dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand/30 bg-surface dark:bg-gray-700 text-content dark:text-gray-100 transition-colors duration-300"></textarea>
                                @error('description') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="edit_content" class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Content</label>
                                <textarea wire:model="content" id="edit_content" rows="5" class="w-full px-3 py-2 border border-line dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand/30 bg-surface dark:bg-gray-700 text-content dark:text-gray-100 transition-colors duration-300"></textarea>
                            </div>

                            <div class="md:col-span-2">
                                <label for="edit_file_upload" class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Replace File (optional)</label>
                                @if($selectedMaterial && $selectedMaterial->file_path)
                                    <p class="text-sm text-content-muted dark:text-content-muted mb-2">Current file: {{ $selectedMaterial->original_filename }}</p>
                                @endif
                                <input wire:model="file_upload" type="file" id="edit_file_upload" class="w-full px-3 py-2 border border-line dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand/30 bg-surface dark:bg-gray-700 text-content dark:text-gray-100 transition-colors duration-300">
                                @error('file_upload') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="edit_tags" class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Tags</label>
                                <input wire:model="tags" type="text" id="edit_tags" class="w-full px-3 py-2 border border-line dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-brand/30 bg-surface dark:bg-gray-700 text-content dark:text-gray-100 transition-colors duration-300">
                            </div>

                            <div class="md:col-span-2">
                                <label class="flex items-center">
                                    <input wire:model="is_public" type="checkbox" class="rounded border-line dark:border-gray-600 text-brand shadow-sm focus:ring-brand/30 bg-surface dark:bg-gray-700">
                                    <span class="ml-2 text-sm text-content dark:text-gray-300">Make this material public</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 pt-6 border-t border-line dark:border-gray-700">
                            <button type="button" wire:click="closeEditModal" class="px-4 py-2 text-content dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-action dark:bg-action text-action-foreground rounded-lg hover:bg-action dark:hover:bg-action flex items-center transition-colors">
                                <span wire:loading.remove wire:target="update">Update Material</span>
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
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" x-trap.inert.noscroll="true" role="dialog" aria-modal="true" aria-label="Delete Learning Material" tabindex="-1" x-data="{}" @keydown.escape.stop="$wire.set(&#x27;showDeleteModal&#x27;, false)">
            <div class="bg-surface dark:bg-gray-800 rounded-lg max-w-md w-full transition-colors duration-300">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30">
                            <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="text-center">
                        <h3 class="text-lg font-medium text-content dark:text-white mb-2">Delete Learning Material</h3>
                        <p class="text-sm text-content-muted dark:text-content-muted mb-6">
                            Are you sure you want to delete "{{ $selectedMaterial->title ?? '' }}"? This action cannot be undone.
                        </p>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button wire:click="closeDeleteModal" class="px-4 py-2 text-content dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
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
function toggleDropdown(id) {
    const dropdown = document.getElementById('dropdown-' + id);
    const allDropdowns = document.querySelectorAll('[id^="dropdown-"]');

    // Close all other dropdowns
    allDropdowns.forEach(d => {
        if (d.id !== 'dropdown-' + id) {
            d.classList.add('hidden');
        }
    });

    // Toggle current dropdown
    dropdown.classList.toggle('hidden');
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('[onclick^="toggleDropdown"]') && !e.target.closest('[id^="dropdown-"]')) {
        document.querySelectorAll('[id^="dropdown-"]').forEach(d => d.classList.add('hidden'));
    }
});
</script>
