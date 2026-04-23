<div class="space-y-6">
    @php
        $normalizeQuestionArray = static function ($value, array $default = []) {
            if (is_array($value)) {
                return $value;
            }

            if ($value instanceof \Illuminate\Support\Collection) {
                return $value->values()->all();
            }

            if ($value instanceof \Illuminate\Contracts\Support\Arrayable) {
                $arrayValue = $value->toArray();

                return is_array($arrayValue) ? $arrayValue : $default;
            }

            if ($value instanceof \JsonSerializable) {
                $jsonValue = $value->jsonSerialize();

                return is_array($jsonValue) ? $jsonValue : $default;
            }

            if (is_string($value)) {
                $decoded = json_decode($value, true);

                return is_array($decoded) ? $decoded : $default;
            }

            return $default;
        };
    @endphp
    <!-- Success Message -->
    @if (session()->has('success'))
        <div class="bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 border border-green-200 dark:border-green-700 p-4 rounded-lg animate__animated animate__fadeIn transition-colors duration-300">
            {{ session('success') }}
        </div>
    @endif

    <!-- Header with Add Question Button -->
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-medium text-themed-primary transition-colors duration-300">Quiz Questions</h3>
            <p class="text-themed-secondary text-sm transition-colors duration-300">Total Points: {{ array_sum(array_column($questions, 'points')) }}</p>
        </div>
        <button wire:click="toggleCreateForm"
                class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors duration-300">
            <i class="fas fa-plus mr-2"></i>
            {{ $showCreateForm ? 'Cancel' : 'Add Question' }}
        </button>
    </div>

    <!-- Create/Edit Question Form -->
    @if ($showCreateForm)
        <div class="bg-themed-tertiary rounded-lg p-6 border border-themed-primary transition-colors duration-300">
            <h4 class="text-themed-primary font-medium mb-4 transition-colors duration-300">
                {{ $editingQuestion ? 'Edit Question' : 'Create New Question' }}
            </h4>

            <form wire:submit.prevent="{{ $editingQuestion ? 'updateQuestion' : 'createQuestion' }}" class="space-y-6">
                <!-- Question Type Selection -->
                <div>
                    <label class="block text-sm font-medium text-themed-primary mb-3 transition-colors duration-300">Question Type</label>
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
                                    class="p-3 rounded-lg text-center transition-colors duration-300 border-2 text-sm
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
                        Question Text <span class="text-red-600 dark:text-red-400">*</span>
                    </label>
                    <textarea wire:model="questionText" rows="3"
                              class="w-full px-4 py-2 bg-themed-secondary border border-themed-primary rounded-lg text-themed-primary 
                                     focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-300"
                              placeholder="Enter your question here..."></textarea>
                    @error('questionText')
                        <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Question Settings -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Points</label>
                        <input type="number" wire:model="points" step="0.5" min="0.5" max="100"
                               class="w-full px-4 py-2 bg-themed-secondary border border-themed-primary rounded-lg text-themed-primary 
                                      focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-300">
                        @error('points')
                            <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">Time Limit (seconds)</label>
                        <input type="number" wire:model="timeLimit" min="1" max="300"
                               class="w-full px-4 py-2 bg-themed-secondary border border-themed-primary rounded-lg text-themed-primary 
                                      focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-300"
                               placeholder="Optional">
                        @error('timeLimit')
                            <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Type-specific Options -->
                @if ($questionType === 'multiple_choice')
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <label class="block text-sm font-medium text-themed-primary transition-colors duration-300">Answer Options</label>
                            <button type="button" wire:click="addOption" 
                                    class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-sm transition-colors duration-300"
                                    {{ count($options) >= 6 ? 'disabled' : '' }}>
                                <i class="fas fa-plus mr-1"></i>Add Option
                            </button>
                        </div>

                        @foreach ($options as $index => $option)
                            <div class="flex items-center gap-3">
                                <div class="flex items-center">
                                    <input type="checkbox" wire:model="correctAnswers" value="{{ $index }}"
                                           class="h-4 w-4 text-green-600 focus:ring-green-500 border-themed-primary rounded bg-themed-secondary">
                                    <label class="ml-2 text-sm text-themed-secondary transition-colors duration-300">Correct</label>
                                </div>
                                
                                <input type="text" wire:model="options.{{ $index }}"
                                       class="flex-1 px-3 py-2 bg-themed-secondary border border-themed-primary rounded-lg text-themed-primary 
                                              focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-300"
                                       placeholder="Option {{ $index + 1 }}">
                                
                                @if (count($options) > 2)
                                    <button type="button" wire:click="removeOption({{ $index }})"
                                            class="px-2 py-2 bg-red-600 hover:bg-red-700 text-white rounded transition-colors duration-300">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                @endif
                            </div>
                        @endforeach
                        
                        @error('correctAnswers')
                            <span class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                @elseif ($questionType === 'true_false')
                    <div class="space-y-4">
                        <label class="block text-sm font-medium text-themed-primary transition-colors duration-300">Correct Answer</label>
                        
                        <div class="space-y-2">
                            <div class="flex items-center gap-3">
                                <input type="radio" wire:model="correctAnswer" value="true" id="true_option"
                                       class="h-4 w-4 text-green-600 focus:ring-green-500 border-themed-primary bg-themed-secondary">
                                <input type="text" wire:model="trueAnswerText"
                                       class="flex-1 px-3 py-2 bg-themed-secondary border border-themed-primary rounded-lg text-themed-primary 
                                              focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-300"
                                       placeholder="True option text">
                                <label for="true_option" class="text-sm text-green-600 dark:text-green-400">Correct</label>
                            </div>
                            
                            <div class="flex items-center gap-3">
                                <input type="radio" wire:model="correctAnswer" value="false" id="false_option"
                                       class="h-4 w-4 text-red-600 focus:ring-red-500 border-themed-primary bg-themed-secondary">
                                <input type="text" wire:model="falseAnswerText"
                                       class="flex-1 px-3 py-2 bg-themed-secondary border border-themed-primary rounded-lg text-themed-primary 
                                              focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-300"
                                       placeholder="False option text">
                                <label for="false_option" class="text-sm text-red-600 dark:text-red-400">Incorrect</label>
                            </div>
                        </div>
                        
                        @error('correctAnswer')
                            <span class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                @elseif (in_array($questionType, ['short_answer', 'fill_blank']))
                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">
                            {{ $questionType === 'short_answer' ? 'Sample/Expected Answer' : 'Correct Answer' }}
                        </label>
                        <input type="text" wire:model="correctAnswer"
                               class="w-full px-4 py-2 bg-themed-secondary border border-themed-primary rounded-lg text-themed-primary 
                                      focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-300"
                               placeholder="{{ $questionType === 'short_answer' ? 'Enter expected answer or keywords' : 'Enter the correct word/phrase' }}">
                        @error('correctAnswer')
                            <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                        @enderror
                        
                        @if ($questionType === 'short_answer')
                            <p class="text-xs text-themed-secondary mt-1 transition-colors duration-300">
                                For short answers, this will be used as a reference for manual grading.
                            </p>
                        @endif
                    </div>

                @elseif ($questionType === 'essay')
                    <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-700 rounded-lg p-4 transition-colors duration-300">
                        <p class="text-blue-800 dark:text-blue-200 text-sm">
                            <i class="fas fa-info-circle mr-2"></i>
                            Essay questions require manual grading. You can provide grading rubrics and sample answers 
                            to help with consistent evaluation.
                        </p>
                    </div>
                @endif

                <!-- Explanation -->
                <div>
                    <label class="block text-sm font-medium text-themed-primary mb-2 transition-colors duration-300">
                        Explanation/Feedback (Optional)
                    </label>
                    <textarea wire:model="explanation" rows="2"
                              class="w-full px-4 py-2 bg-themed-secondary border border-themed-primary rounded-lg text-themed-primary 
                                     focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-colors duration-300"
                              placeholder="Explain why this is the correct answer or provide learning feedback..."></textarea>
                    @error('explanation')
                        <span class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Form Actions -->
                <div class="flex justify-between pt-4 border-t border-themed-primary transition-colors duration-300">
                    <button type="button" wire:click="toggleCreateForm"
                            class="px-4 py-2 bg-themed-tertiary hover:bg-themed-secondary text-themed-primary rounded-lg transition-colors duration-300 border border-themed-primary">
                        Cancel
                    </button>
                    
                    <button type="submit" wire:loading.attr="disabled"
                            class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors duration-300 
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
        <h4 class="text-themed-primary font-medium transition-colors duration-300">Questions ({{ count($questions) }})</h4>

        @if (count($questions) > 0)
            <div class="space-y-3" id="questions-container">
                @foreach ($questions as $index => $question)
                    <div class="bg-themed-tertiary rounded-lg border border-themed-primary p-4 sortable-item transition-colors duration-300" 
                         data-id="{{ $question['id'] }}">
                        <div class="flex items-start gap-4">
                            <!-- Drag Handle -->
                            <div class="drag-handle cursor-move text-themed-secondary hover:text-themed-primary mt-1 transition-colors duration-300">
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
                                            <span class="px-2 py-1 text-xs rounded-full border transition-colors duration-300
                                                {{ $question['question_type'] === 'multiple_choice' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 border-blue-200 dark:border-blue-700' : 
                                                   ($question['question_type'] === 'true_false' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300 border-green-200 dark:border-green-700' : 
                                                    ($question['question_type'] === 'short_answer' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300 border-yellow-200 dark:border-yellow-700' : 
                                                     ($question['question_type'] === 'essay' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300 border-purple-200 dark:border-purple-700' : 
                                                      'bg-themed-secondary text-themed-primary border-themed-primary'))) }}">
                                                {{ ucfirst(str_replace('_', ' ', $question['question_type'])) }}
                                            </span>
                                            <span class="text-sm text-green-600 dark:text-green-400 font-medium">{{ $question['points'] }} pts</span>
                                            @if ($question['time_limit'])
                                                <span class="text-sm text-blue-600 dark:text-blue-400">{{ $question['time_limit'] }}s</span>
                                            @endif
                                        </div>
                                        
                                        <p class="text-themed-primary font-medium mb-2 transition-colors duration-300">{{ $question['question_text'] }}</p>

                                        @if ($question['question_type'] === 'multiple_choice')
                                            @php
                                                $options = $normalizeQuestionArray($question['options']);
                                                $correctAnswers = array_map('intval', $normalizeQuestionArray($question['correct_answers']));
                                            @endphp
                                            <div class="space-y-1">
                                                @foreach ($options as $optIndex => $option)
                                                    <div class="flex items-center gap-2 text-sm">
                                                        <span class="w-5 h-5 rounded-full border-2 flex items-center justify-center text-xs
                                                            {{ in_array($optIndex, $correctAnswers) ? 'border-green-500 bg-green-500 text-white' : 'border-themed-primary' }}">
                                                            {{ chr(65 + $optIndex) }}
                                                        </span>
                                                        <span class="text-themed-secondary {{ in_array($optIndex, $correctAnswers) ? 'font-medium text-green-700 dark:text-green-300' : '' }} transition-colors duration-300">
                                                            {{ $option }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @elseif ($question['question_type'] === 'true_false')
                                            @php
                                                $options = $normalizeQuestionArray($question['options'], ['True', 'False']);
                                                $correctAnswers = array_map('intval', $normalizeQuestionArray($question['correct_answers'], [0]));
                                            @endphp
                                            <div class="flex gap-4 text-sm">
                                                <span class="flex items-center gap-1">
                                                    <span class="w-4 h-4 rounded-full {{ $correctAnswers[0] === 0 ? 'bg-green-500' : 'bg-themed-tertiary' }}"></span>
                                                    <span class="text-themed-secondary transition-colors duration-300">{{ $options[0] }}</span>
                                                </span>
                                                <span class="flex items-center gap-1">
                                                    <span class="w-4 h-4 rounded-full {{ $correctAnswers[0] === 1 ? 'bg-green-500' : 'bg-themed-tertiary' }}"></span>
                                                    <span class="text-themed-secondary transition-colors duration-300">{{ $options[1] }}</span>
                                                </span>
                                            </div>
                                        @elseif (in_array($question['question_type'], ['short_answer', 'fill_blank']))
                                            @php
                                                $correctAnswers = $normalizeQuestionArray($question['correct_answers']);
                                            @endphp
                                            @if (!empty($correctAnswers[0]))
                                                <div class="text-sm text-green-700 dark:text-green-300 bg-green-50 dark:bg-green-900/20 px-2 py-1 rounded border border-green-200 dark:border-green-700 transition-colors duration-300">
                                                    <strong>Expected:</strong> {{ $correctAnswers[0] }}
                                                </div>
                                            @endif
                                        @endif

                                        @if ($question['explanation'])
                                            <div class="mt-2 text-sm text-themed-secondary bg-themed-secondary px-3 py-2 rounded border border-themed-primary transition-colors duration-300">
                                                <strong>Explanation:</strong> {{ $question['explanation'] }}
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex gap-2 ml-4">
                                        <button wire:click="editQuestion({{ $question['id'] }})"
                                                class="px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm transition-colors duration-300">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        
                                        <button wire:click="duplicateQuestion({{ $question['id'] }})"
                                                class="px-2 py-1 bg-green-600 hover:bg-green-700 text-white rounded text-sm transition-colors duration-300"
                                                title="Duplicate Question">
                                            <i class="fas fa-copy"></i>
                                        </button>

                                        <button wire:click="deleteQuestion({{ $question['id'] }})"
                                                onclick="return confirm('Are you sure you want to delete this question?')"
                                                class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white rounded text-sm transition-colors duration-300">
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

                <div class="bg-themed-tertiary rounded-lg p-3 text-center border border-themed-primary transition-colors duration-300">
                    <div class="text-xl font-bold text-blue-600 dark:text-blue-400">{{ $typeStats['multiple_choice'] ?? 0 }}</div>
                    <div class="text-xs text-themed-secondary transition-colors duration-300">Multiple Choice</div>
                </div>

                <div class="bg-themed-tertiary rounded-lg p-3 text-center border border-themed-primary transition-colors duration-300">
                    <div class="text-xl font-bold text-green-600 dark:text-green-400">{{ $typeStats['true_false'] ?? 0 }}</div>
                    <div class="text-xs text-themed-secondary transition-colors duration-300">True/False</div>
                </div>

                <div class="bg-themed-tertiary rounded-lg p-3 text-center border border-themed-primary transition-colors duration-300">
                    <div class="text-xl font-bold text-yellow-600 dark:text-yellow-400">{{ ($typeStats['short_answer'] ?? 0) + ($typeStats['essay'] ?? 0) }}</div>
                    <div class="text-xs text-themed-secondary transition-colors duration-300">Written</div>
                </div>

                <div class="bg-themed-tertiary rounded-lg p-3 text-center border border-themed-primary transition-colors duration-300">
                    <div class="text-xl font-bold text-purple-600 dark:text-purple-400">{{ $totalPoints }}</div>
                    <div class="text-xs text-themed-secondary transition-colors duration-300">Total Points</div>
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-themed-tertiary rounded-full flex items-center justify-center mx-auto mb-4 border border-themed-primary transition-colors duration-300">
                    <i class="fas fa-question text-2xl text-themed-secondary transition-colors duration-300"></i>
                </div>
                <h4 class="text-lg font-medium text-themed-primary mb-2 transition-colors duration-300">No Questions Yet</h4>
                <p class="text-themed-secondary mb-4 transition-colors duration-300">Create questions to build your quiz assessment.</p>
                <button wire:click="toggleCreateForm"
                        class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors duration-300">
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
