{{-- resources/views/livewire/blog/admin-blog-post-form.blade.php --}}
<div>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                {{ $isEdit ? 'Edit Post' : 'Create Post' }}
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                {{ $isEdit ? 'Update your blog post' : 'Create a new blog post' }}
            </p>
        </div>
        <a href="{{ route('admin.blog.posts.index') }}"
            class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to Posts
        </a>
    </div>

    <form wire:submit.prevent="save">
        <div class="grid lg:grid-cols-3 gap-8">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Title --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model.live="title"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-lg"
                                placeholder="Enter post title..." required>
                            @error('title')<span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>@enderror
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Slug <span class="text-red-500">*</span>
                                </label>
                                <button type="button" wire:click="generateSlug"
                                    class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                    <i class="fas fa-sync mr-1"></i>
                                    Generate from title
                                </button>
                            </div>
                            <input type="text" wire:model="slug"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="post-slug-url" required>
                            <p class="text-sm text-gray-500 mt-1">
                                <i class="fas fa-info-circle mr-1"></i>
                                URL: {{ url('/blog') }}/{{ $slug ?: 'your-slug' }}
                            </p>
                            @error('slug')<span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                {{-- Content Editor --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6" wire:ignore>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Content <span class="text-red-500">*</span>
                    </label>
                    <div class="mb-4">
                        <input id="content" type="hidden" name="content" value="{{ $content }}">
                        <trix-editor input="content" class="prose max-w-none" style="min-height: 400px;"
                            placeholder="Start writing your blog post...">
                        </trix-editor>
                    </div>
                    @error('content')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                {{-- Excerpt --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Excerpt
                    </label>
                    <textarea wire:model="excerpt" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="Brief description of your post..."></textarea>
                    <p class="text-sm text-gray-500 mt-2">
                        <i class="fas fa-lightbulb mr-1"></i>
                        Leave empty to auto-generate from content (first 200 characters).
                    </p>
                    @error('excerpt')<span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>@enderror
                </div>

                {{-- SEO Settings --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                    <button type="button" wire:click="$toggle('showSeoSection')"
                        class="w-full flex items-center justify-between p-6 text-left hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <span class="text-lg font-medium text-gray-900 dark:text-white">
                            <i class="fas fa-search mr-2 text-green-500"></i>
                            SEO Settings
                        </span>
                        <i class="fas fa-chevron-{{ $showSeoSection ? 'up' : 'down' }} text-gray-400"></i>
                    </button>

                    @if($showSeoSection)
                        <div class="px-6 pb-6 border-t border-gray-200 dark:border-gray-700 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Meta Title
                                    <span class="text-gray-400 font-normal">(SEO Title)</span>
                                </label>
                                <input type="text" wire:model="meta_title"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="Auto-filled from post title">
                                <div class="flex justify-between items-center mt-1">
                                    <p class="text-sm text-gray-500">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        This appears in search results and browser tabs
                                    </p>
                                    <span class="text-sm {{ strlen($meta_title) > 60 ? 'text-red-500' : 'text-gray-500' }}">
                                        {{ strlen($meta_title) }}/60
                                    </span>
                                </div>
                                @error('meta_title')<span
                                class="text-red-500 text-sm mt-1 block">{{ $message }}</span>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Meta Description
                                    <span class="text-gray-400 font-normal">(Search Description)</span>
                                </label>
                                <textarea wire:model="meta_description" rows="3"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="Brief description that appears in search results..."></textarea>
                                <div class="flex justify-between items-center mt-1">
                                    <p class="text-sm text-gray-500">
                                        <i class="fas fa-search mr-1"></i>
                                        This appears below the title in search results
                                    </p>
                                    <span
                                        class="text-sm {{ strlen($meta_description) > 160 ? 'text-red-500' : 'text-gray-500' }}">
                                        {{ strlen($meta_description) }}/160
                                    </span>
                                </div>
                                @error('meta_description')<span
                                class="text-red-500 text-sm mt-1 block">{{ $message }}</span>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Keywords
                                    <span class="text-gray-400 font-normal">(For SEO)</span>
                                </label>
                                <div class="flex flex-wrap gap-2 mb-2">
                                    @foreach($meta_keywords as $index => $keyword)
                                        <span
                                            class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm flex items-center">
                                            {{ $keyword }}
                                            <button type="button" wire:click="removeKeyword({{ $index }})"
                                                class="ml-2 text-blue-600 hover:text-blue-800">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </span>
                                    @endforeach
                                </div>
                                <div class="flex">
                                    <input type="text" wire:model="newKeyword" wire:keydown.enter.prevent="addKeyword"
                                        class="flex-1 px-4 py-2 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        placeholder="Add keyword...">
                                    <button type="button" wire:click="addKeyword"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-r-lg hover:bg-blue-700">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Publish Settings --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        <i class="fas fa-paper-plane text-blue-500 mr-2"></i>
                        Publish Settings
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="status"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="draft">📝 Draft</option>
                                <option value="published">🌐 Published</option>
                                <option value="scheduled">⏰ Scheduled</option>
                            </select>
                            @error('status')<span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>@enderror
                        </div>

                        {{-- Show date/time field only for scheduled posts --}}
                        @if($status === 'scheduled')
                            <div
                                class="bg-yellow-50 dark:bg-yellow-900 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4">
                                <label class="block text-sm font-medium text-yellow-800 dark:text-yellow-200 mb-2">
                                    <i class="fas fa-calendar-alt mr-1"></i>
                                    Schedule Publish Date & Time <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" wire:model="published_at"
                                    class="w-full px-4 py-2 border border-yellow-300 rounded-lg focus:ring-2 focus:ring-yellow-500 dark:bg-yellow-800 dark:border-yellow-600 dark:text-white"
                                    min="{{ now()->format('Y-m-d\TH:i') }}" required>
                                <p class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Post will be automatically published at this date and time
                                </p>
                                @error('published_at')<span
                                class="text-red-500 text-sm mt-1 block">{{ $message }}</span>@enderror
                            </div>
                        @endif

                        @if($status === 'published')
                            <div
                                class="bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg p-3">
                                <p class="text-sm text-green-800 dark:text-green-200">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Post will be published immediately with current date and time
                                </p>
                            </div>
                        @endif

                        @if($status === 'draft')
                            <div
                                class="bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg p-3">
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    <i class="fas fa-edit mr-1"></i>
                                    Post saved as draft - not visible to public
                                </p>
                            </div>
                        @endif

                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-3">
                            <div class="flex items-center">
                                <input type="checkbox" wire:model="is_featured" id="is_featured"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <label for="is_featured" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-star text-yellow-500 mr-1"></i>
                                    Featured Post
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" wire:model="allow_comments" id="allow_comments"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <label for="allow_comments" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-comments text-blue-500 mr-1"></i>
                                    Allow Comments
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

             {{-- Category & Tags --}}
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
        <i class="fas fa-tags text-purple-500 mr-2"></i>
        Categories & Tags
    </h3>

    <div class="space-y-4">
        {{-- Multi-select Categories --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Categories
                <span class="text-gray-400 font-normal">(Select multiple)</span>
            </label>
            
            {{-- Selected Categories Display --}}
            @if(count($category_ids) > 0)
                <div class="flex flex-wrap gap-2 mb-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    @foreach($category_ids as $index => $categoryId)
                        @php $category = $categories->find($categoryId); @endphp
                        @if($category)
                            <span class="px-3 py-1 text-sm rounded-full flex items-center"
                                  style="background-color: {{ $category->color }}20; color: {{ $category->color }}; border: 1px solid {{ $category->color }}40;">
                                <i class="fas fa-folder mr-1"></i>
                                {{ $category->name }}
                                <button type="button" 
                                        wire:click="$set('category_ids.{{ $index }}', null)"
                                        class="ml-2 hover:text-red-500">
                                    <i class="fas fa-times"></i>
                                </button>
                            </span>
                        @endif
                    @endforeach
                </div>
            @endif
            
            {{-- Category Selection Dropdown --}}
            <div class="relative">
                <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        onchange="addCategory(this.value); this.value='';">
                    <option value="">Select a category to add...</option>
                    @foreach($categories as $category)
                        @if(!in_array($category->id, $category_ids))
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            
            @if(count($categories) === 0)
                <p class="text-sm text-gray-500 mt-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    No categories available. <a href="{{ route('admin.blog.categories.index') }}" class="text-blue-600 hover:underline">Create categories first</a>.
                </p>
            @endif
        </div>

        {{-- Tags --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Tags
                <span class="text-gray-400 font-normal">(Free-form keywords)</span>
            </label>
            <div class="flex flex-wrap gap-2 mb-2">
                @foreach($tags as $index => $tag)
                    <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm flex items-center">
                        #{{ $tag }}
                        <button type="button" wire:click="removeTag({{ $index }})"
                            class="ml-2 text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </span>
                @endforeach
            </div>
            <div class="flex">
                <input type="text" wire:model="newTag" wire:keydown.enter.prevent="addTag"
                    class="flex-1 px-4 py-2 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    placeholder="Add tag...">
                <button type="button" wire:click="addTag"
                    class="px-4 py-2 bg-gray-600 text-white rounded-r-lg hover:bg-gray-700">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
            <p class="text-sm text-gray-500 mt-2">
                <i class="fas fa-lightbulb mr-1"></i>
                Tags help users find related content and improve SEO
            </p>
        </div>
    </div>
</div>


{{-- Featured Image --}}
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
        <i class="fas fa-image text-green-500 mr-2"></i>
        Featured Image
    </h3>

    {{-- Show current/existing image --}}
    @if($existing_image && !$removeExistingImage)
        <div class="mb-4">
            <div class="relative group">
                <img src="{{ Storage::url($existing_image) }}" alt="Current featured image"
                    class="w-full h-48 object-cover rounded-lg">
                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-opacity rounded-lg flex items-center justify-center">
                    <button type="button" wire:click="toggleRemoveImage"
                        class="bg-red-500 text-white p-2 rounded-full hover:bg-red-600 transition-colors opacity-0 group-hover:opacity-100"
                        title="Remove image">
                        <i class="fas fa-trash text-sm"></i>
                    </button>
                </div>
            </div>
            <p class="text-sm text-green-600 mt-2">
                <i class="fas fa-check-circle mr-1"></i>
                Current featured image
            </p>
        </div>
    @endif

    {{-- Show removal confirmation --}}
    @if($removeExistingImage)
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
            <div class="flex items-center justify-between">
                <p class="text-sm text-red-800">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Image will be removed when you save the post.
                </p>
                <button type="button" wire:click="toggleRemoveImage"
                    class="text-sm text-red-600 hover:text-red-800 font-medium">
                    <i class="fas fa-undo mr-1"></i>
                    Keep image
                </button>
            </div>
        </div>
    @endif

    {{-- Show new image preview --}}
    @if($featured_image && is_object($featured_image) && method_exists($featured_image, 'temporaryUrl'))
        <div class="mb-4">
            <img src="{{ $featured_image->temporaryUrl() }}" alt="Preview"
                class="w-full h-48 object-cover rounded-lg">
            <p class="text-sm text-blue-600 mt-2">
                <i class="fas fa-upload mr-1"></i>
                New image {{ $existing_image ? '(will replace current)' : '' }}
            </p>
        </div>
    @endif

    {{-- File upload input --}}
    @if(!$removeExistingImage)
        <div>
            <input type="file" wire:model="featured_image" accept="image/*"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">

            @if($existing_image)
                <p class="text-sm text-gray-500 mt-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    Choose a new image to replace the current one, or leave empty to keep it.
                </p>
            @else
                <p class="text-sm text-gray-500 mt-2">
                    <i class="fas fa-upload mr-1"></i>
                    Upload a featured image for your post (recommended: 1200x630px, max 2MB)
                </p>
            @endif

            @error('featured_image')
                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
            @enderror
        </div>
    @endif
</div>


                {{-- Action Buttons --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="space-y-3">
                        <button type="submit"
                            class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium focus:ring-2 focus:ring-blue-500">
                            <i class="fas fa-save mr-2"></i>
                            {{ $isEdit ? 'Update Post' : 'Create Post' }}
                        </button>

                        <button type="button" wire:click="saveDraft"
                            class="w-full px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                            <i class="fas fa-file-alt mr-2"></i>
                            Save as Draft
                        </button>

                        @if($status !== 'published')
                            <button type="button" wire:click="publish"
                                class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                <i class="fas fa-globe mr-2"></i>
                                Publish Now
                            </button>
                        @endif

                        @if($isEdit && $post && $post->slug)
                            <a href="{{ route('blog.show', $post->slug) }}" target="_blank"
                                class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-center block">
                                <i class="fas fa-external-link-alt mr-2"></i>
                                Preview Post
                            </a>
                        @endif
                    </div>

                    {{-- Quick Stats (for edit mode) --}}
                    @if($isEdit && $post)
                        <div class="border-t border-gray-200 dark:border-gray-700 mt-6 pt-4">
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Post Statistics</h4>
                            <div class="grid grid-cols-3 gap-4 text-center">
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                                    <div class="text-lg font-bold text-gray-900 dark:text-white">
                                        {{ number_format($post->views_count) }}</div>
                                    <div class="text-xs text-gray-500">Views</div>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                                    <div class="text-lg font-bold text-gray-900 dark:text-white">
                                        {{ number_format($post->likes_count) }}</div>
                                    <div class="text-xs text-gray-500">Likes</div>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                                    <div class="text-lg font-bold text-gray-900 dark:text-white">
                                        {{ number_format($post->comments_count) }}</div>
                                    <div class="text-xs text-gray-500">Comments</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </form>

    {{-- Trix Editor Styling --}}
    <style>
        trix-editor {
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            padding: 1rem;
            min-height: 400px;
            line-height: 1.6;
        }

        .dark trix-editor {
            border-color: #4b5563;
            background-color: #374151;
            color: white;
        }

        trix-editor:focus {
            border-color: #3b82f6;
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* Improve Trix toolbar styling */
        trix-toolbar .trix-button-group {
            margin-bottom: 5px;
        }

        trix-toolbar .trix-button {
            background: white;
            border: 1px solid #d1d5db;
            margin-right: 2px;
        }

        trix-toolbar .trix-button:hover {
            background: #f3f4f6;
        }

        trix-toolbar .trix-button.trix-active {
            background: #3b82f6;
            color: white;
        }
    </style>

   {{-- JavaScript for multi-category functionality --}}
<script>
    function addCategory(categoryId) {
        if (categoryId) {
            // Get current category_ids array
            let currentCategories = @this.get('category_ids') || [];
            
            // Add new category if not already present
            if (!currentCategories.includes(parseInt(categoryId))) {
                currentCategories.push(parseInt(categoryId));
                @this.set('category_ids', currentCategories);
            }
        }
    }

    // Enhanced Trix editor initialization
    document.addEventListener('livewire:init', () => {
        function initializeTrix() {
            const trixEditor = document.querySelector('trix-editor');
            const contentInput = document.getElementById('content');

            if (trixEditor && contentInput) {
                // Set initial content
                if (contentInput.value) {
                    trixEditor.editor.loadHTML(contentInput.value);
                }

                // Handle content changes
                let timeout;
                trixEditor.addEventListener('trix-change', function(event) {
                    clearTimeout(timeout);
                    timeout = setTimeout(() => {
                        @this.set('content', event.target.innerHTML, false);
                    }, 300);
                });

                // Handle blur events  
                trixEditor.addEventListener('trix-blur', function(event) {
                    @this.set('content', event.target.innerHTML, false);
                });
            }
        }

        initializeTrix();
        document.addEventListener('livewire:navigated', initializeTrix);
    });

    // Ensure content is captured before form submission
    document.addEventListener('livewire:morph', () => {
        const trixEditor = document.querySelector('trix-editor');
        if (trixEditor) {
            @this.set('content', trixEditor.innerHTML, false);
        }
    });
</script>
</div>