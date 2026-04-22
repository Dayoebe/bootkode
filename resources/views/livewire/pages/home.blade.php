@php
    $primaryCtaUrl = auth()->check() ? route('dashboard') : route('register');

    $programs = [
        [
            'key' => 'frontend',
            'name' => 'Frontend Developer',
            'icon' => 'fa-laptop-code',
            'tone' => '#0ea5e9',
            'summary' => 'Build responsive, production-grade interfaces using HTML, CSS, JavaScript, and modern UI tooling.',
            'items' => [
                'HTML, CSS and JavaScript fundamentals',
                'UI architecture with reusable components',
                'Responsive and accessibility-first workflows',
                'Portfolio-ready capstone project',
            ],
        ],
        [
            'key' => 'backend',
            'name' => 'Backend Developer',
            'icon' => 'fa-server',
            'tone' => '#3b82f6',
            'summary' => 'Design secure APIs, manage data systems, and deploy scalable backend services with Laravel.',
            'items' => [
                'Laravel architecture and API design',
                'Database modeling and optimization',
                'Authentication, authorization, and security',
                'Testing and deployment practices',
            ],
        ],
        [
            'key' => 'mobile',
            'name' => 'Mobile App Developer',
            'icon' => 'fa-mobile-alt',
            'tone' => '#6366f1',
            'summary' => 'Create cross-platform apps with smooth UX, practical features, and real-world deployment workflows.',
            'items' => [
                'Flutter and React Native foundations',
                'State management and app architecture',
                'Mobile UI, UX, and performance',
                'Play Store and App Store release prep',
            ],
        ],
        [
            'key' => 'career',
            'name' => 'Career And Freelance Track',
            'icon' => 'fa-briefcase',
            'tone' => '#d946ef',
            'summary' => 'Turn skills into opportunities through portfolio strategy, interview prep, and freelance systems.',
            'items' => [
                'Portfolio and personal brand strategy',
                'Mock interviews and feedback loops',
                'Freelance client acquisition fundamentals',
                'Proposal writing and pricing systems',
            ],
        ],
    ];

    $testimonials = [
        [
            'quote' => 'BootKode moved me from beginner to paid frontend gigs in under a year. The roadmap and mentor reviews were the difference.',
            'name' => 'Aisha M.',
            'title' => 'Frontend Developer, Lagos',
            'avatar' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&w=400&q=80',
        ],
        [
            'quote' => 'Everything felt organized and practical. Instead of random tutorials, I had a clear system and weekly momentum.',
            'name' => 'Kingsley O.',
            'title' => 'Backend Learner, Abuja',
            'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a3dd782dab4?auto=format&fit=crop&w=400&q=80',
        ],
        [
            'quote' => 'The mobile-first setup made learning possible even with limited internet. I studied consistently during NYSC service.',
            'name' => 'Chidinma E.',
            'title' => 'Mobile Developer, Enugu',
            'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=400&q=80',
        ],
    ];

    $faqs = [
        [
            'q' => 'Is BootKode beginner-friendly?',
            'a' => 'Yes. Learning paths begin with fundamentals and progressively build toward real projects and job-ready outcomes.',
        ],
        [
            'q' => 'Can I learn from my phone?',
            'a' => 'Yes. The homepage and learning flow are built mobile-first, with offline-friendly materials and small-screen optimized layouts.',
        ],
        [
            'q' => 'Do I get mentorship support?',
            'a' => 'Yes. You can join mentor sessions, peer communities, and structured feedback loops during your learning journey.',
        ],
        [
            'q' => 'How do I start?',
            'a' => 'Create an account, choose a track, and follow the structured roadmap. Returning users can continue from their dashboard.',
        ],
    ];
@endphp

