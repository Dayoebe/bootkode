<div class="space-y-6" data-move-list="qna-topics-{{ $assessmentId }}">
    <x-ui.flash />
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div><h3 class="text-lg font-semibold text-content">Q&A topics</h3><p class="text-sm text-content-muted">{{ count($topics) }} topics · {{ array_sum(array_column($topics, 'points')) }} points</p></div>
        <div class="flex flex-wrap gap-3"><a class="bk-button bk-button-secondary" href="{{ route('assessment.discussion', $assessmentId) }}">Participation and moderation</a><x-secondary-button wire:click="toggleCreateForm">{{ $showCreateForm ? 'Cancel topic form' : 'Add Q&A topic' }}</x-secondary-button></div>
    </div>
    <p class="text-sm text-content-muted">Learners submit their own responses for instructor marking. Peer sharing is optional after submission and never changes official marks. Original requirements remain attached to existing attempts.</p>
    @if($showCreateForm)
        <x-ui.panel :title="$editingTopic ? 'Edit Q&A topic' : 'Create Q&A topic'">
            <form wire:submit="{{ $editingTopic ? 'updateTopic' : 'createTopic' }}" class="space-y-5">
                @if($errors->any())<div role="alert" class="rounded-lg border border-line p-4 text-content"><p class="font-semibold">Check the topic fields.</p><ul class="list-inside list-disc">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
                <div><x-input-label for="qna-title" value="Topic title" /><x-text-input id="qna-title" wire:model="topicTitle" required maxlength="255" class="w-full" aria-describedby="qna-title-error" :aria-invalid="$errors->has('topicTitle') ? 'true' : 'false'" /><x-input-error id="qna-title-error" :messages="$errors->get('topicTitle')" /></div>
                <div><x-input-label for="qna-description" value="Learner instructions" /><textarea id="qna-description" wire:model="topicDescription" required maxlength="2000" rows="4" class="w-full rounded-lg border-line bg-surface text-content focus:ring-action" aria-describedby="qna-description-error" aria-invalid="{{ $errors->has('topicDescription') ? 'true' : 'false' }}"></textarea><x-input-error id="qna-description-error" :messages="$errors->get('topicDescription')" /></div>
                <div><x-input-label for="qna-type" value="Topic type" /><select id="qna-type" wire:model="topicType" class="min-h-11 w-full rounded-lg border-line bg-surface text-content focus:ring-action">@foreach(\App\Services\QnaResponseService::TOPICS as $type)<option value="{{ $type }}">{{ ucfirst(str_replace('_', ' ', $type)) }}</option>@endforeach</select></div>
                <p class="text-sm text-content-muted">Topic type describes the exercise. The sharing controls below determine participant visibility.</p>
                <div class="grid gap-4 sm:grid-cols-2">
                    @foreach(['points' => ['Points', 1, 100, '0.01'], 'minResponses' => ['Required original responses', 1, 10, 1], 'minResponseLength' => ['Minimum characters per response', 10, 1000, 1], 'timeLimit' => ['Optional topic time estimate (minutes)', 1, 10080, 1]] as $field => $control)
                        <div><x-input-label :for="'qna-'.$field" :value="$control[0]" /><x-text-input :id="'qna-'.$field" type="number" wire:model="{{ $field }}" :min="$control[1]" :max="$control[2]" :step="$control[3]" class="w-full" :aria-describedby="'qna-'.$field.'-error'" :aria-invalid="$errors->has($field) ? 'true' : 'false'" /><x-input-error :id="'qna-'.$field.'-error'" :messages="$errors->get($field)" /></div>
                    @endforeach
                </div>
                <p class="text-sm text-content-muted">The assessment deadline governs submission. Each response allows up to 4,000 characters; all responses together allow 10,000. Optional peer feedback is separate from the required original responses.</p>
                <label class="flex min-h-11 items-center gap-3 text-content"><input type="checkbox" wire:model="isRequired" class="rounded border-line text-action focus:ring-action" />Require this topic</label>
                <label class="flex min-h-11 items-center gap-3 text-content"><input type="checkbox" wire:model="allowPeerReview" class="rounded border-line text-action focus:ring-action" />Allow learners to share submitted work and give peer feedback</label>
                <label class="flex min-h-11 items-center gap-3 text-content"><input type="checkbox" wire:model="moderatorApproval" class="rounded border-line text-action focus:ring-action" />Require course reviewer approval before shared responses and peer feedback become visible</label>
                <fieldset class="space-y-3"><legend class="font-semibold text-content">Learner prompts</legend>
                    @foreach($discussionPrompts as $index => $prompt)
                        <div wire:key="qna-prompt-{{ $index }}" class="flex flex-wrap items-end gap-3"><div class="min-w-0 flex-1"><x-input-label :for="'qna-prompt-'.$index" :value="'Prompt '.($index + 1)" /><x-text-input :id="'qna-prompt-'.$index" wire:model="discussionPrompts.{{ $index }}" maxlength="1000" class="w-full" /></div><x-secondary-button wire:click="removeDiscussionPrompt({{ $index }})">Remove prompt {{ $index + 1 }}</x-secondary-button></div>
                    @endforeach
                    <x-secondary-button wire:click="addDiscussionPrompt" :disabled="count($discussionPrompts) >= 10">Add prompt</x-secondary-button>
                </fieldset>
                <fieldset class="space-y-4"><legend class="font-semibold text-content">Private evaluation criteria</legend><p class="text-sm text-content-muted">Weights must total 100%. These grading notes are available to the instructor, not the peer board.</p>
                    @foreach($evaluationCriteria as $index => $criterion)
                        <div wire:key="qna-criterion-{{ $index }}" class="space-y-3 rounded-lg border border-line p-3">
                            @foreach(['name' => 'Criterion name', 'weight' => 'Weight (%)', 'description' => 'Private guidance'] as $field => $label)
                                <div><x-input-label :for="'qna-criterion-'.$index.'-'.$field" :value="$label" /><x-text-input :id="'qna-criterion-'.$index.'-'.$field" wire:model="evaluationCriteria.{{ $index }}.{{ $field }}" :type="$field === 'weight' ? 'number' : 'text'" class="w-full" :aria-describedby="'qna-criterion-'.$index.'-'.$field.'-error'" /><x-input-error :id="'qna-criterion-'.$index.'-'.$field.'-error'" :messages="$errors->get('evaluationCriteria.'.$index.'.'.$field)" /></div>
                            @endforeach
                            <x-secondary-button wire:click="removeEvaluationCriteria({{ $index }})" :disabled="count($evaluationCriteria) <= 1">Remove criterion {{ $index + 1 }}</x-secondary-button>
                        </div>
                    @endforeach
                    <x-input-error :messages="$errors->get('evaluationCriteria')" /><x-secondary-button wire:click="addEvaluationCriteria" :disabled="count($evaluationCriteria) >= 20">Add evaluation criterion</x-secondary-button>
                </fieldset>
                <div class="flex flex-wrap gap-3"><x-primary-button wire:loading.attr="disabled" wire:target="createTopic,updateTopic">Save Q&A topic</x-primary-button><x-secondary-button wire:click="toggleCreateForm">Cancel topic form</x-secondary-button></div>
            </form>
        </x-ui.panel>
    @endif
    @forelse($topics as $index => $topic)
        <section wire:key="qna-topic-{{ $topic['id'] }}" data-move-item="{{ $topic['id'] }}" tabindex="-1" class="space-y-3 rounded-lg border border-line bg-surface p-4">
            <h4 class="break-words font-semibold text-content">{{ $index + 1 }}. {{ $topic['question_text'] }}</h4><p class="whitespace-pre-wrap break-words text-content-muted">{{ $topic['explanation'] }}</p><p class="text-sm text-content-muted">{{ $topic['points'] }} points · {{ $topic['is_required'] ? 'Required' : 'Optional' }}</p>
            <div class="flex flex-wrap gap-3"><x-secondary-button wire:click="editTopic({{ $topic['id'] }})">Edit topic {{ $index + 1 }}</x-secondary-button><x-secondary-button wire:click="duplicateTopic({{ $topic['id'] }})">Copy topic {{ $index + 1 }}</x-secondary-button><x-secondary-button wire:click="moveTopic({{ $topic['id'] }}, -1)" data-move-key="{{ $topic['id'] }}" data-move-direction="up" :disabled="$index === 0">Move topic {{ $index + 1 }} up</x-secondary-button><x-secondary-button wire:click="moveTopic({{ $topic['id'] }}, 1)" data-move-key="{{ $topic['id'] }}" data-move-direction="down" :disabled="$index === count($topics) - 1">Move topic {{ $index + 1 }} down</x-secondary-button><x-danger-button wire:click="deleteTopic({{ $topic['id'] }})" wire:confirm="Delete this topic? Recorded learning history prevents deletion.">Delete topic {{ $index + 1 }}</x-danger-button></div>
        </section>
    @empty<x-ui.empty-state title="No Q&A topics" description="Add a topic with clear original-response requirements and private evaluation criteria." />@endforelse
</div>
