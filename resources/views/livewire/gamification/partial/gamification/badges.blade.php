@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-900 via-blue-900 to-indigo-900 text-white">
    <div class="container mx-auto px-6 py-8">
        <div class="mb-8">
            <h1 class="text-4xl font-bold bg-gradient-to-r from-yellow-400 to-orange-500 bg-clip-text text-transparent">
                <i class="fas fa-medal mr-3"></i>My Badges
            </h1>
            <p class="text-gray-300 mt-2">Your achievements and milestones</p>
        </div>

        <!-- Achievement Progress -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-black/30 rounded-xl p-6 backdrop-blur-sm">
                <h3 class="text-lg font-bold mb-3 text-yellow-400">Level Progress</h3>
                <div class="text-2xl font-bold">{{ $progress['level_progress']['current'] }}</div>
                <div class="text-sm text-gray-300">Next: Level {{ $progress['level_progress']['next_milestone'] ?? 'Max' }}</div>
                <div class="w-full bg-gray-700 rounded-full h-2 mt-2">
                    <div class="bg-gradient-to-r from-yellow-400 to-orange-500 h-2 rounded-full" 
                         style="width: {{ $progress['level_progress']['progress'] }}%"></div>
                </div>
            </div>

            <div class="bg-black/30 rounded-xl p-6 backdrop-blur-sm">
                <h3 class="text-lg font-bold mb-3 text-red-400">Streak Progress</h3>
                <div class="text-2xl font-bold">{{ $progress['streak_progress']['current'] }}</div>
                <div class="text-sm text-gray-300">Best: {{ $progress['streak_progress']['longest'] }} days</div>
                <div class="text-xs text-gray-400 mt-2">Next milestone: {{ $progress['streak_progress']['next_milestone'] ?? 'Max' }} days</div>
            </div>

            <div class="bg-black/30 rounded-xl p-6 backdrop-blur-sm">
                <h3 class="text-lg font-bold mb-3 text-green-400">Courses</h3>
                <div class="text-2xl font-bold">{{ $progress['course_progress']['completed'] }}</div>
                <div class="text-sm text-gray-300">Completed</div>
                <div class="text-xs text-gray-400 mt-2">Next milestone: {{ $progress['course_progress']['next_milestone'] ?? 'Max' }} courses</div>
            </div>

            <div class="bg-black/30 rounded-xl p-6 backdrop-blur-sm">
                <h3 class="text-lg font-bold mb-3 text-blue-400">Quiz Performance</h3>
                <div class="text-2xl font-bold">{{ $progress['quiz_performance']['average_score'] }}%</div>
                <div class="text-sm text-gray-300">Average Score</div>
                <div class="text-xs text-gray-400 mt-2">Next milestone: {{ $progress['quiz_performance']['next_milestone'] ?? 'Perfect' }}%</div>
            </div>
        </div>

        <!-- Badges Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($userBadges as $badge)
            <div class="badge-card bg-black/30 rounded-xl p-6 backdrop-blur-sm border-2 transition-all duration-300 transform hover:scale-105"
                 style="border-color: {{ $badge->rarity_color }}">
                <div class="text-center">
                    <div class="text-4xl mb-3" style="color: {{ $badge->badge_color }}">
                        <i class="{{ $badge->badge_icon }}"></i>
                    </div>
                    <h3 class="text-lg font-bold mb-2">{{ $badge->badge_name }}</h3>
                    <p class="text-gray-300 text-sm mb-3">{{ $badge->badge_description }}</p>
                    
                    <!-- Rarity Badge -->
                    <div class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase mb-2"
                         style="background-color: {{ $badge->rarity_color }}20; color: {{ $badge->rarity_color }};">
                        {{ $badge->rarity }}
                    </div>
                    
                    @if($badge->points_reward > 0)
                    <div class="text-xs text-yellow-400 mb-2">
                        <i class="fas fa-star"></i> +{{ $badge->points_reward }} points
                    </div>
                    @endif
                    
                    <div class="text-xs text-gray-400">
                        Earned {{ $badge->created_at->diffForHumans() }}
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <i class="fas fa-medal text-6xl text-gray-400 mb-4"></i>
                <h3 class="text-xl font-bold text-gray-400 mb-2">No badges yet!</h3>
                <p class="text-gray-500">Start learning to unlock your first achievement badges.</p>
                <a href="{{ route('student.dashboard') }}" class="inline-block mt-4 bg-gradient-to-r from-purple-600 to-pink-600 hover:from-purple-700 hover:to-pink-700 px-6 py-3 rounded-lg transition-all duration-300 transform hover:scale-105">
                    Start Learning
                </a>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($userBadges->hasPages())
        <div class="mt-8">
            {{ $userBadges->links() }}
        </div>
        @endif
    </div>
</div