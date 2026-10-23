
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
        <div class="relative">
            <input
 aria-label="Search templates"                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Search templates..."
                class="pl-10 pr-4 py-2 w-full md:w-64 border border-line rounded-lg focus:ring-2 focus:ring-brand/30 focus:border-transparent"
            >
            <i class="fas fa-search absolute left-3 top-3 text-content-muted" aria-hidden="true"></i>
        </div>

        <button
            wire:click="$set('showCreateModal', true)"
            class="bg-action hover:bg-action text-action-foreground px-4 py-2 rounded-lg font-medium transition-colors"
        >
            <i class="fas fa-plus mr-2" aria-hidden="true"></i>Create Template
        </button>
    </div>

    <!-- Templates Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($templates as $template)
            <div class="bg-surface rounded-lg shadow border border-line overflow-hidden group">
                <!-- Template Preview -->
                <div class="h-48 bg-surface-muted relative overflow-hidden">
                    <div class="w-full h-full flex items-center justify-center text-content-muted">
                        <i class="fas fa-newspaper text-4xl" aria-hidden="true"></i>
                    </div>

                    @if($template->is_default)
                        <div class="absolute top-2 right-2 bg-yellow-500 text-white px-2 py-1 text-xs rounded">
                            Default
                        </div>
                    @endif

                    <!-- Hover overlay -->
                    <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center">
                        <div class="flex space-x-2">
                            <button
                                wire:click="editTemplate({{ $template->id }})"
                                class="bg-surface text-content px-3 py-1 rounded text-sm hover:bg-surface-muted"
                            >
                                <i class="fas fa-edit mr-1" aria-hidden="true"></i>Edit
                            </button>
                            <button
                                wire:click="duplicateTemplate({{ $template->id }})"
                                class="bg-surface text-content px-3 py-1 rounded text-sm hover:bg-surface-muted"
                            >
                                <i class="fas fa-copy mr-1" aria-hidden="true"></i>Copy
                            </button>
                        </div>
                    </div>
                </div>

                <div class="p-4">
                    <h3 class="font-semibold text-content mb-1 truncate">{{ $template->name }}</h3>
                    <p class="text-sm text-content-muted mb-3 line-clamp-2">{{ $template->description ?: 'No description provided.' }}</p>

                    <div class="flex justify-between items-center text-xs text-content-muted mb-3">
                        <span>By {{ $template->creator->name }}</span>
                        <span>{{ $template->created_at->format('M j, Y') }}</span>
                    </div>

                    <div class="flex justify-between items-center">
                        <div class="flex space-x-1">
                            @if(!$template->is_default)
                                <button
                                    wire:click="setAsDefault({{ $template->id }})"
                                    class="text-yellow-600 hover:text-yellow-800 text-sm"
                                    title="Set as Default"
                                >
                                    <i class="fas fa-star" aria-hidden="true"></i>
                                </button>
                            @endif
                            <button
                                wire:click="deleteTemplate({{ $template->id }})"
                                onclick="return confirm('Are you sure you want to delete this template?')"
                                class="text-red-600 hover:text-red-800 text-sm"
                                title="Delete"
                            >
                                <i class="fas fa-trash" aria-hidden="true"></i>
                            </button>
                        </div>

                        <span class="text-xs text-content-muted">
                            Template
                        </span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <i class="fas fa-newspaper text-4xl text-gray-300 mb-4" aria-hidden="true"></i>
                <p class="text-content-muted">No templates found.</p>
                <button
                    wire:click="$set('showCreateModal', true)"
                    class="mt-4 bg-action hover:bg-action text-action-foreground px-4 py-2 rounded-lg font-medium transition-colors"
                >
                    Create Your First Template
                </button>
            </div>
        @endforelse
    </div>

    @if($templates->hasPages())
        <div class="mt-6">
            {{ $templates->links() }}
        </div>
    @endif

    <!-- Create Template Modal -->
    <div x-data="{ show: @entangle('showCreateModal') }" x-show="show" class="fixed inset-0 z-50 overflow-y-auto" x-cloak x-trap.inert.noscroll="show" role="dialog" aria-modal="true" aria-label="Create New Template" tabindex="-1" @keydown.escape.stop="show = false">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="show = false"></div>
            <div class="relative bg-surface rounded-lg w-full max-w-4xl max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <h3 class="text-lg font-medium mb-4">Create New Template</h3>
                    <form wire:submit.prevent="createTemplate">
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-content mb-1">Template Name *</label>
                                    <input
 aria-label="Name"                                        type="text"
                                        wire:model="name"
                                        required
                                        class="w-full px-3 py-2 border border-line rounded-lg focus:ring-2 focus:ring-brand/30 focus:border-transparent"
                                    >
                                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="flex items-center">
                                        <input
                                            type="checkbox"
                                            wire:model="isDefault"
                                            class="rounded border-line text-brand focus:ring-brand/30 mr-2"
                                        >
                                        <span class="text-sm font-medium text-content">Set as Default Template</span>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-content mb-1">Description</label>
                                <textarea
 aria-label="Brief description of this template"                                    wire:model="description"
                                    rows="2"
                                    placeholder="Brief description of this template..."
                                    class="w-full px-3 py-2 border border-line rounded-lg focus:ring-2 focus:ring-brand/30 focus:border-transparent"
                                ></textarea>
                            </div>

                            <!-- Template Variables -->
                            <div>
                                <label class="block text-sm font-medium text-content mb-2">Template Variables</label>
                                <div class="space-y-2">
                                    @foreach($variables as $index => $variable)
                                        <div class="flex space-x-2 items-end">
                                            <div class="flex-1">
                                                <input
 aria-label="Variable name (e.g., company_name)"                                                    type="text"
                                                    wire:model="variables.{{ $index }}.name"
                                                    placeholder="Variable name (e.g., company_name)"
                                                    class="w-full px-3 py-2 border border-line rounded-lg focus:ring-2 focus:ring-brand/30 focus:border-transparent text-sm"
                                                >
                                            </div>
                                            <div class="flex-1">
                                                <input
 aria-label="Display label"                                                    type="text"
                                                    wire:model="variables.{{ $index }}.label"
                                                    placeholder="Display label"
                                                    class="w-full px-3 py-2 border border-line rounded-lg focus:ring-2 focus:ring-brand/30 focus:border-transparent text-sm"
                                                >
                                            </div>
                                            <div>
                                                <select
                                                    wire:model="variables.{{ $index }}.type"
                                                    class="px-3 py-2 border border-line rounded-lg focus:ring-2 focus:ring-brand/30 text-sm"
                                                >
                                                    <option value="text">Text</option>
                                                    <option value="textarea">Textarea</option>
                                                    <option value="url">URL</option>
                                                    <option value="color">Color</option>
                                                </select>
                                            </div>
                                            <button
                                                type="button"
                                                wire:click="removeVariable({{ $index }})"
                                                class="px-3 py-2 text-red-600 hover:text-red-800"
                                             aria-label="Remove variable">
                                                <i class="fas fa-trash" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                                <button
                                    type="button"
                                    wire:click="addVariable"
                                    class="mt-2 text-brand hover:text-brand text-sm"
                                >
                                    <i class="fas fa-plus mr-1" aria-hidden="true"></i>Add Variable
                                </button>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-content mb-1">HTML Content *</label>
                                <textarea
 aria-label="Enter your template HTML content here"                                    wire:model="htmlContent"
                                    rows="20"
                                    required
                                    placeholder="Enter your template HTML content here..."
                                    class="w-full px-3 py-2 border border-line rounded-lg focus:ring-2 focus:ring-brand/30 focus:border-transparent font-mono text-sm"
                                ></textarea>
                                @error('htmlContent') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                <p class="text-xs text-content-muted mt-1">
                                    Use variables like @{{variable_name}} and standard newsletter variables: @{{subscriber_email}}, @{{subscriber_first_name}}, etc.
                                </p>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button
                                type="button"
                                @click="show = false"
                                class="px-4 py-2 text-content border border-line rounded-lg hover:bg-surface-muted"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="px-4 py-2 bg-action text-action-foreground rounded-lg hover:bg-action"
                            >
                                Create Template
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Template Modal -->
    <div x-data="{ show: @entangle('showEditModal') }" x-show="show" class="fixed inset-0 z-50 overflow-y-auto" x-cloak x-trap.inert.noscroll="show" role="dialog" aria-modal="true" aria-label="Edit Template" tabindex="-1" @keydown.escape.stop="show = false">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="show = false"></div>
            <div class="relative bg-surface rounded-lg w-full max-w-4xl max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <h3 class="text-lg font-medium mb-4">Edit Template</h3>
                    <form wire:submit.prevent="updateTemplate">
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-content mb-1">Template Name *</label>
                                    <input
 aria-label="Name"                                        type="text"
                                        wire:model="name"
                                        required
                                        class="w-full px-3 py-2 border border-line rounded-lg focus:ring-2 focus:ring-brand/30 focus:border-transparent"
                                    >
                                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="flex items-center">
                                        <input
                                            type="checkbox"
                                            wire:model="isDefault"
                                            class="rounded border-line text-brand focus:ring-brand/30 mr-2"
                                        >
                                        <span class="text-sm font-medium text-content">Set as Default Template</span>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-content mb-1">Description</label>
                                <textarea
 aria-label="Description"                                    wire:model="description"
                                    rows="2"
                                    class="w-full px-3 py-2 border border-line rounded-lg focus:ring-2 focus:ring-brand/30 focus:border-transparent"
                                ></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-content mb-1">HTML Content *</label>
                                <textarea
 aria-label="Html content"                                    wire:model="htmlContent"
                                    rows="20"
                                    required
                                    class="w-full px-3 py-2 border border-line rounded-lg focus:ring-2 focus:ring-brand/30 focus:border-transparent font-mono text-sm"
                                ></textarea>
                                @error('htmlContent') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button
                                type="button"
                                @click="show = false"
                                class="px-4 py-2 text-content border border-line rounded-lg hover:bg-surface-muted"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="px-4 py-2 bg-action text-action-foreground rounded-lg hover:bg-action"
                            >
                                Update Template
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
