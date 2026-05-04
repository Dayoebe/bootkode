@php
    $primaryCtaUrl = auth()->check() ? route(auth()->user()->getDashboardRouteName()) : route('register');
    $courseUrl = auth()->check() ? route('student.course-catalog') : route('register');

    $stats = [
        'courses' => \App\Models\Learning\Course::query()->where('is_published', true)->where('is_approved', true)->count(),
        'lessons' => \App\Models\Learning\Lesson::query()
            ->whereHas('section.course', fn ($query) => $query->where('is_published', true)->where('is_approved', true))
            ->count(),
        'tracks' => \App\Models\Learning\CourseCategory::query()->has('courses')->count(),
    ];

    $featuredCourses = \App\Models\Learning\Course::query()
        ->with('category')
        ->where('is_published', true)
        ->where('is_approved', true)
        ->latest('id')
        ->take(4)
        ->get();

    $tracks = [
        ['title' => 'Programming', 'icon' => 'fa-code', 'color' => 'bg-teal-700', 'copy' => 'Python, problem solving, debugging, and projects.'],
        ['title' => 'Web Development', 'icon' => 'fa-laptop-code', 'color' => 'bg-sky-700', 'copy' => 'Frontend, backend, databases, auth, deployment.'],
        ['title' => 'Data Analysis', 'icon' => 'fa-chart-line', 'color' => 'bg-rose-700', 'copy' => 'Excel, SQL, Python, dashboards, reporting.'],
        ['title' => 'Cybersecurity', 'icon' => 'fa-shield-halved', 'color' => 'bg-amber-600', 'copy' => 'Security basics, web defense, monitoring, response.'],
    ];
@endphp

