<div class="tab-content">
    <div class="mb-12">
        <div class="flex items-center mb-6">
            <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900 rounded-2xl flex items-center justify-center mr-6">
                <i class="fas fa-user-graduate text-blue-600 dark:text-blue-300 text-2xl"></i>
            </div>
            <div>
                <h2 class="text-4xl font-bold text-gray-900 dark:text-white">Student Journey</h2>
                <p class="text-xl text-blue-600 font-semibold">Code, Certify, Conquer</p>
            </div>
        </div>
        <p class="text-lg text-gray-600 dark:text-gray-400 leading-relaxed max-w-4xl">
            Join {{ number_format($stats['totalStudents']) }} students who are mastering in-demand digital skills. 
            Our comprehensive learning ecosystem combines world-class courses, hands-on projects, and personalized mentorship 
            to ensure your success in the global digital economy.
        </p>
    </div>

    <!-- Student Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-12">
        <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-2xl text-center">
            <div class="text-3xl font-bold text-blue-600 mb-2">{{ number_format($stats['totalEnrollments']) }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Total Enrollments</div>
        </div>
        <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-2xl text-center">
            <div class="text-3xl font-bold text-blue-600 mb-2">{{ number_format($stats['completedCourses']) }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Completed Courses</div>
        </div>
        <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-2xl text-center">
            <div class="text-3xl font-bold text-blue-600 mb-2">{{ number_format($stats['issuedCertificates']) }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Certificates Issued</div>
        </div>
        <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-2xl text-center">
            <div class="text-3xl font-bold text-blue-600 mb-2">{{ $stats['successRate'] }}%</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Success Rate</div>
        </div>
    </div>

    <!-- Student Roadmap -->
    <div class="mb-12">
        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-8 text-center">Your Learning Roadmap</h3>
        <div class="relative">
            <!-- Timeline Line -->
            <div class="absolute left-1/2 transform -translate-x-px h-full w-0.5 bg-blue-200 dark:bg-blue-800"></div>
            
            <!-- Step 1 -->
            <div class="relative flex items-center mb-12">
                <div class="flex-1 pr-8 text-right">
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-2xl border border-blue-100 dark:border-blue-800">
                        <div class="flex items-center justify-end mb-4">
                            <h4 class="text-xl font-bold text-gray-900 dark:text-white mr-3">Discovery & Enrollment</h4>
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-800 rounded-lg flex items-center justify-center">
                                <i class="fas fa-compass text-blue-600 dark:text-blue-300"></i>
                            </div>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 mb-4">
                            Explore our library of {{ number_format($stats['totalCourses']) }} courses and career roadmaps. Take our skill assessment 
                            quiz to find the perfect learning path for your goals.
                        </p>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center justify-end">
                                <span class="text-gray-600 dark:text-gray-400 mr-2">Browse {{ number_format($stats['totalCourses']) }} courses</span>
                                <i class="fas fa-check text-green-500"></i>
                            </div>
                            <div class="flex items-center justify-end">
                                <span class="text-gray-600 dark:text-gray-400 mr-2">Take skill assessment</span>
                                <i class="fas fa-check text-green-500"></i>
                            </div>
                            <div class="flex items-center justify-end">
                                <span class="text-gray-600 dark:text-gray-400 mr-2">Get personalized recommendations</span>
                                <i class="fas fa-check text-green-500"></i>
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
                                <i class="fas fa-book-open text-blue-600 dark:text-blue-300"></i>
                            </div>
                            <h4 class="text-xl font-bold text-gray-900 dark:text-white">Active Learning & Practice</h4>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 mb-4">
                            Engage with {{ number_format($stats['totalLessons']) }} interactive lessons including HD videos, downloadable resources, and 
                            hands-on coding exercises. Build real-world projects to strengthen your portfolio.
                        </p>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span class="text-gray-600 dark:text-gray-400">Watch expert-led video tutorials</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span class="text-gray-600 dark:text-gray-400">Complete coding challenges</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span class="text-gray-600 dark:text-gray-400">Build portfolio projects</span>
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
                            <h4 class="text-xl font-bold text-gray-900 dark:text-white mr-3">Assessment & Validation</h4>
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-800 rounded-lg flex items-center justify-center">
                                <i class="fas fa-tasks text-blue-600 dark:text-blue-300"></i>
                            </div>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 mb-4">
                            Demonstrate your knowledge through {{ number_format($stats['totalAssessments']) }} comprehensive assessments, peer code reviews, 
                            and capstone projects that mirror real industry challenges.
                        </p>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center justify-end">
                                <span class="text-gray-600 dark:text-gray-400 mr-2">Pass skill assessments</span>
                                <i class="fas fa-check text-green-500"></i>
                            </div>
                            <div class="flex items-center justify-end">
                                <span class="text-gray-600 dark:text-gray-400 mr-2">Submit capstone project</span>
                                <i class="fas fa-check text-green-500"></i>
                            </div>
                            <div class="flex items-center justify-end">
                                <span class="text-gray-600 dark:text-gray-400 mr-2">Receive peer feedback</span>
                                <i class="fas fa-check text-green-500"></i>
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
                                <i class="fas fa-certificate text-blue-600 dark:text-blue-300"></i>
                            </div>
                            <h4 class="text-xl font-bold text-gray-900 dark:text-white">Certification & Recognition</h4>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 mb-4">
                            Earn industry-recognized certificates that validate your skills. {{ number_format($stats['issuedCertificates']) }} certificates 
                            have already been issued to successful graduates.
                        </p>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span class="text-gray-600 dark:text-gray-400">Receive digital certificate</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span class="text-gray-600 dark:text-gray-400">LinkedIn verification</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span class="text-gray-600 dark:text-gray-400">Career placement support</span>
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
                            <h4 class="text-xl font-bold text-gray-900 dark:text-white mr-3">Career Launch & Growth</h4>
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-800 rounded-lg flex items-center justify-center">
                                <i class="fas fa-rocket text-blue-600 dark:text-blue-300"></i>
                            </div>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 mb-4">
                            Join our alumni network of {{ number_format($stats['completedCourses']) }} successful graduates, access exclusive job opportunities, 
                            and continue growing your skills with our {{ number_format($stats['totalMentors']) }} expert mentors.
                        </p>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center justify-end">
                                <span class="text-gray-600 dark:text-gray-400 mr-2">Alumni network access</span>
                                <i class="fas fa-check text-green-500"></i>
                            </div>
                            <div class="flex items-center justify-end">
                                <span class="text-gray-600 dark:text-gray-400 mr-2">Job placement assistance</span>
                                <i class="fas fa-check text-green-500"></i>
                            </div>
                            <div class="flex items-center justify-end">
                                <span class="text-gray-600 dark:text-gray-400 mr-2">Continuous learning path</span>
                                <i class="fas fa-check text-green-500"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center text-white font-bold text-lg border-4 border-white dark:border-gray-800 shadow-lg z-10">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="flex-1 pl-8"></div>
            </div>
        </div>
    </div>

    <!-- Support Features -->
    <div class="grid md:grid-cols-3 gap-8 mt-12">
        <div class="bg-blue-50 dark:bg-blue-900/10 p-6 rounded-2xl border border-blue-100 dark:border-blue-800">
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-800 rounded-xl flex items-center justify-center mb-4">
                <i class="fas fa-users text-blue-600 dark:text-blue-300 text-xl"></i>
            </div>
            <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Community Support</h4>
            <p class="text-gray-600 dark:text-gray-400">Connect with {{ number_format($stats['totalStudents']) }} learners, join study groups, and participate in coding challenges together.</p>
        </div>
        <div class="bg-blue-50 dark:bg-blue-900/10 p-6 rounded-2xl border border-blue-100 dark:border-blue-800">
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-800 rounded-xl flex items-center justify-center mb-4">
                <i class="fas fa-mobile-alt text-blue-600 dark:text-blue-300 text-xl"></i>
            </div>
            <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Mobile Learning</h4>
            <p class="text-gray-600 dark:text-gray-400">Learn on-the-go with our mobile app. Download content for offline access and never miss a lesson.</p>
        </div>
        <div class="bg-blue-50 dark:bg-blue-900/10 p-6 rounded-2xl border border-blue-100 dark:border-blue-800">
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-800 rounded-xl flex items-center justify-center mb-4">
                <i class="fas fa-headset text-blue-600 dark:text-blue-300 text-xl"></i>
            </div>
            <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">24/7 Support</h4>
            <p class="text-gray-600 dark:text-gray-400">Get help whenever you need it with our round-the-clock support team and AI-powered learning assistant.</p>
        </div>
    </div>
</div>