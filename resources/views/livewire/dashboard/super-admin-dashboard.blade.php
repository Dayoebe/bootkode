<div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-300 p-4 md:p-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 transition-colors duration-300">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center space-y-4 lg:space-y-0">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center transition-colors duration-300">
                        <i class="fas fa-user-shield text-blue-600 dark:text-blue-400 mr-3"></i>
                        Super Admin Dashboard
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-1 transition-colors duration-300">Welcome back, {{ auth()->user()->name }}! Here's what's happening with your platform.</p>
                </div>
                
                <div class="flex flex-wrap items-center space-x-3">
                    <!-- Timeframe Selector -->
                    <div class="flex bg-gray-100 dark:bg-gray-700 rounded-lg p-1 transition-colors duration-300">
                        @foreach(['24hours' => '24h', '7days' => '7d', '30days' => '30d', '90days' => '90d'] as $value => $label)
                            <button 
                                wire:click="updateTimeframe('{{ $value }}')"
                                class="px-3 py-1 rounded-md text-sm font-medium transition-all duration-200 {{ $selectedTimeframe === $value ? 'bg-white dark:bg-gray-600 text-blue-600 dark:text-blue-400 shadow-sm' : 'text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                    
                    <!-- Quick Actions -->
                    <button wire:click="toggleQuickActionModal" 
                            class="bg-blue-600 hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                        <i class="fas fa-bolt mr-2"></i>
                        Quick Actions
                    </button>
                    
                    <!-- Settings -->
                    <button class="p-2 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-200">
                        <i class="fas fa-cog text-lg"></i>
                    </button>
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
                    <p class="text-sm text-gray-600 dark:text-gray-400 font-medium transition-colors duration-300">Total Users</p>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-1 transition-colors duration-300">{{ number_format($this->overviewStats['total_users']) }}</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="text-green-600 dark:text-green-400 font-medium transition-colors duration-300">+{{ $this->overviewStats['new_users_today'] }}</span>
                        <span class="text-gray-500 dark:text-gray-400 ml-1 transition-colors duration-300">today</span>
                    </div>
                </div>
                <div class="bg-blue-100 dark:bg-blue-900/30 p-3 rounded-full transition-colors duration-300">
                    <i class="fas fa-users text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 hover:shadow-md dark:hover:shadow-xl transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 font-medium transition-colors duration-300">Active Courses</p>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-1 transition-colors duration-300">{{ number_format($this->overviewStats['published_courses']) }}</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="text-orange-600 dark:text-orange-400 font-medium transition-colors duration-300">{{ $this->overviewStats['pending_courses'] }}</span>
                        <span class="text-gray-500 dark:text-gray-400 ml-1 transition-colors duration-300">pending</span>
                    </div>
                </div>
                <div class="bg-green-100 dark:bg-green-900/30 p-3 rounded-full transition-colors duration-300">
                    <i class="fas fa-book text-green-600 dark:text-green-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 hover:shadow-md dark:hover:shadow-xl transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 font-medium transition-colors duration-300">Total Revenue</p>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-1 transition-colors duration-300">₦{{ number_format($this->overviewStats['total_revenue'], 2) }}</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="text-green-600 dark:text-green-400 font-medium transition-colors duration-300">₦{{ number_format($this->overviewStats['monthly_revenue'], 2) }}</span>
                        <span class="text-gray-500 dark:text-gray-400 ml-1 transition-colors duration-300">this month</span>
                    </div>
                </div>
                <div class="bg-yellow-100 dark:bg-yellow-900/30 p-3 rounded-full transition-colors duration-300">
                    <i class="fas fa-chart-line text-yellow-600 dark:text-yellow-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 hover:shadow-md dark:hover:shadow-xl transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 font-medium transition-colors duration-300">Support Tickets</p>
                    <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-1 transition-colors duration-300">{{ $this->overviewStats['open_tickets'] }}</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="text-red-600 dark:text-red-400 font-medium transition-colors duration-300">{{ $this->overviewStats['pending_certificates'] }}</span>
                        <span class="text-gray-500 dark:text-gray-400 ml-1 transition-colors duration-300">cert. pending</span>
                    </div>
                </div>
                <div class="bg-red-100 dark:bg-red-900/30 p-3 rounded-full transition-colors duration-300">
                    <i class="fas fa-ticket-alt text-red-600 dark:text-red-400 text-xl"></i>
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
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 transition-colors duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white transition-colors duration-300">User Growth</h2>
                    <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400 transition-colors duration-300">
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
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 transition-colors duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white transition-colors duration-300">Revenue Analytics</h2>
                    <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400 transition-colors duration-300">
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
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 transition-colors duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white transition-colors duration-300">Top Performing Courses</h2>
                    <a href="{{ route('all-course') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 text-sm font-medium transition-colors duration-300">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="space-y-4">
                    @forelse($this->coursePerformance as $course)
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200">
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900 dark:text-white transition-colors duration-300">{{ Str::limit($course['title'], 40) }}</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 transition-colors duration-300">by {{ $course['instructor'] }}</p>
                        </div>
                        <div class="flex items-center space-x-6 text-sm">
                            <div class="text-center">
                                <div class="font-semibold text-gray-900 dark:text-white transition-colors duration-300">{{ $course['enrollments'] }}</div>
                                <div class="text-gray-500 dark:text-gray-400 transition-colors duration-300">enrollments</div>
                            </div>
                            <div class="text-center">
                                <div class="font-semibold text-green-600 dark:text-green-400 transition-colors duration-300">{{ $course['completion_rate'] }}%</div>
                                <div class="text-gray-500 dark:text-gray-400 transition-colors duration-300">completion</div>
                            </div>
                            <div class="text-center">
                                <div class="font-semibold text-yellow-600 dark:text-yellow-400 transition-colors duration-300">{{ number_format($course['rating'], 1) }}</div>
                                <div class="text-gray-500 dark:text-gray-400 transition-colors duration-300">rating</div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-500 dark:text-gray-400 text-center py-4 transition-colors duration-300">No course data available</p>
                    @endforelse
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column (System Info & Activities) -->
        <div class="space-y-8">
            <!-- System Health -->
            @if($showWidgets['system_health'])
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 transition-colors duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white transition-colors duration-300">System Health</h2>
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-green-500 dark:bg-green-400 rounded-full mr-2"></div>
                        <span class="text-sm text-green-600 dark:text-green-400 font-medium transition-colors duration-300">Operational</span>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 rounded-lg transition-colors duration-300">
                        <div class="flex items-center">
                            <i class="fas fa-database text-green-600 dark:text-green-400 mr-3"></i>
                            <span class="font-medium text-gray-900 dark:text-white transition-colors duration-300">Database</span>
                        </div>
                        <span class="text-green-600 dark:text-green-400 font-medium transition-colors duration-300">{{ $this->systemHealth['database_health']['status'] === 'healthy' ? 'Healthy' : 'Error' }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg transition-colors duration-300">
                        <div class="flex items-center">
                            <i class="fas fa-memory text-blue-600 dark:text-blue-400 mr-3"></i>
                            <span class="font-medium text-gray-900 dark:text-white transition-colors duration-300">Cache</span>
                        </div>
                        <span class="text-blue-600 dark:text-blue-400 font-medium transition-colors duration-300">{{ $this->systemHealth['cache_status'] === 'operational' ? 'Active' : 'Error' }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg transition-colors duration-300">
                        <div class="flex items-center">
                            <i class="fas fa-hdd text-gray-600 dark:text-gray-300 mr-3"></i>
                            <span class="font-medium text-gray-900 dark:text-white transition-colors duration-300">Storage</span>
                        </div>
                        <span class="text-gray-600 dark:text-gray-300 font-medium transition-colors duration-300">{{ $this->systemHealth['storage_usage']['percentage'] }}%</span>
                    </div>
                </div>
            </div>
            @endif

            <!-- Recent Activities -->
            @if($showWidgets['recent_activities'])
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 transition-colors duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white transition-colors duration-300">Recent Activities</h2>
                    <button class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 text-sm font-medium transition-colors duration-300">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </button>
                </div>
                <div class="space-y-3">
                    @forelse($this->recentActivities as $activity)
                    <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                        <div class="flex-shrink-0 w-8 h-8 bg-gray-100 dark:bg-gray-600 rounded-full flex items-center justify-center transition-colors duration-300">
                            <i class="{{ $activity['icon'] }} text-xs {{ $activity['color'] }} dark:{{ str_replace('text-', 'text-', $activity['color']) }}"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white transition-colors duration-300">{{ $activity['description'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 transition-colors duration-300">{{ $activity['causer'] }} • {{ $activity['created_at'] }}</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-500 dark:text-gray-400 text-center py-4 transition-colors duration-300">No recent activities</p>
                    @endforelse
                </div>
            </div>
            @endif

            <!-- Pending Approvals -->
            @if($showWidgets['pending_approvals'])
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 transition-colors duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white transition-colors duration-300">Pending Approvals</h2>
                    <div class="flex items-center space-x-1">
                        <span class="bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300 text-xs font-medium px-2 py-1 rounded-full transition-colors duration-300">
                            {{ count($this->pendingApprovals['courses']) + count($this->pendingApprovals['certificates']) }}
                        </span>
                    </div>
                </div>
                
                <!-- Courses Tab -->
                <div class="space-y-4">
                    <div>
                        <div class="font-semibold text-gray-900 dark:text-white transition-colors duration-300">Create Course</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 transition-colors duration-300">Add new learning content</div>
                    </div>
                </button>

                <button wire:click="quickAction('manage_users')"
                        class="flex items-center p-4 text-left bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/30 rounded-xl transition-all duration-200 group border border-transparent hover:border-green-200 dark:hover:border-green-800">
                    <div class="bg-green-600 dark:bg-green-500 p-2 rounded-lg mr-4 group-hover:bg-green-700 dark:group-hover:bg-green-600 transition-colors">
                        <i class="fas fa-users text-white"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 dark:text-white transition-colors duration-300">Manage Users</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 transition-colors duration-300">User administration panel</div>
                    </div>
                </button>

                <button wire:click="quickAction('view_tickets')"
                        class="flex items-center p-4 text-left bg-yellow-50 dark:bg-yellow-900/20 hover:bg-yellow-100 dark:hover:bg-yellow-900/30 rounded-xl transition-all duration-200 group border border-transparent hover:border-yellow-200 dark:hover:border-yellow-800">
                    <div class="bg-yellow-600 dark:bg-yellow-500 p-2 rounded-lg mr-4 group-hover:bg-yellow-700 dark:group-hover:bg-yellow-600 transition-colors">
                        <i class="fas fa-ticket-alt text-white"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 dark:text-white transition-colors duration-300">Support Tickets</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 transition-colors duration-300">Handle user requests</div>
                    </div>
                </button>

                <button wire:click="quickAction('view_analytics')"
                        class="flex items-center p-4 text-left bg-purple-50 dark:bg-purple-900/20 hover:bg-purple-100 dark:hover:bg-purple-900/30 rounded-xl transition-all duration-200 group border border-transparent hover:border-purple-200 dark:hover:border-purple-800">
                    <div class="bg-purple-600 dark:bg-purple-500 p-2 rounded-lg mr-4 group-hover:bg-purple-700 dark:group-hover:bg-purple-600 transition-colors">
                        <i class="fas fa-chart-bar text-white"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 dark:text-white transition-colors duration-300">View Analytics</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 transition-colors duration-300">Platform insights</div>
                    </div>
                </button>

                <button wire:click="quickAction('system_settings')"
                        class="flex items-center p-4 text-left bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-xl transition-all duration-200 group border border-transparent hover:border-gray-200 dark:hover:border-gray-600">
                    <div class="bg-gray-600 dark:bg-gray-500 p-2 rounded-lg mr-4 group-hover:bg-gray-700 dark:group-hover:bg-gray-400 transition-colors">
                        <i class="fas fa-cog text-white"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-900 dark:text-white transition-colors duration-300">System Settings</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 transition-colors duration-300">Configure platform</div>
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
                    class="bg-white dark:bg-gray-800 shadow-lg border border-gray-200 dark:border-gray-700 rounded-full p-3 hover:shadow-xl dark:hover:shadow-2xl transition-all duration-200">
                <i class="fas fa-sliders-h text-gray-600 dark:text-gray-300"></i>
            </button>
            
            <div x-show="open" 
                 x-transition
                 @click.outside="open = false"
                 class="absolute bottom-full right-0 mb-2 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 p-4 w-64 transition-colors duration-300">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-3 transition-colors duration-300">Dashboard Widgets</h3>
                <div class="space-y-2">
                    @foreach($showWidgets as $widget => $isVisible)
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" 
                               wire:model="showWidgets.{{ $widget }}" 
                               class="rounded border-gray-300 dark:border-gray-600 text-blue-600 dark:text-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400 dark:bg-gray-700">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300 transition-colors duration-300">{{ ucwords(str_replace('_', ' ', $widget)) }}</span>
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
                    borderColor: isDark ? '#60A5FA' : '#3B82F6',
                    backgroundColor: isDark ? 'rgba(96, 165, 250, 0.1)' : 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.4,
                }, {
                    label: 'Total Users',
                    data: userGrowthData.map(item => item.total_users),
                    borderColor: isDark ? '#34D399' : '#10B981',
                    backgroundColor: isDark ? 'rgba(52, 211, 153, 0.1)' : 'rgba(16, 185, 129, 0.1)',
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
                    backgroundColor: isDark ? 'rgba(251, 191, 36, 0.8)' : 'rgba(245, 158, 11, 0.8)',
                    borderColor: isDark ? '#FCD34D' : '#F59E0B',
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

// Auto-refresh dashboard data
setInterval(() => {
    @this.call('loadAllData');
}, {{ $refreshInterval }});
</script>
@endpush
