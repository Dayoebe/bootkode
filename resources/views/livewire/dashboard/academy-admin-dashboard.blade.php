<div class="min-h-screen bg-themed-primary transition-colors duration-300 p-4 md:p-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-themed-secondary rounded-2xl shadow-sm border border-themed-primary p-6 transition-colors duration-300">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center space-y-4 lg:space-y-0">
                <div>
                    <h1 class="text-3xl font-bold text-themed-primary flex items-center transition-colors duration-300">
                        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 dark:from-indigo-400 dark:to-purple-500 p-2 rounded-xl mr-3">
                            <i class="fas fa-graduation-cap text-white text-lg"></i>
                        </div>
                        Academy Admin Dashboard
                    </h1>
                    <p class="text-themed-secondary mt-2 transition-colors duration-300">Welcome back, {{ auth()->user()->name }}! Manage your academy with confidence.</p>
                    <div class="flex items-center mt-3 space-x-4 text-sm text-themed-tertiary transition-colors duration-300">
                        <span class="flex items-center"><i class="fas fa-clock mr-1"></i> Last updated: {{ now()->format('M d, Y H:i') }}</span>
                        <span class="flex items-center"><i class="fas fa-users mr-1"></i> {{ $this->overviewStats['total_students'] }} Students</span>
                    </div>
                </div>
                
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Timeframe Selector -->
                    <div class="flex bg-themed-tertiary rounded-xl p-1 border border-themed-primary transition-colors duration-300">
                        @foreach(['7days' => '7d', '30days' => '30d', '90days' => '90d'] as $value => $label)
                            <button 
                                wire:click="updateTimeframe('{{ $value }}')"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ $selectedTimeframe === $value ? 'bg-themed-secondary text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-themed-secondary hover:text-themed-primary' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                    
                    <!-- Quick Actions -->
                    <button wire:click="toggleQuickActionModal" 
                            class="bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-500 dark:to-purple-500 hover:from-indigo-700 hover:to-purple-700 dark:hover:from-indigo-600 dark:hover:to-purple-600 text-white px-5 py-2 rounded-xl font-medium transition-all duration-200 flex items-center shadow-lg">
                        <i class="fas fa-lightning-bolt mr-2"></i>
                        Quick Actions
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Key Metrics Grid -->
    @if($showWidgets['overview_stats'])
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Students Card -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 dark:text-blue-200 text-sm font-medium">Total Students</p>
                    <h3 class="text-3xl font-bold mt-1">{{ number_format($this->overviewStats['total_students']) }}</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="bg-blue-400 dark:bg-blue-500 bg-opacity-30 px-2 py-1 rounded-full text-blue-100 dark:text-blue-200">
                            +{{ $this->overviewStats['new_students_today'] }} today
                        </span>
                    </div>
                </div>
                <div class="bg-blue-400 dark:bg-blue-500 bg-opacity-30 p-3 rounded-xl">
                    <i class="fas fa-user-graduate text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Courses Card -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 dark:from-green-600 dark:to-green-700 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 dark:text-green-200 text-sm font-medium">Active Courses</p>
                    <h3 class="text-3xl font-bold mt-1">{{ number_format($this->overviewStats['published_courses']) }}</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="bg-green-400 dark:bg-green-500 bg-opacity-30 px-2 py-1 rounded-full text-green-100 dark:text-green-200">
                            {{ $this->overviewStats['pending_courses'] }} pending
                        </span>
                    </div>
                </div>
                <div class="bg-green-400 dark:bg-green-500 bg-opacity-30 p-3 rounded-xl">
                    <i class="fas fa-book-open text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Enrollments Card -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 dark:from-purple-600 dark:to-purple-700 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 dark:text-purple-200 text-sm font-medium">Total Enrollments</p>
                    <h3 class="text-3xl font-bold mt-1">{{ number_format($this->overviewStats['total_enrollments']) }}</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="bg-purple-400 dark:bg-purple-500 bg-opacity-30 px-2 py-1 rounded-full text-purple-100 dark:text-purple-200">
                            {{ $this->overviewStats['completion_rate'] }}% completion rate
                        </span>
                    </div>
                </div>
                <div class="bg-purple-400 dark:bg-purple-500 bg-opacity-30 p-3 rounded-xl">
                    <i class="fas fa-chart-line text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Support Card -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 dark:from-orange-600 dark:to-orange-700 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 dark:text-orange-200 text-sm font-medium">Open Tickets</p>
                    <h3 class="text-3xl font-bold mt-1">{{ $this->overviewStats['open_tickets'] }}</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="bg-orange-400 dark:bg-orange-500 bg-opacity-30 px-2 py-1 rounded-full text-orange-100 dark:text-orange-200">
                            {{ $this->overviewStats['pending_certificates'] }} cert. pending
                        </span>
                    </div>
                </div>
                <div class="bg-orange-400 dark:bg-orange-500 bg-opacity-30 p-3 rounded-xl">
                    <i class="fas fa-headset text-2xl"></i>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Main Dashboard Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <!-- Left Column -->
        <div class="xl:col-span-2 space-y-8">
            <!-- Student Growth Analytics -->
            @if($showWidgets['student_analytics'])
            <div class="bg-themed-secondary rounded-2xl shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-themed-primary flex items-center transition-colors duration-300">
                        <i class="fas fa-chart-area text-indigo-600 dark:text-indigo-400 mr-2"></i>
                        Student Growth & Engagement
                    </h2>
                    <div class="flex items-center space-x-2 text-sm text-themed-tertiary transition-colors duration-300">
                        <span class="bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 px-3 py-1 rounded-full font-medium transition-colors duration-300">
                            Last {{ $selectedTimeframe === '7days' ? '7 days' : ($selectedTimeframe === '30days' ? '30 days' : '90 days') }}
                        </span>
                    </div>
                </div>
                <div class="h-80">
                    <canvas id="studentGrowthChart"></canvas>
                </div>
            </div>
            @endif

            <!-- Course Performance -->
            @if($showWidgets['course_management'])
            <div class="bg-themed-secondary rounded-2xl shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-themed-primary flex items-center transition-colors duration-300">
                        <i class="fas fa-trophy text-yellow-500 dark:text-yellow-400 mr-2"></i>
                        Top Performing Courses
                    </h2>
                    <a href="{{ route('all-course') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 text-sm font-medium flex items-center transition-colors duration-300">
                        Manage All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="space-y-4">
                    @forelse($this->coursePerformance as $index => $course)
                    <div class="flex items-center justify-between p-4 bg-themed-tertiary rounded-xl hover:bg-themed-secondary transition-all duration-200">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-600 dark:from-indigo-400 dark:to-purple-500 rounded-lg flex items-center justify-center text-white font-bold">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-themed-primary transition-colors duration-300">{{ Str::limit($course['title'], 45) }}</h4>
                                <p class="text-sm text-themed-secondary transition-colors duration-300">by {{ $course['instructor'] }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-6 text-sm">
                            <div class="text-center">
                                <div class="font-bold text-indigo-600 dark:text-indigo-400 transition-colors duration-300">{{ $course['enrollments'] }}</div>
                                <div class="text-themed-tertiary transition-colors duration-300">enrollments</div>
                            </div>
                            <div class="text-center">
                                <div class="font-bold text-green-600 dark:text-green-400 transition-colors duration-300">{{ $course['completion_rate'] }}%</div>
                                <div class="text-themed-tertiary transition-colors duration-300">completion</div>
                            </div>
                            <div class="text-center">
                                <div class="font-bold text-yellow-600 dark:text-yellow-400 flex items-center transition-colors duration-300">
                                    {{ number_format($course['rating'], 1) }}
                                    <i class="fas fa-star ml-1 text-xs"></i>
                                </div>
                                <div class="text-themed-tertiary transition-colors duration-300">rating</div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-themed-tertiary transition-colors duration-300">
                        <i class="fas fa-book-open text-4xl mb-4 text-themed-tertiary opacity-50"></i>
                        <p>No course performance data available</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif

            <!-- Learning Progress Analytics -->
            @if($showWidgets['learning_progress'])
            <div class="bg-themed-secondary rounded-2xl shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-themed-primary flex items-center transition-colors duration-300">
                        <i class="fas fa-graduation-cap text-green-600 dark:text-green-400 mr-2"></i>
                        Learning Progress Analytics
                    </h2>
                    <a href="{{ route('learning.analytics') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 text-sm font-medium flex items-center transition-colors duration-300">
                        View Details <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="h-64">
                    <canvas id="learningProgressChart"></canvas>
                </div>
            </div>
            @endif

            <!-- Category Performance -->
            @if($showWidgets['course_management'])
            <div class="bg-themed-secondary rounded-2xl shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-themed-primary flex items-center transition-colors duration-300">
                        <i class="fas fa-layer-group text-indigo-600 dark:text-indigo-400 mr-2"></i>
                        Category Performance
                    </h2>
                    <a href="{{ route('course-categories') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 text-sm font-medium flex items-center transition-colors duration-300">
                        Manage <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="space-y-3">
                    @forelse($this->categoryStats as $category)
                    <div class="flex items-center justify-between p-4 bg-themed-tertiary rounded-xl hover:bg-themed-secondary transition-colors duration-200">
                        <div class="flex-1">
                            <h4 class="font-semibold text-themed-primary transition-colors duration-300">{{ $category['name'] }}</h4>
                            <p class="text-sm text-themed-secondary transition-colors duration-300">{{ $category['courses_count'] }} courses</p>
                        </div>
                        <div class="flex items-center space-x-4 text-sm">
                            <div class="text-center">
                                <div class="font-bold text-indigo-600 dark:text-indigo-400 transition-colors duration-300">{{ $category['enrollments'] }}</div>
                                <div class="text-themed-tertiary transition-colors duration-300">enrollments</div>
                            </div>
                            <div class="text-center">
                                <div class="font-bold text-green-600 dark:text-green-400 transition-colors duration-300">{{ $category['popularity'] }}</div>
                                <div class="text-themed-tertiary transition-colors duration-300">avg/course</div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-6 text-themed-tertiary transition-colors duration-300">
                        <i class="fas fa-layer-group text-3xl mb-3 opacity-50"></i>
                        <p>No category data available</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column -->
        <div class="space-y-8">
            <!-- Instructor Performance -->
            @if($showWidgets['instructor_performance'])
            <div class="bg-themed-secondary rounded-2xl shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-themed-primary flex items-center transition-colors duration-300">
                        <i class="fas fa-chalkboard-teacher text-purple-600 dark:text-purple-400 mr-2"></i>
                        Top Instructors
                    </h2>
                    <a href="{{ route('user-management', ['role' => 'instructor']) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 text-sm font-medium transition-colors duration-300">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="space-y-4">
                    @forelse($this->instructorPerformance as $instructor)
                    <div class="flex items-center space-x-4 p-3 rounded-xl hover:bg-themed-tertiary transition-colors duration-200">
                        <div class="flex-shrink-0">
                            @if($instructor['profile_picture'])
                                <img src="{{ Storage::url($instructor['profile_picture']) }}" 
                                     alt="{{ $instructor['name'] }}" 
                                     class="w-12 h-12 rounded-full object-cover border-2 border-indigo-100 dark:border-indigo-700">
                            @else
                                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-600 dark:from-purple-400 dark:to-indigo-500 rounded-full flex items-center justify-center text-white font-semibold text-lg">
                                    {{ substr($instructor['name'], 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-semibold text-themed-primary truncate transition-colors duration-300">{{ $instructor['name'] }}</h4>
                            <p class="text-sm text-themed-secondary transition-colors duration-300">{{ $instructor['courses_count'] }} courses</p>
                        </div>
                        <div class="text-right">
                            <div class="flex items-center text-sm font-semibold text-green-600 dark:text-green-400 transition-colors duration-300">
                                {{ $instructor['total_enrollments'] }}
                                <i class="fas fa-users ml-1"></i>
                            </div>
                            <div class="flex items-center text-xs text-yellow-600 dark:text-yellow-400 transition-colors duration-300">
                                {{ $instructor['average_rating'] }}
                                <i class="fas fa-star ml-1"></i>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-6 text-themed-tertiary transition-colors duration-300">
                        <i class="fas fa-chalkboard-teacher text-3xl mb-3 opacity-50"></i>
                        <p>No instructor data available</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif

            <!-- Content Approval Queue -->
            @if($showWidgets['content_approval'])
            <div class="bg-themed-secondary rounded-2xl shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-themed-primary flex items-center transition-colors duration-300">
                        <i class="fas fa-clipboard-check text-orange-600 dark:text-orange-400 mr-2"></i>
                        Pending Approvals
                    </h2>
                    <div class="flex items-center space-x-1">
                        <span class="bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300 text-xs font-medium px-2 py-1 rounded-full transition-colors duration-300">
                            {{ count($this->contentApprovalQueue['courses']) + count($this->contentApprovalQueue['certificates']) + count($this->contentApprovalQueue['blog_posts']) }}
                        </span>
                    </div>
                </div>
                
                <!-- Courses Pending Approval -->
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-themed-primary mb-3 flex items-center transition-colors duration-300">
                        <i class="fas fa-book text-blue-600 dark:text-blue-400 mr-2"></i>
                        Courses ({{ count($this->contentApprovalQueue['courses']) }})
                    </h3>
                    @forelse($this->contentApprovalQueue['courses'] as $course)
                    <div class="flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg mb-2 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors duration-200">
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-medium text-themed-primary truncate transition-colors duration-300">{{ $course['title'] }}</h4>
                            <p class="text-xs text-themed-secondary transition-colors duration-300">{{ $course['instructor'] }} • {{ $course['created_at'] }}</p>
                        </div>
                        <div class="flex space-x-2">
                            <button wire:click="approveCourse({{ $course['id'] }})" 
                                    class="text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 p-1 rounded transition-colors"
                                    title="Approve Course">
                                <i class="fas fa-check text-sm"></i>
                            </button>
                            <button class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 p-1 rounded transition-colors"
                                    title="Reject Course">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                    </div>
                    @empty
                    <p class="text-themed-tertiary text-sm transition-colors duration-300">No pending courses</p>
                    @endforelse
                </div>

                <!-- Certificates Pending Approval -->
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-themed-primary mb-3 flex items-center transition-colors duration-300">
                        <i class="fas fa-award text-purple-600 dark:text-purple-400 mr-2"></i>
                        Certificates ({{ count($this->contentApprovalQueue['certificates']) }})
                    </h3>
                    @forelse($this->contentApprovalQueue['certificates'] as $certificate)
                    <div class="flex items-center justify-between p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg mb-2 hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors duration-200">
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-medium text-themed-primary transition-colors duration-300">{{ $certificate['user'] }}</h4>
                            <p class="text-xs text-themed-secondary transition-colors duration-300">{{ Str::limit($certificate['course'], 30) }} • {{ $certificate['created_at'] }}</p>
                        </div>
                        <div class="flex space-x-2">
                            <button wire:click="approveCertificate({{ $certificate['id'] }})" 
                                    class="text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 p-1 rounded transition-colors"
                                    title="Approve Certificate">
                                <i class="fas fa-check text-sm"></i>
                            </button>
                            <button class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 p-1 rounded transition-colors"
                                    title="Reject Certificate">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                    </div>
                    @empty
                    <p class="text-themed-tertiary text-sm transition-colors duration-300">No pending certificates</p>
                    @endforelse
                </div>

                <!-- Blog Posts Pending Approval -->
                <div>
                    <h3 class="text-sm font-semibold text-themed-primary mb-3 flex items-center transition-colors duration-300">
                        <i class="fas fa-pen text-green-600 dark:text-green-400 mr-2"></i>
                        Blog Posts ({{ count($this->contentApprovalQueue['blog_posts']) }})
                    </h3>
                    @forelse($this->contentApprovalQueue['blog_posts'] as $post)
                    <div class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 rounded-lg mb-2 hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors duration-200">
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-medium text-themed-primary truncate transition-colors duration-300">{{ $post['title'] }}</h4>
                            <p class="text-xs text-themed-secondary transition-colors duration-300">{{ $post['author'] }} • {{ $post['created_at'] }}</p>
                        </div>
                        <div class="flex space-x-2">
                            <button class="text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 p-1 rounded transition-colors"
                                    title="Approve Post">
                                <i class="fas fa-check text-sm"></i>
                            </button>
                            <button class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 p-1 rounded transition-colors"
                                    title="Reject Post">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                    </div>
                    @empty
                    <p class="text-themed-tertiary text-sm transition-colors duration-300">No pending blog posts</p>
                    @endforelse
                </div>
            </div>
            @endif

            <!-- Recent Activities -->
            @if($showWidgets['recent_activities'])
            <div class="bg-themed-secondary rounded-2xl shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-themed-primary flex items-center transition-colors duration-300">
                        <i class="fas fa-history text-indigo-600 dark:text-indigo-400 mr-2"></i>
                        Recent Activities
                    </h2>
                </div>
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @forelse($this->recentActivities as $activity)
                    <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-themed-tertiary transition-colors duration-200">
                        <div class="flex-shrink-0 w-8 h-8 bg-themed-tertiary rounded-full flex items-center justify-center transition-colors duration-300">
                            <i class="{{ $activity['icon'] }} text-xs {{ $activity['color'] }}"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-themed-primary transition-colors duration-300">{{ $activity['description'] }}</p>
                            <p class="text-xs text-themed-tertiary transition-colors duration-300">{{ $activity['causer'] }} • {{ $activity['created_at'] }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-6 text-themed-tertiary transition-colors duration-300">
                        <i class="fas fa-history text-3xl mb-3 opacity-50"></i>
                        <p>No recent activities</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif

            <!-- Support Overview -->
            @if($showWidgets['support_overview'])
            <div class="bg-themed-secondary rounded-2xl shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-themed-primary flex items-center transition-colors duration-300">
                        <i class="fas fa-headset text-blue-600 dark:text-blue-400 mr-2"></i>
                        Support Overview
                    </h2>
                    <a href="{{ route('support.tickets') }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 text-sm font-medium transition-colors duration-300">
                        Manage <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                <!-- Support Stats -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="text-center p-4 bg-red-50 dark:bg-red-900/20 rounded-xl transition-colors duration-300">
                        <div class="text-2xl font-bold text-red-600 dark:text-red-400 transition-colors duration-300">{{ $this->supportOverview['open_tickets'] }}</div>
                        <div class="text-xs text-red-600 dark:text-red-400 font-medium transition-colors duration-300">Open Tickets</div>
                    </div>
                    <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-xl transition-colors duration-300">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400 transition-colors duration-300">{{ $this->supportOverview['resolution_rate'] }}%</div>
                        <div class="text-xs text-green-600 dark:text-green-400 font-medium transition-colors duration-300">Resolution Rate</div>
                    </div>
                </div>

                <!-- Recent Tickets -->
                <div class="space-y-3">
                    <h3 class="text-sm font-semibold text-themed-primary transition-colors duration-300">Recent Tickets</h3>
                    @forelse($this->supportOverview['recent_tickets'] as $ticket)
                    <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-themed-tertiary transition-colors duration-200">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center {{ $ticket['status'] === 'open' ? 'bg-red-100 dark:bg-red-900/30' : 'bg-green-100 dark:bg-green-900/30' }} transition-colors duration-300">
                            <i class="fas {{ $ticket['status'] === 'open' ? 'fa-exclamation text-red-600 dark:text-red-400' : 'fa-check text-green-600 dark:text-green-400' }} text-xs"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-themed-primary truncate transition-colors duration-300">{{ $ticket['subject'] }}</p>
                            <p class="text-xs text-themed-secondary transition-colors duration-300">{{ $ticket['user'] }} • {{ $ticket['created_at'] }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-themed-tertiary text-sm transition-colors duration-300">No recent tickets</p>
                    @endforelse
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Quick Actions Modal -->
    @if($showQuickActionModal)
    <div class="fixed inset-0 bg-black/50 dark:bg-black/70 flex items-center justify-center z-50 p-4 transition-colors duration-300">
        <div class="bg-themed-secondary rounded-3xl p-8 w-full max-w-2xl transform transition-all duration-300 scale-100 max-h-[90vh] overflow-y-auto border border-themed-primary">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-2xl font-bold text-themed-primary flex items-center transition-colors duration-300">
                    <i class="fas fa-lightning-bolt text-indigo-600 dark:text-indigo-400 mr-3"></i>
                    Quick Actions
                </h3>
                <button wire:click="toggleQuickActionModal" class="text-themed-tertiary hover:text-themed-secondary transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Course Management -->
                <button wire:click="quickAction('manage_courses')" 
                        class="flex items-center p-4 text-left bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-xl transition-all duration-200 group border border-transparent hover:border-blue-200 dark:hover:border-blue-800">
                    <div class="bg-blue-600 dark:bg-blue-500 p-3 rounded-lg mr-4 group-hover:bg-blue-700 dark:group-hover:bg-blue-600 transition-colors">
                        <i class="fas fa-book-open text-white"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-themed-primary transition-colors duration-300">Manage Courses</div>
                        <div class="text-sm text-themed-secondary transition-colors duration-300">View and organize all courses</div>
                    </div>
                </button>

                <!-- Student Management -->
                <button wire:click="quickAction('manage_students')"
                        class="flex items-center p-4 text-left bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/30 rounded-xl transition-all duration-200 group border border-transparent hover:border-green-200 dark:hover:border-green-800">
                    <div class="bg-green-600 dark:bg-green-500 p-3 rounded-lg mr-4 group-hover:bg-green-700 dark:group-hover:bg-green-600 transition-colors">
                        <i class="fas fa-user-graduate text-white"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-themed-primary transition-colors duration-300">Manage Students</div>
                        <div class="text-sm text-themed-secondary transition-colors duration-300">Student administration panel</div>
                    </div>
                </button>

                <!-- Instructor Management -->
                <button wire:click="quickAction('manage_instructors')"
                        class="flex items-center p-4 text-left bg-purple-50 dark:bg-purple-900/20 hover:bg-purple-100 dark:hover:bg-purple-900/30 rounded-xl transition-all duration-200 group border border-transparent hover:border-purple-200 dark:hover:border-purple-800">
                    <div class="bg-purple-600 dark:bg-purple-500 p-3 rounded-lg mr-4 group-hover:bg-purple-700 dark:group-hover:bg-purple-600 transition-colors">
                        <i class="fas fa-chalkboard-teacher text-white"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-themed-primary transition-colors duration-300">Manage Instructors</div>
                        <div class="text-sm text-themed-secondary transition-colors duration-300">Instructor oversight and support</div>
                    </div>
                </button>

                <!-- Content Approval -->
                <button wire:click="quickAction('approve_content')"
                        class="flex items-center p-4 text-left bg-orange-50 dark:bg-orange-900/20 hover:bg-orange-100 dark:hover:bg-orange-900/30 rounded-xl transition-all duration-200 group border border-transparent hover:border-orange-200 dark:hover:border-orange-800">
                    <div class="bg-orange-600 dark:bg-orange-500 p-3 rounded-lg mr-4 group-hover:bg-orange-700 dark:group-hover:bg-orange-600 transition-colors">
                        <i class="fas fa-clipboard-check text-white"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-themed-primary transition-colors duration-300">Content Approval</div>
                        <div class="text-sm text-themed-secondary transition-colors duration-300">Review pending content</div>
                    </div>
                </button>

                <!-- Certificates -->
                <button wire:click="quickAction('view_certificates')"
                        class="flex items-center p-4 text-left bg-yellow-50 dark:bg-yellow-900/20 hover:bg-yellow-100 dark:hover:bg-yellow-900/30 rounded-xl transition-all duration-200 group border border-transparent hover:border-yellow-200 dark:hover:border-yellow-800">
                    <div class="bg-yellow-600 dark:bg-yellow-500 p-3 rounded-lg mr-4 group-hover:bg-yellow-700 dark:group-hover:bg-yellow-600 transition-colors">
                        <i class="fas fa-award text-white"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-themed-primary transition-colors duration-300">Certificates</div>
                        <div class="text-sm text-themed-secondary transition-colors duration-300">Manage certifications</div>
                    </div>
                </button>

                <!-- Support Tickets -->
                <button wire:click="quickAction('view_tickets')"
                        class="flex items-center p-4 text-left bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-xl transition-all duration-200 group border border-transparent hover:border-red-200 dark:hover:border-red-800">
                    <div class="bg-red-600 dark:bg-red-500 p-3 rounded-lg mr-4 group-hover:bg-red-700 dark:group-hover:bg-red-600 transition-colors">
                        <i class="fas fa-headset text-white"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-themed-primary transition-colors duration-300">Support Tickets</div>
                        <div class="text-sm text-themed-secondary transition-colors duration-300">Handle user support requests</div>
                    </div>
                </button>

                <!-- Categories -->
                <button wire:click="quickAction('manage_categories')"
                        class="flex items-center p-4 text-left bg-indigo-50 dark:bg-indigo-900/20 hover:bg-indigo-100 dark:hover:bg-indigo-900/30 rounded-xl transition-all duration-200 group border border-transparent hover:border-indigo-200 dark:hover:border-indigo-800">
                    <div class="bg-indigo-600 dark:bg-indigo-500 p-3 rounded-lg mr-4 group-hover:bg-indigo-700 dark:group-hover:bg-indigo-600 transition-colors">
                        <i class="fas fa-tags text-white"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-themed-primary transition-colors duration-300">Categories</div>
                        <div class="text-sm text-themed-secondary transition-colors duration-300">Organize course categories</div>
                    </div>
                </button>

                <!-- Analytics -->
                <button wire:click="quickAction('learning_analytics')"
                        class="flex items-center p-4 text-left bg-teal-50 dark:bg-teal-900/20 hover:bg-teal-100 dark:hover:bg-teal-900/30 rounded-xl transition-all duration-200 group border border-transparent hover:border-teal-200 dark:hover:border-teal-800">
                    <div class="bg-teal-600 dark:bg-teal-500 p-3 rounded-lg mr-4 group-hover:bg-teal-700 dark:group-hover:bg-teal-600 transition-colors">
                        <i class="fas fa-chart-bar text-white"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-themed-primary transition-colors duration-300">Learning Analytics</div>
                        <div class="text-sm text-themed-secondary transition-colors duration-300">Detailed performance insights</div>
                    </div>
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Widget Toggle Panel -->
    <div class="fixed bottom-6 right-6 z-40">
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" 
                    class="bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-500 dark:to-purple-500 shadow-lg rounded-full p-4 hover:shadow-xl dark:hover:shadow-2xl transition-all duration-200 text-white">
                <i class="fas fa-sliders-h"></i>
            </button>
            
            <div x-show="open" 
                 x-transition
                 @click.outside="open = false"
                 class="absolute bottom-full right-0 mb-3 bg-themed-secondary rounded-xl shadow-xl border border-themed-primary p-4 w-64 transition-colors duration-300">
                <h3 class="font-semibold text-themed-primary mb-4 flex items-center transition-colors duration-300">
                    <i class="fas fa-cog text-indigo-600 dark:text-indigo-400 mr-2"></i>
                    Dashboard Widgets
                </h3>
                <div class="space-y-3">
                    @foreach($showWidgets as $widget => $isVisible)
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" 
                               wire:model="showWidgets.{{ $widget }}" 
                               class="rounded border-themed-primary text-indigo-600 dark:text-indigo-400 focus:ring-indigo-500 dark:focus:ring-indigo-400 dark:bg-themed-tertiary transition-colors duration-300">
                        <span class="ml-3 text-sm text-themed-secondary group-hover:text-themed-primary transition-colors duration-300">
                            {{ ucwords(str_replace('_', ' ', $widget)) }}
                        </span>
                    </label>
                    @endforeach
                </div>
            </div>
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

    // Student Growth Chart
    const studentGrowthData = @json($this->studentGrowthData);
    if (studentGrowthData.length > 0) {
        const ctx1 = document.getElementById('studentGrowthChart').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: studentGrowthData.map(item => item.date),
                datasets: [{
                    label: 'New Students',
                    data: studentGrowthData.map(item => item.new_students),
                    borderColor: isDark ? '#8B5CF6' : '#6366F1',
                    backgroundColor: isDark ? 'rgba(139, 92, 246, 0.1)' : 'rgba(99, 102, 241, 0.1)',
                    fill: true,
                    tension: 0.4,
                }, {
                    label: 'Total Students',
                    data: studentGrowthData.map(item => item.total_students),
                    borderColor: isDark ? '#34D399' : '#10B981',
                    backgroundColor: isDark ? 'rgba(52, 211, 153, 0.1)' : 'rgba(16, 185, 129, 0.1)',
                    fill: false,
                    tension: 0.4,
                }, {
                    label: 'Daily Enrollments',
                    data: studentGrowthData.map(item => item.enrollments),
                    borderColor: isDark ? '#A78BFA' : '#8B5CF6',
                    backgroundColor: isDark ? 'rgba(167, 139, 250, 0.1)' : 'rgba(139, 92, 246, 0.1)',
                    fill: false,
                    tension: 0.4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: isDark ? '#D1D5DB' : '#374151'
                        }
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

    // Learning Progress Chart
    const learningProgressData = @json($this->learningAnalytics);
    if (learningProgressData.length > 0) {
        const ctx2 = document.getElementById('learningProgressChart').getContext('2d');
        new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: learningProgressData.map(item => item.date),
                datasets: [{
                    label: 'Course Completions',
                    data: learningProgressData.map(item => item.completions),
                    backgroundColor: isDark ? 'rgba(52, 211, 153, 0.8)' : 'rgba(16, 185, 129, 0.8)',
                    borderColor: isDark ? '#34D399' : '#10B981',
                    borderWidth: 1,
                    borderRadius: 4,
                }, {
                    label: 'New Enrollments',
                    data: learningProgressData.map(item => item.enrollments),
                    backgroundColor: isDark ? 'rgba(139, 92, 246, 0.8)' : 'rgba(99, 102, 241, 0.8)',
                    borderColor: isDark ? '#8B5CF6' : '#6366F1',
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: isDark ? '#D1D5DB' : '#374151'
                        }
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

// Auto-refresh dashboard data
setInterval(() => {
    @this.call('loadAllData');
}, {{ $refreshInterval }});
</script>
@endpush