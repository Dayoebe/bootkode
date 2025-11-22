<div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-x-8"
    x-transition:enter-end="opacity-100 transform translate-x-0" x-transition:leave="transition ease-in duration-200"
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
                <h2 class="text-xl sm:text-2xl font-bold text-themed-primary transition-colors duration-300">
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
                <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">
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