<div class="space-y-6">
    <!-- Success Message -->
    @if (session()->has('success'))
        <div class="bg-green-600 text-white p-4 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-medium text-white">Lesson Assessments</h3>
            <button wire:click="toggleCreateForm"
                class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors">
                <i class="fas fa-plus mr-2"></i>
                {{ $showCreateForm ? 'Cancel' : 'Add Assessment' }}
            </button>
        </div>

        <!-- Create Assessment Form -->
        @if ($showCreateForm)
            <div class="bg-gray-700 rounded-lg p-6 mb-6 border border-gray-600">
                <h4 class="text-white font-medium mb-4">Create New Assessment</h4>

                <!-- Assessment Types Selection -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
                    <button wire:click="selectAssessmentType('quiz')"
                        class="p-4 rounded-lg text-center transition-colors {{ $assessmentType === 'quiz' ? 'bg-blue-600 text-white' : 'bg-gray-600 text-gray-300 hover:bg-gray-500' }}">
                        <i class="fas fa-question-circle text-2xl mb-2 block"></i>
                        <span class="text-sm font-medium">Quiz</span>
                    </button>

                    <button wire:click="selectAssessmentType('project')"
                        class="p-4 rounded-lg text-center transition-colors {{ $assessmentType === 'project' ? 'bg-green-600 text-white' : 'bg-gray-600 text-gray-300 hover:bg-gray-500' }}">
                        <i class="fas fa-project-diagram text-2xl mb-2 block"></i>
                        <span class="text-sm font-medium">Project</span>
                    </button>

                    <button wire:click="selectAssessmentType('assignment')"
                        class="p-4 rounded-lg text-center transition-colors {{ $assessmentType === 'assignment' ? 'bg-yellow-600 text-white' : 'bg-gray-600 text-gray-300 hover:bg-gray-500' }}">
                        <i class="fas fa-clipboard-list text-2xl mb-2 block"></i>
                        <span class="text-sm font-medium">Assignment</span>
                    </button>

                    <button wire:click="selectAssessmentType('qna')"
                        class="p-4 rounded-lg text-center transition-colors {{ $assessmentType === 'qna' ? 'bg-purple-600 text-white' : 'bg-gray-600 text-gray-300 hover:bg-gray-500' }}">
                        <i class="fas fa-comments text-2xl mb-2 block"></i>
                        <span class="text-sm font-medium">Q&A</span>
                    </button>
                </div>

                <form wire:submit.prevent="createAssessment" class="space-y-4">
                    <!-- Title and Description -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Assessment Title *</label>
                            <input type="text" wire:model="assessmentTitle"
                                class="w-full px-4 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            @error('assessmentTitle')
                                <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Pass Percentage</label>
                            <input type="number" wire:model="passPercentage" min="1" max="100"
                                class="w-full px-4 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            @error('passPercentage')
                                <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Description</label>
                        <textarea wire:model="assessmentDescription" rows="3"
                            class="w-full px-4 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent"></textarea>
                        @error('assessmentDescription')
                            <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Settings -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Time Limit (minutes)</label>
                            <input type="number" wire:model="timeLimit" min="1" placeholder="Optional"
                                class="w-full px-4 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            @error('timeLimit')
                                <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="space-y-3 pt-6">
                            <div class="flex items-center">
                                <input type="checkbox" wire:model="isMandatory" id="mandatory"
                                    class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-600 rounded bg-gray-700">
                                <label for="mandatory" class="ml-2 text-sm text-gray-300">
                                    Mandatory for completion
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" wire:model="allowMultipleAttempts" id="multiple_attempts"
                                    class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-600 rounded bg-gray-700">
                                <label for="multiple_attempts" class="ml-2 text-sm text-gray-300">
                                    Allow multiple attempts
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" wire:model="showResultsImmediately" id="show_results"
                                    class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-600 rounded bg-gray-700">
                                <label for="show_results" class="ml-2 text-sm text-gray-300">
                                    Show results immediately
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit"
                            class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors">
                            Create Assessment
                        </button>
                        <button type="button" wire:click="toggleCreateForm"
                            class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <!-- Existing Assessments List -->
        <div class="space-y-4">
            <h4 class="text-white font-medium">Current Assessments ({{ count($assessments) }})</h4>

            @if (count($assessments) > 0)
                <div class="space-y-3">
                    @foreach ($assessments as $assessment)
                        <div class="bg-gray-700 rounded-lg p-4 border border-gray-600">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start gap-4">
                                    <!-- Assessment Type Icon -->
                                    <div class="flex-shrink-0">
                                        @if ($assessment['type'] === 'quiz')
                                            <div
                                                class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-question-circle text-white"></i>
                                            </div>
                                        @elseif($assessment['type'] === 'project')
                                            <div
                                                class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-project-diagram text-white"></i>
                                            </div>
                                        @elseif($assessment['type'] === 'assignment')
                                            <div
                                                class="w-10 h-10 bg-yellow-600 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-clipboard-list text-white"></i>
                                            </div>
                                        @else
                                            <div
                                                class="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-comments text-white"></i>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Assessment Info -->
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <h5 class="text-white font-medium">{{ $assessment['title'] }}</h5>
                                            <span
                                                class="px-2 py-1 text-xs rounded-full {{ $assessment['type'] === 'quiz'
                                                    ? 'bg-blue-100 text-blue-800'
                                                    : ($assessment['type'] === 'project'
                                                        ? 'bg-green-100 text-green-800'
                                                        : ($assessment['type'] === 'assignment'
                                                            ? 'bg-yellow-100 text-yellow-800'
                                                            : 'bg-purple-100 text-purple-800')) }}">
                                                {{ ucfirst($assessment['type']) }}
                                            </span>
                                            @if ($assessment['is_mandatory'])
                                                <span
                                                    class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">Mandatory</span>
                                            @endif
                                        </div>

                                        @if ($assessment['description'])
                                            <p class="text-gray-400 text-sm mb-2">{{ $assessment['description'] }}</p>
                                        @endif

                                        <div class="flex items-center gap-4 text-sm text-gray-400">
                                            <span><i
                                                    class="fas fa-percentage mr-1"></i>{{ $assessment['pass_percentage'] }}%
                                                to pass</span>
                                            @if ($assessment['estimated_duration_minutes'])
                                                <span><i
                                                        class="fas fa-clock mr-1"></i>{{ $assessment['estimated_duration_minutes'] }}
                                                    min</span>
                                            @endif
                                            <span><i class="fas fa-list-ol mr-1"></i>Order:
                                                {{ $assessment['order'] }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex gap-2">
                                    <button
                                        class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm transition-colors">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button wire:click="deleteAssessment({{ $assessment['id'] }})"
                                        onclick="return confirm('Are you sure you want to delete this assessment?')"
                                        class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-sm transition-colors">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-gray-400 text-center py-8">
                    <i class="fas fa-clipboard text-4xl mb-4"></i>
                    <p>No assessments created yet.</p>
                    <p class="text-sm">Add an assessment to test student understanding.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Assessment Tips -->
    <div class="bg-blue-900/30 border border-blue-700 rounded-lg p-4">
        <h4 class="text-blue-300 font-medium mb-2"><i class="fas fa-info-circle mr-2"></i>Assessment Tips</h4>
        <ul class="text-blue-200 text-sm space-y-1">
            <li>• <strong>Quiz:</strong> Best for knowledge retention and quick concept checks</li>
            <li>• <strong>Project:</strong> Ideal for hands-on skill application and creativity</li>
            <li>• <strong>Assignment:</strong> Great for detailed analysis and written responses</li>
            <li>• <strong>Q&A:</strong> Perfect for discussion and critical thinking</li>
        </ul>
    </div>
</div>
