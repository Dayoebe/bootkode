<div class="min-h-screen bg-themed-primary transition-colors duration-300 p-4 sm:p-6" x-data="{
    currentStep: @entangle('currentStep'),
    totalSteps: @entangle('totalSteps'),
    showPreview: false,
    animateStep: true,
    isEditMode: {{ json_encode($isEditMode ?? false) }}
}" x-init="
$watch('currentStep', () => {
    animateStep = false;
    setTimeout(() => animateStep = true, 50);
});

// Listen for delayed redirect event
window.addEventListener('redirect-after-delay', (event) => {
    console.log('Redirect event received', event.detail);
    setTimeout(() => {
        console.log('Redirecting to:', event.detail.url);
        
        let redirectUrl = event.detail.url;
        if (!redirectUrl.startsWith('http')) {
            redirectUrl = window.location.origin + redirectUrl;
        }
        
        window.location.href = redirectUrl;
    }, event.detail.delay);
});
">
    <!-- Header with Progress -->
    <div class="mb-6 sm:mb-8">
        <div
            class="bg-themed-secondary rounded-2xl p-4 sm:p-6 shadow-xl border border-themed-primary transition-colors duration-300">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 sm:mb-6">
                <div class="mb-4 sm:mb-0">
                    <h1
                        class="text-2xl sm:text-3xl font-bold text-themed-primary flex items-center gap-2 sm:gap-3 transition-colors duration-300">
                        <i
                            class="fas fa-{{ $isEditMode ?? false ? 'edit' : 'rocket' }} text-{{ $isEditMode ?? false ? 'orange' : 'purple' }}-500 dark:text-{{ $isEditMode ?? false ? 'orange' : 'purple' }}-400"></i>
                        {{ ($isEditMode ?? false) ? 'Edit Course' : 'Create New Course' }}
                    </h1>
                    <p class="text-themed-secondary mt-1 sm:mt-2 text-sm sm:text-base transition-colors duration-300">
                        {{ ($isEditMode ?? false) ? 'Update your course content and settings' : 'Share your knowledge and inspire learners worldwide' }}
                    </p>
                </div>
                <div class="text-right">
                    <div class="text-sm text-themed-secondary transition-colors duration-300">Step</div>
                    <div class="text-xl sm:text-2xl font-bold text-{{ $isEditMode ?? false ? 'orange' : 'purple' }}-500 dark:text-{{ $isEditMode ?? false ? 'orange' : 'purple' }}-400"
                        x-text="`${currentStep}/${totalSteps}`"></div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="relative">
                <div class="flex items-center justify-between mb-2 overflow-x-auto pb-2">
                    <template x-for="step in totalSteps" :key="step">
                        <div class="flex items-center min-w-0 flex-shrink-0">
                            <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-full flex items-center justify-center text-xs sm:text-sm font-semibold transition-all duration-300"
                                :class="step <= currentStep ? 'bg-{{ $isEditMode ?? false ? 'orange' : 'purple' }}-500 text-white' : 'bg-themed-tertiary text-themed-secondary border border-themed-primary'">
                                <span x-text="step"></span>
                            </div>
                            <div x-show="step < totalSteps"
                                class="h-0.5 w-8 sm:w-16 mx-1 sm:mx-2 transition-colors duration-300"
                                :class="step < currentStep ? 'bg-{{ $isEditMode ?? false ? 'orange' : 'purple' }}-500' : 'bg-themed-tertiary'">
                            </div>
                        </div>
                    </template>
                </div>
                <div class="grid grid-cols-5 gap-1 text-xs text-themed-secondary mt-2 transition-colors duration-300">
                    <span class="text-center">Basic Info</span>
                    <span class="text-center">Description</span>
                    <span class="text-center">Pricing</span>
                    <span class="text-center">Details</span>
                    <span class="text-center">Review</span>
                </div>
            </div>
        </div>
    </div>

    <div class="transition-colors duration-300">
        <form wire:submit="save">
            <!-- Step 1: Basic Information -->
            <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-x-8"
                x-transition:enter-end="opacity-100 transform translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform translate-x-0"
                x-transition:leave-end="opacity-0 transform -translate-x-8">

                <div
                    class="bg-themed-secondary rounded-2xl p-4 sm:p-6 lg:p-8 shadow-xl border border-themed-primary transition-colors duration-300">
                    <div class="flex items-center gap-2 sm:gap-3 mb-4 sm:mb-6">
                        <div
                            class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-500 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-info-circle text-white text-lg sm:text-xl"></i>
                        </div>
                        <div>
                            <h2
                                class="text-xl sm:text-2xl font-bold text-themed-primary transition-colors duration-300">
                                Basic Information</h2>
                            <p class="text-themed-secondary text-sm sm:text-base transition-colors duration-300">
                                Let's start with the essentials</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
                        <!-- Left Column -->
                        <div class="space-y-4 sm:space-y-6">
                            <!-- Course Title -->
                            <div class="group">
                                <label
                                    class="block text-sm font-medium text-themed-primary mb-2 flex items-center gap-2 transition-colors duration-300">
                                    <i class="fas fa-heading text-purple-500 dark:text-purple-400"></i>
                                    Course Title <span class="text-red-500">*</span>
                                </label>
                                <input type="text" wire:model.live="title"
                                    class="w-full px-3 sm:px-4 py-3 bg-themed-tertiary border border-themed-primary rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent text-themed-primary placeholder-themed-secondary transition-all duration-200 group-hover:border-purple-400"
                                    placeholder="e.g., Complete Web Development Bootcamp">
                                @error('title')
                                    <p class="mt-2 text-sm text-red-500 animate-pulse">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Course Slug -->
                            <div class="group">
                                <label
                                    class="block text-sm font-medium text-themed-primary mb-2 flex items-center gap-2 transition-colors duration-300">
                                    <i class="fas fa-link text-purple-500 dark:text-purple-400"></i>
                                    Course URL Slug <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span
                                        class="absolute left-3 top-1/2 transform -translate-y-1/2 text-themed-secondary text-sm hidden sm:inline transition-colors duration-300">bootkode.com/</span>
                                    <input type="text" wire:model.live="slug"
                                        class="w-full pl-3 sm:pl-32 pr-4 py-3 bg-themed-tertiary border border-themed-primary rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent text-themed-primary placeholder-themed-secondary transition-all duration-200 group-hover:border-purple-400"
                                        placeholder="course-web-development">
                                </div>
                                <p class="text-xs text-themed-secondary mt-1 transition-colors duration-300">
                                    {{ ($isEditMode ?? false) ? 'Edit carefully - changing this may break existing links' : 'Auto-generated from title. Use lowercase letters, numbers, and hyphens only.' }}
                                </p>
                                @error('slug')
                                    <p class="mt-2 text-sm text-red-500 animate-pulse">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Course Subtitle -->
                            <div class="group">
                                <label
                                    class="block text-sm font-medium text-themed-primary mb-2 flex items-center gap-2 transition-colors duration-300">
                                    <i class="fas fa-subtitle text-purple-500 dark:text-purple-400"></i>
                                    Course Subtitle
                                </label>
                                <input type="text" wire:model="subtitle"
                                    class="w-full px-3 sm:px-4 py-3 bg-themed-tertiary border border-themed-primary rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent text-themed-primary placeholder-themed-secondary transition-all duration-200 group-hover:border-purple-400"
                                    placeholder="A catchy subtitle for your course">
                                @error('subtitle')
                                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-4 sm:space-y-6">
                            <!-- Category -->
                            <div class="group">
                                <label
                                    class="block text-sm font-medium text-themed-primary mb-2 flex items-center gap-2 transition-colors duration-300">
                                    <i class="fas fa-tags text-purple-500 dark:text-purple-400"></i>
                                    Category <span class="text-red-500">*</span>
                                </label>
                                <select wire:model="category_id"
                                    class="w-full px-3 sm:px-4 py-3 bg-themed-tertiary border border-themed-primary rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent text-themed-primary transition-all duration-200 group-hover:border-purple-400">
                                    <option value="">Choose a category...</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Difficulty Level -->
                            <div class="group">
                                <label
                                    class="block text-sm font-medium text-themed-primary mb-2 flex items-center gap-2 transition-colors duration-300">
                                    <i class="fas fa-layer-group text-purple-500 dark:text-purple-400"></i>
                                    Difficulty Level <span class="text-red-500">*</span>
                                </label>
                                <select wire:model="difficulty_level"
                                    class="w-full px-3 sm:px-4 py-3 bg-themed-tertiary border border-themed-primary rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent text-themed-primary transition-all duration-200 group-hover:border-purple-400">
                                    @foreach ($difficultyLevels as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('difficulty_level')
                                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 2: Course Description -->
            <div x-show="currentStep === 2" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-x-8"
                x-transition:enter-end="opacity-100 transform translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform translate-x-0"
                x-transition:leave-end="opacity-0 transform -translate-x-8">

                <div
                    class="bg-themed-secondary rounded-2xl p-4 sm:p-6 lg:p-8 shadow-xl border border-themed-primary transition-colors duration-300">
                    <div class="flex items-center gap-2 sm:gap-3 mb-4 sm:mb-6">
                        <div
                            class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-align-left text-white text-lg sm:text-xl"></i>
                        </div>
                        <div>
                            <h2
                                class="text-xl sm:text-2xl font-bold text-themed-primary transition-colors duration-300">
                                Course Description & Thumbnail</h2>
                            <p class="text-themed-secondary text-sm sm:text-base transition-colors duration-300">
                                Tell students what they'll learn and add a course image</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
                        <!-- Description -->
                        <div class="space-y-4 sm:space-y-6">
                            <div>
                                <label
                                    class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">
                                    Course Description
                                </label>
                                <textarea wire:model="description" rows="8"
                                    class="w-full px-3 sm:px-4 py-3 bg-themed-tertiary border border-themed-primary rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-themed-primary placeholder-themed-secondary resize-none transition-colors duration-300"
                                    placeholder="Describe what students will learn, what skills they'll gain, and why they should take your course..."></textarea>
                                <div
                                    class="mt-2 flex justify-between text-xs text-themed-secondary transition-colors duration-300">
                                    <span>Make it engaging and detailed</span>
                                    <span x-text="`${($wire.description || '').length}/2000`"></span>
                                </div>
                                @error('description')
                                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Target Audience -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-themed-primary mb-2 flex items-center gap-2 transition-colors duration-300">
                                    <i class="fas fa-users text-blue-500 dark:text-blue-400"></i>
                                    Target Audience
                                </label>
                                <input type="text" wire:model="target_audience"
                                    class="w-full px-3 sm:px-4 py-3 bg-themed-tertiary border border-themed-primary rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-themed-primary placeholder-themed-secondary transition-colors duration-300"
                                    placeholder="e.g., Beginners, Professionals, Students">
                                @error('target_audience')
                                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Duration -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-themed-primary mb-2 flex items-center gap-2 transition-colors duration-300">
                                    <i class="fas fa-clock text-blue-500 dark:text-blue-400"></i>
                                    Estimated Duration (minutes)
                                </label>
                                <input type="number" wire:model="estimated_duration_minutes"
                                    class="w-full px-3 sm:px-4 py-3 bg-themed-tertiary border border-themed-primary rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-themed-primary placeholder-themed-secondary transition-colors duration-300"
                                    placeholder="e.g., 120">
                                @error('estimated_duration_minutes')
                                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Enhanced Thumbnail Upload Section -->
                        <div class="space-y-4 sm:space-y-6">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label
                                        class="text-sm font-medium text-themed-primary flex items-center gap-2 transition-colors duration-300">
                                        <i class="fas fa-image text-blue-500 dark:text-blue-400"></i>
                                        Course Thumbnail
                                    </label>
                                </div>

                                <!-- Enhanced Image Preview and Upload Area -->
                                <div class="relative">
                                    <!-- File Input (Hidden) -->
                                    <input type="file" wire:model="thumbnail" class="hidden" id="thumbnail-upload"
                                        accept="image/jpeg,image/png,image/jpg">

                                    <!-- Image Preview Area -->
                                    <div
                                        class="border-2 border-dashed border-themed-primary rounded-xl overflow-hidden bg-themed-tertiary transition-all duration-300 hover:border-blue-400 hover:bg-themed-secondary">

                                        <!-- New Uploaded Image Preview -->
                                        @if ($thumbnail)
                                            <div class="relative group">
                                                <img src="{{ $thumbnail->temporaryUrl() }}" alt="New thumbnail preview"
                                                    class="w-full h-48 sm:h-56 object-cover transition-transform duration-300 group-hover:scale-105">

                                                <!-- New Image Badge -->
                                                <div class="absolute top-3 left-3">
                                                    <span
                                                        class="bg-green-500 text-white px-2 py-1 rounded-lg text-xs font-bold shadow-md">
                                                        <i class="fas fa-plus mr-1"></i> New Image
                                                    </span>
                                                </div>

                                                <!-- Remove Button -->
                                                <button type="button" wire:click="removeThumbnail"
                                                    class="absolute top-3 right-3 bg-red-500 hover:bg-red-600 text-white p-2 rounded-lg shadow-md transition-colors duration-200 opacity-0 group-hover:opacity-100">
                                                    <i class="fas fa-trash text-sm"></i>
                                                </button>

                                                <!-- Change Image Button -->
                                                <label for="thumbnail-upload"
                                                    class="absolute bottom-3 left-1/2 transform -translate-x-1/2 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg shadow-md cursor-pointer transition-all duration-200 opacity-0 group-hover:opacity-100">
                                                    <i class="fas fa-camera mr-2"></i>Change Image
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
                                                    <span
                                                        class="bg-blue-500 text-white px-2 py-1 rounded-lg text-xs font-bold shadow-md">
                                                        <i class="fas fa-image mr-1"></i> Current Image
                                                    </span>
                                                </div>

                                                <!-- Remove Button -->
                                                <button type="button" wire:click="removeThumbnail"
                                                    class="absolute top-3 right-3 bg-red-500 hover:bg-red-600 text-white p-2 rounded-lg shadow-md transition-colors duration-200 opacity-0 group-hover:opacity-100">
                                                    <i class="fas fa-trash text-sm"></i>
                                                </button>

                                                <!-- Change Image Button -->
                                                <label for="thumbnail-upload"
                                                    class="absolute bottom-3 left-1/2 transform -translate-x-1/2 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg shadow-md cursor-pointer transition-all duration-200 opacity-0 group-hover:opacity-100">
                                                    <i class="fas fa-camera mr-2"></i>Change Image
                                                </label>
                                            </div>

                                            <!-- No Image State -->
                                        @else
                                            <label for="thumbnail-upload" class="cursor-pointer block">
                                                <div
                                                    class="h-48 sm:h-56 flex flex-col items-center justify-center text-center p-6 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors duration-300">
                                                    <div class="mb-4">
                                                        <i
                                                            class="fas fa-cloud-upload-alt text-4xl sm:text-5xl text-themed-secondary mb-4 transition-colors duration-300"></i>
                                                    </div>
                                                    <h4
                                                        class="text-lg font-semibold text-themed-primary mb-2 transition-colors duration-300">
                                                        {{ ($isEditMode ?? false) ? (($shouldRemoveThumbnail ?? false) ? 'Upload New Thumbnail' : 'No Thumbnail Set') : 'Upload Course Thumbnail' }}
                                                    </h4>
                                                    <p
                                                        class="text-themed-secondary text-sm mb-4 transition-colors duration-300">
                                                        Click here to browse and select an image
                                                    </p>
                                                    <div
                                                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                                                        <i class="fas fa-plus mr-2"></i>Select Image
                                                    </div>
                                                </div>
                                            </label>
                                        @endif
                                    </div>

                                    <!-- Upload Progress -->
                                    <div wire:loading wire:target="thumbnail"
                                        class="absolute inset-0 bg-themed-secondary/90 rounded-xl flex items-center justify-center backdrop-blur-sm z-10 transition-colors duration-300">
                                        <div class="text-center">
                                            <div
                                                class="animate-spin rounded-full h-8 w-8 border-4 border-blue-500 border-t-transparent mx-auto mb-3">
                                            </div>
                                            <p
                                                class="text-sm font-medium text-themed-primary transition-colors duration-300">
                                                Uploading...</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Image Requirements -->
                                <div
                                    class="mt-3 p-3 bg-themed-tertiary rounded-lg border border-themed-primary transition-colors duration-300">
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
                                    <div
                                        class="mt-2 p-3 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-700 transition-colors duration-300">
                                        <p class="text-sm text-red-600 dark:text-red-400 flex items-center gap-2">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            {{ $message }}
                                        </p>
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3: Pricing -->
            <div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-x-8"
                x-transition:enter-end="opacity-100 transform translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform translate-x-0"
                x-transition:leave-end="opacity-0 transform -translate-x-8">

                <div
                    class="bg-themed-secondary rounded-2xl p-4 sm:p-6 lg:p-8 shadow-xl border border-themed-primary transition-colors duration-300">
                    <div class="flex items-center gap-2 sm:gap-3 mb-4 sm:mb-6">
                        <div
                            class="w-10 h-10 sm:w-12 sm:h-12 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-dollar-sign text-white text-lg sm:text-xl"></i>
                        </div>
                        <div>
                            <h2
                                class="text-xl sm:text-2xl font-bold text-themed-primary transition-colors duration-300">
                                Pricing & Access</h2>
                            <p class="text-themed-secondary text-sm sm:text-base transition-colors duration-300">
                                Set your course pricing strategy</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
                        <!-- Free Course Option -->
                        <div class="relative">
                            <input type="checkbox" wire:model.live="is_free" id="free_option" class="sr-only">
                            <label for="free_option"
                                class="block p-4 sm:p-6 rounded-xl cursor-pointer transition-all duration-200 border-2"
                                :class="$wire.is_free ? 'border-green-500 bg-green-50 dark:bg-green-500/20' : 'border-themed-primary bg-themed-tertiary hover:border-green-400'">
                                <div class="text-center">
                                    <div class="w-12 h-12 sm:w-16 sm:h-16 mx-auto mb-3 sm:mb-4 rounded-full flex items-center justify-center transition-colors duration-300"
                                        :class="$wire.is_free ? 'bg-green-500' : 'bg-themed-secondary border border-themed-primary'">
                                        <i class="fas fa-gift text-lg sm:text-2xl"
                                            :class="$wire.is_free ? 'text-white' : 'text-themed-secondary'"></i>
                                    </div>
                                    <h3
                                        class="text-base sm:text-lg font-semibold text-themed-primary mb-2 transition-colors duration-300">
                                        Free Course</h3>
                                    <p class="text-themed-secondary text-xs sm:text-sm transition-colors duration-300">
                                        Open access for everyone</p>
                                </div>
                            </label>
                        </div>

                        <!-- Premium Course Option -->
                        <div class="relative">
                            <input type="checkbox" wire:model.live="is_premium" id="premium_option" class="sr-only">
                            <label for="premium_option"
                                class="block p-4 sm:p-6 rounded-xl cursor-pointer transition-all duration-200 border-2"
                                :class="$wire.is_premium ? 'border-purple-500 bg-purple-50 dark:bg-purple-500/20' : 'border-themed-primary bg-themed-tertiary hover:border-purple-400'">
                                <div class="text-center">
                                    <div class="w-12 h-12 sm:w-16 sm:h-16 mx-auto mb-3 sm:mb-4 rounded-full flex items-center justify-center transition-colors duration-300"
                                        :class="$wire.is_premium ? 'bg-purple-500' : 'bg-themed-secondary border border-themed-primary'">
                                        <i class="fas fa-crown text-lg sm:text-2xl"
                                            :class="$wire.is_premium ? 'text-white' : 'text-themed-secondary'"></i>
                                    </div>
                                    <h3
                                        class="text-base sm:text-lg font-semibold text-themed-primary mb-2 transition-colors duration-300">
                                        Premium Course</h3>
                                    <p class="text-themed-secondary text-xs sm:text-sm transition-colors duration-300">
                                        Paid course with premium features</p>
                                </div>
                            </label>
                        </div>

                        <!-- Regular Paid Course -->
                        <div class="relative">
                            <button type="button" wire:click="setPaidCourse"
                                class="block w-full p-4 sm:p-6 rounded-xl border-2 transition-all duration-200"
                                :class="!$wire.is_free && !$wire.is_premium ? 'border-blue-500 bg-blue-50 dark:bg-blue-500/20' : 'border-themed-primary bg-themed-tertiary hover:border-blue-400'">
                                <div class="text-center">
                                    <div class="w-12 h-12 sm:w-16 sm:h-16 mx-auto mb-3 sm:mb-4 rounded-full flex items-center justify-center transition-colors duration-300"
                                        :class="!$wire.is_free && !$wire.is_premium ? 'bg-blue-500' : 'bg-themed-secondary border border-themed-primary'">
                                        <i class="fas fa-money-bill-wave text-lg sm:text-2xl"
                                            :class="!$wire.is_free && !$wire.is_premium ? 'text-white' : 'text-themed-secondary'"></i>
                                    </div>
                                    <h3
                                        class="text-base sm:text-lg font-semibold text-themed-primary mb-2 transition-colors duration-300">
                                        Paid Course</h3>
                                    <p class="text-themed-secondary text-xs sm:text-sm transition-colors duration-300">
                                        Standard paid course</p>
                                </div>
                            </button>
                        </div>
                    </div>

                    <!-- Price Input -->
                    <div x-show="!$wire.is_free" x-transition class="mb-6">
                        <label
                            class="block text-sm font-medium text-themed-primary mb-2 flex items-center gap-2 transition-colors duration-300">
                            <i class="fas fa-tag text-green-500 dark:text-green-400"></i>
                            Course Price ($)
                        </label>
                        <div class="relative">
                            <span
                                class="absolute left-3 top-1/2 transform -translate-y-1/2 text-themed-secondary text-lg transition-colors duration-300">$</span>
                            <input type="number" wire:model="price" step="0.01" min="0"
                                class="w-full pl-10 pr-4 py-3 bg-themed-tertiary border border-themed-primary rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent text-themed-primary placeholder-themed-secondary transition-colors duration-300"
                                placeholder="9.99">
                        </div>
                        @error('price')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Publishing Options -->
                    <div class="space-y-4">
                        <div class="flex items-center space-x-3 sm:space-x-4">
                            <input type="checkbox" wire:model="is_published" id="publish_now"
                                class="h-4 w-4 sm:h-5 sm:w-5 text-green-500 rounded border-themed-primary bg-themed-tertiary focus:ring-green-400">
                            <label for="publish_now"
                                class="text-themed-primary flex items-center gap-2 text-sm sm:text-base transition-colors duration-300">
                                <i class="fas fa-globe text-green-500 dark:text-green-400"></i>
                                Publish immediately after approval
                            </label>
                        </div>

                        <!-- Schedule Publishing -->
                        <div x-show="$wire.is_published" x-transition>
                            <label
                                class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">
                                Schedule Publication (Optional)
                            </label>
                            <input type="datetime-local" wire:model="scheduled_publish_at"
                                class="w-full px-3 sm:px-4 py-3 bg-themed-tertiary border border-themed-primary rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent text-themed-primary transition-colors duration-300">
                            <p class="text-xs text-themed-secondary mt-1 transition-colors duration-300">Leave empty to
                                publish immediately
                                when approved</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 4: Additional Details -->
            <div x-show="currentStep === 4" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-x-8"
                x-transition:enter-end="opacity-100 transform translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform translate-x-0"
                x-transition:leave-end="opacity-0 transform -translate-x-8">

                <div
                    class="bg-themed-secondary rounded-2xl p-4 sm:p-6 lg:p-8 shadow-xl border border-themed-primary transition-colors duration-300">
                    <div class="flex items-center gap-2 sm:gap-3 mb-4 sm:mb-6">
                        <div
                            class="w-10 h-10 sm:w-12 sm:h-12 bg-indigo-500 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-list-ul text-white text-lg sm:text-xl"></i>
                        </div>
                        <div>
                            <h2
                                class="text-xl sm:text-2xl font-bold text-themed-primary transition-colors duration-300">
                                Additional Details</h2>
                            <p class="text-themed-secondary text-sm sm:text-base transition-colors duration-300">
                                Enhance your course with extra information</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
                        <!-- Left Column -->
                        <div class="space-y-4 sm:space-y-6">
                            <!-- Learning Outcomes -->
                            <div
                                class="bg-indigo-50 dark:bg-indigo-900/30 p-4 sm:p-6 rounded-xl border border-indigo-200 dark:border-indigo-800 transition-colors duration-300">
                                <label
                                    class="block text-sm font-medium text-indigo-700 dark:text-indigo-300 mb-4 flex items-center gap-2 transition-colors duration-300">
                                    <i class="fas fa-lightbulb text-indigo-500 dark:text-indigo-400"></i>
                                    Learning Outcomes
                                </label>
                                <div class="space-y-3">
                                    @foreach ($learning_outcomes as $index => $outcome)
                                        <div class="flex items-center gap-3" wire:key="outcome-{{ $index }}">
                                            <div class="flex-1">
                                                <input type="text" wire:model="learning_outcomes.{{ $index }}"
                                                    class="w-full px-3 sm:px-4 py-2 sm:py-3 bg-themed-secondary border border-themed-primary rounded-lg text-themed-primary placeholder-themed-secondary focus:ring-2 focus:ring-indigo-500 text-sm sm:text-base transition-colors duration-300"
                                                    placeholder="Students will be able to...">
                                            </div>
                                            @if (count($learning_outcomes) > 1)
                                                <button type="button" wire:click="removeLearningOutcome({{ $index }})"
                                                    class="text-red-500 hover:text-red-600 dark:text-red-400 dark:hover:text-red-300 p-2 transition-colors duration-300">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" wire:click="addLearningOutcome"
                                    class="mt-3 px-3 sm:px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2 text-sm sm:text-base">
                                    <i class="fas fa-plus"></i> Add Outcome
                                </button>
                            </div>

                            <!-- Prerequisites -->
                            <div
                                class="bg-orange-50 dark:bg-orange-900/30 p-4 sm:p-6 rounded-xl border border-orange-200 dark:border-orange-800 transition-colors duration-300">
                                <label
                                    class="block text-sm font-medium text-orange-700 dark:text-orange-300 mb-4 flex items-center gap-2 transition-colors duration-300">
                                    <i class="fas fa-exclamation-circle text-orange-500 dark:text-orange-400"></i>
                                    Prerequisites
                                </label>
                                <div class="space-y-3">
                                    @foreach ($prerequisites as $index => $prerequisite)
                                        <div class="flex items-center gap-3" wire:key="prereq-{{ $index }}">
                                            <div class="flex-1">
                                                <input type="text" wire:model="prerequisites.{{ $index }}"
                                                    class="w-full px-3 sm:px-4 py-2 sm:py-3 bg-themed-secondary border border-themed-primary rounded-lg text-themed-primary placeholder-themed-secondary focus:ring-2 focus:ring-orange-500 text-sm sm:text-base transition-colors duration-300"
                                                    placeholder="Basic knowledge of...">
                                            </div>
                                            @if (count($prerequisites) > 1)
                                                <button type="button" wire:click="removePrerequisite({{ $index }})"
                                                    class="text-red-500 hover:text-red-600 dark:text-red-400 dark:hover:text-red-300 p-2 transition-colors duration-300">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" wire:click="addPrerequisite"
                                    class="mt-3 px-3 sm:px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors flex items-center gap-2 text-sm sm:text-base">
                                    <i class="fas fa-plus"></i> Add Prerequisite
                                </button>
                            </div>

                            <!-- Completion Threshold -->
                            <div
                                class="bg-green-50 dark:bg-green-900/30 p-4 sm:p-6 rounded-xl border border-green-200 dark:border-green-800 transition-colors duration-300">
                                <label
                                    class="block text-sm font-medium text-green-700 dark:text-green-300 mb-2 flex items-center gap-2 transition-colors duration-300">
                                    <i class="fas fa-percentage text-green-500 dark:text-green-400"></i>
                                    Completion Threshold (%)
                                </label>
                                <div class="relative">
                                    <input type="number" wire:model="completion_rate_threshold" min="0" max="100"
                                        step="0.01"
                                        class="w-full px-3 sm:px-4 py-3 bg-themed-tertiary border border-themed-primary rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent text-themed-primary placeholder-themed-secondary text-sm sm:text-base transition-colors duration-300">
                                    <span
                                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-themed-secondary transition-colors duration-300">%</span>
                                </div>
                                <p class="text-xs text-themed-secondary mt-2 transition-colors duration-300">
                                    Minimum completion rate for certificate eligibility (0-100%)
                                </p>
                                @error('completion_rate_threshold')
                                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-4 sm:space-y-6">
                            <!-- Frequently Asked Questions -->
                            <div
                                class="bg-teal-50 dark:bg-teal-900/30 p-4 sm:p-6 rounded-xl border border-teal-200 dark:border-teal-800 transition-colors duration-300">
                                <label
                                    class="block text-sm font-medium text-teal-700 dark:text-teal-300 mb-4 flex items-center gap-2 transition-colors duration-300">
                                    <i class="fas fa-question-circle text-teal-500 dark:text-teal-400"></i>
                                    Frequently Asked Questions
                                </label>
                                <div class="space-y-4">
                                    @foreach ($faqs as $index => $faq)
                                        <div class="bg-themed-secondary p-3 sm:p-4 rounded-lg border border-themed-primary transition-colors duration-300"
                                            wire:key="faq-{{ $index }}">
                                            <div class="space-y-3">
                                                <input type="text" wire:model="faqs.{{ $index }}.question"
                                                    class="w-full px-3 sm:px-4 py-2 bg-themed-tertiary border border-themed-primary rounded-lg text-themed-primary placeholder-themed-secondary focus:ring-2 focus:ring-teal-500 text-sm sm:text-base transition-colors duration-300"
                                                    placeholder="What is this course about?">
                                                <textarea wire:model="faqs.{{ $index }}.answer" rows="2"
                                                    class="w-full px-3 sm:px-4 py-2 bg-themed-tertiary border border-themed-primary rounded-lg text-themed-primary placeholder-themed-secondary resize-none focus:ring-2 focus:ring-teal-500 text-sm sm:text-base transition-colors duration-300"
                                                    placeholder="This course covers..."></textarea>
                                            </div>
                                            @if (count($faqs) > 1)
                                                <button type="button" wire:click="removeFaq({{ $index }})"
                                                    class="mt-2 text-red-500 hover:text-red-600 dark:text-red-400 dark:hover:text-red-300 text-sm flex items-center gap-1 transition-colors duration-300">
                                                    <i class="fas fa-trash"></i> Remove FAQ
                                                </button>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" wire:click="addFaq"
                                    class="mt-3 px-3 sm:px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors flex items-center gap-2 text-sm sm:text-base">
                                    <i class="fas fa-plus"></i> Add FAQ
                                </button>
                            </div>

                            <!-- Materials Included -->
                            <div
                                class="bg-purple-50 dark:bg-purple-900/30 p-4 sm:p-6 rounded-xl border border-purple-200 dark:border-purple-800 transition-colors duration-300">
                                <label
                                    class="block text-sm font-medium text-purple-700 dark:text-purple-300 mb-4 flex items-center gap-2 transition-colors duration-300">
                                    <i class="fas fa-box-open text-purple-500 dark:text-purple-400"></i>
                                    Materials Included
                                </label>
                                <div class="space-y-3">
                                    @foreach ($materials_included as $index => $material)
                                        <div class="flex items-center gap-3" wire:key="material-{{ $index }}">
                                            <div class="flex-1">
                                                <input type="text" wire:model="materials_included.{{ $index }}"
                                                    class="w-full px-3 sm:px-4 py-2 sm:py-3 bg-themed-secondary border border-themed-primary rounded-lg text-themed-primary placeholder-themed-secondary focus:ring-2 focus:ring-purple-500 text-sm sm:text-base transition-colors duration-300"
                                                    placeholder="Downloadable resources, code files...">
                                            </div>
                                            @if (count($materials_included) > 1)
                                                <button type="button" wire:click="removeMaterial({{ $index }})"
                                                    class="text-red-500 hover:text-red-600 dark:text-red-400 dark:hover:text-red-300 p-2 transition-colors duration-300">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" wire:click="addMaterial"
                                    class="mt-3 px-3 sm:px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center gap-2 text-sm sm:text-base">
                                    <i class="fas fa-plus"></i> Add Material
                                </button>
                            </div>

                            <!-- Syllabus Overview -->
                            <div
                                class="bg-blue-50 dark:bg-blue-900/30 p-4 sm:p-6 rounded-xl border border-blue-200 dark:border-blue-800 transition-colors duration-300">
                                <label
                                    class="block text-sm font-medium text-blue-700 dark:text-blue-300 mb-4 flex items-center gap-2 transition-colors duration-300">
                                    <i class="fas fa-book-open text-blue-500 dark:text-blue-400"></i>
                                    Syllabus Overview
                                </label>
                                <textarea wire:model="syllabus_overview" rows="4"
                                    class="w-full px-3 sm:px-4 py-3 bg-themed-secondary border border-themed-primary rounded-lg text-themed-primary placeholder-themed-secondary focus:ring-2 focus:ring-blue-500 resize-none text-sm sm:text-base transition-colors duration-300"
                                    placeholder="Provide a high-level overview of your course modules and structure..."></textarea>
                                @error('syllabus_overview')
                                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 5: Review & Submit -->
            <div x-show="currentStep === 5" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-x-8"
                x-transition:enter-end="opacity-100 transform translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform translate-x-0"
                x-transition:leave-end="opacity-0 transform -translate-x-8">

                <div
                    class="bg-themed-secondary rounded-2xl p-4 sm:p-6 lg:p-8 shadow-xl border border-themed-primary transition-colors duration-300">
                    <div class="flex items-center gap-2 sm:gap-3 mb-4 sm:mb-6">
                        <div
                            class="w-10 h-10 sm:w-12 sm:h-12 bg-yellow-500 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-check-circle text-white text-lg sm:text-xl"></i>
                        </div>
                        <div>
                            <h2
                                class="text-xl sm:text-2xl font-bold text-themed-primary transition-colors duration-300">
                                Review & Submit</h2>
                            <p class="text-themed-secondary text-sm sm:text-base transition-colors duration-300">
                                Review your course details before publishing</p>
                        </div>
                    </div>

                    <!-- Course Preview Card -->
                    <div
                        class="bg-themed-tertiary rounded-xl p-4 sm:p-6 mb-6 sm:mb-8 border border-themed-primary transition-colors duration-300">
                        <div class="flex flex-col lg:flex-row gap-4 sm:gap-6">
                            <!-- Thumbnail -->
                            <div class="flex-shrink-0">
                                @if ($thumbnail)
                                    <img src="{{ $thumbnail->temporaryUrl() }}" alt="Course thumbnail"
                                        class="w-full lg:w-48 h-32 object-cover rounded-lg shadow-md">
                                @elseif(($isEditMode ?? false) && isset($existingThumbnail) && $existingThumbnail && !($shouldRemoveThumbnail ?? false))
                                    <img src="{{ asset('storage/' . $existingThumbnail) }}" alt="Course thumbnail"
                                        class="w-full lg:w-48 h-32 object-cover rounded-lg shadow-md">
                                @else
                                    <div
                                        class="w-full lg:w-48 h-32 bg-themed-secondary rounded-lg flex items-center justify-center border border-themed-primary transition-colors duration-300">
                                        <i
                                            class="fas fa-image text-3xl text-themed-secondary transition-colors duration-300"></i>
                                    </div>
                                @endif
                            </div>

                            <!-- Course Details -->
                            <div class="flex-1">
                                <h3
                                    class="text-xl sm:text-2xl font-bold text-themed-primary mb-2 transition-colors duration-300">
                                    {{ $title ?? 'Untitled Course' }}
                                </h3>
                                <p
                                    class="text-themed-secondary mb-3 sm:mb-4 text-sm sm:text-base transition-colors duration-300">
                                    {{ $subtitle ?? 'No subtitle provided' }}
                                </p>

                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 text-sm">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-tag text-purple-500 dark:text-purple-400"></i>
                                        <span class="text-themed-primary transition-colors duration-300">
                                            {{ $categories->where('id', $category_id)->first()->name ?? 'No category' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-layer-group text-blue-500 dark:text-blue-400"></i>
                                        <span class="text-themed-primary transition-colors duration-300">
                                            {{ $difficultyLevels[$difficulty_level] ?? 'Not specified' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-clock text-green-500 dark:text-green-400"></i>
                                        <span class="text-themed-primary transition-colors duration-300">
                                            {{ $estimated_duration_minutes ?? 0 }} minutes
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-dollar-sign text-yellow-500 dark:text-yellow-400"></i>
                                        <span class="text-themed-primary transition-colors duration-300">
                                            @if ($is_free)
                                                Free
                                            @elseif($is_premium)
                                                Premium - ${{ number_format($price, 2) }}
                                            @else
                                                ${{ number_format($price, 2) }}
                                            @endif
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-users text-indigo-500 dark:text-indigo-400"></i>
                                        <span class="text-themed-primary transition-colors duration-300">
                                            {{ $target_audience ?? 'Not specified' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-globe text-red-500 dark:text-red-400"></i>
                                        <span class="text-themed-primary transition-colors duration-300">
                                            {{ $is_published ? 'Published' : 'Draft' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detailed Review Sections -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
                        <!-- Description & Outcomes -->
                        <div class="space-y-4 sm:space-y-6">
                            <div
                                class="bg-themed-tertiary p-4 sm:p-6 rounded-xl border border-themed-primary transition-colors duration-300">
                                <h4
                                    class="text-lg font-semibold text-themed-primary mb-3 flex items-center gap-2 transition-colors duration-300">
                                    <i class="fas fa-align-left text-blue-500 dark:text-blue-400"></i>
                                    Description
                                </h4>
                                <p
                                    class="text-themed-secondary text-sm sm:text-base leading-relaxed transition-colors duration-300">
                                    {{ $description ?: 'No description provided' }}
                                </p>
                            </div>

                            @if (!empty(array_filter($learning_outcomes)))
                                <div
                                    class="bg-themed-tertiary p-4 sm:p-6 rounded-xl border border-themed-primary transition-colors duration-300">
                                    <h4
                                        class="text-lg font-semibold text-themed-primary mb-3 flex items-center gap-2 transition-colors duration-300">
                                        <i class="fas fa-lightbulb text-indigo-500 dark:text-indigo-400"></i>
                                        Learning Outcomes
                                    </h4>
                                    <ul class="space-y-2">
                                        @foreach ($learning_outcomes as $outcome)
                                            @if (!empty(trim($outcome)))
                                                <li
                                                    class="flex items-start gap-2 text-themed-secondary text-sm sm:text-base transition-colors duration-300">
                                                    <i
                                                        class="fas fa-check text-green-500 dark:text-green-400 mt-1 flex-shrink-0"></i>
                                                    <span>{{ $outcome }}</span>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>

                        <!-- Requirements & Materials -->
                        <div class="space-y-4 sm:space-y-6">
                            @if (!empty(array_filter($prerequisites)))
                                <div
                                    class="bg-themed-tertiary p-4 sm:p-6 rounded-xl border border-themed-primary transition-colors duration-300">
                                    <h4
                                        class="text-lg font-semibold text-themed-primary mb-3 flex items-center gap-2 transition-colors duration-300">
                                        <i class="fas fa-exclamation-circle text-orange-500 dark:text-orange-400"></i>
                                        Prerequisites
                                    </h4>
                                    <ul class="space-y-2">
                                        @foreach ($prerequisites as $prerequisite)
                                            @if (!empty(trim($prerequisite)))
                                                <li
                                                    class="flex items-start gap-2 text-themed-secondary text-sm sm:text-base transition-colors duration-300">
                                                    <i
                                                        class="fas fa-circle text-orange-500 dark:text-orange-400 text-xs mt-2 flex-shrink-0"></i>
                                                    <span>{{ $prerequisite }}</span>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if (!empty(array_filter($materials_included)))
                                <div
                                    class="bg-themed-tertiary p-4 sm:p-6 rounded-xl border border-themed-primary transition-colors duration-300">
                                    <h4
                                        class="text-lg font-semibold text-themed-primary mb-3 flex items-center gap-2 transition-colors duration-300">
                                        <i class="fas fa-box-open text-teal-500 dark:text-teal-400"></i>
                                        Materials Included
                                    </h4>
                                    <ul class="space-y-2">
                                        @foreach ($materials_included as $material)
                                            @if (!empty(trim($material)))
                                                <li
                                                    class="flex items-start gap-2 text-themed-secondary text-sm sm:text-base transition-colors duration-300">
                                                    <i
                                                        class="fas fa-check-circle text-teal-500 dark:text-teal-400 mt-1 flex-shrink-0"></i>
                                                    <span>{{ $material }}</span>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if (!empty(array_filter($tags)))
                                <div
                                    class="bg-themed-tertiary p-4 sm:p-6 rounded-xl border border-themed-primary transition-colors duration-300">
                                    <h4
                                        class="text-lg font-semibold text-themed-primary mb-3 flex items-center gap-2 transition-colors duration-300">
                                        <i class="fas fa-tags text-purple-500 dark:text-purple-400"></i>
                                        Tags
                                    </h4>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($tags as $tag)
                                            @if (!empty(trim($tag)))
                                                <span
                                                    class="px-3 py-1 bg-purple-100 dark:bg-purple-900/50 text-purple-700 dark:text-purple-300 rounded-full text-sm font-medium transition-colors duration-300">
                                                    {{ $tag }}
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Navigation Buttons -->
    <div class="mt-6 sm:mt-8 flex flex-col sm:flex-row justify-between gap-3 sm:gap-4">
        <button type="button" x-show="currentStep > 1" @click="currentStep--"
            class="order-2 sm:order-1 w-full sm:w-auto px-6 sm:px-8 py-3 sm:py-4 bg-themed-secondary hover:bg-themed-tertiary text-themed-primary border border-themed-primary rounded-xl font-semibold transition-all duration-200 hover:scale-105 flex items-center justify-center gap-2">
            <i class="fas fa-arrow-left"></i>
            Previous Step
        </button>

        <div class="order-1 sm:order-2 flex flex-col sm:flex-row gap-3 sm:gap-4 w-full sm:w-auto">
            <!-- Save as Draft -->
            <button type="button" wire:click="saveDraft" wire:loading.attr="disabled"
                class="w-full sm:w-auto px-6 sm:px-8 py-3 sm:py-4 bg-gray-500 hover:bg-gray-600 text-white rounded-xl font-semibold transition-all duration-200 hover:scale-105 flex items-center justify-center gap-2 disabled:opacity-50">
                <i class="fas fa-save"></i>
                <span wire:loading.remove wire:target="saveDraft">Save as Draft</span>
                <span wire:loading wire:target="saveDraft">Saving...</span>
            </button>

            <!-- Next Step / Submit -->
            <button type="button" x-show="currentStep < totalSteps" @click="currentStep++" wire:loading.attr="disabled"
                class="w-full sm:w-auto px-6 sm:px-8 py-3 sm:py-4 bg-purple-500 hover:bg-purple-600 text-white rounded-xl font-semibold transition-all duration-200 hover:scale-105 flex items-center justify-center gap-2 disabled:opacity-50">
                Next Step
                <i class="fas fa-arrow-right"></i>
            </button>

            <!-- Submit Course -->
            <button type="button" x-show="currentStep === totalSteps" wire:click="save" wire:loading.attr="disabled"
                class="w-full sm:w-auto px-6 sm:px-8 py-3 sm:py-4 bg-green-500 hover:bg-green-600 text-white rounded-xl font-semibold transition-all duration-200 hover:scale-105 flex items-center justify-center gap-2 disabled:opacity-50">
                <i class="fas fa-rocket"></i>
                <span wire:loading.remove wire:target="save">Submit Course</span>
                <span wire:loading wire:target="save">Submitting...</span>
            </button>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div wire:loading.class.remove="hidden"
        class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-themed-secondary rounded-2xl p-6 sm:p-8 max-w-sm mx-4">
            <div
                class="animate-spin rounded-full h-12 w-12 border-4 border-purple-500 border-t-transparent mx-auto mb-4">
            </div>
            <p class="text-themed-primary text-center font-semibold text-lg transition-colors duration-300">
                Processing...</p>
            <p class="text-themed-secondary text-center mt-2 transition-colors duration-300">Please wait while we save
                your course</p>
        </div>
    </div>
</div>