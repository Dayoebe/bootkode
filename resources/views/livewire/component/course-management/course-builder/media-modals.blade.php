<div>

    <!-- Image Upload Modal -->
    @if ($showImageModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
        wire:click.self="closeModals">
        <div class="bg-gray-800 rounded-xl border border-gray-700 max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-white">Add Image</h3>
                    <button wire:click="closeModals" class="text-gray-400 hover:text-white">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Upload Image</label>
                        <input type="file" wire:model="mediaFile" accept="image/*"
                            class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                        @error('mediaFile')
                            <span class="text-red-400 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Image Caption (Optional)</label>
                        <input type="text" wire:model="mediaCaption" placeholder="Describe your image..."
                            class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div class="flex space-x-3">
                        <button wire:click="addImage"
                            class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-plus mr-1"></i> Add Image
                        </button>
                        <button wire:click="closeModals"
                            class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Video Upload Modal -->
@if ($showVideoModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
        wire:click.self="closeModals">
        <div class="bg-gray-800 rounded-xl border border-gray-700 max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-white">Add Video</h3>
                    <button wire:click="closeModals" class="text-gray-400 hover:text-white">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">YouTube URL</label>
                        <input type="url" wire:model="videoUrl"
                            placeholder="https://youtube.com/watch?v=..."
                            class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('videoUrl')
                            <span class="text-red-400 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="text-center text-gray-400">OR</div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Upload Video File</label>
                        <input type="file" wire:model="mediaFile" accept="video/*"
                            class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-red-600 file:text-white hover:file:bg-red-700">
                        @error('mediaFile')
                            <span class="text-red-400 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Video Title (Optional)</label>
                        <input type="text" wire:model="videoTitle" placeholder="Video title..."
                            class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div class="flex space-x-3">
                        <button wire:click="addVideo"
                            class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-plus mr-1"></i> Add Video
                        </button>
                        <button wire:click="closeModals"
                            class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Audio Upload Modal -->
@if ($showAudioModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
        wire:click.self="closeModals">
        <div class="bg-gray-800 rounded-xl border border-gray-700 max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-white">Add Audio</h3>
                    <button wire:click="closeModals" class="text-gray-400 hover:text-white">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Upload Audio File</label>
                        <input type="file" wire:model="mediaFile" accept="audio/*"
                            class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-700">
                        @error('mediaFile')
                            <span class="text-red-400 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Audio Title (Optional)</label>
                        <input type="text" wire:model="audioTitle" placeholder="Audio title..."
                            class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div class="flex space-x-3">
                        <button wire:click="addAudio"
                            class="flex-1 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                            <i class="fas fa-plus mr-1"></i> Add Audio
                        </button>
                        <button wire:click="closeModals"
                            class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- File Upload Modal -->
@if ($showFileModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
        wire:click.self="closeModals">
        <div class="bg-gray-800 rounded-xl border border-gray-700 max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-white">Add File</h3>
                    <button wire:click="closeModals" class="text-gray-400 hover:text-white">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Upload File</label>
                        <input type="file" wire:model="mediaFile"
                            accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip,.rar"
                            class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-600 file:text-white hover:file:bg-green-700">
                        @error('mediaFile')
                            <span class="text-red-400 text-sm">{{ $message }}</span>
                        @enderror
                        <p class="text-xs text-gray-500 mt-1">Supported formats: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, TXT, ZIP, RAR</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">File Description</label>
                        <input type="text" wire:model="fileDescription"
                            placeholder="Describe this file..."
                            class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div class="flex space-x-3">
                        <button wire:click="addFile"
                            class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-plus mr-1"></i> Add File
                        </button>
                        <button wire:click="closeModals"
                            class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
</div>