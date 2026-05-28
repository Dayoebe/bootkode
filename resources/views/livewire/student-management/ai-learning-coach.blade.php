<div class="space-y-6">
    @if (session()->has('message'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-200">
            {{ session('message') }}
        </div>
    @endif

    <div class="rounded-xl border border-themed-primary bg-themed-secondary p-6 shadow-sm transition-colors duration-300">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-accent-primary">Real AI layer</p>
                <h2 class="text-2xl font-bold text-themed-primary">AI Learning Coach</h2>
                <p class="mt-1 max-w-3xl text-sm text-themed-secondary">
                    Skill diagnosis, adaptive learning path, tutor answers, assessment feedback, and course recommendations from your BootKode activity.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <span class="rounded-full border border-themed-primary px-3 py-2 text-xs font-semibold text-themed-secondary">
                    {{ $aiProviderReady ? 'OpenAI enabled' : 'Local course-context AI' }}
                </span>
                <button type="button" wire:click="refreshProfile"
                    class="rounded-lg border border-themed-primary px-4 py-2 text-sm font-semibold text-themed-primary transition hover:border-accent-primary hover:text-accent-primary">
                    <i class="fas fa-rotate mr-2"></i>Refresh Diagnosis
                </button>
            </div>
        </div>

        <form wire:submit.prevent="saveGoal" class="mt-5 grid gap-3 lg:grid-cols-[1fr_auto]">
            <input type="text" wire:model="goal"
                class="rounded-lg border border-themed-primary bg-themed-secondary px-4 py-3 text-sm text-themed-primary placeholder-themed-tertiary focus:border-accent-primary focus:ring-accent-primary"
                placeholder="Set a learner goal, e.g. Become job-ready for Laravel backend roles">
            <button type="submit" class="rounded-lg bg-accent-primary px-4 py-3 text-sm font-semibold text-white transition hover:bg-accent-secondary">
                Save Goal
            </button>
        </form>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-lg border border-themed-primary bg-themed-secondary p-4">
            <p class="text-xs font-semibold uppercase text-themed-secondary">Enrolled</p>
            <p class="mt-1 text-2xl font-bold text-themed-primary">{{ count($signals['enrollments'] ?? []) }}</p>
        </div>
        <div class="rounded-lg border border-themed-primary bg-themed-secondary p-4">
            <p class="text-xs font-semibold uppercase text-themed-secondary">Assessment Signals</p>
            <p class="mt-1 text-2xl font-bold text-themed-primary">{{ count($signals['assessment_attempts'] ?? []) }}</p>
        </div>
        <div class="rounded-lg border border-themed-primary bg-themed-secondary p-4">
            <p class="text-xs font-semibold uppercase text-themed-secondary">Certificates</p>
            <p class="mt-1 text-2xl font-bold text-themed-primary">{{ count($signals['certificates'] ?? []) }}</p>
        </div>
        <div class="rounded-lg border border-themed-primary bg-themed-secondary p-4">
            <p class="text-xs font-semibold uppercase text-themed-secondary">Projects</p>
            <p class="mt-1 text-2xl font-bold text-themed-primary">{{ $signals['portfolio_count'] ?? 0 }}</p>
        </div>
    </div>

    <div class="rounded-xl border border-themed-primary bg-themed-secondary p-2 shadow-sm">
        <div class="grid gap-2 md:grid-cols-5">
            @foreach([
                'diagnosis' => ['Skill Diagnosis', 'fa-stethoscope'],
                'path' => ['Adaptive Path', 'fa-route'],
                'tutor' => ['AI Tutor', 'fa-robot'],
                'feedback' => ['Assessment Feedback', 'fa-clipboard-check'],
                'recommendations' => ['Recommendations', 'fa-lightbulb'],
            ] as $tab => [$label, $icon])
                <button type="button" wire:click="setTab('{{ $tab }}')"
                    class="rounded-lg px-3 py-3 text-sm font-semibold transition {{ $activeTab === $tab ? 'bg-accent-primary text-white' : 'text-themed-secondary hover:bg-themed-tertiary hover:text-themed-primary' }}">
                    <i class="fas {{ $icon }} mr-2"></i>{{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    @if($activeTab === 'diagnosis')
        <div class="grid gap-4 lg:grid-cols-2">
            @forelse($skillDiagnosis as $skill)
                <div class="rounded-xl border border-themed-primary bg-themed-secondary p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="font-semibold text-themed-primary">{{ $skill['label'] }}</h3>
                            <p class="mt-1 text-sm text-themed-secondary">{{ $skill['level'] }}</p>
                        </div>
                        <span class="rounded-full bg-accent-primary/10 px-3 py-1 text-sm font-bold text-accent-primary">{{ $skill['score'] }}%</span>
                    </div>
                    <div class="mt-4 h-2 rounded-full bg-themed-tertiary">
                        <div class="h-2 rounded-full bg-accent-primary" style="width: {{ $skill['score'] }}%"></div>
                    </div>
                    @if(!empty($skill['evidence']))
                        <div class="mt-4">
                            <p class="text-xs font-semibold uppercase text-themed-secondary">Evidence</p>
                            <ul class="mt-2 space-y-1 text-sm text-themed-secondary">
                                @foreach($skill['evidence'] as $item)
                                    <li><i class="fas fa-check text-emerald-500 mr-2"></i>{{ $item }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if(!empty($skill['gaps']))
                        <div class="mt-4">
                            <p class="text-xs font-semibold uppercase text-themed-secondary">Gaps</p>
                            <ul class="mt-2 space-y-1 text-sm text-themed-secondary">
                                @foreach($skill['gaps'] as $item)
                                    <li><i class="fas fa-triangle-exclamation text-orange-500 mr-2"></i>{{ $item }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <p class="mt-4 rounded-lg bg-themed-tertiary p-3 text-sm text-themed-primary">{{ $skill['next_action'] }}</p>
                </div>
            @empty
                <div class="rounded-xl border border-dashed border-themed-primary bg-themed-secondary p-10 text-center lg:col-span-2">
                    <i class="fas fa-stethoscope text-4xl text-themed-tertiary"></i>
                    <h3 class="mt-3 text-lg font-semibold text-themed-primary">No diagnosis yet</h3>
                    <p class="mt-1 text-sm text-themed-secondary">Enroll in a course or complete an assessment to generate stronger signals.</p>
                </div>
            @endforelse
        </div>
    @endif

    @if($activeTab === 'path')
        <div class="rounded-xl border border-themed-primary bg-themed-secondary p-5 shadow-sm">
            <h3 class="text-lg font-semibold text-themed-primary">Adaptive Learning Path</h3>
            <div class="mt-5 space-y-4">
                @forelse($adaptivePath as $index => $step)
                    <div class="grid gap-3 rounded-lg border border-themed-primary p-4 md:grid-cols-[48px_1fr_auto] md:items-center">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full bg-accent-primary text-sm font-bold text-white">{{ $index + 1 }}</div>
                        <div>
                            <p class="text-xs font-semibold uppercase text-themed-secondary">{{ $step['label'] }} · {{ ucfirst($step['priority']) }}</p>
                            <h4 class="mt-1 font-semibold text-themed-primary">{{ $step['title'] }}</h4>
                            <p class="mt-1 text-sm text-themed-secondary">{{ $step['detail'] }}</p>
                        </div>
                        <a href="{{ $step['url'] }}" class="rounded-lg border border-themed-primary px-3 py-2 text-sm font-semibold text-themed-primary hover:border-accent-primary hover:text-accent-primary">
                            Open
                        </a>
                    </div>
                @empty
                    <p class="rounded-lg border border-dashed border-themed-primary p-8 text-center text-sm text-themed-secondary">No path yet. Set a goal or enroll in a course.</p>
                @endforelse
            </div>
        </div>
    @endif

    @if($activeTab === 'tutor')
        <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_420px]">
            <div class="rounded-xl border border-themed-primary bg-themed-secondary p-5 shadow-sm">
                <h3 class="text-lg font-semibold text-themed-primary">Ask The AI Tutor</h3>
                <form wire:submit.prevent="askTutor" class="mt-4 space-y-4">
                    <select wire:model="selectedCourseId"
                        class="w-full rounded-lg border border-themed-primary bg-themed-secondary px-4 py-3 text-sm text-themed-primary">
                        <option value="">Use all available course context</option>
                        @foreach($enrolledCourses as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>
                    <textarea wire:model="question" rows="6"
                        class="w-full rounded-lg border border-themed-primary bg-themed-secondary px-4 py-3 text-sm text-themed-primary placeholder-themed-tertiary focus:border-accent-primary focus:ring-accent-primary"
                        placeholder="Ask about a lesson, error, concept, project, assessment, or what to learn next."></textarea>
                    @error('question') <p class="text-sm text-red-500">{{ $message }}</p> @enderror
                    <button type="submit" class="rounded-lg bg-accent-primary px-4 py-3 text-sm font-semibold text-white transition hover:bg-accent-secondary">
                        <i class="fas fa-paper-plane mr-2"></i>Ask Tutor
                    </button>
                </form>
            </div>

            <div class="rounded-xl border border-themed-primary bg-themed-secondary p-5 shadow-sm">
                <h3 class="text-lg font-semibold text-themed-primary">Recent Answers</h3>
                <div class="mt-4 space-y-4">
                    @forelse($tutorMessages as $message)
                        <div class="rounded-lg border border-themed-primary p-4">
                            <p class="text-sm font-semibold text-themed-primary">{{ $message->question }}</p>
                            <p class="mt-2 whitespace-pre-line text-sm text-themed-secondary">{{ $message->answer }}</p>
                            <p class="mt-3 text-xs text-themed-tertiary">{{ strtoupper($message->source) }} · {{ $message->created_at->diffForHumans() }}</p>
                        </div>
                    @empty
                        <p class="rounded-lg border border-dashed border-themed-primary p-6 text-center text-sm text-themed-secondary">No tutor history yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    @if($activeTab === 'feedback')
        <div class="grid gap-4 lg:grid-cols-2">
            @forelse($assessmentFeedback as $feedback)
                <div class="rounded-xl border border-themed-primary bg-themed-secondary p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase text-themed-secondary">{{ $feedback['course_title'] }}</p>
                            <h3 class="mt-1 font-semibold text-themed-primary">{{ $feedback['assessment_title'] }}</h3>
                        </div>
                        <span class="rounded-full px-3 py-1 text-sm font-bold {{ $feedback['passed'] ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-200' : 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-200' }}">
                            {{ $feedback['percentage'] }}%
                        </span>
                    </div>
                    <p class="mt-4 text-sm text-themed-secondary">{{ $feedback['summary'] }}</p>
                    @if(!empty($feedback['focus_points']))
                        <div class="mt-4 space-y-3">
                            @foreach($feedback['focus_points'] as $point)
                                <div class="rounded-lg bg-themed-tertiary p-3">
                                    <p class="text-sm font-semibold text-themed-primary">{{ $point['topic'] }}</p>
                                    <p class="mt-1 text-sm text-themed-secondary">{{ $point['hint'] }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <div class="rounded-xl border border-dashed border-themed-primary bg-themed-secondary p-10 text-center lg:col-span-2">
                    <i class="fas fa-clipboard-check text-4xl text-themed-tertiary"></i>
                    <h3 class="mt-3 text-lg font-semibold text-themed-primary">No assessment attempts yet</h3>
                    <p class="mt-1 text-sm text-themed-secondary">Complete quizzes or projects to get targeted feedback.</p>
                </div>
            @endforelse
        </div>
    @endif

    @if($activeTab === 'recommendations')
        <div class="grid gap-4 lg:grid-cols-2 xl:grid-cols-3">
            @forelse($recommendations as $course)
                <div class="rounded-xl border border-themed-primary bg-themed-secondary p-5 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase text-themed-secondary">{{ $course['category'] ?: 'Course' }}</p>
                            <h3 class="mt-1 font-semibold text-themed-primary">{{ $course['title'] }}</h3>
                            <p class="mt-1 text-sm text-themed-secondary">{{ ucfirst($course['difficulty']) }} · {{ $course['instructor'] ?: 'BootKode' }}</p>
                        </div>
                        <span class="rounded-full bg-accent-primary/10 px-3 py-1 text-sm font-bold text-accent-primary">{{ $course['score'] }}%</span>
                    </div>
                    <p class="mt-4 text-sm text-themed-secondary">{{ $course['reason'] }}</p>
                    <a href="{{ $course['url'] }}" class="mt-4 inline-flex rounded-lg border border-themed-primary px-3 py-2 text-sm font-semibold text-themed-primary hover:border-accent-primary hover:text-accent-primary">
                        View Course
                    </a>
                </div>
            @empty
                <div class="rounded-xl border border-dashed border-themed-primary bg-themed-secondary p-10 text-center lg:col-span-2 xl:col-span-3">
                    <i class="fas fa-lightbulb text-4xl text-themed-tertiary"></i>
                    <h3 class="mt-3 text-lg font-semibold text-themed-primary">No recommendations yet</h3>
                    <p class="mt-1 text-sm text-themed-secondary">Publish and approve courses, or complete more activity, to improve recommendation matching.</p>
                </div>
            @endforelse
        </div>
    @endif
</div>
