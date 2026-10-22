<div class="min-h-screen bg-themed-primary">
    <div class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
        <x-ui.page-header title="Community" description="Discuss your learning, share projects, and join study groups and events." />
        <dl class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
            @foreach(['totalMembers' => 'Members', 'activeStudyGroups' => 'Study groups', 'activeChallenges' => 'Challenges', 'upcomingEvents' => 'Upcoming events'] as $key => $label)
                <div class="rounded-lg border border-line bg-surface p-4">
                    <dt class="text-sm text-content-muted">{{ $label }}</dt>
                    <dd class="mt-1 text-xl font-semibold text-content">{{ number_format($stats[$key]) }}</dd>
                </div>
            @endforeach
        </dl>
    </div>

    <!-- Tab Navigation -->
    <div class="bg-themed-secondary border-b border-themed-primary sticky top-0 z-30">
        <div class="px-4 sm:px-6 lg:px-8">
            <nav class="flex gap-1 overflow-x-auto">
                @php
                    $tabs = [
                        'forums' => ['icon' => 'fas fa-comments', 'label' => 'Forums', 'color' => 'blue'],
                        'study-groups' => ['icon' => 'fas fa-users', 'label' => 'Study Groups', 'color' => 'green'],
                        'code-challenges' => ['icon' => 'fas fa-code', 'label' => 'Challenges', 'color' => 'orange'],
                        'live-events' => ['icon' => 'fas fa-video', 'label' => 'Events', 'color' => 'purple'],
                        'feedback' => ['icon' => 'fas fa-message', 'label' => 'Feedback', 'color' => 'red'],
                    ];

                    if ($isAdmin) {
                        $tabs['moderation'] = ['icon' => 'fas fa-shield', 'label' => 'Moderation', 'color' => 'yellow'];
                    }
                @endphp

                @foreach($tabs as $tabKey => $tabConfig)
                    <button wire:click="setTab('{{ $tabKey }}')"
                            aria-pressed="{{ $activeTab === $tabKey ? 'true' : 'false' }}" class="group min-h-11 py-3 px-4 font-medium text-sm transition-all whitespace-nowrap {{ $activeTab === $tabKey ? 'border-b-2 border-brand text-brand' : 'text-themed-secondary hover:text-themed-primary border-b-2 border-transparent' }}">
                        <i class="{{ $tabConfig['icon'] }} mr-2"></i>{{ $tabConfig['label'] }}
                    </button>
                @endforeach
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="px-4 sm:px-6 lg:px-8 py-8">
        <x-ui.flash />

        @switch($activeTab)
            @case('forums')
                @include('livewire.community.tabs.forums')
                @break
            @case('study-groups')
                @include('livewire.community.tabs.study-groups')
                @break
            @case('code-challenges')
                @include('livewire.community.tabs.code-challenges')
                @break
            @case('live-events')
                @include('livewire.community.tabs.live-events')
                @break
            @case('feedback')
                @include('livewire.community.tabs.feedback')
                @break
            @case('moderation')
                @if($isAdmin)
                    @include('livewire.community.tabs.moderation')
                @else
                    <div class="text-center py-12 bg-themed-secondary border border-themed-primary rounded-lg">
                        <i class="fas fa-lock text-themed-secondary text-4xl mb-3 block"></i>
                        <h3 class="text-lg font-semibold text-themed-primary mb-1">Access Denied</h3>
                        <p class="text-themed-secondary">You don't have permission to access this section.</p>
                    </div>
                @endif
                @break
        @endswitch
    </div>

    <!-- Modals -->
    @include('livewire.community.modals.forum-modal')
    @include('livewire.community.modals.study-group-modal')
    @include('livewire.community.modals.code-challenge-modal')
    @include('livewire.community.modals.live-event-modal')
    @include('livewire.community.modals.feedback-modal')

    <p wire:loading.delay role="status" class="px-4 pb-4 text-sm text-content-muted">Updating community…</p>
</div>
