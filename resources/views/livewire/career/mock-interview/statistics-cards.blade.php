<div class="grid grid-cols-2 lg:grid-cols-6 gap-4">
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 text-white text-center">
        <div class="text-2xl font-bold">{{ $totalInterviews }}</div>
        <div class="text-sm opacity-90">Total</div>
    </div>
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 text-white text-center">
        <div class="text-2xl font-bold">{{ $completedInterviews }}</div>
        <div class="text-sm opacity-90">Completed</div>
    </div>
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 text-white text-center">
        <div class="text-2xl font-bold">{{ number_format($averageScore, 1) }}</div>
        <div class="text-sm opacity-90">Avg Score</div>
    </div>
    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-4 text-white text-center">
        <div class="text-2xl font-bold">{{ $upcomingInterviews }}</div>
        <div class="text-sm opacity-90">Upcoming</div>
    </div>
    <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl p-4 text-white text-center">
        <div class="text-2xl font-bold">{{ $streakCount }}</div>
        <div class="text-sm opacity-90">Streak</div>
    </div>
    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl p-4 text-white text-center">
        <div class="text-2xl font-bold">{{ $improvementRate }}%</div>
        <div class="text-sm opacity-90">Improvement</div>
    </div>
</div>