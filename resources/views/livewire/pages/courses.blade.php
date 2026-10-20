<div class="bk-shell py-6 sm:py-10">
    <x-ui.page-header eyebrow="Learn. Build. Grow." title="Find your next skill" description="Explore published BootKode courses. Review the level, syllabus and price before you begin." />
    <form wire:submit.prevent class="bk-panel mb-8 grid gap-4 p-5 sm:grid-cols-[1fr_240px_auto] sm:items-end" role="search" aria-label="Find courses">
        <div><label for="course-search" class="mb-2 block text-sm font-semibold">Search courses</label><x-text-input id="course-search" type="search" wire:model.live.debounce.350ms="search" placeholder="What would you like to learn?" maxlength="120" /></div>
        <div><label for="course-category" class="mb-2 block text-sm font-semibold">Subject</label><select id="course-category" class="bk-input" wire:model.live="category"><option value="">All subjects</option>@foreach($categories as $item)<option value="{{ $item->id }}">{{ $item->name }}</option>@endforeach</select></div>
        <x-secondary-button wire:click="resetFilters">Clear filters</x-secondary-button>
    </form>
    <div class="mb-4 flex items-center justify-between text-sm text-content-muted" role="status">
        <span>{{ $courses->total() }} {{ \Illuminate\Support\Str::plural('course', $courses->total()) }}</span>
        <span wire:loading wire:target="search,category,resetFilters,gotoPage,nextPage,previousPage">Updating courses…</span>
    </div>
    <div class="grid gap-6 sm:grid-cols-2 xl:grid-cols-3" wire:loading.class="opacity-60">
        @forelse($courses as $course)
            <x-course-card :course="$course" wire:key="public-course-{{ $course->id }}" />
        @empty
            <x-ui.empty-state class="sm:col-span-2 xl:col-span-3" title="No courses found" description="Try another subject or clear your search. New courses appear here when they are published."><x-secondary-button wire:click="resetFilters">Show all courses</x-secondary-button></x-ui.empty-state>
        @endforelse
    </div>
    <div class="mt-8">{{ $courses->links() }}</div>
</div>
