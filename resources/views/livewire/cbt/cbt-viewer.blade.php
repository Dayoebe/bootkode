<div class="mx-auto max-w-5xl space-y-6 p-4 sm:p-6">
    <x-ui.page-header title="Assessment results" description="Your submitted attempts and recorded grades. Written assessments remain pending until reviewed." />
    <x-ui.panel title="Your assessments">
        <ul class="divide-y divide-line">@forelse($userAssessments as $item)<li class="flex flex-wrap items-center justify-between gap-3 py-4"><h2 class="min-w-0 break-words font-semibold text-content">{{ $item->title }}</h2><x-secondary-button wire:click="viewAssessmentDetails({{ $item->id }})">View attempts</x-secondary-button></li>@empty<li><x-ui.empty-state title="No submitted assessments" description="Complete an assessment to see your results here." /></li>@endforelse</ul>
        {{ $userAssessments->links() }}
    </x-ui.panel>
    <x-modal name="assessment-results" model="viewDetails" label="Your assessment attempts">
        <div class="space-y-4 p-4 sm:p-6">
            <h2 class="break-words text-xl font-semibold text-content">{{ $assessment?->title ?? 'Assessment attempts' }}</h2>
            <ul class="divide-y divide-line">@foreach($attempts as $attempt)<li class="flex flex-wrap items-center justify-between gap-3 py-3"><span class="text-content">Attempt {{ $attempt['attempt_number'] }} · {{ ($attempt['pending_review'] ?? false) ? 'Pending review' : $attempt['percentage'].'%' }}</span><x-secondary-button wire:click="viewAttemptDetails({{ $attempt['attempt_number'] }})">View result</x-secondary-button></li>@endforeach</ul>
            @if($selectedAttempt)
                <div role="status" class="rounded-lg border border-line p-4"><h3 class="font-semibold text-content">{{ ($selectedAttempt['pending_review'] ?? false) ? 'Pending instructor review' : ($selectedAttempt['passed'] ? 'Passed' : 'Not passed') }}</h3>@unless($selectedAttempt['pending_review'] ?? false)<p class="mt-2 text-content">{{ $selectedAttempt['total_points'] }} / {{ $selectedAttempt['max_points'] }} points · {{ $selectedAttempt['percentage'] }}%</p>@endunless
                    @include('livewire.cbt.partials.attempt-responses')
                </div>
            @endif
            <x-secondary-button wire:click="closeDetails">Close results</x-secondary-button>
        </div>
    </x-modal>
</div>
