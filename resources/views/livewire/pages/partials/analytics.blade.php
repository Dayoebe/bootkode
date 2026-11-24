<div class="space-y-6">
    <section class="rounded-lg border border-line bg-surface p-6 shadow-sm" aria-labelledby="page-analytics-title">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 id="page-analytics-title" class="text-xl font-semibold text-content">Page analytics</h2>
                <p class="mt-1 text-content-muted">Recorded page counters and publishing activity only.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <label for="analytics-range" class="sr-only">Activity range</label>
                <select id="analytics-range" wire:model="analyticsRange" wire:change="updateAnalyticsRange"
                        class="rounded-lg border border-line bg-surface px-3 py-2 text-sm text-content focus:border-brand focus:ring-brand/30">
                    <option value="7">Last 7 days</option>
                    <option value="30">Last 30 days</option>
                    <option value="90">Last 90 days</option>
                    <option value="365">Last year</option>
                </select>
                <button type="button" wire:click="loadAnalyticsData" class="rounded-lg bg-action px-4 py-2 text-sm text-action-foreground focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand">Refresh</button>
                <button type="button" wire:click="exportAnalytics" class="rounded-lg border border-line px-4 py-2 text-sm font-medium text-content focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand">Export recorded data</button>
            </div>
        </div>
    </section>

    <dl class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach([
            'total_views' => ['Recorded views', 'fa-eye'],
            'total_pages' => ['All pages', 'fa-file-alt'],
            'published_pages' => ['Published pages', 'fa-circle-check'],
            'draft_pages' => ['Draft pages', 'fa-pen-to-square'],
        ] as $key => [$label, $icon])
            <div class="rounded-lg border border-line bg-surface p-5 shadow-sm">
                <dt class="flex items-center gap-2 text-sm font-medium text-content-muted"><i aria-hidden="true" class="fas {{ $icon }} text-brand"></i>{{ $label }}</dt>
                <dd class="mt-2 text-2xl font-bold text-content">{{ number_format($analyticsData[$key] ?? 0) }}</dd>
            </div>
        @endforeach
    </dl>

    <section class="rounded-lg border border-amber-300 bg-amber-50 p-5 text-amber-950" aria-labelledby="analytics-unavailable-title">
        <h3 id="analytics-unavailable-title" class="font-semibold">Detailed audience analytics are not available</h3>
        <p class="mt-1 text-sm">{{ $analyticsData['unavailable_reason'] ?? 'No event-level analytics source is configured.' }}</p>
        <p class="mt-2 text-sm">No visitor, revenue, conversion, session, referrer, device or performance values are estimated.</p>
    </section>

    <section class="overflow-hidden rounded-lg border border-line bg-surface shadow-sm" aria-labelledby="top-pages-title">
        <div class="border-b border-line px-6 py-4">
            <h3 id="top-pages-title" class="text-lg font-semibold text-content">Most-viewed published pages</h3>
            <p class="mt-1 text-sm text-content-muted">Lifetime counters currently stored on each page.</p>
        </div>
        <x-ui.table label="Recorded page views">
            <table class="min-w-full divide-y divide-line">
                <thead class="bg-surface-muted"><tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-content-muted">Page</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-content-muted">Recorded views</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-content-muted">Status</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-content-muted">Action</th>
                </tr></thead>
                <tbody class="divide-y divide-line bg-surface">
                    @forelse($topPages as $page)
                        <tr>
                            <td class="px-6 py-4"><p class="font-medium text-content">{{ $page['title'] }}</p><p class="text-sm text-content-muted">/{{ $page['slug'] }}</p></td>
                            <td class="px-6 py-4 text-sm text-content">{{ number_format($page['views']) }}</td>
                            <td class="px-6 py-4 text-sm text-content">{{ ucfirst($page['status']) }}</td>
                            <td class="px-6 py-4 text-right"><button type="button" wire:click="viewPageDetails({{ $page['id'] }})" class="rounded px-2 py-1 text-sm font-medium text-brand focus-visible:outline focus-visible:outline-2 focus-visible:outline-brand">View page</button></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-10 text-center text-content-muted">No published page counters are available yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </x-ui.table>
    </section>

    <section class="rounded-lg border border-line bg-surface p-6 shadow-sm" aria-labelledby="recent-page-activity-title">
        <h3 id="recent-page-activity-title" class="text-lg font-semibold text-content">Recent publishing activity</h3>
        <div class="mt-5 space-y-4">
            @forelse($recentActivity as $activity)
                <div class="flex items-start justify-between gap-4 border-b border-line pb-4 last:border-0 last:pb-0">
                    <div>
                        <p class="text-sm text-content"><span class="font-medium">{{ $activity['user_name'] }}</span> {{ $activity['action'] }} <span class="font-medium">{{ \Illuminate\Support\Str::limit($activity['page_title'], 60) }}</span></p>
                        <p class="mt-1 text-xs text-content-muted">{{ $activity['time'] }}</p>
                    </div>
                    <span class="rounded-full bg-surface-muted px-2 py-1 text-xs text-content">{{ ucfirst($activity['status']) }}</span>
                </div>
            @empty
                <p class="py-6 text-center text-content-muted">No publishing activity exists in this period.</p>
            @endforelse
        </div>
    </section>
</div>
