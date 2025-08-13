<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">
        <i class="fas fa-cog mr-2"></i> CBT Management
    </h1>

    <!-- Create Exam Button -->
    <div class="mb-6">
        <button wire:click="toggleCreateExamModal" 
                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200">
            <i class="fas fa-plus-circle mr-2"></i> Create New Exam
        </button>
    </div>

    <!-- Exams List -->
    <div class="bg-white rounded-xl shadow-lg p-6 animate__animated animate__fadeIn">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Exams</h2>
        @forelse($exams as $exam)
            <div class="border-b border-gray-200 pb-4 mb-4">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-600">{{ $exam['title'] }}</p>
                        <p class="text-xs text-gray-400">Duration: {{ $exam['duration_minutes'] }} minutes | Total Marks: {{ $exam['total_marks'] }}</p>
                    </div>
                    <button wire:click="loadQuestions({{ $exam['id'] }})"
                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200">
                        <i class="fas fa-eye mr-2"></i> View Questions
                    </button>
                </div>
            </div>
        @empty
            <p class="text-gray-500">No exams available.</p>
        @endforelse
    </div>

    <!-- Questions List -->
    @if($selectedExamId)
        <div class="bg-white rounded-xl shadow-lg p-6 mt-6 animate__animated animate__fadeIn">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Questions for {{ CbtExam::find($selectedExamId)->title }}</h2>
            <button wire:click="toggleCreateQuestionModal" 
                    class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors duration-200 mb-4">
                <i class="fas fa-plus-circle mr-2"></i> Add Question
            </button>
            <div class="space-y-4">
                @forelse($questions as $question)
                    <div class="border-b border-gray-200 pb-4">
                        <p class="text-sm font-medium text-gray-600">{!! nl2br(e($question['question'])) !!}</p>
                        <ul class="list-disc pl-5 text-sm text-gray-600">
                            @foreach($question['options'] as $index => $option)
                                <li class="{{ $index == $question['correct_option_index'] ? 'text-green-600' : '' }}">
                                    {{ $option }} {{ $index == $question['correct_option_index'] ? '(Correct)' : '' }}
                                </li>
                            @endforeach
                        </ul>
                        <p class="text-xs text-gray-400">Marks: {{ $question['marks'] }}</p>
                    </div>
                @empty
                    <p class="text-gray-500">No questions available.</p>
                @endforelse
            </div>
        </div>
    @endif

    <!-- Create Exam Modal -->
    <div x-data="{ open: @entangle('showCreateExamModal') }" x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-xl p-6 w-full max-w-md">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Create New Exam</h3>
            <div class="space-y-4">
                <div>
                    <label class="text-sm text-gray-600">Title</label>
                    <input wire:model="examTitle" type="text" class="w-full border rounded-md p-2">
                    @error('examTitle') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="text-sm text-gray-600">Description</label>
                    <textarea wire:model="examDescription" class="w-full border rounded-md p-2"></textarea>
                </div>
                <div>
                    <label class="text-sm text-gray-600">Duration (minutes)</label>
                    <input wire:model="examDuration" type="number" class="w-full border rounded-md p-2">
                    @error('examDuration') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="flex space-x-4">
                    <button wire:click="createExam" 
                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200">
                        <i class="fas fa-save mr-2"></i> Create
                    </button>
                    <button @click="open = false" 
                            class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200">
                        <i class="fas fa-times mr-2"></i> Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Question Modal -->
    <div x-data="{ open: @entangle('showCreateQuestionModal') }" x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-xl p-6 w-full max-w-md">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Add New Question</h3>
            <div class="space-y-4">
                <div>
                    <label class="text-sm text-gray-600">Question</label>
                    <textarea wire:model="questionText" class="w-full border rounded-md p-2"></textarea>
                    @error('questionText') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                @for($i = 0; $i < 4; $i++)
                    <div>
                        <label class="text-sm text-gray-600">Option {{ $i + 1 }}</label>
                        <input wire:model="questionOptions.{{ $i }}" type="text" class="w-full border rounded-md p-2">
                        @error("questionOptions.$i") <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                    </div>
                @endfor
                <div>
                    <label class="text-sm text-gray-600">Correct Option (0-3)</label>
                    <input wire:model="correctOptionIndex" type="number" min="0" max="3" class="w-full border rounded-md p-2">
                    @error('correctOptionIndex') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="text-sm text-gray-600">Marks</label>
                    <input wire:model="questionMarks" type="number" min="1" class="w-full border rounded-md p-2">
                    @error('questionMarks') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="flex space-x-4">
                    <button wire:click="createQuestion" 
                            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200">
                        <i class="fas fa-save mr-2"></i> Add
                    </button>
                    <button @click="open = false" 
                            class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors duration-200">
                        <i class="fas fa-times mr-2"></i> Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>