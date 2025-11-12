<div class="py-4 px-4 bg-themed-primary dark:bg-gray-900 min-h-screen transition-colors duration-300">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-themed-primary flex items-center">
                <i class="fas fa-cog mr-2"></i>CBT Management
            </h1>
            <p class="text-themed-secondary">Create and manage CBT assessments and questions</p>
        </div>
        <button wire:click="$set('showCreateModal', true)"
            class="bg-accent-themed-primary hover:bg-accent-themed-secondary text-white px-4 py-2 rounded-lg flex items-center transition-colors">
            <i class="fas fa-plus mr-2"></i>Create CBT Assessment
        </button>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div class="bg-green-100 dark:bg-green-900/30 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-300 px-4 py-3 rounded mb-6 animate-pulse"
            role="alert">
            <div class="flex items-center">
                <i class="fas fa-check-circle mr-2"></i>
                <span>{{ session('message') }}</span>
                <button type="button"
                    class="ml-auto text-green-700 dark:text-green-300 hover:text-green-900 dark:hover:text-green-100"
                    onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 dark:bg-red-900/30 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-300 px-4 py-3 rounded mb-6"
            role="alert">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <span>{{ session('error') }}</span>
                <button type="button"
                    class="ml-auto text-red-700 dark:text-red-300 hover:text-red-900 dark:hover:text-red-100"
                    onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    <!-- Assessments List -->
    <div class="bg-themed-secondary rounded-lg shadow-lg border border-themed-primary">
        <div class="bg-themed-secondary py-4 px-6 border-b border-themed-secondary">
            <h6 class="text-lg font-semibold text-accent-themed-primary">CBT Assessments</h6>
        </div>
        <div class="p-6">
            @if($assessments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full table-auto">
                        <thead class="bg-themed-tertiary">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-themed-secondary uppercase tracking-wider">Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-themed-secondary uppercase tracking-wider">Course</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-themed-secondary uppercase tracking-wider">Questions</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-themed-secondary uppercase tracking-wider">Duration</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-themed-secondary uppercase tracking-wider">Pass %</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-themed-secondary uppercase tracking-wider">Max Score</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-themed-secondary uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-themed-secondary divide-y divide-themed-primary">
                            @foreach($assessments as $assessment)
                                <tr class="hover:bg-themed-tertiary transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-semibold text-themed-primary">{{ $assessment->title }}</div>
                                        <div class="text-sm text-themed-secondary">{{ Str::limit($assessment->description, 50) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-themed-primary">{{ $assessment->course->title ?? 'No Course' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-themed-tertiary text-accent-themed-primary">
                                            {{ $assessment->questions->count() }} Questions
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-themed-primary">{{ $assessment->formatted_duration }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-themed-primary">{{ $assessment->pass_percentage }}%</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-themed-primary">{{ $assessment->max_score }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex space-x-2">
                                            <button wire:click="manageQuestions({{ $assessment->id }})"
                                                class="text-accent-themed-primary hover:text-accent-themed-secondary p-2 rounded-lg hover:bg-themed-tertiary transition-colors"
                                                title="Manage Questions">
                                                <i class="fas fa-question-circle"></i>
                                            </button>
                                            <button wire:click="editAssessment({{ $assessment->id }})"
                                                class="text-themed-secondary hover:text-themed-primary p-2 rounded-lg hover:bg-themed-tertiary transition-colors"
                                                title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button wire:click="deleteAssessment({{ $assessment->id }})"
                                                wire:confirm="Are you sure you want to delete this assessment?"
                                                class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors"
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
                    <i class="fas fa-clipboard-list text-6xl text-themed-tertiary mb-4"></i>
                    <h5 class="text-xl font-semibold text-themed-secondary mb-2">No CBT Assessments Yet</h5>
                    <p class="text-themed-tertiary">Create your first CBT assessment to get started.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Create Assessment Modal -->
    <div class="@if($showCreateModal) fixed inset-0 bg-gray-600 bg-opacity-50 dark:bg-gray-900 dark:bg-opacity-75 overflow-y-auto h-full w-full z-50 @else hidden @endif">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-lg bg-themed-secondary border-themed-primary @if($showCreateModal) animate-fade-in-down @endif">
            <div class="border-b border-themed-secondary pb-4 mb-4">
                <h5 class="text-xl font-semibold text-themed-primary flex items-center">
                    <i class="fas fa-plus mr-2"></i>Create CBT Assessment
                </h5>
                <button type="button" class="absolute top-4 right-4 text-themed-tertiary hover:text-themed-secondary" wire:click="closeModals">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div>
                <form wire:submit="createAssessment">
                    <div class="mb-4">
                        <label for="course_id" class="block text-sm font-medium text-themed-primary mb-2">Course (Optional)</label>
                        <select wire:model="course_id" class="w-full px-3 py-2 border border-themed-secondary rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-themed-primary focus:border-accent-themed-primary bg-themed-primary text-themed-primary placeholder-themed-tertiary">
                            <option value="">Select a course (optional)</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->title }}</option>
                            @endforeach
                        </select>
                        @error('course_id') <div class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="title" class="block text-sm font-medium text-themed-primary mb-2">Assessment Title</label>
                        <input type="text" wire:model="title" class="w-full px-3 py-2 border border-themed-secondary rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-themed-primary focus:border-accent-themed-primary bg-themed-primary text-themed-primary placeholder-themed-tertiary" required>
                        @error('title') <div class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-themed-primary mb-2">Description</label>
                        <textarea wire:model="description" class="w-full px-3 py-2 border border-themed-secondary rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-themed-primary focus:border-accent-themed-primary bg-themed-primary text-themed-primary placeholder-themed-tertiary" rows="3"></textarea>
                        @error('description') <div class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div>
                            <label for="pass_percentage" class="block text-sm font-medium text-themed-primary mb-2">Pass Percentage</label>
                            <input type="number" wire:model="pass_percentage" class="w-full px-3 py-2 border border-themed-secondary rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-themed-primary focus:border-accent-themed-primary bg-themed-primary text-themed-primary placeholder-themed-tertiary" min="1" max="100" required>
                            @error('pass_percentage') <div class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label for="estimated_duration_minutes" class="block text-sm font-medium text-themed-primary mb-2">Duration (Minutes)</label>
                            <input type="number" wire:model="estimated_duration_minutes" class="w-full px-3 py-2 border border-themed-secondary rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-themed-primary focus:border-accent-themed-primary bg-themed-primary text-themed-primary placeholder-themed-tertiary" min="1" required>
                            @error('estimated_duration_minutes') <div class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label for="max_score" class="block text-sm font-medium text-themed-primary mb-2">Max Score</label>
                            <input type="number" wire:model="max_score" class="w-full px-3 py-2 border border-themed-secondary rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-themed-primary focus:border-accent-themed-primary bg-themed-primary text-themed-primary placeholder-themed-tertiary" min="1" required>
                            @error('max_score') <div class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t border-themed-secondary">
                        <button type="button" class="px-4 py-2 bg-themed-tertiary text-themed-primary rounded-lg hover:bg-themed-secondary transition-colors border border-themed-secondary" wire:click="closeModals">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-accent-themed-primary text-white rounded-lg hover:bg-accent-themed-secondary transition-colors flex items-center">
                            <i class="fas fa-save mr-2"></i>Create Assessment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Assessment Modal -->
    <div class="@if($showEditModal) fixed inset-0 bg-gray-600 bg-opacity-50 dark:bg-gray-900 dark:bg-opacity-75 overflow-y-auto h-full w-full z-50 @else hidden @endif">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-lg bg-themed-secondary border-themed-primary @if($showEditModal) animate-fade-in-down @endif">
            <div class="border-b border-themed-secondary pb-4 mb-4">
                <h5 class="text-xl font-semibold text-themed-primary flex items-center">
                    <i class="fas fa-edit mr-2"></i>Edit CBT Assessment
                </h5>
                <button type="button" class="absolute top-4 right-4 text-themed-tertiary hover:text-themed-secondary" wire:click="closeModals">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div>
                <form wire:submit="updateAssessment">
                    <div class="mb-4">
                        <label for="course_id" class="block text-sm font-medium text-themed-primary mb-2">Course (Optional)</label>
                        <select wire:model="course_id" class="w-full px-3 py-2 border border-themed-secondary rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-themed-primary focus:border-accent-themed-primary bg-themed-primary text-themed-primary placeholder-themed-tertiary">
                            <option value="">Select a course (optional)</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->title }}</option>
                            @endforeach
                        </select>
                        @error('course_id') <div class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="title" class="block text-sm font-medium text-themed-primary mb-2">Assessment Title</label>
                        <input type="text" wire:model="title" class="w-full px-3 py-2 border border-themed-secondary rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-themed-primary focus:border-accent-themed-primary bg-themed-primary text-themed-primary placeholder-themed-tertiary" required>
                        @error('title') <div class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-themed-primary mb-2">Description</label>
                        <textarea wire:model="description" class="w-full px-3 py-2 border border-themed-secondary rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-themed-primary focus:border-accent-themed-primary bg-themed-primary text-themed-primary placeholder-themed-tertiary" rows="3"></textarea>
                        @error('description') <div class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div>
                            <label for="pass_percentage" class="block text-sm font-medium text-themed-primary mb-2">Pass Percentage</label>
                            <input type="number" wire:model="pass_percentage" class="w-full px-3 py-2 border border-themed-secondary rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-themed-primary focus:border-accent-themed-primary bg-themed-primary text-themed-primary placeholder-themed-tertiary" min="1" max="100" required>
                            @error('pass_percentage') <div class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label for="estimated_duration_minutes" class="block text-sm font-medium text-themed-primary mb-2">Duration (Minutes)</label>
                            <input type="number" wire:model="estimated_duration_minutes" class="w-full px-3 py-2 border border-themed-secondary rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-themed-primary focus:border-accent-themed-primary bg-themed-primary text-themed-primary placeholder-themed-tertiary" min="1" required>
                            @error('estimated_duration_minutes') <div class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div>
                            <label for="max_score" class="block text-sm font-medium text-themed-primary mb-2">Max Score</label>
                            <input type="number" wire:model="max_score" class="w-full px-3 py-2 border border-themed-secondary rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-themed-primary focus:border-accent-themed-primary bg-themed-primary text-themed-primary placeholder-themed-tertiary" min="1" required>
                            @error('max_score') <div class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t border-themed-secondary">
                        <button type="button" class="px-4 py-2 bg-themed-tertiary text-themed-primary rounded-lg hover:bg-themed-secondary transition-colors border border-themed-secondary" wire:click="closeModals">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-accent-themed-primary text-white rounded-lg hover:bg-accent-themed-secondary transition-colors flex items-center">
                            <i class="fas fa-save mr-2"></i>Update Assessment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Questions Management Modal -->
    @if($selectedAssessment)
        <div class="@if($showQuestionModal) fixed inset-0 bg-gray-600 bg-opacity-50 dark:bg-gray-900 dark:bg-opacity-75 overflow-y-auto h-full w-full z-50 @else hidden @endif">
            <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-7xl shadow-lg rounded-lg bg-themed-secondary border-themed-primary @if($showQuestionModal) animate-fade-in-up @endif">
                <div class="border-b border-themed-secondary pb-4 mb-4">
                    <h5 class="text-xl font-semibold text-themed-primary flex items-center">
                        <i class="fas fa-question-circle mr-2"></i>
                        Manage Questions - {{ $selectedAssessment->title }}
                    </h5>
                    <button type="button" class="absolute top-4 right-4 text-themed-tertiary hover:text-themed-secondary" wire:click="closeModals">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Add Question Form -->
                        <div>
                            <h6 class="text-lg font-semibold text-themed-primary mb-4">Add New Question</h6>
                            <form wire:submit="addQuestion">
                                <div class="mb-4">
                                    <label for="question_text" class="block text-sm font-medium text-themed-primary mb-2">
                                        Question Text
                                        <span class="text-xs text-themed-tertiary">(Use $...$ for inline math and $$...$$ for display math)</span>
                                    </label>
                                    <textarea wire:model="question_text" class="w-full px-3 py-2 border border-themed-secondary rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-themed-primary focus:border-accent-themed-primary bg-themed-primary text-themed-primary placeholder-themed-tertiary" rows="3" placeholder="E.g., Solve the equation: $x^2 + y^2 = 25$" required></textarea>
                                    @error('question_text') <div class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</div> @enderror

                                    <!-- Live Preview -->
                                    <div class="mt-2 p-3 bg-themed-tertiary rounded-lg">
                                        <label class="text-xs text-themed-secondary mb-1 block">Preview:</label>
                                        <div id="question-preview" class="text-themed-primary min-h-6 math-content">
                                            @if($question_text)
                                                {!! $question_text !!}
                                            @else
                                                <span class="text-themed-tertiary">Preview will appear here</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                    <div class="md:col-span-2">
                                        <label for="question_type" class="block text-sm font-medium text-themed-primary mb-2">Question Type</label>
                                        <select wire:model.live="question_type" class="w-full px-3 py-2 border border-themed-secondary rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-themed-primary focus:border-accent-themed-primary bg-themed-primary text-themed-primary placeholder-themed-tertiary" required>
                                            <option value="multiple_choice">Multiple Choice</option>
                                            <option value="true_false">True/False</option>
                                            <option value="short_answer">Short Answer</option>
                                            <option value="essay">Essay</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="points" class="block text-sm font-medium text-themed-primary mb-2">Points</label>
                                        <input type="number" wire:model="points" class="w-full px-3 py-2 border border-themed-secondary rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-themed-primary focus:border-accent-themed-primary bg-themed-primary text-themed-primary placeholder-themed-tertiary" step="0.1" min="0.1" required>
                                        @error('points') <div class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                @if($question_type === 'multiple_choice')
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-themed-primary mb-2">
                                            Options
                                            <span class="text-xs text-themed-tertiary">(Use $...$ for inline math and $$...$$ for display math)</span>
                                        </label>
                                        @foreach($options as $index => $option)
                                            <div class="mb-3">
                                                <div class="flex items-center mb-1">
                                                    <span class="bg-themed-tertiary text-themed-primary px-3 py-2 rounded-l-lg border border-r-0 border-themed-secondary text-sm font-medium">
                                                        {{ chr(65 + $index) }}
                                                    </span>
                                                    <input 
                                                        type="text" 
                                                        wire:model="options.{{ $index }}" 
                                                        class="flex-1 px-3 py-2 border border-themed-secondary focus:outline-none focus:ring-2 focus:ring-accent-themed-primary focus:border-accent-themed-primary bg-themed-primary text-themed-primary placeholder-themed-tertiary" 
                                                        placeholder="Option {{ chr(65 + $index) }} (e.g., $x^2 + 5x + 6$)">
                                                    <div class="bg-themed-tertiary border border-l-0 border-r-0 border-themed-secondary px-3 py-2">
                                                        <input 
                                                            type="checkbox" 
                                                            wire:model="correct_answers" 
                                                            value="{{ $index }}" 
                                                            class="form-checkbox h-4 w-4 text-accent-themed-primary" 
                                                            title="Correct Answer">
                                                    </div>
                                                    <button 
                                                        type="button"
                                                        onclick="toggleOptionPreview({{ $index }})"
                                                        class="bg-themed-tertiary border border-l-0 border-themed-secondary rounded-r-lg px-3 py-2 hover:bg-themed-secondary transition-colors"
                                                        title="Toggle Preview">
                                                        <i class="fas fa-eye text-themed-secondary"></i>
                                                    </button>
                                                </div>
                                                
                                                <!-- Preview Container (Hidden by default) -->
                                                <div id="option-preview-container-{{ $index }}" class="hidden mt-1 ml-12 option-preview-container">
                                                    <div class="p-2 bg-themed-tertiary rounded-lg border border-themed-secondary">
                                                        <label class="text-xs text-themed-secondary mb-1 block">Preview:</label>
                                                        <div id="option-preview-{{ $index }}" class="text-themed-primary min-h-6 math-content option-preview-content">
                                                            <span class="text-themed-tertiary">Preview will appear here</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        @error('options') <div class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</div> @enderror
                                        @error('correct_answers') <div class="text-red-500 dark:text-red-400 text-sm mt-1">{{ $message }}</div> @enderror
                                    </div>
                                @elseif($question_type === 'true_false')
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-themed-primary mb-2">Correct Answer</label>
                                        <div class="space-y-2">
                                            <div class="flex items-center">
                                                <input type="radio" wire:model="correct_answers" value="0" class="form-radio h-4 w-4 text-accent-themed-primary" id="true_option">
                                                <label class="ml-2 text-sm text-themed-primary" for="true_option">True</label>
                                            </div>
                                            <div class="flex items-center">
                                                <input type="radio" wire:model="correct_answers" value="1" class="form-radio h-4 w-4 text-accent-themed-primary" id="false_option">
                                                <label class="ml-2 text-sm text-themed-primary" for="false_option">False</label>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="mb-4">
                                    <label for="explanation" class="block text-sm font-medium text-themed-primary mb-2">
                                        Explanation
                                        <span class="text-xs text-themed-tertiary">(Use $...$ for inline math and $...$ for display math)</span>
                                    </label>
                                    <textarea wire:model="explanation" class="w-full px-3 py-2 border border-themed-secondary rounded-lg focus:outline-none focus:ring-2 focus:ring-accent-themed-primary focus:border-accent-themed-primary bg-themed-primary text-themed-primary placeholder-themed-tertiary" rows="2" placeholder="E.g., Using Pythagorean theorem: $a^2 + b^2 = c^2$"></textarea>

                                    <!-- Live Preview -->
                                    <div class="mt-2 p-3 bg-themed-tertiary rounded-lg">
                                        <label class="text-xs text-themed-secondary mb-1 block">Preview:</label>
                                        <div id="explanation-preview" class="text-themed-primary min-h-6 math-content">
                                            @if($explanation)
                                                {!! $explanation !!}
                                            @else
                                                <span class="text-themed-tertiary">Preview will appear here</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center">
                                    <i class="fas fa-plus mr-2"></i>Add Question
                                </button>
                            </form>
                        </div>

                        <!-- Questions List -->
                        <div>
                            <h6 class="text-lg font-semibold text-themed-primary mb-4">Questions ({{ $selectedAssessment->questions->count() }})</h6>
                            @if($selectedAssessment->questions->count() > 0)
                                <div class="space-y-3 max-h-96 overflow-y-auto">
                                    @foreach($selectedAssessment->questions as $question)
                                        <div class="bg-themed-tertiary border border-themed-secondary rounded-lg p-4 hover:shadow-md transition-shadow">
                                            <div class="flex justify-between items-start">
                                                <div class="flex-1">
                                                    <h6 class="font-semibold text-themed-primary mb-1">
                                                        Q{{ $loop->iteration }}.
                                                        <span class="math-content">{!! $question->question_text !!}</span>
                                                    </h6>
                                                    @if($question->options && count($question->options) > 0)
                                                        <div class="mt-2 ml-4 space-y-1">
                                                            @foreach($question->options as $index => $option)
                                                                <div class="text-sm text-themed-secondary flex items-start">
                                                                    <span class="font-medium mr-2">{{ chr(65 + $index) }}.</span>
                                                                    <span class="math-content flex-1">{!! $option !!}</span>
                                                                    @if(in_array($index, $question->correct_answers ?? []))
                                                                        <i class="fas fa-check text-green-600 ml-2"></i>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                    @if($question->explanation)
                                                        <div class="mt-2 text-sm text-themed-secondary">
                                                            <strong>Explanation:</strong>
                                                            <span class="math-content">{!! $question->explanation !!}</span>
                                                        </div>
                                                    @endif
                                                    <div class="flex space-x-2 mt-2">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-themed-secondary text-accent-themed-primary">
                                                            {{ ucfirst(str_replace('_', ' ', $question->question_type)) }}
                                                        </span>
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-themed-tertiary text-accent-themed-primary">
                                                            {{ $question->points }} pts
                                                        </span>
                                                    </div>
                                                </div>
                                                <button wire:click="deleteQuestion({{ $question->id }})" wire:confirm="Are you sure you want to delete this question?" class="ml-3 text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300 p-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8 border-2 border-dashed border-themed-secondary rounded-lg">
                                    <i class="fas fa-question text-4xl text-themed-tertiary mb-3"></i>
                                    <p class="text-themed-secondary">No questions added yet</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <style>
        /* Animation keyframes */
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in-down { animation: fadeInDown 0.3s ease-out; }
        .animate-fade-in-up { animation: fadeInUp 0.3s ease-out; }

        /* Form elements */
        .form-checkbox:checked { background-color: rgb(var(--accent-primary)); border-color: rgb(var(--accent-primary)); }
        .form-radio:checked { background-color: rgb(var(--accent-primary)); border-color: rgb(var(--accent-primary)); }

        .dark .form-checkbox { background-color: #374151; border-color: #6b7280; }
        .dark .form-radio { background-color: #374151; border-color: #6b7280; }
        .dark .form-checkbox:checked { background-color: rgb(var(--accent-primary)); border-color: rgb(var(--accent-primary)); }
        .dark .form-radio:checked { background-color: rgb(var(--accent-primary)); border-color: rgb(var(--accent-primary)); }

        /* Smooth transitions */
        * { transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease; }

        /* MathJax styling */
        .math-content mjx-container { display: inline-block !important; }
        
        /* Preview styling */
        #question-preview, #explanation-preview {
            min-height: 24px;
            border-left: 3px solid rgb(var(--accent-primary));
            padding-left: 8px;
        }

        /* Option preview containers */
        .option-preview-container {
            transition: all 0.3s ease-in-out;
            max-height: 0;
            overflow: hidden;
            opacity: 0;
        }

        .option-preview-container:not(.hidden) {
            max-height: 200px;
            opacity: 1;
        }

        /* Option preview content */
        .option-preview-content {
            min-height: 24px;
            border-left: 3px solid rgb(var(--accent-primary));
            padding-left: 8px;
        }

        /* Eye icon animation */
        button[onclick^="toggleOptionPreview"] i {
            transition: transform 0.2s ease;
        }

        button[onclick^="toggleOptionPreview"]:hover i {
            transform: scale(1.1);
        }
    </style>

    <script>
    // Wait for MathJax to be ready
    function waitForMathJax() {
        return new Promise((resolve) => {
            if (typeof MathJax !== 'undefined' && typeof MathJax.typesetPromise !== 'undefined') {
                resolve();
            } else {
                document.addEventListener('mathjax-loaded', resolve);
            }
        });
    }

    // Initialize MathJax integration
    async function initMathJax() {
        await waitForMathJax();
        console.log('MathJax initialized successfully');
        
        // Process all math content on page load
        MathJax.typesetPromise().catch(err => console.error('MathJax error:', err));
        
        // Set up Livewire integration
        setupLivewireIntegration();
        
        // Set up live preview handlers
        setupLivePreviews();
    }

    // Handle Livewire DOM updates
    function setupLivewireIntegration() {
        // Listen for Livewire updates
        document.addEventListener('livewire:init', () => {
            Livewire.hook('morph.updated', ({ el, component }) => {
                // Typeset any math content that was updated
                const mathElements = el.querySelectorAll('.math-content');
                if (mathElements.length > 0) {
                    MathJax.typesetPromise(Array.from(mathElements)).catch(err => 
                        console.error('MathJax typeset error:', err)
                    );
                }
            });

            // Also handle after any Livewire request completes
            Livewire.hook('request', ({ respond }) => {
                respond(() => {
                    setTimeout(() => {
                        MathJax.typesetPromise().catch(err => 
                            console.error('MathJax typeset error:', err)
                        );
                    }, 100);
                });
            });
        });
    }

    // Set up live preview for question and explanation inputs
    function setupLivePreviews() {
        let questionTimeout, explanationTimeout;

        // Update preview function
        function updatePreview(inputValue, previewId) {
            const preview = document.getElementById(previewId);
            if (!preview) return;

            // Ensure inputValue is a string
            const value = String(inputValue || '');

            if (value.trim()) {
                preview.innerHTML = value;
                // Clear any existing MathJax output first
                preview.querySelectorAll('mjx-container').forEach(el => el.remove());
                // Typeset the new content
                MathJax.typesetPromise([preview]).catch(err => 
                    console.error('MathJax preview error:', err)
                );
            } else {
                preview.innerHTML = '<span class="text-themed-tertiary">Preview will appear here</span>';
            }
        }

        // Toggle option preview visibility
        window.toggleOptionPreview = function(index) {
            const previewContainer = document.getElementById('option-preview-container-' + index);
            if (previewContainer) {
                previewContainer.classList.toggle('hidden');
                // If showing, update the preview immediately
                if (!previewContainer.classList.contains('hidden')) {
                    const input = document.querySelector(`input[wire\\:model="options.${index}"]`);
                    if (input) {
                        updatePreview(input.value, 'option-preview-' + index);
                    }
                }
            }
        };

        // Listen for Livewire updates on the question_text field
        Livewire.on('question-text-updated', (value) => {
            clearTimeout(questionTimeout);
            questionTimeout = setTimeout(() => {
                updatePreview(value, 'question-preview');
            }, 300);
        });

        // Listen for Livewire updates on the explanation field
        Livewire.on('explanation-updated', (value) => {
            clearTimeout(explanationTimeout);
            explanationTimeout = setTimeout(() => {
                updatePreview(value, 'explanation-preview');
            }, 300);
        });

        // Also handle direct input events as fallback
        document.addEventListener('input', (e) => {
            const target = e.target;
            
            // Check if this is the question text textarea
            if (target.hasAttribute('wire:model') && 
                (target.getAttribute('wire:model') === 'question_text' || 
                 target.getAttribute('wire:model').includes('question_text'))) {
                clearTimeout(questionTimeout);
                questionTimeout = setTimeout(() => {
                    updatePreview(target.value, 'question-preview');
                }, 300);
            }
            
            // Check if this is the explanation textarea
            if (target.hasAttribute('wire:model') && 
                (target.getAttribute('wire:model') === 'explanation' || 
                 target.getAttribute('wire:model').includes('explanation'))) {
                clearTimeout(explanationTimeout);
                explanationTimeout = setTimeout(() => {
                    updatePreview(target.value, 'explanation-preview');
                }, 300);
            }

            // Check if this is an option input
            if (target.hasAttribute('wire:model') && 
                target.getAttribute('wire:model').includes('options.')) {
                const match = target.getAttribute('wire:model').match(/options\.(\d+)/);
                if (match) {
                    const optionIndex = match[1];
                    clearTimeout(window['optionTimeout' + optionIndex]);
                    window['optionTimeout' + optionIndex] = setTimeout(() => {
                        updatePreview(target.value, 'option-preview-' + optionIndex);
                    }, 300);
                }
            }
        });
    }

    // Start initialization when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMathJax);
    } else {
        initMathJax();
    }
    </script>
</div>