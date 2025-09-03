<div x-data="gamificationDashboard()" 
     x-init="init()"
     class="min-h-screen bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900 text-white relative overflow-hidden">
    
    <!-- Animated Background Elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-10 left-10 w-20 h-20 bg-yellow-400 rounded-full opacity-20 animate-bounce" style="animation-delay: 0s;"></div>
        <div class="absolute top-32 right-20 w-16 h-16 bg-pink-400 rounded-full opacity-20 animate-bounce" style="animation-delay: 1s;"></div>
        <div class="absolute bottom-20 left-1/4 w-12 h-12 bg-green-400 rounded-full opacity-20 animate-bounce" style="animation-delay: 2s;"></div>
        <div class="absolute bottom-40 right-1/3 w-14 h-14 bg-blue-400 rounded-full opacity-20 animate-bounce" style="animation-delay: 0.5s;"></div>
    </div>

    <!-- Floating Particles -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="particle" style="left: 10%; animation-delay: 0s;"></div>
        <div class="particle" style="left: 20%; animation-delay: 2s;"></div>
        <div class="particle" style="left: 30%; animation-delay: 4s;"></div>
        <div class="particle" style="left: 40%; animation-delay: 6s;"></div>
        <div class="particle" style="left: 50%; animation-delay: 8s;"></div>
        <div class="particle" style="left: 60%; animation-delay: 10s;"></div>
        <div class="particle" style="left: 70%; animation-delay: 12s;"></div>
        <div class="particle" style="left: 80%; animation-delay: 14s;"></div>
        <div class="particle" style="left: 90%; animation-delay: 16s;"></div>
    </div>

    <!-- Main Container -->
    <div class="relative z-10 p-6">
        <!-- Header Stats Bar -->
        <div class="mb-8 animate__animated animate__fadeInDown">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-4xl font-bold bg-gradient-to-r from-yellow-400 to-orange-500 bg-clip-text text-transparent">
                    <i class="fas fa-gamepad mr-3"></i>Gaming Hub
                </h1>
                <button wire:click="refreshData" 
                        class="px-4 py-2 bg-purple-600 hover:bg-purple-700 rounded-lg transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>

            <!-- User Stats Cards -->
            <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6">
                <!-- Level Card -->
                <div class="bg-gradient-to-r from-yellow-500 to-orange-600 rounded-xl p-4 transform hover:scale-105 transition-all duration-300 shadow-xl">
                    <div class="text-center">
                        <i class="fas fa-trophy text-2xl mb-2"></i>
                        <div class="text-xs opacity-80">LEVEL</div>
                        <div class="text-2xl font-bold">{{ $userStats['level'] ?? 1 }}</div>
                    </div>
                </div>

                <!-- Points Card -->
                <div class="bg-gradient-to-r from-blue-500 to-cyan-600 rounded-xl p-4 transform hover:scale-105 transition-all duration-300 shadow-xl">
                    <div class="text-center">
                        <i class="fas fa-star text-2xl mb-2"></i>
                        <div class="text-xs opacity-80">POINTS</div>
                        <div class="text-lg font-bold">{{ number_format($userStats['total_points'] ?? 0) }}</div>
                    </div>
                </div>

                <!-- Coins Card -->
                <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl p-4 transform hover:scale-105 transition-all duration-300 shadow-xl">
                    <div class="text-center">
                        <i class="fas fa-coins text-2xl mb-2"></i>
                        <div class="text-xs opacity-80">COINS</div>
                        <div class="text-lg font-bold">{{ number_format($userStats['coins'] ?? 0) }}</div>
                    </div>
                </div>

                <!-- Gems Card -->
                <div class="bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl p-4 transform hover:scale-105 transition-all duration-300 shadow-xl">
                    <div class="text-center">
                        <i class="fas fa-gem text-2xl mb-2"></i>
                        <div class="text-xs opacity-80">GEMS</div>
                        <div class="text-lg font-bold">{{ $userStats['gems'] ?? 0 }}</div>
                    </div>
                </div>

                <!-- Energy Card -->
                <div class="bg-gradient-to-r from-red-500 to-rose-600 rounded-xl p-4 transform hover:scale-105 transition-all duration-300 shadow-xl">
                    <div class="text-center">
                        <i class="fas fa-battery-three-quarters text-2xl mb-2"></i>
                        <div class="text-xs opacity-80">ENERGY</div>
                        <div class="text-lg font-bold">{{ $userStats['energy'] ?? 100 }}/100</div>
                    </div>
                </div>

                <!-- Streak Card -->
                <div class="bg-gradient-to-r from-orange-500 to-red-600 rounded-xl p-4 transform hover:scale-105 transition-all duration-300 shadow-xl">
                    <div class="text-center">
                        <i class="fas fa-fire text-2xl mb-2"></i>
                        <div class="text-xs opacity-80">STREAK</div>
                        <div class="text-lg font-bold">{{ $userStats['current_streak'] ?? 0 }}</div>
                    </div>
                </div>
            </div>

            <!-- XP Progress Bar -->
            <div class="bg-black/20 rounded-xl p-4 backdrop-blur-sm">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium">Level {{ $userStats['level'] ?? 1 }} Progress</span>
                    <span class="text-sm">{{ $userStats['experience_points'] ?? 0 }}/{{ $userStats['experience_to_next_level'] ?? 100 }} XP</span>
                </div>
                <div class="w-full bg-gray-700 rounded-full h-3 overflow-hidden">
                    <div class="bg-gradient-to-r from-yellow-400 to-orange-500 h-full rounded-full transition-all duration-1000 ease-out relative overflow-hidden"
                         style="width: {{ $userStats['progress_percentage'] ?? 0 }}%">
                        <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex space-x-2 mb-8 bg-black/20 rounded-xl p-2 backdrop-blur-sm">
            <button wire:click="setActiveTab('overview')" 
                    class="px-6 py-3 rounded-lg transition-all duration-300 font-medium {{ $activeTab === 'overview' ? 'bg-purple-600 text-white' : 'text-gray-300 hover:text-white' }}">
                <i class="fas fa-home mr-2"></i>Overview
            </button>
            <button wire:click="setActiveTab('games')" 
                    class="px-6 py-3 rounded-lg transition-all duration-300 font-medium {{ $activeTab === 'games' ? 'bg-purple-600 text-white' : 'text-gray-300 hover:text-white' }}">
                <i class="fas fa-gamepad mr-2"></i>Games
            </button>
            <button wire:click="setActiveTab('badges')" 
                    class="px-6 py-3 rounded-lg transition-all duration-300 font-medium {{ $activeTab === 'badges' ? 'bg-purple-600 text-white' : 'text-gray-300 hover:text-white' }}">
                <i class="fas fa-medal mr-2"></i>Badges
            </button>
            <button wire:click="setActiveTab('leaderboard')" 
                    class="px-6 py-3 rounded-lg transition-all duration-300 font-medium {{ $activeTab === 'leaderboard' ? 'bg-purple-600 text-white' : 'text-gray-300 hover:text-white' }}">
                <i class="fas fa-trophy mr-2"></i>Leaderboard
            </button>
            <button wire:click="setActiveTab('store')" 
                    class="px-6 py-3 rounded-lg transition-all duration-300 font-medium {{ $activeTab === 'store' ? 'bg-purple-600 text-white' : 'text-gray-300 hover:text-white' }}">
                <i class="fas fa-store mr-2"></i>Store
            </button>
        </div>

        <!-- Tab Content -->
        <div class="space-y-6">
            <!-- Overview Tab -->
            @if($activeTab === 'overview')
            <div class="animate__animated animate__fadeIn">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Daily Quests -->
                    <div class="lg:col-span-2 bg-black/30 rounded-xl p-6 backdrop-blur-sm">
                        <h3 class="text-xl font-bold mb-4 text-yellow-400">
                            <i class="fas fa-tasks mr-2"></i>Daily Quests
                        </h3>
                        @if(!empty($userStats['daily_quests']))
                            <div class="space-y-3">
                                @foreach($userStats['daily_quests'] as $quest)
                                <div class="bg-white/10 rounded-lg p-4 {{ isset($quest['completed']) && $quest['completed'] ? 'border-l-4 border-green-500' : '' }}">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <h4 class="font-semibold {{ isset($quest['completed']) && $quest['completed'] ? 'line-through text-green-400' : '' }}">
                                                {{ $quest['title'] }}
                                            </h4>
                                            <p class="text-sm text-gray-300">{{ $quest['description'] }}</p>
                                            <div class="mt-2">
                                                <div class="flex items-center space-x-2 text-xs">
                                                    <span><i class="fas fa-coins text-yellow-400"></i> {{ $quest['reward_coins'] }}</span>
                                                    <span><i class="fas fa-star text-blue-400"></i> {{ $quest['reward_xp'] }} XP</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            @if(isset($quest['completed']) && $quest['completed'])
                                                <i class="fas fa-check-circle text-green-400 text-2xl"></i>
                                            @else
                                                <div class="text-right">
                                                    <div class="text-lg font-bold">{{ $quest['progress'] }}/{{ $quest['target'] }}</div>
                                                    <div class="w-16 bg-gray-600 rounded-full h-2">
                                                        <div class="bg-gradient-to-r from-blue-400 to-purple-500 h-2 rounded-full transition-all duration-500"
                                                             style="width: {{ ($quest['progress'] / $quest['target']) * 100 }}%"></div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-calendar-day text-4xl text-gray-400 mb-4"></i>
                                <p class="text-gray-400">No daily quests available. Check back tomorrow!</p>
                            </div>
                        @endif
                    </div>

                    <!-- Recent Activity & Quick Actions -->
                    <div class="space-y-6">
                        <!-- Recent Badges -->
                        <div class="bg-black/30 rounded-xl p-6 backdrop-blur-sm">
                            <h3 class="text-xl font-bold mb-4 text-purple-400">
                                <i class="fas fa-medal mr-2"></i>Recent Badges
                            </h3>
                            @if(!empty($userStats['recent_badges']) && count($userStats['recent_badges']) > 0)
                                <div class="space-y-3">
                                    @foreach($userStats['recent_badges'] as $badge)
                                    <div class="flex items-center space-x-3 p-3 bg-white/10 rounded-lg">
                                        <div class="text-2xl" style="color: {{ $badge->badge_color ?? '#64748B' }}">
                                            <i class="{{ $badge->badge_icon ?? 'fas fa-medal' }}"></i>
                                        </div>
                                        <div>
                                            <div class="font-semibold">{{ $badge->badge_name ?? 'Achievement' }}</div>
                                            <div class="text-xs text-gray-300">{{ $badge->badge_description ?? 'Earned an achievement' }}</div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-gray-400 text-center py-4">No badges earned yet. Start learning to unlock achievements!</p>
                            @endif
                        </div>

                        <!-- Quick Actions -->
                        <div class="bg-black/30 rounded-xl p-6 backdrop-blur-sm">
                            <h3 class="text-xl font-bold mb-4 text-green-400">
                                <i class="fas fa-bolt mr-2"></i>Quick Actions
                            </h3>
                            <div class="space-y-3">
                                <button wire:click="claimDailyReward" 
                                        class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 px-4 py-3 rounded-lg transition-all duration-300 transform hover:scale-105 font-medium">
                                    <i class="fas fa-gift mr-2"></i>Claim Daily Reward
                                </button>
                                <button wire:click="setActiveTab('games')"
                                        class="w-full bg-gradient-to-r from-blue-500 to-cyan-600 hover:from-blue-600 hover:to-cyan-700 px-4 py-3 rounded-lg transition-all duration-300 transform hover:scale-105 font-medium">
                                    <i class="fas fa-play mr-2"></i>Play Games
                                </button>
                                <button wire:click="setActiveTab('store')"
                                        class="w-full bg-gradient-to-r from-purple-500 to-pink-600 hover:from-purple-600 hover:to-pink-700 px-4 py-3 rounded-lg transition-all duration-300 transform hover:scale-105 font-medium">
                                    <i class="fas fa-shopping-cart mr-2"></i>Visit Store
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Games Tab -->
            @if($activeTab === 'games')
            <div class="animate__animated animate__fadeIn">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @forelse($availableGames as $game)
                    <div class="game-card bg-black/30 rounded-xl p-6 backdrop-blur-sm border border-white/10 hover:border-white/30 transition-all duration-300 transform hover:scale-105 hover:shadow-2xl">
                        <div class="text-center">
                            <div class="text-4xl mb-4">
                                <i class="{{ $game['icon'] }} text-yellow-400"></i>
                            </div>
                            <h3 class="text-xl font-bold mb-2">{{ $game['name'] }}</h3>
                            <p class="text-gray-300 text-sm mb-4">{{ $game['description'] }}</p>
                            
                            <!-- Game Stats -->
                            <div class="space-y-2 mb-4 text-xs">
                                <div class="flex justify-between">
                                    <span>Energy Cost:</span>
                                    <span class="text-red-400"><i class="fas fa-battery-half"></i> {{ $game['energy_cost'] }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Max Reward:</span>
                                    <span class="text-yellow-400"><i class="fas fa-coins"></i> {{ $game['max_score_reward'] }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Difficulty:</span>
                                    <span class="text-purple-400">{{ $game['difficulty'] }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Time:</span>
                                    <span class="text-blue-400">{{ $game['estimated_time'] }}</span>
                                </div>
                            </div>

                            <!-- High Score -->
                            @php
                                $highScore = auth()->user()->gamificationData?->getGameScore($game['id']) ?? 0;
                            @endphp
                            @if($highScore > 0)
                                <div class="bg-yellow-500/20 rounded-lg p-2 mb-4">
                                    <div class="text-xs text-yellow-400">HIGH SCORE</div>
                                    <div class="text-lg font-bold text-yellow-300">{{ number_format($highScore) }}</div>
                                </div>
                            @endif

                            <button wire:click="playGame('{{ $game['id'] }}')" 
                                    class="w-full bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 px-4 py-3 rounded-lg transition-all duration-300 transform hover:scale-105 font-medium shadow-lg">
                                <i class="fas fa-play mr-2"></i>Play Now
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full text-center py-12">
                        <i class="fas fa-gamepad text-6xl text-gray-400 mb-4"></i>
                        <h3 class="text-xl font-bold text-gray-400 mb-2">No games available!</h3>
                        <p class="text-gray-500">Check back later for new games.</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif

            <!-- Badges Tab -->
            @if($activeTab === 'badges')
            <div class="animate__animated animate__fadeIn">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @forelse(auth()->user()->badges()->latest()->get() as $badge)
                    <div class="badge-card bg-black/30 rounded-xl p-6 backdrop-blur-sm border-2 transition-all duration-300 transform hover:scale-105"
                         style="border-color: {{ $badge->rarity_color ?? '#64748B' }}">
                        <div class="text-center">
                            <div class="text-4xl mb-3" style="color: {{ $badge->badge_color ?? '#64748B' }}">
                                <i class="{{ $badge->badge_icon ?? 'fas fa-medal' }}"></i>
                            </div>
                            <h3 class="text-lg font-bold mb-2">{{ $badge->badge_name }}</h3>
                            <p class="text-gray-300 text-sm mb-3">{{ $badge->badge_description }}</p>
                            
                            <!-- Rarity Badge -->
                            <div class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase mb-2"
                                 style="background-color: {{ $badge->rarity_color ?? '#64748B' }}20; color: {{ $badge->rarity_color ?? '#64748B' }};">
                                {{ $badge->rarity ?? 'common' }}
                            </div>
                            
                            <div class="text-xs text-gray-400">
                                Earned {{ $badge->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full text-center py-12">
                        <i class="fas fa-medal text-6xl text-gray-400 mb-4"></i>
                        <h3 class="text-xl font-bold text-gray-400 mb-2">No badges earned yet!</h3>
                        <p class="text-gray-500">Complete activities to unlock achievements.</p>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif

            <!-- Leaderboard Tab -->
            @if($activeTab === 'leaderboard')
            <div class="animate__animated animate__fadeIn">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Overall Leaderboard -->
                    <div class="bg-black/30 rounded-xl p-6 backdrop-blur-sm">
                        <h3 class="text-xl font-bold mb-4 text-yellow-400">
                            <i class="fas fa-crown mr-2"></i>Top Players
                        </h3>
                        <div class="space-y-3">
                            @forelse($leaderboards['overall'] ?? [] as $index => $player)
                            <div class="flex items-center space-x-4 p-3 bg-white/10 rounded-lg {{ $player->user_id === auth()->id() ? 'border-l-4 border-yellow-400' : '' }}">
                                <div class="text-2xl font-bold {{ $index < 3 ? 'text-yellow-400' : 'text-gray-400' }}">
                                    #{{ $index + 1 }}
                                </div>
                                <div class="flex-1">
                                    <div class="font-semibold">{{ $player->user->name }}</div>
                                    <div class="text-sm text-gray-300">Level {{ $player->level }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold text-blue-400">{{ number_format($player->total_points) }}</div>
                                    <div class="text-xs text-gray-400">points</div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-8">
                                <i class="fas fa-trophy text-4xl text-gray-400 mb-4"></i>
                                <p class="text-gray-400">No leaderboard data available yet.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Weekly Leaderboard -->
                    <div class="bg-black/30 rounded-xl p-6 backdrop-blur-sm">
                        <h3 class="text-xl font-bold mb-4 text-green-400">
                            <i class="fas fa-calendar-week mr-2"></i>This Week
                        </h3>
                        <div class="space-y-3">
                            @forelse($leaderboards['weekly'] ?? [] as $index => $player)
                            <div class="flex items-center space-x-4 p-3 bg-white/10 rounded-lg">
                                <div class="text-xl font-bold text-green-400">#{{ $index + 1 }}</div>
                                <div class="flex-1">
                                    <div class="font-semibold">{{ $player->user->name ?? 'Anonymous' }}</div>
                                </div>
                                <div class="text-right">
                                    <div class="font-bold text-green-400">{{ number_format($player->weekly_points ?? 0) }}</div>
                                    <div class="text-xs text-gray-400">weekly pts</div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-8">
                                <i class="fas fa-calendar-week text-4xl text-gray-400 mb-4"></i>
                                <p class="text-gray-400">No weekly data available yet.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Store Tab -->
            @if($activeTab === 'store')
            <div class="animate__animated animate__fadeIn">
                @forelse($storeItems as $itemType => $items)
                <div class="mb-8">
                    <h3 class="text-2xl font-bold mb-4 capitalize text-purple-400">
                        <i class="fas fa-{{ $itemType === 'avatar' ? 'user' : ($itemType === 'theme' ? 'palette' : ($itemType === 'boost' ? 'rocket' : 'star')) }} mr-2"></i>
                        {{ str_replace('_', ' ', $itemType) }}s
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach($items as $item)
                        <div class="store-item bg-black/30 rounded-xl p-6 backdrop-blur-sm border border-white/10 hover:border-purple-400 transition-all duration-300 transform hover:scale-105">
                            <div class="text-center">
                                <div class="text-3xl mb-3 text-purple-400">
                                    <i class="{{ $item->icon }}"></i>
                                </div>
                                <h4 class="text-lg font-bold mb-2">{{ $item->name }}</h4>
                                <p class="text-gray-300 text-sm mb-4">{{ $item->description }}</p>
                                
                                <!-- Requirements -->
                                @if($item->required_level > 1)
                                <div class="text-xs text-yellow-400 mb-2">
                                    <i class="fas fa-lock mr-1"></i>Requires Level {{ $item->required_level }}
                                </div>
                                @endif

                                <!-- Price -->
                                <div class="flex justify-center space-x-4 mb-4">
                                    @if($item->cost_coins > 0)
                                    <span class="text-yellow-400">
                                        <i class="fas fa-coins"></i> {{ number_format($item->cost_coins) }}
                                    </span>
                                    @endif
                                    @if($item->cost_gems > 0)
                                    <span class="text-purple-400">
                                        <i class="fas fa-gem"></i> {{ $item->cost_gems }}
                                    </span>
                                    @endif
                                </div>

                                @php
                                    $canPurchase = $item->canUserPurchase(auth()->user());
                                    $alreadyOwned = auth()->user()->storePurchases()->where('reward_store_item_id', $item->id)->exists();
                                @endphp

                                @if($alreadyOwned)
                                    <button class="w-full bg-green-600 px-4 py-2 rounded-lg font-medium cursor-not-allowed" disabled>
                                        <i class="fas fa-check mr-2"></i>Owned
                                    </button>
                                @elseif($canPurchase)
                                    <button wire:click="purchaseItem({{ $item->id }})" 
                                            class="w-full bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 px-4 py-2 rounded-lg transition-all duration-300 transform hover:scale-105 font-medium">
                                        <i class="fas fa-shopping-cart mr-2"></i>Purchase
                                    </button>
                                @else
                                    <button class="w-full bg-gray-600 px-4 py-2 rounded-lg font-medium cursor-not-allowed" disabled>
                                        <i class="fas fa-lock mr-2"></i>Locked
                                    </button>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @empty
                <div class="text-center py-12">
                    <i class="fas fa-store text-6xl text-gray-400 mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-400 mb-2">Store is empty!</h3>
                    <p class="text-gray-500">Check back later for new items.</p>
                </div>
                @endforelse
            </div>
            @endif
        </div>
    </div>

    <!-- Notification Modal -->
    @if($showNotification)
    <div class="fixed inset-0 bg-black/50 flex items-center justify-center z-50" 
         x-transition:enter="animate__animated animate__fadeIn animate__faster"
         x-transition:leave="animate__animated animate__fadeOut animate__faster">
        <div class="bg-gradient-to-br from-purple-900 to-blue-900 rounded-2xl p-8 max-w-md mx-4 border border-purple-400 shadow-2xl">
            <div class="text-center">
                <div class="text-6xl mb-4">
                    @if(isset($notificationData['type']) && $notificationData['type'] === 'success')
                        <i class="fas fa-trophy text-yellow-400"></i>
                    @elseif(isset($notificationData['type']) && $notificationData['type'] === 'error')
                        <i class="fas fa-exclamation-triangle text-red-400"></i>
                    @else
                        <i class="fas fa-info-circle text-blue-400"></i>
                    @endif
                </div>
                <h3 class="text-2xl font-bold mb-2">{{ $notificationData['title'] ?? 'Notification' }}</h3>
                <p class="text-gray-300 mb-6">{{ $notificationData['message'] ?? 'Something happened!' }}</p>
                
                <!-- Rewards Display -->
                @if(isset($notificationData['rewards']))
                    <div class="bg-black/30 rounded-lg p-4 mb-6">
                        <h4 class="font-bold mb-3 text-yellow-400">Rewards Earned!</h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            @if(isset($notificationData['rewards']['coins_gained']))
                            <div>
                                <i class="fas fa-coins text-yellow-400"></i>
                                <span>+{{ $notificationData['rewards']['coins_gained'] }} Coins</span>
                            </div>
                            @endif
                            @if(isset($notificationData['rewards']['xp_gained']))
                            <div>
                                <i class="fas fa-star text-blue-400"></i>
                                <span>+{{ $notificationData['rewards']['xp_gained'] }} XP</span>
                            </div>
                            @endif
                        </div>
                        @if(isset($notificationData['rewards']['level_up']) && $notificationData['rewards']['level_up'])
                        <div class="mt-3 p-2 bg-yellow-500/20 rounded text-yellow-300">
                            <i class="fas fa-level-up-alt"></i> LEVEL UP! You're now level {{ $notificationData['rewards']['new_level'] ?? 'N/A' }}!
                        </div>
                        @endif
                    </div>
                @endif
                
                <button wire:click="hideNotification" 
                        class="bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 px-8 py-3 rounded-lg transition-all duration-300 transform hover:scale-105 font-bold">
                    Awesome!
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- CSS Styles -->
    <style>
        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: #FCD34D;
            border-radius: 50%;
            animation: float 20s infinite linear;
        }

        @keyframes float {
            0% {
                transform: translateY(100vh) scale(0);
                opacity: 1;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100px) scale(1);
                opacity: 0;
            }
        }

        .game-card {
            background: linear-gradient(135deg, rgba(147, 51, 234, 0.1) 0%, rgba(79, 70, 229, 0.1) 100%);
        }

        .badge-card {
            background: linear-gradient(135deg, rgba(168, 85, 247, 0.1) 0%, rgba(236, 72, 153, 0.1) 100%);
        }

        .store-item {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(147, 51, 234, 0.1) 100%);
        }
    </style>

    <!-- Alpine.js Component -->
    <script>
        function gamificationDashboard() {
            return {
                init() {
                    // Auto-refresh energy every minute
                    setInterval(() => {
                        if (document.visibilityState === 'visible') {
                            @this.call('loadData');
                        }
                    }, 60000);

                    // Add sparkle effect on hover for interactive elements
                    this.addHoverEffects();
                },

                addHoverEffects() {
                    setTimeout(() => {
                        const interactiveElements = document.querySelectorAll('.game-card, .badge-card, .store-item');
                        
                        interactiveElements.forEach(element => {
                            element.addEventListener('mouseenter', function() {
                                this.style.boxShadow = '0 0 30px rgba(147, 51, 234, 0.6)';
                            });
                            
                            element.addEventListener('mouseleave', function() {
                                this.style.boxShadow = '';
                            });
                        });
                    }, 100);
                }
            }
        }
    </script>
</div>