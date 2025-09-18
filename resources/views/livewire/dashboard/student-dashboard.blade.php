<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-50 p-4 md:p-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 relative overflow-hidden">
            <!-- Background decoration -->
            <div
                class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-blue-100 to-purple-100 rounded-full -translate-y-8 translate-x-8 opacity-50">
            </div>

            <div
                class="flex flex-col lg:flex-row justify-between items-start lg:items-center space-y-4 lg:space-y-0 relative">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-graduation-cap text-blue-600 mr-3"></i>
                        Welcome back, {{ auth()->user()->name }}!
                    </h1>
                    <p class="text-gray-600 mt-1">Ready to continue your learning journey?</p>

                    @if($this->quickStats['study_streak'] > 0)
                        <div
                            class="mt-3 inline-flex items-center bg-orange-100 text-orange-800 px-3 py-1 rounded-full text-sm font-medium">
                            <i class="fas fa-fire mr-2"></i>
                            {{ $this->quickStats['study_streak'] }} day study streak!
                        </div>
                    @endif
                </div>

                <div class="flex flex-wrap items-center space-x-3">
                    <!-- Level Badge -->
                    <div
                        class="bg-gradient-to-r from-purple-600 to-blue-600 text-white px-4 py-2 rounded-lg font-medium">
                        <i class="fas fa-star mr-2"></i>
                        Level {{ $this->quickStats['current_level'] }}
                    </div>

                    <!-- Timeframe Selector -->
                    <div class="flex bg-gray-100 rounded-lg p-1">
                        @foreach(['7days' => '7d', '30days' => '30d'] as $value => $label)
                            <button wire:click="updateTimeframe('{{ $value }}')"
                                class="px-3 py-1 rounded-md text-sm font-medium transition-all duration-200 {{ $selectedTimeframe === $value ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
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
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 lg:p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 font-medium">Enrolled</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ $this->quickStats['enrolled_courses'] }}</h3>
                        <p class="text-xs text-green-600 font-medium">{{ $this->quickStats['completed_courses'] }} completed
                        </p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-book text-blue-600"></i>
                    </div>
                </div>
            </div>

            <div
                class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 lg:p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 font-medium">Certificates</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ $this->quickStats['certificates_earned'] }}
                        </h3>
                        <p class="text-xs text-purple-600 font-medium">earned</p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-certificate text-purple-600"></i>
                    </div>
                </div>
            </div>

            <div
                class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 lg:p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 font-medium">Avg Score</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">
                            {{ number_format($this->quickStats['average_score'], 1) }}%</h3>
                        <p class="text-xs text-yellow-600 font-medium">assessments</p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <i class="fas fa-chart-line text-yellow-600"></i>
                    </div>
                </div>
            </div>

            <div
                class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 lg:p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 font-medium">Study Time</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ $this->quickStats['total_study_hours'] }}h</h3>
                        <p class="text-xs text-green-600 font-medium">total</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-clock text-green-600"></i>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <!-- Left Column -->
        <div class="xl:col-span-2 space-y-8">
            <!-- Learning Progress -->
            @if($showWidgets['learning_progress'])
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-900">Continue Learning</h2>
                        <a href="{{ route('student.enrolled-courses') }}"
                            class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse($this->learningProgress as $progress)
                            <div
                                class="border border-gray-200 rounded-lg p-4 hover:border-blue-300 transition-colors duration-200">
                                <div class="flex items-start space-x-3">
                                    @if($progress['thumbnail'])
                                        <img src="{{ $progress['thumbnail'] }}" alt="{{ $progress['title'] }}"
                                            class="w-16 h-16 rounded-lg object-cover">
                                    @else
                                        <div
                                            class="w-16 h-16 bg-gradient-to-br from-blue-100 to-purple-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-book text-blue-600"></i>
                                        </div>
                                    @endif

                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-gray-900 truncate">{{ $progress['title'] }}</h3>
                                        <!-- Progress bar -->
                                        <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                            <div class="bg-gradient-to-r from-blue-500 to-purple-500 h-2 rounded-full"
                                                style="width: {{ $progress['progress'] }}%"></div>
                                        </div>
                                        <div class="flex items-center justify-between mt-1 text-xs text-gray-600">
                                            <span>{{ $progress['progress'] }}% complete</span>
                                            @if($progress['estimated_remaining'])
                                                <span>{{ $progress['estimated_remaining'] }}h remaining</span>
                                            @endif
                                        </div>
                                        @if($progress['next_lesson'])
                                            <p class="text-xs text-gray-600 mt-1">
                                                Next: {{ $progress['next_lesson']['title'] }}
                                            </p>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-500 ml-4">
                                        {{ $progress['last_accessed']->format('M j') }}
                                    </div>

                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6">
                                <i class="fas fa-book-open text-gray-400 text-3xl mb-2"></i>
                                <p class="text-gray-600">No active courses</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column -->
        <div class="space-y-8">
            <!-- Upcoming Tasks -->
            @if($showWidgets['upcoming_tasks'])
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Upcoming Tasks</h2>

                    <div class="space-y-3">
                        @forelse($this->upcomingTasks as $task)
                            <div
                                class="flex items-center space-x-3 p-3 rounded-lg border border-gray-200 hover:border-blue-300 transition-colors">
                                <div class="flex-shrink-0">
                                    @if($task['type'] === 'assessment')
                                        <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-clipboard-check text-red-600 text-sm"></i>
                                        </div>
                                    @else
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                            <i class="fas fa-play text-blue-600 text-sm"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-gray-900 truncate">{{ $task['title'] }}</h4>
                                    <p class="text-xs text-gray-600">{{ $task['course'] }}</p>
                                    <div class="flex items-center mt-1">
                                        <span
                                            class="text-xs px-2 py-1 rounded-full {{ $task['priority'] === 'high' ? 'bg-red-100 text-red-800' : ($task['priority'] === 'medium' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800') }}">
                                            {{ ucfirst($task['priority']) }}
                                        </span>
                                        <span class="text-xs text-gray-500 ml-2">{{ $task['due_date']->format('M j') }}</span>
                                    </div>
                                </div>
                                <a href="{{ $task['url'] }}" class="text-blue-600 hover:text-blue-700">
                                    <i class="fas fa-arrow-right text-sm"></i>
                                </a>
                            </div>
                        @empty
                            <div class="text-center py-6">
                                <i class="fas fa-tasks text-gray-400 text-3xl mb-2"></i>
                                <p class="text-gray-600">No upcoming tasks</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif

            <!-- Recent Achievements -->
            @if($showWidgets['achievements'])
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Recent Achievements</h2>

                    <div class="space-y-3">
                        @forelse($this->recentAchievements as $achievement)
                            <div
                                class="flex items-center space-x-3 p-3 rounded-lg bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200">
                                <div class="text-2xl">{{ $achievement['icon'] }}</div>
                                <div class="flex-1">
                                    <h4 class="text-sm font-semibold text-gray-900">{{ $achievement['name'] }}</h4>
                                    <p class="text-xs text-gray-600">{{ $achievement['description'] }}</p>
                                    <span
                                        class="text-xs px-2 py-1 bg-{{ $achievement['rarity'] === 'legendary' ? 'purple' : ($achievement['rarity'] === 'epic' ? 'indigo' : 'blue') }}-100 text-{{ $achievement['rarity'] === 'legendary' ? 'purple' : ($achievement['rarity'] === 'epic' ? 'indigo' : 'blue') }}-800 rounded-full mt-1 inline-block">
                                        {{ ucfirst($achievement['rarity']) }}
                                    </span>
                                </div>
                                <span class="text-xs text-gray-500">{{ $achievement['earned_at']->format('M j') }}</span>
                            </div>
                        @empty
                            <div class="text-center py-6">
                                <i class="fas fa-trophy text-gray-400 text-3xl mb-2"></i>
                                <p class="text-gray-600">No achievements yet</p>
                                <p class="text-xs text-gray-500 mt-1">Complete lessons to earn your first achievement!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif

            <!-- Career Tools -->
            @if($showWidgets['career_tools'])
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-6">Career Tools</h2>

                    <div class="space-y-4">
                        <!-- Portfolio Status -->
                        <div class="border border-gray-200 rounded-lg p-3">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Portfolio</span>
                                <span class="text-sm text-gray-600">{{ $this->careerTools['portfolio_completion'] }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-gradient-to-r from-green-400 to-blue-500 h-2 rounded-full"
                                    style="width: {{ $this->careerTools['portfolio_completion'] }}%"></div>
                            </div>
                            <a href="{{ route('portfolio.show') }}"
                                class="text-xs text-blue-600 hover:text-blue-700 mt-2 inline-block">
                                Update Portfolio
                            </a>
                        </div>

                        <!-- Resume Status -->
                        <div class="border border-gray-200 rounded-lg p-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-sm font-medium text-gray-700">Resume</span>
                                    @if($this->careerTools['resume_status']['exists'])
                                        <p class="text-xs text-gray-600">
                                            {{ $this->careerTools['resume_status']['completion'] }}% complete</p>
                                    @endif
                                </div>
                                <a href="{{ route('resume.builder') }}"
                                    class="text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded-md">
                                    {{ $this->careerTools['resume_status']['exists'] ? 'Edit' : 'Create' }}
                                </a>
                            </div>
                        </div>

                        <!-- Recent Job Applications -->
                        @if(count($this->careerTools['job_applications']) > 0)
                            <div>
                                <h3 class="text-sm font-semibold text-gray-700 mb-2">Recent Applications</h3>
                                <div class="space-y-2">
                                    @foreach($this->careerTools['job_applications'] as $application)
                                        <div class="flex items-center justify-between text-sm">
                                            <span
                                                class="text-gray-600 truncate">{{ \Str::limit($application->job->title, 25) }}</span>
                                            <span
                                                class="px-2 py-1 text-xs rounded-full {{ $application->status_color === 'green' ? 'bg-green-100 text-green-800' : ($application->status_color === 'yellow' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                                {{ $application->status_label }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Quick Actions -->
                        <div class="pt-3 border-t border-gray-200">
                            <a href="{{ route('search.job') }}"
                                class="block text-center bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg text-sm font-medium transition-colors">
                                Browse Jobs
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- System Status -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">System Status</h2>

                <div class="flex items-center space-x-3">
                    <div
                        class="w-3 h-3 rounded-full {{ $this->systemHealth['status'] === 'operational' ? 'bg-green-500' : 'bg-red-500' }}">
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">
                            {{ $this->systemHealth['status'] === 'operational' ? 'All Systems Operational' : 'Service Issues' }}
                        </p>
                        <p class="text-xs text-gray-600">{{ $this->systemHealth['message'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">Updated {{ $this->systemHealth['updated_at'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Weekly Goals Progress (Full Width) -->
    @if($showWidgets['performance_analytics'])
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mt-8">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Weekly Goals</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Lessons Goal -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Lessons Completed</span>
                        <span class="text-sm text-gray-600">
                            {{ $this->performanceAnalytics['weekly_goals']['lessons_completed'] }} /
                            {{ $this->performanceAnalytics['weekly_goals']['lessons_target'] }}
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-gradient-to-r from-blue-500 to-purple-500 h-3 rounded-full transition-all duration-300"
                            style="width: {{ min(($this->performanceAnalytics['weekly_goals']['lessons_completed'] / $this->performanceAnalytics['weekly_goals']['lessons_target']) * 100, 100) }}%">
                        </div>
                    </div>
                </div>

                <!-- Study Hours Goal -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Study Hours</span>
                        <span class="text-sm text-gray-600">
                            {{ $this->performanceAnalytics['weekly_goals']['study_hours_completed'] }} /
                            {{ $this->performanceAnalytics['weekly_goals']['study_hours_target'] }}h
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-gradient-to-r from-green-400 to-blue-500 h-3 rounded-full transition-all duration-300"
                            style="width: {{ min(($this->performanceAnalytics['weekly_goals']['study_hours_completed'] / $this->performanceAnalytics['weekly_goals']['study_hours_target']) * 100, 100) }}%">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Study Activity Chart
            const studyData = @json($this->performanceAnalytics['study_activity']);
            if (studyData.length > 0) {
                const ctx = document.getElementById('studyActivityChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: studyData.map(item => item.date),
                        datasets: [{
                            label: 'Daily Activity',
                            data: studyData.map(item => item.activity),
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: 'rgb(59, 130, 246)',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }
        });

        // Auto-refresh dashboard data every 5 minutes
        setInterval(() => {
            @this.call('loadAllData');
        }, 300000);
    </script>
@endpush