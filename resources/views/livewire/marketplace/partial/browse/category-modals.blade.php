{{-- resources/views/livewire/marketplace/partial/modals/category-modals.blade.php --}}

<!-- Create Category Modal -->
@if($showCreateCategoryForm)
    <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Create Category</h3>
                <button wire:click="closeCreateCategoryForm" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form wire:submit.prevent="createCategory" class="space-y-4">
                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                    <input wire:model="categoryName" type="text" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                           placeholder="Enter category name">
                    @error('categoryName') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea wire:model="categoryDescription" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                              placeholder="Brief description of the category"></textarea>
                    @error('categoryDescription') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Icon and Color -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Icon</label>
                        <select wire:model="categoryIcon" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            @foreach($availableIcons as $iconClass => $iconName)
                                <option value="{{ $iconClass }}">{{ $iconName }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                        <input wire:model="categoryColor" type="color" 
                               class="w-full h-10 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>
                </div>

                <!-- Preview -->
                <div class="p-3 bg-gray-50 rounded-lg">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Preview</label>
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                             style="background-color: {{ $categoryColor }}20">
                            <i class="{{ $categoryIcon }}" style="color: {{ $categoryColor }}"></i>
                        </div>
                        <div>
                            <span class="font-medium">{{ $categoryName ?: 'Category Name' }}</span>
                            @if($categoryDescription)
                                <p class="text-sm text-gray-600">{{ $categoryDescription }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Options -->
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input wire:model="categoryIsFeatured" type="checkbox" 
                               class="rounded text-purple-600 focus:ring-purple-500">
                        <span class="ml-2 text-sm text-gray-700">Featured category (appears in featured section)</span>
                    </label>
                </div>

                <!-- Sort Order -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                    <input wire:model="categorySortOrder" type="number" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                           placeholder="0">
                    <p class="text-xs text-gray-500 mt-1">Lower numbers appear first</p>
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" wire:click="closeCreateCategoryForm"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        Create Category
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif

<!-- Edit Category Modal -->
@if($showEditCategoryForm && $editingCategory)
    <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Edit Category</h3>
                <button wire:click="closeEditCategoryForm" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form wire:submit.prevent="updateCategory" class="space-y-4">
                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                    <input wire:model="categoryName" type="text" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                           placeholder="Enter category name">
                    @error('categoryName') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea wire:model="categoryDescription" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                              placeholder="Brief description of the category"></textarea>
                    @error('categoryDescription') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Icon and Color -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Icon</label>
                        <select wire:model="categoryIcon" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                            @foreach($availableIcons as $iconClass => $iconName)
                                <option value="{{ $iconClass }}">{{ $iconName }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                        <input wire:model="categoryColor" type="color" 
                               class="w-full h-10 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    </div>
                </div>

                <!-- Preview -->
                <div class="p-3 bg-gray-50 rounded-lg">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Preview</label>
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                             style="background-color: {{ $categoryColor }}20">
                            <i class="{{ $categoryIcon }}" style="color: {{ $categoryColor }}"></i>
                        </div>
                        <div>
                            <span class="font-medium">{{ $categoryName ?: 'Category Name' }}</span>
                            @if($categoryDescription)
                                <p class="text-sm text-gray-600">{{ $categoryDescription }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Current Stats -->
                <div class="p-3 bg-blue-50 rounded-lg">
                    <h4 class="text-sm font-medium text-blue-900 mb-2">Category Stats</h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-blue-700">Items:</span>
                            <span class="font-medium text-blue-900">{{ $editingCategory->items_count ?? 0 }}</span>
                        </div>
                        <div>
                            <span class="text-blue-700">Status:</span>
                            <span class="font-medium text-blue-900">{{ $editingCategory->is_active ? 'Active' : 'Inactive' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Options -->
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input wire:model="categoryIsFeatured" type="checkbox" 
                               class="rounded text-purple-600 focus:ring-purple-500">
                        <span class="ml-2 text-sm text-gray-700">Featured category (appears in featured section)</span>
                    </label>
                </div>

                <!-- Sort Order -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                    <input wire:model="categorySortOrder" type="number" min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                           placeholder="0">
                    <p class="text-xs text-gray-500 mt-1">Lower numbers appear first</p>
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-3 pt-4 border-t">
                    <button type="button" wire:click="closeEditCategoryForm"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        Update Category
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif