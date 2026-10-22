<div class="space-y-6">
    <x-ui.page-header title="Financial rules" description="The current rules used for course revenue and withdrawal requests." />
    <x-ui.panel title="Current limits">
        <dl class="grid gap-6 sm:grid-cols-3">
            <div><dt class="text-sm text-content-muted">Default platform share</dt><dd class="mt-2 text-xl font-bold tabular-nums">{{ $platformPercentage }}%</dd></div>
            <div><dt class="text-sm text-content-muted">Minimum withdrawal</dt><dd class="mt-2 text-xl font-bold tabular-nums">₦{{ number_format($minimumWithdrawal, 2) }}</dd></div>
            <div><dt class="text-sm text-content-muted">Maximum withdrawal</dt><dd class="mt-2 text-xl font-bold tabular-nums">₦{{ number_format($maximumWithdrawal, 2) }}</dd></div>
        </dl>
        <p class="mt-6 text-sm leading-6 text-content-muted">Existing course-specific revenue splits are preserved. Withdrawals require administrator review.</p>
    </x-ui.panel>
</div>
