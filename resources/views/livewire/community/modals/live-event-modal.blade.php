{{-- resources/views/livewire/community/modals/live-event-modal.blade.php --}}
@if($activeModal === 'live-event')
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-themed-secondary rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto animate-slide-in">
            <div class="sticky top-0 bg-themed-secondary border-b border-themed-primary p-6 flex items-center justify-between">
                <h2 class="text-xl font-bold text-themed-primary">Create Live Event</h2>
                <button wire:click="closeModal()" class="text-themed-secondary hover:text-themed-primary transition-colors">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>

            <form wire:submit.prevent="createLiveEvent" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-themed-primary mb-2">Event Title</label>
                    <input type="text" wire:model="eventTitle"
                           class="w-full bg-themed-tertiary border border-themed-primary text-themed-primary rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           placeholder="React Masterclass Webinar">
                    @error('eventTitle') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-themed-primary mb-2">Description</label>
                    <textarea wire:model="eventDescription" rows="3"
                              class="w-full bg-themed-tertiary border border-themed-primary text-themed-primary rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                              placeholder="What will be discussed?"></textarea>
                    @error('eventDescription') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-themed-primary mb-2">Location / Meeting Link</label>
                    <input type="text" wire:model="eventLocation"
                           class="w-full bg-themed-tertiary border border-themed-primary text-themed-primary rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           placeholder="Zoom link, Discord, etc.">
                    @error('eventLocation') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-2">Start Date & Time</label>
                        <input type="datetime-local" wire:model="eventStartDate"
                               class="w-full bg-themed-tertiary border border-themed-primary text-themed-primary rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        @error('eventStartDate') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-2">End Date & Time</label>
                        <input type="datetime-local" wire:model="eventEndDate"
                               class="w-full bg-themed-tertiary border border-themed-primary text-themed-primary rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-2">Max Attendees</label>
                        <input type="number" wire:model="eventMaxParticipants" min="1"
                               class="w-full bg-themed-tertiary border border-themed-primary text-themed-primary rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               placeholder="Leave empty for unlimited">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-2">Tags</label>
                        <input type="text" wire:model="eventTags"
                               class="w-full bg-themed-tertiary border border-themed-primary text-themed-primary rounded-lg px-3 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                               placeholder="React, JavaScript (comma-separated)">
                    </div>
                </div>

                <div class="flex gap-3 justify-end pt-4">
                    <button type="button" wire:click="closeModal()"
                            class="px-4 py-2 bg-themed-tertiary border border-themed-primary text-themed-primary rounded-lg hover:bg-themed-primary/10 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition-colors flex items-center gap-2">
                        <i class="fas fa-calendar-plus"></i>Create Event
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif
