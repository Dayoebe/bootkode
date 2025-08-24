<!-- Assessment Form View (Create/Edit) -->
<form wire:submit.prevent="{{ $activeView === 'create' ? 'createAssessment' : 'updateAssessment' }}" 
      class="space-y-6">
      
    <!-- Assessment Type Selection -->
    <div class="space-y-4">
        <h4 class="text-white font-medium">Assessment Type</h4>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            @php
                $assessmentTypes = [
                    'quiz' => [
                        'icon' => 'fa-question-circle',
                        'color' => 'blue',
                        'label' => 'Quiz',
                        'description' => 'Multiple choice, true/false, fill-in-the-blank'
                    ],
                    'project' => [
                        'icon' => 'fa-project-diagram',
                        'color' => 'green',
                        'label' => 'Project',
                        'description' => 'File uploads, portfolio submissions'
                    ],
                    'assignment' => [
                        'icon' => 'fa-clipboard-list',
                        'color' => 'yellow',
                        'label' => 'Assignment',
                        'description' => 'Written responses, essays, reports'
                    ],
                    'qna' => [
                        'icon' => 'fa-comments',
                        'color' => 'purple',
                        'label' => 'Q&A',
                        'description' => 'Discussion-based evaluation'
                    ]
                ];
            @endphp

            @foreach ($assessmentTypes as $type => $config)
                <button type="button" wire:click="selectAssessmentType('{{ $type }}')"
                        class="p-4 rounded-lg text-center transition-colors border-2
                            {{ $assessmentType === $type 
                                ? 'bg-' . $config['color'] . '-600 border-' . $config['color'] . '-500 text-white' 
                                : 'bg-gray-700 border-gray-600 text-gray-300 hover:bg-gray-600 hover:border-gray-500' }}">
                    <i class="fas {{ $config['icon'] }} text-2xl mb-2 block"></i>
                    <div class="font-medium">{{ $config['label'] }}</div>
                    <div class="text-xs mt-1 opacity-75">{{ $config['description'] }}</div>
                </button>
            @endforeach
        </div>
    </div>

    <!-- Basic Information -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">
                Assessment Title <span class="text-red-400">*</span>
            </label>
            <input type="text" wire:model="assessmentTitle"
                   class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white 
                          focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                   placeholder="Enter assessment title...">
            @error('assessmentTitle')
                <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">Weight</label>
            <input type="number" wire:model="weight" step="0.1" min="0.1" max="10"
                   class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white 
                          focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                   placeholder="1">
            <p class="text-xs text-gray-500 mt-1">How much this assessment counts towards final grade</p>
            @error('weight')
                <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <!-- Description -->
    <div>
        <label class="block text-sm font-medium text-gray-300 mb-2">Description</label>
        <textarea wire:model="assessmentDescription" rows="3"
                  class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white 
                         focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                  placeholder="Describe what students will be evaluated on..."></textarea>
        @error('assessmentDescription')
            <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
        @enderror
    </div>

    <!-- Assessment Settings -->
    <div class="bg-gray-700 rounded-lg p-6">
        <h4 class="text-white font-medium mb-4">Assessment Settings</h4>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Pass Percentage -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">
                    Pass Percentage <span class="text-red-400">*</span>
                </label>
                <div class="relative">
                    <input type="number" wire:model="passPercentage" min="1" max="100"
                           class="w-full px-4 py-2 pr-8 bg-gray-600 border border-gray-500 rounded-lg text-white 
                                  focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <span class="absolute right-3 top-2 text-gray-400">%</span>
                </div>
                @error('passPercentage')
                    <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>

            <!-- Time Limit -->
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Time Limit (minutes)</label>
                <input type="number" wire:model="timeLimit" min="1" 
                       class="w-full px-4 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white 
                              focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                       placeholder="Optional - leave empty for no limit">
                @error('timeLimit')
                    <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <!-- Additional Settings for Quiz Type -->
        @if($assessmentType === 'quiz')
            <div class="mt-6">
                <h5 class="text-sm font-medium text-gray-300 mb-3">Quiz Settings</h5>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Maximum Attempts</label>
                        <select wire:model="maxAttempts" 
                                class="w-full px-4 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white 
                                       focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <option value="">Unlimited</option>
                            <option value="1">1 attempt</option>
                            <option value="2">2 attempts</option>
                            <option value="3">3 attempts</option>
                            <option value="5">5 attempts</option>
                        </select>
                    </div>
                </div>
            </div>
        @endif

        <!-- Boolean Settings -->
        <div class="mt-6 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <label class="text-sm font-medium text-gray-300">Mandatory for Completion</label>
                    <p class="text-xs text-gray-500">Students must pass this assessment to complete the lesson</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" wire:model="isMandatory" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-600 peer-focus:outline-none peer-focus:ring-4 
                               peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full 
                               peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] 
                               after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 
                               after:transition-all peer-checked:bg-purple-600"></div>
                </label>
            </div>

            <div class="flex items-center justify-between">
                <div>
                    <label class="text-sm font-medium text-gray-300">Allow Multiple Attempts</label>
                    <p class="text-xs text-gray-500">Students can retake this assessment if they fail</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" wire:model="allowMultipleAttempts" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-600 peer-focus:outline-none peer-focus:ring-4 
                               peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full 
                               peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] 
                               after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 
                               after:transition-all peer-checked:bg-purple-600"></div>
                </label>
            </div>

            <div class="flex items-center justify-between">
                <div>
                    <label class="text-sm font-medium text-gray-300">Show Results Immediately</label>
                    <p class="text-xs text-gray-500">Display results and feedback after submission</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" wire:model="showResultsImmediately" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-600 peer-focus:outline-none peer-focus:ring-4 
                               peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full 
                               peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] 
                               after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 
                               after:transition-all peer-checked:bg-purple-600"></div>
                </label>
            </div>
        </div>
    </div>

    <!-- Type-Specific Information -->
    @if($assessmentType !== 'quiz')
        <div class="bg-gray-700 rounded-lg p-6">
            <h4 class="text-white font-medium mb-4">
                {{ ucfirst($assessmentType) }} Guidelines
            </h4>
            
            @if($assessmentType === 'project')
                <div class="space-y-3 text-sm text-gray-300">
                    <p><strong>Project assessments are perfect for:</strong></p>
                    <ul class="list-disc list-inside space-y-1 ml-4 text-gray-400">
                        <li>Hands-on skill application and creativity</li>
                        <li>Portfolio development and showcase pieces</li>
                        <li>Real-world problem-solving scenarios</li>
                        <li>Collaborative work and team projects</li>
                    </ul>
                    <div class="bg-blue-900/30 border border-blue-700 rounded p-3 mt-4">
                        <p class="text-blue-200 text-xs">
                            <i class="fas fa-lightbulb mr-1"></i>
                            <strong>Tip:</strong> After creating this assessment, you'll be able to add specific deliverables, 
                            rubrics, and file upload requirements.
                        </p>
                    </div>
                </div>
            @elseif($assessmentType === 'assignment')
                <div class="space-y-3 text-sm text-gray-300">
                    <p><strong>Assignment assessments work well for:</strong></p>
                    <ul class="list-disc list-inside space-y-1 ml-4 text-gray-400">
                        <li>Written responses and essay questions</li>
                        <li>Analysis and critical thinking tasks</li>
                        <li>Research-based submissions</li>
                        <li>Detailed explanations and documentation</li>
                    </ul>
                </div>
            @elseif($assessmentType === 'qna')
                <div class="space-y-3 text-sm text-gray-300">
                    <p><strong>Q&A assessments are ideal for:</strong></p>
                    <ul class="list-disc list-inside space-y-1 ml-4 text-gray-400">
                        <li>Discussion-based learning and evaluation</li>
                        <li>Peer interaction and knowledge sharing</li>
                        <li>Open-ended questions and debates</li>
                        <li>Community building and engagement</li>
                    </ul>
                </div>
            @endif
        </div>
    @endif

    <!-- Form Actions -->
    <div class="flex items-center justify-between pt-6 border-t border-gray-700">
        <button type="button" wire:click="backToList"
                class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to List
        </button>
        
        <div class="flex gap-3">
            @if($activeView === 'edit')
                <button type="button" wire:click="backToList"
                        class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    Cancel
                </button>
            @endif
            
            <button type="submit" wire:loading.attr="disabled"
                    class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors 
                           disabled:opacity-50 flex items-center gap-2">
                <span wire:loading.remove>
                    <i class="fas fa-{{ $activeView === 'create' ? 'plus' : 'save' }} mr-2"></i>
                    {{ $activeView === 'create' ? 'Create Assessment' : 'Update Assessment' }}
                </span>
                <span wire:loading class="flex items-center gap-2">
                    <i class="fas fa-spinner fa-spin"></i>
                    {{ $activeView === 'create' ? 'Creating...' : 'Updating...' }}
                </span>
            </button>
        </div>
    </div>
</form>