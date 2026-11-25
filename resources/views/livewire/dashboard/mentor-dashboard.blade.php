@assets
    @vite('resources/js/charts.js')
@endassets

@php
    $dashboardFirstName = str(auth()->user()->name)->before(' ')->toString();
    $dashboardStats = $this->overviewStats;
@endphp

<div class="bk-dashboard-home space-y-6">
    <x-dashboard.home-hero
        role="Mentor workspace"
        :title="'Guide progress that matters, '.$dashboardFirstName.'.'"
        description="Keep mentees moving with timely sessions, thoughtful feedback, and a clear view of every active mentorship."
        icon="fas fa-people-arrows"
        :focus-title="$dashboardStats['pending_requests'] > 0 ? $dashboardStats['pending_requests'].' mentorship request'.($dashboardStats['pending_requests'] === 1 ? '' : 's').' waiting' : ($dashboardStats['upcoming_sessions'] > 0 ? $dashboardStats['upcoming_sessions'].' upcoming session'.($dashboardStats['upcoming_sessions'] === 1 ? '' : 's') : 'No urgent mentoring action')"
        :focus-description="$dashboardStats['pending_requests'] > 0 ? 'Review the request and decide whether the mentorship is a good fit.' : 'Open the mentoring workspace to plan sessions and review learner work.'"
    >
        <x-slot:actions>
            <a href="{{ route('mentorship.dashboard') }}" class="bk-dashboard-hero-primary" wire:navigate>Open mentoring hub <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i></a>
            <a href="{{ route('mentorship.sessions') }}" class="bk-dashboard-hero-secondary" wire:navigate>Manage sessions</a>
        </x-slot:actions>
        <x-slot:focusAction><a href="{{ route('mentorship.dashboard') }}" class="bk-dashboard-hero-secondary" wire:navigate>Review mentoring activity</a></x-slot:focusAction>
        <x-slot:highlights>
            <x-dashboard.hero-stat label="Active mentees" :value="$dashboardStats['active_mentees']" :context="$dashboardStats['total_mentees'].' total relationships'" icon="fas fa-user-group" />
            <x-dashboard.hero-stat label="Upcoming" :value="$dashboardStats['upcoming_sessions']" context="scheduled sessions" icon="fas fa-calendar-check" />
            <x-dashboard.hero-stat label="Recorded hours" :value="$dashboardStats['mentorship_hours'].'h'" :context="$dashboardStats['completed_sessions'].' completed sessions'" icon="fas fa-clock" />
        </x-slot:highlights>
        <x-slot:tools>
            <span class="bk-dashboard-toolbar-label">View</span>
            <select aria-label="Filter mentees" wire:model.live="selectedMenteeFilter" class="bk-input w-full sm:w-auto">
                <option value="all">All mentees</option>
                <option value="active">Active mentees</option>
                <option value="new">New mentees</option>
                <option value="struggling">Needs attention</option>
            </select>
            <div role="group" aria-label="Reporting period" class="bk-dashboard-filter-group">
                @foreach(['7days' => '7d', '30days' => '30d', '90days' => '90d'] as $value => $label)
                    <button type="button" wire:click="updateTimeframe('{{ $value }}')" aria-label="Report for {{ $label }}" aria-pressed="{{ $selectedTimeframe === $value ? 'true' : 'false' }}" class="bk-dashboard-filter-button {{ $selectedTimeframe === $value ? 'is-active' : '' }}">{{ $label }}</button>
                @endforeach
            </div>
        </x-slot:tools>
    </x-dashboard.home-hero>

    <x-dashboard.workspace-guide role="mentor" />

    <!-- Overview Stats Grid -->
    @if($showWidgets['overview_stats'])
        <div class="bk-dashboard-metric-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
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
                        <i aria-hidden="true" class="fas fa-users text-brand dark:text-brand text-xl"></i>
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
                        <i aria-hidden="true" class="fas fa-comments text-brand dark:text-brand text-xl"></i>
                    </div>
                </div>
            </div>

            <div
                class="bg-themed-secondary rounded-lg shadow-sm border border-themed-primary p-6   transition-all duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-themed-secondary font-medium transition-colors duration-300">Recorded Outcomes</p>
                        <h3 class="text-3xl font-bold text-themed-primary mt-1 transition-colors duration-300">
                            {{ $this->overviewStats['success_rate'] }}%</h3>
                        <div class="flex items-center mt-2 text-sm">
                            @if($this->overviewStats['avg_session_rating'] > 0)
                                <i aria-hidden="true" class="fas fa-star text-yellow-400 dark:text-yellow-300 mr-2"></i>
                                <span class="text-themed-tertiary transition-colors duration-300">{{ number_format($this->overviewStats['avg_session_rating'], 1) }} mentor rating</span>
                            @else
                                <span class="text-themed-tertiary transition-colors duration-300">No mentor reviews yet</span>
                            @endif
                        </div>
                    </div>
                    <div class="bg-yellow-100 dark:bg-yellow-900/30 p-3 rounded-full transition-colors duration-300">
                        <i aria-hidden="true" class="fas fa-trophy text-yellow-600 dark:text-yellow-400 text-xl"></i>
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
                        <i aria-hidden="true" class="fas fa-briefcase text-brand dark:text-brand text-xl"></i>
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
                            View All <i aria-hidden="true" class="fas fa-arrow-right ml-1"></i>
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
                                                <i aria-hidden="true" class="fas fa-star text-yellow-400 dark:text-yellow-300 mr-1"></i>
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
                                <i aria-hidden="true" class="fas fa-user-plus text-themed-tertiary text-4xl mb-4"></i>
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
                                @forelse($this->sessionAnalytics['session_types'] as $type => $percentage)
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
                                @empty
                                    <p class="text-sm text-themed-secondary">No completed sessions in this period.</p>
                                @endforelse
                            </div>

                            <!-- Success Metrics -->
                            <div class="mt-6 pt-4 border-t border-themed-primary transition-colors duration-300">
                                <h4 class="text-sm font-semibold text-themed-primary mb-3 transition-colors duration-300">
                                    Recorded progress</h4>
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
                                        <div class="text-xs text-themed-secondary transition-colors duration-300">Completed
                                            Relationships</div>
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
                                <i aria-hidden="true" class="fas fa-chart-line text-themed-tertiary text-3xl mb-2"></i>
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
                    <h2 class="text-xl font-bold text-themed-primary mb-2 transition-colors duration-300">Recorded This Month</h2>
                    <p class="text-sm text-themed-secondary mb-5">Actual activity linked to your mentorship relationships.</p>

                    <dl class="grid grid-cols-3 gap-3">
                        <div class="rounded-lg bg-themed-tertiary p-3 text-center">
                            <dt class="text-xs text-themed-secondary">Sessions</dt>
                            <dd class="mt-1 text-2xl font-bold text-themed-primary">{{ $this->mentorshipGoals['recorded_this_month']['sessions_completed'] }}</dd>
                        </div>
                        <div class="rounded-lg bg-themed-tertiary p-3 text-center">
                            <dt class="text-xs text-themed-secondary">Certificates</dt>
                            <dd class="mt-1 text-2xl font-bold text-themed-primary">{{ $this->mentorshipGoals['recorded_this_month']['mentee_certifications'] }}</dd>
                        </div>
                        <div class="rounded-lg bg-themed-tertiary p-3 text-center">
                            <dt class="text-xs text-themed-secondary">Hired</dt>
                            <dd class="mt-1 text-2xl font-bold text-themed-primary">{{ $this->mentorshipGoals['recorded_this_month']['job_placements'] }}</dd>
                        </div>
                    </dl>
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
                            View All <i aria-hidden="true" class="fas fa-arrow-right ml-1"></i>
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
                                <i aria-hidden="true" class="fas fa-microphone-alt text-themed-tertiary text-3xl mb-2"></i>
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
                            {{ $this->overviewStats['open_support_tickets'] }} open
                        </span>
                    </div>

                    <div class="space-y-3 max-h-80 overflow-y-auto">
                        @forelse($this->supportTickets as $ticket)
                            <div
                                class="flex items-center space-x-3 p-3 border border-themed-primary rounded-lg transition-colors duration-300">
                                <div
                                    class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center {{ $ticket['priority'] === 'high' ? 'bg-red-100 dark:bg-red-900/30' : ($ticket['priority'] === 'medium' ? 'bg-yellow-100 dark:bg-yellow-900/30' : 'bg-green-100 dark:bg-green-900/30') }} transition-colors duration-300">
                                    <i aria-hidden="true"
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
                                <i aria-hidden="true" class="fas fa-check-circle text-green-400 dark:text-green-500 text-3xl mb-2"></i>
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
                                    <i aria-hidden="true" class="{{ $activity['icon'] }} text-xs {{ $activity['color'] }}"></i>
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
                                <i aria-hidden="true" class="fas fa-clock text-themed-tertiary text-3xl mb-2"></i>
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
