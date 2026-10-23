{{-- Enhanced Templates Manager Partial --}}
<div class="space-y-6">
    <!-- Templates Header -->
    <div class="bg-surface rounded-lg shadow-sm p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <div>
                <h2 class="text-xl font-semibold text-content">Template Manager</h2>
                <p class="text-content-muted mt-1">Manage and customize your page templates</p>
            </div>

            <div class="flex items-center space-x-4">
                <button wire:click="startNewTemplate"
                        class="bg-action text-action-foreground px-4 py-2 rounded-lg hover:bg-action transition-colors">
                    <i aria-hidden="true" class="fas fa-plus mr-2"></i>Create Template
                </button>

                <button wire:click="$set('showImportTemplate', true)"
                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    <i aria-hidden="true" class="fas fa-upload mr-2"></i>Import Template
                </button>
            </div>
        </div>
    </div>

    <!-- Built-in Templates -->
    <div class="bg-surface rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-line">
            <h3 class="text-lg font-semibold text-content">Built-in Templates</h3>
            <p class="text-sm text-content-muted mt-1">Pre-designed templates ready to use</p>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($availableTemplates as $template)
                    <div class="border border-line rounded-lg overflow-hidden  transition-shadow">
                        <div class="aspect-video bg-surface-muted   flex items-center justify-center">
                            <div class="text-center">
                                <i aria-hidden="true" class="fas fa-eye text-content-muted text-3xl mb-2"></i>
                                <p class="text-sm text-content-muted">Preview Available</p>
                            </div>
                        </div>

                        <div class="p-4">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-semibold text-content">{{ $template['name'] }}</h4>
                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-brand/10 text-brand">
                                    {{ $template['category'] }}
                                </span>
                            </div>

                            <p class="text-sm text-content-muted mb-4">{{ $template['description'] }}</p>

                            <!-- Features -->
                            <div class="flex flex-wrap gap-1 mb-4">
                                @foreach($template['features'] as $feature)
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded bg-surface-muted text-content">
                                        {{ $feature }}
                                    </span>
                                @endforeach
                            </div>

                            <!-- Usage Count -->
                            <div class="flex items-center justify-between text-sm text-content-muted mb-4">
                                <span>Used in {{ $template['usage_count'] }} page(s)</span>
                                <span class="flex items-center">
                                    <div class="w-2 h-2 rounded-full {{ $template['is_active'] ? 'bg-green-400' : 'bg-gray-400' }} mr-2"></div>
                                    {{ $template['is_active'] ? 'Active' : 'Inactive' }}
                                </span>
                            </div>

                            <div class="flex space-x-2">
                                <button wire:click="previewTemplate('{{ $template['id'] }}')"
                                        class="flex-1 text-sm px-3 py-2 bg-brand/10 text-brand rounded hover:bg-indigo-200 transition-colors">
                                    <i aria-hidden="true" class="fas fa-eye mr-1"></i>Preview
                                </button>
                                <button wire:click="duplicateTemplate('{{ $template['id'] }}')"
                                        class="flex-1 text-sm px-3 py-2 border border-line text-content rounded hover:bg-surface-muted transition-colors">
                                    <i aria-hidden="true" class="fas fa-copy mr-1"></i>Duplicate
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Custom Templates -->
    @if(count($customTemplates) > 0)
        <div class="bg-surface rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-line">
                <h3 class="text-lg font-semibold text-content">Custom Templates</h3>
                <p class="text-sm text-content-muted mt-1">Your custom-created templates</p>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($customTemplates as $template)
                        <div class="border border-line rounded-lg overflow-hidden  transition-shadow">
                            <div class="aspect-video bg-surface-muted   flex items-center justify-center">
                                <div class="text-center">
                                    <i aria-hidden="true" class="fas fa-code text-brand text-3xl mb-2"></i>
                                    <p class="text-sm text-brand">Custom Template</p>
                                </div>
                            </div>

                            <div class="p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-semibold text-content">{{ $template['name'] }}</h4>
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-brand/10 text-brand">
                                        Custom
                                    </span>
                                </div>

                                <p class="text-sm text-content-muted mb-4">{{ $template['description'] }}</p>

                                <!-- Features -->
                                <div class="flex flex-wrap gap-1 mb-4">
                                    @foreach($template['features'] as $feature)
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded bg-surface-muted text-content">
                                            {{ $feature }}
                                        </span>
                                    @endforeach
                                </div>

                                <!-- Usage Count -->
                                <div class="flex items-center justify-between text-sm text-content-muted mb-4">
                                    <span>Used in {{ $template['usage_count'] }} page(s)</span>
                                    <span class="text-xs text-content-muted">
                                        Created: {{ \Carbon\Carbon::parse($template['created_at'])->format('M j, Y') }}
                                    </span>
                                </div>

                                <div class="flex space-x-2">
                                    <button wire:click="editTemplate('{{ $template['id'] }}')"
                                            class="flex-1 text-sm px-3 py-2 bg-brand/10 text-brand rounded hover:bg-purple-200 transition-colors">
                                        <i aria-hidden="true" class="fas fa-edit mr-1"></i>Edit
                                    </button>
                                    <button wire:click="previewTemplate('{{ $template['id'] }}')"
                                            class="text-sm px-3 py-2 border border-line text-content rounded hover:bg-surface-muted transition-colors" aria-label="Preview template">
                                        <i aria-hidden="true" class="fas fa-eye"></i>
                                    </button>
                                    <div x-data="{ open: false }" class="relative">
                                        <button @click="open = !open"
                                                class="text-sm px-3 py-2 border border-line text-content rounded hover:bg-surface-muted transition-colors" aria-label="More actions">
                                            <i aria-hidden="true" class="fas fa-ellipsis-h"></i>
                                        </button>
                                        <div x-show="open" @click.away="open = false"
                                             class="absolute right-0 mt-2 w-48 rounded-md shadow-sm bg-surface ring-1 ring-black ring-opacity-5 z-50">
                                            <div class="py-1">
                                                <button wire:click="duplicateTemplate('{{ $template['id'] }}')"
                                                        class="flex items-center w-full text-left px-4 py-2 text-sm text-content hover:bg-surface-muted">
                                                    <i aria-hidden="true" class="fas fa-copy w-4 mr-3"></i>Duplicate
                                                </button>
                                                <button wire:click="exportTemplate('{{ $template['id'] }}')"
                                                        class="flex items-center w-full text-left px-4 py-2 text-sm text-content hover:bg-surface-muted">
                                                    <i aria-hidden="true" class="fas fa-download w-4 mr-3"></i>Export
                                                </button>
                                                <div class="border-t border-line"></div>
                                                <button wire:click="deleteTemplate('{{ $template['id'] }}')"
                                                        wire:confirm="Are you sure you want to delete this template?"
                                                        class="flex items-center w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50">
                                                    <i aria-hidden="true" class="fas fa-trash w-4 mr-3"></i>Delete
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Template Editor Modal -->
    @if($showTemplateEditor)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 overflow-y-auto" x-trap.inert.noscroll="true" role="dialog" aria-modal="true" aria-label="Template editor" tabindex="-1" x-data="{}" @keydown.escape.stop="$wire.set(&#x27;showTemplateEditor&#x27;, false)">
            <div class="bg-surface rounded-lg shadow-sm max-w-6xl w-full max-h-screen overflow-y-auto">
                <div class="p-6 border-b border-line">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-content">
                            {{ $editingTemplate ? 'Edit Template' : 'Create New Template' }}
                        </h3>
                        <button wire:click="$set('showTemplateEditor', false)"
                                class="text-content-muted hover:text-content-muted" aria-label="Close">
                            <i aria-hidden="true" class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <form wire:submit.prevent="saveTemplate">
                    <div class="p-6 space-y-6">
                        <!-- Template Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-content mb-1">Template Name</label>
                                <input aria-label="My Custom Template" type="text"
                                       wire:model="templateName"
                                       class="w-full border border-line rounded-lg px-3 py-2 focus:ring-brand/30 focus:border-brand"
                                       placeholder="My Custom Template">
                                @error('templateName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-content mb-1">Description</label>
                                <input aria-label="A brief description of this template" type="text"
                                       wire:model="templateDescription"
                                       class="w-full border border-line rounded-lg px-3 py-2 focus:ring-brand/30 focus:border-brand"
                                       placeholder="A brief description of this template">
                                @error('templateDescription') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Template Code Editor -->
                        <div class="space-y-4">
                            <!-- HTML -->
                            <div>
                                <label class="block text-sm font-medium text-content mb-2">Template HTML (Blade)</label>
                                <div class="border border-line rounded-lg">
                                    <textarea aria-label="Enter your Blade template code here" wire:model="templateHtml"
                                              rows="15"
                                              class="w-full rounded-lg px-3 py-2 font-mono text-sm focus:ring-brand/30 focus:border-brand resize-none"
                                              placeholder="Enter your Blade template code here..."></textarea>
                                </div>
                                @error('templateHtml') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- CSS -->
                            <div>
                                <label class="block text-sm font-medium text-content mb-2">Custom CSS</label>
                                <div class="border border-line rounded-lg">
                                    <textarea aria-label="/* Custom styles for this template */" wire:model="templateCss"
                                              rows="10"
                                              class="w-full rounded-lg px-3 py-2 font-mono text-sm focus:ring-brand/30 focus:border-brand resize-none"
                                              placeholder="/* Custom styles for this template */"></textarea>
                                </div>
                            </div>

                            <!-- JavaScript -->
                            <div>
                                <label class="block text-sm font-medium text-content mb-2">Custom JavaScript</label>
                                <div class="border border-line rounded-lg">
                                    <textarea aria-label="// Custom JavaScript for this template" wire:model="templateJs"
                                              rows="8"
                                              class="w-full rounded-lg px-3 py-2 font-mono text-sm focus:ring-brand/30 focus:border-brand resize-none"
                                              placeholder="// Custom JavaScript for this template"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-surface-muted border-t border-line flex items-center justify-between">
                        <button type="button"
                                wire:click="$set('showTemplateEditor', false)"
                                class="px-4 py-2 text-content bg-surface border border-line rounded-lg hover:bg-surface-muted">
                            Cancel
                        </button>
                        <div class="flex space-x-3">
                            @if($editingTemplate)
                                <button type="button"
                                        wire:click="previewTemplate('{{ $editingTemplate }}')"
                                        class="px-4 py-2 bg-action text-action-foreground rounded-lg hover:bg-action">
                                    Preview Changes
                                </button>
                            @endif
                            <button type="submit"
                                    class="px-4 py-2 bg-action text-action-foreground rounded-lg hover:bg-action">
                                {{ $editingTemplate ? 'Update Template' : 'Create Template' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Import Template Modal -->
    @if($showImportTemplate)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" x-trap.inert.noscroll="true" role="dialog" aria-modal="true" aria-label="Import Template" tabindex="-1" x-data="{}" @keydown.escape.stop="$wire.set(&#x27;showImportTemplate&#x27;, false)">
            <div class="bg-surface rounded-lg shadow-sm max-w-lg w-full">
                <div class="p-6 border-b border-line">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-content">Import Template</h3>
                        <button wire:click="$set('showImportTemplate', false)"
                                class="text-content-muted hover:text-content-muted" aria-label="Close">
                            <i aria-hidden="true" class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <form wire:submit.prevent="importTemplate">
                    <div class="p-6">
                        <div class="border-2 border-dashed border-line rounded-lg p-6 text-center">
                            <input type="file"
                                   wire:model="importFile"
                                   accept=".zip"
                                   class="hidden"
                                   id="template_import">

                            <label for="template_import" class="cursor-pointer">
                                <i aria-hidden="true" class="fas fa-upload text-content-muted text-4xl mb-4"></i>
                                <p class="text-lg text-content-muted mb-2">Upload Template Package</p>
                                <p class="text-sm text-content-muted">ZIP files only, max 10MB</p>
                            </label>

                            @if($importFile)
                                <div class="mt-4 text-sm text-green-600">
                                    <i aria-hidden="true" class="fas fa-check-circle mr-2"></i>
                                    File selected: {{ $importFile->getClientOriginalName() }}
                                </div>
                            @endif
                        </div>

                        @error('importFile')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="px-6 py-4 bg-surface-muted border-t border-line flex items-center justify-between">
                        <button type="button"
                                wire:click="$set('showImportTemplate', false)"
                                class="px-4 py-2 text-content bg-surface border border-line rounded-lg hover:bg-surface-muted">
                            Cancel
                        </button>
                        <button type="submit"
                                @if(!$importFile) disabled @endif
                                class="px-4 py-2 bg-action text-action-foreground rounded-lg hover:bg-action disabled:opacity-50 disabled:cursor-not-allowed">
                            Import Template
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Template Preview Modal -->
    @if($selectedTemplate)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-surface rounded-lg shadow-sm max-w-4xl w-full max-h-screen overflow-y-auto">
                <div class="p-6 border-b border-line">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-content">{{ $selectedTemplate['name'] }} Preview</h3>
                        <button wire:click="$set('selectedTemplate', null)"
                                class="text-content-muted hover:text-content-muted" aria-label="Close">
                            <i aria-hidden="true" class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <div class="bg-surface-muted rounded-lg p-4 mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h4 class="font-medium text-content">{{ $selectedTemplate['name'] }}</h4>
                                <p class="text-sm text-content-muted">{{ $selectedTemplate['description'] }}</p>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-brand/10 text-brand">
                                {{ $selectedTemplate['category'] }}
                            </span>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            @foreach($selectedTemplate['features'] as $feature)
                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded bg-surface text-content">
                                    {{ $feature }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <!-- Preview iframe would go here -->
                    <div class="bg-surface border-2 border-line rounded-lg" style="height: 500px;">
                        <div class="flex items-center justify-center h-full">
                            <div class="text-center">
                                <i aria-hidden="true" class="fas fa-browser text-gray-300 text-6xl mb-4"></i>
                                <p class="text-content-muted text-lg mb-2">Template Preview</p>
                                <p class="text-content-muted text-sm">Live preview would be shown here</p>
                                @if($templatePreview)
                                    <a href="{{ $templatePreview }}" target="_blank"
                                       class="inline-flex items-center mt-4 text-brand hover:text-brand">
                                        <i aria-hidden="true" class="fas fa-external-link-alt mr-2"></i>
                                        Open in New Tab
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-surface-muted border-t border-line flex items-center justify-between">
                    <div class="text-sm text-content-muted">
                        Used in {{ $selectedTemplate['usage_count'] }} page(s)
                    </div>
                    <div class="flex space-x-3">
                        @if(!$selectedTemplate['is_custom'])
                            <button wire:click="duplicateTemplate('{{ $selectedTemplate['id'] }}')"
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                <i aria-hidden="true" class="fas fa-copy mr-2"></i>Duplicate
                            </button>
                        @else
                            <button wire:click="editTemplate('{{ $selectedTemplate['id'] }}')"
                                    class="px-4 py-2 bg-action text-action-foreground rounded-lg hover:bg-action">
                                <i aria-hidden="true" class="fas fa-edit mr-2"></i>Edit
                            </button>
                        @endif
                        <button wire:click="$set('selectedTemplate', null)"
                                class="px-4 py-2 text-content bg-surface border border-line rounded-lg hover:bg-surface-muted">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
