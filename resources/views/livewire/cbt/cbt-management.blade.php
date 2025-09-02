<div class="py-4 px-4">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-cog mr-2"></i>CBT Management
            </h1>
            <p class="text-gray-600">Create and manage CBT assessments and questions</p>
        </div>
        <button wire:click="$set('showCreateModal', true)"
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
            <i class="fas fa-plus mr-2"></i>Create CBT Assessment
        </button>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 animate-pulse" role="alert">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span>{{ session('message') }}</span>
                <button type="button" class="ml-auto text-green-700 hover:text-green-900"
                    onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6" role="alert">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span>{{ session('error') }}</span>
                <button type="button" class="ml-auto text-red-700 hover:text-red-900"
                    onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    <!-- Assessments List -->
    <div class="bg-white rounded-lg shadow-lg">
        <div class="bg-white py-4 px-6 border-b border-gray-200">
            <h6 class="text-lg font-semibold text-blue-600">CBT Assessments</h6>
        </div>
        <div class="p-6">
            @if($assessments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Course</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Questions</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Duration</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Pass %</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Max Score</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($assessments as $assessment)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-semibold text-gray-900">{{ $assessment->title }}</div>
                                        <div class="text-sm text-gray-500">{{ Str::limit($assessment->description, 50) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $assessment->course->title ?? 'No Course' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $assessment->questions->count() }} Questions
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $assessment->formatted_duration }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $assessment->pass_percentage }}%</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $assessment->max_score }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex space-x-2">
                                            <button wire:click="manageQuestions({{ $assessment->id }})"
                                                class="text-blue-600 hover:text-blue-900 p-2 rounded-lg hover:bg-blue-50 transition-colors"
                                                title="Manage Questions">
                                                <i class="fas fa-question-circle"></i>
                                            </button>
                                            <button wire:click="editAssessment({{ $assessment->id }})"
                                                class="text-gray-600 hover:text-gray-900 p-2 rounded-lg hover:bg-gray-50 transition-colors"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button wire:click="deleteAssessment({{ $assessment->id }})"
                                                wire:confirm="Are you sure you want to delete this assessment?"
                                                class="text-red-600 hover:text-red-900 p-2 rounded-lg hover:bg-red-50 transition-colors"
                                                title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-6">
                    {{ $assessments->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-clipboard-list text-6xl text-gray-400 mb-4"></i>
                    <h5 class="text-xl font-semibold text-gray-500 mb-2">No CBT Assessments Yet</h5>
                    <p class="text-gray-400">Create your first CBT assessment to get started.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Create Assessment Modal -->
    <div
        class="@if($showCreateModal) fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 @else hidden @endif">
        <div
            class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-lg bg-white @if($showCreateModal) animate-fade-in-down @endif">
            <div class="border-b border-gray-200 pb-4 mb-4">
                <h5 class="text-xl font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-plus mr-2"></i>Create CBT Assessment
                </h5>
                <button type="button" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600"
                    wire:click="closeModals">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div>
                <form wire:submit="createAssessment">
                    <div class="mb-4">
                        <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">Course
                            (Optional)</label>
                        <select wire:model="course_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select a course (optional)</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->title }}</option>
                            @endforeach
                        </select>
                        @error('course_id') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Assessment Title</label>
                        <input type="text" wire:model="title"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required>
                        @error('title') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="description"
                            class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea wire:model="description"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            rows="3"></textarea>
                        @error('description') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div>
                            <label for="pass_percentage" class="block text-sm font-medium text-gray-700 mb-2">Pass
                                Percentage</label>
                            <input type="number" wire:model="pass_percentage"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                min="1" max="100" required>
                            @error('pass_percentage') <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label for="estimated_duration_minutes"
                                class="block text-sm font-medium text-gray-700 mb-2">Duration (Minutes)</label>
                            <input type="number" wire:model="estimated_duration_minutes"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                min="1" required>
                            @error('estimated_duration_minutes') <div class="text-red-500 text-sm mt-1">{{ $message }}
                            </div> @enderror
                        </div>
                        <div>
                            <label for="max_score" class="block text-sm font-medium text-gray-700 mb-2">Max
                                Score</label>
                            <input type="number" wire:model="max_score"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                min="1" required>
                            @error('max_score') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                        <button type="button"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors"
                            wire:click="closeModals">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                            <i class="fas fa-save mr-2"></i>Create Assessment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Assessment Modal -->
    <div
        class="@if($showEditModal) fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 @else hidden @endif">
        <div
            class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-lg bg-white @if($showEditModal) animate-fade-in-down @endif">
            <div class="border-b border-gray-200 pb-4 mb-4">
                <h5 class="text-xl font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-edit mr-2"></i>Edit CBT Assessment
                </h5>
                <button type="button" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600"
                    wire:click="closeModals">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div>
                <form wire:submit="updateAssessment">
                    <div class="mb-4">
                        <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">Course
                            (Optional)</label>
                        <select wire:model="course_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select a course (optional)</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->title }}</option>
                            @endforeach
                        </select>
                        @error('course_id') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Assessment Title</label>
                        <input type="text" wire:model="title"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            required>
                        @error('title') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="description"
                            class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea wire:model="description"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            rows="3"></textarea>
                        @error('description') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div>
                            <label for="pass_percentage" class="block text-sm font-medium text-gray-700 mb-2">Pass
                                Percentage</label>
                            <input type="number" wire:model="pass_percentage"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                min="1" max="100" required>
                            @error('pass_percentage') <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label for="estimated_duration_minutes"
                                class="block text-sm font-medium text-gray-700 mb-2">Duration (Minutes)</label>
                            <input type="number" wire:model="estimated_duration_minutes"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                min="1" required>
                            @error('estimated_duration_minutes') <div class="text-red-500 text-sm mt-1">{{ $message }}
                            </div> @enderror
                        </div>
                        <div>
                            <label for="max_score" class="block text-sm font-medium text-gray-700 mb-2">Max
                                Score</label>
                            <input type="number" wire:model="max_score"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                min="1" required>
                            @error('max_score') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                        <button type="button"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors"
                            wire:click="closeModals">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                            <i class="fas fa-save mr-2"></i>Update Assessment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Questions Management Modal -->
    @if($selectedAssessment)
        <div
            class="@if($showQuestionModal) fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 @else hidden @endif">
            <div
                class="relative top-10 mx-auto p-5 border w-11/12 max-w-7xl shadow-lg rounded-lg bg-white @if($showQuestionModal) animate-fade-in-up @endif">
                <div class="border-b border-gray-200 pb-4 mb-4">
                    <h5 class="text-xl font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-question-circle mr-2"></i>
                        Manage Questions - {{ $selectedAssessment->title }}
                    </h5>
                    <button type="button" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600"
                        wire:click="closeModals">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Add Question Form -->
                        <div>
                            <h6 class="text-lg font-semibold mb-4">Add New Question</h6>
                            <form wire:submit="addQuestion">
                                <div class="mb-4">
                                    <label for="question_text" class="block text-sm font-medium text-gray-700 mb-2">Question
                                        Text</label>
                                    <textarea wire:model="question_text"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        rows="3" required></textarea>
                                    @error('question_text') <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                    <div class="md:col-span-2">
                                        <label for="question_type"
                                            class="block text-sm font-medium text-gray-700 mb-2">Question Type</label>
                                        <select wire:model.live="question_type"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            required>
                                            <option value="multiple_choice">Multiple Choice</option>
                                            <option value="true_false">True/False</option>
                                            <option value="short_answer">Short Answer</option>
                                            <option value="essay">Essay</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="points"
                                            class="block text-sm font-medium text-gray-700 mb-2">Points</label>
                                        <input type="number" wire:model="points"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                            step="0.1" min="0.1" required>
                                        @error('points') <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                @if($question_type === 'multiple_choice')
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Options</label>
                                        @foreach($options as $index => $option)
                                            <div class="flex items-center mb-2">
                                                <span
                                                    class="bg-gray-100 text-gray-700 px-3 py-2 rounded-l-lg border border-r-0 border-gray-300 text-sm font-medium">
                                                    {{ chr(65 + $index) }}
                                                </span>
                                                <input type="text" wire:model="options.{{ $index }}"
                                                    class="flex-1 px-3 py-2 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                    placeholder="Option {{ chr(65 + $index) }}">
                                                <div class="bg-gray-50 border border-l-0 border-gray-300 rounded-r-lg px-3 py-2">
                                                    <input type="checkbox" wire:model="correct_answers" value="{{ $index }}"
                                                        class="form-checkbox h-4 w-4 text-blue-600" title="Correct Answer">
                                                </div>
                                            </div>
                                        @endforeach
                                        @error('options') <div class="text-red-500 text-sm mt-1">{{ $message }}</div> @enderror
                                        @error('correct_answers') <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @elseif($question_type === 'true_false')
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Correct Answer</label>
                                        <div class="space-y-2">
                                            <div class="flex items-center">
                                                <input type="radio" wire:model="correct_answers" value="0"
                                                    class="form-radio h-4 w-4 text-blue-600" id="true_option">
                                                <label class="ml-2 text-sm text-gray-700" for="true_option">True</label>
                                            </div>
                                            <div class="flex items-center">
                                                <input type="radio" wire:model="correct_answers" value="1"
                                                    class="form-radio h-4 w-4 text-blue-600" id="false_option">
                                                <label class="ml-2 text-sm text-gray-700" for="false_option">False</label>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="mb-4">
                                    <label for="explanation"
                                        class="block text-sm font-medium text-gray-700 mb-2">Explanation (Optional)</label>
                                    <textarea wire:model="explanation"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        rows="2" placeholder="Provide explanation for the correct answer"></textarea>
                                </div>

                                <button type="submit"
                                    class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center">
                                    <i class="fas fa-plus mr-2"></i>Add Question
                                </button>
                            </form>
                        </div>

                        <!-- Questions List -->
                        <div>
                            <h6 class="text-lg font-semibold mb-4">Questions ({{ $selectedAssessment->questions->count() }})
                            </h6>
                            @if($selectedAssessment->questions->count() > 0)
                                <div class="space-y-3 max-h-96 overflow-y-auto">
                                    @foreach($selectedAssessment->questions as $question)
                                        <div
                                            class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                            <div class="flex justify-between items-start">
                                                <div class="flex-1">
                                                    <h6 class="font-semibold text-gray-900 mb-1">Q{{ $loop->iteration }}.
                                                        {{ Str::limit($question->question_text, 60) }}</h6>
                                                    <div class="flex space-x-2">
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                            {{ ucfirst($question->question_type) }}
                                                        </span>
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                            {{ $question->points }} pts
                                                        </span>
                                                    </div>
                                                </div>
                                                <button wire:click="deleteQuestion({{ $question->id }})"
                                                    wire:confirm="Are you sure you want to delete this question?"
                                                    class="ml-3 text-red-600 hover:text-red-900 p-2 rounded-lg hover:bg-red-50 transition-colors">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8 border-2 border-dashed border-gray-300 rounded-lg">
                                    <i class="fas fa-question text-4xl text-gray-400 mb-3"></i>
                                    <p class="text-gray-500">No questions added yet</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <style>
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-down {
            animation: fadeInDown 0.3s ease-out;
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.3s ease-out;
        }

        /* Custom form styles for consistency */
        .form-checkbox:checked {
            background-color: #3b82f6;
            border-color: #3b82f6;
        }

        .form-radio:checked {
            background-color: #3b82f6;
            border-color: #3b82f6;
        }
    </style>
</div>