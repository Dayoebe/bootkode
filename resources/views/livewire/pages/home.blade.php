@php
    $primaryCtaUrl = auth()->check() ? route(auth()->user()->getDashboardRouteName()) : route('register');
    $courseUrl = auth()->check() ? route('student.course-catalog') : route('register');
    $stats = [
        'courses' => \App\Models\Learning\Course::query()->where('is_published', true)->where('is_approved', true)->count(),
        'lessons' => \App\Models\Learning\Lesson::query()->whereHas('section.course', fn ($query) => $query->where('is_published', true)->where('is_approved', true))->count(),
        'tracks' => \App\Models\Learning\CourseCategory::query()->has('courses')->count(),
    ];
    $featuredCourses = \App\Models\Learning\Course::query()->with('category')->where('is_published', true)->where('is_approved', true)->latest('id')->take(3)->get();
    $pathways = [
        ['number' => '01', 'title' => 'Choose your direction', 'copy' => 'Follow a structured learning path built around the skill and role you want to pursue.'],
        ['number' => '02', 'title' => 'Learn by building', 'copy' => 'Move through focused lessons, practical exercises, assessments, and portfolio-ready projects.'],
        ['number' => '03', 'title' => 'Get meaningful support', 'copy' => 'Use mentorship, peer learning, feedback, and AI-assisted tools when you need momentum.'],
        ['number' => '04', 'title' => 'Show what you can do', 'copy' => 'Turn completed work into visible progress, career assets, and verifiable certificates.'],
    ];
    $capabilities = [
        ['icon' => 'fa-route', 'title' => 'Guided pathways', 'copy' => 'Clear milestones replace disconnected tutorials and guesswork.'],
        ['icon' => 'fa-laptop-code', 'title' => 'Practical learning', 'copy' => 'Projects, exercises, quizzes, and code-focused assessments.'],
        ['icon' => 'fa-people-group', 'title' => 'Human support', 'copy' => 'Mentorship, study groups, reviews, and a learning community.'],
        ['icon' => 'fa-certificate', 'title' => 'Verified outcomes', 'copy' => 'Digital certificates connected to real course completion records.'],
        ['icon' => 'fa-mobile-screen', 'title' => 'Learn anywhere', 'copy' => 'A mobile-first experience with offline learning support.'],
        ['icon' => 'fa-briefcase', 'title' => 'Career momentum', 'copy' => 'Portfolio, CV, interview, and opportunity-readiness tools.'],
    ];
@endphp

