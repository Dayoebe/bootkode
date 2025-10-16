<div class="min-h-screen bg-themed-primary transition-colors duration-300 p-4 md:p-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-themed-secondary rounded-2xl shadow-sm border border-themed-primary p-6 relative overflow-hidden transition-colors duration-300">
            <!-- Background decoration -->
            <div class="absolute top-0 right-0 w-32 h-32 bg-blue-100 dark:bg-blue-900/20 rounded-full -translate-y-8 translate-x-8 opacity-50 transition-colors duration-300"></div>

            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center space-y-4 lg:space-y-0 relative">
                <div>
                    <h1 class="text-3xl font-bold text-themed-primary flex items-center transition-colors duration-300">
                        <i class="fas fa-graduation-cap text-blue-600 dark:text-blue-400 mr-3"></i>
                        Welcome back, {{ auth()->user()->name }}!
                    </h1>
                    <p class="text-themed-secondary mt-1 transition-colors duration-300">Ready to continue your learning journey?</p>

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
                    <div class="flex bg-themed-tertiary rounded-lg p-1 transition-colors duration-300">
                        @foreach(['7days' => '7d', '30days' => '30d'] as $value => $label)
                            <button wire:click="updateTimeframe('{{ $value }}')"
                                class="px-3 py-1 rounded-md text-sm font-medium transition-all duration-200 {{ $selectedTimeframe === $value ? 'bg-themed-secondary text-blue-600 dark:text-blue-400 shadow-sm' : 'text-themed-secondary hover:text-themed-primary' }}">
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
            <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-4 lg:p-6 hover:shadow-md dark:hover:shadow-xl transition-all duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-themed-secondary font-medium transition-colors duration-300">Enrolled</p>
                        <h3 class="text-2xl font-bold text-themed-primary mt-1 transition-colors duration-300">{{ $this->quickStats['enrolled_courses'] }}</h3>
                        <p class="text-xs text-green-600 dark:text-green-400 font-medium transition-colors duration-300">{{ $this->quickStats['completed_courses'] }} completed</p>
                    </div>
                    <div class="bg-blue-100 dark:bg-blue-900/30 p-3 rounded-full transition-colors duration-300">
                        <i class="fas fa-book text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Certificates -->
            <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-4 lg:p-6 hover:shadow-md dark:hover:shadow-xl transition-all duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-themed-secondary font-medium transition-colors duration-300">Certificates</p>
                        <h3 class="text-2xl font-bold text-themed-primary mt-1 transition-colors duration-300">{{ $this->quickStats['certificates_earned'] }}</h3>
                        <p class="text-xs text-purple-600 dark:text-purple-400 font-medium transition-colors duration-300">earned</p>
                    </div>
                    <div class="bg-purple-100 dark:bg-purple-900/30 p-3 rounded-full transition-colors duration-300">
                        <i class="fas fa-certificate text-purple-600 dark:text-purple-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Average Score -->
            <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-4 lg:p-6 hover:shadow-md dark:hover:shadow-xl transition-all duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-themed-secondary font-medium transition-colors duration-300">Avg Score</p>
                        <h3 class="text-2xl font-bold text-themed-primary mt-1 transition-colors duration-300">{{ number_format($this->quickStats['average_score'], 1) }}%</h3>
                        <p class="text-xs text-yellow-600 dark:text-yellow-400 font-medium transition-colors duration-300">assessments</p>
                    </div>
                    <div class="bg-yellow-100 dark:bg-yellow-900/30 p-3 rounded-full transition-colors duration-300">
                        <i class="fas fa-chart-line text-yellow-600 dark:text-yellow-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Study Time -->
            <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-4 lg:p-6 hover:shadow-md dark:hover:shadow-xl transition-all duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-themed-secondary font-medium transition-colors duration-300">Study Time</p>
                        <h3 class="text-2xl font-bold text-themed-primary mt-1 transition-colors duration-300">{{ $this->quickStats['total_study_hours'] }}h</h3>
                        <p class="text-xs text-green-600 dark:text-green-400 font-medium transition-colors duration-300">total</p>
                    </div>
                    <div class="bg-green-100 dark:bg-green-900/30 p-3 rounded-full transition-colors duration-300">
                        <i class="fas fa-clock text-green-600 dark:text-green-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Achievements -->
            <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-4 lg:p-6 hover:shadow-md dark:hover:shadow-xl transition-all duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-themed-secondary font-medium transition-colors duration-300">Achievements</p>
                        <h3 class="text-2xl font-bold text-themed-primary mt-1 transition-colors duration-300">{{ $this->quickStats['achievements_unlocked'] }}</h3>
                        <p class="text-xs text-indigo-600 dark:text-indigo-400 font-medium transition-colors duration-300">unlocked</p>
                    </div>
                    <div class="bg-indigo-100 dark:bg-indigo-900/30 p-3 rounded-full transition-colors duration-300">
                        <i class="fas fa-trophy text-indigo-600 dark:text-indigo-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Pending Tasks -->
            <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-4 lg:p-6 hover:shadow-md dark:hover:shadow-xl transition-all duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-themed-secondary font-medium transition-colors duration-300">Pending</p>
                        <h3 class="text-2xl font-bold text-themed-primary mt-1 transition-colors duration-300">{{ $this->quickStats['pending_assessments'] }}</h3>
                        <p class="text-xs text-red-600 dark:text-red-400 font-medium transition-colors duration-300">assessments</p>
                    </div>
                    <div class="bg-red-100 dark:bg-red-900/30 p-3 rounded-full transition-colors duration-300">
                        <i class="fas fa-tasks text-red-600 dark:text-red-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Lessons Completed -->
            <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-4 lg:p-6 hover:shadow-md dark:hover:shadow-xl transition-all duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-themed-secondary font-medium transition-colors duration-300">Lessons</p>
                        <h3 class="text-2xl font-bold text-themed-primary mt-1 transition-colors duration-300">{{ $this->quickStats['lessons_completed'] }}</h3>
                        <p class="text-xs text-teal-600 dark:text-teal-400 font-medium transition-colors duration-300">completed</p>
                    </div>
                    <div class="bg-teal-100 dark:bg-teal-900/30 p-3 rounded-full transition-colors duration-300">
                        <i class="fas fa-check-circle text-teal-600 dark:text-teal-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Wishlist -->
            <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-4 lg:p-6 hover:shadow-md dark:hover:shadow-xl transition-all duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-themed-secondary font-medium transition-colors duration-300">Wishlist</p>
                        <h3 class="text-2xl font-bold text-themed-primary mt-1 transition-colors duration-300">{{ $this->quickStats['wishlist_count'] }}</h3>
                        <p class="text-xs text-pink-600 dark:text-pink-400 font-medium transition-colors duration-300">courses saved</p>
                    </div>
                    <div class="bg-pink-100 dark:bg-pink-900/30 p-3 rounded-full transition-colors duration-300">
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
                <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-themed-primary transition-colors duration-300">Continue Learning</h2>
                        <a href="{{ route('student.enrolled-courses') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 text-sm font-medium transition-colors duration-300">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>

                    <div class="space-y-4">
                        @forelse($this->learningProgress as $progress)
                            <div class="border border-themed-primary rounded-lg p-4 hover:border-blue-300 dark:hover:border-blue-500 transition-colors duration-200">
                                <div class="flex items-start space-x-4">
                                    @if($progress['thumbnail'])
                                        <img src="{{ asset('storage/' . $progress['thumbnail']) }}" alt="{{ $progress['title'] }}" class="w-20 h-20 rounded-lg object-cover flex-shrink-0">
                                    @else
                                        <div class="w-20 h-20 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center flex-shrink-0 transition-colors duration-300">
                                            <i class="fas fa-book text-blue-600 dark:text-blue-400 text-2xl"></i>
                                        </div>
                                    @endif

                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between mb-2">
                                            <div class="flex-1">
                                                <h3 class="font-semibold text-themed-primary transition-colors duration-300">{{ $progress['title'] }}</h3>
                                                <p class="text-xs text-themed-secondary mt-1 transition-colors duration-300">
                                                    by {{ $progress['instructor_name'] }} • {{ $progress['completed_lessons'] }}/{{ $progress['total_lessons'] }} lessons
                                                </p>
                                            </div>
                                            <span class="text-xs text-themed-secondary ml-4 flex-shrink-0 transition-colors duration-300">
                                                {{ $progress['last_accessed']->format('M j') }}
                                            </span>
                                        </div>

                                        <!-- Progress bar -->
                                        <div class="w-full bg-themed-tertiary rounded-full h-2 mt-2 transition-colors duration-300">
                                            <div class="bg-gradient-to-r from-blue-500 to-purple-500 h-2 rounded-full transition-all duration-300" style="width: {{ $progress['progress'] }}%"></div>
                                        </div>

                                        <div class="flex items-center justify-between mt-2">
                                            <span class="text-xs text-themed-secondary transition-colors duration-300">{{ $progress['progress'] }}% complete</span>
                                            @if($progress['estimated_remaining'])
                                                <span class="text-xs text-themed-secondary transition-colors duration-300">{{ $progress['estimated_remaining'] }}h remaining</span>
                                            @endif>
                                        </div>

                                        @if($progress['next_lesson'])
                                            <div class="mt-3 flex items-center justify-between">
                                                <p class="text-sm text-themed-primary transition-colors duration-300">
                                                    <i class="fas fa-play-circle text-blue-500 mr-1"></i>
                                                    Next: {{ $progress['next_lesson']['title'] }}
                                                </p>
                                                <a href="{{ route('course.view', $progress['id']) }}" class="text-sm bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white px-4 py-1 rounded-md transition-colors">
                                                    Continue
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <i class="fas fa-book-open text-themed-tertiary text-4xl mb-3"></i>
                                <p class="text-themed-secondary mb-4 transition-colors duration-300">No active courses</p>
                                <a href="{{ route('student.course-catalog') }}" class="inline-block bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition-colors">
                                    Browse Courses
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif

            <!-- Performance Analytics -->
            @if($showWidgets['performance_analytics'])
                <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                    <h2 class="text-xl font-bold text-themed-primary mb-6 transition-colors duration-300">Study Activity</h2>
                    
                    <div class="mb-4 flex items-center justify-between">
                        <span class="text-sm text-themed-secondary transition-colors duration-300">
                            Total: {{ number_format($this->performanceAnalytics['total_study_time_this_period'], 1) }}h this period
                        </span>
                        <a href="{{ route('learning.analytics') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline transition-colors duration-300">
                            View Details
                        </a>
                    </div>

                    <div class="h-64">
                        <canvas id="studyActivityChart"></canvas>
                    </div>
                </div>

                <!-- Subject Performance -->
                <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                    <h2 class="text-xl font-bold text-themed-primary mb-6 transition-colors duration-300">Performance by Subject Category</h2>
                    
                    <div class="space-y-4">
                        @forelse($this->performanceAnalytics['subject_performance'] as $subject)
                            <div class="border-b border-themed-primary pb-4 last:border-0 transition-colors duration-300">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-medium text-themed-primary transition-colors duration-300">{{ $subject['category'] }}</h4>
                                    <span class="text-sm text-themed-secondary transition-colors duration-300">
                                        {{ $subject['courses_completed'] }}/{{ $subject['courses_enrolled'] }} completed
                                    </span>
                                </div>
                                <div class="w-full bg-themed-tertiary rounded-full h-2 transition-colors duration-300">
                                    <div class="bg-gradient-to-r from-green-400 to-blue-500 h-2 rounded-full" 
                                         style="width: {{ $subject['average_progress'] }}%"></div>
                                </div>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="text-xs text-themed-secondary transition-colors duration-300">{{ $subject['average_progress'] }}% avg progress</span>
                                    <span class="text-xs font-medium {{ $subject['completion_rate'] >= 70 ? 'text-green-600 dark:text-green-400' : 'text-orange-600 dark:text-orange-400' }} transition-colors duration-300">
                                        {{ $subject['completion_rate'] }}% completion rate
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-themed-secondary py-8 transition-colors duration-300">No performance data available</p>
                        @endforelse
                    </div>
                </div>

                <!-- Weekly Goals Progress -->
                <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                    <h2 class="text-xl font-bold text-themed-primary mb-6 transition-colors duration-300">Weekly Goals</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Lessons Goal -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-themed-primary transition-colors duration-300">Lessons Completed</span>
                                <span class="text-sm text-themed-secondary transition-colors duration-300">
                                    {{ $this->performanceAnalytics['weekly_goals']['lessons_completed'] }} /
                                    {{ $this->performanceAnalytics['weekly_goals']['lessons_target'] }}
                                </span>
                            </div>
                            <div class="w-full bg-themed-tertiary rounded-full h-3 transition-colors duration-300">
                                <div class="bg-gradient-to-r from-blue-500 to-purple-500 h-3 rounded-full transition-all duration-300"
                                    style="width: {{ min(($this->performanceAnalytics['weekly_goals']['lessons_completed'] / $this->performanceAnalytics['weekly_goals']['lessons_target']) * 100, 100) }}%">
                                </div>
                            </div>
                        </div>

                        <!-- Study Hours Goal -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-themed-primary transition-colors duration-300">Study Hours</span>
                                <span class="text-sm text-themed-secondary transition-colors duration-300">
                                    {{ $this->performanceAnalytics['weekly_goals']['study_hours_completed'] }} /
                                    {{ $this->performanceAnalytics['weekly_goals']['study_hours_target'] }}h
                                </span>
                            </div>
                            <div class="w-full bg-themed-tertiary rounded-full h-3 transition-colors duration-300">
                                <div class="bg-gradient-to-r from-green-400 to-blue-500 h-3 rounded-full transition-all duration-300"
                                    style="width: {{ min(($this->performanceAnalytics['weekly_goals']['study_hours_completed'] / $this->performanceAnalytics['weekly_goals']['study_hours_target']) * 100, 100) }}%">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Improvement Areas -->
                    @if($this->performanceAnalytics['improvement_areas']->isNotEmpty())
                        <div class="mt-6 pt-6 border-t border-themed-primary transition-colors duration-300">
                            <h3 class="text-sm font-semibold text-themed-primary mb-3 transition-colors duration-300">Suggested Focus Areas</h3>
                            <div class="space-y-2">
                                @foreach($this->performanceAnalytics['improvement_areas'] as $area)
                                    <div class="flex items-start space-x-2 text-sm">
                                        <i class="fas fa-lightbulb text-yellow-500 dark:text-yellow-400 mt-1"></i>
                                        <div>
                                            <p class="text-themed-primary font-medium transition-colors duration-300">{{ $area['area'] }}</p>
                                            <p class="text-themed-secondary text-xs transition-colors duration-300">{{ $area['suggestion'] }}</p>
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
                <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-themed-primary transition-colors duration-300">Your Recent Reviews</h2>
                        <a href="{{ route('course-reviews') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline transition-colors duration-300">View All</a>
                    </div>

                    <div class="space-y-4">
                        @foreach($this->recentReviews as $review)
                            <div class="border border-themed-primary rounded-lg p-4 transition-colors duration-300">
                                <div class="flex items-start justify-between mb-2">
                                    <h4 class="font-medium text-themed-primary transition-colors duration-300">{{ $review['course_title'] }}</h4>
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star text-sm {{ $i <= $review['rating'] ? 'text-yellow-400 dark:text-yellow-300' : 'text-themed-tertiary' }} transition-colors duration-300"></i>
                                        @endfor
                                    </div>
                                </div>
                                <p class="text-sm text-themed-secondary mb-2 transition-colors duration-300">{{ $review['comment'] }}</p>
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-themed-tertiary transition-colors duration-300">{{ $review['created_at']->diffForHumans() }}</span>
                                    <div class="flex items-center space-x-3">
                                        @if($review['is_approved'])
                                            <span class="text-green-600 dark:text-green-400 transition-colors duration-300"><i class="fas fa-check-circle mr-1"></i>Approved</span>
                                        @else
                                            <span class="text-yellow-600 dark:text-yellow-400 transition-colors duration-300"><i class="fas fa-clock mr-1"></i>Pending</span>
                                        @endif
                                        @if($review['has_reply'])
                                            <span class="text-blue-600 dark:text-blue-400 transition-colors duration-300"><i class="fas fa-reply mr-1"></i>Replied</span>
                                        @endif
                                        <span class="text-themed-tertiary transition-colors duration-300"><i class="fas fa-thumbs-up mr-1"></i>{{ $review['helpful_count'] }}</span>
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
                <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                    <h2 class="text-xl font-bold text-themed-primary mb-6 transition-colors duration-300">Upcoming Tasks</h2>

                    <div class="space-y-3">
                        @forelse($this->upcomingTasks as $task)
                            <div class="flex items-center space-x-3 p-3 rounded-lg border border-themed-primary hover:border-blue-300 dark:hover:border-blue-500 transition-colors">
                                <div class="flex-shrink-0">
                                    @if($task['type'] === 'assessment')
                                        <div class="w-8 h-8 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center transition-colors duration-300">
                                            <i class="fas fa-clipboard-check text-red-600 dark:text-red-400 text-sm"></i>
                                        </div>
                                    @elseif($task['type'] === 'certificate')
                                        <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-full flex items-center justify-center transition-colors duration-300">
                                            <i class="fas fa-certificate text-purple-600 dark:text-purple-400 text-sm"></i>
                                        </div>
                                    @else
                                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center transition-colors duration-300">
                                            <i class="fas fa-play text-blue-600 dark:text-blue-400 text-sm"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-themed-primary truncate transition-colors duration-300">{{ $task['title'] }}</h4>
                                    <p class="text-xs text-themed-secondary truncate transition-colors duration-300">{{ $task['course'] }}</p>
                                    <div class="flex items-center mt-1 space-x-2">
                                        <span class="text-xs px-2 py-1 rounded-full {{ $task['priority'] === 'high' ? 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300' : ($task['priority'] === 'medium' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300' : 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300') }} transition-colors duration-300">
                                            {{ ucfirst($task['priority']) }}
                                        </span>
                                        <span class="text-xs text-themed-tertiary transition-colors duration-300">{{ $task['due_date']->format('M j') }}</span>
                                        @if($task['is_mandatory'])
                                            <span class="text-xs text-red-600 dark:text-red-400 font-medium transition-colors duration-300">Required</span>
                                        @endif
                                    </div>
                                </div>
                                <a href="{{ $task['url'] }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 transition-colors">
                                    <i class="fas fa-arrow-right text-sm"></i>
                                </a>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <i class="fas fa-tasks text-themed-tertiary text-3xl mb-2"></i>
                                <p class="text-themed-secondary transition-colors duration-300">No upcoming tasks</p>
                                <p class="text-xs text-themed-tertiary mt-1 transition-colors duration-300">You're all caught up!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif

            <!-- Recent Achievements -->
            @if($showWidgets['achievements'])
                <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-themed-primary transition-colors duration-300">Recent Achievements</h2>
                        <a href="{{ route('gamification.badges') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline transition-colors duration-300">View All</a>
                    </div>

                    <div class="space-y-3">
                        @forelse($this->recentAchievements as $achievement)
                            <div class="flex items-center space-x-3 p-3 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800/50 transition-colors duration-300">
                                <div class="text-3xl flex-shrink-0">{{ $achievement['icon'] }}</div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-semibold text-themed-primary transition-colors duration-300">{{ $achievement['name'] }}</h4>
                                    <p class="text-xs text-themed-secondary transition-colors duration-300">{{ $achievement['description'] }}</p>
                                    <div class="flex items-center mt-1 space-x-2">
                                        <span class="text-xs px-2 py-1 rounded-full {{ $achievement['rarity'] === 'legendary' ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300' : ($achievement['rarity'] === 'epic' ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-300' : 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300') }} transition-colors duration-300">
                                            {{ ucfirst($achievement['rarity']) }}
                                        </span>
                                        @if($achievement['points_earned'] > 0)
                                            <span class="text-xs text-yellow-600 dark:text-yellow-400 font-medium transition-colors duration-300">
                                                +{{ $achievement['points_earned'] }} pts
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <span class="text-xs text-themed-tertiary flex-shrink-0 transition-colors duration-300">{{ $achievement['earned_at']->format('M j') }}</span>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <i class="fas fa-trophy text-themed-tertiary text-3xl mb-2"></i>
                                <p class="text-themed-secondary transition-colors duration-300">No achievements yet</p>
                                <p class="text-xs text-themed-tertiary mt-1 transition-colors duration-300">Complete lessons to earn your first achievement!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif

            <!-- Announcements -->
            @if($showWidgets['announcements'])
                <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-themed-primary transition-colors duration-300">Announcements</h2>
                        <a href="{{ route('announcements') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline transition-colors duration-300">View All</a>
                    </div>

                    <div class="space-y-4">
                        @forelse($this->recentAnnouncements as $announcement)
                            <div class="border-l-4 {{ $announcement['is_new'] ? 'border-blue-500 dark:border-blue-400' : 'border-themed-tertiary' }} pl-4 py-2 transition-colors duration-300">
                                <div class="flex items-start justify-between mb-1">
                                    <h4 class="text-sm font-semibold text-themed-primary transition-colors duration-300">{{ $announcement['title'] }}</h4>
                                    @if($announcement['is_new'])
                                        <span class="text-xs bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 px-2 py-1 rounded-full ml-2 transition-colors duration-300">New</span>
                                    @endif
                                </div>
                                <p class="text-xs text-themed-secondary mb-2 transition-colors duration-300">{{ $announcement['content'] }}</p>
                                <div class="flex items-center justify-between text-xs text-themed-tertiary transition-colors duration-300">
                                    <span>{{ $announcement['course_title'] }}</span>
                                    <span>{{ $announcement['published_at']->diffForHumans() }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <i class="fas fa-bullhorn text-themed-tertiary text-3xl mb-2"></i>
                                <p class="text-themed-secondary transition-colors duration-300">No announcements</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif

            <!-- Career Tools -->
            @if($showWidgets['career_tools'])
                <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                    <h2 class="text-xl font-bold text-themed-primary mb-6 transition-colors duration-300">Career Tools</h2>

                    <div class="space-y-4">
                        <!-- Portfolio Status -->
                        <div class="border border-themed-primary rounded-lg p-3 transition-colors duration-300">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-themed-primary transition-colors duration-300">Portfolio</span>
                                <span class="text-sm text-themed-secondary transition-colors duration-300">{{ $this->careerTools['portfolio_completion'] }}%</span>
                            </div>
                            <div class="w-full bg-themed-tertiary rounded-full h-2 transition-colors duration-300">
                                <div class="bg-gradient-to-r from-green-400 to-blue-500 h-2 rounded-full" style="width: {{ $this->careerTools['portfolio_completion'] }}%"></div>
                            </div>
                            <a href="{{ route('portfolio.show') }}" class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 mt-2 inline-block transition-colors duration-300">
                                Update Portfolio
                            </a>
                        </div>

                        <!-- Resume Status -->
                        <div class="border border-themed-primary rounded-lg p-3 transition-colors duration-300">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-sm font-medium text-themed-primary transition-colors duration-300">Resume</span>
                                    @if($this->careerTools['resume_status']['exists'])
                                        <p class="text-xs text-themed-secondary transition-colors duration-300">
                                            {{ $this->careerTools['resume_status']['completion'] }}% complete
                                        </p>
                                    @endif
                                </div>
                                <a href="{{ route('resume.builder') }}" class="text-xs bg-themed-tertiary hover:bg-blue-100 dark:hover:bg-blue-900/30 text-themed-primary dark:text-blue-400 px-3 py-1 rounded-md transition-colors duration-300">
                                    {{ $this->careerTools['resume_status']['exists'] ? 'Edit' : 'Create' }}
                                </a>
                            </div>
                        </div>

                        <!-- Job Applications Stats -->
                        <div class="border border-themed-primary rounded-lg p-3 transition-colors duration-300">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-medium text-themed-primary transition-colors duration-300">Job Applications</span>
                                <span class="text-sm text-themed-secondary transition-colors duration-300">{{ $this->careerTools['total_applications'] }} total</span>
                            </div>
                            @if($this->careerTools['shortlisted_count'] > 0)
                                <div class="flex items-center space-x-2 mb-2">
                                    <i class="fas fa-check-circle text-green-500 dark:text-green-400 text-sm"></i>
                                    <span class="text-xs text-themed-secondary transition-colors duration-300">{{ $this->careerTools['shortlisted_count'] }} shortlisted</span>
                                </div>
                            @endif
                        </div>

                        <!-- Recent Job Applications -->
                        @if(count($this->careerTools['job_applications']) > 0)
                            <div>
                                <h3 class="text-sm font-semibold text-themed-primary mb-2 transition-colors duration-300">Recent Applications</h3>
                                <div class="space-y-2">
                                    @foreach($this->careerTools['job_applications'] as $application)
                                        <div class="flex items-center justify-between text-sm p-2 bg-themed-tertiary rounded transition-colors duration-300">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-themed-primary truncate font-medium transition-colors duration-300">{{ \Str::limit($application['job_title'], 20) }}</p>
                                                <p class="text-xs text-themed-secondary transition-colors duration-300">{{ $application['company'] }}</p>
                                            </div>
                                            <span class="px-2 py-1 text-xs rounded-full ml-2 {{ 
                                                $application['status_color'] === 'green' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 
                                                ($application['status_color'] === 'yellow' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300' : 
                                                ($application['status_color'] === 'blue' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300' :
                                                'bg-themed-secondary text-themed-primary'))
                                            }} transition-colors duration-300">
                                                {{ $application['status_label'] }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Quick Actions -->
                        <div class="pt-3 border-t border-themed-primary transition-colors duration-300">
                            <a href="{{ route('search.job') }}" class="block text-center bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white py-2 rounded-lg text-sm font-medium transition-colors duration-300">
                                Browse Jobs
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Mentorship Status -->
            @if($showWidgets['mentorship_status'] && ($this->mentorshipStatus['active_mentorships']->isNotEmpty() || $this->mentorshipStatus['pending_requests'] > 0))
                <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-themed-primary transition-colors duration-300">Mentorship</h2>
                        <a href="{{ route('mentorship.hub') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline transition-colors duration-300">View All</a>
                    </div>

                    @if($this->mentorshipStatus['pending_requests'] > 0)
                        <div class="mb-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg transition-colors duration-300">
                            <p class="text-sm text-yellow-800 dark:text-yellow-300 transition-colors duration-300">
                                <i class="fas fa-clock mr-2"></i>
                                {{ $this->mentorshipStatus['pending_requests'] }} pending request{{ $this->mentorshipStatus['pending_requests'] > 1 ? 's' : '' }}
                            </p>
                        </div>
                    @endif

                    @if($this->mentorshipStatus['active_mentorships']->isNotEmpty())
                        <div class="space-y-3">
                            @foreach($this->mentorshipStatus['active_mentorships'] as $mentorship)
                                <div class="border border-themed-primary rounded-lg p-3 transition-colors duration-300">
                                    <div class="flex items-center justify-between mb-2">
                                        <h4 class="text-sm font-semibold text-themed-primary transition-colors duration-300">{{ $mentorship['mentor_name'] }}</h4>
                                        <span class="text-xs text-themed-secondary transition-colors duration-300">{{ $mentorship['duration_weeks'] }} weeks</span>
                                    </div>
                                    <div class="w-full bg-themed-tertiary rounded-full h-2 mb-2 transition-colors duration-300">
                                        <div class="bg-gradient-to-r from-purple-500 to-pink-500 h-2 rounded-full" style="width: {{ $mentorship['progress_percentage'] }}%"></div>
                                    </div>
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-themed-secondary transition-colors duration-300">{{ $mentorship['progress_percentage'] }}% complete</span>
                                        @if($mentorship['next_session'])
                                            <span class="text-blue-600 dark:text-blue-400 transition-colors duration-300">Next: {{ $mentorship['next_session']->scheduled_at->format('M j') }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if($this->mentorshipStatus['completed_mentorships'] > 0)
                        <div class="mt-4 pt-4 border-t border-themed-primary text-center transition-colors duration-300">
                            <p class="text-xs text-themed-secondary transition-colors duration-300">
                                {{ $this->mentorshipStatus['completed_mentorships'] }} completed mentorship{{ $this->mentorshipStatus['completed_mentorships'] > 1 ? 's' : '' }}
                            </p>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Wishlist Preview -->
            @if($showWidgets['wishlist_preview'] && $this->wishlistPreview->isNotEmpty())
                <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-themed-primary transition-colors duration-300">Wishlist</h2>
                        <a href="{{ route('student.saved-resources') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline transition-colors duration-300">View All</a>
                    </div>

                    <div class="space-y-3">
                        @foreach($this->wishlistPreview as $item)
                            <div class="flex items-start space-x-3 border border-themed-primary rounded-lg p-3 hover:border-blue-300 dark:hover:border-blue-500 transition-colors">
                                @if($item['thumbnail'])
                                    <img src="{{ asset('storage/' . $item['thumbnail']) }}" alt="{{ $item['title'] }}" class="w-16 h-16 rounded object-cover flex-shrink-0">
                                @else
                                    <div class="w-16 h-16 bg-themed-tertiary rounded flex items-center justify-center flex-shrink-0 transition-colors duration-300">
                                        <i class="fas fa-book text-themed-secondary"></i>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-themed-primary truncate transition-colors duration-300">{{ $item['title'] }}</h4>
                                    <p class="text-xs text-themed-secondary transition-colors duration-300">by {{ $item['instructor'] }}</p>
                                    <div class="flex items-center justify-between mt-1">
                                        <span class="text-sm font-bold text-themed-primary transition-colors duration-300">
                                            {{ $item['is_free'] ? 'Free' : '₦' . number_format($item['price'], 2) }}
                                        </span>
                                        @if($item['rating'])
                                            <div class="flex items-center">
                                                <i class="fas fa-star text-yellow-400 dark:text-yellow-300 text-xs"></i>
                                                <span class="text-xs text-themed-secondary ml-1 transition-colors duration-300">{{ number_format($item['rating'], 1) }}</span>
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
            <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                <h2 class="text-lg font-bold text-themed-primary mb-4 transition-colors duration-300">System Status</h2>

                <div class="flex items-center space-x-3">
                    <div class="w-3 h-3 rounded-full {{ $this->systemHealth['status'] === 'operational' ? 'bg-green-500 dark:bg-green-400' : 'bg-red-500 dark:bg-red-400' }} animate-pulse"></div>
                    <div>
                        <p class="text-sm font-medium text-themed-primary transition-colors duration-300">
                            {{ $this->systemHealth['status'] === 'operational' ? 'All Systems Operational' : 'Service Issues' }}
                        </p>
                        <p class="text-xs text-themed-secondary transition-colors duration-300">{{ $this->systemHealth['message'] }}</p>
                        <p class="text-xs text-themed-tertiary mt-1 transition-colors duration-300">Updated {{ $this->systemHealth['updated_at'] }}</p>
                        @if($this->systemHealth['incidents_count'] > 0)
                            <p class="text-xs text-orange-600 dark:text-orange-400 mt-1 transition-colors duration-300">
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
                                pointBorderColor: isDark ? 'rgb(31, 41, 55)' : '#ffffff',
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