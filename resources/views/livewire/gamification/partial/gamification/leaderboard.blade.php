@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900 text-white">
    <div class="container mx-auto px-6 py-8">
        <div class="mb-8">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-yellow-400 to-orange-500 bg-clip-text text-transparent">
                <i class="fas fa-trophy mr-3"></i>Leaderboards
            </h1>
            <p class="text-gray-300 mt-2">See how you rank among other learners</p>
        </div>

        <!-- Leaderboard Type Tabs -->
        <div class="flex space-x-2 mb-8 bg-black/20 rounded-xl p-2 backdrop-blur-sm">
            <a href="?type=overall" class="px-6 py-3 rounded-lg transition-all duration-300 font-medium {{ $type === 'overall' ? 'bg-purple-600 text-white' : 'text-gray-300 hover:text-white' }}">
                <i class="fas fa-crown mr-2"></i>Overall
            </a>
            <a href="?type=weekly" class="px-6 py-3 rounded-lg transition-all duration-300 font-medium {{ $type === 'weekly' ? 'bg-purple-600 text-white' : 'text-gray-300 hover:text-white' }}">
                <i class="fas fa-calendar-week mr-2"></i>This Week
            </a>
            <a href="?type=games" class="px-6 py-3 rounded-lg transition-all duration-300 font-medium {{ $type === 'games' ? 'bg-purple-600 text-white' : 'text-gray-300 hover:text-white' }}">
                <i class="fas fa-gamepad mr-2"></i>Games
            </a>
        </div>

        @if($type === 'overall')
            <!-- Overall Leaderboard -->
            <div class="bg-black/30 rounded-xl p-6 backdrop-blur-sm">
                <h3 class="text-2xl font-bold mb-6 text-yellow-400">
                    <i class="fas fa-crown mr-2"></i>Top Players of All Time
                </h3>
                <div class="space-y-4">
                    @forelse($leaderboards['overall'] ?? [] as $index => $player)
                    <div class="flex items-center space-x-4 p-4 bg-white/10 rounded-lg transition-all duration-300 hover:bg-white/20 {{ $player->user_id === auth()->id() ? 'border-l-4 border-yellow-400 bg-yellow-500/10' : '' }}">
                        <div class="text-3xl font-bold {{ $index < 3 ? 'text-yellow-400' : 'text-gray-400' }}">
                            @if($index === 0)
                                <i class="fas fa-crown"></i>
                            @elseif($index === 1)
                                <i class="fas fa-medal text-gray-300"></i>
                            @elseif($index === 2)
                                <i class="fas fa-medal text-orange-400"></i>
                            @else
                                #{{ $index + 1 }}
                            @endif
                        </div>
                        <div class="flex-1">
                            <div class="font-semibold text-lg">{{ $player->user->name }}</div>
                            <div class="text-sm text-gray-300">Level {{ $player->level }} • {{ $player->current_streak }} day streak</div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-blue-400 text-xl">{{ number_format($player->total_points) }}</div>
                            <div class="text-xs text-gray-400">total points</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <i class="fas fa-trophy text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-400">No rankings available yet.</p>
                    </div>
                    @endforelse
                </div>
            </div>

        @elseif($type === 'weekly')
            <!-- Weekly Leaderboard -->
            <div class="bg-black/30 rounded-xl p-6 backdrop-blur-sm">
                <h3 class="text-2xl font-bold mb-6 text-green-400">
                    <i class="fas fa-calendar-week mr-2"></i>This Week's Top Performers
                </h3>
                <div class="space-y-4">
                    @forelse($leaderboards['weekly'] ?? [] as $index => $player)
                    <div class="flex items-center space-x-4 p-4 bg-white/10 rounded-lg transition-all duration-300 hover:bg-white/20">
                        <div class="text-2xl font-bold text-green-400">#{{ $index + 1 }}</div>
                        <div class="flex-1">
                            <div class="font-semibold text-lg">{{ $player->user->name }}</div>
                            <div class="text-sm text-gray-300">This week's activity</div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-green-400 text-xl">{{ number_format($player->weekly_points) }}</div>
                            <div class="text-xs text-gray-400">weekly points</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <i class="fas fa-calendar-week text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-400">No weekly rankings yet. Start learning to appear here!</p>
                    </div>
                    @endforelse
                </div>
            </div>

        @else
            <!-- Game Leaderboards -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @foreach($leaderboards['games'] ?? [] as $gameId => $gamePlayers)
                <div class="bg-black/30 rounded-xl p-6 backdrop-blur-sm">
                    <h3 class="text-xl font-bold mb-4 text-purple-400 capitalize">
                        <i class="fas fa-gamepad mr-2"></i>{{ str_replace('_', ' ', $gameId) }}
                    </h3>
                    <div class="space-y-3">
                        @forelse($gamePlayers as $index => $player)
                        <div class="flex items-center space-x-3 p-3 bg-white/10 rounded-lg">
                            <div class="text-lg font-bold text-purple-400">#{{ $index + 1 }}</div>
                            <div class="flex-1">
                                <div class="font-semibold">{{ $player->user->name }}</div>
                                <div class="text-xs text-gray-400">Level {{ $player->level }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-purple-400">{{ number_format($player->game_scores[$gameId] ?? 0) }}</div>
                                <div class="text-xs text-gray-400">high score</div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4">
                            <p class="text-gray-400 text-sm">No scores yet for this game</p>
                        </div>
                        @endforelse
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection