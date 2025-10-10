<div class="space-y-6">
    <!-- Header & Controls -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 transition-colors duration-300">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Document Categories</h2>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Organize and manage document categories</p>
            </div>
            
            <button 
                wire:click="openCreateModal"
                class="bg-indigo-600 dark:bg-indigo-500 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-colors flex items-center space-x-2"
            >
                <i class="fas fa-plus"></i>
                <span>Add New Category</span>
            </button>
        </div>

        <!-- Search Bar -->
        <div class="mt-6">
            <div class="relative">
                <input 
                    wire:model.live.debounce.300ms="search" 
                    type="text" 
                    placeholder="Search categories..." 
                    class="w-full px-4 py-2 pl-10 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500"
                >
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-500"></i>
            </div>
        </div>
    </div>

    <!-- Categories List -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden transition-colors duration-300">
        @if($categories->count() > 0)
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($categories as $category)
                    <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4 flex-1">
                                <!-- Category Icon & Color -->
                                <div class="flex items-center justify-center w-12 h-12 rounded-lg" style="background-color: {{ $category->color }}20">
                                    <i class="{{ $category->icon }} text-xl" style="color: {{ $category->color }}"></i>
                                </div>
                                
                                <!-- Category Info -->
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-1">
                                        <h4 class="text-lg font-medium text-gray-900 dark:text-white">{{ $category->name }}</h4>
                                        
                                        <!-- Status Badge -->
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $category->is_active ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' }}
                                        ">
                                            <i class="fas {{ $category->is_active ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>
                                            {{ $category->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                        
                                        <!-- Document Count -->
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                            {{ $category->documents_count ?? 0 }} documents
                                        </span>
                                    </div>
                                    
                                    @if($category->description)
                                        <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $category->description }}</p>
                                    @endif
                                    
                                    <div class="flex items-center space-x-4 text-xs text-gray-500 dark:text-gray-400 mt-2">
                                        <span>
                                            <i class="fas fa-calendar mr-1"></i>
                                            Created {{ $category->created_at->format('M j, Y') }}
                                        </span>
                                        @if($category->updated_at && $category->updated_at->ne($category->created_at))
                                            <span>
                                                <i class="fas fa-edit mr-1"></i>
                                                Updated {{ $category->updated_at->diffForHumans() }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Category Actions -->
                            <div class="flex items-center space-x-2 ml-4">
                                <!-- Order Controls -->
                                <div class="flex items-center space-x-1">
                                    <button 
                                        wire:click="moveUp({{ $category->id }})"
                                        class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700"
                                        title="Move Up"
                                    >
                                        <i class="fas fa-chevron-up"></i>
                                    </button>
                                    <button 
                                        wire:click="moveDown({{ $category->id }})"
                                        class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 p-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700"
                                        title="Move Down"
                                    >
                                        <i class="fas fa-chevron-down"></i>
                                    </button>
                                </div>
                                
                                <!-- Status Toggle -->
                                <button 
                                    wire:click="toggleStatus({{ $category->id }})"
                                    class="px-3 py-1 rounded text-xs font-medium transition-colors
                                        {{ $category->is_active ? 'text-orange-700 dark:text-orange-400 bg-orange-100 dark:bg-orange-900/30 hover:bg-orange-200 dark:hover:bg-orange-900/40' : 'text-green-700 dark:text-green-400 bg-green-100 dark:bg-green-900/30 hover:bg-green-200 dark:hover:bg-green-900/40' }}
                                    "
                                >
                                    {{ $category->is_active ? 'Deactivate' : 'Activate' }}
                                </button>
                                
                                <!-- Edit Button -->
                                <button 
                                    wire:click="openEditModal({{ $category->id }})"
                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 px-3 py-1 rounded text-xs font-medium transition-colors"
                                >
                                    <i class="fas fa-edit mr-1"></i>
                                    Edit
                                </button>
                                
                                <!-- Delete Button -->
                                <button 
                                    wire:click="openDeleteModal({{ $category->id }})"
                                    class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 px-3 py-1 rounded text-xs font-medium transition-colors"
                                    {{ ($category->documents_count ?? 0) > 0 ? 'disabled title="Cannot delete category with documents"' : '' }}
                                >
                                    <i class="fas fa-trash mr-1"></i>
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-12 text-center">
                <i class="fas fa-tags text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No categories found</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-4">
                    @if($search)
                        No categories match your search criteria.
                    @else
                        Get started by creating your first document category.
                    @endif
                </p>
                <button wire:click="openCreateModal" class="bg-indigo-600 dark:bg-indigo-500 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 dark:hover:bg-indigo-600">
                    Create Category
                </button>
            </div>
        @endif
    </div>

    <!-- Create Category Modal -->
    @if($showCreateModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg max-w-2xl w-full max-h-screen overflow-y-auto transition-colors duration-300">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Create New Category</h3>
                        <button wire:click="closeCreateModal" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <form wire:submit="save" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category Name *</label>
                                <input 
                                    wire:model="name" 
                                    type="text" 
                                    id="name" 
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" 
                                    required
                                >
                                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="icon" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Icon *</label>
                                <select wire:model="icon" id="icon" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" required>
                                    @foreach($iconOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('icon') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Color *</label>
                                <div class="flex items-center space-x-2">
                                    <input 
                                        wire:model="color" 
                                        type="color" 
                                        id="color" 
                                        class="w-12 h-10 border border-gray-300 dark:border-gray-600 rounded cursor-pointer bg-white dark:bg-gray-700"
                                    >
                                    <input 
                                        wire:model="color" 
                                        type="text" 
                                        class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                        placeholder="#3B82F6"
                                    >
                                </div>
                                @error('color') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                                <textarea 
                                    wire:model="description" 
                                    id="description" 
                                    rows="3" 
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500"
                                    placeholder="Brief description of this category..."
                                ></textarea>
                                @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="flex items-center">
                                    <input wire:model="is_active" type="checkbox" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active (category will be available for use)</span>
                                </label>
                            </div>
                        </div>

                        <!-- Preview -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Preview:</h4>
                            <div class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex items-center justify-center w-10 h-10 rounded-lg" style="background-color: {{ $color }}20">
                                    <i class="{{ $icon }} text-lg" style="color: {{ $color }}"></i>
                                </div>
                                <div>
                                    <h5 class="font-medium text-gray-900 dark:text-white">{{ $name ?: 'Category Name' }}</h5>
                                    @if($description)
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $description }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <button type="button" wire:click="closeCreateModal" class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 dark:bg-indigo-500 text-white rounded-lg hover:bg-indigo-700 dark:hover:bg-indigo-600 flex items-center">
                                <span wire:loading.remove wire:target="save">Create Category</span>
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

    <!-- Edit Category Modal -->
    @if($showEditModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-800 rounded-lg max-w-2xl w-full max-h-screen overflow-y-auto transition-colors duration-300">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Edit Category</h3>
                        <button wire:click="closeEditModal" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>

                    <form wire:submit="update" class="space-y-4">
                        <!-- Same form fields as create modal -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label for="edit_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category Name *</label>
                                <input 
                                    wire:model="name" 
                                    type="text" 
                                    id="edit_name" 
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" 
                                    required
                                >
                                @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="edit_icon" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Icon *</label>
                                <select wire:model="icon" id="edit_icon" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100" required>
                                    @foreach($iconOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('icon') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="edit_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Color *</label>
                                <div class="flex items-center space-x-2">
                                    <input 
                                        wire:model="color" 
                                        type="color" 
                                        id="edit_color" 
                                        class="w-12 h-10 border border-gray-300 dark:border-gray-600 rounded cursor-pointer bg-white dark:bg-gray-700"
                                    >
                                    <input 
                                        wire:model="color" 
                                        type="text" 
                                        class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                    >
                                </div>
                                @error('color') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="edit_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                                <textarea 
                                    wire:model="description" 
                                    id="edit_description" 
                                    rows="3" 
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                                ></textarea>
                                @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label class="flex items-center">
                                    <input wire:model="is_active" type="checkbox" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active (category will be available for use)</span>
                                </label>
                            </div>
                        </div>

                        <!-- Preview -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Preview:</h4>
                            <div class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex items-center justify-center w-10 h-10 rounded-lg" style="background-color: {{ $color }}20">
                                    <i class="{{ $icon }} text-lg" style="color: {{ $color }}"></i>
                                </div>
                                <div>
                                    <h5 class="font-medium text-gray-900 dark:text-white">{{ $name ?: 'Category Name' }}</h5>
                                    @if($description)
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $description }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <button type="button" wire:click="closeEditModal" class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 dark:bg-indigo-500 text-white rounded-lg hover:bg-indigo-700 dark:hover:bg-indigo-600 flex items-center">
                                <span wire:loading.remove wire:target="update">Update Category</span>
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
            <div class="bg-white dark:bg-gray-800 rounded-lg max-w-md w-full transition-colors duration-300">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30">
                            <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400"></i>
                        </div>
                    </div>
                    <div class="text-center">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Delete Category</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                            Are you sure you want to delete "{{ $selectedCategory->name ?? '' }}"? This action cannot be undone.
                            @if(($selectedCategory->documents_count ?? 0) > 0)
                                <br><br>
                                <span class="text-red-600 dark:text-red-400 font-medium">This category contains {{ $selectedCategory->documents_count }} documents and cannot be deleted.</span>
                            @endif
                        </p>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button wire:click="closeDeleteModal" class="px-4 py-2 text-gray-700 dark:text-gray-300 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600">
                            Cancel
                        </button>
                        @if(($selectedCategory->documents_count ?? 0) === 0)
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
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>