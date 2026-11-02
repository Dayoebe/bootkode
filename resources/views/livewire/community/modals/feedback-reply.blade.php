<x-modal name="community-feedback-reply" model="showFeedbackReplyModal" label="Reply to community feedback">
    <div class="space-y-4 p-4 sm:p-6">
        <div class="flex flex-wrap items-center justify-between gap-3"><h2 class="text-xl font-semibold text-content">Reply to community feedback</h2><x-secondary-button wire:click="closeFeedbackReply">Close reply</x-secondary-button></div>
        @if($feedbackReplyId)
            <h3 class="break-words font-semibold text-content">{{ $feedbackReplyContext['subject'] }}</h3><p class="whitespace-pre-wrap break-words text-content-muted">{{ $feedbackReplyContext['message'] }}</p>
            @if($feedbackReplyContext['admin_response'])
                <p class="font-semibold text-content">Recorded community reply</p><p class="whitespace-pre-wrap break-words text-content">{{ $feedbackReplyContext['admin_response'] }}</p><p class="text-sm text-content-muted">Recorded replies are retained and cannot be silently replaced.</p>
            @else
                <form wire:submit="saveFeedbackReply" class="space-y-3">
                    <x-input-label for="community-reply-text" value="Private reply to the requester" /><p id="community-reply-help" class="text-sm text-content-muted">10–10,000 characters. The reply is retained with its real responder and time. The requester receives a private portal notification. Email alerts follow their saved preference.</p>
                    <textarea id="community-reply-text" wire:model="feedbackReplyText" rows="6" required minlength="10" maxlength="10000" class="w-full rounded-lg border border-line bg-surface px-3 py-2 text-content focus:outline-none focus:ring-2 focus:ring-action" aria-describedby="community-reply-help community-reply-error" aria-invalid="{{ $errors->has('feedbackReplyText') ? 'true' : 'false' }}"></textarea><x-input-error id="community-reply-error" :messages="$errors->get('feedbackReplyText')" />
                    <x-primary-button wire:loading.attr="disabled" wire:target="saveFeedbackReply">Save private reply</x-primary-button>
                </form>
            @endif
        @endif
    </div>
</x-modal>
