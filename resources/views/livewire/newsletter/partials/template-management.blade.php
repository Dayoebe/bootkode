
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
        <div class="relative">
            <input 
                type="text" 
                wire:model.live.debounce.300ms="search" 
                placeholder="Search templates..."
                class="pl-10 pr-4 py-2 w-full md:w-64 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
        </div>

        <button 
            wire:click="$set('showCreateModal', true)"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors"
        >
            <i class="fas fa-plus mr-2"></i>Create Template
        </button>
    </div>

    <!-- Templates Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($templates as $template)
            <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden group">
                <!-- Template Preview -->
                <div class="h-48 bg-gray-100 relative overflow-hidden">
                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                        <i class="fas fa-newspaper text-4xl"></i>
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
                                class="bg-white text-gray-800 px-3 py-1 rounded text-sm hover:bg-gray-100"
                            >
                                <i class="fas fa-edit mr-1"></i>Edit
                            </button>
                            <button 
                                wire:click="duplicateTemplate({{ $template->id }})"
                                class="bg-white text-gray-800 px-3 py-1 rounded text-sm hover:bg-gray-100"
                            >
                                <i class="fas fa-copy mr-1"></i>Copy
                            </button>
                        </div>
                    </div>
                </div>

                <div class="p-4">
                    <h3 class="font-semibold text-gray-900 mb-1 truncate">{{ $template->name }}</h3>
                    <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $template->description ?: 'No description provided.' }}</p>
                    
                    <div class="flex justify-between items-center text-xs text-gray-500 mb-3">
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
                                    <i class="fas fa-star"></i>
                                </button>
                            @endif
                            <button 
                                wire:click="deleteTemplate({{ $template->id }})"
                                onclick="return confirm('Are you sure you want to delete this template?')"
                                class="text-red-600 hover:text-red-800 text-sm"
                                title="Delete"
                            >
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>

                        <span class="text-xs text-gray-500">
                            Template
                        </span>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <i class="fas fa-newspaper text-4xl text-gray-300 mb-4"></i>
                <p class="text-gray-500">No templates found.</p>
                <button 
                    wire:click="$set('showCreateModal', true)"
                    class="mt-4 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors"
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
    <div x-data="{ show: @entangle('showCreateModal') }" x-show="show" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="show = false"></div>
            <div class="relative bg-white rounded-lg w-full max-w-4xl max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <h3 class="text-lg font-medium mb-4">Create New Template</h3>
                    <form wire:submit.prevent="createTemplate">
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Template Name *</label>
                                    <input 
                                        type="text" 
                                        wire:model="name" 
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >
                                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="flex items-center">
                                        <input 
                                            type="checkbox" 
                                            wire:model="isDefault"
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-2"
                                        >
                                        <span class="text-sm font-medium text-gray-700">Set as Default Template</span>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea 
                                    wire:model="description" 
                                    rows="2"
                                    placeholder="Brief description of this template..."
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                ></textarea>
                            </div>

                            <!-- Template Variables -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Template Variables</label>
                                <div class="space-y-2">
                                    @foreach($variables as $index => $variable)
                                        <div class="flex space-x-2 items-end">
                                            <div class="flex-1">
                                                <input 
                                                    type="text" 
                                                    wire:model="variables.{{ $index }}.name"
                                                    placeholder="Variable name (e.g., company_name)"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                                >
                                            </div>
                                            <div class="flex-1">
                                                <input 
                                                    type="text" 
                                                    wire:model="variables.{{ $index }}.label"
                                                    placeholder="Display label"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                                >
                                            </div>
                                            <div>
                                                <select 
                                                    wire:model="variables.{{ $index }}.type"
                                                    class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm"
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
                                            >
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                                <button 
                                    type="button"
                                    wire:click="addVariable"
                                    class="mt-2 text-blue-600 hover:text-blue-800 text-sm"
                                >
                                    <i class="fas fa-plus mr-1"></i>Add Variable
                                </button>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">HTML Content *</label>
                                <textarea 
                                    wire:model="htmlContent" 
                                    rows="20"
                                    required
                                    placeholder="Enter your template HTML content here..."
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono text-sm"
                                ></textarea>
                                @error('htmlContent') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                <p class="text-xs text-gray-500 mt-1">
                                    Use variables like @{{variable_name}} and standard newsletter variables: @{{subscriber_email}}, @{{subscriber_first_name}}, etc.
                                </p>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button 
                                type="button" 
                                @click="show = false"
                                class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50"
                            >
                                Cancel
                            </button>
                            <button 
                                type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
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
    <div x-data="{ show: @entangle('showEditModal') }" x-show="show" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black bg-opacity-50" @click="show = false"></div>
            <div class="relative bg-white rounded-lg w-full max-w-4xl max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <h3 class="text-lg font-medium mb-4">Edit Template</h3>
                    <form wire:submit.prevent="updateTemplate">
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Template Name *</label>
                                    <input 
                                        type="text" 
                                        wire:model="name" 
                                        required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    >
                                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="flex items-center">
                                        <input 
                                            type="checkbox" 
                                            wire:model="isDefault"
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-2"
                                        >
                                        <span class="text-sm font-medium text-gray-700">Set as Default Template</span>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea 
                                    wire:model="description" 
                                    rows="2"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                ></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">HTML Content *</label>
                                <textarea 
                                    wire:model="htmlContent" 
                                    rows="20"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono text-sm"
                                ></textarea>
                                @error('htmlContent') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button 
                                type="button" 
                                @click="show = false"
                                class="px-4 py-2 text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50"
                            >
                                Cancel
                            </button>
                            <button 
                                type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
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