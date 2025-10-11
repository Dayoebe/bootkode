{{-- FILE: resources/views/livewire/mentorship/partial/analytics.blade.php --}}

<div class="space-y-8">
    <!-- Analytics Header -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Performance Analytics</h2>
            <div class="flex items-center space-x-4">
                <select wire:model.live="dateFilter"
                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="this_week">This Week</option>
                    <option value="this_month">This Month</option>
                    <option value="this_quarter">This Quarter</option>
                    <option value="this_year">This Year</option>
                </select>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 rounded-xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">Sessions This Period</p>
                        <p class="text-3xl font-bold">{{ $performanceMetrics['sessions_conducted'] ?? 0 }}</p>
                        <p class="text-xs opacity-75 mt-1">{{ ucfirst(str_replace('_', ' ', $dateFilter)) }}</p>
                    </div>
                    <div class="text-3xl opacity-80">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-500 to-green-600 dark:from-green-600 dark:to-green-700 rounded-xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">Average Rating</p>
                        <p class="text-3xl font-bold">{{ number_format($performanceMetrics['average_session_rating'] ?? 0, 1) }}</p>
                        <p class="text-xs opacity-75 mt-1">Session feedback</p>
                    </div>
                    <div class="text-3xl opacity-80">
                        <i class="fas fa-star"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-500 to-purple-600 dark:from-purple-600 dark:to-purple-700 rounded-xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">Code Reviews</p>
                        <p class="text-3xl font-bold">{{ $performanceMetrics['code_reviews_completed'] ?? 0 }}</p>
                        <p class="text-xs opacity-75 mt-1">Completed reviews</p>
                    </div>
                    <div class="text-3xl opacity-80">
                        <i class="fas fa-code"></i>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-orange-500 to-orange-600 dark:from-orange-600 dark:to-orange-700 rounded-xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">Response Time</p>
                        <p class="text-3xl font-bold">{{ $performanceMetrics['response_time_hours'] ?? 0 }}h</p>
                        <p class="text-xs opacity-75 mt-1">Average response</p>
                    </div>
                    <div class="text-3xl opacity-80">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Charts -->
    <div class="grid lg:grid-cols-2 gap-8">
        <!-- Earnings Chart -->
        @if(auth()->user()->mentorProfile && auth()->user()->mentorProfile->hourly_rate > 0)
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Earnings Overview</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                        <div>
                            <p class="text-sm text-green-600 dark:text-green-400 font-medium">This Month</p>
                            <p class="text-2xl font-bold text-green-800 dark:text-green-300">${{ number_format($monthlyEarnings, 2) }}</p>
                        </div>
                        <div class="text-green-600 dark:text-green-400">
                            <i class="fas fa-dollar-sign text-2xl"></i>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                        <div>
                            <p class="text-sm text-blue-600 dark:text-blue-400 font-medium">Total Earnings</p>
                            <p class="text-2xl font-bold text-blue-800 dark:text-blue-300">${{ number_format($totalEarnings, 2) }}</p>
                        </div>
                        <div class="text-blue-600 dark:text-blue-400">
                            <i class="fas fa-chart-line text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Session Stats -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Session Statistics</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">Completed Sessions</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $completedSessions }}</p>
                    </div>
                    <div class="text-gray-600 dark:text-gray-400">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                </div>
                <div class="flex items-center justify-between p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg border border-orange-200 dark:border-orange-800">
                    <div>
                        <p class="text-sm text-orange-600 dark:text-orange-400 font-medium">Upcoming Sessions</p>
                        <p class="text-2xl font-bold text-orange-800 dark:text-orange-300">{{ $upcomingSessions }}</p>
                    </div>
                    <div class="text-orange-600 dark:text-orange-400">
                        <i class="fas fa-calendar-alt text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Analytics -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Detailed Insights</h3>
        <div class="grid md:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center mx-auto mb-3 border border-blue-200 dark:border-blue-800">
                    <i class="fas fa-users text-2xl"></i>
                </div>
                <h4 class="font-semibold text-gray-900 dark:text-white">Total Mentees</h4>
                <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $totalMentees }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">All time</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-full flex items-center justify-center mx-auto mb-3 border border-green-200 dark:border-green-800">
                    <i class="fas fa-star text-2xl"></i>
                </div>
                <h4 class="font-semibold text-gray-900 dark:text-white">Average Rating</h4>
                <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ number_format($averageRating, 1) }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $totalReviews }} reviews</p>
            </div>
            <div class="text-center">
                <div class="w-16 h-16 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-full flex items-center justify-center mx-auto mb-3 border border-purple-200 dark:border-purple-800">
                    <i class="fas fa-code text-2xl"></i>
                </div>
                <h4 class="font-semibold text-gray-900 dark:text-white">Code Reviews</h4>
                <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $pendingCodeReviews }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400">Pending</p>
            </div>
        </div>
    </div>
</div>