<div>
    <div class="flex items-center justify-between mb-2">
        <label class="text-sm font-medium text-themed-primary flex items-center gap-2 transition-colors duration-300">
            <i class="fas fa-image text-blue-500 dark:text-blue-400"></i>
            Course Thumbnail
        </label>
    </div>

    <!-- Enhanced Image Preview and Upload Area -->
    <div class="relative">
        <!-- File Input (Hidden) -->
        <input type="file" 
               wire:model="thumbnail" 
               class="hidden" 
               id="thumbnail-upload"
               accept="image/jpeg,image/png,image/jpg">

        <!-- Image Preview Area -->
        <div class="border-2 border-dashed border-themed-primary rounded-xl overflow-hidden bg-themed-tertiary transition-all duration-300 hover:border-blue-400 hover:bg-themed-secondary">

            <!-- New Uploaded Image Preview -->
            @if ($thumbnailPreview)
                <div class="relative group">
                    <img src="{{ $thumbnailPreview }}" 
                         alt="New thumbnail preview"
                         class="w-full h-48 sm:h-56 object-cover transition-transform duration-300 group-hover:scale-105">

                    <!-- New Image Badge -->
                    <div class="absolute top-3 left-3">
                        <span class="bg-green-500 text-white px-2 py-1 rounded-lg text-xs font-bold shadow-md flex items-center gap-1">
                            <i class="fas fa-plus"></i> New Image
                        </span>
                    </div>

                    <!-- FIXED: More visible Remove Button with clear icon -->
                    <button type="button" 
                            wire:click="removeThumbnail"
                            class="absolute top-3 right-3 bg-red-500 hover:bg-red-600 text-white w-10 h-10 rounded-lg shadow-lg transition-all duration-200 flex items-center justify-center group/btn">
                        <i class="fas fa-trash-alt text-lg"></i>
                        <span class="absolute -bottom-8 right-0 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover/btn:opacity-100 whitespace-nowrap transition-opacity">
                            Remove Image
                        </span>
                    </button>

                    <!-- Change Image Button -->
                    <label for="thumbnail-upload"
                           class="absolute bottom-3 left-1/2 transform -translate-x-1/2 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg shadow-md cursor-pointer transition-all duration-200 opacity-0 group-hover:opacity-100 flex items-center gap-2">
                        <i class="fas fa-camera"></i>
                        <span>Change Image</span>
                    </label>
                </div>

            <!-- Existing Image (Edit Mode) -->
            @elseif(($isEditMode ?? false) && isset($existingThumbnail) && $existingThumbnail && !($shouldRemoveThumbnail ?? false))
                <div class="relative group">
                    <img src="{{ asset('storage/' . $existingThumbnail) }}"
                         alt="Current course thumbnail"
                         class="w-full h-48 sm:h-56 object-cover transition-transform duration-300 group-hover:scale-105">

                    <!-- Current Image Badge -->
                    <div class="absolute top-3 left-3">
                        <span class="bg-blue-500 text-white px-2 py-1 rounded-lg text-xs font-bold shadow-md flex items-center gap-1">
                            <i class="fas fa-image"></i> Current Image
                        </span>
                    </div>

                    <!-- FIXED: More visible Remove Button with icon and tooltip -->
                    <button type="button" 
                            wire:click="removeThumbnail"
                            class="absolute top-3 right-3 bg-red-500 hover:bg-red-600 text-white w-10 h-10 rounded-lg shadow-lg transition-all duration-200 flex items-center justify-center group/btn z-10">
                        <i class="fas fa-trash-alt text-lg"></i>
                        <span class="absolute -bottom-8 right-0 bg-gray-900 text-white text-xs px-2 py-1 rounded opacity-0 group-hover/btn:opacity-100 whitespace-nowrap transition-opacity pointer-events-none">
                            Remove Image
                        </span>
                    </button>

                    <!-- Change Image Button -->
                    <label for="thumbnail-upload"
                           class="absolute bottom-3 left-1/2 transform -translate-x-1/2 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg shadow-md cursor-pointer transition-all duration-200 opacity-0 group-hover:opacity-100 flex items-center gap-2">
                        <i class="fas fa-camera"></i>
                        <span>Change Image</span>
                    </label>
                </div>

            <!-- No Image State -->
            @else
                <label for="thumbnail-upload" class="cursor-pointer block">
                    <div class="h-48 sm:h-56 flex flex-col items-center justify-center text-center p-6 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors duration-300">
                        <div class="mb-4">
                            <i class="fas fa-cloud-upload-alt text-4xl sm:text-5xl text-themed-secondary mb-4 transition-colors duration-300"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-themed-primary mb-2 transition-colors duration-300">
                            {{ ($isEditMode ?? false) ? (($shouldRemoveThumbnail ?? false) ? 'Upload New Thumbnail' : 'No Thumbnail Set') : 'Upload Course Thumbnail' }}
                        </h4>
                        <p class="text-themed-secondary text-sm mb-4 transition-colors duration-300">
                            Click here to browse and select an image
                        </p>
                        <div class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2">
                            <i class="fas fa-plus"></i>
                            <span>Select Image</span>
                        </div>
                    </div>
                </label>
            @endif
        </div>

        <!-- Upload Progress -->
        <div wire:loading wire:target="thumbnail"
             class="absolute inset-0 bg-themed-secondary/90 rounded-xl flex items-center justify-center backdrop-blur-sm z-10 transition-colors duration-300">
            <div class="text-center">
                <div class="animate-spin rounded-full h-8 w-8 border-4 border-blue-500 border-t-transparent mx-auto mb-3"></div>
                <p class="text-sm font-medium text-themed-primary transition-colors duration-300">
                    Uploading...
                </p>
            </div>
        </div>
    </div>

    <!-- Image Requirements -->
    <div class="mt-3 p-3 bg-themed-tertiary rounded-lg border border-themed-primary transition-colors duration-300">
        <div class="flex items-start gap-2">
            <i class="fas fa-info-circle text-blue-500 mt-0.5 flex-shrink-0"></i>
            <div class="text-xs text-themed-secondary transition-colors duration-300">
                <p class="font-semibold mb-1">Image Requirements:</p>
                <ul class="list-disc list-inside space-y-1 text-xs">
                    <li>Recommended size: 1280x720px (16:9 ratio)</li>
                    <li>Supported formats: JPG, PNG</li>
                    <li>Maximum file size: 2MB</li>
                    <li>High quality images work best</li>
                </ul>
            </div>
        </div>
    </div>

    @error('thumbnail')
        <div class="mt-2 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-700 transition-colors duration-300">
            <p class="text-sm text-red-600 dark:text-red-400 flex items-center gap-2">
                <i class="fas fa-exclamation-triangle"></i>
                {{ $message }}
            </p>
        </div>
    @enderror
</div>