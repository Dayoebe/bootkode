<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ activeTab: 'personal' }">
    <!-- Profile Header -->
    <div class="bg-gradient-to-r from-gray-800 to-gray-700 p-6 rounded-2xl shadow-xl text-white mb-8">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div class="flex items-center space-x-4">
                <div class="relative">
                    @if ($user->profile_picture)
                        <img src="{{ asset('storage/' . $user->profile_picture) }}"
                            class="h-24 w-24 rounded-full object-cover border-4 border-blue-500/20 shadow-lg">
                    @else
                        <div
                            class="h-24 w-24 rounded-full bg-gradient-to-r from-blue-500 to-pink-600 flex items-center justify-center text-white text-4xl font-bold shadow-lg">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                    <span
                        class="absolute bottom-0 right-0 bg-green-500 rounded-full h-5 w-5 border-2 border-gray-800"></span>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-white">{{ $user->name }}</h1>
                    <div class="flex flex-wrap items-center gap-2 mt-2">
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-700 text-blue-300">
                            {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                        </span>
                        @if ($user->email_verified_at)
                            <span
                                class="px-3 py-1 rounded-full text-xs font-medium bg-green-900/30 text-green-400 flex items-center">
                                <i class="fas fa-check-circle mr-1"></i> Verified
                            </span>
                        @else
                            <span
                                class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-900/30 text-yellow-400 flex items-center">
                                <i class="fas fa-exclamation-circle mr-1"></i> Unverified
                            </span>
                        @endif
                        @if ($user->is_active)
                            <span
                                class="px-3 py-1 rounded-full text-xs font-medium bg-blue-900/30 text-blue-400 flex items-center">
                                <i class="fas fa-circle mr-1 text-xs"></i> Active
                            </span>
                        @endif
                    </div>
                    @if ($user->bio)
                        <p class="text-gray-300 mt-2 text-sm">{{ $user->bio }}</p>
                    @endif
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                <a href="{{ route('profile.edit') }}"
                    class="px-6 py-3 bg-gradient-to-r from-blue-600 to-pink-600 hover:from-blue-700 hover:to-pink-700 text-white rounded-xl font-semibold shadow-lg transition-all duration-300 flex items-center justify-center">
                    <i class="fas fa-user-edit mr-2"></i> Edit Profile
                </a>
                <a href="#"
                    class="px-6 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-xl font-medium transition-all duration-300 flex items-center justify-center">
                    <i class="fas fa-share-alt mr-2"></i> Share Profile
                </a>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="mb-6 border-b border-gray-700">
        <nav class="flex space-x-8">
            <button @click="activeTab = 'personal'"
                :class="{ 'border-blue-500 text-blue-400': activeTab === 'personal', 'border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-400': activeTab !== 'personal' }"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                <i class="fas fa-user-circle mr-2"></i> Personal Info
            </button>
            <button @click="activeTab = 'education'"
                :class="{ 'border-blue-500 text-blue-400': activeTab === 'education', 'border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-400': activeTab !== 'education' }"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                <i class="fas fa-graduation-cap mr-2"></i> Education
            </button>
            <button @click="activeTab = 'social'"
                :class="{ 'border-blue-500 text-blue-400': activeTab === 'social', 'border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-400': activeTab !== 'social' }"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                <i class="fas fa-share-alt mr-2"></i> Social Links
            </button>
            <button @click="activeTab = 'activity'"
                :class="{ 'border-blue-500 text-blue-400': activeTab === 'activity', 'border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-400': activeTab !== 'activity' }"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                <i class="fas fa-chart-line mr-2"></i> Activity
            </button>
            <button @click="activeTab = 'progress'"
                :class="{ 'border-blue-500 text-blue-400': activeTab === 'progress', 'border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-400': activeTab !== 'progress' }"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                <i class="fas fa-chart-pie mr-2"></i> Progress
            </button>
            <button @click="activeTab = 'resources'"
                :class="{ 'border-blue-500 text-blue-400': activeTab === 'resources', 'border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-400': activeTab !== 'resources' }"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                <i class="fas fa-bookmark mr-2"></i> Resources
            </button>
            <button @click="activeTab = 'wishlist'"
                :class="{ 'border-blue-500 text-blue-400': activeTab === 'wishlist', 'border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-400': activeTab !== 'wishlist' }"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                <i class="fas fa-heart mr-2"></i> Wishlist
            </button>
            <button @click="activeTab = 'notes'"
                :class="{ 'border-blue-500 text-blue-400': activeTab === 'notes', 'border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-400': activeTab !== 'notes' }"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                <i class="fas fa-sticky-note mr-2"></i> Notes
            </button>
            <button @click="activeTab = 'reviews'"
                :class="{ 'border-blue-500 text-blue-400': activeTab === 'reviews', 'border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-400': activeTab !== 'reviews' }"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                <i class="fas fa-star mr-2"></i> Reviews
            </button>
        </nav>
    </div>

    <!-- Tab Content -->
    <div class="bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
        <!-- Personal Information Tab -->
        <div x-show="activeTab === 'personal'" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="p-6">
            <h2 class="text-xl font-bold text-white flex items-center mb-6">
                <i class="fas fa-user-circle text-blue-400 mr-3"></i> Personal Information
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Info -->
                <div class="space-y-6">
                    <div>
                        <p class="text-sm font-medium text-gray-400 mb-1">Full Name</p>
                        <p class="text-lg text-white">{{ $user->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-400 mb-1">Email Address</p>
                        <p class="text-lg text-white">{{ $user->email }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-400 mb-1">Phone Number</p>
                        <p class="text-lg text-white">{{ $user->phone_number ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-400 mb-1">Date of Birth</p>
                        <p class="text-lg text-white">
                            {{ $user->date_of_birth ? $user->date_of_birth->format('M d, Y') : 'Not provided' }}</p>
                    </div>
                </div>

                <!-- Address Info -->
                <div class="space-y-6">
                    <div>
                        <p class="text-sm font-medium text-gray-400 mb-1">Occupation</p>
                        <p class="text-lg text-white">{{ $user->occupation ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-400 mb-1">Full Address</p>
                        <p class="text-lg text-white">{{ $user->full_address ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-400 mb-1">Account Created</p>
                        <p class="text-lg text-white">{{ $user->created_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-400 mb-1">Last Updated</p>
                        <p class="text-lg text-white">{{ $user->updated_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Education Tab -->
        <div x-show="activeTab === 'education'" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="p-6">
            <h2 class="text-xl font-bold text-white flex items-center mb-6">
                <i class="fas fa-graduation-cap text-pink-400 mr-3"></i> Education & Career
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-6">
                    <div>
                        <p class="text-sm font-medium text-gray-400 mb-1">Education Level</p>
                        <p class="text-lg text-white">{{ $user->education_level ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-400 mb-1">Occupation</p>
                        <p class="text-lg text-white">{{ $user->occupation ?? 'Not provided' }}</p>
                    </div>
                </div>

                <div class="space-y-6">
                    <div>
                        <p class="text-sm font-medium text-gray-400 mb-1">Skills & Interests</p>
                        <div class="flex flex-wrap gap-2 mt-2">
                            @if ($user->skills)
                                @foreach (explode(',', $user->skills) as $skill)
                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-700 text-blue-300">
                                        {{ trim($skill) }}
                                    </span>
                                @endforeach
                            @else
                                <p class="text-gray-400">Not specified</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Social Links Tab -->
        <div x-show="activeTab === 'social'" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="p-6">
            <h2 class="text-xl font-bold text-white flex items-center mb-6">
                <i class="fas fa-share-alt text-green-400 mr-3"></i> Social Links
            </h2>

            @if ($user->social_links && count($user->social_links) > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($user->social_links as $platform => $url)
                        <a href="{{ $url }}" target="_blank"
                            class="bg-gray-700 hover:bg-gray-600 rounded-lg p-4 transition-colors duration-200 flex items-center">
                            <div class="w-10 h-10 rounded-full bg-gray-600 flex items-center justify-center mr-4">
                                @switch($platform)
                                    @case('twitter')
                                        <i class="fab fa-twitter text-blue-400"></i>
                                    @break

                                    @case('facebook')
                                        <i class="fab fa-facebook-f text-blue-600"></i>
                                    @break

                                    @case('linkedin')
                                        <i class="fab fa-linkedin-in text-blue-500"></i>
                                    @break

                                    @case('github')
                                        <i class="fab fa-github text-gray-300"></i>
                                    @break

                                    @case('instagram')
                                        <i class="fab fa-instagram text-pink-500"></i>
                                    @break

                                    @default
                                        <i class="fas fa-link text-pink-400"></i>
                                @endswitch
                            </div>
                            <div>
                                <p class="text-white font-medium capitalize">{{ $platform }}</p>
                                <p class="text-gray-400 text-sm truncate">{{ $url }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="bg-gray-700/50 rounded-lg p-8 text-center">
                    <i class="fas fa-share-alt text-gray-500 text-4xl mb-3"></i>
                    <h3 class="text-lg text-gray-300 font-medium">No social links added</h3>
                    <p class="text-gray-500 mt-1">Add your social media profiles to connect with others</p>
                    <a href="{{ route('profile.edit') }}"
                        class="mt-4 inline-block px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors duration-200">
                        <i class="fas fa-plus mr-1"></i> Add Social Links
                    </a>
                </div>
            @endif
        </div>

        <!-- Activity Tab -->
        <div x-show="activeTab === 'activity'" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="p-6">
            <h2 class="text-xl font-bold text-white flex items-center mb-6">
                <i class="fas fa-chart-line text-yellow-400 mr-3"></i> Activity Overview
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div
                    class="bg-gradient-to-br from-gray-800 to-gray-700 p-5 rounded-xl border border-gray-700 hover:border-blue-500 transition-colors duration-200">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-gray-400 font-medium">Courses Enrolled</h3>
                        <i class="fas fa-book-open text-blue-400"></i>
                    </div>
                    <p class="text-3xl font-bold text-white">{{ $activityStats['courses_enrolled']['total'] }}</p>
                    <p class="text-sm text-gray-400 mt-2">+{{ $activityStats['courses_enrolled']['this_month'] }} this
                        month</p>
                </div>

                <div
                    class="bg-gradient-to-br from-gray-800 to-gray-700 p-5 rounded-xl border border-gray-700 hover:border-green-500 transition-colors duration-200">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-gray-400 font-medium">Lessons Completed</h3>
                        <i class="fas fa-check-circle text-green-400"></i>
                    </div>
                    <p class="text-3xl font-bold text-white">{{ $activityStats['lessons_completed']['total'] }}</p>
                    <p class="text-sm text-gray-400 mt-2">+{{ $activityStats['lessons_completed']['this_month'] }}
                        this month</p>
                </div>
            </div>

            <!-- Recent Activity Timeline -->
            <div class="mt-8">
                <h3 class="text-lg font-medium text-white mb-4 flex items-center">
                    <i class="fas fa-history text-gray-400 mr-2"></i> Recent Activity
                </h3>

                <div class="space-y-4">
                    @forelse($recentActivities as $activity)
                        <div class="flex items-start">
                            <div
                                class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-700 flex items-center justify-center mr-4">
                                <i class="fas fa-{{ $activity['icon'] }} text-{{ $activity['color'] }}"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-white font-medium">{{ $activity['title'] }}</p>
                                <p class="text-sm text-gray-400">{{ $activity['course'] }}</p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ Carbon\Carbon::parse($activity['date'])->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-400">
                            <i class="fas fa-inbox text-4xl mb-2"></i>
                            <p>No recent activity found</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        <!-- Progress Tab -->
        <div x-show="activeTab === 'progress'" class="p-6">
            <h2 class="text-xl font-bold text-white flex items-center mb-6">
                <i class="fas fa-chart-pie text-pink-400 mr-3"></i> Learning Progress
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Courses Card -->
                <div
                    class="bg-gradient-to-br from-blue-900/30 to-blue-800/30 p-5 rounded-xl border border-blue-800/50">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-gray-400 font-medium">Courses</h3>
                        <i class="fas fa-book text-blue-400"></i>
                    </div>
                    <div class="flex items-end justify-between">
                        <div>
                            <p class="text-3xl font-bold text-white">{{ $learningProgress['total_courses'] }}</p>
                            <p class="text-sm text-gray-400">Enrolled</p>
                        </div>
                    </div>
                </div>

                <!-- Lessons Card -->
                <div
                    class="bg-gradient-to-br from-green-900/30 to-green-800/30 p-5 rounded-xl border border-green-800/50">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-gray-400 font-medium">Lessons</h3>
                        <i class="fas fa-check-circle text-green-400"></i>
                    </div>
                    <div class="flex items-end justify-between">
                        <div>
                            <p class="text-3xl font-bold text-white">{{ $learningProgress['completed_lessons'] }}</p>
                            <p class="text-sm text-gray-400">Completed</p>
                        </div>
                    </div>
                </div>

                <!-- Resources Card -->
                <div
                    class="bg-gradient-to-br from-yellow-900/30 to-yellow-800/30 p-5 rounded-xl border border-yellow-800/50">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-gray-400 font-medium">Resources</h3>
                        <i class="fas fa-bookmark text-yellow-400"></i>
                    </div>
                    <div class="flex items-end justify-between">
                        <div>
                            <p class="text-3xl font-bold text-white">{{ $learningProgress['saved_resources'] }}</p>
                            <p class="text-sm text-gray-400">Saved</p>
                        </div>
                    </div>
                </div>
                <!-- Wishlist Card -->
                <div
                    class="bg-gradient-to-br from-pink-900/30 to-pink-800/30 p-5 rounded-xl border border-pink-800/50">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-gray-400 font-medium">Wishlist</h3>
                        <i class="fas fa-heart text-pink-400"></i>
                    </div>
                    <div class="flex items-end justify-between">
                        <div>
                            <p class="text-3xl font-bold text-white">{{ $learningProgress['wishlist_items'] }}</p>
                            <p class="text-sm text-gray-400">Items</p>
                        </div>
                    </div>
                </div>
                {{-- assignment card --}}
                <div
                    class="bg-gradient-to-br from-pink-900/30 to-pink-800/30 p-5 rounded-xl border border-pink-800/50">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-gray-400 font-medium">Assignments</h3>
                        <i class="fas fa-tasks text-pink-400"></i>
                    </div>
                    <div class="flex items  
                        justify-between">
                        <div>
                            <p class="text-3xl font-bold text-white">{{ $learningProgress['completed_assignments'] }}
                            </p>
                            <p class="text-sm text-gray-400">Completed</p>
                        </div>
                    </div>
                </div>

                <!-- Course Progress Visualization -->
                <div
                    class="bg-gradient-to-br from-blue-900/30 to-pink-800/30 p-5 rounded-xl border border-pink-800/50">

                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-lg font-medium text-white mb-4">Course Progress</h3>
                        <i class="fas fa-chart-bar text-blue-400"></i>
                    </div>
                    @foreach ($user->courses as $course)
                        @php
                            $completedCount = $user
                                ->completedLessons()
                                ->whereHas('module', function ($query) use ($course) {
                                    $query->where('course_id', $course->id);
                                })
                                ->count();
                            $totalLessons = $course->lessons()->count();
                            $percentage = $totalLessons > 0 ? ($completedCount / $totalLessons) * 100 : 0;
                        @endphp
                        <div class="mb-4">
                            <div class="flex justify-between mb-1">
                                <span class="text-sm font-medium text-blue-400">{{ $course->title }}</span>
                                <span class="text-sm font-medium text-gray-400">
                                    {{ $completedCount }}/{{ $totalLessons }}
                                </span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-2.5">
                                <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $percentage }}%">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Resources Tab -->
        <div x-show="activeTab === 'resources'" class="p-6">
            <h2 class="text-xl font-bold text-white flex items-center mb-6">
                <i class="fas fa-bookmark text-yellow-400 mr-3"></i> Saved Resources
            </h2>

            @if ($savedResources->count() > 0)
                <div class="space-y-4">
                    @foreach ($savedResources as $resource)
                        <div class="bg-gray-700/50 hover:bg-gray-700 rounded-lg p-4 transition-colors duration-200">
                            <div class="flex items-start">
                                <div
                                    class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-600 flex items-center justify-center mr-4">
                                    @if ($resource->resourceable_type === 'App\Models\Lesson')
                                        <i class="fas fa-play text-blue-400"></i>
                                    @elseif($resource->resourceable_type === 'App\Models\Assignment')
                                        <i class="fas fa-tasks text-green-400"></i>
                                    @else
                                        <i class="fas fa-file text-pink-400"></i>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-white font-medium">{{ $resource->resourceable->title }}</h4>
                                    <p class="text-sm text-gray-400">{{ $resource->course->title ?? 'General' }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">Saved
                                        {{ $resource->created_at->diffForHumans() }}</p>
                                </div>

                                <a href="#" class="text-blue-400 hover:text-blue-300 ml-4">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-gray-700/50 rounded-lg p-8 text-center">
                    <i class="fas fa-bookmark text-gray-500 text-4xl mb-3"></i>
                    <h3 class="text-lg text-gray-300 font-medium">No saved resources</h3>
                    <p class="text-gray-500 mt-1">Bookmark important lessons and assignments for quick access</p>
                </div>
            @endif
        </div>
        <!-- Wishlist Tab -->
        <div x-show="activeTab === 'wishlist'" class="p-6">
            <h2 class="text-xl font-bold text-white flex items-center mb-6">
                <i class="fas fa-heart text-pink-400 mr-3"></i> Wishlist
            </h2>

            @if ($wishlist->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ($wishlist as $item)
                        <div class="bg-gray-700/50 hover:bg-gray-700 rounded-lg p-4 transition-colors duration-200">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 h-16 w-16 rounded-lg bg-gray-600 overflow-hidden mr-4">
                                    @if ($item->course->thumbnail)
                                        <img src="{{ asset('storage/' . $item->course->thumbnail) }}"
                                            class="h-full w-full object-cover">
                                    @else
                                        <div
                                            class="h-full w-full bg-gradient-to-r from-blue-500 to-pink-600 flex items-center justify-center">
                                            <i class="fas fa-book text-white text-xl"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-white font-medium">{{ $item->course->title }}</h4>
                                    <p class="text-sm text-gray-400">
                                        {{ $item->course->category->name ?? 'No category' }}</p>
                                    <div class="flex items-center mt-2">
                                        <span class="text-xs px-2 py-1 bg-gray-600 rounded-full text-gray-300">
                                            {{ $item->course->difficulty_level }}
                                        </span>
                                        <span class="text-xs px-2 py-1 bg-gray-600 rounded-full text-gray-300 ml-2">
                                            {{ $item->course->estimated_duration_minutes }} mins
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-gray-700/50 rounded-lg p-8 text-center">
                    <i class="fas fa-heart text-gray-500 text-4xl mb-3"></i>
                    <h3 class="text-lg text-gray-300 font-medium">Your wishlist is empty</h3>
                    <p class="text-gray-500 mt-1">Save courses you're interested in for later</p>
                </div>
            @endif
        </div>
        <!-- Notes tab -->
        <div x-show="activeTab === 'notes'" class="p-6">
            <h2 class="text-xl font-bold text-white flex items-center mb-6">
                <i class="fas fa-sticky-note text-green-400 mr-3"></i> My Notes
            </h2>

            @if ($notes->count() > 0)
                <div class="space-y-4">
                    @foreach ($notes as $note)
                        <div class="bg-gray-700/50 hover:bg-gray-700 rounded-lg p-4 transition-colors duration-200">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="text-white font-medium">
                                    {{ $note->is_general_note ? 'General Note' : $note->course->title ?? 'Course Note' }}
                                </h4>
                                <span class="text-xs text-gray-400">{{ $note->updated_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-gray-300 text-sm line-clamp-2">{{ Str::limit($note->content, 150) }}
                            </p>
                            @if (!$note->is_general_note)
                                <div class="mt-2 flex items-center text-xs text-gray-400">
                                    <i class="fas fa-book mr-1"></i>
                                    <span>{{ $note->course->title }}</span>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-gray-700/50 rounded-lg p-8 text-center">
                    <i class="fas fa-sticky-note text-gray-500 text-4xl mb-3"></i>
                    <h3 class="text-lg text-gray-300 font-medium">No notes yet</h3>
                    <p class="text-gray-500 mt-1">Take notes while learning to reinforce your knowledge</p>
                </div>
            @endif
        </div>

        <!-- Reviews Tab -->
        {{-- <div x-show="activeTab === 'reviews'" class="p-6">
            <h2 class="text-xl font-bold text-white flex items-center mb-6">
                <i class="fas fa-star text-yellow-400 mr-3"></i> My Reviews
            </h2>

            @if ($reviews->count() > 0)
                <div class="space-y-6">
                    @foreach ($reviews as $review)
                        <div class="bg-gray-700/50 hover:bg-gray-700 rounded-xl p-5 transition-colors duration-200">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <h4 class="text-white font-medium">{{ $review->course->title }}</h4>
                                    <div class="flex items-center mt-1">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i
                                                class="fas fa-star {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-600' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                                <span class="text-xs text-gray-400">{{ $review->created_at->format('M d, Y') }}</span>
                            </div>
                            @if ($review->comment)
                                <p class="text-gray-300 text-sm">{{ $review->comment }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-gray-700/50 rounded-lg p-8 text-center">
                    <i class="fas fa-star text-gray-500 text-4xl mb-3"></i>
                    <h3 class="text-lg text-gray-300 font-medium">No reviews yet</h3>
                    <p class="text-gray-500 mt-1">Share your thoughts on courses you've taken</p>
                </div>
            @endif
        </div> --}}





    </div>
</div>
