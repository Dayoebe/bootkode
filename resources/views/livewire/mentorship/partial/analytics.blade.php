<div class="space-y-8">
    <!-- Analytics Header -->
    <div
        class="bg-themed-secondary rounded-2xl shadow-lg p-6 border border-themed-primary transition-colors duration-300">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-themed-primary transition-colors duration-300">Performance Analytics</h2>
            <div class="flex items-center space-x-4">
                <select wire:model.live="dateFilter"
                    class="px-4 py-2 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-primary focus:border-transparent bg-themed-secondary text-themed-primary transition-colors duration-300">
                    <option value="this_week">This Week</option>
                    <option value="this_month">This Month</option>
                    <option value="this_quarter">This Quarter</option>
                    <option value="this_year">This Year</option>
                </select>
            </div>
        </div>
        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-gradient-to-br from-accent-primary to-accent-secondary rounded-xl p-6 text-white">
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

            <div
                class="bg-gradient-to-br from-green-500 to-green-600 dark:from-green-600 dark:to-green-700 rounded-xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">Average Rating</p>
                        <p class="text-3xl font-bold">
                            {{ number_format($performanceMetrics['average_session_rating'] ?? 0, 1) }}</p>
                        <p class="text-xs opacity-75 mt-1">Session feedback</p>
                    </div>
                    <div class="text-3xl opacity-80">
                        <i class="fas fa-star"></i>
                    </div>
                </div>
            </div>

            <div
                class="bg-gradient-to-br from-purple-500 to-purple-600 dark:from-purple-600 dark:to-purple-700 rounded-xl p-6 text-white">
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

            <div
                class="bg-gradient-to-br from-orange-500 to-orange-600 dark:from-orange-600 dark:to-orange-700 rounded-xl p-6 text-white">
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
            <div
                class="bg-themed-secondary rounded-2xl shadow-lg p-6 border border-themed-primary transition-colors duration-300">
                <h3 class="text-xl font-bold text-themed-primary mb-6 transition-colors duration-300">Earnings Overview</h3>
                <div class="space-y-4">
                    <div
                        class="flex items-center justify-between p-4 bg-green-100/50 dark:bg-green-900/30 rounded-lg border border-green-200/50 dark:border-green-800 transition-colors duration-300">
                        <div>
                            <p
                                class="text-sm text-green-700 dark:text-green-400 font-medium transition-colors duration-300">
                                This Month</p>
                            <p class="text-2xl font-bold text-green-900 dark:text-green-300 transition-colors duration-300">
                                ${{ number_format($monthlyEarnings, 2) }}</p>
                        </div>
                        <div class="text-green-700 dark:text-green-400 transition-colors duration-300">
                            <i class="fas fa-dollar-sign text-2xl"></i>
                        </div>
                    </div>
                    <div
                        class="flex items-center justify-between p-4 bg-accent-primary/10 border border-accent-primary/30 rounded-lg transition-colors duration-300">
                        <div>
                            <p class="text-sm text-accent-primary font-medium transition-colors duration-300">Total Earnings
                            </p>
                            <p class="text-2xl font-bold text-accent-primary transition-colors duration-300">
                                ${{ number_format($totalEarnings, 2) }}</p>
                        </div>
                        <div class="text-accent-primary transition-colors duration-300">
                            <i class="fas fa-chart-line text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Session Stats -->
        <div
            class="bg-themed-secondary rounded-2xl shadow-lg p-6 border border-themed-primary transition-colors duration-300">
            <h3 class="text-xl font-bold text-themed-primary mb-6 transition-colors duration-300">Session Statistics
            </h3>
            <div class="space-y-4">
                <div
                    class="flex items-center justify-between p-4 bg-themed-tertiary rounded-lg border border-themed-primary transition-colors duration-300">
                    <div>
                        <p class="text-sm text-themed-secondary font-medium transition-colors duration-300">Completed
                            Sessions</p>
                        <p class="text-2xl font-bold text-themed-primary transition-colors duration-300">
                            {{ $completedSessions }}</p>
                    </div>
                    <div class="text-themed-secondary transition-colors duration-300">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                </div>
                <div
                    class="flex items-center justify-between p-4 bg-orange-100/50 dark:bg-orange-900/30 rounded-lg border border-orange-200/50 dark:border-orange-800 transition-colors duration-300">
                    <div>
                        <p
                            class="text-sm text-orange-700 dark:text-orange-400 font-medium transition-colors duration-300">
                            Upcoming Sessions</p>
                        <p
                            class="text-2xl font-bold text-orange-900 dark:text-orange-300 transition-colors duration-300">
                            {{ $upcomingSessions }}</p>
                    </div>
                    <div class="text-orange-700 dark:text-orange-400 transition-colors duration-300">
                        <i class="fas fa-calendar-alt text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Analytics -->
    <div
        class="bg-themed-secondary rounded-2xl shadow-lg p-6 border border-themed-primary transition-colors duration-300">
        <h3 class="text-xl font-bold text-themed-primary mb-6 transition-colors duration-300">Detailed Insights</h3>
        <div class="grid md:grid-cols-3 gap-6">
            <div class="text-center">
                <div
                    class="w-16 h-16 bg-accent-primary/20 text-accent-primary rounded-full flex items-center justify-center mx-auto mb-3 border border-accent-primary/30 transition-colors duration-300">
                    <i class="fas fa-users text-2xl"></i>
                </div>
                <h4 class="font-semibold text-themed-primary transition-colors duration-300">Total Mentees</h4>
                <p class="text-3xl font-bold text-accent-primary transition-colors duration-300">{{ $totalMentees }}</p>
                <p class="text-sm text-themed-secondary transition-colors duration-300">All time</p>
            </div>
            <div class="text-center">
                <div
                    class="w-16 h-16 bg-green-100/50 dark:bg-green-900/30 text-green-700 dark:text-green-400 rounded-full flex items-center justify-center mx-auto mb-3 border border-green-200/50 dark:border-green-800 transition-colors duration-300">
                    <i class="fas fa-star text-2xl"></i>
                </div>
                <h4 class="font-semibold text-themed-primary transition-colors duration-300">Average Rating</h4>
                <p class="text-3xl font-bold text-green-700 dark:text-green-400 transition-colors duration-300">
                    {{ number_format($averageRating, 1) }}</p>
                <p class="text-sm text-themed-secondary transition-colors duration-300">{{ $totalReviews }} reviews</p>
            </div>
            <div class="text-center">
                <div
                    class="w-16 h-16 bg-purple-100/50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 rounded-full flex items-center justify-center mx-auto mb-3 border border-purple-200/50 dark:border-purple-800 transition-colors duration-300">
                    <i class="fas fa-code text-2xl"></i>
                </div>
                <h4 class="font-semibold text-themed-primary transition-colors duration-300">Code Reviews</h4>
                <p class="text-3xl font-bold text-purple-700 dark:text-purple-400 transition-colors duration-300">
                    {{ $pendingCodeReviews }}</p>
                <p class="text-sm text-themed-secondary transition-colors duration-300">Pending</p>
            </div>
        </div>
    </div>
</div>