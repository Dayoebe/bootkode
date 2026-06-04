<!-- Create/Edit Page Form -->
<div class="bg-white rounded-lg shadow-sm">
    <!-- Form Header -->
    <div class="p-6 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">
                    {{ $editingPageId ? 'Edit Page' : 'Create New Page' }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $editingPageId ? 'Update your page content and settings' : 'Create a new SEO-friendly page for your website' }}
                </p>
            </div>
            <button 
                wire:click="{{ $editingPageId ? 'hideEditForm' : 'hideCreateForm' }}"
                class="text-gray-400 hover:text-gray-600"
            >
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <!-- Step Progress -->
        <div class="mt-6">
            <div class="flex items-center">
                @foreach([1, 2, 3, 4] as $step)
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full {{ $currentStep >= $step ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-600' }}">
                            {{ $step }}
                        </div>
                        @if($step < 4)
                            <div class="w-16 h-1 {{ $currentStep > $step ? 'bg-indigo-600' : 'bg-gray-200' }}"></div>
                        @endif
                    </div>
                @endforeach
            </div>
            <div class="flex justify-between text-xs text-gray-600 mt-2">
                <span>Basic Info</span>
                <span>Content</span>
                <span>SEO & Media</span>
                <span>Advanced</span>
            </div>
        </div>
    </div>

    <form wire:submit.prevent="save">
        <div class="p-6">
            <!-- Step 1: Basic Information -->
            @if($currentStep === 1)
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Title -->
                        <div class="md:col-span-2">
                            <label for="title" class="block text-sm font-medium text-gray-700">Page Title *</label>
                            <input 
                                type="text" 
                                id="title"
                                wire:model.live.debounce.300ms="title"
                                class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Enter your page title..."
                            >
                            @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Slug -->
                        <div class="md:col-span-2">
                            <div class="flex items-center justify-between gap-3">
                                <label for="slug" class="block text-sm font-medium text-gray-700">URL Slug</label>
                                @if($slugManuallyEdited)
                                    <button
                                        type="button"
                                        wire:click="syncSlugFromTitle"
                                        class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-semibold text-gray-700 transition-colors hover:bg-gray-50"
                                    >
                                        <i class="fas fa-rotate"></i>
                                        Sync from title
                                    </button>
                                @endif
                            </div>
                            <div class="mt-1 flex rounded-lg shadow-sm">
                                <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                                    {{ url('/') }}/
                                </span>
                                <input 
                                    type="text" 
                                    id="slug"
                                    wire:model.live.debounce.300ms="slug"
                                    class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-lg border border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="page-url-slug"
                                >
                            </div>
                            @error('slug') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            <p class="text-sm text-gray-500 mt-1">Auto-generated from title.</p>
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                            <select 
                                id="status"
                                wire:model="status"
                                class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
                            >
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                                <option value="archived">Archived</option>
                            </select>
                            @error('status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Template -->
                        <div>
                            <label for="template" class="block text-sm font-medium text-gray-700">Template *</label>
                            <select 
                                id="template"
                                wire:model="template"
                                class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
                            >
                                @foreach($templateOptions as $value => $label)
                                    @if($value !== '')
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endif
                                @endforeach
                            </select>
                            @error('template') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Excerpt -->
                        <div class="md:col-span-2">
                            <label for="excerpt" class="block text-sm font-medium text-gray-700">Excerpt</label>
                            <textarea 
                                id="excerpt"
                                wire:model.blur="excerpt"
                                rows="3"
                                class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Brief description of your page..."
                            ></textarea>
                            @error('excerpt') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            <p class="text-sm text-gray-500 mt-1">This will be auto-generated from content if left empty</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Step 2: Content -->
            @if($currentStep === 2)
                <div class="space-y-6">
                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Page Content *</label>
                        <div wire:ignore>
                            <textarea 
                                id="content"
                                wire:model="content"
                                rows="20"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Write your page content here..."
                            ></textarea>
                        </div>
                        @error('content') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Page Blocks -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Content Blocks</h3>
                        <div class="space-y-4">
                            <!-- Hero Block -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <label class="flex items-center">
                                        <input 
                                            type="checkbox" 
                                            wire:model="page_blocks.hero.enabled"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        >
                                        <span class="ml-2 text-sm font-medium text-gray-700">Hero Section</span>
                                    </label>
                                </div>
                                @if($page_blocks['hero']['enabled'] ?? false)
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <input 
                                            type="text" 
                                            wire:model="page_blocks.hero.title"
                                            placeholder="Hero title"
                                            class="block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                                        >
                                        <input 
                                            type="text" 
                                            wire:model="page_blocks.hero.subtitle"
                                            placeholder="Hero subtitle"
                                            class="block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                                        >
                                        <input 
                                            type="text" 
                                            wire:model="page_blocks.hero.cta_text"
                                            placeholder="Button text"
                                            class="block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                                        >
                                        <input 
                                            type="url" 
                                            wire:model="page_blocks.hero.cta_link"
                                            placeholder="Button link"
                                            class="block w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                                        >
                                    </div>
                                @endif
                            </div>

                            <!-- Features Block -->
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <label class="flex items-center">
                                        <input 
                                            type="checkbox" 
                                            wire:model="page_blocks.features.enabled"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                        >
                                        <span class="ml-2 text-sm font-medium text-gray-700">Features Section</span>
                                    </label>
                                </div>
                                @if($page_blocks['features']['enabled'] ?? false)
                                    <div class="space-y-3">
                                        @foreach($page_blocks['features']['items'] ?? [] as $index => $feature)
                                            <div class="flex space-x-2">
                                                <input 
                                                    type="text" 
                                                    wire:model="page_blocks.features.items.{{ $index }}.icon"
                                                    placeholder="Icon class (e.g., fas fa-star)"
                                                    class="block w-1/4 border border-gray-300 rounded-lg px-3 py-2 text-sm"
                                                >
                                                <input 
                                                    type="text" 
                                                    wire:model="page_blocks.features.items.{{ $index }}.title"
                                                    placeholder="Feature title"
                                                    class="block w-1/4 border border-gray-300 rounded-lg px-3 py-2 text-sm"
                                                >
                                                <input 
                                                    type="text" 
                                                    wire:model="page_blocks.features.items.{{ $index }}.description"
                                                    placeholder="Feature description"
                                                    class="block w-1/2 border border-gray-300 rounded-lg px-3 py-2 text-sm"
                                                >
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Step 3: SEO & Media -->
            @if($currentStep === 3)
                <div class="space-y-6">
                    <!-- SEO Section -->
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">SEO Settings</h3>
                            <button 
                                type="button"
                                wire:click="analyzeSeo"
                                class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700"
                            >
                                Analyze SEO
                            </button>
                        </div>
                        
                        <div class="grid grid-cols-1 gap-6">
                            <!-- Meta Title -->
                            <div>
                                <label for="meta_title" class="block text-sm font-medium text-gray-700">Meta Title</label>
                                <input 
                                    type="text" 
                                    id="meta_title"
                                    wire:model.blur="meta_title"
                                    maxlength="60"
                                    class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="SEO title for search engines..."
                                >
                                <div class="flex justify-between text-sm text-gray-500 mt-1">
                                    <span>Leave empty to use page title</span>
                                    <span>{{ strlen($meta_title) }}/60</span>
                                </div>
                                @error('meta_title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Meta Description -->
                            <div>
                                <label for="meta_description" class="block text-sm font-medium text-gray-700">Meta Description</label>
                                <textarea 
                                    id="meta_description"
                                    wire:model.blur="meta_description"
                                    rows="3"
                                    maxlength="160"
                                    class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Brief description for search engines..."
                                ></textarea>
                                <div class="flex justify-between text-sm text-gray-500 mt-1">
                                    <span>This appears in search results</span>
                                    <span>{{ strlen($meta_description) }}/160</span>
                                </div>
                                @error('meta_description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Meta Keywords -->
                            <div>
                                <label for="meta_keywords" class="block text-sm font-medium text-gray-700">Meta Keywords</label>
                                <input 
                                    type="text" 
                                    id="meta_keywords"
                                    wire:model="meta_keywords"
                                    class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="keyword1, keyword2, keyword3..."
                                >
                                <p class="text-sm text-gray-500 mt-1">Comma-separated keywords</p>
                                @error('meta_keywords') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- No Index -->
                            <div class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    id="no_index"
                                    wire:model="no_index"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                >
                                <label for="no_index" class="ml-2 text-sm text-gray-700">
                                    Prevent search engines from indexing this page
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Media Section -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Media</h3>
                        
                        <!-- Featured Image -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Featured Image</label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6">
                                <input 
                                    type="file" 
                                    wire:model="featured_image"
                                    accept="image/*"
                                    class="hidden"
                                    id="featured_image"
                                >
                                <label for="featured_image" class="cursor-pointer">
                                    <div class="text-center">
                                        <i class="fas fa-cloud-upload-alt text-gray-400 text-4xl mb-4"></i>
                                        <p class="text-sm text-gray-600">Click to upload featured image</p>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                                    </div>
                                </label>
                                @if($featured_image)
                                    <div class="mt-4 text-sm text-green-600">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        Image selected: {{ $featured_image->getClientOriginalName() }}
                                    </div>
                                @endif
                            </div>
                            @error('featured_image') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            @endif

            <!-- Step 4: Advanced Settings -->
            @if($currentStep === 4)
                <div class="space-y-6">
                    <!-- Publishing Schedule -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Publishing Schedule</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label for="published_at" class="block text-sm font-medium text-gray-700">Publish Date</label>
                                <input 
                                    type="datetime-local" 
                                    id="published_at"
                                    wire:model="published_at"
                                    class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
                                >
                            </div>
                            <div>
                                <label for="scheduled_at" class="block text-sm font-medium text-gray-700">Schedule For</label>
                                <input 
                                    type="datetime-local" 
                                    id="scheduled_at"
                                    wire:model="scheduled_at"
                                    class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
                                >
                            </div>
                            <div>
                                <label for="expires_at" class="block text-sm font-medium text-gray-700">Expires At</label>
                                <input 
                                    type="datetime-local" 
                                    id="expires_at"
                                    wire:model="expires_at"
                                    class="mt-1 block w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Page Settings -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Page Settings</h3>
                        <div class="space-y-4">
                            <label class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    wire:model="settings.enable_sharing"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                >
                                <span class="ml-2 text-sm text-gray-700">Enable social sharing buttons</span>
                            </label>
                            <label class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    wire:model="settings.enable_reading_time"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                >
                                <span class="ml-2 text-sm text-gray-700">Show reading time estimate</span>
                            </label>
                            <label class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    wire:model="settings.enable_toc"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                >
                                <span class="ml-2 text-sm text-gray-700">Generate table of contents</span>
                            </label>
                        </div>
                    </div>

                    <!-- Shortcodes -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Custom Shortcodes</h3>
                        <div class="space-y-3">
                            @foreach($shortcodes as $key => $value)
                                <div class="flex space-x-2">
                                    <input 
                                        type="text" 
                                        value="{{ $key }}"
                                        readonly
                                        class="block w-1/3 border border-gray-300 rounded-lg px-3 py-2 bg-gray-50 text-sm"
                                    >
                                    <input 
                                        type="text" 
                                        wire:model="shortcodes.{{ $key }}"
                                        placeholder="Shortcode value"
                                        class="block flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm"
                                    >
                                    <button 
                                        type="button"
                                        wire:click="removeShortcode('{{ $key }}')"
                                        class="text-red-600 hover:text-red-700 px-2"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            @endforeach
                            <button 
                                type="button"
                                wire:click="addShortcode"
                                class="text-indigo-600 hover:text-indigo-700 text-sm"
                            >
                                <i class="fas fa-plus mr-2"></i>Add Shortcode
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Form Footer -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg">
            <div class="flex items-center justify-between">
                <!-- Step Navigation -->
                <div class="flex space-x-3">
                    @if($currentStep > 1)
                        <button 
                            type="button"
                            wire:click="previousStep"
                            class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
                        >
                            Previous
                        </button>
                    @endif
                    
                    @if($currentStep < 4)
                        <button 
                            type="button"
                            wire:click="nextStep"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700"
                        >
                            Next
                        </button>
                    @endif
                </div>

                <!-- Save Actions -->
                @if($currentStep === 4)
                    <div class="flex space-x-3">
                        <button 
                            type="button"
                            wire:click="save('draft')"
                            class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50"
                        >
                            Save Draft
                        </button>
                        
                        @if($scheduled_at)
                            <button 
                                type="button"
                                wire:click="save('schedule')"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
                            >
                                Schedule
                            </button>
                        @endif
                        
                        <button 
                            type="button"
                            wire:click="save('publish')"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700"
                        >
                            {{ $editingPageId ? 'Update & Publish' : 'Create & Publish' }}
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </form>
</div>

@script
<script>
    // Initialize rich text editor for content
    document.addEventListener('livewire:navigated', () => {
        if (typeof tinymce !== 'undefined') {
            tinymce.remove('#content');
        }
        
        // You can replace this with your preferred editor
        // Example with TinyMCE (you'll need to include it in your layout)
        /*
        tinymce.init({
            selector: '#content',
            height: 500,
            plugins: 'advlist autolink lists link image charmap print preview anchor',
            toolbar: 'undo redo | formatselect | bold italic backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | help',
            setup: function(editor) {
                editor.on('change', function() {
                    @this.set('content', editor.getContent());
                });
            }
        });
        */
    });
</script>
@endscript
