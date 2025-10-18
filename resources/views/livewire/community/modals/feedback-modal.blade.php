{{-- resources/views/livewire/community/modals/feedback-modal.blade.php --}}
@if($activeModal === 'feedback')
    <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div
            class="bg-themed-secondary rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto animate-slide-in">
            <div
                class="sticky top-0 bg-themed-secondary border-b border-themed-primary p-6 flex items-center justify-between">
                <h2 class="text-xl font-bold text-themed-primary">Submit Feedback</h2>
                <button wire:click="closeModal()" class="text-themed-secondary hover:text-themed-primary transition-colors">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>

            <form wire:submit.prevent="submitFeedback" class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-2">Category</label>
                        <select wire:model="feedbackCategory"
                            class="w-full bg-themed-tertiary border border-themed-primary text-themed-primary rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="general">General Feedback</option>
                            <option value="feature_request">Feature Request</option>
                            <option value="bug_report">Bug Report</option>
                            <option value="course_feedback">Course Feedback</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-themed-primary mb-2">Priority</label>
                        <select wire:model="feedbackPriority"
                            class="w-full bg-themed-tertiary border border-themed-primary text-themed-primary rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-themed-primary mb-2">Subject</label>
                    <input type="text" wire:model="feedbackSubject"
                        class="w-full bg-themed-tertiary border border-themed-primary text-themed-primary rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        placeholder="Brief summary">
                    @error('feedbackSubject') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-themed-primary mb-2">Message</label>
                    <textarea wire:model="feedbackMessage" rows="5"
                        class="w-full bg-themed-tertiary border border-themed-primary text-themed-primary rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        placeholder="Provide detailed feedback..."></textarea>
                    @error('feedbackMessage') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="bg-blue-100/20 border border-blue-500/30 rounded-lg p-3 text-sm text-blue-300">
                    <i class="fas fa-lightbulb mr-2"></i>
                    Your feedback helps us improve the platform. Be specific and constructive!
                </div>

                <div class="flex gap-3 justify-end pt-4">
                    <button type="button" wire:click="closeModal()"
                        class="px-4 py-2 bg-themed-tertiary border border-themed-primary text-themed-primary rounded-lg hover:bg-themed-primary/10 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors flex items-center gap-2">
                        <i class="fas fa-paper-plane"></i>Submit Feedback
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif