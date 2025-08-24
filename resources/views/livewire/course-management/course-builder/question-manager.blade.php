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
            <h3 class="text-lg font-medium text-white">Quiz Questions</h3>
            <p class="text-gray-400 text-sm">Total Points: {{ array_sum(array_column($questions, 'points')) }}</p>
        </div>
        <button wire:click="toggleCreateForm"
                class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors">
            <i class="fas fa-plus mr-2"></i>
            {{ $showCreateForm ? 'Cancel' : 'Add Question' }}
        </button>
    </div>

    <!-- Create/Edit Question Form -->
    @if ($showCreateForm)
        <div class="bg-gray-700 rounded-lg p-6 border border-gray-600">
            <h4 class="text-white font-medium mb-4">
                {{ $editingQuestion ? 'Edit Question' : 'Create New Question' }}
            </h4>

            <form wire:submit.prevent="{{ $editingQuestion ? 'updateQuestion' : 'createQuestion' }}" class="space-y-6">
                <!-- Question Type Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-3">Question Type</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        @php
                            $questionTypes = [
                                'multiple_choice' => ['icon' => 'fa-list', 'label' => 'Multiple Choice', 'color' => 'blue'],
                                'true_false' => ['icon' => 'fa-check-circle', 'label' => 'True/False', 'color' => 'green'],
                                'short_answer' => ['icon' => 'fa-edit', 'label' => 'Short Answer', 'color' => 'yellow'],
                                'essay' => ['icon' => 'fa-align-left', 'label' => 'Essay', 'color' => 'purple'],
                                'fill_blank' => ['icon' => 'fa-i-cursor', 'label' => 'Fill in Blank', 'color' => 'pink'],
                                'matching' => ['icon' => 'fa-exchange-alt', 'label' => 'Matching', 'color' => 'indigo'],
                            ];
                        @endphp

                        @foreach ($questionTypes as $type => $config)
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
                        Question Text <span class="text-red-400">*</span>
                    </label>
                    <textarea wire:model="questionText" rows="3"
                              class="w-full px-4 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white 
                                     focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                              placeholder="Enter your question here..."></textarea>
                    @error('questionText')
                        <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Question Settings -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Points</label>
                        <input type="number" wire:model="points" step="0.5" min="0.5" max="100"
                               class="w-full px-4 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white 
                                      focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        @error('points')
                            <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Time Limit (seconds)</label>
                        <input type="number" wire:model="timeLimit" min="1" max="300"
                               class="w-full px-4 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white 
                                      focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               placeholder="Optional">
                        @error('timeLimit')
                            <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Type-specific Options -->
                @if ($questionType === 'multiple_choice')
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <label class="block text-sm font-medium text-gray-300">Answer Options</label>
                            <button type="button" wire:click="addOption" 
                                    class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-sm"
                                    {{ count($options) >= 6 ? 'disabled' : '' }}>
                                <i class="fas fa-plus mr-1"></i>Add Option
                            </button>
                        </div>

                        @foreach ($options as $index => $option)
                            <div class="flex items-center gap-3">
                                <div class="flex items-center">
                                    <input type="checkbox" wire:model="correctAnswers" value="{{ $index }}"
                                           class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-600 rounded bg-gray-700">
                                    <label class="ml-2 text-sm text-gray-400">Correct</label>
                                </div>
                                
                                <input type="text" wire:model="options.{{ $index }}"
                                       class="flex-1 px-3 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white 
                                              focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                       placeholder="Option {{ $index + 1 }}">
                                
                                @if (count($options) > 2)
                                    <button type="button" wire:click="removeOption({{ $index }})"
                                            class="px-2 py-2 bg-red-600 hover:bg-red-700 text-white rounded">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                @endif
                            </div>
                        @endforeach
                        
                        @error('correctAnswers')
                            <span class="text-red-400 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                @elseif ($questionType === 'true_false')
                    <div class="space-y-4">
                        <label class="block text-sm font-medium text-gray-300">Correct Answer</label>
                        
                        <div class="space-y-2">
                            <div class="flex items-center gap-3">
                                <input type="radio" wire:model="correctAnswer" value="true" id="true_option"
                                       class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-600 bg-gray-700">
                                <input type="text" wire:model="trueAnswerText"
                                       class="flex-1 px-3 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white 
                                              focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                       placeholder="True option text">
                                <label for="true_option" class="text-sm text-green-400">Correct</label>
                            </div>
                            
                            <div class="flex items-center gap-3">
                                <input type="radio" wire:model="correctAnswer" value="false" id="false_option"
                                       class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-600 bg-gray-700">
                                <input type="text" wire:model="falseAnswerText"
                                       class="flex-1 px-3 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white 
                                              focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                       placeholder="False option text">
                                <label for="false_option" class="text-sm text-red-400">Incorrect</label>
                            </div>
                        </div>
                        
                        @error('correctAnswer')
                            <span class="text-red-400 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                @elseif (in_array($questionType, ['short_answer', 'fill_blank']))
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            {{ $questionType === 'short_answer' ? 'Sample/Expected Answer' : 'Correct Answer' }}
                        </label>
                        <input type="text" wire:model="correctAnswer"
                               class="w-full px-4 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white 
                                      focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               placeholder="{{ $questionType === 'short_answer' ? 'Enter expected answer or keywords' : 'Enter the correct word/phrase' }}">
                        @error('correctAnswer')
                            <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                        @enderror
                        
                        @if ($questionType === 'short_answer')
                            <p class="text-xs text-gray-500 mt-1">
                                For short answers, this will be used as a reference for manual grading.
                            </p>
                        @endif
                    </div>

                @elseif ($questionType === 'essay')
                    <div class="bg-blue-900/30 border border-blue-700 rounded-lg p-4">
                        <p class="text-blue-200 text-sm">
                            <i class="fas fa-info-circle mr-2"></i>
                            Essay questions require manual grading. You can provide grading rubrics and sample answers 
                            to help with consistent evaluation.
                        </p>
                    </div>
                @endif

                <!-- Explanation -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        Explanation/Feedback (Optional)
                    </label>
                    <textarea wire:model="explanation" rows="2"
                              class="w-full px-4 py-2 bg-gray-600 border border-gray-500 rounded-lg text-white 
                                     focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                              placeholder="Explain why this is the correct answer or provide learning feedback..."></textarea>
                    @error('explanation')
                        <span class="text-red-400 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Form Actions -->
                <div class="flex justify-between pt-4 border-t border-gray-600">
                    <button type="button" wire:click="toggleCreateForm"
                            class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                        Cancel
                    </button>
                    
                    <button type="submit" wire:loading.attr="disabled"
                            class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors 
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
        <h4 class="text-white font-medium">Questions ({{ count($questions) }})</h4>

        @if (count($questions) > 0)
            <div class="space-y-3" id="questions-container">
                @foreach ($questions as $index => $question)
                    <div class="bg-gray-700 rounded-lg border border-gray-600 p-4 sortable-item" 
                         data-id="{{ $question['id'] }}">
                        <div class="flex items-start gap-4">
                            <!-- Drag Handle -->
                            <div class="drag-handle cursor-move text-gray-500 hover:text-gray-300 mt-1">
                                <i class="fas fa-grip-vertical"></i>
                            </div>

                            <!-- Question Number -->
                            <div class="flex-shrink-0 w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                {{ $question['order'] }}
                            </div>

                            <!-- Question Content -->
                            <div class="flex-1">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="px-2 py-1 text-xs rounded-full 
                                                {{ $question['question_type'] === 'multiple_choice' ? 'bg-blue-100 text-blue-800' : 
                                                   ($question['question_type'] === 'true_false' ? 'bg-green-100 text-green-800' : 
                                                    ($question['question_type'] === 'short_answer' ? 'bg-yellow-100 text-yellow-800' : 
                                                     ($question['question_type'] === 'essay' ? 'bg-purple-100 text-purple-800' : 
                                                      'bg-gray-100 text-gray-800'))) }}">
                                                {{ ucfirst(str_replace('_', ' ', $question['question_type'])) }}
                                            </span>
                                            <span class="text-sm text-green-400 font-medium">{{ $question['points'] }} pts</span>
                                            @if ($question['time_limit'])
                                                <span class="text-sm text-blue-400">{{ $question['time_limit'] }}s</span>
                                            @endif
                                        </div>
                                        
                                        <p class="text-white font-medium mb-2">{{ $question['question_text'] }}</p>

                                        @if ($question['question_type'] === 'multiple_choice')
                                            @php
                                                $options = json_decode($question['options'], true) ?? [];
                                                $correctAnswers = json_decode($question['correct_answers'], true) ?? [];
                                            @endphp
                                            <div class="space-y-1">
                                                @foreach ($options as $optIndex => $option)
                                                    <div class="flex items-center gap-2 text-sm">
                                                        <span class="w-5 h-5 rounded-full border-2 flex items-center justify-center text-xs
                                                            {{ in_array($optIndex, $correctAnswers) ? 'border-green-500 bg-green-500 text-white' : 'border-gray-500' }}">
                                                            {{ chr(65 + $optIndex) }}
                                                        </span>
                                                        <span class="text-gray-300 {{ in_array($optIndex, $correctAnswers) ? 'font-medium text-green-300' : '' }}">
                                                            {{ $option }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @elseif ($question['question_type'] === 'true_false')
                                            @php
                                                $options = json_decode($question['options'], true) ?? ['True', 'False'];
                                                $correctAnswers = json_decode($question['correct_answers'], true) ?? [0];
                                            @endphp
                                            <div class="flex gap-4 text-sm">
                                                <span class="flex items-center gap-1">
                                                    <span class="w-4 h-4 rounded-full {{ $correctAnswers[0] === 0 ? 'bg-green-500' : 'bg-gray-500' }}"></span>
                                                    {{ $options[0] }}
                                                </span>
                                                <span class="flex items-center gap-1">
                                                    <span class="w-4 h-4 rounded-full {{ $correctAnswers[0] === 1 ? 'bg-green-500' : 'bg-gray-500' }}"></span>
                                                    {{ $options[1] }}
                                                </span>
                                            </div>
                                        @elseif (in_array($question['question_type'], ['short_answer', 'fill_blank']))
                                            @php
                                                $correctAnswers = json_decode($question['correct_answers'], true) ?? [];
                                            @endphp
                                            @if (!empty($correctAnswers[0]))
                                                <div class="text-sm text-green-300 bg-green-900/20 px-2 py-1 rounded">
                                                    <strong>Expected:</strong> {{ $correctAnswers[0] }}
                                                </div>
                                            @endif
                                        @endif

                                        @if ($question['explanation'])
                                            <div class="mt-2 text-sm text-gray-400 bg-gray-800 px-3 py-2 rounded">
                                                <strong>Explanation:</strong> {{ $question['explanation'] }}
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
                                                onclick="return confirm('Are you sure you want to delete this question?')"
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

            <!-- Question Statistics -->
            <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
                @php
                    $typeStats = array_count_values(array_column($questions, 'question_type'));
                    $totalPoints = array_sum(array_column($questions, 'points'));
                @endphp

                <div class="bg-gray-700 rounded-lg p-3 text-center">
                    <div class="text-xl font-bold text-blue-400">{{ $typeStats['multiple_choice'] ?? 0 }}</div>
                    <div class="text-xs text-gray-400">Multiple Choice</div>
                </div>

                <div class="bg-gray-700 rounded-lg p-3 text-center">
                    <div class="text-xl font-bold text-green-400">{{ $typeStats['true_false'] ?? 0 }}</div>
                    <div class="text-xs text-gray-400">True/False</div>
                </div>

                <div class="bg-gray-700 rounded-lg p-3 text-center">
                    <div class="text-xl font-bold text-yellow-400">{{ ($typeStats['short_answer'] ?? 0) + ($typeStats['essay'] ?? 0) }}</div>
                    <div class="text-xs text-gray-400">Written</div>
                </div>

                <div class="bg-gray-700 rounded-lg p-3 text-center">
                    <div class="text-xl font-bold text-purple-400">{{ $totalPoints }}</div>
                    <div class="text-xs text-gray-400">Total Points</div>
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-question text-2xl text-gray-500"></i>
                </div>
                <h4 class="text-lg font-medium text-white mb-2">No Questions Yet</h4>
                <p class="text-gray-400 mb-4">Create questions to build your quiz assessment.</p>
                <button wire:click="toggleCreateForm"
                        class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg">
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