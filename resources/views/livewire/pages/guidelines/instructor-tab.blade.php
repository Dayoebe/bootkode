<div class="tab-content">
    <div class="mb-12">
        <div class="flex items-center mb-6">
            <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900 rounded-2xl flex items-center justify-center mr-6">
                <i class="fas fa-chalkboard-teacher text-blue-600 dark:text-blue-300 text-2xl"></i>
            </div>
            <div>
                <h2 class="text-4xl font-bold text-gray-900 dark:text-white">Instructor Excellence</h2>
                <p class="text-xl text-blue-600 font-semibold">Educate, Inspire, Transform</p>
            </div>
        </div>
        <p class="text-lg text-gray-600 dark:text-gray-400 leading-relaxed max-w-4xl">
            Join {{ number_format($stats['totalInstructors']) }} expert instructors who are shaping Africa's tech talent. Create world-class courses, 
            reach {{ number_format($stats['totalStudents']) }} learners, and build sustainable income while making meaningful impact.
        </p>
    </div>

    <!-- Instructor Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-12">
        <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-2xl text-center">
            <div class="text-3xl font-bold text-blue-600 mb-2">{{ number_format($stats['totalInstructors']) }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Active Instructors</div>
        </div>
        <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-2xl text-center">
            <div class="text-3xl font-bold text-blue-600 mb-2">{{ number_format($stats['totalCourses']) }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Courses Created</div>
        </div>
        <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-2xl text-center">
            <div class="text-3xl font-bold text-blue-600 mb-2">{{ number_format($stats['totalEnrollments']) }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Student Enrollments</div>
        </div>
        <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-2xl text-center">
            <div class="text-3xl font-bold text-blue-600 mb-2">{{ number_format($stats['averageRating'], 1) }}/5</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Course Rating</div>
        </div>
    </div>

    <!-- Instructor Roadmap -->
    <div class="mb-12">
        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-8 text-center">Your Teaching Journey</h3>
        <div class="relative">
            <div class="absolute left-1/2 transform -translate-x-px h-full w-0.5 bg-blue-200 dark:bg-blue-800"></div>
            
            <!-- Step 1 -->
            <div class="relative flex items-center mb-12">
                <div class="flex-1 pr-8 text-right">
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-2xl border border-blue-100 dark:border-blue-800">
                        <div class="flex items-center justify-end mb-4">
                            <h4 class="text-xl font-bold text-gray-900 dark:text-white mr-3">Application & Vetting</h4>
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-800 rounded-lg flex items-center justify-center">
                                <i class="fas fa-user-check text-blue-600 dark:text-blue-300"></i>
                            </div>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 mb-4">
                            Submit your application with portfolio and teaching philosophy. Our team reviews 
                            your expertise and provides personalized onboarding.
                        </p>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div class="bg-white dark:bg-gray-700 p-3 rounded-lg">
                                <div class="font-medium text-gray-900 dark:text-white">Required</div>
                                <div class="text-gray-600 dark:text-gray-400">3+ years experience</div>
                            </div>
                            <div class="bg-white dark:bg-gray-700 p-3 rounded-lg">
                                <div class="font-medium text-gray-900 dark:text-white">Review Time</div>
                                <div class="text-gray-600 dark:text-gray-400">5-7 business days</div>
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
                                <i class="fas fa-video text-blue-600 dark:text-blue-300"></i>
                            </div>
                            <h4 class="text-xl font-bold text-gray-900 dark:text-white">Content Development</h4>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 mb-4">
                            Use our advanced course builder to create engaging content with videos, interactive 
                            exercises, and assessments. Join our {{ number_format($stats['totalInstructors']) }} instructors creating world-class education.
                        </p>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between bg-white dark:bg-gray-700 p-3 rounded-lg">
                                <span class="text-gray-700 dark:text-gray-300">Video Recording Studio</span>
                                <i class="fas fa-check text-green-500"></i>
                            </div>
                            <div class="flex items-center justify-between bg-white dark:bg-gray-700 p-3 rounded-lg">
                                <span class="text-gray-700 dark:text-gray-300">Interactive Code Editor</span>
                                <i class="fas fa-check text-green-500"></i>
                            </div>
                            <div class="flex items-center justify-between bg-white dark:bg-gray-700 p-3 rounded-lg">
                                <span class="text-gray-700 dark:text-gray-300">Assessment Builder</span>
                                <i class="fas fa-check text-green-500"></i>
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
                            <h4 class="text-xl font-bold text-gray-900 dark:text-white mr-3">Quality Assurance</h4>
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-800 rounded-lg flex items-center justify-center">
                                <i class="fas fa-star text-blue-600 dark:text-blue-300"></i>
                            </div>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 mb-4">
                            Our curriculum team reviews your content for quality, accuracy, and alignment with 
                            industry standards. Get feedback to perfect your course before launch.
                        </p>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center justify-end">
                                <span class="text-gray-600 dark:text-gray-400 mr-2">Content accuracy check</span>
                                <i class="fas fa-check text-green-500"></i>
                            </div>
                            <div class="flex items-center justify-end">
                                <span class="text-gray-600 dark:text-gray-400 mr-2">Technical review</span>
                                <i class="fas fa-check text-green-500"></i>
                            </div>
                            <div class="flex items-center justify-end">
                                <span class="text-gray-600 dark:text-gray-400 mr-2">Pedagogical assessment</span>
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
                                <i class="fas fa-rocket text-blue-600 dark:text-blue-300"></i>
                            </div>
                            <h4 class="text-xl font-bold text-gray-900 dark:text-white">Course Launch & Marketing</h4>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 mb-4">
                            Launch your course with our marketing support. Reach our community of {{ number_format($stats['totalUsers']) }} users 
                            through platform promotion and social media campaigns.
                        </p>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div class="bg-white dark:bg-gray-700 p-3 rounded-lg">
                                <div class="font-medium text-gray-900 dark:text-white">Marketing Support</div>
                                <div class="text-gray-600 dark:text-gray-400">Featured placement</div>
                            </div>
                            <div class="bg-white dark:bg-gray-700 p-3 rounded-lg">
                                <div class="font-medium text-gray-900 dark:text-white">Revenue Share</div>
                                <div class="text-gray-600 dark:text-gray-400">Up to 70%</div>
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
                            <h4 class="text-xl font-bold text-gray-900 dark:text-white mr-3">Teaching & Growth</h4>
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-800 rounded-lg flex items-center justify-center">
                                <i class="fas fa-chart-line text-blue-600 dark:text-blue-300"></i>
                            </div>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 mb-4">
                            Engage with students, track progress, and continuously improve your courses. Build 
                            your reputation and expand your teaching impact across Africa.
                        </p>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center justify-end">
                                <span class="text-gray-600 dark:text-gray-400 mr-2">Student Q&A sessions</span>
                                <i class="fas fa-check text-green-500"></i>
                            </div>
                            <div class="flex items-center justify-end">
                                <span class="text-gray-600 dark:text-gray-400 mr-2">Performance analytics</span>
                                <i class="fas fa-check text-green-500"></i>
                            </div>
                            <div class="flex items-center justify-end">
                                <span class="text-gray-600 dark:text-gray-400 mr-2">Revenue tracking</span>
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

    <!-- Instructor Benefits -->
    <div class="grid md:grid-cols-3 gap-8 mt-12">
        <div class="bg-blue-50 dark:bg-blue-900/10 p-6 rounded-2xl border border-blue-100 dark:border-blue-800">
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-800 rounded-xl flex items-center justify-center mb-4">
                <i class="fas fa-dollar-sign text-blue-600 dark:text-blue-300 text-xl"></i>
            </div>
            <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Competitive Revenue</h4>
            <p class="text-gray-600 dark:text-gray-400">Earn up to 70% revenue share from course sales with transparent monthly payouts and performance bonuses.</p>
        </div>
        <div class="bg-blue-50 dark:bg-blue-900/10 p-6 rounded-2xl border border-blue-100 dark:border-blue-800">
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-800 rounded-xl flex items-center justify-center mb-4">
                <i class="fas fa-tools text-blue-600 dark:text-blue-300 text-xl"></i>
            </div>
            <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Advanced Tools</h4>
            <p class="text-gray-600 dark:text-gray-400">Access professional recording equipment, editing software, and interactive content creation tools.</p>
        </div>
        <div class="bg-blue-50 dark:bg-blue-900/10 p-6 rounded-2xl border border-blue-100 dark:border-blue-800">
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-800 rounded-xl flex items-center justify-center mb-4">
                <i class="fas fa-users text-blue-600 dark:text-blue-300 text-xl"></i>
            </div>
            <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Global Reach</h4>
            <p class="text-gray-600 dark:text-gray-400">Connect with learners across Africa and beyond, building your personal brand and professional network.</p>
        </div>
    </div>
</div>