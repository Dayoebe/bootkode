{{-- resources/views/livewire/blog/admin-blog-categories.blade.php --}}
<div>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Blog Categories</h1>
            <p class="text-gray-600 dark:text-gray-400">Organize your blog content with categories</p>
        </div>
        <button wire:click="openCreateModal" 
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            <i class="fas fa-plus mr-2"></i>
            Add Category
        </button>
    </div>

    {{-- Search --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
        <input type="text" 
               wire:model.live.debounce.300ms="search" 
               placeholder="Search categories..."
               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
    </div>

    {{-- Categories Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Posts</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($categories as $category)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-4 h-4 rounded-full mr-3" style="background-color: {{ $category->color }}"></div>
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $category->name }}</div>
                                    <div class="text-sm text-gray-500">/{{ $category->slug }}</div>
                                    @if($category->description)
                                        <div class="text-sm text-gray-600 mt-1">{{ Str::limit($category->description, 100) }}</div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 text-sm rounded">
                                {{ $category->posts_count }} posts
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                @if($category->is_active)
                                    <span class="px-2 py-1 bg-green-100 text-green-800 text-sm rounded">Active</span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-800 text-sm rounded">Inactive</span>
                                @endif
                                <button wire:click="toggleStatus({{ $category->id }})" 
                                        class="ml-2 text-sm text-blue-600 hover:text-blue-800">
                                    Toggle
                                </button>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $category->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <button wire:click="openEditModal({{ $category->id }})" 
                                        class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button wire:click="confirmDelete({{ $category->id }})" 
                                        class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-folder text-4xl mb-4"></i>
                            <p>No categories found.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="px-6 py-3 border-t border-gray-200 dark:border-gray-700">
            {{ $categories->links() }}
        </div>
    </div>

    {{-- Create/Edit Modal --}}
    @if($showModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-md mx-4 w-full">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    {{ $editMode ? 'Edit Category' : 'Create Category' }}
                </h3>
                
                <form wire:submit.prevent="saveCategory">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name</label>
                            <input type="text" 
                                   wire:model.live="name" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @error('name')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Slug</label>
                            <input type="text" 
                                   wire:model="slug" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @error('slug')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                            <textarea wire:model="description" 
                                      rows="3" 
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"></textarea>
                            @error('description')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Color</label>
                            <input type="color" 
                                   wire:model="color" 
                                   class="w-full h-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            @error('color')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Sort Order</label>
                                <input type="number" 
                                       wire:model="sort_order" 
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            <div class="flex items-center mt-6">
                                <input type="checkbox" 
                                       wire:model="is_active" 
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <label class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-4 mt-6">
                        <button type="button" 
                                wire:click="closeModal" 
                                class="px-4 py-2 text-gray-600 hover:text-gray-800">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            {{ $editMode ? 'Update' : 'Create' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Delete Modal --}}
    @if($showDeleteModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-sm mx-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Delete Category</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Are you sure? Categories with posts cannot be deleted.</p>
                <div class="flex justify-end space-x-4">
                    <button wire:click="$set('showDeleteModal', false)" 
                            class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                    <button wire:click="deleteCategory" 
                            class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Delete</button>
                </div>
            </div>
        </div>
    @endif
</div>
