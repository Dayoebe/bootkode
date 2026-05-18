<div class="min-h-screen bg-themed-primary transition-colors duration-300 p-4 md:p-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-themed-secondary rounded-2xl shadow-sm border border-themed-primary p-6 transition-colors duration-300">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center space-y-4 lg:space-y-0">
                <div>
                    <h1 class="text-3xl font-bold text-themed-primary flex items-center transition-colors duration-300">
                        <i class="fas fa-user-shield accent-themed-primary mr-3"></i>
                        Super Admin Dashboard
                    </h1>
                    <p class="text-themed-secondary mt-1 transition-colors duration-300">Welcome back, {{ auth()->user()->name }}! Here's what's happening with your platform.</p>
                </div>
                
                <div class="flex flex-wrap items-center space-x-3">
                    <!-- Timeframe Selector -->
                    <div class="flex bg-themed-tertiary rounded-lg p-1 transition-colors duration-300">
                        @foreach(['24hours' => '24h', '7days' => '7d', '30days' => '30d', '90days' => '90d'] as $value => $label)
                            <button 
                                wire:click="updateTimeframe('{{ $value }}')"
                                class="px-3 py-1 rounded-md text-sm font-medium transition-all duration-200 {{ $selectedTimeframe === $value ? 'bg-themed-secondary accent-themed-primary shadow-sm' : 'text-themed-secondary hover:text-themed-primary' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                    
                    <!-- Quick Actions -->
                    <button wire:click="toggleQuickActionModal" 
                            class="bg-accent-themed-primary hover:bg-accent-themed-secondary text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200 flex items-center">
                        <i class="fas fa-bolt mr-2"></i>
                        Quick Actions
                    </button>
                    
                    <!-- Settings -->
                    <button class="p-2 text-themed-tertiary hover:text-themed-secondary transition-colors duration-200">
                        <i class="fas fa-cog text-lg"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Overview Stats Grid -->
    @if($showWidgets['overview_stats'])
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-6 hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-themed-secondary font-medium transition-colors duration-300">Total Users</p>
                    <h3 class="text-3xl font-bold text-themed-primary mt-1 transition-colors duration-300">{{ number_format($this->overviewStats['total_users']) }}</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="text-green-600 dark:text-green-400 font-medium transition-colors duration-300">+{{ $this->overviewStats['new_users_today'] }}</span>
                        <span class="text-themed-tertiary ml-1 transition-colors duration-300">today</span>
                    </div>
                </div>
                <div class="bg-blue-100 dark:bg-blue-900/30 p-3 rounded-full transition-colors duration-300">
                    <i class="fas fa-users text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-6 hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-themed-secondary font-medium transition-colors duration-300">Active Courses</p>
                    <h3 class="text-3xl font-bold text-themed-primary mt-1 transition-colors duration-300">{{ number_format($this->overviewStats['published_courses']) }}</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="text-orange-600 dark:text-orange-400 font-medium transition-colors duration-300">{{ $this->overviewStats['pending_courses'] }}</span>
                        <span class="text-themed-tertiary ml-1 transition-colors duration-300">pending</span>
                    </div>
                </div>
                <div class="bg-green-100 dark:bg-green-900/30 p-3 rounded-full transition-colors duration-300">
                    <i class="fas fa-book text-green-600 dark:text-green-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-6 hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-themed-secondary font-medium transition-colors duration-300">Total Revenue</p>
                    <h3 class="text-3xl font-bold text-themed-primary mt-1 transition-colors duration-300">₦{{ number_format($this->overviewStats['total_revenue'], 2) }}</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="text-green-600 dark:text-green-400 font-medium transition-colors duration-300">₦{{ number_format($this->overviewStats['monthly_revenue'], 2) }}</span>
                        <span class="text-themed-tertiary ml-1 transition-colors duration-300">this month</span>
                    </div>
                </div>
                <div class="bg-yellow-100 dark:bg-yellow-900/30 p-3 rounded-full transition-colors duration-300">
                    <i class="fas fa-chart-line text-yellow-600 dark:text-yellow-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-6 hover:shadow-md transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-themed-secondary font-medium transition-colors duration-300">Support Tickets</p>
                    <h3 class="text-3xl font-bold text-themed-primary mt-1 transition-colors duration-300">{{ $this->overviewStats['open_tickets'] }}</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="text-red-600 dark:text-red-400 font-medium transition-colors duration-300">{{ $this->overviewStats['pending_certificates'] }}</span>
                        <span class="text-themed-tertiary ml-1 transition-colors duration-300">cert. pending</span>
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
            <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-themed-primary transition-colors duration-300">User Growth</h2>
                    <div class="flex items-center space-x-2 text-sm text-themed-tertiary transition-colors duration-300">
                        <i class="fas fa-chart-area"></i>
                        <span>Last {{ $selectedTimeframe === '24hours' ? '24 hours' : ($selectedTimeframe === '7days' ? '7 days' : ($selectedTimeframe === '30days' ? '30 days' : '90 days')) }}</span>
                    </div>
                </div>
                <div class="h-80 bg-themed-tertiary rounded-lg p-4">
                    <canvas id="userGrowthChart"></canvas>
                </div>
            </div>
            @endif

            <!-- Revenue Analytics -->
            @if($showWidgets['revenue_analytics'])
            <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-themed-primary transition-colors duration-300">Revenue Analytics</h2>
                    <div class="flex items-center space-x-2 text-sm text-themed-tertiary transition-colors duration-300">
                        <i class="fas fa-money-bill-wave"></i>
                        <span>Daily Revenue Trend</span>
                    </div>
                </div>
                <div class="h-80 bg-themed-tertiary rounded-lg p-4">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
            @endif

            <!-- Course Performance -->
            @if($showWidgets['course_performance'])
            <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-themed-primary transition-colors duration-300">Top Performing Courses</h2>
                    <a href="{{ route('all-course') }}" class="accent-themed-primary hover:text-accent-themed-secondary text-sm font-medium transition-colors duration-300">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="space-y-4">
                    @forelse($this->coursePerformance as $course)
                    <div class="flex items-center justify-between p-4 bg-themed-tertiary rounded-lg hover:bg-themed-primary transition-colors duration-200">
                        <div class="flex-1">
                            <h4 class="font-semibold text-themed-primary transition-colors duration-300">{{ Str::limit($course['title'], 40) }}</h4>
                            <p class="text-sm text-themed-secondary transition-colors duration-300">by {{ $course['instructor'] }}</p>
                        </div>
                        <div class="flex items-center space-x-6 text-sm">
                            <div class="text-center">
                                <div class="font-semibold text-themed-primary transition-colors duration-300">{{ $course['enrollments'] }}</div>
                                <div class="text-themed-tertiary transition-colors duration-300">enrollments</div>
                            </div>
                            <div class="text-center">
                                <div class="font-semibold text-green-600 dark:text-green-400 transition-colors duration-300">{{ $course['completion_rate'] }}%</div>
                                <div class="text-themed-tertiary transition-colors duration-300">completion</div>
                            </div>
                            <div class="text-center">
                                <div class="font-semibold text-yellow-600 dark:text-yellow-400 transition-colors duration-300">{{ number_format($course['rating'], 1) }}</div>
                                <div class="text-themed-tertiary transition-colors duration-300">rating</div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-themed-tertiary text-center py-4 transition-colors duration-300">No course data available</p>
                    @endforelse
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column (System Info & Activities) -->
        <div class="space-y-8">
            <!-- System Health -->
            @if($showWidgets['system_health'])
            <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-themed-primary transition-colors duration-300">System Health</h2>
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-green-500 dark:bg-green-400 rounded-full mr-2"></div>
                        <span class="text-sm text-green-600 dark:text-green-400 font-medium transition-colors duration-300">Operational</span>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 rounded-lg transition-colors duration-300">
                        <div class="flex items-center">
                            <i class="fas fa-database text-green-600 dark:text-green-400 mr-3"></i>
                            <span class="font-medium text-themed-primary transition-colors duration-300">Database</span>
                        </div>
                        <span class="text-green-600 dark:text-green-400 font-medium transition-colors duration-300">{{ $this->systemHealth['database_health']['status'] === 'healthy' ? 'Healthy' : 'Error' }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg transition-colors duration-300">
                        <div class="flex items-center">
                            <i class="fas fa-memory text-blue-600 dark:text-blue-400 mr-3"></i>
                            <span class="font-medium text-themed-primary transition-colors duration-300">Cache</span>
                        </div>
                        <span class="text-blue-600 dark:text-blue-400 font-medium transition-colors duration-300">{{ $this->systemHealth['cache_status'] === 'operational' ? 'Active' : 'Error' }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-themed-tertiary rounded-lg transition-colors duration-300">
                        <div class="flex items-center">
                            <i class="fas fa-hdd text-themed-secondary mr-3"></i>
                            <span class="font-medium text-themed-primary transition-colors duration-300">Storage</span>
                        </div>
                        <span class="text-themed-secondary font-medium transition-colors duration-300">{{ $this->systemHealth['storage_usage']['percentage'] }}%</span>
                    </div>
                </div>
            </div>
            @endif

            <!-- Recent Activities -->
            @if($showWidgets['recent_activities'])
            <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-themed-primary transition-colors duration-300">Recent Activities</h2>
                    <button class="accent-themed-primary hover:text-accent-themed-secondary text-sm font-medium transition-colors duration-300">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </button>
                </div>
                <div class="space-y-3">
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
                    <p class="text-themed-tertiary text-center py-4 transition-colors duration-300">No recent activities</p>
                    @endforelse
                </div>
            </div>
            @endif

            <!-- Pending Approvals -->
            @if($showWidgets['pending_approvals'])
            <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-themed-primary transition-colors duration-300">Pending Approvals</h2>
                    <div class="flex items-center space-x-1">
                        <span class="bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-300 text-xs font-medium px-2 py-1 rounded-full transition-colors duration-300">
                            {{ count($this->pendingApprovals['courses']) + count($this->pendingApprovals['certificates']) }}
                        </span>
                    </div>
                </div>
                
                <!-- Courses Tab -->
                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-semibold text-themed-secondary mb-3 transition-colors duration-300">
                            Courses ({{ count($this->pendingApprovals['courses']) }})
                        </h3>
                        @forelse($this->pendingApprovals['courses'] as $course)
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
                        <p class="text-themed-tertiary text-sm">No pending courses</p>
                        @endforelse
                    </div>

                    <!-- Certificates -->
                    <div>
                        <h3 class="text-sm font-semibold text-themed-secondary mb-3 transition-colors duration-300">
                            Certificates ({{ count($this->pendingApprovals['certificates']) }})
                        </h3>
                        @forelse($this->pendingApprovals['certificates'] as $certificate)
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
                        <p class="text-themed-tertiary text-sm">No pending certificates</p>
                        @endforelse
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Quick Actions Modal -->
    @if($showQuickActionModal)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4 transition-colors duration-300">
        <div class="bg-themed-secondary rounded-3xl p-8 w-full max-w-2xl transform transition-all duration-300 scale-100 max-h-[90vh] overflow-y-auto border border-themed-primary">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-2xl font-bold text-themed-primary flex items-center transition-colors duration-300">
                    <i class="fas fa-lightning-bolt accent-themed-primary mr-3"></i>
                    Quick Actions
                </h3>
                <button wire:click="toggleQuickActionModal" class="text-themed-tertiary hover:text-themed-secondary transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Quick action buttons -->
                <button wire:click="quickAction('manage_users')"
                        class="flex items-center p-4 text-left bg-themed-tertiary hover:bg-themed-primary rounded-xl transition-all duration-200 group border border-transparent hover:border-themed-secondary">
                    <div class="bg-accent-themed-primary p-2 rounded-lg mr-4 group-hover:bg-accent-themed-secondary transition-colors">
                        <i class="fas fa-users text-white"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-themed-primary transition-colors duration-300">Manage Users</div>
                        <div class="text-sm text-themed-secondary transition-colors duration-300">User administration panel</div>
                    </div>
                </button>

                <!-- Add more quick action buttons as needed -->
            </div>
        </div>
    </div>
    @endif

    <!-- Widget Toggle Panel -->
    <div class="fixed bottom-6 right-6 z-40">
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" 
                    class="bg-themed-secondary shadow-lg border border-themed-primary rounded-full p-3 hover:shadow-xl transition-all duration-200">
                <i class="fas fa-sliders-h text-themed-secondary"></i>
            </button>
            
            <div x-show="open" 
                 x-transition
                 @click.outside="open = false"
                 class="absolute bottom-full right-0 mb-2 bg-themed-secondary rounded-xl shadow-xl border border-themed-primary p-4 w-64 transition-colors duration-300">
                <h3 class="font-semibold text-themed-primary mb-3 transition-colors duration-300">Dashboard Widgets</h3>
                <div class="space-y-2">
                    @foreach($showWidgets as $widget => $isVisible)
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" 
                               wire:model="showWidgets.{{ $widget }}" 
                               class="rounded border-themed-secondary text-accent-themed-primary focus:ring-accent-themed-primary">
                        <span class="ml-2 text-sm text-themed-secondary transition-colors duration-300">{{ ucwords(str_replace('_', ' ', $widget)) }}</span>
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
    const theme = document.documentElement.className || 'light';
    const isDark = theme === 'dark';
    
    // Chart colors based on theme
    const chartColors = {
        light: {
            text: '#374151',
            grid: 'rgba(0, 0, 0, 0.05)',
            primary: '#3B82F6',
            secondary: '#10B981'
        },
        dark: {
            text: '#D1D5DB',
            grid: 'rgba(55, 65, 81, 0.3)',
            primary: '#60A5FA',
            secondary: '#34D399'
        },
        sepia: {
            text: '#5C4B37',
            grid: 'rgba(92, 75, 55, 0.1)',
            primary: '#8B5C2E',
            secondary: '#A0784F'
        },
        ocean: {
            text: '#0C4A6E',
            grid: 'rgba(12, 74, 110, 0.1)',
            primary: '#0284C7',
            secondary: '#0891B2'
        },
        forest: {
            text: '#14532D',
            grid: 'rgba(20, 83, 45, 0.1)',
            primary: '#16A34A',
            secondary: '#15803D'
        }
    };

    const colors = chartColors[theme] || chartColors.light;

    // User Growth Chart
    const userGrowthData = @json($this->userGrowthData ?? []);
    if (window.bootkodeDashboardCharts?.shouldRender('userGrowthChart', userGrowthData, ['new_users', 'total_users'])) {
        const ctx1 = document.getElementById('userGrowthChart');
        if (ctx1) {
            new Chart(ctx1.getContext('2d'), {
                type: 'line',
                data: {
                    labels: userGrowthData.map(item => item.date),
                    datasets: [{
                        label: 'New Users',
                        data: userGrowthData.map(item => item.new_users),
                        borderColor: colors.primary,
                        backgroundColor: colors.primary + '20',
                        fill: true,
                        tension: 0.4,
                    }, {
                        label: 'Total Users',
                        data: userGrowthData.map(item => item.total_users),
                        borderColor: colors.secondary,
                        backgroundColor: colors.secondary + '20',
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
                            labels: { color: colors.text }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { color: colors.text },
                            grid: { color: colors.grid }
                        },
                        x: {
                            ticks: { color: colors.text },
                            grid: { display: false }
                        }
                    }
                }
            });
        }
    }

    // Revenue Chart
    const revenueData = @json($this->revenueAnalytics ?? []);
    if (window.bootkodeDashboardCharts?.shouldRender('revenueChart', revenueData, ['revenue'])) {
        const ctx2 = document.getElementById('revenueChart');
        if (ctx2) {
            new Chart(ctx2.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: revenueData.map(item => item.date),
                    datasets: [{
                        label: 'Revenue',
                        data: revenueData.map(item => item.revenue),
                        backgroundColor: colors.secondary + 'CC',
                        borderColor: colors.secondary,
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
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return '₦' + Number(context.parsed.y || 0).toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                color: colors.text,
                                callback: function(value) {
                                    return '₦' + Number(value || 0).toLocaleString();
                                }
                            },
                            grid: { color: colors.grid }
                        },
                        x: {
                            ticks: { color: colors.text },
                            grid: { display: false }
                        }
                    }
                }
            });
        }
    }

    // Listen for theme changes and reload charts
    window.addEventListener('theme-changed', function() {
        setTimeout(() => {
            location.reload();
        }, 100);
    });
});

// Auto-refresh dashboard data
setInterval(() => {
    @this.call('loadAllData');
}, {{ $refreshInterval ?? 300000 }});
</script>
@endpush
