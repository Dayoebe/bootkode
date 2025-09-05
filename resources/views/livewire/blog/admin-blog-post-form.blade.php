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
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Title</label>
                            <input type="text" wire:model.live="title"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white text-lg"
                                placeholder="Enter post title...">
                            @error('title')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Slug</label>
                                <button type="button" wire:click="generateSlug"
                                    class="text-sm text-blue-600 hover:text-blue-800">
                                    Generate from title
                                </button>
                            </div>
                            <input type="text" wire:model="slug"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                placeholder="post-slug-url">
                            @error('slug')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                {{-- Content Editor --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6" wire:ignore>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Content</label>
                    <div class="mb-4">
                        <input id="content" type="hidden" name="content" value="{{ $content }}">
                        <trix-editor input="content" class="prose max-w-none" style="min-height: 400px;"></trix-editor>
                    </div>
                    @error('content')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                {{-- JavaScript to handle Trix editor initialization --}}
                <script>
                    document.addEventListener('livewire:init', () => {
                        // Initialize Trix editor with existing content
                        const trixEditor = document.querySelector('trix-editor');
                        const contentInput = document.getElementById('content');

                        if (trixEditor && contentInput) {
                            // Set the initial content
                            trixEditor.editor.loadHTML(contentInput.value);

                            // Update Livewire when content changes
                            trixEditor.addEventListener('trix-change', function (event) {
                                @this.set('content', event.target.innerHTML);
                            });

                            trixEditor.addEventListener('trix-blur', function (event) {
                                @this.set('content', event.target.innerHTML);
                            });
                        }
                    });

                    // Also initialize when Livewire finishes rendering
                    document.addEventListener('livewire:navigated', () => {
                        const trixEditor = document.querySelector('trix-editor');
                        const contentInput = document.getElementById('content');

                        if (trixEditor && contentInput) {
                            trixEditor.editor.loadHTML(contentInput.value);
                        }
                    });
                </script>

                {{-- Excerpt --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Excerpt
                        (Optional)</label>
                    <textarea wire:model="excerpt" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="Brief description of your post..."></textarea>
                    <p class="text-sm text-gray-500 mt-2">Leave empty to auto-generate from content.</p>
                    @error('excerpt')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                {{-- SEO Settings --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
                    <button type="button" wire:click="$toggle('showSeoSection')"
                        class="w-full flex items-center justify-between p-6 text-left">
                        <span class="text-lg font-medium text-gray-900 dark:text-white">SEO Settings</span>
                        <i class="fas fa-chevron-{{ $showSeoSection ? 'up' : 'down' }} text-gray-400"></i>
                    </button>

                    @if($showSeoSection)
                        <div class="px-6 pb-6 border-t border-gray-200 dark:border-gray-700 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Meta
                                    Title</label>
                                <input type="text" wire:model="meta_title"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="SEO title (60 chars max)">
                                <p class="text-sm text-gray-500 mt-1">{{ strlen($meta_title) }}/60 characters</p>
                                @error('meta_title')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Meta
                                    Description</label>
                                <textarea wire:model="meta_description" rows="3"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    placeholder="SEO description (500 chars max)"></textarea>
                                <p class="text-sm text-gray-500 mt-1">{{ strlen($meta_description) }}/500 characters</p>
                                @error('meta_description')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                            </div>

                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Keywords</label>
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
                                        Add
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
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Publish Settings</h3>

                    <div class="space-y-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                            <select wire:model.live="status"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                                <option value="scheduled">Scheduled</option>
                            </select>
                            @error('status')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                        </div>

                        {{-- FIXED: Only show date/time field for scheduled posts --}}
                        @if($status === 'scheduled')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Schedule Publish Date & Time <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" wire:model="published_at"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                    required>
                                <p class="text-sm text-gray-500 mt-1">Select when this post should be published</p>
                                @error('published_at')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                            </div>
                        @endif

                        <div class="flex items-center">
                            <input type="checkbox" wire:model="is_featured"
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <label class="ml-2 text-sm text-gray-700 dark:text-gray-300">Featured Post</label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" wire:model="allow_comments"
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <label class="ml-2 text-sm text-gray-700 dark:text-gray-300">Allow Comments</label>
                        </div>
                    </div>
                </div>

                {{-- Category & Tags --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Category & Tags</h3>

                    <div class="space-y-4">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
                            <select wire:model="category_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tags</label>
                            <div class="flex flex-wrap gap-2 mb-2">
                                @foreach($tags as $index => $tag)
                                    <span
                                        class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm flex items-center">
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
                                    Add
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Featured Image - FIXED to show previous image properly --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Featured Image</h3>

                    {{-- Show current/existing image --}}
                    @if($existing_image && !$featured_image)
                        <div class="mb-4">
                            <img src="{{ Storage::url($existing_image) }}" alt="Current featured image"
                                class="w-full h-48 object-cover rounded-lg">
                            <p class="text-sm text-gray-500 mt-2">
                                <i class="fas fa-image mr-1"></i>
                                Current featured image
                            </p>
                        </div>
                    @endif

                    <div>
                        <input type="file" wire:model="featured_image" accept="image/*"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">

                        @if($isEdit && $existing_image)
                            <p class="text-sm text-gray-500 mt-2">
                                <i class="fas fa-info-circle mr-1"></i>
                                Leave empty to keep current image, or select new image to replace it.
                            </p>
                        @endif

                        @error('featured_image')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror

                        {{-- Show preview for new uploads --}}
                        @if($featured_image && is_object($featured_image))
                            <div class="mt-4">
                                <img src="{{ $featured_image->temporaryUrl() }}" alt="Preview"
                                    class="w-full h-48 object-cover rounded-lg">
                                <p class="text-sm text-green-600 mt-2">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    New image preview {{ $existing_image ? '(will replace current image)' : '' }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <div class="space-y-3">
                        <button type="submit"
                            class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
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
                                <i class="fas fa-upload mr-2"></i>
                                Publish Now
                            </button>
                        @endif

                        @if($isEdit && $post->slug)
                            <a href="{{ route('blog.show', $post->slug) }}" target="_blank"
                                class="w-full px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors text-center block">
                                <i class="fas fa-external-link-alt mr-2"></i>
                                Preview Post
                            </a>
                        @endif
                    </div>
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
        }

        .dark trix-editor {
            border-color: #4b5563;
            background-color: #374151;
            color: white;
        }
    </style>

    {{-- JavaScript to handle Trix editor content synchronization --}}
    <script>
        document.addEventListener('livewire:init', () => {
            // Handle Trix editor content updates
            document.addEventListener('trix-change', function (event) {
                @this.set('content', event.target.innerHTML);
            });

            document.addEventListener('trix-blur', function (event) {
                @this.set('content', event.target.innerHTML);
            });

            // Before form submission, capture Trix content
            document.addEventListener('submit', function (event) {
                const trixEditor = document.querySelector('trix-editor');
                if (trixEditor) {
                    @this.set('content', trixEditor.innerHTML);
                }
            });
        });

        // Capture content before any Livewire request
        document.addEventListener('livewire:morph', () => {
            const trixEditor = document.querySelector('trix-editor');
            if (trixEditor) {
                @this.set('content', trixEditor.innerHTML);
            }
        });
    </script>
</div>