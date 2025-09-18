<div class="min-h-screen bg-gradient-to-br from-slate-50 to-gray-100 p-4 md:p-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center space-y-4 lg:space-y-0">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-user-shield text-blue-600 mr-3"></i>
                        Super Admin Dashboard
                    </h1>
                    <p class="text-gray-600 mt-1">Welcome back, {{ auth()->user()->name }}! Here's what's happening with your platform.</p>
                </div>
                
                <div class="flex flex-wrap items-center space-x-3">
                    <!-- Timeframe Selector -->
                    <div class="flex bg-gray-100 rounded-lg p-1">
                        @foreach(['24hours' => '24h', '7days' => '7d', '30days' => '30d', '90days' => '90d'] as $value => $label)
                            <button 
                                wire:click="updateTimeframe('{{ $value }}')"
                                class="px-3 py-1 rounded-md text-sm font-medium transition-all duration-200 {{ $selectedTimeframe === $value ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                    
                    <!-- Quick Actions -->
                    <button wire:click="toggleQuickActionModal" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                        <i class="fas fa-bolt mr-2"></i>
                        Quick Actions
                    </button>
                    
                    <!-- Settings -->
                    <button class="p-2 text-gray-400 hover:text-gray-600 transition-colors duration-200">
                        <i class="fas fa-cog text-lg"></i>
                    </button>
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
                    <p class="text-sm text-gray-600 font-medium">Total Users</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($this->overviewStats['total_users']) }}</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="text-green-600 font-medium">+{{ $this->overviewStats['new_users_today'] }}</span>
                        <span class="text-gray-500 ml-1">today</span>
                    </div>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Active Courses</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($this->overviewStats['published_courses']) }}</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="text-orange-600 font-medium">{{ $this->overviewStats['pending_courses'] }}</span>
                        <span class="text-gray-500 ml-1">pending</span>
                    </div>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-book text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Revenue</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-1">₦{{ number_format($this->overviewStats['total_revenue'], 2) }}</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="text-green-600 font-medium">₦{{ number_format($this->overviewStats['monthly_revenue'], 2) }}</span>
                        <span class="text-gray-500 ml-1">this month</span>
                    </div>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <i class="fas fa-chart-line text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Support Tickets</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ $this->overviewStats['open_tickets'] }}</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="text-red-600 font-medium">{{ $this->overviewStats['pending_certificates'] }}</span>
                        <span class="text-gray-500 ml-1">cert. pending</span>
                    </div>
                </div>
                <div class="bg-red-100 p-3 rounded-full">
                    <i class="fas fa-ticket-alt text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <!-- Left Column (Charts) -->
        <div class="xl:col-span-2 space-y-8">
            <!-- User Growth Chart -->
            @if($showWidgets['user_analytics'])
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900">User Growth</h2>
                    <div class="flex items-center space-x-2 text-sm text-gray-500">
                        <i class="fas fa-chart-area"></i>
                        <span>Last {{ $selectedTimeframe === '24hours' ? '24 hours' : ($selectedTimeframe === '7days' ? '7 days' : ($selectedTimeframe === '30days' ? '30 days' : '90 days')) }}</span>
                    </div>
                </div>
                <div class="h-80">
                    <canvas id="userGrowthChart"></canvas>
                </div>
            </div>
            @endif

            <!-- Revenue Analytics -->
            @if($showWidgets['revenue_analytics'])
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Revenue Analytics</h2>
                    <div class="flex items-center space-x-2 text-sm text-gray-500">
                        <i class="fas fa-money-bill-wave"></i>
                        <span>Daily Revenue Trend</span>
                    </div>
                </div>
                <div class="h-80">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
            @endif

            <!-- Course Performance -->
            @if($showWidgets['course_performance'])
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Top Performing Courses</h2>
                    <a href="{{ route('all-course') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="space-y-4">
                    @forelse($this->coursePerformance as $course)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900">{{ Str::limit($course['title'], 40) }}</h4>
                            <p class="text-sm text-gray-600">by {{ $course['instructor'] }}</p>
                        </div>
                        <div class="flex items-center space-x-6 text-sm">
                            <div class="text-center">
                                <div class="font-semibold text-gray-900">{{ $course['enrollments'] }}</div>
                                <div class="text-gray-500">enrollments</div>
                            </div>
                            <div class="text-center">
                                <div class="font-semibold text-green-600">{{ $course['completion_rate'] }}%</div>
                                <div class="text-gray-500">completion</div>
                            </div>
                            <div class="text-center">
                                <div class="font-semibold text-yellow-600">{{ number_format($course['rating'], 1) }}</div>
                                <div class="text-gray-500">rating</div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-500 text-center py-4">No course data available</p>
                    @endforelse
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column (System Info & Activities) -->
        <div class="space-y-8">
            <!-- System Health -->
            @if($showWidgets['system_health'])
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900">System Health</h2>
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                        <span class="text-sm text-green-600 font-medium">Operational</span>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-database text-green-600 mr-3"></i>
                            <span class="font-medium">Database</span>
                        </div>
                        <span class="text-green-600 font-medium">{{ $this->systemHealth['database_health']['status'] === 'healthy' ? 'Healthy' : 'Error' }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-memory text-blue-600 mr-3"></i>
                            <span class="font-medium">Cache</span>
                        </div>
                        <span class="text-blue-600 font-medium">{{ $this->systemHealth['cache_status'] === 'operational' ? 'Active' : 'Error' }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-hdd text-gray-600 mr-3"></i>
                            <span class="font-medium">Storage</span>
                        </div>
                        <span class="text-gray-600 font-medium">{{ $this->systemHealth['storage_usage']['percentage'] }}%</span>
                    </div>
                </div>
            </div>
            @endif

            <!-- Recent Activities -->
            @if($showWidgets['recent_activities'])
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Recent Activities</h2>
                    <button class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </button>
                </div>
                <div class="space-y-3">
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
                    <p class="text-gray-500 text-center py-4">No recent activities</p>
                    @endforelse
                </div>
            </div>
            @endif

            <!-- Pending Approvals -->
            @if($showWidgets['pending_approvals'])
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Pending Approvals</h2>
                    <div class="flex items-center space-x-1">
                        <span class="bg-orange-100 text-orange-800 text-xs font-medium px-2 py-1 rounded-full">
                            {{ count($this->pendingApprovals['courses']) + count($this->pendingApprovals['certificates']) }}
                        </span>
                    </div>
                </div>
                
                <!-- Courses Tab -->
                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <i class="fas fa-book text-blue-600 mr-2"></i>
                            Courses ({{ count($this->pendingApprovals['courses']) }})
                        </h3>
                        @forelse($this->pendingApprovals['courses'] as $course)
                        <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg mb-2">
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-gray-900 truncate">{{ $course['title'] }}</h4>
                                <p class="text-xs text-gray-600">{{ $course['instructor'] }} • {{ $course['created_at'] }}</p>
                            </div>
                            <div class="flex space-x-2">
                                <button class="text-green-600 hover:text-green-700 p-1">
                                    <i class="fas fa-check text-xs"></i>
                                </button>
                                <button class="text-red-600 hover:text-red-700 p-1">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                            </div>
                        </div>
                        @empty
                        <p class="text-gray-500 text-sm">No pending courses</p>
                        @endforelse
                    </div>

                    <!-- Certificates Tab -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <i class="fas fa-certificate text-purple-600 mr-2"></i>
                            Certificates ({{ count($this->pendingApprovals['certificates']) }})
                        </h3>
                        @forelse($this->pendingApprovals['certificates'] as $certificate)
                        <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg mb-2">
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-gray-900">{{ $certificate['user'] }}</h4>
                                <p class="text-xs text-gray-600">{{ Str::limit($certificate['course'], 30) }} • {{ $certificate['created_at'] }}</p>
                            </div>
                            <div class="flex space-x-2">
                                <button class="text-green-600 hover:text-green-700 p-1">
                                    <i class="fas fa-check text-xs"></i>
                                </button>
                                <button class="text-red-600 hover:text-red-700 p-1">
                                    <i class="fas fa-times text-xs"></i>
                                </button>
                            </div>
                        </div>
                        @empty
                        <p class="text-gray-500 text-sm">No pending certificates</p>
                        @endforelse
                    </div>
                </div>
            </div>
            @endif

            <!-- Support Overview -->
            @if($showWidgets['support_overview'])
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Support Overview</h2>
                    <a href="{{ route('support.tickets') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                        Manage <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                <!-- Support Stats -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="text-center p-3 bg-red-50 rounded-lg">
                        <div class="text-2xl font-bold text-red-600">{{ $this->supportOverview['open_tickets'] }}</div>
                        <div class="text-xs text-red-600 font-medium">Open</div>
                    </div>
                    <div class="text-center p-3 bg-green-50 rounded-lg">
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
        <div class="bg-white rounded-2xl p-8 w-full max-w-md transform transition-all duration-300 scale-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-900">Quick Actions</h3>
                <button wire:click="toggleQuickActionModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="grid grid-cols-1 gap-3">
                <button wire:click="quickAction('create_course')" 
                        class="flex items-center p-4 text-left bg-gradient-to-r from-blue-50 to-blue-100 hover:from-blue-100 hover:to-blue-200 rounded-xl transition-all duration-200 group">
                    <div class="bg-blue-600 p-2 rounded-lg mr-4 group-hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus text-white"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900">Create Course</div>
                        <div class="text-sm text-gray-600">Add new learning content</div>
                    </div>
                </button>

                <button wire:click="quickAction('manage_users')"
                        class="flex items-center p-4 text-left bg-gradient-to-r from-green-50 to-green-100 hover:from-green-100 hover:to-green-200 rounded-xl transition-all duration-200 group">
                    <div class="bg-green-600 p-2 rounded-lg mr-4 group-hover:bg-green-700 transition-colors">
                        <i class="fas fa-users text-white"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900">Manage Users</div>
                        <div class="text-sm text-gray-600">User administration panel</div>
                    </div>
                </button>

                <button wire:click="quickAction('view_tickets')"
                        class="flex items-center p-4 text-left bg-gradient-to-r from-yellow-50 to-yellow-100 hover:from-yellow-100 hover:to-yellow-200 rounded-xl transition-all duration-200 group">
                    <div class="bg-yellow-600 p-2 rounded-lg mr-4 group-hover:bg-yellow-700 transition-colors">
                        <i class="fas fa-ticket-alt text-white"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900">Support Tickets</div>
                        <div class="text-sm text-gray-600">Handle user requests</div>
                    </div>
                </button>

                <button wire:click="quickAction('view_analytics')"
                        class="flex items-center p-4 text-left bg-gradient-to-r from-purple-50 to-purple-100 hover:from-purple-100 hover:to-purple-200 rounded-xl transition-all duration-200 group">
                    <div class="bg-purple-600 p-2 rounded-lg mr-4 group-hover:bg-purple-700 transition-colors">
                        <i class="fas fa-chart-bar text-white"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900">View Analytics</div>
                        <div class="text-sm text-gray-600">Platform insights</div>
                    </div>
                </button>

                <button wire:click="quickAction('system_settings')"
                        class="flex items-center p-4 text-left bg-gradient-to-r from-gray-50 to-gray-100 hover:from-gray-100 hover:to-gray-200 rounded-xl transition-all duration-200 group">
                    <div class="bg-gray-600 p-2 rounded-lg mr-4 group-hover:bg-gray-700 transition-colors">
                        <i class="fas fa-cog text-white"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900">System Settings</div>
                        <div class="text-sm text-gray-600">Configure platform</div>
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
                    class="bg-white shadow-lg border border-gray-200 rounded-full p-3 hover:shadow-xl transition-shadow duration-200">
                <i class="fas fa-sliders-h text-gray-600"></i>
            </button>
            
            <div x-show="open" 
                 x-transition
                 @click.outside="open = false"
                 class="absolute bottom-full right-0 mb-2 bg-white rounded-xl shadow-xl border border-gray-200 p-4 w-64">
                <h3 class="font-semibold text-gray-900 mb-3">Dashboard Widgets</h3>
                <div class="space-y-2">
                    @foreach($showWidgets as $widget => $isVisible)
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" 
                               wire:model="showWidgets.{{ $widget }}" 
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">{{ ucwords(str_replace('_', ' ', $widget)) }}</span>
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
    // User Growth Chart
    const userGrowthData = @json($this->userGrowthData);
    if (userGrowthData.length > 0) {
        const ctx1 = document.getElementById('userGrowthChart').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: userGrowthData.map(item => item.date),
                datasets: [{
                    label: 'New Users',
                    data: userGrowthData.map(item => item.new_users),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.4,
                }, {
                    label: 'Total Users',
                    data: userGrowthData.map(item => item.total_users),
                    borderColor: 'rgb(16, 185, 129)',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
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

    // Revenue Chart
    const revenueData = @json($this->revenueAnalytics);
    if (revenueData.length > 0) {
        const ctx2 = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: revenueData.map(item => item.date),
                datasets: [{
                    label: 'Daily Revenue (₦)',
                    data: revenueData.map(item => item.revenue),
                    backgroundColor: 'rgba(245, 158, 11, 0.8)',
                    borderColor: 'rgb(245, 158, 11)',
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

// Auto-refresh dashboard data
setInterval(() => {
    @this.call('loadAllData');
}, {{ $refreshInterval }});
</script>
@endpush