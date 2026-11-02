<div class="mx-auto max-w-5xl space-y-6 p-4 sm:p-6">
    <x-ui.page-header title="Q&A participation" :description="$assessment->title" />
    <p role="status" aria-live="polite" class="text-content-muted">{{ $message }}</p>
    <x-ui.panel title="Private course discussion">
        <p class="text-content-muted">Submit your own response to a topic before reading or reviewing shared responses to that topic. Sharing is optional and uses participant labels. Only submitted Q&A text is shared; grades, instructor feedback, files and private evaluation criteria stay private. Peer feedback never changes official marks.</p>
        <p class="mt-3 text-sm text-content-muted">Withdrawal removes your response from the peer board and retains the original assessment and moderation history. A withdrawn or rejected response cannot be shared again; additional assessment attempts follow the instructor's limit.</p>
        @if($moderator)<p class="mt-3 text-sm text-content-muted">Course reviewers can inspect submitted work and pending requests. A reason is required for each decision. Official marking stays in Assessment Reviews.</p>@endif
        <div class="mt-4 flex flex-wrap gap-3"><x-secondary-button wire:click="reloadDiscussion">Reload discussion</x-secondary-button><a class="bk-button bk-button-secondary" href="{{ route('lesson.assessments', $assessment->lesson_id) }}">Back to lesson assessments</a>@if($moderator)<a class="bk-button bk-button-secondary" href="{{ route('assessment.reviews') }}">Official assessment reviews</a>@endif</div>
    </x-ui.panel>
    @forelse($entries as $entry)
        <section id="discussion-entry-{{ $entry['id'] }}" tabindex="-1" wire:key="discussion-{{ $entry['id'] }}" class="space-y-4 rounded-lg border border-line bg-surface p-4 sm:p-6">
            <div><h2 class="break-words text-lg font-semibold text-content">{{ $entry['label'] }}</h2><p class="text-sm text-content-muted">{{ $entry['topic'] }} · {{ ['private' => 'Private', 'pending' => 'Awaiting sharing approval', 'published' => 'Shared with eligible peers', 'withdrawn' => 'Sharing withdrawn', 'rejected' => 'Sharing declined', 'hidden' => 'Hidden by reviewer'][$entry['status']] }}</p></div>
            <p class="whitespace-pre-wrap break-words text-content">{{ $entry['response'] }}</p>
            <p id="discussion-error-{{ $entry['id'] }}" role="alert" class="text-sm text-danger">{{ $errors->first('entry.'.$entry['id']) }}</p>
            @if($entry['own'] && $entry['status'] === 'private')
                <p class="text-sm text-content-muted">Sharing publishes the submitted text to eligible learners under this topic's original approval rules. It does not publish files or your grade.</p>
                <x-secondary-button wire:click="share({{ $entry['id'] }})" wire:confirm="Share this submitted Q&A text with eligible course participants?" wire:loading.attr="disabled" wire:target="share">Share submitted response</x-secondary-button>
            @endif
            @if($entry['own'] && in_array($entry['status'], ['pending', 'published']))<x-secondary-button wire:click="withdraw({{ $entry['id'] }})" wire:confirm="Withdraw this response from peer sharing? Your original assessment is retained." wire:loading.attr="disabled">Withdraw sharing</x-secondary-button>@endif
            @foreach($entry['decisions'] as $decision)<p class="whitespace-pre-wrap break-words text-sm text-content-muted">Recorded decision: {{ $decision['reason'] }}</p>@endforeach
            @foreach($entry['reviews'] as $review)
                <div class="space-y-2 border-t border-line pt-4" wire:key="feedback-{{ $review['id'] }}"><h3 class="font-semibold text-content">{{ $review['own'] ? 'Your peer feedback' : 'Peer feedback' }} · {{ ucfirst($review['status']) }}</h3><p class="whitespace-pre-wrap break-words text-content">{{ $review['text'] }}</p>
                    @if($moderator && !$review['own'] && in_array($review['status'], ['pending', 'published']))
                        <div class="flex flex-wrap gap-3">@foreach($review['status'] === 'pending' ? ['publish' => 'Approve feedback', 'reject' => 'Decline feedback'] : ['hide' => 'Hide feedback'] as $decision => $label)<x-secondary-button wire:click="moderateReview({{ $entry['id'] }}, {{ $entry['version'] }}, '{{ $review['id'] }}', '{{ $decision }}')" :aria-describedby="'discussion-reason-'.$entry['id'].' discussion-error-'.$entry['id']" wire:loading.attr="disabled">{{ $label }}</x-secondary-button>@endforeach</div>
                    @endif
                </div>
            @endforeach
            @if($entry['can_review'])
                <form wire:submit="sendFeedback({{ $entry['id'] }})" class="space-y-3 border-t border-line pt-4">
                    <x-input-label :for="'peer-feedback-'.$entry['id']" value="Constructive peer feedback" /><p id="peer-feedback-help-{{ $entry['id'] }}" class="text-sm text-content-muted">20–4,000 characters. One retained feedback entry per shared response. Approval follows the original topic setting.</p>
                    <textarea id="peer-feedback-{{ $entry['id'] }}" wire:model="feedback.{{ $entry['id'] }}" rows="4" required minlength="20" maxlength="4000" class="w-full rounded-lg border-line bg-surface text-content focus:ring-action" aria-describedby="peer-feedback-help-{{ $entry['id'] }} discussion-error-{{ $entry['id'] }}" aria-invalid="{{ $errors->has('entry.'.$entry['id']) ? 'true' : 'false' }}"></textarea>
                    <x-secondary-button type="submit" wire:loading.attr="disabled" wire:target="sendFeedback">Send peer feedback</x-secondary-button>
                </form>
            @endif
            @if($moderator)
                <div class="space-y-3 border-t border-line pt-4"><x-input-label :for="'discussion-reason-'.$entry['id']" value="Moderation reason (10–1,000 characters)" /><textarea id="discussion-reason-{{ $entry['id'] }}" wire:model="reasons.{{ $entry['id'] }}" rows="3" minlength="10" maxlength="1000" class="w-full rounded-lg border-line bg-surface text-content focus:ring-action" aria-describedby="discussion-error-{{ $entry['id'] }}" aria-invalid="{{ $errors->has('entry.'.$entry['id']) ? 'true' : 'false' }}"></textarea>
                    @if(!$entry['own'] && in_array($entry['status'], ['pending', 'published']))<div class="flex flex-wrap gap-3">@foreach($entry['status'] === 'pending' ? ['publish' => 'Approve sharing', 'reject' => 'Decline sharing'] : ['hide' => 'Hide shared response'] as $decision => $label)<x-secondary-button wire:click="moderate({{ $entry['id'] }}, {{ $entry['version'] }}, '{{ $decision }}')" wire:loading.attr="disabled">{{ $label }}</x-secondary-button>@endforeach</div>@endif
                </div>
            @endif
        </section>
    @empty<x-ui.empty-state title="No available shared responses" description="Submit a configured Q&A topic to see your own sharing controls. Peer responses appear only after you submit that topic and their authors choose to share. Older text-only attempts retain their original private format." />@endforelse
    {{ $entries->links() }}
</div>
