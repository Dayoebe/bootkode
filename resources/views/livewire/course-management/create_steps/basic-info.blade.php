<div class="bg-themed-secondary rounded-lg p-4 sm:p-6 lg:p-8 shadow-sm border border-themed-primary transition-colors duration-300">
    <div class="flex items-center gap-2 sm:gap-3 mb-4 sm:mb-6">
        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-action rounded-full flex items-center justify-center flex-shrink-0">
            <i class="fas fa-info-circle text-action-foreground text-lg sm:text-xl"></i>
        </div>
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-themed-primary transition-colors duration-300">Basic Information</h2>
            <p class="text-themed-secondary text-sm sm:text-base transition-colors duration-300">Let's start with the essentials</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
        <!-- Left Column -->
        <div class="space-y-4 sm:space-y-6">
            <!-- Course Title - FIXED: Changed wire:model.live to wire:model.blur -->
            <div class="group">
                <label class="block text-sm font-medium text-themed-primary mb-2 flex items-center gap-2 transition-colors duration-300">
                    <i class="fas fa-heading text-brand dark:text-brand"></i>
                    Course Title <span class="text-red-500">*</span>
                </label>
                <input aria-label="e.g., Complete Web Development Bootcamp" type="text"
                       wire:model.blur="title"
                       class="w-full px-3 sm:px-4 py-3 bg-themed-tertiary border border-themed-primary rounded-lg focus:ring-2 focus:ring-brand/30 focus:border-transparent text-themed-primary placeholder-themed-secondary transition-all duration-200 group-hover:border-brand"
                       placeholder="e.g., Complete Web Development Bootcamp">
                @error('title')
                    <p class="mt-2 text-sm text-red-500 ">{{ $message }}</p>
                @enderror
            </div>

            <!-- Course Slug -->
            <div class="group">
                <label class="block text-sm font-medium text-themed-primary mb-2 flex items-center gap-2 transition-colors duration-300">
                    <i class="fas fa-link text-brand dark:text-brand"></i>
                    Course URL Slug <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-themed-secondary text-sm hidden sm:inline transition-colors duration-300">bootkode.com/</span>
                    <input aria-label="course-web-development" type="text"
                           wire:model="slug"
                           class="w-full pl-3 sm:pl-32 pr-4 py-3 bg-themed-tertiary border border-themed-primary rounded-lg focus:ring-2 focus:ring-brand/30 focus:border-transparent text-themed-primary placeholder-themed-secondary transition-all duration-200 group-hover:border-brand"
                           placeholder="course-web-development">
                </div>
                <p class="text-xs text-themed-secondary mt-1 transition-colors duration-300">
                    {{ ($isEditMode ?? false) ? 'Edit carefully - changing this may break existing links' : 'Auto-generated from title. Use lowercase letters, numbers, and hyphens only.' }}
                </p>
                @error('slug')
                    <p class="mt-2 text-sm text-red-500 ">{{ $message }}</p>
                @enderror
            </div>

            <!-- Course Subtitle -->
            <div class="group">
                <label class="block text-sm font-medium text-themed-primary mb-2 flex items-center gap-2 transition-colors duration-300">
                    <i class="fas fa-subtitle text-brand dark:text-brand"></i>
                    Course Subtitle
                </label>
                <input aria-label="A catchy subtitle for your course" type="text"
                       wire:model="subtitle"
                       class="w-full px-3 sm:px-4 py-3 bg-themed-tertiary border border-themed-primary rounded-lg focus:ring-2 focus:ring-brand/30 focus:border-transparent text-themed-primary placeholder-themed-secondary transition-all duration-200 group-hover:border-brand"
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
                <label class="block text-sm font-medium text-themed-primary mb-2 flex items-center gap-2 transition-colors duration-300">
                    <i class="fas fa-tags text-brand dark:text-brand"></i>
                    Category <span class="text-red-500">*</span>
                </label>
                <select aria-label="Category id" wire:model="category_id"
                        class="w-full px-3 sm:px-4 py-3 bg-themed-tertiary border border-themed-primary rounded-lg focus:ring-2 focus:ring-brand/30 focus:border-transparent text-themed-primary transition-all duration-200 group-hover:border-brand">
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
                <label class="block text-sm font-medium text-themed-primary mb-2 flex items-center gap-2 transition-colors duration-300">
                    <i class="fas fa-layer-group text-brand dark:text-brand"></i>
                    Difficulty Level <span class="text-red-500">*</span>
                </label>
                <select aria-label="Difficulty level" wire:model="difficulty_level"
                        class="w-full px-3 sm:px-4 py-3 bg-themed-tertiary border border-themed-primary rounded-lg focus:ring-2 focus:ring-brand/30 focus:border-transparent text-themed-primary transition-all duration-200 group-hover:border-brand">
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
