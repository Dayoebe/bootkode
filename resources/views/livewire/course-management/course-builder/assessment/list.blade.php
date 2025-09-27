<!-- Assessment List View -->
@if (count($assessments) > 0)
    <div class="space-y-4">
        @foreach ($assessments as $assessment)
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 p-4 transition-colors duration-300">
                <div class="flex items-start justify-between">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2 py-1 text-xs rounded-full border font-medium
                                {{ $assessment['type'] === 'quiz' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 border-blue-200 dark:border-blue-700' : 
                                   ($assessment['type'] === 'project' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 border-green-200 dark:border-green-700' : 
                                    ($assessment['type'] === 'assignment' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300 border-yellow-200 dark:border-yellow-700' : 
                                     'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300 border-purple-200 dark:border-purple-700')) }}">
                                {{ ucfirst($assessment['type']) }}
                            </span>
                            
                            @if ($assessment['is_mandatory'])
                                <span class="px-2 py-1 text-xs bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300 rounded-full border border-red-200 dark:border-red-700">
                                    Required
                                </span>
                            @endif
                            
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $assessment['pass_percentage'] }}% to pass
                            </span>
                        </div>
                        
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-1 truncate">
                            {{ $assessment['title'] }}
                        </h4>
                        
                        @if ($assessment['description'])
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                {{ Str::limit($assessment['description'], 120) }}
                            </p>
                        @endif
                        
                        <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                            <span>
                                <i class="fas fa-question-circle mr-1"></i>
                                {{ count($assessment['questions'] ?? []) }} {{ $this->getAssessmentItemType($assessment['type']) }}
                            </span>
                            
                            @if ($assessment['estimated_duration_minutes'])
                                <span>
                                    <i class="fas fa-clock mr-1"></i>
                                    {{ $assessment['estimated_duration_minutes'] }} minutes
                                </span>
                            @endif
                            
                            <span>
                                <i class="fas fa-weight mr-1"></i>
                                Weight: {{ $assessment['weight'] ?? 1 }}x
                            </span>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex gap-2 ml-4 flex-shrink-0">
                        @if ($this->canManageQuestions($assessment['type']))
                            <button wire:click="manageQuestions({{ $assessment['id'] }})"
                                    class="px-3 py-1 bg-purple-600 hover:bg-purple-700 text-white rounded text-sm transition-colors duration-300"
                                    title="Manage {{ $this->getAssessmentItemType($assessment['type']) }}">
                                <i class="fas fa-list mr-1"></i>
                                <span class="hidden sm:inline">{{ ucfirst($this->getAssessmentItemType($assessment['type'])) }}</span>
                            </button>
                        @endif
                        
                        <button wire:click="editAssessment({{ $assessment['id'] }})"
                                class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm transition-colors duration-300">
                            <i class="fas fa-edit"></i>
                        </button>
                        
                        <button wire:click="duplicateAssessment({{ $assessment['id'] }})"
                                class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-sm transition-colors duration-300"
                                title="Duplicate Assessment">
                            <i class="fas fa-copy"></i>
                        </button>
                        
                        <button wire:click="deleteAssessment({{ $assessment['id'] }})"
                                onclick="return confirm('Are you sure you want to delete this assessment? This action cannot be undone.')"
                                class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-sm transition-colors duration-300">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Assessment Statistics -->
    <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
        @php
            $totalAssessments = count($assessments);
            $requiredAssessments = collect($assessments)->where('is_mandatory', true)->count();
            $totalQuestions = collect($assessments)->sum(fn($a) => count($a['questions'] ?? []));
            $avgPassPercentage = collect($assessments)->avg('pass_percentage');
        @endphp
        
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 text-center border border-gray-200 dark:border-gray-700">
            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $totalAssessments }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Total Assessments</div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 text-center border border-gray-200 dark:border-gray-700">
            <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $requiredAssessments }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Required</div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 text-center border border-gray-200 dark:border-gray-700">
            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $totalQuestions }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Total Items</div>
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 text-center border border-gray-200 dark:border-gray-700">
            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ round($avgPassPercentage) }}%</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Avg Pass Rate</div>
        </div>
    </div>
@else
    <!-- Empty State -->
    <div class="text-center py-12">
        <div class="w-20 h-20 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-200 dark:border-gray-600">
            <i class="fas fa-clipboard-check text-3xl text-gray-400 dark:text-gray-500"></i>
        </div>
        <h3 class="text-xl font-medium text-gray-800 dark:text-white mb-2">No Assessments Yet</h3>
        <p class="text-gray-600 dark:text-gray-400 mb-6 max-w-md mx-auto">
            Assessments help evaluate student understanding and progress. Create your first assessment to get started.
        </p>
        <button wire:click="toggleCreateForm"
                class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors duration-300 font-medium">
            <i class="fas fa-plus mr-2"></i>
            Create Your First Assessment
        </button>
    </div>
@endif