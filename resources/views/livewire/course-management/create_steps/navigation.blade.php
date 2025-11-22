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