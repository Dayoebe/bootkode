<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    <!-- Header Section -->
    <div class="bg-white shadow-lg border-b border-gray-200">
        <div class="container mx-auto px-6 py-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="mb-6 lg:mb-0">
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">Mentorship Network</h1>
                    <p class="text-xl text-gray-600">Connect, Learn, and Grow with Expert Mentors</p>
                </div>

                <!-- Quick Stats Dashboard -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 text-white text-center">
                        <div class="text-2xl font-bold">{{ $activeMentorships }}</div>
                        <div class="text-sm opacity-90">Active Mentorships</div>
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

    <div class=" px-6 py-8">
        <!-- Navigation Tabs -->
        <div class="mb-8">
            <nav class="flex flex-wrap space-x-2 bg-white rounded-xl p-2 shadow-sm">
                <button wire:click="setActiveTab('dashboard')"
                    class="px-6 py-3 rounded-lg font-semibold transition-all {{ $activeTab === 'dashboard' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                    <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                </button>

                @if(auth()->user()->isStudent())
                    <button wire:click="setActiveTab('find-mentor')"
                        class="px-6 py-3 rounded-lg font-semibold transition-all {{ $activeTab === 'find-mentor' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                        <i class="fas fa-search mr-2"></i>Find Mentors
                    </button>
                @endif

                <button wire:click="setActiveTab('my-mentorships')"
                    class="px-6 py-3 rounded-lg font-semibold transition-all {{ $activeTab === 'my-mentorships' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                    <i class="fas fa-handshake mr-2"></i>My Mentorships
                </button>

                <button wire:click="setActiveTab('sessions')"
                    class="px-6 py-3 rounded-lg font-semibold transition-all {{ $activeTab === 'sessions' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                    <i class="fas fa-calendar-check mr-2"></i>Sessions
                </button>

                <button wire:click="setActiveTab('code-reviews')"
                    class="px-6 py-3 rounded-lg font-semibold transition-all {{ $activeTab === 'code-reviews' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                    <i class="fas fa-code mr-2"></i>Code Reviews
                </button>

                @if(auth()->user()->isMentor() || auth()->user()->isAcademyAdmin() || auth()->user()->isSuperAdmin())
                    <button wire:click="setActiveTab('mentor-dashboard')"
                        class="px-6 py-3 rounded-lg font-semibold transition-all {{ $activeTab === 'mentor-dashboard' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50' }}">
                        <i class="fas fa-chalkboard-teacher mr-2"></i>Mentor Panel
                    </button>
                @endif
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="space-y-8">
            {{-- Dashboard Tab --}}
            @if($activeTab === 'dashboard')
                <div class="space-y-6">
                    <!-- Welcome Section -->
                    <div class="bg-white rounded-2xl shadow-lg p-8">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <h2 class="text-3xl font-bold text-gray-900 mb-2">Welcome Back, {{ auth()->user()->name }}!
                                </h2>
                                <p class="text-lg text-gray-600">Your mentorship journey continues here</p>
                            </div>
                            <div class="text-6xl text-blue-500">
                                <i class="fas fa-hands-helping"></i>
                            </div>
                        </div>

                        @if(auth()->user()->isStudent())
                            <!-- Student Quick Actions -->
                            <div class="grid md:grid-cols-3 gap-4">
                                <button wire:click="setActiveTab('find-mentor')"
                                    class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white p-6 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                    <i class="fas fa-search text-2xl mb-3"></i>
                                    <h3 class="text-lg font-semibold">Find New Mentor</h3>
                                    <p class="text-sm opacity-90">Discover expert mentors in your field</p>
                                </button>

                                <button wire:click="setActiveTab('sessions')"
                                    class="bg-gradient-to-r from-green-600 to-emerald-600 text-white p-6 rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                    <i class="fas fa-calendar-plus text-2xl mb-3"></i>
                                    <h3 class="text-lg font-semibold">Schedule Session</h3>
                                    <p class="text-sm opacity-90">Book time with your mentors</p>
                                </button>

                                <button wire:click="setActiveTab('code-reviews')"
                                    class="bg-gradient-to-r from-purple-600 to-pink-600 text-white p-6 rounded-xl hover:from-purple-700 hover:to-pink-700 transition-all shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                    <i class="fas fa-code text-2xl mb-3"></i>
                                    <h3 class="text-lg font-semibold">Request Review</h3>
                                    <p class="text-sm opacity-90">Get your code professionally reviewed</p>
                                </button>
                            </div>
                        @endif

                        @if(auth()->user()->isMentor())
                            <!-- Mentor Quick Actions -->
                            <div class="grid md:grid-cols-3 gap-4">
                                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white p-6 rounded-xl">
                                    <i class="fas fa-users text-2xl mb-3"></i>
                                    <h3 class="text-lg font-semibold">Active Mentees</h3>
                                    <p class="text-3xl font-bold">{{ $activeMentorships }}</p>
                                </div>

                                <div class="bg-gradient-to-r from-green-600 to-emerald-600 text-white p-6 rounded-xl">
                                    <i class="fas fa-calendar text-2xl mb-3"></i>
                                    <h3 class="text-lg font-semibold">Sessions This Week</h3>
                                    <p class="text-3xl font-bold">{{ $upcomingSessions }}</p>
                                </div>

                                <div class="bg-gradient-to-r from-purple-600 to-pink-600 text-white p-6 rounded-xl">
                                    <i class="fas fa-code text-2xl mb-3"></i>
                                    <h3 class="text-lg font-semibold">Pending Reviews</h3>
                                    <p class="text-3xl font-bold">{{ $pendingCodeReviews }}</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Recent Activity -->
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
                                                    <p class="text-sm text-gray-600 mt-1">
                                                        @if(auth()->user()->isStudent())
                                                            with {{ $session->mentorship->mentor->name }}
                                                        @else
                                                            with {{ $session->mentorship->mentee->name }}
                                                        @endif
                                                    </p>
                                                    <p class="text-xs text-blue-600 font-medium mt-2">
                                                        {{ $session->scheduled_at->format('M j, Y g:i A') }}
                                                    </p>
                                                </div>
                                                <div
                                                    class="text-2xl text-{{ $session->type === 'code_review' ? 'purple' : 'blue' }}-500">
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
                                                        <span
                                                            class="text-xs bg-{{ $review->status === 'pending' ? 'yellow' : ($review->status === 'completed' ? 'green' : 'blue') }}-100 text-{{ $review->status === 'pending' ? 'yellow' : ($review->status === 'completed' ? 'green' : 'blue') }}-800 px-2 py-1 rounded-full">
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
                </div>
            @endif

            {{-- Find Mentor Tab --}}
            @if($activeTab === 'find-mentor')
                <div class="space-y-6">
                    <!-- Search and Filters -->
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6">
                            <h2 class="text-2xl font-bold text-gray-900 mb-4 lg:mb-0">Find Your Perfect Mentor</h2>
                            <div class="text-sm text-gray-600">
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                                    {{ count($mentors) }} mentors available
                                </span>
                            </div>
                        </div>

                        <!-- Search Bar -->
                        <div class="mb-6">
                            <div class="relative">
                                <input wire:model.live.debounce.300ms="searchTerm" type="text"
                                    placeholder="Search by name, skills, or specialization..."
                                    class="w-full pl-12 pr-4 py-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg">
                                <div class="absolute left-4 top-1/2 transform -translate-y-1/2">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Filters -->
                        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                            <select wire:model.live="experienceFilter"
                                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">All Experience</option>
                                <option value="junior">Junior Level</option>
                                <option value="mid">Mid Level</option>
                                <option value="senior">Senior Level</option>
                                <option value="expert">Expert Level</option>
                            </select>

                            <select wire:model.live="availabilityFilter"
                                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="available">Available Now</option>
                                <option value="all">All Mentors</option>
                            </select>

                            <select wire:model.live="ratingFilter"
                                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Any Rating</option>
                                <option value="4.5">4.5+ Stars</option>
                                <option value="4">4+ Stars</option>
                                <option value="3.5">3.5+ Stars</option>
                            </select>

                            <select wire:model.live="priceRangeFilter"
                                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Any Price</option>
                                <option value="0-0">Free</option>
                                <option value="0-50">$0-50/hr</option>
                                <option value="50-100">$50-100/hr</option>
                                <option value="100-200">$100+/hr</option>
                            </select>

                            <select wire:model.live="specializationFilter"
                                class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">All Specializations</option>
                                <option value="Web Development">Web Development</option>
                                <option value="Mobile Development">Mobile Development</option>
                                <option value="Data Science">Data Science</option>
                                <option value="DevOps">DevOps</option>
                                <option value="UI/UX Design">UI/UX Design</option>
                            </select>

                            <div class="flex items-center space-x-2">
                                <button wire:click="$set('viewMode', 'grid')"
                                    class="p-2 rounded-lg {{ $viewMode === 'grid' ? 'bg-blue-100 text-blue-600' : 'text-gray-400 hover:text-gray-600' }}">
                                    <i class="fas fa-th"></i>
                                </button>
                                <button wire:click="$set('viewMode', 'list')"
                                    class="p-2 rounded-lg {{ $viewMode === 'list' ? 'bg-blue-100 text-blue-600' : 'text-gray-400 hover:text-gray-600' }}">
                                    <i class="fas fa-list"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Mentors Grid/List -->
                    @if(count($mentors) > 0)
                        @if($viewMode === 'grid')
                            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($mentors as $mentor)
                                    <div
                                        class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-blue-200">
                                        <!-- Mentor Header -->
                                        <div class="p-6 border-b border-gray-100">
                                            <div class="flex items-center space-x-4">
                                                <div
                                                    class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                                                    {{ substr($mentor->user->name, 0, 1) }}
                                                </div>
                                                <div class="flex-1">
                                                    <h3 class="text-xl font-bold text-gray-900">{{ $mentor->user->name }}</h3>
                                                    <p class="text-sm text-gray-600">{{ $mentor->experience_label }}</p>
                                                    <div class="flex items-center mt-1">
                                                        <div class="text-yellow-400 mr-2">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                @if($i <= floor($mentor->rating))
                                                                    <i class="fas fa-star"></i>
                                                                @elseif($i - 0.5 <= $mentor->rating)
                                                                    <i class="fas fa-star-half-alt"></i>
                                                                @else
                                                                    <i class="far fa-star"></i>
                                                                @endif
                                                            @endfor
                                                        </div>
                                                        <span class="text-sm text-gray-600">({{ $mentor->total_reviews }})</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Mentor Details -->
                                        <div class="p-6">
                                            <!-- Availability Status -->
                                            <div class="mb-4">
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                                                    {{ $mentor->canAcceptMentees() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    <div
                                                        class="w-2 h-2 rounded-full {{ $mentor->canAcceptMentees() ? 'bg-green-400' : 'bg-red-400' }} mr-2">
                                                    </div>
                                                    {{ $mentor->availability_status }}
                                                </span>
                                            </div>

                                            <!-- Bio -->
                                            <p class="text-gray-700 text-sm mb-4 line-clamp-3">
                                                {{ Str::limit($mentor->bio, 120) }}
                                            </p>

                                            <!-- Specializations -->
                                            <div class="mb-4">
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach(array_slice($mentor->specializations ?? [], 0, 3) as $spec)
                                                        <span class="bg-blue-50 text-blue-700 text-xs px-2 py-1 rounded-full">
                                                            {{ $spec }}
                                                        </span>
                                                    @endforeach
                                                    @if(count($mentor->specializations ?? []) > 3)
                                                        <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full">
                                                            +{{ count($mentor->specializations) - 3 }} more
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Pricing -->
                                            <div class="mb-4">
                                                @if($mentor->offers_free_sessions)
                                                    <span class="text-green-600 font-semibold">Free Sessions Available</span>
                                                @else
                                                    <span
                                                        class="text-gray-900 font-semibold">${{ number_format($mentor->hourly_rate, 0) }}/hour</span>
                                                @endif
                                            </div>

                                            <!-- Stats -->
                                            <div class="flex justify-between text-sm text-gray-600 mb-6">
                                                <span>{{ $mentor->total_mentees }} mentees</span>
                                                <span>{{ $mentor->total_sessions }} sessions</span>
                                                <span>{{ $mentor->years_experience }}+ years</span>
                                            </div>

                                            <!-- Actions -->
                                            <div class="space-y-2">
                                                <button wire:click="selectMentor({{ $mentor->id }})"
                                                    class="w-full bg-blue-600 text-white px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                                                    View Profile
                                                </button>
                                                @if($mentor->canAcceptMentees())
                                                    <button wire:click="requestMentorship({{ $mentor->id }})"
                                                        class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                                        Request Mentorship
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <!-- List View -->
                            <div class="space-y-4">
                                @foreach($mentors as $mentor)
                                    <div
                                        class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 p-6 border border-gray-100 hover:border-blue-200">
                                        <div class="flex flex-col lg:flex-row lg:items-center">
                                            <!-- Mentor Info -->
                                            <div class="flex items-center space-x-4 mb-4 lg:mb-0 lg:flex-1">
                                                <div
                                                    class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold">
                                                    {{ substr($mentor->user->name, 0, 1) }}
                                                </div>
                                                <div class="flex-1">
                                                    <h3 class="text-lg font-bold text-gray-900">{{ $mentor->user->name }}</h3>
                                                    <p class="text-sm text-gray-600">{{ $mentor->experience_label }} •
                                                        {{ $mentor->years_experience }}+ years</p>
                                                    <div class="flex items-center mt-1">
                                                        <div class="text-yellow-400 mr-2 text-sm">
                                                            {{ $mentor->rating_stars }}
                                                        </div>
                                                        <span class="text-sm text-gray-600">({{ $mentor->total_reviews }})</span>
                                                        <span class="mx-2 text-gray-300">•</span>
                                                        <span
                                                            class="text-sm {{ $mentor->canAcceptMentees() ? 'text-green-600' : 'text-red-600' }}">
                                                            {{ $mentor->availability_status }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Specializations -->
                                            <div class="lg:flex-1 lg:mx-6 mb-4 lg:mb-0">
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach(array_slice($mentor->specializations ?? [], 0, 4) as $spec)
                                                        <span class="bg-blue-50 text-blue-700 text-xs px-2 py-1 rounded-full">
                                                            {{ $spec }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>

                                            <!-- Pricing and Actions -->
                                            <div class="lg:flex-shrink-0">
                                                <div class="text-center lg:text-right mb-4">
                                                    @if($mentor->offers_free_sessions)
                                                        <div class="text-green-600 font-semibold">Free Sessions</div>
                                                    @else
                                                        <div class="text-gray-900 font-semibold text-lg">
                                                            ${{ number_format($mentor->hourly_rate, 0) }}/hr</div>
                                                    @endif
                                                    <div class="text-sm text-gray-600">{{ $mentor->total_mentees }} mentees</div>
                                                </div>
                                                <div class="flex space-x-2">
                                                    <button wire:click="selectMentor({{ $mentor->id }})"
                                                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                                        View Profile
                                                    </button>
                                                    @if($mentor->canAcceptMentees())
                                                        <button wire:click="requestMentorship({{ $mentor->id }})"
                                                            class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors text-sm">
                                                            Request
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <div class="text-center py-16 bg-white rounded-2xl shadow-lg">
                            <div class="text-6xl text-gray-400 mb-4">
                                <i class="fas fa-search"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-700 mb-2">No mentors found</h3>
                            <p class="text-gray-500 mb-6">Try adjusting your search criteria or filters</p>
                            <button wire:click="$set('searchTerm', '')"
                                class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                                Clear Filters
                            </button>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Modals and additional content would continue here... -->
    <!-- This template continues with the other tabs, modals, and functionality -->

    <style>
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

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

    <!-- Wire loading indicators -->
    <div wire:loading class="fixed inset-0 bg-black bg-opacity-25 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 shadow-xl">
            <div class="flex items-center space-x-3">
                <svg class="animate-spin h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <span class="text-gray-700 font-medium">Loading...</span>
            </div>
        </div>
    </div>
</div>