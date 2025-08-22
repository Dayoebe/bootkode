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
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-white">
                <i class="fas fa-book-open text-blue-400 mr-2"></i>
                Lesson Editor
            </h2>
            <p class="text-gray-400">
                Section: <span class="text-blue-300">{{ $lesson->section->title ?? 'No Section' }}</span>
                | Course: <span class="text-green-300">{{ $lesson->section->course->title ?? 'No Course' }}</span>
            </p>
        </div>
        <button wire:click="saveLesson" wire:loading.attr="disabled"
            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg flex items-center gap-2 transition-colors disabled:opacity-50">
            <i class="fas fa-save"></i> 
            <span wire:loading.remove>Save Lesson</span>
            <span wire:loading>Saving...</span>
        </button>
    </div>

    <!-- Success Message -->
    @if (session()->has('success'))
        <div class="bg-green-600 text-white p-4 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <!-- Navigation Tabs -->
    <div class="border-b border-gray-700">
        <nav class="-mb-px flex space-x-8">
            <button @click="activeTab = 'content'"
                :class="{ 'border-blue-500 text-blue-400': activeTab === 'content', 'border-transparent text-gray-400 hover:text-gray-300': activeTab !== 'content' }"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
                <i class="fas fa-align-left"></i> Content
            </button>
            <button @click="activeTab = 'media'"
                :class="{ 'border-pink-500 text-pink-400': activeTab === 'media', 'border-transparent text-gray-400 hover:text-gray-300': activeTab !== 'media' }"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
                <i class="fas fa-photo-video"></i> Media & Files
            </button>
            <button @click="activeTab = 'settings'"
                :class="{ 'border-green-500 text-green-400': activeTab === 'settings', 'border-transparent text-gray-400 hover:text-gray-300': activeTab !== 'settings' }"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
                <i class="fas fa-cog"></i> Settings
            </button>
            <button @click="activeTab = 'assessment'"
                :class="{ 'border-purple-500 text-purple-400': activeTab === 'assessment', 'border-transparent text-gray-400 hover:text-gray-300': activeTab !== 'assessment' }"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
                <i class="fas fa-clipboard-check"></i> Assessment
            </button>
        </nav>
    </div>

    <!-- Content Tab -->
    <div x-show="activeTab === 'content'" class="space-y-6 animate__animated animate__fadeIn">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 my-3">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Lesson Title *</label>
                <input type="text" wire:model.live.debounce.500ms="title"
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
                    <button wire:click="generateSlug" wire:loading.attr="disabled"
                        class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg disabled:opacity-50"
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
                class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                placeholder="Brief description of this lesson..."></textarea>
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
                    <button @click="$refs.trixEditor.editor.insertHTML('<ul><li>List item</li></ul>')"
                        class="px-3 py-1 bg-gray-700 hover:bg-gray-600 rounded text-sm">
                        <i class="fas fa-list-ul mr-1"></i> List
                    </button>
                </div>
            </div>
            <div class="border border-gray-700 rounded-lg overflow-hidden shadow-lg">
                <trix-editor wire:model="content" x-ref="trixEditor"
                    placeholder="Start writing your lesson content here..."
                    class="trix-content bg-gray-800 text-white min-h-[400px] p-4"></trix-editor>
            </div>
            @error('content')
                <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <!-- Media & Files Tab -->
    <div x-show="activeTab === 'media'" class="space-y-6 my-2">
        
        <!-- YouTube Video -->
        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <h3 class="text-lg font-medium text-white mb-4 flex items-center gap-2">
                <i class="fas fa-video text-red-500"></i> YouTube Video
            </h3>
            <div class="flex gap-2">
                <input type="url" wire:model="video_url" placeholder="https://www.youtube.com/watch?v=..."
                    class="flex-1 px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
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

        <!-- Images Upload -->
        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <h3 class="text-lg font-medium text-white mb-4 flex items-center gap-2">
                <i class="fas fa-images text-blue-400"></i> Images
            </h3>

            <!-- Existing Images -->
            @if(count($images) > 0)
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                    @foreach($images as $index => $image)
                        <div class="relative group">
                            <img src="{{ Storage::url($image['path']) }}" alt="Lesson image"
                                class="w-full h-32 object-cover rounded-lg border border-gray-600">
                            <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center gap-2">
                                <button @click="previewUrl = '{{ Storage::url($image['path']) }}'; previewType = 'image'"
                                    class="px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button wire:click="removeFile('images', {{ $index }})"
                                    class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs">
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
                    class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg cursor-pointer">
                    Choose Image
                </label>
                @if ($imageUpload)
                    <span class="text-gray-300">{{ $imageUpload->getClientOriginalName() }}</span>
                    <button wire:click="uploadImage" wire:loading.attr="disabled"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg disabled:opacity-50">
                        Upload
                    </button>
                @endif
            </div>
            @error('imageUpload')
                <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
            @enderror
        </div>

        <!-- Documents Upload -->
        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <h3 class="text-lg font-medium text-white mb-4 flex items-center gap-2">
                <i class="fas fa-file-alt text-yellow-400"></i> Documents
            </h3>

            <!-- Existing Documents -->
            @if(count($documents) > 0)
                <div class="space-y-2 mb-4">
                    @foreach($documents as $index => $doc)
                        <div class="flex items-center justify-between bg-gray-700 p-3 rounded-lg">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-file-{{ $doc['type'] === 'pdf' ? 'pdf' : 'alt' }} text-lg text-gray-400"></i>
                                <div>
                                    <p class="text-white font-medium">{{ $doc['name'] }}</p>
                                    <p class="text-gray-400 text-sm">{{ number_format($doc['size'] / 1024, 1) }} KB</p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <a href="{{ Storage::url($doc['path']) }}" target="_blank"
                                    class="px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-xs">
                                    <i class="fas fa-download"></i>
                                </a>
                                <button wire:click="removeFile('documents', {{ $index }})"
                                    class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs">
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
                    class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg cursor-pointer">
                    Choose Document
                </label>
                @if ($documentUpload)
                    <span class="text-gray-300">{{ $documentUpload->getClientOriginalName() }}</span>
                    <button wire:click="uploadDocument" wire:loading.attr="disabled"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg disabled:opacity-50">
                        Upload
                    </button>
                @endif
            </div>
            @error('documentUpload')
                <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
            @enderror
        </div>

        <!-- Audio Upload -->
        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <h3 class="text-lg font-medium text-white mb-4 flex items-center gap-2">
                <i class="fas fa-music text-green-400"></i> Audio Files
            </h3>

            <!-- Existing Audio Files -->
            @if(count($audios) > 0)
                <div class="space-y-3 mb-4">
                    @foreach($audios as $index => $audio)
                        <div class="bg-gray-700 p-4 rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-music text-green-400"></i>
                                    <span class="text-white">{{ $audio['name'] }}</span>
                                </div>
                                <button wire:click="removeFile('audios', {{ $index }})"
                                    class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs">
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
                    class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg cursor-pointer">
                    Choose Audio File
                </label>
                @if ($audioUpload)
                    <span class="text-gray-300">{{ $audioUpload->getClientOriginalName() }}</span>
                    <button wire:click="uploadAudio" wire:loading.attr="disabled"
                        class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg disabled:opacity-50">
                        Upload
                    </button>
                @endif
            </div>
            @error('audioUpload')
                <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
            @enderror
        </div>

        <!-- External Links -->
        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <h3 class="text-lg font-medium text-white mb-4 flex items-center gap-2">
                <i class="fas fa-external-link-alt text-purple-400"></i> External Links
            </h3>

            <!-- Existing Links -->
            @if(count($external_links) > 0)
                <div class="space-y-2 mb-4">
                    @foreach($external_links as $index => $link)
                        <div class="flex items-center justify-between bg-gray-700 p-3 rounded-lg">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-link text-purple-400"></i>
                                <div>
                                    <a href="{{ $link['url'] }}" target="_blank" class="text-blue-400 hover:text-blue-300">
                                        {{ $link['title'] }}
                                    </a>
                                    <p class="text-gray-400 text-sm">{{ $link['url'] }}</p>
                                </div>
                            </div>
                            <button wire:click="removeFile('external_links', {{ $index }})"
                                class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-xs">
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
                        class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    @error('newLinkTitle')
                        <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>
                <div class="flex gap-2">
                    <input type="url" wire:model="newLinkUrl" placeholder="https://example.com"
                        class="flex-1 px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <button wire:click="addExternalLink" wire:loading.attr="disabled"
                        class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg disabled:opacity-50">
                        Add Link
                    </button>
                </div>
                @error('newLinkUrl')
                    <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

