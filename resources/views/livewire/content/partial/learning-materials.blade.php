<div class="space-y-6">
    <!-- Header & Controls -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Learning Materials</h2>
                <p class="text-gray-600 mt-1">Manage educational resources and materials</p>
            </div>
            
            <button 
                wire:click="openCreateModal"
                class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors flex items-center space-x-2"
            >
                <i class="fas fa-plus"></i>
                <span>Add New Material</span>
            </button>
        </div>

        <!-- Filters -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <input 
                    wire:model.live.debounce.300ms="search" 
                    type="text" 
                    placeholder="Search materials..." 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                >
            </div>
            
            <div>
                <select wire:model.live="selectedType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Types</option>
                    @foreach($types as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <select wire:model.live="selectedCourse" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Courses</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <select wire:model.live="sortBy" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
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
            <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow p-6">
                <!-- Material Header -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $material->type === 'document' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $material->type === 'presentation' ? 'bg-purple-100 text-purple-800' : '' }}
                                {{ $material->type === 'worksheet' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $material->type === 'template' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $material->type === 'guide' ? 'bg-red-100 text-red-800' : '' }}
                                {{ $material->type === 'other' ? 'bg-gray-100 text-gray-800' : '' }}
                            ">
                                {{ $types[$material->type] ?? $material->type }}
                            </span>
                            
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $material->difficulty_level === 'beginner' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $material->difficulty_level === 'intermediate' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $material->difficulty_level === 'advanced' ? 'bg-orange-100 text-orange-800' : '' }}
                                {{ $material->difficulty_level === 'expert' ? 'bg-red-100 text-red-800' : '' }}
                            ">
                                {{ ucfirst($material->difficulty_level) }}
                            </span>
                        </div>
                        
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $material->title }}</h3>
                        <p class="text-sm text-gray-600 line-clamp-2">{{ $material->description ?: 'No description available' }}</p>
                    </div>
                    
                    <div class="ml-4">
                        <div class="relative inline-block text-left">
                            <button type="button" class="p-1 text-gray-400 hover:text-gray-600" onclick="toggleDropdown({{ $material->id }})">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div id="dropdown-{{ $material->id }}" class="hidden absolute right-0 z-10 mt-2 w-48 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5">
                                <div class="py-1">
                                    @if($material->file_path)
                                        <button wire:click="downloadFile({{ $material->id }})" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                            <i class="fas fa-download mr-2"></i> Download
                                        </button>
                                    @endif
                                    
                                    <button wire:click="openEditModal({{ $material->id }})" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
                                        <i class="fas fa-edit mr-2"></i> Edit
                                    </button>
                                    <button wire:click="openDeleteModal({{ $material->id }})" class="w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50 flex items-center">
                                        <i class="fas fa-trash mr-2"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Material Content -->
                <div class="mb-4">
                    @if($material->file_path)
                        <div class="flex items-center space-x-2 text-sm text-gray-600 mb-2">
                            <i class="fas fa-paperclip"></i>
                            <span>{{ $material->original_filename }}</span>
                            @if($material->file_size)
                                <span class="text-gray-400">• {{ number_format($material->file_size / 1024 / 1024, 1) }} MB</span>
                            @endif
                        </div>
                    @endif
                    
                    @if($material->course)
                        <div class="flex items-center space-x-2 text-sm text-gray-600 mb-2">
                            <i class="fas fa-book"></i>
                            <span>{{ $material->course->title }}</span>
                        </div>
                    @endif
                    
                    @if($material->tags)
                        <div class="flex flex-wrap gap-1 mb-2">
                            @foreach(explode(',', $material->tags) as $tag)
                                <span class="inline-block bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">
                                    {{ trim($tag) }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Material Footer -->
                <div class="flex items-center justify-between text-sm text-gray-500 pt-4 border-t">
                    <div class="flex items-center space-x-4">
                        <span>
                            <i class="fas fa-user mr-1"></i>
                            {{ $material->creator->name ?? 'Unknown' }}
                        </span>
                        @if($material->download_count > 0)
                            <span>
                                <i class="fas fa-download mr-1"></i>
                                {{ $material->download_count }}
                            </span>
                        @endif
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        @if($material->is_public)
                            <i class="fas fa-globe text-green-500" title="Public"></i>
                        @else
                            <i class="fas fa-lock text-gray-400" title="Private"></i>
                        @endif
                        <span>{{ $material->created_at->format('M j, Y') }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="text-center py-12">
                    <i class="fas fa-book-open text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No materials found</h3>
                    <p class="text-gray-500 mb-4">
                        @if($search || $selectedType || $selectedCourse)
                            Try adjusting your filters or search terms.
                        @else
                            Get started by creating your first learning material.
                        @endif
                    </p>
                    <button wire:click="openCreateModal" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
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
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg max-w-2xl w-full max-h-screen overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Create New Learning Material</h3>
                        <button wire:click="closeCreateModal" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form wire:submit="save" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                                <input wire:model="title" type="text" id="title" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" required>
                                @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                                <select wire:model="type" id="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" required>
                                    <option value="">Select Type</option>
                                    @foreach($types as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="difficulty_level" class="block text-sm font-medium text-gray-700 mb-1">Difficulty Level</label>
                                <select wire:model="difficulty_level" id="difficulty_level" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                    <option value="beginner">Beginner</option>
                                    <option value="intermediate">Intermediate</option>
                                    <option value="advanced">Advanced</option>
                                    <option value="expert">Expert</option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label for="course_id" class="block text-sm font-medium text-gray-700 mb-1">Associated Course</label>
                                <select wire:model="course_id" id="course_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                    <option value="">No Course</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea wire:model="description" id="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" placeholder="Brief description of the material"></textarea>
                                @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                                <textarea wire:model="content" id="content" rows="5" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" placeholder="Text content or notes about this material"></textarea>
                            </div>

                            <div class="md:col-span-2">
                                <label for="file_upload" class="block text-sm font-medium text-gray-700 mb-1">File Upload</label>
                                <input wire:model="file_upload" type="file" id="file_upload" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                <p class="text-sm text-gray-500 mt-1">Maximum file size: 50MB</p>
                                @error('file_upload') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="tags" class="block text-sm font-medium text-gray-700 mb-1">Tags</label>
                                <input wire:model="tags" type="text" id="tags" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" placeholder="Comma-separated tags">
                            </div>

                            <div class="md:col-span-2">
                                <label class="flex items-center">
                                    <input wire:model="is_public" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700">Make this material public</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 pt-6 border-t">
                            <button type="button" wire:click="closeCreateModal" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center">
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
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg max-w-2xl w-full max-h-screen overflow-y-auto">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-900">Edit Learning Material</h3>
                        <button wire:click="closeEditModal" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form wire:submit="update" class="space-y-4">
                        <!-- Same form fields as create modal -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label for="edit_title" class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                                <input wire:model="title" type="text" id="edit_title" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" required>
                                @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="edit_type" class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                                <select wire:model="type" id="edit_type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" required>
                                    <option value="">Select Type</option>
                                    @foreach($types as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="edit_difficulty_level" class="block text-sm font-medium text-gray-700 mb-1">Difficulty Level</label>
                                <select wire:model="difficulty_level" id="edit_difficulty_level" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                    <option value="beginner">Beginner</option>
                                    <option value="intermediate">Intermediate</option>
                                    <option value="advanced">Advanced</option>
                                    <option value="expert">Expert</option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label for="edit_course_id" class="block text-sm font-medium text-gray-700 mb-1">Associated Course</label>
                                <select wire:model="course_id" id="edit_course_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                    <option value="">No Course</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label for="edit_description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea wire:model="description" id="edit_description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"></textarea>
                                @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="edit_content" class="block text-sm font-medium text-gray-700 mb-1">Content</label>
                                <textarea wire:model="content" id="edit_content" rows="5" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500"></textarea>
                            </div>

                            <div class="md:col-span-2">
                                <label for="edit_file_upload" class="block text-sm font-medium text-gray-700 mb-1">Replace File (optional)</label>
                                @if($selectedMaterial && $selectedMaterial->file_path)
                                    <p class="text-sm text-gray-600 mb-2">Current file: {{ $selectedMaterial->original_filename }}</p>
                                @endif
                                <input wire:model="file_upload" type="file" id="edit_file_upload" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                @error('file_upload') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="edit_tags" class="block text-sm font-medium text-gray-700 mb-1">Tags</label>
                                <input wire:model="tags" type="text" id="edit_tags" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                            </div>

                            <div class="md:col-span-2">
                                <label class="flex items-center">
                                    <input wire:model="is_public" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700">Make this material public</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 pt-6 border-t">
                            <button type="button" wire:click="closeEditModal" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center">
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
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg max-w-md w-full">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                    </div>
                    <div class="text-center">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Delete Learning Material</h3>
                        <p class="text-sm text-gray-500 mb-6">
                            Are you sure you want to delete "{{ $selectedMaterial->title ?? '' }}"? This action cannot be undone.
                        </p>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button wire:click="closeDeleteModal" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
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