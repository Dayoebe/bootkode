@php
    $overview = $overviewStats ?? [];
    $course = $courseStats ?? [];
    $engagement = $engagementMetrics ?? [];
    $categories = array_slice($categoryBreakdown ?? [], 0, 6);
    $trending = array_slice($trendingCourses ?? [], 0, 5);

    $metricCards = [
        ['label' => 'Total users', 'value' => $overview['total_users'] ?? 0, 'icon' => 'fa-users', 'tone' => 'text-teal-700 bg-teal-50'],
        ['label' => 'Published courses', 'value' => $overview['published_courses'] ?? 0, 'icon' => 'fa-book-open', 'tone' => 'text-sky-700 bg-sky-50'],
        ['label' => 'Total lessons', 'value' => $overview['total_lessons'] ?? 0, 'icon' => 'fa-list-check', 'tone' => 'text-rose-700 bg-rose-50'],
        ['label' => 'Certificates issued', 'value' => $overview['certificates_issued'] ?? 0, 'icon' => 'fa-certificate', 'tone' => 'text-amber-700 bg-amber-50'],
    ];
@endphp

<div class="bk-edge-to-edge bg-slate-50">
    <section class="bg-slate-950 text-white">
        <div class="bk-shell py-12 sm:py-16 lg:py-20">
            <span class="bk-eyebrow border-white/15 bg-white/10 text-white">Statistics</span>
            <h1 class="bk-display mt-4 max-w-4xl text-3xl font-black leading-tight text-white sm:text-5xl">
                BootKode platform numbers in one clean view
            </h1>
            <p class="mt-4 max-w-2xl text-base leading-7 text-slate-300">
                Course depth, learner activity, certificates, and category coverage from the live application database.
            </p>
        </div>
    </section>

    <section class="py-10 sm:py-14 lg:py-16">
        <div class="bk-shell">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($metricCards as $metric)
                    <article class="bk-card p-5">
                        <div class="flex items-start justify-between gap-4">
                            <span class="{{ $metric['tone'] }} grid h-12 w-12 place-items-center rounded-2xl">
                                <i class="fas {{ $metric['icon'] }}"></i>
                            </span>
                            <span class="text-3xl font-black text-slate-950">{{ number_format($metric['value']) }}</span>
                        </div>
                        <h2 class="mt-4 text-sm font-black text-slate-700">{{ $metric['label'] }}</h2>
                    </article>
                @endforeach
            </div>

            <div class="mt-6 grid gap-6 lg:grid-cols-[0.62fr_0.38fr]">
                <div class="bk-card p-5 sm:p-6">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <span class="bk-eyebrow">Course mix</span>
                            <h2 class="bk-display mt-3 text-3xl font-black text-slate-950">Published learning supply</h2>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-3 sm:grid-cols-3">
                        @foreach ([
                            ['label' => 'Published', 'value' => $course['total_published'] ?? 0],
                            ['label' => 'Free', 'value' => $course['free_courses'] ?? 0],
                            ['label' => 'Premium', 'value' => $course['premium_courses'] ?? 0],
                        ] as $item)
                            <div class="rounded-2xl bg-slate-50 p-4">
                                <p class="text-2xl font-black text-slate-950">{{ number_format($item['value']) }}</p>
                                <p class="mt-1 text-sm font-bold text-slate-600">{{ $item['label'] }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 space-y-3">
                        @forelse ($categories as $category)
                            @php
                                $maxCourses = max(1, collect($categories)->max('courses') ?? 1);
                                $percent = min(100, (($category['courses'] ?? 0) / $maxCourses) * 100);
                            @endphp
                            <div>
                                <div class="flex items-center justify-between gap-4 text-sm">
                                    <span class="font-black text-slate-800">{{ $category['name'] }}</span>
                                    <span class="font-bold text-slate-500">{{ $category['courses'] }} courses</span>
                                </div>
                                <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-100">
                                    <div class="h-full rounded-full bg-teal-700" style="width: {{ $percent }}%;"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-slate-600">Category data will appear when courses are categorized.</p>
                        @endforelse
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bk-card p-5 sm:p-6">
                        <span class="bk-eyebrow">Activity</span>
                        <div class="mt-5 grid gap-3">
                            @foreach ([
                                ['label' => 'Today', 'value' => $engagement['daily_active_users'] ?? 0],
                                ['label' => 'This week', 'value' => $engagement['weekly_active_users'] ?? 0],
                                ['label' => 'This month', 'value' => $engagement['monthly_active_users'] ?? 0],
                            ] as $item)
                                <div class="flex items-center justify-between rounded-2xl bg-slate-50 px-4 py-3">
                                    <span class="text-sm font-bold text-slate-600">{{ $item['label'] }}</span>
                                    <span class="text-xl font-black text-slate-950">{{ number_format($item['value']) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="bk-card p-5 sm:p-6">
                        <span class="bk-eyebrow">Quality</span>
                        <div class="mt-5 space-y-3">
                            <div class="rounded-2xl bg-slate-50 p-4">
                                <p class="text-2xl font-black text-slate-950">{{ $overview['course_approval_rate'] ?? 0 }}%</p>
                                <p class="text-sm font-bold text-slate-600">Course approval rate</p>
                            </div>
                            <div class="rounded-2xl bg-slate-50 p-4">
                                <p class="text-2xl font-black text-slate-950">{{ $overview['avg_completion_rate'] ?? 0 }}%</p>
                                <p class="text-sm font-bold text-slate-600">Certificate to enrollment ratio</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-6 grid gap-6 lg:grid-cols-2">
                <div class="bk-card p-5 sm:p-6">
                    <span class="bk-eyebrow">Trending courses</span>
                    <div class="mt-5 space-y-3">
                        @forelse ($trending as $item)
                            <div class="flex items-center justify-between gap-4 rounded-2xl border border-slate-200 p-4">
                                <div class="min-w-0">
                                    <h3 class="truncate font-black text-slate-950">{{ $item['title'] }}</h3>
                                    <p class="mt-1 text-sm text-slate-500">{{ $item['enrollments_count'] }} recent enrollments</p>
                                </div>
                                <span class="rounded-full bg-amber-50 px-3 py-1 text-sm font-black text-amber-700">
                                    {{ $item['average_rating'] }}/5
                                </span>
                            </div>
                        @empty
                            <p class="text-sm text-slate-600">Trending course data will appear after enrollments grow.</p>
                        @endforelse
                    </div>
                </div>

                <div class="bk-card p-5 sm:p-6">
                    <span class="bk-eyebrow">User growth</span>
                    <div class="mt-5 grid grid-cols-6 items-end gap-2">
                        @foreach ($userGrowthData ?? [] as $point)
                            @php
                                $maxUsers = max(1, collect($userGrowthData ?? [])->max('cumulative') ?? 1);
                                $height = max(12, (($point['cumulative'] ?? 0) / $maxUsers) * 160);
                            @endphp
                            <div class="flex min-h-[190px] flex-col items-center justify-end gap-2">
                                <div class="w-full rounded-t-2xl bg-teal-700" style="height: {{ $height }}px;"></div>
                                <span class="text-xs font-bold text-slate-500">{{ $point['month'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
