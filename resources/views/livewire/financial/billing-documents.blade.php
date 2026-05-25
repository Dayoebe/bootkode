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
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-accent-primary">Billing</p>
                <h1 class="text-2xl font-bold text-themed-primary">Receipts & invoices</h1>
                <p class="mt-1 max-w-2xl text-sm text-themed-secondary">
                    View payment receipts, invoices, and refund credit notes attached to your BootKode account.
                </p>
            </div>
            <button type="button" wire:click="generateReceipts" class="rounded-lg bg-accent-primary px-4 py-3 text-sm font-semibold text-white hover:bg-accent-secondary">
                <i class="fas fa-rotate mr-2"></i>Update Receipts
            </button>
        </div>

        @unless($tablesReady)
            <div class="mt-5 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                Billing documents will appear after the commercial readiness migration has been applied.
            </div>
        @endunless
    </div>

    <div class="rounded-xl border border-themed-primary bg-themed-secondary p-5 shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-themed-primary text-sm">
                <thead>
                    <tr class="text-left text-xs font-semibold uppercase text-themed-secondary">
                        <th class="py-3">Document</th>
                        <th class="py-3">Type</th>
                        <th class="py-3">Issued</th>
                        <th class="py-3 text-right">Paid</th>
                        <th class="py-3 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-themed-primary">
                    @forelse($documents as $document)
                        <tr>
                            <td class="py-3">
                                <a href="{{ route('commercial.documents.show', $document) }}" target="_blank" class="font-semibold text-accent-primary hover:underline">{{ $document->document_number }}</a>
                                <p class="text-xs text-themed-tertiary">{{ $document->customer_email }}</p>
                            </td>
                            <td class="py-3 text-themed-secondary">{{ str($document->type)->headline() }}</td>
                            <td class="py-3 text-themed-secondary">{{ optional($document->issued_on)->format('M d, Y') }}</td>
                            <td class="py-3 text-right font-semibold text-themed-primary">{{ $document->formatted_paid }}</td>
                            <td class="py-3 text-right text-themed-secondary">{{ str($document->status)->headline() }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center">
                                <i class="fas fa-receipt text-4xl text-themed-tertiary"></i>
                                <h2 class="mt-3 text-lg font-semibold text-themed-primary">No billing documents yet</h2>
                                <p class="mt-1 text-sm text-themed-secondary">Successful payments will generate receipts here.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($documents, 'links'))
            <div class="mt-5">
                {{ $documents->links() }}
            </div>
        @endif
    </div>
</div>
