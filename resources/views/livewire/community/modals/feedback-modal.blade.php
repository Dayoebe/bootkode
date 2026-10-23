{{-- resources/views/livewire/community/modals/feedback-modal.blade.php --}}
@if($activeModal === 'feedback')
    <div role="dialog" aria-modal="true" aria-label="Submit Feedback" x-data x-trap.inert.noscroll="true" x-on:keydown.escape.window="$wire.closeModal()" class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div
            class="bg-themed-secondary rounded-lg shadow-sm max-w-2xl w-full max-h-[90vh] overflow-y-auto animate-slide-in">
            <div
                class="sticky top-0 bg-themed-secondary border-b border-themed-primary p-6 flex items-center justify-between">
                <h2 class="text-xl font-bold text-themed-primary">Submit Feedback</h2>
                <button type="button" aria-label="Close dialog" wire:click="closeModal()" class="text-themed-secondary hover:text-themed-primary transition-colors">
                    <i class="fas fa-times text-2xl" aria-hidden="true"></i>
                </button>
            </div>

            <form wire:submit.prevent="submitFeedback" class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="community-feedbackCategory" class="block text-sm font-medium text-themed-primary mb-2">Category</label>
                        <select wire:model="feedbackCategory"
                            class="w-full bg-themed-tertiary border border-themed-primary text-themed-primary rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent" id="community-feedbackCategory" aria-describedby="community-feedbackCategory-error">
                            <option value="general">General Feedback</option>
                            <option value="feature_request">Feature Request</option>
                            <option value="bug_report">Bug Report</option>
                            <option value="course_feedback">Course Feedback</option>
                        </select>
                    </div>
                    <div>
                        <label for="community-feedbackPriority" class="block text-sm font-medium text-themed-primary mb-2">Priority</label>
                        <select wire:model="feedbackPriority"
                            class="w-full bg-themed-tertiary border border-themed-primary text-themed-primary rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent" id="community-feedbackPriority" aria-describedby="community-feedbackPriority-error">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="community-feedbackSubject" class="block text-sm font-medium text-themed-primary mb-2">Subject</label>
                    <input type="text" wire:model="feedbackSubject"
                        class="w-full bg-themed-tertiary border border-themed-primary text-themed-primary rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        placeholder="Brief summary" id="community-feedbackSubject" aria-describedby="community-feedbackSubject-error">
                    @error('feedbackSubject') <span id="community-feedbackSubject-error" class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="community-feedbackMessage" class="block text-sm font-medium text-themed-primary mb-2">Message</label>
                    <textarea wire:model="feedbackMessage" rows="5"
                        class="w-full bg-themed-tertiary border border-themed-primary text-themed-primary rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        placeholder="Provide detailed feedback..." id="community-feedbackMessage" aria-describedby="community-feedbackMessage-error"></textarea>
                    @error('feedbackMessage') <span id="community-feedbackMessage-error" class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="bg-brand/10 border border-brand rounded-lg p-3 text-sm text-brand">
                    <i class="fas fa-lightbulb mr-2" aria-hidden="true"></i>
                    Your feedback helps us improve the platform. Be specific and constructive!
                </div>

                <div class="flex gap-3 justify-end pt-4">
                    <button type="button" wire:click="closeModal()"
                        class="px-4 py-2 bg-themed-tertiary border border-themed-primary text-themed-primary rounded-lg hover:bg-themed-primary/10 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors flex items-center gap-2">
                        <i class="fas fa-paper-plane" aria-hidden="true"></i>Submit Feedback
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif
