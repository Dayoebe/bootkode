<div class="space-y-6 animate__animated animate__fadeIn" x-data="{
    activeTab: 'content',
    showMediaDeleteConfirm: null,
    previewUrl: null,
    previewType: null
}" wire:key="lesson-editor-{{ $lessonId }}">

    <!-- Lesson Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-white">
                <i class="fas fa-book-open text-blue-400 mr-2"></i>
                Lesson Editor
            </h2>
            <p class="text-gray-400">
                Section: <span class="text-blue-300">{{ $lesson->section->title ?? 'No Section' }}</span>
            </p>
        </div>
        <button wire:click="saveLesson"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg flex items-center gap-2 transition-colors">
            <i class="fas fa-save"></i> Save Lesson
        </button>
    </div>

    <!-- Navigation Tabs -->
    <div class="border-b border-gray-700">
        <nav class="-mb-px flex space-x-8">
            <button @click="activeTab = 'content'"
                :class="{ 'border-blue-500 text-blue-400': activeTab === 'content', 'border-transparent text-gray-400 hover:text-gray-300': activeTab !== 'content' }"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
                <i class="fas fa-align-left"></i> Content
            </button>
            <button @click="activeTab = 'media'"
                :class="{ 'border-pink-500 text-blue-400': activeTab === 'media', 'border-transparent text-gray-400 hover:text-gray-300': activeTab !== 'media' }"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
                <i class="fas fa-photo-video"></i> Media & Files
            </button>
            <button @click="activeTab = 'settings'"
                :class="{ 'border-red-500 text-blue-400': activeTab === 'settings', 'border-transparent text-gray-400 hover:text-gray-300': activeTab !== 'settings' }"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
                <i class="fas fa-cog"></i> Settings
            </button>
            <button @click="activeTab = 'assessment'"
            :class="{ 'border-purple-500 text-blue-400': activeTab === 'assessment', 'border-transparent text-gray-400 hover:text-gray-300': activeTab !== 'assessment' }"
            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
            <i class="fas fa-book"></i> Assessment
            </button>
        </button>
        </nav>
    </div>

    <!-- Content Tab -->
    <div x-show="activeTab === 'content'" class="space-y-6 animate__animated animate__fadeIn">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 my-3">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Lesson Title *</label>
                <input type="text" wire:model.debounce.500ms="title"
                    class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                @error('title')
                    <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">URL Slug *</label>
                <div class="flex gap-2">
                    <input type="text" wire:model="slug"
                        class="flex-1 px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <button wire:click="generateSlug"
                        class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg"
                        title="Generate from title">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>
                @error('slug')
                    <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Description -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">Description</label>
            <textarea wire:model="description" rows="3"
                class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"></textarea>
            @error('description')
                <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
            @enderror
        </div>

        <!-- Content Editor -->
        <div>
            <div class="flex justify-between items-center mb-2">
                <label class="block text-sm font-medium text-gray-300">Lesson Content</label>
                <div class="flex gap-2">
                    <button @click="$refs.trixEditor.editor.insertHTML('<h2>Heading</h2>')"
                        class="px-3 py-1 bg-gray-700 hover:bg-gray-600 rounded text-sm">
                        <i class="fas fa-heading mr-1"></i> Heading
                    </button>
                    <button @click="$refs.trixEditor.editor.insertHTML('<p>Paragraph text...</p>')"
                        class="px-3 py-1 bg-gray-700 hover:bg-gray-600 rounded text-sm">
                        <i class="fas fa-paragraph mr-1"></i> Paragraph
                    </button>
                </div>
            </div>
            <div class="border border-gray-700 rounded-lg overflow-hidden shadow-lg">
                <trix-editor wire:model="content" x-ref="trixEditor" placeholder="Start writing your lesson content here..."
                    class="trix-content bg-gray-800 text-white min-h-[300px] p-4"></trix-editor>
            </div>
            @error('content')
                <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <!-- Media Tab -->
    <div x-show="activeTab === 'media'" class="space-y-6 my-2">
        <!-- Video URL -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">YouTube Video URL</label>
            <div class="flex gap-2">
                <input type="url" wire:model="video_url" placeholder="https://www.youtube.com/watch?v=..."
                    class="flex-1 px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                @if ($video_url)
                    <button @click="previewUrl = '{{ $video_url }}'; previewType = 'video'"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                        Preview
                    </button>
                @endif
            </div>
            @error('video_url')
                <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
            @enderror
        </div>
        
        <!-- Image Upload -->
        <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
            <h3 class="text-lg font-medium text-white mb-4 flex items-center gap-2">
                <i class="fas fa-image text-blue-400"></i> Featured Image
            </h3>

            @if ($lesson->image_path)
                <div class="flex items-center gap-4 mb-4">
                    <img src="{{ Storage::url($lesson->image_path) }}" alt="Lesson image"
                        class="w-32 h-32 object-cover rounded-lg border border-gray-700">
                    <div class="space-y-2">
                        <button wire:click="deleteImage"
                            class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-sm">
                            Remove Image
                        </button>
                        <button
                            @click="previewUrl = '{{ Storage::url($lesson->image_path) }}'; previewType = 'image'"
                            class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm">
                            Preview Image
                        </button>
                    </div>
                </div>
            @endif

            <div class="flex items-center gap-2">
                <input type="file" wire:model="imageUpload" id="imageUpload" class="hidden">
                <label for="imageUpload"
                    class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg cursor-pointer">
                    Choose Image
                </label>
                @if ($imageUpload)
                    <span class="text-gray-300">{{ $imageUpload->getClientOriginalName() }}</span>
                    <button wire:click="uploadImage"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">
                        Upload
                    </button>
                @endif
            </div>
            @error('imageUpload')
                <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
            @enderror
        </div>

        <!-- Audio Upload -->
        <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
            <h3 class="text-lg font-medium text-white mb-4 flex items-center gap-2">
                <i class="fas fa-music text-blue-400"></i> Audio File
            </h3>

            @if ($lesson->audio_path)
                <div class="flex items-center gap-4 mb-4">
                    <audio controls class="w-full max-w-md">
                        <source src="{{ Storage::url($lesson->audio_path) }}" type="audio/mpeg">
                    </audio>
                    <button wire:click="deleteAudio"
                        class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-sm">
                        Remove Audio
                    </button>
                </div>
            @endif

            <div class="flex items-center gap-2">
                <input type="file" wire:model="audioUpload" id="audioUpload" class="hidden"
                    accept="audio/*">
                <label for="audioUpload"
                    class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg cursor-pointer">
                    Choose Audio File
                </label>
                @if ($audioUpload)
                    <span class="text-gray-300">{{ $audioUpload->getClientOriginalName() }}</span>
                    <button wire:click="uploadAudio"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">
                        Upload
                    </button>
                @endif
            </div>
            @error('audioUpload')
                <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <!-- Settings Tab -->
    <div x-show="activeTab === 'settings'" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Duration (minutes)</label>
                <input type="number" wire:model="duration_minutes" min="1" max="600"
                    class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                @error('duration_minutes')
                    <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex items-center">
                <input type="checkbox" wire:model="is_free" id="is_free"
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-700 rounded bg-gray-800">
                <label for="is_free" class="ml-2 block text-sm text-gray-300">
                    Free Lesson (available without enrollment)
                </label>
            </div>
        </div>
    </div>
        <!-- Assessment Tab -->
        <div x-show="activeTab === 'assessment'" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Duration (minutes)</label>
                    <input type="number" wire:model="duration_minutes" min="1" max="600"
                        class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('duration_minutes')
                        <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>
    
                <div class="flex items-center">
                    <input type="checkbox" wire:model="is_free" id="is_free"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-700 rounded bg-gray-800">
                    <label for="is_free" class="ml-2 block text-sm text-gray-300">
                        Free Lesson (available without enrollment)
                    </label>
                </div>
            </div>
        </div>

    <!-- Preview Modal -->
    <div x-show="previewUrl" x-transition.opacity class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50" style="display: none"
        :style="previewUrl ? 'display: flex' : ''" @keydown.escape="previewUrl = null">
        <div class="bg-gray-800 rounded-xl p-6 max-w-4xl w-full border border-gray-700">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-white">Preview</h3>
                <button @click="previewUrl = null" class="text-gray-400 hover:text-white p-1">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="max-h-[80vh] overflow-auto">
                <template x-if="previewType === 'video'">
                    <div class="aspect-w-16 aspect-h-9">
                        <iframe class="w-full h-[500px]"
                            :src="'https://www.youtube.com/embed/' + previewUrl.split('v=')[1].split('&')[0]"
                            frameborder="0" allowfullscreen></iframe>
                    </div>
                </template>
                <template x-if="previewType === 'image'">
                    <img :src="previewUrl" alt="Preview" class="max-w-full mx-auto rounded-lg">
                </template>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .trix-content {
                color: #f3f4f6;
                font-size: 1rem;
                line-height: 1.5;
            }

            .trix-content h1 {
                font-size: 1.5rem;
                font-weight: bold;
                margin: 1rem 0;
            }

            .trix-content h2 {
                font-size: 1.3rem;
                font-weight: bold;
                margin: 0.8rem 0;
            }

            .trix-content a {
                color: #60a5fa;
                background-color: white;
                text-decoration: underline;
            }

            .trix-content img {
                max-width: 100%;
                height: auto;
            }

            .trix-button-group--file-tools {
                display: none !important;
            }
        </style>
    @endpush
</div>