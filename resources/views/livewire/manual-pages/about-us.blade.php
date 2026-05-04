<div class="bk-edge-to-edge bg-slate-50">
    <section class="relative overflow-hidden bg-slate-950 text-white">
        <img src="{{ asset('img/dayo.png') }}" alt="BootKode founder" class="absolute inset-0 h-full w-full object-cover object-top opacity-[0.26]">
        <div class="absolute inset-0 bg-slate-950/78"></div>

        <div class="bk-shell relative py-14 sm:py-20 lg:py-24">
            <div class="max-w-4xl">
                <span class="bk-eyebrow border-white/15 bg-white/10 text-white">About BootKode</span>
                <h1 class="bk-display mt-4 text-3xl font-black leading-tight text-white sm:text-5xl lg:text-6xl">
                    Practical tech education for Africa's next builders
                </h1>
                <p class="mt-5 max-w-2xl text-base leading-7 text-slate-300 sm:text-lg">
                    BootKode bridges the skill gap with complete courses, mentorship, certificates, and project-driven learning for people building real digital careers.
                </p>
            </div>

            <div class="mt-9 grid grid-cols-2 gap-3 lg:grid-cols-4">
                @foreach ([
                    ['label' => 'Users', 'value' => $stats['total_users'] ?? 0, 'color' => 'text-teal-300'],
                    ['label' => 'Courses', 'value' => $stats['total_courses'] ?? 0, 'color' => 'text-sky-300'],
                    ['label' => 'Certificates', 'value' => $stats['certificates_issued'] ?? 0, 'color' => 'text-rose-300'],
                    ['label' => 'Lessons', 'value' => $stats['total_lessons'] ?? 0, 'color' => 'text-amber-300'],
                ] as $item)
                    <div class="rounded-2xl border border-white/10 bg-white/10 p-4 backdrop-blur">
                        <p class="text-2xl font-black {{ $item['color'] }}">{{ number_format($item['value']) }}</p>
                        <p class="mt-1 text-sm font-bold text-slate-300">{{ $item['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-white py-12 sm:py-16 lg:py-20">
        <div class="bk-shell grid gap-8 lg:grid-cols-[0.85fr_1.15fr] lg:items-center">
            <div>
                <span class="bk-eyebrow">Our story</span>
                <h2 class="bk-display mt-3 text-3xl font-black text-slate-950 sm:text-4xl">Built from teaching, software practice, and learner support</h2>
                <p class="mt-4 text-base leading-7 text-slate-600">
                    BootKode is a digital education platform under Wireless Computer Services. It focuses on accessible, practical, and career-aware technology training for learners who need structure, accountability, and visible progress.
                </p>
                <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ auth()->check() ? route(auth()->user()->getDashboardRouteName()) : route('register') }}" class="bk-primary-btn">Start learning</a>
                    <a href="{{ route('contact') }}" class="bk-secondary-btn">Contact BootKode</a>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <article class="bk-card p-5">
                    <i class="fas fa-bullseye text-2xl text-teal-700"></i>
                    <h3 class="mt-4 text-xl font-black text-slate-950">Mission</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">
                        Empower the next generation of African tech talent with practical education, career direction, and credible proof of skill.
                    </p>
                </article>
                <article class="bk-card p-5">
                    <i class="fas fa-rocket text-2xl text-rose-700"></i>
                    <h3 class="mt-4 text-xl font-black text-slate-950">Vision</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">
                        Build a strong grassroots technology learning ecosystem that helps learners move into real opportunity.
                    </p>
                </article>
                <article class="bk-card p-5 sm:col-span-2">
                    <i class="fas fa-graduation-cap text-2xl text-sky-700"></i>
                    <h3 class="mt-4 text-xl font-black text-slate-950">Learning standard</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">
                        Every course should lead to practice, review, confidence, and evidence. Learners need more than content volume; they need a clear path from first lesson to finished work.
                    </p>
                </article>
            </div>
        </div>
    </section>

    <section class="bg-slate-50 py-12 sm:py-16 lg:py-20">
        <div class="bk-shell">
            <div class="text-center">
                <span class="bk-eyebrow">Platform depth</span>
                <h2 class="bk-display mt-3 text-3xl font-black text-slate-950 sm:text-4xl">The operating picture</h2>
            </div>

            <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([
                    ['icon' => 'fa-layer-group', 'label' => 'Course categories', 'value' => $stats['course_categories'] ?? 0, 'copy' => 'Different learning tracks'],
                    ['icon' => 'fa-chalkboard-user', 'label' => 'Active instructors', 'value' => $stats['active_instructors'] ?? 0, 'copy' => 'Educators and trainers'],
                    ['icon' => 'fa-trophy', 'label' => 'Completed courses', 'value' => $stats['completed_courses'] ?? 0, 'copy' => 'Completion evidence'],
                    ['icon' => 'fa-clock', 'label' => 'Course hours', 'value' => round(($stats['total_course_hours'] ?? 0) / 60), 'suffix' => 'h', 'copy' => 'Available learning time'],
                ] as $metric)
                    <article class="bk-card p-5">
                        <div class="flex items-start justify-between gap-4">
                            <span class="grid h-11 w-11 place-items-center rounded-2xl bg-slate-100 text-teal-700">
                                <i class="fas {{ $metric['icon'] }}"></i>
                            </span>
                            <span class="text-2xl font-black text-slate-950">{{ number_format($metric['value']) }}{{ $metric['suffix'] ?? '' }}</span>
                        </div>
                        <h3 class="mt-4 font-black text-slate-950">{{ $metric['label'] }}</h3>
                        <p class="mt-1 text-sm text-slate-600">{{ $metric['copy'] }}</p>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="bg-white py-12 sm:py-16 lg:py-20">
        <div class="bk-shell grid gap-8 lg:grid-cols-[0.8fr_1.2fr] lg:items-center">
            <div class="bk-card overflow-hidden">
                <img src="{{ asset('img/dayo.png') }}" alt="Oyetoke Adedayo Ebenezer" class="h-[420px] w-full object-cover object-top">
            </div>
            <div>
                <span class="bk-eyebrow">Founder</span>
                <h2 class="bk-display mt-3 text-3xl font-black text-slate-950 sm:text-4xl">Oyetoke Adedayo Ebenezer</h2>
                <p class="mt-2 text-sm font-black text-teal-700">Full Stack Developer | Educator | Entrepreneur</p>
                <p class="mt-4 text-base leading-7 text-slate-600">
                    Adedayo is a self-taught full-stack developer and educator with a background in teaching Mathematics and Physics. His practical teaching experience shaped BootKode's focus on clear structure, real practice, and career-ready digital skill development.
                </p>

                @if (!empty($teamStats))
                    <div class="mt-6 grid gap-3 sm:grid-cols-2">
                        @foreach (array_slice($teamStats, 0, 4) as $stat)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-xl font-black text-slate-950">{{ number_format($stat['count']) }}</p>
                                <p class="mt-1 text-sm font-bold text-slate-600">{{ $stat['label'] }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>

    @if ($recentAnnouncements->count() > 0)
        <section class="bg-slate-50 py-12 sm:py-16">
            <div class="bk-shell">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <span class="bk-eyebrow">Updates</span>
                        <h2 class="bk-display mt-3 text-3xl font-black text-slate-950">Recent announcements</h2>
                    </div>
                </div>
                <div class="mt-8 grid gap-4 md:grid-cols-3">
                    @foreach ($recentAnnouncements as $announcement)
                        <article class="bk-card p-5">
                            <p class="text-xs font-black text-teal-700">{{ $announcement->published_at->format('M j, Y') }}</p>
                            <h3 class="mt-2 line-clamp-2 font-black text-slate-950">{{ $announcement->title }}</h3>
                            <p class="mt-2 line-clamp-3 text-sm leading-6 text-slate-600">
                                {{ \Illuminate\Support\Str::limit(strip_tags($announcement->content), 120) }}
                            </p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</div>
