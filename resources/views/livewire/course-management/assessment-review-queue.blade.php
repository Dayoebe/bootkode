<div class="mx-auto max-w-6xl space-y-6 p-4 sm:p-6">
    <x-ui.page-header title="Assessment reviews" description="Review written submissions, respond to mark review requests and retain a clear correction history." />
    <p role="status" aria-live="polite" class="text-content-muted">{{ $message }}</p>
    <x-ui.panel title="Submitted attempts">
        <div class="mb-4 max-w-sm">
            <x-input-label for="review-status" value="Show submissions" />
            <select id="review-status" wire:model.live="status" class="min-h-11 w-full rounded-lg border-line bg-surface text-content focus:ring-action">
                <option value="pending">Awaiting initial review</option><option value="requested">Mark review requested</option><option value="finalized">Finalized results</option>
            </select>
        </div>
        <ul class="divide-y divide-line">
            @forelse($entries as $entry)
                <li class="flex flex-wrap items-center justify-between gap-4 py-4" wire:key="review-{{ $entry->id }}">
                    <div class="min-w-0"><h2 class="break-words font-semibold text-content">{{ $titles[$entry->metadata['assessment_id']] ?? 'Assessment' }}</h2><p class="break-words text-sm text-content-muted">{{ $entry->user->name }} · Attempt {{ $entry->metadata['attempt'] }} · {{ $entry->ended_at->format('M j, Y H:i') }}</p>
                        <p class="text-sm text-content-muted">{{ ($entry->metadata['results']['pending_review'] ?? false) ? 'Awaiting initial review' : 'Recorded result: '.$entry->metadata['results']['percentage'].'%' }}@if($entry->metadata['open_mark_reviews'] ?? 0) · {{ $entry->metadata['open_mark_reviews'] }} open mark review request(s)@endif</p>
                    </div>
                    <x-secondary-button wire:click="review({{ $entry->id }})" wire:loading.class="opacity-60" wire:target="review">Review submission</x-secondary-button>
                </li>
            @empty
                <li><x-ui.empty-state title="No matching submissions" description="Choose another submission status. Historical answers without a recorded attempt require a separate records review." /></li>
            @endforelse
        </ul>
        {{ $entries->links() }}
    </x-ui.panel>
    <x-modal name="assessment-review" model="showReview" label="Review written submission">
        <div class="space-y-6 p-4 sm:p-6">
            <div class="flex flex-wrap justify-between gap-3"><h2 class="text-xl font-semibold text-content">Review written submission</h2><x-secondary-button wire:click="$set('showReview', false)">Close review</x-secondary-button></div>
            <p role="status" aria-live="polite" class="text-sm text-content-muted">{{ $message }}</p>
            @if($review)
                @php($finalized = !($review->metadata['results']['pending_review'] ?? true))
                <p class="text-content-muted">{{ $review->user->name }} · Attempt {{ $review->metadata['attempt'] }}. {{ $finalized ? 'Recorded result: '.$review->metadata['results']['percentage'].'%.' : 'Final results publish after every answer is marked.' }}</p>
                <x-secondary-button wire:click="review({{ $review->id }})" wire:loading.attr="disabled">Reload submission</x-secondary-button>
                @foreach($review->metadata['questions'] as $question)
                    @php($answer = $responses->get($question['id']))
                    @continue(in_array($question['question_type'], ['multiple_choice', 'true_false', ...\App\Services\StructuredQuestionService::TYPES]) || !$answer)
                    <section id="mark-section-{{ $answer->id }}" tabindex="-1" wire:key="mark-{{ $answer->id }}" class="space-y-3 rounded-lg border border-line p-4">
                        <h3 class="break-words font-semibold text-content">{{ strip_tags($question['question_text']) }}</h3>
                        <p class="whitespace-pre-wrap break-words text-content">{{ $answer->answer ?? 'No written response.' }}</p>
                        @include('livewire.cbt.partials.submitted-files', ['files' => app(\App\Services\AssessmentSubmissionService::class)->projectedFiles($review, $answer->question_id)])
                        @php($originalRules = $review->metadata['grading'][$question['id']]['submission_rules'] ?? [])
                        @if($originalRules['enabled'] ?? false)
                            <p class="text-sm text-content-muted">Original file requirements: {{ $originalRules['min_files'] }}–{{ $originalRules['max_files'] }} files; up to {{ number_format($originalRules['max_kb'] / 1024, 2) }} MiB each; {{ implode(', ', $originalRules['extensions']) }}.</p>
                        @endif
                        @if($originalRules['word_limit'] ?? null)
                            <p class="text-sm text-content-muted">Original written word limit: {{ $originalRules['word_limit'] }}.</p>
                        @endif
                        @php($criteria = $review->metadata['grading'][$question['id']]['criteria'] ?? [])
                        @foreach(['sample_answer' => 'Private sample answer', 'grading_notes' => 'Private grading notes'] as $field => $label)
                            @if(is_string($criteria[$field] ?? null) && trim($criteria[$field]) !== '')<details class="text-sm text-content-muted"><summary class="cursor-pointer py-3">{{ $label }}</summary><p class="whitespace-pre-wrap break-words">{{ strip_tags($criteria[$field]) }}</p></details>@endif
                        @endforeach
                        @foreach(['rubric_criteria' => 'Private grading rubric', 'rubric_levels' => 'Private grading levels'] as $field => $label)
                            @php($rows = collect(is_array($criteria[$field] ?? null) ? $criteria[$field] : [])->filter(fn ($row) => is_array($row) && is_string($row['name'] ?? null)))
                            @if($rows->isNotEmpty())
                                <details class="text-sm text-content-muted">
                                    <summary class="cursor-pointer py-3">{{ $label }}</summary>
                                    <ul class="space-y-3">
                                        @foreach($rows as $row)
                                            <li>
                                                <p class="font-semibold">
                                                    {{ $row['name'] }}
                                                    @if(is_numeric($row['weight'] ?? null))
                                                        · {{ $row['weight'] }}%
                                                    @elseif(is_numeric($row['points'] ?? null))
                                                        · {{ $row['points'] }} points
                                                    @endif
                                                </p>
                                                @if(is_string($row['description'] ?? null))<p class="whitespace-pre-wrap break-words">{{ strip_tags($row['description']) }}</p>@endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </details>
                            @endif
                        @endforeach
                        @if(!empty($review->metadata['grading'][$question['id']]['explanation']))<details class="text-sm text-content-muted"><summary class="cursor-pointer py-3">Instructor guidance</summary><p class="whitespace-pre-wrap break-words">{{ strip_tags($review->metadata['grading'][$question['id']]['explanation']) }}</p></details>@endif
                        @php($reference = app(\App\Services\StructuredQuestionService::class)->array($review->metadata['grading'][$question['id']]['correct_answers'] ?? []))
                        @if($reference && collect($reference)->every(fn ($value) => is_scalar($value)))<details class="text-sm text-content-muted"><summary class="cursor-pointer py-3">Private reference answer</summary><p class="whitespace-pre-wrap break-words">{{ implode('; ', array_map('strval', $reference)) }}</p></details>@endif
                        @if($answer->graded_at)
                            <p class="text-content">Recorded mark: {{ $answer->points_earned }} / {{ $question['points'] }}.</p><p class="whitespace-pre-wrap break-words text-content-muted">{{ $answer->feedback }}</p>
                            @foreach(collect($review->metadata['mark_reviews'] ?? [])->where('answer_id', $answer->id) as $request)
                                <div class="space-y-3 border-t border-line py-3">
                                    <h4 class="font-semibold text-content">Mark review: {{ ['open' => 'Awaiting a decision', 'upheld' => 'Recorded mark upheld', 'corrected' => 'Mark corrected'][$request['status']] }}</h4>
                                    <p class="whitespace-pre-wrap break-words text-sm text-content-muted">Learner request: {{ $request['reason'] }}</p>
                                    @if($request['status'] === 'open')
                                        <p class="text-sm text-content-muted">Correct the recorded mark below, or explain why the mark should remain.</p>
                                        <form wire:submit="upholdMark('{{ $request['id'] }}')" class="space-y-3">
                                            <x-input-label for="decision-{{ $request['id'] }}" value="Reason for keeping the mark" />
                                            <textarea id="decision-{{ $request['id'] }}" wire:model="decisionReasons.{{ $request['id'] }}" rows="3" minlength="10" maxlength="2000" required class="w-full rounded-lg border-line bg-surface text-content focus:ring-action" aria-describedby="decision-error-{{ $request['id'] }}" aria-invalid="{{ $errors->has('decisionReasons.'.$request['id']) ? 'true' : 'false' }}"></textarea>
                                            <x-input-error id="decision-error-{{ $request['id'] }}" :messages="$errors->get('decisionReasons.'.$request['id'])" />
                                            <x-secondary-button type="submit" wire:loading.attr="disabled" wire:target="upholdMark">Keep mark and close request</x-secondary-button>
                                        </form>
                                    @else
                                        <p class="whitespace-pre-wrap break-words text-sm text-content-muted">Decision: {{ $request['decision'] }}</p>
                                    @endif
                                </div>
                            @endforeach
                            @if($correctionAnswerId === $answer->id)
                                <form wire:submit="publishCorrection" class="space-y-3 border-t border-line pt-4">
                                    <h4 class="font-semibold text-content">Correct recorded mark</h4>
                                    <p id="correction-help" class="text-sm text-content-muted">Publishing updates this result and future eligibility immediately. The previous mark, result and reason stay in history. Existing rewards, completion records and issued certificates are retained.</p>
                                    <div><x-input-label for="correction-points" :value="'Corrected points (0 to '.$question['points'].')'" /><x-text-input id="correction-points" type="number" min="0" :max="$question['points']" step="0.01" wire:model="correctionPoints" class="w-full" required aria-describedby="correction-points-error" :aria-invalid="$errors->has('correctionPoints') ? 'true' : 'false'" /><x-input-error id="correction-points-error" :messages="$errors->get('correctionPoints')" /></div>
                                    <div><x-input-label for="correction-feedback" value="Updated feedback for learner" /><textarea id="correction-feedback" wire:model="correctionFeedback" maxlength="4000" rows="3" class="w-full rounded-lg border-line bg-surface text-content focus:ring-action" aria-describedby="correction-feedback-error" aria-invalid="{{ $errors->has('correctionFeedback') ? 'true' : 'false' }}"></textarea><x-input-error id="correction-feedback-error" :messages="$errors->get('correctionFeedback')" /></div>
                                    <div><x-input-label for="correction-reason" value="Reason for correction" /><textarea id="correction-reason" wire:model="correctionReason" minlength="10" maxlength="2000" rows="3" required class="w-full rounded-lg border-line bg-surface text-content focus:ring-action" aria-describedby="correction-help correction-reason-error" aria-invalid="{{ $errors->has('correctionReason') ? 'true' : 'false' }}"></textarea><x-input-error id="correction-reason-error" :messages="$errors->get('correctionReason')" /></div>
                                    <div class="flex flex-wrap gap-3"><x-primary-button wire:loading.attr="disabled" wire:target="publishCorrection">Publish correction</x-primary-button><x-secondary-button wire:click="cancelCorrection">Cancel correction</x-secondary-button></div>
                                </form>
                            @elseif($finalized)
                                <x-secondary-button id="correct-mark-{{ $answer->id }}" wire:click="beginCorrection({{ $answer->id }})" wire:loading.attr="disabled" wire:target="beginCorrection">Correct recorded mark</x-secondary-button>
                            @endif
                            @php($corrections = collect($review->metadata['mark_corrections'] ?? [])->where('answer_id', $answer->id))
                            @if($corrections->isNotEmpty())
                                <details class="text-sm text-content-muted"><summary class="cursor-pointer py-3 font-semibold">Mark correction history ({{ $corrections->count() }})</summary>
                                    <ol class="space-y-3">@foreach($corrections as $event)<li class="space-y-1 border-t border-line py-3"><p>Mark changed from {{ $event['before']['points'] }} to {{ $event['after']['points'] }} points · {{ \Carbon\Carbon::parse($event['at'])->format('M j, Y H:i') }}</p><p class="whitespace-pre-wrap break-words">{{ $event['reason'] }}</p><p>Result changed from {{ $event['before_result']['percentage'] }}% to {{ $event['after_result']['percentage'] }}%.</p></li>@endforeach</ol>
                                </details>
                            @endif
                        @else
                            <form wire:submit="grade({{ $answer->id }})" class="space-y-3">
                                <div><x-input-label for="mark-{{ $answer->id }}" :value="'Points (0 to '.$question['points'].')'" /><x-text-input id="mark-{{ $answer->id }}" type="number" min="0" :max="$question['points']" step="0.01" wire:model="marks.{{ $answer->id }}" class="w-full" required aria-describedby="mark-error-{{ $answer->id }}" :aria-invalid="$errors->has('marks.'.$answer->id) ? 'true' : 'false'" /><x-input-error id="mark-error-{{ $answer->id }}" :messages="$errors->get('marks.'.$answer->id)" /></div>
                                <div><x-input-label for="feedback-{{ $answer->id }}" value="Feedback for learner" /><textarea id="feedback-{{ $answer->id }}" wire:model="feedback.{{ $answer->id }}" maxlength="4000" rows="4" class="w-full rounded-lg border-line bg-surface text-content focus:ring-action" aria-describedby="feedback-error-{{ $answer->id }}" aria-invalid="{{ $errors->has('feedback.'.$answer->id) ? 'true' : 'false' }}"></textarea><x-input-error id="feedback-error-{{ $answer->id }}" :messages="$errors->get('feedback.'.$answer->id)" /></div>
                                <x-primary-button wire:loading.attr="disabled" wire:target="grade">Record mark</x-primary-button>
                            </form>
                        @endif
                    </section>
                @endforeach
            @endif
        </div>
    </x-modal>
</div>
