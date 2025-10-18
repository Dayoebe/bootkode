{{-- resources/views/livewire/career/admin-mock/question-modals.blade.php --}}

@if($showCreateQuestionModal)
<div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
    <div class="bg-themed-secondary rounded-2xl max-w-3xl w-full max-h-[90vh] overflow-hidden">
        <div class="px-6 py-4 border-b border-themed-primary flex items-center justify-between">
            <h3 class="text-xl font-bold text-themed-primary">
                {{ $editingQuestionId ? 'Edit Question' : 'Create New Question' }}
            </h3>
            <button wire:click="$set('showCreateQuestionModal', false)" 
                class="text-themed-secondary hover:text-themed-primary">
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
                    <textarea wire:model="question" rows="3"
                        class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-blue-500 bg-themed-secondary text-themed-primary"
                        placeholder="Enter your question..."></textarea>
                    @error('question') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Type and Difficulty -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-themed-primary mb-2">Type *</label>
                        <select wire:model="type"
                            class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-blue-500 bg-themed-secondary text-themed-primary">
                            <option value="technical">Technical</option>
                            <option value="behavioral">Behavioral</option>
                            <option value="system_design">System Design</option>
                            <option value="coding">Coding</option>
                            <option value="hr">HR</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-themed-primary mb-2">Difficulty *</label>
                        <select wire:model="difficulty_level"
                            class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-blue-500 bg-themed-secondary text-themed-primary">
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
                    <select wire:model="answer_type"
                        class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-blue-500 bg-themed-secondary text-themed-primary">
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
                    <textarea wire:model="correct_answer" rows="3"
                        class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-blue-500 bg-themed-secondary text-themed-primary"
                        placeholder="Enter the correct or sample answer..."></textarea>
                </div>

                <!-- Keywords -->
                <div>
                    <label class="block text-sm font-semibold text-themed-primary mb-2">
                        Keywords (comma-separated)
                    </label>
                    <input wire:model="keywords" type="text"
                        class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-blue-500 bg-themed-secondary text-themed-primary"
                        placeholder="keyword1, keyword2, keyword3">
                    <p class="text-xs text-themed-secondary mt-1">Used for auto-evaluation of text answers</p>
                </div>

                <!-- Points and Time -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-themed-primary mb-2">Max Points *</label>
                        <input wire:model="max_points" type="number" min="1" max="100"
                            class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-blue-500 bg-themed-secondary text-themed-primary">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-themed-primary mb-2">Time Limit (seconds) *</label>
                        <input wire:model="time_limit" type="number" min="30" max="3600"
                            class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-blue-500 bg-themed-secondary text-themed-primary">
                    </div>
                </div>

                <!-- Category, Industry, Job Role -->
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-themed-primary mb-2">Category</label>
                        <input wire:model="category" type="text"
                            class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-blue-500 bg-themed-secondary text-themed-primary"
                            placeholder="e.g., JavaScript">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-themed-primary mb-2">Industry</label>
                        <input wire:model="industry" type="text"
                            class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-blue-500 bg-themed-secondary text-themed-primary"
                            placeholder="e.g., Tech">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-themed-primary mb-2">Job Role</label>
                        <input wire:model="job_role" type="text"
                            class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-blue-500 bg-themed-secondary text-themed-primary"
                            placeholder="e.g., Frontend Dev">
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-4 mt-6 pt-6 border-t border-themed-primary">
                <button type="button" wire:click="$set('showCreateQuestionModal', false)"
                    class="px-6 py-3 border border-themed-primary text-themed-primary rounded-xl hover:bg-themed-tertiary transition-colors">
                    Cancel
                </button>
                <button type="submit"
                    class="bg-blue-600 text-white px-8 py-3 rounded-xl hover:bg-blue-700 transition-colors font-semibold">
                    {{ $editingQuestionId ? 'Update' : 'Create' }} Question
                </button>
            </div>
        </form>
    </div>
</div>
@endif