<div x-data="{ activeTab: 'student' }" class="p-4 md:p-8 bg-gray-50 dark:bg-gray-900 min-h-screen font-sans">
    <div class="">
        <!-- Header Section -->
        <header class="text-center mb-12">
            <h1
                class="text-4xl md:text-5xl font-extrabold text-gray-900 dark:text-white mb-4 animate__animated animate__fadeInDown">
                Welcome to BootKode
            </h1>
            <p class="text-lg md:text-xl text-gray-600 dark:text-gray-300 animate__animated animate__fadeInUp">
                Your pathway to digital skills, mentorship, and a thriving career.
            </p>
        </header>

        <!-- Tab Navigation -->
        <div class="flex flex-wrap justify-center mb-8 gap-3 animate__animated animate__fadeIn">
            <button @click="activeTab = 'student'"
                :class="{ 'bg-purple-600 text-white shadow-lg scale-105': activeTab === 'student', 'bg-gray-200 text-gray-700 hover:bg-purple-100': activeTab !== 'student' }"
                class="flex items-center space-x-2 px-6 py-3 rounded-full font-semibold transition-all duration-300 transform dark:text-gray-900">
                <i class="fas fa-user-graduate"></i>
                <span>Students</span>
            </button>
            <button @click="activeTab = 'instructor'"
                :class="{ 'bg-purple-600 text-white shadow-lg scale-105': activeTab === 'instructor', 'bg-gray-200 text-gray-700 hover:bg-purple-100': activeTab !== 'instructor' }"
                class="flex items-center space-x-2 px-6 py-3 rounded-full font-semibold transition-all duration-300 transform dark:text-gray-900">
                <i class="fas fa-chalkboard-teacher"></i>
                <span>Instructors</span>
            </button>
            <button @click="activeTab = 'mentor'"
                :class="{ 'bg-purple-600 text-white shadow-lg scale-105': activeTab === 'mentor', 'bg-gray-200 text-gray-700 hover:bg-purple-100': activeTab !== 'mentor' }"
                class="flex items-center space-x-2 px-6 py-3 rounded-full font-semibold transition-all duration-300 transform dark:text-gray-900">
                <i class="fas fa-handshake"></i>
                <span>Mentors</span>
            </button>
            <button @click="activeTab = 'admin'"
                :class="{ 'bg-purple-600 text-white shadow-lg scale-105': activeTab === 'admin', 'bg-gray-200 text-gray-700 hover:bg-purple-100': activeTab !== 'admin' }"
                class="flex items-center space-x-2 px-6 py-3 rounded-full font-semibold transition-all duration-300 transform dark:text-gray-900">
                <i class="fas fa-user-shield"></i>
                <span>Admins</span>
            </button>
        </div>

        <!-- Tab Content -->
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 md:p-10 animate__animated animate__fadeInUp animate__delay-1s">
            <!-- Student Content -->
            <!-- Student Content -->
            <div x-show="activeTab === 'student'" x-transition:enter.duration.500ms>
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center mb-2">
                        <div
                            class="w-12 h-12 bg-gradient-to-r from-purple-500 to-indigo-600 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-user-graduate text-white text-xl"></i>
                        </div>
                        Student Journey: Code, Certify, Conquer
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400">
                        BootKode empowers you to learn digital skills and launch a career. Here's how you can get
                        started:
                    </p>
                </div>

                <div class="grid md:grid-cols-2 gap-8">
                    <div
                        class="bg-gradient-to-br from-purple-50 to-indigo-50 dark:from-gray-700 dark:to-gray-900 p-6 rounded-xl border border-purple-100 dark:border-gray-600">
                        <div class="flex items-start mb-4">
                            <div
                                class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-200 text-lg mr-4">
                                <i class="fas fa-pen-nib"></i>
                            </div>
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white">1. Register & Explore</h3>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300">Sign up for free and browse our extensive library of
                            courses and career roadmaps. Discover your passion, whether it's web development, mobile
                            apps, or data science.</p>
                    </div>

                    <div
                        class="bg-gradient-to-br from-purple-50 to-indigo-50 dark:from-gray-700 dark:to-gray-900 p-6 rounded-xl border border-purple-100 dark:border-gray-600">
                        <div class="flex items-start mb-4">
                            <div
                                class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-200 text-lg mr-4">
                                <i class="fas fa-book-open"></i>
                            </div>
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white">2. Learn & Build</h3>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300">Engage with high-quality content including videos,
                            PDFs, and hands-on projects. Complete quizzes and assignments, get your code reviewed, and
                            build a portfolio.</p>
                    </div>

                    <div
                        class="bg-gradient-to-br from-purple-50 to-indigo-50 dark:from-gray-700 dark:to-gray-900 p-6 rounded-xl border border-purple-100 dark:border-gray-600">
                        <div class="flex items-start mb-4">
                            <div
                                class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-200 text-lg mr-4">
                                <i class="fas fa-certificate"></i>
                            </div>
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white">3. Certify & Launch</h3>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300">After completing a course and its projects, you can
                            earn a verifiable professional certificate. Use this credential to showcase your skills to
                            potential employers.</p>
                    </div>

                    <div
                        class="bg-gradient-to-br from-purple-50 to-indigo-50 dark:from-gray-700 dark:to-gray-900 p-6 rounded-xl border border-purple-100 dark:border-gray-600">
                        <div class="flex items-start mb-4">
                            <div
                                class="flex-shrink-0 w-10 h-10 flex items-center justify-center rounded-lg bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-200 text-lg mr-4">
                                <i class="fas fa-comments"></i>
                            </div>
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white">4. Engage & Grow</h3>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300">Join our mentorship program and peer community.
                            Connect with experienced developers, get personalized feedback on your portfolio, and
                            receive career advice.</p>
                    </div>
                </div>

                <div class="mt-10 pt-8 border-t border-gray-200 dark:border-gray-700">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Student Journey Map</h3>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-2xl p-4">
                        <div class="flex overflow-x-auto pb-4">
                            <div class="flex-none w-64 bg-white dark:bg-gray-600 rounded-xl p-4 shadow mr-4">
                                <div class="text-center mb-3">
                                    <div
                                        class="w-12 h-12 bg-purple-100 dark:bg-purple-800 rounded-full flex items-center justify-center mx-auto mb-2">
                                        <i class="fas fa-user-plus text-purple-600 dark:text-purple-300"></i>
                                    </div>
                                    <h4 class="font-semibold">Sign Up</h4>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Create your free account in seconds
                                </p>
                            </div>
                            <div class="flex-none w-64 bg-white dark:bg-gray-600 rounded-xl p-4 shadow mr-4">
                                <div class="text-center mb-3">
                                    <div
                                        class="w-12 h-12 bg-purple-100 dark:bg-purple-800 rounded-full flex items-center justify-center mx-auto mb-2">
                                        <i class="fas fa-compass text-purple-600 dark:text-purple-300"></i>
                                    </div>
                                    <h4 class="font-semibold">Explore</h4>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Browse courses and roadmaps</p>
                            </div>
                            <div class="flex-none w-64 bg-white dark:bg-gray-600 rounded-xl p-4 shadow mr-4">
                                <div class="text-center mb-3">
                                    <div
                                        class="w-12 h-12 bg-purple-100 dark:bg-purple-800 rounded-full flex items-center justify-center mx-auto mb-2">
                                        <i class="fas fa-play-circle text-purple-600 dark:text-purple-300"></i>
                                    </div>
                                    <h4 class="font-semibold">Learn</h4>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Start your first course</p>
                            </div>
                            <div class="flex-none w-64 bg-white dark:bg-gray-600 rounded-xl p-4 shadow mr-4">
                                <div class="text-center mb-3">
                                    <div
                                        class="w-12 h-12 bg-purple-100 dark:bg-purple-800 rounded-full flex items-center justify-center mx-auto mb-2">
                                        <i class="fas fa-code text-purple-600 dark:text-purple-300"></i>
                                    </div>
                                    <h4 class="font-semibold">Practice</h4>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Complete projects and exercises</p>
                            </div>
                            <div class="flex-none w-64 bg-white dark:bg-gray-600 rounded-xl p-4 shadow">
                                <div class="text-center mb-3">
                                    <div
                                        class="w-12 h-12 bg-purple-100 dark:bg-purple-800 rounded-full flex items-center justify-center mx-auto mb-2">
                                        <i class="fas fa-graduation-cap text-purple-600 dark:text-purple-300"></i>
                                    </div>
                                    <h4 class="font-semibold">Graduate</h4>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-300">Earn your certificate</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Instructor Content -->
            <div x-show="activeTab === 'instructor'" x-transition:enter.duration.500ms>
                <div class="mb-6">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center mb-2">
                        <i class="fas fa-chalkboard-teacher text-purple-600 mr-3"></i> Instructor Guide: Educate &
                        Empower
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400">
                        As an instructor, you are at the heart of our mission. Your expertise helps shape the next
                        generation of African tech talent.
                    </p>
                </div>

                <div class="space-y-6 text-gray-700 dark:text-gray-300">
                    <div class="flex items-start">
                        <div
                            class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full bg-purple-100 text-purple-600 mt-1 mr-4">
                            <i class="fas fa-upload"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white">1. Create & Publish Courses
                            </h3>
                            <p>Share your knowledge by creating comprehensive courses. Our platform supports various
                                content types, including video, audio, and documents. You can structure your lessons,
                                add quizzes, and design hands-on projects to ensure students gain practical skills.</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div
                            class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full bg-purple-100 text-purple-600 mt-1 mr-4">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white">2. Manage Your Courses &
                                Students</h3>
                            <p>Use your dedicated dashboard to track student progress, review assignments, and manage
                                your course materials. You can see enrollment numbers, completion rates, and feedback to
                                refine your content and help your students succeed.</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div
                            class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full bg-purple-100 text-purple-600 mt-1 mr-4">
                            <i class="fas fa-star"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white">3. Review & Certify</h3>
                            <p>Review student projects and approve certificate requests. Your approval ensures the
                                quality of our certified graduates and strengthens the value of BootKode's credentials
                                in the job market.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mentor Content -->
            <div x-show="activeTab === 'mentor'" x-transition:enter.duration.500ms>
                <div class="mb-6">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center mb-2">
                        <i class="fas fa-handshake text-purple-600 mr-3"></i> Mentor Guide: Support & Inspire
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400">
                        Mentors provide the crucial human connection that most online platforms lack. Your role is to
                        guide, support, and inspire our learners.
                    </p>
                </div>

                <div class="space-y-6 text-gray-700 dark:text-gray-300">
                    <div class="flex items-start">
                        <div
                            class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full bg-purple-100 text-purple-600 mt-1 mr-4">
                            <i class="fas fa-comments"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white">1. Provide Guidance</h3>
                            <p>Engage with students through the mentorship system. You can offer live sessions, provide
                                personal feedback on their projects, and answer their questions about career paths and
                                industry trends.</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div
                            class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full bg-purple-100 text-purple-600 mt-1 mr-4">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white">2. Review & Advise</h3>
                            <p>Help students refine their portfolios and resumes. Your professional advice can be the
                                difference between a good portfolio and a job-winning one. Guide them through the
                                challenges of the job market and freelancing world.</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div
                            class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full bg-purple-100 text-purple-600 mt-1 mr-4">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white">3. Inspire the Next Generation
                            </h3>
                            <p>Share your experiences and successes to motivate learners. Your real-world insights are
                                invaluable and will help shape the confidence and skills of our students, turning them
                                into successful professionals.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin Content -->
            <div x-show="activeTab === 'admin'" x-transition:enter.duration.500ms>
                <div class="mb-6">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center mb-2">
                        <i class="fas fa-user-shield text-purple-600 mr-3"></i> Admin Guide: Manage & Grow
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400">
                        As an admin, you ensure the smooth and effective operation of the entire BootKode ecosystem.
                    </p>
                </div>

                <div class="space-y-6 text-gray-700 dark:text-gray-300">
                    <div class="flex items-start">
                        <div
                            class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full bg-purple-100 text-purple-600 mt-1 mr-4">
                            <i class="fas fa-users-cog"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white">1. User & Role Management</h3>
                            <p>Oversee all user accounts, assign roles (Student, Instructor, Mentor, etc.), and ensure
                                the platform's security and integrity. You have the power to manage permissions and
                                access levels as needed.</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div
                            class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full bg-purple-100 text-purple-600 mt-1 mr-4">
                            <i class="fas fa-check-double"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white">2. Content & Course Approval
                            </h3>
                            <p>Review and approve new courses and lessons submitted by instructors. Your role is to
                                maintain the quality and relevance of all content published on the platform, ensuring it
                                aligns with BootKode's standards.</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div
                            class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full bg-purple-100 text-purple-600 mt-1 mr-4">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white">3. Certificate Management</h3>
                            <p>Manage and approve certificate requests, ensuring that all completion requirements are
                                met before a certificate is issued. This is a critical function for maintaining the
                                value of our credentials.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .shadow-lg {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        /* Animate.css custom duration */
        .animate__animated {
            animation-duration: 1.5s;
        }

        .dark .bg-white {
            background-color: #1a202c;
            /* Equivalent to gray-800 */
        }
    </style>
</div>
