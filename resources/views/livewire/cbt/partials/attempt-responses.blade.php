<div class="mt-4 space-y-4">
    <p role="status" aria-live="polite" class="text-sm text-content-muted">{{ $reviewMessage }}</p>
    @foreach($responses as $response)
        <section id="response-result-{{ $response['id'] }}" tabindex="-1" class="min-w-0 space-y-2 rounded-lg border border-line p-4" wire:key="response-result-{{ $response['id'] }}">
            <h3 class="break-words font-semibold text-content">{{ $response['question'] }}</h3>
            <p class="whitespace-pre-wrap break-words text-content">{{ $response['response'] }}</p>
            @include('livewire.cbt.partials.submitted-files', ['files' => $response['files']])
            <p class="text-sm text-content-muted">{{ $response['pending_review'] ? 'Awaiting instructor review' : 'Recorded mark: '.$response['points'].' / '.($response['max_points'] ?? 'unavailable').' points' }}</p>
            @if($response['feedback'])<p class="whitespace-pre-wrap break-words text-content-muted">Instructor feedback: {{ $response['feedback'] }}</p>@endif
            @if($response['corrections'])
                <details class="text-sm text-content-muted"><summary class="cursor-pointer py-3 font-semibold">Mark correction history ({{ count($response['corrections']) }})</summary>
                    <ol class="space-y-3">
                        @foreach($response['corrections'] as $correction)
                            <li class="space-y-1 border-t border-line py-3"><p>Mark changed from {{ $correction['before_points'] }} to {{ $correction['after_points'] }} points · {{ \Carbon\Carbon::parse($correction['at'])->format('M j, Y H:i') }}</p><p class="whitespace-pre-wrap break-words">{{ $correction['reason'] }}</p></li>
                        @endforeach
                    </ol>
                </details>
            @endif
            @foreach($response['mark_reviews'] as $request)
                <div class="space-y-1 border-t border-line py-3 text-sm text-content-muted"><p class="font-semibold">Mark review: {{ ['open' => 'Awaiting a decision', 'upheld' => 'Recorded mark upheld', 'corrected' => 'Mark corrected'][$request['status']] }}</p><p class="whitespace-pre-wrap break-words">Your request: {{ $request['reason'] }}</p>@if(isset($request['decision']))<p class="whitespace-pre-wrap break-words">Reviewer decision: {{ $request['decision'] }}</p>@endif</div>
            @endforeach
            @if($response['can_withdraw_sharing'] ?? false)<x-secondary-button wire:click="withdrawQnaSharing({{ $response['id'] }})" wire:confirm="Withdraw this response from peer sharing? Your assessment history will be retained." wire:loading.attr="disabled">Withdraw Q&amp;A sharing</x-secondary-button>@endif
            @if($response['can_request_review'])
                <details><summary class="cursor-pointer py-3 text-sm font-semibold text-content">Request a review of this mark</summary>
                    <form wire:submit="requestMarkReview({{ $response['id'] }})" class="space-y-3">
                        <p id="review-help-{{ $response['id'] }}" class="text-sm text-content-muted">Explain what the reviewer should reconsider (10–2,000 characters). One request is available per recorded mark. Your result remains recorded while the request is open.</p>
                        <x-input-label for="review-reason-{{ $response['id'] }}" value="Reason for mark review" />
                        <textarea id="review-reason-{{ $response['id'] }}" wire:model="reviewReasons.{{ $response['id'] }}" rows="3" minlength="10" maxlength="2000" required class="w-full rounded-lg border-line bg-surface text-content focus:ring-action" aria-describedby="review-help-{{ $response['id'] }} review-error-{{ $response['id'] }}" aria-invalid="{{ $errors->has('reviewReasons.'.$response['id']) ? 'true' : 'false' }}"></textarea>
                        <x-input-error id="review-error-{{ $response['id'] }}" :messages="$errors->get('reviewReasons.'.$response['id'])" />
                        <x-secondary-button type="submit" wire:loading.attr="disabled" wire:target="requestMarkReview">Send mark review request</x-secondary-button>
                    </form>
                </details>
            @endif
        </section>
    @endforeach
</div>
