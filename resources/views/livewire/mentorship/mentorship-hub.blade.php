<div class="min-h-screen bg-themed-primary transition-colors duration-300">
    <!-- Header Section -->
    <div class="bg-themed-secondary dark:shadow-lg border-b border-themed-primary transition-colors duration-300">
        <div class="px-6 py-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="mb-6 lg:mb-0">
                    <h1 class="text-4xl font-bold text-themed-primary mb-2 transition-colors duration-300">Mentorship Dashboard</h1>
                    <p class="text-xl text-themed-secondary transition-colors duration-300">Connect, Learn, and Grow with Expert Mentors</p>
                </div>

                <!-- Quick Stats Dashboard -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 rounded-xl p-4 text-white text-center">
                        <div class="text-2xl font-bold">{{ $activeMentorships }}</div>
                        <div class="text-sm opacity-90">Active Mentorships</div>
                    </div>

                    <div class="bg-gradient-to-br from-green-500 to-green-600 dark:from-green-600 dark:to-green-700 rounded-xl p-4 text-white text-center">
                        <div class="text-2xl font-bold">{{ $totalSessions }}</div>
                        <div class="text-sm opacity-90">Total Sessions</div>
                    </div>

                    <div class="bg-gradient-to-br from-orange-500 to-orange-600 dark:from-orange-600 dark:to-orange-700 rounded-xl p-4 text-white text-center">
                        <div class="text-2xl font-bold">{{ $upcomingSessions }}</div>
                        <div class="text-sm opacity-90">Upcoming</div>
                    </div>

                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 dark:from-purple-600 dark:to-purple-700 rounded-xl p-4 text-white text-center">
                        <div class="text-2xl font-bold">{{ $pendingCodeReviews }}</div>
                        <div class="text-sm opacity-90">Code Reviews</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session('message'))
        <div class="container mx-auto px-6 pt-4">
            <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg relative animate-fade-in">
                <span class="block sm:inline">{{ session('message') }}</span>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="container mx-auto px-6 pt-4">
            <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg relative animate-fade-in">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <div class="px-6 py-8">
        <!-- Quick Actions -->
        <div class="grid md:grid-cols-3 gap-6 mb-8">
            @if(auth()->user()->isStudent())
                <a href="{{ route('mentorship.find') }}" 
                   class="bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-700 dark:to-indigo-700 text-white p-6 rounded-xl hover:from-blue-700 hover:to-indigo-700 dark:hover:from-blue-800 dark:hover:to-indigo-800 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-search text-2xl mb-3"></i>
                    <h3 class="text-lg font-semibold">Find New Mentor</h3>
                    <p class="text-sm opacity-90">Discover expert mentors in your field</p>
                </a>

                <a href="{{ route('mentorship.sessions') }}"
                   class="bg-gradient-to-r from-green-600 to-emerald-600 dark:from-green-700 dark:to-emerald-700 text-white p-6 rounded-xl hover:from-green-700 hover:to-emerald-700 dark:hover:from-green-800 dark:hover:to-emerald-800 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-calendar-plus text-2xl mb-3"></i>
                    <h3 class="text-lg font-semibold">Schedule Session</h3>
                    <p class="text-sm opacity-90">Book time with your mentors</p>
                </a>

                <a href="{{ route('mentorship.code-reviews') }}"
                   class="bg-gradient-to-r from-purple-600 to-pink-600 dark:from-purple-700 dark:to-pink-700 text-white p-6 rounded-xl hover:from-purple-700 hover:to-pink-700 dark:hover:from-purple-800 dark:hover:to-pink-800 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-code text-2xl mb-3"></i>
                    <h3 class="text-lg font-semibold">Request Review</h3>
                    <p class="text-sm opacity-90">Get your code professionally reviewed</p>
                </a>
            @endif

            @if(auth()->user()->isMentor())
                <a href="{{ route('mentorship.dashboard') }}"
                   class="bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-700 dark:to-indigo-700 text-white p-6 rounded-xl hover:from-blue-700 hover:to-indigo-700 dark:hover:from-blue-800 dark:hover:to-indigo-800 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-chalkboard-teacher text-2xl mb-3"></i>
                    <h3 class="text-lg font-semibold">Mentor Dashboard</h3>
                    <p class="text-sm opacity-90">Manage your mentees and sessions</p>
                </a>

                <a href="{{ route('mentorship.sessions') }}"
                   class="bg-gradient-to-r from-green-600 to-emerald-600 dark:from-green-700 dark:to-emerald-700 text-white p-6 rounded-xl hover:from-green-700 hover:to-emerald-700 dark:hover:from-green-800 dark:hover:to-emerald-800 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-calendar text-2xl mb-3"></i>
                    <h3 class="text-lg font-semibold">My Sessions</h3>
                    <p class="text-sm opacity-90">View and manage your sessions</p>
                </a>

                <a href="{{ route('mentorship.code-reviews') }}"
                   class="bg-gradient-to-r from-purple-600 to-pink-600 dark:from-purple-700 dark:to-pink-700 text-white p-6 rounded-xl hover:from-purple-700 hover:to-pink-700 dark:hover:from-purple-800 dark:hover:to-pink-800 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                    <i class="fas fa-code text-2xl mb-3"></i>
                    <h3 class="text-lg font-semibold">Code Reviews</h3>
                    <p class="text-sm opacity-90">Pending code reviews</p>
                </a>
            @endif
        </div>

        <!-- Main Content Grid -->
        <div class="grid lg:grid-cols-2 gap-8">
            <!-- My Active Mentorships -->
            <div class="bg-themed-secondary rounded-2xl shadow-lg p-6 border border-themed-primary transition-colors duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-themed-primary transition-colors duration-300">My Active Mentorships</h3>
                    <a href="{{ route('mentorship.my-mentorships') }}" class="text-accent-primary hover:text-accent-secondary transition-colors">
                        <i class="fas fa-arrow-right"></i>
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
                                            Started {{ $mentorship->started_at->diffForHumans() }}
                                        </p>
                                        @if($mentorship->duration_weeks)
                                            <div class="mt-2">
                                                <div class="w-full bg-themed-tertiary rounded-full h-2">
                                                    <div class="bg-accent-primary h-2 rounded-full transition-colors duration-300" 
                                                         style="width: {{ $mentorship->progress_percentage }}%"></div>
                                                </div>
                                                <p class="text-xs text-themed-tertiary mt-1 transition-colors duration-300">
                                                    {{ $mentorship->progress_percentage }}% complete
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-2xl text-accent-primary transition-colors duration-300">
                                        <i class="fas fa-handshake"></i>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-themed-secondary transition-colors duration-300">
                        <i class="fas fa-handshake text-4xl mb-3"></i>
                        <p>No active mentorships</p>
                    </div>
                @endif
            </div>

            <!-- Upcoming Sessions -->
            <div class="bg-themed-secondary rounded-2xl shadow-lg p-6 border border-themed-primary transition-colors duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-themed-primary transition-colors duration-300">Upcoming Sessions</h3>
                    <a href="{{ route('mentorship.sessions') }}" class="text-accent-primary hover:text-accent-secondary transition-colors">
                        <i class="fas fa-arrow-right"></i>
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
                                        <p class="text-xs text-accent-primary font-medium mt-2 transition-colors duration-300">
                                            {{ $session->scheduled_at->format('M j, Y g:i A') }}
                                        </p>
                                    </div>
                                    <div class="text-2xl transition-colors duration-300"
                                         :class="{'text-purple-500 dark:text-purple-400': '{{ $session->type === 'code_review' }}', 'text-blue-500 dark:text-blue-400': '{{ $session->type !== 'code_review' }}'}">
                                        <i class="fas fa-{{ $session->type === 'code_review' ? 'code' : 'video' }}"></i>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-themed-secondary transition-colors duration-300">
                        <i class="fas fa-calendar-times text-4xl mb-3"></i>
                        <p>No upcoming sessions</p>
                    </div>
                @endif
            </div>

            <!-- Recent Code Reviews -->
            <div class="bg-themed-secondary rounded-2xl shadow-lg p-6 border border-themed-primary transition-colors duration-300 lg:col-span-2">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-themed-primary transition-colors duration-300">Recent Code Reviews</h3>
                    <a href="{{ route('mentorship.code-reviews') }}" class="text-accent-primary hover:text-accent-secondary transition-colors">
                        <i class="fas fa-arrow-right"></i>
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
                                            {{ $review->requested_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    <div class="text-2xl text-purple-500 dark:text-purple-400">
                                        <i class="fas fa-code-branch"></i>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-themed-secondary transition-colors duration-300">
                        <i class="fas fa-code text-4xl mb-3"></i>
                        <p>No code reviews yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }

        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</div>