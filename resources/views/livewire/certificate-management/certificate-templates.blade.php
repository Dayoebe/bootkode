<div class="min-h-screen bg-themed-primary p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-themed-primary">Certificate Templates</h1>
                <p class="text-themed-secondary mt-2">Manage certificate designs and layouts</p>
            </div>
            
            @can('create', 'certificates')
            <button wire:click="showCreateModal" 
                    class="px-6 py-3 bg-accent-themed-primary hover:bg-accent-themed-secondary text-white rounded-lg font-semibold transition-colors flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Create Template
            </button>
            @endcan
        </div>

        <!-- Templates Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @forelse($templates as $template)
            <div class="bg-themed-secondary rounded-xl overflow-hidden border border-themed-primary hover:border-accent-themed-primary transition-all duration-200 shadow-lg hover:shadow-xl">
                <!-- Template Preview -->
                <div class="relative aspect-video bg-gradient-to-br from-themed-tertiary to-themed-secondary overflow-hidden">
                    @if($template['preview_url'])
                    <img src="{{ $template['preview_url'] }}" 
                         alt="{{ $template['name'] }}" 
                         class="w-full h-full object-cover">
                    @else
                    <div class="w-full h-full flex items-center justify-center">
                        <div class="text-center">
                            <i class="fas fa-file-pdf text-4xl text-themed-secondary mb-2"></i>
                            <p class="text-themed-secondary text-sm">No preview available</p>
                        </div>
                    </div>
                    @endif

                    <!-- Default Badge -->
                    @if($template['is_default'])
                    <div class="absolute top-2 right-2 bg-green-600 text-white px-3 py-1 rounded-full text-xs font-semibold flex items-center">
                        <i class="fas fa-check-circle mr-1"></i> Default
                    </div>
                    @endif
                </div>

                <!-- Template Info -->
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-themed-primary mb-1">{{ $template['name'] }}</h3>
                    <p class="text-themed-secondary text-sm mb-4">{{ $template['description'] }}</p>

                    <!-- Color Preview -->
                    <div class="mb-4 p-3 bg-themed-tertiary rounded-lg">
                        <div class="text-xs text-themed-secondary mb-2 font-semibold">Colors</div>
                        <div class="flex gap-2">
                            <div class="flex-1 h-8 rounded-lg border-2 border-themed-primary" 
                                 style="background-color: {{ $template['settings']['backgroundColor'] }}"></div>
                            <div class="flex-1 h-8 rounded-lg border-2 border-themed-primary" 
                                 style="background-color: {{ $template['settings']['accentColor'] }}"></div>
                            <div class="flex-1 h-8 rounded-lg border-2 border-themed-primary" 
                                 style="background-color: {{ $template['settings']['borderColor'] }}"></div>
                        </div>
                    </div>

                    <!-- Font Info -->
                    <div class="mb-4 p-3 bg-themed-tertiary rounded-lg">
                        <div class="text-xs text-themed-secondary font-semibold mb-2">Typography</div>
                        <div class="space-y-1 text-sm">
                            <div class="flex justify-between">
                                <span class="text-themed-secondary">Header:</span>
                                <span class="text-themed-primary font-medium">{{ $template['settings']['headerFont'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-themed-secondary">Body:</span>
                                <span class="text-themed-primary font-medium">{{ $template['settings']['bodyFont'] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="space-y-2">
                        <button wire:click="previewTemplate({{ $template['id'] }})"
                                class="w-full bg-accent-themed-primary hover:bg-accent-themed-secondary text-white py-2 px-4 rounded-lg transition-colors flex items-center justify-center font-medium">
                            <i class="fas fa-eye mr-2"></i>
                            Preview
                        </button>
                        
                        <div class="flex gap-2">
                            @can('update', 'certificates')
                            <button wire:click="showEditModal({{ $template['id'] }})"
                                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-3 rounded-lg transition-colors flex items-center justify-center text-sm">
                                <i class="fas fa-edit mr-1"></i>
                                Edit
                            </button>
                            @endcan

                            @if(!$template['is_default'] && auth()->user()->isSuperAdmin())
                            <button wire:click="setDefaultTemplate({{ $template['id'] }})"
                                    class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 px-3 rounded-lg transition-colors flex items-center justify-center text-sm">
                                <i class="fas fa-star mr-1"></i>
                                Set Default
                            </button>
                            @endif

                            @if(!$template['is_default'] && auth()->user()->isSuperAdmin())
                            <button wire:click="deleteTemplate({{ $template['id'] }})"
                                    class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2 px-3 rounded-lg transition-colors flex items-center justify-center text-sm"
                                    onclick="return confirm('Are you sure?')">
                                <i class="fas fa-trash mr-1"></i>
                                Delete
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full bg-themed-secondary rounded-xl p-12 text-center border border-themed-primary">
                <i class="fas fa-palette text-6xl text-themed-tertiary mb-6"></i>
                <h3 class="text-xl font-semibold text-themed-primary mb-4">No Templates Found</h3>
                <p class="text-themed-secondary mb-8">Create your first certificate template to get started.</p>
                @can('create', 'certificates')
                <button wire:click="showCreateModal"
                        class="inline-flex items-center px-6 py-3 bg-accent-themed-primary hover:bg-accent-themed-secondary text-white rounded-lg font-semibold transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Create Template
                </button>
                @endcan
            </div>
            @endforelse
        </div>

        <!-- Template Usage Stats -->
        @if(count($templates) > 0)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-themed-secondary rounded-xl p-6 border border-themed-primary">
                <h4 class="text-themed-secondary text-sm font-semibold mb-2">Total Templates</h4>
                <div class="text-3xl font-bold text-themed-primary">{{ count($templates) }}</div>
            </div>
            
            <div class="bg-themed-secondary rounded-xl p-6 border border-themed-primary">
                <h4 class="text-themed-secondary text-sm font-semibold mb-2">Active Templates</h4>
                <div class="text-3xl font-bold text-accent-themed-primary">
                    {{ collect($templates)->filter(fn($t) => !$t['is_default'])->count() }}
                </div>
            </div>
            
            <div class="bg-themed-secondary rounded-xl p-6 border border-themed-primary">
                <h4 class="text-themed-secondary text-sm font-semibold mb-2">Default Template</h4>
                <div class="text-lg font-semibold text-themed-primary">
                    {{ collect($templates)->firstWhere('is_default')['name'] ?? 'None' }}
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Preview Modal -->
    @if($showPreview && $previewCertificate)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl max-w-4xl w-full max-h-screen overflow-y-auto">
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900">Template Preview (Sample)</h3>
                <button wire:click="closeModals" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="p-6">
                <!-- Preview the certificate using the template -->
                <div class="bg-gray-50 p-6 rounded-lg">
                    @include('certificates.public-view', ['certificate' => $previewCertificate, 'isPdf' => false])
                </div>
            </div>

            <div class="border-t border-gray-200 px-6 py-4 flex justify-end gap-4">
                <button wire:click="closeModals"
                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-lg transition-colors">
                    Close Preview
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Create/Edit Modal -->
    @if($showCreateModal || $showEditModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-themed-secondary rounded-xl max-w-2xl w-full max-h-screen overflow-y-auto border border-themed-primary">
            <div class="sticky top-0 bg-themed-secondary border-b border-themed-primary px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-bold text-themed-primary">
                    {{ $showEditModal ? 'Edit Template' : 'Create Template' }}
                </h3>
                <button wire:click="closeModals" class="text-themed-secondary hover:text-themed-primary">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="p-6 space-y-6">
                <!-- Template Name -->
                <div>
                    <label class="block text-sm font-medium text-themed-primary mb-2">Template Name</label>
                    <input type="text" 
                           wire:model="templateName" 
                           placeholder="e.g., Professional Gold"
                           class="w-full bg-themed-tertiary border border-themed-primary rounded-lg px-4 py-2 text-themed-primary focus:ring-2 focus:ring-accent-themed-primary transition-colors">
                    @error('templateName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-themed-primary mb-2">Description</label>
                    <textarea wire:model="templateDescription" 
                              placeholder="Describe this template..."
                              rows="3"
                              class="w-full bg-themed-tertiary border border-themed-primary rounded-lg px-4 py-2 text-themed-primary focus:ring-2 focus:ring-accent-themed-primary transition-colors resize-none"></textarea>
                    @error('templateDescription') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Colors Section -->
                <div class="bg-themed-tertiary rounded-lg p-4">
                    <h4 class="font-semibold text-themed-primary mb-4">Colors</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-themed-secondary mb-2">Background</label>
                            <input type="color" 
                                   wire:model="backgroundColor"
                                   class="w-full h-10 rounded-lg cursor-pointer border border-themed-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-themed-secondary mb-2">Border</label>
                            <input type="color" 
                                   wire:model="borderColor"
                                   class="w-full h-10 rounded-lg cursor-pointer border border-themed-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-themed-secondary mb-2">Text</label>
                            <input type="color" 
                                   wire:model="textColor"
                                   class="w-full h-10 rounded-lg cursor-pointer border border-themed-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-themed-secondary mb-2">Accent</label>
                            <input type="color" 
                                   wire:model="accentColor"
                                   class="w-full h-10 rounded-lg cursor-pointer border border-themed-primary">
                        </div>
                    </div>
                </div>

                <!-- Fonts Section -->
                <div class="bg-themed-tertiary rounded-lg p-4">
                    <h4 class="font-semibold text-themed-primary mb-4">Typography</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-themed-secondary mb-2">Header Font</label>
                            <select wire:model="headerFont"
                                    class="w-full bg-themed-secondary border border-themed-primary rounded-lg px-4 py-2 text-themed-primary focus:ring-2 focus:ring-accent-themed-primary transition-colors">
                                <option>Playfair Display</option>
                                <option>Garamond</option>
                                <option>Georgia</option>
                                <option>Merriweather</option>
                                <option>Lora</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-themed-secondary mb-2">Body Font</label>
                            <select wire:model="bodyFont"
                                    class="w-full bg-themed-secondary border border-themed-primary rounded-lg px-4 py-2 text-themed-primary focus:ring-2 focus:ring-accent-themed-primary transition-colors">
                                <option>Crimson Text</option>
                                <option>Lora</option>
                                <option>Merriweather</option>
                                <option>Inter</option>
                                <option>Montserrat</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Is Default Checkbox -->
                @if(auth()->user()->isSuperAdmin())
                <div class="flex items-center">
                    <input type="checkbox" 
                           wire:model="isDefault"
                           id="isDefault"
                           class="w-4 h-4 rounded border-themed-primary cursor-pointer">
                    <label for="isDefault" class="ml-3 text-themed-primary font-medium cursor-pointer">
                        Set as default template
                    </label>
                </div>
                @endif
            </div>

            <div class="border-t border-themed-primary px-6 py-4 flex justify-end gap-4 bg-themed-tertiary">
                <button wire:click="closeModals"
                        class="px-4 py-2 bg-themed-primary hover:bg-themed-secondary text-white rounded-lg transition-colors">
                    Cancel
                </button>
                <button wire:click="{{ $showEditModal ? 'updateTemplate' : 'saveTemplate' }}"
                        class="px-4 py-2 bg-accent-themed-primary hover:bg-accent-themed-secondary text-white rounded-lg transition-colors font-semibold">
                    {{ $showEditModal ? 'Update' : 'Create' }} Template
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Loading Overlay -->
    <div wire:loading class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-themed-secondary rounded-lg p-8 flex items-center border border-themed-primary">
            <i class="fas fa-spinner fa-spin text-accent-themed-primary text-2xl mr-4"></i>
            <span class="text-themed-primary font-medium">Processing...</span>
        </div>
    </div>
</div>