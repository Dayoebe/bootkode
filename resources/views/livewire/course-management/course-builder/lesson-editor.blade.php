<div>
    <div class="space-y-6 animate__animated animate__fadeIn" x-data="{
        activeTab: 'content',
        showMediaDeleteConfirm: null,
        previewUrl: null,
        previewType: null,
        showFilePreview: null
    }"
        wire:key="lesson-editor-{{ $lessonId }}">

        <!-- Lesson Header -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 transition-colors duration-300">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                        <i class="fas fa-book-open text-blue-600 dark:text-blue-400 mr-2"></i>
                        Lesson Editor
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400">
                        Section: <span class="text-blue-600 dark:text-blue-300">{{ $lesson->section->title ?? 'No Section' }}</span>
                        | Course: <span class="text-green-600 dark:text-green-300">{{ $lesson->section->course->title ?? 'No Course' }}</span>
                    </p>
                </div>
                <button wire:click="saveLesson" wire:loading.attr="disabled"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg flex items-center gap-2 transition-colors duration-300 disabled:opacity-50">
                    <i class="fas fa-save"></i>
                    <span wire:loading.remove>Save Lesson</span>
                    <span wire:loading>Saving...</span>
                </button>
            </div>
        </div>

        <!-- Success Message -->
        @if (session()->has('success'))
            <div class="bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 border border-green-200 dark:border-green-700 p-4 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- Navigation Tabs -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 transition-colors duration-300">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-8 px-6">
                    <button @click="activeTab = 'content'"
                        :class="{ 'border-blue-500 text-blue-600 dark:text-blue-400': activeTab === 'content', 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-300': activeTab !== 'content' }"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2 transition-colors duration-300">
                        <i class="fas fa-align-left"></i> Content
                    </button>
                    <button @click="activeTab = 'media'"
                        :class="{ 'border-pink-500 text-pink-600 dark:text-pink-400': activeTab === 'media', 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-300': activeTab !== 'media' }"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2 transition-colors duration-300">
                        <i class="fas fa-photo-video"></i> Media & Files
                    </button>
                    <button @click="activeTab = 'settings'"
                        :class="{ 'border-green-500 text-green-600 dark:text-green-400': activeTab === 'settings', 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-300': activeTab !== 'settings' }"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2 transition-colors duration-300">
                        <i class="fas fa-cog"></i> Settings
                    </button>
                    <button @click="activeTab = 'assessment'"
                        :class="{ 'border-purple-500 text-purple-600 dark:text-purple-400': activeTab === 'assessment', 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-300': activeTab !== 'assessment' }"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2 transition-colors duration-300">
                        <i class="fas fa-clipboard-check"></i> Assessment
                    </button>
                </nav>
            </div>

            <!-- Content Tab -->
            <div x-show="activeTab === 'content'" class="p-6 space-y-6 animate__animated animate__fadeIn">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Lesson Title *</label>
                        <input type="text" wire:model.live.debounce.500ms="title"
                            class="w-full px-4 py-2 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                        @error('title')
                            <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">URL Slug *</label>
                        <div class="flex gap-2">
                            <input type="text" wire:model="slug"
                                class="flex-1 px-4 py-2 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                            <button wire:click="generateSlug" wire:loading.attr="disabled"
                                class="px-4 py-2 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 text-gray-800 dark:text-white rounded-lg disabled:opacity-50 transition-colors duration-300"
                                title="Generate from title">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                        @error('slug')
                            <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                    <textarea wire:model="description" rows="3"
                        class="w-full px-4 py-2 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300"
                        placeholder="Brief description of this lesson..."></textarea>
                    @error('description')
                        <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Content Editor -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-md font-medium text-gray-700 dark:text-gray-300">Lesson Content</label>
                    </div>
                    <div wire:ignore.self class="border border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden shadow-lg">
                        <trix-editor wire:model.live.debounce.1500ms="content" x-ref="trixEditor"
                            placeholder="Start writing your lesson content here..."
                            class="trix-content bg-white dark:bg-gray-700 text-gray-900 dark:text-white min-h-[400px] p-4"
                            wire:key="trix-{{ $lessonId }}"
                            aria-label="Lesson Content Editor">
                        </trix-editor>
                    </div>
                    @error('content')
                        <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                    @enderror
                    <div x-show="saving"
                        class="text-sm text-blue-600 dark:text-blue-400 mt-2 flex items-center gap-1 animate__animated animate__fadeIn">
                        <i class="fas fa-spinner fa-spin"></i> Saving content...
                    </div>
                </div>
            </div>

            <!-- Media & Files Tab -->
            <div x-show="activeTab === 'media'" class="p-6 space-y-6">

                <!-- YouTube Video -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 border border-gray-200 dark:border-gray-600 transition-colors duration-300">
                    <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-video text-red-600 dark:text-red-500"></i> YouTube Video
                    </h3>
                    <div class="flex gap-2">
                        <input type="url" wire:model="video_url" placeholder="https://www.youtube.com/watch?v=..."
                            class="flex-1 px-4 py-2 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                        @if ($video_url)
                            <button @click="previewUrl = '{{ $video_url }}'; previewType = 'video'"
                                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-300">
                                Preview
                            </button>
                        @endif
                    </div>
                    @error('video_url')
                        <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Images Upload -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 border border-gray-200 dark:border-gray-600 transition-colors duration-300">
                    <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-images text-blue-600 dark:text-blue-400"></i> Images
                    </h3>

                    <!-- Existing Images -->
                    @if (count($images) > 0)
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                            @foreach ($images as $index => $image)
                                <div class="relative group">
                                    <img src="{{ Storage::url($image['path']) }}" alt="Lesson image"
                                        class="w-full h-32 object-cover rounded-lg border border-gray-300 dark:border-gray-600">
                                    <div
                                        class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center gap-2">
                                        <button
                                            @click="previewUrl = '{{ Storage::url($image['path']) }}'; previewType = 'image'"
                                            class="px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs transition-colors duration-300">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button wire:click="removeFile('images', {{ $index }})"
                                            class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs transition-colors duration-300">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Upload New Image -->
                    <div class="flex items-center gap-2">
                        <input type="file" wire:model="imageUpload" id="imageUpload" class="hidden" accept="image/*">
                        <label for="imageUpload"
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 text-gray-800 dark:text-white rounded-lg cursor-pointer transition-colors duration-300">
                            Choose Image
                        </label>
                        @if ($imageUpload)
                            <span class="text-gray-700 dark:text-gray-300">{{ $imageUpload->getClientOriginalName() }}</span>
                            <button wire:click="uploadImage" wire:loading.attr="disabled"
                                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg disabled:opacity-50 transition-colors duration-300">
                                Upload
                            </button>
                        @endif
                    </div>
                    @error('imageUpload')
                        <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Documents Upload -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 border border-gray-200 dark:border-gray-600 transition-colors duration-300">
                    <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-file-alt text-yellow-600 dark:text-yellow-400"></i> Documents
                    </h3>

                    <!-- Existing Documents -->
                    @if (count($documents) > 0)
                        <div class="space-y-2 mb-4">
                            @foreach ($documents as $index => $doc)
                                <div class="flex items-center justify-between bg-gray-100 dark:bg-gray-600 p-3 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-file-{{ $doc['type'] === 'pdf' ? 'pdf' : 'alt' }} text-lg text-gray-600 dark:text-gray-400"></i>
                                        <div>
                                            <p class="text-gray-800 dark:text-white font-medium">{{ $doc['name'] }}</p>
                                            <p class="text-gray-600 dark:text-gray-400 text-sm">{{ number_format($doc['size'] / 1024, 1) }} KB</p>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <a href="{{ Storage::url($doc['path']) }}" target="_blank"
                                            class="px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs transition-colors duration-300">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <button wire:click="removeFile('documents', {{ $index }})"
                                            class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs transition-colors duration-300">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Upload New Document -->
                    <div class="flex items-center gap-2">
                        <input type="file" wire:model="documentUpload" id="documentUpload" class="hidden"
                            accept=".pdf,.doc,.docx,.txt,.epub,.ppt,.pptx">
                        <label for="documentUpload"
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 text-gray-800 dark:text-white rounded-lg cursor-pointer transition-colors duration-300">
                            Choose Document
                        </label>
                        @if ($documentUpload)
                            <span class="text-gray-700 dark:text-gray-300">{{ $documentUpload->getClientOriginalName() }}</span>
                            <button wire:click="uploadDocument" wire:loading.attr="disabled"
                                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg disabled:opacity-50 transition-colors duration-300">
                                Upload
                            </button>
                        @endif
                    </div>
                    @error('documentUpload')
                        <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Audio Upload -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 border border-gray-200 dark:border-gray-600 transition-colors duration-300">
                    <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-music text-green-600 dark:text-green-400"></i> Audio Files
                    </h3>

                    <!-- Existing Audio Files -->
                    @if (count($audios) > 0)
                        <div class="space-y-3 mb-4">
                            @foreach ($audios as $index => $audio)
                                <div class="bg-gray-100 dark:bg-gray-600 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center gap-3">
                                            <i class="fas fa-music text-green-600 dark:text-green-400"></i>
                                            <span class="text-gray-800 dark:text-white">{{ $audio['name'] }}</span>
                                        </div>
                                        <button wire:click="removeFile('audios', {{ $index }})"
                                            class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs transition-colors duration-300">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <audio controls class="w-full">
                                        <source src="{{ Storage::url($audio['path']) }}" type="audio/mpeg">
                                    </audio>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Upload New Audio -->
                    <div class="flex items-center gap-2">
                        <input type="file" wire:model="audioUpload" id="audioUpload" class="hidden" accept="audio/*">
                        <label for="audioUpload"
                            class="px-4 py-2 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 text-gray-800 dark:text-white rounded-lg cursor-pointer transition-colors duration-300">
                            Choose Audio File
                        </label>
                        @if ($audioUpload)
                            <span class="text-gray-700 dark:text-gray-300">{{ $audioUpload->getClientOriginalName() }}</span>
                            <button wire:click="uploadAudio" wire:loading.attr="disabled"
                                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg disabled:opacity-50 transition-colors duration-300">
                                Upload
                            </button>
                        @endif
                    </div>
                    @error('audioUpload')
                        <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- External Links -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 border border-gray-200 dark:border-gray-600 transition-colors duration-300">
                    <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-external-link-alt text-purple-600 dark:text-purple-400"></i> External Links
                    </h3>

                    <!-- Existing Links -->
                    @if (count($external_links) > 0)
                        <div class="space-y-2 mb-4">
                            @foreach ($external_links as $index => $link)
                                <div class="flex items-center justify-between bg-gray-100 dark:bg-gray-600 p-3 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <div class="flex items-center gap-3">
                                        <i class="fas fa-link text-purple-600 dark:text-purple-400"></i>
                                        <div>
                                            <a href="{{ $link['url'] }}" target="_blank"
                                                class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors duration-300">
                                                {{ $link['title'] }}
                                            </a>
                                            <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $link['url'] }}</p>
                                        </div>
                                    </div>
                                    <button wire:click="removeFile('external_links', {{ $index }})"
                                        class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs transition-colors duration-300">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Add New Link -->
                    <div class="space-y-3">
                        <div>
                            <input type="text" wire:model="newLinkTitle" placeholder="Link Title"
                                class="w-full px-4 py-2 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-300">
                            @error('newLinkTitle')
                                <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="flex gap-2">
                            <input type="url" wire:model="newLinkUrl" placeholder="https://example.com"
                                class="flex-1 px-4 py-2 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-300">
                            <button wire:click="addExternalLink" wire:loading.attr="disabled"
                                class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg disabled:opacity-50 transition-colors duration-300">
                                Add Link
                            </button>
                        </div>
                        @error('newLinkUrl')
                            <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Settings Tab -->
            <div x-show="activeTab === 'settings'" class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Duration -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Duration (minutes)</label>
                        <input type="number" wire:model="duration_minutes" min="1" max="600"
                            class="w-full px-4 py-2 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                        @error('duration_minutes')
                            <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Scheduled Publish -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Schedule Publish</label>
                        <input type="datetime-local" wire:model="scheduled_publish_at"
                            class="w-full px-4 py-2 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                        @error('scheduled_publish_at')
                            <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                        @enderror
                        <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Leave empty to publish immediately when course is published</p>
                    </div>
                </div>

                <!-- Course Pricing Info (Read-only) -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 border border-gray-200 dark:border-gray-600 transition-colors duration-300">
                    <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-600 dark:text-blue-400"></i>
                        Course Pricing Information
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div class="bg-gray-100 dark:bg-gray-600 p-3 rounded-lg border border-gray-200 dark:border-gray-600">
                            <span class="text-gray-700 dark:text-gray-300">Course Type</span>
                            <p class="text-lg font-medium mt-1">
                                @if ($lesson->section->course->is_free)
                                    <span class="text-green-600 dark:text-green-400"><i class="fas fa-check mr-1"></i>Free Course</span>
                                @elseif($lesson->section->course->is_premium)
                                    <span class="text-yellow-600 dark:text-yellow-400"><i class="fas fa-crown mr-1"></i>Premium Course</span>
                                @else
                                    <span class="text-blue-600 dark:text-blue-400"><i class="fas fa-dollar-sign mr-1"></i>Paid Course</span>
                                @endif
                            </p>
                        </div>
                        <div class="bg-gray-100 dark:bg-gray-600 p-3 rounded-lg border border-gray-200 dark:border-gray-600">
                            <span class="text-gray-700 dark:text-gray-300">Course Price</span>
                            <p class="text-lg font-medium text-gray-800 dark:text-white mt-1">
                                {{ $lesson->section->course->formatted_price }}
                            </p>
                        </div>
                        <div class="bg-gray-100 dark:bg-gray-600 p-3 rounded-lg border border-gray-200 dark:border-gray-600">
                            <span class="text-gray-700 dark:text-gray-300">Access Level</span>
                            <p class="text-lg font-medium text-gray-800 dark:text-white mt-1">
                                @if ($lesson->section->course->is_free)
                                    Public
                                @else
                                    Enrolled Only
                                @endif
                            </p>
                        </div>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mt-3">
                        <i class="fas fa-lightbulb mr-1"></i>
                        Pricing is managed at the course level. All lessons inherit the course's access settings.
                    </p>
                </div>

                <!-- Additional Settings -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 border border-gray-200 dark:border-gray-600 transition-colors duration-300">
                    <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-4">Lesson Settings</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Completion Time Estimate</label>
                            <select wire:model="completion_time_type"
                                class="w-full px-4 py-2 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                                <option value="reading">Reading Time</option>
                                <option value="watching">Watching Time</option>
                                <option value="practice">Practice Time</option>
                                <option value="total">Total Time</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Difficulty Level</label>
                            <select wire:model="difficulty_level"
                                class="w-full px-4 py-2 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors duration-300">
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                                <option value="expert">Expert</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assessment Tab -->
            <div x-show="activeTab === 'assessment'" class="p-6 space-y-6">
                @livewire('course-management.course-builder.assessment-manager', ['lessonId' => $lessonId])
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 border border-gray-200 dark:border-gray-600 transition-colors duration-300">
                    <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-4">Assessment Settings</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pass Percentage</label>
                            <input type="number" min="0" max="100" value="70"
                                class="w-full px-4 py-2 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-300">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Time Limit (minutes)</label>
                            <input type="number" min="1" placeholder="Optional"
                                class="w-full px-4 py-2 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg text-gray-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-300">
                        </div>
                    </div>

                    <div class="mt-4 space-y-3">
                        <div class="flex items-center">
                            <input type="checkbox" id="mandatory_assessment"
                                class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700">
                            <label for="mandatory_assessment" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                Mandatory for lesson completion
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" id="multiple_attempts"
                                class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700">
                            <label for="multiple_attempts" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                Allow multiple attempts
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" id="show_results"
                                class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 dark:border-gray-600 rounded bg-white dark:bg-gray-700">
                            <label for="show_results" class="ml-2 block text-sm text-gray-700 dark:text-gray-300">
                                Show results immediately after submission
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div x-show="previewUrl" x-transition.opacity
        class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50" style="display: none"
        :style="previewUrl ? 'display: flex' : ''" @keydown.escape="previewUrl = null; previewType = null">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-6 max-w-6xl w-full max-h-[90vh] overflow-auto border border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Preview</h3>
                <button @click="previewUrl = null; previewType = null" class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-white p-1 transition-colors duration-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="max-h-[80vh] overflow-auto">
                <template x-if="previewType === 'video'">
                    <div class="aspect-w-16 aspect-h-9">
                        <iframe class="w-full h-[500px] rounded-lg"
                            :src="'https://www.youtube.com/embed/' + previewUrl.split('v=')[1]?.split('&')[0]"
                            frameborder="0" allowfullscreen></iframe>
                    </div>
                </template>
                <template x-if="previewType === 'image'">
                    <img :src="previewUrl" alt="Preview" class="max-w-full mx-auto rounded-lg">
                </template>
            </div>
        </div>
    </div>

    <!-- Loading States -->
    <div wire:loading.delay wire:target="uploadImage,uploadDocument,uploadAudio,saveLesson"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-40">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 flex items-center gap-4 border border-gray-200 dark:border-gray-700">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 dark:border-blue-400"></div>
            <span class="text-gray-800 dark:text-white">Processing...</span>
        </div>
    </div>
    <link rel="stylesheet" href="{{ asset('css/trix.css') }}">

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Prevent Trix from adding unwanted attributes
                document.addEventListener('trix-before-initialize', function(event) {
                    event.target.toolbarElement.querySelector('[data-trix-action="attachFiles"]')?.remove();
                });

                // Handle content updates properly
                document.addEventListener('trix-change', function(event) {
                    // Let Livewire handle the content update
                    event.target.dispatchEvent(new Event('input', {
                        bubbles: true
                    }));
                });
            });
        </script>

        <script>
            // Auto-save functionality
            let autoSaveTimeout;

            document.addEventListener('livewire:init', () => {
                Livewire.on('lesson-updated', () => {
                    // Show success notification
                    const notification = document.createElement('div');
                    notification.className =
                        'fixed top-4 right-4 bg-green-600 text-white p-4 rounded-lg shadow-lg z-50 animate__animated animate__fadeIn';
                    notification.innerHTML = '<i class="fas fa-check mr-2"></i>Lesson saved successfully!';
                    document.body.appendChild(notification);

                    setTimeout(() => {
                        notification.remove();
                    }, 3000);
                });
            });

            // Auto-save on content change (debounced)
            function scheduleAutoSave() {
                clearTimeout(autoSaveTimeout);
                autoSaveTimeout = setTimeout(() => {
                    @this.call('saveLesson');
                }, 30000); // Auto-save after 30 seconds of inactivity
            }

            // Listen for trix editor changes
            document.addEventListener('trix-change', scheduleAutoSave);
        </script>
    @endpush
</div>