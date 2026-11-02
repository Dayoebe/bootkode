<div x-data="lessonEditorManager()">
    <!-- Auto-save Status Indicator -->
    <div x-show="showAutoSaveStatus" x-transition.opacity class="fixed top-4 right-4 z-40">
        <div class="px-3 py-2 rounded-lg shadow-sm text-sm font-medium transition-colors duration-300"
             :class="autoSaveStatus === 'saving' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' :
                    autoSaveStatus === 'saved' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' :
                    'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300'">
            <div x-show="autoSaveStatus === 'saving'" class="flex items-center gap-2">
                <div class="w-3 h-3 border-2 border-brand border-t-transparent rounded-full animate-spin"></div>
                Saving...
            </div>
            <div x-show="autoSaveStatus === 'saved'" class="flex items-center gap-2">
                <i aria-hidden="true" class="fas fa-check-circle"></i>
                All changes saved
            </div>
            <div x-show="autoSaveStatus === 'error'" class="flex items-center gap-2">
                <i aria-hidden="true" class="fas fa-exclamation-triangle"></i>
                Save failed - <button @click="retrySave()" class="underline">retry</button>
            </div>
        </div>
    </div>

    <div class="space-y-4">

        <!-- Success Message -->
        @if (session()->has('success'))
            <div x-data="{ show: true }" x-show="show" x-transition.opacity
                 class="bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 border border-green-200 dark:border-green-700 p-4 rounded-lg flex items-center justify-between transition-colors duration-300">
                <div class="flex items-center gap-2">
                    <i aria-hidden="true" class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
                <button @click="show = false" class="text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-200 transition-colors duration-300" aria-label="Close">
                    <i aria-hidden="true" class="fas fa-times"></i>
                </button>
            </div>
        @endif

        <!-- Lesson Header -->
        <div class="bg-themed-secondary rounded-lg shadow-sm border border-themed-primary p-4 transition-colors duration-300">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <h2 class="text-xl font-bold text-themed-primary transition-colors duration-300 truncate">
                        <i aria-hidden="true" class="fas fa-book-open text-brand dark:text-brand mr-2"></i>
                        {{ $lesson->title ?? 'Lesson Editor' }}
                    </h2>
                    <p class="text-sm text-themed-secondary transition-colors duration-300 truncate">
                        Section: <span class="text-brand dark:text-brand">{{ $lesson->section->title ?? 'No Section' }}</span>
                    </p>
                </div>
                <div class="flex gap-2 flex-shrink-0">
                    <button @click="performQuickSave()" :disabled="isSaving"
                        class="px-3 py-2 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 dark:disabled:bg-gray-600 text-white rounded-lg flex items-center gap-2 transition-colors duration-300 text-sm">
                        <i aria-hidden="true" class="fas fa-bolt" x-show="!isSaving"></i>
                        <i aria-hidden="true" class="fas fa-spinner fa-spin" x-show="isSaving"></i>
                        <span class="hidden sm:inline">Quick Save</span>
                    </button>
                    <button wire:click="saveLesson" wire:loading.attr="disabled"
                        class="px-3 py-2 bg-action hover:bg-action disabled:bg-gray-400 dark:disabled:bg-gray-600 text-action-foreground rounded-lg flex items-center gap-2 transition-colors duration-300 text-sm">
                        <i aria-hidden="true" class="fas fa-save" wire:loading.remove></i>
                        <i aria-hidden="true" class="fas fa-spinner fa-spin" wire:loading></i>
                        <span wire:loading.remove class="hidden sm:inline">Save All</span>
                        <span wire:loading class="hidden sm:inline">Saving...</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="bg-themed-secondary rounded-lg shadow-sm border border-themed-primary transition-colors duration-300">
            <div class="border-b border-themed-primary">
                <nav class="-mb-px flex space-x-4 px-4 overflow-x-auto">
                    <button aria-label="Content" @click="switchTab('content')" :disabled="isSaving"
                        :class="{ 'border-blue-500 text-blue-600 dark:text-blue-400': activeTab === 'content', 'border-transparent text-themed-secondary hover:text-themed-primary': activeTab !== 'content' }"
                        class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm flex items-center gap-2 transition-colors duration-300 disabled:opacity-50 relative">
                        <i aria-hidden="true" class="fas fa-align-left"></i>
                        <span class="hidden sm:inline">Content</span>
                        <div x-show="hasUnsavedContent && (titleChanged || descriptionChanged || contentChanged)" class="w-2 h-2 bg-orange-500 rounded-full"></div>
                    </button>
                    <button aria-label="Media" @click="switchTab('media')" :disabled="isSaving"
                        :class="{ 'border-pink-500 text-pink-600 dark:text-pink-400': activeTab === 'media', 'border-transparent text-themed-secondary hover:text-themed-primary': activeTab !== 'media' }"
                        class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm flex items-center gap-2 transition-colors duration-300 disabled:opacity-50 relative">
                        <i aria-hidden="true" class="fas fa-photo-video"></i>
                        <span class="hidden sm:inline">Media</span>
                    </button>
                    <button aria-label="Settings" @click="switchTab('settings')" :disabled="isSaving"
                        :class="{ 'border-green-500 text-green-600 dark:text-green-400': activeTab === 'settings', 'border-transparent text-themed-secondary hover:text-themed-primary': activeTab !== 'settings' }"
                        class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm flex items-center gap-2 transition-colors duration-300 disabled:opacity-50 relative">
                        <i aria-hidden="true" class="fas fa-cog"></i>
                        <span class="hidden sm:inline">Settings</span>
                    </button>
                    <button aria-label="Assessment" @click="switchTab('assessment')" :disabled="isSaving"
                        :class="{ 'border-purple-500 text-purple-600 dark:text-purple-400': activeTab === 'assessment', 'border-transparent text-themed-secondary hover:text-themed-primary': activeTab !== 'assessment' }"
                        class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm flex items-center gap-2 transition-colors duration-300 disabled:opacity-50 relative">
                        <i aria-hidden="true" class="fas fa-clipboard-check"></i>
                        <span class="hidden sm:inline">Assessment</span>
                    </button>
                </nav>
            </div>


            <!-- Content Tab -->
            <div x-show="activeTab === 'content'" class="p-4 space-y-4">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">
                            Lesson Title *
                            <span x-show="titleChanged" class="text-orange-500 text-xs">(unsaved)</span>
                        </label>
                        <input aria-label="Title" type="text"
                            wire:model.live.debounce.500ms="title"
                            @input="markFieldChanged('title'); scheduleSlugGeneration()"
                            class="w-full px-3 py-2 bg-themed-tertiary border border-themed-primary rounded-lg text-themed-primary focus:ring-2 focus:ring-brand/30 focus:border-transparent transition-colors duration-300 text-sm"
                            :class="{ 'border-orange-400': titleChanged }">
                        @error('title')
                            <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">
                            URL Slug *
                            <span x-show="slugChanged" class="text-orange-500 text-xs">(unsaved)</span>
                        </label>
                        <div class="flex gap-2">
                            <input aria-label="Slug" type="text"
                                wire:model.live.debounce.500ms="slug"
                                @input="markFieldChanged('slug'); slugManuallyEdited = true"
                                class="flex-1 px-3 py-2 bg-themed-tertiary border border-themed-primary rounded-lg text-themed-primary focus:ring-2 focus:ring-brand/30 focus:border-transparent transition-colors duration-300 text-sm">
                            <button @click="manualSlugGeneration()" :disabled="isSaving"
                                class="px-3 py-2 bg-themed-tertiary hover:bg-themed-secondary text-themed-primary rounded-lg disabled:opacity-50 transition-colors duration-300 text-sm border border-themed-primary" aria-label="Manual slug generation">
                                <i aria-hidden="true" class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                        @error('slug')
                            <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">
                        Description
                        <span x-show="descriptionChanged" class="text-orange-500 text-xs">(unsaved)</span>
                    </label>
                    <textarea aria-label="Brief description of this lesson" wire:model.live.debounce.500ms="description" @input="markFieldChanged('description')" rows="3"
                        class="w-full px-3 py-2 bg-themed-tertiary border border-themed-primary rounded-lg text-themed-primary focus:ring-2 focus:ring-brand/30 focus:border-transparent transition-colors duration-300 text-sm"
                        placeholder="Brief description of this lesson..."></textarea>
                </div>

                <!-- CRITICAL FIX: Trix Editor with proper wire:ignore -->
                <div wire:ignore>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-sm font-medium text-themed-primary transition-colors duration-300">
                            Lesson Content
                            <span x-show="contentChanged" class="text-orange-500 text-xs">(unsaved)</span>
                        </label>
                        <div class="flex gap-1 text-xs">
                            <button @click="insertTemplate('intro')" class="px-2 py-1 bg-themed-tertiary text-themed-primary rounded hover:bg-themed-secondary transition-colors duration-300 border border-themed-primary">
                                <i aria-hidden="true" class="fas fa-play mr-1"></i>Intro
                            </button>
                            <button @click="insertTemplate('objectives')" class="px-2 py-1 bg-themed-tertiary text-themed-primary rounded hover:bg-themed-secondary transition-colors duration-300 border border-themed-primary">
                                <i aria-hidden="true" class="fas fa-bullseye mr-1"></i>Goals
                            </button>
                        </div>
                    </div>

                    <div class="border border-themed-primary rounded-lg overflow-hidden">
                        <input id="trix-content-{{ $lessonId }}" type="hidden" value="{{ $content }}">
                        <trix-editor
                            x-ref="trixEditor"
                            input="trix-content-{{ $lessonId }}"
                            placeholder="Start writing your lesson content..."
                            class="trix-content bg-themed-tertiary text-themed-primary min-h-[400px] transition-colors duration-300">
                        </trix-editor>
                    </div>
                </div>
            </div>


            <!-- Media Tab -->
            <div x-show="activeTab === 'media'" class="p-4 space-y-4">
                <!-- YouTube Video -->
                <div class="bg-themed-tertiary rounded-lg p-4 border border-themed-primary transition-colors duration-300">
                    <h3 class="text-base font-medium text-themed-primary mb-3 flex items-center gap-2 transition-colors duration-300">
                        <i aria-hidden="true" class="fas fa-video text-red-600 dark:text-red-500"></i> YouTube Video
                    </h3>
                    <div class="flex gap-2">
                        <input aria-label="https://www.youtube.com/watch?v=" type="url" wire:model="video_url"
                               placeholder="https://www.youtube.com/watch?v=..."
                            class="flex-1 px-3 py-2 bg-themed-secondary border border-themed-primary rounded-lg text-themed-primary focus:ring-2 focus:ring-brand/30 focus:border-transparent transition-colors duration-300 text-sm">
                        @if ($video_url)
                            <button type="button" onclick="window.open('{{ $video_url }}', '_blank')"
                                class="px-3 py-2 bg-action hover:bg-action text-action-foreground rounded-lg transition-colors duration-300 text-sm flex-shrink-0">
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
                    <div class="bg-themed-tertiary rounded-lg p-4 border border-themed-primary transition-colors duration-300">
                        <h4 class="font-medium text-themed-primary mb-3 flex items-center gap-2 transition-colors duration-300">
                            <i aria-hidden="true" class="fas fa-image text-green-600"></i> Images
                        </h4>
                        <div class="space-y-2">
                            <input aria-label="Image upload" type="file" wire:model="imageUpload" accept="image/*" class="w-full text-sm file:mr-2 file:py-1 file:px-3 file:rounded file:border-0 file:text-sm file:bg-brand/10 file:text-brand hover:file:bg-brand/10">
                            @if ($imageUpload)
                                <button wire:click="uploadImage" wire:loading.attr="disabled"
                                    class="w-full px-3 py-2 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white rounded text-sm transition-colors duration-300">
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
                                    <div class="flex items-center justify-between text-xs bg-themed-secondary p-2 rounded border border-themed-primary transition-colors duration-300">
                                        <span class="truncate">{{ $image['name'] }}</span>
                                        <button wire:click="removeFile('images', {{ $index }})" class="text-red-600 hover:text-red-800 transition-colors duration-300" aria-label="Remove file">
                                            <i aria-hidden="true" class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Documents -->
                    <div class="bg-themed-tertiary rounded-lg p-4 border border-themed-primary transition-colors duration-300">
                        <h4 class="font-medium text-themed-primary mb-3 flex items-center gap-2 transition-colors duration-300">
                            <i aria-hidden="true" class="fas fa-file-alt text-brand"></i> Documents
                        </h4>
                        <div class="space-y-2">
                            <input aria-label="Document upload" type="file" wire:model="documentUpload" accept=".pdf,.doc,.docx,.txt,.epub,.ppt,.pptx" class="w-full text-sm file:mr-2 file:py-1 file:px-3 file:rounded file:border-0 file:text-sm file:bg-brand/10 file:text-brand hover:file:bg-brand/10">
                            @if ($documentUpload)
                                <button wire:click="uploadDocument" wire:loading.attr="disabled"
                                    class="w-full px-3 py-2 bg-action hover:bg-action disabled:bg-gray-400 text-action-foreground rounded text-sm transition-colors duration-300">
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
                                    <div class="flex items-center justify-between text-xs bg-themed-secondary p-2 rounded border border-themed-primary transition-colors duration-300">
                                        <span class="truncate">{{ $doc['name'] }}</span>
                                        <button wire:click="removeFile('documents', {{ $index }})" class="text-red-600 hover:text-red-800 transition-colors duration-300" aria-label="Remove file">
                                            <i aria-hidden="true" class="fas fa-trash"></i>
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
                    <div class="bg-themed-tertiary rounded-lg p-4 border border-themed-primary transition-colors duration-300">
                        <h4 class="font-medium text-themed-primary mb-3 flex items-center gap-2 transition-colors duration-300">
                            <i aria-hidden="true" class="fas fa-music text-brand"></i> Audio Files
                        </h4>
                        <div class="space-y-2">
                            <input aria-label="Audio upload" type="file" wire:model="audioUpload" accept=".mp3,.wav,.m4a,.aac" class="w-full text-sm file:mr-2 file:py-1 file:px-3 file:rounded file:border-0 file:text-sm file:bg-brand/10 file:text-brand hover:file:bg-brand/10">
                            @if ($audioUpload)
                                <button wire:click="uploadAudio" wire:loading.attr="disabled"
                                    class="w-full px-3 py-2 bg-action hover:bg-action disabled:bg-gray-400 text-action-foreground rounded text-sm transition-colors duration-300">
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
                                    <div class="flex items-center justify-between text-xs bg-themed-secondary p-2 rounded border border-themed-primary transition-colors duration-300">
                                        <span class="truncate">{{ $audio['name'] }}</span>
                                        <button wire:click="removeFile('audios', {{ $index }})" class="text-red-600 hover:text-red-800 transition-colors duration-300" aria-label="Remove file">
                                            <i aria-hidden="true" class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Video Files -->
                    <div class="bg-themed-tertiary rounded-lg p-4 border border-themed-primary transition-colors duration-300">
                        <h4 class="font-medium text-themed-primary mb-3 flex items-center gap-2 transition-colors duration-300">
                            <i aria-hidden="true" class="fas fa-film text-red-600"></i> Video Files
                        </h4>
                        <div class="space-y-2">
                            <input aria-label="Video upload" type="file" wire:model="videoUpload" accept=".mp4,.avi,.mov,.wmv" class="w-full text-sm file:mr-2 file:py-1 file:px-3 file:rounded file:border-0 file:text-sm file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                            @if ($videoUpload)
                                <button wire:click="uploadVideo" wire:loading.attr="disabled"
                                    class="w-full px-3 py-2 bg-red-600 hover:bg-red-700 disabled:bg-gray-400 text-white rounded text-sm transition-colors duration-300">
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
                                    <div class="flex items-center justify-between text-xs bg-themed-secondary p-2 rounded border border-themed-primary transition-colors duration-300">
                                        <span class="truncate">{{ $video['name'] }}</span>
                                        <button wire:click="removeFile('videos', {{ $index }})" class="text-red-600 hover:text-red-800 transition-colors duration-300" aria-label="Remove file">
                                            <i aria-hidden="true" class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- External Links -->
                <div class="bg-themed-tertiary rounded-lg p-4 border border-themed-primary transition-colors duration-300">
                    <h4 class="font-medium text-themed-primary mb-3 flex items-center gap-2 transition-colors duration-300">
                        <i aria-hidden="true" class="fas fa-link text-brand"></i> External Links
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 mb-3">
                        <input aria-label="Link title" type="text" wire:model="newLinkTitle" placeholder="Link title"
                               class="px-3 py-2 bg-themed-secondary border border-themed-primary rounded text-sm text-themed-primary transition-colors duration-300">
                        <input aria-label="https://example.com" type="url" wire:model="newLinkUrl" placeholder="https://example.com"
                               class="px-3 py-2 bg-themed-secondary border border-themed-primary rounded text-sm text-themed-primary transition-colors duration-300">
                        <button wire:click="addExternalLink" wire:loading.attr="disabled"
                            class="px-3 py-2 bg-action hover:bg-action disabled:bg-gray-400 text-action-foreground rounded text-sm transition-colors duration-300">
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
                                <div class="flex items-center justify-between text-xs bg-themed-secondary p-2 rounded border border-themed-primary transition-colors duration-300">
                                    <a href="{{ $link['url'] }}" target="_blank" class="truncate text-brand hover:underline">{{ $link['title'] }}</a>
                                    <button wire:click="removeFile('external_links', {{ $index }})" class="text-red-600 hover:text-red-800 ml-2 transition-colors duration-300" aria-label="Remove file">
                                        <i aria-hidden="true" class="fas fa-trash"></i>
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
                        <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">
                            Duration (minutes)
                        </label>
                        <input aria-label="Estimated completion time" type="number" wire:model="duration_minutes" min="1" max="600"
                            class="w-full px-3 py-2 bg-themed-tertiary border border-themed-primary rounded-lg text-themed-primary focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors duration-300 text-sm"
                            placeholder="Estimated completion time">
                        @error('duration_minutes')
                            <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                        @enderror
                        <p class="text-xs text-themed-secondary mt-1 transition-colors duration-300">How long students should spend on this lesson</p>
                    </div>

                    <!-- Difficulty Level -->
                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Difficulty Level</label>
                        <select aria-label="Difficulty level" wire:model="difficulty_level"
                            class="w-full px-3 py-2 bg-themed-tertiary border border-themed-primary rounded-lg text-themed-primary focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors duration-300 text-sm">
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                            <option value="expert">Expert</option>
                        </select>
                        <p class="text-xs text-themed-secondary mt-1 transition-colors duration-300">Help students understand the lesson complexity</p>
                    </div>

                    <!-- Completion Time Type -->
                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Completion Type</label>
                        <select aria-label="Completion time type" wire:model="completion_time_type"
                            class="w-full px-3 py-2 bg-themed-tertiary border border-themed-primary rounded-lg text-themed-primary focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors duration-300 text-sm">
                            <option value="reading">Reading</option>
                            <option value="watching">Watching</option>
                            <option value="practice">Practice</option>
                            <option value="total">Total Time</option>
                        </select>
                        <p class="text-xs text-themed-secondary mt-1 transition-colors duration-300">Primary activity type for this lesson</p>
                    </div>

                    <!-- Scheduled Publish -->
                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Scheduled Publish</label>
                        <input aria-label="Scheduled publish at" type="datetime-local" wire:model="scheduled_publish_at"
                            class="w-full px-3 py-2 bg-themed-tertiary border border-themed-primary rounded-lg text-themed-primary focus:ring-2 focus:ring-green-500 focus:border-transparent transition-colors duration-300 text-sm">
                        @error('scheduled_publish_at')
                            <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                        @enderror
                        <p class="text-xs text-themed-secondary mt-1 transition-colors duration-300">Optional: Schedule when this lesson becomes available</p>
                    </div>
                </div>

                <!-- Lesson Statistics -->
                <div class="bg-themed-tertiary rounded-lg p-4 border border-themed-primary transition-colors duration-300">
                    <h4 class="font-medium text-themed-primary mb-3 transition-colors duration-300">Lesson Statistics</h4>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                        <div>
                            <div class="text-2xl font-bold text-brand dark:text-brand">{{ count($images) }}</div>
                            <div class="text-xs text-themed-secondary transition-colors duration-300">Images</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ count($documents) }}</div>
                            <div class="text-xs text-themed-secondary transition-colors duration-300">Documents</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-brand dark:text-brand">{{ count($audios) }}</div>
                            <div class="text-xs text-themed-secondary transition-colors duration-300">Audio Files</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ count($videos) }}</div>
                            <div class="text-xs text-themed-secondary transition-colors duration-300">Video Files</div>
                        </div>
                    </div>
                </div>

                <!-- Content Analysis -->
                @if($content)
                    <div class="bg-themed-tertiary rounded-lg p-4 border border-themed-primary transition-colors duration-300">
                        <h4 class="font-medium text-themed-primary mb-3 transition-colors duration-300">Content Analysis</h4>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-center">
                            <div>
                                <div class="text-xl font-bold text-brand dark:text-brand">{{ str_word_count(strip_tags($content)) }}</div>
                                <div class="text-xs text-themed-secondary transition-colors duration-300">Words</div>
                            </div>
                            <div>
                                <div class="text-xl font-bold text-brand dark:text-brand">{{ strlen(strip_tags($content)) }}</div>
                                <div class="text-xs text-themed-secondary transition-colors duration-300">Characters</div>
                            </div>
                            <div>
                                <div class="text-xl font-bold text-orange-600 dark:text-orange-400">{{ ceil(str_word_count(strip_tags($content)) / 200) }} min</div>
                                <div class="text-xs text-themed-secondary transition-colors duration-300">Est. Reading Time</div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Assessment Tab -->
            <div x-show="activeTab === 'assessment'" role="region" aria-label="Lesson assessments" class="p-4">
                @livewire('course-management.course-builder.assessment-manager', ['lessonId' => $lessonId])
            </div>
        </div>
    </div>


</div>
