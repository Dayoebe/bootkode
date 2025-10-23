<div class="space-y-6">
    <!-- Success Message -->
    @if (session()->has('success'))
        <div class="bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 border border-green-200 dark:border-green-700 p-4 rounded-lg animate__animated animate__fadeIn transition-colors duration-300">
            {{ session('success') }}
        </div>
    @endif

    <!-- Header with Add Question Button -->
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-medium text-themed-primary transition-colors duration-300">Assignment Questions</h3>
            <p class="text-themed-secondary text-sm transition-colors duration-300">Total Points: {{ array_sum(array_column($questions, 'points')) }}</p>
        </div>
        <button wire:click="toggleCreateForm"
            class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-colors">
            <i class="fas fa-plus mr-2"></i>
            {{ $showCreateForm ? 'Cancel' : 'Add Question' }}
        </button>
    </div>

    <!-- Create/Edit Question Form -->
    @if ($showCreateForm)
        <div class="bg-themed-tertiary rounded-lg p-6 border border-themed-primary transition-colors duration-300">
            <h4 class="text-themed-primary font-medium mb-4 transition-colors duration-300">
                {{ $editingQuestion ? 'Edit Assignment Question' : 'Create New Assignment Question' }}
            </h4>

            <form wire:submit.prevent="{{ $editingQuestion ? 'updateQuestion' : 'createQuestion' }}" class="space-y-6">
                <!-- Question Type Selection -->
                <div>
                    <label class="block text-sm font-medium text-themed-primary mb-3 transition-colors duration-300">Assignment Type</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                        @php
                            $assignmentTypes = [
                                'essay' => ['icon' => 'fa-align-left', 'label' => 'Essay', 'color' => 'purple'],
                                'short_answer' => ['icon' => 'fa-edit', 'label' => 'Short Answer', 'color' => 'blue'],
                                'analysis' => ['icon' => 'fa-search', 'label' => 'Analysis', 'color' => 'green'],
                                'reflection' => ['icon' => 'fa-lightbulb', 'label' => 'Reflection', 'color' => 'yellow'],
                                'research' => ['icon' => 'fa-book', 'label' => 'Research', 'color' => 'red'],
                            ];
                        @endphp

                        @foreach ($assignmentTypes as $type => $config)
                            <button type="button" wire:click="selectQuestionType('{{ $type }}')"
                                class="p-3 rounded-lg text-center transition-colors border-2 text-sm duration-300
                                        {{ $questionType === $type
                                            ? 'bg-' . $config['color'] . '-600 border-' . $config['color'] . '-500 text-white'
                                            : 'bg-themed-secondary border-themed-primary text-themed-primary hover:bg-themed-tertiary hover:border-' . $config['color'] . '-400' }}">
                                <i class="fas {{ $config['icon'] }} text-lg mb-1 block"></i>
                                <div class="font-medium">{{ $config['label'] }}</div>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- Question Text -->
                <div>
                    <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">
                        Question/Prompt <span class="text-red-600 dark:text-red-400">*</span>
                    </label>
                    <textarea wire:model="questionText" rows="4"
                        class="w-full px-4 py-2 bg-themed-secondary border border-themed-primary rounded-lg text-themed-primary 
                                     focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-colors duration-300"
                        placeholder="Enter your assignment question or prompt here..."></textarea>
                    @error('questionText')
                        <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Basic Settings -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Points</label>
                        <input type="number" wire:model="points" step="1" min="1" max="100"
                            class="w-full px-4 py-2 bg-themed-secondary border border-themed-primary rounded-lg text-themed-primary 
                                      focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-colors duration-300">
                        @error('points')
                            <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Word Limit</label>
                        <input type="number" wire:model="wordLimit" min="10" max="10000"
                            class="w-full px-4 py-2 bg-themed-secondary border border-themed-primary rounded-lg text-themed-primary 
                                      focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-colors duration-300"
                            placeholder="Optional">
                        @error('wordLimit')
                            <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Time Limit (minutes)</label>
                        <input type="number" wire:model="timeLimit" min="1" max="300"
                            class="w-full px-4 py-2 bg-themed-secondary border border-themed-primary rounded-lg text-themed-primary 
                                      focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-colors duration-300"
                            placeholder="Optional">
                        @error('timeLimit')
                            <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Explanation/Instructions -->
                <div>
                    <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">
                        Instructions/Explanation (Optional)
                    </label>
                    <textarea wire:model="explanation" rows="3"
                        class="w-full px-4 py-2 bg-themed-secondary border border-themed-primary rounded-lg text-themed-primary 
                                     focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-colors duration-300"
                        placeholder="Provide additional instructions or context for the assignment..."></textarea>
                    @error('explanation')
                        <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Rubric Criteria -->
                <div class="bg-themed-secondary rounded-lg p-4 border border-themed-primary transition-colors duration-300">
                    <div class="flex items-center justify-between mb-3">
                        <h5 class="text-themed-primary font-medium transition-colors duration-300">Grading Rubric</h5>
                        <button type="button" wire:click="addRubricCriteria"
                            class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-sm transition-colors">
                            <i class="fas fa-plus mr-1"></i>Add Criteria
                        </button>
                    </div>

                    @foreach ($rubricCriteria as $index => $criteria)
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-3 p-3 bg-themed-tertiary rounded-lg border border-themed-primary transition-colors duration-300">
                            <div>
                                <label class="block text-xs text-themed-secondary mb-1 transition-colors duration-300">Criteria Name</label>
                                <input type="text" wire:model="rubricCriteria.{{ $index }}.name"
                                    class="w-full px-3 py-2 bg-themed-secondary border border-themed-primary rounded-lg text-themed-primary text-sm transition-colors duration-300"
                                    placeholder="e.g., Content Knowledge">
                            </div>
                            <div>
                                <label class="block text-xs text-themed-secondary mb-1 transition-colors duration-300">Weight (%)</label>
                                <input type="number" wire:model="rubricCriteria.{{ $index }}.weight"
                                    min="1" max="100"
                                    class="w-full px-3 py-2 bg-themed-secondary border border-themed-primary rounded-lg text-themed-primary text-sm transition-colors duration-300"
                                    placeholder="40">
                            </div>
                            <div>
                                <label class="block text-xs text-themed-secondary mb-1 transition-colors duration-300">Description</label>
                                <input type="text" wire:model="rubricCriteria.{{ $index }}.description"
                                    class="w-full px-3 py-2 bg-themed-secondary border border-themed-primary rounded-lg text-themed-primary text-sm transition-colors duration-300"
                                    placeholder="Brief description">
                            </div>
                            <div class="flex items-end">
                                @if (count($rubricCriteria) > 1)
                                    <button type="button" wire:click="removeRubricCriteria({{ $index }})"
                                        class="px-2 py-2 bg-red-600 hover:bg-red-700 text-white rounded w-full transition-colors">
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
                    <div class="text-sm {{ $totalWeight == 100 ? 'text-green-600 dark:text-green-400' : 'text-yellow-600 dark:text-yellow-400' }} mt-2 transition-colors duration-300">
                        Total Weight: {{ $totalWeight }}%
                        @if ($totalWeight != 100)
                            (Should equal 100%)
                        @endif
                    </div>
                </div>

                <!-- Sample Answer & Grading Notes -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Sample Answer (Optional)</label>
                        <textarea wire:model="sampleAnswer" rows="4"
                            class="w-full px-4 py-2 bg-themed-secondary border border-themed-primary rounded-lg text-themed-primary 
                                         focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-colors duration-300"
                            placeholder="Provide a sample answer for grading reference..."></textarea>
                        @error('sampleAnswer')
                            <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Grading Notes (Optional)</label>
                        <textarea wire:model="gradingNotes" rows="4"
                            class="w-full px-4 py-2 bg-themed-secondary border border-themed-primary rounded-lg text-themed-primary 
                                         focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-colors duration-300"
                            placeholder="Notes for instructors on how to grade this assignment..."></textarea>
                        @error('gradingNotes')
                            <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- File Upload Settings -->
                <div class="bg-themed-secondary rounded-lg p-4 border border-themed-primary transition-colors duration-300">
                    <div class="flex items-center justify-between mb-3">
                        <h5 class="text-themed-primary font-medium transition-colors duration-300">File Upload Settings</h5>
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="allowFileUpload"
                                class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-themed-primary rounded bg-themed-tertiary">
                            <span class="ml-2 text-sm text-themed-primary transition-colors duration-300">Allow file uploads</span>
                        </label>
                    </div>

                    @if ($allowFileUpload)
                        <div class="space-y-4">
                            <!-- File Types -->
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label class="block text-sm font-medium text-themed-primary transition-colors duration-300">Allowed File Types</label>
                                    <button type="button" wire:click="addFileType"
                                        class="px-2 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-sm transition-colors">
                                        <i class="fas fa-plus mr-1"></i>Add Type
                                    </button>
                                </div>

                                @if (count($fileTypes) > 0)
                                    @foreach ($fileTypes as $index => $fileType)
                                        <div class="flex gap-2 mb-2">
                                            <input type="text" wire:model="fileTypes.{{ $index }}"
                                                class="flex-1 px-3 py-2 bg-themed-tertiary border border-themed-primary rounded-lg text-themed-primary text-sm transition-colors duration-300"
                                                placeholder="e.g., pdf, docx, jpg">
                                            <button type="button" wire:click="removeFileType({{ $index }})"
                                                class="px-2 py-2 bg-red-600 hover:bg-red-700 text-white rounded transition-colors">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-sm text-themed-secondary transition-colors duration-300">No file types specified - all types allowed</p>
                                @endif
                            </div>

                            <!-- Max File Size -->
                            <div>
                                <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Max File Size (MB)</label>
                                <input type="number" wire:model="maxFileSize" min="1" max="100"
                                    class="w-32 px-3 py-2 bg-themed-tertiary border border-themed-primary rounded-lg text-themed-primary transition-colors duration-300">
                                @error('maxFileSize')
                                    <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Required Toggle -->
                <div class="flex items-center">
                    <input type="checkbox" wire:model="isRequired" id="isRequired"
                        class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-themed-primary rounded bg-themed-tertiary">
                    <label for="isRequired" class="ml-2 block text-sm text-themed-primary transition-colors duration-300">
                        Required assignment (must be completed to pass)
                    </label>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-between pt-4 border-t border-themed-primary transition-colors duration-300">
                    <button type="button" wire:click="toggleCreateForm"
                        class="px-4 py-2 bg-themed-tertiary hover:bg-themed-secondary text-themed-primary rounded-lg transition-colors duration-300 border border-themed-primary">
                        Cancel
                    </button>

                    <button type="submit" wire:loading.attr="disabled"
                        class="px-6 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-colors duration-300 
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
        <h4 class="text-themed-primary font-medium transition-colors duration-300">Assignment Questions ({{ count($questions) }})</h4>

        @if (count($questions) > 0)
            <div class="space-y-3" id="questions-container">
                @foreach ($questions as $index => $question)
                    @php
                        $assignmentData = json_decode($question['options'], true) ?? [];
                        $assignmentType = $assignmentData['assignment_type'] ?? 'essay';
                        $rubricCriteria = $assignmentData['rubric_criteria'] ?? [];
                    @endphp
                    <div class="bg-themed-tertiary rounded-lg border border-themed-primary p-4 sortable-item transition-colors duration-300"
                        data-id="{{ $question['id'] }}">
                        <div class="flex items-start gap-4">
                            <!-- Drag Handle -->
                            <div class="drag-handle cursor-move text-themed-secondary hover:text-themed-primary mt-1 transition-colors duration-300">
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
                                                class="px-2 py-1 text-xs rounded-full border transition-colors duration-300
                                                {{ $assignmentType === 'essay'
                                                    ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300 border-purple-200 dark:border-purple-700'
                                                    : ($assignmentType === 'short_answer'
                                                        ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 border-blue-200 dark:border-blue-700'
                                                        : ($assignmentType === 'analysis'
                                                            ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 border-green-200 dark:border-green-700'
                                                            : ($assignmentType === 'reflection'
                                                                ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 border-yellow-200 dark:border-yellow-700'
                                                                : 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 border-red-200 dark:border-red-700'))) }}">
                                                {{ ucfirst(str_replace('_', ' ', $assignmentType)) }}
                                            </span>
                                            <span class="text-sm text-green-600 dark:text-green-400 font-medium">{{ $question['points'] }}
                                                pts</span>
                                            @if ($assignmentData['word_limit'] ?? null)
                                                <span
                                                    class="text-sm text-blue-600 dark:text-blue-400">{{ $assignmentData['word_limit'] }}
                                                    words max</span>
                                            @endif
                                            @if ($question['time_limit'])
                                                <span
                                                    class="text-sm text-purple-600 dark:text-purple-400">{{ $question['time_limit'] }}min</span>
                                            @endif
                                            @if ($question['is_required'])
                                                <span class="text-xs text-red-600 dark:text-red-400">Required</span>
                                            @endif
                                        </div>

                                        <p class="text-themed-primary font-medium mb-2 transition-colors duration-300">{{ $question['question_text'] }}</p>

                                        @if ($question['explanation'])
                                            <div class="text-sm text-themed-secondary bg-themed-secondary px-3 py-2 rounded mb-2 border border-themed-primary transition-colors duration-300">
                                                <strong>Instructions:</strong> {{ $question['explanation'] }}
                                            </div>
                                        @endif>

                                        <!-- Rubric Preview -->
                                        @if (!empty($rubricCriteria))
                                            <div class="text-sm text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-900/20 px-3 py-2 rounded border border-blue-200 dark:border-blue-700 transition-colors duration-300">
                                                <strong>Grading Rubric:</strong>
                                                @foreach ($rubricCriteria as $criteria)
                                                    {{ $criteria['name'] }}
                                                    ({{ $criteria['weight'] }}%){{ !$loop->last ? ', ' : '' }}
                                                @endforeach
                                            </div>
                                        @endif

                                        <!-- File Upload Info -->
                                        @if ($assignmentData['allow_file_upload'] ?? false)
                                            <div class="text-sm text-green-700 dark:text-green-300 bg-green-50 dark:bg-green-900/20 px-3 py-2 rounded mt-2 border border-green-200 dark:border-green-700 transition-colors duration-300">
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
                                                class="text-sm text-yellow-700 dark:text-yellow-300 bg-yellow-50 dark:bg-yellow-900/20 px-3 py-2 rounded mt-2 border border-yellow-200 dark:border-yellow-700 transition-colors duration-300">
                                                <strong>Sample Answer Available</strong>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex gap-2 ml-4">
                                        <button wire:click="editQuestion({{ $question['id'] }})"
                                            class="px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm transition-colors">
                                            <i class="fas fa-edit"></i>
                                        </button>

                                        <button wire:click="duplicateQuestion({{ $question['id'] }})"
                                            class="px-2 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-sm transition-colors"
                                            title="Duplicate Question">
                                            <i class="fas fa-copy"></i>
                                        </button>

                                        <button wire:click="deleteQuestion({{ $question['id'] }})"
                                            onclick="return confirm('Are you sure you want to delete this assignment question?')"
                                            class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-sm transition-colors">
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

                <div class="bg-themed-tertiary rounded-lg p-3 text-center border border-themed-primary transition-colors duration-300">
                    <div class="text-xl font-bold text-yellow-600 dark:text-yellow-400">{{ $totalPoints }}</div>
                    <div class="text-xs text-themed-secondary transition-colors duration-300">Total Points</div>
                </div>

                <div class="bg-themed-tertiary rounded-lg p-3 text-center border border-themed-primary transition-colors duration-300">
                    <div class="text-xl font-bold text-purple-600 dark:text-purple-400">{{ $typeStats['essay'] ?? 0 }}</div>
                    <div class="text-xs text-themed-secondary transition-colors duration-300">Essays</div>
                </div>

                <div class="bg-themed-tertiary rounded-lg p-3 text-center border border-themed-primary transition-colors duration-300">
                    <div class="text-xl font-bold text-blue-600 dark:text-blue-400">
                        {{ ($typeStats['analysis'] ?? 0) + ($typeStats['research'] ?? 0) }}</div>
                    <div class="text-xs text-themed-secondary transition-colors duration-300">Research/Analysis</div>
                </div>

                <div class="bg-themed-tertiary rounded-lg p-3 text-center border border-themed-primary transition-colors duration-300">
                    <div class="text-xl font-bold text-green-600 dark:text-green-400">{{ $timedQuestions }}</div>
                    <div class="text-xs text-themed-secondary transition-colors duration-300">Timed Questions</div>
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-themed-tertiary rounded-full flex items-center justify-center mx-auto mb-4 border border-themed-primary transition-colors duration-300">
                    <i class="fas fa-clipboard-list text-2xl text-themed-secondary transition-colors duration-300"></i>
                </div>
                <h4 class="text-lg font-medium text-themed-primary mb-2 transition-colors duration-300">No Assignment Questions Yet</h4>
                <p class="text-themed-secondary mb-4 transition-colors duration-300">Create assignment questions to evaluate student understanding through
                    written work.</p>
                <button wire:click="toggleCreateForm"
                    class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-colors">
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