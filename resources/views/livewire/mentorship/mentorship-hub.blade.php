<div class="space-y-6">
    <x-ui.page-header title="Mentorship" description="Follow your learning relationships, sessions, and code reviews." />
    <x-ui.flash />
    <dl class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        @foreach(['Active mentorships' => $activeMentorships, 'Completed sessions' => $totalSessions, 'Upcoming sessions' => $upcomingSessions, 'Pending code reviews' => $pendingCodeReviews] as $label => $value)
            <div class="rounded-lg border border-line bg-surface p-4"><dt class="text-sm text-content-muted">{{ $label }}</dt><dd class="mt-1 text-xl font-semibold text-content">{{ $value }}</dd></div>
        @endforeach
    </dl>
    <div class="space-y-6">
        <!-- Quick Actions -->
        <div class="grid md:grid-cols-3 gap-6 mb-8">
            @if(auth()->user()->isStudent())
                <a href="{{ route('mentorship.find') }}"
                   class="rounded-lg border border-line bg-surface p-6 text-content shadow-sm transition-colors hover:border-brand focus-visible:outline-brand">
                    <i class="fas fa-search text-2xl mb-3" aria-hidden="true"></i>
                    <h3 class="text-lg font-semibold">Find New Mentor</h3>
                    <p class="text-sm text-content-muted">Discover expert mentors in your field</p>
                </a>

                <a href="{{ route('mentorship.sessions') }}"
                   class="rounded-lg border border-line bg-surface p-6 text-content shadow-sm transition-colors hover:border-brand focus-visible:outline-brand">
                    <i class="fas fa-calendar-plus text-2xl mb-3" aria-hidden="true"></i>
                    <h3 class="text-lg font-semibold">Schedule Session</h3>
                    <p class="text-sm text-content-muted">Book time with your mentors</p>
                </a>

                <a href="{{ route('mentorship.code-reviews') }}"
                   class="rounded-lg border border-line bg-surface p-6 text-content shadow-sm transition-colors hover:border-brand focus-visible:outline-brand">
                    <i class="fas fa-code text-2xl mb-3" aria-hidden="true"></i>
                    <h3 class="text-lg font-semibold">Request Review</h3>
                    <p class="text-sm text-content-muted">Get your code professionally reviewed</p>
                </a>
            @endif

            @if(auth()->user()->isMentor())
                <a href="{{ route('mentorship.dashboard') }}"
                   class="rounded-lg border border-line bg-surface p-6 text-content shadow-sm transition-colors hover:border-brand focus-visible:outline-brand">
                    <i class="fas fa-chalkboard-teacher text-2xl mb-3" aria-hidden="true"></i>
                    <h3 class="text-lg font-semibold">Mentor Dashboard</h3>
                    <p class="text-sm text-content-muted">Manage your mentees and sessions</p>
                </a>

                <a href="{{ route('mentorship.sessions') }}"
                   class="rounded-lg border border-line bg-surface p-6 text-content shadow-sm transition-colors hover:border-brand focus-visible:outline-brand">
                    <i class="fas fa-calendar text-2xl mb-3" aria-hidden="true"></i>
                    <h3 class="text-lg font-semibold">My Sessions</h3>
                    <p class="text-sm text-content-muted">View and manage your sessions</p>
                </a>

                <a href="{{ route('mentorship.code-reviews') }}"
                   class="rounded-lg border border-line bg-surface p-6 text-content shadow-sm transition-colors hover:border-brand focus-visible:outline-brand">
                    <i class="fas fa-code text-2xl mb-3" aria-hidden="true"></i>
                    <h3 class="text-lg font-semibold">Code Reviews</h3>
                    <p class="text-sm text-content-muted">Pending code reviews</p>
                </a>
            @endif
        </div>

        <!-- Main Content Grid -->
        <div class="grid lg:grid-cols-2 gap-8">
            <!-- My Active Mentorships -->
            <div class="bg-themed-secondary rounded-lg shadow-sm p-6 border border-themed-primary transition-colors duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-themed-primary transition-colors duration-300">My Active Mentorships</h3>
                    <a href="{{ route('mentorship.my-mentorships') }}" class="text-brand hover:text-brand transition-colors">
                        <i class="fas fa-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>

                @if(count($myActiveMentorships) > 0)
                    <div class="space-y-4">
                        @foreach($myActiveMentorships as $mentorship)
                            <div class="border border-themed-primary rounded-lg p-4 hover:bg-themed-tertiary transition-colors bg-themed-secondary">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-themed-primary transition-colors duration-300">
                                            @if(auth()->user()->isStudent())
                                                {{ $mentorship->mentor->name }}
                                            @else
                                                {{ $mentorship->mentee->name }}
                                            @endif
                                        </h4>
                                        <p class="text-sm text-themed-secondary mt-1 transition-colors duration-300">
                                            {{ $mentorship->started_at ? 'Started '.$mentorship->started_at->diffForHumans() : 'Start date not recorded' }}
                                        </p>
                                        @if($mentorship->duration_weeks)
                                            <div class="mt-2">
                                                <div class="w-full bg-themed-tertiary rounded-full h-2">
                                                    <div class="bg-brand h-2 rounded-full transition-colors duration-300"
                                                         style="width: {{ $mentorship->progress_percentage }}%"></div>
                                                </div>
                                                <p class="text-xs text-themed-tertiary mt-1 transition-colors duration-300">
                                                    {{ $mentorship->progress_percentage }}% complete
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-2xl text-brand transition-colors duration-300">
                                        <i class="fas fa-handshake" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-themed-secondary transition-colors duration-300">
                        <i class="fas fa-handshake text-4xl mb-3" aria-hidden="true"></i>
                        <p>No active mentorships</p>
                    </div>
                @endif
            </div>

            <!-- Upcoming Sessions -->
            <div class="bg-themed-secondary rounded-lg shadow-sm p-6 border border-themed-primary transition-colors duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-themed-primary transition-colors duration-300">Upcoming Sessions</h3>
                    <a href="{{ route('mentorship.sessions') }}" class="text-brand hover:text-brand transition-colors">
                        <i class="fas fa-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>

                @if(count($upcomingSessionsList) > 0)
                    <div class="space-y-4">
                        @foreach($upcomingSessionsList as $session)
                            <div class="border border-themed-primary rounded-lg p-4 hover:bg-themed-tertiary transition-colors bg-themed-secondary">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-themed-primary transition-colors duration-300">{{ $session->title }}</h4>
                                        <p class="text-sm text-themed-secondary mt-1 transition-colors duration-300">
                                            @if(auth()->user()->isStudent())
                                                with {{ $session->mentorship->mentor->name }}
                                            @else
                                                with {{ $session->mentorship->mentee->name }}
                                            @endif
                                        </p>
                                        <p class="text-xs text-brand font-medium mt-2 transition-colors duration-300">
                                            {{ $session->scheduled_at->format('M j, Y g:i A') }}
                                        </p>
                                    </div>
                                    <div class="text-2xl transition-colors duration-300"
                                         :class="{'text-purple-500 dark:text-purple-400': '{{ $session->type === 'code_review' }}', 'text-blue-500 dark:text-blue-400': '{{ $session->type !== 'code_review' }}'}">
                                        <i class="fas fa-{{ $session->type === 'code_review' ? 'code' : 'video' }}" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-themed-secondary transition-colors duration-300">
                        <i class="fas fa-calendar-times text-4xl mb-3" aria-hidden="true"></i>
                        <p>No upcoming sessions</p>
                    </div>
                @endif
            </div>

            <!-- Recent Code Reviews -->
            <div class="bg-themed-secondary rounded-lg shadow-sm p-6 border border-themed-primary transition-colors duration-300 lg:col-span-2">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-themed-primary transition-colors duration-300">Recent Code Reviews</h3>
                    <a href="{{ route('mentorship.code-reviews') }}" class="text-brand hover:text-brand transition-colors">
                        <i class="fas fa-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>

                @if(count($recentCodeReviews) > 0)
                    <div class="grid md:grid-cols-2 gap-4">
                        @foreach($recentCodeReviews as $review)
                            <div class="border border-themed-primary rounded-lg p-4 hover:bg-themed-tertiary transition-colors bg-themed-secondary">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-themed-primary transition-colors duration-300">{{ $review->title }}</h4>
                                        <div class="flex items-center mt-2 space-x-4">
                                            <span class="text-xs px-2 py-1 rounded-full border transition-colors duration-300"
                                                  :class="{
                                                    'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300 border-yellow-200 dark:border-yellow-800': '{{ $review->status === 'pending' }}',
                                                    'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 border-green-200 dark:border-green-800': '{{ $review->status === 'completed' }}',
                                                    'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 border-blue-200 dark:border-blue-800': '{{ $review->status !== 'pending' && $review->status !== 'completed' }}'
                                                  }">
                                                {{ ucfirst($review->status) }}
                                            </span>
                                            <span class="text-xs bg-themed-tertiary text-themed-secondary px-2 py-1 rounded-full border border-themed-primary transition-colors duration-300">
                                                {{ ucfirst($review->priority) }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-themed-tertiary mt-2 transition-colors duration-300">
                                            {{ $review->requested_at?->diffForHumans() ?? 'Request date not recorded' }}
                                        </p>
                                    </div>
                                    <div class="text-2xl text-brand dark:text-brand">
                                        <i class="fas fa-code-branch" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-themed-secondary transition-colors duration-300">
                        <i class="fas fa-code text-4xl mb-3" aria-hidden="true"></i>
                        <p>No code reviews yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>
