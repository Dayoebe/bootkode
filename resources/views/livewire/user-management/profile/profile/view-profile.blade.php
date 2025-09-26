<div class="px-2 sm:px-4 lg:px-6">
<!-- Personal Information Tab -->
<div x-show="activeTab === 'personal'" x-transition.opacity.duration.300ms class="py-4 sm:py-6 lg:py-8">
    <div class="flex items-center mb-6 sm:mb-8">
        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center mr-3 sm:mr-4">
            <i class="fas fa-user-circle text-white text-lg sm:text-xl"></i>
        </div>
        <div>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white transition-colors duration-300">Personal Information</h2>
            <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400 transition-colors duration-300">Your profile details and account information</p>
        </div>
    </div>

    <!-- Changed grid layout to be responsive -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 md:gap-8">
        <!-- Basic Info -->
        <div class="space-y-4 sm:space-y-6">
            <div class="bg-white dark:bg-gray-700/30 p-4 sm:p-6 rounded-xl border border-gray-200 dark:border-gray-600/50 backdrop-blur-sm transition-colors duration-300">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3 sm:mb-4 flex items-center transition-colors duration-300">
                    <i class="fas fa-user text-blue-500 dark:text-blue-400 mr-2"></i>
                    Basic Information
                </h3>

                <div class="space-y-3 sm:space-y-4">
                    <div>
                        <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 mb-1 transition-colors duration-300">Full Name</p>
                        <p class="text-base sm:text-lg text-gray-900 dark:text-white transition-colors duration-300">{{ $user->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 mb-1 transition-colors duration-300">Email Address</p>
                        <p class="text-base sm:text-lg text-gray-900 dark:text-white flex items-center transition-colors duration-300">
                            {{ $user->email }}
                            @if ($user->email_verified_at)
                                <span class="ml-2 text-green-500 dark:text-green-400"><i class="fas fa-check-circle"></i></span>
                            @else
                                <span class="ml-2 text-yellow-500 dark:text-yellow-400"><i class="fas fa-exclamation-circle"></i></span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 mb-1 transition-colors duration-300">Phone Number</p>
                        <p class="text-base sm:text-lg text-gray-900 dark:text-white transition-colors duration-300">{{ $user->phone_number ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 mb-1 transition-colors duration-300">Date of Birth</p>
                        <p class="text-base sm:text-lg text-gray-900 dark:text-white transition-colors duration-300">
                            {{ $user->date_of_birth ? $user->date_of_birth->format('M d, Y') : 'Not provided' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Address & Professional Info -->
        <div class="space-y-4 sm:space-y-6">
            <div class="bg-white dark:bg-gray-700/30 p-4 sm:p-6 rounded-xl border border-gray-200 dark:border-gray-600/50 backdrop-blur-sm transition-colors duration-300">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3 sm:mb-4 flex items-center transition-colors duration-300">
                    <i class="fas fa-briefcase text-green-500 dark:text-green-400 mr-2"></i>
                    Professional & Location
                </h3>

                <div class="space-y-3 sm:space-y-4">
                    <div>
                        <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 mb-1 transition-colors duration-300">Occupation</p>
                        <p class="text-base sm:text-lg text-gray-900 dark:text-white transition-colors duration-300">{{ $user->occupation ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 mb-1 transition-colors duration-300">Full Address</p>
                        <p class="text-base sm:text-lg text-gray-900 dark:text-white transition-colors duration-300">{{ $user->full_address ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 mb-1 transition-colors duration-300">Account Created</p>
                        <p class="text-base sm:text-lg text-gray-900 dark:text-white transition-colors duration-300">{{ $user->created_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 mb-1 transition-colors duration-300">Last Updated</p>
                        <p class="text-base sm:text-lg text-gray-900 dark:text-white transition-colors duration-300">{{ $user->updated_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Education Tab -->
    <div x-show="activeTab === 'education'" x-transition.opacity.duration.300ms class="py-4 sm:py-6 lg:py-8">
        <div class="flex items-center mb-6 sm:mb-8">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl flex items-center justify-center mr-3 sm:mr-4">
                <i class="fas fa-graduation-cap text-white text-lg sm:text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white transition-colors duration-300">Education & Career</h2>
                <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400 transition-colors duration-300">Your educational background and professional skills</p>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 sm:gap-6 md:gap-8">
            <div class="bg-white dark:bg-gray-700/30 p-4 sm:p-6 rounded-xl border border-gray-200 dark:border-gray-600/50 backdrop-blur-sm transition-colors duration-300">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3 sm:mb-4 flex items-center transition-colors duration-300">
                    <i class="fas fa-graduation-cap text-purple-500 dark:text-purple-400 mr-2"></i>
                    Education & Career
                </h3>

                <div class="space-y-3 sm:space-y-4">
                    <div>
                        <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 mb-1 transition-colors duration-300">Education Level</p>
                        <p class="text-base sm:text-lg text-gray-900 dark:text-white transition-colors duration-300">{{ $user->education_level ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <p class="text-xs sm:text-sm font-medium text-gray-500 dark:text-gray-400 mb-1 transition-colors duration-300">Occupation</p>
                        <p class="text-base sm:text-lg text-gray-900 dark:text-white transition-colors duration-300">{{ $user->occupation ?? 'Not provided' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-700/30 p-4 sm:p-6 rounded-xl border border-gray-200 dark:border-gray-600/50 backdrop-blur-sm transition-colors duration-300">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3 sm:mb-4 flex items-center transition-colors duration-300">
                    <i class="fas fa-tools text-pink-500 dark:text-pink-400 mr-2"></i>
                    Skills & Interests
                </h3>

                <div>
                    @if ($user->skills)
                        <div class="flex flex-wrap gap-2">
                            @foreach (explode(',', $user->skills) as $skill)
                                <span class="px-2 py-1 sm:px-3 sm:py-2 rounded-full text-xs sm:text-sm font-medium bg-purple-100 dark:bg-purple-500/20 border border-purple-200 dark:border-purple-500/30 text-purple-700 dark:text-purple-300 backdrop-blur-sm transition-colors duration-300">
                                    {{ trim($skill) }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 sm:py-6 text-gray-500 dark:text-gray-400 transition-colors duration-300">
                            <i class="fas fa-tools text-xl sm:text-2xl mb-2"></i>
                            <p class="text-sm sm:text-base">No skills specified</p>
                            <button wire:click="toggleEditMode" class="mt-2 text-xs sm:text-sm text-blue-500 dark:text-blue-400 hover:text-blue-600 dark:hover:text-blue-300 transition-colors duration-300">
                                Add skills →
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Social Links Tab -->
    <div x-show="activeTab === 'social'" x-transition.opacity.duration.300ms class="py-4 sm:py-6 lg:py-8">
        <div class="flex items-center mb-6 sm:mb-8">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-r from-pink-500 to-red-600 rounded-xl flex items-center justify-center mr-3 sm:mr-4">
                <i class="fas fa-share-alt text-white text-lg sm:text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white transition-colors duration-300">Social Links</h2>
                <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400 transition-colors duration-300">Connect with your social media profiles</p>
            </div>
        </div>

        @if ($user->social_links && count($user->social_links) > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                @foreach ($user->social_links as $platform => $url)
                    <a href="{{ $url }}" target="_blank"
                        class="bg-white dark:bg-gray-700/30 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-xl p-4 sm:p-6 transition-all duration-300 border border-gray-200 dark:border-gray-600/50 backdrop-blur-sm group">
                        <div class="flex items-center">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-gradient-to-r from-pink-100 dark:from-pink-500/20 to-red-100 dark:to-red-500/20 border border-pink-200 dark:border-pink-500/30 flex items-center justify-center mr-3 sm:mr-4 group-hover:scale-105 transition-transform duration-300">
                                @switch($platform)
                                    @case('twitter')
                                        <i class="fab fa-twitter text-blue-400 text-lg sm:text-xl"></i>
                                    @break

                                    @case('facebook')
                                        <i class="fab fa-facebook-f text-blue-500 text-lg sm:text-xl"></i>
                                    @break

                                    @case('linkedin')
                                        <i class="fab fa-linkedin-in text-blue-600 text-lg sm:text-xl"></i>
                                    @break

                                    @case('github')
                                        <i class="fab fa-github text-gray-600 dark:text-gray-300 text-lg sm:text-xl"></i>
                                    @break

                                    @case('instagram')
                                        <i class="fab fa-instagram text-pink-500 text-lg sm:text-xl"></i>
                                    @break

                                    @case('website')
                                        <i class="fas fa-globe text-pink-400 text-lg sm:text-xl"></i>
                                    @break

                                    @default
                                        <i class="fas fa-link text-gray-400 text-lg sm:text-xl"></i>
                                @endswitch
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-gray-900 dark:text-white font-semibold text-sm sm:text-base capitalize transition-colors duration-300">{{ $platform }}</p>
                                <p class="text-gray-500 dark:text-gray-400 text-xs sm:text-sm truncate transition-colors duration-300">{{ parse_url($url, PHP_URL_HOST) ?? $url }}</p>
                            </div>
                            <i class="fas fa-external-link-alt text-gray-400 dark:text-gray-500 group-hover:text-gray-600 dark:group-hover:text-white transition-colors duration-300 text-sm sm:text-base"></i>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="bg-white dark:bg-gray-700/20 rounded-xl p-6 sm:p-8 md:p-12 text-center border border-gray-200 dark:border-gray-600/50 backdrop-blur-sm transition-colors duration-300">
                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-pink-100 dark:from-pink-500/20 to-red-100 dark:to-red-500/20 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4 transition-colors duration-300">
                    <i class="fas fa-share-alt text-pink-500 dark:text-pink-400 text-xl sm:text-2xl md:text-3xl"></i>
                </div>
                <h3 class="text-lg sm:text-xl text-gray-900 dark:text-white font-semibold mb-2 transition-colors duration-300">No social links added</h3>
                <p class="text-sm sm:text-base text-gray-500 dark:text-gray-400 mb-4 sm:mb-6 transition-colors duration-300">Connect your social media profiles to share with others</p>
                <button wire:click="toggleEditMode"
                    class="px-4 py-2 sm:px-6 sm:py-3 bg-gradient-to-r from-pink-600 to-red-600 hover:from-pink-700 hover:to-red-700 text-white rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-pink-500/25 text-sm sm:text-base">
                    <i class="fas fa-plus mr-2"></i> Add Social Links
                </button>
            </div>
        @endif
    </div>

    <!-- Activity Tab -->
    <div x-show="activeTab === 'activity'" x-transition.opacity.duration.300ms class="py-4 sm:py-6 lg:py-8">
        <div class="flex items-center mb-6 sm:mb-8">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center mr-3 sm:mr-4">
                <i class="fas fa-chart-line text-white text-lg sm:text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white transition-colors duration-300">Activity Overview</h2>
                <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400 transition-colors duration-300">Your learning activity and engagement metrics</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 mb-6 sm:mb-8">
            <div class="bg-gradient-to-br from-blue-50 dark:from-blue-500/10 to-blue-100 dark:to-blue-600/10 p-4 sm:p-6 rounded-xl border border-blue-200 dark:border-blue-500/30 backdrop-blur-sm transition-colors duration-300">
                <div class="flex items-center justify-between mb-3 sm:mb-4">
                    <h3 class="text-blue-600 dark:text-blue-300 font-semibold text-sm sm:text-base transition-colors duration-300">Courses Enrolled</h3>
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-blue-100 dark:bg-blue-500/20 rounded-lg flex items-center justify-center transition-colors duration-300">
                        <i class="fas fa-book-open text-blue-500 dark:text-blue-400 text-sm sm:text-base"></i>
                    </div>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-1 sm:mb-2 transition-colors duration-300">{{ $activityStats['courses_enrolled']['total'] ?? 0 }}</p>
                <p class="text-xs sm:text-sm text-blue-600 dark:text-blue-300 transition-colors duration-300">+{{ $activityStats['courses_enrolled']['this_month'] ?? 0 }} this month</p>
            </div>

            <div class="bg-gradient-to-br from-emerald-50 dark:from-emerald-500/10 to-emerald-100 dark:to-emerald-600/10 p-4 sm:p-6 rounded-xl border border-emerald-200 dark:border-emerald-500/30 backdrop-blur-sm transition-colors duration-300">
                <div class="flex items-center justify-between mb-3 sm:mb-4">
                    <h3 class="text-emerald-600 dark:text-emerald-300 font-semibold text-sm sm:text-base transition-colors duration-300">Lessons Completed</h3>
                    <div class="w-8 h-8 sm:w-10 sm:h-10 bg-emerald-100 dark:bg-emerald-500/20 rounded-lg flex items-center justify-center transition-colors duration-300">
                        <i class="fas fa-check-circle text-emerald-500 dark:text-emerald-400 text-sm sm:text-base"></i>
                    </div>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-gray-909 dark:text-white mb-1 sm:mb-2 transition-colors duration-300">{{ $activityStats['lessons_completed']['total'] ?? 0 }}</p>
                <p class="text-xs sm:text-sm text-emerald-600 dark:text-emerald-300 transition-colors duration-300">+{{ $activityStats['lessons_completed']['this_month'] ?? 0 }} this month</p>
            </div>
        </div>

        <!-- Recent Activity Timeline -->
        <div class="bg-white dark:bg-gray-700/30 p-4 sm:p-6 rounded-xl border border-gray-200 dark:border-gray-600/50 backdrop-blur-sm transition-colors duration-300">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 sm:mb-6 flex items-center transition-colors duration-300">
                <i class="fas fa-history text-emerald-500 dark:text-emerald-400 mr-2"></i> Recent Activity
            </h3>

            <div class="space-y-3 sm:space-y-4">
                @forelse($recentActivities ?? [] as $activity)
                    <div class="flex items-start bg-gray-50 dark:bg-gray-800/50 p-3 sm:p-4 rounded-xl border border-gray-100 dark:border-gray-600/30 transition-colors duration-300">
                        <div class="flex-shrink-0 w-8 h-8 sm:w-10 sm:h-10 rounded-lg bg-{{ $activity['color'] ?? 'blue' }}-100 dark:bg-{{ $activity['color'] ?? 'blue' }}-500/20 border border-{{ $activity['color'] ?? 'blue' }}-200 dark:border-{{ $activity['color'] ?? 'blue' }}-500/30 flex items-center justify-center mr-3 sm:mr-4 transition-colors duration-300">
                            <i class="fas fa-{{ $activity['icon'] ?? 'activity' }} text-{{ $activity['color'] ?? 'blue' }}-600 dark:text-{{ $activity['color'] ?? 'blue' }}-400 text-sm sm:text-base"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-white font-medium text-sm sm:text-base">{{ $activity['title'] }}</p>
                            <p class="text-gray-300 text-xs sm:text-sm">{{ $activity['course'] ?? '' }}</p>
                            <p class="text-gray-400 text-xs mt-1">
                                {{ isset($activity['date']) ? Carbon\Carbon::parse($activity['date'])->diffForHumans() : '' }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 sm:py-12 text-gray-400">
                        <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                            <i class="fas fa-inbox text-lg sm:text-2xl"></i>
                        </div>
                        <h4 class="text-base sm:text-lg font-medium text-gray-300 mb-2">No recent activity</h4>
                        <p class="text-sm sm:text-base">Start learning to see your activity here</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Progress Tab -->
    <div x-show="activeTab === 'progress'" x-transition.opacity.duration.300ms class="py-4 sm:py-6 lg:py-8">
        <div class="flex items-center mb-6 sm:mb-8">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-r from-orange-500 to-amber-600 rounded-xl flex items-center justify-center mr-3 sm:mr-4">
                <i class="fas fa-chart-pie text-white text-lg sm:text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-white">Learning Progress</h2>
                <p class="text-sm sm:text-base text-gray-400">Track your learning journey and achievements</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
            <!-- Stats Cards -->
            <div class="bg-gradient-to-br from-blue-500/10 to-blue-600/10 p-4 sm:p-6 rounded-xl border border-blue-500/30 backdrop-blur-sm">
                <div class="flex items-center justify-between mb-3 sm:mb-4">
                    <h3 class="text-blue-300 font-semibold text-sm sm:text-base">Courses</h3>
                    <i class="fas fa-book text-blue-400 text-lg sm:text-xl"></i>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-white">{{ $learningProgress['total_courses'] ?? 0 }}</p>
                <p class="text-xs sm:text-sm text-blue-300">Enrolled</p>
            </div>

            <div class="bg-gradient-to-br from-emerald-500/10 to-emerald-600/10 p-4 sm:p-6 rounded-xl border border-emerald-500/30 backdrop-blur-sm">
                <div class="flex items-center justify-between mb-3 sm:mb-4">
                    <h3 class="text-emerald-300 font-semibold text-sm sm:text-base">Lessons</h3>
                    <i class="fas fa-check-circle text-emerald-400 text-lg sm:text-xl"></i>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-white">{{ $learningProgress['completed_lessons'] ?? 0 }}</p>
                <p class="text-xs sm:text-sm text-emerald-300">Completed</p>
            </div>

            <div class="bg-gradient-to-br from-amber-500/10 to-amber-600/10 p-4 sm:p-6 rounded-xl border border-amber-500/30 backdrop-blur-sm">
                <div class="flex items-center justify-between mb-3 sm:mb-4">
                    <h3 class="text-amber-300 font-semibold text-sm sm:text-base">Resources</h3>
                    <i class="fas fa-bookmark text-amber-400 text-lg sm:text-xl"></i>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-white">{{ $learningProgress['saved_resources'] ?? 0 }}</p>
                <p class="text-xs sm:text-sm text-amber-300">Saved</p>
            </div>

            <div class="bg-gradient-to-br from-pink-500/10 to-pink-600/10 p-4 sm:p-6 rounded-xl border border-pink-500/30 backdrop-blur-sm">
                <div class="flex items-center justify-between mb-3 sm:mb-4">
                    <h3 class="text-pink-300 font-semibold text-sm sm:text-base">Wishlist</h3>
                    <i class="fas fa-heart text-pink-400 text-lg sm:text-xl"></i>
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-white">{{ $learningProgress['wishlist_items'] ?? 0 }}</p>
                <p class="text-xs sm:text-sm text-pink-300">Items</p>
            </div>
        </div>

        <!-- Course Progress -->
        <div class="bg-gray-700/30 p-4 sm:p-6 rounded-xl border border-gray-600/50 backdrop-blur-sm">
            <h3 class="text-lg font-semibold text-white mb-4 sm:mb-6 flex items-center">
                <i class="fas fa-chart-bar text-orange-400 mr-2"></i> Course Progress
            </h3>

            @if (isset($user->courses) && $user->courses->count() > 0)
                <div class="space-y-3 sm:space-y-4">
                    @foreach ($user->courses as $course)
                        @php
                            $totalLessons = $course->allLessons()->count();
                            $completedCount = $user
                                ->completedLessons()
                                ->whereHas('section', function ($query) use ($course) {
                                    $query->where('course_id', $course->id);
                                })
                                ->count();
                            $percentage = $totalLessons > 0 ? ($completedCount / $totalLessons) * 100 : 0;
                        @endphp
                        <div class="bg-gray-800/50 p-3 sm:p-4 rounded-lg border border-gray-600/30">
                            <div class="flex justify-between items-center mb-2 sm:mb-3">
                                <h4 class="text-white font-medium text-sm sm:text-base">{{ $course->title }}</h4>
                                <span class="text-xs sm:text-sm text-gray-400">
                                    {{ $completedCount }}/{{ $totalLessons }} lessons
                                </span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-2 sm:h-3">
                                <div class="bg-gradient-to-r from-orange-500 to-amber-500 h-2 sm:h-3 rounded-full transition-all duration-500"
                                    style="width: {{ $percentage }}%"></div>
                            </div>
                            <div class="flex justify-between items-center mt-1 sm:mt-2">
                                <span class="text-xs text-gray-400">{{ number_format($percentage, 1) }}%
                                    complete</span>
                                @if ($percentage == 100)
                                    <span class="text-xs text-green-400 font-medium">
                                        <i class="fas fa-check-circle mr-1"></i>Completed
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 sm:py-12 text-gray-400">
                    <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                        <i class="fas fa-book-open text-lg sm:text-2xl"></i>
                    </div>
                    <h4 class="text-base sm:text-lg font-medium text-gray-300 mb-2">No courses enrolled</h4>
                    <p class="text-sm sm:text-base">Enroll in courses to track your progress</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Resources Tab -->
    <div x-show="activeTab === 'resources'" x-transition.opacity.duration.300ms class="py-4 sm:py-6 lg:py-8">
        <div class="flex items-center mb-6 sm:mb-8">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-r from-yellow-500 to-amber-600 rounded-xl flex items-center justify-center mr-3 sm:mr-4">
                <i class="fas fa-bookmark text-white text-lg sm:text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-white">Saved Resources</h2>
                <p class="text-sm sm:text-base text-gray-400">Quick access to your bookmarked content</p>
            </div>
        </div>

        @if (isset($savedResources) && $savedResources->count() > 0)
            <div class="space-y-3 sm:space-y-4">
                @foreach ($savedResources as $resource)
                    <div class="bg-gray-700/30 hover:bg-gray-700/50 rounded-xl p-4 sm:p-6 transition-all duration-300 border border-gray-600/50 backdrop-blur-sm group">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-gradient-to-br from-yellow-500/20 to-amber-500/20 border border-yellow-500/30 flex items-center justify-center mr-3 sm:mr-4">
                                @if ($resource->resourceable_type === 'App\Models\Lesson')
                                    <i class="fas fa-play text-blue-400 text-sm sm:text-base"></i>
                                @elseif($resource->resourceable_type === 'App\Models\Assignment')
                                    <i class="fas fa-tasks text-emerald-400 text-sm sm:text-base"></i>
                                @else
                                    <i class="fas fa-file text-yellow-400 text-sm sm:text-base"></i>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-white font-semibold text-sm sm:text-base mb-1">
                                    {{ $resource->resourceable->title ?? 'Untitled' }}</h4>
                                <p class="text-gray-300 text-xs sm:text-sm">{{ $resource->course->title ?? 'General' }}</p>
                                <p class="text-gray-400 text-xs mt-1 sm:mt-2">Saved
                                    {{ $resource->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <a href="#"
                                class="text-gray-400 hover:text-white transition-colors group-hover:translate-x-1 transform duration-200 text-sm sm:text-base">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-gray-700/20 rounded-xl p-6 sm:p-8 md:p-12 text-center border border-gray-600/50 backdrop-blur-sm">
                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-yellow-500/20 to-amber-500/20 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                    <i class="fas fa-bookmark text-yellow-400 text-xl sm:text-2xl md:text-3xl"></i>
                </div>
                <h3 class="text-lg sm:text-xl text-white font-semibold mb-2">No saved resources</h3>
                <p class="text-sm sm:text-base text-gray-400 mb-4 sm:mb-6">Bookmark important lessons and assignments for quick access</p>
            </div>
        @endif
    </div>

    <!-- Wishlist Tab -->
    <div x-show="activeTab === 'wishlist'" x-transition.opacity.duration.300ms class="py-4 sm:py-6 lg:py-8">
        <div class="flex items-center mb-6 sm:mb-8">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-r from-red-500 to-pink-600 rounded-xl flex items-center justify-center mr-3 sm:mr-4">
                <i class="fas fa-heart text-white text-lg sm:text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-white">Wishlist</h2>
                <p class="text-sm sm:text-base text-gray-400">Courses you're planning to take</p>
            </div>
        </div>

        @if (isset($wishlist) && $wishlist->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                @foreach ($wishlist as $item)
                    <div class="bg-gray-700/30 hover:bg-gray-700/50 rounded-xl p-4 sm:p-6 transition-all duration-300 border border-gray-600/50 backdrop-blur-sm group">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-12 h-12 sm:w-16 sm:h-16 rounded-xl overflow-hidden mr-3 sm:mr-4 bg-gray-800/50">
                                @if ($item->course->thumbnail)
                                    <img src="{{ asset('storage/' . $item->course->thumbnail) }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-red-500/20 to-pink-500/20 border border-red-500/30 flex items-center justify-center">
                                        <i class="fas fa-book text-red-400 text-lg sm:text-xl"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="text-white font-semibold text-sm sm:text-base mb-1 sm:mb-2">{{ $item->course->title }}</h4>
                                <p class="text-gray-300 text-xs sm:text-sm mb-2 sm:mb-3">
                                    {{ $item->course->category->name ?? 'No category' }}
                                </p>
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-1 bg-red-500/20 border border-red-500/30 rounded-full text-xs text-red-300">
                                        {{ $item->course->difficulty_level ?? 'Beginner' }}
                                    </span>
                                    <span class="px-2 py-1 bg-pink-500/20 border border-pink-500/30 rounded-full text-xs text-pink-300">
                                        {{ $item->course->estimated_duration_minutes ?? 0 }} mins
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-gray-700/20 rounded-xl p-6 sm:p-8 md:p-12 text-center border border-gray-600/50 backdrop-blur-sm">
                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-red-500/20 to-pink-500/20 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                    <i class="fas fa-heart text-red-400 text-xl sm:text-2xl md:text-3xl"></i>
                </div>
                <h3 class="text-lg sm:text-xl text-white font-semibold mb-2">Your wishlist is empty</h3>
                <p class="text-sm sm:text-base text-gray-400 mb-4 sm:mb-6">Save courses you're interested in for later</p>
                <button class="px-4 py-2 sm:px-6 sm:py-3 bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-red-500/25 text-sm sm:text-base">
                    <i class="fas fa-plus mr-2"></i> Browse Courses
                </button>
            </div>
        @endif
    </div>

    <!-- Certificates Tab -->
    <div x-show="activeTab === 'certificates'" x-transition.opacity.duration.300ms class="py-4 sm:py-6 lg:py-8">
        <div class="flex items-center mb-6 sm:mb-8">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-r from-amber-500 to-yellow-600 rounded-xl flex items-center justify-center mr-3 sm:mr-4">
                <i class="fas fa-certificate text-white text-lg sm:text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-white">Certificates</h2>
                <p class="text-sm sm:text-base text-gray-400">Your earned certificates and achievements</p>
            </div>
        </div>

        @if (isset($certificates) && $certificates->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                @foreach ($certificates as $certificate)
                    <div class="bg-gradient-to-br from-amber-500/10 to-yellow-500/10 border border-amber-500/30 rounded-xl p-4 sm:p-6 hover:shadow-lg hover:shadow-amber-500/20 transition-all duration-300 backdrop-blur-sm">
                        <div class="flex items-start justify-between mb-3 sm:mb-4">
                            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-amber-500/20 to-yellow-500/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-certificate text-amber-400 text-lg sm:text-xl"></i>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-amber-400 font-medium">
                                    {{ $certificate->issued_at->format('M Y') }}</p>
                                <p class="text-xs text-gray-400">Issued</p>
                            </div>
                        </div>

                        <div class="mb-3 sm:mb-4">
                            <h4 class="text-white font-semibold text-sm sm:text-base mb-1">{{ $certificate->course->title }}</h4>
                            <p class="text-gray-300 text-xs sm:text-sm">{{ $certificate->course->category->name ?? 'General' }}
                            </p>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-6 h-6 bg-green-500/20 rounded-full flex items-center justify-center mr-2">
                                    <i class="fas fa-check text-green-400 text-xs"></i>
                                </div>
                                <span class="text-xs text-green-400 font-medium">Verified</span>
                            </div>
                            <div class="flex space-x-2">
                                <button class="px-2 py-1 sm:px-3 sm:py-1.5 bg-amber-500/20 hover:bg-amber-500/30 border border-amber-500/30 text-amber-400 text-xs rounded-lg transition-colors">
                                    <i class="fas fa-eye mr-1"></i> View
                                </button>
                                <button class="px-2 py-1 sm:px-3 sm:py-1.5 bg-gray-700/50 hover:bg-gray-700 border border-gray-600 text-gray-300 text-xs rounded-lg transition-colors">
                                    <i class="fas fa-download mr-1"></i> Download
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-gray-700/20 rounded-xl p-6 sm:p-8 md:p-12 text-center border border-gray-600/50 backdrop-blur-sm">
                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-amber-500/20 to-yellow-500/20 rounded-full flex items-center justify-center mx-auto mb-3 sm:mb-4">
                    <i class="fas fa-certificate text-amber-400 text-xl sm:text-2xl md:text-3xl"></i>
                </div>
                <h3 class="text-lg sm:text-xl text-white font-semibold mb-2">No certificates yet</h3>
                <p class="text-sm sm:text-base text-gray-400 mb-4 sm:mb-6">Complete courses to earn certificates and showcase your achievements</p>
                <button class="px-4 py-2 sm:px-6 sm:py-3 bg-gradient-to-r from-amber-600 to-yellow-600 hover:from-amber-700 hover:to-yellow-700 text-white rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-amber-500/25 text-sm sm:text-base">
                    <i class="fas fa-graduation-cap mr-2"></i> Start Learning
                </button>
            </div>
        @endif
    </div>

    <!-- Settings Tab -->
    <div x-show="activeTab === 'settings'" x-transition.opacity.duration.300ms class="py-4 sm:py-6 lg:py-8">
        <div class="flex items-center mb-6 sm:mb-8">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-r from-gray-500 to-gray-600 rounded-xl flex items-center justify-center mr-3 sm:mr-4">
                <i class="fas fa-cog text-white text-lg sm:text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-white">Account Settings</h2>
                <p class="text-sm sm:text-base text-gray-400">Manage your account preferences and privacy</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 sm:gap-8">
            <!-- Account Security -->
            <div class="bg-gray-700/30 p-4 sm:p-6 rounded-xl border border-gray-600/50 backdrop-blur-sm">
                <h3 class="text-lg font-semibold text-white mb-3 sm:mb-4 flex items-center">
                    <i class="fas fa-shield-alt text-green-400 mr-2"></i>
                    Account Security
                </h3>

                <div class="space-y-3 sm:space-y-4">
                    <div class="flex items-center justify-between p-3 sm:p-4 bg-gray-800/50 rounded-lg border border-gray-600/30">
                        <div>
                            <p class="text-white font-medium text-sm sm:text-base">Two-Factor Authentication</p>
                            <p class="text-gray-400 text-xs sm:text-sm">Add extra security to your account</p>
                        </div>
                        <button class="px-3 py-2 sm:px-4 sm:py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs sm:text-sm font-medium transition-colors">
                            Enable
                        </button>
                    </div>

                    <div class="flex items-center justify-between p-3 sm:p-4 bg-gray-800/50 rounded-lg border border-gray-600/30">
                        <div>
                            <p class="text-white font-medium text-sm sm:text-base">Change Password</p>
                            <p class="text-gray-400 text-xs sm:text-sm">Update your account password</p>
                        </div>
                        <button class="px-3 py-2 sm:px-4 sm:py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-xs sm:text-sm font-medium transition-colors">
                            Change
                        </button>
                    </div>

                    <div class="flex items-center justify-between p-3 sm:p-4 bg-gray-800/50 rounded-lg border border-gray-600/30">
                        <div>
                            <p class="text-white font-medium text-sm sm:text-base">Login Sessions</p>
                            <p class="text-gray-400 text-xs sm:text-sm">Manage active login sessions</p>
                        </div>
                        <button class="px-3 py-2 sm:px-4 sm:py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs sm:text-sm font-medium transition-colors">
                            View
                        </button>
                    </div>
                </div>
            </div>

            <!-- Privacy & Notifications -->
            <div class="bg-gray-700/30 p-4 sm:p-6 rounded-xl border border-gray-600/50 backdrop-blur-sm">
                <h3 class="text-lg font-semibold text-white mb-3 sm:mb-4 flex items-center">
                    <i class="fas fa-bell text-purple-400 mr-2"></i>
                    Notifications & Privacy
                </h3>

                <div class="space-y-3 sm:space-y-4">
                    <div class="flex items-center justify-between p-3 sm:p-4 bg-gray-800/50 rounded-lg border border-gray-600/30">
                        <div>
                            <p class="text-white font-medium text-sm sm:text-base">Email Notifications</p>
                            <p class="text-gray-400 text-xs sm:text-sm">Course updates and announcements</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" class="sr-only peer" checked>
                            <div class="w-9 h-5 sm:w-11 sm:h-6 bg-gray-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 sm:after:h-5 sm:after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>

                    <div class="flex items-center justify-between p-3 sm:p-4 bg-gray-800/50 rounded-lg border border-gray-600/30">
                        <div>
                            <p class="text-white font-medium text-sm sm:text-base">Profile Visibility</p>
                            <p class="text-gray-400 text-xs sm:text-sm">Who can see your profile</p>
                        </div>
                        <select class="bg-gray-700 border border-gray-600 text-white text-xs sm:text-sm rounded-lg px-2 py-1 sm:px-3 sm:py-2">
                            <option>Public</option>
                            <option>Private</option>
                            <option>Friends Only</option>
                        </select>
                    </div>

                    <div class="flex items-center justify-between p-3 sm:p-4 bg-gray-800/50 rounded-lg border border-gray-600/30">
                        <div>
                            <p class="text-white font-medium text-sm sm:text-base">Data Export</p>
                            <p class="text-gray-400 text-xs sm:text-sm">Download your account data</p>
                        </div>
                        <button class="px-3 py-2 sm:px-4 sm:py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-xs sm:text-sm font-medium transition-colors">
                            Export
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="mt-6 sm:mt-8 bg-red-500/10 p-4 sm:p-6 rounded-xl border border-red-500/30 backdrop-blur-sm">
            <h3 class="text-lg font-semibold text-red-300 mb-3 sm:mb-4 flex items-center">
                <i class="fas fa-exclamation-triangle text-red-400 mr-2"></i>
                Danger Zone
            </h3>

            <div class="space-y-3 sm:space-y-4">
                <div class="flex items-center justify-between p-3 sm:p-4 bg-red-900/20 rounded-lg border border-red-500/30">
                    <div>
                        <p class="text-red-300 font-medium text-sm sm:text-base">Deactivate Account</p>
                        <p class="text-red-200/70 text-xs sm:text-sm">Temporarily disable your account</p>
                    </div>
                    <button class="px-3 py-2 sm:px-4 sm:py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs sm:text-sm font-medium transition-colors">
                        Deactivate
                    </button>
                </div>

                <div class="flex items-center justify-between p-3 sm:p-4 bg-red-900/20 rounded-lg border border-red-500/30">
                    <div>
                        <p class="text-red-300 font-medium text-sm sm:text-base">Delete Account</p>
                        <p class="text-red-200/70 text-xs sm:text-sm">Permanently delete your account and data</p>
                    </div>
                    <button class="px-3 py-2 sm:px-4 sm:py-2 bg-red-700 hover:bg-red-800 text-white rounded-lg text-xs sm:text-sm font-medium transition-colors">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>