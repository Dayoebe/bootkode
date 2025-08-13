<div class="mt-6 bg-gray-800 rounded-xl border border-gray-700 shadow-xl">
    @if ($quiz)
        <div class="p-6 border-b border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-white">{{ $quiz->title }}</h3>
                    @if($quiz->description)
                        <p class="text-gray-400 mt-1">{{ $quiz->description }}</p>
                    @endif
                    <p class="text-sm text-gray-500 mt-1">Pass percentage: {{ $quiz->pass_percentage }}%</p>
                </div>
                <div class="flex items-center space-x-2">
                    <button wire:click="previewQuiz"
                        class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-eye mr-1"></i> Preview
                    </button>
                    <button wire:click="exportQuiz"
                        class="px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-download mr-1"></i> Export
                    </button>
                </div>
            </div>
        </div>

        <div class="p-6">
            <!-- Add Question Form -->
            <div class="bg-gray-700 rounded-lg p-4 mb-6">
                <h4 class="text-lg font-semibold text-white mb-4">Add New Question</h4>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Question Text</label>
                        <textarea wire:model="newQuestionText"
                            class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 resize-none"
                            rows="3" placeholder="Enter your question..."></textarea>
                        @error('newQuestionText')
                            <span class="text-red-400 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300 mb-2">Question Type</label>
                        <select wire:model.live="newQuestionType"
                            class="px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white">
                            <option value="multiple_choice">Multiple Choice</option>
                            <option value="true_false">True/False</option>
                            <option value="short_answer">Short Answer</option>
                            <option value="essay">Essay</option>
                        </select>
                    </div>

                    @if ($newQuestionType === 'multiple_choice')
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Answer Options</label>
                            @for ($i = 0; $i < 4; $i++)
                                <div class="flex items-center space-x-2 mb-2">
                                    <input type="radio" name="correct_option" value="{{ $i }}"
                                        wire:click="$set('correctOptionIndex', {{ $i }})"
                                        {{ $correctOptionIndex === $i ? 'checked' : '' }}
                                        class="text-green-500 bg-gray-700 border-gray-600 focus:ring-green-500 focus:ring-offset-gray-800">
                                    <input type="text" wire:model="newQuestionOptions.{{ $i }}"
                                        placeholder="Option {{ $i + 1 }}..."
                                        class="flex-1 px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400">
                                </div>
                            @endfor
                            <p class="text-xs text-gray-500 mt-1">Select the radio button next to the correct answer</p>
                        </div>
                    @elseif($newQuestionType === 'true_false')
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Correct Answer</label>
                            <div class="flex space-x-4">
                                <label class="flex items-center">
                                    <input type="radio" name="true_false_answer" value="0"
                                        wire:click="$set('correctOptionIndex', 0)"
                                        {{ $correctOptionIndex === 0 ? 'checked' : '' }}
                                        class="text-green-500 bg-gray-700 border-gray-600 focus:ring-green-500 focus:ring-offset-gray-800">
                                    <span class="ml-2 text-white">True</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="true_false_answer" value="1"
                                        wire:click="$set('correctOptionIndex', 1)"
                                        {{ $correctOptionIndex === 1 ? 'checked' : '' }}
                                        class="text-green-500 bg-gray-700 border-gray-600 focus:ring-green-500 focus:ring-offset-gray-800">
                                    <span class="ml-2 text-white">False</span>
                                </label>
                            </div>
                        </div>
                    @elseif(in_array($newQuestionType, ['short_answer', 'essay']))
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Correct Answer</label>
                            <textarea wire:model="newQuestionCorrectAnswer"
                                class="w-full px-3 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white placeholder-gray-400 resize-none"
                                rows="{{ $newQuestionType === 'essay' ? '4' : '2' }}" placeholder="Enter the correct answer..."></textarea>
                            @error('newQuestionCorrectAnswer')
                                <span class="text-red-400 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif

                    <button wire:click="addQuestion"
                        class="px-4 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition-colors">
                        <i class="fas fa-plus mr-1"></i> Add Question
                    </button>
                </div>
            </div>

            <!-- Questions List -->
            @if ($questions && $questions->count() > 0)
                <div class="space-y-4">
                    <h4 class="text-lg font-semibold text-white">Questions ({{ $questions->count() }})</h4>
                    <div id="questionsList" class="space-y-4">
                        @foreach ($questions as $question)
                            <div class="bg-gray-700 rounded-lg p-4" data-question-id="{{ $question->id }}">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start space-x-3 flex-1">
                                        <i class="fas fa-grip-vertical text-gray-400 cursor-move mt-1"></i>
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2 mb-2">
                                                <span class="px-2 py-1 bg-pink-600 text-white text-xs rounded">
                                                    {{ ucfirst(str_replace('_', ' ', $question->type)) }}
                                                </span>
                                            </div>
                                            <p class="text-white font-medium mb-2">{{ $question->question_text }}</p>

                                            @if ($question->type === 'multiple_choice' && $question->options->count() > 0)
                                                <div class="space-y-1">
                                                    @foreach ($question->options as $option)
                                                        <div class="flex items-center space-x-2">
                                                            <i class="fas fa-{{ $option->is_correct ? 'check-circle text-green-400' : 'circle text-gray-500' }}"></i>
                                                            <span class="text-gray-300">{{ $option->option_text }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @elseif($question->type === 'true_false' && $question->options->count() > 0)
                                                <div class="space-y-1">
                                                    @foreach ($question->options as $option)
                                                        <div class="flex items-center space-x-2">
                                                            <i class="fas fa-{{ $option->is_correct ? 'check-circle text-green-400' : 'circle text-gray-500' }}"></i>
                                                            <span class="text-gray-300">{{ $option->option_text }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @elseif(in_array($question->type, ['short_answer', 'essay']))
                                                <div class="bg-gray-800 rounded p-3">
                                                    <p class="text-sm text-gray-400 mb-1">Correct Answer:</p>
                                                    <p class="text-gray-300">{{ $question->correct_answer }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2 ml-4">
                                        <button wire:click="duplicateQuestion({{ $question->id }})"
                                            class="text-blue-400 hover:text-blue-300">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                        <button wire:click="deleteQuestion({{ $question->id }})"
                                            class="text-red-400 hover:text-red-300">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="text-center text-gray-400 py-8">
                    <i class="fas fa-question-circle text-3xl mb-3"></i>
                    <p>No questions added yet.</p>
                    <p class="text-sm mt-2">Add your first question above.</p>
                </div>
            @endif
        </div>
    @else
        <!-- Quiz Creation Form -->
        <div class="p-6">
            <div class="text-center text-gray-400 py-8">
                <i class="fas fa-question-circle text-3xl mb-3"></i>
                <p>No quiz selected.</p>
                <p class="text-sm mt-2">Create a new quiz to get started.</p>
                <button wire:click="showQuizModal"
                    class="mt-4 px-4 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition-colors">
                    <i class="fas fa-plus mr-1"></i> Create Quiz
                </button>
            </div>
        </div>
    @endif

    <!-- Quiz Creation Modal -->
    @if ($showQuizModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
            wire:click.self="closeModals">
            <div class="bg-gray-800 rounded-xl border border-gray-700 max-w-md w-full">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-white">Create Quiz</h3>
                        <button wire:click="closeModals" class="text-gray-400 hover:text-white">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Quiz Title</label>
                            <input type="text" wire:model="newQuizTitle" placeholder="Quiz title..."
                                class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('newQuizTitle')
                                <span class="text-red-400 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Quiz Description (Optional)</label>
                            <textarea wire:model="newQuizDescription" placeholder="Quiz description..."
                                class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                rows="3"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Pass Percentage</label>
                            <input type="number" wire:model="newQuizPassPercentage" min="1" max="100"
                                class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('newQuizPassPercentage')
                                <span class="text-red-400 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="flex space-x-3">
                            <button wire:click="createQuiz({{ $lessonId ?? 'null' }})"
                                class="flex-1 px-4 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition-colors">
                                <i class="fas fa-plus mr-1"></i> Create Quiz
                            </button>
                            <button wire:click="closeModals"
                                class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    // Initialize sortable for questions
    document.addEventListener('livewire:navigated', () => {
        if (typeof Sortable !== 'undefined') {
            const questionsList = document.getElementById('questionsList');
            if (questionsList) {
                new Sortable(questionsList, {
                    handle: '.fa-grip-vertical',
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    onEnd: (evt) => {
                        const orderedIds = Array.from(questionsList.children).map(
                            el => el.getAttribute('data-question-id')
                        );
                        @this.call('reorderQuestions', orderedIds);
                    }
                });
            }
        }
    });
</script>