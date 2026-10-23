{{-- resources/views/livewire/community/modals/code-challenge-modal.blade.php --}}
@if($activeModal === 'code-challenge')
    <div role="dialog" aria-modal="true" aria-label="{{ $selectedItem ? 'Submit a solution' : 'Create a challenge' }}" x-data x-trap.inert.noscroll="true" x-on:keydown.escape.window="$wire.closeModal()" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-themed-secondary rounded-lg shadow-sm max-w-2xl w-full max-h-[90vh] overflow-y-auto animate-slide-in">
            <div class="sticky top-0 bg-themed-secondary border-b border-themed-primary p-6 flex items-center justify-between">
                <h2 class="text-xl font-bold text-themed-primary">{{ $selectedItem ? 'Submit a solution' : 'Create a challenge' }}</h2>
                <button type="button" aria-label="Close dialog" wire:click="closeModal()" class="text-themed-secondary hover:text-themed-primary transition-colors">
                    <i aria-hidden="true" class="fas fa-times text-2xl"></i>
                </button>
            </div>

            @if($selectedItem)
                <form wire:submit="submitCodeSolution" class="space-y-4 p-6">
                    <h3 class="text-lg font-semibold text-content">{{ $challengeTitle }}</h3>
                    <p class="whitespace-pre-line text-content-muted">{{ $challengeDescription }}</p>
                    <p class="text-sm text-content-muted">Your solution is saved for review. Submitting does not assign a grade.</p>
                    <div>
                        <x-input-label for="challenge-language" value="Language" />
                        <x-text-input id="challenge-language" wire:model="language" class="mt-1 w-full" required aria-describedby="challenge-language-error" />
                        <x-input-error id="challenge-language-error" :messages="$errors->get('language')" />
                    </div>
                    <div>
                        <x-input-label for="challenge-code" value="Your solution" />
                        <textarea aria-label="Code" id="challenge-code" wire:model="code" rows="8" class="mt-1 w-full rounded-lg border-line bg-surface text-content font-mono" required aria-describedby="challenge-code-error"></textarea>
                        <x-input-error id="challenge-code-error" :messages="$errors->get('code')" />
                    </div>
                    <div class="flex flex-wrap justify-end gap-3">
                        <x-secondary-button wire:click="closeModal">Cancel</x-secondary-button>
                        <x-primary-button wire:loading.attr="disabled" wire:target="submitCodeSolution">Submit solution</x-primary-button>
                    </div>
                </form>
            @else
            <form wire:submit.prevent="createChallenge" class="p-6 space-y-4">
                <div>
                    <label for="community-challengeTitle" class="block text-sm font-medium text-themed-primary mb-2">Challenge Title</label>
                    <input type="text" wire:model="challengeTitle"
                           class="w-full bg-themed-tertiary border border-themed-primary text-themed-primary rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                           placeholder="Two Sum Problem" id="community-challengeTitle" aria-describedby="community-challengeTitle-error">
                    @error('challengeTitle') <span id="community-challengeTitle-error" class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="community-challengeDescription" class="block text-sm font-medium text-themed-primary mb-2">Description</label>
                    <textarea wire:model="challengeDescription" rows="3"
                              class="w-full bg-themed-tertiary border border-themed-primary text-themed-primary rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                              placeholder="Brief description of what to solve" id="community-challengeDescription" aria-describedby="community-challengeDescription-error"></textarea>
                    @error('challengeDescription') <span id="community-challengeDescription-error" class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="community-challengeDifficulty" class="block text-sm font-medium text-themed-primary mb-2">Difficulty</label>
                        <select wire:model="challengeDifficulty"
                                class="w-full bg-themed-tertiary border border-themed-primary text-themed-primary rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-transparent" id="community-challengeDifficulty" aria-describedby="community-challengeDifficulty-error">
                            <option value="easy">Easy</option>
                            <option value="medium">Medium</option>
                            <option value="hard">Hard</option>
                        </select>
                    </div>
                    <div>
                        <label for="community-challengePoints" class="block text-sm font-medium text-themed-primary mb-2">Points</label>
                        <input type="number" wire:model="challengePoints" min="10" max="1000" step="10"
                               class="w-full bg-themed-tertiary border border-themed-primary text-themed-primary rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-transparent" id="community-challengePoints" aria-describedby="community-challengePoints-error">
                    </div>
                </div>

                <div class="flex gap-3 justify-end pt-4">
                    <button type="button" wire:click="closeModal()"
                            class="px-4 py-2 bg-themed-tertiary border border-themed-primary text-themed-primary rounded-lg hover:bg-themed-primary/10 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg font-medium transition-colors flex items-center gap-2">
                        <i aria-hidden="true" class="fas fa-code"></i>Create Challenge
                    </button>
                </div>
            </form>
            @endif
        </div>
    </div>
@endif
