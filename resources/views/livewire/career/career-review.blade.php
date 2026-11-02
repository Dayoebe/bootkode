<div class="space-y-6">
    <x-ui.page-header title="Career requirements & evidence review" description="Review source material before publishing requirements or verifying a learner’s evidence.">
        <x-slot:actions><a href="{{ route('admin.job') }}" class="bk-button bk-button-secondary">Manage vacancies</a><a href="{{ route('career.centre') }}" class="bk-button bk-button-secondary">Career Centre</a></x-slot:actions>
    </x-ui.page-header>
    <p role="status" aria-live="polite" class="text-content-secondary">{{ $notice }}</p>
    <div class="grid items-start gap-6 lg:grid-cols-2">
        <x-ui.panel title="Recent vacancies">
            <p class="mb-3 text-sm text-content-secondary">Browse saved vacancies and their current review state.</p>
            @forelse($jobs as $vacancy)<div class="border-t border-themed-primary py-3"><h3 class="break-words font-semibold text-content">{{ $vacancy->title }}</h3><p class="mt-1 text-sm text-content-secondary">{{ $vacancy->requirements_reviewed_at ? 'Previously reviewed — check current source' : 'No published requirement review' }}</p><x-secondary-button class="mt-2" wire:click="loadJob({{ $vacancy->id }})">Review requirements<span class="sr-only"> for {{ $vacancy->title }}</span></x-secondary-button></div>@empty<p class="text-content-secondary">No vacancies are saved.</p>@endforelse
            <div class="mt-4">{{ $jobs->links() }}</div>
        </x-ui.panel>
        <x-ui.panel title="Evidence review queue">
            <p class="mb-3 text-sm text-content-secondary">Review evidence by status, oldest first. Verification is a deliberate review decision, not an AI prediction.</p>
            <div class="mb-4"><x-input-label for="evidence-status-filter" value="Evidence status" /><select id="evidence-status-filter" wire:model.live="evidenceFilter" class="bk-input mt-1 w-full">@foreach(['pending', 'verified', 'rejected', 'revoked', 'all'] as $status)<option value="{{ $status }}">{{ ucfirst($status) }}</option>@endforeach</select></div>
            @forelse($pendingEvidence as $item)<div class="border-t border-themed-primary py-3"><h3 class="break-words font-semibold text-content">{{ $item->label }}</h3><p class="mt-1 text-sm text-content-secondary">{{ $item->user?->name ?? 'Historical learner' }} · {{ $kinds[$item->kind] }} · {{ ucfirst($item->review_status) }}</p><x-secondary-button class="mt-2" wire:click="selectEvidence({{ $item->id }})">Inspect evidence<span class="sr-only"> {{ $item->id }}</span></x-secondary-button></div>@empty<p class="text-content-secondary">No evidence has this status.</p>@endforelse
            <div class="mt-4">{{ $pendingEvidence->links() }}</div>
        </x-ui.panel>
    </div>
    @if($job)
        <x-ui.panel id="career-requirements" tabindex="-1" :title="'Requirements: '.$job->title">
            <details open><summary class="font-semibold text-content">Original vacancy source</summary><p class="mt-3 whitespace-pre-wrap break-words text-sm text-content-secondary">{{ app(\App\Services\Career\JobRequirementService::class)->source($job) }}</p></details>
            <p class="mt-4 text-sm text-content-secondary">Extract a draft or add requirements manually. Check every classification and whether it is mandatory. Each item needs an exact excerpt from the source. A source edit invalidates the review; reload before saving changes.</p>
            <div class="mt-4 flex flex-wrap gap-3"><x-secondary-button wire:click="extract" wire:loading.attr="disabled" wire:target="extract">Draft requirements with AI</x-secondary-button><x-secondary-button wire:click="loadJob({{ $job->id }})">Reload saved source</x-secondary-button></div>
            <p wire:loading wire:target="extract" role="status" class="mt-3 text-sm text-content-secondary">Extracting a reviewable draft…</p>
            @if($aiError)<p role="alert" class="mt-3 text-content-secondary">{{ $aiError }}</p>@endif
            <form wire:submit="publish" class="mt-5 space-y-5" novalidate>
                <x-input-error :messages="$errors->get('items')" />
                @foreach($items as $index => $item)
                    <fieldset wire:key="requirement-{{ $index }}" class="min-w-0 space-y-4 rounded-lg border border-themed-primary p-4"><legend class="px-1 font-bold text-content">Requirement {{ $index + 1 }}</legend>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div><x-input-label :for="'requirement-kind-'.$index" value="Requirement type" /><select id="requirement-kind-{{ $index }}" wire:model="items.{{ $index }}.kind" class="bk-input mt-1 w-full" aria-describedby="requirement-kind-error-{{ $index }}">@foreach($kinds as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach</select><x-input-error :id="'requirement-kind-error-'.$index" :messages="$errors->get('items.'.$index.'.kind')" /></div>
                            <div><x-input-label :for="'requirement-label-'.$index" value="Canonical name" /><x-text-input :id="'requirement-label-'.$index" wire:model="items.{{ $index }}.label" class="mt-1 w-full" maxlength="120" :aria-describedby="'requirement-label-error-'.$index" required /><x-input-error :id="'requirement-label-error-'.$index" :messages="$errors->get('items.'.$index.'.label')" /></div>
                        </div>
                        <label class="flex min-h-11 items-center gap-3 text-content"><input type="checkbox" wire:model="items.{{ $index }}.mandatory" class="rounded text-action focus:ring-action" />Mandatory — missing reviewed evidence will block a new application</label><x-input-error :messages="$errors->get('items.'.$index.'.mandatory')" />
                        <div><x-input-label :for="'requirement-years-'.$index" value="Minimum years (experience requirements only)" /><x-text-input :id="'requirement-years-'.$index" type="number" min="0" max="80" step="0.1" wire:model="items.{{ $index }}.minimum_years" class="mt-1 w-full" :aria-describedby="'requirement-years-error-'.$index" /><x-input-error :id="'requirement-years-error-'.$index" :messages="$errors->get('items.'.$index.'.minimum_years')" /></div>
                        <div><x-input-label :for="'requirement-excerpt-'.$index" value="Exact supporting excerpt" /><textarea id="requirement-excerpt-{{ $index }}" wire:model="items.{{ $index }}.excerpt" class="bk-input mt-1 w-full" rows="3" maxlength="500" aria-describedby="requirement-excerpt-error-{{ $index }}" required></textarea><x-input-error :id="'requirement-excerpt-error-'.$index" :messages="$errors->get('items.'.$index.'.excerpt')" /></div>
                        <x-secondary-button wire:click="removeRequirement({{ $index }})">Remove requirement {{ $index + 1 }}</x-secondary-button>
                    </fieldset>
                @endforeach
                <x-secondary-button wire:click="addRequirement">Add requirement</x-secondary-button>
                <div><x-input-label for="requirements-reason" value="Review decision and source checks" /><textarea id="requirements-reason" wire:model="reason" class="bk-input mt-1 w-full" rows="3" minlength="10" maxlength="1000" aria-describedby="requirements-reason-help requirements-reason-error" required></textarea><p id="requirements-reason-help" class="mt-1 text-sm text-content-secondary">Explain your checks, especially mandatory/preferred distinctions. Publishing an empty list explicitly records that no structured requirements are enforced. Previous reviews remain in history.</p><x-input-error id="requirements-reason-error" :messages="$errors->get('reason')" /></div>
                <x-primary-button wire:loading.attr="disabled" wire:target="publish" wire:confirm="Publish this reviewed requirement list? New applications will use it immediately.">Publish reviewed requirements</x-primary-button>
            </form>
        </x-ui.panel>
    @endif
    @if($selectedEvidence)
        <x-ui.panel id="career-evidence-review" tabindex="-1" :title="'Evidence: '.$selectedEvidence->label">
            <dl class="grid gap-4 sm:grid-cols-2 text-sm"><div><dt class="font-bold text-content">Learner</dt><dd class="text-content-secondary">{{ $selectedEvidence->user?->name ?? 'Historical learner' }}</dd></div><div><dt class="font-bold text-content">Type / source</dt><dd class="text-content-secondary">{{ $kinds[$selectedEvidence->kind] }} · {{ $selectedEvidence->source_type }}{{ $selectedEvidence->source_id ? ' #'.$selectedEvidence->source_id : '' }}</dd></div><div><dt class="font-bold text-content">Status</dt><dd class="text-content-secondary">{{ ucfirst($selectedEvidence->review_status) }}</dd></div>@if($selectedEvidence->years_experience !== null)<div><dt class="font-bold text-content">Claimed relevant years</dt><dd class="text-content-secondary">{{ $selectedEvidence->years_experience }}</dd></div>@endif</dl>
            <dl class="mt-4 space-y-2">@foreach($sourceSummary as $label => $value)<div><dt class="font-semibold text-content">{{ $label }}</dt><dd class="break-words text-sm text-content-secondary">{{ $value }}</dd></div>@endforeach</dl>
            @if($selectedEvidence->source_type === 'assessment')<a href="{{ route('assessment.reviews') }}" class="bk-button bk-button-secondary mt-3">Open assessment reviews</a>@endif
            <h3 class="mt-4 font-bold text-content">Learner’s source details</h3><p class="mt-2 whitespace-pre-wrap break-words text-content-secondary">{{ $selectedEvidence->reference }}</p>
            <p class="mt-3 text-sm text-content-secondary">{{ app(\App\Services\Career\CompetencyEvidenceService::class)->validSource($selectedEvidence) ? 'The saved source currently passes its structural checks. You must still judge relevance and authenticity.' : 'The saved source is not currently usable. Do not verify it.' }}</p>
            @if($selectedEvidence->source_type === 'external')<p class="mt-2 text-sm text-content-secondary">External references are not automatically verified. Independently check the issuer, actual competency, qualification or experience before approving.</p>@endif
            @if(in_array($selectedEvidence->review_status, ['pending', 'verified'], true))
            <form wire:submit="decide('{{ $selectedEvidence->review_status === 'verified' ? 'revoked' : 'verified' }}')" class="mt-5 space-y-4" novalidate>
                <div><x-input-label for="evidence-review-reason" value="Decision reason and verification checks" /><textarea id="evidence-review-reason" wire:model="evidenceReason" class="bk-input mt-1 w-full" rows="3" minlength="10" maxlength="1000" aria-describedby="evidence-review-reason-error" required></textarea><x-input-error id="evidence-review-reason-error" :messages="$errors->get('evidenceReason')" /></div>
                @if($selectedEvidence->review_status === 'pending')<div><x-input-label for="evidence-expiry" :value="'Evidence expiry ('.config('app.timezone').', optional)'" /><x-text-input id="evidence-expiry" type="datetime-local" wire:model="expiresAt" class="mt-1 w-full" aria-describedby="evidence-expiry-error" /><x-input-error id="evidence-expiry-error" :messages="$errors->get('expiresAt')" /></div>@endif
                <div class="flex flex-wrap gap-3"><x-primary-button wire:loading.attr="disabled" wire:confirm="Record this evidence decision? It changes eligibility for future applications.">{{ $selectedEvidence->review_status === 'verified' ? 'Revoke verification' : 'Verify evidence' }}</x-primary-button>@if($selectedEvidence->review_status === 'pending')<x-secondary-button wire:click="decide('rejected')" wire:loading.attr="disabled">Reject with reason</x-secondary-button>@endif</div>
            </form>
            @else<p class="mt-4 text-sm text-content-secondary">This decision is retained. The learner can request reassessment before another verification.</p>@endif
            <details class="mt-4"><summary class="font-semibold text-content">Decision history</summary><ul class="mt-2 space-y-2">@foreach(array_reverse($selectedEvidence->review_history ?? []) as $event)<li class="break-words text-sm text-content-secondary">{{ $event['at'] }} · {{ ucfirst($event['to']) }} · {{ $event['reason'] }}</li>@endforeach</ul></details>
        </x-ui.panel>
    @endif
</div>
