{{-- resources/views/livewire/course-management/create-course.blade.php --}}
<div class="min-h-screen bg-themed-primary transition-colors duration-300 p-4 sm:p-6" 
     x-data="{
         currentStep: @entangle('currentStep'),
         totalSteps: @entangle('totalSteps'),
         isEditMode: {{ json_encode($isEditMode ?? false) }}
     }">

    <!-- Header with Progress -->
    <div class="mb-6 sm:mb-8">
        <div class="bg-themed-secondary rounded-2xl p-4 sm:p-6 shadow-xl border border-themed-primary transition-colors duration-300">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 sm:mb-6">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-2xl sm:text-3xl font-bold text-themed-primary flex items-center gap-2 sm:gap-3 transition-colors duration-300">
                        <i class="fas fa-{{ $isEditMode ?? false ? 'edit' : 'rocket' }} text-{{ $isEditMode ?? false ? 'orange' : 'purple' }}-500"></i>
                        {{ ($isEditMode ?? false) ? 'Edit Course' : 'Create New Course' }}
                    </h1>
                    <p class="text-themed-secondary mt-1 sm:mt-2 text-sm sm:text-base transition-colors duration-300">
                        {{ ($isEditMode ?? false) ? 'Update your course content and settings' : 'Share your knowledge and inspire learners worldwide' }}
                    </p>
                </div>
                <div class="text-right">
                    <div class="text-sm text-themed-secondary transition-colors duration-300">Step</div>
                    <div class="text-xl sm:text-2xl font-bold text-{{ $isEditMode ?? false ? 'orange' : 'purple' }}-500" 
                         x-text="`${currentStep}/${totalSteps}`"></div>
                </div>
            </div>

            <!-- Progress Bar -->
            @include('livewire.course-management.create_steps.progress-bar')
        </div>
    </div>

    <div class="transition-colors duration-300">
        <form wire:submit="save">
            <!-- Step 1: Basic Information -->
            <div x-show="currentStep === 1" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-x-8"
                 x-transition:enter-end="opacity-100 transform translate-x-0">
                @include('livewire.course-management.create_steps.basic-info')
            </div>

            <!-- Step 2: Description & Thumbnail -->
            <div x-show="currentStep === 2" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-x-8"
                 x-transition:enter-end="opacity-100 transform translate-x-0">
                @include('livewire.course-management.create_steps.description')
            </div>

            <!-- Step 3: Pricing -->
            <div x-show="currentStep === 3" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-x-8"
                 x-transition:enter-end="opacity-100 transform translate-x-0">
                @include('livewire.course-management.create_steps.pricing')
            </div>

            <!-- Step 4: Additional Details -->
            <div x-show="currentStep === 4" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-x-8"
                 x-transition:enter-end="opacity-100 transform translate-x-0">
                @include('livewire.course-management.create_steps.details')
            </div>

            <!-- Step 5: Review & Submit -->
            <div x-show="currentStep === 5" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform translate-x-8"
                 x-transition:enter-end="opacity-100 transform translate-x-0">
                @include('livewire.course-management.create_steps.review')
            </div>
        </form>
    </div>

    <!-- Navigation Buttons -->
    @include('livewire.course-management.create_steps.navigation')

    <!-- Loading Overlay -->
    @include('livewire.course-management.create_steps.loading-overlay')
</div>