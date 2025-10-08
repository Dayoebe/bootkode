<div class="space-y-6">
    <!-- Header and Controls -->
    <div class="flex flex-col sm:flex-row gap-4 justify-between items-start sm:items-center">
        <div>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Institution Analytics</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">Comprehensive analytics and insights</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button wire:click="exportAnalytics" 
                    class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition-colors">
                <i class="fas fa-download mr-2"></i>
                Export Data
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 transition-colors duration-300">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Institution</label>
                <select wire:model.live="selectedInstitution" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="all">All Institutions</option>
                    @foreach($institutions as $institution)
                        <option value="{{ $institution->id }}">{{ $institution->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date Range</label>
                <select wire:model.live="dateRange" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md focus:ring-blue-500 focus:border-blue-500">
                    <option value="7">Last 7 days</option>
                    <option value="30">Last 30 days</option>
                    <option value="90">Last 90 days</option>
                    <option value="365">Last year</option>
                </select>
            </div>
        </div>
    </div>

    @if(!empty($analyticsData))
        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @if(isset($analyticsData['institutions']))
                <!-- Total Institutions -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-colors duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Institutions</p>
                            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ number_format($analyticsData['institutions']['total']) }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ number_format($analyticsData['institutions']['active']) }} active</p>
                        </div>
                        <div class="bg-blue-100 dark:bg-blue-900/30 p-3 rounded-full">
                            <i class="fas fa-university text-blue-600 dark:text-blue-400 text-xl"></i>
                        </div>
                    </div>
                </div>
            @endif

            @if(isset($analyticsData['users']))
                <!-- Total Users -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-colors duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Users</p>
                            <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ number_format($analyticsData['users']['total']) }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ number_format($analyticsData['users']['active']) }} active</p>
                        </div>
                        <div class="bg-green-100 dark:bg-green-900/30 p-3 rounded-full">
                            <i class="fas fa-users text-green-600 dark:text-green-400 text-xl"></i>
                        </div>
                    </div>
                </div>
            @endif

            @if(isset($analyticsData['enrollments']))
                <!-- Total Enrollments -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-colors duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Enrollments</p>
                            <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ number_format($analyticsData['enrollments']['total']) }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ number_format($analyticsData['enrollments']['completed']) }} completed</p>
                        </div>
                        <div class="bg-purple-100 dark:bg-purple-900/30 p-3 rounded-full">
                            <i class="fas fa-graduation-cap text-purple-600 dark:text-purple-400 text-xl"></i>
                        </div>
                    </div>
                </div>
            @endif

            @if(isset($analyticsData['certificates']))
                <!-- Total Certificates -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-colors duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Certificates Issued</p>
                            <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">{{ number_format($analyticsData['certificates']['total']) }}</p>
                            @if(isset($analyticsData['certificates']['this_month']))
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ number_format($analyticsData['certificates']['this_month']) }} this month</p>
                            @endif
                        </div>
                        <div class="bg-yellow-100 dark:bg-yellow-900/30 p-3 rounded-full">
                            <i class="fas fa-certificate text-yellow-600 dark:text-yellow-400 text-xl"></i>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Charts Section -->
        @if(isset($analyticsData['growth_trend']) || isset($analyticsData['activity']))
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Growth Chart -->
                @if(isset($analyticsData['growth_trend']))
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-colors duration-300">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Growth Trend</h3>
                            <div class="flex space-x-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300">
                                    <span class="w-2 h-2 bg-blue-500 dark:bg-blue-400 rounded-full mr-1"></span>
                                    Institutions
                                </span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                    <span class="w-2 h-2 bg-green-500 dark:bg-green-400 rounded-full mr-1"></span>
                                    Users
                                </span>
                            </div>
                        </div>
                        <div class="h-64">
                            <canvas id="growthTrendChart" class="w-full h-full"></canvas>
                        </div>
                    </div>
                @endif

                <!-- Institution Types -->
                @if(isset($analyticsData['institutions']['by_type']))
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-colors duration-300">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Institution Types</h3>
                            <i class="fas fa-chart-pie text-gray-400 dark:text-gray-500"></i>
                        </div>
                        <div class="space-y-4">
                            @php
                                $total = array_sum($analyticsData['institutions']['by_type']);
                                $colors = ['bg-blue-500', 'bg-green-500', 'bg-purple-500', 'bg-yellow-500', 'bg-red-500', 'bg-indigo-500'];
                                $darkColors = ['dark:bg-blue-400', 'dark:bg-green-400', 'dark:bg-purple-400', 'dark:bg-yellow-400', 'dark:bg-red-400', 'dark:bg-indigo-400'];
                                $index = 0;
                            @endphp
                            @foreach($analyticsData['institutions']['by_type'] as $type => $count)
                                @php 
                                    $percentage = $total > 0 ? ($count / $total) * 100 : 0; 
                                    $color = $colors[$index % count($colors)];
                                    $darkColor = $darkColors[$index % count($darkColors)];
                                    $index++;
                                @endphp
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-3 h-3 rounded-full {{ $color }} {{ $darkColor }}"></div>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ ucfirst(str_replace('_', ' ', $type)) }}</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $count }}</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-500">({{ number_format($percentage, 1) }}%)</span>
                                    </div>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="h-2 rounded-full {{ $color }} {{ $darkColor }} transition-all duration-300" style="width: {{ $percentage }}%"></div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Top Institutions and Popular Courses -->
        @if(isset($analyticsData['top_institutions']) || isset($analyticsData['activity']['popular_courses']))
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Top Institutions -->
                @if(isset($analyticsData['top_institutions']))
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-colors duration-300">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Top Institutions</h3>
                            <span class="text-xs text-gray-500 dark:text-gray-400">By user count</span>
                        </div>
                        <div class="space-y-4">
                            @foreach($analyticsData['top_institutions'] as $institution)
                                <div class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                                            <span class="text-white font-semibold text-sm">
                                                {{ substr($institution->name, 0, 2) }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900 dark:text-white">{{ Str::limit($institution->name, 25) }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $institution->institution_type_name }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ number_format($institution->users_count) }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">users</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Popular Courses -->
                @if(isset($analyticsData['activity']['popular_courses']))
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-colors duration-300">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Popular Courses</h3>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Most enrolled</span>
                        </div>
                        <div class="space-y-3">
                            @foreach($analyticsData['activity']['popular_courses'] as $course)
                                <div class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ Str::limit($course['course_title'], 30) }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Course</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-semibold text-gray-900 dark:text-white">{{ number_format($course['enrollment_count']) }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">enrollments</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Detailed Analytics Tables -->
        @if($selectedInstitution !== 'all' && isset($analyticsData['usage_report']))
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 transition-colors duration-300">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Usage Report</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($analyticsData['usage_report']['users']['active_in_period']) }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Active Users</div>
                        <div class="text-xs text-gray-400 dark:text-gray-500">in selected period</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($analyticsData['usage_report']['courses']['total_accessed']) }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Courses Accessed</div>
                        <div class="text-xs text-gray-400 dark:text-gray-500">unique courses</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($analyticsData['usage_report']['courses']['completed']) }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Completed</div>
                        <div class="text-xs text-gray-400 dark:text-gray-500">course completions</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($analyticsData['usage_report']['engagement']['total_study_time']) }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Study Minutes</div>
                        <div class="text-xs text-gray-400 dark:text-gray-500">total time spent</div>
                    </div>
                </div>
            </div>
        @endif

    @else
        <!-- Loading or Empty State -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center transition-colors duration-300">
            <i class="fas fa-chart-line text-4xl text-gray-300 dark:text-gray-600 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Loading Analytics...</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Please wait while we gather the data.</p>
        </div>
    @endif
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(isset($analyticsData['growth_trend']) && !empty($analyticsData['growth_trend']['months']))
        const ctx = document.getElementById('growthTrendChart');
        if (ctx) {
            // Detect dark mode
            const isDarkMode = document.documentElement.classList.contains('dark');
            const gridColor = isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.05)';
            const textColor = isDarkMode ? '#9CA3AF' : '#6B7280';
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($analyticsData['growth_trend']['months']),
                    datasets: [{
                        label: 'Institutions',
                        data: @json($analyticsData['growth_trend']['institutions']),
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Users',
                        data: @json($analyticsData['growth_trend']['users']),
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true
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
                                color: gridColor
                            },
                            ticks: {
                                color: textColor
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: textColor
                            }
                        }
                    },
                    elements: {
                        point: {
                            radius: 4,
                            hoverRadius: 6
                        }
                    }
                }
            });
        }
    @endif
});
</script>
@endpush