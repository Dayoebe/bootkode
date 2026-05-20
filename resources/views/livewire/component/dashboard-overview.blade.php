<div class="space-y-6">
    <section class="bg-themed-secondary border border-themed-primary rounded-xl p-5 md:p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-accent-themed-primary/10 text-accent-themed-primary text-xs font-semibold mb-3">
                    <i class="fas fa-route"></i>
                    Learner journey
                </div>
                <h1 class="text-2xl md:text-3xl font-bold text-themed-primary">Know exactly what to do next</h1>
                <p class="text-themed-secondary mt-2">
                    Move through one clear path from goal selection to course work, reviews, certificates, and career help.
                </p>
            </div>

            <div class="w-full lg:w-80 bg-themed-primary border border-themed-secondary rounded-lg p-4">
                <div class="flex items-center justify-between text-sm mb-2">
                    <span class="font-semibold text-themed-primary">Path completion</span>
                    <span class="font-bold text-accent-themed-primary">{{ $journey['progress'] }}%</span>
                </div>
                <div class="h-2.5 bg-themed-tertiary rounded-full overflow-hidden">
                    <div class="h-2.5 bg-accent-themed-primary rounded-full" style="width: {{ $journey['progress'] }}%"></div>
                </div>
                <div class="mt-3 text-xs text-themed-tertiary">{{ $journey['completed'] }} of {{ $journey['total'] }} journey steps complete</div>
                <div class="mt-4 flex items-center justify-between gap-3">
                    <div>
                        <div class="text-xs text-themed-tertiary uppercase font-semibold">Next</div>
                        <div class="font-medium text-themed-primary">{{ $journey['next'] }}</div>
                    </div>
                    <a href="{{ route('learner.journey') }}" wire:navigate class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-accent-themed-primary text-white hover:bg-accent-themed-secondary text-sm font-medium">
                        Open
                        <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-themed-secondary border border-themed-primary rounded-lg p-5">
            <div class="w-10 h-10 rounded-lg bg-accent-themed-primary/10 text-accent-themed-primary flex items-center justify-center mb-4">
                <i class="fas fa-book"></i>
            </div>
            <h3 class="text-lg font-bold text-themed-primary">Enrolled Courses</h3>
            <p class="text-4xl font-bold text-themed-primary mt-2">{{ $enrolledCourses }}</p>
            <a href="{{ route('student.enrolled-courses') }}" wire:navigate class="inline-flex items-center gap-2 mt-4 text-sm text-accent-themed-primary hover:text-accent-themed-secondary font-medium">
                View courses
                <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>

        <div class="bg-themed-secondary border border-themed-primary rounded-lg p-5">
            <div class="w-10 h-10 rounded-lg bg-accent-themed-primary/10 text-accent-themed-primary flex items-center justify-center mb-4">
                <i class="fas fa-compass"></i>
            </div>
            <h3 class="text-lg font-bold text-themed-primary">Recommended Start</h3>
            <p class="text-themed-secondary mt-2">Use the guided journey to choose a goal and get the right course suggestions.</p>
            <a href="{{ route('learner.journey') }}#goal-options" wire:navigate class="inline-flex items-center gap-2 mt-4 text-sm text-accent-themed-primary hover:text-accent-themed-secondary font-medium">
                Choose goal
                <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>

        <div class="bg-themed-secondary border border-themed-primary rounded-lg p-5">
            <div class="w-10 h-10 rounded-lg bg-accent-themed-primary/10 text-accent-themed-primary flex items-center justify-center mb-4">
                <i class="fas fa-briefcase"></i>
            </div>
            <h3 class="text-lg font-bold text-themed-primary">Career Support</h3>
            <p class="text-themed-secondary mt-2">Portfolio, resume, interviews, and job search now sit at the end of the same path.</p>
            <a href="{{ route('portfolio.show') }}" wire:navigate class="inline-flex items-center gap-2 mt-4 text-sm text-accent-themed-primary hover:text-accent-themed-secondary font-medium">
                Open tools
                <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
    </section>
</div>
