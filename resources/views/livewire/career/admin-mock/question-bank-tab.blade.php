{{-- resources/views/livewire/career/admin-mock/question-bank-tab.blade.php --}}

<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex items-center justify-between">
        <h2 class="text-xl font-semibold" style="color: rgb(var(--text-primary))">Question Bank</h2>
        <button wire:click="$set('showCreateQuestionModal', true)"
            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
            <i class="fas fa-plus mr-2"></i> Add Question
        </button>
    </div>

    <!-- Filters -->
    <div class="shadow rounded-lg p-6" style="background-color: rgb(var(--bg-secondary))">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input wire:model.live.debounce.300ms="questionSearch" type="text"
                placeholder="Search questions..."
                class="px-3 py-2 border rounded-md focus:ring-2 focus:outline-none"
                style="background-color: rgb(var(--bg-secondary)); color: rgb(var(--text-primary)); border-color: rgb(var(--border-primary))">

            <select wire:model.live="questionFilterType"
                class="px-3 py-2 border rounded-md focus:ring-2 focus:outline-none"
                style="background-color: rgb(var(--bg-secondary)); color: rgb(var(--text-primary)); border-color: rgb(var(--border-primary))">
                <option value="">All Types</option>
                <option value="technical">Technical</option>
                <option value="behavioral">Behavioral</option>
                <option value="system_design">System Design</option>
                <option value="coding">Coding</option>
                <option value="hr">HR</option>
            </select>

            <select wire:model.live="questionFilterDifficulty"
                class="px-3 py-2 border rounded-md focus:ring-2 focus:outline-none"
                style="background-color: rgb(var(--bg-secondary)); color: rgb(var(--text-primary)); border-color: rgb(var(--border-primary))">
                <option value="">All Difficulty</option>
                <option value="beginner">Beginner</option>
                <option value="intermediate">Intermediate</option>
                <option value="advanced">Advanced</option>
                <option value="expert">Expert</option>
            </select>

            <select wire:model.live="questionFilterStatus"
                class="px-3 py-2 border rounded-md focus:ring-2 focus:outline-none"
                style="background-color: rgb(var(--bg-secondary)); color: rgb(var(--text-primary)); border-color: rgb(var(--border-primary))">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="approved">Approved</option>
                <option value="pending">Pending Approval</option>
            </select>
        </div>
    </div>

    <!-- Questions Table -->
    <div class="shadow overflow-hidden sm:rounded-lg" style="background-color: rgb(var(--bg-secondary))">
        <table class="min-w-full divide-y" style="border-color: rgb(var(--border-primary))">
            <thead style="background-color: rgb(var(--bg-tertiary))">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: rgb(var(--text-secondary))">
                        Question
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: rgb(var(--text-secondary))">
                        Type
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: rgb(var(--text-secondary))">
                        Difficulty
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: rgb(var(--text-secondary))">
                        Points
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: rgb(var(--text-secondary))">
                        Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: rgb(var(--text-secondary))">
                        Usage
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider" style="color: rgb(var(--text-secondary))">
                        Actions
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y" style="background-color: rgb(var(--bg-secondary)); border-color: rgb(var(--border-primary))">
                @forelse($this->questions as $question)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium" style="color: rgb(var(--text-primary))">
                                {{ Str::limit($question->question, 80) }}
                            </div>
                            @if($question->category)
                                <div class="text-xs" style="color: rgb(var(--text-secondary))">
                                    Category: {{ $question->category }}
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $question->type_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $question->difficulty_level === 'beginner' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $question->difficulty_level === 'intermediate' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $question->difficulty_level === 'advanced' ? 'bg-orange-100 text-orange-800' : '' }}
                                {{ $question->difficulty_level === 'expert' ? 'bg-red-100 text-red-800' : '' }}">
                                {{ $question->difficulty_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: rgb(var(--text-primary))">
                            {{ $question->max_points }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($question->is_approved)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i> Approved
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i> Pending
                                </span>
                            @endif
                            @if(!$question->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 ml-1">
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm" style="color: rgb(var(--text-secondary))">
                            {{ $question->times_used }} times
                            @if($question->avg_score)
                                <div class="text-xs">Avg: {{ number_format($question->avg_score, 1) }}%</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <button wire:click="viewQuestion({{ $question->id }})"
                                    class="hover:opacity-80 transition-opacity"
                                    style="color: rgb(var(--accent-primary))">
                                    <i class="fas fa-eye"></i>
                                </button>

                                <button wire:click="editQuestion({{ $question->id }})"
                                    class="text-yellow-600 hover:text-yellow-800 transition-colors">
                                    <i class="fas fa-edit"></i>
                                </button>

                                @if(!$question->is_approved)
                                    <button wire:click="approveQuestion({{ $question->id }})"
                                        class="text-green-600 hover:text-green-800 transition-colors">
                                        <i class="fas fa-check"></i>
                                    </button>
                                @endif

                                <button wire:click="toggleQuestionStatus({{ $question->id }})"
                                    class="text-gray-600 hover:text-gray-800 transition-colors">
                                    <i class="fas fa-{{ $question->is_active ? 'ban' : 'check-circle' }}"></i>
                                </button>

                                <button wire:click="deleteQuestion({{ $question->id }})"
                                    wire:confirm="Are you sure you want to delete this question?"
                                    class="text-red-600 hover:text-red-800 transition-colors">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center" style="color: rgb(var(--text-secondary))">
                            <i class="fas fa-question-circle text-4xl mb-4" style="color: rgb(var(--text-tertiary))"></i>
                            <p>No questions found. Create your first question to get started!</p>
                            <button wire:click="$set('showCreateQuestionModal', true)"
                                class="mt-4 bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors">
                                <i class="fas fa-plus mr-2"></i> Create Question
                            </button>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if($this->questions->hasPages())
            <div class="px-6 py-3 border-t" style="border-color: rgb(var(--border-primary))">
                {{ $this->questions->links() }}
            </div>
        @endif
    </div>

    <!-- Question Sets Section -->
    <div class="mt-8">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold" style="color: rgb(var(--text-primary))">Question Sets</h3>
            <button wire:click="$set('showCreateSetModal', true)"
                class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                <i class="fas fa-folder-plus mr-2"></i> Create Set
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($this->questionSets as $set)
                <div class="shadow rounded-lg p-6" style="background-color: rgb(var(--bg-secondary))">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h4 class="text-lg font-medium" style="color: rgb(var(--text-primary))">{{ $set->name }}</h4>
                            <p class="text-sm mt-1" style="color: rgb(var(--text-secondary))">{{ Str::limit($set->description, 100) }}</p>
                        </div>
                        @if($set->is_template)
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                Template
                            </span>
                        @endif
                    </div>

                    <div class="space-y-2 text-sm" style="color: rgb(var(--text-secondary))">
                        <div class="flex justify-between">
                            <span>Questions:</span>
                            <span class="font-medium" style="color: rgb(var(--text-primary))">{{ $set->total_questions }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Duration:</span>
                            <span class="font-medium" style="color: rgb(var(--text-primary))">{{ $set->formatted_duration }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Type:</span>
                            <span class="font-medium" style="color: rgb(var(--text-primary))">{{ ucfirst($set->type) }}</span>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center space-x-2">
                        <button wire:click="viewQuestionSet({{ $set->id }})"
                            class="flex-1 bg-blue-600 text-white px-3 py-2 rounded-md hover:bg-blue-700 transition-colors text-sm">
                            <i class="fas fa-eye mr-1"></i> View
                        </button>
                        <button wire:click="editQuestionSet({{ $set->id }})"
                            class="px-3 py-2 bg-yellow-100 text-yellow-700 rounded-md hover:bg-yellow-200 transition-colors">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button wire:click="deleteQuestionSet({{ $set->id }})"
                            wire:confirm="Are you sure?"
                            class="px-3 py-2 bg-red-100 text-red-700 rounded-md hover:bg-red-200 transition-colors">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-8" style="color: rgb(var(--text-secondary))">
                    <i class="fas fa-folder-open text-4xl mb-4" style="color: rgb(var(--text-tertiary))"></i>
                    <p>No question sets yet. Create a set to group related questions!</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modals will be added separately -->
@include('livewire.career.admin-mock.question-modals')