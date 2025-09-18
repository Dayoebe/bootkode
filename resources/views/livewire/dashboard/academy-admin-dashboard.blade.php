<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-blue-50 p-4 md:p-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 backdrop-blur-sm bg-opacity-95">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center space-y-4 lg:space-y-0">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 p-2 rounded-xl mr-3">
                            <i class="fas fa-graduation-cap text-white text-lg"></i>
                        </div>
                        Academy Admin Dashboard
                    </h1>
                    <p class="text-gray-600 mt-2">Welcome back, {{ auth()->user()->name }}! Manage your academy with confidence.</p>
                    <div class="flex items-center mt-3 space-x-4 text-sm text-gray-500">
                        <span class="flex items-center"><i class="fas fa-clock mr-1"></i> Last updated: {{ now()->format('M d, Y H:i') }}</span>
                        <span class="flex items-center"><i class="fas fa-users mr-1"></i> {{ $this->overviewStats['total_students'] }} Students</span>
                    </div>
                </div>
                
                <div class="flex flex-wrap items-center gap-3">
                    <!-- Timeframe Selector -->
                    <div class="flex bg-gray-50 rounded-xl p-1 border">
                        @foreach(['7days' => '7d', '30days' => '30d', '90days' => '90d'] as $value => $label)
                            <button 
                                wire:click="updateTimeframe('{{ $value }}')"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 {{ $selectedTimeframe === $value ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                    
                    <!-- Quick Actions -->
                    <button wire:click="toggleQuickActionModal" 
                            class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-5 py-2 rounded-xl font-medium transition-all duration-200 flex items-center shadow-lg">
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
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Students</p>
                    <h3 class="text-3xl font-bold mt-1">{{ number_format($this->overviewStats['total_students']) }}</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="bg-blue-400 bg-opacity-30 px-2 py-1 rounded-full text-blue-100">
                            +{{ $this->overviewStats['new_students_today'] }} today
                        </span>
                    </div>
                </div>
                <div class="bg-blue-400 bg-opacity-30 p-3 rounded-xl">
                    <i class="fas fa-user-graduate text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Courses Card -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Active Courses</p>
                    <h3 class="text-3xl font-bold mt-1">{{ number_format($this->overviewStats['published_courses']) }}</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="bg-green-400 bg-opacity-30 px-2 py-1 rounded-full text-green-100">
                            {{ $this->overviewStats['pending_courses'] }} pending
                        </span>
                    </div>
                </div>
                <div class="bg-green-400 bg-opacity-30 p-3 rounded-xl">
                    <i class="fas fa-book-open text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Enrollments Card -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Total Enrollments</p>
                    <h3 class="text-3xl font-bold mt-1">{{ number_format($this->overviewStats['total_enrollments']) }}</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="bg-purple-400 bg-opacity-30 px-2 py-1 rounded-full text-purple-100">
                            {{ $this->overviewStats['completion_rate'] }}% completion rate
                        </span>
                    </div>
                </div>
                <div class="bg-purple-400 bg-opacity-30 p-3 rounded-xl">
                    <i class="fas fa-chart-line text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Support Card -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-6 text-white shadow-lg hover:shadow-xl transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium">Open Tickets</p>
                    <h3 class="text-3xl font-bold mt-1">{{ $this->overviewStats['open_tickets'] }}</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="bg-orange-400 bg-opacity-30 px-2 py-1 rounded-full text-orange-100">
                            {{ $this->overviewStats['pending_certificates'] }} cert. pending
                        </span>
                    </div>
                </div>
                <div class="bg-orange-400 bg-opacity-30 p-3 rounded-xl">
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
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-chart-area text-indigo-600 mr-2"></i>
                        Student Growth & Engagement
                    </h2>
                    <div class="flex items-center space-x-2 text-sm text-gray-500">
                        <span class="bg-indigo-100 text-indigo-600 px-3 py-1 rounded-full font-medium">
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
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-trophy text-yellow-500 mr-2"></i>
                        Top Performing Courses
                    </h2>
                    <a href="{{ route('all-course') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium flex items-center">
                        Manage All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="space-y-4">
                    @forelse($this->coursePerformance as $index => $course)
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl hover:from-indigo-50 hover:to-blue-50 transition-all duration-200">
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900">{{ Str::limit($course['title'], 45) }}</h4>
                                <p class="text-sm text-gray-600">by {{ $course['instructor'] }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-6 text-sm">
                            <div class="text-center">
                                <div class="font-bold text-indigo-600">{{ $course['enrollments'] }}</div>
                                <div class="text-gray-500">enrollments</div>
                            </div>
                            <div class="text-center">
                                <div class="font-bold text-green-600">{{ $course['completion_rate'] }}%</div>
                                <div class="text-gray-500">completion</div>
                            </div>
                            <div class="text-center">
                                <div class="font-bold text-yellow-600 flex items-center">
                                    {{ number_format($course['rating'], 1) }}
                                    <i class="fas fa-star ml-1 text-xs"></i>
                                </div>
                                <div class="text-gray-500">rating</div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-book-open text-4xl mb-4 text-gray-300"></i>
                        <p>No course performance data available</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif

            <!-- Learning Progress Analytics -->
            @if($showWidgets['learning_progress'])
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-graduation-cap text-green-600 mr-2"></i>
                        Learning Progress Analytics
                    </h2>
                    <a href="{{ route('learning.analytics') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium flex items-center">
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
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-layer-group text-indigo-600 mr-2"></i>
                        Category Performance
                    </h2>
                    <a href="{{ route('course-categories') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium flex items-center">
                        Manage <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="space-y-3">
                    @forelse($this->categoryStats as $category)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-indigo-50 transition-colors duration-200">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900">{{ $category['name'] }}</h4>
                            <p class="text-sm text-gray-600">{{ $category['courses_count'] }} courses</p>
                        </div>
                        <div class="flex items-center space-x-4 text-sm">
                            <div class="text-center">
                                <div class="font-bold text-indigo-600">{{ $category['enrollments'] }}</div>
                                <div class="text-gray-500">enrollments</div>
                            </div>
                            <div class="text-center">
                                <div class="font-bold text-green-600">{{ $category['popularity'] }}</div>
                                <div class="text-gray-500">avg/course</div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-6 text-gray-500">
                        <i class="fas fa-layer-group text-3xl mb-3 text-gray-300"></i>
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
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-chalkboard-teacher text-purple-600 mr-2"></i>
                        Top Instructors
                    </h2>
                    <a href="{{ route('user-management', ['role' => 'instructor']) }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="space-y-4">
                    @forelse($this->instructorPerformance as $instructor)
                    <div class="flex items-center space-x-4 p-3 rounded-xl hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex-shrink-0">
                            @if($instructor['profile_picture'])
                                <img src="{{ Storage::url($instructor['profile_picture']) }}" 
                                     alt="{{ $instructor['name'] }}" 
                                     class="w-12 h-12 rounded-full object-cover border-2 border-indigo-100">
                            @else
                                <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-semibold text-lg">
                                    {{ substr($instructor['name'], 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-semibold text-gray-900 truncate">{{ $instructor['name'] }}</h4>
                            <p class="text-sm text-gray-600">{{ $instructor['courses_count'] }} courses</p>
                        </div>
                        <div class="text-right">
                            <div class="flex items-center text-sm font-semibold text-green-600">
                                {{ $instructor['total_enrollments'] }}
                                <i class="fas fa-users ml-1"></i>
                            </div>
                            <div class="flex items-center text-xs text-yellow-600">
                                {{ $instructor['average_rating'] }}
                                <i class="fas fa-star ml-1"></i>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-6 text-gray-500">
                        <i class="fas fa-chalkboard-teacher text-3xl mb-3 text-gray-300"></i>
                        <p>No instructor data available</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif

            <!-- Content Approval Queue -->
            @if($showWidgets['content_approval'])
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-clipboard-check text-orange-600 mr-2"></i>
                        Pending Approvals
                    </h2>
                    <div class="flex items-center space-x-1">
                        <span class="bg-orange-100 text-orange-800 text-xs font-medium px-2 py-1 rounded-full">
                            {{ count($this->contentApprovalQueue['courses']) + count($this->contentApprovalQueue['certificates']) + count($this->contentApprovalQueue['blog_posts']) }}
                        </span>
                    </div>
                </div>
                
                <!-- Courses Pending Approval -->
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                        <i class="fas fa-book text-blue-600 mr-2"></i>
                        Courses ({{ count($this->contentApprovalQueue['courses']) }})
                    </h3>
                    @forelse($this->contentApprovalQueue['courses'] as $course)
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg mb-2 hover:bg-blue-100 transition-colors duration-200">
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-medium text-gray-900 truncate">{{ $course['title'] }}</h4>
                            <p class="text-xs text-gray-600">{{ $course['instructor'] }} • {{ $course['created_at'] }}</p>
                        </div>
                        <div class="flex space-x-2">
                            <button wire:click="approveCourse({{ $course['id'] }})" 
                                    class="text-green-600 hover:text-green-700 p-1 rounded transition-colors"
                                    title="Approve Course">
                                <i class="fas fa-check text-sm"></i>
                            </button>
                            <button class="text-red-600 hover:text-red-700 p-1 rounded transition-colors"
                                    title="Reject Course">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-500 text-sm">No pending courses</p>
                    @endforelse
                </div>

                <!-- Certificates Pending Approval -->
                <div class="mb-6">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                        <i class="fas fa-award text-purple-600 mr-2"></i>
                        Certificates ({{ count($this->contentApprovalQueue['certificates']) }})
                    </h3>
                    @forelse($this->contentApprovalQueue['certificates'] as $certificate)
                    <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg mb-2 hover:bg-purple-100 transition-colors duration-200">
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-medium text-gray-900">{{ $certificate['user'] }}</h4>
                            <p class="text-xs text-gray-600">{{ Str::limit($certificate['course'], 30) }} • {{ $certificate['created_at'] }}</p>
                        </div>
                        <div class="flex space-x-2">
                            <button wire:click="approveCertificate({{ $certificate['id'] }})" 
                                    class="text-green-600 hover:text-green-700 p-1 rounded transition-colors"
                                    title="Approve Certificate">
                                <i class="fas fa-check text-sm"></i>
                            </button>
                            <button class="text-red-600 hover:text-red-700 p-1 rounded transition-colors"
                                    title="Reject Certificate">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-500 text-sm">No pending certificates</p>
                    @endforelse
                </div>

                <!-- Blog Posts Pending Approval -->
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                        <i class="fas fa-pen text-green-600 mr-2"></i>
                        Blog Posts ({{ count($this->contentApprovalQueue['blog_posts']) }})
                    </h3>
                    @forelse($this->contentApprovalQueue['blog_posts'] as $post)
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg mb-2 hover:bg-green-100 transition-colors duration-200">
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-medium text-gray-900 truncate">{{ $post['title'] }}</h4>
                            <p class="text-xs text-gray-600">{{ $post['author'] }} • {{ $post['created_at'] }}</p>
                        </div>
                        <div class="flex space-x-2">
                            <button class="text-green-600 hover:text-green-700 p-1 rounded transition-colors"
                                    title="Approve Post">
                                <i class="fas fa-check text-sm"></i>
                            </button>
                            <button class="text-red-600 hover:text-red-700 p-1 rounded transition-colors"
                                    title="Reject Post">
                                <i class="fas fa-times text-sm"></i>
                            </button>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-500 text-sm">No pending blog posts</p>
                    @endforelse
                </div>
            </div>
            @endif

            <!-- Recent Activities -->
            @if($showWidgets['recent_activities'])
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-history text-indigo-600 mr-2"></i>
                        Recent Activities
                    </h2>
                </div>
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @forelse($this->recentActivities as $activity)
                    <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex-shrink-0 w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                            <i class="{{ $activity['icon'] }} text-xs {{ $activity['color'] }}"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">{{ $activity['description'] }}</p>
                            <p class="text-xs text-gray-500">{{ $activity['causer'] }} • {{ $activity['created_at'] }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-6 text-gray-500">
                        <i class="fas fa-history text-3xl mb-3 text-gray-300"></i>
                        <p>No recent activities</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif

            <!-- Support Overview -->
            @if($showWidgets['support_overview'])
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-headset text-blue-600 mr-2"></i>
                        Support Overview
                    </h2>
                    <a href="{{ route('support.tickets') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
                        Manage <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                <!-- Support Stats -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="text-center p-4 bg-red-50 rounded-xl">
                        <div class="text-2xl font-bold text-red-600">{{ $this->supportOverview['open_tickets'] }}</div>
                        <div class="text-xs text-red-600 font-medium">Open Tickets</div>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded-xl">
                        <div class="text-2xl font-bold text-green-600">{{ $this->supportOverview['resolution_rate'] }}%</div>
                        <div class="text-xs text-green-600 font-medium">Resolution Rate</div>
                    </div>
                </div>

                <!-- Recent Tickets -->
                <div class="space-y-3">
                    <h3 class="text-sm font-semibold text-gray-700">Recent Tickets</h3>
                    @forelse($this->supportOverview['recent_tickets'] as $ticket)
                    <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center {{ $ticket['status'] === 'open' ? 'bg-red-100' : 'bg-green-100' }}">
                            <i class="fas {{ $ticket['status'] === 'open' ? 'fa-exclamation text-red-600' : 'fa-check text-green-600' }} text-xs"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $ticket['subject'] }}</p>
                            <p class="text-xs text-gray-600">{{ $ticket['user'] }} • {{ $ticket['created_at'] }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-500 text-sm">No recent tickets</p>
                    @endforelse
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Quick Actions Modal -->
    @if($showQuickActionModal)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-3xl p-8 w-full max-w-2xl transform transition-all duration-300 scale-100 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-2xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-lightning-bolt text-indigo-600 mr-3"></i>
                    Quick Actions
                </h3>
                <button wire:click="toggleQuickActionModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Course Management -->
                <button wire:click="quickAction('manage_courses')" 
                        class="flex items-center p-4 text-left bg-gradient-to-r from-blue-50 to-blue-100 hover:from-blue-100 hover:to-blue-200 rounded-xl transition-all duration-200 group">
                    <div class="bg-blue-600 p-3 rounded-lg mr-4 group-hover:bg-blue-700 transition-colors">
                        <i class="fas fa-book-open text-white"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900">Manage Courses</div>
                        <div class="text-sm text-gray-600">View and organize all courses</div>
                    </div>
                </button>

                <!-- Student Management -->
                <button wire:click="quickAction('manage_students')"
                        class="flex items-center p-4 text-left bg-gradient-to-r from-green-50 to-green-100 hover:from-green-100 hover:to-green-200 rounded-xl transition-all duration-200 group">
                    <div class="bg-green-600 p-3 rounded-lg mr-4 group-hover:bg-green-700 transition-colors">
                        <i class="fas fa-user-graduate text-white"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900">Manage Students</div>
                        <div class="text-sm text-gray-600">Student administration panel</div>
                    </div>
                </button>

                <!-- Instructor Management -->
                <button wire:click="quickAction('manage_instructors')"
                        class="flex items-center p-4 text-left bg-gradient-to-r from-purple-50 to-purple-100 hover:from-purple-100 hover:to-purple-200 rounded-xl transition-all duration-200 group">
                    <div class="bg-purple-600 p-3 rounded-lg mr-4 group-hover:bg-purple-700 transition-colors">
                        <i class="fas fa-chalkboard-teacher text-white"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900">Manage Instructors</div>
                        <div class="text-sm text-gray-600">Instructor oversight and support</div>
                    </div>
                </button>

                <!-- Content Approval -->
                <button wire:click="quickAction('approve_content')"
                        class="flex items-center p-4 text-left bg-gradient-to-r from-orange-50 to-orange-100 hover:from-orange-100 hover:to-orange-200 rounded-xl transition-all duration-200 group">
                    <div class="bg-orange-600 p-3 rounded-lg mr-4 group-hover:bg-orange-700 transition-colors">
                        <i class="fas fa-clipboard-check text-white"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900">Content Approval</div>
                        <div class="text-sm text-gray-600">Review pending content</div>
                    </div>
                </button>

                <!-- Certificates -->
                <button wire:click="quickAction('view_certificates')"
                        class="flex items-center p-4 text-left bg-gradient-to-r from-yellow-50 to-yellow-100 hover:from-yellow-100 hover:to-yellow-200 rounded-xl transition-all duration-200 group">
                    <div class="bg-yellow-600 p-3 rounded-lg mr-4 group-hover:bg-yellow-700 transition-colors">
                        <i class="fas fa-award text-white"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900">Certificates</div>
                        <div class="text-sm text-gray-600">Manage certifications</div>
                    </div>
                </button>

                <!-- Support Tickets -->
                <button wire:click="quickAction('view_tickets')"
                        class="flex items-center p-4 text-left bg-gradient-to-r from-red-50 to-red-100 hover:from-red-100 hover:to-red-200 rounded-xl transition-all duration-200 group">
                    <div class="bg-red-600 p-3 rounded-lg mr-4 group-hover:bg-red-700 transition-colors">
                        <i class="fas fa-headset text-white"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900">Support Tickets</div>
                        <div class="text-sm text-gray-600">Handle user support requests</div>
                    </div>
                </button>

                <!-- Categories -->
                <button wire:click="quickAction('manage_categories')"
                        class="flex items-center p-4 text-left bg-gradient-to-r from-indigo-50 to-indigo-100 hover:from-indigo-100 hover:to-indigo-200 rounded-xl transition-all duration-200 group">
                    <div class="bg-indigo-600 p-3 rounded-lg mr-4 group-hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-tags text-white"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900">Categories</div>
                        <div class="text-sm text-gray-600">Organize course categories</div>
                    </div>
                </button>

                <!-- Analytics -->
                <button wire:click="quickAction('learning_analytics')"
                        class="flex items-center p-4 text-left bg-gradient-to-r from-teal-50 to-teal-100 hover:from-teal-100 hover:to-teal-200 rounded-xl transition-all duration-200 group">
                    <div class="bg-teal-600 p-3 rounded-lg mr-4 group-hover:bg-teal-700 transition-colors">
                        <i class="fas fa-chart-bar text-white"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900">Learning Analytics</div>
                        <div class="text-sm text-gray-600">Detailed performance insights</div>
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
                    class="bg-gradient-to-r from-indigo-600 to-purple-600 shadow-lg rounded-full p-4 hover:shadow-xl transition-all duration-200 text-white">
                <i class="fas fa-sliders-h"></i>
            </button>
            
            <div x-show="open" 
                 x-transition
                 @click.outside="open = false"
                 class="absolute bottom-full right-0 mb-3 bg-white rounded-xl shadow-xl border border-gray-200 p-4 w-64">
                <h3 class="font-semibold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-cog text-indigo-600 mr-2"></i>
                    Dashboard Widgets
                </h3>
                <div class="space-y-3">
                    @foreach($showWidgets as $widget => $isVisible)
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" 
                               wire:model="showWidgets.{{ $widget }}" 
                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-3 text-sm text-gray-700 group-hover:text-gray-900">
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
                    borderColor: 'rgb(99, 102, 241)',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    fill: true,
                    tension: 0.4,
                }, {
                    label: 'Total Students',
                    data: studentGrowthData.map(item => item.total_students),
                    borderColor: 'rgb(16, 185, 129)',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    fill: false,
                    tension: 0.4,
                }, {
                    label: 'Daily Enrollments',
                    data: studentGrowthData.map(item => item.enrollments),
                    borderColor: 'rgb(147, 51, 234)',
                    backgroundColor: 'rgba(147, 51, 234, 0.1)',
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
                    backgroundColor: 'rgba(16, 185, 129, 0.8)',
                    borderColor: 'rgb(16, 185, 129)',
                    borderWidth: 1,
                    borderRadius: 4,
                }, {
                    label: 'New Enrollments',
                    data: learningProgressData.map(item => item.enrollments),
                    backgroundColor: 'rgba(99, 102, 241, 0.8)',
                    borderColor: 'rgb(99, 102, 241)',
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

// Auto-refresh dashboard data
setInterval(() => {
    @this.call('loadAllData');
}, {{ $refreshInterval }});
</script>
@endpush