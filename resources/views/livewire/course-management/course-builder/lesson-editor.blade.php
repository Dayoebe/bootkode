<div x-data="lessonEditorManager()" x-init="init()">
    <!-- Auto-save Status Indicator -->
    <div x-show="showAutoSaveStatus" x-transition.opacity class="fixed top-4 right-4 z-40">
        <div class="px-3 py-2 rounded-lg shadow-lg text-sm font-medium"
             :class="autoSaveStatus === 'saving' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' :
                    autoSaveStatus === 'saved' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' :
                    'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300'">
            <div x-show="autoSaveStatus === 'saving'" class="flex items-center gap-2">
                <div class="w-3 h-3 border-2 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                Saving...
            </div>
            <div x-show="autoSaveStatus === 'saved'" class="flex items-center gap-2">
                <i class="fas fa-check-circle"></i>
                All changes saved
            </div>
            <div x-show="autoSaveStatus === 'error'" class="flex items-center gap-2">
                <i class="fas fa-exclamation-triangle"></i>
                Save failed - <button @click="retrySave()" class="underline">retry</button>
            </div>
        </div>
    </div>

    <div class="space-y-4" wire:key="lesson-editor-{{ $lessonId }}">

        <!-- Success Message -->
        @if (session()->has('success'))
            <div x-data="{ show: true }" x-show="show" x-transition.opacity 
                 class="bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 border border-green-200 dark:border-green-700 p-4 rounded-lg flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
                <button @click="show = false" class="text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        @endif

        <!-- Lesson Header -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 transition-colors duration-300">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white transition-colors duration-300 truncate">
                        <i class="fas fa-book-open text-blue-600 dark:text-blue-400 mr-2"></i>
                        {{ $lesson->title ?? 'Lesson Editor' }}
                    </h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 transition-colors duration-300 truncate">
                        Section: <span class="text-blue-600 dark:text-blue-300">{{ $lesson->section->title ?? 'No Section' }}</span>
                    </p>
                </div>
                <div class="flex gap-2 flex-shrink-0">
                    <button @click="performQuickSave()" :disabled="isSaving" 
                        class="px-3 py-2 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white rounded-lg flex items-center gap-2 transition-colors duration-300 text-sm">
                        <i class="fas fa-bolt" x-show="!isSaving"></i>
                        <i class="fas fa-spinner fa-spin" x-show="isSaving"></i>
                        <span class="hidden sm:inline">Quick Save</span>
                    </button>
                    <button wire:click="saveLesson" wire:loading.attr="disabled"
                        class="px-3 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white rounded-lg flex items-center gap-2 transition-colors duration-300 text-sm">
                        <i class="fas fa-save" wire:loading.remove></i>
                        <i class="fas fa-spinner fa-spin" wire:loading></i>
                        <span wire:loading.remove class="hidden sm:inline">Save All</span>
                        <span wire:loading class="hidden sm:inline">Saving...</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 transition-colors duration-300">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-4 px-4 overflow-x-auto">
                    <button @click="switchTab('content')" :disabled="isSaving"
                        :class="{ 'border-blue-500 text-blue-600 dark:text-blue-400': activeTab === 'content', 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-300': activeTab !== 'content' }"
                        class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm flex items-center gap-2 transition-colors duration-300 disabled:opacity-50 relative">
                        <i class="fas fa-align-left"></i> 
                        <span class="hidden sm:inline">Content</span>
                        <div x-show="hasUnsavedContent && (titleChanged || descriptionChanged || contentChanged)" class="w-2 h-2 bg-orange-500 rounded-full"></div>
                    </button>
                    <button @click="switchTab('media')" :disabled="isSaving"
                        :class="{ 'border-pink-500 text-pink-600 dark:text-pink-400': activeTab === 'media', 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-300': activeTab !== 'media' }"
                        class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm flex items-center gap-2 transition-colors duration-300 disabled:opacity-50 relative">
                        <i class="fas fa-photo-video"></i> 
                        <span class="hidden sm:inline">Media</span>
                        <div x-show="hasUnsavedContent && mediaChanged" class="w-2 h-2 bg-orange-500 rounded-full"></div>
                    </button>
                    <button @click="switchTab('settings')" :disabled="isSaving"
                        :class="{ 'border-green-500 text-green-600 dark:text-green-400': activeTab === 'settings', 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-300': activeTab !== 'settings' }"
                        class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm flex items-center gap-2 transition-colors duration-300 disabled:opacity-50 relative">
                        <i class="fas fa-cog"></i> 
                        <span class="hidden sm:inline">Settings</span>
                        <div x-show="hasUnsavedContent && settingsChanged" class="w-2 h-2 bg-orange-500 rounded-full"></div>
                    </button>
                    <button @click="switchTab('assessment')" :disabled="isSaving"
                        :class="{ 'border-purple-500 text-purple-600 dark:text-purple-400': activeTab === 'assessment', 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-300': activeTab !== 'assessment' }"
                        class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm flex items-center gap-2 transition-colors duration-300 disabled:opacity-50 relative">
                        <i class="fas fa-clipboard-check"></i> 
                        <span class="hidden sm:inline">Assessment</span>
                    </button>
                </nav>
            </div>

            <!-- Content Tab -->
            <div x-show="activeTab === 'content'" class="p-4 space-y-4">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-300">
                            Lesson Title * 
                            <span x-show="titleChanged" class="text-orange-500 text-xs">(unsaved)</span>
                        </label>
                        <input type="text" wire:model.live.debounce.500ms="title" @input="markFieldChanged('title')"
                            class="w-full px-3 py-2 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300 text-sm"
                            :class="{ 'border-orange-400': titleChanged }">
                        @error('title')
                            <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-300">
                            URL Slug *
                            <span x-show="slugChanged" class="text-orange-500 text-xs">(unsaved)</span>
                        </label>
                        <div class="flex gap-2">
                            <input type="text" wire:model="slug" @input="markFieldChanged('slug')"
                                class="flex-1 px-3 py-2 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300 text-sm"
                                :class="{ 'border-orange-400': slugChanged }">
                            <button wire:click="generateSlug" wire:loading.attr="disabled" :disabled="isSaving"
                                class="px-3 py-2 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 text-gray-800 dark:text-white rounded-lg disabled:opacity-50 transition-colors duration-300 flex-shrink-0 text-sm"
                                title="Generate from title">
                                <i class="fas fa-sync-alt" wire:loading.remove></i>
                                <i class="fas fa-spinner fa-spin" wire:loading></i>
                            </button>
                        </div>
                        @error('slug')
                            <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2 transition-colors duration-300">
                        Description
                        <span x-show="descriptionChanged" class="text-orange-500 text-xs">(unsaved)</span>
                    </label>
                    <textarea wire:model="description" @input="markFieldChanged('description')" rows="3"
                        class="w-full px-3 py-2 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300 text-sm"
                        :class="{ 'border-orange-400': descriptionChanged }"
                        placeholder="Brief description of this lesson..."></textarea>
                    @error('description')
                        <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Content Editor -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 transition-colors duration-300">
                            Lesson Content
                            <span x-show="contentChanged" class="text-orange-500 text-xs">(unsaved changes)</span>
                        </label>
                        <div class="flex gap-1 text-xs">
                            <button @click="insertTemplate('intro')" class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                <i class="fas fa-play mr-1"></i>Intro
                            </button>
                            <button @click="insertTemplate('objectives')" class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                <i class="fas fa-bullseye mr-1"></i>Goals
                            </button>
                            <button @click="insertTemplate('summary')" class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                <i class="fas fa-clipboard-list mr-1"></i>Summary
                            </button>
                        </div>
                    </div>
                    <div wire:ignore.self class="border border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden shadow-sm"
                         :class="{ 'border-orange-400': contentChanged }">
                        <trix-editor wire:model.live.debounce.1500ms="content" x-ref="trixEditor" @trix-change="markFieldChanged('content')"
                            placeholder="Start writing your lesson content here..."
                            class="trix-content bg-white dark:bg-gray-700 text-gray-900 dark:text-white min-h-[300px] p-3 transition-colors duration-300 text-sm"
                            wire:key="trix-{{ $lessonId }}"
                            aria-label="Lesson Content Editor">
                        </trix-editor>
                    </div>
                    @error('content')
                        <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Media Tab -->
            <div x-show="activeTab === 'media'" class="p-4 space-y-4">
                <!-- YouTube Video -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600 transition-colors duration-300">
                    <h3 class="text-base font-medium text-gray-800 dark:text-white mb-3 flex items-center gap-2 transition-colors duration-300">
                        <i class="fas fa-video text-red-600 dark:text-red-500"></i> YouTube Video
                    </h3>
                    <div class="flex gap-2">
                        <input type="url" wire:model="video_url" @input="markFieldChanged('media')" 
                               placeholder="https://www.youtube.com/watch?v=..."
                            class="flex-1 px-3 py-2 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300 text-sm">
                        @if ($video_url)
                            <button type="button" onclick="window.open('{{ $video_url }}', '_blank')"
                                class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-300 text-sm flex-shrink-0">
                                Preview
                            </button>
                        @endif
                    </div>
                    @error('video_url')
                        <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- File Upload Sections -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Images -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                        <h4 class="font-medium text-gray-800 dark:text-white mb-3 flex items-center gap-2">
                            <i class="fas fa-image text-green-600"></i> Images
                        </h4>
                        <div class="space-y-2">
                            <input type="file" wire:model="imageUpload" accept="image/*" class="w-full text-sm file:mr-2 file:py-1 file:px-3 file:rounded file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            @if ($imageUpload)
                                <button wire:click="uploadImage" wire:loading.attr="disabled"
                                    class="w-full px-3 py-2 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white rounded text-sm transition-colors">
                                    <span wire:loading.remove wire:target="uploadImage">Upload Image</span>
                                    <span wire:loading wire:target="uploadImage">Uploading...</span>
                                </button>
                            @endif
                        </div>
                        @error('imageUpload')
                            <span class="text-red-600 dark:text-red-400 text-xs mt-1">{{ $message }}</span>
                        @enderror
                        @if (count($images) > 0)
                            <div class="mt-3 space-y-2 max-h-32 overflow-y-auto">
                                @foreach ($images as $index => $image)
                                    <div class="flex items-center justify-between text-xs bg-white dark:bg-gray-600 p-2 rounded">
                                        <span class="truncate">{{ $image['name'] }}</span>
                                        <button wire:click="removeFile('images', {{ $index }})" class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Documents -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                        <h4 class="font-medium text-gray-800 dark:text-white mb-3 flex items-center gap-2">
                            <i class="fas fa-file-alt text-blue-600"></i> Documents
                        </h4>
                        <div class="space-y-2">
                            <input type="file" wire:model="documentUpload" accept=".pdf,.doc,.docx,.txt,.epub,.ppt,.pptx" class="w-full text-sm file:mr-2 file:py-1 file:px-3 file:rounded file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            @if ($documentUpload)
                                <button wire:click="uploadDocument" wire:loading.attr="disabled"
                                    class="w-full px-3 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white rounded text-sm transition-colors">
                                    <span wire:loading.remove wire:target="uploadDocument">Upload Document</span>
                                    <span wire:loading wire:target="uploadDocument">Uploading...</span>
                                </button>
                            @endif
                        </div>
                        @error('documentUpload')
                            <span class="text-red-600 dark:text-red-400 text-xs mt-1">{{ $message }}</span>
                        @enderror
                        @if (count($documents) > 0)
                            <div class="mt-3 space-y-2 max-h-32 overflow-y-auto">
                                @foreach ($documents as $index => $doc)
                                    <div class="flex items-center justify-between text-xs bg-white dark:bg-gray-600 p-2 rounded">
                                        <span class="truncate">{{ $doc['name'] }}</span>
                                        <button wire:click="removeFile('documents', {{ $index }})" class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Audio and Video Files -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Audio Files -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                        <h4 class="font-medium text-gray-800 dark:text-white mb-3 flex items-center gap-2">
                            <i class="fas fa-music text-purple-600"></i> Audio Files
                        </h4>
                        <div class="space-y-2">
                            <input type="file" wire:model="audioUpload" accept=".mp3,.wav,.m4a,.aac" class="w-full text-sm file:mr-2 file:py-1 file:px-3 file:rounded file:border-0 file:text-sm file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                            @if ($audioUpload)
                                <button wire:click="uploadAudio" wire:loading.attr="disabled"
                                    class="w-full px-3 py-2 bg-purple-600 hover:bg-purple-700 disabled:bg-gray-400 text-white rounded text-sm transition-colors">
                                    <span wire:loading.remove wire:target="uploadAudio">Upload Audio</span>
                                    <span wire:loading wire:target="uploadAudio">Uploading...</span>
                                </button>
                            @endif
                        </div>
                        @error('audioUpload')
                            <span class="text-red-600 dark:text-red-400 text-xs mt-1">{{ $message }}</span>
                        @enderror
                        @if (count($audios) > 0)
                            <div class="mt-3 space-y-2 max-h-32 overflow-y-auto">
                                @foreach ($audios as $index => $audio)
                                    <div class="flex items-center justify-between text-xs bg-white dark:bg-gray-600 p-2 rounded">
                                        <span class="truncate">{{ $audio['name'] }}</span>
                                        <button wire:click="removeFile('audios', {{ $index }})" class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Video Files -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                        <h4 class="font-medium text-gray-800 dark:text-white mb-3 flex items-center gap-2">
                            <i class="fas fa-film text-red-600"></i> Video Files
                        </h4>
                        <div class="space-y-2">
                            <input type="file" wire:model="videoUpload" accept=".mp4,.avi,.mov,.wmv" class="w-full text-sm file:mr-2 file:py-1 file:px-3 file:rounded file:border-0 file:text-sm file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                            @if ($videoUpload)
                                <button wire:click="uploadVideo" wire:loading.attr="disabled"
                                    class="w-full px-3 py-2 bg-red-600 hover:bg-red-700 disabled:bg-gray-400 text-white rounded text-sm transition-colors">
                                    <span wire:loading.remove wire:target="uploadVideo">Upload Video</span>
                                    <span wire:loading wire:target="uploadVideo">Uploading...</span>
                                </button>
                            @endif
                        </div>
                        @error('videoUpload')
                            <span class="text-red-600 dark:text-red-400 text-xs mt-1">{{ $message }}</span>
                        @enderror
                        @if (count($videos) > 0)
                            <div class="mt-3 space-y-2 max-h-32 overflow-y-auto">
                                @foreach ($videos as $index => $video)
                                    <div class="flex items-center justify-between text-xs bg-white dark:bg-gray-600 p-2 rounded">
                                        <span class="truncate">{{ $video['name'] }}</span>
                                        <button wire:click="removeFile('videos', {{ $index }})" class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- External Links -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                    <h4 class="font-medium text-gray-800 dark:text-white mb-3 flex items-center gap-2">
                        <i class="fas fa-link text-purple-600"></i> External Links
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 mb-3">
                        <input type="text" wire:model="newLinkTitle" placeholder="Link title" 
                               class="px-3 py-2 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded text-sm">
                        <input type="url" wire:model="newLinkUrl" placeholder="https://example.com" 
                               class="px-3 py-2 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded text-sm">
                        <button wire:click="addExternalLink" wire:loading.attr="disabled"
                            class="px-3 py-2 bg-purple-600 hover:bg-purple-700 disabled:bg-gray-400 text-white rounded text-sm transition-colors">
                            <span wire:loading.remove wire:target="addExternalLink">Add Link</span>
                            <span wire:loading wire:target="addExternalLink">Adding...</span>
                        </button>
                    </div>
                    @error('newLinkTitle')
                        <span class="text-red-600 dark:text-red-400 text-xs">{{ $message }}</span>
                    @enderror
                    @error('newLinkUrl')
                        <span class="text-red-600 dark:text-red-400 text-xs">{{ $message }}</span>
                    @enderror
                    @if (count($external_links) > 0)
                        <div class="space-y-2 max-h-32 overflow-y-auto">
                            @foreach ($external_links as $index => $link)
                                <div class="flex items-center justify-between text-xs bg-white dark:bg-gray-600 p-2 rounded">
                                    <a href="{{ $link['url'] }}" target="_blank" class="truncate text-blue-600 hover:underline">{{ $link['title'] }}</a>
                                    <button wire:click="removeFile('external_links', {{ $index }})" class="text-red-600 hover:text-red-800 ml-2">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Settings Tab -->
            <div x-show="activeTab === 'settings'" class="p-4 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Duration -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Duration (minutes)
                            <span x-show="settingsChanged" class="text-orange-500 text-xs">(unsaved)</span>
                        </label>
                        <input type="number" wire:model="duration_minutes" @input="markFieldChanged('settings')" min="1" max="600"
                            class="w-full px-3 py-2 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors duration-300 text-sm"
                            placeholder="Estimated completion time">
                        @error('duration_minutes')
                            <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                        @enderror
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">How long students should spend on this lesson</p>
                    </div>

                    <!-- Difficulty Level -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Difficulty Level</label>
                        <select wire:model="difficulty_level" @change="markFieldChanged('settings')"
                            class="w-full px-3 py-2 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors duration-300 text-sm">
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                            <option value="expert">Expert</option>
                        </select>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Help students understand the lesson complexity</p>
                    </div>

                    <!-- Completion Time Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Completion Type</label>
                        <select wire:model="completion_time_type" @change="markFieldChanged('settings')"
                            class="w-full px-3 py-2 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors duration-300 text-sm">
                            <option value="reading">Reading</option>
                            <option value="watching">Watching</option>
                            <option value="practice">Practice</option>
                            <option value="total">Total Time</option>
                        </select>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Primary activity type for this lesson</p>
                    </div>

                    <!-- Scheduled Publish -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Scheduled Publish</label>
                        <input type="datetime-local" wire:model="scheduled_publish_at" @change="markFieldChanged('settings')"
                            class="w-full px-3 py-2 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors duration-300 text-sm">
                        @error('scheduled_publish_at')
                            <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                        @enderror
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Optional: Schedule when this lesson becomes available</p>
                    </div>
                </div>

                <!-- Lesson Statistics -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                    <h4 class="font-medium text-gray-800 dark:text-white mb-3">Lesson Statistics</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                        <div>
                            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ count($images) }}</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">Images</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ count($documents) }}</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">Documents</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ count($audios) }}</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">Audio Files</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ count($videos) }}</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">Video Files</div>
                        </div>
                    </div>
                </div>

                <!-- Content Analysis -->
                @if($content)
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                        <h4 class="font-medium text-gray-800 dark:text-white mb-3">Content Analysis</h4>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-center">
                            <div>
                                <div class="text-xl font-bold text-indigo-600 dark:text-indigo-400">{{ str_word_count(strip_tags($content)) }}</div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">Words</div>
                            </div>
                            <div>
                                <div class="text-xl font-bold text-teal-600 dark:text-teal-400">{{ strlen(strip_tags($content)) }}</div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">Characters</div>
                            </div>
                            <div>
                                <div class="text-xl font-bold text-orange-600 dark:text-orange-400">{{ ceil(str_word_count(strip_tags($content)) / 200) }} min</div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">Est. Reading Time</div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Assessment Tab -->
            <div x-show="activeTab === 'assessment'" class="p-4">
                @livewire('course-management.course-builder.assessment-manager', ['lessonId' => $lessonId])
            </div>
        </div>
    </div>

    <script>
        function lessonEditorManager() {
            return {
                activeTab: 'content',
                autoSaveStatus: 'saved',
                showAutoSaveStatus: false,
                isSaving: false,
                hasUnsavedContent: false,
                titleChanged: false,
                slugChanged: false,
                descriptionChanged: false,
                contentChanged: false,
                settingsChanged: false,
                mediaChanged: false,
                autoSaveTimeout: null,
                
                init() {
                    this.setupEventListeners();
                },
                
                setupEventListeners() {
                    Livewire.on('lesson-saved', () => {
                        this.handleSuccessfulSave();
                    });
                    
                    window.addEventListener('beforeunload', (e) => {
                        if (this.hasUnsavedContent) {
                            e.preventDefault();
                            e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
                        }
                    });
                    
                    document.addEventListener('trix-change', () => {
                        this.markFieldChanged('content');
                    });

                    // Listen for file uploads
                    Livewire.on('file-uploaded', () => {
                        this.markFieldChanged('media');
                    });
                },
                
                switchTab(tab) {
                    if (!this.isSaving) {
                        this.activeTab = tab;
                    }
                },
                
                markFieldChanged(field) {
                    this.hasUnsavedContent = true;
                    this[field + 'Changed'] = true;
                    this.scheduleAutoSave();
                },
                
                scheduleAutoSave() {
                    clearTimeout(this.autoSaveTimeout);
                    this.autoSaveTimeout = setTimeout(() => {
                        this.performAutoSave();
                    }, 3000);
                },
                
                async performAutoSave() {
                    if (this.isSaving || !this.hasUnsavedContent) return;
                    
                    this.autoSaveStatus = 'saving';
                    this.showAutoSaveStatus = true;
                    
                    try {
                        await this.$wire.call('autoSave');
                        this.handleSuccessfulSave();
                    } catch (error) {
                        this.autoSaveStatus = 'error';
                        setTimeout(() => this.showAutoSaveStatus = false, 3000);
                    }
                },
                
                async performQuickSave() {
                    if (this.isSaving) return;
                    
                    this.isSaving = true;
                    this.autoSaveStatus = 'saving';
                    this.showAutoSaveStatus = true;
                    
                    try {
                        await this.$wire.call('quickSave');
                        this.handleSuccessfulSave();
                    } catch (error) {
                        this.autoSaveStatus = 'error';
                        setTimeout(() => this.showAutoSaveStatus = false, 3000);
                    } finally {
                        this.isSaving = false;
                    }
                },
                
                handleSuccessfulSave() {
                    this.autoSaveStatus = 'saved';
                    this.hasUnsavedContent = false;
                    this.titleChanged = false;
                    this.slugChanged = false;
                    this.descriptionChanged = false;
                    this.contentChanged = false;
                    this.settingsChanged = false;
                    this.mediaChanged = false;
                    
                    setTimeout(() => {
                        this.showAutoSaveStatus = false;
                    }, 2000);
                },
                
                async retrySave() {
                    await this.performQuickSave();
                },
                
                insertTemplate(type) {
                    const templates = {
                        intro: '<h2>Introduction</h2><p>Welcome to this lesson. In this section, we will explore...</p>',
                        objectives: '<h2>Learning Objectives</h2><ul><li>Understand the key concepts</li><li>Apply the knowledge practically</li><li>Evaluate the outcomes</li></ul>',
                        summary: '<h2>Summary</h2><p>In this lesson, we covered the following key points:</p><ul><li>Key point 1</li><li>Key point 2</li><li>Key point 3</li></ul>'
                    };
                    
                    if (this.$refs.trixEditor && templates[type]) {
                        this.$refs.trixEditor.editor.insertHTML(templates[type]);
                        this.markFieldChanged('content');
                    }
                }
            };
        }
    </script>
    
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.addEventListener('trix-before-initialize', function(event) {
                    event.target.toolbarElement.querySelector('[data-trix-action="attachFiles"]')?.remove();
                });
            });
        </script>
    @endpush
</div>