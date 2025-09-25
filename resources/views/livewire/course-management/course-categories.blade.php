<div class="bg-gradient-to-br from-slate-50 to-blue-50 dark:from-gray-900 dark:to-gray-800 min-h-screen p-4 lg:p-6 transition-colors duration-300" 
     x-data="{ tooltip: '', localModalOpen: @entangle('isModalOpen') }" 
     wire:category-updated.window="$refresh">

    <!-- Header Section -->
    <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-xl rounded-2xl shadow-lg p-6 mb-6 border border-white/20 dark:border-gray-700/30 transition-colors duration-300">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between">
            <div class="flex items-center mb-4 lg:mb-0">
                <div class="relative">
                    <div class="bg-purple-500 p-3 rounded-xl mr-4 shadow-md">
                        <i class="fas fa-tags text-white text-xl"></i>
                    </div>
                    <div class="absolute -top-1 -right-1 bg-emerald-500 w-4 h-4 rounded-full flex items-center justify-center">
                        <i class="fas fa-plus text-white text-[8px]"></i>
                    </div>
                </div>
                <div>
                    <h1 class="text-2xl font-bold bg-gradient-to-r from-gray-900 to-purple-600 dark:from-white dark:to-purple-400 bg-clip-text text-transparent">
                        Course Categories
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1 text-sm">
                        Organize your courses with meaningful categories
                    </p>
                </div>
            </div>
            
            <div class="flex flex-col sm:flex-row gap-3">
                <button wire:click="create" 
                        class="group bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-xl font-semibold text-sm transition-all duration-300 flex items-center justify-center transform hover:scale-105 shadow-lg">
                    <i class="fas fa-plus-circle mr-2 group-hover:rotate-90 transition-transform duration-300"></i> 
                    Create Category
                </button>
                
                <button wire:click="suggestAiContent" 
                        class="group bg-purple-600 hover:bg-purple-700 text-white px-4 py-2.5 rounded-xl font-semibold text-sm transition-all duration-300 flex items-center justify-center transform hover:scale-105 shadow-lg">
                    <i class="fas fa-robot mr-2 group-hover:scale-110 transition-transform duration-300"></i> 
                    AI Suggest
                </button>
            </div>
        </div>
    </div>

    <!-- Search Section -->
    <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-xl rounded-2xl shadow-lg p-6 mb-6 border border-white/20 dark:border-gray-700/30 transition-colors duration-300">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center transition-colors duration-300">
                <i class="fas fa-search text-blue-500 mr-2"></i>
                Search Categories
            </h3>
        </div>
        
        <div class="relative">
            <input type="text" wire:model.live.debounce.300ms="search" 
                   placeholder="Search categories by name or description..."
                   class="w-full pl-10 pr-4 py-3 bg-white dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:border-purple-500 dark:focus:border-purple-400 focus:ring-2 focus:ring-purple-500/20 transition-all duration-300"
                   aria-label="Search categories">
            <div class="absolute left-3 top-3 text-gray-400">
                <i class="fas fa-search"></i>
            </div>
            <div wire:loading wire:target="search" class="absolute right-3 top-3">
                <i class="fas fa-spinner animate-spin text-purple-500"></i>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 px-4 py-3 rounded-xl mb-4 transition-colors duration-300" 
             role="alert">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('message') }}
            </div>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="bg-red-100 dark:bg-red-900/20 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-300 px-4 py-3 rounded-xl mb-4 transition-colors duration-300" 
             role="alert">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Categories Table -->
    <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-xl rounded-2xl shadow-lg border border-white/20 dark:border-gray-700/30 overflow-hidden transition-colors duration-300"
         wire:loading.class="opacity-50">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider transition-colors duration-300">
                            Name
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hidden md:table-cell transition-colors duration-300">
                            Description
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hidden sm:table-cell transition-colors duration-300">
                            Slug
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider transition-colors duration-300">
                            Courses
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider transition-colors duration-300">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-600 transition-colors duration-300">
                    <tr wire:loading wire:target="search, $refresh">
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400 transition-colors duration-300">
                            <i class="fas fa-circle-notch fa-spin mr-2"></i> Loading...
                        </td>
                    </tr>
                    @forelse ($categories as $index => $category)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white transition-colors duration-300">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-gradient-to-r from-purple-400 to-blue-400 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-tag text-white text-xs"></i>
                                    </div>
                                    {{ $category->name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300 hidden md:table-cell transition-colors duration-300">
                                {{ Str::limit($category->description, 50) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 hidden sm:table-cell transition-colors duration-300">
                                <code class="bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded text-xs">
                                    {{ $category->slug }}
                                </code>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300 transition-colors duration-300">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-200">
                                    {{ $category->courses_count }} courses
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <button wire:click="edit({{ $category->id }})" 
                                            class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 transition-colors duration-300 p-2 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20" 
                                            title="Edit {{ $category->name }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button wire:click="confirmDelete({{ $category->id }})" 
                                            class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 transition-colors duration-300 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20" 
                                            title="Delete {{ $category->name }}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4 transition-colors duration-300">
                                        <i class="fas fa-tags text-gray-400 text-2xl"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2 transition-colors duration-300">No categories found</h3>
                                    <p class="text-gray-500 dark:text-gray-400 mb-4 text-sm transition-colors duration-300">
                                        {{ $search ? 'Try adjusting your search terms.' : 'Create your first category to get started.' }}
                                    </p>
                                    @if(!$search)
                                        <button wire:click="create" 
                                                class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-300">
                                            <i class="fas fa-plus mr-2"></i>
                                            Create First Category
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($categories->hasPages())
        <div class="mt-6">
            {{ $categories->links('pagination::tailwind') }}
        </div>
    @endif

    <!-- Create/Edit Modal -->
    <div x-show="localModalOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50"
         x-cloak>
        <div @click.away="$wire.closeModal()"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95" 
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100" 
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md mx-2 p-6 border border-gray-200 dark:border-gray-700 transition-colors duration-300">
            
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-purple-100 dark:bg-purple-900/50 rounded-full flex items-center justify-center mx-auto mb-4 transition-colors duration-300">
                    <i class="fas {{ $categoryId ? 'fa-edit' : 'fa-plus' }} text-purple-600 dark:text-purple-400 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 transition-colors duration-300">
                    {{ $categoryId ? 'Edit Category' : 'Create New Category' }}
                </h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm transition-colors duration-300">
                    {{ $categoryId ? 'Update the category details below.' : 'Add a new category to organize your courses.' }}
                </p>
            </div>

            <form wire:submit="store" class="space-y-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-300">
                        Category Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model="name" id="name"
                           class="w-full px-4 py-3 bg-white dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:border-purple-500 dark:focus:border-purple-400 focus:ring-2 focus:ring-purple-500/20 transition-all duration-300"
                           placeholder="e.g., Web Development, Data Science..."
                           x-ref="nameInput">
                    @error('name') 
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-300">
                        Description
                    </label>
                    <textarea wire:model="description" id="description" rows="3"
                              class="w-full px-4 py-3 bg-white dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:border-purple-500 dark:focus:border-purple-400 focus:ring-2 focus:ring-purple-500/20 transition-all duration-300 resize-none"
                              placeholder="Describe what this category covers..."
                              maxlength="1000"></textarea>
                    <div class="flex justify-between items-center mt-2 text-xs text-gray-500 dark:text-gray-400">
                        <span>Optional but recommended for clarity</span>
                        <span>{{ strlen($description) }}/1000</span>
                    </div>
                    @error('description') 
                        <p class="mt-2 text-sm text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex gap-3 pt-4">
                    <button type="button" wire:click="closeModal"
                            class="flex-1 bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-gray-200 font-semibold py-3 px-6 rounded-xl transition-colors duration-300">
                        Cancel
                    </button>
                    <button type="submit" wire:loading.attr="disabled"
                            class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-xl transition-colors duration-300 disabled:opacity-50">
                        <span wire:loading.remove>{{ $categoryId ? 'Update' : 'Create' }}</span>
                        <span wire:loading>
                            <i class="fas fa-spinner fa-spin mr-2"></i>{{ $categoryId ? 'Updating...' : 'Creating...' }}
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-show="$wire.showConfirmDelete" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50"
         x-cloak>
        <div @click.away="$wire.set('showConfirmDelete', false)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95" 
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100" 
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md mx-2 p-6 border border-gray-200 dark:border-gray-700 transition-colors duration-300">
            
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-red-100 dark:bg-red-900/50 rounded-full flex items-center justify-center mx-auto mb-4 transition-colors duration-300">
                    <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 transition-colors duration-300">Confirm Deletion</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm transition-colors duration-300">
                    Are you sure you want to delete this category? This action cannot be undone.
                </p>
            </div>

            <div class="flex gap-3">
                <button wire:click="$set('showConfirmDelete', false)"
                        class="flex-1 bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-gray-200 font-semibold py-3 px-6 rounded-xl transition-colors duration-300">
                    Cancel
                </button>
                <button wire:click="delete"
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-xl transition-colors duration-300">
                    Delete Category
                </button>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div wire:loading class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-40">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 flex items-center shadow-2xl border border-gray-200 dark:border-gray-700 transition-colors duration-300">
            <div class="relative mr-4">
                <div class="animate-spin rounded-full h-8 w-8 border-2 border-gray-300 dark:border-gray-600"></div>
                <div class="animate-spin rounded-full h-8 w-8 border-2 border-purple-600 border-t-transparent absolute top-0"></div>
            </div>
            <span class="text-gray-800 dark:text-white font-semibold">Processing...</span>
        </div>
    </div>

    @push('styles')
        <style>
            [x-cloak] { display: none !important; }
        </style>
    @endpush
</div>