@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900 text-white">
    <div class="container mx-auto px-6 py-8">
        <div class="mb-8">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-yellow-400 to-orange-500 bg-clip-text text-transparent">
                <i class="fas fa-gamepad mr-3"></i>Mini Games
            </h1>
            <p class="text-gray-300 mt-2">Challenge yourself and earn rewards!</p>
        </div>

        <!-- Energy Status -->
        <div class="bg-black/30 rounded-xl p-6 backdrop-blur-sm mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-red-400">
                        <i class="fas fa-battery-three-quarters mr-2"></i>Energy Status
                    </h3>
                    <p class="text-gray-300">{{ $energyStatus['current'] }}/{{ $energyStatus['max'] }} Energy Available</p>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-red-400">{{ $energyStatus['current'] }}</div>
                    @if(!$energyStatus['is_full'])
                        <div class="text-sm text-gray-400">
                            Next energy in {{ $energyStatus['next_energy_in'] }} min
                        </div>
                    @endif
                </div>
            </div>
            <div class="w-full bg-gray-700 rounded-full h-3 mt-4">
                <div class="bg-gradient-to-r from-red-400 to-rose-500 h-3 rounded-full transition-all duration-500"
                     style="width: {{ $energyStatus['percentage'] }}%"></div>
            </div>
            @if(!$energyStatus['is_full'])
                <div class="text-xs text-gray-400 mt-2">
                    Full energy in {{ $energyStatus['time_to_full'] }} minutes
                </div>
            @endif
        </div>

        <!-- Game Stats Summary -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-black/30 rounded-xl p-6 backdrop-blur-sm">
                <h3 class="text-lg font-bold mb-2 text-blue-400">Games Played</h3>
                <div class="text-3xl font-bold">{{ $gameStats['total_games_played'] }}</div>
            </div>
            <div class="bg-black/30 rounded-xl p-6 backdrop-blur-sm">
                <h3 class="text-lg font-bold mb-2 text-green-400">Favorite Game</h3>
                <div class="text-lg font-bold capitalize">
                    {{ $gameStats['favorite_game'] ? str_replace('_', ' ', $gameStats['favorite_game']) : 'None yet' }}
                </div>
            </div>
            <div class="bg-black/30 rounded-xl p-6 backdrop-blur-sm">
                <h3 class="text-lg font-bold mb-2 text-purple-400">Best Scores</h3>
                <div class="text-sm">
                    @if(!empty($gameStats['best_scores']))
                        @foreach(array_slice($gameStats['best_scores'], 0, 2) as $game => $score)
                            <div>{{ str_replace('_', ' ', $game) }}: {{ number_format($score) }}</div>
                        @endforeach
                    @else
                        No scores yet
                    @endif
                </div>
            </div>
        </div>

        <!-- Available Games -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($availableGames as $game)
            <div class="game-card bg-black/30 rounded-xl p-6 backdrop-blur-sm border border-white/10 hover:border-white/30 transition-all duration-300 transform hover:scale-105 hover:shadow-2xl">
                <div class="text-center">
                    <div class="text-5xl mb-4">
                        <i class="{{ $game['icon'] }} text-yellow-400"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-2">{{ $game['name'] }}</h3>
                    <p class="text-gray-300 text-sm mb-4">{{ $game['description'] }}</p>
                    
                    <!-- Game Stats -->
                    <div class="space-y-2 mb-4 text-xs bg-white/5 rounded-lg p-3">
                        <div class="flex justify-between">
                            <span>Energy Cost:</span>
                            <span class="text-red-400">
                                <i class="fas fa-battery-half"></i> {{ $game['energy_cost'] }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span>Max Reward:</span>
                            <span class="text-yellow-400">
                                <i class="fas fa-coins"></i> {{ $game['max_score_reward'] }}
                            </span>
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

                    <!-- Personal High Score -->
                    @php
                        $highScore = $gameStats['best_scores'][$game['id']] ?? 0;
                    @endphp
                    @if($highScore > 0)
                        <div class="bg-yellow-500/20 rounded-lg p-3 mb-4">
                            <div class="text-xs text-yellow-400 font-bold">YOUR HIGH SCORE</div>
                            <div class="text-2xl font-bold text-yellow-300">{{ number_format($highScore) }}</div>
                        </div>
                    @endif

                    <!-- Play Button -->
                    @if($energyStatus['current'] >= $game['energy_cost'])
                        <a href="{{ route('gamification.play-game', $game['id']) }}" 
                           class="inline-block w-full bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 px-4 py-3 rounded-lg transition-all duration-300 transform hover:scale-105 font-medium shadow-lg">
                            <i class="fas fa-play mr-2"></i>Play Now
                        </a>
                    @else
                        <button class="w-full bg-gray-600 px-4 py-3 rounded-lg font-medium cursor-not-allowed" disabled>
                            <i class="fas fa-battery-empty mr-2"></i>Not Enough Energy
                        </button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        @if(empty($availableGames))
        <div class="text-center py-12">
            <i class="fas fa-gamepad text-6xl text-gray-400 mb-4"></i>
            <h3 class="text-xl font-bold text-gray-400 mb-2">No games available!</h3>
            <p class="text-gray-500">Check back later for new games.</p>
        </div>
        @endif
    </div>
</div>

<style>
.game-card {
    background: linear-gradient(135deg, rgba(147, 51, 234, 0.1) 0%, rgba(79, 70, 229, 0.1) 100%);
}
</style>
@endsection