<!-- Settings Tab -->
<div x-show="activeTab === 'settings'" class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Duration -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">Duration (minutes)</label>
            <input type="number" wire:model="duration_minutes" min="1" max="600"
                class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            @error('duration_minutes')
                <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
            @enderror
        </div>

        <!-- Scheduled Publish -->
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">Schedule Publish</label>
            <input type="datetime-local" wire:model="scheduled_publish_at"
                class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            @error('scheduled_publish_at')
                <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
            @enderror
            <p class="text-gray-400 text-sm mt-1">Leave empty to publish immediately when course is published</p>
        </div>
    </div>

    <!-- Course Pricing Info (Read-only) -->
    <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
        <h3 class="text-lg font-medium text-white mb-4 flex items-center gap-2">
            <i class="fas fa-info-circle text-blue-400"></i>
            Course Pricing Information
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div class="bg-gray-700 p-3 rounded-lg">
                <span class="text-gray-300">Course Type</span>
                <p class="text-lg font-medium mt-1">
                    @if($lesson->section->course->is_free)
                        <span class="text-green-400"><i class="fas fa-check mr-1"></i>Free Course</span>
                    @elseif($lesson->section->course->is_premium)
                        <span class="text-yellow-400"><i class="fas fa-crown mr-1"></i>Premium Course</span>
                    @else
                        <span class="text-blue-400"><i class="fas fa-dollar-sign mr-1"></i>Paid Course</span>
                    @endif
                </p>
            </div>
            <div class="bg-gray-700 p-3 rounded-lg">
                <span class="text-gray-300">Course Price</span>
                <p class="text-lg font-medium text-white mt-1">
                    {{ $lesson->section->course->formatted_price }}
                </p>
            </div>
            <div class="bg-gray-700 p-3 rounded-lg">
                <span class="text-gray-300">Access Level</span>
                <p class="text-lg font-medium text-white mt-1">
                    @if($lesson->section->course->is_free)
                        Public
                    @else
                        Enrolled Only
                    @endif
                </p>
            </div>
        </div>
        <p class="text-gray-400 text-sm mt-3">
            <i class="fas fa-lightbulb mr-1"></i>
            Pricing is managed at the course level. All lessons inherit the course's access settings.
        </p>
    </div>

    <!-- Additional Settings -->
    <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
        <h3 class="text-lg font-medium text-white mb-4">Lesson Settings</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Completion Time Estimate</label>
                <select wire:model="completion_time_type" 
                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="reading">Reading Time</option>
                    <option value="watching">Watching Time</option>
                    <option value="practice">Practice Time</option>
                    <option value="total">Total Time</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Difficulty Level</label>
                <select wire:model="difficulty_level" 
                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent">
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
    <div x-show="activeTab === 'assessment'" class="space-y-6">
        @livewire('component.course-management.course-builder.assessment-manager', ['lessonId' => $lessonId])
        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
            <h3 class="text-lg font-medium text-white mb-4">Assessment Settings</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Pass Percentage</label>
                    <input type="number" min="0" max="100" value="70"
                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Time Limit (minutes)</label>
                    <input type="number" min="1" placeholder="Optional"
                    class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>
            </div>
            
            <div class="mt-4 space-y-3">
                <div class="flex items-center">
                    <input type="checkbox" id="mandatory_assessment" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-700 rounded bg-gray-800">
                    <label for="mandatory_assessment" class="ml-2 block text-sm text-gray-300">
                        Mandatory for lesson completion
                    </label>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" id="multiple_attempts" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-700 rounded bg-gray-800">
                    <label for="multiple_attempts" class="ml-2 block text-sm text-gray-300">
                        Allow multiple attempts
                    </label>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" id="show_results" class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-700 rounded bg-gray-800">
                    <label for="show_results" class="ml-2 block text-sm text-gray-300">
                        Show results immediately after submission
                    </label>
                </div>
            </div>
        </div>
    </div>
</div> 

    <!-- Preview Modal -->
    <div x-show="previewUrl" x-transition.opacity
        class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50" style="display: none"
        :style="previewUrl ? 'display: flex' : ''" @keydown.escape="previewUrl = null; previewType = null">
        <div class="bg-gray-800 rounded-xl p-6 max-w-6xl w-full max-h-[90vh] overflow-auto border border-gray-700">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-white">Preview</h3>
                <button @click="previewUrl = null; previewType = null" class="text-gray-400 hover:text-white p-1">
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
        <div class="bg-gray-800 rounded-lg p-6 flex items-center gap-4">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-400"></div>
            <span class="text-white">Processing...</span>
        </div>
    </div>

    @push('styles')
        <style>
            .trix-content {
                color: #f3f4f6 !important;
                font-size: 1rem;
                line-height: 1.6;
                background-color: #1f2937 !important;
            }

            .trix-content h1 {
                font-size: 1.8rem;
                font-weight: bold;
                margin: 1.5rem 0 1rem 0;
                color: #f3f4f6;
            }

            .trix-content h2 {
                font-size: 1.5rem;
                font-weight: bold;
                margin: 1.2rem 0 0.8rem 0;
                color: #f3f4f6;
            }

            .trix-content h3 {
                font-size: 1.3rem;
                font-weight: bold;
                margin: 1rem 0 0.6rem 0;
                color: #f3f4f6;
            }

            .trix-content p {
                margin: 0.8rem 0;
                color: #f3f4f6;
            }

            .trix-content a {
                color: #60a5fa;
                text-decoration: underline;
            }

            .trix-content a:hover {
                color: #93c5fd;
            }

            .trix-content img {
                max-width: 100%;
                height: auto;
                border-radius: 0.5rem;
                margin: 1rem 0;
            }

            .trix-content ul, .trix-content ol {
                margin: 1rem 0;
                padding-left: 2rem;
                color: #f3f4f6;
            }

            .trix-content li {
                margin: 0.5rem 0;
            }

            .trix-content blockquote {
                border-left: 4px solid #60a5fa;
                padding-left: 1rem;
                margin: 1rem 0;
                font-style: italic;
                color: #d1d5db;
                background-color: #374151;
                padding: 1rem;
                border-radius: 0.5rem;
            }

            .trix-content strong {
                font-weight: bold;
                color: #f9fafb;
            }

            .trix-content em {
                font-style: italic;
                color: #e5e7eb;
            }

            .trix-content code {
                background-color: #374151;
                color: #f59e0b;
                padding: 0.2rem 0.4rem;
                border-radius: 0.25rem;
                font-family: 'Courier New', monospace;
                font-size: 0.9rem;
            }

            .trix-content pre {
                background-color: #1f2937;
                color: #f3f4f6;
                padding: 1rem;
                border-radius: 0.5rem;
                overflow-x: auto;
                margin: 1rem 0;
                border: 1px solid #4b5563;
            }

            /* Hide file attachment tools from Trix */
            .trix-button-group--file-tools {
                display: none !important;
            }

            /* Style the toolbar */
            trix-toolbar {
                background-color: #374151 !important;
                border-color: #4b5563 !important;
            }

            trix-toolbar .trix-button-group {
                border-color: #4b5563 !important;
            }

            trix-toolbar .trix-button {
                color: #d1d5db !important;
                background-color: transparent !important;
            }

            trix-toolbar .trix-button:hover {
                background-color: #4b5563 !important;
            }

            trix-toolbar .trix-button.trix-active {
                background-color: #3b82f6 !important;
                color: white !important;
            }

            /* Loading animation */
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(10px); }
                to { opacity: 1; transform: translateY(0); }
            }

            .animate__fadeIn {
                animation: fadeIn 0.3s ease-in-out;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            // Auto-save functionality
            let autoSaveTimeout;
            
            document.addEventListener('livewire:init', () => {
                Livewire.on('lesson-updated', () => {
                    // Show success notification
                    const notification = document.createElement('div');
                    notification.className = 'fixed top-4 right-4 bg-green-600 text-white p-4 rounded-lg shadow-lg z-50 animate__animated animate__fadeIn';
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
