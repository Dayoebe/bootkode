@php
    $statistics = $statistics ?? [
        'totalInterviews' => 0,
        'completedInterviews' => 0,
        'averageScore' => null,
        'upcomingInterviews' => 0,
    ];
@endphp

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-brand-ink     rounded-lg p-4 text-white text-center shadow-sm  transition-shadow">
        <div class="text-2xl font-bold">{{ $statistics['totalInterviews'] ?? 0 }}</div>
        <div class="text-sm opacity-90">Total</div>
    </div>
    <div class="bg-brand-ink     rounded-lg p-4 text-white text-center shadow-sm  transition-shadow">
        <div class="text-2xl font-bold">{{ $statistics['completedInterviews'] ?? 0 }}</div>
        <div class="text-sm opacity-90">Completed</div>
    </div>
    <div class="bg-brand-ink     rounded-lg p-4 text-white text-center shadow-sm  transition-shadow">
        <div class="text-2xl font-bold">{{ $statistics['averageScore'] !== null ? number_format($statistics['averageScore'], 1).'%' : '—' }}</div>
        <div class="text-sm opacity-90">Reviewed average</div>
    </div>
    <div class="bg-brand-ink     rounded-lg p-4 text-white text-center shadow-sm  transition-shadow">
        <div class="text-2xl font-bold">{{ $statistics['upcomingInterviews'] ?? 0 }}</div>
        <div class="text-sm opacity-90">Upcoming</div>
    </div>
</div>
