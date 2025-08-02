<div>
    <div class="container mx-auto px-4 py-6 sm:px-6 lg:px-8">
        <!-- Header with Create Button -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Course Categories</h1>
            <button wire:click="create"
                class="w-full sm:w-auto bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded transition-colors">
                Create New Category
            </button>
        </div>

        <!-- Search and Filter -->
        <div class="mb-6">
            <input type="text" wire:model.debounce.300ms="search" placeholder="Search categories..."
                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
        </div>

        <!-- Flash Messages -->
        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('message') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <!-- Categories Table - Responsive Design -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Name</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">
                                Description</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">
                                Slug</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($categories as $category)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <div class="flex items-center">
                                        {{ $category->name }}
                                    </div>
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-500 hidden md:table-cell">
                                    {{ Str::limit($category->description, 50) }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell">
                                    {{ $category->slug }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button wire:click="edit({{ $category->id }})"
                                            class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                            <i class="fas fa-edit"></i>

                                        </button>
                                        <button wire:click="delete({{ $category->id }})"
                                            class="text-red-600 hover:text-red-900" title="Delete"
                                            onclick="return confirm('Are you sure?')">

                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">No categories
                                    found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $categories->links() }}
        </div>

        <!-- Create/Edit Modal -->
        @if ($isModalOpen)
            <div class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center p-4 z-50">
                <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-2">
                    <div class="p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">
                            {{ $categoryId ? 'Edit Category' : 'Create New Category' }}
                        </h2>

                        <form wire:submit.prevent="store">
                            <div class="mb-4">
                                <label for="name" class="block text-sm font-medium text-gray-700">Name *</label>
                                <input type="text" wire:model="name" id="name"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                @error('name')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="description"
                                    class="block text-sm font-medium text-gray-700">Description</label>
                                <textarea wire:model="description" id="description" rows="3"
                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500"></textarea>
                                @error('description')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                                <button type="button" wire:click="closeModal"
                                    class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    Cancel
                                </button>
                                <button type="submit"
                                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    {{ $categoryId ? 'Update' : 'Create' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
