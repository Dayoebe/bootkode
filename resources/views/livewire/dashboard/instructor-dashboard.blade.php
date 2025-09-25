<div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-300 p-4 md:p-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 relative overflow-hidden transition-colors duration-300">
            <!-- Background decoration -->
            <div class="absolute top-0 right-0 w-40 h-40 bg-indigo-100 dark:bg-indigo-900/20 rounded-full -translate-y-12 translate-x-12 opacity-60 transition-colors duration-300"></div>
            
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center space-y-4 lg:space-y-0 relative">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center transition-colors duration-300">
                        <i class="fas fa-chalkboard-teacher text-indigo-600 dark:text-indigo-400 mr-3"></i>
                        Instructor Dashboard
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1 transition-colors duration-300">Welcome back, {{ auth()->user()->name }}! Manage your courses and track student progress.</p>
                </div>
                
                <div class="flex flex-wrap items-center space-x-3">
                    <!-- Course Filter -->
                    <select wire:model.live="selectedCourseFilter" class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm text-gray-900 dark:text-white transition-colors duration-300">
                        <option value="all">All Courses</option>
                        @foreach(auth()->user()->courses as $course)
                            <option value="{{ $course->id }}">{{ \Str::limit($course->title, 30) }}</option>
                        @endforeach
                    </select>
                    
                    <!-- Timeframe Selector -->
                    <div class="flex bg-gray-100 dark:bg-gray-700 rounded-lg p-1 transition-colors duration-300">
                        @foreach(['7days' => '7d', '30days' => '30d', '90days' => '90d'] as $value => $label)
                            <button 
                                wire:click="updateTimeframe('{{ $value }}')"
                                class="px-3 py-1 rounded-md text-sm font-medium transition-all duration-200 {{ $selectedTimeframe === $value ? 'bg-white dark:bg-gray-600 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                    
                    <!-- Quick Actions -->
                    <a href="{{ route('create.course') }}" class="bg-indigo-600 dark:bg-indigo-500 hover:bg-indigo-700 dark:hover:bg-indigo-600 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
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
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 hover:shadow-md dark:hover:shadow-xl transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 font-medium transition-colors duration-300">Total Courses</p>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-1 transition-colors duration-300">{{ $this->overviewStats['total_courses'] }}</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="text-green-600 dark:text-green-400 font-medium transition-colors duration-300">{{ $this->overviewStats['published_courses'] }}</span>
                        <span class="text-gray-500 dark:text-gray-400 ml-1 transition-colors duration-300">published</span>
                    </div>
                </div>
                <div class="bg-blue-100 dark:bg-blue-900/30 p-3 rounded-full transition-colors duration-300">
                    <i class="fas fa-book text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 hover:shadow-md dark:hover:shadow-xl transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 font-medium transition-colors duration-300">Total Students</p>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-1 transition-colors duration-300">{{ number_format($this->overviewStats['total_students']) }}</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="text-green-600 dark:text-green-400 font-medium transition-colors duration-300">+{{ $this->overviewStats['new_enrollments'] }}</span>
                        <span class="text-gray-500 dark:text-gray-400 ml-1 transition-colors duration-300">this period</span>
                    </div>
                </div>
                <div class="bg-green-100 dark:bg-green-900/30 p-3 rounded-full transition-colors duration-300">
                    <i class="fas fa-users text-green-600 dark:text-green-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 hover:shadow-md dark:hover:shadow-xl transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 font-medium transition-colors duration-300">Average Rating</p>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-1 transition-colors duration-300">{{ number_format($this->overviewStats['average_rating'], 1) }}</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <div class="flex text-yellow-400 dark:text-yellow-300 mr-2">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star{{ $i <= $this->overviewStats['average_rating'] ? '' : '-o' }}"></i>
                            @endfor
                        </div>
                        <span class="text-gray-500 dark:text-gray-400 transition-colors duration-300">{{ $this->overviewStats['total_reviews'] }} reviews</span>
                    </div>
                </div>
                <div class="bg-yellow-100 dark:bg-yellow-900/30 p-3 rounded-full transition-colors duration-300">
                    <i class="fas fa-star text-yellow-600 dark:text-yellow-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 hover:shadow-md dark:hover:shadow-xl transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 font-medium transition-colors duration-300">Total Earnings</p>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-1 transition-colors duration-300">₦{{ number_format($this->overviewStats['total_earnings'], 0) }}</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="text-green-600 dark:text-green-400 font-medium transition-colors duration-300">₦{{ number_format($this->overviewStats['monthly_earnings'], 0) }}</span>
                        <span class="text-gray-500 dark:text-gray-400 ml-1 transition-colors duration-300">this month</span>
                    </div>
                </div>
                <div class="bg-purple-100 dark:bg-purple-900/30 p-3 rounded-full transition-colors duration-300">
                    <i class="fas fa-wallet text-purple-600 dark:text-purple-400 text-xl"></i>
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
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 transition-colors duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white transition-colors duration-300">Course Performance</h2>
                    <a href="{{ route('my-course') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 text-sm font-medium transition-colors duration-300">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                <div class="space-y-4">
                    @forelse($this->coursePerformance->take(5) as $course)
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200">
                        <div class="flex items-center space-x-3">
                            @if($course['thumbnail'])
                            <img src="{{ $course['thumbnail'] }}" alt="{{ $course['title'] }}" class="w-12 h-12 rounded-lg object-cover">
                            @else
                            <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center transition-colors duration-300">
                                <i class="fas fa-book text-indigo-600 dark:text-indigo-400"></i>
                            </div>
                            @endif
                            
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-white transition-colors duration-300">{{ \Str::limit($course['title'], 40) }}</h4>
                                <div class="flex items-center space-x-4 text-sm text-gray-600 dark:text-gray-400 mt-1 transition-colors duration-300">
                                    <span>{{ $course['total_enrollments'] }} students</span>
                                    <span>{{ $course['completion_rate'] }}% completion</span>
                                    <span class="flex items-center">
                                        <i class="fas fa-star text-yellow-400 dark:text-yellow-300 mr-1"></i>
                                        {{ number_format($course['average_rating'], 1) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-right">
                            <div class="text-lg font-bold text-gray-900 dark:text-white transition-colors duration-300">₦{{ number_format($course['revenue'], 0) }}</div>
                            <span class="px-2 py-1 text-xs rounded-full {{ $course['status'] === 'Published' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : ($course['status'] === 'Pending Approval' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300') }} transition-colors duration-300">
                                {{ $course['status'] }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <i class="fas fa-book-open text-gray-400 dark:text-gray-500 text-4xl mb-4"></i>
                        <p class="text-gray-600 dark:text-gray-400 mb-4 transition-colors duration-300">No courses created yet</p>
                        <a href="{{ route('create.course') }}" class="bg-indigo-600 dark:bg-indigo-500 hover:bg-indigo-700 dark:hover:bg-indigo-600 text-white px-4 py-2 rounded-lg transition-colors">
                            Create Your First Course
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif

            <!-- Student Analytics -->
            @if($showWidgets['student_analytics'])
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 transition-colors duration-300">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 transition-colors duration-300">Student Analytics</h2>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Enrollment Trends Chart -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 transition-colors duration-300">Enrollment Trends</h3>
                        <div class="h-48">
                            <canvas id="enrollmentTrendsChart"></canvas>
                        </div>
                    </div>
                    
                    <!-- Student Engagement Metrics -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 transition-colors duration-300">Student Engagement</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400 transition-colors duration-300">Total Students</span>
                                <span class="text-lg font-bold text-gray-900 dark:text-white transition-colors duration-300">{{ number_format($this->studentAnalytics['total_students']) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400 transition-colors duration-300">Active Students</span>
                                <span class="text-lg font-bold text-green-600 dark:text-green-400 transition-colors duration-300">{{ $this->studentAnalytics['active_students'] }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400 transition-colors duration-300">Engagement Rate</span>
                                <div class="flex items-center space-x-2">
                                    <div class="w-16 bg-gray-200 dark:bg-gray-600 rounded-full h-2 transition-colors duration-300">
                                        <div class="bg-gradient-to-r from-green-400 to-blue-500 dark:from-green-300 dark:to-blue-400 h-2 rounded-full" 
                                             style="width: {{ $this->studentAnalytics['engagement_rate'] }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white transition-colors duration-300">{{ $this->studentAnalytics['engagement_rate'] }}%</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400 transition-colors duration-300">Completion Rate</span>
                                <span class="text-lg font-bold text-purple-600 dark:text-purple-400 transition-colors duration-300">{{ $this->studentAnalytics['completion_rate'] }}%</span>
                            </div>
                        </div>
                        
                        <!-- Top Performing Students -->
                        @if(count($this->studentAnalytics['top_students']) > 0)
                        <div class="mt-6">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 transition-colors duration-300">Top Performing Students</h4>
                            <div class="space-y-2">
                                @foreach($this->studentAnalytics['top_students'] as $student)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400 transition-colors duration-300">{{ $student['name'] }}</span>
                                    <span class="font-medium text-indigo-600 dark:text-indigo-400 transition-colors duration-300">{{ $student['average_score'] }}%</span>
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
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 transition-colors duration-300">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 transition-colors duration-300">Earnings Overview</h2>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Daily Earnings Chart -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 transition-colors duration-300">Daily Earnings ({{ $selectedTimeframe === '7days' ? 'Last 7 Days' : ($selectedTimeframe === '30days' ? 'Last 30 Days' : 'Last 90 Days') }})</h3>
                        <div class="h-48">
                            <canvas id="earningsChart"></canvas>
                        </div>
                    </div>
                    
                    <!-- Top Earning Courses -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 transition-colors duration-300">Top Earning Courses</h3>
                        <div class="space-y-3">
                            @forelse($this->earningsOverview['top_earning_courses'] as $course)
                            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg transition-colors duration-300">
                                <span class="text-sm text-gray-600 dark:text-gray-400 truncate transition-colors duration-300">{{ \Str::limit($course['title'], 30) }}</span>
                                <span class="text-sm font-bold text-gray-900 dark:text-white transition-colors duration-300">₦{{ number_format($course['revenue'], 0) }}</span>
                            </div>
                            @empty
                            <p class="text-gray-500 dark:text-gray-400 text-sm">No earnings data available</p>
                            @endforelse
                        </div>
                        
                        <!-- Earnings Summary -->
                        <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-600 transition-colors duration-300">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm text-gray-600 dark:text-gray-400 transition-colors duration-300">Pending Earnings</span>
                                <span class="text-sm font-bold text-orange-600 dark:text-orange-400 transition-colors duration-300">₦{{ number_format($this->earningsOverview['pending_earnings'], 0) }}</span>
                            </div>
                            <a href="{{ route('instructor.earnings') }}" class="block text-center bg-indigo-600 dark:bg-indigo-500 hover:bg-indigo-700 dark:hover:bg-indigo-600 text-white py-2 rounded-lg text-sm font-medium transition-colors">
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
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 transition-colors duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white transition-colors duration-300">Certificate Requests</h2>
                    <span class="bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 text-xs font-medium px-2 py-1 rounded-full transition-colors duration-300">
                        {{ count($this->certificateRequests) }} pending
                    </span>
                </div>
                
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @forelse($this->certificateRequests as $request)
                    <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 transition-colors duration-300">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 dark:text-white transition-colors duration-300">{{ $request['student_name'] }}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400 transition-colors duration-300">{{ \Str::limit($request['course_title'], 30) }}</p>
                                <div class="flex items-center mt-2 text-xs text-gray-500 dark:text-gray-400 transition-colors duration-300">
                                    <span>Grade: {{ $request['grade'] ?? 'N/A' }}</span>
                                    <span class="mx-2">•</span>
                                    <span>{{ $request['requested_at']->format('M j, Y') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex space-x-2 mt-3">
                            <button wire:click="approveCertificate({{ $request['id'] }})" 
                                    class="flex-1 bg-green-600 dark:bg-green-500 hover:bg-green-700 dark:hover:bg-green-600 text-white text-xs py-2 rounded-lg transition-colors">
                                <i class="fas fa-check mr-1"></i>
                                Approve
                            </button>
                            <button onclick="rejectCertificate({{ $request['id'] }})" 
                                    class="flex-1 bg-red-600 dark:bg-red-500 hover:bg-red-700 dark:hover:bg-red-600 text-white text-xs py-2 rounded-lg transition-colors">
                                <i class="fas fa-times mr-1"></i>
                                Reject
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-6">
                        <i class="fas fa-certificate text-gray-400 dark:text-gray-500 text-3xl mb-2"></i>
                        <p class="text-gray-600 dark:text-gray-400 transition-colors duration-300">No pending certificate requests</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif

            <!-- Recent Enrollments -->
            @if($showWidgets['recent_enrollments'])
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 transition-colors duration-300">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 transition-colors duration-300">Recent Enrollments</h2>
                
                <div class="space-y-3 max-h-80 overflow-y-auto">
                    @forelse($this->recentEnrollments as $enrollment)
                    <div class="flex items-center space-x-3 p-3 border border-gray-200 dark:border-gray-600 rounded-lg transition-colors duration-300">
                        <div class="flex-shrink-0 w-10 h-10 bg-indigo-100 dark:bg-indigo-900/30 rounded-full flex items-center justify-center transition-colors duration-300">
                            <i class="fas fa-user text-indigo-600 dark:text-indigo-400"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate transition-colors duration-300">{{ $enrollment['student_name'] }}</h4>
                            <p class="text-xs text-gray-600 dark:text-gray-400 truncate transition-colors duration-300">{{ \Str::limit($enrollment['course_title'], 25) }}</p>
                            <div class="flex items-center mt-1">
                                <div class="w-16 bg-gray-200 dark:bg-gray-600 rounded-full h-1 transition-colors duration-300">
                                    <div class="bg-indigo-500 dark:bg-indigo-400 h-1 rounded-full" style="width: {{ $enrollment['progress'] }}%"></div>
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 ml-2 transition-colors duration-300">{{ $enrollment['progress'] }}%</span>
                            </div>
                        </div>
                        <span class="text-xs text-gray-500 dark:text-gray-400 transition-colors duration-300">{{ $enrollment['enrolled_at']->format('M j') }}</span>
                    </div>
                    @empty
                    <div class="text-center py-6">
                        <i class="fas fa-user-plus text-gray-400 dark:text-gray-500 text-3xl mb-2"></i>
                        <p class="text-gray-600 dark:text-gray-400 transition-colors duration-300">No recent enrollments</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif

            <!-- Course Reviews -->
            @if($showWidgets['course_reviews'])
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 transition-colors duration-300">
                <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6 transition-colors duration-300">Recent Reviews</h2>
                
                <div class="space-y-4 max-h-96 overflow-y-auto">
                    @forelse($this->courseReviews as $review)
                    <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 transition-colors duration-300">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white transition-colors duration-300">{{ $review['student_name'] }}</h4>
                                <p class="text-xs text-gray-600 dark:text-gray-400 transition-colors duration-300">{{ \Str::limit($review['course_title'], 30) }}</p>
                            </div>
                            <div class="flex items-center">
                                <div class="flex text-yellow-400 dark:text-yellow-300 mr-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star{{ $i <= $review['rating'] ? '' : '-o' }} text-xs"></i>
                                    @endfor
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 transition-colors duration-300">{{ $review['created_at']->format('M j') }}</span>
                            </div>
                        </div>
                        @if($review['comment'])
                        <p class="text-sm text-gray-700 dark:text-gray-300 transition-colors duration-300">{{ \Str::limit($review['comment'], 100) }}</p>
                        @endif
                    </div>
                    @empty
                    <div class="text-center py-6">
                        <i class="fas fa-star text-gray-400 dark:text-gray-500 text-3xl mb-2"></i>
                        <p class="text-gray-600 dark:text-gray-400 transition-colors duration-300">No recent reviews</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif

            <!-- Marketplace Items -->
            @if($showWidgets['marketplace_items'] && count($this->marketplaceItems) > 0)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 transition-colors duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white transition-colors duration-300">Marketplace Items</h2>
                    <a href="{{ route('marketplace.seller.listings') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 text-sm font-medium transition-colors duration-300">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                <div class="space-y-3">
                    @foreach($this->marketplaceItems as $item)
                    <div class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-600 rounded-lg transition-colors duration-300">
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate transition-colors duration-300">{{ \Str::limit($item['title'], 25) }}</h4>
                            <div class="flex items-center space-x-2 mt-1 text-xs text-gray-600 dark:text-gray-400 transition-colors duration-300">
                                <span>{{ $item['type'] }}</span>
                                <span class="px-2 py-1 rounded-full {{ $item['status'] === 'Approved' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300' }} transition-colors duration-300">
                                    {{ $item['status'] }}
                                </span>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-bold text-gray-900 dark:text-white transition-colors duration-300">{{ $item['price'] }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 transition-colors duration-300">{{ $item['sales'] }} sales</div>
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
    // Get current theme for chart colors
    const isDark = document.documentElement.classList.contains('dark');
    
    // Chart.js default configuration for dark mode
    Chart.defaults.color = isDark ? '#D1D5DB' : '#374151';
    Chart.defaults.borderColor = isDark ? '#374151' : '#E5E7EB';
    Chart.defaults.backgroundColor = isDark ? '#1F2937' : '#FFFFFF';

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
                    borderColor: isDark ? '#8B5CF6' : '#6366F1',
                    backgroundColor: isDark ? 'rgba(139, 92, 246, 0.1)' : 'rgba(99, 102, 241, 0.1)',
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
                        ticks: {
                            color: isDark ? '#9CA3AF' : '#6B7280'
                        },
                        grid: {
                            color: isDark ? 'rgba(55, 65, 81, 0.3)' : 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        ticks: {
                            color: isDark ? '#9CA3AF' : '#6B7280'
                        },
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
                    backgroundColor: isDark ? 'rgba(168, 85, 247, 0.8)' : 'rgba(147, 51, 234, 0.8)',
                    borderColor: isDark ? '#A855F7' : '#9333EA',
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
                            color: isDark ? '#9CA3AF' : '#6B7280',
                            callback: function(value) {
                                return '₦' + value.toLocaleString();
                            }
                        },
                        grid: {
                            color: isDark ? 'rgba(55, 65, 81, 0.3)' : 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        ticks: {
                            color: isDark ? '#9CA3AF' : '#6B7280'
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    // Listen for dark mode changes and update charts
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                // Reload charts when dark mode changes
                setTimeout(() => {
                    location.reload();
                }, 100);
            }
        });
    });
    
    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class']
    });
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