<div class="bk-edge-to-edge overflow-hidden bg-[#f6f8fb]">
    <section class="relative isolate overflow-hidden bg-[#07152d] text-white">
        <div class="absolute inset-0 -z-10 opacity-90" aria-hidden="true">
            <div class="absolute -right-40 -top-64 h-[42rem] w-[42rem] rounded-full bg-cyan-400/15 blur-3xl"></div>
            <div class="absolute -bottom-80 -left-56 h-[44rem] w-[44rem] rounded-full bg-indigo-500/20 blur-3xl"></div>
            <div class="bk-hero-grid absolute inset-0 opacity-20"></div>
        </div>
        <div class="bk-shell grid min-h-[760px] gap-14 py-16 lg:grid-cols-[1.02fr_.98fr] lg:items-center lg:py-20">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-3 py-2 text-xs font-extrabold text-cyan-100 backdrop-blur">
                    <span class="h-2 w-2 rounded-full bg-[#8fe388]"></span>Technology education built around real progress
                </div>
                <h1 class="bk-display mt-7 text-[2.75rem] font-extrabold leading-[1.04] tracking-[-0.045em] text-white sm:text-6xl lg:text-[4.6rem]">
                    Build the skills.<br><span class="text-[#8fe388]">Prove the work.</span><br>Move forward.
                </h1>
                <p class="mt-7 max-w-2xl text-base font-medium leading-8 text-slate-300 sm:text-lg">BootKode brings structured courses, hands-on projects, mentorship, career tools, and verified credentials into one focused learning platform.</p>
                <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ $primaryCtaUrl }}" class="inline-flex min-h-14 items-center justify-center gap-3 rounded-full bg-[#8fe388] px-6 text-sm font-black text-[#07152d] shadow-xl shadow-emerald-950/20 transition hover:-translate-y-0.5 hover:bg-[#a8f0a2]">
                        {{ auth()->check() ? 'Continue learning' : 'Create your free account' }} <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                    <a href="{{ $courseUrl }}" class="inline-flex min-h-14 items-center justify-center rounded-full border border-white/20 bg-white/5 px-6 text-sm font-black text-white transition hover:border-white/40 hover:bg-white/10">Explore courses</a>
                </div>
                <div class="mt-10 flex flex-wrap gap-x-7 gap-y-3 text-sm font-bold text-slate-300">
                    <span class="inline-flex items-center gap-2"><i class="fas fa-check-circle text-[#8fe388]"></i> Start without payment</span>
                    <span class="inline-flex items-center gap-2"><i class="fas fa-check-circle text-[#8fe388]"></i> Mobile-first learning</span>
                    <span class="inline-flex items-center gap-2"><i class="fas fa-check-circle text-[#8fe388]"></i> Verifiable credentials</span>
                </div>
            </div>
            <div class="relative mx-auto w-full max-w-[560px] lg:ml-auto">
                <div class="absolute -inset-10 rounded-full bg-cyan-300/10 blur-3xl"></div>
                <div class="relative overflow-hidden rounded-[2rem] border border-white/15 bg-white/[0.08] p-3 shadow-2xl shadow-black/30 backdrop-blur-xl sm:p-4">
                    <div class="rounded-[1.45rem] bg-white p-5 text-slate-950 sm:p-6">
                        <div class="flex items-center justify-between gap-4">
                            <div><p class="text-xs font-black uppercase tracking-[0.16em] text-slate-400">Learning workspace</p><h2 class="mt-1 text-xl font-black">Your next milestone</h2></div>
                            <span class="grid h-11 w-11 place-items-center rounded-2xl bg-[#eafbe8] text-emerald-700"><i class="fas fa-bolt"></i></span>
                        </div>
                        <div class="mt-6 rounded-2xl bg-[#07152d] p-5 text-white">
                            <div class="flex items-center justify-between gap-4"><span class="rounded-full bg-white/10 px-3 py-1 text-[11px] font-extrabold text-cyan-100">CURRENT PATH</span><span class="text-xs font-bold text-slate-300">Module 4 of 8</span></div>
                            <h3 class="mt-7 text-xl font-black">Build a production-ready web application</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-300">Continue from database design into application logic and testing.</p>
                            <div class="mt-6 h-2 overflow-hidden rounded-full bg-white/10"><div class="h-full w-[52%] rounded-full bg-[#8fe388]"></div></div>
                            <div class="mt-3 flex justify-between text-xs font-bold text-slate-300"><span>Progress</span><span>52%</span></div>
                        </div>
                        <div class="mt-4 grid grid-cols-2 gap-3">
                            <div class="rounded-2xl border border-slate-200 p-4"><span class="grid h-9 w-9 place-items-center rounded-xl bg-cyan-50 text-cyan-700"><i class="fas fa-code text-sm"></i></span><p class="mt-4 text-xs font-bold text-slate-500">Next activity</p><p class="mt-1 text-sm font-black">Practical exercise</p></div>
                            <div class="rounded-2xl border border-slate-200 p-4"><span class="grid h-9 w-9 place-items-center rounded-xl bg-violet-50 text-violet-700"><i class="fas fa-comments text-sm"></i></span><p class="mt-4 text-xs font-bold text-slate-500">Support</p><p class="mt-1 text-sm font-black">Mentor & community</p></div>
                        </div>
                    </div>
                </div>
                <div class="absolute -bottom-6 -left-3 hidden items-center gap-3 rounded-2xl border border-white/20 bg-white p-4 text-slate-950 shadow-2xl sm:flex">
                    <span class="grid h-10 w-10 place-items-center rounded-xl bg-[#eafbe8] text-emerald-700"><i class="fas fa-certificate"></i></span>
                    <span><span class="block text-xs font-bold text-slate-500">Completion matters</span><span class="block text-sm font-black">Evidence you can share</span></span>
                </div>
            </div>
        </div>
    </section>

    <section class="border-b border-slate-200 bg-white">
        <div class="bk-shell grid grid-cols-3 divide-x divide-slate-200 py-7 sm:py-9">
            <div class="px-3 text-center sm:px-8"><p class="text-2xl font-black text-[#07152d] sm:text-3xl">{{ number_format($stats['courses']) }}</p><p class="mt-1 text-xs font-bold text-slate-500 sm:text-sm">Published courses</p></div>
            <div class="px-3 text-center sm:px-8"><p class="text-2xl font-black text-[#07152d] sm:text-3xl">{{ number_format($stats['lessons']) }}</p><p class="mt-1 text-xs font-bold text-slate-500 sm:text-sm">Structured lessons</p></div>
            <div class="px-3 text-center sm:px-8"><p class="text-2xl font-black text-[#07152d] sm:text-3xl">{{ number_format($stats['tracks']) }}</p><p class="mt-1 text-xs font-bold text-slate-500 sm:text-sm">Learning tracks</p></div>
        </div>
    </section>

    <section class="py-20 sm:py-24">
        <div class="bk-shell grid gap-8 lg:grid-cols-[.72fr_1.28fr] lg:gap-16">
            <div class="lg:sticky lg:top-28 lg:self-start">
                <span class="text-xs font-black uppercase tracking-[0.18em] text-teal-700">A clearer way to learn</span>
                <h2 class="bk-display mt-4 text-3xl font-extrabold leading-tight tracking-[-0.035em] text-[#07152d] sm:text-5xl">From curiosity to credible skill.</h2>
                <p class="mt-5 text-base font-medium leading-7 text-slate-600">Every part of the experience is designed to help you keep moving—not simply collect content.</p>
            </div>
            <div class="divide-y divide-slate-200 border-y border-slate-200">
                @foreach($pathways as $step)
                    <article class="group grid gap-4 py-7 sm:grid-cols-[80px_1fr] sm:gap-6 sm:py-9">
                        <span class="text-sm font-black text-teal-700">{{ $step['number'] }}</span>
                        <div><h3 class="text-xl font-black text-[#07152d] transition group-hover:text-teal-700 sm:text-2xl">{{ $step['title'] }}</h3><p class="mt-2 max-w-2xl text-sm font-medium leading-7 text-slate-600 sm:text-base">{{ $step['copy'] }}</p></div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-white py-20 sm:py-24">
        <div class="bk-shell">
            <div class="max-w-3xl"><span class="text-xs font-black uppercase tracking-[0.18em] text-teal-700">One connected platform</span><h2 class="bk-display mt-4 text-3xl font-extrabold leading-tight tracking-[-0.035em] text-[#07152d] sm:text-5xl">The tools around the course matter too.</h2><p class="mt-5 text-base font-medium leading-7 text-slate-600">Learning works better when practice, support, progress, and career preparation live in the same place.</p></div>
            <div class="mt-12 grid gap-px overflow-hidden rounded-[2rem] border border-slate-200 bg-slate-200 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($capabilities as $capability)
                    <article class="group bg-white p-7 transition hover:bg-[#f7fbff] sm:p-8">
                        <span class="grid h-12 w-12 place-items-center rounded-2xl bg-[#eef4fa] text-[#0a5c76] transition group-hover:bg-[#07152d] group-hover:text-white"><i class="fas {{ $capability['icon'] }}"></i></span>
                        <h3 class="mt-7 text-lg font-black text-[#07152d]">{{ $capability['title'] }}</h3><p class="mt-3 text-sm font-medium leading-6 text-slate-600">{{ $capability['copy'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-20 sm:py-24">
        <div class="bk-shell">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                <div class="max-w-2xl"><span class="text-xs font-black uppercase tracking-[0.18em] text-teal-700">Explore learning</span><h2 class="bk-display mt-4 text-3xl font-extrabold tracking-[-0.035em] text-[#07152d] sm:text-5xl">Start with a course that leads somewhere.</h2></div>
                <a href="{{ $courseUrl }}" class="inline-flex items-center gap-2 text-sm font-black text-[#07152d] hover:text-teal-700">Browse the catalogue <i class="fas fa-arrow-right text-xs"></i></a>
            </div>
            <div class="mt-10 grid gap-5 lg:grid-cols-3">
                @forelse($featuredCourses as $course)
                    <article class="group overflow-hidden rounded-[1.5rem] border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-xl hover:shadow-slate-900/10">
                        <div class="aspect-[16/10] overflow-hidden bg-slate-100"><img src="{{ $course->thumbnail ? asset('storage/' . $course->thumbnail) : asset('images/default-course.png') }}" alt="{{ $course->title }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-105"></div>
                        <div class="p-6">
                            <div class="flex items-center justify-between gap-3 text-xs font-extrabold"><span class="rounded-full bg-teal-50 px-3 py-1 text-teal-800">{{ $course->category?->name ?? 'Course' }}</span><span class="text-slate-500">{{ $course->total_lessons }} lessons</span></div>
                            <h3 class="mt-5 line-clamp-2 text-xl font-black leading-snug text-[#07152d]">{{ $course->title }}</h3><p class="mt-3 line-clamp-2 text-sm font-medium leading-6 text-slate-600">{{ \Illuminate\Support\Str::limit(strip_tags($course->description), 110) }}</p>
                            <a href="{{ auth()->check() ? route('course.view', $course) : route('register') }}" class="mt-6 inline-flex items-center gap-2 text-sm font-black text-teal-700">{{ auth()->check() ? 'Open course' : 'Start learning' }} <i class="fas fa-arrow-right text-xs"></i></a>
                        </div>
                    </article>
                @empty
                    <div class="rounded-[1.5rem] border border-dashed border-slate-300 bg-white p-8 text-sm font-semibold text-slate-600 lg:col-span-3">Published courses will appear here as they become available.</div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="bg-[#07152d] py-20 text-white sm:py-24">
        <div class="bk-shell grid gap-10 lg:grid-cols-[1fr_auto] lg:items-center">
            <div class="max-w-3xl"><span class="text-xs font-black uppercase tracking-[0.18em] text-[#8fe388]">Your next step can be small</span><h2 class="bk-display mt-4 text-3xl font-extrabold leading-tight tracking-[-0.035em] text-white sm:text-5xl">Choose one skill. Build one project. Create momentum.</h2><p class="mt-5 max-w-2xl text-base font-medium leading-7 text-slate-300">Create an account, find a path, and begin with a learning experience designed to help you finish.</p></div>
            <div class="flex flex-col gap-3 sm:flex-row lg:flex-col">
                <a href="{{ $primaryCtaUrl }}" class="inline-flex min-h-14 items-center justify-center gap-3 rounded-full bg-[#8fe388] px-7 text-sm font-black text-[#07152d] hover:bg-[#a8f0a2]">{{ auth()->check() ? 'Return to your dashboard' : 'Start learning free' }} <i class="fas fa-arrow-right text-xs"></i></a>
                <a href="{{ route('about') }}" class="inline-flex min-h-14 items-center justify-center rounded-full border border-white/20 px-7 text-sm font-black text-white hover:bg-white/10">Learn about BootKode</a>
            </div>
        </div>
    </section>
</div>
