<div class="min-h-screen bg-gradient-to-br from-indigo-50 to-purple-50 p-4 md:p-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 relative overflow-hidden">
            <!-- Background decoration -->
            <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full -translate-y-12 translate-x-12 opacity-60"></div>
            
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center space-y-4 lg:space-y-0 relative">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-chalkboard-teacher text-indigo-600 mr-3"></i>
                        Instructor Dashboard
                    </h1>
                    <p class="text-gray-600 mt-1">Welcome back, {{ auth()->user()->name }}! Manage your courses and track student progress.</p>
                </div>
                
                <div class="flex flex-wrap items-center space-x-3">
                    <!-- Course Filter -->
                    <select wire:model.live="selectedCourseFilter" class="bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="all">All Courses</option>
                        @foreach(auth()->user()->courses as $course)
                            <option value="{{ $course->id }}">{{ \Str::limit($course->title, 30) }}</option>
                        @endforeach
                    </select>
                    
                    <!-- Timeframe Selector -->
                    <div class="flex bg-gray-100 rounded-lg p-1">
                        @foreach(['7days' => '7d', '30days' => '30d', '90days' => '90d'] as $value => $label)
                            <button 
                                wire:click="updateTimeframe('{{ $value }}')"
                                class="px-3 py-1 rounded-md text-sm font-medium transition-all duration-200 {{ $selectedTimeframe === $value ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                    
                    <!-- Quick Actions -->
                    <a href="{{ route('create_course') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                        <i class="fas fa-plus mr-2"></i>
                        New Course
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Overview Stats Grid -->
    @if($showWidgets['overview_stats'])
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Courses</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ $this->overviewStats['total_courses'] }}</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="text-green-600 font-medium">{{ $this->overviewStats['published_courses'] }}</span>
                        <span class="text-gray-500 ml-1">published</span>
                    </div>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-book text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Students</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($this->overviewStats['total_students']) }}</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="text-green-600 font-medium">+{{ $this->overviewStats['new_enrollments'] }}</span>
                        <span class="text-gray-500 ml-1">this period</span>
                    </div>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-users text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Average Rating</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($this->overviewStats['average_rating'], 1) }}</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <div class="flex text-yellow-400 mr-2">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star{{ $i <= $this->overviewStats['average_rating'] ? '' : '-o' }}"></i>
                            @endfor
                        </div>
                        <span class="text-gray-500">{{ $this->overviewStats['total_reviews'] }} reviews</span>
                    </div>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <i class="fas fa-star text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Earnings</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-1">₦{{ number_format($this->overviewStats['total_earnings'], 0) }}</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="text-green-600 font-medium">₦{{ number_format($this->overviewStats['monthly_earnings'], 0) }}</span>
                        <span class="text-gray-500 ml-1">this month</span>
                    </div>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <i class="fas fa-wallet text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <!-- Left Column -->
        <div class="xl:col-span-2 space-y-8">
            <!-- Course Performance -->
            @if($showWidgets['course_performance'])
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Course Performance</h2>
                    <a href="{{ route('my-course') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                <div class="space-y-4">
                    @forelse($this->coursePerformance->take(5) as $course)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                        <div class="flex items-center space-x-3">
                            @if($course['thumbnail'])
                            <img src="{{ $course['thumbnail'] }}" alt="{{ $course['title'] }}" class="w-12 h-12 rounded-lg object-cover">
                            @else
                            <div class="w-12 h-12 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-book text-indigo-600"></i>
                            </div>
                            @endif
                            
                            <div>
                                <h4 class="font-semibold text-gray-900">{{ \Str::limit($course['title'], 40) }}</h4>
                                <div class="flex items-center space-x-4 text-sm text-gray-600 mt-1">
                                    <span>{{ $course['total_enrollments'] }} students</span>
                                    <span>{{ $course['completion_rate'] }}% completion</span>
                                    <span class="flex items-center">
                                        <i class="fas fa-star text-yellow-400 mr-1"></i>
                                        {{ number_format($course['average_rating'], 1) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-right">
                            <div class="text-lg font-bold text-gray-900">₦{{ number_format($course['revenue'], 0) }}</div>
                            <span class="px-2 py-1 text-xs rounded-full {{ $course['status'] === 'Published' ? 'bg-green-100 text-green-800' : ($course['status'] === 'Pending Approval' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ $course['status'] }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <i class="fas fa-book-open text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-600 mb-4">No courses created yet</p>
                        <a href="{{ route('create_course') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors">
                            Create Your First Course
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif

            <!-- Student Analytics -->
            @if($showWidgets['student_analytics'])
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Student Analytics</h2>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Enrollment Trends Chart -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Enrollment Trends</h3>
                        <div class="h-48">
                            <canvas id="enrollmentTrendsChart"></canvas>
                        </div>
                    </div>
                    
                    <!-- Student Engagement Metrics -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Student Engagement</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Total Students</span>
                                <span class="text-lg font-bold text-gray-900">{{ number_format($this->studentAnalytics['total_students']) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Active Students</span>
                                <span class="text-lg font-bold text-green-600">{{ $this->studentAnalytics['active_students'] }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Engagement Rate</span>
                                <div class="flex items-center space-x-2">
                                    <div class="w-16 bg-gray-200 rounded-full h-2">
                                        <div class="bg-gradient-to-r from-green-400 to-blue-500 h-2 rounded-full" 
                                             style="width: {{ $this->studentAnalytics['engagement_rate'] }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $this->studentAnalytics['engagement_rate'] }}%</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Completion Rate</span>
                                <span class="text-lg font-bold text-purple-600">{{ $this->studentAnalytics['completion_rate'] }}%</span>
                            </div>
                        </div>
                        
                        <!-- Top Performing Students -->
                        @if(count($this->studentAnalytics['top_students']) > 0)
                        <div class="mt-6">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Top Performing Students</h4>
                            <div class="space-y-2">
                                @foreach($this->studentAnalytics['top_students'] as $student)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600">{{ $student['name'] }}</span>
                                    <span class="font-medium text-indigo-600">{{ $student['average_score'] }}%</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Earnings Overview -->
            @if($showWidgets['earnings_overview'])
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Earnings Overview</h2>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Daily Earnings Chart -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Daily Earnings ({{ $selectedTimeframe === '7days' ? 'Last 7 Days' : ($selectedTimeframe === '30days' ? 'Last 30 Days' : 'Last 90 Days') }})</h3>
                        <div class="h-48">
                            <canvas id="earningsChart"></canvas>
                        </div>
                    </div>
                    
                    <!-- Top Earning Courses -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Top Earning Courses</h3>
                        <div class="space-y-3">
                            @forelse($this->earningsOverview['top_earning_courses'] as $course)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <span class="text-sm text-gray-600 truncate">{{ \Str::limit($course['title'], 30) }}</span>
                                <span class="text-sm font-bold text-gray-900">₦{{ number_format($course['revenue'], 0) }}</span>
                            </div>
                            @empty
                            <p class="text-gray-500 text-sm">No earnings data available</p>
                            @endforelse
                        </div>
                        
                        <!-- Earnings Summary -->
                        <div class="mt-6 pt-4 border-t border-gray-200">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm text-gray-600">Pending Earnings</span>
                                <span class="text-sm font-bold text-orange-600">₦{{ number_format($this->earningsOverview['pending_earnings'], 0) }}</span>
                            </div>
                            <a href="{{ route('instructor.earnings') }}" class="block text-center bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-lg text-sm font-medium transition-colors">
                                View Detailed Earnings
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column -->
        <div class="space-y-8">
            <!-- Certificate Requests -->
            @if($showWidgets['certificate_requests'])
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Certificate Requests</h2>
                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded-full">
                        {{ count($this->certificateRequests) }} pending
                    </span>
                </div>
                
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @forelse($this->certificateRequests as $request)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900">{{ $request['student_name'] }}</h4>
                                <p class="text-sm text-gray-600">{{ \Str::limit($request['course_title'], 30) }}</p>
                                <div class="flex items-center mt-2 text-xs text-gray-500">
                                    <span>Grade: {{ $request['grade'] ?? 'N/A' }}</span>
                                    <span class="mx-2">•</span>
                                    <span>{{ $request['requested_at']->format('M j, Y') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex space-x-2 mt-3">
                            <button wire:click="approveCertificate({{ $request['id'] }})" 
                                    class="flex-1 bg-green-600 hover:bg-green-700 text-white text-xs py-2 rounded-lg transition-colors">
                                <i class="fas fa-check mr-1"></i>
                                Approve
                            </button>
                            <button onclick="rejectCertificate({{ $request['id'] }})" 
                                    class="flex-1 bg-red-600 hover:bg-red-700 text-white text-xs py-2 rounded-lg transition-colors">
                                <i class="fas fa-times mr-1"></i>
                                Reject
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-6">
                        <i class="fas fa-certificate text-gray-400 text-3xl mb-2"></i>
                        <p class="text-gray-600">No pending certificate requests</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif

            <!-- Recent Enrollments -->
            @if($showWidgets['recent_enrollments'])
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Recent Enrollments</h2>
                
                <div class="space-y-3 max-h-80 overflow-y-auto">
                    @forelse($this->recentEnrollments as $enrollment)
                    <div class="flex items-center space-x-3 p-3 border border-gray-200 rounded-lg">
                        <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-indigo-600"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-medium text-gray-900 truncate">{{ $enrollment['student_name'] }}</h4>
                            <p class="text-xs text-gray-600 truncate">{{ \Str::limit($enrollment['course_title'], 25) }}</p>
                            <div class="flex items-center mt-1">
                                <div class="w-16 bg-gray-200 rounded-full h-1">
                                    <div class="bg-indigo-500 h-1 rounded-full" style="width: {{ $enrollment['progress'] }}%"></div>
                                </div>
                                <span class="text-xs text-gray-500 ml-2">{{ $enrollment['progress'] }}%</span>
                            </div>
                        </div>
                        <span class="text-xs text-gray-500">{{ $enrollment['enrolled_at']->format('M j') }}</span>
                    </div>
                    @empty
                    <div class="text-center py-6">
                        <i class="fas fa-user-plus text-gray-400 text-3xl mb-2"></i>
                        <p class="text-gray-600">No recent enrollments</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif

            <!-- Course Reviews -->
            @if($showWidgets['course_reviews'])
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Recent Reviews</h2>
                
                <div class="space-y-4 max-h-96 overflow-y-auto">
                    @forelse($this->courseReviews as $review)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <h4 class="text-sm font-semibold text-gray-900">{{ $review['student_name'] }}</h4>
                                <p class="text-xs text-gray-600">{{ \Str::limit($review['course_title'], 30) }}</p>
                            </div>
                            <div class="flex items-center">
                                <div class="flex text-yellow-400 mr-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star{{ $i <= $review['rating'] ? '' : '-o' }} text-xs"></i>
                                    @endfor
                                </div>
                                <span class="text-xs text-gray-500">{{ $review['created_at']->format('M j') }}</span>
                            </div>
                        </div>
                        @if($review['comment'])
                        <p class="text-sm text-gray-700">{{ \Str::limit($review['comment'], 100) }}</p>
                        @endif
                    </div>
                    @empty
                    <div class="text-center py-6">
                        <i class="fas fa-star text-gray-400 text-3xl mb-2"></i>
                        <p class="text-gray-600">No recent reviews</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif

            <!-- Marketplace Items -->
            @if($showWidgets['marketplace_items'] && count($this->marketplaceItems) > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Marketplace Items</h2>
                    <a href="{{ route('marketplace.seller.listings') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                <div class="space-y-3">
                    @foreach($this->marketplaceItems as $item)
                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-gray-900 truncate">{{ \Str::limit($item['title'], 25) }}</h4>
                            <div class="flex items-center space-x-2 mt-1 text-xs text-gray-600">
                                <span>{{ $item['type'] }}</span>
                                <span class="px-2 py-1 rounded-full {{ $item['status'] === 'Approved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $item['status'] }}
                                </span>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-bold text-gray-900">{{ $item['price'] }}</div>
                            <div class="text-xs text-gray-500">{{ $item['sales'] }} sales</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enrollment Trends Chart
    const enrollmentData = @json($this->studentAnalytics['enrollment_trends']);
    if (enrollmentData.length > 0) {
        const ctx1 = document.getElementById('enrollmentTrendsChart').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: enrollmentData.map(item => item.date),
                datasets: [{
                    label: 'New Enrollments',
                    data: enrollmentData.map(item => item.enrollments),
                    borderColor: 'rgb(99, 102, 241)',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    fill: true,
                    tension: 0.4,
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

    // Earnings Chart
    const earningsData = @json($this->earningsOverview['daily_earnings']);
    if (earningsData.length > 0) {
        const ctx2 = document.getElementById('earningsChart').getContext('2d');
        new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: earningsData.map(item => item.date),
                datasets: [{
                    label: 'Daily Earnings (₦)',
                    data: earningsData.map(item => item.earnings),
                    backgroundColor: 'rgba(147, 51, 234, 0.8)',
                    borderColor: 'rgb(147, 51, 234)',
                    borderWidth: 1,
                    borderRadius: 4,
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
                        ticks: {
                            callback: function(value) {
                                return '₦' + value.toLocaleString();
                            }
                        },
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

// Certificate rejection modal
function rejectCertificate(certificateId) {
    const reason = prompt('Please provide a reason for rejection:');
    if (reason && reason.trim()) {
        @this.call('rejectCertificate', certificateId, reason.trim());
    }
}

// Auto-refresh dashboard data
setInterval(() => {
    @this.call('loadAllData');
}, 300000); // 5 minutes
</script>
@endpush