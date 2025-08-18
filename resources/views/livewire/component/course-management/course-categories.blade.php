<div class="bg-gray-800 p-8 rounded-2xl shadow-2xl text-white animate__animated animate__fadeIn" x-data="{ tooltip: '', localModalOpen: false }" x-init="$watch('$wire.isModalOpen', value => localModalOpen = value)" wire:category-updated.window="$refresh">
    @csrf
    <!-- Header with Create Button -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 animate__animated animate__fadeInDown">
        <h1 class="text-3xl font-extrabold text-white">
            <i class="fas fa-tags mr-2 text-indigo-300" aria-hidden="true"></i> Course Categories
        </h1>
        <div class="flex space-x-3">
            <button wire:click="create" class="w-full sm:w-auto px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 flex items-center" aria-label="Create new category">
                <i class="fas fa-plus-circle mr-2"></i> Create New Category
            </button>
            <button wire:click="suggestAiContent" class="w-full sm:w-auto px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 flex items-center" aria-label="Suggest AI-generated category">
                <i class="fas fa-robot mr-2"></i> AI Suggest
            </button>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="mb-6 animate__animated animate__fadeIn" style="animation-delay: 0.1s">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search categories..."
               class="w-full px-4 py-2 bg-indigo-800/50 border border-indigo-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 text-white placeholder-indigo-300 shadow-md transition-all duration-300 hover:shadow-lg"
               aria-label="Search categories">
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-4 animate__animated animate__bounceIn" role="alert">
            {{ session('message') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-4 animate__animated animate__shakeX" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <!-- Categories Table -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-xl overflow-hidden animate__animated animate__fadeIn" style="animation-delay: 0.2s" wire:loading.class="opacity-50">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                <thead class="bg-indigo-50 dark:bg-indigo-900/50">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-indigo-200 uppercase tracking-wider">Name</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-indigo-200 uppercase tracking-wider hidden md:table-cell">Description</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-indigo-200 uppercase tracking-wider hidden sm:table-cell">Slug</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-indigo-200 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600">
                    <tr wire:loading wire:target="search, $refresh">
                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-300">
                            <i class="fas fa-circle-notch fa-spin mr-2"></i> Loading...
                        </td>
                    </tr>
                    @forelse ($categories as $index => $category)
                        <tr class="hover:bg-indigo-50 dark:hover:bg-indigo-900/50 animate__animated animate__fadeInUp" style="animation-delay: {{ $index * 0.1 }}s">
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ $category->name }}
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-500 dark:text-gray-300 hidden md:table-cell">
                                {{ Str::limit($category->description, 50) }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 hidden sm:table-cell">
                                {{ $category->slug }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button wire:click="edit({{ $category->id }})" class="text-indigo-500 hover:text-indigo-700 transition-colors" title="Edit {{ $category->name }}" aria-label="Edit {{ $category->name }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button wire:click="confirmDelete({{ $category->id }})" class="text-red-500 hover:text-red-700 transition-colors" title="Delete {{ $category->name }}" aria-label="Delete {{ $category->name }}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-300">No categories found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4 animate__animated animate__fadeIn" style="animation-delay: 0.3s">
        {{ $categories->links('pagination::tailwind') }}
    </div>

    <!-- Create/Edit Modal -->
    <div x-cloak x-show="localModalOpen" x-trap="localModalOpen" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center p-4 z-50 animate__animated animate__fadeIn">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md mx-2 p-6" role="dialog" aria-modal="true" aria-labelledby="modal-title">
            <h2 id="modal-title" class="text-xl font-semibold text-gray-800 dark:text-white mb-4">
                {{ $categoryId ? 'Edit Category' : 'Create New Category' }}
            </h2>
            <form wire:submit="store">
                @csrf
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Name <span class="text-red-400">*</span></label>
                    <input type="text" wire:model.live="name" id="name"
                           class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm py-2 px-3 focus:outline-none focus:ring-purple-500 focus:border-purple-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                           aria-label="Category Name" aria-required="true" x-ref="nameInput">
                    @error('name') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Description</label>
                    <textarea wire:model="description" id="description" rows="3"
                              class="mt-1 block w-full border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm py-2 px-3 focus:outline-none focus:ring-purple-500 focus:border-purple-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white resize-y"
                              aria-label="Category Description" maxlength="1000"></textarea>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ strlen($description) }}/1000 characters</p>
                    @error('description') <span class="text-red-400 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                    <button type="button" @click="localModalOpen = false" wire:click="closeModal" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                        Cancel
                    </button>
                    <button type="submit" wire:loading.attr="disabled"
                            class="px-4 py-2 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 disabled:opacity-50 flex items-center">
                        <span wire:loading.remove>{{ $categoryId ? 'Update' : 'Create' }}</span>
                        <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i> Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-cloak x-show="$wire.showConfirmDelete" x-trap="$wire.showConfirmDelete" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center p-4 z-50 animate__animated animate__fadeIn">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md mx-2 p-6" role="dialog" aria-modal="true" aria-labelledby="delete-modal-title">
            <h2 id="modal-title" class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Confirm Deletion</h2>
            <p class="text-gray-600 dark:text-gray-300 mb-4">Are you sure you want to delete this category? This action cannot be undone.</p>
            <div class="flex justify-end space-x-3">
                <button wire:click="$set('showConfirmDelete', false)" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                    Cancel
                </button>
                <button wire:click="delete" class="px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors focus:outline-none focus:ring-2 focus:ring-red-500">
                    Delete
                </button>
            </div>
        </div>
    </div>
</div>