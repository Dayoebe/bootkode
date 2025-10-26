<div class="bg-themed-primary min-h-screen transition-colors duration-300">
    <div class="px-4 sm:px-6 lg:px-8 py-6 space-y-6" 
         x-data="{ tooltip: '', localModalOpen: @entangle('isModalOpen') }" 
         wire:category-updated.window="$refresh">
        
        <!-- Page Header -->
        <div class="bg-themed-secondary rounded-2xl shadow-lg border border-themed-primary p-6 animate__animated animate__fadeInDown transition-colors duration-300">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="bg-gradient-to-br from-purple-500 to-pink-500 p-4 rounded-2xl shadow-lg">
                        <i class="fas fa-tags text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-themed-primary transition-colors duration-300">
                            Course Categories
                        </h1>
                        <p class="text-themed-secondary mt-1 transition-colors duration-300">
                            Organize your courses with meaningful categories
                        </p>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-3">
                    <button wire:click="create" 
                            class="inline-flex items-center justify-center gap-2 bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-plus-circle"></i>
                        <span>Create Category</span>
                    </button>
                    
                    <button wire:click="suggestAiContent" 
                            class="inline-flex items-center justify-center gap-2 bg-purple-500 hover:bg-purple-600 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-wand-magic-sparkles"></i>
                        <span>AI Suggest</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Search Section -->
        <div class="bg-themed-secondary rounded-2xl shadow-lg border border-themed-primary p-6 transition-colors duration-300">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-themed-primary flex items-center gap-2 transition-colors duration-300">
                    <i class="fas fa-search text-accent-themed-primary"></i>
                    Search Categories
                </h3>
            </div>
            
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="search" 
                       placeholder="Search categories by name or description..."
                       class="w-full pl-10 pr-10 py-3 bg-themed-tertiary border-2 border-themed-primary rounded-xl text-themed-primary placeholder-themed-secondary focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all duration-300"
                       aria-label="Search categories">
                <div class="absolute left-3 top-1/2 -translate-y-1/2 text-themed-secondary transition-colors duration-300">
                    <i class="fas fa-search"></i>
                </div>
                <div wire:loading wire:target="search" class="absolute right-3 top-1/2 -translate-y-1/2">
                    <i class="fas fa-spinner animate-spin text-purple-500"></i>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('message'))
            <div class="bg-green-100/50 border border-green-400 text-green-700 px-6 py-4 rounded-xl transition-colors duration-300 animate__animated animate__fadeIn" 
                 role="alert">
                <div class="flex items-center gap-2">
                    <i class="fas fa-check-circle text-lg"></i>
                    <span class="font-medium">{{ session('message') }}</span>
                </div>
            </div>
        @endif
        @if (session()->has('error'))
            <div class="bg-red-100/50 border border-red-400 text-red-700 px-6 py-4 rounded-xl transition-colors duration-300 animate__animated animate__fadeIn" 
                 role="alert">
                <div class="flex items-center gap-2">
                    <i class="fas fa-exclamation-circle text-lg"></i>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- Categories Table -->
        <div class="bg-themed-secondary rounded-2xl shadow-lg border border-themed-primary overflow-hidden transition-colors duration-300"
             wire:loading.class="opacity-50">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-themed-primary">
                    <thead class="bg-themed-tertiary transition-colors duration-300">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-themed-primary uppercase tracking-wider transition-colors duration-300">
                                Name
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-themed-primary uppercase tracking-wider hidden md:table-cell transition-colors duration-300">
                                Description
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-themed-primary uppercase tracking-wider hidden sm:table-cell transition-colors duration-300">
                                Slug
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-themed-primary uppercase tracking-wider transition-colors duration-300">
                                Courses
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-themed-primary uppercase tracking-wider transition-colors duration-300">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-themed-primary transition-colors duration-300">
                        <tr wire:loading wire:target="search, $refresh">
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-themed-secondary transition-colors duration-300">
                                <div class="flex items-center justify-center gap-2">
                                    <i class="fas fa-circle-notch fa-spin"></i>
                                    <span>Loading...</span>
                                </div>
                            </td>
                        </tr>
                        @forelse ($categories as $index => $category)
                            <tr class="hover:bg-themed-tertiary transition-colors duration-200 animate__animated animate__fadeInUp" style="animation-delay: {{ $index * 0.05 }}s">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-themed-primary transition-colors duration-300">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-purple-400 to-accent-themed-primary rounded-xl flex items-center justify-center shadow-md">
                                            <i class="fas fa-tag text-white"></i>
                                        </div>
                                        <span class="font-semibold">{{ $category->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-themed-secondary hidden md:table-cell transition-colors duration-300">
                                    {{ Str::limit($category->description, 50) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-themed-secondary hidden sm:table-cell transition-colors duration-300">
                                    <code class="bg-themed-tertiary px-3 py-1 rounded-lg text-xs border border-themed-primary transition-colors duration-300">
                                        {{ $category->slug }}
                                    </code>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-themed-primary transition-colors duration-300">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-accent-themed-primary/20 text-accent-themed-primary transition-colors duration-300">
                                        {{ $category->courses_count }} courses
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center gap-2">
                                        <button wire:click="edit({{ $category->id }})" 
                                                class="p-2 rounded-lg text-accent-themed-primary hover:bg-accent-themed-primary/10 transition-all duration-300 transform hover:scale-110" 
                                                title="Edit {{ $category->name }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button wire:click="confirmDelete({{ $category->id }})" 
                                                class="p-2 rounded-lg text-red-600 hover:bg-red-100/50 transition-all duration-300 transform hover:scale-110" 
                                                title="Delete {{ $category->name }}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 bg-themed-tertiary rounded-full flex items-center justify-center mb-4 transition-colors duration-300 border border-themed-primary">
                                            <i class="fas fa-tags text-themed-secondary text-3xl"></i>
                                        </div>
                                        <h3 class="text-lg font-bold text-themed-primary mb-2 transition-colors duration-300">No categories found</h3>
                                        <p class="text-themed-secondary mb-6 transition-colors duration-300">
                                            {{ $search ? 'Try adjusting your search terms.' : 'Create your first category to get started.' }}
                                        </p>
                                        @if(!$search)
                                            <button wire:click="create" 
                                                    class="inline-flex items-center gap-2 bg-purple-500 hover:bg-purple-600 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105">
                                                <i class="fas fa-plus"></i>
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
            <div class="bg-themed-secondary rounded-2xl shadow-lg border border-themed-primary p-4 transition-colors duration-300">
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
                 class="bg-themed-secondary rounded-2xl shadow-2xl w-full max-w-md mx-2 p-6 border border-themed-primary transition-colors duration-300">
                
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-purple-100/50 rounded-full flex items-center justify-center mx-auto mb-4 transition-colors duration-300">
                        <i class="fas {{ $categoryId ? 'fa-edit' : 'fa-plus' }} text-purple-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-themed-primary mb-2 transition-colors duration-300">
                        {{ $categoryId ? 'Edit Category' : 'Create New Category' }}
                    </h3>
                    <p class="text-themed-secondary text-sm transition-colors duration-300">
                        {{ $categoryId ? 'Update the category details below.' : 'Add a new category to organize your courses.' }}
                    </p>
                </div>

                <form wire:submit="store" class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-bold text-themed-primary mb-2 transition-colors duration-300">
                            Category Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model="name" id="name"
                               class="w-full px-4 py-3 bg-themed-tertiary border-2 border-themed-primary rounded-xl text-themed-primary placeholder-themed-secondary focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all duration-300"
                               placeholder="e.g., Web Development, Data Science..."
                               x-ref="nameInput">
                        @error('name') 
                            <p class="mt-2 text-sm text-red-500 flex items-center gap-1">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="description" class="block text-sm font-bold text-themed-primary mb-2 transition-colors duration-300">
                            Description
                        </label>
                        <textarea wire:model="description" id="description" rows="3"
                                  class="w-full px-4 py-3 bg-themed-tertiary border-2 border-themed-primary rounded-xl text-themed-primary placeholder-themed-secondary focus:border-purple-500 focus:ring-2 focus:ring-purple-500/20 transition-all duration-300 resize-none"
                                  placeholder="Describe what this category covers..."
                                  maxlength="1000"></textarea>
                        <div class="flex justify-between items-center mt-2 text-xs text-themed-secondary transition-colors duration-300">
                            <span>Optional but recommended</span>
                            <span>{{ strlen($description) }}/1000</span>
                        </div>
                        @error('description') 
                            <p class="mt-2 text-sm text-red-500 flex items-center gap-1">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    
                    <div class="flex gap-3 pt-4">
                        <button type="button" wire:click="closeModal"
                                class="flex-1 bg-themed-tertiary hover:bg-themed-secondary text-themed-primary font-semibold py-3 px-6 rounded-xl transition-all duration-300 border border-themed-primary transform hover:scale-105">
                            Cancel
                        </button>
                        <button type="submit" wire:loading.attr="disabled"
                                class="flex-1 bg-purple-500 hover:bg-purple-600 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 disabled:opacity-50 transform hover:scale-105">
                            <span wire:loading.remove wire:target="store">{{ $categoryId ? 'Update' : 'Create' }}</span>
                            <span wire:loading wire:target="store" class="flex items-center justify-center gap-2">
                                <i class="fas fa-spinner fa-spin"></i>
                                {{ $categoryId ? 'Updating...' : 'Creating...' }}
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
                 class="bg-themed-secondary rounded-2xl shadow-2xl w-full max-w-md mx-2 p-6 border border-themed-primary transition-colors duration-300">
                
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-red-100/50 rounded-full flex items-center justify-center mx-auto mb-4 transition-colors duration-300">
                        <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-themed-primary mb-2 transition-colors duration-300">Confirm Deletion</h3>
                    <p class="text-themed-secondary text-sm transition-colors duration-300">
                        Are you sure you want to delete this category? This action cannot be undone.
                    </p>
                </div>

                <div class="flex gap-3">
                    <button wire:click="$set('showConfirmDelete', false)"
                            class="flex-1 bg-themed-tertiary hover:bg-themed-secondary text-themed-primary font-semibold py-3 px-6 rounded-xl transition-all duration-300 border border-themed-primary transform hover:scale-105">
                        Cancel
                    </button>
                    <button wire:click="delete"
                            class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-105">
                        Delete Category
                    </button>
                </div>
            </div>
        </div>

        <!-- Loading Overlay -->
        <div wire:loading class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-40">
            <div class="bg-themed-secondary rounded-2xl p-8 flex items-center shadow-2xl border border-themed-primary transition-colors duration-300">
                <div class="relative mr-4">
                    <div class="animate-spin rounded-full h-12 w-12 border-4 border-themed-tertiary"></div>
                    <div class="animate-spin rounded-full h-12 w-12 border-4 border-purple-500 border-t-transparent absolute top-0"></div>
                </div>
                <span class="text-themed-primary font-semibold transition-colors duration-300">Processing...</span>
            </div>
        </div>
    </div>
    
<style>
    [x-cloak] { display: none !important; }
</style>
</div>