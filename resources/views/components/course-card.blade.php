@props(['course'])

<article {{ $attributes->class(['bk-panel flex h-full flex-col overflow-hidden']) }}>
    <a href="{{ route('courses.show', $course->slug) }}" tabindex="-1" aria-hidden="true">
        <img src="{{ $course->thumbnail_url }}" alt="" loading="lazy" width="640" height="400" class="aspect-[16/10] w-full bg-slate-100 object-cover">
    </a>
    <div class="flex flex-1 flex-col p-5 sm:p-6">
        <div class="flex flex-wrap items-center justify-between gap-2 text-xs font-semibold text-content-muted">
            <span>{{ $course->category?->name ?? 'Technology' }}</span>
            <span>{{ ucfirst($course->difficulty_level) }}</span>
        </div>
        <h3 class="mt-4 text-lg font-bold leading-snug text-content"><a href="{{ route('courses.show', $course->slug) }}" class="hover:underline">{{ $course->title }}</a></h3>
        <p class="mt-3 line-clamp-3 text-sm leading-6 text-content-muted">{{ \Illuminate\Support\Str::limit(strip_tags($course->description ?? ''), 150) }}</p>
        <div class="mt-auto flex flex-wrap items-center justify-between gap-3 border-t border-line pt-4 text-sm">
            <span class="mt-4 text-content-muted">{{ $course->all_lessons_count ?? $course->total_lessons }} lessons</span>
            <span class="mt-4 font-bold tabular-nums text-content">{{ (float) $course->price > 0 ? '₦'.number_format($course->price, 2) : 'Free' }}</span>
        </div>
    </div>
</article>
