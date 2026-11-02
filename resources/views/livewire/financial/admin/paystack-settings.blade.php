<div class="space-y-6">
    <h1 class=" font-bold text-themed-primary bk-page-title">Payment configuration</h1>
    <div class="bk-dashboard-panel space-y-5 p-6">
        <p class="text-themed-secondary">Payment credentials are managed through the server environment. Keys are never sent to your browser.</p>
        <dl class="grid gap-5 sm:grid-cols-2">
            <div><dt class="text-sm text-themed-secondary">Configuration</dt><dd class="mt-1 font-semibold">{{ $configured ? 'Keys configured' : 'Setup required' }}</dd></div>
            <div><dt class="text-sm text-themed-secondary">Gateway mode</dt><dd class="mt-1 font-semibold">{{ $configured ? ($testMode ? 'Test mode' : 'Live mode') : 'Unavailable' }}</dd></div>
        </dl>
        <div>
            <label for="paystack-webhook" class="block text-sm font-semibold">Webhook URL</label>
            <input id="paystack-webhook" class="mt-2 w-full rounded-lg border border-themed-primary bg-themed-primary p-3" value="{{ $webhookUrl }}" readonly>
        </div>
        <p class="text-sm text-themed-secondary">Configured keys do not verify connectivity. Confirm payment and signed webhook delivery in the Paystack test environment before enabling live payments.</p>
    </div>
</div>
