{{-- resources/views/livewire/marketplace/partial/modals/category-modals.blade.php --}}

<!-- Create Category Modal -->
@if($showCreateCategoryForm)
    <div class="fixed inset-0 bg-black bg-opacity-50 dark:bg-gray-900 dark:bg-opacity-60 z-50 flex items-center justify-center p-4 transition-colors duration-300">
        <div class="bg-white dark:bg-gray-800 rounded-lg max-w-md w-full p-6 max-h-[90vh] overflow-y-auto transition-colors duration-300">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Create Category</h3>
                <button wire:click="closeCreateCategoryForm" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-400 transition-colors duration-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form wire:submit.prevent="createCategory" class="space-y-4">
                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name *</label>
                    <input wire:model="categoryName" type="text" 
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-colors duration-300"
                           placeholder="Enter category name">
                    @error('categoryName') <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                    <textarea wire:model="categoryDescription" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-colors duration-300"
                              placeholder="Brief description of the category"></textarea>
                    @error('categoryDescription') <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Icon and Color -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Icon</label>
                        <select wire:model="categoryIcon" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-colors duration-300">
                            @foreach($availableIcons as $iconClass => $iconName)
                                <option value="{{ $iconClass }}">{{ $iconName }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Color</label>
                        <input wire:model="categoryColor" type="color" 
                               class="w-full h-10 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-colors duration-300">
                    </div>
                </div>

                <!-- Preview -->
                <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg transition-colors duration-300">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Preview</label>
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                             style="background-color: {{ $categoryColor }}20">
                            <i class="{{ $categoryIcon }}" style="color: {{ $categoryColor }}"></i>
                        </div>
                        <div>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $categoryName ?: 'Category Name' }}</span>
                            @if($categoryDescription)
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $categoryDescription }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Options -->
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input wire:model="categoryIsFeatured" type="checkbox" 
                               class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Featured category (appears in featured section)</span>
                    </label>
                </div>

                <!-- Sort Order -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sort Order</label>
                    <input wire:model="categorySortOrder" type="number" min="0"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-colors duration-300"
                           placeholder="0">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Lower numbers appear first</p>
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" wire:click="closeCreateCategoryForm"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-300">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors duration-300">
                        Create Category
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif

<!-- Edit Category Modal -->
@if($showEditCategoryForm && $editingCategory)
    <div class="fixed inset-0 bg-black bg-opacity-50 dark:bg-gray-900 dark:bg-opacity-60 z-50 flex items-center justify-center p-4 transition-colors duration-300">
        <div class="bg-white dark:bg-gray-800 rounded-lg max-w-md w-full p-6 max-h-[90vh] overflow-y-auto transition-colors duration-300">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Edit Category</h3>
                <button wire:click="closeEditCategoryForm" class="text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-400 transition-colors duration-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form wire:submit.prevent="updateCategory" class="space-y-4">
                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Name *</label>
                    <input wire:model="categoryName" type="text" 
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-colors duration-300"
                           placeholder="Enter category name">
                    @error('categoryName') <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                    <textarea wire:model="categoryDescription" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-colors duration-300"
                              placeholder="Brief description of the category"></textarea>
                    @error('categoryDescription') <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Icon and Color -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Icon</label>
                        <select wire:model="categoryIcon" 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-colors duration-300">
                            @foreach($availableIcons as $iconClass => $iconName)
                                <option value="{{ $iconClass }}">{{ $iconName }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Color</label>
                        <input wire:model="categoryColor" type="color" 
                               class="w-full h-10 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-colors duration-300">
                    </div>
                </div>

                <!-- Preview -->
                <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg transition-colors duration-300">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Preview</label>
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                             style="background-color: {{ $categoryColor }}20">
                            <i class="{{ $categoryIcon }}" style="color: {{ $categoryColor }}"></i>
                        </div>
                        <div>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $categoryName ?: 'Category Name' }}</span>
                            @if($categoryDescription)
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $categoryDescription }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Current Stats -->
                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg transition-colors duration-300">
                    <h4 class="text-sm font-medium text-blue-900 dark:text-blue-300 mb-2">Category Stats</h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-blue-700 dark:text-blue-400">Items:</span>
                            <span class="font-medium text-blue-900 dark:text-blue-300">{{ $editingCategory->items_count ?? 0 }}</span>
                        </div>
                        <div>
                            <span class="text-blue-700 dark:text-blue-400">Status:</span>
                            <span class="font-medium text-blue-900 dark:text-blue-300">{{ $editingCategory->is_active ? 'Active' : 'Inactive' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Options -->
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input wire:model="categoryIsFeatured" type="checkbox" 
                               class="rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Featured category (appears in featured section)</span>
                    </label>
                </div>

                <!-- Sort Order -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sort Order</label>
                    <input wire:model="categorySortOrder" type="number" min="0"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-colors duration-300"
                           placeholder="0">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Lower numbers appear first</p>
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" wire:click="closeEditCategoryForm"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-300">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors duration-300">
                        Update Category
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif