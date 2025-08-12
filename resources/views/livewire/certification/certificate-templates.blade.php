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

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                            <textarea 
                                id="description" 
                                wire:model="description"
                                rows="2"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            ></textarea>
                        </div>

                        <div>
                            <label for="backgroundImage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Background Image</label>
                            <input 
                                type="file" 
                                id="backgroundImage" 
                                wire:model="backgroundImage"
                                accept="image/*"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            >
                            @error('backgroundImage') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            
                            @if($backgroundImagePreview)
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Current Image:</p>
                                    <img src="{{ Storage::disk('public')->url($backgroundImagePreview) }}" alt="Template Preview" class="h-32 object-contain border border-gray-200 dark:border-gray-600">
                                </div>
                            @endif
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Default Font</label>
                            <select 
                                wire:model="defaultFont"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            >
                                @foreach($fontOptions as $font)
                                    <option value="{{ $font }}">{{ $font }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="defaultFontSize" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Default Font Size</label>
                            <input 
                                type="number" 
                                id="defaultFontSize" 
                                wire:model="defaultFontSize"
                                min="8" 
                                max="72"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                            >
                            @error('defaultFontSize') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="defaultFontColor" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Default Font Color</label>
                            <div class="flex items-center">
                                <input 
                                    type="color" 
                                    id="defaultFontColor" 
                                    wire:model="defaultFontColor"
                                    class="w-10 h-10 border border-gray-300 dark:border-gray-600 rounded-md mr-2"
                                >
                                <input 
                                    type="text" 
                                    wire:model="defaultFontColor"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                >
                            </div>
                            @error('defaultFontColor') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="inline-flex items-center">
                                <input 
                                    type="checkbox" 
                                    wire:model="isActive"
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600"
                                >
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active Template</span>
                            </label>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h4 class="text-md font-medium text-gray-900 dark:text-white mb-2">Content Areas</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                            Define where dynamic content should be placed on the certificate.
                        </p>

                        <div class="space-y-4">
                            @foreach($contentAreas as $index => $area)
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md border border-gray-200 dark:border-gray-600">
                                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Field Name *</label>
                                            <input 
                                                type="text" 
                                                wire:model="contentAreas.{{ $index }}.name"
                                                class="w-full px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                                required
                                            >
                                            @error("contentAreas.{$index}.name") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">X Position (px) *</label>
                                            <input 
                                                type="number" 
                                                wire:model="contentAreas.{{ $index }}.x"
                                                class="w-full px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                                required
                                            >
                                            @error("contentAreas.{$index}.x") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Y Position (px) *</label>
                                            <input 
                                                type="number" 
                                                wire:model="contentAreas.{{ $index }}.y"
                                                class="w-full px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                                required
                                            >
                                            @error("contentAreas.{$index}.y") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="flex items-end space-x-2">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Width (px) *</label>
                                                <input 
                                                    type="number" 
                                                    wire:model="contentAreas.{{ $index }}.width"
                                                    class="w-full px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                                    required
                                                >
                                                @error("contentAreas.{$index}.width") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Height (px) *</label>
                                                <input 
                                                    type="number" 
                                                    wire:model="contentAreas.{{ $index }}.height"
                                                    class="w-full px-3 py-1 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                                    required
                                                >
                                                @error("contentAreas.{$index}.height") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                            </div>
                                            @if($index > 0)
                                                <button 
                                                    type="button" 
                                                    wire:click="removeContentArea({{ $index }})"
                                                    class="px-2 py-1 bg-red-100 text-red-700 rounded-md hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-red-500"
                                                >
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button 
                            type="button" 
                            wire:click="addContentArea"
                            class="mt-2 px-3 py-1 bg-gray-100 dark:bg-gray-600 text-gray-800 dark:text-white rounded-md hover:bg-gray-200 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-500"
                        >
                            <i class="fas fa-plus mr-1"></i> Add Content Area
                        </button>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button 
                            type="button" 
                            wire:click="$set('showForm', false)"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        >
                            {{ $isEditing ? 'Update Template' : 'Create Template' }}
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
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Content Areas
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Default Font
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($templates as $template)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                        <i class="fas fa-stamp text-blue-600 dark:text-blue-300"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $template->name }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ Str::limit($template->description, 30) }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button 
                                    wire:click="toggleTemplateStatus({{ $template->id }})"
                                    @class([
                                        'px-2 inline-flex text-xs leading-5 font-semibold rounded-full cursor-pointer',
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
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
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
</div>