<div class="bk-edge-to-edge overflow-hidden bg-slate-50">
    <section class="relative min-h-[calc(100svh-7rem)] overflow-hidden bg-slate-950 text-white">
        <img
            src="{{ asset('img/dayo.png') }}"
            alt="BootKode founder in a technology workspace"
            class="absolute inset-0 h-full w-full object-cover opacity-[0.38]"
        >
        <div class="absolute inset-0 bg-slate-950/72"></div>
        <div class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-slate-950 to-transparent"></div>

        <div class="bk-shell relative flex min-h-[calc(100svh-7rem)] items-end pb-28 pt-16 sm:pb-12 lg:pt-20">
            <div class="max-w-4xl">
                <span class="bk-eyebrow border-white/20 bg-white/10 text-white">Code. Certify. Conquer.</span>
                <h1 class="bk-display mt-5 text-3xl font-black leading-tight text-white sm:text-6xl lg:text-7xl">
                    BootKode Academy
                </h1>
                <p class="mt-5 max-w-2xl text-base leading-7 text-slate-200 sm:text-lg">
                    Practical tech training for learners who want complete courses, mentor direction, certificates, and portfolio-ready project evidence.
                </p>

                <div class="mt-7 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ $primaryCtaUrl }}" class="bk-primary-btn bg-white text-slate-950 hover:bg-slate-100">
                        {{ auth()->check() ? 'Continue learning' : 'Start free' }}
                        <i class="fas fa-arrow-right text-sm"></i>
                    </a>
                    <a href="{{ $courseUrl }}" class="bk-secondary-btn border-white/30 bg-white/10 text-white hover:bg-white/15">
                        Browse courses
                    </a>
                </div>

                <div class="mt-8 grid max-w-xl grid-cols-3 gap-2 sm:gap-3">
                    <div class="rounded-2xl border border-white/15 bg-white/10 p-3 backdrop-blur">
                        <p class="text-2xl font-black text-white">{{ number_format($stats['courses']) }}</p>
                        <p class="text-xs font-bold text-slate-300">Courses</p>
                    </div>
                    <div class="rounded-2xl border border-white/15 bg-white/10 p-3 backdrop-blur">
                        <p class="text-2xl font-black text-white">{{ number_format($stats['lessons']) }}</p>
                        <p class="text-xs font-bold text-slate-300">Lessons</p>
                    </div>
                    <div class="rounded-2xl border border-white/15 bg-white/10 p-3 backdrop-blur">
                        <p class="text-2xl font-black text-white">{{ number_format($stats['tracks']) }}</p>
                        <p class="text-xs font-bold text-slate-300">Tracks</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="border-b border-slate-200 bg-white">
        <div class="bk-shell grid gap-3 py-4 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($tracks as $track)
                <a href="{{ $courseUrl }}" class="group flex items-start gap-3 rounded-2xl border border-slate-200 bg-white p-4 transition hover:-translate-y-0.5 hover:border-teal-200 hover:shadow-lg hover:shadow-slate-900/5">
                    <span class="{{ $track['color'] }} grid h-11 w-11 shrink-0 place-items-center rounded-2xl text-white">
                        <i class="fas {{ $track['icon'] }}"></i>
                    </span>
                    <span>
                        <span class="block font-black text-slate-950">{{ $track['title'] }}</span>
                        <span class="mt-1 block text-sm leading-5 text-slate-600">{{ $track['copy'] }}</span>
                    </span>
                </a>
            @endforeach
        </div>
    </section>

    <section class="bg-slate-50 py-12 sm:py-16 lg:py-20">
        <div class="bk-shell">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <span class="bk-eyebrow">Latest courses</span>
                    <h2 class="bk-display mt-3 text-3xl font-black text-slate-950 sm:text-4xl">Complete paths, not loose tutorials</h2>
                </div>
                <a href="{{ $courseUrl }}" class="bk-secondary-btn w-full sm:w-auto">View all courses</a>
            </div>

            <div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                @forelse ($featuredCourses as $course)
                    <article class="bk-card overflow-hidden">
                        <div class="aspect-[4/3] bg-slate-100">
                            <img
                                src="{{ $course->thumbnail ? asset('storage/' . $course->thumbnail) : asset('images/default-course.png') }}"
                                alt="{{ $course->title }}"
                                class="h-full w-full object-cover"
                            >
                        </div>
                        <div class="p-4">
                            <div class="flex items-center justify-between gap-2">
                                <span class="rounded-full bg-teal-50 px-2.5 py-1 text-xs font-black text-teal-800">{{ $course->category?->name ?? 'Course' }}</span>
                                <span class="text-xs font-bold text-slate-500">{{ $course->total_lessons }} lessons</span>
                            </div>
                            <h3 class="mt-3 line-clamp-2 text-lg font-black leading-snug text-slate-950">{{ $course->title }}</h3>
                            <p class="mt-2 line-clamp-3 text-sm leading-6 text-slate-600">{{ \Illuminate\Support\Str::limit(strip_tags($course->description), 130) }}</p>
                            <a href="{{ auth()->check() ? route('course.view', $course) : route('register') }}" class="mt-4 inline-flex w-full items-center justify-center rounded-2xl bg-slate-950 px-4 py-3 text-sm font-black text-white transition hover:bg-slate-800">
                                {{ auth()->check() ? 'Open course' : 'Start course' }}
                            </a>
                        </div>
                    </article>
                @empty
                    <div class="bk-card p-6 text-slate-600 sm:col-span-2 xl:col-span-4">
                        Courses will appear here once they are published.
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="bg-white py-12 sm:py-16 lg:py-20">
        <div class="bk-shell grid gap-8 lg:grid-cols-[0.95fr_1.05fr] lg:items-center">
            <div>
                <span class="bk-eyebrow">Learning rhythm</span>
                <h2 class="bk-display mt-3 text-3xl font-black text-slate-950 sm:text-4xl">A focused path from first lesson to portfolio proof</h2>
                <p class="mt-4 text-base leading-7 text-slate-600">
                    BootKode keeps the learner close to outcomes: structured modules, practical checks, mentor-ready milestones, and certificates tied to completion.
                </p>
                <div class="mt-6 grid gap-3">
                    @foreach ([
                        ['icon' => 'fa-route', 'title' => 'Choose a path', 'copy' => 'Start with a complete course that matches the skill you want.'],
                        ['icon' => 'fa-screwdriver-wrench', 'title' => 'Build the work', 'copy' => 'Practice with tasks, quizzes, and capstone-oriented lessons.'],
                        ['icon' => 'fa-certificate', 'title' => 'Show evidence', 'copy' => 'Earn certificates and keep project proof ready for opportunities.'],
                    ] as $step)
                        <div class="flex gap-4 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-white text-teal-700 shadow-sm">
                                <i class="fas {{ $step['icon'] }}"></i>
                            </span>
                            <div>
                                <h3 class="font-black text-slate-950">{{ $step['title'] }}</h3>
                                <p class="mt-1 text-sm leading-6 text-slate-600">{{ $step['copy'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bk-card overflow-hidden">
                <div class="bg-slate-950 p-4 text-white">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-black">Today on BootKode</p>
                        <span class="rounded-full bg-teal-400 px-3 py-1 text-xs font-black text-slate-950">Live</span>
                    </div>
                </div>
                <div class="divide-y divide-slate-200 bg-white">
                    @foreach ([
                        ['time' => '09:00', 'title' => 'Full-stack lesson block', 'copy' => 'Continue with backend foundations and data modeling.'],
                        ['time' => '12:30', 'title' => 'Quiz checkpoint', 'copy' => 'Review module decisions before moving ahead.'],
                        ['time' => '16:00', 'title' => 'Portfolio practice', 'copy' => 'Turn the lesson into project evidence.'],
                    ] as $event)
                        <div class="flex gap-4 p-4">
                            <span class="w-16 shrink-0 text-sm font-black text-slate-950">{{ $event['time'] }}</span>
                            <span>
                                <span class="block font-black text-slate-950">{{ $event['title'] }}</span>
                                <span class="mt-1 block text-sm leading-6 text-slate-600">{{ $event['copy'] }}</span>
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section class="bg-slate-950 py-12 text-white sm:py-16 lg:py-20">
        <div class="bk-shell grid gap-8 lg:grid-cols-[0.8fr_1fr] lg:items-center">
            <div class="overflow-hidden rounded-[2rem] border border-white/10 bg-white/5">
                <img src="{{ asset('img/dayo.png') }}" alt="Oyetoke Adedayo Ebenezer" class="h-[380px] w-full object-cover object-top">
            </div>
            <div>
                <span class="bk-eyebrow border-white/15 bg-white/10 text-white">Founder led</span>
                <h2 class="bk-display mt-3 text-3xl font-black text-white sm:text-4xl">Built around practical teaching, not empty content volume</h2>
                <p class="mt-4 text-base leading-7 text-slate-300">
                    BootKode grew from hands-on technology education and keeps that standard visible: learners need clear structure, real practice, and guidance they can act on.
                </p>
                <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('about') }}" class="bk-primary-btn bg-white text-slate-950 hover:bg-slate-100">About BootKode</a>
                    <a href="{{ route('contact') }}" class="bk-secondary-btn border-white/30 bg-white/10 text-white hover:bg-white/15">Talk to us</a>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white py-12 sm:py-16">
        <div class="bk-shell rounded-[2rem] bg-slate-100 p-6 sm:p-8 lg:p-10">
            <div class="grid gap-6 lg:grid-cols-[1fr_auto] lg:items-center">
                <div>
                    <h2 class="bk-display text-3xl font-black text-slate-950">Start with one complete course.</h2>
                    <p class="mt-3 max-w-2xl text-base leading-7 text-slate-600">
                        Create your account, choose a path, and move through lessons with a layout that works cleanly on desktop and phone.
                    </p>
                </div>
                <a href="{{ $primaryCtaUrl }}" class="bk-primary-btn w-full sm:w-auto">
                    {{ auth()->check() ? 'Open dashboard' : 'Create free account' }}
                </a>
            </div>
        </div>
    </section>
</div>
