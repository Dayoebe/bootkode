@php
    $statistics = $statistics ?? [
        'totalInterviews' => 0,
        'completedInterviews' => 0,
        'averageScore' => 0,
        'upcomingInterviews' => 0,
    ];
@endphp

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 rounded-xl p-4 text-white text-center shadow-lg hover:shadow-xl transition-shadow">
        <div class="text-2xl font-bold">{{ $statistics['totalInterviews'] ?? 0 }}</div>
        <div class="text-sm opacity-90">Total</div>
    </div>
    <div class="bg-gradient-to-br from-green-500 to-green-600 dark:from-green-600 dark:to-green-700 rounded-xl p-4 text-white text-center shadow-lg hover:shadow-xl transition-shadow">
        <div class="text-2xl font-bold">{{ $statistics['completedInterviews'] ?? 0 }}</div>
        <div class="text-sm opacity-90">Completed</div>
    </div>
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 dark:from-purple-600 dark:to-purple-700 rounded-xl p-4 text-white text-center shadow-lg hover:shadow-xl transition-shadow">
        <div class="text-2xl font-bold">{{ number_format($statistics['averageScore'] ?? 0, 1) }}</div>
        <div class="text-sm opacity-90">Avg Score</div>
    </div>
    <div class="bg-gradient-to-br from-orange-500 to-orange-600 dark:from-orange-600 dark:to-orange-700 rounded-xl p-4 text-white text-center shadow-lg hover:shadow-xl transition-shadow">
        <div class="text-2xl font-bold">{{ $statistics['upcomingInterviews'] ?? 0 }}</div>
        <div class="text-sm opacity-90">Upcoming</div>
    </div>
</div>