{{-- resources/views/livewire/career/admin-question-sets.blade.php --}}

<div class="min-h-screen bg-themed-primary p-6">
    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-themed-primary">Question Sets</h1>
            <button wire:click="$set('showCreateSetModal', true)"
                class="bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus mr-2"></i> Create Question Set
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($questionSets as $set)
                <div class="bg-themed-secondary rounded-xl shadow-lg p-6 border border-themed-primary">
                    <h3 class="text-xl font-bold text-themed-primary mb-2">{{ $set->name }}</h3>
                    <p class="text-themed-secondary text-sm mb-4">{{ Str::limit($set->description, 100) }}</p>
                    
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-themed-secondary">Questions:</span>
                            <span class="font-semibold text-themed-primary">{{ $set->total_questions }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-themed-secondary">Duration:</span>
                            <span class="font-semibold text-themed-primary">{{ $set->formatted_duration }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-themed-secondary">Type:</span>
                            <span class="font-semibold text-themed-primary">{{ ucfirst($set->type) }}</span>
                        </div>
                    </div>

                    <div class="mt-4 flex space-x-2">
                        <button wire:click="editSet({{ $set->id }})"
                            class="flex-1 bg-yellow-600 text-white px-4 py-2 rounded-lg hover:bg-yellow-700 transition-colors">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </button>
                        <button wire:click="deleteSet({{ $set->id }})" wire:confirm="Delete this set?"
                            class="flex-1 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-trash mr-1"></i> Delete
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16">
                    <i class="fas fa-folder-open text-6xl text-themed-tertiary mb-4"></i>
                    <p class="text-themed-secondary mb-4">No question sets yet</p>
                    <button wire:click="$set('showCreateSetModal', true)"
                        class="bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 transition-colors">
                        Create Your First Set
                    </button>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Create Set Modal --}}
    @if($showCreateSetModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-themed-secondary rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
                <div class="px-6 py-4 border-b border-themed-primary flex justify-between items-center">
                    <h3 class="text-xl font-bold text-themed-primary">Create Question Set</h3>
                    <button wire:click="$set('showCreateSetModal', false)">
                        <i class="fas fa-times text-themed-secondary"></i>
                    </button>
                </div>

                <form wire:submit.prevent="createSet" class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-themed-primary mb-2">Set Name *</label>
                            <input wire:model="name" type="text" required
                                class="w-full px-4 py-3 border border-themed-primary rounded-xl bg-themed-secondary text-themed-primary">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-themed-primary mb-2">Description</label>
                            <textarea wire:model="description" rows="3"
                                class="w-full px-4 py-3 border border-themed-primary rounded-xl bg-themed-secondary text-themed-primary"></textarea>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-themed-primary mb-2">Type</label>
                                <select wire:model="type"
                                    class="w-full px-4 py-3 border border-themed-primary rounded-xl bg-themed-secondary text-themed-primary">
                                    <option value="technical">Technical</option>
                                    <option value="behavioral">Behavioral</option>
                                    <option value="mixed">Mixed</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-themed-primary mb-2">Difficulty</label>
                                <select wire:model="difficulty_level"
                                    class="w-full px-4 py-3 border border-themed-primary rounded-xl bg-themed-secondary text-themed-primary">
                                    <option value="beginner">Beginner</option>
                                    <option value="intermediate">Intermediate</option>
                                    <option value="advanced">Advanced</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-themed-primary mb-2">Duration (min)</label>
                                <input wire:model="estimated_duration" type="number" min="15"
                                    class="w-full px-4 py-3 border border-themed-primary rounded-xl bg-themed-secondary text-themed-primary">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-themed-primary mb-2">Select Questions</label>
                            <div class="border border-themed-primary rounded-xl p-4 max-h-96 overflow-y-auto">
                                @foreach($availableQuestions as $question)
                                    <label class="flex items-start p-3 hover:bg-themed-tertiary rounded-lg cursor-pointer">
                                        <input type="checkbox" wire:click="toggleQuestion({{ $question->id }})"
                                            {{ in_array($question->id, $selectedQuestions) ? 'checked' : '' }}
                                            class="mt-1 mr-3">
                                        <div class="flex-1">
                                            <p class="text-themed-primary">{{ Str::limit($question->question, 100) }}</p>
                                            <p class="text-xs text-themed-secondary mt-1">
                                                {{ $question->type_label }} • {{ $question->difficulty_label }} • {{ $question->max_points }} pts
                                            </p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                            <p class="text-sm text-themed-secondary mt-2">
                                Selected: {{ count($selectedQuestions) }} questions
                            </p>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4 mt-6">
                        <button type="button" wire:click="$set('showCreateSetModal', false)"
                            class="px-6 py-3 rounded-xl bg-themed-tertiary text-themed-secondary hover:bg-themed-primary hover:text-white transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-6 py-3 rounded-xl bg-blue-600 text-white hover:bg-blue-700 transition-colors">
                            <i class="fas fa-save mr-2"></i> Save Set
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>