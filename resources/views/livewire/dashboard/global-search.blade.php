<div class="space-y-6">
    <x-ui.page-header title="Search workspace" description="Find the courses, resources and tools available to your account." />
    <section class="rounded-lg border border-themed-primary bg-themed-secondary p-4 shadow-sm sm:p-5">
        <label for="workspace-search-page" class="text-xs font-bold uppercase tracking-wide text-themed-tertiary">
            Search workspace
        </label>
        <div class="mt-2 flex flex-col gap-3 sm:flex-row">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-sm text-themed-tertiary"></i>
                <input
                    id="workspace-search-page"
                    type="search"
                    wire:model.live.debounce.300ms="q"
                    placeholder="Search courses, lessons, users, certificates, pages, posts..."
                    class="h-12 w-full rounded-lg border border-themed-primary bg-themed-tertiary pl-10 pr-4 text-sm font-semibold text-themed-primary placeholder:text-themed-tertiary outline-none transition focus:border-brand focus:bg-themed-secondary focus:ring-4 focus:ring-brand/30"
                    autofocus
                >
            </div>

            <a href="{{ route('dashboard') }}" class="inline-flex h-12 items-center justify-center gap-2 rounded-lg border border-themed-primary px-4 text-sm font-bold text-themed-secondary transition hover:bg-themed-tertiary hover:text-themed-primary">
                <i class="fas fa-arrow-left"></i>
                Dashboard
            </a>
        </div>

        <div class="mt-3 text-sm font-semibold text-themed-secondary">
            @if($q)
                {{ $total }} {{ \Illuminate\Support\Str::plural('result', $total) }} for <span class="font-bold text-themed-primary">"{{ $q }}"</span>
            @else
                Start typing to search all connected dashboard records.
            @endif
        </div>
    </section>

    @if($q && $total === 0)
        <section class="rounded-lg border border-dashed border-themed-primary bg-themed-secondary px-4 py-12 text-center">
            <i class="fas fa-search text-3xl text-themed-tertiary"></i>
            <h2 class="bk-display mt-4 text-xl font-bold text-themed-primary">No results found</h2>
            <p class="mt-2 text-sm font-semibold text-themed-secondary">Try a course title, lesson name, user email, certificate number, page, post, or material keyword.</p>
        </section>
    @endif

    @if($total > 0)
        <div class="space-y-5">
            @foreach($groups as $group)
                <section class="space-y-3">
                    <div class="flex items-center justify-between gap-3">
                        <h2 class="flex items-center gap-2 text-sm font-bold uppercase tracking-wide text-themed-secondary">
                            <i class="{{ $group['icon'] }} text-brand"></i>
                            {{ $group['label'] }}
                        </h2>
                        <span class="rounded-full bg-themed-tertiary px-2.5 py-1 text-xs font-bold text-themed-tertiary">
                            {{ count($group['items']) }}
                        </span>
                    </div>

                    <div class="grid gap-3 lg:grid-cols-2">
                        @foreach($group['items'] as $item)
                            <a href="{{ $item['url'] }}" class="group flex min-w-0 gap-3 rounded-lg border border-themed-primary bg-themed-secondary p-4 shadow-sm transition hover:border-brand hover:bg-themed-tertiary">
                                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-lg bg-brand/10 text-brand dark:bg-teal-400/10 dark:text-brand">
                                    <i class="{{ $item['icon'] }}"></i>
                                </span>
                                <span class="min-w-0">
                                    <span class="block truncate text-sm font-bold text-themed-primary group-hover:text-brand dark:group-hover:text-brand">{{ $item['title'] }}</span>
                                    @if($item['description'])
                                        <span class="mt-1 line-clamp-2 block text-sm font-semibold leading-5 text-themed-secondary">{{ $item['description'] }}</span>
                                    @endif
                                    <span class="mt-2 block truncate text-xs font-bold uppercase tracking-wide text-themed-tertiary">{{ $item['meta'] }}</span>
                                </span>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    @endif
</div>
