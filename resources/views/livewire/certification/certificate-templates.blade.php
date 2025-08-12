<div class="container mx-auto px-4 py-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 animate__animated animate__fadeIn">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Certificate Templates</h2>
            <button 
                wire:click="createTemplate"
                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
                <i class="fas fa-plus mr-2"></i> New Template
            </button>
        </div>

        @if($showForm)
            <div class="bg-white dark:bg-gray-700 rounded-lg shadow-md p-6 mb-6 border border-gray-200 dark:border-gray-600 animate__animated animate__fadeIn">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    {{ $isEditing ? 'Edit Template' : 'Create New Template' }}
                </h3>

                <form wire:submit.prevent="saveTemplate">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Template Name *</label>
                            <input 
                                type="text" 
                                id="name" 
                                wire:model="name"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                required
                            >
                            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description (Rich Text)</label>
                            <div id="description-editor" x-data="editorJs('description-editor', '{{ $descriptionJson }}', (output) => $wire.set('descriptionJson', output)"></div>
                            @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="backgroundImage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Background Image</label>
                            <input 
                                type="file" 
                                id="backgroundImage" 
                                wire:model="backgroundImage"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            >
                            @if($backgroundImagePreview)
                                <img src="{{ $backgroundImagePreview }}" alt="Preview" class="mt-2 max-w-xs rounded-md shadow-md animate__animated animate__fadeIn">
                            @endif
                            @error('backgroundImage') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Content Areas *</label>
                            @foreach($contentAreas as $index => $area)
                                <div class="border border-gray-300 dark:border-gray-600 p-4 mb-4 rounded-md relative animate__animated animate__fadeIn">
                                    <button type="button" wire:click="removeContentArea({{ $index }})" class="absolute top-2 right-2 text-red-500 hover:text-red-700">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs">Name *</label>
                                            <input wire:model="contentAreas.{{ $index }}.name" class="w-full px-2 py-1 border rounded-md dark:bg-gray-700 dark:text-white">
                                            @error("contentAreas.{{ $index }}.name") <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="col-span-2">
                                            <label class="block text-xs">Content (Rich Text)</label>
                                            <div id="content-area-{{ $index }}" x-data="editorJs('content-area-{{ $index }}', '{{ $contentAreas[$index]['content'] }}', (output) => $wire.call('updateContentArea', {{ $index }}, output)"></div>
                                            @error("contentAreas.{{ $index }}.content") <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-xs">X Position *</label>
                                            <input type="number" wire:model="contentAreas.{{ $index }}.x" class="w-full px-2 py-1 border rounded-md dark:bg-gray-700 dark:text-white">
                                            @error("contentAreas.{{ $index }}.x") <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-xs">Y Position *</label>
                                            <input type="number" wire:model="contentAreas.{{ $index }}.y" class="w-full px-2 py-1 border rounded-md dark:bg-gray-700 dark:text-white">
                                            @error("contentAreas.{{ $index }}.y") <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-xs">Width *</label>
                                            <input type="number" wire:model="contentAreas.{{ $index }}.width" class="w-full px-2 py-1 border rounded-md dark:bg-gray-700 dark:text-white">
                                            @error("contentAreas.{{ $index }}.width") <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-xs">Height *</label>
                                            <input type="number" wire:model="contentAreas.{{ $index }}.height" class="w-full px-2 py-1 border rounded-md dark:bg-gray-700 dark:text-white">
                                            @error("contentAreas.{{ $index }}.height") <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <button type="button" wire:click="addContentArea" class="px-3 py-1 bg-green-600 text-white rounded-md">
                                <i class="fas fa-plus mr-1"></i> Add Area
                            </button>
                        </div>

                        <div>
                            <label for="defaultFont" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Default Font *</label>
                            <input 
                                type="text" 
                                id="defaultFont" 
                                wire:model="defaultFont"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                required
                            >
                            @error('defaultFont') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="defaultFontSize" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Default Font Size *</label>
                            <input 
                                type="number" 
                                id="defaultFontSize" 
                                wire:model="defaultFontSize"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                required
                            >
                            @error('defaultFontSize') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="defaultFontColor" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Default Font Color *</label>
                            <input 
                                type="color" 
                                id="defaultFontColor" 
                                wire:model="defaultFontColor"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                required
                            >
                            @error('defaultFontColor') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="inline-flex items-center">
                                <input type="checkbox" wire:model="isActive" class="form-checkbox">
                                <span class="ml-2">Active</span>
                            </label>
                            @error('isActive') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md">
                            {{ $isEditing ? 'Update' : 'Create' }} Template
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Name
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Description
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Content Areas
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Font
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($templates as $template)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ $template->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {!! json_decode($template->description ?? '{}', true)['blocks'][0]['data']['text'] ?? 'No description' !!}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button 
                                    wire:click="toggleTemplateStatus({{ $template->id }})"
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full transition-colors duration-200"
                                    @class([
                                        'bg-green-100 text-green-800' => $template->is_active,
                                        'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-300' => !$template->is_active,
                                    ])
                                >
                                    {{ $template->is_active ? 'Active' : 'Inactive' }}
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ count($template->content_areas) }} areas
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $template->default_font }} ({{ $template->default_font_size }}px)
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button 
                                        wire:click="editTemplate({{ $template->id }})"
                                        class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300"
                                        title="Edit Template"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button 
                                        wire:click="previewTemplate({{ $template->id }})"
                                        class="text-green-600 dark:text-green-400 hover:text-green-900 dark:hover:text-green-300 animate__animated animate__pulse"
                                        title="Preview Template"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button 
                                        wire:click="deleteTemplate({{ $template->id }})"
                                        class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300"
                                        title="Delete Template"
                                        onclick="confirm('Are you sure you want to delete this template?') || event.stopImmediatePropagation()"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                No templates found. Create your first template to get started.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $templates->links() }}
        </div>
    </div>

    <!-- Preview Modal -->
    <x-modal name="preview-modal" wire:model="previewTemplateId" maxWidth="2xl">
        <x-slot name="title">
            Template Preview
        </x-slot>

        <x-slot name="content">
            @if($previewTemplateId)
                @php
                    $template = App\Models\CertificateTemplate::find($previewTemplateId);
                @endphp
                <div class="relative animate__animated animate__fadeIn">
                    <img src="{{ Storage::url($template->background_image_path ?? '') }}" alt="Background" class="w-full h-auto rounded-md shadow-md" onerror="this.src='https://via.placeholder.com/800x600?text=No+Background';">
                    @foreach($template->content_areas as $area)
                        <div 
                            class="absolute p-2 overflow-hidden"
                            style="
                                left: {{ $area['x'] ?? 0 }}px; 
                                top: {{ $area['y'] ?? 0 }}px; 
                                width: {{ $area['width'] ?? 100 }}px; 
                                height: {{ $area['height'] ?? 50 }}px; 
                                font-family: {{ $template->default_font }}; 
                                font-size: {{ $template->default_font_size }}px; 
                                color: {{ $template->default_font_color }};
                            "
                        >
                            {!! json_decode($area['content'] ?? '{}', true)['blocks'][0]['data']['text'] ?? ($area['name'] ?? 'Placeholder') !!}
                        </div>
                    @endforeach
                </div>
            @endif
        </x-slot>

        <x-slot name="footer">
            <button 
                wire:click="closePreview"
                class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
                Close
            </button>
        </x-slot>
    </x-modal>
</div>