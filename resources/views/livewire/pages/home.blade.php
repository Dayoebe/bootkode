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
        ['title' => 'Frontend Developer', 'icon' => 'fa-window-maximize', 'color' => 'bg-red-600', 'copy' => 'HTML, CSS, JavaScript, React or Vue, and responsive interfaces.'],
        ['title' => 'Backend Developer', 'icon' => 'fa-server', 'color' => 'bg-orange-600', 'copy' => 'PHP, Laravel, APIs, databases, and secure server logic.'],
        ['title' => 'Full-Stack Developer', 'icon' => 'fa-layer-group', 'color' => 'bg-amber-500', 'copy' => 'Frontend, backend, deployment, and complete product builds.'],
        ['title' => 'Mobile Developer', 'icon' => 'fa-mobile-screen-button', 'color' => 'bg-yellow-500', 'copy' => 'Flutter or React Native workflows for app-ready products.'],
        ['title' => 'UI/UX Designer', 'icon' => 'fa-pen-ruler', 'color' => 'bg-lime-600', 'copy' => 'User flows, interface systems, prototypes, and product thinking.'],
        ['title' => 'WordPress Developer', 'icon' => 'fa-wordpress', 'color' => 'bg-green-600', 'copy' => 'Sites, themes, plugins, hosting, and client-ready delivery.'],
        ['title' => 'Freelance Developer', 'icon' => 'fa-briefcase', 'color' => 'bg-emerald-600', 'copy' => 'Portfolio, pricing, client communication, and income-ready skills.'],
    ];

    $productSignals = [
        ['title' => 'Africa-ready roadmaps', 'icon' => 'fa-route', 'class' => 'bg-blue-500', 'copy' => 'Defined career paths built for local learners and real opportunities.'],
        ['title' => 'Video, PDF, audio', 'icon' => 'fa-file-circle-play', 'class' => 'bg-orange-500', 'copy' => 'Flexible content for phones, low data, revision, and offline study.'],
        ['title' => 'AI learning support', 'icon' => 'fa-robot', 'class' => 'bg-violet-500', 'copy' => 'Recommendations, code help, CV tools, and interview practice.'],
        ['title' => 'Verified certification', 'icon' => 'fa-certificate', 'class' => 'bg-rose-500', 'copy' => 'Project-tied credentials with printable and shareable proof.'],
    ];

    $platformFeatures = [
        ['title' => 'Modular courses', 'icon' => 'fa-photo-film', 'class' => 'bg-red-500', 'copy' => 'Short video lessons, study packs, audio revision, and live examples.'],
        ['title' => 'Career roadmaps', 'icon' => 'fa-map-location-dot', 'class' => 'bg-orange-500', 'copy' => 'Beginner to advanced milestones with project checkpoints.'],
        ['title' => 'Smart AI tools', 'icon' => 'fa-wand-magic-sparkles', 'class' => 'bg-amber-500', 'copy' => 'Lesson recommendations, code explanations, and mock interview feedback.'],
        ['title' => 'Reviews and quizzes', 'icon' => 'fa-list-check', 'class' => 'bg-lime-600', 'copy' => 'Assignments, code submissions, mini-projects, and immediate checks.'],
        ['title' => 'Digital credentials', 'icon' => 'fa-qrcode', 'class' => 'bg-emerald-600', 'copy' => 'QR-ready certificates tied to courses, projects, and completion records.'],
        ['title' => 'Mentor community', 'icon' => 'fa-people-group', 'class' => 'bg-teal-500', 'copy' => 'Peer groups, mentor sessions, feedback, and practical support loops.'],
        ['title' => 'Gamified progress', 'icon' => 'fa-trophy', 'class' => 'bg-sky-500', 'copy' => 'Badges, streaks, learning points, and leaderboards for consistency.'],
        ['title' => 'Mobile PWA', 'icon' => 'fa-mobile-screen', 'class' => 'bg-indigo-500', 'copy' => 'Installable mobile-first experience with fast loading and offline fallback.'],
        ['title' => 'Operator dashboards', 'icon' => 'fa-gauge-high', 'class' => 'bg-fuchsia-500', 'copy' => 'Admin and instructor tools for courses, users, progress, and certificates.'],
    ];
