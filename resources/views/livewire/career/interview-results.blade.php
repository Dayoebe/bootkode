<div class="mx-auto max-w-5xl space-y-6 px-4 py-8 sm:px-6">
    <header>
        <p class="text-sm font-medium text-brand">Mock interview</p>
        <h1 class="mt-1 text-2xl font-bold text-content">{{ $interview->title }}</h1>
        <p class="mt-2 text-content-muted">Completed {{ $interview->completed_at?->diffForHumans() }}</p>
    </header>

    @if($interview->overall_score === null)
        <section class="rounded-lg border border-amber-300 bg-amber-50 p-6 text-amber-950" aria-labelledby="assessment-status">
            <h2 id="assessment-status" class="text-lg font-semibold">Assessment unavailable</h2>
            <p class="mt-2">Your responses were saved, but no reviewed rubric or qualified reviewer has assessed them. BootKode does not estimate technical ability, communication or confidence from completion speed.</p>
        </section>
    @else
        <section class="rounded-lg border border-line bg-surface p-6">
            <h2 class="text-lg font-semibold text-content">Reviewed result</h2>
            <p class="mt-2 text-3xl font-bold text-brand">{{ number_format($interview->overall_score, 1) }}%</p>
        </section>
    @endif

    <section class="rounded-lg border border-line bg-surface p-6" aria-labelledby="participation-summary">
        <h2 id="participation-summary" class="text-lg font-semibold text-content">Participation summary</h2>
        <dl class="mt-4 grid gap-4 sm:grid-cols-2">
            <div><dt class="text-sm text-content-muted">Questions answered</dt><dd class="mt-1 font-semibold text-content">{{ count($interview->user_responses ?? []) }} of {{ count($interview->questions ?? []) }}</dd></div>
            <div><dt class="text-sm text-content-muted">Completion rate</dt><dd class="mt-1 font-semibold text-content">{{ number_format($interview->completion_rate ?? 0, 1) }}%</dd></div>
            <div><dt class="text-sm text-content-muted">Average recorded response time</dt><dd class="mt-1 font-semibold text-content">{{ $interview->avg_response_time !== null ? number_format($interview->avg_response_time, 1).' seconds' : 'Not recorded' }}</dd></div>
            <div><dt class="text-sm text-content-muted">Status</dt><dd class="mt-1 font-semibold text-content">{{ $interview->status_label }}</dd></div>
        </dl>
    </section>

    <div class="flex flex-wrap gap-3">
        @if($interview->allow_retakes && $interview->retake_count < $interview->max_retakes)
            <button type="button" wire:click="retakeInterview" class="rounded-lg bg-action px-5 py-3 font-medium text-action-foreground focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand">Retake interview</button>
        @endif
        <button type="button" wire:click="downloadReport" class="rounded-lg border border-line px-5 py-3 font-medium text-content focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand">Download factual report</button>
    </div>
</div>
