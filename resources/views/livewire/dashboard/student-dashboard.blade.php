<div>
    <!-- Dashboard Header -->
    <div class="bg-gradient-to-r from-blue-600 to-purple-700 p-8 rounded-xl shadow-lg border border-gray-700 flex flex-col md:flex-row items-center justify-between animate-fade-in-down">
        <div class="text-white">
            <h1 class="text-3xl font-extrabold mb-2">Welcome Back, {{ auth()->user()->name }}!</h1>
            <p class="text-blue-100 text-lg">Your learning journey continues. You've completed {{ $quickStats['lessonsCompleted'] }} lessons so far!</p>
            @if($quickStats['studyStreak'] > 0)
                <div class="mt-3 flex items-center">
                    <span class="text-yellow-300 text-xl mr-2">🔥</span>
                    <span class="text-yellow-200 font-semibold">{{ $quickStats['studyStreak'] }} day study streak!</span>
                </div>
            @endif
        </div>
        <div class="mt-4 md:mt-0">
            <span class="text-6xl animate-pulse">👨‍🎓</span>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="px-4 py-6 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Quick Stats -->
            @foreach([
                ['title' => 'Enrolled Courses', 'value' => $quickStats['enrolledCourses'], 'icon' => '📚', 'color' => 'blue'],
                ['title' => 'Certificates', 'value' => $quickStats['certificatesEarned'], 'icon' => '🏆', 'color' => 'green'],
                ['title' => 'Active Courses', 'value' => $quickStats['activeCourses'], 'icon' => '🎯', 'color' => 'purple'],
                ['title' => 'Lessons Completed', 'value' => $quickStats['lessonsCompleted'], 'icon' => '✔️', 'color' => 'yellow'],
            ] as $stat)
            <div class="bg-gray-800 rounded-lg shadow p-6 border border-gray-700 transform transition-transform hover:scale-105">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-400 text-sm">{{ $stat['title'] }}</p>
                        <p class="text-2xl font-bold text-white mt-1">{{ $stat['value'] }}</p>
                    </div>
                    <span class="text-3xl">{{ $stat['icon'] }}</span>
                </div>
            </div>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Learning Statistics -->
            <div class="bg-gray-800 rounded-lg shadow p-6 border border-gray-700 lg:col-span-1">
                <h2 class="text-xl font-bold text-white mb-4 flex items-center">
                    <span class="mr-2">📊</span> Learning Statistics
                </h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400">Last 7 Days</span>
                        <span class="font-bold text-white">{{ $learningStats['recentLessons'] }} lessons</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400">Avg. Study Time</span>
                        <span class="font-bold text-white">{{ $learningStats['avgStudyTime'] }} min/day</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400">Consistency</span>
                        <span class="font-bold text-white">{{ $learningStats['consistencyScore'] }}%</span>
                    </div>
                </div>
                <div class="mt-6 pt-4 border-t border-gray-700">
                    <a href="{{ route('student.learning-analytics') }}" class="text-blue-400 hover:text-blue-300 text-sm flex items-center">
                        View detailed analytics
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Recent Announcements -->
            <div class="bg-gray-800 rounded-lg shadow p-6 border border-gray-700 lg:col-span-2">
                <h2 class="text-xl font-bold text-white mb-4 flex items-center">
                    <span class="mr-2">📣</span> Recent Announcements
                </h2>
                <div class="space-y-4">
                    @forelse($recentAnnouncements as $announcement)
                    <div class="bg-gray-700 p-4 rounded-lg hover:bg-gray-600 transition-colors duration-200">
                        <h3 class="text-white font-semibold">{{ $announcement->title }}</h3>
                        <p class="text-gray-400 text-sm mt-1">
                            {{ Str::limit($announcement->content, 120) }}
                        </p>
                        <div class="flex justify-between items-center mt-3">
                            <span class="text-xs text-gray-500">Published: {{ $announcement->published_at->format('F d, Y') }}</span>
                            @if($announcement->course)
                            <span class="text-xs bg-blue-900 text-blue-200 px-2 py-1 rounded">{{ $announcement->course->title }}</span>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-gray-500 py-4">
                        <span class="text-4xl">📢</span>
                        <p class="mt-2">No recent announcements</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Next Lessons & Achievements -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Next Lessons -->
            <div class="bg-gray-800 rounded-lg shadow p-6 border border-gray-700">
                <h2 class="text-xl font-bold text-white mb-4 flex items-center">
                    <span class="mr-2">⏭️</span> Next Lessons
                </h2>
                <div class="space-y-4">
                    @forelse($nextLessons as $lesson)
                    <div class="bg-gray-700 p-4 rounded-lg flex items-start justify-between hover:bg-gray-600 transition-colors duration-200">
                        <div>
                            <h3 class="text-white font-semibold">{{ $lesson->lesson_title }}</h3>
                            <p class="text-gray-400 text-sm mt-1">{{ $lesson->course_title }}</p>
                            <span class="text-xs text-red-400 mt-2 block">Suggested completion: {{ $lesson->due_date }}</span>
                        </div>
                        <a href="{{ route('course.view', ['course' => $lesson->course_id]) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg text-sm">
                            Continue
                        </a>
                    </div>
                    @empty
                    <div class="text-center text-gray-500 py-4">
                        <span class="text-4xl">🎯</span>
                        <p class="mt-2">No upcoming lessons</p>
                        <a href="{{ route('student.course-catalog') }}" class="text-blue-400 hover:text-blue-300 text-sm mt-2 inline-block">
                            Browse courses
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Achievements -->
            <div class="bg-gray-800 rounded-lg shadow p-6 border border-gray-700">
                <h2 class="text-xl font-bold text-white mb-4 flex items-center">
                    <span class="mr-2">🏆</span> Recent Achievements
                </h2>
                <div class="space-y-4">
                    @forelse($recentAchievements as $achievement)
                    <div class="bg-gray-700 p-4 rounded-lg flex items-center hover:bg-gray-600 transition-colors duration-200">
                        <span class="text-2xl mr-3">{{ $achievement->achievement_icon }}</span>
                        <div>
                            <h3 class="text-white font-semibold">{{ $achievement->achievement_name }}</h3>
                            <p class="text-gray-400 text-sm mt-1">{{ $achievement->achievement_description }}</p>
                            <span class="text-xs text-gray-500 mt-2 block">Earned: {{ $achievement->earned_at->format('M j, Y') }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-gray-500 py-4">
                        <span class="text-4xl">⭐</span>
                        <p class="mt-2">No achievements yet</p>
                        <p class="text-sm mt-1">Complete lessons to earn achievements!</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Enrolled Courses -->
        <div class="mb-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold text-white">Your Courses</h2>
                <a href="{{ route('student.enrolled-courses') }}" class="text-blue-400 hover:text-blue-300 text-sm">
                    View all courses
                </a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse(array_slice($enrolledCourses, 0, 3) as $course)
                <div class="bg-gray-800 rounded-lg shadow overflow-hidden border border-gray-700 transform transition-transform hover:scale-105">
                    @if($course->thumbnail)
                    <div class="h-40 bg-gray-700 overflow-hidden">
                        <img src="{{ $course->thumbnail }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                    </div>
                    @endif
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-white font-semibold text-lg">{{ Str::limit($course->title, 40) }}</h3>
                            @if ($course->status == 'Completed')
                                <span class="px-2 py-1 bg-purple-500 text-white text-xs rounded-full">{{ $course->status }}</span>
                            @elseif ($course->status == 'In Progress')
                                <span class="px-2 py-1 bg-green-500 text-white text-xs rounded-full">{{ $course->status }}</span>
                            @else
                                <span class="px-2 py-1 bg-yellow-500 text-white text-xs rounded-full">{{ $course->status }}</span>
                            @endif
                        </div>
                        <p class="text-gray-400 text-sm mb-4">
                            {{ Str::limit($course->subtitle, 80) }}
                        </p>
                        <div class="w-full bg-gray-700 rounded-full h-2.5 mb-4">
                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $course->completion_percentage }}%"></div>
                        </div>
                        <div class="flex justify-between items-center mb-4">
                            <p class="text-sm text-gray-400">{{ $course->completion_percentage }}% Complete</p>
                            <p class="text-xs text-gray-500">Last accessed: {{ $course->last_accessed ? $course->last_accessed->diffForHumans() : 'Never' }}</p>
                        </div>
                        @if ($course->is_completed)
                            <a href="{{ route('dashboard.certificates') }}" class="w-full text-center block px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors duration-200">
                                View Certificate
                            </a>
                        @else
                            <a href="{{ route('student.enrolled-courses') }}" class="w-full text-center block px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200">
                                {{ $course->completion_percentage > 0 ? 'Continue Course' : 'Start Course' }}
                            </a>
                        @endif
                    </div>
                </div>
                @empty
                <div class="col-span-3 text-center py-10">
                    <span class="text-6xl">📚</span>
                    <p class="text-gray-500 mt-4">You are not currently enrolled in any courses.</p>
                    <a href="{{ route('student.course-catalog') }}" class="inline-block mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200">
                        Browse Course Catalog
                    </a>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Recommended Courses & Support -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Recommended Courses -->
            <div class="bg-gray-800 rounded-lg shadow p-6 border border-gray-700 lg:col-span-2">
                <h2 class="text-xl font-bold text-white mb-4 flex items-center">
                    <span class="mr-2">🌟</span> Recommended For You
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($recommendedCourses as $course)
                    <div class="bg-gray-700 rounded-lg p-4 hover:bg-gray-600 transition-colors duration-200">
                        <h3 class="text-white font-semibold">{{ Str::limit($course->title, 30) }}</h3>
                        <p class="text-gray-400 text-sm mt-1">{{ Str::limit($course->subtitle, 60) }}</p>
                        <div class="flex justify-between items-center mt-3">
                            <span class="text-xs text-gray-500">{{ $course->difficulty_level }}</span>
                            <span class="text-xs px-2 py-1 bg-green-800 text-green-200 rounded">{{ $course->is_free ? 'Free' : 'Premium' }}</span>
                        </div>
                        <a href="{{ route('course.view', ['course' => $course->slug]) }}" class="block mt-3 text-center text-sm bg-gray-600 hover:bg-gray-500 text-white py-1 rounded">
                            View Course
                        </a>
                    </div>
                    @empty
                    <div class="col-span-2 text-center text-gray-500 py-4">
                        <span class="text-4xl">🎓</span>
                        <p class="mt-2">No recommendations available</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Support & System Status -->
            <div class="space-y-6">
                <!-- Support Ticket Summary -->
                <div class="bg-gray-800 rounded-lg shadow p-6 border border-gray-700">
                    <h2 class="text-xl font-bold text-white mb-4 flex items-center">
                        <span class="mr-2">🛠️</span> Support & Help
                    </h2>
                    <h3 class="font-semibold text-white mb-2">My Tickets</h3>
                    <ul class="space-y-3 mb-4">
                        @forelse($supportTickets as $ticket)
                        <li class="flex items-center justify-between text-gray-400 p-2 bg-gray-700 rounded">
                            <span>Ticket #{{ $ticket->id }}</span>
                            <span class="text-sm font-semibold
                                @if ($ticket->status == 'resolved')
                                    text-green-400
                                @elseif ($ticket->status == 'pending')
                                    text-yellow-400
                                @else
                                    text-red-400
                                @endif
                            ">{{ ucfirst($ticket->status) }}</span>
                        </li>
                        @empty
                        <li class="text-gray-500 text-center py-2">No active support tickets</li>
                        @endforelse
                    </ul>
                    <a href="{{ route('help.support') }}" class="block text-center mt-4 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors duration-200">
                        Get Help
                    </a>
                </div>

                <!-- System Status -->
                <div class="bg-gray-800 rounded-lg shadow p-6 border border-gray-700">
                    <h2 class="text-xl font-bold text-white mb-4 flex items-center">
                        <span class="mr-2">📈</span> System Status
                    </h2>
                    <div class="space-y-3">
                        @forelse($systemStatus as $status)
                        <div class="bg-gray-700 p-3 rounded-lg">
                            <div class="flex justify-between items-center">
                                <span class="text-white font-medium">{{ $status->service }}</span>
                                <span class="text-xs px-2 py-1 rounded 
                                    @if($status->status == 'operational') bg-green-800 text-green-200
                                    @elseif($status->status == 'degraded') bg-yellow-800 text-yellow-200
                                    @else bg-red-800 text-red-200 @endif">
                                    {{ ucfirst($status->status) }}
                                </span>
                            </div>
                            <p class="text-gray-400 text-sm mt-1">{{ $status->title }}</p>
                        </div>
                        @empty
                        <div class="text-center text-gray-500 py-2">
                            <span class="text-green-500 text-xl">✔️</span>
                            <p class="mt-1">All systems operational</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');
    body {
        font-family: 'Inter', sans-serif;
    }
    .animate-fade-in-down {
        animation: fadeInDown 0.5s ease-out;
    }
    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    </style>
</div>