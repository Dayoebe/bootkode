<div class="mx-auto max-w-7xl space-y-6 p-4 sm:p-6">
    <x-ui.page-header title="Mentor dashboard" description="Manage your mentees, sessions, profile, and mentoring activity." />
    <x-ui.flash />
    <nav aria-label="Mentor sections" class="flex max-w-full gap-4 overflow-x-auto border-b border-line">
        @foreach(['overview' => 'Overview', 'mentorships' => 'My mentees', 'sessions' => 'Sessions', 'code-reviews' => 'Code reviews', 'profile' => 'Profile', 'analytics' => 'Analytics'] as $tab => $label)
            <button type="button" wire:click="setActiveTab('{{ $tab }}')" aria-pressed="{{ $activeTab === $tab ? 'true' : 'false' }}" @class(['min-h-11 shrink-0 border-b-2 px-2 py-3 text-sm font-semibold', 'border-brand text-brand' => $activeTab === $tab, 'border-transparent text-content-muted' => $activeTab !== $tab])>{{ $label }}</button>
        @endforeach
    </nav>
    <div class="min-w-0" wire:loading.class="opacity-60" wire:target="setActiveTab">
        @switch($activeTab)
            @case('mentorships') @livewire('mentorship.partial.mentorships') @break
            @case('sessions') @livewire('mentorship.sessions', ['embedded' => true]) @break
            @case('code-reviews') @livewire('mentorship.code-reviews', ['embedded' => true]) @break
            @case('profile') @livewire('mentorship.partial.profile') @break
            @case('analytics') @livewire('mentorship.partial.analytics') @break
            @default @livewire('mentorship.partial.overview')
        @endswitch
    </div>
    <p role="status" class="text-sm text-content-muted" wire:loading.delay wire:target="setActiveTab">Updating mentor activity…</p>
</div>
