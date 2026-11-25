@php
    $completedInterviews = $this->mockInterviews->where('status', 'completed');
    $assessedInterviews = $completedInterviews->whereNotNull('overall_score');
    $averageCompletion = $completedInterviews->avg('completion_rate');
@endphp

<div class="space-y-6">
    <section class="rounded-lg border border-line bg-surface p-6" aria-labelledby="practice-summary-title">
        <h2 id="practice-summary-title" class="text-xl font-semibold text-content">Practice summary</h2>
        <p class="mt-1 text-content-muted">Factual participation data from your mock interviews.</p>
        <dl class="mt-5 grid gap-4 sm:grid-cols-3">
            <div class="rounded-lg bg-surface-muted p-4">
                <dt class="text-sm text-content-muted">Completed interviews</dt>
                <dd class="mt-1 text-2xl font-bold text-content">{{ $completedInterviews->count() }}</dd>
            </div>
            <div class="rounded-lg bg-surface-muted p-4">
                <dt class="text-sm text-content-muted">Average completion</dt>
                <dd class="mt-1 text-2xl font-bold text-content">{{ $averageCompletion !== null ? number_format($averageCompletion, 1).'%' : 'Unavailable' }}</dd>
            </div>
            <div class="rounded-lg bg-surface-muted p-4">
                <dt class="text-sm text-content-muted">Reviewed assessments</dt>
                <dd class="mt-1 text-2xl font-bold text-content">{{ $assessedInterviews->count() }}</dd>
            </div>
        </dl>
    </section>

    @if($assessedInterviews->isEmpty())
        <section class="rounded-lg border border-amber-300 bg-amber-50 p-6 text-amber-950" aria-labelledby="score-analytics-status">
            <h2 id="score-analytics-status" class="font-semibold">Performance analytics are unavailable</h2>
            <p class="mt-2 text-sm">No completed interview has been assessed with a reviewed rubric. Communication, confidence, technical and improvement scores are not estimated from participation.</p>
        </section>
    @else
        <section class="rounded-lg border border-line bg-surface p-6" aria-labelledby="reviewed-score-summary">
            <h2 id="reviewed-score-summary" class="font-semibold text-content">Reviewed assessment summary</h2>
            <p class="mt-3 text-3xl font-bold text-brand">{{ number_format($assessedInterviews->avg('overall_score'), 1) }}%</p>
            <p class="mt-1 text-sm text-content-muted">Average across {{ $assessedInterviews->count() }} reviewed {{ Str::plural('assessment', $assessedInterviews->count()) }}.</p>
        </section>
    @endif

    <section class="rounded-lg border border-line bg-surface p-6" aria-labelledby="completed-interviews-title">
        <h2 id="completed-interviews-title" class="text-lg font-semibold text-content">Completed interviews</h2>
        <div class="mt-4 space-y-3">
            @forelse($completedInterviews->take(10) as $interview)
                <div class="flex flex-col justify-between gap-2 rounded-lg bg-surface-muted p-4 sm:flex-row sm:items-center">
                    <div>
                        <p class="font-medium text-content">{{ $interview->title }}</p>
                        <p class="text-sm text-content-muted">{{ $interview->completed_at?->format('M d, Y H:i') ?? 'Completion time unavailable' }}</p>
                    </div>
                    <p class="text-sm font-medium text-content">
                        {{ $interview->overall_score !== null ? number_format($interview->overall_score, 1).'% assessed' : 'Not assessed' }}
                    </p>
                </div>
            @empty
                <p class="py-6 text-center text-content-muted">Complete a mock interview to see participation data.</p>
            @endforelse
        </div>
    </section>
</div>
