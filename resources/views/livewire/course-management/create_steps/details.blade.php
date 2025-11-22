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