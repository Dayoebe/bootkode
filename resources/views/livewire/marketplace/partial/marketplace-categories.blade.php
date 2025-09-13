{{-- resources/views/livewire/marketplace/partial/marketplace-categories.blade.php --}}
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between space-y-4 md:space-y-0">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Browse Categories</h2>
                <p class="text-gray-600">Discover items by category</p>
            </div>
            
            @if($canManage)
                <button wire:click="openCreateForm" 
                        class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Add Category
                </button>
            @endif
        </div>
    </div>

    <!-- Featured Categories -->
    @if($featuredCategories->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4 flex items-center">
                <i class="fas fa-star text-yellow-500 mr-2"></i>
                Featured Categories
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-4">
                @foreach($featuredCategories as $category)
                    <a href="{{ route('marketplace.browse') }}?categories[]={{ $category->id }}" 
                       class="group p-4 bg-gray-50 rounded-lg hover:bg-purple-50 transition-colors text-center">
                        <div class="w-12 h-12 mx-auto mb-2 rounded-lg flex items-center justify-center group-hover:bg-purple-200 transition-colors"
                             style="background-color: {{ $category->color }}20">
                            <i class="{{ $category->icon }} text-xl" style="color: {{ $category->color }}"></i>
                        </div>
                        <h4 class="font-medium text-gray-900 text-sm">{{ $category->name }}</h4>
                        <p class="text-xs text-gray-500">{{ $category->items_count }} items</p>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    <!-- All Categories -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">All Categories</h3>
        
        @if($categories->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($categories as $category)
                    <div class="group relative border border-gray-200 rounded-lg p-4 hover:border-purple-300 transition-colors">
                        <!-- Category Link -->
                        <a href="{{ route('marketplace.browse') }}?categories[]={{ $category->id }}" 
                           class="block">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0 w-12 h-12 rounded-lg flex items-center justify-center transition-colors"
                                     style="background-color: {{ $category->color }}20">
                                    <i class="{{ $category->icon }} text-xl" style="color: {{ $category->color }}"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-medium text-gray-900 group-hover:text-purple-600 transition-colors">
                                        {{ $category->name }}
                                        @if($category->is_featured)
                                            <i class="fas fa-star text-yellow-500 text-xs ml-1"></i>
                                        @endif
                                    </h4>
                                    @if($category->description)
                                        <p class="text-sm text-gray-600 line-clamp-2">{{ $category->description }}</p>
                                    @endif
                                    <div class="flex items-center mt-2 space-x-3 text-xs text-gray-500">
                                        <span><i class="fas fa-box mr-1"></i>{{ $category->items_count }} items</span>
                                        <span class="px-2 py-1 bg-{{ $category->is_active ? 'green' : 'red' }}-100 text-{{ $category->is_active ? 'green' : 'red' }}-800 rounded-full">
                                            {{ $category->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </a>

                        <!-- Admin Actions -->
                        @if($canManage)
                            <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <div class="flex space-x-1">
                                    <button wire:click="openEditForm({{ $category->id }})"
                                            class="p-1 bg-blue-600 text-white rounded text-xs hover:bg-blue-700 transition-colors">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    
                                    <button wire:click="toggleFeatured({{ $category->id }})"
                                            class="p-1 bg-{{ $category->is_featured ? 'yellow' : 'gray' }}-600 text-white rounded text-xs hover:bg-{{ $category->is_featured ? 'yellow' : 'gray' }}-700 transition-colors">
                                        <i class="fas fa-star"></i>
                                    </button>
                                    
                                    <button wire:click="toggleActive({{ $category->id }})"
                                            class="p-1 bg-{{ $category->is_active ? 'red' : 'green' }}-600 text-white rounded text-xs hover:bg-{{ $category->is_active ? 'red' : 'green' }}-700 transition-colors">
                                        <i class="fas fa-{{ $category->is_active ? 'eye-slash' : 'eye' }}"></i>
                                    </button>
                                    
                                    @if($category->items_count === 0)
                                        <button wire:click="deleteCategory({{ $category->id }})"
                                                onclick="confirm('Are you sure you want to delete this category?') || event.stopImmediatePropagation()"
                                                class="p-1 bg-red-600 text-white rounded text-xs hover:bg-red-700 transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="mt-6">
                {{ $categories->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-folder-open text-gray-300 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No categories yet</h3>
                <p class="text-gray-500 mb-6">Categories help organize marketplace items.</p>
                @if($canManage)
                    <button wire:click="openCreateForm"
                            class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Create First Category
                    </button>
                @endif
            </div>
        @endif
    </div>

    <!-- Create Category Modal -->
    @if($showCreateForm)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg max-w-md w-full p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Create Category</h3>
                    <button wire:click="closeCreateForm" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <form wire:submit.prevent="createCategory" class="space-y-4">
                    <!-- Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                        <input wire:model="name" type="text" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                        @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea wire:model="description" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"></textarea>
                        @error('description') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Icon and Color -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Icon</label>
                            <select wire:model="icon" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                                @foreach($availableIcons as $iconClass => $iconName)
                                    <option value="{{ $iconClass }}">{{ $iconName }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                            <input wire:model="color" type="color" 
                                   class="w-full h-10 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                        </div>
                    </div>

                    <!-- Preview -->
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Preview</label>
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                                 style="background-color: {{ $color }}20">
                                <i class="{{ $icon }}" style="color: {{ $color }}"></i>
                            </div>
                            <span class="font-medium">{{ $name ?: 'Category Name' }}</span>
                        </div>
                    </div>

                    <!-- Options -->
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input wire:model="is_featured" type="checkbox" 
                                   class="rounded text-purple-600 focus:ring-purple-500">
                            <span class="ml-2 text-sm text-gray-700">Featured category</span>
                        </label>
                    </div>

                    <!-- Sort Order -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                        <input wire:model="sort_order" type="number" min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" wire:click="closeCreateForm"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                            Create Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
<!-- Edit Category Modal -->
@if($showEditForm && $editingCategory)
    <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Edit Category</h3>
                <button wire:click="closeEditForm" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form wire:submit.prevent="updateCategory" class="space-y-4">
                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                    <input wire:model="name" type="text" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea wire:model="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"></textarea>
                    @error('description') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Icon and Color -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Icon</label>
                        <select wire:model="icon" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            @foreach($availableIcons as $iconClass => $iconName)
                                <option value="{{ $iconClass }}">{{ $iconName }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                        <input wire:model="color" type="color" 
                               class="w-full h-10 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>
                </div>

                <!-- Preview -->
                <div class="p-3 bg-gray-50 rounded-lg">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Preview</label>
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                             style="background-color: {{ $color }}20">
                            <i class="{{ $icon }}" style="color: {{ $color }}"></i>
                        </div>
                        <span class="font-medium">{{ $name ?: 'Category Name' }}</span>
                    </div>
                </div>

                <!-- Options -->
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input wire:model="is_featured" type="checkbox" 
                               class="rounded text-purple-600 focus:ring-purple-500">
                        <span class="ml-2 text-sm text-gray-700">Featured category</span>
                    </label>
                </div>

                <!-- Sort Order -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                    <input wire:model="sort_order" type="number" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-3 pt-4">
                    <button type="button" wire:click="closeEditForm"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                        Update Category
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif