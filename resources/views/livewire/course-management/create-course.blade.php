<div class="min-h-screen bg-gradient-to-br from-gray-900 via-purple-900 to-gray-900 p-6" x-data="{
    currentStep: @entangle('currentStep'),
    totalSteps: @entangle('totalSteps'),
    showPreview: false,
    animateStep: true
}"
x-init="
$watch('currentStep', () => {
    animateStep = false;
    setTimeout(() => animateStep = true, 50);
});

// Listen for delayed redirect event
window.addEventListener('redirect-after-delay', (event) => {
    console.log('Redirect event received', event.detail);
    setTimeout(() => {
        console.log('Redirecting to:', event.detail.url);
        
        // Handle both relative and absolute URLs
        let redirectUrl = event.detail.url;
        if (!redirectUrl.startsWith('http')) {
            // If it's a relative URL, prepend the base URL
            redirectUrl = window.location.origin + redirectUrl;
        }
        
        window.location.href = redirectUrl;
    }, event.detail.delay);
});
">
    <!-- Header with Progress -->
    <div class=" mb-8">
        <div class="bg-gray-800/90 backdrop-blur-sm rounded-2xl p-6 shadow-2xl border border-purple-500/20">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                        <i class="fas fa-rocket text-purple-400"></i>
                        Create New Course
                    </h1>
                    <p class="text-gray-300 mt-2">Share your knowledge and inspire learners worldwide</p>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-400">Step</div>
                    <div class="text-2xl font-bold text-purple-400" x-text="`${currentStep}/${totalSteps}`"></div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="relative">
                <div class="flex items-center justify-between mb-2">
                    <template x-for="step in totalSteps" :key="step">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold transition-all duration-300"
                                :class="step <= currentStep ? 'bg-purple-500 text-white' : 'bg-gray-600 text-gray-300'">
                                <span x-text="step"></span>
                            </div>
                            <div x-show="step < totalSteps" class="h-0.5 w-16 mx-2 transition-colors duration-300"
                                :class="step < currentStep ? 'bg-purple-500' : 'bg-gray-600'"></div>
                        </div>
                    </template>
                </div>
                <div class="flex justify-between text-xs text-gray-400 mt-2">
                    <span>Basic Info</span>
                    <span>Description</span>
                    <span>Pricing</span>
                    <span>Details</span>
                    <span>Review</span>
                </div>
            </div>
        </div>
    </div>

    <form wire:submit="save" class="">
        <!-- Step 1: Basic Information -->
        <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-x-8"
            x-transition:enter-end="opacity-100 transform translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-x-0"
            x-transition:leave-end="opacity-0 transform -translate-x-8">

            <div class="bg-gray-800/90 backdrop-blur-sm rounded-2xl p-8 shadow-2xl border border-purple-500/20">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-info-circle text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">Basic Information</h2>
                        <p class="text-gray-300">Let's start with the essentials</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <!-- Course Title -->
                        <div class="group">
                            <label class="block text-sm font-medium text-purple-200 mb-2 flex items-center gap-2">
                                <i class="fas fa-heading text-purple-400"></i>
                                Course Title <span class="text-red-400">*</span>
                            </label>
                            <input type="text" wire:model.live="title"
                                class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent text-white placeholder-gray-400 transition-all duration-200 group-hover:border-purple-400"
                                placeholder="e.g., Complete Web Development Bootcamp">
                            @error('title')
                                <p class="mt-2 text-sm text-red-400 animate-pulse">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Course Slug -->
                        <div class="group">
                            <label class="block text-sm font-medium text-purple-200 mb-2 flex items-center gap-2">
                                <i class="fas fa-link text-purple-400"></i>
                                Course URL Slug <span class="text-red-400">*</span>
                            </label>
                            <div class="relative">
                                <span
                                    class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm">bootkode.com/</span>
                                <input type="text" wire:model.live="slug"
                                    class="w-full pl-40 pr-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent text-white placeholder-gray-400 transition-all duration-200 group-hover:border-purple-400"
                                    placeholder="course-web-development">
                            </div>
                            <p class="text-xs text-gray-400 mt-1">Auto-generated from title. Use lowercase letters,
                                numbers, and hyphens only.</p>
                            @error('slug')
                                <p class="mt-2 text-sm text-red-400 animate-pulse">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Course Subtitle -->
                        <div class="group">
                            <label class="block text-sm font-medium text-purple-200 mb-2 flex items-center gap-2">
                                <i class="fas fa-subtitle text-purple-400"></i>
                                Course Subtitle
                            </label>
                            <input type="text" wire:model="subtitle"
                                class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent text-white placeholder-gray-400 transition-all duration-200 group-hover:border-purple-400"
                                placeholder="A catchy subtitle for your course">
                            @error('subtitle')
                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <!-- Category -->
                        <div class="group">
                            <label class="block text-sm font-medium text-purple-200 mb-2 flex items-center gap-2">
                                <i class="fas fa-tags text-purple-400"></i>
                                Category <span class="text-red-400">*</span>
                            </label>
                            <select wire:model="category_id"
                                class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent text-white transition-all duration-200 group-hover:border-purple-400">
                                <option value="">Choose a category...</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Difficulty Level -->
                        <div class="group">
                            <label class="block text-sm font-medium text-purple-200 mb-2 flex items-center gap-2">
                                <i class="fas fa-layer-group text-purple-400"></i>
                                Difficulty Level <span class="text-red-400">*</span>
                            </label>
                            <select wire:model="difficulty_level"
                                class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent text-white transition-all duration-200 group-hover:border-purple-400">
                                @foreach ($difficultyLevels as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('difficulty_level')
                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
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

            <div class="bg-gray-800/90 backdrop-blur-sm rounded-2xl p-8 shadow-2xl border border-purple-500/20">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-align-left text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">Course Description</h2>
                        <p class="text-gray-300">Tell students what they'll learn</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Description -->
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-blue-200 mb-2">
                                Course Description
                            </label>
                            <textarea wire:model="description" rows="8"
                                class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-white placeholder-gray-400 resize-none"
                                placeholder="Describe what students will learn, what skills they'll gain, and why they should take your course..."></textarea>
                            <div class="mt-2 flex justify-between text-xs text-gray-400">
                                <span>Make it engaging and detailed</span>
                                <span x-text="`${($wire.description || '').length}/2000`"></span>
                            </div>
                            @error('description')
                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Target Audience -->
                        <div>
                            <label class="block text-sm font-medium text-blue-200 mb-2 flex items-center gap-2">
                                <i class="fas fa-users text-blue-400"></i>
                                Target Audience
                            </label>
                            <input type="text" wire:model="target_audience"
                                class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-white placeholder-gray-400"
                                placeholder="e.g., Beginners, Professionals, Students">
                            @error('target_audience')
                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Thumbnail Upload -->
                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-blue-200 mb-2 flex items-center gap-2">
                                <i class="fas fa-image text-blue-400"></i>
                                Course Thumbnail
                            </label>
                            <div
                                class="border-2 border-dashed border-gray-600 rounded-xl p-6 text-center hover:border-blue-400 transition-colors">
                                <input type="file" wire:model="thumbnail" class="hidden" id="thumbnail">
                                <label for="thumbnail" class="cursor-pointer">
                                    @if ($thumbnail)
                                        <img src="{{ $thumbnail->temporaryUrl() }}" alt="Preview"
                                            class="w-full h-48 object-cover rounded-lg mb-4">
                                    @else
                                        <div
                                            class="w-full h-48 bg-gray-700 rounded-lg mb-4 flex items-center justify-center">
                                            <div class="text-center">
                                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-4"></i>
                                                <p class="text-gray-400">Click to upload thumbnail</p>
                                            </div>
                                        </div>
                                    @endif
                                    <p class="text-sm text-gray-400">Recommended: 1280x720px, JPG or PNG</p>
                                </label>
                            </div>
                            @error('thumbnail')
                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Duration -->
                        <div>
                            <label class="block text-sm font-medium text-blue-200 mb-2 flex items-center gap-2">
                                <i class="fas fa-clock text-blue-400"></i>
                                Estimated Duration (minutes)
                            </label>
                            <input type="number" wire:model="estimated_duration_minutes"
                                class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-white placeholder-gray-400"
                                placeholder="e.g., 120">
                            @error('estimated_duration_minutes')
                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
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

            <div class="bg-gray-800/90 backdrop-blur-sm rounded-2xl p-8 shadow-2xl border border-purple-500/20">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">Pricing & Access</h2>
                        <p class="text-gray-300">Set your course pricing strategy</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <!-- Free Course Option -->
                    <div class="relative">
                        <input type="checkbox" wire:model.live="is_free" id="free_option" class="sr-only">
                        <label for="free_option"
                            class="block p-6 rounded-xl cursor-pointer transition-all duration-200 border-2"
                            :class="$wire.is_free ? 'border-green-500 bg-green-500/20' :
                                'border-gray-600 bg-gray-700/50 hover:border-green-400'">
                            <div class="text-center">
                                <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center"
                                    :class="$wire.is_free ? 'bg-green-500' : 'bg-gray-600'">
                                    <i class="fas fa-gift text-white text-2xl"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-white mb-2">Free Course</h3>
                                <p class="text-gray-300 text-sm">Open access for everyone</p>
                            </div>
                        </label>
                    </div>

                    <!-- Premium Course Option -->
                    <div class="relative">
                        <input type="checkbox" wire:model.live="is_premium" id="premium_option" class="sr-only">
                        <label for="premium_option"
                            class="block p-6 rounded-xl cursor-pointer transition-all duration-200 border-2"
                            :class="$wire.is_premium ? 'border-purple-500 bg-purple-500/20' :
                                'border-gray-600 bg-gray-700/50 hover:border-purple-400'">
                            <div class="text-center">
                                <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center"
                                    :class="$wire.is_premium ? 'bg-purple-500' : 'bg-gray-600'">
                                    <i class="fas fa-crown text-white text-2xl"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-white mb-2">Premium Course</h3>
                                <p class="text-gray-300 text-sm">Paid course with premium features</p>
                            </div>
                        </label>
                    </div>

                    <!-- Regular Paid Course -->
                    <div class="relative">
                        <button type="button" wire:click="setPaidCourse"
                            class="block w-full p-6 rounded-xl border-2 transition-all duration-200"
                            :class="!$wire.is_free && !$wire.is_premium ? 'border-blue-500 bg-blue-500/20' :
                                'border-gray-600 bg-gray-700/50 hover:border-blue-400'">
                            <div class="text-center">
                                <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center"
                                    :class="!$wire.is_free && !$wire.is_premium ? 'bg-blue-500' : 'bg-gray-600'">
                                    <i class="fas fa-money-bill-wave text-white text-2xl"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-white mb-2">Paid Course</h3>
                                <p class="text-gray-300 text-sm">Standard paid course</p>
                            </div>
                        </button>
                    </div>
                </div>

                <!-- Price Input -->
                <div x-show="!$wire.is_free" x-transition class="mb-6">
                    <label class="block text-sm font-medium text-green-200 mb-2 flex items-center gap-2">
                        <i class="fas fa-tag text-green-400"></i>
                        Course Price ($)
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg">$</span>
                        <input type="number" wire:model="price" step="0.01" min="0"
                            class="w-full pl-10 pr-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent text-white placeholder-gray-400"
                            placeholder="9.99">
                    </div>
                    @error('price')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Publishing Options -->
                <div class="space-y-4">
                    <div class="flex items-center space-x-4">
                        <input type="checkbox" wire:model="is_published" id="publish_now"
                            class="h-5 w-5 text-green-500 rounded border-gray-600 bg-gray-700 focus:ring-green-400">
                        <label for="publish_now" class="text-white flex items-center gap-2">
                            <i class="fas fa-globe text-green-400"></i>
                            Publish immediately after approval
                        </label>
                    </div>

                    <!-- Schedule Publishing -->
                    <div x-show="$wire.is_published" x-transition>
                        <label class="block text-sm font-medium text-green-200 mb-2">
                            Schedule Publication (Optional)
                        </label>
                        <input type="datetime-local" wire:model="scheduled_publish_at"
                            class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent text-white">
                        <p class="text-xs text-gray-400 mt-1">Leave empty to publish immediately when approved</p>
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

            <div class="bg-gray-800/90 backdrop-blur-sm rounded-2xl p-8 shadow-2xl border border-purple-500/20">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 bg-indigo-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-list-ul text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">Additional Details</h2>
                        <p class="text-gray-300">Enhance your course with extra information</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <!-- Learning Outcomes -->
                        <div class="bg-indigo-800/30 p-6 rounded-xl">
                            <label class="block text-sm font-medium text-indigo-200 mb-4 flex items-center gap-2">
                                <i class="fas fa-lightbulb text-indigo-400"></i>
                                Learning Outcomes
                            </label>
                            <div class="space-y-3">
                                @foreach ($learning_outcomes as $index => $outcome)
                                    <div class="flex items-center gap-3" wire:key="outcome-{{ $index }}">
                                        <div class="flex-1">
                                            <input type="text" wire:model="learning_outcomes.{{ $index }}"
                                                class="w-full px-4 py-2 bg-gray-700/50 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500"
                                                placeholder="Students will be able to...">
                                        </div>
                                        @if (count($learning_outcomes) > 1)
                                            <button type="button"
                                                wire:click="removeLearningOutcome({{ $index }})"
                                                class="text-red-400 hover:text-red-300 p-2">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" wire:click="addLearningOutcome"
                                class="mt-3 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-2">
                                <i class="fas fa-plus"></i> Add Outcome
                            </button>
                        </div>

                        <!-- Prerequisites -->
                        <div class="bg-orange-800/30 p-6 rounded-xl">
                            <label class="block text-sm font-medium text-orange-200 mb-4 flex items-center gap-2">
                                <i class="fas fa-clipboard-check text-orange-400"></i>
                                Prerequisites
                            </label>
                            <div class="space-y-3">
                                @foreach ($prerequisites as $index => $prereq)
                                    <div class="flex items-center gap-3" wire:key="prereq-{{ $index }}">
                                        <div class="flex-1">
                                            <textarea wire:model="prerequisites.{{ $index }}" rows="2"
                                                class="w-full px-4 py-2 bg-gray-700/50 border border-gray-600 rounded-lg text-white placeholder-gray-400 resize-none focus:ring-2 focus:ring-orange-500"
                                                placeholder="What should students know beforehand?"></textarea>
                                        </div>
                                        @if (count($prerequisites) > 1)
                                            <button type="button"
                                                wire:click="removePrerequisite({{ $index }})"
                                                class="text-red-400 hover:text-red-300 p-2">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" wire:click="addPrerequisite"
                                class="mt-3 px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors flex items-center gap-2">
                                <i class="fas fa-plus"></i> Add Prerequisite
                            </button>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <!-- Syllabus Overview -->
                        <div>
                            <label class="block text-sm font-medium text-indigo-200 mb-2 flex items-center gap-2">
                                <i class="fas fa-book-open text-indigo-400"></i>
                                Syllabus Overview
                            </label>
                            <textarea wire:model="syllabus_overview" rows="6"
                                class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-white placeholder-gray-400 resize-none"
                                placeholder="Provide a high-level overview of your course modules and structure..."></textarea>
                            @error('syllabus_overview')
                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- FAQs -->
                        <div class="bg-purple-800/30 p-6 rounded-xl mb-6">
                            <label class="block text-sm font-medium text-purple-200 mb-4 flex items-center gap-2">
                                <i class="fas fa-question-circle text-purple-400"></i>
                                Frequently Asked Questions
                            </label>
                            <div class="space-y-4">
                                @foreach ($faqs as $index => $faq)
                                    <div class="bg-gray-700/50 p-4 rounded-lg" wire:key="faq-{{ $index }}">
                                        <div class="space-y-3">
                                            <input type="text" wire:model="faqs.{{ $index }}.question"
                                                class="w-full px-4 py-2 bg-gray-600/50 border border-gray-500 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500"
                                                placeholder="What is this course about?">
                                            <textarea wire:model="faqs.{{ $index }}.answer" rows="2"
                                                class="w-full px-4 py-2 bg-gray-600/50 border border-gray-500 rounded-lg text-white placeholder-gray-400 resize-none focus:ring-2 focus:ring-purple-500"
                                                placeholder="This course covers..."></textarea>
                                        </div>
                                        @if (count($faqs) > 1)
                                            <button type="button" wire:click="removeFaq({{ $index }})"
                                                class="mt-2 text-red-400 hover:text-red-300 text-sm flex items-center gap-1">
                                                <i class="fas fa-trash"></i> Remove FAQ
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" wire:click="addFaq"
                                class="mt-3 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center gap-2">
                                <i class="fas fa-plus"></i> Add FAQ
                            </button>
                        </div>

                        <!-- Completion Threshold -->
                        <div>
                            <label class="block text-sm font-medium text-indigo-200 mb-2 flex items-center gap-2">
                                <i class="fas fa-percentage text-indigo-400"></i>
                                Completion Threshold (%)
                            </label>
                            <div class="relative">
                                <input type="number" wire:model="completion_rate_threshold" min="0"
                                    max="100" step="0.01"
                                    class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-white placeholder-gray-400">
                                <span
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400">%</span>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">Minimum completion rate for certificate eligibility
                            </p>
                            @error('completion_rate_threshold')
                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
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

            <div class="bg-gray-800/90 backdrop-blur-sm rounded-2xl p-8 shadow-2xl border border-purple-500/20">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-white text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-white">Review & Submit</h2>
                        <p class="text-gray-300">Final check before submitting for approval</p>
                    </div>
                </div>

                <!-- Course Preview -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <!-- Course Card Preview -->
                    <div class="bg-gray-700/50 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-white mb-4 flex items-center gap-2">
                            <i class="fas fa-eye text-blue-400"></i>
                            Course Preview
                        </h3>
                        <div class="bg-gray-800 rounded-lg p-4">
                            @if ($thumbnail)
                                <img src="{{ $thumbnail->temporaryUrl() }}" alt="Course thumbnail"
                                    class="w-full h-32 object-cover rounded-lg mb-4">
                            @else
                                <div class="w-full h-32 bg-gray-600 rounded-lg mb-4 flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400 text-2xl"></i>
                                </div>
                            @endif
                            <h4 class="text-white font-semibold mb-2">{{ $title ?: 'Course Title' }}</h4>
                            <p class="text-gray-300 text-sm mb-2">{{ $subtitle ?: 'Course subtitle...' }}</p>
                            <div class="flex justify-between items-center">
                                <span class="text-purple-400 font-semibold">
                                    @if ($is_free)
                                        Free
                                    @else
                                        ${{ number_format($price, 2) }}
                                    @endif
                                </span>
                                <span class="text-gray-400 text-sm capitalize">{{ $difficulty_level }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Course Summary -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-white flex items-center gap-2">
                            <i class="fas fa-list text-green-400"></i>
                            Course Summary
                        </h3>

                        <div class="space-y-3">
                            <div class="flex justify-between py-2 border-b border-gray-600">
                                <span class="text-gray-300">Category:</span>
                                <span class="text-white">
                                    {{ $categories->firstWhere('id', $category_id)->name ?? 'Not selected' }}
                                </span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-600">
                                <span class="text-gray-300">Difficulty:</span>
                                <span class="text-white capitalize">{{ $difficulty_level }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-600">
                                <span class="text-gray-300">Duration:</span>
                                <span class="text-white">
                                    {{ $estimated_duration_minutes ? $estimated_duration_minutes . ' minutes' : 'Not specified' }}
                                </span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-600">
                                <span class="text-gray-300">Price:</span>
                                <span class="text-white">
                                    @if ($is_free)
                                        Free
                                    @elseif($is_premium)
                                        ${{ number_format($price, 2) }} (Premium)
                                    @else
                                        ${{ number_format($price, 2) }}
                                    @endif
                                </span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-600">
                                <span class="text-gray-300">Learning Outcomes:</span>
                                <span class="text-white">{{ count(array_filter($learning_outcomes)) }} items</span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="text-gray-300">Prerequisites:</span>
                                <span class="text-white">{{ count(array_filter($prerequisites)) }} items</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submission Notice -->
                <div class="bg-blue-800/30 border border-blue-600 rounded-xl p-6 mb-6">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-info-circle text-blue-400 text-xl mt-1"></i>
                        <div>
                            <h4 class="text-white font-semibold mb-2">Submission for Approval</h4>
                            <p class="text-gray-300 text-sm">
                                Your course will be submitted for review by our academy administrators.
                                You'll receive a notification once it's approved and published.
                                You can continue to edit your course content in the course builder after submission.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Admin Approval Option (if applicable) -->
                @if (Auth::user()->hasRole('super_admin') || Auth::user()->hasRole('academy_admin'))
                    <div class="bg-purple-800/30 border border-purple-600 rounded-xl p-6 mb-6">
                        <div class="flex items-center space-x-4">
                            <input type="checkbox" wire:model="is_approved" id="admin_approve"
                                class="h-5 w-5 text-purple-500 rounded border-gray-600 bg-gray-700 focus:ring-purple-400">
                            <label for="admin_approve" class="text-white flex items-center gap-2">
                                <i class="fas fa-shield-alt text-purple-400"></i>
                                Approve course immediately (Admin only)
                            </label>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="mt-8 flex justify-between">
            <button type="button" x-show="currentStep > 1" @click="$wire.previousStep()"
                class="px-6 py-3 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-colors flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>
                Previous
            </button>

            <div class="flex gap-4">
                <button type="button" x-show="currentStep < totalSteps" @click="$wire.nextStep()"
                    class="px-6 py-3 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition-colors flex items-center gap-2">
                    Next
                    <i class="fas fa-arrow-right"></i>
                </button>

                <button type="submit" x-show="currentStep === totalSteps" wire:loading.attr="disabled"
                    class="px-8 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl font-semibold hover:from-green-700 hover:to-emerald-700 transition-all duration-200 shadow-lg hover:shadow-xl flex items-center gap-2 disabled:opacity-50">
                    <span wire:loading.remove>
                        <i class="fas fa-rocket"></i>
                        Submit Course
                    </span>
                    <span wire:loading>
                        <i class="fas fa-spinner fa-spin"></i>
                        Submitting...
                    </span>
                </button>
            </div>
        </div>

        <!-- Step Indicators (Mobile) -->
        <div class="mt-6 flex justify-center md:hidden">
            <div class="flex space-x-2">
                <template x-for="step in totalSteps" :key="step">
                    <button type="button" @click="$wire.goToStep(step)"
                        class="w-3 h-3 rounded-full transition-colors"
                        :class="step === currentStep ? 'bg-purple-500' : step < currentStep ? 'bg-purple-300' : 'bg-gray-600'">
                    </button>
                </template>
            </div>
        </div>
    </form>
</div>
