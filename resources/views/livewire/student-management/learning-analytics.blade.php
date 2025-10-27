<div class="min-h-screen bg-themed-primary transition-colors duration-300">
    <!-- Header with Glassmorphism Effect -->
    <div class="sticky top-0 z-50 backdrop-blur-lg bg-themed-secondary/80 border-b border-themed-primary/50 transition-colors duration-300">
        <div class="px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-10 h-10 bg-gradient-to-r from-accent-themed-primary to-accent-themed-secondary rounded-lg flex items-center justify-center shadow-lg">
                            <i class="fas fa-chart-line text-white"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-accent-themed-primary">
                                Learning Analytics
                            </h1>
                            <p class="text-themed-secondary text-sm">Track your learning journey</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <!-- Category Filter -->
                    <select wire:model="selectedCategory" wire:change="updatedSelectedCategory"
                        class="bg-themed-tertiary/50 backdrop-blur border border-themed-secondary/50 text-themed-primary rounded-lg px-3 py-2 text-sm hover:bg-themed-tertiary/70 transition-all duration-200 focus:ring-2 focus:ring-accent-themed-primary">
                        <option value="all">All Categories</option>
                        @if ($categories && count($categories) > 0)
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        @endif
                    </select>

                    <!-- Time Range Filter -->
                    <select wire:model="timeRange" wire:change="updatedTimeRange"
                        class="bg-themed-tertiary/50 backdrop-blur border border-themed-secondary/50 text-themed-primary rounded-lg px-3 py-2 text-sm hover:bg-themed-tertiary/70 transition-all duration-200 focus:ring-2 focus:ring-accent-themed-primary">
                        <option value="week">Last Week</option>
                        <option value="month">Last Month</option>
                        <option value="quarter">Last Quarter</option>
                        <option value="year">Last Year</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="px-4 sm:px-6 lg:px-8 py-8">
        <!-- Stats Overview with Modern Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 gap-4 mb-8">
            <!-- Main Stats -->
            <div class="lg:col-span-1 bg-gradient-to-br from-accent-themed-primary/20 to-accent-themed-secondary/20 backdrop-blur-sm border border-accent-themed-primary/30 rounded-2xl p-6 hover:shadow-2xl hover:shadow-accent-themed-primary/20 transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-accent-themed-primary text-sm font-medium">Courses</p>
                        <p class="text-2xl font-bold text-themed-primary">{{ $totalStats['totalCourses'] ?? 0 }}</p>
                        <p class="text-xs text-accent-themed-primary mt-1">{{ $totalStats['completedCourses'] ?? 0 }} completed</p>
                    </div>
                    <div class="w-12 h-12 bg-accent-themed-primary/30 rounded-xl flex items-center justify-center">
                        <i class="fas fa-book text-accent-themed-primary text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1 bg-gradient-to-br from-green-500/20 to-green-600/20 backdrop-blur-sm border border-green-500/30 rounded-2xl p-6 hover:shadow-2xl hover:shadow-green-500/20 transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-300 text-sm font-medium">Lessons</p>
                        <p class="text-2xl font-bold text-themed-primary">{{ $totalStats['totalLessons'] ?? 0 }}</p>
                        <p class="text-xs text-green-200 mt-1">{{ $totalStats['recentLessons'] ?? 0 }} this {{ strtolower($timeRange) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-green-500/30 rounded-xl flex items-center justify-center">
                        <i class="fas fa-graduation-cap text-green-300 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1 bg-gradient-to-br from-purple-500/20 to-purple-600/20 backdrop-blur-sm border border-purple-500/30 rounded-2xl p-6 hover:shadow-2xl hover:shadow-purple-500/20 transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-300 text-sm font-medium">Avg Score</p>
                        <p class="text-2xl font-bold text-themed-primary">{{ number_format($totalStats['averageScore'] ?? 0, 1) }}%</p>
                        <p class="text-xs text-purple-200 mt-1">Assessment average</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-500/30 rounded-xl flex items-center justify-center">
                        <i class="fas fa-trophy text-purple-300 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1 bg-gradient-to-br from-yellow-500/20 to-orange-500/20 backdrop-blur-sm border border-yellow-500/30 rounded-2xl p-6 hover:shadow-2xl hover:shadow-yellow-500/20 transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-yellow-300 text-sm font-medium">Streak</p>
                        <p class="text-2xl font-bold text-themed-primary">{{ $totalStats['activeStreak'] ?? 0 }}</p>
                        <p class="text-xs text-yellow-200 mt-1">Day streak 🔥</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-500/30 rounded-xl flex items-center justify-center">
                        <i class="fas fa-fire text-yellow-300 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1 bg-gradient-to-br from-pink-500/20 to-rose-500/20 backdrop-blur-sm border border-pink-500/30 rounded-2xl p-6 hover:shadow-2xl hover:shadow-pink-500/20 transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-pink-300 text-sm font-medium">Study Time</p>
                        <p class="text-2xl font-bold text-themed-primary">{{ number_format(($totalStats['totalStudyTime'] ?? 0) / 60, 1) }}h</p>
                        <p class="text-xs text-pink-200 mt-1">Total hours</p>
                    </div>
                    <div class="w-12 h-12 bg-pink-500/30 rounded-xl flex items-center justify-center">
                        <i class="fas fa-clock text-pink-300 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Weekly Goal Progress -->
            <div class="lg:col-span-1 bg-gradient-to-br from-indigo-500/20 to-indigo-600/20 backdrop-blur-sm border border-indigo-500/30 rounded-2xl p-6 hover:shadow-2xl hover:shadow-indigo-500/20 transition-all duration-300">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-indigo-300 text-sm font-medium">Weekly Goal</p>
                        <p class="text-2xl font-bold text-themed-primary">{{ number_format($totalStats['weeklyGoalProgress'] ?? 0, 0) }}%</p>
                        <p class="text-xs text-indigo-200 mt-1">{{ $weeklyGoal }} lessons/week</p>
                    </div>
                    <div class="w-12 h-12 bg-indigo-500/30 rounded-xl flex items-center justify-center">
                        <i class="fas fa-target text-indigo-300 text-xl"></i>
                    </div>
                </div>
                <div class="mt-3 w-full bg-indigo-900/50 rounded-full h-2">
                    <div class="bg-gradient-to-r from-indigo-400 to-indigo-500 h-2 rounded-full transition-all duration-500"
                        style="width: {{ min(100, $totalStats['weeklyGoalProgress'] ?? 0) }}%"></div>
                </div>
            </div>
        </div>

        <!-- Achievements Section -->
        @if (!empty($achievements) && is_array($achievements) && count($achievements) > 0)
            <div class="mb-8">
                <div class="bg-themed-secondary/50 backdrop-blur-sm border border-themed-secondary/50 rounded-2xl p-6 transition-colors duration-300">
                    <h3 class="text-lg font-semibold text-themed-primary mb-4 flex items-center">
                        <span class="w-2 h-2 bg-yellow-400 rounded-full mr-2"></span>
                        Recent Achievements
                    </h3>
                    <div class="flex flex-wrap gap-3">
                        @foreach ($achievements as $achievement)
                            <div class="bg-gradient-to-r from-yellow-500/20 to-orange-500/20 border border-yellow-500/30 rounded-xl p-3 flex items-center space-x-3 hover:shadow-lg hover:shadow-yellow-500/20 transition-all duration-300">
                                <span class="text-2xl">{{ $achievement['icon'] ?? '🏆' }}</span>
                                <div>
                                    <p class="text-themed-primary font-medium text-sm">{{ $achievement['title'] ?? 'Achievement' }}</p>
                                    <p class="text-yellow-200 text-xs">{{ $achievement['description'] ?? 'Great job!' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Predictions Section -->
        @if (!empty($predictions) && is_array($predictions))
            <div class="mb-8">
                <div class="bg-themed-secondary/50 backdrop-blur-sm border border-themed-secondary/50 rounded-2xl p-6 transition-colors duration-300">
                    <h3 class="text-lg font-semibold text-themed-primary mb-4 flex items-center">
                        <span class="w-2 h-2 bg-purple-400 rounded-full mr-2"></span>
                        AI Predictions & Recommendations
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-purple-500/10 border border-purple-500/30 rounded-xl p-4 transition-colors duration-300">
                            <div class="flex items-center space-x-2 mb-2">
                                <i class="fas fa-crystal-ball text-purple-400"></i>
                                <p class="text-purple-300 font-medium">Next Month</p>
                            </div>
                            <p class="text-themed-primary text-lg font-bold">{{ $predictions['projectedLessonsNextMonth'] ?? 0 }} lessons</p>
                            <p class="text-purple-200 text-xs">Projected completion</p>
                        </div>

                        @if (isset($predictions['estimatedCompletionWeeks']) && $predictions['estimatedCompletionWeeks'])
                            <div class="bg-accent-themed-primary/10 border border-accent-themed-primary/30 rounded-xl p-4 transition-colors duration-300">
                                <div class="flex items-center space-x-2 mb-2">
                                    <i class="fas fa-calendar-alt text-accent-themed-primary"></i>
                                    <p class="text-accent-themed-primary font-medium">Course Completion</p>
                                </div>
                                <p class="text-themed-primary text-lg font-bold">{{ $predictions['estimatedCompletionWeeks'] }} weeks</p>
                                <p class="text-accent-themed-primary text-xs">Estimated time</p>
                            </div>
                        @endif

                        <div class="bg-green-500/10 border border-green-500/30 rounded-xl p-4 transition-colors duration-300">
                            <div class="flex items-center space-x-2 mb-2">
                                <i class="fas fa-lightbulb text-green-400"></i>
                                <p class="text-green-300 font-medium">Recommendation</p>
                            </div>
                            <p class="text-themed-primary text-lg font-bold">{{ $predictions['recommendedStudyTime'] ?? 'Keep it up!' }}</p>
                            <p class="text-green-200 text-xs">Optimal study time</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Charts Section -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 mb-8">
            <!-- Activity Chart -->
            <div class="bg-themed-secondary/50 backdrop-blur-sm border border-themed-secondary/50 rounded-2xl p-6 hover:shadow-2xl hover:shadow-accent-themed-primary/10 transition-all duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-themed-primary flex items-center">
                        <span class="w-2 h-2 bg-accent-themed-primary rounded-full mr-2"></span>
                        Learning Activity
                    </h3>
                    <div class="flex items-center space-x-2 text-sm text-themed-secondary">
                        <span class="w-3 h-3 bg-accent-themed-primary rounded-full"></span>
                        <span>Lessons</span>
                        <span class="w-3 h-3 bg-green-500 rounded-full ml-2"></span>
                        <span>Study Time</span>
                    </div>
                </div>
                <div class="h-80">
                    <canvas x-data="chartComponent('activity', @js($activityData))" wire:ignore class="w-full h-full" style="display: block;"></canvas>
                </div>
            </div>

            <!-- Progress Chart -->
            <div class="bg-themed-secondary/50 backdrop-blur-sm border border-themed-secondary/50 rounded-2xl p-6 hover:shadow-2xl hover:shadow-green-500/10 transition-all duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-themed-primary flex items-center">
                        <span class="w-2 h-2 bg-green-400 rounded-full mr-2"></span>
                        Course Progress
                    </h3>
                    <div class="text-sm text-themed-secondary">Top 10 Courses</div>
                </div>
                <div class="h-80">
                    <canvas x-data="chartComponent('progress', @js($progressData))" wire:ignore class="w-full h-full" style="display: block;"></canvas>
                </div>
            </div>

            <!-- Performance Chart -->
            <div class="bg-themed-secondary/50 backdrop-blur-sm border border-themed-secondary/50 rounded-2xl p-6 hover:shadow-2xl hover:shadow-purple-500/10 transition-all duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-themed-primary flex items-center">
                        <span class="w-2 h-2 bg-purple-400 rounded-full mr-2"></span>
                        Assessment Performance
                    </h3>
                    <div class="text-sm text-themed-secondary">Average Scores</div>
                </div>
                <div class="h-80">
                    <canvas x-data="chartComponent('performance', @js($performanceData))" wire:ignore class="w-full h-full" style="display: block;"></canvas>
                </div>
            </div>

            <!-- Weekly Progress Chart -->
            <div class="bg-themed-secondary/50 backdrop-blur-sm border border-themed-secondary/50 rounded-2xl p-6 hover:shadow-2xl hover:shadow-indigo-500/10 transition-all duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-themed-primary flex items-center">
                        <span class="w-2 h-2 bg-indigo-400 rounded-full mr-2"></span>
                        Weekly Progress
                    </h3>
                    <div class="text-sm text-themed-secondary">Goal: {{ $weeklyGoal }} lessons/week</div>
                </div>
                <div class="h-80">
                    <canvas x-data="chartComponent('weekly', @js($weeklyProgressData))" wire:ignore class="w-full h-full" style="display: block;"></canvas>
                </div>
            </div>
        </div>

        <!-- Category Distribution Chart -->
        <div class="mb-8">
            <div class="bg-themed-secondary/50 backdrop-blur-sm border border-themed-secondary/50 rounded-2xl p-6 hover:shadow-2xl hover:shadow-yellow-500/10 transition-all duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-themed-primary flex items-center">
                        <span class="w-2 h-2 bg-yellow-400 rounded-full mr-2"></span>
                        Learning Distribution by Category
                    </h3>
                    <div class="text-sm text-themed-secondary">Time Spent (Hours)</div>
                </div>
                <div class="h-96">
                    <canvas x-data="chartComponent('category', @js($categoryDistributionData))" wire:ignore class="w-full h-full" style="display: block;"></canvas>
                </div>
            </div>
        </div>

        <!-- Course Progress Table -->
        @if ($recentCourses && count($recentCourses) > 0)
            <div class="bg-themed-secondary/50 backdrop-blur-sm border border-themed-secondary/50 rounded-2xl overflow-hidden transition-colors duration-300">
                <div class="p-6 border-b border-themed-secondary/50">
                    <h3 class="text-lg font-semibold text-themed-primary flex items-center">
                        <span class="w-2 h-2 bg-accent-themed-primary rounded-full mr-2"></span>
                        Recent Course Activity
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-themed-secondary/50">
                        <thead class="bg-themed-tertiary/30">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-themed-secondary uppercase tracking-wider">Course</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-themed-secondary uppercase tracking-wider">Progress</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-themed-secondary uppercase tracking-wider">Last Accessed</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-themed-secondary uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-themed-secondary uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-themed-secondary/30">
                            @foreach ($recentCourses as $course)
                                <tr class="hover:bg-themed-tertiary/20 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gradient-to-r from-accent-themed-primary to-accent-themed-secondary rounded-lg flex items-center justify-center mr-3 shadow-md">
                                                <i class="fas fa-book text-white text-sm"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-themed-primary">{{ $course->title ?? 'Course Title' }}</p>
                                                <p class="text-xs text-themed-secondary">{{ ucfirst($course->difficulty_level ?? 'beginner') }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="w-full bg-themed-tertiary/50 rounded-full h-2 mb-1">
                                            <div class="bg-gradient-to-r from-accent-themed-primary to-accent-themed-secondary h-2 rounded-full transition-all duration-500"
                                                style="width: {{ $course->pivot->progress ?? 0 }}%"></div>
                                        </div>
                                        <span class="text-xs text-themed-secondary">{{ $course->pivot->progress ?? 0 }}%</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-themed-primary">
                                        @if ($course->pivot->updated_at)
                                            {{ $course->pivot->updated_at->format('M d, Y') }}
                                        @else
                                            Never
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $progress = $course->pivot->progress ?? 0;
                                        @endphp
                                        @if ($progress == 100)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-500/20 text-green-400 border border-green-500/30">
                                                <span class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1"></span>
                                                Completed
                                            </span>
                                        @elseif($progress > 0)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-accent-themed-primary/20 text-accent-themed-primary border border-accent-themed-primary/30">
                                                <span class="w-1.5 h-1.5 bg-accent-themed-primary rounded-full mr-1"></span>
                                                In Progress
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-themed-tertiary/50 text-themed-secondary border border-themed-secondary/30">
                                                <span class="w-1.5 h-1.5 bg-themed-secondary rounded-full mr-1"></span>
                                                Not Started
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if ($course->slug)
                                            <a href="{{ route('course.view', $course->slug) }}"
                                                class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-medium bg-accent-themed-primary/20 text-accent-themed-primary border border-accent-themed-primary/30 hover:bg-accent-themed-primary/30 hover:text-accent-themed-primary transition-all duration-200">
                                                <i class="fas fa-play mr-1 text-xs"></i>
                                                {{ ($course->pivot->progress ?? 0) > 0 ? 'Continue' : 'Start' }}
                                            </a>
                                        @else
                                            <span class="text-themed-tertiary text-xs">Unavailable</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-themed-secondary/50 backdrop-blur-sm border border-themed-secondary/50 rounded-2xl p-12 text-center transition-colors duration-300">
                <div class="w-20 h-20 bg-themed-tertiary/50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-chart-line text-themed-tertiary text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-themed-primary mb-2">No Learning Data Yet</h3>
                <p class="text-themed-secondary mb-6">Start learning to see your analytics and track your progress here.</p>
                <a href="{{ route('student.course-catalog') }}"
                    class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium bg-accent-themed-primary/20 text-accent-themed-primary border border-accent-themed-primary/30 hover:bg-accent-themed-primary/30 hover:text-accent-themed-primary transition-all duration-200">
                    <i class="fas fa-book-open mr-2"></i>
                    Browse Courses
                </a>
            </div>
        @endif
    </div>
</div>

@push('styles')
    <style>
        /* Enhanced Learning Analytics Styles with Theme Support */
        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }

        /* Animated Progress Bars */
        @keyframes progressAnimation {
            0% {
                width: 0%;
            }

            100% {
                width: var(--progress-width);
            }
        }

        .animated-progress {
            animation: progressAnimation 2s ease-in-out forwards;
        }

        /* Card Hover Effects */
        .stat-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        /* Chart Container Enhancements */
        .chart-container {
            position: relative;
            background: rgba(17, 24, 39, 0.6);
            border-radius: 16px;
            padding: 24px;
            border: 1px solid rgba(75, 85, 99, 0.3);
            transition: all 0.3s ease;
        }

        .chart-container:hover {
            border-color: rgba(59, 130, 246, 0.5);
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.1);
        }

        /* Loading States */
        .loading-skeleton {
            background: linear-gradient(90deg,
                    rgba(75, 85, 99, 0.3) 25%,
                    rgba(107, 114, 128, 0.5) 50%,
                    rgba(75, 85, 99, 0.3) 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        /* Custom Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(55, 65, 81, 0.5);
            border-radius: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(59, 130, 246, 0.6);
            border-radius: 4px;
            transition: background 0.3s ease;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(59, 130, 246, 0.8);
        }

        /* Responsive Grid Animations */
        .grid-item {
            animation: slideInUp 0.6s ease-out;
            animation-fill-mode: both;
        }

        @keyframes slideInUp {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Error state styles */
        .error-state {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 12px;
            padding: 24px;
            text-align: center;
        }

        .error-state .icon {
            width: 48px;
            height: 48px;
            background: rgba(239, 68, 68, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }

        /* Smooth theme transitions */
        * {
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }

        /* Gradient text support */
        .text-gradient {
            background: linear-gradient(135deg, rgb(var(--accent-primary)), rgb(var(--accent-secondary)));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Define chartComponent BEFORE Alpine.js initializes
        document.addEventListener('alpine:init', () => {
            Alpine.data('chartComponent', (type, data) => ({
                chart: null,
                chartType: type,
                chartData: data,

                init() {
                    // Use $nextTick to ensure DOM is ready
                    this.$nextTick(() => {
                        this.initChart();
                    });

                    // Listen for Livewire updates
                    if (window.Livewire) {
                        window.Livewire.on('refreshCharts', () => {
                            this.$nextTick(() => {
                                this.initChart();
                            });
                        });
                    }
                },

                initChart() {
                    // Validate canvas element
                    if (!this.$el || !this.$el.getContext) {
                        console.warn('Chart canvas element not found or invalid');
                        return;
                    }

                    // Validate chart data
                    if (!this.chartData || !this.chartData.labels || !this.chartData.datasets) {
                        console.warn('Invalid chart data provided for', this.chartType);
                        this.renderEmptyChart();
                        return;
                    }

                    try {
                        const ctx = this.$el.getContext('2d');
                        if (!ctx) {
                            console.warn('Could not get 2D context from canvas');
                            return;
                        }

                        // Destroy existing chart if it exists
                        if (this.chart) {
                            this.chart.destroy();
                        }

                        const commonOptions = {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top',
                                    labels: {
                                        usePointStyle: true,
                                        color: 'rgb(var(--text-primary))',
                                        font: {
                                            size: 12
                                        },
                                        filter: function(legendItem, chartData) {
                                            // Hide empty datasets from legend
                                            const dataset = chartData.datasets[legendItem.datasetIndex];
                                            return dataset && dataset.data && dataset.data.some(value => value > 0);
                                        }
                                    }
                                },
                                tooltip: {
                                    backgroundColor: 'rgb(var(--bg-secondary))',
                                    titleColor: 'rgb(var(--text-primary))',
                                    bodyColor: 'rgb(var(--text-secondary))',
                                    borderColor: 'rgb(var(--border-primary))',
                                    borderWidth: 1,
                                    cornerRadius: 8,
                                    displayColors: true,
                                    callbacks: {
                                        label: function(context) {
                                            let label = context.dataset.label || '';
                                            if (label) {
                                                label += ': ';
                                            }
                                            if (context.parsed !== null) {
                                                if (context.dataset.label?.includes('Time') || context.dataset.label?.includes('hours')) {
                                                    label += context.parsed + 'h';
                                                } else if (context.dataset.label?.includes('%')) {
                                                    label += context.parsed + '%';
                                                } else {
                                                    label += context.parsed;
                                                }
                                            }
                                            return label;
                                        }
                                    }
                                }
                            },
                            scales: this.getScalesConfig(),
                            animation: {
                                duration: 1000,
                                easing: 'easeInOutQuart'
                            },
                            onHover: (event, elements) => {
                                event.native.target.style.cursor = elements.length > 0 ? 'pointer' : 'default';
                            }
                        };

                        this.chart = new Chart(ctx, {
                            type: this.getChartType(),
                            data: this.chartData,
                            options: commonOptions
                        });

                        console.log('Chart initialized successfully:', this.chartType);

                    } catch (error) {
                        console.error('Error initializing chart:', error);
                        this.renderErrorState();
                    }
                },

                renderEmptyChart() {
                    if (!this.$el || !this.$el.getContext) return;
                    const ctx = this.$el.getContext('2d');
                    ctx.fillStyle = 'rgb(var(--text-tertiary))';
                    ctx.font = '14px system-ui';
                    ctx.textAlign = 'center';
                    ctx.fillText('No data available', this.$el.width / 2, this.$el.height / 2);
                },

                renderErrorState() {
                    if (!this.$el || !this.$el.getContext) return;
                    const ctx = this.$el.getContext('2d');
                    ctx.fillStyle = '#ef4444';
                    ctx.font = '14px system-ui';
                    ctx.textAlign = 'center';
                    ctx.fillText('Error loading chart', this.$el.width / 2, this.$el.height / 2);
                },

                getChartType() {
                    const typeMap = {
                        'activity': 'line',
                        'progress': 'bar',
                        'performance': 'bar',
                        'weekly': 'bar',
                        'category': 'doughnut'
                    };
                    return typeMap[this.chartType] || 'bar';
                },

                getScalesConfig() {
                    if (['category'].includes(this.chartType)) {
                        return {}; // No scales for pie/doughnut charts
                    }

                    const baseScale = {
                        grid: {
                            color: 'rgba(var(--border-primary), 0.3)',
                            borderColor: 'rgba(var(--border-secondary), 0.5)'
                        },
                        ticks: {
                            color: 'rgb(var(--text-secondary))',
                            font: {
                                size: 11
                            }
                        }
                    };

                    if (this.chartType === 'activity') {
                        return {
                            x: { ...baseScale },
                            y: {
                                ...baseScale,
                                type: 'linear',
                                display: true,
                                position: 'left',
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Lessons',
                                    color: 'rgb(var(--text-secondary))'
                                }
                            },
                            y1: {
                                ...baseScale,
                                type: 'linear',
                                display: true,
                                position: 'right',
                                beginAtZero: true,
                                grid: {
                                    drawOnChartArea: false
                                },
                                title: {
                                    display: true,
                                    text: 'Hours',
                                    color: 'rgb(var(--text-secondary))'
                                }
                            }
                        };
                    }

                    if (this.chartType === 'performance') {
                        return {
                            x: { ...baseScale },
                            y: {
                                ...baseScale,
                                beginAtZero: true,
                                max: 100,
                                ticks: {
                                    ...baseScale.ticks,
                                    callback: function(value) {
                                        return value + '%';
                                    }
                                }
                            }
                        };
                    }

                    return {
                        x: { ...baseScale },
                        y: { ...baseScale, beginAtZero: true }
                    };
                }
            }));
        });

        // Error handling
        window.addEventListener('error', function(e) {
            if (e.message.includes('Chart') || e.message.includes('canvas')) {
                console.warn('Chart rendering issue detected:', e.message);
            }
        });

        // Livewire integration
        document.addEventListener('livewire:navigated', function() {
            if (typeof Alpine !== 'undefined') {
                Alpine.nextTick(() => {
                    // Trigger chart refresh if needed
                });
            }
        });
    </script>
@endpush