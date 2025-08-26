<!-- Assessment List View -->
@if (count($assessments) > 0)
    <div class="space-y-4" id="assessments-container">
        @foreach ($assessments as $index => $assessment)
            <div class="bg-gray-700 rounded-lg border border-gray-600 p-4 sortable-item" 
                 data-id="{{ $assessment['id'] }}">
                <div class="flex items-start justify-between">
                    <!-- Assessment Info -->
                    <div class="flex items-start gap-4 flex-1">
                        <!-- Drag Handle -->
                        <div class="drag-handle cursor-move text-gray-500 hover:text-gray-300 mt-2">
                            <i class="fas fa-grip-vertical"></i>
                        </div>

                        <!-- Assessment Type Icon -->
                        <div class="flex-shrink-0">
                            @php
                                $typeConfig = [
                                    'quiz' => ['icon' => 'fa-question-circle', 'color' => 'bg-blue-600', 'label' => 'Quiz'],
                                    'project' => ['icon' => 'fa-project-diagram', 'color' => 'bg-green-600', 'label' => 'Project'],
                                    'assignment' => ['icon' => 'fa-clipboard-list', 'color' => 'bg-yellow-600', 'label' => 'Assignment'],
                                    'qna' => ['icon' => 'fa-comments', 'color' => 'bg-purple-600', 'label' => 'Q&A']
                                ];
                                $config = $typeConfig[$assessment['type']] ?? $typeConfig['quiz'];
                            @endphp
                            <div class="w-12 h-12 {{ $config['color'] }} rounded-lg flex items-center justify-center">
                                <i class="fas {{ $config['icon'] }} text-white text-lg"></i>
                            </div>
                        </div>

                        <!-- Assessment Details -->
                        <div class="flex-1">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <h5 class="text-white font-medium text-lg">{{ $assessment['title'] }}</h5>
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            {{ $assessment['type'] === 'quiz' ? 'bg-blue-100 text-blue-800' : 
                                               ($assessment['type'] === 'project' ? 'bg-green-100 text-green-800' : 
                                                ($assessment['type'] === 'assignment' ? 'bg-yellow-100 text-yellow-800' : 
                                                 'bg-purple-100 text-purple-800')) }}">
                                            {{ $config['label'] }}
                                        </span>
                                        @if ($assessment['is_mandatory'])
                                            <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">
                                                Mandatory
                                            </span>
                                        @endif
                                    </div>

                                    @if ($assessment['description'])
                                        <p class="text-gray-400 text-sm mb-3 line-clamp-2">
                                            {{ $assessment['description'] }}
                                        </p>
                                    @endif

                                    <!-- Assessment Stats -->
                                    <div class="flex items-center gap-6 text-sm text-gray-400">
                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-percentage"></i>
                                            {{ $assessment['pass_percentage'] }}% to pass
                                        </span>
                                        
                                        @if ($assessment['estimated_duration_minutes'])
                                            <span class="flex items-center gap-1">
                                                <i class="fas fa-clock"></i>
                                                {{ $assessment['estimated_duration_minutes'] }} min
                                            </span>
                                        @endif

                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-weight-hanging"></i>
                                            Weight: {{ $assessment['weight'] ?? 1 }}x
                                        </span>

                                        <span class="flex items-center gap-1">
                                            <i class="fas fa-list-ol"></i>
                                            {{ count($assessment['questions'] ?? []) }} 
                                            @if($assessment['type'] === 'quiz')
                                                questions
                                            @elseif($assessment['type'] === 'project')
                                                criteria
                                            @elseif($assessment['type'] === 'assignment')
                                                questions
                                            @elseif($assessment['type'] === 'qna')
                                                topics
                                            @else
                                                items
                                            @endif
                                        </span>
                                    </div>
                                </div>

                                <!-- Order Badge -->
                                <div class="text-right">
                                    <span class="inline-block w-8 h-8 bg-gray-600 rounded-full text-center leading-8 text-white text-sm font-medium">
                                        {{ $assessment['order'] }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col gap-2 ml-4">
                        <!-- Manage Questions/Criteria/Topics Button - Now available for all types -->
                        <button wire:click="manageQuestions({{ $assessment['id'] }})"
                                class="px-3 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-sm transition-colors flex items-center gap-2">
                            <i class="fas fa-{{ $assessment['type'] === 'quiz' ? 'list' : ($assessment['type'] === 'project' ? 'tasks' : ($assessment['type'] === 'qna' ? 'comments' : 'edit')) }}"></i>
                            @if($assessment['type'] === 'quiz')
                                Questions
                            @elseif($assessment['type'] === 'project')
                                Criteria
                            @elseif($assessment['type'] === 'assignment')
                                Questions
                            @elseif($assessment['type'] === 'qna')
                                Topics
                            @else
                                Manage
                            @endif
                        </button>

                        <div class="flex gap-2">
                            <button wire:click="editAssessment({{ $assessment['id'] }})"
                                    class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm transition-colors">
                                <i class="fas fa-edit"></i>
                            </button>

                            <button wire:click="duplicateAssessment({{ $assessment['id'] }})"
                                    class="px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm transition-colors"
                                    title="Duplicate Assessment">
                                <i class="fas fa-copy"></i>
                            </button>

                            <button wire:click="deleteAssessment({{ $assessment['id'] }})"
                                    onclick="return confirm('Are you sure you want to delete this assessment? This action cannot be undone.')"
                                    class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm transition-colors">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Progress Bar for Assessment Completion -->
                @php
                    $totalItems = count($assessment['questions'] ?? []);
                    $progressPercent = $totalItems > 0 ? 100 : 0;
                    $statusText = $totalItems > 0 ? 'Ready' : 'Needs ' . ($assessment['type'] === 'quiz' ? 'Questions' : ($assessment['type'] === 'project' ? 'Criteria' : ($assessment['type'] === 'qna' ? 'Topics' : 'Items')));
                @endphp
                <div class="mt-4 pt-3 border-t border-gray-600">
                    <div class="flex items-center justify-between text-sm mb-1">
                        <span class="text-gray-400">{{ ucfirst($assessment['type']) }} Setup Progress</span>
                        <span class="text-gray-300">{{ $statusText }}</span>
                    </div>
                    <div class="w-full bg-gray-600 rounded-full h-2">
                        <div class="bg-{{ $totalItems > 0 ? 'green' : 'red' }}-500 h-2 rounded-full transition-all duration-300" 
                             style="width: {{ $progressPercent }}%"></div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Assessment Summary Stats -->
    <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        @php
            $stats = [
                'total' => count($assessments),
                'quizzes' => count(array_filter($assessments, fn($a) => $a['type'] === 'quiz')),
                'projects' => count(array_filter($assessments, fn($a) => $a['type'] === 'project')),
                'assignments' => count(array_filter($assessments, fn($a) => $a['type'] === 'assignment')),
                'qna' => count(array_filter($assessments, fn($a) => $a['type'] === 'qna')),
                'mandatory' => count(array_filter($assessments, fn($a) => $a['is_mandatory'])),
            ];
        @endphp

        <div class="bg-gray-700 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-blue-400">{{ $stats['total'] }}</div>
            <div class="text-sm text-gray-400">Total Assessments</div>
        </div>

        <div class="bg-gray-700 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-green-400">{{ $stats['quizzes'] }}</div>
            <div class="text-sm text-gray-400">Quizzes</div>
        </div>

        <div class="bg-gray-700 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-purple-400">{{ $stats['projects'] }}</div>
            <div class="text-sm text-gray-400">Projects</div>
        </div>

        <div class="bg-gray-700 rounded-lg p-4 text-center">
            <div class="text-2xl font-bold text-red-400">{{ $stats['mandatory'] }}</div>
            <div class="text-sm text-gray-400">Mandatory</div>
        </div>
    </div>
@else
    <!-- Empty State -->
    <div class="text-center py-12">
        <div class="w-20 h-20 bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-clipboard-check text-3xl text-gray-500"></i>
        </div>
        <h4 class="text-xl font-medium text-white mb-2">No Assessments Yet</h4>
        <p class="text-gray-400 mb-6 max-w-md mx-auto">
            Create assessments to evaluate student understanding and track their progress through your lesson.
        </p>
        <button wire:click="toggleCreateForm"
                class="px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition-colors">
            <i class="fas fa-plus mr-2"></i>
            Create Your First Assessment
        </button>
    </div>
@endif

@push('scripts')
<script>
// Drag and drop functionality for reordering assessments
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('assessments-container');
    if (container && typeof Sortable !== 'undefined') {
        new Sortable(container, {
            handle: '.drag-handle',
            animation: 150,
            ghostClass: 'opacity-50',
            onEnd: function(evt) {
                const orderedIds = Array.from(container.children).map(el => el.dataset.id);
                @this.reorderAssessments(orderedIds);
            }
        });
    }
});
</script>
@endpush