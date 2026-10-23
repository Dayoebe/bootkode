<div class="w-full min-w-0 space-y-6">
    <section class="relative overflow-hidden rounded-lg bg-slate-950 p-5 text-white shadow-sm shadow-slate-900/10 md:p-7">
        <div class="pointer-events-none absolute -right-16 -top-24 h-64 w-64 rounded-full bg-teal-400/10 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-28 left-1/3 h-56 w-56 rounded-full bg-sky-400/10 blur-3xl"></div>
        <div class="relative flex flex-col gap-6 xl:flex-row xl:items-center xl:justify-between">
            <div class="max-w-3xl">
                <div class="mb-4 inline-flex items-center gap-2 rounded-full border border-brand bg-teal-300/10 px-3 py-1 text-xs font-bold uppercase tracking-[0.14em] text-brand">
                    <i class="fas fa-route"></i>
                    Guided learner journey
                </div>
                <h1 class="bk-display  font-bold leading-tight  bk-page-title">From learning goal to career-ready evidence.</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-300 md:text-base">
                    Follow one clear path: choose a goal, enroll, learn, submit work, get reviewed, earn a certificate, and move into career tools.
                </p>
            </div>

            <div class="w-full rounded-lg border border-white/10 bg-white/[0.07] p-4  xl:w-96">
                <div class="flex items-center justify-between text-sm mb-2">
                    <span class="font-semibold text-slate-200">Journey progress</span>
                    <span class="font-bold text-brand">{{ $journeyProgress }}%</span>
                </div>
                <div class="h-2.5 w-full overflow-hidden rounded-full bg-white/10">
                    <div class="h-2.5 rounded-full bg-teal-300 transition-all duration-500" style="width: {{ $journeyProgress }}%"></div>
                </div>
                <div class="mt-3 text-xs font-medium text-content-muted">{{ $completedSteps }} of {{ count($steps) }} steps completed</div>
                @if ($nextStep)
                    <div class="mt-4 flex items-start gap-3 border-t border-white/10 pt-4">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-teal-300 text-content">
                            <i class="{{ $nextStep['icon'] }}"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-bold uppercase tracking-wide text-content-muted">Next step</p>
                            <p class="font-semibold text-white">{{ $nextStep['label'] }}</p>
                            @if ($nextStep['route'])
                                <a href="{{ $nextStep['route'] }}" wire:navigate class="mt-2 inline-flex items-center gap-2 text-sm font-bold text-brand hover:text-white">
                                    {{ $nextStep['action'] }}
                                    <i class="fas fa-arrow-right text-xs"></i>
                                </a>
                            @else
                                <a href="#goal-options" class="mt-2 inline-flex items-center gap-2 text-sm font-bold text-brand hover:text-white">
                                    {{ $nextStep['action'] }}
                                    <i class="fas fa-arrow-right text-xs"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section id="goal-options" class="space-y-4 rounded-lg border border-themed-primary bg-themed-secondary p-5 shadow-sm md:p-6">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold text-themed-primary">Choose Your Goal</h2>
                <p class="text-themed-secondary text-sm">This controls the recommended courses and keeps the path focused.</p>
            </div>
            @if ($selectedGoalData)
                <div class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-themed-secondary border border-themed-primary text-sm text-themed-primary">
                    <i class="{{ $selectedGoalData['icon'] }} text-brand"></i>
                    {{ $selectedGoalData['label'] }}
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach ($goals as $key => $goal)
                <button
                    type="button"
                    wire:click="chooseGoal('{{ $key }}')"
                    class="@class([
                        'text-left bg-themed-secondary border rounded-lg p-4 transition-all duration-200 hover:border-brand hover:-translate-y-0.5',
                        'border-brand ring-2 ring-brand/20' => $selectedGoal === $key,
                        'border-themed-primary' => $selectedGoal !== $key,
                    ])"
                >
                    <div class="flex items-start gap-3">
                        <div class="@class([
                            'w-10 h-10 rounded-lg flex items-center justify-center shrink-0',
                            'bg-action text-action-foreground' => $selectedGoal === $key,
                            'bg-themed-tertiary text-brand' => $selectedGoal !== $key,
                        ])">
                            <i class="{{ $goal['icon'] }}"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-themed-primary">{{ $goal['label'] }}</div>
                            <div class="text-sm text-themed-secondary mt-1">{{ $goal['short'] }}</div>
                        </div>
                    </div>
                </button>
            @endforeach
        </div>
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="space-y-4 rounded-lg border border-themed-primary bg-themed-secondary p-5 shadow-sm md:p-6 xl:col-span-2">
            <div>
                <h2 class="text-xl font-bold text-themed-primary">Your Path</h2>
                <p class="text-themed-secondary text-sm">Each step opens the exact place where the learner should act next.</p>
            </div>

            <div class="relative mt-5 space-y-3 before:absolute before:bottom-8 before:left-5 before:top-8 before:w-px before:bg-themed-secondary">
                @foreach ($steps as $index => $step)
                    <article class="@class([
                        'relative grid min-w-0 gap-4 rounded-lg border p-4 pl-16 transition md:grid-cols-[minmax(0,1fr)_11rem] md:items-center md:pl-16',
                        'border-green-500/40 bg-green-500/[0.04]' => $step['status'] === 'complete',
                        'border-brand bg-brand/10 shadow-sm ring-1 ring-brand/20' => $step['status'] === 'next',
                        'border-themed-primary bg-themed-primary' => $step['status'] === 'open',
                    ])">
                        <div class="@class([
                            'absolute left-0 top-4 z-10 flex h-10 w-10 items-center justify-center rounded-lg text-sm shadow-sm',
                            'bg-green-500 text-white' => $step['status'] === 'complete',
                            'bg-action text-action-foreground' => $step['status'] === 'next',
                            'border border-themed-secondary bg-themed-secondary text-themed-secondary' => $step['status'] === 'open',
                        ])">
                            @if ($step['status'] === 'complete')
                                <i class="fas fa-check"></i>
                            @else
                                <i class="{{ $step['icon'] }}"></i>
                            @endif
                        </div>

                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-[10px] font-bold uppercase tracking-[0.14em] text-themed-tertiary">Step {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                <span class="@class([
                                    'rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide',
                                    'bg-green-500/10 text-green-600' => $step['status'] === 'complete',
                                    'bg-brand/20 text-brand' => $step['status'] === 'next',
                                    'bg-themed-tertiary text-themed-tertiary' => $step['status'] === 'open',
                                ])">
                                    {{ $step['status'] === 'complete' ? 'Completed' : ($step['status'] === 'next' ? 'Do this next' : 'Upcoming') }}
                                </span>
                            </div>
                            <h3 class="mt-1 font-bold text-themed-primary">{{ $step['label'] }}</h3>
                            <p class="mt-1 text-sm leading-6 text-themed-secondary">{{ $step['copy'] }}</p>
                            <p class="mt-2 text-xs font-semibold text-themed-tertiary"><i class="fas fa-chart-simple mr-1.5"></i>{{ $step['metric'] }}</p>
                        </div>

                        <div class="md:text-right">
                            @if ($step['route'])
                                <a href="{{ $step['route'] }}" wire:navigate class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-themed-secondary bg-themed-secondary px-3 py-2.5 text-sm font-bold text-themed-primary transition hover:border-brand md:w-auto">
                                    {{ $step['action'] }}
                                    <i class="fas fa-arrow-right text-xs"></i>
                                </a>
                            @else
                                <a href="#goal-options" class="inline-flex w-full items-center justify-center gap-2 rounded-lg border border-themed-secondary bg-themed-secondary px-3 py-2.5 text-sm font-bold text-themed-primary transition hover:border-brand md:w-auto">
                                    {{ $step['action'] }}
                                    <i class="fas fa-arrow-right text-xs"></i>
                                </a>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </div>

        <aside class="space-y-4">
            <div class="bg-themed-secondary border border-themed-primary rounded-lg p-4">
                <h2 class="text-lg font-bold text-themed-primary">Current Focus</h2>
                @if ($currentCourse)
                    <div class="mt-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="font-semibold text-themed-primary">{{ $currentCourse->title }}</h3>
                                <p class="text-sm text-themed-secondary mt-1">{{ $currentCourse->category ?? 'Course' }}</p>
                            </div>
                            <span class="text-sm font-bold text-brand">{{ $currentCourse->progress }}%</span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-themed-tertiary overflow-hidden mt-3">
                            <div class="h-2 rounded-full bg-action" style="width: {{ $currentCourse->progress }}%"></div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 mt-4 text-sm">
                            <div class="bg-themed-primary border border-themed-secondary rounded-lg p-3">
                                <div class="text-themed-tertiary text-xs">Instructor</div>
                                <div class="font-medium text-themed-primary truncate">{{ $currentCourse->instructor ?? 'Not assigned' }}</div>
                            </div>
                            <div class="bg-themed-primary border border-themed-secondary rounded-lg p-3">
                                <div class="text-themed-tertiary text-xs">Lessons done</div>
                                <div class="font-medium text-themed-primary">{{ $stats['lessonCompletionCount'] }}</div>
                            </div>
                        </div>
                        <a href="{{ route('course.view', ['course' => $currentCourse->slug, 'continue' => 1]) }}" wire:navigate class="mt-4 inline-flex w-full items-center justify-center gap-2 px-4 py-2 rounded-lg bg-action text-action-foreground hover:bg-action/90 font-medium transition-colors">
                            Continue learning
                            <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                @else
                    <div class="mt-4 rounded-lg border-2 border-dashed border-themed-secondary bg-themed-primary p-5 text-center">
                        <i class="fas fa-book-open text-3xl text-themed-tertiary mb-3"></i>
                        <h3 class="font-semibold text-themed-primary">No active course yet</h3>
                        <p class="text-sm text-themed-secondary mt-1">Choose a goal, then enroll in a course to start the path.</p>
                        <a href="{{ route('student.course-catalog') }}" wire:navigate class="mt-4 inline-flex items-center justify-center gap-2 px-4 py-2 rounded-lg bg-action text-action-foreground hover:bg-action/90 text-sm font-medium">
                            Browse catalog
                            <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                @endif
            </div>

            <div class="bg-themed-secondary border border-themed-primary rounded-lg p-4">
                <h2 class="text-lg font-bold text-themed-primary">Career Launchpad</h2>
                <div class="mt-4 grid grid-cols-1 gap-3">
                    <a href="{{ route('portfolio.show') }}" wire:navigate class="flex items-center justify-between gap-3 bg-themed-primary border border-themed-secondary hover:border-brand rounded-lg p-3 transition-colors">
                        <span class="flex items-center gap-3 text-themed-primary font-medium"><i class="fas fa-id-card text-brand"></i> Portfolio</span>
                        <span class="text-xs text-themed-tertiary">{{ $stats['portfolioCount'] }}</span>
                    </a>
                    <a href="{{ route('resume.builder') }}" wire:navigate class="flex items-center justify-between gap-3 bg-themed-primary border border-themed-secondary hover:border-brand rounded-lg p-3 transition-colors">
                        <span class="flex items-center gap-3 text-themed-primary font-medium"><i class="fas fa-file-alt text-brand"></i> Resume</span>
                        <span class="text-xs text-themed-tertiary">{{ $stats['resumeProfileCount'] }}</span>
                    </a>
                    <a href="{{ route('user.interview') }}" wire:navigate class="flex items-center justify-between gap-3 bg-themed-primary border border-themed-secondary hover:border-brand rounded-lg p-3 transition-colors">
                        <span class="flex items-center gap-3 text-themed-primary font-medium"><i class="fas fa-comments text-brand"></i> Mock interviews</span>
                        <span class="text-xs text-themed-tertiary">{{ $stats['mockInterviewCount'] }}</span>
                    </a>
                    <a href="{{ route('search.job') }}" wire:navigate class="flex items-center justify-between gap-3 bg-themed-primary border border-themed-secondary hover:border-brand rounded-lg p-3 transition-colors">
                        <span class="flex items-center gap-3 text-themed-primary font-medium"><i class="fas fa-search-dollar text-brand"></i> Job search</span>
                        <span class="text-xs text-themed-tertiary">{{ $stats['jobApplicationCount'] }}</span>
                    </a>
                </div>
            </div>
        </aside>
    </section>

    <section class="space-y-4">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-3">
            <div>
                <h2 class="text-xl font-bold text-themed-primary">Recommended Courses</h2>
                <p class="text-themed-secondary text-sm">Recommendations use your selected goal and exclude courses you already joined.</p>
            </div>
            <a href="{{ route('student.course-catalog') }}" wire:navigate class="inline-flex items-center gap-2 text-sm text-brand hover:text-accent-themed-secondary font-medium">
                View full catalog
                <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>

        @if ($recommendedCourses->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                @foreach ($recommendedCourses as $course)
                    <article class="bg-themed-secondary border border-themed-primary rounded-lg overflow-hidden hover:border-brand transition-colors">
                        <div class="h-32 bg-themed-tertiary overflow-hidden">
                            <img src="{{ asset('storage/' . ($course->thumbnail ?? 'images/default-course.png')) }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                        </div>
                        <div class="p-4">
                            <div class="text-xs text-themed-tertiary mb-2">{{ $course->category->name ?? 'Course' }}</div>
                            <h3 class="font-semibold text-themed-primary line-clamp-2">{{ $course->title }}</h3>
                            <p class="text-sm text-themed-secondary mt-2 line-clamp-2">{{ $course->subtitle ?: $course->description }}</p>
                            <div class="flex items-center justify-between mt-4 text-xs text-themed-tertiary">
                                <span><i class="fas fa-users mr-1"></i>{{ $course->enrollments_count ?? 0 }}</span>
                                <span>{{ $course->formatted_price }}</span>
                            </div>
                            <a href="{{ route('student.course-catalog', ['search' => $course->title]) }}" wire:navigate class="mt-4 inline-flex w-full items-center justify-center gap-2 px-3 py-2 rounded-lg bg-themed-primary border border-themed-secondary text-themed-primary hover:border-brand text-sm font-medium transition-colors">
                                View in catalog
                                <i class="fas fa-arrow-right text-xs"></i>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="bg-themed-secondary border-2 border-dashed border-themed-secondary rounded-lg p-8 text-center">
                <i class="fas fa-compass text-4xl text-themed-tertiary mb-3"></i>
                <h3 class="text-lg font-semibold text-themed-primary">No recommendations yet</h3>
                <p class="text-themed-secondary mt-1">Select a goal or add published courses to the catalog to populate this section.</p>
                <a href="{{ route('student.course-catalog') }}" wire:navigate class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-action text-action-foreground hover:bg-action/90 text-sm font-medium">
                    Browse catalog
                    <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>
        @endif
    </section>
</div>
