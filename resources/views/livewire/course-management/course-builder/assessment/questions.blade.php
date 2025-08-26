<!-- Questions/Criteria Management View -->
@if ($selectedAssessment)
    <div class="space-y-6">
        <!-- Assessment Info Header -->
        <div class="bg-gray-700 rounded-lg p-4 border border-gray-600">
            <div class="flex items-center justify-between">
                <div>
                    <h4 class="text-xl font-bold text-white">{{ $selectedAssessment['title'] }}</h4>
                    <p class="text-gray-400 mt-1">
                        Managing {{ $this->getAssessmentItemType($selectedAssessment['type']) }} for this
                        {{ $selectedAssessment['type'] }} assessment
                    </p>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-purple-400">{{ count($selectedAssessment['questions'] ?? []) }}
                    </div>
                    <div class="text-sm text-gray-400">{{ $this->getAssessmentItemType($selectedAssessment['type']) }}
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
            <div class="bg-yellow-900/30 border border-yellow-700 rounded-lg p-6 text-center">
                <div class="w-16 h-16 bg-yellow-600/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-exclamation-triangle text-2xl text-yellow-400"></i>
                </div>
                <h4 class="text-lg font-medium text-white mb-2">Assessment Type Not Supported</h4>
                <p class="text-gray-400 mb-4">
                    The assessment type "{{ $selectedAssessment['type'] }}" doesn't have a dedicated manager yet.
                </p>
                <button wire:click="backToList" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg">
                    Back to Assessments
                </button>
            </div>
        @endif
    </div>
@else
    <!-- Error State -->
    <div class="text-center py-8">
        <div class="w-16 h-16 bg-red-600/20 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-exclamation-triangle text-2xl text-red-400"></i>
        </div>
        <h4 class="text-lg font-medium text-white mb-2">Assessment Not Found</h4>
        <p class="text-gray-400 mb-4">The selected assessment could not be loaded.</p>
        <button wire:click="backToList" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg">
            Back to Assessments
        </button>
    </div>
@endif
