<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $document->document_number }} - BootKode</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 text-slate-900">
    <main class="mx-auto max-w-4xl p-4 sm:p-8">
        <div class="mb-4 flex justify-end print:hidden">
            <button type="button" onclick="window.print()" class="rounded-[8px] bg-slate-950 px-4 py-2 text-sm font-bold text-white">Print / Save PDF</button>
        </div>

        <section class="rounded-[8px] bg-white p-6 shadow-sm print:shadow-none">
            <div class="flex flex-col gap-6 border-b border-slate-200 pb-6 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <div class="flex items-center gap-3">
                        <span class="grid h-11 w-11 place-items-center rounded-[8px] bg-slate-950 text-white">
                            <i class="fas fa-code"></i>
                        </span>
                        <div>
                            <p class="text-xl font-black">BootKode</p>
                            <p class="text-xs font-bold uppercase tracking-[0.16em] text-teal-700">Academy</p>
                        </div>
                    </div>
                    <p class="mt-4 max-w-sm text-sm leading-6 text-slate-600">
                        Africa-ready tech education with structured learning, mentorship, certification, marketplace, and career support.
                    </p>
                </div>

                <div class="text-left sm:text-right">
                    <p class="text-sm font-bold uppercase tracking-[0.18em] text-slate-500">{{ str($document->type)->headline() }}</p>
                    <h1 class="mt-1 text-2xl font-black text-slate-950">{{ $document->document_number }}</h1>
                    <p class="mt-2 text-sm text-slate-600">Issued {{ optional($document->issued_on)->format('F d, Y') }}</p>
                    <p class="text-sm text-slate-600">Status: {{ str($document->status)->headline() }}</p>
                </div>
            </div>

            <div class="grid gap-6 border-b border-slate-200 py-6 sm:grid-cols-2">
                <div>
                    <p class="text-xs font-black uppercase tracking-[0.16em] text-slate-400">Bill to</p>
                    <p class="mt-2 font-bold text-slate-950">{{ $document->customer_name ?: $document->user?->name ?: 'Customer' }}</p>
                    <p class="text-sm text-slate-600">{{ $document->customer_email ?: $document->user?->email }}</p>
                </div>
                <div class="sm:text-right">
                    <p class="text-xs font-black uppercase tracking-[0.16em] text-slate-400">Payment reference</p>
                    <p class="mt-2 font-mono text-sm text-slate-700">{{ $document->paystackTransaction?->reference ?? data_get($document->metadata, 'reference', 'N/A') }}</p>
                    @if($document->paid_at)
                        <p class="mt-1 text-sm text-slate-600">Paid {{ $document->paid_at->format('F d, Y H:i') }}</p>
                    @endif
                </div>
            </div>

            <div class="py-6">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-left text-xs font-black uppercase tracking-[0.12em] text-slate-500">
                            <th class="py-3">Item</th>
                            <th class="py-3 text-right">Qty</th>
                            <th class="py-3 text-right">Unit</th>
                            <th class="py-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($document->line_items ?? [] as $item)
                            <tr>
                                <td class="py-4 font-semibold text-slate-900">{{ $item['description'] ?? 'BootKode item' }}</td>
                                <td class="py-4 text-right text-slate-600">{{ $item['quantity'] ?? 1 }}</td>
                                <td class="py-4 text-right text-slate-600">{{ $document->formatMoney((float) ($item['unit_price'] ?? 0)) }}</td>
                                <td class="py-4 text-right font-semibold text-slate-900">{{ $document->formatMoney((float) ($item['total'] ?? 0)) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end border-t border-slate-200 pt-6">
                <div class="w-full max-w-sm space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-600">Subtotal</span>
                        <span class="font-semibold">{{ $document->formatMoney((float) $document->subtotal) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Discount</span>
                        <span class="font-semibold">{{ $document->formatMoney((float) $document->discount_total) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Tax</span>
                        <span class="font-semibold">{{ $document->formatMoney((float) $document->tax_total) }}</span>
                    </div>
                    <div class="flex justify-between border-t border-slate-200 pt-3 text-lg">
                        <span class="font-black">Total</span>
                        <span class="font-black">{{ $document->formatted_total }}</span>
                    </div>
                    @if((float) $document->amount_refunded > 0)
                        <div class="flex justify-between text-orange-700">
                            <span class="font-semibold">Refunded</span>
                            <span class="font-bold">{{ $document->formatMoney((float) $document->amount_refunded) }}</span>
                        </div>
                    @endif
                </div>
            </div>

            @if($document->notes)
                <div class="mt-6 rounded-[8px] bg-slate-50 p-4 text-sm text-slate-700">
                    {{ $document->notes }}
                </div>
            @endif
        </section>
    </main>
</body>
</html>
