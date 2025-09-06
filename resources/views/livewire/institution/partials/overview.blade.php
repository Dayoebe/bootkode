<!-- Overview Dashboard -->
<div class="space-y-6">
    <!-- Key Metrics Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Revenue Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                    <p class="text-3xl font-bold text-green-600">${{ number_format($stats['total_revenue']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $selectedPeriod }} days</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Recent Enrollments Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">New Enrollments</p>
                    <p class="text-3xl font-bold text-blue-600">{{ number_format($stats['recent_enrollments']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $selectedPeriod }} days</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-user-graduate text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Certificates Issued Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Certificates Issued</p>
                    <p class="text-3xl font-bold text-purple-600">{{ number_format($stats['certificates_issued']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $selectedPeriod }} days</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <i class="fas fa-certificate text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Active Institutions Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Institutions</p>
                    <p class="text-3xl font-bold text-indigo-600">{{ number_format($stats['active_institutions']) }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ round(($stats['active_institutions'] / max($stats['total_institutions'], 1)) * 100, 1) }}% of total
                    </p>
                </div>
                <div class="bg-indigo-100 p-3 rounded-full">
                    <i class="fas fa-university text-indigo-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Analytics Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Growth Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Growth Trends</h3>
                <div class="flex space-x-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        <span class="w-2 h-2 bg-blue-500 rounded-full mr-1"></span>
                        Institutions
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-1"></span>
                        Users
                    </span>
                </div>
            </div>
            <div class="h-64">
                @if(!empty($monthlyGrowthData['months']))
                    <canvas id="growthChart" class="w-full h-full"></canvas>
                @else
                    <div class="flex items-center justify-center h-full text-gray-500">
                        <div class="text-center">
                            <i class="fas fa-chart-line text-4xl mb-2"></i>
                            <p>No data available</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Institution Type Breakdown -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Institution Types</h3>
                <i class="fas fa-chart-pie text-gray-400"></i>
            </div>
            <div class="space-y-4">
                @if($institutionTypeBreakdown->count() > 0)
                    @php
                        $total = $institutionTypeBreakdown->sum();
                        $colors = ['bg-blue-500', 'bg-green-500', 'bg-purple-500', 'bg-yellow-500', 'bg-red-500', 'bg-indigo-500', 'bg-pink-500'];
                    @endphp
                    @foreach($institutionTypeBreakdown as $type => $count)
                        @php $percentage = $total > 0 ? ($count / $total) * 100 : 0; @endphp
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-3 h-3 rounded-full {{ $colors[$loop->index % count($colors)] }}"></div>
                                <span class="text-sm font-medium text-gray-700">{{ $type }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-600">{{ $count }}</span>
                                <span class="text-xs text-gray-500">({{ number_format($percentage, 1) }}%)</span>
                            </div>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full {{ $colors[$loop->index % count($colors)] }}" 
                                 style="width: {{ $percentage }}%"></div>
                        </div>
                    @endforeach
                @else
                    <div class="flex items-center justify-center py-8 text-gray-500">
                        <div class="text-center">
                            <i class="fas fa-chart-pie text-4xl mb-2"></i>
                            <p>No institutions yet</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- License Status and Top Institutions Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- License Status Breakdown -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">License Status</h3>
                <i class="fas fa-key text-gray-400"></i>
            </div>
            <div class="grid grid-cols-2 gap-4">
                @php
                    $statusColors = [
                        'Active' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'icon' => 'fas fa-check-circle text-green-600'],
                        'Expiring Soon' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'icon' => 'fas fa-exclamation-triangle text-yellow-600'],
                        'Expired' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'icon' => 'fas fa-times-circle text-red-600'],
                        'Suspended' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'fas fa-pause-circle text-gray-600']
                    ];
                @endphp
                @foreach($licenseStatusBreakdown as $status => $count)
                    @php $colors = $statusColors[$status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'fas fa-question-circle text-gray-600']; @endphp
                    <div class="p-4 rounded-lg {{ $colors['bg'] }}">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-2xl font-bold {{ $colors['text'] }}">{{ $count }}</p>
                                <p class="text-sm {{ $colors['text'] }}">{{ $status }}</p>
                            </div>
                            <i class="{{ $colors['icon'] }} text-xl"></i>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Top Institutions by Users -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Top Institutions</h3>
                <span class="text-xs text-gray-500">By active users</span>
            </div>
            <div class="space-y-4">
                @if($topInstitutions->count() > 0)
                    @foreach($topInstitutions as $institution)
                        <div class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                                    @if($institution->logo)
                                        <img src="{{ $institution->logo }}" alt="{{ $institution->name }}" class="w-8 h-8 rounded object-cover">
                                    @else
                                        <span class="text-white font-semibold text-sm">
                                            {{ substr($institution->name, 0, 2) }}
                                        </span>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ Str::limit($institution->name, 25) }}</p>
                                    <p class="text-xs text-gray-500">{{ $institution->institution_type_name }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-900">{{ number_format($institution->users_count) }}</p>
                                <p class="text-xs text-gray-500">users</p>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="flex items-center justify-center py-8 text-gray-500">
                        <div class="text-center">
                            <i class="fas fa-building text-4xl mb-2"></i>
                            <p>No institutions yet</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Recent Activities</h3>
            <span class="text-xs text-gray-500">Last {{ $selectedPeriod }} days</span>
        </div>
        <div class="space-y-4">
            @if($recentActivities->count() > 0)
                @foreach($recentActivities as $activity)
                    <div class="flex items-start space-x-4 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-{{ $activity['color'] }}-100 rounded-full flex items-center justify-center">
                                <i class="{{ $activity['icon'] }} text-{{ $activity['color'] }}-600 text-sm"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-gray-900">{{ $activity['title'] }}</p>
                            <p class="text-sm text-gray-600">{{ $activity['description'] }}</p>
                            <div class="flex items-center space-x-4 mt-1">
                                <span class="text-xs text-gray-500">
                                    <i class="fas fa-user mr-1"></i>{{ $activity['user'] }}
                                </span>
                                <span class="text-xs text-gray-500">
                                    <i class="fas fa-clock mr-1"></i>{{ $activity['time']->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="flex items-center justify-center py-8 text-gray-500">
                    <div class="text-center">
                        <i class="fas fa-history text-4xl mb-2"></i>
                        <p>No recent activities</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(!empty($monthlyGrowthData['months']))
        const ctx = document.getElementById('growthChart');
        if (ctx) {
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: @json($monthlyGrowthData['months']),
                    datasets: [{
                        label: 'Institutions',
                        data: @json($monthlyGrowthData['institutions']),
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Users',
                        data: @json($monthlyGrowthData['users']),
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
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
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