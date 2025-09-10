<div class="min-h-screen bg-slate-50">
    <!-- Header Section -->
    <div class="bg-white border-b border-slate-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900">
                        <i class="fas fa-chart-bar text-blue-500 mr-3"></i>
                        Platform Analytics
                    </h1>
                    <p class="text-slate-600 mt-2">Comprehensive insights into BootKode's growth and performance</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="bg-green-100 text-green-800 px-4 py-2 rounded-lg font-medium">
                        <i class="fas fa-circle text-xs mr-2"></i>
                        Live Data
                    </div>
                    <div class="text-sm text-slate-500">
                        Last updated: {{ now()->format('M j, Y g:i A') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Users Card -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 hover:shadow-lg transition-all duration-300 group">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-blue-100 p-3 rounded-xl group-hover:bg-blue-200 transition-colors">
                        <i class="fas fa-users text-2xl text-blue-600"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-slate-900">{{ number_format($overviewStats['total_users']) }}</div>
                        <div class="text-sm text-slate-500">Total Users</div>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        <span class="text-sm text-slate-600">{{ $overviewStats['active_user_percentage'] }}% active</span>
                    </div>
                    <div class="text-xs text-green-600 font-medium">
                        +{{ number_format($overviewStats['active_users']) }} this month
                    </div>
                </div>
            </div>

            <!-- Total Courses Card -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 hover:shadow-lg transition-all duration-300 group">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-green-100 p-3 rounded-xl group-hover:bg-green-200 transition-colors">
                        <i class="fas fa-book text-2xl text-green-600"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-slate-900">{{ number_format($overviewStats['published_courses']) }}</div>
                        <div class="text-sm text-slate-500">Published Courses</div>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-600">{{ $overviewStats['course_approval_rate'] }}% approval rate</span>
                    <div class="text-xs text-blue-600 font-medium">
                        {{ number_format($overviewStats['total_lessons']) }} lessons
                    </div>
                </div>
            </div>

            <!-- Certificates Card -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 hover:shadow-lg transition-all duration-300 group">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-purple-100 p-3 rounded-xl group-hover:bg-purple-200 transition-colors">
                        <i class="fas fa-certificate text-2xl text-purple-600"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-slate-900">{{ number_format($overviewStats['certificates_issued']) }}</div>
                        <div class="text-sm text-slate-500">Certificates Issued</div>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-600">{{ $overviewStats['avg_completion_rate'] }}% completion rate</span>
                    <div class="text-xs text-purple-600 font-medium">
                        {{ number_format($overviewStats['total_enrollments']) }} enrollments
                    </div>
                </div>
            </div>

            <!-- Team Stats Card -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200 hover:shadow-lg transition-all duration-300 group">
                <div class="flex items-center justify-between mb-4">
                    <div class="bg-orange-100 p-3 rounded-xl group-hover:bg-orange-200 transition-colors">
                        <i class="fas fa-chalkboard-teacher text-2xl text-orange-600"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-slate-900">{{ number_format($overviewStats['total_instructors']) }}</div>
                        <div class="text-sm text-slate-500">Active Instructors</div>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-slate-600">{{ number_format($overviewStats['total_students']) }} students</span>
                    <div class="text-xs text-orange-600 font-medium">
                        Growing team
                    </div>
                </div>
            </div>
        </div>


            <!-- Charts and Metrics Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- User Growth Chart -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 lg:col-span-2">
                    <h3 class="text-xl font-bold text-slate-900 mb-4 flex items-center">
                        <i class="fas fa-chart-line text-blue-500 mr-3"></i>
                        User Growth (Last 12 Months)
                    </h3>
                    <canvas id="userGrowthChart" class="w-full h-80"></canvas>
                </div>
                
                <!-- Geographic Breakdown Pie Chart -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                    <h3 class="text-xl font-bold text-slate-900 mb-4 flex items-center">
                        <i class="fas fa-globe-africa text-green-500 mr-3"></i>
                        Geographic Breakdown
                    </h3>
                    <canvas id="geographicChart" class="w-full h-80"></canvas>
                </div>
            </div>

             <!-- Additional Analytics Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Course Completion Rates Chart -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                <h3 class="text-xl font-bold text-slate-900 mb-4 flex items-center">
                    <i class="fas fa-percent text-purple-500 mr-3"></i>
                    Course Completion Rates
                </h3>
                <canvas id="completionRatesChart" class="w-full h-80"></canvas>
            </div>

            <!-- Revenue Trend Chart -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                <h3 class="text-xl font-bold text-slate-900 mb-4 flex items-center">
                    <i class="fas fa-dollar-sign text-orange-500 mr-3"></i>
                    Revenue Trend (Last 6 Months)
                </h3>
                <canvas id="revenueTrendChart" class="w-full h-80"></canvas>
            </div>
        </div>


        
        <!-- Charts Row 1 -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- User Growth Chart -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
                <h3 class="text-xl font-bold text-slate-900 mb-6 flex items-center">
                    <i class="fas fa-chart-line text-blue-500 mr-3"></i>
                    User Growth Trend
                </h3>
                <canvas id="userGrowthChart" width="400" height="200"></canvas>
            </div>

            <!-- Certificate Trend Chart -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
                <h3 class="text-xl font-bold text-slate-900 mb-6 flex items-center">
                    <i class="fas fa-award text-purple-500 mr-3"></i>
                    Certificate Issuance Trend
                </h3>
                <canvas id="certificateChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- Interactive Metrics Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Engagement Metrics -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
                <h3 class="text-xl font-bold text-slate-900 mb-6 flex items-center">
                    <i class="fas fa-fire text-red-500 mr-3"></i>
                    Engagement Metrics
                </h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl">
                        <div>
                            <div class="font-semibold text-slate-900">Daily Active</div>
                            <div class="text-sm text-slate-600">Users today</div>
                        </div>
                        <div class="text-2xl font-bold text-blue-600">{{ number_format($engagementMetrics['daily_active_users']) }}</div>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl">
                        <div>
                            <div class="font-semibold text-slate-900">Weekly Active</div>
                            <div class="text-sm text-slate-600">Past 7 days</div>
                        </div>
                        <div class="text-2xl font-bold text-green-600">{{ number_format($engagementMetrics['weekly_active_users']) }}</div>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl">
                        <div>
                            <div class="font-semibold text-slate-900">Monthly Active</div>
                            <div class="text-sm text-slate-600">Past 30 days</div>
                        </div>
                        <div class="text-2xl font-bold text-purple-600">{{ number_format($engagementMetrics['monthly_active_users']) }}</div>
                    </div>
                </div>
            </div>

            <!-- Geographic Distribution -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
                <h3 class="text-xl font-bold text-slate-900 mb-6 flex items-center">
                    <i class="fas fa-globe-africa text-green-500 mr-3"></i>
                    Geographic Distribution
                </h3>
                <div class="space-y-3">
                    @foreach($geographicData as $country => $data)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-3 h-3 rounded-full bg-blue-500"></div>
                            <span class="text-sm font-medium text-slate-900">{{ $country }}</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-slate-600">{{ number_format($data['users']) }}</span>
                            <span class="text-xs text-slate-500">{{ $data['percentage'] }}%</span>
                        </div>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2">
                        <div class="bg-blue-500 h-2 rounded-full transition-all duration-700" 
                             style="width: {{ $data['percentage'] }}%"></div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Recent Activity Feed -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
                <h3 class="text-xl font-bold text-slate-900 mb-6 flex items-center">
                    <i class="fas fa-bell text-yellow-500 mr-3"></i>
                    Recent Activity
                </h3>
                <div class="space-y-4">
                    @foreach($recentActivity as $activity)
                    <div class="flex items-start space-x-3 p-3 hover:bg-slate-50 rounded-lg transition-colors">
                        <div class="bg-{{ $activity['color'] }}-100 p-2 rounded-lg flex-shrink-0">
                            <i class="{{ $activity['icon'] }} text-{{ $activity['color'] }}-600"></i>
                        </div>
                        <div class="flex-grow">
                            <p class="text-sm font-medium text-slate-900">{{ $activity['message'] }}</p>
                            <p class="text-xs text-slate-500 mt-1">{{ $activity['time'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Top Performers and Trending Content -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Top Performers -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
                <h3 class="text-xl font-bold text-slate-900 mb-6 flex items-center">
                    <i class="fas fa-star text-yellow-500 mr-3"></i>
                    Top Performers
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Top Students -->
                    <div>
                        <h4 class="text-lg font-semibold text-slate-800 mb-3">Top Students</h4>
                        <div class="space-y-3">
                            @foreach($topPerformers['top_students'] as $student)
                                <div class="flex items-center justify-between p-2 rounded-lg hover:bg-slate-50">
                                    <div class="font-medium text-slate-700">{{ $student['name'] }}</div>
                                    <div class="text-sm text-slate-500">{{ number_format($student['certificates_count']) }} certificates</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <!-- Top Instructors -->
                    <div>
                        <h4 class="text-lg font-semibold text-slate-800 mb-3">Top Instructors</h4>
                        <div class="space-y-3">
                            @foreach($topPerformers['top_instructors'] as $instructor)
                                <div class="flex items-center justify-between p-2 rounded-lg hover:bg-slate-50">
                                    <div class="font-medium text-slate-700">{{ $instructor['name'] }}</div>
                                    <div class="text-sm text-slate-500">{{ number_format($instructor['courses_count']) }} courses</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Trending Courses -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
                <h3 class="text-xl font-bold text-slate-900 mb-6 flex items-center">
                    <i class="fas fa-fire-alt text-orange-500 mr-3"></i>
                    Trending Content
                </h3>
                <div class="space-y-4">
                    @foreach($trendingCourses as $course)
                    <div class="flex items-center justify-between p-3 rounded-lg hover:bg-slate-50">
                        <div class="flex-grow">
                            <div class="font-medium text-slate-900">{{ $course['title'] }}</div>
                            <div class="flex items-center text-sm text-slate-500 mt-1">
                                <i class="fas fa-star text-yellow-400 mr-1"></i>
                                <span>{{ $course['average_rating'] }} ({{ number_format($course['enrollments_count']) }} enrollments this week)</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- Additional Metrics Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Revenue Analytics -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
                <h3 class="text-xl font-bold text-slate-900 mb-6 flex items-center">
                    <i class="fas fa-dollar-sign text-green-500 mr-3"></i>
                    Revenue Analytics
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-slate-50 rounded-xl text-center">
                        <div class="text-2xl font-bold text-slate-900">${{ number_format($revenueData['total_revenue'], 2) }}</div>
                        <div class="text-sm text-slate-600">Total Revenue</div>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-xl text-center">
                        <div class="text-2xl font-bold text-slate-900">${{ number_format($revenueData['monthly_recurring'], 2) }}</div>
                        <div class="text-sm text-slate-600">Monthly Recurring</div>
                    </div>
                </div>
                <div class="mt-6">
                    <h4 class="font-semibold text-slate-800 mb-3">Revenue by Month</h4>
                    <canvas id="revenueChart" width="400" height="200"></canvas>
                </div>
            </div>
            
            <!-- Completion Rates -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
                <h3 class="text-xl font-bold text-slate-900 mb-6 flex items-center">
                    <i class="fas fa-clipboard-check text-blue-500 mr-3"></i>
                    Completion Analytics
                </h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl">
                        <div>
                            <div class="font-semibold text-slate-900">Overall Trend</div>
                            <div class="text-sm text-slate-600">Avg. over time</div>
                        </div>
                        <div class="text-2xl font-bold text-blue-600">{{ $completionRates['overall_trend'][5]['rate'] }}%</div>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl">
                        <div>
                            <div class="font-semibold text-slate-900">By Difficulty</div>
                            <div class="text-sm text-slate-600">Beginner Courses</div>
                        </div>
                        <div class="text-2xl font-bold text-green-600">{{ $completionRates['by_difficulty']['Beginner'] }}%</div>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl">
                        <div>
                            <div class="font-semibold text-slate-900">By Duration</div>
                            <div class="text-sm text-slate-600">Under 2 hours</div>
                        </div>
                        <div class="text-2xl font-bold text-purple-600">{{ $completionRates['by_duration']['Under 2 hours'] }}%</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 2 -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Weekly User Activity -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
                <h3 class="text-xl font-bold text-slate-900 mb-6 flex items-center">
                    <i class="fas fa-calendar-alt text-yellow-500 mr-3"></i>
                    Weekly User Pattern
                </h3>
                <canvas id="weeklyPatternChart" width="400" height="200"></canvas>
            </div>
            
            <!-- Course Breakdown -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
                <h3 class="text-xl font-bold text-slate-900 mb-6 flex items-center">
                    <i class="fas fa-chart-pie text-red-500 mr-3"></i>
                    Course Breakdown
                </h3>
                <div class="space-y-4">
                    @foreach($categoryBreakdown as $category)
                    <div class="flex items-center justify-between p-3 rounded-lg bg-slate-50">
                        <div class="flex-grow">
                            <div class="font-medium text-slate-900">{{ $category['name'] }}</div>
                            <div class="text-sm text-slate-600">{{ number_format($category['courses']) }} courses, {{ number_format($category['enrollments']) }} enrollments</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>


<!-- Add the Chart.js library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Wait for the window to load before running the script
    window.onload = function() {
        // --- User Growth Chart ---
        const userGrowthData = @json($userGrowthData);
        const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
        new Chart(userGrowthCtx, {
            type: 'line',
            data: {
                labels: userGrowthData.map(d => d.month),
                datasets: [{
                    label: 'Cumulative Users',
                    data: userGrowthData.map(d => d.cumulative),
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.2)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true },
                    x: { grid: { display: false } }
                }
            }
        });

        // --- Geographic Breakdown Chart ---
        const geographicData = @json($geographicData);
        // Prepare data for the pie chart
        const geoLabels = Object.keys(geographicData);
        const geoUsers = Object.values(geographicData).map(d => d.users);
        const geographicCtx = document.getElementById('geographicChart').getContext('2d');
        new Chart(geographicCtx, {
            type: 'pie',
            data: {
                labels: geoLabels,
                datasets: [{
                    label: 'Users by Country',
                    data: geoUsers,
                    backgroundColor: [
                        '#3b82f6', '#10b981', '#a855f7', '#f59e0b', '#ef4444', '#64748b'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed !== null) {
                                    label += context.parsed + ' users (' + geographicData[context.label].percentage + '%)';
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });

        // --- Course Completion Rates Chart ---
        const completionRatesData = @json($completionRates['overall_trend']);
        const completionRatesCtx = document.getElementById('completionRatesChart').getContext('2d');
        new Chart(completionRatesCtx, {
            type: 'bar',
            data: {
                labels: completionRatesData.map(d => d.month),
                datasets: [{
                    label: 'Completion Rate',
                    data: completionRatesData.map(d => d.rate),
                    backgroundColor: '#a855f7',
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
        
        // --- Revenue Trend Chart ---
        const revenueTrendData = @json($revenueData['revenue_by_month']);
        const revenueTrendCtx = document.getElementById('revenueTrendChart').getContext('2d');
        new Chart(revenueTrendCtx, {
            type: 'line',
            data: {
                labels: revenueTrendData.map(d => d.month),
                datasets: [{
                    label: 'Revenue',
                    data: revenueTrendData.map(d => d.amount),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.2)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true },
                    x: { grid: { display: false } }
                }
            }
        });
    }
</script>

</div>
