@props([
    'density' => 'default',
])

@php
    $signals = [
        ['icon' => 'fa-book-open', 'class' => 'bg-red-500', 'left' => 3, 'top' => 12],
        ['icon' => 'fa-laptop-code', 'class' => 'bg-orange-500', 'left' => 18, 'top' => 34],
        ['icon' => 'fa-chart-line', 'class' => 'bg-amber-500', 'left' => 34, 'top' => 18],
        ['icon' => 'fa-bolt', 'class' => 'bg-yellow-400 text-slate-950', 'left' => 48, 'top' => 48],
        ['icon' => 'fa-seedling', 'class' => 'bg-lime-500', 'left' => 63, 'top' => 22],
        ['icon' => 'fa-check', 'class' => 'bg-green-500', 'left' => 78, 'top' => 42],
        ['icon' => 'fa-certificate', 'class' => 'bg-emerald-500', 'left' => 91, 'top' => 14],
        ['icon' => 'fa-message', 'class' => 'bg-teal-500', 'left' => 12, 'top' => 71],
        ['icon' => 'fa-video', 'class' => 'bg-cyan-500', 'left' => 27, 'top' => 58],
        ['icon' => 'fa-cloud-arrow-up', 'class' => 'bg-sky-500', 'left' => 43, 'top' => 78],
        ['icon' => 'fa-code-branch', 'class' => 'bg-blue-500', 'left' => 59, 'top' => 65],
        ['icon' => 'fa-user-graduate', 'class' => 'bg-indigo-500', 'left' => 73, 'top' => 82],
        ['icon' => 'fa-wand-magic-sparkles', 'class' => 'bg-violet-500', 'left' => 88, 'top' => 64],
        ['icon' => 'fa-rocket', 'class' => 'bg-purple-500', 'left' => 8, 'top' => 43],
        ['icon' => 'fa-pen-nib', 'class' => 'bg-fuchsia-500', 'left' => 23, 'top' => 9],
        ['icon' => 'fa-heart', 'class' => 'bg-pink-500', 'left' => 39, 'top' => 39],
        ['icon' => 'fa-briefcase', 'class' => 'bg-rose-500', 'left' => 68, 'top' => 8],
        ['icon' => 'fa-layer-group', 'class' => 'bg-slate-500', 'left' => 83, 'top' => 29],
        ['icon' => 'fa-database', 'class' => 'bg-gray-500', 'left' => 31, 'top' => 85],
        ['icon' => 'fa-shield-halved', 'class' => 'bg-zinc-500', 'left' => 53, 'top' => 11],
        ['icon' => 'fa-diagram-project', 'class' => 'bg-neutral-500', 'left' => 96, 'top' => 78],
        ['icon' => 'fa-terminal', 'class' => 'bg-stone-500', 'left' => 1, 'top' => 88],
        ['icon' => 'fa-compass', 'class' => 'bk-bg-taupe', 'left' => 47, 'top' => 28],
        ['icon' => 'fa-palette', 'class' => 'bk-bg-mauve', 'left' => 74, 'top' => 55],
        ['icon' => 'fa-wifi', 'class' => 'bk-bg-mist text-slate-950', 'left' => 15, 'top' => 20],
        ['icon' => 'fa-leaf', 'class' => 'bk-bg-olive', 'left' => 57, 'top' => 89],
    ];

    $visible = $density === 'dense' ? $signals : array_slice($signals, 0, 18);
@endphp

<div {{ $attributes->merge(['class' => 'bk-icon-field absolute inset-0 overflow-hidden']) }} aria-hidden="true">
    @foreach ($visible as $signal)
        <span
            class="bk-icon-orbit absolute grid place-items-center rounded-[8px] {{ $signal['class'] }} text-white"
            style="--i: {{ $loop->index }}; left: {{ $signal['left'] }}%; top: {{ $signal['top'] }}%;"
        >
            <i class="fas {{ $signal['icon'] }} text-sm"></i>
        </span>
    @endforeach
</div>
