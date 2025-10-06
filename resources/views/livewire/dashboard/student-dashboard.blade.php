<div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-300 p-4 md:p-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 relative overflow-hidden transition-colors duration-300">
            <!-- Background decoration -->
            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-100 dark:bg-blue-900/20 rounded-full -translate-y-8 translate-x-8 opacity-50 transition-colors duration-300"></div>

            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center space-y-4 lg:space-y-0 relative">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center transition-colors duration-300">
                        <i class="fas fa-graduation-cap text-blue-600 dark:text-blue-400 mr-3"></i>
                        Welcome back, {{ auth()->user()->name }}!
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1 transition-colors duration-300">Ready to continue your learning journey?</p>

                    @if($this->quickStats['study_streak'] > 0)
                        <div class="mt-3 inline-flex items-center bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300 px-3 py-1 rounded-full text-sm font-medium transition-colors duration-300">
                            <i class="fas fa-fire mr-2"></i>
                            {{ $this->quickStats['study_streak'] }} day study streak!
                        </div>
                    @endif
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <!-- Level Badge -->
                    <div class="bg-gradient-to-r from-purple-600 to-blue-600 dark:from-purple-500 dark:to-blue-500 text-white px-4 py-2 rounded-lg font-medium transition-all duration-300">
                        <i class="fas fa-star mr-2"></i>
                        Level {{ $this->quickStats['current_level'] }}
                    </div>

                    <!-- Points Badge -->
                    <div class="bg-gradient-to-r from-yellow-500 to-orange-500 text-white px-4 py-2 rounded-lg font-medium transition-all duration-300">
                        <i class="fas fa-coins mr-2"></i>
                        {{ number_format($this->quickStats['total_points']) }} pts
                    </div>

                    <!-- Timeframe Selector -->
                    <div class="flex bg-gray-100 dark:bg-gray-700 rounded-lg p-1 transition-colors duration-300">
                        @foreach(['7days' => '7d', '30days' => '30d'] as $value => $label)
                            <button wire:click="updateTimeframe('{{ $value }}')"
                                class="px-3 py-1 rounded-md text-sm font-medium transition-all duration-200 {{ $selectedTimeframe === $value ? 'bg-white dark:bg-gray-600 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    @if($showWidgets['quick_stats'])
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6 mb-8">
            <!-- Enrolled Courses -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 lg:p-6 hover:shadow-md dark:hover:shadow-xl transition-all duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">Enrolled</p>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $this->quickStats['enrolled_courses'] }}</h3>
                        <p class="text-xs text-green-600 dark:text-green-400 font-medium">{{ $this->quickStats['completed_courses'] }} completed</p>
                    </div>
                    <div class="bg-blue-100 dark:bg-blue-900/30 p-3 rounded-full">
                        <i class="fas fa-book text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Certificates -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 lg:p-6 hover:shadow-md dark:hover:shadow-xl transition-all duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">Certificates</p>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $this->quickStats['certificates_earned'] }}</h3>
                        <p class="text-xs text-purple-600 dark:text-purple-400 font-medium">earned</p>
                    </div>
                    <div class="bg-purple-100 dark:bg-purple-900/30 p-3 rounded-full">
                        <i class="fas fa-certificate text-purple-600 dark:text-purple-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Average Score -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 lg:p-6 hover:shadow-md dark:hover:shadow-xl transition-all duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">Avg Score</p>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($this->quickStats['average_score'], 1) }}%</h3>
                        <p class="text-xs text-yellow-600 dark:text-yellow-400 font-medium">assessments</p>
                    </div>
                    <div class="bg-yellow-100 dark:bg-yellow-900/30 p-3 rounded-full">
                        <i class="fas fa-chart-line text-yellow-600 dark:text-yellow-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Study Time -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 lg:p-6 hover:shadow-md dark:hover:shadow-xl transition-all duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">Study Time</p>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $this->quickStats['total_study_hours'] }}h</h3>
                        <p class="text-xs text-green-600 dark:text-green-400 font-medium">total</p>
                    </div>
                    <div class="bg-green-100 dark:bg-green-900/30 p-3 rounded-full">
                        <i class="fas fa-clock text-green-600 dark:text-green-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Achievements -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 lg:p-6 hover:shadow-md dark:hover:shadow-xl transition-all duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">Achievements</p>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $this->quickStats['achievements_unlocked'] }}</h3>
                        <p class="text-xs text-indigo-600 dark:text-indigo-400 font-medium">unlocked</p>
                    </div>
                    <div class="bg-indigo-100 dark:bg-indigo-900/30 p-3 rounded-full">
                        <i class="fas fa-trophy text-indigo-600 dark:text-indigo-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Pending Tasks -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 lg:p-6 hover:shadow-md dark:hover:shadow-xl transition-all duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">Pending</p>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $this->quickStats['pending_assessments'] }}</h3>
                        <p class="text-xs text-red-600 dark:text-red-400 font-medium">assessments</p>
                    </div>
                    <div class="bg-red-100 dark:bg-red-900/30 p-3 rounded-full">
                        <i class="fas fa-tasks text-red-600 dark:text-red-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Lessons Completed -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 lg:p-6 hover:shadow-md dark:hover:shadow-xl transition-all duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">Lessons</p>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $this->quickStats['lessons_completed'] }}</h3>
                        <p class="text-xs text-teal-600 dark:text-teal-400 font-medium">completed</p>
                    </div>
                    <div class="bg-teal-100 dark:bg-teal-900/30 p-3 rounded-full">
                        <i class="fas fa-check-circle text-teal-600 dark:text-teal-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Wishlist -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 lg:p-6 hover:shadow-md dark:hover:shadow-xl transition-all duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">Wishlist</p>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $this->quickStats['wishlist_count'] }}</h3>
                        <p class="text-xs text-pink-600 dark:text-pink-400 font-medium">courses saved</p>
                    </div>
                    <div class="bg-pink-100 dark:bg-pink-900/30 p-3 rounded-full">
                        <i class="fas fa-heart text-pink-600 dark:text-pink-400 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <!-- Left Column (2/3 width) -->
        <div class="xl:col-span-2 space-y-8">
            <!-- Learning Progress -->
            @if($showWidgets['learning_progress'])
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Continue Learning</h2>
                        <a href="{{ route('student.enrolled-courses') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 text-sm font-medium">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>

                    <div class="space-y-4">
                        @forelse($this->learningProgress as $progress)
                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 hover:border-blue-300 dark:hover:border-blue-500 transition-colors duration-200">
                                <div class="flex items-start space-x-4">
                                    @if($progress['thumbnail'])
                                        <img src="{{ asset('storage/' . $progress['thumbnail']) }}" alt="{{ $progress['title'] }}" class="w-20 h-20 rounded-lg object-cover flex-shrink-0">
                                    @else
                                        <div class="w-20 h-20 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-book text-blue-600 dark:text-blue-400 text-2xl"></i>
                                        </div>
                                    @endif

                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between mb-2">
                                            <div class="flex-1">
                                                <h3 class="font-semibold text-gray-900 dark:text-white">{{ $progress['title'] }}</h3>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                    by {{ $progress['instructor_name'] }} • {{ $progress['completed_lessons'] }}/{{ $progress['total_lessons'] }} lessons
                                                </p>
                                            </div>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-4 flex-shrink-0">
                                                {{ $progress['last_accessed']->format('M j') }}
                                            </span>
                                        </div>

                                        <!-- Progress bar -->
                                        <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2 mt-2">
                                            <div class="bg-gradient-to-r from-blue-500 to-purple-500 h-2 rounded-full transition-all duration-300" style="width: {{ $progress['progress'] }}%"></div>
                                        </div>

                                        <div class="flex items-center justify-between mt-2">
                                            <span class="text-xs text-gray-600 dark:text-gray-400">{{ $progress['progress'] }}% complete</span>
                                            @if($progress['estimated_remaining'])
                                                <span class="text-xs text-gray-600 dark:text-gray-400">{{ $progress['estimated_remaining'] }}h remaining</span>
                                            @endif>
                                        </div>

                                        @if($progress['next_lesson'])
                                            <div class="mt-3 flex items-center justify-between">
                                                <p class="text-sm text-gray-700 dark:text-gray-300">
                                                    <i class="fas fa-play-circle text-blue-500 mr-1"></i>
                                                    Next: {{ $progress['next_lesson']['title'] }}
                                                </p>
                                                <a href="{{ route('course.view', $progress['id']) }}" class="text-sm bg-blue-600 hover:bg-blue-700 text-white px-4 py-1 rounded-md transition-colors">
                                                    Continue
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <i class="fas fa-book-open text-gray-400 dark:text-gray-500 text-4xl mb-3"></i>
                                <p class="text-gray-600 dark:text-gray-400 mb-4">No active courses</p>
                                <a href="{{ route('student.course-catalog') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors">
                                    Browse Courses
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif

            <!-- Performance Analytics -->
            @if($showWidgets['performance_analytics'])
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Study Activity</h2>
                    
                    <div class="mb-4 flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">
                            Total: {{ number_format($this->performanceAnalytics['total_study_time_this_period'], 1) }}h this period
                        </span>
                        <a href="{{ route('learning.analytics') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                            View Details
                        </a>
                    </div>

                    <div class="h-64">
                        <canvas id="studyActivityChart"></canvas>
                    </div>
                </div>

                <!-- Subject Performance -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Performance by Subject Category</h2>
                    
                    <div class="space-y-4">
                        @forelse($this->performanceAnalytics['subject_performance'] as $subject)
                            <div class="border-b border-gray-200 dark:border-gray-700 pb-4 last:border-0">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-medium text-gray-900 dark:text-white">{{ $subject['category'] }}</h4>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $subject['courses_completed'] }}/{{ $subject['courses_enrolled'] }} completed
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-green-400 to-blue-500 h-2 rounded-full" 
                                         style="width: {{ $subject['average_progress'] }}%"></div>
                                </div>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="text-xs text-gray-600 dark:text-gray-400">{{ $subject['average_progress'] }}% avg progress</span>
                                    <span class="text-xs font-medium {{ $subject['completion_rate'] >= 70 ? 'text-green-600' : 'text-orange-600' }}">
                                        {{ $subject['completion_rate'] }}% completion rate
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-gray-500 dark:text-gray-400 py-8">No performance data available</p>
                        @endforelse
                    </div>
                </div>

                <!-- Weekly Goals Progress -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Weekly Goals</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Lessons Goal -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Lessons Completed</span>
                                <span class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $this->performanceAnalytics['weekly_goals']['lessons_completed'] }} /
                                    {{ $this->performanceAnalytics['weekly_goals']['lessons_target'] }}
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-3">
                                <div class="bg-gradient-to-r from-blue-500 to-purple-500 h-3 rounded-full transition-all duration-300"
                                    style="width: {{ min(($this->performanceAnalytics['weekly_goals']['lessons_completed'] / $this->performanceAnalytics['weekly_goals']['lessons_target']) * 100, 100) }}%">
                                </div>
                            </div>
                        </div>

                        <!-- Study Hours Goal -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Study Hours</span>
                                <span class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $this->performanceAnalytics['weekly_goals']['study_hours_completed'] }} /
                                    {{ $this->performanceAnalytics['weekly_goals']['study_hours_target'] }}h
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-3">
                                <div class="bg-gradient-to-r from-green-400 to-blue-500 h-3 rounded-full transition-all duration-300"
                                    style="width: {{ min(($this->performanceAnalytics['weekly_goals']['study_hours_completed'] / $this->performanceAnalytics['weekly_goals']['study_hours_target']) * 100, 100) }}%">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Improvement Areas -->
                    @if($this->performanceAnalytics['improvement_areas']->isNotEmpty())
                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Suggested Focus Areas</h3>
                            <div class="space-y-2">
                                @foreach($this->performanceAnalytics['improvement_areas'] as $area)
                                    <div class="flex items-start space-x-2 text-sm">
                                        <i class="fas fa-lightbulb text-yellow-500 mt-1"></i>
                                        <div>
                                            <p class="text-gray-700 dark:text-gray-300 font-medium">{{ $area['area'] }}</p>
                                            <p class="text-gray-600 dark:text-gray-400 text-xs">{{ $area['suggestion'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Recent Reviews -->
            @if($showWidgets['recent_reviews'] && $this->recentReviews->isNotEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Your Recent Reviews</h2>
                        <a href="{{ route('course-reviews') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">View All</a>
                    </div>

                    <div class="space-y-4">
                        @foreach($this->recentReviews as $review)
                            <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4">
                                <div class="flex items-start justify-between mb-2">
                                    <h4 class="font-medium text-gray-900 dark:text-white">{{ $review['course_title'] }}</h4>
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star text-sm {{ $i <= $review['rating'] ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ $review['comment'] }}</p>
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-gray-500">{{ $review['created_at']->diffForHumans() }}</span>
                                    <div class="flex items-center space-x-3">
                                        @if($review['is_approved'])
                                            <span class="text-green-600"><i class="fas fa-check-circle mr-1"></i>Approved</span>
                                        @else
                                            <span class="text-yellow-600"><i class="fas fa-clock mr-1"></i>Pending</span>
                                        @endif
                                        @if($review['has_reply'])
                                            <span class="text-blue-600"><i class="fas fa-reply mr-1"></i>Replied</span>
                                        @endif
                                        <span class="text-gray-500"><i class="fas fa-thumbs-up mr-1"></i>{{ $review['helpful_count'] }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column (1/3 width) -->
        <div class="space-y-8">
            <!-- Upcoming Tasks -->
            @if($showWidgets['upcoming_tasks'])
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Upcoming Tasks</h2>

                    <div class="space-y-3">
                        @forelse($this->upcomingTasks as $task)
                            <div class="flex items-center space-x-3 p-3 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-blue-300 dark:hover:border-blue-500 transition-colors">
                                <div class="flex-shrink-0">
                                    @if($task['type'] === 'assessment')
                                        <div class="w-8 h-8 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center">
                                            <i class="fas fa-clipboard-check text-red-600 dark:text-red-400 text-sm"></i>
                                        </div>
                                    @elseif($task['type'] === 'certificate')
                                        <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center">
                                            <i class="fas fa-certificate text-purple-600 dark:text-purple-400 text-sm"></i>
                                        </div>
                                    @else
                                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                                            <i class="fas fa-play text-blue-600 dark:text-blue-400 text-sm"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $task['title'] }}</h4>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 truncate">{{ $task['course'] }}</p>
                                    <div class="flex items-center mt-1 space-x-2">
                                        <span class="text-xs px-2 py-1 rounded-full {{ $task['priority'] === 'high' ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' : ($task['priority'] === 'medium' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300' : 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300') }}">
                                            {{ ucfirst($task['priority']) }}
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $task['due_date']->format('M j') }}</span>
                                        @if($task['is_mandatory'])
                                            <span class="text-xs text-red-600 dark:text-red-400 font-medium">Required</span>
                                        @endif
                                    </div>
                                </div>
                                <a href="{{ $task['url'] }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                                    <i class="fas fa-arrow-right text-sm"></i>
                                </a>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <i class="fas fa-tasks text-gray-400 dark:text-gray-500 text-3xl mb-2"></i>
                                <p class="text-gray-600 dark:text-gray-400">No upcoming tasks</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">You're all caught up!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif

            <!-- Recent Achievements -->
            @if($showWidgets['achievements'])
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Recent Achievements</h2>
                        <a href="{{ route('gamification.badges') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">View All</a>
                    </div>

                    <div class="space-y-3">
                        @forelse($this->recentAchievements as $achievement)
                            <div class="flex items-center space-x-3 p-3 rounded-lg bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20 border border-yellow-200 dark:border-yellow-800/50">
                                <div class="text-3xl flex-shrink-0">{{ $achievement['icon'] }}</div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $achievement['name'] }}</h4>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ $achievement['description'] }}</p>
                                    <div class="flex items-center mt-1 space-x-2">
                                        <span class="text-xs px-2 py-1 rounded-full {{ $achievement['rarity'] === 'legendary' ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300' : ($achievement['rarity'] === 'epic' ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-300' : 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300') }}">
                                            {{ ucfirst($achievement['rarity']) }}
                                        </span>
                                        @if($achievement['points_earned'] > 0)
                                            <span class="text-xs text-yellow-600 dark:text-yellow-400 font-medium">
                                                +{{ $achievement['points_earned'] }} pts
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 flex-shrink-0">{{ $achievement['earned_at']->format('M j') }}</span>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <i class="fas fa-trophy text-gray-400 dark:text-gray-500 text-3xl mb-2"></i>
                                <p class="text-gray-600 dark:text-gray-400">No achievements yet</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Complete lessons to earn your first achievement!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif

            <!-- Announcements -->
            @if($showWidgets['announcements'])
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Announcements</h2>
                        <a href="{{ route('announcements') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">View All</a>
                    </div>

                    <div class="space-y-4">
                        @forelse($this->recentAnnouncements as $announcement)
                            <div class="border-l-4 {{ $announcement['is_new'] ? 'border-blue-500' : 'border-gray-300 dark:border-gray-600' }} pl-4 py-2">
                                <div class="flex items-start justify-between mb-1">
                                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $announcement['title'] }}</h4>
                                    @if($announcement['is_new'])
                                        <span class="text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 px-2 py-1 rounded-full ml-2">New</span>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">{{ $announcement['content'] }}</p>
                                <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                                    <span>{{ $announcement['course_title'] }}</span>
                                    <span>{{ $announcement['published_at']->diffForHumans() }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <i class="fas fa-bullhorn text-gray-400 dark:text-gray-500 text-3xl mb-2"></i>
                                <p class="text-gray-600 dark:text-gray-400">No announcements</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif

            <!-- Career Tools -->
            @if($showWidgets['career_tools'])
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Career Tools</h2>

                    <div class="space-y-4">
                        <!-- Portfolio Status -->
                        <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-3">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Portfolio</span>
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $this->careerTools['portfolio_completion'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                <div class="bg-gradient-to-r from-green-400 to-blue-500 h-2 rounded-full" style="width: {{ $this->careerTools['portfolio_completion'] }}%"></div>
                            </div>
                            <a href="{{ route('portfolio.show') }}" class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 mt-2 inline-block">
                                Update Portfolio
                            </a>
                        </div>

                        <!-- Resume Status -->
                        <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Resume</span>
                                    @if($this->careerTools['resume_status']['exists'])
                                        <p class="text-xs text-gray-600 dark:text-gray-400">
                                            {{ $this->careerTools['resume_status']['completion'] }}% complete
                                        </p>
                                    @endif
                                </div>
                                <a href="{{ route('resume.builder') }}" class="text-xs bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 px-3 py-1 rounded-md transition-colors">
                                    {{ $this->careerTools['resume_status']['exists'] ? 'Edit' : 'Create' }}
                                </a>
                            </div>
                        </div>

                        <!-- Job Applications Stats -->
                        <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-3">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Job Applications</span>
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $this->careerTools['total_applications'] }} total</span>
                            </div>
                            @if($this->careerTools['shortlisted_count'] > 0)
                                <div class="flex items-center space-x-2 mb-2">
                                    <i class="fas fa-check-circle text-green-500 text-sm"></i>
                                    <span class="text-xs text-gray-600 dark:text-gray-400">{{ $this->careerTools['shortlisted_count'] }} shortlisted</span>
                                </div>
                            @endif
                        </div>

                        <!-- Recent Job Applications -->
                        @if(count($this->careerTools['job_applications']) > 0)
                            <div>
                                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Recent Applications</h3>
                                <div class="space-y-2">
                                    @foreach($this->careerTools['job_applications'] as $application)
                                        <div class="flex items-center justify-between text-sm p-2 bg-gray-50 dark:bg-gray-700/50 rounded">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-gray-700 dark:text-gray-300 truncate font-medium">{{ \Str::limit($application['job_title'], 20) }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $application['company'] }}</p>
                                            </div>
                                            <span class="px-2 py-1 text-xs rounded-full ml-2 {{ 
                                                $application['status_color'] === 'green' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 
                                                ($application['status_color'] === 'yellow' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300' : 
                                                ($application['status_color'] === 'blue' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' :
                                                'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300'))
                                            }}">
                                                {{ $application['status_label'] }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Quick Actions -->
                        <div class="pt-3 border-t border-gray-200 dark:border-gray-600">
                            <a href="{{ route('search.job') }}" class="block text-center bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg text-sm font-medium transition-colors">
                                Browse Jobs
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Mentorship Status -->
            @if($showWidgets['mentorship_status'] && ($this->mentorshipStatus['active_mentorships']->isNotEmpty() || $this->mentorshipStatus['pending_requests'] > 0))
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Mentorship</h2>
                        <a href="{{ route('mentorship.hub') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">View All</a>
                    </div>

                    @if($this->mentorshipStatus['pending_requests'] > 0)
                        <div class="mb-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                            <p class="text-sm text-yellow-800 dark:text-yellow-300">
                                <i class="fas fa-clock mr-2"></i>
                                {{ $this->mentorshipStatus['pending_requests'] }} pending request{{ $this->mentorshipStatus['pending_requests'] > 1 ? 's' : '' }}
                            </p>
                        </div>
                    @endif

                    @if($this->mentorshipStatus['active_mentorships']->isNotEmpty())
                        <div class="space-y-3">
                            @foreach($this->mentorshipStatus['active_mentorships'] as $mentorship)
                                <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $mentorship['mentor_name'] }}</h4>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $mentorship['duration_weeks'] }} weeks</span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2 mb-2">
                                        <div class="bg-gradient-to-r from-purple-500 to-pink-500 h-2 rounded-full" style="width: {{ $mentorship['progress_percentage'] }}%"></div>
                                    </div>
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-gray-600 dark:text-gray-400">{{ $mentorship['progress_percentage'] }}% complete</span>
                                        @if($mentorship['next_session'])
                                            <span class="text-blue-600 dark:text-blue-400">Next: {{ $mentorship['next_session']->scheduled_at->format('M j') }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if($this->mentorshipStatus['completed_mentorships'] > 0)
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600 text-center">
                            <p class="text-xs text-gray-600 dark:text-gray-400">
                                {{ $this->mentorshipStatus['completed_mentorships'] }} completed mentorship{{ $this->mentorshipStatus['completed_mentorships'] > 1 ? 's' : '' }}
                            </p>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Wishlist Preview -->
            @if($showWidgets['wishlist_preview'] && $this->wishlistPreview->isNotEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Wishlist</h2>
                        <a href="{{ route('student.saved-resources') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">View All</a>
                    </div>

                    <div class="space-y-3">
                        @foreach($this->wishlistPreview as $item)
                            <div class="flex items-start space-x-3 border border-gray-200 dark:border-gray-600 rounded-lg p-3 hover:border-blue-300 dark:hover:border-blue-500 transition-colors">
                                @if($item['thumbnail'])
                                    <img src="{{ asset('storage/' . $item['thumbnail']) }}" alt="{{ $item['title'] }}" class="w-16 h-16 rounded object-cover flex-shrink-0">
                                @else
                                    <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-book text-gray-400"></i>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $item['title'] }}</h4>
                                    <p class="text-xs text-gray-600 dark:text-gray-400">by {{ $item['instructor'] }}</p>
                                    <div class="flex items-center justify-between mt-1">
                                        <span class="text-sm font-bold text-gray-900 dark:text-white">
                                            {{ $item['is_free'] ? 'Free' : '₦' . number_format($item['price'], 2) }}
                                        </span>
                                        @if($item['rating'])
                                            <div class="flex items-center">
                                                <i class="fas fa-star text-yellow-400 text-xs"></i>
                                                <span class="text-xs text-gray-600 dark:text-gray-400 ml-1">{{ number_format($item['rating'], 1) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- System Status -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">System Status</h2>

                <div class="flex items-center space-x-3">
                    <div class="w-3 h-3 rounded-full {{ $this->systemHealth['status'] === 'operational' ? 'bg-green-500 dark:bg-green-400' : 'bg-red-500 dark:bg-red-400' }} animate-pulse"></div>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $this->systemHealth['status'] === 'operational' ? 'All Systems Operational' : 'Service Issues' }}
                        </p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">{{ $this->systemHealth['message'] }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Updated {{ $this->systemHealth['updated_at'] }}</p>
                        @if($this->systemHealth['incidents_count'] > 0)
                            <p class="text-xs text-orange-600 dark:text-orange-400 mt-1">
                                {{ $this->systemHealth['incidents_count'] }} incident{{ $this->systemHealth['incidents_count'] > 1 ? 's' : '' }} this week
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Get current theme for chart colors
            const isDark = document.documentElement.classList.contains('dark');
            
            // Study Activity Chart
            const studyData = @json($this->performanceAnalytics['study_activity']);
            if (studyData.length > 0) {
                const ctx = document.getElementById('studyActivityChart');
                if (ctx) {
                    new Chart(ctx.getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: studyData.map(item => item.date),
                            datasets: [{
                                label: 'Study Hours',
                                data: studyData.map(item => item.activity),
                                borderColor: isDark ? '#60A5FA' : '#3B82F6',
                                backgroundColor: isDark ? 'rgba(96, 165, 250, 0.1)' : 'rgba(59, 130, 246, 0.1)',
                                fill: true,
                                tension: 0.4,
                                pointBackgroundColor: isDark ? '#60A5FA' : '#3B82F6',
                                pointBorderColor: isDark ? '#1F2937' : '#ffffff',
                                pointBorderWidth: 2,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    backgroundColor: isDark ? '#1F2937' : '#ffffff',
                                    titleColor: isDark ? '#F9FAFB' : '#111827',
                                    bodyColor: isDark ? '#D1D5DB' : '#6B7280',
                                    borderColor: isDark ? '#374151' : '#E5E7EB',
                                    borderWidth: 1,
                                    padding: 12,
                                    displayColors: false,
                                    callbacks: {
                                        label: function(context) {
                                            return context.parsed.y.toFixed(1) + ' hours';
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: isDark ? 'rgba(55, 65, 81, 0.3)' : 'rgba(0, 0, 0, 0.05)'
                                    },
                                    ticks: {
                                        color: isDark ? '#9CA3AF' : '#6B7280',
                                        callback: function(value) {
                                            return value + 'h';
                                        }
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        color: isDark ? '#9CA3AF' : '#6B7280'
                                    }
                                }
                            },
                            interaction: {
                                intersect: false,
                                mode: 'index'
                            }
                        }
                    });
                }
            }
        });

        // Auto-refresh dashboard data every 5 minutes
        setInterval(() => {
            @this.call('$refresh');
        }, 300000);

        // Listen for theme changes
        window.addEventListener('dark-mode-toggled', function() {
            location.reload();
        });
    </script>
@endpush