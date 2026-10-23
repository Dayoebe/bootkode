{{-- resources/views/livewire/marketplace/partial/modals/category-modals.blade.php --}}

<!-- Create Category Modal -->
@if($showCreateCategoryForm)
    <div class="fixed inset-0 bg-black bg-opacity-50 dark:bg-gray-900 dark:bg-opacity-60 z-50 flex items-center justify-center p-4 transition-colors duration-300" x-trap.inert.noscroll="true" role="dialog" aria-modal="true" aria-label="Create Category" tabindex="-1" x-data="{}" @keydown.escape.stop="$wire.set(&#x27;showCreateCategoryForm&#x27;, false)">
        <div class="bg-surface dark:bg-gray-800 rounded-lg max-w-md w-full p-6 max-h-[90vh] overflow-y-auto transition-colors duration-300">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-content dark:text-white">Create Category</h3>
                <button wire:click="closeCreateCategoryForm" class="text-content-muted dark:text-content-muted hover:text-content-muted dark:hover:text-content-muted transition-colors duration-300" aria-label="Close">
                    <i class="fas fa-times" aria-hidden="true"></i>
                </button>
            </div>

            <form wire:submit.prevent="createCategory" class="space-y-4">
                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Name *</label>
                    <input aria-label="Enter category name" wire:model="categoryName" type="text"
                           class="w-full px-3 py-2 border border-line dark:border-gray-600 bg-surface dark:bg-gray-700 text-content dark:text-white rounded-lg focus:ring-brand/30 focus:border-brand transition-colors duration-300"
                           placeholder="Enter category name">
                    @error('categoryName') <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Description</label>
                    <textarea aria-label="Brief description of the category" wire:model="categoryDescription" rows="3"
                              class="w-full px-3 py-2 border border-line dark:border-gray-600 bg-surface dark:bg-gray-700 text-content dark:text-white rounded-lg focus:ring-brand/30 focus:border-brand transition-colors duration-300"
                              placeholder="Brief description of the category"></textarea>
                    @error('categoryDescription') <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Icon and Color -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Icon</label>
                        <select aria-label="Category icon" wire:model="categoryIcon"
                                class="w-full px-3 py-2 border border-line dark:border-gray-600 bg-surface dark:bg-gray-700 text-content dark:text-white rounded-lg focus:ring-brand/30 focus:border-brand transition-colors duration-300">
                            @foreach($availableIcons as $iconClass => $iconName)
                                <option value="{{ $iconClass }}">{{ $iconName }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Color</label>
                        <input aria-label="Category color" wire:model="categoryColor" type="color"
                               class="w-full h-10 border border-line dark:border-gray-600 rounded-lg focus:ring-brand/30 focus:border-brand transition-colors duration-300">
                    </div>
                </div>

                <!-- Preview -->
                <div class="p-3 bg-surface-muted dark:bg-gray-700 rounded-lg transition-colors duration-300">
                    <label class="block text-sm font-medium text-content dark:text-gray-300 mb-2">Preview</label>
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                             style="background-color: {{ $categoryColor }}20">
                            <i class="{{ $categoryIcon }}" style="color: {{ $categoryColor }}"></i>
                        </div>
                        <div>
                            <span class="font-medium text-content dark:text-white">{{ $categoryName ?: 'Category Name' }}</span>
                            @if($categoryDescription)
                                <p class="text-sm text-content-muted dark:text-content-muted">{{ $categoryDescription }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Options -->
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input wire:model="categoryIsFeatured" type="checkbox"
                               class="rounded border-line dark:border-gray-600 text-brand focus:ring-brand/30">
                        <span class="ml-2 text-sm text-content dark:text-gray-300">Featured category (appears in featured section)</span>
                    </label>
                </div>

                <!-- Sort Order -->
                <div>
                    <label class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Sort Order</label>
                    <input aria-label="0" wire:model="categorySortOrder" type="number" min="0"
                           class="w-full px-3 py-2 border border-line dark:border-gray-600 bg-surface dark:bg-gray-700 text-content dark:text-white rounded-lg focus:ring-brand/30 focus:border-brand transition-colors duration-300"
                           placeholder="0">
                    <p class="text-xs text-content-muted dark:text-content-muted mt-1">Lower numbers appear first</p>
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-line dark:border-gray-700">
                    <button type="button" wire:click="closeCreateCategoryForm"
                            class="px-4 py-2 border border-line dark:border-gray-600 rounded-lg text-content dark:text-gray-300 hover:bg-surface-muted dark:hover:bg-gray-700 transition-colors duration-300">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-action dark:bg-action text-action-foreground rounded-lg hover:bg-action dark:hover:bg-action transition-colors duration-300">
                        Create Category
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif

<!-- Edit Category Modal -->
@if($showEditCategoryForm && $editingCategory)
    <div class="fixed inset-0 bg-black bg-opacity-50 dark:bg-gray-900 dark:bg-opacity-60 z-50 flex items-center justify-center p-4 transition-colors duration-300" x-trap.inert.noscroll="true" role="dialog" aria-modal="true" aria-label="Edit Category" tabindex="-1" x-data="{}" @keydown.escape.stop="$wire.set(&#x27;showEditCategoryForm&#x27;, false)">
        <div class="bg-surface dark:bg-gray-800 rounded-lg max-w-md w-full p-6 max-h-[90vh] overflow-y-auto transition-colors duration-300">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-content dark:text-white">Edit Category</h3>
                <button wire:click="closeEditCategoryForm" class="text-content-muted dark:text-content-muted hover:text-content-muted dark:hover:text-content-muted transition-colors duration-300" aria-label="Close">
                    <i class="fas fa-times" aria-hidden="true"></i>
                </button>
            </div>

            <form wire:submit.prevent="updateCategory" class="space-y-4">
                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Name *</label>
                    <input aria-label="Enter category name" wire:model="categoryName" type="text"
                           class="w-full px-3 py-2 border border-line dark:border-gray-600 bg-surface dark:bg-gray-700 text-content dark:text-white rounded-lg focus:ring-brand/30 focus:border-brand transition-colors duration-300"
                           placeholder="Enter category name">
                    @error('categoryName') <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Description</label>
                    <textarea aria-label="Brief description of the category" wire:model="categoryDescription" rows="3"
                              class="w-full px-3 py-2 border border-line dark:border-gray-600 bg-surface dark:bg-gray-700 text-content dark:text-white rounded-lg focus:ring-brand/30 focus:border-brand transition-colors duration-300"
                              placeholder="Brief description of the category"></textarea>
                    @error('categoryDescription') <p class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Icon and Color -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Icon</label>
                        <select aria-label="Category icon" wire:model="categoryIcon"
                                class="w-full px-3 py-2 border border-line dark:border-gray-600 bg-surface dark:bg-gray-700 text-content dark:text-white rounded-lg focus:ring-brand/30 focus:border-brand transition-colors duration-300">
                            @foreach($availableIcons as $iconClass => $iconName)
                                <option value="{{ $iconClass }}">{{ $iconName }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Color</label>
                        <input aria-label="Category color" wire:model="categoryColor" type="color"
                               class="w-full h-10 border border-line dark:border-gray-600 rounded-lg focus:ring-brand/30 focus:border-brand transition-colors duration-300">
                    </div>
                </div>

                <!-- Preview -->
                <div class="p-3 bg-surface-muted dark:bg-gray-700 rounded-lg transition-colors duration-300">
                    <label class="block text-sm font-medium text-content dark:text-gray-300 mb-2">Preview</label>
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                             style="background-color: {{ $categoryColor }}20">
                            <i class="{{ $categoryIcon }}" style="color: {{ $categoryColor }}"></i>
                        </div>
                        <div>
                            <span class="font-medium text-content dark:text-white">{{ $categoryName ?: 'Category Name' }}</span>
                            @if($categoryDescription)
                                <p class="text-sm text-content-muted dark:text-content-muted">{{ $categoryDescription }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Current Stats -->
                <div class="p-3 bg-brand/10 dark:bg-action/20 border border-brand dark:border-brand rounded-lg transition-colors duration-300">
                    <h4 class="text-sm font-medium text-brand dark:text-brand mb-2">Category Stats</h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-brand dark:text-brand">Items:</span>
                            <span class="font-medium text-brand dark:text-brand">{{ $editingCategory->items_count ?? 0 }}</span>
                        </div>
                        <div>
                            <span class="text-brand dark:text-brand">Status:</span>
                            <span class="font-medium text-brand dark:text-brand">{{ $editingCategory->is_active ? 'Active' : 'Inactive' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Options -->
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input wire:model="categoryIsFeatured" type="checkbox"
                               class="rounded border-line dark:border-gray-600 text-brand focus:ring-brand/30">
                        <span class="ml-2 text-sm text-content dark:text-gray-300">Featured category (appears in featured section)</span>
                    </label>
                </div>

                <!-- Sort Order -->
                <div>
                    <label class="block text-sm font-medium text-content dark:text-gray-300 mb-1">Sort Order</label>
                    <input aria-label="0" wire:model="categorySortOrder" type="number" min="0"
                           class="w-full px-3 py-2 border border-line dark:border-gray-600 bg-surface dark:bg-gray-700 text-content dark:text-white rounded-lg focus:ring-brand/30 focus:border-brand transition-colors duration-300"
                           placeholder="0">
                    <p class="text-xs text-content-muted dark:text-content-muted mt-1">Lower numbers appear first</p>
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-line dark:border-gray-700">
                    <button type="button" wire:click="closeEditCategoryForm"
                            class="px-4 py-2 border border-line dark:border-gray-600 rounded-lg text-content dark:text-gray-300 hover:bg-surface-muted dark:hover:bg-gray-700 transition-colors duration-300">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-action dark:bg-action text-action-foreground rounded-lg hover:bg-action dark:hover:bg-action transition-colors duration-300">
                        Update Category
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif
