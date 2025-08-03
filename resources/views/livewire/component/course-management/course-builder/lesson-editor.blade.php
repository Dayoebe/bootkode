<div class="bg-gray-800 rounded-xl border border-gray-700 shadow-xl">
    <!-- Editor Header -->
    <div class="p-6 border-b border-gray-700">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h2 class="text-2xl font-bold text-white mb-2">{{ $lesson->title }}</h2>
                <div class="flex flex-wrap items-center space-x-4 text-sm text-gray-400">
                    <span><i class="fas fa-list mr-1"></i> Section:
                        {{ $lesson->section->title ?? 'Unknown' }}</span>
                    <span><i class="fas fa-{{ $lesson->content_type === 'video' ? 'video' : ($lesson->content_type === 'file' ? 'file' : 'file-text') }} mr-1"></i>
                        {{ ucfirst($lesson->content_type) }} Content</span>
                    @if ($lesson->duration_minutes)
                        <span><i class="fas fa-clock mr-1"></i>
                            {{ $lesson->duration_minutes }} minutes</span>
                    @endif
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <button wire:click="previewLesson"
                    class="px-4 py-2 bg-gray-700 text-gray-300 rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-eye mr-1"></i> Preview
                </button>
                <button wire:click="save"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-semibold"
                    :class="{ 'opacity-50': !$wire.isDirty }">
                    <i class="fas fa-save mr-1"></i> Save Lesson
                </button>
            </div>
        </div>
    </div>

    <!-- Auto-save indicator -->
    @if ($autoSaveMessage)
        <div class="px-6 py-2 bg-green-600 bg-opacity-20 border-b border-green-600 border-opacity-30">
            <div class="flex items-center text-green-400 text-sm">
                <i class="fas fa-check mr-2"></i>{{ $autoSaveMessage }}
            </div>
        </div>
    @endif

    <!-- Content Editor -->
    <div class="p-6">
        <!-- Rich Text Toolbar -->
        <div class="mb-4 flex flex-wrap items-center gap-2 p-3 bg-gray-700 rounded-lg border border-gray-600">
            <div class="flex items-center space-x-1 border-r border-gray-600 pr-3">
                <button type="button" onclick="document.execCommand('bold', false, null)"
                    class="p-2 text-gray-300 hover:text-white hover:bg-gray-600 rounded transition-colors"
                    title="Bold">
                    <i class="fas fa-bold"></i>
                </button>
                <button type="button" onclick="document.execCommand('italic', false, null)"
                    class="p-2 text-gray-300 hover:text-white hover:bg-gray-600 rounded transition-colors"
                    title="Italic">
                    <i class="fas fa-italic"></i>
                </button>
                <button type="button" onclick="document.execCommand('underline', false, null)"
                    class="p-2 text-gray-300 hover:text-white hover:bg-gray-600 rounded transition-colors"
                    title="Underline">
                    <i class="fas fa-underline"></i>
                </button>
            </div>

            <div class="flex items-center space-x-1 border-r border-gray-600 pr-3">
                <button type="button" onclick="document.execCommand('formatBlock', false, 'h1')"
                    class="p-2 text-gray-300 hover:text-white hover:bg-gray-600 rounded transition-colors"
                    title="Heading 1">
                    <i class="fas fa-heading"></i>
                </button>
                <button type="button" onclick="document.execCommand('insertUnorderedList', false, null)"
                    class="p-2 text-gray-300 hover:text-white hover:bg-gray-600 rounded transition-colors"
                    title="Bullet List">
                    <i class="fas fa-list-ul"></i>
                </button>
                <button type="button" onclick="document.execCommand('insertOrderedList', false, null)"
                    class="p-2 text-gray-300 hover:text-white hover:bg-gray-600 rounded transition-colors"
                    title="Numbered List">
                    <i class="fas fa-list-ol"></i>
                </button>
                <button type="button" onclick="document.execCommand('formatBlock', false, 'blockquote')"
                    class="p-2 text-gray-300 hover:text-white hover:bg-gray-600 rounded transition-colors"
                    title="Quote">
                    <i class="fas fa-quote-left"></i>
                </button>
            </div>

            <div class="flex items-center space-x-1">
                <button wire:click="showImageModal"
                    class="flex flex-col items-center space-y-1 px-3 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                    <i class="fas fa-image"></i>
                    <span class="text-xs">Image</span>
                </button>
                <button wire:click="showVideoModal"
                    class="flex flex-col items-center space-y-1 px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-video"></i>
                    <span class="text-xs">Video</span>
                </button>
                <button wire:click="showAudioModal"
                    class="flex flex-col items-center space-y-1 px-3 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
                    <i class="fas fa-music"></i>
                    <span class="text-xs">Audio</span>
                </button>
                <button wire:click="showFileModal"
                    class="flex flex-col items-center space-y-1 px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-file-upload"></i>
                    <span class="text-xs">File</span>
                </button>
                <button wire:click="addCodeBlock"
                    class="flex flex-col items-center space-y-1 px-3 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-code"></i>
                    <span class="text-xs">Code</span>
                </button>
                <button wire:click="addNoteBlock"
                    class="flex flex-col items-center space-y-1 px-3 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                    <i class="fas fa-lightbulb"></i>
                    <span class="text-xs">Note</span>
                </button>
            </div>
        </div>

        <!-- Content Editor Area -->
        <div class="bg-gray-700 rounded-lg p-4 min-h-96">
            <div wire:ignore>
                <trix-editor
                    class="trix-content bg-gray-800 text-white rounded-lg p-4 min-h-96"
                    wire:model="content"
                    wire:key="trix-editor-{{ $lessonId }}"></trix-editor>
            </div>
            
            <!-- Content Blocks -->
            <div id="contentBlocks" class="mt-4 space-y-4">
                @foreach($contentBlocks as $block)
                    <div class="content-block bg-gray-800 rounded-lg p-4 border border-gray-600" 
                         data-block-id="{{ $block['id'] }}">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-grip-vertical text-gray-400 cursor-move"></i>
                                <span class="text-xs text-gray-400">
                                    {{ ucfirst($block['type']) }} Block
                                </span>
                            </div>
                            <button wire:click="removeContentBlock('{{ $block['id'] }}')"
                                class="text-red-400 hover:text-red-300">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        
                        @if($block['type'] === 'image')
                            <div class="flex flex-col items-center">
                                <img src="{{ Storage::disk('public')->url($block['file_path']) }}" 
                                     alt="{{ $block['caption'] ?? '' }}" 
                                     class="max-w-full h-auto rounded-lg mb-2">
                                @if(isset($block['caption']) && $block['caption'])
                                    <p class="text-sm text-gray-300">{{ $block['caption'] }}</p>
                                @endif
                            </div>
                        @elseif($block['type'] === 'video')
                            @if(isset($block['video_url']))
                                <div class="aspect-w-16 aspect-h-9">
                                    <iframe src="{{ $block['video_url'] }}" 
                                            class="w-full h-96 rounded-lg" 
                                            frameborder="0" 
                                            allowfullscreen></iframe>
                                </div>
                                @if(isset($block['title']))
                                    <p class="text-sm text-gray-300 mt-2">{{ $block['title'] }}</p>
                                @endif
                            @else
                                <video controls class="w-full rounded-lg">
                                    <source src="{{ Storage::disk('public')->url($block['file_path']) }}" 
                                            type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                                @if(isset($block['title']))
                                    <p class="text-sm text-gray-300 mt-2">{{ $block['title'] }}</p>
                                @endif
                            @endif
                        @elseif($block['type'] === 'file')
                            <div class="flex items-center space-x-3 p-3 bg-gray-700 rounded-lg">
                                <i class="fas fa-file text-2xl text-blue-400"></i>
                                <div class="flex-1">
                                    <p class="text-white font-medium">{{ $block['file_name'] }}</p>
                                    <p class="text-xs text-gray-400">{{ $this->formatFileSize($block['file_size']) }}</p>
                                    @if(isset($block['description']) && $block['description'])
                                        <p class="text-sm text-gray-300 mt-1">{{ $block['description'] }}</p>
                                    @endif
                                </div>
                                <a href="{{ Storage::disk('public')->url($block['file_path']) }}" 
                                   download="{{ $block['file_name'] }}"
                                   class="px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        @elseif($block['type'] === 'audio')
                            <div class="flex items-center space-x-3 p-3 bg-gray-700 rounded-lg">
                                <i class="fas fa-music text-2xl text-purple-400"></i>
                                <div class="flex-1">
                                    <p class="text-white font-medium">{{ $block['title'] }}</p>
                                    <p class="text-xs text-gray-400">{{ $this->formatFileSize($block['file_size']) }}</p>
                                </div>
                                <audio controls class="flex-1">
                                    <source src="{{ Storage::disk('public')->url($block['file_path']) }}" 
                                            type="audio/mpeg">
                                    Your browser does not support the audio element.
                                </audio>
                            </div>
                        @elseif($block['type'] === 'code')
                            <div class="relative">
                                <div class="flex justify-between items-center bg-gray-900 px-3 py-2 rounded-t-lg">
                                    <span class="text-sm text-gray-300">{{ $block['title'] }}</span>
                                    <button onclick="copyToClipboard('{{ $block['id'] }}')"
                                        class="text-gray-400 hover:text-white text-sm">
                                        <i class="fas fa-copy mr-1"></i> Copy
                                    </button>
                                </div>
                                <textarea id="code-{{ $block['id'] }}" 
                                          class="w-full bg-gray-900 text-green-400 font-mono text-sm p-3 rounded-b-lg border-t-0 border-gray-700"
                                          rows="8" readonly>{{ $block['code'] }}</textarea>
                            </div>
                        @elseif($block['type'] === 'note')
                            <div class="note-{{ $block['note_type'] }} p-4 rounded-lg">
                                <div class="flex items-start space-x-3">
                                    <i class="fas fa-{{ $this->getNoteIcon($block['note_type']) }} text-lg mt-1"></i>
                                    <div>
                                        <h4 class="font-bold text-white">{{ $block['title'] }}</h4>
                                        <p class="text-gray-300 mt-1">{{ $block['content'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
    // Auto-save functionality
    let autoSaveTimeout;
    document.addEventListener('trix-change', (e) => {
        @this.set('content', e.target.value);
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(() => {
            @this.call('autoSave');
        }, 3000);
    });

    // Copy to clipboard function
    function copyToClipboard(blockId) {
        const codeElement = document.getElementById('code-' + blockId);
        if (codeElement) {
            navigator.clipboard.writeText(codeElement.value).then(() => {
                @this.dispatch('notify', 'Code copied to clipboard!', 'success');
            });
        }
    }

    // Initialize sortable for content blocks
    document.addEventListener('livewire:navigated', () => {
        if (typeof Sortable !== 'undefined') {
            const contentBlocksContainer = document.getElementById('contentBlocks');
            if (contentBlocksContainer) {
                new Sortable(contentBlocksContainer, {
                    handle: '.fa-grip-vertical',
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    onEnd: (evt) => {
                        const orderedIds = Array.from(contentBlocksContainer.children).map(
                            el => el.getAttribute('data-block-id')
                        );
                        @this.call('reorderContentBlocks', orderedIds);
                    }
                });
            }
        }
    });
</script>