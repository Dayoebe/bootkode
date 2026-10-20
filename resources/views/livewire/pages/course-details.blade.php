<div class="bk-shell py-6 sm:py-10">
    <a href="{{ route('courses.index') }}" class="mb-6 inline-flex min-h-11 items-center gap-2 text-sm font-bold text-brand"><span aria-hidden="true">←</span> All courses</a>
    <div class="grid gap-8 lg:grid-cols-[1fr_340px] lg:gap-12">
        <div>
            <x-ui.page-header :eyebrow="$course->category?->name ?? 'BootKode course'" :title="$course->title" :description="$course->subtitle" />
            <p class="mb-6 text-sm text-content-muted">{{ ucfirst($course->difficulty_level) }} · {{ $course->all_lessons_count }} lessons @if($course->instructor) · {{ $course->instructor->name }} @endif</p>
            <x-ui.panel title="About this course"><p class="whitespace-pre-line leading-7 text-content-muted">{{ strip_tags($course->description ?? '') }}</p></x-ui.panel>
            @if($course->learning_outcomes)
                <x-ui.panel class="mt-6" title="What you will learn"><ul class="list-disc space-y-3 pl-5 text-content-muted">@foreach((array) $course->learning_outcomes as $outcome) @if(is_string($outcome))<li>{{ $outcome }}</li>@endif @endforeach</ul></x-ui.panel>
            @endif
            <x-ui.panel class="mt-6" title="Course structure">
                <ol class="divide-y divide-line">@forelse($course->sections as $section)<li class="flex gap-4 py-4"><span class="text-sm font-bold text-brand">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><span class="font-semibold">{{ $section->title }}</span></li>@empty<li class="text-sm text-content-muted">The course outline will appear when available.</li>@endforelse</ol>
            </x-ui.panel>
        </div>
        <aside>
            <div class="bk-panel overflow-hidden lg:sticky lg:top-28">
                <img src="{{ $course->thumbnail_url }}" alt="" width="640" height="400" class="aspect-[16/10] w-full object-cover">
                <div class="space-y-5 p-6">
                    <p class="text-2xl font-bold tabular-nums">{{ (float) $course->price > 0 ? '₦'.number_format($course->price, 2) : 'Free course' }}</p>
                    @auth
                        <a href="{{ $enrolled ? route('course.view', $course->slug) : route('course.checkout', $course->id) }}" class="bk-button bk-button-primary w-full">{{ $enrolled ? 'Continue learning' : ((float) $course->price > 0 ? 'Review purchase' : 'Begin this course') }}</a>
                    @else
                        <a href="{{ route('register') }}" class="bk-button bk-button-primary w-full">Create a learning account</a>
                        <p class="text-sm text-content-muted">Already learning with us? <a href="{{ route('login') }}" class="font-bold text-brand underline">Sign in</a></p>
                    @endauth
                    <p class="text-sm leading-6 text-content-muted">Your account keeps your enrollment and learning progress together. @if((float) $course->price > 0) Payment is reviewed at checkout. @endif</p>
                </div>
            </div>
        </aside>
    </div>
</div>
