<div class="mx-auto max-w-7xl space-y-6 p-4 sm:p-6">
    <x-ui.page-header title="Marketplace" description="Browse resources, manage purchases, and follow your selling activity.">
        <x-slot:actions><x-secondary-button wire:click="refreshStats" wire:loading.attr="disabled">Refresh statistics</x-secondary-button></x-slot:actions>
    </x-ui.page-header>
    <x-ui.flash />
    <dl class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        @foreach(['Published items' => $stats['total_items'], 'My purchases' => $stats['my_orders'], 'My listings' => $stats['my_listings'], 'Paid vendor earnings' => '₦'.number_format($stats['total_earnings'], 2)] as $label => $value)
            <div class="rounded-lg border border-line bg-surface p-4"><dt class="text-sm text-content-muted">{{ $label }}</dt><dd class="mt-1 text-xl font-semibold text-content">{{ $value }}</dd></div>
        @endforeach
    </dl>
    @php
        $tabs = ['browse' => 'Browse', 'shopping' => 'Purchases and cart'];
        if(auth()->user()->canManageCourses()) $tabs += ['vendor' => 'My listings', 'business' => 'My business'];
        if(auth()->user()->hasAnyRole(['super_admin', 'academy_admin', 'content_editor'])) $tabs['content'] = 'Content and reviews';
        if(auth()->user()->hasAnyRole(['super_admin', 'academy_admin'])) $tabs += ['admin' => 'Administration', 'system' => 'Settings and analytics'];
    @endphp
    <nav aria-label="Marketplace sections" class="flex gap-4 overflow-x-auto border-b border-line">
        @foreach($tabs as $tab => $label)
            <button type="button" wire:click="setActiveTab('{{ $tab }}')" aria-pressed="{{ $activeTab === $tab ? 'true' : 'false' }}" @class(['min-h-11 shrink-0 border-b-2 px-2 py-3 text-sm font-semibold', 'border-brand text-brand' => $activeTab === $tab, 'border-transparent text-content-muted' => $activeTab !== $tab])>{{ $label }}</button>
        @endforeach
    </nav>
    <div class="min-w-0" wire:loading.class="opacity-60" wire:target="setActiveTab">
        @switch($activeTab)
            @case('shopping') @livewire('marketplace.partial.marketplace-shopping', key('marketplace-shopping')) @break
            @case('vendor') @livewire('marketplace.partial.marketplace-vendor', key('marketplace-vendor')) @break
            @case('business') @livewire('marketplace.partial.marketplace-business', key('marketplace-business')) @break
            @case('admin') @livewire('marketplace.partial.marketplace-admin', key('marketplace-admin')) @break
            @case('content') @livewire('marketplace.partial.marketplace-content', key('marketplace-content')) @break
            @case('system') @livewire('marketplace.partial.marketplace-system', key('marketplace-system')) @break
            @default @livewire('marketplace.partial.marketplace-browse', key('marketplace-browse'))
        @endswitch
    </div>
    <p wire:loading.delay wire:target="setActiveTab,refreshStats" role="status" class="text-sm text-content-muted">Updating marketplace…</p>
</div>
