{{-- resources/views/livewire/blog/admin-blog-post-form.blade.php --}}
<div>
    {{-- Error/Success Messages --}}
    @if (session()->has('message'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            <i class="fas fa-exclamation-circle mr-2"></i>
            {{ session('error') }}
        </div>
    @endif

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
                            <input type="text" wire:model.blur="title"
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

                {{-- Content Editor - FIXED --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Content <span class="text-red-500">*</span>
                    </label>
                    <div class="mb-4">
                        <div wire:ignore>
                            <input id="trix-content" type="hidden" name="content" value="{{ $content }}">
                            <trix-editor input="trix-content" class="prose max-w-none trix-content"
                                style="min-height: 400px;" placeholder="Start writing your blog post...">
                            </trix-editor>
                        </div>
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
                                </label>
                                <input type="text" wire:model="meta_title"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="Auto-filled from post title">
                                @error('meta_title')<span
                                class="text-red-500 text-sm mt-1 block">{{ $message }}</span>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Meta Description
                                </label>
                                <textarea wire:model="meta_description" rows="3"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="Brief description for search engines..."></textarea>
                                @error('meta_description')<span
                                class="text-red-500 text-sm mt-1 block">{{ $message }}</span>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Keywords
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
                                @error('published_at')<span
                                class="text-red-500 text-sm mt-1 block">{{ $message }}</span>@enderror
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

                {{-- FIXED: Multiple Categories --}}
{{-- Multiple Categories --}}
<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
        <i class="fas fa-folder text-purple-500 mr-2"></i>
        Categories & Tags
    </h3>

    <div class="space-y-4">
        {{-- Multiple Category Selection --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Categories <span class="text-red-500">*</span>
                <span class="text-xs text-gray-500 font-normal">(Select one or more)</span>
            </label>
            
            {{-- Selected Categories Display --}}
            @if(count($category_ids) > 0)
                <div class="mb-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="flex flex-wrap gap-2">
                        @foreach($category_ids as $index => $categoryId)
                            @php $selectedCategory = $categories->find($categoryId); @endphp
                            @if($selectedCategory)
                                <span class="px-3 py-1 text-sm rounded-full flex items-center"
                                      style="background-color: {{ $selectedCategory->color }}20; color: {{ $selectedCategory->color }}; border: 1px solid {{ $selectedCategory->color }}40;">
                                    <i class="fas fa-folder mr-1"></i>
                                    {{ $selectedCategory->name }}
                                    @if($index === 0)
                                        <span class="ml-2 px-1.5 py-0.5 bg-white/50 rounded text-xs">Primary</span>
                                    @endif
                                    <button type="button" 
                                            wire:click="removeCategory({{ $index }})"
                                            class="ml-2 hover:text-red-500">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </span>
                            @endif
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-500 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        The first category is the primary category for URL routing.
                    </p>
                </div>
            @endif
            
            {{-- Category Selection Dropdown --}}
            <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    onchange="if(this.value && !@js($category_ids).includes(parseInt(this.value))) { @this.category_ids.push(parseInt(this.value)); } this.value='';">
                <option value="">Add a category...</option>
                @foreach($categories as $category)
                    @if(!in_array($category->id, $category_ids))
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endif
                @endforeach
            </select>
            
            @if(count($categories) === 0)
                <p class="text-sm text-gray-500 mt-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    No categories available. <a href="{{ route('admin.blog.categories.index') }}" class="text-blue-600 hover:underline">Create categories first</a>.
                </p>
            @endif
            @error('category_ids')<span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>@enderror
        </div>

        {{-- Tags --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Tags
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
        </div>
    </div>
</div>

                {{-- Featured Image --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        <i class="fas fa-image text-green-500 mr-2"></i>
                        Featured Image
                    </h3>

                    @if($existing_image && !$removeExistingImage)
                        <div class="mb-4">
                            <div class="relative group">
                                <img src="{{ Storage::url($existing_image) }}" alt="Current featured image"
                                    class="w-full h-48 object-cover rounded-lg">
                                <div
                                    class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-opacity rounded-lg flex items-center justify-center">
                                    <button type="button" wire:click="toggleRemoveImage"
                                        class="bg-red-500 text-white p-2 rounded-full hover:bg-red-600 transition-colors opacity-0 group-hover:opacity-100">
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

                    @if($removeExistingImage)
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                            <div class="flex items-center justify-between">
                                <p class="text-sm text-red-800">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    Image will be removed when you save.
                                </p>
                                <button type="button" wire:click="toggleRemoveImage"
                                    class="text-sm text-red-600 hover:text-red-800 font-medium">
                                    <i class="fas fa-undo mr-1"></i>
                                    Keep
                                </button>
                            </div>
                        </div>
                    @endif

                    @if($featured_image)
                        <div class="mb-4" wire:loading.remove wire:target="featured_image">
                            @if(is_object($featured_image) && method_exists($featured_image, 'temporaryUrl'))
                                <img src="{{ $featured_image->temporaryUrl() }}" alt="Preview"
                                    class="w-full h-48 object-cover rounded-lg">
                                <p class="text-sm text-blue-600 mt-2">
                                    <i class="fas fa-upload mr-1"></i>
                                    New image
                                </p>
                            @endif
                        </div>
                        <div class="mb-4" wire:loading wire:target="featured_image">
                            <div class="w-full h-48 bg-gray-200 rounded-lg flex items-center justify-center">
                                <i class="fas fa-spinner fa-spin text-3xl text-gray-400"></i>
                            </div>
                            <p class="text-sm text-blue-600 mt-2">Uploading...</p>
                        </div>
                    @endif

                    @if(!$removeExistingImage)
                        <div>
                            <input type="file" wire:model="featured_image" accept="image/*"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-sm">
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
                            class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="save">
                                <i class="fas fa-save mr-2"></i>
                                {{ $isEdit ? 'Update Post' : 'Create Post' }}
                            </span>
                            <span wire:loading wire:target="save">
                                <i class="fas fa-spinner fa-spin mr-2"></i>
                                Saving...
                            </span>
                        </button>

                        <button type="button" wire:click="saveDraft"
                            class="w-full px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="saveDraft">
                                <i class="fas fa-file-alt mr-2"></i>
                                Save as Draft
                            </span>
                            <span wire:loading wire:target="saveDraft">
                                <i class="fas fa-spinner fa-spin mr-2"></i>
                                Saving...
                            </span>
                        </button>

                        @if($status !== 'published')
                            <button type="button" wire:click="publish"
                                class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="publish">
                                    <i class="fas fa-globe mr-2"></i>
                                    Publish Now
                                </span>
                                <span wire:loading wire:target="publish">
                                    <i class="fas fa-spinner fa-spin mr-2"></i>
                                    Publishing...
                                </span>
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

    {{-- Trix Styling --}}
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

    {{-- FIXED: Improved Trix Editor JavaScript --}}
    @script
    <script>
        let trixEditor = null;
        let hiddenInput = null;
    
        function syncTrixContent() {
            if (trixEditor && hiddenInput) {
                const content = trixEditor.innerHTML;
                hiddenInput.value = content;
                @this.content = content;
                console.log('Content synced, length:', content.length);
                return content;
            }
            return null;
        }
    
        function initTrixEditor() {
            trixEditor = document.querySelector('trix-editor');
            hiddenInput = document.getElementById('trix-content');
            
            if (!trixEditor || !hiddenInput) {
                console.error('Trix elements not found');
                return;
            }
    
            // Load initial content
            const initialContent = hiddenInput.value || '';
            if (initialContent && trixEditor.editor) {
                trixEditor.editor.loadHTML(initialContent);
            }
    
            // Sync content on change (debounced)
            let timeout;
            trixEditor.addEventListener('trix-change', function(e) {
                clearTimeout(timeout);
                timeout = setTimeout(syncTrixContent, 500);
            });
    
            // CRITICAL: Sync on blur
            trixEditor.addEventListener('trix-blur', syncTrixContent);
    
            console.log('Trix editor initialized');
        }
    
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', initTrixEditor);
    
        // Re-initialize after Livewire updates
        document.addEventListener('livewire:navigated', initTrixEditor);
        
        Livewire.hook('morph.updated', () => {
            setTimeout(initTrixEditor, 100);
        });
    
        // CRITICAL: Intercept form submission to sync content first
        Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
            // Sync content before any Livewire action
            syncTrixContent();
        });
    </script>
    @endscript
</div>