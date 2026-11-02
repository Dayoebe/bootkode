<div class="space-y-6" data-move-list="project-criteria-{{ $assessmentId }}">
    <x-ui.flash />
    <x-input-error :messages="$errors->all()" role="alert" />
    <div class="flex flex-wrap items-center justify-between gap-3"><div><h2 class="text-xl font-semibold text-content">Project criteria</h2><p class="text-sm text-content-muted">{{ count($criteria) }} criteria · {{ array_sum(array_column($criteria, 'points')) }} total points</p></div><x-secondary-button wire:click="toggleCreateForm">{{ $showCreateForm ? 'Cancel criterion' : 'Add criterion' }}</x-secondary-button></div>
    @if($showCreateForm)
        <x-ui.panel :title="$editingCriteria ? 'Edit project criterion' : 'Create project criterion'">
            <form wire:submit="{{ $editingCriteria ? 'updateCriteria' : 'createCriteria' }}" class="space-y-5">
                <div><x-input-label for="project-kind" value="Criterion type" /><select id="project-kind" wire:model.live="criteriaType" class="min-h-11 w-full rounded-lg border-line bg-surface text-content focus:ring-action" aria-describedby="project-kind-error">@foreach(['deliverable' => 'File deliverable', 'rubric' => 'Written rubric criterion', 'presentation' => 'Presentation response', 'documentation' => 'Documentation response'] as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach</select><x-input-error id="project-kind-error" :messages="$errors->get('criteriaType')" /></div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div><x-input-label for="project-title" value="Criterion title" /><x-text-input id="project-title" wire:model="criteriaTitle" maxlength="255" required class="w-full" aria-describedby="project-title-error" :aria-invalid="$errors->has('criteriaTitle') ? 'true' : 'false'" /><x-input-error id="project-title-error" :messages="$errors->get('criteriaTitle')" /></div>
                    <div><x-input-label for="project-points" value="Points" /><x-text-input id="project-points" type="number" min="1" max="100" step="0.01" wire:model="points" required class="w-full" aria-describedby="project-points-error" :aria-invalid="$errors->has('points') ? 'true' : 'false'" /><x-input-error id="project-points-error" :messages="$errors->get('points')" /></div>
                </div>
                <div><x-input-label for="project-description" value="Instructions for learner" /><textarea id="project-description" wire:model="criteriaDescription" rows="4" maxlength="1000" required class="w-full rounded-lg border-line bg-surface text-content focus:ring-action" aria-describedby="project-description-error" aria-invalid="{{ $errors->has('criteriaDescription') ? 'true' : 'false' }}"></textarea><x-input-error id="project-description-error" :messages="$errors->get('criteriaDescription')" /></div>
                <label class="flex min-h-11 items-center gap-3 text-content"><input type="checkbox" wire:model="isRequired" /><span>Require a response to this criterion</span></label>
                @if($criteriaType === 'deliverable')
                    @include('livewire.course-management.course-builder.file-requirements', ['prefix' => 'project', 'project' => true])
                @elseif($criteriaType === 'rubric')
                    <div class="space-y-4"><h3 class="font-semibold text-content">Private grading levels</h3>
                        @foreach($rubricLevels as $index => $level)
                            <fieldset class="space-y-3 rounded-lg border border-line p-4" wire:key="project-level-{{ $index }}"><legend class="px-1 text-sm font-semibold text-content">Level {{ $index + 1 }}</legend>
                                <div><x-input-label for="level-name-{{ $index }}" value="Level name" /><x-text-input id="level-name-{{ $index }}" wire:model="rubricLevels.{{ $index }}.name" class="w-full" required aria-describedby="level-name-error-{{ $index }}" /><x-input-error id="level-name-error-{{ $index }}" :messages="$errors->get('rubricLevels.'.$index.'.name')" /></div>
                                <div><x-input-label for="level-points-{{ $index }}" value="Level points" /><x-text-input id="level-points-{{ $index }}" type="number" min="0" max="100" step="0.01" wire:model="rubricLevels.{{ $index }}.points" class="w-full" required aria-describedby="level-points-error-{{ $index }}" /><x-input-error id="level-points-error-{{ $index }}" :messages="$errors->get('rubricLevels.'.$index.'.points')" /></div>
                                <div><x-input-label for="level-description-{{ $index }}" value="Private grading description" /><textarea id="level-description-{{ $index }}" wire:model="rubricLevels.{{ $index }}.description" maxlength="1000" rows="2" class="w-full rounded-lg border-line bg-surface text-content focus:ring-action" aria-describedby="level-description-error-{{ $index }}"></textarea><x-input-error id="level-description-error-{{ $index }}" :messages="$errors->get('rubricLevels.'.$index.'.description')" /></div>
                                @if(count($rubricLevels) > 2)<x-secondary-button wire:click="removeRubricLevel({{ $index }})" :aria-label="'Remove grading level '.($index + 1)">Remove level</x-secondary-button>@endif
                            </fieldset>
                        @endforeach
                        <x-secondary-button wire:click="addRubricLevel">Add grading level</x-secondary-button>
                    </div>
                @else<p class="text-sm text-content-muted">Learners submit a written response for this criterion. Use File deliverable for uploaded work.</p>@endif
                <x-primary-button wire:loading.attr="disabled" wire:target="createCriteria,updateCriteria">{{ $editingCriteria ? 'Save criterion' : 'Create criterion' }}</x-primary-button>
            </form>
        </x-ui.panel>
    @endif
    @forelse($criteria as $index => $criterion)
        <x-ui.panel :title="$criterion['question_text']"><p class="mb-3 text-sm text-content-muted">{{ $criterion['points'] }} points · {{ $criterion['is_required'] ? 'Required response' : 'Optional response' }}</p><p class="mb-4 whitespace-pre-wrap break-words text-content-muted">{{ strip_tags($criterion['explanation'] ?? '') }}</p>
            <div class="flex flex-wrap gap-3"><x-secondary-button wire:click="editCriteria({{ $criterion['id'] }})">Edit criterion</x-secondary-button><x-secondary-button wire:click="duplicateCriteria({{ $criterion['id'] }})">Copy criterion</x-secondary-button><x-secondary-button data-move-key="{{ $criterion['id'] }}" data-move-direction="up" wire:click="moveCriteria({{ $criterion['id'] }}, -1)" :disabled="$index === 0">Move criterion {{ $index + 1 }} up</x-secondary-button><x-secondary-button data-move-key="{{ $criterion['id'] }}" data-move-direction="down" wire:click="moveCriteria({{ $criterion['id'] }}, 1)" :disabled="$index === count($criteria) - 1">Move criterion {{ $index + 1 }} down</x-secondary-button><x-danger-button wire:click="deleteCriteria({{ $criterion['id'] }})" wire:confirm="Delete this criterion? Recorded learning history prevents deletion.">Delete criterion</x-danger-button></div>
        </x-ui.panel>
    @empty<x-ui.empty-state title="No project criteria" description="Add clear instructions and configure file requirements where uploaded work is needed." />@endforelse
</div>
