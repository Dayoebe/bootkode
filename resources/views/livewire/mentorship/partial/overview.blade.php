<div>
    <!-- Welcome Section -->
    <div
        class="bg-themed-secondary rounded-2xl shadow-lg p-8 border border-themed-primary transition-colors duration-300">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-3xl font-bold text-themed-primary mb-2 transition-colors duration-300">Welcome Back,
                    {{ auth()->user()->name }}!</h2>
                <p class="text-lg text-themed-secondary transition-colors duration-300">Your mentorship impact dashboard
                </p>
            </div>
            <div class="flex space-x-4">
                @if($profileId)
                            <button wire:click="editProfile"
                                class="bg-accent-primary hover:bg-accent-secondary text-white px-6 py-3 rounded-lg transition-colors">
                                <i class="fas fa-edit mr-2"></i>Edit Profile
                            </button>
                            <button wire:click="toggleAvailability" class="px-6 py-3 rounded-lg transition-colors duration-300 {{ $isAvailable
                    ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 hover:bg-red-200 dark:hover:bg-red-900/50'
                    : 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 hover:bg-green-200 dark:hover:bg-green-900/50' 
                                    }}">
                                <i class="fas fa-{{ $isAvailable ? 'pause' : 'play' }} mr-2"></i>
                                {{ $isAvailable ? 'Go Unavailable' : 'Go Available' }}
                            </button>
                @else
                    <button wire:click="applyToBecomeMentor"
                        class="bg-accent-primary hover:bg-accent-secondary text-white px-6 py-3 rounded-lg transition-colors">
                        <i class="fas fa-user-plus mr-2"></i>Apply to Become Mentor
                    </button>
                @endif
            </div>
        </div>
        <!-- Performance Metrics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-br from-accent-primary to-accent-secondary rounded-xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">Sessions Conducted</p>
                        <p class="text-3xl font-bold">{{ $performanceMetrics['sessions_conducted'] ?? 0 }}</p>
                    </div>
                    <div class="text-3xl opacity-80">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                </div>
            </div>

            <div
                class="bg-gradient-to-br from-green-500 to-green-600 dark:from-green-600 dark:to-green-700 rounded-xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">Avg Session Rating</p>
                        <p class="text-3xl font-bold">
                            {{ number_format($performanceMetrics['average_session_rating'] ?? 0, 1) }}</p>
                    </div>
                    <div class="text-3xl opacity-80">
                        <i class="fas fa-star"></i>
                    </div>
                </div>
            </div>

            <div
                class="bg-gradient-to-br from-purple-500 to-purple-600 dark:from-purple-600 dark:to-purple-700 rounded-xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">Code Reviews</p>
                        <p class="text-3xl font-bold">{{ $performanceMetrics['code_reviews_completed'] ?? 0 }}</p>
                    </div>
                    <div class="text-3xl opacity-80">
                        <i class="fas fa-code"></i>
                    </div>
                </div>
            </div>

            <div
                class="bg-gradient-to-br from-orange-500 to-orange-600 dark:from-orange-600 dark:to-orange-700 rounded-xl p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm opacity-90">Response Time</p>
                        <p class="text-3xl font-bold">{{ $performanceMetrics['response_time_hours'] ?? 0 }}h</p>
                    </div>
                    <div class="text-3xl opacity-80">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Requests -->
        @if(count($pendingRequestsList) > 0)
            <div
                class="bg-yellow-100/50 dark:bg-yellow-900/30 border border-yellow-200/50 dark:border-yellow-800 rounded-xl p-6 mb-6 transition-colors duration-300">
                <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-300 mb-4 transition-colors duration-300">
                    <i class="fas fa-bell mr-2"></i>Pending Mentorship Requests ({{ count($pendingRequestsList) }})
                </h3>
                <div class="space-y-3">
                    @foreach($pendingRequestsList as $request)
                        <div
                            class="bg-themed-secondary rounded-lg p-4 flex items-center justify-between border border-yellow-200/50 dark:border-yellow-800 transition-colors duration-300">
                            <div class="flex-1">
                                <h4 class="font-semibold text-themed-primary transition-colors duration-300">
                                    {{ $request->mentee->name }}</h4>
                                <p class="text-sm text-themed-secondary transition-colors duration-300">
                                    {{ \Illuminate\Support\Str::limit($request->request_message, 100) }}</p>
                                <p class="text-xs text-themed-tertiary mt-1 transition-colors duration-300">
                                    {{ $request->requested_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex space-x-2">
                                <button wire:click="acceptMentorship({{ $request->id }})"
                                    class="bg-green-600 hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                    Accept
                                </button>
                                <button wire:click="rejectMentorship({{ $request->id }})"
                                    class="bg-red-600 hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm transition-colors">
                                    Reject
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Recent Activity Grid -->
    <div class="grid lg:grid-cols-2 gap-8 mt-8">
        <!-- Upcoming Sessions -->
        <div
            class="bg-themed-secondary rounded-2xl shadow-lg p-6 border border-themed-primary transition-colors duration-300">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-themed-primary transition-colors duration-300">Upcoming Sessions</h3>
                <button wire:click="$dispatch('change-tab', {tab: 'sessions'})"
                    class="text-accent-primary hover:text-accent-secondary transition-colors">
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>

            @if(count($upcomingSessionsList) > 0)
                <div class="space-y-4">
                    @foreach($upcomingSessionsList as $session)
                        <div
                            class="border border-themed-primary rounded-lg p-4 hover:bg-themed-tertiary transition-colors bg-themed-secondary">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-themed-primary transition-colors duration-300">
                                        {{ $session->title }}</h4>
                                    <p class="text-sm text-themed-secondary mt-1 transition-colors duration-300">with
                                        {{ $session->mentorship->mentee->name }}</p>
                                    <p class="text-xs text-accent-primary font-medium mt-2 transition-colors duration-300">
                                        {{ $session->scheduled_at->format('M j, Y g:i A') }}
                                    </p>
                                </div>
                                <div class="text-2xl text-accent-primary transition-colors duration-300">
                                    <i class="fas fa-{{ $session->type === 'code_review' ? 'code' : 'video' }}"></i>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-themed-secondary transition-colors duration-300">
                    <i class="fas fa-calendar-times text-4xl mb-3"></i>
                    <p>No upcoming sessions scheduled</p>
                </div>
            @endif
        </div>

        <!-- Recent Code Reviews -->
        <div
            class="bg-themed-secondary rounded-2xl shadow-lg p-6 border border-themed-primary transition-colors duration-300">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-themed-primary transition-colors duration-300">Recent Code Reviews
                </h3>
                <button wire:click="$dispatch('change-tab', {tab: 'code-reviews'})"
                    class="text-accent-primary hover:text-accent-secondary transition-colors">
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>

            @if(count($recentCodeReviews) > 0)
                <div class="space-y-4">
                    @foreach($recentCodeReviews as $review)
                        <div
                            class="border border-themed-primary rounded-lg p-4 hover:bg-themed-tertiary transition-colors bg-themed-secondary">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-themed-primary transition-colors duration-300">
                                        {{ $review->title }}</h4>
                                    <div class="flex items-center mt-2 space-x-4">
                                        <span
                                            class="text-xs bg-{{ $review->status === 'pending' ? 'yellow' : ($review->status === 'completed' ? 'green' : 'blue') }}-100 dark:bg-{{ $review->status === 'pending' ? 'yellow' : ($review->status === 'completed' ? 'green' : 'blue') }}-900/30 
                                                       text-{{ $review->status === 'pending' ? 'yellow' : ($review->status === 'completed' ? 'green' : 'blue') }}-800 dark:text-{{ $review->status === 'pending' ? 'yellow' : ($review->status === 'completed' ? 'green' : 'blue') }}-300 
                                                       px-2 py-1 rounded-full border border-{{ $review->status === 'pending' ? 'yellow' : ($review->status === 'completed' ? 'green' : 'blue') }}-200 dark:border-{{ $review->status === 'pending' ? 'yellow' : ($review->status === 'completed' ? 'green' : 'blue') }}-800 transition-colors duration-300">
                                            {{ ucfirst($review->status) }}
                                        </span>
                                        <span
                                            class="text-xs bg-themed-tertiary text-themed-secondary px-2 py-1 rounded-full border border-themed-primary transition-colors duration-300">
                                            {{ ucfirst($review->priority) }} Priority
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