<div class="bg-gray-50 antialiased">
    <style>
        /* Custom animation for pulse effect on button */
        @keyframes pulse-custom {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.05);
            }
        }

        .animate-pulse-custom {
            animation: pulse-custom 1.5s infinite ease-in-out;
        }

        /* Fade in animation */
        .animate-fade-in {
            animation: fadeIn 1s ease-out forwards;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Delay animations for staggered effect */
        .animate-delay-0\.2s {
            animation-delay: 0.2s;
        }

        .animate-delay-0\.4s {
            animation-delay: 0.4s;
        }

        .animate-delay-0\.6s {
            animation-delay: 0.6s;
        }

        .animate-delay-0\.8s {
            animation-delay: 0.8s;
        }

        .animate-delay-1s {
            animation-delay: 1s;
        }
    </style>


    <main>
        <!-- Hero Section -->
        <section
            class="relative bg-gradient-to-br from-blue-700 to-indigo-900 text-white py-20 lg:py-32 overflow-hidden rounded-b-3xl shadow-xl">
            <div class="absolute inset-0 opacity-10">
                <svg class="h-full w-full" fill="none" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid slice"
                    xmlns="http://www.w3.org/2000/svg">
                    <pattern id="pattern-circles" x="0" y="0" width="10" height="10" patternUnits="userSpaceOnUse">
                        <circle cx="5" cy="5" r="1" fill="rgba(255,255,255,0.1)" />
                    </pattern>
                    <rect x="0" y="0" width="100%" height="100%" fill="url(#pattern-circles)" />
                </svg>
            </div>

            <div class="px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="lg:grid lg:grid-cols-12 lg:gap-8 items-center">
                    <div class="sm:text-center lg:text-left lg:col-span-7 animate-fade-in">
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-tight mb-6">
                            Empower Your Future. <br class="hidden sm:inline"> Master Digital Skills.
                        </h1>
                        <p class="text-lg sm:text-xl max-w-2xl mx-auto lg:mx-0 mb-8 text-blue-100">
                            BootKode provides structured, affordable, and locally relevant coding education to unlock
                            Africa's tech potential.
                        </p>
                        <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
                            <a href="#"
                                class="inline-flex items-center justify-center px-8 py-4 border border-transparent border-white text-base font-bold rounded-full shadow-lg hover:text-white text-indigo-600 bg-white hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-300 ease-in-out transform hover:scale-105">
                                Start Learning Free <i class="fas fa-arrow-right ml-3"></i>
                            </a>
                            <a href="#features"
                                class="inline-flex items-center justify-center px-8 py-4 border-2 border-white text-base font-bold rounded-full text-white hover:bg-white hover:text-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition duration-300 ease-in-out transform hover:scale-105">
                                Explore Courses <i class="fas fa-book-open ml-3"></i>
                            </a>
                        </div>
                    </div>

                    <div class="mt-12 lg:mt-0 lg:col-span-5 animate-fade-in animate-delay-200">
                        <div class="relative mx-auto w-full max-w-md lg:max-w-none">
                            <div class="relative rounded-xl shadow-2xl overflow-hidden">
                                <img class="w-full h-auto"
                                    src="https://images.unsplash.com/photo-1551434678-e076c223a692?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80"
                                    alt="African students learning tech skills">
                                <div class="absolute inset-0 bg-gradient-to-t from-blue-900/50 to-blue-700/30"></div>
                            </div>
                            <div
                                class="absolute -bottom-8 -right-8 w-64 h-64 bg-blue-200 rounded-full opacity-20 animate-pulse">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-16 bg-white" x-data="{ activeTab: 'education' }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="text-center mb-16 animate-fade-in">
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900">
                        Why Choose BootKode?
                    </h2>
                    <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-600">
                        Africa's most comprehensive tech education ecosystem
                    </p>
                </div>

                <!-- Tab Navigation -->
                <div class="flex justify-center space-x-2 mb-12">
                    <button @click="activeTab = 'education'"
                        :class="{ 'bg-blue-600 text-white': activeTab === 'education', 'bg-gray-100 text-gray-700': activeTab !== 'education' }"
                        class="px-6 py-3 rounded-lg font-medium transition-all duration-300 flex items-center">
                        <i class="fas fa-graduation-cap mr-2"></i> Education
                    </button>
                    <button @click="activeTab = 'mentorship'"
                        :class="{ 'bg-blue-600 text-white': activeTab === 'mentorship', 'bg-gray-100 text-gray-700': activeTab !== 'mentorship' }"
                        class="px-6 py-3 rounded-lg font-medium transition-all duration-300 flex items-center">
                        <i class="fas fa-hands-helping mr-2"></i> Mentorship
                    </button>
                    <button @click="activeTab = 'certification'"
                        :class="{ 'bg-blue-600 text-white': activeTab === 'certification', 'bg-gray-100 text-gray-700': activeTab !== 'certification' }"
                        class="px-6 py-3 rounded-lg font-medium transition-all duration-300 flex items-center">
                        <i class="fas fa-certificate mr-2"></i> Certification
                    </button>
                </div>

                <!-- Tab Content -->
                <div class="grid md:grid-cols-3 gap-8">
                    <!-- Education Tab -->
                    <div x-show="activeTab === 'education'" class="grid md:grid-cols-3 gap-8 md:col-span-3">
                        <div
                            class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 hover:shadow-xl transition-shadow duration-300 animate-fade-in">
                            <div
                                class="flex items-center justify-center h-12 w-12 rounded-md bg-blue-100 text-blue-600 mb-4">
                                <i class="fas fa-road text-xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Structured Roadmaps</h3>
                            <p class="text-gray-600">
                                Clear, step-by-step paths from beginner to job-ready developer with African context.
                            </p>
                        </div>

                        <div
                            class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 hover:shadow-xl transition-shadow duration-300 animate-fade-in animate-delay-0.2s">
                            <div
                                class="flex items-center justify-center h-12 w-12 rounded-md bg-blue-100 text-blue-600 mb-4">
                                <i class="fas fa-mobile-alt text-xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Mobile-First Learning</h3>
                            <p class="text-gray-600">
                                Optimized for low-end smartphones with offline access to PDFs and audio lessons.
                            </p>
                        </div>

                        <div
                            class="bg-white p-8 rounded-xl shadow-lg flex flex-col items-center transform hover:scale-105 transition duration-300 animate-fade-in animate-delay-0.4s">
                            <div class="bg-blue-100 text-blue-600 rounded-full p-4 mb-6">
                                <i class="fas fa-graduation-cap text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3 text-center">Education that Transforms
                            </h3>
                            <p class="text-gray-600 text-center">
                                Structured curriculum, modular content, and practical exercises that build real careers.
                            </p>
                        </div>
                    </div>

                    <!-- Mentorship Tab -->
                    <div x-show="activeTab === 'mentorship'" class="grid md:grid-cols-2 lg:grid-cols-4 gap-8 md:col-span-3">
                        <div
                            class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 hover:shadow-xl transition-shadow duration-300 animate-fade-in">
                            <div
                                class="flex items-center justify-center h-12 w-12 rounded-md bg-green-100 text-green-600 mb-4">
                                <i class="fas fa-users text-xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Peer Communities</h3>
                            <p class="text-gray-600">
                                Join discussion groups, share knowledge, and get help from fellow learners.
                            </p>
                        </div>

                        <div
                            class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 hover:shadow-xl transition-shadow duration-300 animate-fade-in animate-delay-0.2s">
                            <div
                                class="flex items-center justify-center h-12 w-12 rounded-md bg-green-100 text-green-600 mb-4">
                                <i class="fas fa-user-tie text-xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Expert Mentors</h3>
                            <p class="text-gray-600">
                                Get guidance from experienced developers who understand the African tech landscape.
                            </p>
                        </div>

                        <div
                            class="bg-white rounded-xl shadow-lg p-8 flex flex-col items-center transform hover:scale-105 transition duration-300 animate-fade-in animate-delay-0.4s">
                            <div class="bg-green-100 text-green-600 rounded-full p-4 mb-6">
                                <i class="fas fa-hands-helping text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">Support that Builds Confidence</h3>
                            <p class="text-gray-600 text-center">Access to experienced mentors, peer communities, and
                                AI-powered guidance.</p>
                        </div>

                        <div
                            class="bg-white rounded-xl shadow-lg p-8 flex flex-col items-center transform hover:scale-105 transition duration-300 animate-fade-in animate-delay-0.6s">
                            <div class="bg-pink-100 text-pink-600 rounded-full p-4 mb-6">
                                <i class="fas fa-rocket text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">Learning is Only the Beginning</h3>
                            <p class="text-gray-600 text-center">Resources for portfolio building, freelancing, job
                                placement, and launching startups.</p>
                        </div>
                    </div>

                    <!-- Certification Tab -->
                    <div x-show="activeTab === 'certification'"
                        class="grid md:grid-cols-3 gap-8 md:col-span-3">
                        <div
                            class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 hover:shadow-xl transition-shadow duration-300 animate-fade-in">
                            <div
                                class="flex items-center justify-center h-12 w-12 rounded-md bg-pink-100 text-pink-600 mb-4">
                                <i class="fas fa-qrcode text-xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Verified Credentials</h3>
                            <p class="text-gray-600">
                                QR-verified certificates that employers can validate with a simple scan.
                            </p>
                        </div>

                        <div
                            class="bg-white p-6 rounded-xl shadow-lg border border-gray-100 hover:shadow-xl transition-shadow duration-300 animate-fade-in animate-delay-0.2s">
                            <div
                                class="flex items-center justify-center h-12 w-12 rounded-md bg-pink-100 text-pink-600 mb-4">
                                <i class="fas fa-project-diagram text-xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Project-Based</h3>
                            <p class="text-gray-600">
                                Certificates tied to real projects you build, not just course completion.
                            </p>
                        </div>

                        <div
                            class="bg-white rounded-xl shadow-lg p-8 flex flex-col items-center transform hover:scale-105 transition duration-300 animate-fade-in animate-delay-0.4s">
                            <div class="bg-yellow-100 text-yellow-600 rounded-full p-4 mb-6">
                                <i class="fas fa-certificate text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">Proof that Opens Doors</h3>
                            <p class="text-gray-600 text-center">Affordable, QR-verified certificates tied to real
                                projects for job readiness.</p>
                        </div>

                    </div>
                </div>
            </div>
        </section>


        <!-- Problem/Solution Section -->
        <section class="py-16 sm:py-20 bg-blue-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="animate__animated animate__fadeInLeft">
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-6">The Problem We Solve: Bridging
                        Africa's Digital Divide</h2>
                    <p class="text-lg text-gray-700 mb-6">Millions of young Africans are eager to tap into the global
                        digital economy but face significant barriers:</p>
                    <ul class="space-y-4 text-gray-700 text-lg">
                        <li class="flex items-start">
                            <i class="fas fa-times-circle text-red-500 mt-1 mr-3 text-xl flex-shrink-0"></i>
                            <span><strong class="font-semibold">Skill Gap:</strong> Lack of practical, job-oriented
                                curricula in traditional education.</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-times-circle text-red-500 mt-1 mr-3 text-xl flex-shrink-0"></i>
                            <span><strong class="font-semibold">Inaccessibility:</strong> Disjointed online resources
                                and high costs of quality tech education.</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-times-circle text-red-500 mt-1 mr-3 text-xl flex-shrink-0"></i>
                            <span><strong class="font-semibold">Lack of Local Relevance:</strong> Content often doesn't
                                resonate with African contexts.</span>
                        </li>
                    </ul>
                </div>
                <div class="animate__animated animate__fadeInRight">
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-6">BootKode: Your Solution to a
                        Thriving Tech Career</h2>
                    <p class="text-lg text-gray-700 mb-6">BootKode directly addresses these challenges by offering:</p>
                    <ul class="space-y-4 text-gray-700 text-lg">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3 text-xl flex-shrink-0"></i>
                            <span><strong class="font-semibold">Structured Roadmaps:</strong> Clear, step-by-step paths
                                from beginner to job-ready developer.</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3 text-xl flex-shrink-0"></i>
                            <span><strong class="font-semibold">Affordable & Accessible:</strong> Freemium model with
                                low-cost certification and mobile-first design.</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3 text-xl flex-shrink-0"></i>
                            <span><strong class="font-semibold">Contextual Learning:</strong> Content tailored by
                                Africans, for Africans, using relatable examples.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- AI-Powered Learning Section -->
        <section class="py-16 sm:py-20 bg-pink-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2
                    class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-12 animate__animated animate__fadeInUp">
                    Intelligent Learning with AI
                </h2>
                <p
                    class="mt-3 max-w-2xl mx-auto text-xl text-gray-600 sm:mt-4 animate__animated animate__fadeInUp animate__delay-0.2s">
                    BootKode leverages cutting-edge AI to personalize your learning journey and provide instant support.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mt-12">
                    <div
                        class="bg-white rounded-xl shadow-lg p-8 flex flex-col items-center transform hover:scale-105 transition duration-300 animate__animated animate__fadeInUp animate__delay-0.4s">
                        <div class="bg-pink-100 text-pink-600 rounded-full p-4 mb-6">
                            <i class="fas fa-robot text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">AI Code Assistant</h3>
                        <p class="text-gray-600 text-center">Get instant explanations, error detection, and tips for
                            your code.</p>
                    </div>
                    <div
                        class="bg-white rounded-xl shadow-lg p-8 flex flex-col items-center transform hover:scale-105 transition duration-300 animate__animated animate__fadeInUp animate__delay-0.6s">
                        <div class="bg-pink-100 text-pink-600 rounded-full p-4 mb-6">
                            <i class="fas fa-chart-line text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Personalized Recommendations</h3>
                        <p class="text-gray-600 text-center">AI suggests next lessons and content based on your
                            progress and interests.</p>
                    </div>
                    <div
                        class="bg-white rounded-xl shadow-lg p-8 flex flex-col items-center transform hover:scale-105 transition duration-300 animate__animated animate__fadeInUp animate__delay-0.8s">
                        <div class="bg-pink-100 text-pink-600 rounded-full p-4 mb-6">
                            <i class="fas fa-user-tie text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Mock Interview Bot</h3>
                        <p class="text-gray-600 text-center">Practice technical interviews with AI and receive valuable
                            feedback.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Learning Paths Section -->
        <section class="py-16 sm:py-20 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2
                    class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-12 animate__animated animate__fadeInUp">
                    Choose Your Learning Path
                </h2>
                <p
                    class="mt-3 max-w-2xl mx-auto text-xl text-gray-500 sm:mt-4 animate__animated animate__fadeInUp animate__delay-0.2s">
                    Structured tracks designed for African job markets
                </p>

                <div class="mt-12 grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                    <!-- Path Card 1: Frontend Developer -->
                    <div
                        class="bg-white overflow-hidden shadow-lg rounded-xl transform transition-all duration-300 hover:scale-105 hover:shadow-2xl animate__animated animate__fadeInUp animate__delay-0.4s">
                        <div class="px-4 py-5 sm:p-6 bg-gradient-to-r from-blue-500 to-blue-600 rounded-t-xl">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-blue-100 rounded-full p-3 shadow-md">
                                    <i class="fas fa-laptop-code text-blue-600 text-2xl"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <h3 class="text-lg font-medium text-white">Frontend Developer</h3>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <ul class="space-y-3 text-left">
                                <li class="flex items-start">
                                    <div class="flex-shrink-0 text-green-500 mt-1">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <p class="ml-3 text-base text-gray-700">HTML, CSS, JavaScript Fundamentals</p>
                                </li>
                                <li class="flex items-start">
                                    <div class="flex-shrink-0 text-green-500 mt-1">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <p class="ml-3 text-base text-gray-700">Vue.js & Alpine.js Integration</p>
                                </li>
                                <li class="flex items-start">
                                    <div class="flex-shrink-0 text-green-500 mt-1">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <p class="ml-3 text-base text-gray-700">Responsive Design with Tailwind CSS</p>
                                </li>
                                <li class="flex items-start">
                                    <div class="flex-shrink-0 text-green-500 mt-1">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <p class="ml-3 text-base text-gray-700">Portfolio Project Development</p>
                                </li>
                            </ul>
                            <div class="mt-6">
                                <a href="#"
                                    class="w-full flex items-center justify-center px-4 py-3 border border-transparent text-base font-medium rounded-full text-blue-700 bg-blue-100 hover:bg-blue-200 transition-all duration-300 transform hover:scale-105">
                                    View Path <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Path Card 2: Backend Developer -->
                    <div
                        class="bg-white overflow-hidden shadow-lg rounded-xl transform transition-all duration-300 hover:scale-105 hover:shadow-2xl animate__animated animate__fadeInUp animate__delay-0.6s">
                        <div class="px-4 py-5 sm:p-6 bg-gradient-to-r from-green-500 to-green-600 rounded-t-xl">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-green-100 rounded-full p-3 shadow-md">
                                    <i class="fas fa-server text-green-600 text-2xl"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <h3 class="text-lg font-medium text-white">Backend Developer</h3>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <ul class="space-y-3 text-left">
                                <li class="flex items-start">
                                    <div class="flex-shrink-0 text-green-500 mt-1">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <p class="ml-3 text-base text-gray-700">Laravel Framework Mastery</p>
                                </li>
                                <li class="flex items-start">
                                    <div class="flex-shrink-0 text-green-500 mt-1">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <p class="ml-3 text-base text-gray-700">RESTful API Development</p>
                                </li>
                                <li class="flex items-start">
                                    <div class="flex-shrink-0 text-green-500 mt-1">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <p class="ml-3 text-base text-gray-700">Database Design & MySQL</p>
                                </li>
                                <li class="flex items-start">
                                    <div class="flex-shrink-0 text-green-500 mt-1">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <p class="ml-3 text-base text-gray-700">Authentication & Authorization</p>
                                </li>
                            </ul>
                            <div class="mt-6">
                                <a href="#"
                                    class="w-full flex items-center justify-center px-4 py-3 border border-transparent text-base font-medium rounded-full text-green-700 bg-green-100 hover:bg-green-200 transition-all duration-300 transform hover:scale-105">
                                    View Path <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Path Card 3: Mobile App Developer -->
                    <div
                        class="bg-white overflow-hidden shadow-lg rounded-xl transform transition-all duration-300 hover:scale-105 hover:shadow-2xl animate__animated animate__fadeInUp animate__delay-0.8s">
                        <div class="px-4 py-5 sm:p-6 bg-gradient-to-r from-pink-500 to-pink-600 rounded-t-xl">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-pink-100 rounded-full p-3 shadow-md">
                                    <i class="fas fa-mobile-alt text-pink-600 text-2xl"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <h3 class="text-lg font-medium text-white">Mobile App Developer</h3>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <ul class="space-y-3 text-left">
                                <li class="flex items-start">
                                    <div class="flex-shrink-0 text-green-500 mt-1">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <p class="ml-3 text-base text-gray-700">Flutter Fundamentals</p>
                                </li>
                                <li class="flex items-start">
                                    <div class="flex-shrink-0 text-green-500 mt-1">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <p class="ml-3 text-base text-gray-700">React Native Basics</p>
                                </li>
                                <li class="flex items-start">
                                    <div class="flex-shrink-0 text-green-500 mt-1">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <p class="ml-3 text-base text-gray-700">UI/UX for Mobile Apps</p>
                                </li>
                                <li class="flex items-start">
                                    <div class="flex-shrink-0 text-green-500 mt-1">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <p class="ml-3 text-base text-gray-700">App Deployment to Stores</p>
                                </li>
                            </ul>
                            <div class="mt-6">
                                <a href="#"
                                    class="w-full flex items-center justify-center px-4 py-3 border border-transparent text-base font-medium rounded-full text-pink-700 bg-pink-100 hover:bg-pink-200 transition-all duration-300 transform hover:scale-105">
                                    View Path <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Path Card 4: UI/UX Designer -->
                    <div
                        class="bg-white overflow-hidden shadow-lg rounded-xl transform transition-all duration-300 hover:scale-105 hover:shadow-2xl animate__animated animate__fadeInUp animate__delay-1s">
                        <div class="px-4 py-5 sm:p-6 bg-gradient-to-r from-pink-500 to-pink-600 rounded-t-xl">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-pink-100 rounded-full p-3 shadow-md">
                                    <i class="fas fa-palette text-pink-600 text-2xl"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <h3 class="text-lg font-medium text-white">UI/UX Designer</h3>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <ul class="space-y-3 text-left">
                                <li class="flex items-start">
                                    <div class="flex-shrink-0 text-green-500 mt-1">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <p class="ml-3 text-base text-gray-700">Design Thinking & User Research</p>
                                </li>
                                <li class="flex items-start">
                                    <div class="flex-shrink-0 text-green-500 mt-1">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <p class="ml-3 text-base text-gray-700">Figma & Adobe XD Proficiency</p>
                                </li>
                                <li class="flex items-start">
                                    <div class="flex-shrink-0 text-green-500 mt-1">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <p class="ml-3 text-base text-gray-700">Prototyping & Wireframing</p>
                                </li>
                                <li class="flex items-start">
                                    <div class="flex-shrink-0 text-green-500 mt-1">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <p class="ml-3 text-base text-gray-700">Usability Testing & Feedback</p>
                                </li>
                            </ul>
                            <div class="mt-6">
                                <a href="#"
                                    class="w-full flex items-center justify-center px-4 py-3 border border-transparent text-base font-medium rounded-full text-pink-700 bg-pink-100 hover:bg-pink-200 transition-all duration-300 transform hover:scale-105">
                                    View Path <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Path Card 5: Freelance Developer -->
                    <div
                        class="bg-white overflow-hidden shadow-lg rounded-xl transform transition-all duration-300 hover:scale-105 hover:shadow-2xl animate__animated animate__fadeInUp animate__delay-1.2s">
                        <div class="px-4 py-5 sm:p-6 bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-t-xl">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-yellow-100 rounded-full p-3 shadow-md">
                                    <i class="fas fa-briefcase text-yellow-600 text-2xl"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <h3 class="text-lg font-medium text-white">Freelance Developer</h3>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <ul class="space-y-3 text-left">
                                <li class="flex items-start">
                                    <div class="flex-shrink-0 text-green-500 mt-1">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <p class="ml-3 text-base text-gray-700">Setting Up Freelance Profiles (Upwork,
                                        Fiverr)</p>
                                </li>
                                <li class="flex items-start">
                                    <div class="flex-shrink-0 text-green-500 mt-1">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <p class="ml-3 text-base text-gray-700">Client Communication & Project Management
                                    </p>
                                </li>
                                <li class="flex items-start">
                                    <div class="flex-shrink-0 text-green-500 mt-1">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <p class="ml-3 text-base text-gray-700">Pricing Strategies & Contracts</p>
                                </li>
                                <li class="flex items-start">
                                    <div class="flex-shrink-0 text-green-500 mt-1">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <p class="ml-3 text-base text-gray-700">Building a Strong Portfolio</p>
                                </li>
                            </ul>
                            <div class="mt-6">
                                <a href="#"
                                    class="w-full flex items-center justify-center px-4 py-3 border border-transparent text-base font-medium rounded-full text-yellow-700 bg-yellow-100 hover:bg-yellow-200 transition-all duration-300 transform hover:scale-105">
                                    View Path <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Path Card 6: DevOps & Deployment -->
                    <div
                        class="bg-white overflow-hidden shadow-lg rounded-xl transform transition-all duration-300 hover:scale-105 hover:shadow-2xl animate__animated animate__fadeInUp animate__delay-1.4s">
                        <div class="px-4 py-5 sm:p-6 bg-gradient-to-r from-red-500 to-red-600 rounded-t-xl">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 bg-red-100 rounded-full p-3 shadow-md">
                                    <i class="fas fa-cloud-upload-alt text-red-600 text-2xl"></i>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <h3 class="text-lg font-medium text-white">DevOps & Deployment</h3>
                                </div>
                            </div>
                        </div>
                        <div class="px-4 py-5 sm:p-6">
                            <ul class="space-y-3 text-left">
                                <li class="flex items-start">
                                    <div class="flex-shrink-0 text-green-500 mt-1">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <p class="ml-3 text-base text-gray-700">Introduction to Cloud Platforms (Google
                                        Cloud)</p>
                                </li>
                                <li class="flex items-start">
                                    <div class="flex-shrink-0 text-green-500 mt-1">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <p class="ml-3 text-base text-gray-700">Git & GitHub for Version Control</p>
                                </li>
                                <li class="flex items-start">
                                    <div class="flex-shrink-0 text-green-500 mt-1">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <p class="ml-3 text-base text-gray-700">CI/CD Pipelines & Automation</p>
                                </li>
                                <li class="flex items-start">
                                    <div class="flex-shrink-0 text-green-500 mt-1">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <p class="ml-3 text-base text-gray-700">Server Management & Security</p>
                                </li>
                            </ul>
                            <div class="mt-6">
                                <a href="#"
                                    class="w-full flex items-center justify-center px-4 py-3 border border-transparent text-base font-medium rounded-full text-red-700 bg-red-100 hover:bg-red-200 transition-all duration-300 transform hover:scale-105">
                                    View Path <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Gamification & Community Section -->
        <section class="py-16 sm:py-20 bg-teal-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2
                    class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-12 animate__animated animate__fadeInUp">
                    Stay Motivated with Gamification & Community
                </h2>
                <p
                    class="mt-3 max-w-2xl mx-auto text-xl text-gray-600 sm:mt-4 animate__animated animate__fadeInUp animate__delay-0.2s">
                    Learning is more fun and effective when you're part of a vibrant community.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mt-12">
                    <div
                        class="bg-white rounded-xl shadow-lg p-8 flex flex-col items-center transform hover:scale-105 transition duration-300 animate__animated animate__fadeInUp animate__delay-0.4s">
                        <div class="bg-teal-100 text-teal-600 rounded-full p-4 mb-6">
                            <i class="fas fa-trophy text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Earn Badges & Climb Leaderboards</h3>
                        <p class="text-gray-600 text-center">Track your progress, earn achievements, and compete with
                            peers.</p>
                    </div>
                    <div
                        class="bg-white rounded-xl shadow-lg p-8 flex flex-col items-center transform hover:scale-105 transition duration-300 animate__animated animate__fadeInUp animate__delay-0.6s">
                        <div class="bg-teal-100 text-teal-600 rounded-full p-4 mb-6">
                            <i class="fas fa-comments text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Engage in Peer Communities</h3>
                        <p class="text-gray-600 text-center">Join discussion groups, share knowledge, and get help from
                            fellow learners.</p>
                    </div>
                    <div
                        class="bg-white rounded-xl shadow-lg p-8 flex flex-col items-center transform hover:scale-105 transition duration-300 animate__animated animate__fadeInUp animate__delay-0.8s">
                        <div class="bg-teal-100 text-teal-600 rounded-full p-4 mb-6">
                            <i class="fas fa-users text-3xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Live Mentorship Sessions</h3>
                        <p class="text-gray-600 text-center">Participate in Q&A sessions and code reviews with
                            experienced mentors.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Mobile-First & Offline Access Section -->
        <section class="py-16 sm:py-20 bg-blue-100">
            <div class="px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="animate__animated animate__fadeInLeft">
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-6">Learn Anywhere, Anytime, Even
                        Offline</h2>
                    <p class="text-lg text-gray-700 mb-6">BootKode is built with Africa's unique challenges in mind,
                        ensuring accessibility for everyone.</p>
                    <ul class="space-y-4 text-gray-700 text-lg">
                        <li class="flex items-start">
                            <i class="fas fa-mobile-alt text-blue-600 mt-1 mr-3 text-xl flex-shrink-0"></i>
                            <span><strong class="font-semibold">Mobile-Responsive PWA:</strong> Access the platform
                                seamlessly on any device, even low-end smartphones.</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-download text-blue-600 mt-1 mr-3 text-xl flex-shrink-0"></i>
                            <span><strong class="font-semibold">Offline Learning Packs:</strong> Download entire
                                modules (videos, PDFs, audio) to learn without internet.</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-globe-africa text-blue-600 mt-1 mr-3 text-xl flex-shrink-0"></i>
                            <span><strong class="font-semibold">Localized Content:</strong> Learn with examples and
                                instructors that resonate with the African context.</span>
                        </li>
                    </ul>
                </div>
                <div class="animate__animated animate__fadeInRight">
                    <img class="rounded-xl shadow-xl w-full h-auto object-cover animate__animated animate__pulse animate__infinite animate__slow"
                        src="https://images.unsplash.com/photo-1551434678-e076c223a692?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80"
                        alt="African students learning to code">
                    <p class="mt-4 text-gray-600">Join us in transforming the future of learning!</p>
                </div>
            </div>
        </section>

        <!-- Testimonial Section -->
        <section class="py-16 sm:py-20 bg-gray-100" x-data="testimonialSlider()">
            <div class="px-4 sm:px-6 lg:px-8 text-center">
                <h2
                    class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-12 animate__animated animate__fadeInUp">
                    What Our Learners Say
                </h2>
                <div class="relative overflow-hidden">
                    <div class="flex transition-transform duration-500 ease-in-out"
                        :style="`transform: translateX(-${currentIndex * 100}%)`">
                        <!-- Testimonial 1 -->
                        <div class="w-full flex-shrink-0 px-4">
                            <div
                                class="bg-white rounded-xl shadow-lg p-8 animate__animated animate__fadeInUp animate__delay-0.2s">
                                <p class="text-gray-700 italic mb-6 text-lg">"BootKode changed my life. I went from
                                    knowing nothing about code to building my first web app in months. The mentorship
                                    was invaluable!"</p>
                                <div class="flex items-center justify-center">
                                    <img class="h-12 w-12 rounded-full object-cover mr-4 shadow-md"
                                        src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=687&q=80"
                                        alt="User Avatar">
                                    <div>
                                        <p class="font-semibold text-gray-900 text-lg">Aisha M.</p>
                                        <p class="text-sm text-gray-500">Frontend Developer, Lagos</p>
                                    </div>
                                </div>
                                <div class="mt-4 flex items-center justify-center">
                                    <div class="flex text-yellow-400">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <div class="ml-2 text-sm text-gray-500">(5.0/5.0 Rating)</div>
                                </div>
                            </div>
                        </div>
                        <!-- Testimonial 2 -->
                        <div class="w-full flex-shrink-0 px-4">
                            <div
                                class="bg-white rounded-xl shadow-lg p-8 animate__animated animate__fadeInUp animate__delay-0.4s">
                                <p class="text-gray-700 italic mb-6 text-lg">"The structured roadmaps are a
                                    game-changer. Finally, a platform that understands the African context and helps you
                                    get job-ready!"</p>
                                <div class="flex items-center justify-center">
                                    <img class="h-12 w-12 rounded-full object-cover mr-4 shadow-md"
                                        src="https://images.unsplash.com/photo-1507003211169-0a3dd782dab4?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=687&q=80"
                                        alt="User Avatar">
                                    <div>
                                        <p class="font-semibold text-gray-900 text-lg">Kingsley O.</p>
                                        <p class="text-sm text-gray-500">Self-Taught Dev, Abuja</p>
                                    </div>
                                </div>
                                <div class="mt-4 flex items-center justify-center">
                                    <div class="flex text-yellow-400">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star-half-alt"></i>
                                    </div>
                                    <div class="ml-2 text-sm text-gray-500">(4.5/5.0 Rating)</div>
                                </div>
                            </div>
                        </div>
                        <!-- Testimonial 3 -->
                        <div class="w-full flex-shrink-0 px-4">
                            <div
                                class="bg-white rounded-xl shadow-lg p-8 animate__animated animate__fadeInUp animate__delay-0.6s">
                                <p class="text-gray-700 italic mb-6 text-lg">"As an NYSC member, BootKode's offline
                                    access was a lifesaver. I could learn even in remote areas and now I'm building my
                                    own startup!"</p>
                                <div class="flex items-center justify-center">
                                    <img class="h-12 w-12 rounded-full object-cover mr-4 shadow-md"
                                        src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=688&q=80"
                                        alt="User Avatar">
                                    <div>
                                        <p class="font-semibold text-gray-900 text-lg">Chidinma E.</p>
                                        <p class="text-sm text-gray-500">Mobile Developer, Enugu</p>
                                    </div>
                                </div>
                                <div class="mt-4 flex items-center justify-center">
                                    <div class="flex text-yellow-400">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <div class="ml-2 text-sm text-gray-500">(5.0/5.0 Rating)</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Testimonial Navigation Buttons -->
                <div class="mt-8 flex justify-center space-x-4">
                    <button @click="prev()"
                        class="bg-blue-600 text-white p-3 rounded-full shadow-lg hover:bg-blue-700 transition duration-300 transform hover:scale-110 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <button @click="next()"
                        class="bg-blue-600 text-white p-3 rounded-full shadow-lg hover:bg-blue-700 transition duration-300 transform hover:scale-110 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </section>

        <!-- Final Call to Action Section -->
        <section
            class="py-16 sm:py-20 bg-gradient-to-r from-blue-600 to-blue-800 text-white text-center rounded-t-xl shadow-lg animate__animated animate__fadeInUp">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl sm:text-4xl font-extrabold mb-4">Join the BootKode Movement Today!</h2>
                <p class="text-lg sm:text-xl max-w-3xl mx-auto mb-8">Unlock your potential, master digital skills, and
                    build a brighter future with Africa's leading tech education platform.</p>
                <a href="#"
                    class="inline-flex items-center justify-center px-10 py-4 border border-transparent text-lg font-bold rounded-full shadow-lg text-blue-800 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition duration-300 ease-in-out transform hover:scale-105 animate-pulse-custom">
                    Get Started for Free <i class="fas fa-arrow-right ml-3"></i>
                </a>
            </div>
        </section>
    </main>

    <script>
        // Alpine.js component for testimonial slider
        function testimonialSlider() {
            return {
                currentIndex: 0,
                testimonials: [
                    // Placeholder testimonial data - you can fetch this dynamically
                    {
                        quote: "BootKode changed my life. I went from knowing nothing about code to building my first web app in months. The mentorship was invaluable!",
                        name: "Aisha M.",
                        location: "Lagos, Nigeria",
                        avatar: "https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=687&q=80",
                        track: "Frontend Developer Track",
                        rating: 5
                    },
                    {
                        quote: "The structured roadmaps are a game-changer. Finally, a platform that understands the African context and helps you get job-ready!",
                        name: "Kingsley O.",
                        location: "Abuja, Nigeria",
                        avatar: "https://images.unsplash.com/photo-1507003211169-0a3dd782dab4?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=687&q=80",
                        track: "Self-Taught Dev",
                        rating: 4.5
                    },
                    {
                        quote: "As an NYSC member, BootKode's offline access was a lifesaver. I could learn even in remote areas and now I'm building my own startup!",
                        name: "Chidinma E.",
                        location: "Enugu, Nigeria",
                        avatar: "https://images.unsplash.com/photo-1544005313-94ddf0286df2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=688&q=80",
                        track: "Mobile Developer",
                        rating: 5
                    }
                ],
                // Function to move to the next testimonial
                next() {
                    this.currentIndex = (this.currentIndex + 1) % this.testimonials.length;
                },
                // Function to move to the previous testimonial
                prev() {
                    this.currentIndex = (this.currentIndex - 1 + this.testimonials.length) % this.testimonials.length;
                }
            }
        }
    </script>
    <div>
        <script>
            // Alpine.js components
            function testimonialSlider() {
                return {
                    currentIndex: 0,
                    testimonials: [{
                            quote: "BootKode changed my life. I went from knowing nothing about code to building my first web app in months. The mentorship was invaluable!",
                            name: "Aisha M.",
                            title: "Frontend Developer, Lagos",
                            avatar: "https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=687&q=80",
                            rating: 5
                        },
                        {
                            quote: "The structured roadmaps are a game-changer. Finally, a platform that understands the African context and helps you get job-ready!",
                            name: "Kingsley O.",
                            title: "Self-Taught Dev, Abuja",
                            avatar: "https://images.unsplash.com/photo-1507003211169-0a3dd782dab4?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=687&q=80",
                            rating: 4.5
                        },
                        {
                            quote: "As an NYSC member, BootKode's offline access was a lifesaver. I could learn even in remote areas and now I'm building my own startup!",
                            name: "Chidinma E.",
                            title: "Mobile Developer, Enugu",
                            avatar: "https://images.unsplash.com/photo-1544005313-94ddf0286df2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=688&q=80",
                            rating: 5
                        }
                    ],
                    next() {
                        this.currentIndex = (this.currentIndex + 1) % this.testimonials.length;
                    },
                    prev() {
                        this.currentIndex = (this.currentIndex - 1 + this.testimonials.length) % this.testimonials.length;
                    },
                    init() {
                        // Auto-rotate testimonials every 5 seconds
                        setInterval(() => this.next(), 5000);
                    }
                }
            }
        </script>
    </div>
</div>
