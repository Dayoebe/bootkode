@php
    $tabs = [
        'overview' => ['label' => 'Overview', 'icon' => 'fa-gauge-high'],
        'errors' => ['label' => 'Errors', 'icon' => 'fa-bug'],
        'jobs' => ['label' => 'Failed Jobs', 'icon' => 'fa-list-check'],
        'mail' => ['label' => 'Mail', 'icon' => 'fa-envelope-circle-check'],
        'webhooks' => ['label' => 'Webhooks', 'icon' => 'fa-code-branch'],
        'slow' => ['label' => 'Slow Pages', 'icon' => 'fa-stopwatch'],
        'routes' => ['label' => 'Broken Routes', 'icon' => 'fa-link-slash'],
    ];

    $severityClasses = [
        'critical' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
        'error' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300',
        'warning' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
        'info' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
    ];

    $statusClasses = [
        'open' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
        'resolved' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
        'ignored' => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
    ];
@endphp

<div class="px-4 sm:px-6 lg:px-8 py-8 space-y-6">
    <div class="bg-themed-secondary border border-themed-primary rounded-lg p-6 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <p class="text-sm font-semibold text-red-600 dark:text-red-400 uppercase tracking-wide">Production Observability</p>
                <h1 class="text-2xl md:text-3xl font-bold text-themed-primary mt-1">Issues that need operator attention</h1>
                <p class="text-themed-secondary mt-2 max-w-3xl">Errors, queue failures, mail failures, webhook failures, slow pages, and broken routes are now visible from one admin screen.</p>
            </div>

            <button type="button" wire:click="$refresh"
                    class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold shadow-sm">
                <i class="fas fa-rotate mr-2"></i> Refresh
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-6 gap-4">
        <button type="button" wire:click="showTab('errors')" class="text-left bg-themed-secondary border border-themed-primary rounded-lg p-4 hover:border-red-300 transition">
            <div class="flex items-center justify-between">
                <span class="text-sm text-themed-secondary">Open errors</span>
                <i class="fas fa-bug text-red-500"></i>
            </div>
            <p class="text-3xl font-bold text-themed-primary mt-3">{{ number_format($summary['open_errors']) }}</p>
        </button>
        <button type="button" wire:click="showTab('jobs')" class="text-left bg-themed-secondary border border-themed-primary rounded-lg p-4 hover:border-orange-300 transition">
            <div class="flex items-center justify-between">
                <span class="text-sm text-themed-secondary">Failed jobs</span>
                <i class="fas fa-list-check text-orange-500"></i>
            </div>
            <p class="text-3xl font-bold text-themed-primary mt-3">{{ number_format($summary['failed_jobs']) }}</p>
        </button>
        <button type="button" wire:click="showTab('mail')" class="text-left bg-themed-secondary border border-themed-primary rounded-lg p-4 hover:border-indigo-300 transition">
            <div class="flex items-center justify-between">
                <span class="text-sm text-themed-secondary">Mail failures</span>
                <i class="fas fa-envelope-circle-check text-indigo-500"></i>
            </div>
            <p class="text-3xl font-bold text-themed-primary mt-3">{{ number_format($summary['mail_failures']) }}</p>
        </button>
        <button type="button" wire:click="showTab('webhooks')" class="text-left bg-themed-secondary border border-themed-primary rounded-lg p-4 hover:border-purple-300 transition">
            <div class="flex items-center justify-between">
                <span class="text-sm text-themed-secondary">Webhook failures</span>
                <i class="fas fa-code-branch text-purple-500"></i>
            </div>
            <p class="text-3xl font-bold text-themed-primary mt-3">{{ number_format($summary['webhook_failures']) }}</p>
        </button>
        <button type="button" wire:click="showTab('slow')" class="text-left bg-themed-secondary border border-themed-primary rounded-lg p-4 hover:border-amber-300 transition">
            <div class="flex items-center justify-between">
                <span class="text-sm text-themed-secondary">Slow pages</span>
                <i class="fas fa-stopwatch text-amber-500"></i>
            </div>
            <p class="text-3xl font-bold text-themed-primary mt-3">{{ number_format($summary['slow_pages']) }}</p>
        </button>
        <button type="button" wire:click="showTab('routes')" class="text-left bg-themed-secondary border border-themed-primary rounded-lg p-4 hover:border-rose-300 transition">
            <div class="flex items-center justify-between">
                <span class="text-sm text-themed-secondary">Broken routes</span>
                <i class="fas fa-link-slash text-rose-500"></i>
            </div>
            <p class="text-3xl font-bold text-themed-primary mt-3">{{ number_format($summary['broken_routes']) }}</p>
        </button>
    </div>

    <div class="bg-themed-secondary border border-themed-primary rounded-lg overflow-hidden">
        <div class="flex overflow-x-auto border-b border-themed-primary">
            @foreach($tabs as $key => $tab)
                <button type="button" wire:click="showTab('{{ $key }}')"
                        class="min-w-max px-4 py-3 text-sm font-semibold border-b-2 transition {{ $activeTab === $key ? 'border-blue-600 text-blue-600 bg-blue-50 dark:bg-blue-900/20 dark:text-blue-300' : 'border-transparent text-themed-secondary hover:text-themed-primary' }}">
                    <i class="fas {{ $tab['icon'] }} mr-2"></i>{{ $tab['label'] }}
                </button>
            @endforeach
        </div>

        <div class="p-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-themed-secondary mb-1">Type</label>
                    <select wire:model.live="typeFilter" class="w-full rounded-lg border border-themed-primary bg-themed-tertiary text-themed-primary px-3 py-2 text-sm">
                        <option value="all">All event types</option>
                        @foreach($types as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-themed-secondary mb-1">Status</label>
                    <select wire:model.live="statusFilter" class="w-full rounded-lg border border-themed-primary bg-themed-tertiary text-themed-primary px-3 py-2 text-sm">
                        <option value="all">All statuses</option>
                        <option value="open">Open</option>
                        <option value="resolved">Resolved</option>
                        <option value="ignored">Ignored</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-themed-secondary mb-1">Severity</label>
                    <select wire:model.live="severityFilter" class="w-full rounded-lg border border-themed-primary bg-themed-tertiary text-themed-primary px-3 py-2 text-sm">
                        <option value="all">All severities</option>
                        @foreach($severities as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-themed-secondary mb-1">Search</label>
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-3 text-themed-secondary text-xs"></i>
                        <input type="search" wire:model.live.debounce.350ms="search"
                               class="w-full rounded-lg border border-themed-primary bg-themed-tertiary text-themed-primary pl-9 pr-3 py-2 text-sm"
                               placeholder="Summary, source, URL...">
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($activeTab === 'jobs')
        <div class="bg-themed-secondary border border-themed-primary rounded-lg overflow-hidden">
            <div class="px-5 py-4 border-b border-themed-primary">
                <h2 class="font-bold text-themed-primary">Latest failed jobs from Laravel queue</h2>
            </div>
            <div class="divide-y divide-themed-primary">
                @forelse($failedJobs as $job)
                    <div class="p-5">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-3">
                            <div>
                                <p class="font-semibold text-themed-primary">{{ $job->name }}</p>
                                <p class="text-sm text-themed-secondary mt-1">{{ $job->connection }} / {{ $job->queue }} / {{ $job->uuid }}</p>
                                <p class="text-sm text-red-600 dark:text-red-300 mt-2">{{ $job->exception }}</p>
                            </div>
                            <span class="text-xs text-themed-secondary">{{ \Illuminate\Support\Carbon::parse($job->failed_at)->diffForHumans() }}</span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-themed-secondary">No failed jobs recorded.</div>
                @endforelse
            </div>
        </div>
    @endif

    @if($activeTab === 'mail')
        <div class="bg-themed-secondary border border-themed-primary rounded-lg overflow-hidden">
            <div class="px-5 py-4 border-b border-themed-primary">
                <h2 class="font-bold text-themed-primary">Newsletter and app mail failures</h2>
            </div>
            <div class="divide-y divide-themed-primary">
                @forelse($mailFailures as $failure)
                    <div class="p-5">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-3">
                            <div>
                                <p class="font-semibold text-themed-primary">{{ $failure->campaign?->name ?? $failure->campaign?->subject ?? 'Newsletter send' }}</p>
                                <p class="text-sm text-themed-secondary">{{ $failure->subscriber?->email }} {{ $failure->subscriber?->full_name ? '(' . $failure->subscriber->full_name . ')' : '' }}</p>
                                <p class="text-sm text-red-600 dark:text-red-300 mt-2">{{ $failure->error_message ?: 'Mail send failed without a stored provider message.' }}</p>
                            </div>
                            <span class="text-xs text-themed-secondary">{{ $failure->updated_at?->diffForHumans() }}</span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-themed-secondary">No newsletter mail failures recorded.</div>
                @endforelse
            </div>
        </div>
    @endif

    @if($activeTab === 'webhooks')
        <div class="bg-themed-secondary border border-themed-primary rounded-lg overflow-hidden">
            <div class="px-5 py-4 border-b border-themed-primary">
                <h2 class="font-bold text-themed-primary">Payment webhook and gateway failures</h2>
            </div>
            <div class="divide-y divide-themed-primary">
                @forelse($webhookFailures as $failure)
                    <div class="p-5">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-3">
                            <div>
                                <p class="font-semibold text-themed-primary">{{ $failure->summary }}</p>
                                <p class="text-sm text-themed-secondary">{{ $failure->source ?? 'payment' }} {{ isset($failure->reference) ? '/ ' . $failure->reference : '' }}</p>
                                <p class="text-sm text-red-600 dark:text-red-300 mt-2">{{ str($failure->message ?? 'Webhook failure')->limit(240) }}</p>
                            </div>
                            <span class="text-xs text-themed-secondary">{{ $failure->last_seen_at?->diffForHumans() }}</span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-themed-secondary">No payment webhook failures recorded.</div>
                @endforelse
            </div>
        </div>
    @endif

    @if($activeTab === 'slow')
        <div class="bg-themed-secondary border border-themed-primary rounded-lg overflow-hidden">
            <div class="px-5 py-4 border-b border-themed-primary">
                <h2 class="font-bold text-themed-primary">Slow page captures</h2>
            </div>
            <div class="divide-y divide-themed-primary">
                @forelse($slowPages as $page)
                    <div class="p-5">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-3">
                            <div>
                                <p class="font-semibold text-themed-primary">{{ $page->route_name ?: $page->summary }}</p>
                                <p class="text-sm text-themed-secondary break-all">{{ $page->method }} {{ $page->url }}</p>
                                <p class="text-sm text-amber-700 dark:text-amber-300 mt-2">{{ number_format($page->duration_ms) }}ms, seen {{ number_format($page->occurrences) }} time{{ $page->occurrences === 1 ? '' : 's' }}</p>
                            </div>
                            <span class="text-xs text-themed-secondary">{{ $page->last_seen_at?->diffForHumans() }}</span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-themed-secondary">No slow pages recorded.</div>
                @endforelse
            </div>
        </div>
    @endif

    @if($activeTab === 'routes')
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">
            <div class="bg-themed-secondary border border-themed-primary rounded-lg overflow-hidden">
                <div class="px-5 py-4 border-b border-themed-primary">
                    <h2 class="font-bold text-themed-primary">Broken dashboard/menu route names</h2>
                </div>
                <div class="divide-y divide-themed-primary">
                    @forelse($brokenNamedRoutes as $route)
                        <div class="p-5">
                            <p class="font-semibold text-themed-primary">{{ $route['label'] }}</p>
                            <p class="text-sm text-themed-secondary">{{ $route['section'] }}</p>
                            <code class="inline-block mt-2 text-xs bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-300 px-2 py-1 rounded">{{ $route['route_name'] }}</code>
                        </div>
                    @empty
                        <div class="p-8 text-center text-themed-secondary">No broken menu route names found.</div>
                    @endforelse
                </div>
            </div>

            <div class="bg-themed-secondary border border-themed-primary rounded-lg overflow-hidden">
                <div class="px-5 py-4 border-b border-themed-primary">
                    <h2 class="font-bold text-themed-primary">Recent 404 route hits</h2>
                </div>
                <div class="divide-y divide-themed-primary">
                    @forelse($brokenRouteHits as $hit)
                        <div class="p-5">
                            <p class="font-semibold text-themed-primary break-all">{{ $hit->method }} {{ $hit->url }}</p>
                            <p class="text-sm text-themed-secondary mt-1">Seen {{ number_format($hit->occurrences) }} time{{ $hit->occurrences === 1 ? '' : 's' }} / {{ $hit->last_seen_at?->diffForHumans() }}</p>
                        </div>
                    @empty
                        <div class="p-8 text-center text-themed-secondary">No 404 hits recorded yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    <div class="bg-themed-secondary border border-themed-primary rounded-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-themed-primary flex flex-col lg:flex-row lg:items-center lg:justify-between gap-2">
            <div>
                <h2 class="font-bold text-themed-primary">Captured observability events</h2>
                <p class="text-sm text-themed-secondary">Open items should be resolved or ignored after an operator checks the underlying issue.</p>
            </div>
            <span class="text-xs text-themed-secondary">{{ $events->total() }} matching event{{ $events->total() === 1 ? '' : 's' }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-themed-primary">
                <thead class="bg-themed-tertiary">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-bold uppercase text-themed-secondary">Issue</th>
                        <th class="px-5 py-3 text-left text-xs font-bold uppercase text-themed-secondary">Source</th>
                        <th class="px-5 py-3 text-left text-xs font-bold uppercase text-themed-secondary">Impact</th>
                        <th class="px-5 py-3 text-left text-xs font-bold uppercase text-themed-secondary">Last seen</th>
                        <th class="px-5 py-3 text-right text-xs font-bold uppercase text-themed-secondary">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-themed-primary">
                    @forelse($events as $event)
                        <tr class="align-top">
                            <td class="px-5 py-4 max-w-xl">
                                <div class="flex flex-wrap items-center gap-2 mb-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold {{ $severityClasses[$event->severity] ?? $severityClasses['warning'] }}">{{ ucfirst($event->severity) }}</span>
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold {{ $statusClasses[$event->status] ?? $statusClasses['open'] }}">{{ ucfirst($event->status) }}</span>
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold bg-themed-tertiary text-themed-secondary">{{ $event->type_label }}</span>
                                </div>
                                <p class="font-semibold text-themed-primary">{{ $event->summary }}</p>
                                @if($event->message)
                                    <p class="text-sm text-themed-secondary mt-1">{{ str($event->message)->limit(220) }}</p>
                                @endif
                                @if($event->url)
                                    <p class="text-xs text-themed-secondary mt-2 break-all">{{ $event->method }} {{ $event->url }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-sm text-themed-secondary">
                                <p class="font-medium text-themed-primary">{{ $event->source ?: 'app' }}</p>
                                @if($event->route_name)
                                    <p class="text-xs mt-1">{{ $event->route_name }}</p>
                                @endif
                                @if($event->user)
                                    <p class="text-xs mt-1">{{ $event->user->name }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-sm text-themed-secondary">
                                <p>{{ number_format($event->occurrences) }} occurrence{{ $event->occurrences === 1 ? '' : 's' }}</p>
                                @if($event->duration_ms)
                                    <p class="mt-1">{{ number_format($event->duration_ms) }}ms</p>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-sm text-themed-secondary">
                                <p>{{ $event->last_seen_at?->diffForHumans() }}</p>
                                <p class="text-xs mt-1">{{ $event->last_seen_at?->format('M j, Y H:i') }}</p>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="inline-flex items-center gap-2">
                                    @if($event->status === 'open')
                                        <button type="button" wire:click="resolveEvent({{ $event->id }})" class="px-3 py-1.5 rounded bg-green-600 hover:bg-green-700 text-white text-xs font-semibold">
                                            <i class="fas fa-check mr-1"></i> Resolve
                                        </button>
                                        <button type="button" wire:click="ignoreEvent({{ $event->id }})" class="px-3 py-1.5 rounded bg-gray-600 hover:bg-gray-700 text-white text-xs font-semibold">
                                            <i class="fas fa-eye-slash mr-1"></i> Ignore
                                        </button>
                                    @else
                                        <button type="button" wire:click="reopenEvent({{ $event->id }})" class="px-3 py-1.5 rounded bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold">
                                            <i class="fas fa-arrow-rotate-left mr-1"></i> Reopen
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-themed-secondary">
                                <i class="fas fa-circle-check text-green-500 text-3xl mb-3"></i>
                                <p>No matching observability events.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($events->hasPages())
            <div class="px-5 py-4 border-t border-themed-primary">
                {{ $events->links() }}
            </div>
        @endif
    </div>

    @if($activeTab === 'overview')
        <div class="bg-themed-secondary border border-themed-primary rounded-lg overflow-hidden">
            <div class="px-5 py-4 border-b border-themed-primary">
                <h2 class="font-bold text-themed-primary">Recent Laravel log warnings and errors</h2>
            </div>
            <div class="divide-y divide-themed-primary">
                @forelse($recentLogs as $log)
                    <div class="p-5">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-3">
                            <div>
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-semibold {{ ($log['level'] ?? '') === 'ERROR' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300' }}">{{ $log['level'] }}</span>
                                <p class="text-sm text-themed-primary mt-2">{{ $log['message'] }}</p>
                            </div>
                            <span class="text-xs text-themed-secondary">{{ $log['date'] }}</span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-themed-secondary">No recent warning/error lines found in the Laravel log.</div>
                @endforelse
            </div>
        </div>
    @endif
</div>
