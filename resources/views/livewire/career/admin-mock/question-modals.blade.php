{{-- resources/views/livewire/career/admin-mock/question-modals.blade.php --}}

@if($showCreateQuestionModal)
<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" x-trap.inert.noscroll="true" role="dialog" aria-modal="true" aria-label="Create question modal" tabindex="-1" x-data="{}" @keydown.escape.stop="$wire.set(&#x27;showCreateQuestionModal&#x27;, false)">
    <div class="bg-themed-secondary rounded-lg max-w-3xl w-full max-h-[90vh] overflow-hidden border border-themed-primary">
        <div class="px-6 py-4 border-b border-themed-primary bg-themed-tertiary flex items-center justify-between">
            <h3 class="text-xl font-bold text-themed-primary">
                {{ $editingQuestionId ? 'Edit Question' : 'Create New Question' }}
            </h3>
            <button wire:click="$set('showCreateQuestionModal', false)"
                class="text-themed-secondary hover:text-themed-primary transition-colors" aria-label="Close">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form wire:submit.prevent="createQuestion" class="p-6 overflow-y-auto max-h-[calc(90vh-140px)]">
            <div class="space-y-4">
                <!-- Question Text -->
                <div>
                    <label class="block text-sm font-semibold text-themed-primary mb-2">
                        Question Text *
                    </label>
                    <textarea aria-label="Enter your question" wire:model="question" rows="3"
                        class="w-full px-4 py-3 border border-themed-primary rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary"
                        placeholder="Enter your question..."></textarea>
                    @error('question') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Type and Difficulty -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-themed-primary mb-2">Type *</label>
                        <select aria-label="Type" wire:model="type"
                            class="w-full px-4 py-3 border border-themed-primary rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent bg-themed-secondary text-themed-primary">
                            <option value="technical">Technical</option>
                            <option value="behavioral">Behavioral</option>
                            <option value="system_design">System Design</option>
                            <option value="coding">Coding</option>
                            <option value="hr">HR</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-themed-primary mb-2">Difficulty *</label>
                        <select aria-label="Difficulty level" wire:model="difficulty_level"
                            class="w-full px-4 py-3 border border-themed-primary rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent bg-themed-secondary text-themed-primary">
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                            <option value="expert">Expert</option>
                        </select>
                    </div>
                </div>

                <!-- Answer Type -->
                <div>
                    <label class="block text-sm font-semibold text-themed-primary mb-2">Answer Type *</label>
                    <select aria-label="Answer type" wire:model="answer_type"
                        class="w-full px-4 py-3 border border-themed-primary rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent bg-themed-secondary text-themed-primary">
                        <option value="text">Text Answer</option>
                        <option value="multiple_choice">Multiple Choice</option>
                        <option value="coding">Coding Challenge</option>
                    </select>
                </div>

                <!-- Correct Answer -->
                <div>
                    <label class="block text-sm font-semibold text-themed-primary mb-2">
                        Correct/Sample Answer
                    </label>
                    <textarea aria-label="Enter the correct or sample answer" wire:model="correct_answer" rows="3"
                        class="w-full px-4 py-3 border border-themed-primary rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary"
                        placeholder="Enter the correct or sample answer..."></textarea>
                </div>

                <!-- Keywords -->
                <div>
                    <label class="block text-sm font-semibold text-themed-primary mb-2">
                        Keywords (comma-separated)
                    </label>
                    <input aria-label="keyword1, keyword2, keyword3" wire:model="keywords" type="text"
                        class="w-full px-4 py-3 border border-themed-primary rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary"
                        placeholder="keyword1, keyword2, keyword3">
                    <p class="text-xs text-themed-secondary mt-1">Used for auto-evaluation of text answers</p>
                </div>

                <!-- Points and Time -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-themed-primary mb-2">Max Points *</label>
                        <input aria-label="Max points" wire:model="max_points" type="number" min="1" max="100"
                            class="w-full px-4 py-3 border border-themed-primary rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-themed-primary mb-2">Time Limit (seconds) *</label>
                        <input aria-label="Time limit" wire:model="time_limit" type="number" min="30" max="3600"
                            class="w-full px-4 py-3 border border-themed-primary rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary">
                    </div>
                </div>

                <!-- Category, Industry, Job Role -->
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-themed-primary mb-2">Category</label>
                        <input aria-label="e.g., JavaScript" wire:model="category" type="text"
                            class="w-full px-4 py-3 border border-themed-primary rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary"
                            placeholder="e.g., JavaScript">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-themed-primary mb-2">Industry</label>
                        <input aria-label="e.g., Tech" wire:model="industry" type="text"
                            class="w-full px-4 py-3 border border-themed-primary rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary"
                            placeholder="e.g., Tech">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-themed-primary mb-2">Job Role</label>
                        <input aria-label="e.g., Frontend Dev" wire:model="job_role" type="text"
                            class="w-full px-4 py-3 border border-themed-primary rounded-lg focus:ring-2 focus:ring-brand focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary"
                            placeholder="e.g., Frontend Dev">
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-4 mt-6 pt-6 border-t border-themed-primary">
                <button type="button" wire:click="$set('showCreateQuestionModal', false)"
                    class="px-6 py-3 border border-themed-primary text-themed-primary rounded-lg hover:bg-themed-tertiary transition-colors font-medium">
                    Cancel
                </button>
                <button type="submit"
                    class="bg-action text-action-foreground px-8 py-3 rounded-lg hover:bg-action/90 transition-colors font-semibold">
                    {{ $editingQuestionId ? 'Update' : 'Create' }} Question
                </button>
            </div>
        </form>
    </div>
</div>
@endif
