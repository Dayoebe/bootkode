<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    <!-- Header Section -->
    <div class="bg-white shadow-lg border-b border-gray-200">
        <div class="container mx-auto px-6 py-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="mb-6 lg:mb-0">
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">Mentor Dashboard</h1>
                    <p class="text-xl text-gray-600">Manage your mentorship activities and grow your impact</p>
                </div>

                <!-- Quick Stats -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 text-white text-center">
                        <div class="text-2xl font-bold">{{ $activeMentees }}</div>
                        <div class="text-sm opacity-90">Active Mentees</div>
                    </div>
                    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 text-white text-center">
                        <div class="text-2xl font-bold">{{ $totalSessions }}</div>
                        <div class="text-sm opacity-90">Total Sessions</div>
                    </div>
                    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-4 text-white text-center">
                        <div class="text-2xl font-bold">{{ $upcomingSessions }}</div>
                        <div class="text-sm opacity-90">Upcoming</div>
                    </div>
                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 text-white text-center">
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
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg relative animate-fade-in">
                <span class="block sm:inline">{{ session('message') }}</span>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="container mx-auto px-6 pt-4">
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg relative animate-fade-in">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <div class="container mx-auto px-6 py-8">
        <!-- Navigation Tabs -->
        <div class="mb-8">
            <nav class="flex flex-wrap space-x-2 bg-white rounded-xl p-2 shadow-sm">
                <button wire:click="setActiveTab('overview')"
                    class="px-6 py-3 rounded-lg font-semibold transition-all {{ $activeTab === 'overview' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                    <i class="fas fa-tachometer-alt mr-2"></i>Overview
                </button>

                <button wire:click="setActiveTab('mentorships')"
                    class="px-6 py-3 rounded-lg font-semibold transition-all {{ $activeTab === 'mentorships' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                    <i class="fas fa-users mr-2"></i>My Mentees
                </button>

                <button wire:click="setActiveTab('sessions')"
                    class="px-6 py-3 rounded-lg font-semibold transition-all {{ $activeTab === 'sessions' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                    <i class="fas fa-calendar-check mr-2"></i>Sessions
                </button>

                <button wire:click="setActiveTab('code-reviews')"
                    class="px-6 py-3 rounded-lg font-semibold transition-all {{ $activeTab === 'code-reviews' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                    <i class="fas fa-code mr-2"></i>Code Reviews
                </button>

                <button wire:click="setActiveTab('profile')"
                    class="px-6 py-3 rounded-lg font-semibold transition-all {{ $activeTab === 'profile' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                    <i class="fas fa-user-edit mr-2"></i>Profile
                </button>

                <button wire:click="setActiveTab('analytics')"
                    class="px-6 py-3 rounded-lg font-semibold transition-all {{ $activeTab === 'analytics' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                    <i class="fas fa-chart-line mr-2"></i>Analytics
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="space-y-8">
            {{-- Overview Tab --}}
            @if($activeTab === 'overview')
                <!-- Welcome Section -->
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-3xl font-bold text-gray-900 mb-2">Welcome Back, {{ auth()->user()->name }}!</h2>
                            <p class="text-lg text-gray-600">Your mentorship impact dashboard</p>
                        </div>
                        <div class="flex space-x-4">
                            @if($profileId)
                                <button wire:click="editProfile" 
                                    class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-edit mr-2"></i>Edit Profile
                                </button>
                                <button wire:click="toggleAvailability" 
                                    class="px-6 py-3 rounded-lg transition-colors {{ $isAvailable ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                                    <i class="fas fa-{{ $isAvailable ? 'pause' : 'play' }} mr-2"></i>
                                    {{ $isAvailable ? 'Go Unavailable' : 'Go Available' }}
                                </button>
                            @else
                                <button wire:click="applyToBecomeMentor"
                                    class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-user-plus mr-2"></i>Apply to Become Mentor
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Performance Metrics Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white">
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

                        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm opacity-90">Avg Session Rating</p>
                                    <p class="text-3xl font-bold">{{ number_format($performanceMetrics['average_session_rating'] ?? 0, 1) }}</p>
                                </div>
                                <div class="text-3xl opacity-80">
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white">
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

                        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-6 text-white">
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
                        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 mb-6">
                            <h3 class="text-lg font-semibold text-yellow-800 mb-4">
                                <i class="fas fa-bell mr-2"></i>Pending Mentorship Requests ({{ count($pendingRequestsList) }})
                            </h3>
                            <div class="space-y-3">
                                @foreach($pendingRequestsList as $request)
                                    <div class="bg-white rounded-lg p-4 flex items-center justify-between">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-900">{{ $request->mentee->name }}</h4>
                                            <p class="text-sm text-gray-600">{{ Str::limit($request->request_message, 100) }}</p>
                                            <p class="text-xs text-gray-500 mt-1">{{ $request->requested_at->diffForHumans() }}</p>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button wire:click="acceptMentorship({{ $request->id }})"
                                                class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 transition-colors">
                                                Accept
                                            </button>
                                            <button wire:click="rejectMentorship({{ $request->id }})"
                                                class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700 transition-colors">
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
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-900">Upcoming Sessions</h3>
                            <button wire:click="setActiveTab('sessions')" class="text-blue-600 hover:text-blue-700">
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>

                        @if(count($upcomingSessionsList) > 0)
                            <div class="space-y-4">
                                @foreach($upcomingSessionsList as $session)
                                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <h4 class="font-semibold text-gray-900">{{ $session->title }}</h4>
                                                <p class="text-sm text-gray-600 mt-1">with {{ $session->mentorship->mentee->name }}</p>
                                                <p class="text-xs text-blue-600 font-medium mt-2">
                                                    {{ $session->scheduled_at->format('M j, Y g:i A') }}
                                                </p>
                                            </div>
                                            <div class="text-2xl text-blue-500">
                                                <i class="fas fa-{{ $session->type === 'code_review' ? 'code' : 'video' }}"></i>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-calendar-times text-4xl mb-3"></i>
                                <p>No upcoming sessions scheduled</p>
                            </div>
                        @endif
                    </div>

                    <!-- Recent Code Reviews -->
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-900">Recent Code Reviews</h3>
                            <button wire:click="setActiveTab('code-reviews')" class="text-blue-600 hover:text-blue-700">
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>

                        @if(count($recentCodeReviews) > 0)
                            <div class="space-y-4">
                                @foreach($recentCodeReviews as $review)
                                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <h4 class="font-semibold text-gray-900">{{ $review->title }}</h4>
                                                <div class="flex items-center mt-2 space-x-4">
                                                    <span class="text-xs bg-{{ $review->status === 'pending' ? 'yellow' : ($review->status === 'completed' ? 'green' : 'blue') }}-100 text-{{ $review->status === 'pending' ? 'yellow' : ($review->status === 'completed' ? 'green' : 'blue') }}-800 px-2 py-1 rounded-full">
                                                        {{ ucfirst($review->status) }}
                                                    </span>
                                                    <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                                                        {{ ucfirst($review->priority) }} Priority
                                                    </span>
                                                </div>
                                                <p class="text-xs text-gray-500 mt-2">
                                                    {{ $review->requested_at->diffForHumans() }}
                                                </p>
                                            </div>
                                            <div class="text-2xl text-purple-500">
                                                <i class="fas fa-code-branch"></i>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <i class="fas fa-code text-4xl mb-3"></i>
                                <p>No code reviews yet</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Mentorships Tab --}}
            @if($activeTab === 'mentorships')
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">My Mentees</h2>
                        <div class="flex items-center space-x-4">
                            <select wire:model.live="statusFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
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
                                <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">
                                                {{ substr($mentorship->mentee->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900">{{ $mentorship->mentee->name }}</h3>
                                                <p class="text-sm text-gray-600">{{ $mentorship->mentee->email }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="bg-{{ $mentorship->status_color }}-100 text-{{ $mentorship->status_color }}-800 px-3 py-1 rounded-full text-sm">
                                                {{ $mentorship->status_label }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <h4 class="font-medium text-gray-900 mb-2">Goals:</h4>
                                        <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                                            @foreach($mentorship->goals ?? [] as $goal)
                                                <li>{{ $goal }}</li>
                                            @endforeach
                                        </ul>
                                    </div>

                                    @if($mentorship->isActive())
                                        <div class="mb-4">
                                            <div class="flex justify-between items-center mb-2">
                                                <span class="text-sm font-medium text-gray-700">Progress</span>
                                                <span class="text-sm text-gray-600">{{ $mentorship->progress_percentage }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-200 rounded-full h-2">
                                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $mentorship->progress_percentage }}%"></div>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="flex items-center justify-between">
                                        <div class="text-sm text-gray-600">
                                            <span>Duration: {{ $mentorship->duration_formatted }}</span>
                                            @if($mentorship->started_at)
                                                <span class="mx-2">•</span>
                                                <span>Started: {{ $mentorship->started_at->format('M j, Y') }}</span>
                                            @endif
                                        </div>
                                        <div class="flex space-x-2">
                                            @if($mentorship->isPending())
                                                <button wire:click="acceptMentorship({{ $mentorship->id }})"
                                                    class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 transition-colors">
                                                    Accept
                                                </button>
                                                <button wire:click="rejectMentorship({{ $mentorship->id }})"
                                                    class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700 transition-colors">
                                                    Reject
                                                </button>
                                            @elseif($mentorship->isActive())
                                                <button wire:click="completeMentorship({{ $mentorship->id }})"
                                                    class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition-colors">
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
                            <div class="text-6xl text-gray-400 mb-4">
                                <i class="fas fa-users"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-700 mb-2">No mentees yet</h3>
                            <p class="text-gray-500">Your mentees will appear here once you accept requests</p>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Sessions Tab --}}
            @if($activeTab === 'sessions')
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Sessions Management</h2>
                        <div class="flex items-center space-x-4">
                            <select wire:model.live="dateFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="this_week">This Week</option>
                                <option value="this_month">This Month</option>
                                <option value="this_quarter">This Quarter</option>
                            </select>
                        </div>
                    </div>

                    @if(count($upcomingSessionsList) > 0)
                        <div class="grid gap-4">
                            @foreach($upcomingSessionsList as $session)
                                <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                                                <i class="fas fa-{{ $session->type === 'code_review' ? 'code' : 'video' }}"></i>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900">{{ $session->title }}</h3>
                                                <p class="text-sm text-gray-600">with {{ $session->mentorship->mentee->name }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-medium text-blue-600">{{ $session->scheduled_at->format('M j, Y') }}</p>
                                            <p class="text-lg font-bold text-gray-900">{{ $session->scheduled_at->format('g:i A') }}</p>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <p class="text-sm text-gray-600">{{ $session->description }}</p>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                                            <span><i class="fas fa-clock mr-1"></i>{{ $session->duration_minutes ?? 60 }} min</span>
                                            <span><i class="fas fa-tag mr-1"></i>{{ ucfirst($session->type) }}</span>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition-colors">
                                                <i class="fas fa-edit mr-1"></i>Edit
                                            </button>
                                            <button class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 transition-colors">
                                                <i class="fas fa-check mr-1"></i>Start
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-16">
                            <div class="text-6xl text-gray-400 mb-4">
                                <i class="fas fa-calendar-plus"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-700 mb-2">No upcoming sessions</h3>
                            <p class="text-gray-500">Schedule sessions with your mentees to get started</p>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Code Reviews Tab --}}
            @if($activeTab === 'code-reviews')
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Code Reviews</h2>
                        <div class="flex items-center space-x-4">
                            @if($pendingCodeReviews > 0)
                                <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm">
                                    {{ $pendingCodeReviews }} Pending
                                </span>
                            @endif
                        </div>
                    </div>

                    @if(count($recentCodeReviews) > 0)
                        <div class="grid gap-4">
                            @foreach($recentCodeReviews as $review)
                                <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center">
                                                <i class="fas fa-code-branch"></i>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-semibold text-gray-900">{{ $review->title }}</h3>
                                                <p class="text-sm text-gray-600">by {{ $review->requester->name }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="bg-{{ $review->status === 'pending' ? 'yellow' : ($review->status === 'completed' ? 'green' : 'blue') }}-100 text-{{ $review->status === 'pending' ? 'yellow' : ($review->status === 'completed' ? 'green' : 'blue') }}-800 px-3 py-1 rounded-full text-sm">
                                                {{ ucfirst($review->status) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <p class="text-sm text-gray-600 mb-2">{{ $review->description }}</p>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($review->technologies ?? [] as $tech)
                                                <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs">{{ $tech }}</span>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                                            <span><i class="fas fa-flag mr-1"></i>{{ ucfirst($review->priority) }}</span>
                                            <span><i class="fas fa-clock mr-1"></i>{{ $review->requested_at->diffForHumans() }}</span>
                                        </div>
                                        <div class="flex space-x-2">
                                            @if($review->status === 'pending')
                                                <button class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-green-700 transition-colors">
                                                    <i class="fas fa-play mr-1"></i>Start Review
                                                </button>
                                            @elseif($review->status === 'in_review')
                                                <button class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition-colors">
                                                    <i class="fas fa-check mr-1"></i>Complete
                                                </button>
                                            @endif
                                            <button class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-200 transition-colors">
                                                <i class="fas fa-eye mr-1"></i>View
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-16">
                            <div class="text-6xl text-gray-400 mb-4">
                                <i class="fas fa-code"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-700 mb-2">No code reviews yet</h3>
                            <p class="text-gray-500">Code review requests will appear here</p>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Profile Tab --}}
            @if($activeTab === 'profile')
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">Mentor Profile</h2>
                        @if(!$profileId)
                            <button wire:click="applyToBecomeMentor"
                                class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-user-plus mr-2"></i>Apply to Become Mentor
                            </button>
                        @else
                            <button wire:click="editProfile"
                                class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                                <i class="fas fa-edit mr-2"></i>Edit Profile
                            </button>
                        @endif
                    </div>

                    @if($profileId)
                        <div class="space-y-6">
                            <!-- Profile Summary -->
                            <div class="border-b border-gray-200 pb-6">
                                <div class="flex items-center space-x-6">
                                    <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-3xl font-bold">
                                        {{ substr(auth()->user()->name, 0, 1) }}
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-2xl font-bold text-gray-900">{{ auth()->user()->name }}</h3>
                                        <p class="text-lg text-gray-600">{{ auth()->user()->mentorProfile->experience_label ?? 'Mentor' }}</p>
                                        <div class="flex items-center mt-2">
                                            <div class="text-yellow-400 mr-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= floor($averageRating) ? '' : 'text-gray-300' }}"></i>
                                                @endfor
                                            </div>
                                            <span class="text-sm text-gray-600">({{ $totalReviews }} reviews)</span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-2xl font-bold text-gray-900">{{ $activeMentees }}/{{ auth()->user()->mentorProfile->max_mentees ?? 5 }}</div>
                                        <div class="text-sm text-gray-600">Active Mentees</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Profile Details -->
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-3">Specializations</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($specializations ?? [] as $spec)
                                            @if($spec)
                                                <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-sm">{{ $spec }}</span>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>

                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-3">Skills</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($skills ?? [] as $skill)
                                            @if($skill)
                                                <span class="bg-green-50 text-green-700 px-3 py-1 rounded-full text-sm">{{ $skill }}</span>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>

                                <div class="md:col-span-2">
                                    <h4 class="font-semibold text-gray-900 mb-3">Bio</h4>
                                    <p class="text-gray-700 leading-relaxed">{{ $bio ?: 'No bio provided yet. Click "Edit Profile" to add your bio.' }}</p>
                                </div>

                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-3">Experience</h4>
                                    <p class="text-gray-700">{{ $yearsExperience }}+ years • {{ ucfirst($experienceLevel) }} Level</p>
                                </div>

                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-3">Availability</h4>
                                    <p class="text-gray-700">
                                        @if($isAvailable)
                                            <span class="text-green-600"><i class="fas fa-check-circle mr-1"></i>Available for new mentees</span>
                                        @else
                                            <span class="text-red-600"><i class="fas fa-pause-circle mr-1"></i>Currently unavailable</span>
                                        @endif
                                    </p>
                                </div>

                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-3">Pricing</h4>
                                    <p class="text-gray-700">
                                        @if($hourlyRate > 0)
                                            ${{ number_format($hourlyRate, 2) }}/hour
                                        @else
                                            Free mentoring
                                        @endif
                                        @if($offersFreeSessions)
                                            <span class="text-green-600 text-sm ml-2">• Offers free sessions</span>
                                        @endif
                                    </p>
                                </div>

                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-3">Contact Links</h4>
                                    <div class="flex space-x-4">
                                        @if($linkedinProfile)
                                            <a href="{{ $linkedinProfile }}" target="_blank" class="text-blue-600 hover:text-blue-800">