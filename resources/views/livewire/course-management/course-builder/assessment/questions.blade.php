<!-- Questions/Criteria Management View -->
@if ($selectedAssessment)
    <div class="space-y-6">
        <!-- Assessment Info Header -->
        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600 transition-colors duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-xl font-bold text-gray-800 dark:text-white transition-colors duration-300">{{ $selectedAssessment['title'] }}</h4>
                    <p class="text-gray-600 dark:text-gray-400 mt-1 transition-colors duration-300">
                        Managing {{ $this->getAssessmentItemType($selectedAssessment['type']) }} for this
                        {{ $selectedAssessment['type'] }} assessment
                    </p>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-purple-600 dark:text-purple-400 transition-colors duration-300">{{ count($selectedAssessment['questions'] ?? []) }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400 transition-colors duration-300">{{ $this->getAssessmentItemType($selectedAssessment['type']) }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Dynamic Manager Based on Assessment Type -->
        @if ($selectedAssessment['type'] === 'quiz')
            @livewire('course-management.course-builder.question-manager', ['assessmentId' => $selectedAssessment['id']], key('question-manager-' . $selectedAssessment['id']))
        @elseif($selectedAssessment['type'] === 'project')
            @livewire('course-management.course-builder.project-criteria-manager', ['assessmentId' => $selectedAssessment['id']], key('project-criteria-manager-' . $selectedAssessment['id']))
        @elseif($selectedAssessment['type'] === 'assignment')
            @livewire('course-management.course-builder.assignment-criteria-manager', ['assessmentId' => $selectedAssessment['id']], key('assignment-criteria-manager-' . $selectedAssessment['id']))
        @elseif($selectedAssessment['type'] === 'qna')
            @livewire('course-management.course-builder.qna-criteria-manager', ['assessmentId' => $selectedAssessment['id']], key('qna-criteria-manager-' . $selectedAssessment['id']))
        @else
            <!-- Fallback for unknown types -->
            <div class="bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-700 rounded-lg p-6 text-center transition-colors duration-300">
                <div class="w-16 h-16 bg-yellow-100 dark:bg-yellow-600/20 rounded-full flex items-center justify-center mx-auto mb-4 transition-colors duration-300">
                    <i class="fas fa-exclamation-triangle text-2xl text-yellow-600 dark:text-yellow-400"></i>
                </div>
                <h4 class="text-lg font-medium text-gray-800 dark:text-white mb-2 transition-colors duration-300">Assessment Type Not Supported</h4>
                <p class="text-gray-600 dark:text-gray-400 mb-4 transition-colors duration-300">
                    The assessment type "{{ $selectedAssessment['type'] }}" doesn't have a dedicated manager yet.
                </p>
                <button wire:click="backToList" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 text-gray-800 dark:text-white rounded-lg transition-colors duration-300">
                    Back to Assessments
                </button>
            </div>
        @endif
    </div>
@else
    <!-- Error State -->
    <div class="text-center py-8">
        <div class="w-16 h-16 bg-red-100 dark:bg-red-600/20 rounded-full flex items-center justify-center mx-auto mb-4 border border-red-200 dark:border-red-700 transition-colors duration-300">
            <i class="fas fa-exclamation-triangle text-2xl text-red-600 dark:text-red-400"></i>
        </div>
        <h4 class="text-lg font-medium text-gray-800 dark:text-white mb-2 transition-colors duration-300">Assessment Not Found</h4>
        <p class="text-gray-600 dark:text-gray-400 mb-4 transition-colors duration-300">The selected assessment could not be loaded.</p>
        <button wire:click="backToList" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 hover:bg-gray-300 dark:hover:bg-gray-500 text-gray-800 dark:text-white rounded-lg transition-colors duration-300">
            Back to Assessments
        </button>
    </div>
@endif