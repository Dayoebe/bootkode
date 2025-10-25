<div class="p-4 md:p-8 bg-gray-50 dark:bg-gray-900 min-h-screen font-sans">
    <!-- Header Section -->
    <header class="text-center mb-16">
        <div class="relative">
            <h1 class="text-5xl md:text-6xl font-bold text-gray-900 dark:text-white mb-6 tracking-tight">
                Welcome to BootKode
            </h1>
            <div class="w-24 h-1 bg-blue-600 mx-auto mb-6 rounded-full"></div>
            <p class="text-xl md:text-2xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto leading-relaxed">
                Your comprehensive pathway to digital excellence, professional mentorship, and a thriving tech career in
                Africa's growing digital economy.
            </p>
            <div class="flex justify-center items-center mt-8 space-x-8 flex-wrap gap-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ number_format($stats['totalUsers']) }}+</div>
                    <div class="text-sm text-gray-500">Active Users</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ number_format($stats['totalCourses']) }}+</div>
                    <div class="text-sm text-gray-500">Courses Available</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $stats['successRate'] }}%</div>
                    <div class="text-sm text-gray-500">Success Rate</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ number_format($stats['totalLessons']) }}+</div>
                    <div class="text-sm text-gray-500">Total Lessons</div>
                </div>
            </div>
        </div>
    </header>

    <!-- Tab Navigation -->
    <div
        class="flex flex-wrap justify-center mb-12 gap-3 bg-white dark:bg-gray-800 p-2 rounded-2xl shadow-lg max-w-4xl mx-auto">
        <button wire:click="selectTab('student')"
            class="flex items-center space-x-3 px-8 py-4 rounded-xl font-semibold transition-all duration-300 {{ $activeTab === 'student' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50 dark:text-gray-300 dark:hover:bg-gray-700' }}">
            <i class="fas fa-user-graduate text-lg"></i>
            <span>Students</span>
            <span
                class="bg-blue-100 text-blue-600 px-2 py-1 rounded-full text-xs ml-2">{{ number_format($stats['totalStudents']) }}</span>
        </button>
        <button wire:click="selectTab('instructor')"
            class="flex items-center space-x-3 px-8 py-4 rounded-xl font-semibold transition-all duration-300 {{ $activeTab === 'instructor' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50 dark:text-gray-300 dark:hover:bg-gray-700' }}">
            <i class="fas fa-chalkboard-teacher text-lg"></i>
            <span>Instructors</span>
            <span
                class="bg-blue-100 text-blue-600 px-2 py-1 rounded-full text-xs ml-2">{{ number_format($stats['totalInstructors']) }}</span>
        </button>
        <button wire:click="selectTab('mentor')"
            class="flex items-center space-x-3 px-8 py-4 rounded-xl font-semibold transition-all duration-300 {{ $activeTab === 'mentor' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50 dark:text-gray-300 dark:hover:bg-gray-700' }}">
            <i class="fas fa-handshake text-lg"></i>
            <span>Mentors</span>
            <span
                class="bg-blue-100 text-blue-600 px-2 py-1 rounded-full text-xs ml-2">{{ number_format($stats['totalMentors']) }}</span>
        </button>
        <button wire:click="selectTab('admin')"
            class="flex items-center space-x-3 px-8 py-4 rounded-xl font-semibold transition-all duration-300 {{ $activeTab === 'admin' ? 'bg-blue-600 text-white shadow-md' : 'text-gray-600 hover:text-blue-600 hover:bg-blue-50 dark:text-gray-300 dark:hover:bg-gray-700' }}">
            <i class="fas fa-user-shield text-lg"></i>
            <span>Admins</span>
        </button>
    </div>

    <!-- Tab Content Container -->
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl p-8 md:p-12 ">

        <!-- Student Tab -->
        @if($activeTab === 'student')
            @include('livewire.manual-pages.guidelines.student-tab', ['stats' => $stats])
        @endif

        <!-- Instructor Tab -->
        @if($activeTab === 'instructor')
            @include('livewire.manual-pages.guidelines.instructor-tab', ['stats' => $stats])
        @endif

        <!-- Mentor Tab -->
        @if($activeTab === 'mentor')
            @include('livewire.manual-pages.guidelines.mentor-tab', ['stats' => $stats])
        @endif

        <!-- Admin Tab -->
        @if($activeTab === 'admin')
            @include('livewire.manual-pages.guidelines.admin-tab', ['stats' => $stats])
        @endif

    </div>

    <!-- Call to Action Section -->
    <div class="mt-16 bg-blue-600 rounded-3xl p-8 md:p-12 text-center text-white">
        <h3 class="text-3xl md:text-4xl font-bold mb-4">Ready to Transform Your Future?</h3>
        <p class="text-xl mb-8 text-blue-100 max-w-3xl mx-auto">
            Join over {{ number_format($stats['totalUsers']) }} learners,
            {{ number_format($stats['totalInstructors']) }} instructors, {{ number_format($stats['totalMentors']) }}
            mentors,
            and administrators who are building Africa's digital future through BootKode.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
            <a href="{{ route('register') }}"
                class="bg-white text-blue-600 px-8 py-4 rounded-xl font-semibold text-lg hover:bg-blue-50 transition-colors duration-300 shadow-lg inline-flex items-center">
                <i class="fas fa-rocket mr-2"></i>
                Get Started Today
            </a>
            <a href="#"
                class="border-2 border-white text-white px-8 py-4 rounded-xl font-semibold text-lg hover:bg-white hover:text-blue-600 transition-colors duration-300 inline-flex items-center">
                <i class="fas fa-calendar-alt mr-2"></i>
                Schedule a Demo
            </a>
        </div>
        <div class="mt-8 flex justify-center items-center space-x-8 text-blue-100 flex-wrap gap-4">
            <div class="flex items-center">
                <i class="fas fa-check mr-2"></i>
                <span>Free to start</span>
            </div>
            <div class="flex items-center">
                <i class="fas fa-check mr-2"></i>
                <span>No setup fees</span>
            </div>
            <div class="flex items-center">
                <i class="fas fa-check mr-2"></i>
                <span>24/7 support</span>
            </div>
            <div class="flex items-center">
                <i class="fas fa-check mr-2"></i>
                <span>{{ $stats['successRate'] }}% success rate</span>
            </div>
        </div>
    </div>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .shadow-2xl {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .shadow-lg {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        /* Custom animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
        }

        /* Hover effects */
        .hover-lift {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .hover-lift:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.15);
        }

        /* Dark mode improvements */
        .dark .bg-white {
            background-color: #1f2937;
        }

        .dark .shadow-2xl {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .roadmap-step {
                flex-direction: column;
                text-align: center;
            }

            .roadmap-line {
                display: none;
            }
        }
    </style>
</div>