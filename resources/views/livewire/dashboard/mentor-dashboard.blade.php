<div class="min-h-screen bg-gradient-to-br from-teal-50 to-cyan-50 p-4 md:p-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 relative overflow-hidden">
            <!-- Background decoration -->
            <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-teal-100 to-cyan-100 rounded-full -translate-y-12 translate-x-12 opacity-60"></div>
            
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center space-y-4 lg:space-y-0 relative">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-hands-helping text-teal-600 mr-3"></i>
                        Mentor Dashboard
                    </h1>
                    <p class="text-gray-600 mt-1">Welcome back, {{ auth()->user()->name }}! Guide your mentees to success.</p>
                    
                    <div class="flex items-center space-x-4 mt-3">
                        <div class="flex items-center bg-teal-100 text-teal-800 px-3 py-1 rounded-full text-sm">
                            <i class="fas fa-users mr-2"></i>
                            {{ $this->overviewStats['active_mentees'] }} active mentees
                        </div>
                        <div class="flex items-center bg-cyan-100 text-cyan-800 px-3 py-1 rounded-full text-sm">
                            <i class="fas fa-chart-line mr-2"></i>
                            {{ $this->overviewStats['success_rate'] }}% success rate
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-wrap items-center space-x-3">
                    <!-- Mentee Filter -->
                    <select wire:model.live="selectedMenteeFilter" class="bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="all">All Mentees</option>
                        <option value="active">Active Mentees</option>
                        <option value="new">New Mentees</option>
                        <option value="struggling">Need Attention</option>
                    </select>
                    
                    <!-- Timeframe Selector -->
                    <div class="flex bg-gray-100 rounded-lg p-1">
                        @foreach(['7days' => '7d', '30days' => '30d', '90days' => '90d'] as $value => $label)
                            <button 
                                wire:click="updateTimeframe('{{ $value }}')"
                                class="px-3 py-1 rounded-md text-sm font-medium transition-all duration-200 {{ $selectedTimeframe === $value ? 'bg-white text-teal-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                    
                    <!-- Quick Actions -->
                    <a href="{{ route('mentorship.actions') }}" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                        <i class="fas fa-calendar-plus mr-2"></i>
                        Schedule Session
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
                    <p class="text-sm text-gray-600 font-medium">Total Mentees</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ $this->overviewStats['total_mentees'] }}</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="text-green-600 font-medium">{{ $this->overviewStats['active_mentees'] }}</span>
                        <span class="text-gray-500 ml-1">active</span>
                    </div>
                </div>
                <div class="bg-teal-100 p-3 rounded-full">
                    <i class="fas fa-users text-teal-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Sessions Completed</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ $this->overviewStats['completed_sessions'] }}</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="text-blue-600 font-medium">{{ $this->overviewStats['upcoming_sessions'] }}</span>
                        <span class="text-gray-500 ml-1">upcoming</span>
                    </div>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-comments text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Success Rate</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ $this->overviewStats['success_rate'] }}%</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <div class="flex text-yellow-400 mr-2">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star{{ $i <= $this->overviewStats['avg_session_rating'] ? '' : '-o' }}"></i>
                            @endfor
                        </div>
                        <span class="text-gray-500">{{ number_format($this->overviewStats['avg_session_rating'], 1) }} rating</span>
                    </div>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <i class="fas fa-trophy text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Career Placements</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ $this->overviewStats['career_placements'] }}</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="text-purple-600 font-medium">{{ $this->overviewStats['certificates_guided'] }}</span>
                        <span class="text-gray-500 ml-1">certificates</span>
                    </div>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <i class="fas fa-briefcase text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <!-- Left Column -->
        <div class="xl:col-span-2 space-y-8">
            <!-- Mentee Progress -->
            @if($showWidgets['mentee_progress'])
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Mentee Progress</h2>
                    <a href="{{ route('mentorship.dashboard') }}" class="text-teal-600 hover:text-teal-700 text-sm font-medium">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                <div class="space-y-4">
                    @forelse($this->menteeProgress->take(6) as $mentee)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                        <div class="flex items-center space-x-4">
                            @if($mentee['profile_picture'])
                            <img src="{{ $mentee['profile_picture'] }}" alt="{{ $mentee['name'] }}" class="w-12 h-12 rounded-full object-cover">
                            @else
                            <div class="w-12 h-12 bg-gradient-to-br from-teal-100 to-cyan-100 rounded-full flex items-center justify-center">
                                <span class="font-bold text-teal-600">{{ substr($mentee['name'], 0, 2) }}</span>
                            </div>
                            @endif
                            
                            <div>
                                <h4 class="font-semibold text-gray-900">{{ $mentee['name'] }}</h4>
                                <div class="flex items-center space-x-3 text-sm text-gray-600 mt-1">
                                    <span>Learning: {{ $mentee['learning_progress'] }}%</span>
                                    <span>Career: {{ $mentee['career_progress']['success_rate'] }}%</span>
                                    <span class="flex items-center">
                                        <i class="fas fa-star text-yellow-400 mr-1"></i>
                                        {{ number_format($mentee['overall_score'], 1) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-right">
                            <span class="px-3 py-1 text-xs rounded-full {{ $mentee['status'] === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($mentee['status']) }}
                            </span>
                            <p class="text-xs text-gray-500 mt-1">{{ $mentee['goals_completed'] }} goals completed</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <i class="fas fa-user-plus text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-600 mb-4">No mentees assigned yet</p>
                        <a href="{{ route('mentorship.actions') }}" class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2 rounded-lg transition-colors">
                            Find Mentees
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif

            <!-- Session Analytics -->
            @if($showWidgets['session_analytics'])
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Session Analytics</h2>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Session Trends Chart -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Session Trends</h3>
                        <div class="h-48">
                            <canvas id="sessionTrendsChart"></canvas>
                        </div>
                    </div>
                    
                    <!-- Session Types Distribution -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Session Types</h3>
                        <div class="space-y-3">
                            @foreach($this->sessionAnalytics['session_types'] as $type => $percentage)
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600 capitalize">{{ str_replace('_', ' ', $type) }}</span>
                                <div class="flex items-center space-x-2">
                                    <div class="w-20 bg-gray-200 rounded-full h-2">
                                        <div class="bg-gradient-to-r from-teal-400 to-cyan-500 h-2 rounded-full" 
                                             style="width: {{ $percentage }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $percentage }}%</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <!-- Success Metrics -->
                        <div class="mt-6 pt-4 border-t border-gray-200">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Success Metrics</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="text-center">
                                    <div class="text-lg font-bold text-teal-600">{{ $this->sessionAnalytics['success_metrics']['goal_completion_rate'] }}%</div>
                                    <div class="text-xs text-gray-500">Goal Completion</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-lg font-bold text-cyan-600">{{ $this->sessionAnalytics['success_metrics']['mentee_retention_rate'] }}%</div>
                                    <div class="text-xs text-gray-500">Retention Rate</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Career Guidance Outcomes -->
            @if($showWidgets['career_guidance'])
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Career Guidance Outcomes</h2>
                
                <div class="space-y-4">
                    @forelse($this->careerGuidance['career_outcomes']->take(5) as $outcome)
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                        <div>
                            <h4 class="font-semibold text-gray-900">{{ $outcome['mentee_name'] }}</h4>
                            <div class="flex items-center space-x-4 text-sm text-gray-600 mt-1">
                                <span>{{ $outcome['total_applications'] }} applications</span>
                                <span>{{ $outcome['interview_invites'] }} interviews</span>
                                <span>{{ $outcome['hired'] }} hired</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-purple-600">{{ $outcome['success_rate'] }}%</div>
                            <div class="text-xs text-gray-500">success rate</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-6">
                        <i class="fas fa-chart-line text-gray-400 text-3xl mb-2"></i>
                        <p class="text-gray-600">No career guidance data available</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column -->
        <div class="space-y-8">
            <!-- Mentorship Goals -->
            @if($showWidgets['mentorship_goals'])
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Monthly Goals</h2>
                
                <div class="space-y-4">
                    <!-- Sessions Goal -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Sessions</span>
                            <span class="text-sm text-gray-600">
                                {{ $this->mentorshipGoals['monthly_targets']['sessions_completed'] }} / {{ $this->mentorshipGoals['monthly_targets']['sessions_target'] }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-gradient-to-r from-teal-500 to-cyan-500 h-3 rounded-full transition-all duration-300" 
                                 style="width: {{ min(($this->mentorshipGoals['monthly_targets']['sessions_completed'] / $this->mentorshipGoals['monthly_targets']['sessions_target']) * 100, 100) }}%"></div>
                        </div>
                    </div>

                    <!-- Certifications Goal -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Certifications</span>
                            <span class="text-sm text-gray-600">
                                {{ $this->mentorshipGoals['monthly_targets']['mentee_certifications_achieved'] }} / {{ $this->mentorshipGoals['monthly_targets']['mentee_certifications_target'] }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-gradient-to-r from-purple-500 to-indigo-500 h-3 rounded-full transition-all duration-300" 
                                 style="width: {{ min(($this->mentorshipGoals['monthly_targets']['mentee_certifications_achieved'] / $this->mentorshipGoals['monthly_targets']['mentee_certifications_target']) * 100, 100) }}%"></div>
                        </div>
                    </div>

                    <!-- Job Placements Goal -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">Job Placements</span>
                            <span class="text-sm text-gray-600">
                                {{ $this->mentorshipGoals['monthly_targets']['job_placements_achieved'] }} / {{ $this->mentorshipGoals['monthly_targets']['job_placements_target'] }}
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-3 rounded-full transition-all duration-300" 
                                 style="width: {{ min(($this->mentorshipGoals['monthly_targets']['job_placements_achieved'] / $this->mentorshipGoals['monthly_targets']['job_placements_target']) * 100, 100) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Mock Interviews -->
            @if($showWidgets['mock_interviews'])
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Recent Mock Interviews</h2>
                    <a href="{{ route('admin.interview') }}" class="text-teal-600 hover:text-teal-700 text-sm font-medium">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                <div class="space-y-3 max-h-80 overflow-y-auto">
                    @forelse($this->mockInterviews as $interview)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900">{{ $interview['mentee_name'] }}</h4>
                                <p class="text-sm text-gray-600">{{ $interview['interview_type'] }}</p>
                                <div class="flex items-center mt-2 space-x-3 text-xs text-gray-500">
                                    <span>Score: {{ $interview['score'] }}%</span>
                                    <span>{{ $interview['duration'] }}min</span>
                                    <span>{{ $interview['conducted_at']->format('M j') }}</span>
                                </div>
                            </div>
                            <span class="px-2 py-1 text-xs rounded-full {{ $interview['feedback_given'] ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $interview['feedback_given'] ? 'Complete' : 'Pending' }}
                            </span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-6">
                        <i class="fas fa-microphone-alt text-gray-400 text-3xl mb-2"></i>
                        <p class="text-gray-600">No mock interviews conducted</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif

            <!-- Support Tickets -->
            @if($showWidgets['support_tickets'])
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Mentee Support</h2>
                    <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded-full">
                        {{ $this->overviewStats['pending_requests'] }} pending
                    </span>
                </div>
                
                <div class="space-y-3 max-h-80 overflow-y-auto">
                    @forelse($this->supportTickets as $ticket)
                    <div class="flex items-center space-x-3 p-3 border border-gray-200 rounded-lg">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center {{ $ticket['priority'] === 'high' ? 'bg-red-100' : ($ticket['priority'] === 'medium' ? 'bg-yellow-100' : 'bg-green-100') }}">
                            <i class="fas fa-exclamation text-xs {{ $ticket['priority'] === 'high' ? 'text-red-600' : ($ticket['priority'] === 'medium' ? 'text-yellow-600' : 'text-green-600') }}"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-medium text-gray-900 truncate">{{ $ticket['mentee_name'] }}</h4>
                            <p class="text-xs text-gray-600 truncate">{{ $ticket['subject'] }}</p>
                            <span class="text-xs text-gray-500">{{ $ticket['created_at']->format('M j, Y') }}</span>
                        </div>
                        <span class="px-2 py-1 text-xs rounded-full {{ $ticket['status'] === 'resolved' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ ucfirst($ticket['status']) }}
                        </span>
                    </div>
                    @empty
                    <div class="text-center py-6">
                        <i class="fas fa-check-circle text-green-400 text-3xl mb-2"></i>
                        <p class="text-gray-600">No pending support tickets</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif

            <!-- Recent Activities -->
            @if($showWidgets['recent_activities'])
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Recent Activities</h2>
                
                <div class="space-y-4">
                    @forelse($this->recentActivities as $activity)
                    <div class="flex items-start space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="flex-shrink-0 w-8 h-8 bg-{{ $activity['color'] }}-100 rounded-full flex items-center justify-center">
                            <i class="{{ $activity['icon'] }} text-{{ $activity['color'] }}-600 text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900">{{ $activity['title'] }}</p>
                            <p class="text-xs text-gray-600">{{ $activity['description'] }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ $activity['timestamp']->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-6">
                        <i class="fas fa-clock text-gray-400 text-3xl mb-2"></i>
                        <p class="text-gray-600">No recent activities</p>
                    </div>
                    @endforelse
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
    // Session Trends Chart
    const sessionData = @json($this->sessionAnalytics['session_trends']);
    if (sessionData.length > 0) {
        const ctx = document.getElementById('sessionTrendsChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: sessionData.map(item => item.date),
                datasets: [{
                    label: 'Sessions',
                    data: sessionData.map(item => item.sessions),
                    borderColor: 'rgb(20, 184, 166)',
                    backgroundColor: 'rgba(20, 184, 166, 0.1)',
                    fill: true,
                    tension: 0.4,
                }, {
                    label: 'Avg Rating',
                    data: sessionData.map(item => item.avg_rating),
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    fill: false,
                    tension: 0.4,
                    yAxisID: 'y1',
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
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        min: 0,
                        max: 5,
                        grid: {
                            drawOnChartArea: false,
                        },
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
}, 300000); // 5 minutes
</script>
@endpush