<div
    x-data="{
        packs: [],
        syncStatus: '',
        refresh() {
            this.packs = window.bootkodeOffline?.getPacks?.() ?? [];
        },
        async sync() {
            this.syncStatus = 'Syncing queued progress...';
            const result = await window.bootkodeOffline.syncQueuedProgress();
            this.syncStatus = result.synced > 0 ? `${result.synced} progress item(s) synced.` : 'No pending offline progress.';
            this.refresh();
        }
    }"
    x-init="refresh(); window.addEventListener('bootkode-offline-packs-updated', () => refresh())"
    class="space-y-6">
    <div class="rounded-xl border border-themed-primary bg-themed-secondary p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-themed-secondary">Mobile learning</p>
                <h1 class="mt-2 text-2xl font-bold text-themed-primary">Offline Learning Packs</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-themed-secondary">
                    Download course lessons, PDFs, images, and audio while online, read them in the offline reader, then sync progress when your connection returns.
                </p>
            </div>
            <button type="button" @click="sync"
                    class="inline-flex items-center justify-center rounded-lg bg-accent-themed-primary px-4 py-2 text-sm font-semibold text-white hover:bg-accent-themed-secondary">
                <i class="fas fa-rotate mr-2"></i>
                Sync Progress
            </button>
        </div>

        <template x-if="syncStatus">
            <div class="mt-4 rounded-lg border border-themed-secondary bg-themed-tertiary px-4 py-3 text-sm text-themed-secondary" x-text="syncStatus"></div>
        </template>
    </div>

    <div class="grid gap-4 lg:grid-cols-[1fr_22rem]">
        <div class="space-y-4">
            @forelse($packs as $pack)
                @php
                    $manifest = $pack->manifest ?? [];
                    $course = $pack->course;
                @endphp
                <article class="rounded-xl border border-themed-primary bg-themed-secondary p-5 shadow-sm">
                    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                                    {{ ucfirst($pack->status ?? 'ready') }}
                                </span>
                                <span class="rounded-full border border-themed-secondary bg-themed-tertiary px-3 py-1 text-xs font-semibold text-themed-secondary">
                                    {{ number_format((float) $pack->size_mb, 2) }} MB
                                </span>
                            </div>
                            <h2 class="mt-3 text-lg font-semibold text-themed-primary">{{ $course?->title ?? 'Course pack' }}</h2>
                            <p class="mt-1 text-sm text-themed-secondary">
                                {{ ($manifest['course']['lesson_count'] ?? $course?->sections?->flatMap->lessons?->count() ?? 0) }} lessons ·
                                downloaded {{ $pack->downloaded_at?->diffForHumans() ?? 'recently' }}
                            </p>
                            <div class="mt-3 h-2 overflow-hidden rounded-full bg-themed-tertiary">
                                <div class="h-full rounded-full bg-accent-themed-primary" style="width: {{ min(100, $pack->storage_usage_percentage) }}%"></div>
                            </div>
                            <p class="mt-1 text-xs text-themed-tertiary">
                                {{ $pack->storage_usage_percentage }}% of {{ number_format($pack->storage_limit_mb) }} MB pack limit.
                                @if($pack->last_synced_at)
                                    Last synced {{ $pack->last_synced_at->diffForHumans() }}.
                                @endif
                            </p>
                        </div>
                        <div class="grid gap-2 sm:grid-cols-3 md:w-72 md:grid-cols-1">
                            <button type="button"
                                    onclick="window.bootkodeOffline.openReader('{{ $course?->slug }}')"
                                    class="rounded-lg bg-accent-themed-primary px-4 py-2 text-sm font-semibold text-white hover:bg-accent-themed-secondary">
                                <i class="fas fa-book-reader mr-2"></i>Read Offline
                            </button>
                            <a href="{{ $course ? route('course.view', $course->slug) : '#' }}"
                               class="rounded-lg border border-themed-secondary bg-themed-tertiary px-4 py-2 text-center text-sm font-semibold text-themed-primary">
                                Online Course
                            </a>
                            <button type="button"
                                    onclick="window.bootkodeOffline.removePack('{{ $course?->slug }}')"
                                    class="rounded-lg border border-red-200 bg-red-50 px-4 py-2 text-sm font-semibold text-red-700 hover:bg-red-100 dark:border-red-800 dark:bg-red-900/20 dark:text-red-300">
                                Remove Local Pack
                            </button>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-xl border-2 border-dashed border-themed-secondary bg-themed-tertiary p-8 text-center">
                    <i class="fas fa-download mb-3 text-4xl text-themed-tertiary"></i>
                    <h2 class="text-lg font-semibold text-themed-primary">No offline packs yet</h2>
                    <p class="mt-2 text-sm text-themed-secondary">Open an enrolled course and use the offline pack panel to download it for mobile learning.</p>
                    <a href="{{ route('student.enrolled-courses') }}" class="mt-4 inline-flex items-center rounded-lg bg-accent-themed-primary px-4 py-2 text-sm font-semibold text-white">
                        <i class="fas fa-book mr-2"></i>My Courses
                    </a>
                </div>
            @endforelse
        </div>

        <aside class="rounded-xl border border-themed-primary bg-themed-secondary p-5 shadow-sm">
            <h2 class="text-base font-semibold text-themed-primary">This Device</h2>
            <div class="mt-4 space-y-3 text-sm text-themed-secondary">
                <div class="rounded-lg border border-themed-secondary bg-themed-tertiary p-3">
                    <p class="font-semibold text-themed-primary" x-text="packs.length"></p>
                    <p class="text-xs text-themed-tertiary">packs stored in this browser</p>
                </div>
                <div class="rounded-lg border border-themed-secondary bg-themed-tertiary p-3">
                    <p class="font-semibold text-themed-primary" x-text="packs.reduce((total, pack) => total + (pack.pendingCount || 0), 0)"></p>
                    <p class="text-xs text-themed-tertiary">pending progress item(s)</p>
                </div>
                <p>Offline packs are stored per browser/device. Removing browser data also removes downloaded packs.</p>
            </div>
        </aside>
    </div>
</div>
