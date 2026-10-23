{{-- resources/views/livewire/community/modals/forum-modal.blade.php --}}
@if($activeModal === 'forum')
    <div role="dialog" aria-modal="true" aria-label="Start New Discussion" x-data x-trap.inert.noscroll="true" x-on:keydown.escape.window="$wire.closeModal()" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-themed-secondary rounded-lg shadow-sm max-w-2xl w-full max-h-[90vh] overflow-y-auto animate-slide-in">
            <div class="sticky top-0 bg-themed-secondary border-b border-themed-primary p-6 flex items-center justify-between">
                <h2 class="text-xl font-bold text-themed-primary">Start New Discussion</h2>
                <button type="button" aria-label="Close dialog" wire:click="closeModal()" class="text-themed-secondary hover:text-themed-primary transition-colors">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>

            <form wire:submit="createThread" class="p-6 space-y-4">
                <div>
                    <label for="community-category" class="block text-sm font-medium text-themed-primary mb-2">Category</label>
                    <select wire:model="category"
                            class="w-full bg-themed-tertiary border border-themed-primary text-themed-primary rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/30 focus:border-transparent" id="community-category" aria-describedby="community-category-error">
                        <option value="general">General Discussion</option>
                        <option value="course-help">Course Help</option>
                        <option value="projects">Projects</option>
                        <option value="careers">Careers & Jobs</option>
                    </select>
                    @error('category') <span id="community-category-error" class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="community-title" class="block text-sm font-medium text-themed-primary mb-2">Title</label>
                    <input type="text" wire:model="title"
                           class="w-full bg-themed-tertiary border border-themed-primary text-themed-primary rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/30 focus:border-transparent"
                           placeholder="What's your question or topic?" id="community-title" aria-describedby="community-title-error">
                    @error('title') <span id="community-title-error" class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="community-content" class="block text-sm font-medium text-themed-primary mb-2">Content</label>
                    <textarea wire:model="content" rows="6"
                              class="w-full bg-themed-tertiary border border-themed-primary text-themed-primary rounded-lg px-3 py-2 focus:ring-2 focus:ring-brand/30 focus:border-transparent"
                              placeholder="Share details, context, and what you've already tried..." id="community-content" aria-describedby="community-content-error"></textarea>
                    @error('content') <span id="community-content-error" class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="flex gap-3 justify-end pt-4">
                    <button type="button" wire:click="closeModal()"
                            class="px-4 py-2 bg-themed-tertiary border border-themed-primary text-themed-primary rounded-lg hover:bg-themed-primary/10 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-action hover:bg-action text-action-foreground rounded-lg font-medium transition-colors flex items-center gap-2">
                        <i class="fas fa-paper-plane"></i>Post Discussion
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif
