<div class="relative">
    <div class="flex items-center justify-between mb-2 overflow-x-auto pb-2">
        <template x-for="step in totalSteps" :key="step">
            <div class="flex items-center min-w-0 flex-shrink-0">
                <div class="w-6 h-6 sm:w-8 sm:h-8 rounded-full flex items-center justify-center text-xs sm:text-sm font-semibold transition-all duration-300"
                    :class="step <= currentStep ? 'bg-{{ $isEditMode ?? false ? 'orange' : 'purple' }}-500 text-white' : 'bg-themed-tertiary text-themed-secondary border border-themed-primary'">
                    <span x-text="step"></span>
                </div>
                <div x-show="step < totalSteps" class="h-0.5 w-8 sm:w-16 mx-1 sm:mx-2 transition-colors duration-300"
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