{{-- resources/views/livewire/career/mock-interview/partials/analytics-tab.blade.php --}}

<div class="space-y-8">
    <!-- Performance Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-themed-secondary rounded-2xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="bg-blue-100 p-3 rounded-full">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-themed-secondary">Overall Performance</p>
                    <p class="text-2xl font-bold text-themed-primary">{{ number_format($averageScore, 1) }}%</p>
                </div>
            </div>
        </div>

        <div class="bg-themed-secondary rounded-2xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="bg-green-100 p-3 rounded-full">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-themed-secondary">Improvement Rate</p>
                    <p class="text-2xl font-bold text-themed-primary">+{{ $improvementRate }}%</p>
                </div>
            </div>
        </div>

        <div class="bg-themed-secondary rounded-2xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="bg-purple-100 p-3 rounded-full">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-themed-secondary">Avg Response Time</p>
                    <p class="text-2xl font-bold text-themed-primary">2.5m</p>
                </div>
            </div>
        </div>

        <div class="bg-themed-secondary rounded-2xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="bg-orange-100 p-3 rounded-full">
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-themed-secondary">Current Streak</p>
                    <p class="text-2xl font-bold text-themed-primary">{{ $streakCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Score Breakdown -->
        <div class="bg-themed-secondary rounded-2xl shadow-lg p-6">
            <h3 class="text-xl font-bold text-themed-primary mb-6">Score Breakdown</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-themed-secondary">Technical Skills</span>
                    <div class="flex items-center">
                        <div class="w-32 bg-themed-tertiary rounded-full h-2 mr-3">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: 85%"></div>
                        </div>
                        <span class="font-semibold text-themed-primary">85%</span>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-themed-secondary">Communication</span>
                    <div class="flex items-center">
                        <div class="w-32 bg-themed-tertiary rounded-full h-2 mr-3">
                            <div class="bg-green-600 h-2 rounded-full" style="width: 78%"></div>
                        </div>
                        <span class="font-semibold text-themed-primary">78%</span>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-themed-secondary">Problem Solving</span>
                    <div class="flex items-center">
                        <div class="w-32 bg-themed-tertiary rounded-full h-2 mr-3">
                            <div class="bg-purple-600 h-2 rounded-full" style="width: 92%"></div>
                        </div>
                        <span class="font-semibold text-themed-primary">92%</span>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-themed-secondary">Confidence</span>
                    <div class="flex items-center">
                        <div class="w-32 bg-themed-tertiary rounded-full h-2 mr-3">
                            <div class="bg-orange-600 h-2 rounded-full" style="width: 72%"></div>
                        </div>
                        <span class="font-semibold text-themed-primary">72%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Interview Type Performance -->
        <div class="bg-themed-secondary rounded-2xl shadow-lg p-6">
            <h3 class="text-xl font-bold text-themed-primary mb-6">Performance by Interview Type</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-themed-secondary">
                        Technical ({{ $mockInterviews->where('type', 'technical')->where('status', 'completed')->count() }})
                    </span>
                    <span class="font-semibold text-blue-600">87%</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-themed-secondary">
                        Behavioral ({{ $mockInterviews->where('type', 'behavioral')->where('status', 'completed')->count() }})
                    </span>
                    <span class="font-semibold text-green-600">82%</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-themed-secondary">
                        System Design ({{ $mockInterviews->where('type', 'system_design')->where('status', 'completed')->count() }})
                    </span>
                    <span class="font-semibold text-purple-600">75%</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-themed-secondary">
                        Coding ({{ $mockInterviews->where('type', 'coding')->where('status', 'completed')->count() }})
                    </span>
                    <span class="font-semibold text-orange-600">89%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Premium Analytics Teaser (if not premium) -->
    @if(!$aiAnalysisEnabled)
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl shadow-lg p-8 text-white">
            <div class="flex flex-col lg:flex-row items-center justify-between gap-6">
                <div class="flex-1">
                    <h3 class="text-2xl font-bold mb-2">Unlock Advanced Analytics</h3>
                    <p class="text-blue-100 mb-4">Get AI-powered insights, speech analysis, and detailed performance metrics</p>
                    <ul class="space-y-2 mb-4">
                        <li class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            AI-Powered Feedback
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Speech & Emotion Analysis
                        </li>
                        <li class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Detailed Performance Metrics
                        </li>
                    </ul>
                </div>
                <button class="bg-white text-blue-600 px-6 py-3 rounded-lg hover:bg-blue-50 transition-colors font-semibold whitespace-nowrap">
                    <i class="fas fa-crown mr-2"></i> Upgrade Now
                </button>
            </div>
        </div>
    @else
        <!-- Premium AI Analytics Section -->
        <div class="bg-themed-secondary rounded-2xl shadow-lg p-8">
            <h3 class="text-xl font-bold text-themed-primary mb-6">AI-Powered Insights</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center">
                    <div class="bg-blue-100 p-4 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h4 class="font-semibold text-themed-primary mb-2">Speech Analysis</h4>
                    <p class="text-sm text-themed-secondary">Your speaking pace and clarity have improved by 15% in the last month.</p>
                </div>

                <div class="text-center">
                    <div class="bg-green-100 p-4 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m-6-4h8m-6-4h8m-6-8h8" />
                        </svg>
                    </div>
                    <h4 class="font-semibold text-themed-primary mb-2">Emotion Tracking</h4>
                    <p class="text-sm text-themed-secondary">Confidence levels are consistently high, with reduced anxiety indicators.</p>
                </div>

                <div class="text-center">
                    <div class="bg-purple-100 p-4 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </div>
                    <h4 class="font-semibold text-themed-primary mb-2">Eye Contact</h4>
                    <p class="text-sm text-themed-secondary">Maintaining good eye contact 78% of the time during video interviews.</p>
                </div>
            </div>
        </div>
    @endif
</div>