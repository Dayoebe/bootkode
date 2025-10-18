{{-- resources/views/livewire/community/modals/code-challenge-modal.blade.php --}}
@if($activeModal === 'code-challenge')
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-themed-secondary rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto animate-slide-in">
            <div class="sticky top-0 bg-themed-secondary border-b border-themed-primary p-6 flex items-center justify-between">
                <h2 class="text-xl font-bold text-themed-primary">Create Code Challenge</h2>
                <button wire:click="closeModal()" class="text-themed-secondary hover:text-themed-primary transition-colors">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>

            <form wire:submit.prevent="createChallenge" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-themed-primary mb-2">Challenge Title</label>
                    <input type="text" wire:model="challengeTitle"
                           class="w-full bg-themed-tertiary border border-themed-primary text-themed-primary rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                           placeholder="Two Sum Problem">
                    @error('challengeTitle') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-themed-primary mb-2">Description</label>
                    <textarea wire:model="challengeDescription" rows="3"
                              class="w-full bg-themed-tertiary border border-themed-primary text-themed-primary rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                              placeholder="Brief description of what to solve"></textarea>
                    @error('challengeDescription') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-2">Difficulty</label>
                        <select wire:model="challengeDifficulty"
                                class="w-full bg-themed-tertiary border border-themed-primary text-themed-primary rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                            <option value="easy">Easy</option>
                            <option value="medium">Medium</option>
                            <option value="hard">Hard</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-2">Points</label>
                        <input type="number" wire:model="challengePoints" min="10" max="1000" step="10"
                               class="w-full bg-themed-tertiary border border-themed-primary text-themed-primary rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    </div>
                </div>

                <div class="flex gap-3 justify-end pt-4">
                    <button type="button" wire:click="closeModal()"
                            class="px-4 py-2 bg-themed-tertiary border border-themed-primary text-themed-primary rounded-lg hover:bg-themed-primary/10 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium transition-colors flex items-center gap-2">
                        <i class="fas fa-code"></i>Create Challenge
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif
