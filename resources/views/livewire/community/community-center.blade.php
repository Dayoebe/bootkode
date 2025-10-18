<div class="min-h-screen bg-themed-primary">
    <!-- Header -->
    <div class="bg-themed-secondary shadow-sm border-b border-themed-primary">
        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex justify-between items-center flex-wrap gap-4">
                <div class="flex items-center gap-4">
                    <div class="bg-gradient-to-br from-blue-500 to-indigo-600 p-3 rounded-xl shadow-lg">
                        <i class="fas fa-community text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-themed-primary">Community Hub</h1>
                        <p class="text-themed-secondary text-sm mt-1">Connect, learn & grow together</p>
                    </div>
                </div>

                <div class="hidden lg:grid grid-cols-4 gap-3">
                    <div class="bg-white/10 backdrop-blur border border-white/20 rounded-lg p-3 text-center">
                        <div class="text-2xl font-bold text-blue-400">{{ $stats['totalMembers'] }}</div>
                        <div class="text-xs text-themed-secondary mt-1">Members</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur border border-white/20 rounded-lg p-3 text-center">
                        <div class="text-2xl font-bold text-green-400">{{ $stats['activeStudyGroups'] }}</div>
                        <div class="text-xs text-themed-secondary mt-1">Groups</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur border border-white/20 rounded-lg p-3 text-center">
                        <div class="text-2xl font-bold text-orange-400">{{ $stats['activeChallenges'] }}</div>
                        <div class="text-xs text-themed-secondary mt-1">Challenges</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur border border-white/20 rounded-lg p-3 text-center">
                        <div class="text-2xl font-bold text-indigo-400">{{ $stats['upcomingEvents'] }}</div>
                        <div class="text-xs text-themed-secondary mt-1">Events</div>
                    </div>
                </div>
            </div>
        </div>
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
                            class="group py-4 px-4 font-medium text-sm transition-all whitespace-nowrap {{ $activeTab === $tabKey ? 'border-b-2 border-' . $tabConfig['color'] . '-500 text-' . $tabConfig['color'] . '-600' : 'text-themed-secondary hover:text-themed-primary border-b-2 border-transparent' }}">
                        <i class="{{ $tabConfig['icon'] }} mr-2"></i>{{ $tabConfig['label'] }}
                    </button>
                @endforeach
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="px-4 sm:px-6 lg:px-8 py-8">
        <!-- Flash Messages -->
        @if(session('message'))
            <div class="mb-6 p-4 bg-green-100/20 border border-green-500/50 rounded-lg text-green-600 flex items-center gap-2">
                <i class="fas fa-check-circle"></i>
                {{ session('message') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-100/20 border border-red-500/50 rounded-lg text-red-600 flex items-center gap-2">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
            </div>
        @endif

        <!-- Tab Content -->
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

    <!-- Loading Overlay -->
    <div wire:loading class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
        <div class="bg-themed-secondary rounded-lg p-6 shadow-xl">
            <div class="flex items-center gap-3">
                <div class="animate-spin h-6 w-6 border-3 border-blue-600 border-t-transparent rounded-full"></div>
                <span class="text-themed-primary font-medium">Loading...</span>
            </div>
        </div>
    </div>
    
    <style>
        @keyframes pulse-soft {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }

        .animate-pulse-soft {
            animation: pulse-soft 2s ease-in-out infinite;
        }

        @keyframes slide-in {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-slide-in {
            animation: slide-in 0.3s ease-out;
        }
    </style>
</div>