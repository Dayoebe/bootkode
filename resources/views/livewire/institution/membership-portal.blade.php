<div class="mx-auto max-w-5xl space-y-6 p-4 sm:p-6">
    <x-ui.page-header title="Institution membership" description="Your active institutional administration memberships. Platform staff manage partnerships, licenses and bulk operations." />
    <x-ui.flash />
    @forelse($institutions as $institution)
        <x-ui.panel :title="$institution->name">
            <dl class="grid gap-4 sm:grid-cols-2">
                <div><dt class="text-sm text-content-muted">Institution status</dt><dd class="font-semibold text-content">{{ ucfirst($institution->status) }}</dd></div>
                <div><dt class="text-sm text-content-muted">License end date</dt><dd class="font-semibold text-content">{{ $institution->license_end_date?->format('j M Y') ?? 'Not recorded' }}</dd></div>
            </dl>
            <a href="{{ route('dashboard') }}" class="bk-button bk-button-primary mt-5">Open learning dashboard</a>
        </x-ui.panel>
    @empty
        <x-ui.empty-state title="No active membership" description="Ask your institution contact to review your invitation." />
    @endforelse
</div>
