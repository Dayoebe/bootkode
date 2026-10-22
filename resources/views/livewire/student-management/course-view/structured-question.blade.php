@if($question['question_type'] === 'ordering')
    <p id="sequence-help-{{ $question['id'] }}" class="mb-4 text-sm text-content-muted">Arrange every item in the correct order. Moving an item saves the sequence automatically. You can also save the displayed order.</p>
    <ol data-move-list="student-order-{{ $question['id'] }}" class="space-y-3" aria-describedby="sequence-help-{{ $question['id'] }}">
        @foreach((!empty($answers[$question['id']]) ? $answers[$question['id']] : range(0, count($question['options']) - 1)) as $position => $optionIndex)
            <li wire:key="sequence-{{ $question['id'] }}-{{ $optionIndex }}" class="space-y-2 rounded-lg border border-line p-3"><p class="break-words text-content">{{ $position + 1 }}. {{ strip_tags($question['options'][$optionIndex] ?? '') }}</p><div class="flex flex-wrap gap-2"><x-secondary-button data-move-key="{{ $optionIndex }}" data-move-direction="up" wire:click="moveOrderingItem({{ $question['id'] }}, {{ $position }}, -1)" :disabled="$position === 0" :aria-label="'Move '.strip_tags($question['options'][$optionIndex] ?? '').' up'">Move up</x-secondary-button><x-secondary-button data-move-key="{{ $optionIndex }}" data-move-direction="down" wire:click="moveOrderingItem({{ $question['id'] }}, {{ $position }}, 1)" :disabled="$position === count($question['options']) - 1" :aria-label="'Move '.strip_tags($question['options'][$optionIndex] ?? '').' down'">Move down</x-secondary-button></div></li>
        @endforeach
    </ol>
    <x-secondary-button class="mt-4" wire:click="saveOrdering({{ $question['id'] }})" wire:loading.attr="disabled">Save this order</x-secondary-button>
@else
    <p id="match-help-{{ $question['id'] }}" class="mb-4 text-sm text-content-muted">Match each target to one choice. Use every choice once. {{ $question['question_type'] === 'drag_drop' ? 'Drag a choice onto a target, or use its labelled selection list.' : 'Use the labelled selection lists.' }}</p>
    @if($question['question_type'] === 'drag_drop')
        <div class="mb-4 flex flex-wrap gap-2" aria-label="Draggable choices">
            @foreach($question['options'] as $optionIndex => $option)<span draggable="true" x-data x-on:dragstart="$event.dataTransfer.setData('application/x-bootkode-choice', @js($question['id'].':'.$optionIndex))" class="max-w-full cursor-grab break-words rounded-lg border border-line bg-surface-muted p-3 text-content">{{ strip_tags($option) }}</span>@endforeach
        </div>
    @endif
    <div class="space-y-4">
        @foreach($question['prompts'] as $promptIndex => $prompt)
            <div wire:key="target-{{ $question['id'] }}-{{ $promptIndex }}" class="rounded-lg border border-line p-3" @if($question['question_type'] === 'drag_drop') x-data x-on:dragover.prevent x-on:drop.prevent="const parts = $event.dataTransfer.getData('application/x-bootkode-choice').split(':'); if (parts[0] === @js((string) $question['id']) && /^[0-9]+$/.test(parts[1] || '')) { const select = $el.querySelector('select'); if ([...select.options].some(option => option.value === parts[1])) { select.value = parts[1]; select.dispatchEvent(new Event('change', { bubbles: true })); select.focus(); } }" @endif>
                <label for="match-{{ $question['id'] }}-{{ $promptIndex }}" class="mb-2 block break-words font-semibold text-content">{{ strip_tags($prompt) }}</label>
                <select id="match-{{ $question['id'] }}-{{ $promptIndex }}" wire:model.live="answers.{{ $question['id'] }}.{{ $promptIndex }}" class="min-h-11 w-full max-w-full rounded-lg border-line bg-surface text-content" aria-describedby="match-help-{{ $question['id'] }}"><option value="">Choose a matching item</option>@foreach($question['options'] as $optionIndex => $option)<option value="{{ $optionIndex }}">{{ strip_tags($option) }}</option>@endforeach</select>
            </div>
        @endforeach
    </div>
@endif
