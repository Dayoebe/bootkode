<div class="mx-auto max-w-7xl space-y-6 p-4 sm:p-6">
    <x-ui.page-header title="Learning rewards" description="Review your recorded progress, rewards and achievements.">
        <x-slot:actions><x-secondary-button wire:click="refreshData" wire:loading.attr="disabled">Refresh rewards</x-secondary-button></x-slot:actions>
    </x-ui.page-header>
    <x-ui.flash :messages="$feedback" />
    <nav aria-label="Learning rewards" class="flex max-w-full gap-2 overflow-x-auto pb-2">
        @foreach(['overview' => 'Overview', 'badges' => 'Badges', 'games' => 'Games', 'leaderboard' => 'Leaderboard', 'store' => 'Rewards store'] as $tab => $label)
            <button type="button" wire:click="setActiveTab('{{ $tab }}')" aria-current="{{ $activeTab === $tab ? 'page' : 'false' }}" @class(['bk-button shrink-0', 'bk-button-primary' => $activeTab === $tab, 'bk-button-secondary' => $activeTab !== $tab])>{{ $label }}</button>
        @endforeach
    </nav>
    <p class="text-sm text-content-muted">Points, coins and gems are learning rewards. They are separate from your NGN wallet.</p>
    @if($activeTab === 'overview')
        <dl class="grid grid-cols-2 gap-4 lg:grid-cols-4">
            @foreach(['level' => 'Level', 'total_points' => 'Points', 'coins' => 'Coins', 'gems' => 'Gems'] as $key => $label)
                <div class="bk-panel p-4"><dt class="text-sm text-content-muted">{{ $label }}</dt><dd class="mt-2 text-2xl font-semibold text-content">{{ number_format($userStats[$key]) }}</dd></div>
            @endforeach
        </dl>
        <x-ui.panel title="Daily reward"><p class="mb-4 text-content-muted">Claim once each calendar day: 10 points, 2 coins and 10 XP. Learning streaks use verified learning activity.</p><x-primary-button wire:click="claimDailyReward" wire:loading.attr="disabled" wire:target="claimDailyReward">Claim daily reward</x-primary-button></x-ui.panel>
        <x-ui.panel title="Recent reward activity">
            <ul class="divide-y divide-line">@forelse($recentTransactions as $transaction)<li class="flex flex-wrap justify-between gap-2 py-3"><span class="break-words text-content">{{ $transaction->description }}</span><span class="text-sm text-content-muted">{{ $transaction->created_at->format('M j, Y H:i') }}</span></li>@empty<li class="py-4 text-content-muted">No reward activity has been recorded yet.</li>@endforelse</ul>
        </x-ui.panel>
    @elseif($activeTab === 'badges')
        <x-ui.panel title="Your awarded badges"><div class="grid gap-4 md:grid-cols-2">@forelse($badges as $badge)<article class="rounded-lg border border-line p-4"><h2 class="font-semibold text-content">{{ $badge->badge_name }}</h2><p class="mt-2 text-content-muted">{{ $badge->badge_description }}</p><p class="mt-2 text-sm text-content-muted">Awarded {{ $badge->created_at->format('M j, Y') }}</p></article>@empty<x-ui.empty-state title="No badges awarded yet" description="Awarded badges will appear here when recorded by the platform." />@endforelse</div></x-ui.panel>
    @elseif($activeTab === 'games')
        <x-ui.empty-state title="No games are published" description="Game play and score rewards are unavailable until a server-verified game is released. Existing scores are retained." />
    @elseif($activeTab === 'leaderboard')
        <x-ui.panel title="Leaderboard participation"><form wire:submit="saveLeaderboardPreference" class="space-y-4"><label class="flex min-h-11 items-start gap-3 text-content"><input type="checkbox" wire:model="publicLeaderboard" class="mt-1 rounded" /><span>Show my name, points and level on the public leaderboard. My profile must also be public.</span></label><x-primary-button wire:loading.attr="disabled">Save participation</x-primary-button></form></x-ui.panel>
        <x-ui.panel title="Public learning leaderboard"><p class="mb-4 text-sm text-content-muted">Only active accounts that opt in and have a public profile appear. This ranking uses recorded points.</p><ol class="divide-y divide-line">@forelse($leaderboards['overall'] as $entry)<li class="flex flex-wrap justify-between gap-3 py-4"><span class="min-w-0 break-words font-semibold text-content">{{ $entry['rank'] }}. {{ $entry['name'] }}</span><span class="text-content-muted">{{ number_format($entry['points']) }} points · Level {{ $entry['level'] }}</span></li>@empty<li><x-ui.empty-state title="No public participants yet" description="Private profiles remain excluded." /></li>@endforelse</ol></x-ui.panel>
    @else
        <x-ui.panel title="Available rewards"><p class="mb-4 text-content-muted">Only configured energy rewards can be redeemed here. Other reward types remain unavailable and cannot spend your currency.</p><div class="grid gap-4 md:grid-cols-2">@forelse($storeItems as $item)<article class="min-w-0 rounded-lg border border-line p-4"><h2 class="break-words font-semibold text-content">{{ $item->name }}</h2><p class="mt-2 break-words text-content-muted">{{ $item->description }}</p><p class="my-4 text-sm text-content-muted">{{ $item->cost_coins }} coins · {{ $item->cost_gems }} gems · Level {{ $item->required_level }}</p><x-secondary-button wire:click="purchaseItem({{ $item->id }})" wire:loading.attr="disabled">Check and redeem</x-secondary-button></article>@empty<x-ui.empty-state title="No rewards available" description="Published reward definitions will appear here." />@endforelse</div></x-ui.panel>
        <x-ui.panel title="Your reward history"><ul class="divide-y divide-line">@forelse($purchases as $purchase)<li class="py-3 text-content">{{ $purchase->item?->name ?? 'Historical reward' }} <span class="text-sm text-content-muted">· {{ $purchase->created_at->format('M j, Y') }}</span></li>@empty<li class="text-content-muted">No rewards have been redeemed.</li>@endforelse</ul></x-ui.panel>
    @endif
</div>
