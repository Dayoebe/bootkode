<div class="space-y-6 p-4 sm:p-6 lg:p-8">
    <div class="rounded-xl border border-themed-primary bg-themed-secondary p-6 shadow-sm">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-accent-primary">Revenue reports</p>
                <h1 class="text-2xl font-bold text-themed-primary">Commercial revenue report</h1>
                <p class="mt-1 max-w-2xl text-sm text-themed-secondary">
                    Gross revenue, net revenue, refunds, commissions, instructor earnings, affiliate commission, and payout exposure.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <input type="date" wire:model.live="dateFrom" class="rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary">
                <input type="date" wire:model.live="dateTo" class="rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary">
                <button type="button" wire:click="exportCsv" class="rounded-lg bg-accent-primary px-4 py-2 text-sm font-semibold text-white hover:bg-accent-secondary">
                    Export CSV
                </button>
            </div>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-lg border border-themed-primary bg-themed-secondary p-4">
            <p class="text-xs font-semibold uppercase text-themed-secondary">Gross revenue</p>
            <p class="mt-2 text-2xl font-bold text-themed-primary">{{ $report['formatted']['gross_revenue'] }}</p>
        </div>
        <div class="rounded-lg border border-themed-primary bg-themed-secondary p-4">
            <p class="text-xs font-semibold uppercase text-themed-secondary">Net revenue</p>
            <p class="mt-2 text-2xl font-bold text-emerald-600">{{ $report['formatted']['net_revenue'] }}</p>
        </div>
        <div class="rounded-lg border border-themed-primary bg-themed-secondary p-4">
            <p class="text-xs font-semibold uppercase text-themed-secondary">Refunds</p>
            <p class="mt-2 text-2xl font-bold text-orange-600">{{ $report['formatted']['refunds'] }}</p>
        </div>
        <div class="rounded-lg border border-themed-primary bg-themed-secondary p-4">
            <p class="text-xs font-semibold uppercase text-themed-secondary">Platform commission</p>
            <p class="mt-2 text-2xl font-bold text-themed-primary">{{ $report['formatted']['platform_commission'] }}</p>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1fr_420px]">
        <div class="rounded-xl border border-themed-primary bg-themed-secondary p-5 shadow-sm">
            <h2 class="text-lg font-semibold text-themed-primary">Daily movement</h2>
            <div class="mt-5 overflow-x-auto">
                <table class="min-w-full divide-y divide-themed-primary text-sm">
                    <thead>
                        <tr class="text-left text-xs font-semibold uppercase text-themed-secondary">
                            <th class="py-3">Date</th>
                            <th class="py-3 text-right">Payments</th>
                            <th class="py-3 text-right">Marketplace</th>
                            <th class="py-3 text-right">Refunds</th>
                            <th class="py-3 text-right">Net</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-themed-primary">
                        @forelse(array_reverse($report['breakdown']) as $row)
                            <tr>
                                <td class="py-3 text-themed-secondary">{{ $row['date'] }}</td>
                                <td class="py-3 text-right text-themed-primary">₦{{ number_format($row['payments'], 2) }}</td>
                                <td class="py-3 text-right text-themed-primary">₦{{ number_format($row['orders'], 2) }}</td>
                                <td class="py-3 text-right text-orange-600">₦{{ number_format($row['refunds'], 2) }}</td>
                                <td class="py-3 text-right font-semibold text-themed-primary">₦{{ number_format($row['net'], 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-8 text-center text-themed-secondary">No revenue movement in this period.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-xl border border-themed-primary bg-themed-secondary p-5 shadow-sm">
            <h2 class="text-lg font-semibold text-themed-primary">Operating split</h2>
            <div class="mt-4 space-y-3">
                @foreach([
                    'successful_payments' => 'Successful Paystack payments',
                    'wallet_course_sales' => 'Wallet course sales',
                    'marketplace_revenue' => 'Marketplace revenue',
                    'instructor_earnings' => 'Instructor/vendor earnings',
                    'affiliate_commission' => 'Affiliate commission',
                    'completed_payouts' => 'Completed payouts',
                    'pending_payouts' => 'Pending payouts',
                ] as $key => $label)
                    <div class="flex items-center justify-between rounded-lg bg-themed-tertiary px-4 py-3">
                        <span class="text-sm font-semibold text-themed-secondary">{{ $label }}</span>
                        <span class="text-sm font-bold text-themed-primary">{{ $report['formatted'][$key] }}</span>
                    </div>
                @endforeach
            </div>
            <a href="{{ route('admin.commercial.readiness') }}" class="mt-4 inline-flex rounded-lg border border-themed-primary px-3 py-2 text-sm font-semibold text-themed-primary hover:border-accent-primary hover:text-accent-primary">
                Open commercial readiness
            </a>
        </div>
    </div>
</div>
