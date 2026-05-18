@props([
    'variant' => 'light',
    'label' => 'BootKode studio',
])

@php
    $isDark = $variant === 'dark';
    $surface = $isDark ? 'border-white/10 bg-slate-950 text-white' : 'border-slate-200 bg-white text-slate-950';
    $panel = $isDark ? 'border-white/10 bg-white/5' : 'border-slate-200 bg-slate-50';
    $muted = $isDark ? 'text-slate-400' : 'text-slate-500';
    $soft = $isDark ? 'bg-white/10' : 'bg-white';

    $path = [
        ['icon' => 'fa-book-open', 'label' => 'Learn', 'class' => 'bg-blue-500'],
        ['icon' => 'fa-code', 'label' => 'Build', 'class' => 'bg-orange-500'],
        ['icon' => 'fa-user-graduate', 'label' => 'Mentor', 'class' => 'bg-emerald-500'],
        ['icon' => 'fa-certificate', 'label' => 'Certify', 'class' => 'bg-rose-500'],
    ];

    $signals = [
        ['icon' => 'fa-message', 'label' => 'Mentor reply', 'meta' => '2 min ago', 'class' => 'bg-teal-500'],
        ['icon' => 'fa-vial-circle-check', 'label' => 'Lab reviewed', 'meta' => '92% score', 'class' => 'bg-lime-500'],
        ['icon' => 'fa-briefcase', 'label' => 'Career match', 'meta' => 'Frontend role', 'class' => 'bk-bg-olive'],
    ];
@endphp

<div {{ $attributes->merge(['class' => 'bk-learning-visual relative overflow-hidden rounded-[8px] border p-4 sm:p-5 ' . $surface]) }}>
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <span class="grid h-10 w-10 place-items-center rounded-[8px] bg-teal-600 text-white shadow-lg shadow-teal-950/20">
                <i class="fas fa-code text-sm"></i>
            </span>
            <div>
                <p class="text-sm font-black">{{ $label }}</p>
                <p class="text-xs font-bold {{ $muted }}">Learning, mentorship, proof, career</p>
            </div>
        </div>
        <div class="hidden gap-1 sm:flex">
            <span class="h-2.5 w-2.5 rounded-full bg-rose-500"></span>
            <span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span>
            <span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
        </div>
    </div>

    <div class="mt-4 grid gap-3 lg:grid-cols-[1.12fr_0.88fr]">
        <div class="bk-lab-screen rounded-[8px] border {{ $panel }} p-3">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-black uppercase {{ $muted }}">Active path</p>
                    <p class="mt-1 text-sm font-black">Full-stack launch track</p>
                </div>
                <span class="rounded-full bg-yellow-400 px-2 py-1 text-[10px] font-black uppercase text-slate-950">Live</span>
            </div>

            <div class="mt-4 grid grid-cols-4 gap-2">
                @foreach ($path as $item)
                    <div class="bk-pop-card rounded-[8px] {{ $soft }} p-3 shadow-sm" style="--i: {{ $loop->index }}">
                        <span class="mx-auto grid h-10 w-10 place-items-center rounded-[8px] {{ $item['class'] }} text-white">
                            <i class="fas {{ $item['icon'] }} text-sm"></i>
                        </span>
                        <p class="mt-2 truncate text-center text-[11px] font-black">{{ $item['label'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="mt-4 space-y-3">
                @foreach ([
                    ['label' => 'Course modules', 'width' => 'w-[86%]', 'class' => 'bg-sky-500'],
                    ['label' => 'Project tasks', 'width' => 'w-[72%]', 'class' => 'bg-fuchsia-500'],
                    ['label' => 'Mentor reviews', 'width' => 'w-[58%]', 'class' => 'bg-emerald-500'],
                ] as $bar)
                    <div>
                        <div class="mb-1 flex items-center justify-between gap-3 text-[11px] font-black">
                            <span class="{{ $muted }}">{{ $bar['label'] }}</span>
                        </div>
                        <div class="h-2.5 overflow-hidden rounded-full {{ $isDark ? 'bg-white/10' : 'bg-slate-200' }}">
                            <span class="{{ $bar['width'] }} block h-full rounded-full {{ $bar['class'] }}"></span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="grid gap-3">
            <div class="bk-floating-card rounded-[8px] border {{ $panel }} p-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-black uppercase {{ $muted }}">Completion</span>
                    <span class="text-sm font-black text-emerald-500">78%</span>
                </div>
                <div class="mt-3 flex h-3 overflow-hidden rounded-full bg-slate-200">
                    <span class="w-[16%] bg-red-500"></span>
                    <span class="w-[16%] bg-orange-500"></span>
                    <span class="w-[14%] bg-yellow-400"></span>
                    <span class="w-[12%] bg-lime-500"></span>
                    <span class="w-[20%] bg-emerald-500"></span>
                </div>
            </div>

            <div class="rounded-[8px] border {{ $panel }} p-3">
                <p class="text-xs font-black uppercase {{ $muted }}">Workspace signals</p>
                <div class="mt-3 grid gap-2">
                    @foreach ($signals as $signal)
                        <div class="bk-signal-line flex items-center gap-3 rounded-[8px] {{ $soft }} p-2 shadow-sm" style="--i: {{ $loop->index }}">
                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-[8px] {{ $signal['class'] }} text-white">
                                <i class="fas {{ $signal['icon'] }} text-sm"></i>
                            </span>
                            <span class="min-w-0">
                                <span class="block truncate text-xs font-black">{{ $signal['label'] }}</span>
                                <span class="block truncate text-[11px] font-semibold {{ $muted }}">{{ $signal['meta'] }}</span>
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-3 gap-2">
                @foreach ([
                    ['label' => 'Quiz', 'icon' => 'fa-circle-question', 'class' => 'bg-violet-500'],
                    ['label' => 'Lab', 'icon' => 'fa-flask', 'class' => 'bg-cyan-500'],
                    ['label' => 'Ship', 'icon' => 'fa-rocket', 'class' => 'bg-purple-500'],
                ] as $pill)
                    <span class="bk-bounce-pill rounded-[8px] {{ $pill['class'] }} px-3 py-2 text-center text-xs font-black text-white" style="--i: {{ $loop->index }}">
                        <i class="fas {{ $pill['icon'] }} mr-1"></i>{{ $pill['label'] }}
                    </span>
                @endforeach
            </div>
        </div>
    </div>
</div>
