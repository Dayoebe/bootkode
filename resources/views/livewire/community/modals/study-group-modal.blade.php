{{-- resources/views/livewire/community/modals/study-group-modal.blade.php --}}
@if($activeModal === 'study-group')
    <div role="dialog" aria-modal="true" aria-label="Create Study Group" x-data x-trap.inert.noscroll="true" x-on:keydown.escape.window="$wire.closeModal()" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-themed-secondary rounded-lg shadow-sm max-w-2xl w-full max-h-[90vh] overflow-y-auto animate-slide-in">
            <div class="sticky top-0 bg-themed-secondary border-b border-themed-primary p-6 flex items-center justify-between">
                <h2 class="text-xl font-bold text-themed-primary">Create Study Group</h2>
                <button type="button" aria-label="Close dialog" wire:click="closeModal()" class="text-themed-secondary hover:text-themed-primary transition-colors">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>

            <form wire:submit.prevent="createStudyGroup" class="p-6 space-y-4">
                <div>
                    <label for="community-groupTitle" class="block text-sm font-medium text-themed-primary mb-2">Group Name</label>
                    <input type="text" wire:model="groupTitle"
                           class="w-full bg-themed-tertiary border border-themed-primary text-themed-primary rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent"
                           placeholder="e.g., JavaScript Fundamentals Study Group" id="community-groupTitle" aria-describedby="community-groupTitle-error">
                    @error('groupTitle') <span id="community-groupTitle-error" class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="community-groupDescription" class="block text-sm font-medium text-themed-primary mb-2">Description</label>
                    <textarea wire:model="groupDescription" rows="3"
                              class="w-full bg-themed-tertiary border border-themed-primary text-themed-primary rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent"
                              placeholder="What will your group focus on? Goals and expectations?" id="community-groupDescription" aria-describedby="community-groupDescription-error"></textarea>
                    @error('groupDescription') <span id="community-groupDescription-error" class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="community-groupStartDate" class="block text-sm font-medium text-themed-primary mb-2">Start Date</label>
                        <input type="datetime-local" wire:model="groupStartDate"
                               class="w-full bg-themed-tertiary border border-themed-primary text-themed-primary rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent" id="community-groupStartDate" aria-describedby="community-groupStartDate-error">
                    </div>
                    <div>
                        <label for="community-groupEndDate" class="block text-sm font-medium text-themed-primary mb-2">End Date</label>
                        <input type="datetime-local" wire:model="groupEndDate"
                               class="w-full bg-themed-tertiary border border-themed-primary text-themed-primary rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent" id="community-groupEndDate" aria-describedby="community-groupEndDate-error">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="community-groupMaxParticipants" class="block text-sm font-medium text-themed-primary mb-2">Max Participants</label>
                        <input type="number" wire:model="groupMaxParticipants" min="2" max="50"
                               class="w-full bg-themed-tertiary border border-themed-primary text-themed-primary rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent"
                               placeholder="Leave empty for unlimited" id="community-groupMaxParticipants" aria-describedby="community-groupMaxParticipants-error">
                    </div>
                    <div>
                        <label for="community-groupTags" class="block text-sm font-medium text-themed-primary mb-2">Tags</label>
                        <input type="text" wire:model="groupTags"
                               class="w-full bg-themed-tertiary border border-themed-primary text-themed-primary rounded-lg px-3 py-2 focus:ring-2 focus:ring-green-500 focus:border-transparent"
                               placeholder="JavaScript, React (comma-separated)" id="community-groupTags" aria-describedby="community-groupTags-error">
                    </div>
                </div>

                <div class="flex gap-3 justify-end pt-4">
                    <button type="button" wire:click="closeModal()"
                            class="px-4 py-2 bg-themed-tertiary border border-themed-primary text-themed-primary rounded-lg hover:bg-themed-primary/10 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors flex items-center gap-2">
                        <i class="fas fa-users"></i>Create Group
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif
