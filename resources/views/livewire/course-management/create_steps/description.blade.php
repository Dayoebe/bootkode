{{-- resources/views/livewire/course-management/steps/description.blade.php --}}
<div class="bg-themed-secondary rounded-2xl p-4 sm:p-6 lg:p-8 shadow-xl border border-themed-primary transition-colors duration-300">
    <div class="flex items-center gap-2 sm:gap-3 mb-4 sm:mb-6">
        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-500 rounded-full flex items-center justify-center flex-shrink-0">
            <i class="fas fa-align-left text-white text-lg sm:text-xl"></i>
        </div>
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-themed-primary transition-colors duration-300">
                Course Description & Thumbnail
            </h2>
            <p class="text-themed-secondary text-sm sm:text-base transition-colors duration-300">
                Tell students what they'll learn and add a course image
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
        <!-- Description Column -->
        <div class="space-y-4 sm:space-y-6">
            <div>
                <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">
                    Course Description
                </label>
                <textarea wire:model="description" 
                          rows="8"
                          class="w-full px-3 sm:px-4 py-3 bg-themed-tertiary border border-themed-primary rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-themed-primary placeholder-themed-secondary resize-none transition-colors duration-300"
                          placeholder="Describe what students will learn, what skills they'll gain, and why they should take your course..."></textarea>
                <div class="mt-2 flex justify-between text-xs text-themed-secondary transition-colors duration-300">
                    <span>Make it engaging and detailed</span>
                    <span x-text="`${($wire.description || '').length}/2000`"></span>
                </div>
                @error('description')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Target Audience -->
            <div>
                <label class="block text-sm font-medium text-themed-primary mb-2 flex items-center gap-2 transition-colors duration-300">
                    <i class="fas fa-users text-blue-500 dark:text-blue-400"></i>
                    Target Audience
                </label>
                <input type="text" 
                       wire:model="target_audience"
                       class="w-full px-3 sm:px-4 py-3 bg-themed-tertiary border border-themed-primary rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-themed-primary placeholder-themed-secondary transition-colors duration-300"
                       placeholder="e.g., Beginners, Professionals, Students">
                @error('target_audience')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Duration -->
            <div>
                <label class="block text-sm font-medium text-themed-primary mb-2 flex items-center gap-2 transition-colors duration-300">
                    <i class="fas fa-clock text-blue-500 dark:text-blue-400"></i>
                    Estimated Duration (minutes)
                </label>
                <input type="number" 
                       wire:model="estimated_duration_minutes"
                       class="w-full px-3 sm:px-4 py-3 bg-themed-tertiary border border-themed-primary rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-themed-primary placeholder-themed-secondary transition-colors duration-300"
                       placeholder="e.g., 120">
                @error('estimated_duration_minutes')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Thumbnail Column -->
        <div class="space-y-4 sm:space-y-6">
            @include('livewire.course-management.create_steps.thumbnail-upload')
        </div>
    </div>
</div>