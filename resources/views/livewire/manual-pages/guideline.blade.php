@php
    $tabs = [
        'student' => ['label' => 'Students', 'icon' => 'fa-user-graduate'],
        'instructor' => ['label' => 'Instructors', 'icon' => 'fa-chalkboard-user'],
        'mentor' => ['label' => 'Mentors', 'icon' => 'fa-people-arrows'],
        'certificate' => ['label' => 'Certificates', 'icon' => 'fa-certificate'],
    ];

    $guides = [
        'student' => [
            ['title' => 'Choose one complete path', 'copy' => 'Start with a course that matches your current goal. Finish its modules before jumping across unrelated topics.'],
            ['title' => 'Practice after every lesson', 'copy' => 'Turn each lesson into a small task, note, code sample, dashboard, or project commit.'],
            ['title' => 'Keep proof of progress', 'copy' => 'Save screenshots, repos, notes, certificates, and capstone outcomes as portfolio evidence.'],
        ],
        'instructor' => [
            ['title' => 'Teach with outcomes', 'copy' => 'Every module should have a clear learner result, practice task, and review checkpoint.'],
            ['title' => 'Keep lessons practical', 'copy' => 'Use real examples, common mistakes, and professional criteria instead of vague theory.'],
            ['title' => 'Review before publishing', 'copy' => 'Check structure, clarity, mobile readability, quizzes, and completion requirements.'],
        ],
        'mentor' => [
            ['title' => 'Review the work, not only the answer', 'copy' => 'Look at reasoning, structure, tradeoffs, naming, and failure handling.'],
            ['title' => 'Give specific next actions', 'copy' => 'Feedback should leave the learner with a clear improvement step.'],
            ['title' => 'Support consistency', 'copy' => 'Help learners keep a realistic weekly rhythm and unblock them quickly.'],
        ],
        'certificate' => [
            ['title' => 'Complete the course requirements', 'copy' => 'Meet the completion threshold, required assessments, and project expectations.'],
            ['title' => 'Request review when ready', 'copy' => 'Submit only after your course progress and evidence are clean enough to verify.'],
            ['title' => 'Share verified credentials', 'copy' => 'Use the certificate verification link when presenting your achievement.'],
        ],
    ];
@endphp

<div class="bk-edge-to-edge bg-slate-50">
    <section class="bg-slate-950 text-white">
        <div class="bk-shell py-12 sm:py-16 lg:py-20">
            <span class="bk-eyebrow border-white/15 bg-white/10 text-white">Guidelines</span>
            <h1 class="bk-display mt-4 max-w-4xl text-3xl font-black leading-tight text-white sm:text-5xl">
                A clear operating standard for learners, instructors, mentors, and certificates
            </h1>
            <div class="mt-8 grid grid-cols-2 gap-3 lg:grid-cols-4">
                @foreach ([
                    ['label' => 'Learners', 'value' => $stats['totalStudents'] ?? 0],
                    ['label' => 'Courses', 'value' => $stats['totalCourses'] ?? 0],
                    ['label' => 'Lessons', 'value' => $stats['totalLessons'] ?? 0],
                    ['label' => 'Assessments', 'value' => $stats['totalAssessments'] ?? 0],
                ] as $stat)
                    <div class="rounded-2xl border border-white/10 bg-white/10 p-4">
                        <p class="text-2xl font-black text-white">{{ number_format($stat['value']) }}</p>
                        <p class="mt-1 text-sm font-bold text-slate-300">{{ $stat['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-10 sm:py-14 lg:py-16">
        <div class="bk-shell grid gap-6 lg:grid-cols-[0.32fr_0.68fr]">
            <aside class="lg:sticky lg:top-24 lg:self-start">
                <div class="bk-card p-3">
                    @foreach ($tabs as $key => $tab)
                        <button
                            type="button"
                            wire:click="selectTab('{{ $key }}')"
                            class="flex w-full items-center gap-3 rounded-2xl px-4 py-3 text-left text-sm font-black transition {{ $activeTab === $key ? 'bg-slate-950 text-white' : 'text-slate-700 hover:bg-slate-100' }}"
                        >
                            <i class="fas {{ $tab['icon'] }} w-5 {{ $activeTab === $key ? 'text-white' : 'text-teal-700' }}"></i>
                            {{ $tab['label'] }}
                        </button>
                    @endforeach
                </div>
            </aside>

            <div class="space-y-5">
                <div class="bk-card p-5 sm:p-6">
                    <div class="flex items-start gap-4">
                        <span class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-teal-50 text-teal-700">
                            <i class="fas {{ $tabs[$activeTab]['icon'] ?? 'fa-compass' }}"></i>
                        </span>
                        <div>
                            <p class="text-sm font-black text-teal-700">{{ $tabs[$activeTab]['label'] ?? 'Guideline' }}</p>
                            <h2 class="bk-display mt-1 text-3xl font-black text-slate-950">What good progress looks like</h2>
                        </div>
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-3">
                    @foreach ($guides[$activeTab] ?? $guides['student'] as $index => $guide)
                        <article class="bk-card p-5">
                            <span class="grid h-10 w-10 place-items-center rounded-2xl bg-slate-100 text-sm font-black text-slate-950">{{ $index + 1 }}</span>
                            <h3 class="mt-4 text-lg font-black text-slate-950">{{ $guide['title'] }}</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600">{{ $guide['copy'] }}</p>
                        </article>
                    @endforeach
                </div>

                <div class="bk-soft-card p-5 sm:p-6">
                    <div class="grid gap-4 sm:grid-cols-3">
                        <div>
                            <p class="text-2xl font-black text-slate-950">{{ number_format($stats['totalEnrollments'] ?? 0) }}</p>
                            <p class="text-sm font-bold text-slate-600">Enrollments</p>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-slate-950">{{ number_format($stats['completedCourses'] ?? 0) }}</p>
                            <p class="text-sm font-bold text-slate-600">Completed courses</p>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-slate-950">{{ $stats['successRate'] ?? 0 }}%</p>
                            <p class="text-sm font-bold text-slate-600">Completion rate</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
