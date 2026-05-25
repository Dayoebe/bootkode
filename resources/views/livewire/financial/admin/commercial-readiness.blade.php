<div class="space-y-6 p-4 sm:p-6 lg:p-8">
    @if (session()->has('message'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800">
            {{ session('error') }}
        </div>
    @endif

    <div class="rounded-xl border border-themed-primary bg-themed-secondary p-6 shadow-sm">
        <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-accent-primary">Commercial readiness</p>
                <h1 class="text-2xl font-bold text-themed-primary">Revenue, billing, refunds, payouts, and packages</h1>
                <p class="mt-1 max-w-3xl text-sm text-themed-secondary">
                    Investor and customer-facing finance controls in one place: receipts, invoices, refund records, payout audit events, revenue exports, and public pricing clarity.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('pricing') }}" target="_blank" class="rounded-lg border border-themed-primary px-4 py-2 text-sm font-semibold text-themed-primary hover:border-accent-primary hover:text-accent-primary">
                    <i class="fas fa-tags mr-2"></i>Public Pricing
                </a>
                <button type="button" wire:click="generateMissingReceipts" class="rounded-lg bg-accent-primary px-4 py-2 text-sm font-semibold text-white hover:bg-accent-secondary">
                    <i class="fas fa-receipt mr-2"></i>Generate Receipts
                </button>
            </div>
        </div>

        @unless($tablesReady)
            <div class="mt-5 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                The commercial readiness tables are not migrated yet. Run <code class="font-mono">php artisan migrate</code> after MySQL is available.
            </div>
        @endunless
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
            <p class="text-xs font-semibold uppercase text-themed-secondary">Pending payouts</p>
            <p class="mt-2 text-2xl font-bold text-themed-primary">{{ $report['formatted']['pending_payouts'] }}</p>
        </div>
    </div>

    <div class="rounded-xl border border-themed-primary bg-themed-secondary p-2 shadow-sm">
        <div class="grid gap-2 md:grid-cols-5">
            @foreach([
                'overview' => ['Overview', 'fa-chart-line'],
                'documents' => ['Documents', 'fa-file-invoice'],
                'refunds' => ['Refunds', 'fa-rotate-left'],
                'payouts' => ['Payout Audit', 'fa-money-check-dollar'],
                'packages' => ['Packages', 'fa-tags'],
            ] as $tab => [$label, $icon])
                <button type="button" wire:click="setTab('{{ $tab }}')"
                    class="rounded-lg px-3 py-3 text-sm font-semibold transition {{ $activeTab === $tab ? 'bg-accent-primary text-white' : 'text-themed-secondary hover:bg-themed-tertiary hover:text-themed-primary' }}">
                    <i class="fas {{ $icon }} mr-2"></i>{{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    @if($activeTab === 'overview')
        <div class="grid gap-6 xl:grid-cols-[1fr_360px]">
            <div class="rounded-xl border border-themed-primary bg-themed-secondary p-5 shadow-sm">
                <div class="flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-themed-primary">Revenue report</h2>
                        <p class="text-sm text-themed-secondary">{{ $report['period']['from'] }} to {{ $report['period']['to'] }}</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <input type="date" wire:model.live="dateFrom" class="rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary">
                        <input type="date" wire:model.live="dateTo" class="rounded-lg border border-themed-primary bg-themed-secondary px-3 py-2 text-sm text-themed-primary">
                        <button type="button" wire:click="exportRevenueCsv" class="rounded-lg border border-themed-primary px-3 py-2 text-sm font-semibold text-themed-primary hover:border-accent-primary hover:text-accent-primary">
                            Export CSV
                        </button>
                    </div>
                </div>

                <div class="mt-5 overflow-x-auto">
                    <table class="min-w-full divide-y divide-themed-primary text-sm">
                        <tbody class="divide-y divide-themed-primary">
                            @foreach([
                                'successful_payments' => 'Successful Paystack payments',
                                'wallet_course_sales' => 'Wallet course sales',
                                'marketplace_revenue' => 'Marketplace revenue',
                                'platform_commission' => 'Platform commission',
                                'instructor_earnings' => 'Instructor/vendor earnings',
                                'affiliate_commission' => 'Affiliate commission',
                                'completed_payouts' => 'Completed payouts',
                            ] as $key => $label)
                                <tr>
                                    <td class="py-3 text-themed-secondary">{{ $label }}</td>
                                    <td class="py-3 text-right font-semibold text-themed-primary">{{ $report['formatted'][$key] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-xl border border-themed-primary bg-themed-secondary p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-themed-primary">Document coverage</h2>
                <div class="mt-4 space-y-3">
                    @foreach(['receipt' => 'Receipts', 'invoice' => 'Invoices', 'credit_note' => 'Credit notes'] as $type => $label)
                        <div class="flex items-center justify-between rounded-lg bg-themed-tertiary px-4 py-3">
                            <span class="text-sm font-semibold text-themed-secondary">{{ $label }}</span>
                            <span class="text-lg font-bold text-themed-primary">{{ $documentCounts[$type] ?? 0 }}</span>
                        </div>
                    @endforeach
                </div>
                <a href="{{ route('admin.payments.processing') }}" class="mt-4 inline-flex rounded-lg border border-themed-primary px-3 py-2 text-sm font-semibold text-themed-primary hover:border-accent-primary hover:text-accent-primary">
                    Open payment processing
                </a>
            </div>
        </div>
    @endif

    @if($activeTab === 'documents')
        <div class="rounded-xl border border-themed-primary bg-themed-secondary p-5 shadow-sm">
            <h2 class="text-lg font-semibold text-themed-primary">Recent receipts, invoices, and credit notes</h2>
            <div class="mt-5 overflow-x-auto">
                <table class="min-w-full divide-y divide-themed-primary text-sm">
                    <thead>
                        <tr class="text-left text-xs font-semibold uppercase text-themed-secondary">
                            <th class="py-3">Document</th>
                            <th class="py-3">Customer</th>
                            <th class="py-3">Type</th>
                            <th class="py-3 text-right">Total</th>
                            <th class="py-3 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-themed-primary">
                        @forelse($documents as $document)
                            <tr>
                                <td class="py-3">
                                    <a href="{{ route('commercial.documents.show', $document) }}" target="_blank" class="font-semibold text-accent-primary hover:underline">{{ $document->document_number }}</a>
                                    <p class="text-xs text-themed-tertiary">{{ optional($document->created_at)->format('M d, Y') }}</p>
                                </td>
                                <td class="py-3 text-themed-secondary">{{ $document->customer_name ?: $document->customer_email ?: 'N/A' }}</td>
                                <td class="py-3 text-themed-secondary">{{ str($document->type)->headline() }}</td>
                                <td class="py-3 text-right font-semibold text-themed-primary">{{ $document->formatted_total }}</td>
                                <td class="py-3 text-right text-themed-secondary">{{ str($document->status)->headline() }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-8 text-center text-themed-secondary">No billing documents yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if($activeTab === 'refunds')
        <div class="grid gap-6 xl:grid-cols-[420px_1fr]">
            <form wire:submit.prevent="processRefund" class="rounded-xl border border-themed-primary bg-themed-secondary p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-themed-primary">Process refund</h2>
                <div class="mt-4 space-y-4">
                    <select wire:model.live="refundTransactionId" class="w-full rounded-lg border border-themed-primary bg-themed-secondary px-3 py-3 text-sm text-themed-primary">
                        <option value="">Select successful payment</option>
                        @foreach($transactions as $transaction)
                            <option value="{{ $transaction['id'] }}">{{ $transaction['reference'] }} - {{ $transaction['customer'] }} - {{ $transaction['formatted_amount'] }}</option>
                        @endforeach
                    </select>
                    @error('refundTransactionId') <p class="text-sm text-red-500">{{ $message }}</p> @enderror

                    <input type="number" step="0.01" min="1" wire:model="refundAmount" class="w-full rounded-lg border border-themed-primary bg-themed-secondary px-3 py-3 text-sm text-themed-primary" placeholder="Refund amount">
                    @error('refundAmount') <p class="text-sm text-red-500">{{ $message }}</p> @enderror

                    <textarea rows="4" wire:model="refundReason" class="w-full rounded-lg border border-themed-primary bg-themed-secondary px-3 py-3 text-sm text-themed-primary" placeholder="Reason for refund"></textarea>
                    @error('refundReason') <p class="text-sm text-red-500">{{ $message }}</p> @enderror

                    <button type="submit" class="w-full rounded-lg bg-accent-primary px-4 py-3 text-sm font-semibold text-white hover:bg-accent-secondary">
                        Record Refund
                    </button>
                </div>
            </form>

            <div class="rounded-xl border border-themed-primary bg-themed-secondary p-5 shadow-sm">
                <h2 class="text-lg font-semibold text-themed-primary">Recent refund requests</h2>
                <div class="mt-5 space-y-3">
                    @forelse($refunds as $refund)
                        <div class="rounded-lg border border-themed-primary p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="font-semibold text-themed-primary">{{ $refund->refund_number }}</p>
                                    <p class="text-sm text-themed-secondary">{{ $refund->reason }}</p>
                                </div>
                                <span class="rounded-full bg-themed-tertiary px-3 py-1 text-xs font-semibold text-themed-primary">{{ str($refund->status)->headline() }}</span>
                            </div>
                            <p class="mt-2 text-sm font-semibold text-themed-primary">{{ $refund->formatted_amount }} · {{ $refund->method }}</p>
                        </div>
                    @empty
                        <p class="rounded-lg border border-dashed border-themed-primary p-8 text-center text-sm text-themed-secondary">No refund records yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    @if($activeTab === 'payouts')
        <div class="rounded-xl border border-themed-primary bg-themed-secondary p-5 shadow-sm">
            <h2 class="text-lg font-semibold text-themed-primary">Payout audit trail</h2>
            <div class="mt-5 space-y-3">
                @forelse($payoutAudits as $audit)
                    <div class="rounded-lg border border-themed-primary p-4">
                        <div class="flex flex-col gap-2 md:flex-row md:items-start md:justify-between">
                            <div>
                                <p class="font-semibold text-themed-primary">{{ str($audit->event)->headline() }}</p>
                                <p class="text-sm text-themed-secondary">
                                    {{ $audit->status_from ?: 'new' }} → {{ $audit->status_to ?: 'recorded' }}
                                    @if($audit->withdrawal)
                                        · {{ $audit->withdrawal->withdrawal_id }}
                                    @endif
                                </p>
                                @if($audit->notes)
                                    <p class="mt-1 text-sm text-themed-secondary">{{ $audit->notes }}</p>
                                @endif
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-themed-primary">{{ $audit->formatted_amount }}</p>
                                <p class="text-xs text-themed-tertiary">{{ optional($audit->created_at)->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="rounded-lg border border-dashed border-themed-primary p-8 text-center text-sm text-themed-secondary">No payout audit events yet.</p>
                @endforelse
            </div>
        </div>
    @endif

    @if($activeTab === 'packages')
        <div class="rounded-xl border border-themed-primary bg-themed-secondary p-5 shadow-sm">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-themed-primary">Public pricing packages</h2>
                    <p class="text-sm text-themed-secondary">These define the public-facing commercial story for learners, institutions, vendors, and partners.</p>
                </div>
                <button type="button" wire:click="seedPackages" class="rounded-lg border border-themed-primary px-3 py-2 text-sm font-semibold text-themed-primary hover:border-accent-primary hover:text-accent-primary">
                    Seed Defaults
                </button>
            </div>

            <div class="mt-5 grid gap-4 lg:grid-cols-2 xl:grid-cols-4">
                @foreach($publicPackages as $package)
                    <article class="rounded-lg border {{ $package['is_featured'] ? 'border-accent-primary' : 'border-themed-primary' }} p-4">
                        <p class="text-xs font-semibold uppercase text-themed-secondary">{{ $package['audience'] }}</p>
                        <h3 class="mt-2 font-bold text-themed-primary">{{ $package['name'] }}</h3>
                        <p class="mt-2 text-2xl font-black text-themed-primary">{{ $package['formatted_price'] }}</p>
                        <p class="mt-2 text-sm text-themed-secondary">{{ $package['description'] }}</p>
                        <ul class="mt-4 space-y-2 text-sm text-themed-secondary">
                            @foreach($package['features'] as $feature)
                                <li><i class="fas fa-check mr-2 text-emerald-500"></i>{{ $feature }}</li>
                            @endforeach
                        </ul>
                    </article>
                @endforeach
            </div>
        </div>
    @endif
</div>