@endphp

<div class="bk-edge-to-edge overflow-hidden bg-slate-50">
    <section class="relative overflow-hidden bg-slate-950 text-white">
        <x-icon-field density="dense" class="opacity-20" />

        <div class="bk-shell relative grid gap-8 py-12 sm:py-16 lg:min-h-[760px] lg:grid-cols-[0.95fr_1.05fr] lg:items-center lg:py-16">
            <div class="max-w-3xl">
                <span class="bk-eyebrow border-white/20 bg-white/10 text-white">Code. Certify. Conquer.</span>
                <h1 class="bk-display mt-5 text-3xl font-black leading-tight text-white sm:text-6xl lg:text-7xl">
                    BootKode
                </h1>
                <p class="mt-5 max-w-2xl text-base leading-7 text-slate-200 sm:text-lg">
                    Empowering Africa's youth with digital skills, mentorship, and careers through structured learning, practical projects, AI support, and verified certificates.
                </p>

                <div class="mt-7 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ $primaryCtaUrl }}" class="bk-primary-btn bg-white text-slate-950 hover:bg-slate-100">
                        {{ auth()->check() ? 'Continue learning' : 'Start learning free' }}
                        <i class="fas fa-arrow-right text-sm"></i>
                    </a>
                    <a href="{{ $courseUrl }}" class="bk-secondary-btn border-white/30 bg-white/10 text-white hover:bg-white/15">
                        Browse courses
                    </a>
                </div>

                <div class="mt-8 grid max-w-xl grid-cols-3 gap-2 sm:gap-3">
                    <div class="rounded-[8px] border border-white/15 bg-red-500 p-3 text-white shadow-lg shadow-red-950/20">
                        <p class="text-2xl font-black text-white">{{ number_format($stats['courses']) }}</p>
                        <p class="text-xs font-bold text-white/80">Courses</p>
                    </div>
                    <div class="rounded-[8px] border border-white/15 bg-sky-500 p-3 text-white shadow-lg shadow-sky-950/20">
                        <p class="text-2xl font-black text-white">{{ number_format($stats['lessons']) }}</p>
                        <p class="text-xs font-bold text-white/80">Lessons</p>
                    </div>
                    <div class="rounded-[8px] border border-white/15 bg-emerald-500 p-3 text-white shadow-lg shadow-emerald-950/20">
                        <p class="text-2xl font-black text-white">{{ number_format($stats['tracks']) }}</p>
                        <p class="text-xs font-bold text-white/80">Tracks</p>
                    </div>
                </div>
            </div>

            <x-learning-visual variant="dark" label="BootKode command center" class="hidden md:block lg:ml-auto" />
        </div>
    </section>

    <section class="bg-white py-6">
        <div class="bk-shell grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ($productSignals as $signal)
                <article class="bk-workflow-card group rounded-[8px] border border-slate-200 bg-white p-4 shadow-sm transition hover:-translate-y-1 hover:shadow-xl hover:shadow-slate-950/10">
                    <div class="flex items-start gap-3">
                        <span class="{{ $signal['class'] }} grid h-11 w-11 shrink-0 place-items-center rounded-[8px] text-white transition group-hover:scale-110">
                            <i class="fas {{ $signal['icon'] }}"></i>
                        </span>
                        <div class="min-w-0">
                            <h2 class="font-black text-slate-950">{{ $signal['title'] }}</h2>
                            <p class="mt-1 text-sm leading-5 text-slate-600">{{ $signal['copy'] }}</p>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="border-b border-slate-200 bg-white">
        <div class="bk-shell grid gap-3 py-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($tracks as $track)
                <a href="{{ $courseUrl }}" class="group flex items-start gap-3 rounded-[8px] border border-slate-200 bg-white p-4 transition hover:-translate-y-1 hover:border-slate-300 hover:shadow-lg hover:shadow-slate-900/5">
                    <span class="{{ $track['color'] }} grid h-11 w-11 shrink-0 place-items-center rounded-[8px] text-white transition group-hover:scale-110">
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
            <div class="max-w-3xl">
                <span class="bk-eyebrow">Platform engine</span>
                <h2 class="bk-display mt-3 text-3xl font-black text-slate-950 sm:text-4xl">A full learning ecosystem, not a loose course list</h2>
                <p class="mt-3 text-base leading-7 text-slate-600">
                    BootKode combines education, mentorship, certification, community, and platform operations into one mobile-first system.
                </p>
            </div>

            <div class="mt-8 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($platformFeatures as $feature)
                    <article class="bk-workflow-card rounded-[8px] border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex items-start gap-3">
                            <span class="{{ $feature['class'] }} grid h-11 w-11 shrink-0 place-items-center rounded-[8px] text-white">
                                <i class="fas {{ $feature['icon'] }}"></i>
                            </span>
                            <div class="min-w-0">
                                <h3 class="font-black text-slate-950">{{ $feature['title'] }}</h3>
                                <p class="mt-1 text-sm leading-6 text-slate-600">{{ $feature['copy'] }}</p>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-white py-12 sm:py-16 lg:py-20">
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
                            <a href="{{ auth()->check() ? route('course.view', $course) : route('register') }}" class="mt-4 inline-flex w-full items-center justify-center rounded-[8px] bg-slate-950 px-4 py-3 text-sm font-black text-white transition hover:bg-slate-800">
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
                        <div class="flex gap-4 rounded-[8px] border border-slate-200 bg-slate-50 p-4 transition hover:-translate-y-1 hover:bg-white hover:shadow-lg hover:shadow-slate-900/5">
                            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-[8px] bg-white text-teal-700 shadow-sm">
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
                        ['time' => '09:00', 'title' => 'Full-stack lesson block', 'copy' => 'Continue with backend foundations and data modeling.', 'color' => 'bg-blue-500'],
                        ['time' => '12:30', 'title' => 'Quiz checkpoint', 'copy' => 'Review module decisions before moving ahead.', 'color' => 'bg-fuchsia-500'],
                        ['time' => '16:00', 'title' => 'Portfolio practice', 'copy' => 'Turn the lesson into project evidence.', 'color' => 'bg-olive-500'],
                    ] as $event)
                        <div class="flex gap-4 p-4">
                            <span class="w-16 shrink-0 text-sm font-black text-slate-950">{{ $event['time'] }}</span>
                            <span>
                                <span class="flex items-center gap-2 font-black text-slate-950"><span class="h-2.5 w-2.5 rounded-full {{ $event['color'] === 'bg-olive-500' ? 'bk-bg-olive' : $event['color'] }}"></span>{{ $event['title'] }}</span>
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
            <x-learning-visual variant="dark" label="Project evidence studio" />
            <div>
                <span class="bk-eyebrow border-white/15 bg-white/10 text-white">Practice led</span>
                <h2 class="bk-display mt-3 text-3xl font-black text-white sm:text-4xl">Built for local relevance, real projects, and visible momentum</h2>
                <p class="mt-4 text-base leading-7 text-slate-300">
                    Learners move through relatable projects, practical reviews, mentorship, and certification instead of scattered tutorials that never become career proof.
                </p>
                <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('about') }}" class="bk-primary-btn bg-white text-slate-950 hover:bg-slate-100">About BootKode</a>
                    <a href="{{ route('contact') }}" class="bk-secondary-btn border-white/30 bg-white/10 text-white hover:bg-white/15">Talk to us</a>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white py-12 sm:py-16">
        <div class="bk-shell rounded-[8px] bg-slate-100 p-6 sm:p-8 lg:p-10">
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
