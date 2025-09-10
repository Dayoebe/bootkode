<div class="tab-content">
    <div class="mb-12">
        <div class="flex items-center mb-6">
            <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900 rounded-2xl flex items-center justify-center mr-6">
                <i class="fas fa-handshake text-blue-600 dark:text-blue-300 text-2xl"></i>
            </div>
            <div>
                <h2 class="text-4xl font-bold text-gray-900 dark:text-white">Mentorship Impact</h2>
                <p class="text-xl text-blue-600 font-semibold">Guide, Support, Transform Lives</p>
            </div>
        </div>
        <p class="text-lg text-gray-600 dark:text-gray-400 leading-relaxed max-w-4xl">
            Join our network of {{ number_format($stats['totalMentors']) }} expert mentors making real impact. Guide talented individuals 
            from {{ number_format($stats['totalStudents']) }} students through their learning journey and help them land their dream jobs.
        </p>
    </div>

    <!-- Mentor Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-12">
        <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-2xl text-center">
            <div class="text-3xl font-bold text-blue-600 mb-2">{{ number_format($stats['totalMentors']) }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Active Mentors</div>
        </div>
        <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-2xl text-center">
            <div class="text-3xl font-bold text-blue-600 mb-2">{{ number_format($stats['totalStudents']) }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Students to Mentor</div>
        </div>
        <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-2xl text-center">
            <div class="text-3xl font-bold text-blue-600 mb-2">{{ $stats['jobPlacementRate'] }}%</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Job Placement Rate</div>
        </div>
        <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-2xl text-center">
            <div class="text-3xl font-bold text-blue-600 mb-2">{{ $stats['mentorRating'] }}/5</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Mentor Rating</div>
        </div>
    </div>

    <!-- Mentor Roadmap -->
    <div class="mb-12">
        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-8 text-center">Your Mentoring Journey</h3>
        <div class="relative">
            <div class="absolute left-1/2 transform -translate-x-px h-full w-0.5 bg-blue-200 dark:bg-blue-800"></div>
            
            <!-- Step 1 -->
            <div class="relative flex items-center mb-12">
                <div class="flex-1 pr-8 text-right">
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-2xl border border-blue-100 dark:border-blue-800">
                        <div class="flex items-center justify-end mb-4">
                            <h4 class="text-xl font-bold text-gray-900 dark:text-white mr-3">Profile & Specialization</h4>
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-800 rounded-lg flex items-center justify-center">
                                <i class="fas fa-id-card text-blue-600 dark:text-blue-300"></i>
                            </div>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 mb-4">
                            Create your mentor profile highlighting your expertise, experience, and mentoring style. 
                            Define your specializations and availability.
                        </p>
                        <div class="space-y-3">
                            <div class="bg-white dark:bg-gray-700 p-3 rounded-lg">
                                <div class="font-medium text-blue-600">Specializations</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Web Dev, Mobile, DevOps, Data Science</div>
                            </div>
                            <div class="bg-white dark:bg-gray-700 p-3 rounded-lg">
                                <div class="font-medium text-blue-600">Experience Level</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">5+ years industry experience required</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg border-4 border-white dark:border-gray-800 shadow-lg z-10">
                    1
                </div>
                <div class="flex-1 pl-8"></div>
            </div>

            <!-- Step 2 -->
            <div class="relative flex items-center mb-12">
                <div class="flex-1 pr-8"></div>
                <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg border-4 border-white dark:border-gray-800 shadow-lg z-10">
                    2
                </div>
                <div class="flex-1 pl-8">
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-2xl border border-blue-100 dark:border-blue-800">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-800 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-search text-blue-600 dark:text-blue-300"></i>
                            </div>
                            <h4 class="text-xl font-bold text-gray-900 dark:text-white">Smart Mentee Matching</h4>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 mb-4">
                            Our AI-powered system matches you with mentees from our {{ number_format($stats['totalStudents']) }} students 
                            based on skills, goals, and compatibility.
                        </p>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span class="text-gray-600 dark:text-gray-400">Skill-based matching</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span class="text-gray-600 dark:text-gray-400">Goal alignment assessment</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span class="text-gray-600 dark:text-gray-400">Schedule compatibility</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="relative flex items-center mb-12">
                <div class="flex-1 pr-8 text-right">
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-2xl border border-blue-100 dark:border-blue-800">
                        <div class="flex items-center justify-end mb-4">
                            <h4 class="text-xl font-bold text-gray-900 dark:text-white mr-3">Active Mentoring</h4>
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-800 rounded-lg flex items-center justify-center">
                                <i class="fas fa-video text-blue-600 dark:text-blue-300"></i>
                            </div>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 mb-4">
                            Conduct regular one-on-one sessions via video calls, review code, provide career guidance, 
                            and help mentees overcome technical and professional challenges.
                        </p>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div class="bg-white dark:bg-gray-700 p-3 rounded-lg">
                                <div class="font-medium text-gray-900 dark:text-white">Session Types</div>
                                <div class="text-gray-600 dark:text-gray-400">1-on-1, Group, Code Review</div>
                            </div>
                            <div class="bg-white dark:bg-gray-700 p-3 rounded-lg">
                                <div class="font-medium text-gray-900 dark:text-white">Flexibility</div>
                                <div class="text-gray-600 dark:text-gray-400">Your schedule, your rules</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg border-4 border-white dark:border-gray-800 shadow-lg z-10">
                    3
                </div>
                <div class="flex-1 pl-8"></div>
            </div>

            <!-- Step 4 -->
            <div class="relative flex items-center mb-12">
                <div class="flex-1 pr-8"></div>
                <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg border-4 border-white dark:border-gray-800 shadow-lg z-10">
                    4
                </div>
                <div class="flex-1 pl-8">
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-2xl border border-blue-100 dark:border-blue-800">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-800 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-briefcase text-blue-600 dark:text-blue-300"></i>
                            </div>
                            <h4 class="text-xl font-bold text-gray-900 dark:text-white">Career Development</h4>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 mb-4">
                            Help mentees build professional portfolios, prepare for interviews, navigate job searches, 
                            and make strategic career decisions in the tech industry.
                        </p>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between bg-white dark:bg-gray-700 p-3 rounded-lg">
                                <span class="text-gray-700 dark:text-gray-300">Portfolio Reviews</span>
                                <i class="fas fa-check text-green-500"></i>
                            </div>
                            <div class="flex items-center justify-between bg-white dark:bg-gray-700 p-3 rounded-lg">
                                <span class="text-gray-700 dark:text-gray-300">Interview Preparation</span>
                                <i class="fas fa-check text-green-500"></i>
                            </div>
                            <div class="flex items-center justify-between bg-white dark:bg-gray-700 p-3 rounded-lg">
                                <span class="text-gray-700 dark:text-gray-300">Industry Networking</span>
                                <i class="fas fa-check text-green-500"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 5 -->
            <div class="relative flex items-center">
                <div class="flex-1 pr-8 text-right">
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-2xl border border-blue-100 dark:border-blue-800">
                        <div class="flex items-center justify-end mb-4">
                            <h4 class="text-xl font-bold text-gray-900 dark:text-white mr-3">Impact & Growth</h4>
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-800 rounded-lg flex items-center justify-center">
                                <i class="fas fa-trophy text-blue-600 dark:text-blue-300"></i>
                            </div>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 mb-4">
                            Track your mentees' progress, celebrate their achievements, and build your reputation 
                            as a top mentor. Earn rewards for successful placements.
                        </p>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center justify-end">
                                <span class="text-gray-600 dark:text-gray-400 mr-2">Success metrics tracking</span>
                                <i class="fas fa-check text-green-500"></i>
                            </div>
                            <div class="flex items-center justify-end">
                                <span class="text-gray-600 dark:text-gray-400 mr-2">Mentor reputation system</span>
                                <i class="fas fa-check text-green-500"></i>
                            </div>
                            <div class="flex items-center justify-end">
                                <span class="text-gray-600 dark:text-gray-400 mr-2">Performance bonuses</span>
                                <i class="fas fa-check text-green-500"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center text-white font-bold text-lg border-4 border-white dark:border-gray-800 shadow-lg z-10">
                    <i class="fas fa-star"></i>
                </div>
                <div class="flex-1 pl-8"></div>
            </div>
        </div>
    </div>

    <!-- Mentor Rewards -->
    <div class="grid md:grid-cols-3 gap-8 mt-12">
        <div class="bg-blue-50 dark:bg-blue-900/10 p-6 rounded-2xl border border-blue-100 dark:border-blue-800">
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-800 rounded-xl flex items-center justify-center mb-4">
                <i class="fas fa-heart text-blue-600 dark:text-blue-300 text-xl"></i>
            </div>
            <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Meaningful Impact</h4>
            <p class="text-gray-600 dark:text-gray-400">Shape careers and transform lives while contributing to Africa's tech ecosystem growth and development.</p>
        </div>
        <div class="bg-blue-50 dark:bg-blue-900/10 p-6 rounded-2xl border border-blue-100 dark:border-blue-800">
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-800 rounded-xl flex items-center justify-center mb-4">
                <i class="fas fa-money-bill-wave text-blue-600 dark:text-blue-300 text-xl"></i>
            </div>
            <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Flexible Earnings</h4>
            <p class="text-gray-600 dark:text-gray-400">Set your own rates, choose your schedule, and earn additional income through performance bonuses and referrals.</p>
        </div>
        <div class="bg-blue-50 dark:bg-blue-900/10 p-6 rounded-2xl border border-blue-100 dark:border-blue-800">
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-800 rounded-xl flex items-center justify-center mb-4">
                <i class="fas fa-network-wired text-blue-600 dark:text-blue-300 text-xl"></i>
            </div>
            <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Professional Network</h4>
            <p class="text-gray-600 dark:text-gray-400">Connect with other industry leaders, expand your professional network, and stay current with tech trends.</p>
        </div>
    </div>
</div>