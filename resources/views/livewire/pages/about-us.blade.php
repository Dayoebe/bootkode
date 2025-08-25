<div class="min-h-screen bg-white">
    <!-- Hero Section -->
    <section class="relative bg-slate-900 text-white py-20">
        <div class="absolute inset-0 bg-slate-800 opacity-50"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="animate-fade-in-up">
                <h1 class="text-5xl md:text-6xl font-bold mb-6">
                    <i class="fas fa-code text-green-500 mr-4"></i>
                    About BootKode
                </h1>
                <p class="text-xl md:text-2xl text-slate-300 max-w-4xl mx-auto leading-relaxed">
                    "Code. Certify. Conquer." — Empowering Africa's Youth with Digital Skills, Mentorship & Careers.
                </p>
            </div>
        </div>

        <!-- Floating Stats Cards -->
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 text-center border border-white/20">
                    <div class="text-3xl font-bold text-green-400">{{ number_format($stats['total_users']) }}</div>
                    <div class="text-slate-300 text-sm">Total Users</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 text-center border border-white/20">
                    <div class="text-3xl font-bold text-blue-400">{{ number_format($stats['total_courses']) }}</div>
                    <div class="text-slate-300 text-sm">Published Courses</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 text-center border border-white/20">
                    <div class="text-3xl font-bold text-purple-400">{{ number_format($stats['certificates_issued']) }}
                    </div>
                    <div class="text-slate-300 text-sm">Certificates Issued</div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 text-center border border-white/20">
                    <div class="text-3xl font-bold text-orange-400">{{ number_format($stats['total_lessons']) }}</div>
                    <div class="text-slate-300 text-sm">Total Lessons</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Our Story -->
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-slate-900 mb-6">
                    <i class="fas fa-info-circle text-green-500 mr-3"></i>
                    Our Story
                </h2>
                <div class="max-w-4xl mx-auto bg-slate-50 rounded-2xl p-8 border border-slate-200">
                    <p class="text-lg text-slate-700 leading-relaxed">
                        BootKode is a transformative digital education platform designed to bridge the skill gap for
                        Africa's youth through practical, job-oriented coding and digital skills training. Founded under
                        Wireless Computer Services, we focus on making tech education accessible, contextual, and
                        impactful for underserved communities across the continent.
                    </p>
                </div>
            </div>

            <!-- Detailed Statistics Grid -->
            <div class="mb-20">
                <h3 class="text-3xl font-bold text-slate-900 text-center mb-12">Platform Statistics</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-slate-50 rounded-xl p-6 border border-slate-200 hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <i class="fas fa-users text-2xl text-blue-500"></i>
                            <span
                                class="text-2xl font-bold text-slate-900">{{ number_format($stats['total_users']) }}</span>
                        </div>
                        <h4 class="font-semibold text-slate-900">Total Users</h4>
                        <p class="text-sm text-slate-600">Registered learners and educators</p>
                    </div>

                    <div class="bg-slate-50 rounded-xl p-6 border border-slate-200 hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <i class="fas fa-book-open text-2xl text-green-500"></i>
                            <span
                                class="text-2xl font-bold text-slate-900">{{ number_format($stats['total_courses']) }}</span>
                        </div>
                        <h4 class="font-semibold text-slate-900">Published Courses</h4>
                        <p class="text-sm text-slate-600">Approved and live courses</p>
                    </div>

                    <div class="bg-slate-50 rounded-xl p-6 border border-slate-200 hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <i class="fas fa-play-circle text-2xl text-purple-500"></i>
                            <span
                                class="text-2xl font-bold text-slate-900">{{ number_format($stats['total_lessons']) }}</span>
                        </div>
                        <h4 class="font-semibold text-slate-900">Total Lessons</h4>
                        <p class="text-sm text-slate-600">Learning modules available</p>
                    </div>

                    <div class="bg-slate-50 rounded-xl p-6 border border-slate-200 hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <i class="fas fa-certificate text-2xl text-orange-500"></i>
                            <span
                                class="text-2xl font-bold text-slate-900">{{ number_format($stats['certificates_issued']) }}</span>
                        </div>
                        <h4 class="font-semibold text-slate-900">Certificates Issued</h4>
                        <p class="text-sm text-slate-600">Successfully completed courses</p>
                    </div>

                    <div class="bg-slate-50 rounded-xl p-6 border border-slate-200 hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <i class="fas fa-layer-group text-2xl text-red-500"></i>
                            <span
                                class="text-2xl font-bold text-slate-900">{{ number_format($stats['course_categories']) }}</span>
                        </div>
                        <h4 class="font-semibold text-slate-900">Course Categories</h4>
                        <p class="text-sm text-slate-600">Different learning tracks</p>
                    </div>

                    <div class="bg-slate-50 rounded-xl p-6 border border-slate-200 hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <i class="fas fa-chalkboard-teacher text-2xl text-indigo-500"></i>
                            <span
                                class="text-2xl font-bold text-slate-900">{{ number_format($stats['active_instructors']) }}</span>
                        </div>
                        <h4 class="font-semibold text-slate-900">Active Instructors</h4>
                        <p class="text-sm text-slate-600">Expert educators on platform</p>
                    </div>

                    <div class="bg-slate-50 rounded-xl p-6 border border-slate-200 hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <i class="fas fa-trophy text-2xl text-yellow-500"></i>
                            <span
                                class="text-2xl font-bold text-slate-900">{{ number_format($stats['completed_courses']) }}</span>
                        </div>
                        <h4 class="font-semibold text-slate-900">Course Completions</h4>
                        <p class="text-sm text-slate-600">Unique courses completed</p>
                    </div>

                    <div class="bg-slate-50 rounded-xl p-6 border border-slate-200 hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <i class="fas fa-clock text-2xl text-teal-500"></i>
                            <span
                                class="text-2xl font-bold text-slate-900">{{ number_format(round($stats['total_course_hours'] / 60)) }}h</span>
                        </div>
                        <h4 class="font-semibold text-slate-900">Total Course Hours</h4>
                        <p class="text-sm text-slate-600">Learning content available</p>
                    </div>
                </div>
            </div>

            <!-- Mission and Vision -->
            <div class="grid md:grid-cols-2 gap-8 mb-20">
                <div class="bg-blue-50 rounded-2xl p-8 border-l-4 border-blue-500">
                    <h3 class="text-2xl font-bold text-slate-900 mb-4 flex items-center">
                        <i class="fas fa-bullseye text-blue-500 mr-3"></i>
                        Mission Statement
                    </h3>
                    <p class="text-slate-700 leading-relaxed">
                        To empower the next generation of African tech talent by providing accessible, practical, and
                        world-class digital education—bridging the skill gap, fostering innovation, and creating career
                        pathways in tech for underserved youth across the continent.
                    </p>
                </div>

                <div class="bg-green-50 rounded-2xl p-8 border-l-4 border-green-500">
                    <h3 class="text-2xl font-bold text-slate-900 mb-4 flex items-center">
                        <i class="fas fa-rocket text-green-500 mr-3"></i>
                        Vision Statement
                    </h3>
                    <p class="text-slate-700 leading-relaxed">
                        To become Africa's leading grassroots coding bootcamp and digital career accelerator—building
                        one million skilled tech professionals by 2030, driving economic growth, and making Africa a
                        global hub for tech innovation and entrepreneurship.
                    </p>
                </div>
            </div>

            <!-- Team Statistics -->
            <div class="mb-20">
                <h3 class="text-3xl font-bold text-slate-900 text-center mb-12">Our Team Breakdown</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($teamStats as $stat)
                        <div class="bg-{{ $stat['color'] }}-50 rounded-xl p-6 border border-{{ $stat['color'] }}-200">
                            <div class="flex items-center justify-between mb-4">
                                <i class="{{ $stat['icon'] }} text-2xl text-{{ $stat['color'] }}-500"></i>
                                <span class="text-2xl font-bold text-slate-900">{{ $stat['count'] }}</span>
                            </div>
                            <h4 class="font-semibold text-slate-900">{{ $stat['label'] }}</h4>
                            <p class="text-sm text-{{ $stat['color'] }}-600">Active team members</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Founder Section -->
            <div class="bg-slate-50 rounded-2xl p-8 mb-20 border border-slate-200">
                <h3 class="text-3xl font-bold text-slate-900 mb-8 text-center">
                    <i class="fas fa-user-tie text-green-500 mr-3"></i>
                    Meet Our Founder
                </h3>
                <div class="flex flex-col lg:flex-row items-center gap-8">
                    <div class="flex-shrink-0">
                        <img src="{{ asset('img/dayo.png') }}"
                            alt="Oyetoke Adedayo Ebenezer, Founder of BootKode"
                            class="w-48 h-48 rounded-full object-cover shadow-xl border-4 border-white">
                    </div>                    
                    <div class="flex-1">
                        <h4 class="text-2xl font-bold text-slate-900 mb-2">Oyetoke Adedayo Ebenezer</h4>
                        <p class="text-green-600 text-lg italic mb-4">Full Stack Developer | Educator | Entrepreneur |
                            Founder, Wireless Computer Services</p>
                        <p class="text-slate-700 leading-relaxed">
                            Adedayo is a dynamic, self-taught full-stack developer with expertise in Laravel, Tailwind,
                            Alpine.js, and more. With a background in teaching Mathematics and Physics during his NYSC
                            service, he founded Wireless Computer Services—a grassroots tech empowerment brand focused
                            on teaching computer basics, coding, and digital skills to youth. His vision drives BootKode
                            to empower Africa's next generation through innovative education.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Recent Announcements -->
            @if ($recentAnnouncements->count() > 0)
                <div class="mb-20">
                    <h3 class="text-3xl font-bold text-slate-900 text-center mb-12">
                        <i class="fas fa-bullhorn text-blue-500 mr-3"></i>
                        Recent Announcements
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach ($recentAnnouncements as $announcement)
                            <div
                                class="bg-white rounded-xl p-6 border border-slate-200 shadow-sm hover:shadow-lg transition-shadow">
                                <div class="flex items-center justify-between mb-4">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Announcement
                                    </span>
                                    <span
                                        class="text-xs text-slate-500">{{ $announcement->published_at->format('M j, Y') }}</span>
                                </div>
                                <h4 class="font-semibold text-slate-900 mb-2">{{ $announcement->title }}</h4>
                                <p class="text-sm text-slate-600 line-clamp-3">
                                    {{ Str::limit(strip_tags($announcement->content), 100) }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- System Status -->
            @if ($systemStatus->count() > 0)
                <div class="mb-20">
                    <h3 class="text-3xl font-bold text-slate-900 text-center mb-12">
                        <i class="fas fa-server text-green-500 mr-3"></i>
                        System Status
                    </h3>
                    <div class="space-y-4">
                        @foreach ($systemStatus as $status)
                            <div
                                class="bg-white rounded-xl p-6 border-l-4 border-{{ $status->severity === 'high' ? 'red' : ($status->severity === 'medium' ? 'yellow' : 'green') }}-500 shadow-sm">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-3">
                                        <i
                                            class="fas fa-{{ $status->resolved_at ? 'check-circle text-green-500' : 'exclamation-triangle text-yellow-500' }}"></i>
                                        <h4 class="font-semibold text-slate-900">{{ $status->title }}</h4>
                                    </div>
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                                {{ $status->resolved_at ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $status->resolved_at ? 'Resolved' : 'Ongoing' }}
                                    </span>
                                </div>
                                <p class="text-sm text-slate-600 mb-2">{{ $status->description }}</p>
                                <div class="text-xs text-slate-500">
                                    Started: {{ $status->started_at->format('M j, Y g:i A') }}
                                    @if ($status->resolved_at)
                                        | Resolved: {{ $status->resolved_at->format('M j, Y g:i A') }}
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- FAQ Section -->
            @if ($faqs->count() > 0)
                <div class="mb-20">
                    <h3 class="text-3xl font-bold text-slate-900 text-center mb-12">
                        <i class="fas fa-question-circle text-purple-500 mr-3"></i>
                        Frequently Asked Questions
                    </h3>
                    <div class="max-w-4xl mx-auto space-y-4">
                        @foreach ($faqs as $index => $faq)
                            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                                <button
                                    class="w-full text-left px-6 py-4 focus:outline-none hover:bg-slate-50 transition-colors"
                                    onclick="toggleFaq({{ $index }})">
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-semibold text-slate-900">{{ $faq->question }}</h4>
                                        <i class="fas fa-chevron-down text-slate-400 transition-transform"
                                            id="icon-{{ $index }}"></i>
                                    </div>
                                </button>
                                <div class="px-6 pb-4 hidden" id="answer-{{ $index }}">
                                    <div class="border-t border-slate-200 pt-4">
                                        <p class="text-slate-700">{{ $faq->answer }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Team Overview -->
            <div class="bg-slate-50 rounded-2xl p-8 mb-20 border border-slate-200">
                <h3 class="text-3xl font-bold text-slate-900 mb-6 text-center">
                    <i class="fas fa-users text-blue-500 mr-3"></i>
                    Our Team Philosophy
                </h3>
                <p class="text-lg text-slate-700 leading-relaxed mb-6">
                    BootKode is committed to building a diverse, inclusive, and mission-aligned team. We prioritize
                    youth, women, and persons with disabilities, onboarding remote talent from across Africa. Our growth
                    plan (2025–2027) includes:
                </p>
                <div class="grid md:grid-cols-2 gap-8">
                    <div>
                        <h4 class="font-semibold text-slate-900 mb-3">Technical Team</h4>
                        <ul class="space-y-2 text-slate-700">
                            <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i>Full Stack
                                Laravel Developers</li>
                            <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i>Mobile App
                                Developers</li>
                            <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i>QA Engineers
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold text-slate-900 mb-3">Content & Learning Team</h4>
                        <ul class="space-y-2 text-slate-700">
                            <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i>Content
                                Writers</li>
                            <li class="flex items-center"><i
                                    class="fas fa-check text-green-500 mr-2"></i>Instructional Designers</li>
                            <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i>Project
                                Reviewers</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold text-slate-900 mb-3">Marketing & Growth Team</h4>
                        <ul class="space-y-2 text-slate-700">
                            <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i>Social Media
                                Managers</li>
                            <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i>SEO
                                Specialists</li>
                            <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i>Brand
                                Partnerships Leads</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold text-slate-900 mb-3">Business/Admin Team</h4>
                        <ul class="space-y-2 text-slate-700">
                            <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i>Operations
                                Managers</li>
                            <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i>HR
                                Coordinators</li>
                            <li class="flex items-center"><i class="fas fa-check text-green-500 mr-2"></i>Finance Team
                            </li>
                        </ul>
                    </div>
                </div>
                <p class="text-slate-700 mt-6 text-center italic">
                    BootKode is not a solo founder's dream—it is a platform of people empowering people, and that
                    mission starts with the team.
                </p>
            </div>

            <!-- CTA Section -->
            <div class="text-center">
                <div class="bg-slate-900 rounded-2xl p-12 text-white">
                    <h3 class="text-3xl font-bold mb-4">Ready to Join Our Mission?</h3>
                    <p class="text-xl text-slate-300 mb-8 max-w-2xl mx-auto">
                        Whether you're looking to learn, teach, or partner with us, we'd love to hear from you.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('contact') }}"
                            class="inline-flex items-center px-8 py-3 bg-green-500 text-white font-semibold rounded-xl hover:bg-green-600 transition-colors">
                            <i class="fas fa-envelope mr-2"></i>
                            Get in Touch
                        </a>
                        <a href="{{ route('courses.available') }}"
                            class="inline-flex items-center px-8 py-3 bg-white text-slate-900 font-semibold rounded-xl hover:bg-slate-100 transition-colors">
                            <i class="fas fa-book mr-2"></i>
                            Explore Courses
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        function toggleFaq(index) {
            const answer = document.getElementById(`answer-${index}`);
            const icon = document.getElementById(`icon-${index}`);

            if (answer.classList.contains('hidden')) {
                answer.classList.remove('hidden');
                icon.classList.add('rotate-180');
            } else {
                answer.classList.add('hidden');
                icon.classList.remove('rotate-180');
            }
        }

        // Add scroll animations
        document.addEventListener('DOMContentLoaded', function() {
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-fade-in-up');
                    }
                });
            }, observerOptions);

            // Observe all stat cards and content sections
            document.querySelectorAll('.bg-slate-50, .bg-white, .bg-blue-50, .bg-green-50').forEach(el => {
                observer.observe(el);
            });
        });
    </script>

    <style>
        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out forwards;
        }

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

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .hover\:shadow-lg:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .transition-shadow {
            transition: box-shadow 0.15s ease-in-out;
        }

        .transition-colors {
            transition: background-color 0.15s ease-in-out, color 0.15s ease-in-out;
        }

        .transition-transform {
            transition: transform 0.15s ease-in-out;
        }

        .rotate-180 {
            transform: rotate(180deg);
        }
    </style>

</div>
