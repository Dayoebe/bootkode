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

    <!-- Changed to responsive two-column layout -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 md:gap-8">
        <!-- Education & Career Section -->
        <div class="space-y-4 sm:space-y-6">
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
        </div>

        <!-- Skills & Interests Section -->
        <div class="space-y-4 sm:space-y-6">
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
@include('livewire.user-management.profile.tab.wishlist-tab')

    <!-- Certificates Tab -->
    @include('livewire.user-management.profile.tab.certificate-tab')
    
    
    <!-- Settings Tab -->
    @include('livewire.user-management.profile.tab.settings-tab')

</div>