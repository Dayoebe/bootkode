@php
    $primaryCtaUrl = auth()->check() ? route(auth()->user()->getDashboardRouteName()) : route('register');
    $courseUrl = route('courses.index');
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
        <div class="bk-shell grid gap-12 py-16 lg:grid-cols-[1.02fr_.98fr] lg:items-center lg:py-20">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-3 py-2 text-xs font-extrabold text-cyan-100 backdrop-blur">
                    <span class="h-2 w-2 rounded-full bg-[#8fe388]"></span>Technology education built around real progress
                </div>
                <h1 class="bk-display mt-7 text-4xl font-extrabold leading-[1.04] tracking-[-0.045em] text-white sm:text-6xl lg:text-6xl">
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
            <aside class="relative border-l border-white/20 pl-6 sm:pl-10">
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-brand-mint">Learn. Build. Grow.</p>
                <h2 class="mt-6 text-2xl font-bold leading-snug sm:text-3xl">A practical place to start.<br>A clear way to keep going.</h2>
                <ol class="mt-8 divide-y divide-white/15">
                    @foreach([['01', 'Learn with structure', 'Choose a course and work through focused lessons.'], ['02', 'Put ideas into practice', 'Build understanding through exercises and assessments.'], ['03', 'Make progress visible', 'Return to your lessons, review your work and keep growing.']] as [$number, $heading, $copy])
                        <li class="flex gap-5 py-5"><span class="pt-1 text-xs font-bold text-brand-mint">{{ $number }}</span><div><h3 class="font-bold">{{ $heading }}</h3><p class="mt-2 text-sm leading-6 text-slate-300">{{ $copy }}</p></div></li>
                    @endforeach
                </ol>
                <a href="{{ route('guideline') }}" class="mt-6 inline-flex min-h-11 items-center gap-3 text-sm font-bold text-brand-mint">How learning works <span aria-hidden="true">→</span></a>
            </aside>
        </div>
    </section>

    @if($stats['courses'] > 0)
    <section class="border-b border-slate-200 bg-white">
        <div class="bk-shell grid grid-cols-3 divide-x divide-slate-200 py-7 sm:py-9">
            <div class="px-3 text-center sm:px-8"><p class="text-2xl font-black text-[#07152d] sm:text-3xl">{{ number_format($stats['courses']) }}</p><p class="mt-1 text-xs font-bold text-slate-500 sm:text-sm">Published courses</p></div>
            <div class="px-3 text-center sm:px-8"><p class="text-2xl font-black text-[#07152d] sm:text-3xl">{{ number_format($stats['lessons']) }}</p><p class="mt-1 text-xs font-bold text-slate-500 sm:text-sm">Structured lessons</p></div>
            <div class="px-3 text-center sm:px-8"><p class="text-2xl font-black text-[#07152d] sm:text-3xl">{{ number_format($stats['tracks']) }}</p><p class="mt-1 text-xs font-bold text-slate-500 sm:text-sm">Learning tracks</p></div>
        </div>
    </section>

    @endif
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
            <div class="mt-12 grid gap-px overflow-hidden rounded-lg border border-slate-200 bg-slate-200 sm:grid-cols-2 lg:grid-cols-3">
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
                    <x-course-card :course="$course" />
                @empty
                    <div class="rounded-lg border border-dashed border-slate-300 bg-white p-8 text-sm font-semibold text-slate-600 lg:col-span-3">Published courses will appear here as they become available.</div>
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
