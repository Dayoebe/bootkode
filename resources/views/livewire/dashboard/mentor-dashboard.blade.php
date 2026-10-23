<div class="space-y-6">
    <x-ui.page-header title="Mentoring workspace" description="Review your mentees, sessions and pending feedback.">
        <x-slot:actions>
<!-- Mentee Filter -->
                    <select aria-label="Filter mentees" wire:model.live="selectedMenteeFilter"
                        class="bg-themed-secondary border border-themed-secondary text-themed-primary px-3 py-2 text-sm rounded-lg focus:ring-2 focus:ring-brand/30 dark:focus:ring-brand/30 focus:border-brand dark:focus:border-brand transition-colors duration-300">
                        <option value="all">All Mentees</option>
                        <option value="active">Active Mentees</option>
                        <option value="new">New Mentees</option>
                        <option value="struggling">Need Attention</option>
                    </select>

                    <!-- Timeframe Selector -->
                    <div role="group" aria-label="Reporting period" class="flex bg-themed-tertiary rounded-lg p-1 transition-colors duration-300">
                        @foreach(['7days' => '7d', '30days' => '30d', '90days' => '90d'] as $value => $label)
                            <button wire:click="updateTimeframe('{{ $value }}')" aria-label="Report for {{ $label }}" aria-pressed="{{ $selectedTimeframe === $value ? 'true' : 'false' }}"
                                class="px-3 py-1 rounded-md text-sm font-medium transition-all duration-200 {{ $selectedTimeframe === $value ? 'bg-themed-secondary text-brand dark:text-brand shadow-sm' : 'text-themed-secondary hover:text-themed-primary' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Quick Actions -->
                    <a href="{{ route('mentorship.hub') }}"
                        class="bg-action dark:bg-action hover:bg-action dark:hover:bg-action text-action-foreground px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                        <i class="fas fa-calendar-plus mr-2"></i>
                        Schedule Session
                    </a>
        </x-slot:actions>
    </x-ui.page-header>

    <!-- Overview Stats Grid -->
    @if($showWidgets['overview_stats'])
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div
                class="bg-themed-secondary rounded-lg shadow-sm border border-themed-primary p-6   transition-all duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-themed-secondary font-medium transition-colors duration-300">Total Mentees
                        </p>
                        <h3 class="text-3xl font-bold text-themed-primary mt-1 transition-colors duration-300">
                            {{ $this->overviewStats['total_mentees'] }}</h3>
                        <div class="flex items-center mt-2 text-sm">
                            <span
                                class="text-green-600 dark:text-green-400 font-medium transition-colors duration-300">{{ $this->overviewStats['active_mentees'] }}</span>
                            <span class="text-themed-tertiary ml-1 transition-colors duration-300">active</span>
                        </div>
                    </div>
                    <div class="bg-brand/10 dark:bg-action/30 p-3 rounded-full transition-colors duration-300">
                        <i class="fas fa-users text-brand dark:text-brand text-xl"></i>
                    </div>
                </div>
            </div>

            <div
                class="bg-themed-secondary rounded-lg shadow-sm border border-themed-primary p-6   transition-all duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-themed-secondary font-medium transition-colors duration-300">Sessions
                            Completed</p>
                        <h3 class="text-3xl font-bold text-themed-primary mt-1 transition-colors duration-300">
                            {{ $this->overviewStats['completed_sessions'] }}</h3>
                        <div class="flex items-center mt-2 text-sm">
                            <span
                                class="text-brand dark:text-brand font-medium transition-colors duration-300">{{ $this->overviewStats['upcoming_sessions'] }}</span>
                            <span class="text-themed-tertiary ml-1 transition-colors duration-300">upcoming</span>
                        </div>
                    </div>
                    <div class="bg-brand/10 dark:bg-action/30 p-3 rounded-full transition-colors duration-300">
                        <i class="fas fa-comments text-brand dark:text-brand text-xl"></i>
                    </div>
                </div>
            </div>

            <div
                class="bg-themed-secondary rounded-lg shadow-sm border border-themed-primary p-6   transition-all duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-themed-secondary font-medium transition-colors duration-300">Success Rate</p>
                        <h3 class="text-3xl font-bold text-themed-primary mt-1 transition-colors duration-300">
                            {{ $this->overviewStats['success_rate'] }}%</h3>
                        <div class="flex items-center mt-2 text-sm">
                            <div class="flex text-yellow-400 dark:text-yellow-300 mr-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <i
                                        class="fas fa-star{{ $i <= $this->overviewStats['avg_session_rating'] ? '' : '-o' }}"></i>
                                @endfor
                            </div>
                            <span
                                class="text-themed-tertiary transition-colors duration-300">{{ number_format($this->overviewStats['avg_session_rating'], 1) }}
                                rating</span>
                        </div>
                    </div>
                    <div class="bg-yellow-100 dark:bg-yellow-900/30 p-3 rounded-full transition-colors duration-300">
                        <i class="fas fa-trophy text-yellow-600 dark:text-yellow-400 text-xl"></i>
                    </div>
                </div>
            </div>

            <div
                class="bg-themed-secondary rounded-lg shadow-sm border border-themed-primary p-6   transition-all duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-themed-secondary font-medium transition-colors duration-300">Career
                            Placements</p>
                        <h3 class="text-3xl font-bold text-themed-primary mt-1 transition-colors duration-300">
                            {{ $this->overviewStats['career_placements'] }}</h3>
                        <div class="flex items-center mt-2 text-sm">
                            <span
                                class="text-brand dark:text-brand font-medium transition-colors duration-300">{{ $this->overviewStats['certificates_guided'] }}</span>
                            <span class="text-themed-tertiary ml-1 transition-colors duration-300">certificates</span>
                        </div>
                    </div>
                    <div class="bg-brand/10 dark:bg-action/30 p-3 rounded-full transition-colors duration-300">
                        <i class="fas fa-briefcase text-brand dark:text-brand text-xl"></i>
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
                <div
                    class="bg-themed-secondary rounded-lg shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-themed-primary transition-colors duration-300">Mentee Progress
                        </h2>
                        <a href="{{ route('mentorship.dashboard') }}"
                            class="text-brand dark:text-brand hover:text-brand dark:hover:text-brand text-sm font-medium transition-colors duration-300">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>

                    <div class="space-y-4">
                        @forelse($this->menteeProgress->take(6) as $mentee)
                            <div
                                class="flex items-center justify-between p-4 bg-themed-tertiary rounded-lg hover:bg-themed-secondary transition-colors duration-200">
                                <div class="flex items-center space-x-4">
                                    @if($mentee['profile_picture'])
                                        <img src="{{ $mentee['profile_picture'] }}" alt="{{ $mentee['name'] }}"
                                            class="w-12 h-12 rounded-full object-cover">
                                    @else
                                        <div
                                            class="w-12 h-12 bg-brand/10 dark:bg-action/30 rounded-full flex items-center justify-center transition-colors duration-300">
                                            <span
                                                class="font-bold text-brand dark:text-brand">{{ substr($mentee['name'], 0, 2) }}</span>
                                        </div>
                                    @endif

                                    <div>
                                        <h4 class="font-semibold text-themed-primary transition-colors duration-300">
                                            {{ $mentee['name'] }}</h4>
                                        <div
                                            class="flex items-center space-x-3 text-sm text-themed-secondary mt-1 transition-colors duration-300">
                                            <span>Learning: {{ $mentee['learning_progress'] }}%</span>
                                            <span>Career: {{ $mentee['career_progress']['success_rate'] }}%</span>
                                            <span class="flex items-center">
                                                <i class="fas fa-star text-yellow-400 dark:text-yellow-300 mr-1"></i>
                                                {{ number_format($mentee['overall_score'], 1) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <span
                                        class="px-3 py-1 text-xs rounded-full {{ $mentee['status'] === 'active' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-themed-tertiary text-themed-primary' }} transition-colors duration-300">
                                        {{ ucfirst($mentee['status']) }}
                                    </span>
                                    <p class="text-xs text-themed-secondary mt-1 transition-colors duration-300">
                                        {{ $mentee['goals_completed'] }} goals completed</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <i class="fas fa-user-plus text-themed-tertiary text-4xl mb-4"></i>
                                <p class="text-themed-secondary mb-4 transition-colors duration-300">No mentees assigned yet</p>
                                <a href="{{ route('mentorship.hub') }}"
                                    class="bg-action dark:bg-action hover:bg-action dark:hover:bg-action text-action-foreground px-4 py-2 rounded-lg transition-colors">
                                    Find Mentees
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif

            <!-- Session Analytics -->
            @if($showWidgets['session_analytics'])
                <div
                    class="bg-themed-secondary rounded-lg shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                    <h2 class="text-xl font-bold text-themed-primary mb-6 transition-colors duration-300">Session Analytics
                    </h2>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Session Trends Chart -->
                        <div>
                            <h3 class="text-sm font-semibold text-themed-primary mb-3 transition-colors duration-300">
                                Session Trends</h3>
                            <div class="h-48">
                                <canvas role="img" aria-label="session Trends chart" id="sessionTrendsChart"></canvas>
                            </div>
                        </div>

                        <!-- Session Types Distribution -->
                        <div>
                            <h3 class="text-sm font-semibold text-themed-primary mb-3 transition-colors duration-300">
                                Session Types</h3>
                            <div class="space-y-3">
                                @foreach($this->sessionAnalytics['session_types'] as $type => $percentage)
                                    <div class="flex items-center justify-between">
                                        <span
                                            class="text-sm text-themed-secondary capitalize transition-colors duration-300">{{ str_replace('_', ' ', $type) }}</span>
                                        <div class="flex items-center space-x-2">
                                            <div
                                                class="w-20 bg-themed-tertiary rounded-full h-2 transition-colors duration-300">
                                                <div class="bg-surface-muted     h-2 rounded-full"
                                                    style="width: {{ $percentage }}%"></div>
                                            </div>
                                            <span
                                                class="text-sm font-medium text-themed-primary transition-colors duration-300">{{ $percentage }}%</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Success Metrics -->
                            <div class="mt-6 pt-4 border-t border-themed-primary transition-colors duration-300">
                                <h4 class="text-sm font-semibold text-themed-primary mb-3 transition-colors duration-300">
                                    Success Metrics</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="text-center">
                                        <div
                                            class="text-lg font-bold text-brand dark:text-brand transition-colors duration-300">
                                            {{ $this->sessionAnalytics['success_metrics']['goal_completion_rate'] }}%</div>
                                        <div class="text-xs text-themed-secondary transition-colors duration-300">Goal
                                            Completion</div>
                                    </div>
                                    <div class="text-center">
                                        <div
                                            class="text-lg font-bold text-brand dark:text-brand transition-colors duration-300">
                                            {{ $this->sessionAnalytics['success_metrics']['mentee_retention_rate'] }}%</div>
                                        <div class="text-xs text-themed-secondary transition-colors duration-300">Retention
                                            Rate</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Career Guidance Outcomes -->
            @if($showWidgets['career_guidance'])
                <div
                    class="bg-themed-secondary rounded-lg shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                    <h2 class="text-xl font-bold text-themed-primary mb-6 transition-colors duration-300">Career Guidance
                        Outcomes</h2>

                    <div class="space-y-4">
                        @forelse($this->careerGuidance['career_outcomes']->take(5) as $outcome)
                            <div
                                class="flex items-center justify-between p-4 border border-themed-primary rounded-lg transition-colors duration-300">
                                <div>
                                    <h4 class="font-semibold text-themed-primary transition-colors duration-300">
                                        {{ $outcome['mentee_name'] }}</h4>
                                    <div
                                        class="flex items-center space-x-4 text-sm text-themed-secondary mt-1 transition-colors duration-300">
                                        <span>{{ $outcome['total_applications'] }} applications</span>
                                        <span>{{ $outcome['interview_invites'] }} interviews</span>
                                        <span>{{ $outcome['hired'] }} hired</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div
                                        class="text-lg font-bold text-brand dark:text-brand transition-colors duration-300">
                                        {{ $outcome['success_rate'] }}%</div>
                                    <div class="text-xs text-themed-secondary transition-colors duration-300">success rate</div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6">
                                <i class="fas fa-chart-line text-themed-tertiary text-3xl mb-2"></i>
                                <p class="text-themed-secondary transition-colors duration-300">No career guidance data
                                    available</p>
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
                <div
                    class="bg-themed-secondary rounded-lg shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                    <h2 class="text-xl font-bold text-themed-primary mb-6 transition-colors duration-300">Monthly Goals</h2>

                    <div class="space-y-4">
                        <!-- Sessions Goal -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span
                                    class="text-sm font-medium text-themed-primary transition-colors duration-300">Sessions</span>
                                <span class="text-sm text-themed-secondary transition-colors duration-300">
                                    {{ $this->mentorshipGoals['monthly_targets']['sessions_completed'] }} /
                                    {{ $this->mentorshipGoals['monthly_targets']['sessions_target'] }}
                                </span>
                            </div>
                            <div class="w-full bg-themed-tertiary rounded-full h-3 transition-colors duration-300">
                                <div class="bg-surface-muted     h-3 rounded-full transition-all duration-300"
                                    style="width: {{ min(($this->mentorshipGoals['monthly_targets']['sessions_completed'] / $this->mentorshipGoals['monthly_targets']['sessions_target']) * 100, 100) }}%">
                                </div>
                            </div>
                        </div>

                        <!-- Certifications Goal -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span
                                    class="text-sm font-medium text-themed-primary transition-colors duration-300">Certifications</span>
                                <span class="text-sm text-themed-secondary transition-colors duration-300">
                                    {{ $this->mentorshipGoals['monthly_targets']['mentee_certifications_achieved'] }} /
                                    {{ $this->mentorshipGoals['monthly_targets']['mentee_certifications_target'] }}
                                </span>
                            </div>
                            <div class="w-full bg-themed-tertiary rounded-full h-3 transition-colors duration-300">
                                <div class="bg-surface-muted     h-3 rounded-full transition-all duration-300"
                                    style="width: {{ min(($this->mentorshipGoals['monthly_targets']['mentee_certifications_achieved'] / $this->mentorshipGoals['monthly_targets']['mentee_certifications_target']) * 100, 100) }}%">
                                </div>
                            </div>
                        </div>

                        <!-- Job Placements Goal -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-themed-primary transition-colors duration-300">Job
                                    Placements</span>
                                <span class="text-sm text-themed-secondary transition-colors duration-300">
                                    {{ $this->mentorshipGoals['monthly_targets']['job_placements_achieved'] }} /
                                    {{ $this->mentorshipGoals['monthly_targets']['job_placements_target'] }}
                                </span>
                            </div>
                            <div class="w-full bg-themed-tertiary rounded-full h-3 transition-colors duration-300">
                                <div class="bg-surface-muted     h-3 rounded-full transition-all duration-300"
                                    style="width: {{ min(($this->mentorshipGoals['monthly_targets']['job_placements_achieved'] / $this->mentorshipGoals['monthly_targets']['job_placements_target']) * 100, 100) }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Mock Interviews -->
            @if($showWidgets['mock_interviews'])
                <div
                    class="bg-themed-secondary rounded-lg shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-themed-primary transition-colors duration-300">Recent Mock
                            Interviews</h2>
                        <a href="{{ route('admin.interview') }}"
                            class="text-brand dark:text-brand hover:text-brand dark:hover:text-brand text-sm font-medium transition-colors duration-300">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>

                    <div class="space-y-3 max-h-80 overflow-y-auto">
                        @forelse($this->mockInterviews as $interview)
                            <div class="border border-themed-primary rounded-lg p-4 transition-colors duration-300">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-themed-primary transition-colors duration-300">
                                            {{ $interview['mentee_name'] }}</h4>
                                        <p class="text-sm text-themed-secondary transition-colors duration-300">
                                            {{ $interview['interview_type'] }}</p>
                                        <div
                                            class="flex items-center mt-2 space-x-3 text-xs text-themed-secondary transition-colors duration-300">
                                            <span>Score: {{ $interview['score'] }}%</span>
                                            <span>{{ $interview['duration'] }}min</span>
                                            <span>{{ $interview['conducted_at']->format('M j') }}</span>
                                        </div>
                                    </div>
                                    <span
                                        class="px-2 py-1 text-xs rounded-full {{ $interview['feedback_given'] ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300' }} transition-colors duration-300">
                                        {{ $interview['feedback_given'] ? 'Complete' : 'Pending' }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6">
                                <i class="fas fa-microphone-alt text-themed-tertiary text-3xl mb-2"></i>
                                <p class="text-themed-secondary transition-colors duration-300">No mock interviews conducted</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif

            <!-- Support Tickets -->
            @if($showWidgets['support_tickets'])
                <div
                    class="bg-themed-secondary rounded-lg shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-themed-primary transition-colors duration-300">Mentee Support</h2>
                        <span
                            class="bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 text-xs font-medium px-2 py-1 rounded-full transition-colors duration-300">
                            {{ $this->overviewStats['pending_requests'] }} pending
                        </span>
                    </div>

                    <div class="space-y-3 max-h-80 overflow-y-auto">
                        @forelse($this->supportTickets as $ticket)
                            <div
                                class="flex items-center space-x-3 p-3 border border-themed-primary rounded-lg transition-colors duration-300">
                                <div
                                    class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center {{ $ticket['priority'] === 'high' ? 'bg-red-100 dark:bg-red-900/30' : ($ticket['priority'] === 'medium' ? 'bg-yellow-100 dark:bg-yellow-900/30' : 'bg-green-100 dark:bg-green-900/30') }} transition-colors duration-300">
                                    <i
                                        class="fas fa-exclamation text-xs {{ $ticket['priority'] === 'high' ? 'text-red-600 dark:text-red-400' : ($ticket['priority'] === 'medium' ? 'text-yellow-600 dark:text-yellow-400' : 'text-green-600 dark:text-green-400') }}"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-medium text-themed-primary truncate transition-colors duration-300">
                                        {{ $ticket['mentee_name'] }}</h4>
                                    <p class="text-xs text-themed-secondary truncate transition-colors duration-300">
                                        {{ $ticket['subject'] }}</p>
                                    <span
                                        class="text-xs text-themed-tertiary transition-colors duration-300">{{ $ticket['created_at']->format('M j, Y') }}</span>
                                </div>
                                <span
                                    class="px-2 py-1 text-xs rounded-full {{ $ticket['status'] === 'resolved' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300' }} transition-colors duration-300">
                                    {{ ucfirst($ticket['status']) }}
                                </span>
                            </div>
                        @empty
                            <div class="text-center py-6">
                                <i class="fas fa-check-circle text-green-400 dark:text-green-500 text-3xl mb-2"></i>
                                <p class="text-themed-secondary transition-colors duration-300">No pending support tickets</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif

            <!-- Recent Activities -->
            @if($showWidgets['recent_activities'])
                <div
                    class="bg-themed-secondary rounded-lg shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                    <h2 class="text-xl font-bold text-themed-primary mb-6 transition-colors duration-300">Recent Activities
                    </h2>

                    <div class="space-y-4">
                        @forelse($this->recentActivities as $activity)
                            <div
                                class="flex items-start space-x-3 p-3 rounded-lg hover:bg-themed-tertiary transition-colors duration-200">
                                <div
                                    class="flex-shrink-0 w-8 h-8 bg-themed-tertiary rounded-full flex items-center justify-center transition-colors duration-300">
                                    <i class="{{ $activity['icon'] }} text-xs {{ $activity['color'] }}"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-themed-primary transition-colors duration-300">
                                        {{ $activity['title'] }}</p>
                                    <p class="text-xs text-themed-secondary transition-colors duration-300">
                                        {{ $activity['description'] }}</p>
                                    <p class="text-xs text-themed-tertiary mt-1 transition-colors duration-300">
                                        {{ $activity['timestamp']->diffForHumans() }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6">
                                <i class="fas fa-clock text-themed-tertiary text-3xl mb-2"></i>
                                <p class="text-themed-secondary transition-colors duration-300">No recent activities</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Get current theme for chart colors
            const isDark = document.documentElement.classList.contains('dark');

            // Chart.js default configuration for dark mode
            Chart.defaults.color = isDark ? '#D1D5DB' : '#374151';
            Chart.defaults.borderColor = isDark ? '#374151' : '#E5E7EB';
            Chart.defaults.backgroundColor = isDark ? '#1F2937' : '#FFFFFF';

            // Session Trends Chart
            const sessionData = @json($this->sessionAnalytics['session_trends']);
            if (window.bootkodeDashboardCharts?.shouldRender('sessionTrendsChart', sessionData, ['sessions', 'avg_rating'])) {
                const ctx = document.getElementById('sessionTrendsChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: sessionData.map(item => item.date),
                        datasets: [{
                            label: 'Sessions',
                            data: sessionData.map(item => item.sessions),
                            borderColor: isDark ? '#5EEAD4' : '#14B8A6',
                            backgroundColor: isDark ? 'rgba(94, 234, 212, 0.1)' : 'rgba(20, 184, 166, 0.1)',
                            fill: true,
                            tension: 0.4,
                        }, {
                            label: 'Avg Rating',
                            data: sessionData.map(item => item.avg_rating),
                            borderColor: isDark ? '#4ADE80' : '#22C55E',
                            backgroundColor: isDark ? 'rgba(74, 222, 128, 0.1)' : 'rgba(34, 197, 94, 0.1)',
                            fill: false,
                            tension: 0.4,
                            yAxisID: 'y1',
                        }]
                    },
                    options: {
                animation: false,
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
                            y1: {
                                type: 'linear',
                                display: true,
                                position: 'right',
                                min: 0,
                                max: 5,
                                ticks: {
                                    color: isDark ? '#9CA3AF' : '#6B7280'
                                },
                                grid: {
                                    drawOnChartArea: false,
                                },
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
            const observer = new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
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
        }, 300000); // 5 minutes
    </script>
@endpush