<div class="-mx-4 sm:-mx-6 md:-mx-8 lg:-mx-12 xl:-mx-16 2xl:-mx-20 bk-home">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Sora:wght@600;700;800&display=swap');

        .bk-home {
            --bg: #f5f7fb;
            --ink: #0f172a;
            --muted: #475569;
            --line: #dbe2ea;
            --card: #ffffff;
            --brand: #0f766e;
            --brand-dark: #115e59;
            --hero: #020617;
            font-family: 'Manrope', 'Outfit', sans-serif;
            color: var(--ink);
            background: var(--bg);
        }

        .bk-home .display {
            font-family: 'Sora', 'Outfit', sans-serif;
            letter-spacing: -0.02em;
            line-height: 1.08;
        }

        .bk-home section > .shell {
            width: min(1200px, calc(100% - 2rem));
            margin-inline: auto;
        }

        .bk-home .card {
            background: var(--card);
            border: 1px solid var(--line);
            border-radius: 16px;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.08);
        }

        .bk-home .kicker {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border-radius: 999px;
            background: #e2e8f0;
            color: #334155;
            padding: 0.45rem 0.9rem;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .bk-home .kicker::before {
            content: "";
            width: 0.42rem;
            height: 0.42rem;
            border-radius: 999px;
            background: var(--brand);
        }

        .bk-home .chip-nav {
            scrollbar-width: none;
        }

        .bk-home .chip-nav::-webkit-scrollbar {
            display: none;
        }

        .bk-home .hero-shape {
            position: absolute;
            border-radius: 999px;
            opacity: 0.26;
            filter: blur(2px);
            pointer-events: none;
        }

        .bk-home .hero-shape.one {
            width: 220px;
            height: 220px;
            right: -40px;
            top: 0;
            background: #f43f5e;
        }

        .bk-home .hero-shape.two {
            width: 180px;
            height: 180px;
            left: -60px;
            bottom: 10px;
            background: #0ea5e9;
        }

        .bk-home .hero-shape.three {
            width: 120px;
            height: 120px;
            right: 35%;
            bottom: -30px;
            background: #6b8e23;
        }

        .bk-home .program-btn {
            border: 1px solid var(--line);
            background: #fff;
            border-radius: 999px;
            padding: 0.65rem 0.95rem;
            font-size: 0.85rem;
            font-weight: 700;
            white-space: nowrap;
            transition: 160ms ease;
        }

        .bk-home .program-btn.active {
            background: #0f766e;
            border-color: #0f766e;
            color: #fff;
        }

        .bk-home .program-view {
            border-left: 6px solid var(--tone);
        }

        .bk-home [x-cloak] {
            display: none !important;
        }
    </style>

    <main class="pb-20">
        <section class="bg-white border-b border-slate-200">
            <div class="shell py-3 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-slate-700">
                    Admissions open for 2026: <span class="font-semibold text-slate-900">Web, Mobile, AI, and Career tracks.</span>
                </p>
                <div class="flex flex-wrap gap-2 text-sm">
                    <a href="{{ route('about') }}" class="px-3 py-1.5 rounded-full bg-slate-100 text-slate-700 hover:bg-slate-200 transition">About</a>
                    <a href="{{ route('contact') }}" class="px-3 py-1.5 rounded-full bg-slate-100 text-slate-700 hover:bg-slate-200 transition">Contact</a>
                    <a href="{{ $primaryCtaUrl }}" class="px-3 py-1.5 rounded-full bg-teal-700 text-white hover:bg-teal-800 transition">
                        {{ auth()->check() ? 'Dashboard' : 'Apply Now' }}
                    </a>
                </div>
            </div>
        </section>

        <section class="relative overflow-hidden bg-slate-950 text-white py-12 sm:py-16 lg:py-20">
            <span class="hero-shape one"></span>
            <span class="hero-shape two"></span>
            <span class="hero-shape three"></span>

            <div class="shell grid gap-8 lg:grid-cols-2 lg:items-center">
                <div>
                    <p class="kicker bg-white/10 text-slate-100">Mobile-first learning experience</p>
                    <h1 class="display mt-4 text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white">
                        Learn faster with a homepage built for your phone first.
                    </h1>
                    <p class="mt-4 text-sm sm:text-base text-slate-200 max-w-2xl leading-relaxed">
                        Navigate quickly, find the right program, and start learning without friction. This interface prioritizes clarity,
                        thumb-friendly actions, and simple section flow.
                    </p>

                    <div class="mt-6 grid grid-cols-3 gap-2 sm:gap-3 max-w-md">
                        <div class="card bg-white/10 border-white/20 p-3 text-center">
                            <p class="display text-lg sm:text-xl text-emerald-300">50K+</p>
                            <p class="text-[11px] text-slate-100">learners</p>
                        </div>
                        <div class="card bg-white/10 border-white/20 p-3 text-center">
                            <p class="display text-lg sm:text-xl text-emerald-300">100+</p>
                            <p class="text-[11px] text-slate-100">courses</p>
                        </div>
                        <div class="card bg-white/10 border-white/20 p-3 text-center">
                            <p class="display text-lg sm:text-xl text-emerald-300">24/7</p>
                            <p class="text-[11px] text-slate-100">support</p>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-col sm:flex-row gap-3">
                        <a href="{{ $primaryCtaUrl }}" class="w-full sm:w-auto inline-flex items-center justify-center rounded-xl px-5 py-3 text-sm font-bold bg-white text-teal-800 hover:bg-slate-100 transition">
                            {{ auth()->check() ? 'Continue Learning' : 'Start Free' }}
                        </a>
                        <a href="#programs" class="w-full sm:w-auto inline-flex items-center justify-center rounded-xl px-5 py-3 text-sm font-bold border border-white/60 text-white hover:bg-white/10 transition">
                            Explore Programs
                        </a>
                    </div>
                </div>

                <div class="card p-4 sm:p-5 lg:p-6 bg-white text-slate-900">
                    <p class="text-xs uppercase tracking-[0.08em] font-semibold text-slate-500">Today on BootKode</p>
                    <div class="mt-4 space-y-3">
                        <div class="rounded-xl border border-slate-200 p-3">
                            <p class="font-semibold text-sm text-slate-900">Frontend Sprint</p>
                            <p class="mt-1 text-xs text-slate-600">Build and deploy a responsive landing page in 90 minutes.</p>
                        </div>
                        <div class="rounded-xl border border-slate-200 p-3">
                            <p class="font-semibold text-sm text-slate-900">Mentor Review Session</p>
                            <p class="mt-1 text-xs text-slate-600">Live feedback on portfolio projects and code architecture.</p>
                        </div>
                        <div class="rounded-xl border border-slate-200 p-3">
                            <p class="font-semibold text-sm text-slate-900">Career Clinic</p>
                            <p class="mt-1 text-xs text-slate-600">Refine CV, GitHub profile, and interview storytelling.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="sticky top-16 z-20 bg-white/95 backdrop-blur border-y border-slate-200">
            <div class="shell py-3 overflow-x-auto chip-nav">
                <div class="flex items-center gap-2 w-max">
                    <a href="#why" class="px-3 py-1.5 rounded-full bg-slate-100 text-slate-700 text-sm font-semibold">Why BootKode</a>
                    <a href="#programs" class="px-3 py-1.5 rounded-full bg-slate-100 text-slate-700 text-sm font-semibold">Programs</a>
                    <a href="#workflow" class="px-3 py-1.5 rounded-full bg-slate-100 text-slate-700 text-sm font-semibold">How It Works</a>
                    <a href="#community" class="px-3 py-1.5 rounded-full bg-slate-100 text-slate-700 text-sm font-semibold">Community</a>
                    <a href="#stories" class="px-3 py-1.5 rounded-full bg-slate-100 text-slate-700 text-sm font-semibold">Stories</a>
                    <a href="#faq" class="px-3 py-1.5 rounded-full bg-slate-100 text-slate-700 text-sm font-semibold">FAQ</a>
                </div>
            </div>
        </section>

        <section id="why" class="py-12 sm:py-16 bg-white">
            <div class="shell">
                <div class="text-center">
                    <p class="kicker">Why BootKode</p>
                    <h2 class="display mt-3 text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900">Easy to navigate, easy to grow</h2>
                    <p class="mt-3 text-sm sm:text-base text-slate-600 max-w-2xl mx-auto">
                        Every part of this interface is optimized for quick scanning and clear next steps on small screens.
                    </p>
                </div>

                <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <article class="card p-4">
                        <div class="w-10 h-10 rounded-lg bg-red-100 text-red-600 flex items-center justify-center">
                            <i class="fas fa-route"></i>
                        </div>
                        <h3 class="mt-3 font-bold text-slate-900">Clear Path</h3>
                        <p class="mt-1 text-sm text-slate-600">Start with fundamentals and move to career-ready outputs in a structured flow.</p>
                    </article>
                    <article class="card p-4">
                        <div class="w-10 h-10 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center">
                            <i class="fas fa-mobile-screen"></i>
                        </div>
                        <h3 class="mt-3 font-bold text-slate-900">Phone Native Feel</h3>
                        <p class="mt-1 text-sm text-slate-600">Tap targets and stacked cards are sized for mobile comfort and speed.</p>
                    </article>
                    <article class="card p-4">
                        <div class="w-10 h-10 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="mt-3 font-bold text-slate-900">Mentor Support</h3>
                        <p class="mt-1 text-sm text-slate-600">Get practical feedback through communities, sessions, and guided reviews.</p>
                    </article>
                    <article class="card p-4">
                        <div class="w-10 h-10 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <h3 class="mt-3 font-bold text-slate-900">Proof of Skill</h3>
                        <p class="mt-1 text-sm text-slate-600">Earn project-backed credentials you can show to employers and clients.</p>
                    </article>
                </div>
            </div>
        </section>

        <section id="programs" class="py-12 sm:py-16 bg-slate-50" x-data="{ openProgram: 'frontend' }">
            <div class="shell">
                <div class="text-center">
                    <p class="kicker">Programs</p>
                    <h2 class="display mt-3 text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900">Choose your learning path</h2>
                </div>

                <div class="mt-6 overflow-x-auto chip-nav">
                    <div class="flex gap-2 w-max mx-auto">
                        @foreach ($programs as $program)
                            <button
                                type="button"
                                @click="openProgram = '{{ $program['key'] }}'"
                                :class="openProgram === '{{ $program['key'] }}' ? 'active' : ''"
                                class="program-btn"
                            >
                                {{ $program['name'] }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="mt-6 space-y-4">
                    @foreach ($programs as $program)
                        <article
                            x-show="openProgram === '{{ $program['key'] }}'"
                            x-cloak
                            class="card p-5 sm:p-6 program-view"
                            style="--tone: {{ $program['tone'] }};"
                        >
                            <div class="flex items-start gap-4">
                                <div class="w-11 h-11 rounded-xl text-white flex items-center justify-center flex-shrink-0" style="background: {{ $program['tone'] }};">
                                    <i class="fas {{ $program['icon'] }}"></i>
                                </div>
                                <div class="min-w-0">
                                    <h3 class="text-xl font-bold text-slate-900">{{ $program['name'] }}</h3>
                                    <p class="mt-2 text-sm sm:text-base text-slate-600">{{ $program['summary'] }}</p>
                                </div>
                            </div>

                            <ul class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-2">
                                @foreach ($program['items'] as $item)
                                    <li class="flex items-start gap-2 text-sm text-slate-700">
                                        <span class="mt-1.5 w-2 h-2 rounded-full" style="background: {{ $program['tone'] }};"></span>
                                        <span>{{ $item }}</span>
                                    </li>
                                @endforeach
                            </ul>

                            <div class="mt-5">
                                <a href="{{ $primaryCtaUrl }}" class="inline-flex items-center justify-center rounded-xl px-4 py-2.5 text-sm font-semibold text-white" style="background: {{ $program['tone'] }};">
                                    Start This Track <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="workflow" class="py-12 sm:py-16 bg-white">
            <div class="shell">
                <div class="text-center">
                    <p class="kicker">How It Works</p>
                    <h2 class="display mt-3 text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900">Simple three-step learning system</h2>
                </div>

                <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <article class="card p-5">
                        <p class="text-xs font-bold uppercase tracking-[0.08em] text-slate-500">Step 1</p>
                        <h3 class="mt-2 text-lg font-bold text-slate-900">Pick a track</h3>
                        <p class="mt-2 text-sm text-slate-600">Select your goal and begin with a guided roadmap tailored to your level.</p>
                    </article>
                    <article class="card p-5">
                        <p class="text-xs font-bold uppercase tracking-[0.08em] text-slate-500">Step 2</p>
                        <h3 class="mt-2 text-lg font-bold text-slate-900">Build projects</h3>
                        <p class="mt-2 text-sm text-slate-600">Complete practical tasks and receive structured feedback from mentors.</p>
                    </article>
                    <article class="card p-5">
                        <p class="text-xs font-bold uppercase tracking-[0.08em] text-slate-500">Step 3</p>
                        <h3 class="mt-2 text-lg font-bold text-slate-900">Launch outcomes</h3>
                        <p class="mt-2 text-sm text-slate-600">Publish your portfolio, earn credentials, and transition into work opportunities.</p>
                    </article>
                </div>
            </div>
        </section>

        <section id="community" class="py-12 sm:py-16 bg-slate-100">
            <div class="shell grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8 items-center">
                <div>
                    <p class="kicker">Community And Mentorship</p>
                    <h2 class="display mt-3 text-2xl sm:text-3xl font-extrabold text-slate-900">Learn with people, not in isolation</h2>
                    <p class="mt-3 text-sm sm:text-base text-slate-600">
                        BootKode combines structured lessons with communities, office hours, and accountability systems to keep momentum high.
                    </p>
                    <ul class="mt-4 space-y-3 text-sm text-slate-700">
                        <li class="flex items-start gap-2"><span class="mt-1.5 w-2 h-2 rounded-full bg-green-500"></span>Weekly mentor feedback sessions</li>
                        <li class="flex items-start gap-2"><span class="mt-1.5 w-2 h-2 rounded-full bg-cyan-500"></span>Peer groups for challenges and practice</li>
                        <li class="flex items-start gap-2"><span class="mt-1.5 w-2 h-2 rounded-full bg-violet-500"></span>Career support for jobs and freelance growth</li>
                    </ul>
                </div>

                <div class="card overflow-hidden">
                    <img
                        src="https://images.unsplash.com/photo-1551434678-e076c223a692?auto=format&fit=crop&w=1200&q=80"
                        alt="BootKode learners collaborating"
                        class="w-full h-64 sm:h-72 object-cover"
                    >
                    <div class="p-4 bg-white">
                        <p class="text-sm font-semibold text-slate-900">Live Cohort Sessions</p>
                        <p class="mt-1 text-sm text-slate-600">Join practical workshops focused on coding, architecture, and delivery.</p>
                    </div>
                </div>
            </div>
        </section>

        <section id="stories" class="py-12 sm:py-16 bg-white">
            <div class="shell">
                <div class="text-center">
                    <p class="kicker">Student Stories</p>
                    <h2 class="display mt-3 text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900">What learners are saying</h2>
                </div>

                <div class="mt-8 overflow-x-auto chip-nav">
                    <div class="flex gap-4 w-max pb-2">
                        @foreach ($testimonials as $story)
                            <article class="card p-5 w-[290px] sm:w-[340px] snap-start">
                                <p class="text-sm italic text-slate-700">"{{ $story['quote'] }}"</p>
                                <div class="mt-4 flex items-center gap-3">
                                    <img src="{{ $story['avatar'] }}" alt="{{ $story['name'] }}" class="w-11 h-11 rounded-full object-cover">
                                    <div>
                                        <p class="text-sm font-bold text-slate-900">{{ $story['name'] }}</p>
                                        <p class="text-xs text-slate-500">{{ $story['title'] }}</p>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <section id="faq" class="py-12 sm:py-16 bg-slate-50" x-data="{ open: 0 }">
            <div class="shell">
                <div class="text-center">
                    <p class="kicker">FAQ</p>
                    <h2 class="display mt-3 text-2xl sm:text-3xl font-extrabold text-slate-900">Quick answers before you start</h2>
                </div>

                <div class="mt-8 space-y-3 max-w-3xl mx-auto">
                    @foreach ($faqs as $idx => $faq)
                        <article class="card p-4 sm:p-5">
                            <button type="button" class="w-full flex items-start justify-between gap-4 text-left" @click="open = open === {{ $idx }} ? -1 : {{ $idx }}">
                                <span class="text-sm sm:text-base font-semibold text-slate-900">{{ $faq['q'] }}</span>
                                <i class="fas" :class="open === {{ $idx }} ? 'fa-minus text-slate-700' : 'fa-plus text-slate-500'"></i>
                            </button>
                            <p x-show="open === {{ $idx }}" x-cloak class="mt-3 text-sm text-slate-600 leading-relaxed">
                                {{ $faq['a'] }}
                            </p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="py-12 sm:py-16 bg-slate-900 text-white">
            <div class="shell text-center">
                <h2 class="display text-2xl sm:text-3xl lg:text-4xl font-extrabold">Ready to begin your tech journey?</h2>
                <p class="mt-3 text-sm sm:text-base text-slate-200 max-w-2xl mx-auto">
                    Create your account, choose a path, and start building real skills with structured support.
                </p>
                <div class="mt-6 flex flex-col sm:flex-row items-center justify-center gap-3">
                    <a href="{{ $primaryCtaUrl }}" class="w-full sm:w-auto inline-flex items-center justify-center rounded-xl px-5 py-3 text-sm font-bold bg-white text-slate-900 hover:bg-slate-100 transition">
                        {{ auth()->check() ? 'Open Dashboard' : 'Create Free Account' }}
                    </a>
                    <a href="{{ route('contact') }}" class="w-full sm:w-auto inline-flex items-center justify-center rounded-xl px-5 py-3 text-sm font-bold border border-white/50 text-white hover:bg-white/10 transition">
                        Talk To Admissions
                    </a>
                </div>
            </div>
        </section>
    </main>
</div>
