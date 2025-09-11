{{-- Enhanced Templates Manager Partial --}}
<div class="space-y-6">
    <!-- Templates Header -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Template Manager</h2>
                <p class="text-gray-600 mt-1">Manage and customize your page templates</p>
            </div>
            
            <div class="flex items-center space-x-4">
                <button wire:click="startNewTemplate"
                        class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Create Template
                </button>
                
                <button wire:click="$set('showImportTemplate', true)"
                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-upload mr-2"></i>Import Template
                </button>
            </div>
        </div>
    </div>

    <!-- Built-in Templates -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Built-in Templates</h3>
            <p class="text-sm text-gray-600 mt-1">Pre-designed templates ready to use</p>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($availableTemplates as $template)
                    <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-shadow">
                        <div class="aspect-video bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                            <div class="text-center">
                                <i class="fas fa-eye text-gray-400 text-3xl mb-2"></i>
                                <p class="text-sm text-gray-500">Preview Available</p>
                            </div>
                        </div>
                        
                        <div class="p-4">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="font-semibold text-gray-900">{{ $template['name'] }}</h4>
                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                    {{ $template['category'] }}
                                </span>
                            </div>
                            
                            <p class="text-sm text-gray-600 mb-4">{{ $template['description'] }}</p>
                            
                            <!-- Features -->
                            <div class="flex flex-wrap gap-1 mb-4">
                                @foreach($template['features'] as $feature)
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded bg-gray-100 text-gray-700">
                                        {{ $feature }}
                                    </span>
                                @endforeach
                            </div>
                            
                            <!-- Usage Count -->
                            <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                                <span>Used in {{ $template['usage_count'] }} page(s)</span>
                                <span class="flex items-center">
                                    <div class="w-2 h-2 rounded-full {{ $template['is_active'] ? 'bg-green-400' : 'bg-gray-400' }} mr-2"></div>
                                    {{ $template['is_active'] ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                            
                            <div class="flex space-x-2">
                                <button wire:click="previewTemplate('{{ $template['id'] }}')"
                                        class="flex-1 text-sm px-3 py-2 bg-indigo-100 text-indigo-700 rounded hover:bg-indigo-200 transition-colors">
                                    <i class="fas fa-eye mr-1"></i>Preview
                                </button>
                                <button wire:click="duplicateTemplate('{{ $template['id'] }}')"
                                        class="flex-1 text-sm px-3 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-copy mr-1"></i>Duplicate
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
        <div class="bg-white rounded-lg shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Custom Templates</h3>
                <p class="text-sm text-gray-600 mt-1">Your custom-created templates</p>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($customTemplates as $template)
                        <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-shadow">
                            <div class="aspect-video bg-gradient-to-br from-purple-100 to-purple-200 flex items-center justify-center">
                                <div class="text-center">
                                    <i class="fas fa-code text-purple-400 text-3xl mb-2"></i>
                                    <p class="text-sm text-purple-600">Custom Template</p>
                                </div>
                            </div>
                            
                            <div class="p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-semibold text-gray-900">{{ $template['name'] }}</h4>
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800">
                                        Custom
                                    </span>
                                </div>
                                
                                <p class="text-sm text-gray-600 mb-4">{{ $template['description'] }}</p>
                                
                                <!-- Features -->
                                <div class="flex flex-wrap gap-1 mb-4">
                                    @foreach($template['features'] as $feature)
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded bg-gray-100 text-gray-700">
                                            {{ $feature }}
                                        </span>
                                    @endforeach
                                </div>
                                
                                <!-- Usage Count -->
                                <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                                    <span>Used in {{ $template['usage_count'] }} page(s)</span>
                                    <span class="text-xs text-gray-400">
                                        Created: {{ \Carbon\Carbon::parse($template['created_at'])->format('M j, Y') }}
                                    </span>
                                </div>
                                
                                <div class="flex space-x-2">
                                    <button wire:click="editTemplate('{{ $template['id'] }}')"
                                            class="flex-1 text-sm px-3 py-2 bg-purple-100 text-purple-700 rounded hover:bg-purple-200 transition-colors">
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </button>
                                    <button wire:click="previewTemplate('{{ $template['id'] }}')"
                                            class="text-sm px-3 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50 transition-colors">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <div x-data="{ open: false }" class="relative">
                                        <button @click="open = !open" 
                                                class="text-sm px-3 py-2 border border-gray-300 text-gray-700 rounded hover:bg-gray-50 transition-colors">
                                            <i class="fas fa-ellipsis-h"></i>
                                        </button>
                                        <div x-show="open" @click.away="open = false" 
                                             class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                            <div class="py-1">
                                                <button wire:click="duplicateTemplate('{{ $template['id'] }}')"
                                                        class="flex items-center w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <i class="fas fa-copy w-4 mr-3"></i>Duplicate
                                                </button>
                                                <button wire:click="exportTemplate('{{ $template['id'] }}')"
                                                        class="flex items-center w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    <i class="fas fa-download w-4 mr-3"></i>Export
                                                </button>
                                                <div class="border-t border-gray-100"></div>
                                                <button wire:click="deleteTemplate('{{ $template['id'] }}')"
                                                        wire:confirm="Are you sure you want to delete this template?"
                                                        class="flex items-center w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-50">
                                                    <i class="fas fa-trash w-4 mr-3"></i>Delete
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
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4 overflow-y-auto">
            <div class="bg-white rounded-lg shadow-xl max-w-6xl w-full max-h-screen overflow-y-auto">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ $editingTemplate ? 'Edit Template' : 'Create New Template' }}
                        </h3>
                        <button wire:click="$set('showTemplateEditor', false)" 
                                class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                
                <form wire:submit.prevent="saveTemplate">
                    <div class="p-6 space-y-6">
                        <!-- Template Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Template Name</label>
                                <input type="text" 
                                       wire:model="templateName"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
                                       placeholder="My Custom Template">
                                @error('templateName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <input type="text" 
                                       wire:model="templateDescription"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
                                       placeholder="A brief description of this template">
                                @error('templateDescription') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Template Code Editor -->
                        <div class="space-y-4">
                            <!-- HTML -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Template HTML (Blade)</label>
                                <div class="border border-gray-300 rounded-lg">
                                    <textarea wire:model="templateHtml"
                                              rows="15"
                                              class="w-full rounded-lg px-3 py-2 font-mono text-sm focus:ring-indigo-500 focus:border-indigo-500 resize-none"
                                              placeholder="Enter your Blade template code here..."></textarea>
                                </div>
                                @error('templateHtml') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <!-- CSS -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Custom CSS</label>
                                <div class="border border-gray-300 rounded-lg">
                                    <textarea wire:model="templateCss"
                                              rows="10"
                                              class="w-full rounded-lg px-3 py-2 font-mono text-sm focus:ring-indigo-500 focus:border-indigo-500 resize-none"
                                              placeholder="/* Custom styles for this template */"></textarea>
                                </div>
                            </div>

                            <!-- JavaScript -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Custom JavaScript</label>
                                <div class="border border-gray-300 rounded-lg">
                                    <textarea wire:model="templateJs"
                                              rows="8"
                                              class="w-full rounded-lg px-3 py-2 font-mono text-sm focus:ring-indigo-500 focus:border-indigo-500 resize-none"
                                              placeholder="// Custom JavaScript for this template"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                        <button type="button" 
                                wire:click="$set('showTemplateEditor', false)"
                                class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            Cancel
                        </button>
                        <div class="flex space-x-3">
                            @if($editingTemplate)
                                <button type="button" 
                                        wire:click="previewTemplate('{{ $editingTemplate }}')"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                    Preview Changes
                                </button>
                            @endif
                            <button type="submit" 
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
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
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-lg w-full">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Import Template</h3>
                        <button wire:click="$set('showImportTemplate', false)" 
                                class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                
                <form wire:submit.prevent="importTemplate">
                    <div class="p-6">
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                            <input type="file" 
                                   wire:model="importFile"
                                   accept=".zip"
                                   class="hidden"
                                   id="template_import">
                            
                            <label for="template_import" class="cursor-pointer">
                                <i class="fas fa-upload text-gray-400 text-4xl mb-4"></i>
                                <p class="text-lg text-gray-600 mb-2">Upload Template Package</p>
                                <p class="text-sm text-gray-500">ZIP files only, max 10MB</p>
                            </label>

                            @if($importFile)
                                <div class="mt-4 text-sm text-green-600">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    File selected: {{ $importFile->getClientOriginalName() }}
                                </div>
                            @endif
                        </div>

                        @error('importFile')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                        <button type="button" 
                                wire:click="$set('showImportTemplate', false)"
                                class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" 
                                @if(!$importFile) disabled @endif
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed">
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
            <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-screen overflow-y-auto">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">{{ $selectedTemplate['name'] }} Preview</h3>
                        <button wire:click="$set('selectedTemplate', null)" 
                                class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="bg-gray-100 rounded-lg p-4 mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h4 class="font-medium text-gray-900">{{ $selectedTemplate['name'] }}</h4>
                                <p class="text-sm text-gray-600">{{ $selectedTemplate['description'] }}</p>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                {{ $selectedTemplate['category'] }}
                            </span>
                        </div>
                        
                        <div class="flex flex-wrap gap-2">
                            @foreach($selectedTemplate['features'] as $feature)
                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded bg-white text-gray-700">
                                    {{ $feature }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <!-- Preview iframe would go here -->
                    <div class="bg-white border-2 border-gray-200 rounded-lg" style="height: 500px;">
                        <div class="flex items-center justify-center h-full">
                            <div class="text-center">
                                <i class="fas fa-browser text-gray-300 text-6xl mb-4"></i>
                                <p class="text-gray-500 text-lg mb-2">Template Preview</p>
                                <p class="text-gray-400 text-sm">Live preview would be shown here</p>
                                @if($templatePreview)
                                    <a href="{{ $templatePreview }}" target="_blank" 
                                       class="inline-flex items-center mt-4 text-indigo-600 hover:text-indigo-500">
                                        <i class="fas fa-external-link-alt mr-2"></i>
                                        Open in New Tab
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        Used in {{ $selectedTemplate['usage_count'] }} page(s)
                    </div>
                    <div class="flex space-x-3">
                        @if(!$selectedTemplate['is_custom'])
                            <button wire:click="duplicateTemplate('{{ $selectedTemplate['id'] }}')" 
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                <i class="fas fa-copy mr-2"></i>Duplicate
                            </button>
                        @else
                            <button wire:click="editTemplate('{{ $selectedTemplate['id'] }}')" 
                                    class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                                <i class="fas fa-edit mr-2"></i>Edit
                            </button>
                        @endif
                        <button wire:click="$set('selectedTemplate', null)" 
                                class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>