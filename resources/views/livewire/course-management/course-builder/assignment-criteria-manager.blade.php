<div class="space-y-6">
    <!-- Success Message -->
    @if (session()->has('success'))
        <div class="bg-green-600 text-white p-4 rounded-lg animate__animated animate__fadeIn">
            {{ session('success') }}
        </div>
    @endif

    <!-- Header with Add Question Button -->
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-medium text-white">Assignment Questions</h3>
            <p class="text-gray-400 text-sm">Total Points: {{ array_sum(array_column($questions, 'points')) }}</p>
        </div>
        <button wire:click="toggleCreateForm"
            class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-colors">
            <i class="fas fa-plus mr-2"></i>
            {{ $showCreateForm ? 'Cancel' : 'Add Question' }}
        </button>
    </div>

    <!-- Create/Edit Question Form -->
    @if ($showCreateForm)
        <div class="bg-gray-700 rounded-lg p-6 border border-gray-600">
            <h4 class="text-white font-medium mb-4">
                {{ $editingQuestion ? 'Edit Assignment Question' : 'Create New Assignment Question' }}
            </h4>

            <form wire:submit.prevent="{{ $editingQuestion ? 'updateQuestion' : 'createQuestion' }}" class="space-y-6">
                <!-- Question Type Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-3">Assignment Type</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                        @php
                            $assignmentTypes = [
                                'essay' => ['icon' => 'fa-align-left', 'label' => 'Essay', 'color' => 'purple'],
                                'short_answer' => ['icon' => 'fa-edit', 'label' => 'Short Answer', 'color' => 'blue'],
                                'analysis' => ['icon' => 'fa-search', 'label' => 'Analysis', 'color' => 'green'],
                                'reflection' => [
                                    'icon' => 'fa-lightbulb',
                                    'label' => 'Reflection',
                                    'color' => 'yellow',
                                ],
                                'research' => ['icon' => 'fa-book', 'label' => 'Research', 'color' => 'red'],
                            ];
                        @endphp

                        @foreach ($assignmentTypes as $type => $config)
                            <button type="button" wire:click="selectQuestionType('{{ $type }}')"
                                class="p-3 rounded-lg text-center transition-colors border-2 text-sm
                                        {{ $questionType === $type
                                            ? 'bg-' . $config['color'] . '-600 border-' . $config['color'] . '-500 text-white'
                                            : 'bg-gray-600 border-gray-500 text-gray-300 hover:bg-gray-500 hover:border-gray-400' }}">
                                <i class="fas {{ $config['icon'] }} text-lg mb-1 block"></i>
                                <div class="font-medium">{{ $config['label'] }}</div>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Question Text -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        Question/Prompt <span class="text-red-400">*</span>
                    </label>
                    <textarea wire:model="questionText" rows="4"
                        class="w-full px-4 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white 
                                     focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                        placeholder="Enter your assignment question or prompt here..."></textarea>
                    @error('questionText')
                        <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Basic Settings -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Points</label>
                        <input type="number" wire:model="points" step="1" min="1" max="100"
                            class="w-full px-4 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white 
                                      focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                        @error('points')
                            <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Word Limit</label>
                        <input type="number" wire:model="wordLimit" min="10" max="10000"
                            class="w-full px-4 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white 
                                      focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                            placeholder="Optional">
                        @error('wordLimit')
                            <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Time Limit (minutes)</label>
                        <input type="number" wire:model="timeLimit" min="1" max="300"
                            class="w-full px-4 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white 
                                      focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                            placeholder="Optional">
                        @error('timeLimit')
                            <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Explanation/Instructions -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        Instructions/Explanation (Optional)
                    </label>
                    <textarea wire:model="explanation" rows="3"
                        class="w-full px-4 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white 
                                     focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                        placeholder="Provide additional instructions or context for the assignment..."></textarea>
                    @error('explanation')
                        <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Rubric Criteria -->
                <div class="bg-gray-600 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h5 class="text-white font-medium">Grading Rubric</h5>
                        <button type="button" wire:click="addRubricCriteria"
                            class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-sm">
                            <i class="fas fa-plus mr-1"></i>Add Criteria
                        </button>
                    </div>

                    @foreach ($rubricCriteria as $index => $criteria)
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-3 p-3 bg-gray-700 rounded-lg">
                            <div>
                                <label class="block text-xs text-gray-400 mb-1">Criteria Name</label>
                                <input type="text" wire:model="rubricCriteria.{{ $index }}.name"
                                    class="w-full px-3 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white text-sm"
                                    placeholder="e.g., Content Knowledge">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-400 mb-1">Weight (%)</label>
                                <input type="number" wire:model="rubricCriteria.{{ $index }}.weight"
                                    min="1" max="100"
                                    class="w-full px-3 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white text-sm"
                                    placeholder="40">
                            </div>
                            <div>
                                <label class="block text-xs text-gray-400 mb-1">Description</label>
                                <input type="text" wire:model="rubricCriteria.{{ $index }}.description"
                                    class="w-full px-3 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white text-sm"
                                    placeholder="Brief description">
                            </div>
                            <div class="flex items-end">
                                @if (count($rubricCriteria) > 1)
                                    <button type="button" wire:click="removeRubricCriteria({{ $index }})"
                                        class="px-2 py-2 bg-red-600 hover:bg-red-700 text-white rounded w-full">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    <!-- Weight Total Check -->
                    @php
                        $totalWeight = array_sum(array_column($rubricCriteria, 'weight'));
                    @endphp
                    <div class="text-sm {{ $totalWeight == 100 ? 'text-green-400' : 'text-yellow-400' }} mt-2">
                        Total Weight: {{ $totalWeight }}%
                        @if ($totalWeight != 100)
                            (Should equal 100%)
                        @endif
                    </div>
                </div>

                <!-- Sample Answer & Grading Notes -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Sample Answer (Optional)</label>
                        <textarea wire:model="sampleAnswer" rows="4"
                            class="w-full px-4 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white 
                                         focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                            placeholder="Provide a sample answer for grading reference..."></textarea>
                        @error('sampleAnswer')
                            <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Grading Notes (Optional)</label>
                        <textarea wire:model="gradingNotes" rows="4"
                            class="w-full px-4 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white 
                                         focus:ring-2 focus:ring-yellow-500 focus:border-transparent"
                            placeholder="Notes for instructors on how to grade this assignment..."></textarea>
                        @error('gradingNotes')
                            <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- File Upload Settings -->
                <div class="bg-gray-600 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h5 class="text-white font-medium">File Upload Settings</h5>
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="allowFileUpload"
                                class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-600 rounded bg-gray-700">
                            <span class="ml-2 text-sm text-gray-300">Allow file uploads</span>
                        </label>
                    </div>

                    @if ($allowFileUpload)
                        <div class="space-y-4">
                            <!-- File Types -->
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-sm font-medium text-gray-300">Allowed File Types</label>
                                    <button type="button" wire:click="addFileType"
                                        class="px-2 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-sm">
                                        <i class="fas fa-plus mr-1"></i>Add Type
                                    </button>
                                </div>

                                @if (count($fileTypes) > 0)
                                    @foreach ($fileTypes as $index => $fileType)
                                        <div class="flex gap-2 mb-2">
                                            <input type="text" wire:model="fileTypes.{{ $index }}"
                                                class="flex-1 px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white text-sm"
                                                placeholder="e.g., pdf, docx, jpg">
                                            <button type="button" wire:click="removeFileType({{ $index }})"
                                                class="px-2 py-2 bg-red-600 hover:bg-red-700 text-white rounded">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-sm text-gray-400">No file types specified - all types allowed</p>
                                @endif
                            </div>

                            <!-- Max File Size -->
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Max File Size (MB)</label>
                                <input type="number" wire:model="maxFileSize" min="1" max="100"
                                    class="w-32 px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white">
                                @error('maxFileSize')
                                    <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Required Toggle -->
                <div class="flex items-center">
                    <input type="checkbox" wire:model="isRequired" id="isRequired"
                        class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-600 rounded bg-gray-700">
                    <label for="isRequired" class="ml-2 block text-sm text-gray-300">
                        Required assignment (must be completed to pass)
                    </label>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-between pt-4 border-t border-gray-600">
                    <button type="button" wire:click="toggleCreateForm"
                        class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                        Cancel
                    </button>

                    <button type="submit" wire:loading.attr="disabled"
                        class="px-6 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-colors 
                                   disabled:opacity-50 flex items-center gap-2">
                        <span wire:loading.remove>
                            <i class="fas fa-{{ $editingQuestion ? 'save' : 'plus' }} mr-2"></i>
                            {{ $editingQuestion ? 'Update Question' : 'Create Question' }}
                        </span>
                        <span wire:loading class="flex items-center gap-2">
                            <i class="fas fa-spinner fa-spin"></i>
                            {{ $editingQuestion ? 'Updating...' : 'Creating...' }}
                        </span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Questions List -->
    <div class="space-y-4">
        <h4 class="text-white font-medium">Assignment Questions ({{ count($questions) }})</h4>

        @if (count($questions) > 0)
            <div class="space-y-3" id="questions-container">
                @foreach ($questions as $index => $question)
                    @php
                        $assignmentData = json_decode($question['options'], true) ?? [];
                        $assignmentType = $assignmentData['assignment_type'] ?? 'essay';
                        $rubricCriteria = $assignmentData['rubric_criteria'] ?? [];
                    @endphp
                    <div class="bg-gray-700 rounded-lg border border-gray-600 p-4 sortable-item"
                        data-id="{{ $question['id'] }}">
                        <div class="flex items-start gap-4">
                            <!-- Drag Handle -->
                            <div class="drag-handle cursor-move text-gray-500 hover:text-gray-300 mt-1">
                                <i class="fas fa-grip-vertical"></i>
                            </div>

                            <!-- Question Number -->
                            <div
                                class="flex-shrink-0 w-8 h-8 bg-yellow-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                {{ $question['order'] }}
                            </div>

                            <!-- Question Content -->
                            <div class="flex-1">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span
                                                class="px-2 py-1 text-xs rounded-full 
                                                {{ $assignmentType === 'essay'
                                                    ? 'bg-purple-100 text-purple-800'
                                                    : ($assignmentType === 'short_answer'
                                                        ? 'bg-blue-100 text-blue-800'
                                                        : ($assignmentType === 'analysis'
                                                            ? 'bg-green-100 text-green-800'
                                                            : ($assignmentType === 'reflection'
                                                                ? 'bg-yellow-100 text-yellow-800'
                                                                : 'bg-red-100 text-red-800'))) }}">
                                                {{ ucfirst(str_replace('_', ' ', $assignmentType)) }}
                                            </span>
                                            <span class="text-sm text-green-400 font-medium">{{ $question['points'] }}
                                                pts</span>
                                            @if ($assignmentData['word_limit'] ?? null)
                                                <span
                                                    class="text-sm text-blue-400">{{ $assignmentData['word_limit'] }}
                                                    words max</span>
                                            @endif
                                            @if ($question['time_limit'])
                                                <span
                                                    class="text-sm text-purple-400">{{ $question['time_limit'] }}min</span>
                                            @endif
                                            @if ($question['is_required'])
                                                <span class="text-xs text-red-400">Required</span>
                                            @endif
                                        </div>

                                        <p class="text-white font-medium mb-2">{{ $question['question_text'] }}</p>

                                        @if ($question['explanation'])
                                            <div class="text-sm text-gray-400 bg-gray-800 px-3 py-2 rounded mb-2">
                                                <strong>Instructions:</strong> {{ $question['explanation'] }}
                                            </div>
                                        @endif

                                        <!-- Rubric Preview -->
                                        @if (!empty($rubricCriteria))
                                            <div class="text-sm text-blue-300 bg-blue-900/20 px-3 py-2 rounded">
                                                <strong>Grading Rubric:</strong>
                                                @foreach ($rubricCriteria as $criteria)
                                                    {{ $criteria['name'] }}
                                                    ({{ $criteria['weight'] }}%){{ !$loop->last ? ', ' : '' }}
                                                @endforeach
                                            </div>
                                        @endif

                                        <!-- File Upload Info -->
                                        @if ($assignmentData['allow_file_upload'] ?? false)
                                            <div class="text-sm text-green-300 bg-green-900/20 px-3 py-2 rounded mt-2">
                                                <strong>File Upload:</strong>
                                                @if (!empty($assignmentData['file_types']))
                                                    {{ implode(', ', $assignmentData['file_types']) }} files allowed,
                                                @endif
                                                max {{ $assignmentData['max_file_size'] ?? 10 }}MB
                                            </div>
                                        @endif

                                        <!-- Sample Answer Preview -->
                                        @if (!empty($assignmentData['sample_answer']))
                                            <div
                                                class="text-sm text-yellow-300 bg-yellow-900/20 px-3 py-2 rounded mt-2">
                                                <strong>Sample Answer Available</strong>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex gap-2 ml-4">
                                        <button wire:click="editQuestion({{ $question['id'] }})"
                                            class="px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <button wire:click="duplicateQuestion({{ $question['id'] }})"
                                            class="px-2 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-sm"
                                            title="Duplicate Question">
                                            <i class="fas fa-copy"></i>
                                        </button>

                                        <button wire:click="deleteQuestion({{ $question['id'] }})"
                                            onclick="return confirm('Are you sure you want to delete this assignment question?')"
                                            class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Assignment Statistics -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                @php
                    $typeStats = [];
                    $totalPoints = array_sum(array_column($questions, 'points'));
                    $totalWordLimit = 0;
                    $timedQuestions = 0;

                    foreach ($questions as $q) {
                        $data = json_decode($q['options'], true) ?? [];
                        $type = $data['assignment_type'] ?? 'essay';
                        $typeStats[$type] = ($typeStats[$type] ?? 0) + 1;
                        if (!empty($data['word_limit'])) {
                            $totalWordLimit += $data['word_limit'];
                        }
                        if (!empty($q['time_limit'])) {
                            $timedQuestions++;
                        }
                    }
                @endphp

                <div class="bg-gray-700 rounded-lg p-3 text-center">
                    <div class="text-xl font-bold text-yellow-400">{{ $totalPoints }}</div>
                    <div class="text-xs text-gray-400">Total Points</div>
                </div>

                <div class="bg-gray-700 rounded-lg p-3 text-center">
                    <div class="text-xl font-bold text-purple-400">{{ $typeStats['essay'] ?? 0 }}</div>
                    <div class="text-xs text-gray-400">Essays</div>
                </div>

                <div class="bg-gray-700 rounded-lg p-3 text-center">
                    <div class="text-xl font-bold text-blue-400">
                        {{ ($typeStats['analysis'] ?? 0) + ($typeStats['research'] ?? 0) }}</div>
                    <div class="text-xs text-gray-400">Research/Analysis</div>
                </div>

                <div class="bg-gray-700 rounded-lg p-3 text-center">
                    <div class="text-xl font-bold text-green-400">{{ $timedQuestions }}</div>
                    <div class="text-xs text-gray-400">Timed Questions</div>
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-clipboard-list text-2xl text-gray-500"></i>
                </div>
                <h4 class="text-lg font-medium text-white mb-2">No Assignment Questions Yet</h4>
                <p class="text-gray-400 mb-4">Create assignment questions to evaluate student understanding through
                    written work.</p>
                <button wire:click="toggleCreateForm"
                    class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg">
                    <i class="fas fa-plus mr-2"></i>
                    Add Your First Question
                </button>
            </div>
        @endif
    </div>
</div>

@push('scripts')
    <script>
        // Sortable functionality for questions
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('questions-container');
            if (container && typeof Sortable !== 'undefined') {
                new Sortable(container, {
                    handle: '.drag-handle',
                    animation: 150,
                    ghostClass: 'opacity-50',
                    onEnd: function(evt) {
                        const orderedIds = Array.from(container.children).map(el => el.dataset.id);
                        @this.reorderQuestions(orderedIds);
                    }
                });
            }
        });
    </script>
@endpush
