<div class="mx-auto max-w-7xl space-y-6 p-4 sm:p-6">
    <x-ui.page-header title="Institution portal" description="Manage partnerships, licenses, cohorts, and institutional learning.">
        <x-slot:actions><x-secondary-button wire:click="refreshStats" wire:loading.attr="disabled">Refresh statistics</x-secondary-button></x-slot:actions>
    </x-ui.page-header>
    <x-ui.flash />
    <dl class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        @foreach(['Institutions' => $stats['total_institutions'], 'Active institutions' => $stats['active_institutions'], 'Active members' => $stats['total_users'], 'Active cohorts' => $stats['active_cohorts']] as $label => $value)
            <div class="rounded-lg border border-line bg-surface p-4"><dt class="text-sm text-content-muted">{{ $label }}</dt><dd class="mt-1 text-xl font-semibold text-content">{{ number_format($value) }}</dd></div>
        @endforeach
    </dl>
    <div class="grid gap-4 sm:grid-cols-2">
        <div><x-input-label for="institution-period" value="Reporting period" /><select id="institution-period" wire:model.live="selectedPeriod" class="mt-1 min-h-11 w-full rounded-lg border-line bg-surface text-content"><option value="7">Last 7 days</option><option value="30">Last 30 days</option><option value="90">Last 90 days</option><option value="365">Last year</option></select></div>
        <div><x-input-label for="institution-type" value="Institution type" /><select id="institution-type" wire:model.live="selectedInstitutionType" class="mt-1 min-h-11 w-full rounded-lg border-line bg-surface text-content"><option value="all">All types</option>@foreach($institutionTypes as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach</select></div>
    </div>
    <nav aria-label="Institution sections" class="flex gap-4 overflow-x-auto border-b border-line">
        @foreach(['overview' => 'Overview', 'partners' => 'Partners', 'licenses' => 'Licenses', 'cohorts' => 'Cohorts', 'bulk-enrollment' => 'Bulk enrollment', 'analytics' => 'Analytics', 'whitelabel' => 'Branding'] as $tab => $label)
            <button type="button" wire:click="setActiveTab('{{ $tab }}')" aria-pressed="{{ $activeTab === $tab ? 'true' : 'false' }}" @class(['min-h-11 shrink-0 border-b-2 px-2 py-3 text-sm font-semibold', 'border-brand text-brand' => $activeTab === $tab, 'border-transparent text-content-muted' => $activeTab !== $tab])>{{ $label }}</button>
        @endforeach
    </nav>
    <div class="min-w-0" wire:loading.class="opacity-60" wire:target="setActiveTab">
        @switch($activeTab)
            @case('partners') @livewire('institution.partner-institutions', key('institution-partners')) @break
            @case('licenses') @livewire('institution.license-management', key('institution-licenses')) @break
            @case('cohorts') @livewire('institution.cohort-management', key('institution-cohorts')) @break
            @case('bulk-enrollment') @livewire('institution.bulk-enrollment', key('institution-bulk')) @break
            @case('analytics') @livewire('institution.institution-analytics', key('institution-analytics')) @break
            @case('whitelabel') @livewire('institution.whitelabel-settings', key('institution-branding')) @break
            @default @include('livewire.institution.partials.overview')
        @endswitch
    </div>
    <p wire:loading.delay wire:target="setActiveTab,refreshStats" role="status" class="text-sm text-content-muted">Updating institutions…</p>
</div>
