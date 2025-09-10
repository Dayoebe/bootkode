<!-- Pages List Tab -->
<div class="bg-white rounded-lg shadow-sm">
    <!-- Filters and Search -->
    <div class="p-6 border-b border-gray-200">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <!-- Search and Filters -->
            <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4">
                <div class="relative">
                    <input 
                        type="text" 
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search pages..." 
                        class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"
                    >
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
                
                <select wire:model.live="statusFilter" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                
                <select wire:model.live="templateFilter" class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach($templateOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                
                <button 
                    wire:click="clearFilters" 
                    class="px-4 py-2 text-gray-500 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors"
                >
                    <i class="fas fa-times mr-2"></i>Clear
                </button>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center space-x-2">
                <button 
                    wire:click="showCreateForm"
                    class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors flex items-center"
                >
                    <i class="fas fa-plus mr-2"></i>Create Page
                </button>
            </div>
        </div>

        <!-- Bulk Actions -->
        @if(count($selectedPages) > 0)
            <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-center justify-between">
                    <span class="text-blue-700">{{ count($selectedPages) }} pages selected</span>
                    <div class="flex items-center space-x-2">
                        <select wire:model="bulkAction" class="border border-blue-300 rounded px-3 py-1 text-sm">
                            @foreach($bulkActions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <button 
                            wire:click="executeBulkAction"
                            class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700"
                        >
                            Apply
                        </button>
                        <button 
                            wire:click="deselectAllPages"
                            class="text-blue-600 hover:text-blue-700 text-sm"
                        >
                            Clear
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Pages Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left">
                        <input type="checkbox" 
                               @if(count($selectedPages) === $pages->count() && $pages->count() > 0) checked @endif
                               wire:click="{{ count($selectedPages) === $pages->count() ? 'deselectAllPages' : 'selectAllPages' }}"
                               class="rounded border-gray-300">
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                        wire:click="sortBy('title')">
                        <div class="flex items-center">
                            Title
                            @if($sortBy === 'title')
                                <i class="ml-1 fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </div>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Template</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                        wire:click="sortBy('view_count')">
                        <div class="flex items-center">
                            Views
                            @if($sortBy === 'view_count')
                                <i class="ml-1 fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </div>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                        wire:click="sortBy('updated_at')">
                        <div class="flex items-center">
                            Updated
                            @if($sortBy === 'updated_at')
                                <i class="ml-1 fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </div>
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($pages as $page)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <input type="checkbox" 
                                   @if(in_array($page->id, $selectedPages)) checked @endif
                                   wire:click="togglePageSelection({{ $page->id }})"
                                   class="rounded border-gray-300">
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">
                                        <button wire:click="editPage({{ $page->id }})" class="hover:text-indigo-600">
                                            {{ $page->title }}
                                        </button>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <a href="{{ $page->getUrl() }}" target="_blank" class="hover:text-indigo-500">
                                            /{{ $page->slug }} <i class="fas fa-external-link-alt ml-1 text-xs"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $page->getStatusBadgeClass() }}">
                                {{ $page->getStatusLabel() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ ucfirst($page->template) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">
                            {{ number_format($page->view_count) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $page->updated_at->diffForHumans() }}
                            @if($page->updater)
                                <br><span class="text-xs">by {{ $page->updater->name }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-medium">
                            <div class="flex items-center justify-end space-x-2">
                                <div x-data="{ open: false }" class="relative">
                                    <button @click="open = !open" class="text-gray-400 hover:text-gray-600 p-1">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div x-show="open" @click.away="open = false" 
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="transform opacity-0 scale-95"
                                         x-transition:enter-end="transform opacity-100 scale-100"
                                         class="absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                        <div class="py-1">
                                            <button wire:click="editPage({{ $page->id }})" 
                                                   class="flex items-center w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-edit w-4 mr-3"></i>Edit
                                            </button>
                                            <a href="{{ $page->getUrl() }}" target="_blank"
                                               class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-eye w-4 mr-3"></i>View
                                            </a>
                                            <button wire:click="duplicatePage({{ $page->id }})"
                                                    class="flex items-center w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-copy w-4 mr-3"></i>Duplicate
                                            </button>
                                            <button wire:click="togglePageStatus({{ $page->id }})"
                                                    class="flex items-center w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                <i class="fas fa-toggle-{{ $page->isPublished() ? 'off' : 'on' }} w-4 mr-3"></i>
                                                {{ $page->isPublished() ? 'Unpublish' : 'Publish' }}
                                            </button>
                                            <div class="border-t border-gray-100"></div>
                                            <button wire:click="deletePage({{ $page->id }})" 
                                                    wire:confirm="Are you sure you want to delete this page?"
                                                    class="flex items-center w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50">
                                                <i class="fas fa-trash w-4 mr-3"></i>Delete
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-file-alt text-gray-300 text-6xl mb-4"></i>
                                <h3 class="text-lg font-medium mb-2">No pages found</h3>
                                <p class="text-sm mb-4">Get started by creating your first page.</p>
                                <button 
                                    wire:click="showCreateForm"
                                    class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors"
                                >
                                    Create Your First Page
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($pages->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $pages->links() }}
        </div>
    @endif
</div>