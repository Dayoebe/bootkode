{{-- Pages List Partial --}}
<div class="bg-surface rounded-lg shadow-sm">
    <!-- Filters and Search -->
    <div class="p-6 border-b border-line">
        <div class="flex flex-col space-y-4">
            <!-- Top Row: Search and Main Actions -->
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                <div class="relative flex-1 max-w-md">
                    <input aria-label="Search pages" type="text"
                           wire:model.live.debounce.300ms="search"
                           placeholder="Search pages..."
                           class="w-full pl-10 pr-4 py-2 border border-line rounded-lg focus:ring-brand/30 focus:border-brand">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-content-muted" aria-hidden="true"></i>
                    </div>
                    @if($search)
                        <button wire:click="$set('search', '')"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center" aria-label="Close">
                            <i class="fas fa-times text-content-muted hover:text-content-muted" aria-hidden="true"></i>
                        </button>
                    @endif
                </div>

                <div class="flex items-center space-x-3">
                    <a href="{{ route('pages.create') }}"
                       class="bg-action text-action-foreground px-4 py-2 rounded-lg hover:bg-action transition-colors flex items-center">
                        <i class="fas fa-plus mr-2" aria-hidden="true"></i>Create Page
                    </a>

                    <button wire:click="clearFilters"
                            class="px-4 py-2 text-content-muted border border-line rounded-lg hover:bg-surface-muted transition-colors">
                        <i class="fas fa-filter-circle-xmark mr-2" aria-hidden="true"></i>Clear Filters
                    </button>
                </div>
            </div>

            <!-- Filters Row -->
            <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                <select aria-label="Status filter" wire:model.live="statusFilter"
                        class="border border-line rounded-lg px-3 py-2 text-sm focus:ring-brand/30 focus:border-brand">
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>

                <select aria-label="Template filter" wire:model.live="templateFilter"
                        class="border border-line rounded-lg px-3 py-2 text-sm focus:ring-brand/30 focus:border-brand">
                    @foreach($templateOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>

                <select aria-label="Author filter" wire:model.live="authorFilter"
                        class="border border-line rounded-lg px-3 py-2 text-sm focus:ring-brand/30 focus:border-brand">
                    <option value="">All Authors</option>
                    @foreach($authors as $author)
                        <option value="{{ $author->id }}">{{ $author->name }}</option>
                    @endforeach
                </select>

                <select aria-label="Date filter" wire:model.live="dateFilter"
                        class="border border-line rounded-lg px-3 py-2 text-sm focus:ring-brand/30 focus:border-brand">
                    @foreach($dateFilters as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>

                <div class="text-sm text-content-muted flex items-center">
                    <i class="fas fa-list-ul mr-2" aria-hidden="true"></i>
                    {{ $pages->total() }} page(s) found
                </div>
            </div>
        </div>

        <!-- Bulk Actions -->
        @if($showBulkActions)
            <div class="mt-4 p-4 bg-brand/10 border border-brand rounded-lg">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-2 sm:space-y-0">
                    <span class="text-brand font-medium">
                        <i class="fas fa-check-square mr-2" aria-hidden="true"></i>
                        {{ count($selectedPages) }} page(s) selected
                    </span>
                    <div class="flex items-center space-x-3">
                        <select aria-label="Bulk action" wire:model="bulkAction"
                                class="border border-brand rounded px-3 py-2 text-sm focus:ring-brand/30 focus:border-brand">
                            @foreach($bulkActions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <button wire:click="executeBulkAction"
                                class="bg-action text-action-foreground px-4 py-2 rounded-lg text-sm hover:bg-action transition-colors">
                            Apply
                        </button>
                        <button wire:click="deselectAllPages"
                                class="text-brand hover:text-brand text-sm font-medium">
                            Clear Selection
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Pages Table -->
    <div class="overflow-x-auto">
        <x-ui.table label="Pages List records"><table class="min-w-full divide-y divide-line">
            <thead class="bg-surface-muted">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left">
                        <input type="checkbox"
                               @if(count($selectedPages) === $pages->count() && $pages->count() > 0) checked @endif
                               wire:click="{{ count($selectedPages) === $pages->count() ? 'deselectAllPages' : 'selectAllPages' }}"
                               class="rounded border-line text-brand focus:ring-brand/30">
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider cursor-pointer hover:bg-surface-muted"
                        wire:click="sortBy('title')">
                        <div class="flex items-center">
                            Title
                            @if($sortBy === 'title')
                                <i class="ml-1 fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-brand" aria-hidden="true"></i>
                            @else
                                <i class="ml-1 fas fa-sort text-gray-300" aria-hidden="true"></i>
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">
                        Status
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">
                        Template
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider">
                        Author
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider cursor-pointer hover:bg-surface-muted"
                        wire:click="sortBy('view_count')">
                        <div class="flex items-center">
                            Views
                            @if($sortBy === 'view_count')
                                <i class="ml-1 fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-brand" aria-hidden="true"></i>
                            @else
                                <i class="ml-1 fas fa-sort text-gray-300" aria-hidden="true"></i>
                            @endif
                        </div>
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-content-muted uppercase tracking-wider cursor-pointer hover:bg-surface-muted"
                        wire:click="sortBy('updated_at')">
                        <div class="flex items-center">
                            Updated
                            @if($sortBy === 'updated_at')
                                <i class="ml-1 fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} text-brand" aria-hidden="true"></i>
                            @else
                                <i class="ml-1 fas fa-sort text-gray-300" aria-hidden="true"></i>
                            @endif
                        </div>
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-content-muted uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="bg-surface divide-y divide-line">
                @forelse($pages as $page)
                    <tr class="hover:bg-surface-muted transition-colors">
                        <td class="px-6 py-4">
                            <input type="checkbox"
                                   @if(in_array($page->id, $selectedPages)) checked @endif
                                   wire:click="togglePageSelection({{ $page->id }})"
                                   class="rounded border-line text-brand focus:ring-brand/30">
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-start space-x-3">
                                <!-- Featured Image Thumbnail -->
                                @if($page->featuredMedia && $page->featuredMedia->count())
                                    <div class="flex-shrink-0">
                                        <img class="h-12 w-12 rounded-lg object-cover border border-line"
                                             src="{{ $page->featuredMedia->first()->getThumbnailUrl('small') ?: $page->featuredMedia->first()->getUrl() }}"
                                             alt="{{ $page->title }}">
                                    </div>
                                @else
                                    <div class="flex-shrink-0 h-12 w-12 bg-surface-muted rounded-lg flex items-center justify-center border border-line">
                                        <i class="fas fa-file-alt text-content-muted" aria-hidden="true"></i>
                                    </div>
                                @endif

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-2">
                                        <button wire:click="editPage({{ $page->id }})"
                                                class="text-sm font-medium text-content hover:text-brand truncate max-w-xs">
                                            {{ $page->title }}
                                        </button>

                                        @if($page->isScheduled())
                                            <i class="fas fa-clock text-brand text-xs" title="Scheduled"></i>
                                        @elseif($page->isExpired())
                                            <i class="fas fa-exclamation-triangle text-red-500 text-xs" title="Expired"></i>
                                        @endif
                                    </div>

                                    <div class="flex items-center space-x-2 mt-1">
                                        <a href="{{ $page->getUrl() }}"
                                           target="_blank"
                                           class="text-sm text-content-muted hover:text-brand truncate max-w-xs">
                                            /{{ $page->slug }}
                                        </a>
                                        <i class="fas fa-external-link-alt text-xs text-gray-300" aria-hidden="true"></i>
                                    </div>

                                    @if($page->excerpt)
                                        <p class="text-sm text-content-muted mt-1 line-clamp-2">
                                            {{ Str::limit($page->excerpt, 100) }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col space-y-1">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $page->getStatusBadgeClass() }}">
                                    {{ $page->getStatusLabel() }}
                                </span>

                                @if($page->scheduled_at && $page->isScheduled())
                                    <span class="text-xs text-content-muted">
                                        Scheduled: {{ $page->scheduled_at->format('M j, g:i A') }}
                                    </span>
                                @elseif($page->published_at && $page->isPublished())
                                    <span class="text-xs text-content-muted">
                                        Published: {{ $page->published_at->format('M j, g:i A') }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-surface-muted text-content">
                                {{ ucfirst(str_replace('-', ' ', $page->template)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-content">
                            <div class="flex items-center">
                                @if($page->creator)
                                    @if($page->creator->avatar)
                                        <img class="h-6 w-6 rounded-full mr-2"
                                             src="{{ $page->creator->avatar }}"
                                             alt="{{ $page->creator->name }}">
                                    @else
                                        <div class="h-6 w-6 rounded-full bg-gray-300 flex items-center justify-center mr-2">
                                            <span class="text-xs font-medium text-content-muted">
                                                {{ substr($page->creator->name, 0, 1) }}
                                            </span>
                                        </div>
                                    @endif
                                    <span class="truncate max-w-24">{{ $page->creator->name }}</span>
                                @else
                                    <span class="text-content-muted">Unknown</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-eye text-content-muted text-xs" aria-hidden="true"></i>
                                <span class="text-sm font-medium text-content">
                                    {{ number_format($page->view_count) }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-content-muted">
                            <div class="flex flex-col">
                                <span>{{ $page->updated_at->format('M j, Y') }}</span>
                                <span class="text-xs">{{ $page->updated_at->format('g:i A') }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-medium">
                            <div x-data="{ open: false }" class="relative inline-block text-left">
                                <button @click="open = !open"
                                        class="text-content-muted hover:text-content-muted p-1 rounded-full hover:bg-surface-muted" aria-label="More actions">
                                    <i class="fas fa-ellipsis-v" aria-hidden="true"></i>
                                </button>

                                <div x-show="open"
                                     @click.away="open = false"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     class="absolute right-0 mt-2 w-56 rounded-md shadow-sm bg-surface ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
                                    <div class="py-1" role="menu">
                                        <button wire:click="editPage({{ $page->id }})"
                                                class="flex items-center w-full text-left px-4 py-2 text-sm text-content hover:bg-surface-muted"
                                                role="menuitem">
                                            <i class="fas fa-edit w-4 mr-3 text-brand" aria-hidden="true"></i>
                                            Edit Page
                                        </button>

                                        <a href="{{ $page->getUrl() }}"
                                           target="_blank"
                                           class="flex items-center px-4 py-2 text-sm text-content hover:bg-surface-muted"
                                           role="menuitem">
                                            <i class="fas fa-eye w-4 mr-3 text-brand" aria-hidden="true"></i>
                                            View Page
                                        </a>

                                        <button wire:click="openQuickEdit({{ $page->id }})"
                                                class="flex items-center w-full text-left px-4 py-2 text-sm text-content hover:bg-surface-muted"
                                                role="menuitem">
                                            <i class="fas fa-bolt w-4 mr-3 text-yellow-500" aria-hidden="true"></i>
                                            Quick Edit
                                        </button>

                                        <button wire:click="duplicatePage({{ $page->id }})"
                                                class="flex items-center w-full text-left px-4 py-2 text-sm text-content hover:bg-surface-muted"
                                                role="menuitem">
                                            <i class="fas fa-copy w-4 mr-3 text-green-500" aria-hidden="true"></i>
                                            Duplicate
                                        </button>

                                        <button wire:click="togglePageStatus({{ $page->id }})"
                                                class="flex items-center w-full text-left px-4 py-2 text-sm text-content hover:bg-surface-muted"
                                                role="menuitem">
                                            <i class="fas fa-toggle-{{ $page->isPublished() ? 'off' : 'on' }} w-4 mr-3 text-brand" aria-hidden="true"></i>
                                            {{ $page->isPublished() ? 'Unpublish' : 'Publish' }}
                                        </button>

                                        <div class="border-t border-line my-1"></div>

                                        <button wire:click="deletePage({{ $page->id }})"
                                                wire:confirm="Are you sure you want to delete '{{ $page->title }}'? This action cannot be undone."
                                                class="flex items-center w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50"
                                                role="menuitem">
                                            <i class="fas fa-trash w-4 mr-3 text-red-500" aria-hidden="true"></i>
                                            Delete Page
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-file-alt text-gray-300 text-6xl mb-6" aria-hidden="true"></i>

                                @if($search || $statusFilter || $templateFilter || $authorFilter || $dateFilter)
                                    <h3 class="text-lg font-medium text-content mb-2">No pages match your filters</h3>
                                    <p class="text-content-muted mb-6">Try adjusting your search criteria or clearing filters</p>
                                    <button wire:click="clearFilters"
                                            class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                                        Clear All Filters
                                    </button>
                                @else
                                    <h3 class="text-lg font-medium text-content mb-2">No pages found</h3>
                                    <p class="text-content-muted mb-6">Get started by creating your first page</p>
                                    <a href="{{ route('pages.create') }}"
                                       class="bg-action text-action-foreground px-6 py-3 rounded-lg hover:bg-action transition-colors">
                                        <i class="fas fa-plus mr-2" aria-hidden="true"></i>Create Your First Page
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table></x-ui.table>
    </div>

    <!-- Pagination -->
    @if($pages->hasPages())
        <div class="px-6 py-4 border-t border-line bg-surface-muted">
            {{ $pages->links() }}
        </div>
    @endif

    <!-- Quick Edit Modal -->
    @if($showQuickEdit && $quickEditPage)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" x-trap.inert.noscroll="true" role="dialog" aria-modal="true" aria-label="Quick edit" tabindex="-1" x-data="{}" @keydown.escape.stop="$wire.set(&#x27;showQuickEdit&#x27;, false)">
            <div class="bg-surface rounded-lg shadow-sm max-w-2xl w-full max-h-screen overflow-y-auto">
                <div class="p-6 border-b border-line">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-content">Quick Edit: {{ $quickEditPage->title }}</h3>
                        <button wire:click="closeQuickEdit" class="text-content-muted hover:text-content-muted" aria-label="Close">
                            <i class="fas fa-times" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>

                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-content mb-1">Title</label>
                        <input aria-label="Title" type="text"
                               wire:model="quickEditPage.title"
                               class="w-full border border-line rounded-lg px-3 py-2 focus:ring-brand/30 focus:border-brand">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-content mb-1">Status</label>
                            <select aria-label="Status" wire:model="quickEditPage.status"
                                    class="w-full border border-line rounded-lg px-3 py-2 focus:ring-brand/30 focus:border-brand">
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-content mb-1">Template</label>
                            <select aria-label="Template" wire:model="quickEditPage.template"
                                    class="w-full border border-line rounded-lg px-3 py-2 focus:ring-brand/30 focus:border-brand">
                                <option value="default">Default Template</option>
                                <option value="landing">Landing Page</option>
                                <option value="blog">Blog Style</option>
                                <option value="full-width">Full Width</option>
                                <option value="minimal">Minimal</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-content mb-1">Excerpt</label>
                        <textarea aria-label="Brief description of the page" wire:model="quickEditPage.excerpt"
                                  rows="3"
                                  class="w-full border border-line rounded-lg px-3 py-2 focus:ring-brand/30 focus:border-brand"
                                  placeholder="Brief description of the page..."></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-content mb-1">Meta Title</label>
                            <input aria-label="SEO title" type="text"
                                   wire:model="quickEditPage.meta_title"
                                   maxlength="60"
                                   class="w-full border border-line rounded-lg px-3 py-2 focus:ring-brand/30 focus:border-brand"
                                   placeholder="SEO title...">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-content mb-1">Meta Description</label>
                            <input aria-label="SEO description" type="text"
                                   wire:model="quickEditPage.meta_description"
                                   maxlength="160"
                                   class="w-full border border-line rounded-lg px-3 py-2 focus:ring-brand/30 focus:border-brand"
                                   placeholder="SEO description...">
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-surface-muted border-t border-line flex items-center justify-between">
                    <button wire:click="closeQuickEdit"
                            class="px-4 py-2 text-content bg-surface border border-line rounded-lg hover:bg-surface-muted">
                        Cancel
                    </button>
                    <button wire:click="updateQuickEdit"
                            class="px-4 py-2 bg-action text-action-foreground rounded-lg hover:bg-action">
                        Update Page
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
