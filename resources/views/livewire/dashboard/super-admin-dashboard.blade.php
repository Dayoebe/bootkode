<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ notificationsOpen: false }">
    <!-- Welcome Header -->
    <div class="bg-gradient-to-r from-blue-800 to-purple-700 p-8 rounded-2xl shadow-xl border border-gray-700 flex flex-col md:flex-row items-center justify-between mb-8 animate__animated animate__fadeIn">
        <div class="text-white">
            <h1 class="text-3xl font-extrabold mb-2">
                <i class="fas fa-user-shield mr-2"></i> Welcome Back, {{ auth()->user()->name }}!
            </h1>
            <p class="text-gray-200 text-lg">Your command center for BootKode Academy operations.</p>
        </div>
        <div class="mt-4 md:mt-0 flex items-center space-x-4">
            <button @click="notificationsOpen = !notificationsOpen" class="text-white hover:text-gray-200">
                <i class="fas fa-bell text-2xl animate__animated animate__pulse animate__infinite"></i>
            </button>
            <button wire:click="toggleQuickActionModal" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200">
                <i class="fas fa-bolt mr-2"></i> Quick Actions
            </button>
        </div>
    </div>

    <!-- Stats Grid -->
    <div x-show="$wire.showWidgets.stats" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6 dashboard-card animate__animated animate__fadeInUp">
            <div class="flex items-center">
                <i class="fas fa-users text-3xl text-blue-600 mr-4"></i>
                <div>
                    <p class="text-sm text-gray-600">Total Users</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $stats['total_users'] }}</h3>
                    <p class="text-xs text-gray-400">
                        Super Admins: {{ $stats['role_counts']['super_admin'] }} | Instructors: {{ $stats['role_counts']['instructor'] }}
                    </p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 dashboard-card animate__animated animate__fadeInUp animate__delay-1s">
            <div class="flex items-center">
                <i class="fas fa-book text-3xl text-green-600 mr-4"></i>
                <div>
                    <p class="text-sm text-gray-600">Total Courses</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $stats['total_courses'] }}</h3>
                    <p class="text-xs text-gray-400">Categories: {{ $stats['course_categories'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 dashboard-card animate__animated animate__fadeInUp animate__delay-2s">
            <div class="flex items-center">
                <i class="fas fa-dollar-sign text-3xl text-yellow-600 mr-4"></i>
                <div>
                    <p class="text-sm text-gray-600">Revenue</p>
                    <h3 class="text-2xl font-bold text-gray-800">${{ number_format($stats['revenue'], 2) }}</h3>
                    <p class="text-xs text-gray-400">From premium courses</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 dashboard-card animate__animated animate__fadeInUp animate__delay-3s">
            <div class="flex items-center">
                <i class="fas fa-certificate text-3xl text-purple-600 mr-4"></i>
                <div>
                    <p class="text-sm text-gray-600">Certificates</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $stats['total_certificates'] }}</h3>
                    <p class="text-xs text-gray-400">Pending: {{ $stats['pending_certificate_approvals'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column: Charts and Activities -->
        <div class="lg:col-span-2 space-y-8">
            <!-- User Growth Chart -->
            <div x-show="$wire.showWidgets.engagement" class="bg-white rounded-xl shadow-lg p-6 dashboard-card animate__animated animate__fadeInUp">
                <h2 class="text-xl font-bold text-gray-800 mb-4">User Growth</h2>
                <canvas id="userGrowthChart" class="w-full h-64"></canvas>
            </div>

            <!-- Course Engagement -->
            <div x-show="$wire.showWidgets.engagement" class="bg-white rounded-xl shadow-lg p-6 dashboard-card animate__animated animate__fadeInUp animate__delay-1s">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Top Courses by Engagement</h2>
                <canvas id="courseEngagementChart" class="w-full h-64"></canvas>
            </div>

            <!-- Recent Activities -->
            <div x-show="$wire.showWidgets.activities" class="bg-white rounded-xl shadow-lg p-6 dashboard-card animate__animated animate__fadeInUp animate__delay-2s">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Recent Activities</h2>
                <div x-data="{ openActivity: null }" class="space-y-4">
                    @forelse($recentActivities as $activity)
                        <div class="border-b border-gray-200 pb-4">
                            <div class="flex justify-between items-center">
                                <div>
                                    <p class="text-sm text-gray-600">{{ $activity['description'] }}</p>
                                    <p class="text-xs text-gray-400">By {{ $activity['causer'] }} • {{ $activity['created_at'] }}</p>
                                </div>
                                <button @click="openActivity = openActivity === '{{ $loop->index }}' ? null : '{{ $loop->index }}'"
                                        class="text-blue-600 hover:text-blue-800">
                                    <i class="fas" :class="{ 'fa-chevron-up': openActivity === '{{ $loop->index }}', 'fa-chevron-down': openActivity !== '{{ $loop->index }}' }"></i>
                                </button>
                            </div>
                            <div x-show="openActivity === '{{ $loop->index }}'" x-transition class="mt-2 text-sm text-gray-600">
                                {{ $activity['description'] }}
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500">No recent activities.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Column: Calendar, Status, Users -->
        <div class="space-y-8">
            <!-- Calendar -->
            <div x-show="$wire.showWidgets.calendar" class="bg-white rounded-xl shadow-lg p-6 dashboard-card animate__ქ

                animate__animated animate__fadeInUp">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Scheduled Events</h2>
                <div id="calendar" class="w-full"></div>
            </div>

            <!-- System Status -->
            <div x-show="$wire.showWidgets.system_status" class="bg-white rounded-xl shadow-lg p-6 dashboard-card animate__animated animate__fadeInUp animate__delay-1s">
                <h2 class="text-xl font-bold text-gray-800 mb-4">System Status</h2>
                <div class="flex items-center mb-4">
                    <i class="fas fa-server text-2xl mr-3"
                       :class="{ 'text-green-600': '{{ $systemStatus['status'] }}' === 'operational', 'text-red-600': '{{ $systemStatus['status'] }}' !== 'operational' }"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Status: {{ ucfirst($systemStatus['status']) }}</p>
                        <p class="text-xs text-gray-400">{{ $systemStatus['message'] }}</p>
                        <p class="text-xs text-gray-400">Updated: {{ $systemStatus['updated_at'] }}</p>
                    </div>
                </div>
                <a href="{{ route('system-status') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200">
                    <i class="fas fa-eye mr-2"></i> View Details
                </a>
            </div>

            <!-- Recent Users -->
            <div x-show="$wire.showWidgets.recent_users" class="bg-white rounded-xl shadow-lg p-6 dashboard-card animate__animated animate__fadeInUp animate__delay-2s">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Recent Users</h2>
                <div class="space-y-4">
                    @forelse($recentUsers as $user)
                        <div class="flex justify-between items-center border-b border-gray-200 pb-4">
                            <div>
                                <p class="text-sm font-medium text-gray-600">{{ $user['name'] }}</p>
                                <p class="text-xs text-gray-400">{{ $user['email'] }} • {{ $user['role'] }}</p>
                                <p class="text-xs text-gray-400">Joined: {{ $user['created_at'] }}</p>
                            </div>
                            <div class="flex space-x-2">
                                <button wire:click="toggleUserStatus({{ $user['id'] }})"
                                        class="px-3 py-1 rounded-md text-sm"
                                        :class="{ 'bg-green-600 text-white hover:bg-green-700': !{{ $user['is_active'] }}, 'bg-red-600 text-white hover:bg-red-700': {{ $user['is_active'] }} }">
                                    <i class="fas" :class="{ 'fa-user-check': !{{ $user['is_active'] }}, 'fa-user-slash': {{ $user['is_active'] }} }"></i>
                                    {{ $user['is_active'] ? 'Ban' : 'Activate' }}
                                </button>
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500">No recent users.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Approvals -->
    <div x-show="$wire.showWidgets.approvals" class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">
        <div class="bg-white rounded-xl shadow-lg p-6 dashboard-card animate__animated animate__fadeInUp">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Pending Course Approvals</h2>
            <div class="space-y-4">
                @forelse($pendingCourses as $course)
                    <div class="border-b border-gray-200 pb-4">
                        <p class="text-sm font-medium text-gray-600">{{ $course['title'] }}</p>
                        <p class="text-xs text-gray-400">By {{ $course['instructor'] }}</p>
                        <a href="{{ route('course-approvals') }}"
                           class="mt-2 inline-flex items-center px-3 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200 text-sm">
                            <i class="fas fa-check-circle mr-2"></i> Review
                        </a>
                    </div>
                @empty
                    <p class="text-gray-500">No pending course approvals.</p>
                @endforelse
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-lg p-6 dashboard-card animate__animated animate__fadeInUp animate__delay-1s">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Pending Certificate Approvals</h2>
            <div class="space-y-4">
                @forelse($pendingCertificates as $certificate)
                    <div class="border-b border-gray-200 pb-4">
                        <p class="text-sm font-medium text-gray-600">{{ $certificate['user'] }}</p>
                        <p class="text-xs text-gray-400">Course: {{ $certificate['course'] }}</p>
                        <a href="{{ route('certificates.approvals') }}"
                           class="mt-2 inline-flex items-center px-3 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200 text-sm">
                            <i class="fas fa-check-circle mr-2"></i> Review
                        </a>
                    </div>
                @empty
                    <p class="text-gray-500">No pending certificate approvals.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Real-Time Notifications -->
    <div x-show="notificationsOpen" class="fixed top-16 right-4 w-80 bg-white rounded-xl shadow-xl p-6 z-50 animate__animated animate__fadeInRight" wire:poll.5000ms="loadNotifications">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Notifications</h3>
        <div class="space-y-4 max-h-96 overflow-y-auto">
            @forelse($notifications as $notification)
                <div class="border-b border-gray-200 pb-4">
                    <p class="text-sm text-gray-600">{{ $notification['message'] }}</p>
                    <p class="text-xs text-gray-400">{{ $notification['created_at'] }}</p>
                </div>
            @empty
                <p class="text-gray-500">No new notifications.</p>
            @endforelse
        </div>
        <button @click="notificationsOpen = false" class="mt-4 w-full px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors duration-200">
            <i class="fas fa-times mr-2"></i> Close
        </button>
    </div>

    <!-- Quick Actions Modal -->
    <div x-data="{ open: @entangle('showQuickActionModal') }" x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
        <div class="bg-white rounded-xl p-6 w-full max-w-md">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Quick Actions</h3>
            <div class="space-y-4">
                <button wire:click="quickAction('create_course')"
                        class="w-full flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200">
                    <i class="fas fa-plus-circle mr-2"></i> Create New Course
                </button>
                <button wire:click="quickAction('manage_users')"
                        class="w-full flex items-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors duration-200">
                    <i class="fas fa-users-cog mr-2"></i> Manage Users
                </button>
                <button wire:click="quickAction('view_tickets')"
                        class="w-full flex items-center px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 transition-colors duration-200">
                    <i class="fas fa-ticket-alt mr-2"></i> View Support Tickets
                </button>
                <button wire:click="quickAction('manage_faqs')"
                        class="w-full flex items-center px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors duration-200">
                    <i class="fas fa-book-open mr-2"></i> Manage FAQs
                </button>
                <button wire:click="quickAction('view_courses')"
                        class="w-full flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition-colors duration-200">
                    <i class="fas fa-book mr-2"></i> View All Courses
                </button>
                <button wire:click="quickAction('manage_categories')"
                        class="w-full flex items-center px-4 py-2 bg-teal-600 text-white rounded-md hover:bg-teal-700 transition-colors duration-200">
                    <i class="fas fa-tags mr-2"></i> Manage Course Categories
                </button>
                <button @click="open = false"
                        class="w-full flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors duration-200">
                    <i class="fas fa-times mr-2"></i> Close
                </button>
            </div>
        </div>
    </div>

    <!-- Widget Toggle -->
    <div class="fixed bottom-4 right-4">
        <button @click="$refs.widgetMenu.classList.toggle('hidden')"
                class="bg-blue-600 text-white px-4 py-2 rounded-full hover:bg-blue-700 transition-colors duration-200">
            <i class="fas fa-cog"></i>
        </button>
        <div x-ref="widgetMenu" class="hidden mt-2 bg-white rounded-xl shadow-xl p-4 w-64">
            <h3 class="text-sm font-bold text-gray-800 mb-2">Toggle Widgets</h3>
            <div class="space-y-2">
                <label class="flex items-center">
                    <input type="checkbox" x-model="$wire.showWidgets.stats" wire:change="toggleWidget('stats')" class="mr-2">
                    Stats
                </label>
                <label class="flex items-center">
                    <input type="checkbox" x-model="$wire.showWidgets.activities" wire:change="toggleWidget('activities')" class="mr-2">
                    Recent Activities
                </label>
                <label class="flex items-center">
                    <input type="checkbox" x-model="$wire.showWidgets.approvals" wire:change="toggleWidget('approvals')" class="mr-2">
                    Pending Approvals
                </label>
                <label class="flex items-center">
                    <input type="checkbox" x-model="$wire.showWidgets.system_status" wire:change="toggleWidget('system_status')" class="mr-2">
                    System Status
                </label>
                <label class="flex items-center">
                    <input type="checkbox" x-model="$wire.showWidgets.recent_users" wire:change="toggleWidget('recent_users')" class="mr-2">
                    Recent Users
                </label>
                <label class="flex items-center">
                    <input type="checkbox" x-model="$wire.showWidgets.calendar" wire:change="toggleWidget('calendar')" class="mr-2">
                    Calendar
                </label>
                <label class="flex items-center">
                    <input type="checkbox" x-model="$wire.showWidgets.engagement" wire:change="toggleWidget('engagement')" class="mr-2">
                    Engagement Charts
                </label>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .dashboard-card:hover {
                transform: translateY(-4px);
                box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
                transition: all 0.3s ease;
            }
            #calendar {
                max-width: 100%;
                height: 300px;
            }
            .fc-event {
                background-color: #3b82f6;
                border-color: #3b82f6;
                color: white;
            }
        </style>
    @endpush

    @push('scripts')
        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.js"></script>
        <!-- FullCalendar -->
        <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // User Growth Chart
                const userGrowthData = @json($this->userGrowthData);
                if (userGrowthData && userGrowthData.length) {
                    const userGrowthChart = new Chart(document.getElementById('userGrowthChart'), {
                        type: 'line',
                        data: {
                            labels: userGrowthData.map(item => item.month),
                            datasets: [{
                                label: 'User Growth',
                                data: userGrowthData.map(item => item.users),
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59, 130, 246, 0.2)',
                                fill: true,
                                tension: 0.4,
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: { y: { beginAtZero: true } },
                        }
                    });
                } else {
                    document.getElementById('userGrowthChart').parentElement.innerHTML = '<p class="text-gray-500">No user growth data available.</p>';
                }

                // Course Engagement Chart
                const courseEngagementData = @json($this->courseEngagementData);
                if (courseEngagementData && courseEngagementData.length) {
                    const courseEngagementChart = new Chart(document.getElementById('courseEngagementChart'), {
                        type: 'bar',
                        data: {
                            labels: courseEngagementData.map(item => item.title),
                            datasets: [
                                {
                                    label: 'Enrollments',
                                    data: courseEngagementData.map(item => item.enrollments),
                                    backgroundColor: '#10b981',
                                },
                                {
                                    label: 'Completion Rate (%)',
                                    data: courseEngagementData.map(item => item.completion_rate),
                                    backgroundColor: '#f59e0b',
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            scales: { y: { beginAtZero: true } }
                        }
                    });
                } else {
                    document.getElementById('courseEngagementChart').parentElement.innerHTML = '<p class="text-gray-500">No course engagement data available.</p>';
                }

                // Calendar
                const calendarEl = document.getElementById('calendar');
                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    height: 'auto',
                    events: [
                        { title: 'Course Launch: Intro to Coding', start: '{{ now()->addDays(2)->format('Y-m-d') }}' },
                        { title: 'System Maintenance', start: '{{ now()->addDays(5)->format('Y-m-d') }}' },
                        { title: 'CBT Exam Period', start: '{{ now()->addDays(10)->format('Y-m-d') }}', end: '{{ now()->addDays(12)->format('Y-m-d') }}' },
                    ],
                    eventClick: function(info) {
                        alert('Event: ' + info.event.title);
                    }
                });
                calendar.render();
            });
        </script>
    @endpush
</div>