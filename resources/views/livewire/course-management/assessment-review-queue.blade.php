<div class="mx-auto max-w-6xl space-y-6 p-4 sm:p-6">
    <x-ui.page-header title="Assessment reviews" description="Review submitted written answers for the courses you manage. Final results publish after every answer is marked." />
    <p role="status" aria-live="polite" class="text-content-muted">{{ $message }}</p>
    <x-ui.panel title="Pending submissions">
        <ul class="divide-y divide-line">
            @forelse($entries as $entry)
                <li class="flex flex-wrap items-center justify-between gap-4 py-4" wire:key="review-{{ $entry->id }}"><div class="min-w-0"><h2 class="break-words font-semibold text-content">{{ $titles[$entry->metadata['assessment_id']] ?? 'Assessment' }}</h2><p class="break-words text-sm text-content-muted">{{ $entry->user->name }} · Attempt {{ $entry->metadata['attempt'] }} · {{ $entry->ended_at->format('M j, Y H:i') }}</p></div><x-secondary-button wire:click="review({{ $entry->id }})">Review submission</x-secondary-button></li>
            @empty<li><x-ui.empty-state title="No submissions awaiting review" description="Submitted written answers for your courses will appear here. Historical answers without a recorded attempt require a separate records review." /></li>@endforelse
        </ul>
        {{ $entries->links() }}
    </x-ui.panel>
    <x-modal name="assessment-review" model="showReview" label="Review written submission">
        <div class="space-y-6 p-4 sm:p-6">
            <div class="flex flex-wrap justify-between gap-3"><h2 class="text-xl font-semibold text-content">Review written submission</h2><x-secondary-button wire:click="$set('showReview', false)">Close review</x-secondary-button></div>
            <p role="status" class="text-sm text-content-muted">{{ $message }}</p>
            @if($review)
                <p class="text-content-muted">{{ $review->user->name }} · Attempt {{ $review->metadata['attempt'] }}. Recorded marks are retained; corrections require a formal records review.</p>
                @foreach($review->metadata['questions'] as $question)
                    @php($answer = $responses->get($question['id']))
                    @continue(in_array($question['question_type'], ['multiple_choice', 'true_false']) || !$answer)
                    <section wire:key="mark-{{ $answer->id }}" class="space-y-3 rounded-lg border border-line p-4">
                        <h3 class="break-words font-semibold text-content">{{ strip_tags($question['question_text']) }}</h3>
                        <p class="whitespace-pre-wrap break-words text-content">{{ $answer->answer ?? 'No answer submitted.' }}</p>
                        @if(!empty($review->metadata['grading'][$question['id']]['explanation']))<details class="text-sm text-content-muted"><summary class="cursor-pointer py-2">Instructor guidance</summary><p class="whitespace-pre-wrap break-words">{{ strip_tags($review->metadata['grading'][$question['id']]['explanation']) }}</p></details>@endif
                        @if($answer->graded_at)<p class="text-content">Recorded mark: {{ $answer->points_earned }} / {{ $question['points'] }}.</p><p class="whitespace-pre-wrap break-words text-content-muted">{{ $answer->feedback }}</p>
                        @else
                            <form wire:submit="grade({{ $answer->id }})" class="space-y-3">
                                <div><x-input-label for="mark-{{ $answer->id }}" :value="'Points (0 to '.$question['points'].')'" /><x-text-input id="mark-{{ $answer->id }}" type="number" min="0" :max="$question['points']" step="0.01" wire:model="marks.{{ $answer->id }}" class="w-full" required aria-describedby="mark-error-{{ $answer->id }}" :aria-invalid="$errors->has('marks.'.$answer->id) ? 'true' : 'false'" /><x-input-error id="mark-error-{{ $answer->id }}" :messages="$errors->get('marks.'.$answer->id)" /></div>
                                <div><x-input-label for="feedback-{{ $answer->id }}" value="Feedback for learner" /><textarea id="feedback-{{ $answer->id }}" wire:model="feedback.{{ $answer->id }}" maxlength="4000" rows="4" class="w-full rounded-lg border-line bg-surface text-content focus:ring-action" aria-describedby="feedback-error-{{ $answer->id }}"></textarea><x-input-error id="feedback-error-{{ $answer->id }}" :messages="$errors->get('feedback.'.$answer->id)" /></div>
                                <x-primary-button wire:loading.attr="disabled">Record mark</x-primary-button>
                            </form>
                        @endif
                    </section>
                @endforeach
            @endif
        </div>
    </x-modal>
</div>
