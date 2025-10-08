<div>
    <div
        class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
        <!-- Header Section -->
        <div class="bg-white dark:bg-gray-800 shadow-lg border-b border-gray-200 dark:border-gray-700">
            <div class="container mx-auto px-6 py-8">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                    <div class="mb-6 lg:mb-0">
                        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">Mentor Dashboard</h1>
                        <p class="text-xl text-gray-600 dark:text-gray-300">Manage your mentorship activities and grow
                            your impact</p>
                    </div>

                    <!-- Quick Stats -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        <div
                            class="bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 rounded-xl p-4 text-white text-center">
                            <div class="text-2xl font-bold">{{ $activeMentees }}</div>
                            <div class="text-sm opacity-90">Active Mentees</div>
                        </div>
                        <div
                            class="bg-gradient-to-br from-green-500 to-green-600 dark:from-green-600 dark:to-green-700 rounded-xl p-4 text-white text-center">
                            <div class="text-2xl font-bold">{{ $totalSessions }}</div>
                            <div class="text-sm opacity-90">Total Sessions</div>
                        </div>
                        <div
                            class="bg-gradient-to-br from-orange-500 to-orange-600 dark:from-orange-600 dark:to-orange-700 rounded-xl p-4 text-white text-center">
                            <div class="text-2xl font-bold">{{ $upcomingSessions }}</div>
                            <div class="text-sm opacity-90">Upcoming</div>
                        </div>
                        <div
                            class="bg-gradient-to-br from-purple-500 to-purple-600 dark:from-purple-600 dark:to-purple-700 rounded-xl p-4 text-white text-center">
                            <div class="text-2xl font-bold">{{ number_format($averageRating, 1) }}</div>
                            <div class="text-sm opacity-90">Rating</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session('message'))
            <div class="container mx-auto px-6 pt-4">
                <div
                    class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg relative animate-fade-in">
                    <span class="block sm:inline">{{ session('message') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="container mx-auto px-6 pt-4">
                <div
                    class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg relative animate-fade-in">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <div class="px-6 py-8">
            <!-- Navigation Tabs -->
            <div class="mb-8">
                <nav class="flex flex-wrap space-x-2 bg-white dark:bg-gray-800 rounded-xl p-2 shadow-sm">
                    <button wire:click="setActiveTab('overview')"
                        class="px-6 py-3 rounded-lg font-semibold transition-all {{ $activeTab === 'overview' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-gray-700' }}">
                        <i class="fas fa-tachometer-alt mr-2"></i>Overview
                    </button>

                    <button wire:click="setActiveTab('mentorships')"
                        class="px-6 py-3 rounded-lg font-semibold transition-all {{ $activeTab === 'mentorships' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-gray-700' }}">
                        <i class="fas fa-users mr-2"></i>My Mentees
                    </button>

                    <button wire:click="setActiveTab('sessions')"
                        class="px-6 py-3 rounded-lg font-semibold transition-all {{ $activeTab === 'sessions' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-gray-700' }}">
                        <i class="fas fa-calendar-check mr-2"></i>Sessions
                    </button>

                    <button wire:click="setActiveTab('code-reviews')"
                        class="px-6 py-3 rounded-lg font-semibold transition-all {{ $activeTab === 'code-reviews' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-gray-700' }}">
                        <i class="fas fa-code mr-2"></i>Code Reviews
                    </button>

                    <button wire:click="setActiveTab('profile')"
                        class="px-6 py-3 rounded-lg font-semibold transition-all {{ $activeTab === 'profile' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-gray-700' }}">
                        <i class="fas fa-user-edit mr-2"></i>Profile
                    </button>

                    <button wire:click="setActiveTab('analytics')"
                        class="px-6 py-3 rounded-lg font-semibold transition-all {{ $activeTab === 'analytics' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-gray-700' }}">
                        <i class="fas fa-chart-line mr-2"></i>Analytics
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div class="space-y-8">
                {{-- Overview Tab --}}
                @if($activeTab === 'overview')
                    <!-- Welcome Section -->
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Welcome Back,
                                    {{ auth()->user()->name }}!</h2>
                                <p class="text-lg text-gray-600 dark:text-gray-300">Your mentorship impact dashboard</p>
                            </div>
                            <div class="flex space-x-4">
                                @if($profileId)
                                    <button wire:click="editProfile"
                                        class="bg-blue-600 dark:bg-blue-700 text-white px-6 py-3 rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors">
                                        <i class="fas fa-edit mr-2"></i>Edit Profile
                                    </button>
                                    <button wire:click="toggleAvailability"
                                        class="px-6 py-3 rounded-lg transition-colors {{ $isAvailable ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 hover:bg-red-200 dark:hover:bg-red-900/50' : 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 hover:bg-green-200 dark:hover:bg-green-900/50' }}">
                                        <i class="fas fa-{{ $isAvailable ? 'pause' : 'play' }} mr-2"></i>
                                        {{ $isAvailable ? 'Go Unavailable' : 'Go Available' }}
                                    </button>
                                @else
                                    <button wire:click="applyToBecomeMentor"
                                        class="bg-blue-600 dark:bg-blue-700 text-white px-6 py-3 rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors">
                                        <i class="fas fa-user-plus mr-2"></i>Apply to Become Mentor
                                    </button>
                                @endif
                            </div>
                        </div>

                        <!-- Performance Metrics Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                            <div
                                class="bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 rounded-xl p-6 text-white">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm opacity-90">Sessions Conducted</p>
                                        <p class="text-3xl font-bold">{{ $performanceMetrics['sessions_conducted'] ?? 0 }}
                                        </p>
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
                                        <p class="text-3xl font-bold">
                                            {{ $performanceMetrics['code_reviews_completed'] ?? 0 }}</p>
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
                                        <p class="text-3xl font-bold">{{ $performanceMetrics['response_time_hours'] ?? 0 }}h
                                        </p>
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
                                class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-xl p-6 mb-6">
                                <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-300 mb-4">
                                    <i class="fas fa-bell mr-2"></i>Pending Mentorship Requests
                                    ({{ count($pendingRequestsList) }})
                                </h3>
                                <div class="space-y-3">
                                    @foreach($pendingRequestsList as $request)
                                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 flex items-center justify-between">
                                            <div class="flex-1">
                                                <h4 class="font-semibold text-gray-900 dark:text-white">{{ $request->mentee->name }}
                                                </h4>
                                                <p class="text-sm text-gray-600 dark:text-gray-300">
                                                    {{ Str::limit($request->request_message, 100) }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                    {{ $request->requested_at->diffForHumans() }}</p>
                                            </div>
                                            <div class="flex space-x-2">
                                                <button wire:click="acceptMentorship({{ $request->id }})"
                                                    class="bg-green-600 dark:bg-green-700 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 dark:hover:bg-green-600 transition-colors">
                                                    Accept
                                                </button>
                                                <button wire:click="rejectMentorship({{ $request->id }})"
                                                    class="bg-red-600 dark:bg-red-700 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700 dark:hover:bg-red-600 transition-colors">
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
                    <div class="grid lg:grid-cols-2 gap-8">
                        <!-- Upcoming Sessions -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Upcoming Sessions</h3>
                                <button wire:click="setActiveTab('sessions')"
                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>

                            @if(count($upcomingSessionsList) > 0)
                                <div class="space-y-4">
                                    @foreach($upcomingSessionsList as $session)
                                        <div
                                            class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                            <div class="flex items-center justify-between">
                                                <div class="flex-1">
                                                    <h4 class="font-semibold text-gray-900 dark:text-white">{{ $session->title }}
                                                    </h4>
                                                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">with
                                                        {{ $session->mentorship->mentee->name }}</p>
                                                    <p class="text-xs text-blue-600 dark:text-blue-400 font-medium mt-2">
                                                        {{ $session->scheduled_at->format('M j, Y g:i A') }}
                                                    </p>
                                                </div>
                                                <div class="text-2xl text-blue-500 dark:text-blue-400">
                                                    <i class="fas fa-{{ $session->type === 'code_review' ? 'code' : 'video' }}"></i>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-calendar-times text-4xl mb-3"></i>
                                    <p>No upcoming sessions scheduled</p>
                                </div>
                            @endif
                        </div>

                        <!-- Recent Code Reviews -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Recent Code Reviews</h3>
                                <button wire:click="setActiveTab('code-reviews')"
                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>

                            @if(count($recentCodeReviews) > 0)
                                <div class="space-y-4">
                                    @foreach($recentCodeReviews as $review)
                                        <div
                                            class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                            <div class="flex items-center justify-between">
                                                <div class="flex-1">
                                                    <h4 class="font-semibold text-gray-900 dark:text-white">{{ $review->title }}
                                                    </h4>
                                                    <div class="flex items-center mt-2 space-x-4">
                                                        <span
                                                            class="text-xs bg-{{ $review->status === 'pending' ? 'yellow' : ($review->status === 'completed' ? 'green' : 'blue') }}-100 dark:bg-{{ $review->status === 'pending' ? 'yellow' : ($review->status === 'completed' ? 'green' : 'blue') }}-900/30 text-{{ $review->status === 'pending' ? 'yellow' : ($review->status === 'completed' ? 'green' : 'blue') }}-800 dark:text-{{ $review->status === 'pending' ? 'yellow' : ($review->status === 'completed' ? 'green' : 'blue') }}-300 px-2 py-1 rounded-full">
                                                            {{ ucfirst($review->status) }}
                                                        </span>
                                                        <span
                                                            class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 px-2 py-1 rounded-full">
                                                            {{ ucfirst($review->priority) }} Priority
                                                        </span>
                                                    </div>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
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
                                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-code text-4xl mb-3"></i>
                                    <p>No code reviews yet</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Mentorships Tab --}}
                @if($activeTab === 'mentorships')
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">My Mentees</h2>
                            <div class="flex items-center space-x-4">
                                <select wire:model.live="statusFilter"
                                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="">All Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="active">Active</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                        </div>

                        @if(count($mentorships) > 0)
                            <div class="grid gap-6">
                                @foreach($mentorships as $mentorship)
                                    <div
                                        class="border border-gray-200 dark:border-gray-700 rounded-lg p-6 hover:shadow-md transition-shadow">
                                        <div class="flex items-center justify-between mb-4">
                                            <div class="flex items-center space-x-4">
                                                <div
                                                    class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 dark:from-blue-600 dark:to-purple-700 rounded-full flex items-center justify-center text-white font-bold">
                                                    {{ substr($mentorship->mentee->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                                        {{ $mentorship->mentee->name }}</h3>
                                                    <p class="text-sm text-gray-600 dark:text-gray-300">
                                                        {{ $mentorship->mentee->email }}</p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <span
                                                    class="bg-{{ $mentorship->status_color }}-100 dark:bg-{{ $mentorship->status_color }}-900/30 text-{{ $mentorship->status_color }}-800 dark:text-{{ $mentorship->status_color }}-300 px-3 py-1 rounded-full text-sm">
                                                    {{ $mentorship->status_label }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <h4 class="font-medium text-gray-900 dark:text-white mb-2">Goals:</h4>
                                            <ul class="list-disc list-inside text-sm text-gray-600 dark:text-gray-300 space-y-1">
                                                @foreach($mentorship->goals ?? [] as $goal)
                                                    <li>{{ $goal }}</li>
                                                @endforeach
                                            </ul>
                                        </div>

                                        @if($mentorship->isActive())
                                            <div class="mb-4">
                                                <div class="flex justify-between items-center mb-2">
                                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Progress</span>
                                                    <span
                                                        class="text-sm text-gray-600 dark:text-gray-400">{{ $mentorship->progress_percentage }}%</span>
                                                </div>
                                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                                    <div class="bg-blue-600 dark:bg-blue-500 h-2 rounded-full"
                                                        style="width: {{ $mentorship->progress_percentage }}%"></div>
                                                </div>
                                            </div>
                                        @endif>

                                        <div class="flex items-center justify-between">
                                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                                <span>Duration: {{ $mentorship->duration_formatted }}</span>
                                                @if($mentorship->started_at)
                                                    <span class="mx-2">•</span>
                                                    <span>Started: {{ $mentorship->started_at->format('M j, Y') }}</span>
                                                @endif
                                            </div>
                                            <div class="flex space-x-2">
                                                @if($mentorship->isPending())
                                                    <button wire:click="acceptMentorship({{ $mentorship->id }})"
                                                        class="bg-green-600 dark:bg-green-700 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 dark:hover:bg-green-600 transition-colors">
                                                        Accept
                                                    </button>
                                                    <button wire:click="rejectMentorship({{ $mentorship->id }})"
                                                        class="bg-red-600 dark:bg-red-700 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700 dark:hover:bg-red-600 transition-colors">
                                                        Reject
                                                    </button>
                                                @elseif($mentorship->isActive())
                                                    <button wire:click="completeMentorship({{ $mentorship->id }})"
                                                        class="bg-blue-600 dark:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors">
                                                        Complete
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-16">
                                <div class="text-6xl text-gray-400 dark:text-gray-500 mb-4">
                                    <i class="fas fa-users"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">No mentees yet</h3>
                                <p class="text-gray-500 dark:text-gray-400">Your mentees will appear here once you accept
                                    requests</p>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Sessions Tab --}}
                @if($activeTab === 'sessions')
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Sessions Management</h2>
                            <div class="flex items-center space-x-4">
                                <select wire:model.live="dateFilter"
                                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="this_week">This Week</option>
                                    <option value="this_month">This Month</option>
                                    <option value="this_quarter">This Quarter</option>
                                </select>
                            </div>
                        </div>

                        @if(count($upcomingSessionsList) > 0)
                            <div class="grid gap-4">
                                @foreach($upcomingSessionsList as $session)
                                    <div
                                        class="border border-gray-200 dark:border-gray-700 rounded-lg p-6 hover:shadow-md transition-shadow">
                                        <div class="flex items-center justify-between mb-4">
                                            <div class="flex items-center space-x-4">
                                                <div
                                                    class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-{{ $session->type === 'code_review' ? 'code' : 'video' }}"></i>
                                                </div>
                                                <div>
                                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                                        {{ $session->title }}</h3>
                                                    <p class="text-sm text-gray-600 dark:text-gray-300">with
                                                        {{ $session->mentorship->mentee->name }}</p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-medium text-blue-600 dark:text-blue-400">
                                                    {{ $session->scheduled_at->format('M j, Y') }}</p>
                                                <p class="text-lg font-bold text-gray-900 dark:text-white">
                                                    {{ $session->scheduled_at->format('g:i A') }}</p>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <p class="text-sm text-gray-600 dark:text-gray-300">{{ $session->description }}</p>
                                        </div>

                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                                <span><i class="fas fa-clock mr-1"></i>{{ $session->duration_minutes ?? 60 }}
                                                    min</span>
                                                <span><i
                                                        class="fas fa-video mr-1"></i>{{ ucfirst($session->format ?? 'video') }}</span>
                                            </div>
                                            <div class="flex space-x-2">
                                                <button
                                                    class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg text-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                                    <i class="fas fa-eye mr-1"></i>View
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-16">
                                <div class="text-6xl text-gray-400 dark:text-gray-500 mb-4">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">No sessions yet</h3>
                                <p class="text-gray-500 dark:text-gray-400">Schedule your first session with your mentees</p>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Code Reviews Tab --}}
                @if($activeTab === 'code-reviews')
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Code Reviews</h2>
                        </div>

                        @if(count($recentCodeReviews) > 0)
                            <div class="grid gap-4">
                                @foreach($recentCodeReviews as $review)
                                    <div
                                        class="border border-gray-200 dark:border-gray-700 rounded-lg p-6 hover:shadow-md transition-shadow">
                                        <div class="flex items-center justify-between mb-4">
                                            <div class="flex items-center space-x-4">
                                                <div
                                                    class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-code-branch"></i>
                                                </div>
                                                <div>
                                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                                        {{ $review->title }}</h3>
                                                    <p class="text-sm text-gray-600 dark:text-gray-300">by
                                                        {{ $review->requester->name }}</p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <span
                                                    class="bg-{{ $review->status === 'pending' ? 'yellow' : ($review->status === 'completed' ? 'green' : 'blue') }}-100 dark:bg-{{ $review->status === 'pending' ? 'yellow' : ($review->status === 'completed' ? 'green' : 'blue') }}-900/30 text-{{ $review->status === 'pending' ? 'yellow' : ($review->status === 'completed' ? 'green' : 'blue') }}-800 dark:text-{{ $review->status === 'pending' ? 'yellow' : ($review->status === 'completed' ? 'green' : 'blue') }}-300 px-3 py-1 rounded-full text-sm">
                                                    {{ ucfirst($review->status) }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                                {{ Str::limit($review->description, 150) }}</p>
                                        </div>

                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                                <span><i class="fas fa-flag mr-1"></i>{{ ucfirst($review->priority) }}</span>
                                                <span><i
                                                        class="fas fa-clock mr-1"></i>{{ $review->requested_at->diffForHumans() }}</span>
                                            </div>
                                            <div class="flex space-x-2">
                                                @if($review->status === 'pending')
                                                    <button
                                                        class="bg-green-600 dark:bg-green-700 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 dark:hover:bg-green-600 transition-colors">
                                                        <i class="fas fa-play mr-1"></i>Start Review
                                                    </button>
                                                @elseif($review->status === 'in_review')
                                                    <button
                                                        class="bg-blue-600 dark:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors">
                                                        <i class="fas fa-check mr-1"></i>Complete
                                                    </button>
                                                @endif
                                                <button
                                                    class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-lg text-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                                                    <i class="fas fa-eye mr-1"></i>View
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-16">
                                <div class="text-6xl text-gray-400 dark:text-gray-500 mb-4">
                                    <i class="fas fa-code"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">No code reviews yet</h3>
                                <p class="text-gray-500 dark:text-gray-400">Code review requests will appear here</p>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Profile Tab --}}
                @if($activeTab === 'profile')
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Mentor Profile</h2>
                            @if(!$profileId)
                                <button wire:click="applyToBecomeMentor"
                                    class="bg-blue-600 dark:bg-blue-700 text-white px-6 py-3 rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors">
                                    <i class="fas fa-user-plus mr-2"></i>Apply to Become Mentor
                                </button>
                            @else
                                <button wire:click="editProfile"
                                    class="bg-blue-600 dark:bg-blue-700 text-white px-6 py-3 rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors">
                                    <i class="fas fa-edit mr-2"></i>Edit Profile
                                </button>
                            @endif
                        </div>

                        @if($profileId)
                            <div class="space-y-6">
                                <!-- Profile Summary -->
                                <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
                                    <div class="flex items-center space-x-6">
                                        <div
                                            class="w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-600 dark:from-blue-600 dark:to-purple-700 rounded-full flex items-center justify-center text-white text-3xl font-bold">
                                            {{ substr(auth()->user()->name, 0, 1) }}
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">
                                                {{ auth()->user()->name }}</h3>
                                            <p class="text-lg text-gray-600 dark:text-gray-300">
                                                {{ auth()->user()->mentorProfile->experience_label ?? 'Mentor' }}</p>
                                            <div class="flex items-center mt-2">
                                                <div class="text-yellow-400 mr-2">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i
                                                            class="fas fa-star {{ $i <= floor($averageRating) ? '' : 'text-gray-300 dark:text-gray-600' }}"></i>
                                                    @endfor
                                                </div>
                                                <span class="text-sm text-gray-600 dark:text-gray-400">({{ $totalReviews }}
                                                    reviews)</span>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                                {{ $activeMentees }}/{{ auth()->user()->mentorProfile->max_mentees ?? 5 }}</div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">Active Mentees</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Profile Details -->
                                <div class="grid md:grid-cols-2 gap-6">
                                    <div>
                                        <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Specializations</h4>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($specializations ?? [] as $spec)
                                                @if($spec)
                                                    <span
                                                        class="bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-3 py-1 rounded-full text-sm">{{ $spec }}</span>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>

                                    <div>
                                        <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Skills</h4>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($skills ?? [] as $skill)
                                                @if($skill)
                                                    <span
                                                        class="bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 px-3 py-1 rounded-full text-sm">{{ $skill }}</span>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="md:col-span-2">
                                        <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Bio</h4>
                                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                                            {{ $bio ?: 'No bio provided yet. Click "Edit Profile" to add your bio.' }}</p>
                                    </div>

                                    <div>
                                        <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Experience</h4>
                                        <p class="text-gray-700 dark:text-gray-300">{{ $yearsExperience }}+ years •
                                            {{ ucfirst($experienceLevel) }} Level</p>
                                    </div>

                                    <div>
                                        <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Availability</h4>
                                        <p class="text-gray-700 dark:text-gray-300">
                                            @if($isAvailable)
                                                <span class="text-green-600 dark:text-green-400"><i
                                                        class="fas fa-check-circle mr-1"></i>Available for new mentees</span>
                                            @else
                                                <span class="text-red-600 dark:text-red-400"><i
                                                        class="fas fa-pause-circle mr-1"></i>Currently unavailable</span>
                                            @endif
                                        </p>
                                    </div>

                                    <div>
                                        <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Pricing</h4>
                                        <p class="text-gray-700 dark:text-gray-300">
                                            @if($hourlyRate > 0)
                                                ${{ number_format($hourlyRate, 2) }}/hour
                                            @else
                                                Free mentoring
                                            @endif
                                            @if($offersFreeSessions)
                                                <span class="text-green-600 dark:text-green-400 text-sm ml-2">• Offers free
                                                    sessions</span>
                                            @endif
                                        </p>
                                    </div>

                                    <div>
                                        <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Contact Links</h4>
                                        <div class="flex space-x-4">
                                            @if($linkedinProfile)
                                                <a href="{{ $linkedinProfile }}" target="_blank"
                                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                                    <i class="fab fa-linkedin text-xl"></i>
                                                </a>
                                            @endif
                                            @if($githubProfile)
                                                <a href="{{ $githubProfile }}" target="_blank"
                                                    class="text-gray-900 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-400">
                                                    <i class="fab fa-github text-xl"></i>
                                                </a>
                                            @endif
                                            @if($portfolioUrl)
                                                <a href="{{ $portfolioUrl }}" target="_blank"
                                                    class="text-purple-600 dark:text-purple-400 hover:text-purple-800 dark:hover:text-purple-300">
                                                    <i class="fas fa-globe text-xl"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Mentoring Approach -->
                                @if($mentoringApproach)
                                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                                        <h4 class="font-semibold text-gray-900 dark:text-white mb-3">Mentoring Approach</h4>
                                        <p class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ $mentoringApproach }}</p>
                                    </div>
                                @endif

                                <!-- Recent Reviews -->
                                @if(count($recentReviews) > 0)
                                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                                        <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Recent Reviews</h4>
                                        <div class="space-y-4">
                                            @foreach($recentReviews as $review)
                                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                                                    <div class="flex items-center justify-between mb-2">
                                                        <div class="flex items-center space-x-3">
                                                            <div
                                                                class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 dark:from-blue-600 dark:to-purple-700 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                                                {{ substr($review->reviewer->name, 0, 1) }}
                                                            </div>
                                                            <div>
                                                                <p class="font-medium text-gray-900 dark:text-white">
                                                                    {{ $review->reviewer->name }}</p>
                                                                <div class="flex items-center">
                                                                    <div class="text-yellow-400 text-sm mr-2">
                                                                        @for($i = 1; $i <= 5; $i++)
                                                                            <i
                                                                                class="fas fa-star {{ $i <= floor($review->overall_rating) ? '' : 'text-gray-300 dark:text-gray-600' }}"></i>
                                                                        @endfor
                                                                    </div>
                                                                    <span
                                                                        class="text-xs text-gray-500 dark:text-gray-400">{{ $review->created_at->format('M j, Y') }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <p class="text-sm text-gray-700 dark:text-gray-300">
                                                        {{ Str::limit($review->review_text, 150) }}</p>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-16">
                                <div class="text-6xl text-gray-400 dark:text-gray-500 mb-4">
                                    <i class="fas fa-user-plus"></i>
                                </div>
                                <h3 class="text-xl font-semibold text-gray-700 dark:text-gray-300 mb-2">No mentor profile yet
                                </h3>
                                <p class="text-gray-500 dark:text-gray-400 mb-6">Create your mentor profile to start accepting
                                    mentees</p>
                                <button wire:click="applyToBecomeMentor"
                                    class="bg-blue-600 dark:bg-blue-700 text-white px-6 py-3 rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors">
                                    <i class="fas fa-user-plus mr-2"></i>Apply to Become Mentor
                                </button>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Analytics Tab --}}
                @if($activeTab === 'analytics')
                    <div class="space-y-8">
                        <!-- Analytics Header -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Performance Analytics</h2>
                                <div class="flex items-center space-x-4">
                                    <select wire:model.live="dateFilter"
                                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        <option value="this_week">This Week</option>
                                        <option value="this_month">This Month</option>
                                        <option value="this_quarter">This Quarter</option>
                                        <option value="this_year">This Year</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Key Metrics -->
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                <div
                                    class="bg-gradient-to-br from-blue-500 to-blue-600 dark:from-blue-600 dark:to-blue-700 rounded-xl p-6 text-white">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm opacity-90">Sessions This Period</p>
                                            <p class="text-3xl font-bold">
                                                {{ $performanceMetrics['sessions_conducted'] ?? 0 }}</p>
                                            <p class="text-xs opacity-75 mt-1">
                                                {{ ucfirst(str_replace('_', ' ', $dateFilter)) }}</p>
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
                                            <p class="text-sm opacity-90">Average Rating</p>
                                            <p class="text-3xl font-bold">
                                                {{ number_format($performanceMetrics['average_session_rating'] ?? 0, 1) }}
                                            </p>
                                            <p class="text-xs opacity-75 mt-1">Session feedback</p>
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
                                            <p class="text-3xl font-bold">
                                                {{ $performanceMetrics['code_reviews_completed'] ?? 0 }}</p>
                                            <p class="text-xs opacity-75 mt-1">Completed reviews</p>
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
                                            <p class="text-3xl font-bold">
                                                {{ $performanceMetrics['response_time_hours'] ?? 0 }}h</p>
                                            <p class="text-xs opacity-75 mt-1">Average response</p>
                                        </div>
                                        <div class="text-3xl opacity-80">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Analytics Charts -->
                        <div class="grid lg:grid-cols-2 gap-8">
                            <!-- Earnings Chart -->
                            @if($hourlyRate > 0)
                                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Earnings Overview</h3>
                                    <div class="space-y-4">
                                        <div
                                            class="flex items-center justify-between p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                            <div>
                                                <p class="text-sm text-green-600 dark:text-green-400 font-medium">This Month</p>
                                                <p class="text-2xl font-bold text-green-800 dark:text-green-300">
                                                    ${{ number_format($monthlyEarnings, 2) }}</p>
                                            </div>
                                            <div class="text-green-600 dark:text-green-400">
                                                <i class="fas fa-dollar-sign text-2xl"></i>
                                            </div>
                                        </div>
                                        <div
                                            class="flex items-center justify-between p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                            <div>
                                                <p class="text-sm text-blue-600 dark:text-blue-400 font-medium">Total Earnings
                                                </p>
                                                <p class="text-2xl font-bold text-blue-800 dark:text-blue-300">
                                                    ${{ number_format($totalEarnings, 2) }}</p>
                                            </div>
                                            <div class="text-blue-600 dark:text-blue-400">
                                                <i class="fas fa-chart-line text-2xl"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Session Stats -->
                            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Session Statistics</h3>
                                <div class="space-y-4">
                                    <div
                                        class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                        <div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">Completed
                                                Sessions</p>
                                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                                {{ $completedSessions }}</p>
                                        </div>
                                        <div class="text-gray-600 dark:text-gray-400">
                                            <i class="fas fa-check-circle text-2xl"></i>
                                        </div>
                                    </div>
                                    <div
                                        class="flex items-center justify-between p-4 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                                        <div>
                                            <p class="text-sm text-orange-600 dark:text-orange-400 font-medium">Upcoming
                                                Sessions</p>
                                            <p class="text-2xl font-bold text-orange-800 dark:text-orange-300">
                                                {{ $upcomingSessions }}</p>
                                        </div>
                                        <div class="text-orange-600 dark:text-orange-400">
                                            <i class="fas fa-calendar-alt text-2xl"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Detailed Analytics -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Detailed Insights</h3>
                            <div class="grid md:grid-cols-3 gap-6">
                                <div class="text-center">
                                    <div
                                        class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-users text-2xl"></i>
                                    </div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white">Total Mentees</h4>
                                    <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $totalMentees }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">All time</p>
                                </div>
                                <div class="text-center">
                                    <div
                                        class="w-16 h-16 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-star text-2xl"></i>
                                    </div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white">Average Rating</h4>
                                    <p class="text-3xl font-bold text-green-600 dark:text-green-400">
                                        {{ number_format($averageRating, 1) }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $totalReviews }} reviews</p>
                                </div>
                                <div class="text-center">
                                    <div
                                        class="w-16 h-16 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-code text-2xl"></i>
                                    </div>
                                    <h4 class="font-semibold text-gray-900 dark:text-white">Code Reviews</h4>
                                    <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">
                                        {{ $pendingCodeReviews }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Pending</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Profile Edit Modal -->
        @if($showProfileModal)
            <div class="fixed inset-0 bg-gray-600 dark:bg-gray-900 bg-opacity-50 dark:bg-opacity-75 overflow-y-auto h-full w-full z-50"
                x-data="{ show: @entangle('showProfileModal') }" x-show="show">
                <div
                    class="relative top-20 mx-auto p-5 border border-gray-200 dark:border-gray-700 w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white dark:bg-gray-800">
                    <div class="mt-3">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Mentor Profile</h3>
                            <button wire:click="closeModal"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>

                        <form wire:submit.prevent="saveProfile" class="space-y-6">
                            <!-- Bio -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bio</label>
                                <textarea wire:model="bio" rows="4"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                    placeholder="Tell mentees about yourself, your experience, and what you can help them with..."></textarea>
                                @error('bio') <span class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Specializations -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Specializations</label>
                                @foreach($specializations as $index => $specialization)
                                    <div class="flex items-center space-x-2 mb-2">
                                        <input type="text" wire:model="specializations.{{ $index }}"
                                            class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                            placeholder="e.g., Web Development, Data Science">
                                        @if(count($specializations) > 1)
                                            <button type="button" wire:click="removeSpecialization({{ $index }})"
                                                class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                                <button type="button" wire:click="addSpecialization"
                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm">
                                    <i class="fas fa-plus mr-1"></i>Add Specialization
                                </button>
                                @error('specializations') <span
                                class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Skills -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Skills</label>
                                @foreach($skills as $index => $skill)
                                    <div class="flex items-center space-x-2 mb-2">
                                        <input type="text" wire:model="skills.{{ $index }}"
                                            class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                            placeholder="e.g., JavaScript, Python, React">
                                        @if(count($skills) > 1)
                                            <button type="button" wire:click="removeSkill({{ $index }})"
                                                class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                                <button type="button" wire:click="addSkill"
                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm">
                                    <i class="fas fa-plus mr-1"></i>Add Skill
                                </button>
                            </div>

                            <!-- Experience Level and Years -->
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Experience
                                        Level</label>
                                    <select wire:model="experienceLevel"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        <option value="">Select Level</option>
                                        <option value="junior">Junior</option>
                                        <option value="mid">Mid Level</option>
                                        <option value="senior">Senior</option>
                                        <option value="expert">Expert</option>
                                    </select>
                                    @error('experienceLevel') <span
                                    class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Years of
                                        Experience</label>
                                    <input type="number" wire:model="yearsExperience" min="0" max="50"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    @error('yearsExperience') <span
                                    class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Mentoring Settings -->
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Maximum
                                        Mentees</label>
                                    <input type="number" wire:model="maxMentees" min="1" max="20"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    @error('maxMentees') <span
                                    class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Hourly
                                        Rate (USD)</label>
                                    <input type="number" wire:model="hourlyRate" min="0" max="500" step="0.01"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                        placeholder="0 for free mentoring">
                                </div>
                            </div>

                            <!-- Timezone -->
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Timezone</label>
                                <select wire:model="timezone"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="UTC">UTC</option>
                                    <option value="America/New_York">Eastern Time</option>
                                    <option value="America/Chicago">Central Time</option>
                                    <option value="America/Denver">Mountain Time</option>
                                    <option value="America/Los_Angeles">Pacific Time</option>
                                    <option value="Europe/London">London</option>
                                    <option value="Europe/Paris">Paris</option>
                                    <option value="Asia/Tokyo">Tokyo</option>
                                    <option value="Asia/Shanghai">Shanghai</option>
                                    <option value="Australia/Sydney">Sydney</option>
                                </select>
                                @error('timezone') <span
                                class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Mentoring Approach -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Mentoring
                                    Approach</label>
                                <textarea wire:model="mentoringApproach" rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                    placeholder="Describe your mentoring style and approach..."></textarea>
                                @error('mentoringApproach') <span
                                class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Communication Preferences -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Communication
                                    Preferences</label>
                                <div class="grid grid-cols-2 gap-3">
                                    @foreach(['Video Calls', 'Voice Calls', 'Text Chat', 'Email', 'Screen Sharing', 'Code Reviews'] as $preference)
                                        <label class="flex items-center">
                                            <input type="checkbox"
                                                wire:click="toggleCommunicationPreference('{{ $preference }}')" {{ in_array($preference, $communicationPreferences) ? 'checked' : '' }}
                                                class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 bg-white dark:bg-gray-700">
                                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $preference }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Social Links -->
                            <div class="grid md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">LinkedIn
                                        Profile</label>
                                    <input type="url" wire:model="linkedinProfile"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                        placeholder="https://linkedin.com/in/...">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">GitHub
                                        Profile</label>
                                    <input type="url" wire:model="githubProfile"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                        placeholder="https://github.com/...">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Portfolio
                                        URL</label>
                                    <input type="url" wire:model="portfolioUrl"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                        placeholder="https://yourportfolio.com">
                                </div>
                            </div>

                            <!-- Availability Settings -->
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="offersFreeSessions"
                                            class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 bg-white dark:bg-gray-700">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Offers Free
                                            Sessions</span>
                                    </label>
                                </div>
                                <div>
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="isAvailable"
                                            class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 bg-white dark:bg-gray-700">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Available for New
                                            Mentees</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <button type="button" wire:click="closeModal"
                                    class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    Cancel
                                </button>
                                <button type="submit"
                                    class="px-6 py-2 bg-blue-600 dark:bg-blue-700 text-white rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors">
                                    <i class="fas fa-save mr-2"></i>Save Profile
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <!-- Mentor Application Modal -->
        @if($showApplicationModal)
            <div class="fixed inset-0 bg-gray-600 dark:bg-gray-900 bg-opacity-50 dark:bg-opacity-75 overflow-y-auto h-full w-full z-50"
                x-data="{ show: @entangle('showApplicationModal') }" x-show="show">
                <div
                    class="relative top-10 mx-auto p-5 border border-gray-200 dark:border-gray-700 w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-md bg-white dark:bg-gray-800">
                    <div class="mt-3">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Apply to Become a Mentor</h3>
                            <button wire:click="closeModal"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>

                        <form wire:submit.prevent="submitMentorApplication" class="space-y-6">
                            <!-- Motivation -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Why do you want to become a mentor? <span class="text-red-500">*</span>
                                </label>
                                <textarea wire:model="motivation" rows="4"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                    placeholder="Share your motivation for mentoring others..."></textarea>
                                @error('motivation') <span
                                class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Teaching Philosophy -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Teaching Philosophy <span class="text-red-500">*</span>
                                </label>
                                <textarea wire:model="teachingPhilosophy" rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                    placeholder="Describe your approach to teaching and mentoring..."></textarea>
                                @error('teachingPhilosophy') <span
                                class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Experience Details -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Professional
                                    Experience</label>
                                @foreach($experienceDetails as $index => $experience)
                                    <div class="flex items-center space-x-2 mb-2">
                                        <input type="text" wire:model="experienceDetails.{{ $index }}"
                                            class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                            placeholder="e.g., Senior Developer at TechCorp (2020-2023)">
                                        @if(count($experienceDetails) > 1)
                                            <button type="button" wire:click="removeExperienceDetail({{ $index }})"
                                                class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                                <button type="button" wire:click="addExperienceDetail"
                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm">
                                    <i class="fas fa-plus mr-1"></i>Add Experience
                                </button>
                            </div>

                            <!-- Expected Mentees and Rate -->
                            <div class="grid md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Expected Number of Mentees <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" wire:model="expectedMentees" min="1" max="10"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    @error('expectedMentees') <span
                                    class="text-red-500 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Proposed
                                        Hourly Rate (USD)</label>
                                    <input type="number" wire:model="proposedHourlyRate" min="0" max="500" step="0.01"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                        placeholder="0 for free mentoring">
                                </div>
                            </div>

                            <!-- References -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">References
                                    (Optional)</label>
                                @foreach($references as $index => $reference)
                                    <div class="flex items-center space-x-2 mb-2">
                                        <input type="text" wire:model="references.{{ $index }}"
                                            class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                            placeholder="Name, Title, Contact Info">
                                        @if(count($references) > 1)
                                            <button type="button" wire:click="removeReference({{ $index }})"
                                                class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                                <button type="button" wire:click="addReference"
                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm">
                                    <i class="fas fa-plus mr-1"></i>Add Reference
                                </button>
                            </div>

                            <!-- Additional Info -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Additional
                                    Information</label>
                                <textarea wire:model="additionalInfo" rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                    placeholder="Any additional information you'd like to share..."></textarea>
                            </div>

                            <!-- Document Upload -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Supporting
                                    Documents (Optional)</label>
                                <input type="file" wire:model="documents" multiple accept=".pdf,.doc,.docx"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Upload resume, certifications, or
                                    other relevant documents (PDF, DOC, DOCX)</p>
                            </div>

                            <!-- Form Actions -->
                            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <button type="button" wire:click="closeModal"
                                    class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    Cancel
                                </button>
                                <button type="submit"
                                    class="px-6 py-2 bg-blue-600 dark:bg-blue-700 text-white rounded-lg hover:bg-blue-700 dark:hover:bg-blue-600 transition-colors">
                                    <i class="fas fa-paper-plane mr-2"></i>Submit Application
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Loading Overlay -->
    <div wire:loading
        class="fixed inset-0 bg-gray-600 dark:bg-gray-900 bg-opacity-50 dark:bg-opacity-75 z-50 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-xl">
            <div class="flex items-center space-x-4">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 dark:border-blue-400"></div>
                <span class="text-lg font-medium text-gray-900 dark:text-white">Processing...</span>
            </div>
        </div>
    </div>

    <style>
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

        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }
    </style>

    <script>
        document.addEventListener('livewire:init', () => {
            // Auto-refresh dashboard every 5 minutes
            setInterval(() => {
                if (document.visibilityState === 'visible') {
                    Livewire.dispatch('refreshDashboard');
                }
            }, 300000);

            // Handle modal close on escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    Livewire.dispatch('closeModal');
                }
            });
        });
    </script>
</div>