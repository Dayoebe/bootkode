{{-- resources/views/livewire/learning-analytics-dashboard.blade.php --}}
<div x-data="livewireDashboard()" 
     x-init="init()" 
     class="min-h-screen bg-gray-900 text-white">
     
    {{-- Real-time data binding with Livewire --}}
    <div wire:poll.30s="loadDashboardData" class="hidden"></div>
    
    {{-- Header Section --}}
    <div class="glass-effect animate__animated animate__fadeInDown">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center floating">
                        <i class="fas fa-chart-line text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold">{{ $user->name }}'s Analytics</h1>
                        <p class="text-gray-300">Level {{ $stats['level'] ?? 1 }} • {{ number_format($stats['total_points'] ?? 0) }} XP</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" wire:model="autoRefresh" id="autoRefresh" class="rounded">
                        <label for="autoRefresh" class="text-sm">Live Updates</label>
                    </div>
                    
                    <button wire:click="refreshData" 
                            class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg transition-all duration-300 hover:scale-105"
                            wire:loading.attr="disabled">
                        <i class="fas fa-sync-alt" 
                           wire:loading.class="animate-spin" 
                           wire:target="refreshData"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Dashboard Grid --}}
    <div class="px-6 py-8">
        {{-- Gamification Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            {{-- Level Progress Card --}}
            <div class="glass-effect rounded-2xl p-6 metric-card transition-all duration-300 animate__animated animate__fadeInUp hover:scale-105" 
                 style="animation-delay: 0.1s">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-blue-500 to-cyan-500 flex items-center justify-center pulse-slow">
                        <i class="fas fa-star text-xl"></i>
                    </div>
                    <span class="text-3xl font-bold text-blue-400">{{ $stats['level'] ?? 1 }}</span>
                </div>
                <h3 class="text-lg font-semibold mb-2">Level Progress</h3>
                <div class="w-full bg-gray-700 rounded-full h-3 mb-2 overflow-hidden">
                    <div class="level-progress h-3 rounded-full transition-all duration-2000 animate__animated animate__slideInLeft" 
                         style="width: {{ $stats['progress_to_next_level'] ?? 0 }}%"></div>
                </div>
                <p class="text-sm text-gray-300">{{ number_format($stats['total_points'] ?? 0) }} XP</p>
            </div>

            {{-- Streak Card --}}
            <div class="glass-effect rounded-2xl p-6 metric-card transition-all duration-300 animate__animated animate__fadeInUp hover:scale-105" 
                 style="animation-delay: 0.2s">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-red-500 to-orange-500 flex items-center justify-center">
                        <i class="fas fa-fire text-xl {{ ($stats['current_streak'] ?? 0) > 0 ? 'animate__animated animate__pulse animate__infinite' : '' }}"></i>
                    </div>
                    <span class="text-3xl font-bold text-red-400">{{ $stats['current_streak'] ?? 0 }}</span>
                </div>
                <h3 class="text-lg font-semibold mb-2">Current Streak</h3>
                <p class="text-sm text-gray-300">Best: {{ $stats['longest_streak'] ?? 0 }} days</p>
                <div class="mt-3 flex items-center space-x-1">
                    @if(isset($streakData['recent_activity']))
                        @foreach(array_slice($streakData['recent_activity'], -7) as $day)
                            <div class="w-4 h-4 rounded-full streak-day {{ $day['active'] ? 'active animate__animated animate__pulse' : 'bg-gray-600' }}"
                                 title="{{ $day['day'] }}: {{ $day['active'] ? 'Active' : 'Inactive' }}">
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            {{-- Energy Card --}}
            <div class="glass-effect rounded-2xl p-6 metric-card transition-all duration-300 animate__animated animate__fadeInUp hover:scale-105" 
                 style="animation-delay: 0.3s">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-green-500 to-emerald-500 flex items-center justify-center">
                        <i class="fas fa-bolt text-xl"></i>
                    </div>
                    <span class="text-3xl font-bold text-green-400">{{ $stats['energy']['current'] ?? 100 }}</span>
                </div>
                <h3 class="text-lg font-semibold mb-2">Energy</h3>
                <div class="w-full bg-gray-700 rounded-full h-3 mb-2 overflow-hidden">
                    <div class="energy-bar h-3 rounded-full transition-all duration-2000 animate__animated animate__slideInLeft" 
                         style="width: {{ $stats['energy']['percentage'] ?? 100 }}%"></div>
                </div>
                <p class="text-xs text-gray-300">
                    @if(($stats['energy']['is_full'] ?? false))
                        <span class="text-green-400 font-bold">⚡ Full Energy!</span>
                    @else
                        Next: {{ $stats['energy']['next_energy_in'] ?? 0 }}m
                    @endif
                </p>
            </div>

            {{-- Currency Card --}}
            <div class="glass-effect rounded-2xl p-6 metric-card transition-all duration-300 animate__animated animate__fadeInUp hover:scale-105" 
                 style="animation-delay: 0.4s">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-full bg-gradient-to-r from-yellow-500 to-amber-500 flex items-center justify-center floating">
                        <i class="fas fa-coins text-xl"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-bold flex items-center text-yellow-400">
                            <i class="fas fa-coins mr-1"></i>
                            {{ number_format($stats['coins'] ?? 0) }}
                        </div>
                        <div class="text-sm flex items-center text-purple-400">
                            <i class="fas fa-gem mr-1"></i>
                            {{ number_format($stats['gems'] ?? 0) }}
                        </div>
                    </div>
                </div>
                <h3 class="text-lg font-semibold mb-2">Currency</h3>
                <p class="text-sm text-gray-300">Visit the store</p>
            </div>
        </div>

        {{-- Main Analytics Section --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            {{-- Interactive Chart --}}
            <div class="lg:col-span-2 glass-effect rounded-2xl p-6 animate__animated animate__fadeInLeft">
                {{-- Chart Controls --}}
                <div class="flex flex-wrap items-center justify-between mb-6">
                    <div class="flex space-x-2 mb-2 md:mb-0">
                        <button wire:click="updateMetric('overview')" 
                                class="px-4 py-2 rounded-lg transition-all duration-300 hover:scale-105 {{ $selectedMetric === 'overview' ? 'bg-blue-600 text-white' : 'bg-gray-700 text-gray-300' }}">
                            Overview
                        </button>
                        <button wire:click="updateMetric('points')" 
                                class="px-4 py-2 rounded-lg transition-all duration-300 hover:scale-105 {{ $selectedMetric === 'points' ? 'bg-blue-600 text-white' : 'bg-gray-700 text-gray-300' }}">
                            Points
                        </button>
                        <button wire:click="updateMetric('learning_time')" 
                                class="px-4 py-2 rounded-lg transition-all duration-300 hover:scale-105 {{ $selectedMetric === 'learning_time' ? 'bg-blue-600 text-white' : 'bg-gray-700 text-gray-300' }}">
                            Time
                        </button>
                        <button wire:click="updateMetric('assessments')" 
                                class="px-4 py-2 rounded-lg transition-all duration-300 hover:scale-105 {{ $selectedMetric === 'assessments' ? 'bg-blue-600 text-white' : 'bg-gray-700 text-gray-300' }}">
                            Scores
                        </button>
                    </div>
                    
                    <select wire:model="selectedTimeframe" 
                            class="bg-gray-700 text-white px-4 py-2 rounded-lg border border-gray-600 transition-all duration-300">
                        <option value="7d">7 Days</option>
                        <option value="30d">30 Days</option>
                        <option value="90d">90 Days</option>
                        <option value="1y">1 Year</option>
                    </select>
                </div>
                
                {{-- Chart Container --}}
                <div class="chart-container rounded-xl p-4 h-80">
                    <canvas id="analyticsChart" 
                            class="w-full h-full"
                            x-data="chartComponent({{ json_encode($chartData) }})"
                            x-init="initChart()"></canvas>
                </div>
            </div>

            {{-- Sidebar: Achievements & Leaderboard --}}
            <div class="space-y-6">
                {{-- Quick Stats Panel --}}
                <div class="glass-effect rounded-2xl p-6 animate__animated animate__fadeInRight">
                    <h3 class="text-xl font-bold mb-4 flex items-center">
                        <i class="fas fa-trophy text-yellow-400 mr-2"></i>
                        Your Stats
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-300">Global Rank</span>
                            <span class="font-bold text-yellow-400">#{{ $stats['rank'] ?? '---' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-300">Courses Done</span>
                            <span class="font-bold text-green-400">{{ $stats['courses_completed'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-300">Lessons Done</span>
                            <span class="font-bold text-blue-400">{{ $stats['lessons_completed'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-300">Avg Quiz Score</span>
                            <span class="font-bold text-purple-400">{{ round($stats['average_quiz_score'] ?? 0) }}%</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-300">Badges Earned</span>
                            <span class="font-bold text-orange-400">{{ $stats['badges_count'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                {{-- Recent Achievements --}}
                <div class="glass-effect rounded-2xl p-6 animate__animated animate__fadeInRight" style="animation-delay: 0.2s">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold flex items-center">
                            <i class="fas fa-medal text-yellow-400 mr-2"></i>
                            Recent Achievements
                        </h3>
                        <button wire:click="toggleAchievements" 
                                class="text-blue-400 hover:text-blue-300 transition-colors">
                            <i class="fas fa-chevron-down transition-transform duration-300 {{ $showAchievements ? 'rotate-180' : '' }}"></i>
                        </button>
                    </div>
                    
                    @if(count($achievements) > 0)
                        <div class="space-y-3" @if($showAchievements) wire:transition @endif>
                            @foreach(array_slice($achievements, 0, $showAchievements ? count($achievements) : 3) as $index => $achievement)
                                <div class="achievement-card rounded-lg p-4 animate__animated animate__fadeInUp hover:scale-105 transition-transform duration-300"
                                     style="animation-delay: {{ $index * 0.1 }}s">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center"
                                             style="background: {{ $achievement['color'] ?? '#3B82F6' }};">
                                            <i class="{{ $achievement['icon'] ?? 'fas fa-trophy' }} text-white"></i>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-semibold">{{ $achievement['name'] }}</h4>
                                            <p class="text-sm text-gray-300">{{ $achievement['description'] }}</p>
                                            <div class="flex items-center space-x-2 mt-1">
                                                <span class="text-xs px-2 py-1 rounded-full
                                                    @switch($achievement['rarity'])
                                                        @case('common') bg-gray-600 text-gray-300 @break
                                                        @case('rare') bg-blue-600 text-blue-200 @break
                                                        @case('epic') bg-purple-600 text-purple-200 @break
                                                        @case('legendary') bg-yellow-600 text-yellow-200 @break
                                                        @default bg-gray-600 text-gray-300
                                                    @endswitch">
                                                    {{ ucfirst($achievement['rarity'] ?? 'common') }}
                                                </span>
                                                <span class="text-xs text-green-400">+{{ $achievement['points_reward'] ?? 0 }} XP</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-400 animate__animated animate__fadeIn">
                            <i class="fas fa-trophy text-4xl mb-2 opacity-50"></i>
                            <p>No achievements yet. Start learning to unlock them!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Advanced Analytics Row --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            {{-- Learning Progress Breakdown --}}
            <div class="glass-effect rounded-2xl p-6 animate__animated animate__fadeInUp" style="animation-delay: 0.5s">
                <h3 class="text-xl font-bold mb-6 flex items-center">
                    <i class="fas fa-chart-pie text-blue-400 mr-2"></i>
                    Learning Progress
                </h3>
                
                <div class="space-y-6">
                    {{-- Course Progress --}}
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-300">Courses in Progress</span>
                            <span class="font-bold">{{ $progressData['courses_in_progress'] ?? 0 }}</span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-2 overflow-hidden">
                            <div class="bg-gradient-to-r from-blue-500 to-cyan-500 h-2 rounded-full transition-all duration-1000 animate__animated animate__slideInLeft"
                                 style="width: {{ min(100, ($progressData['courses_in_progress'] ?? 0) * 20) }}%"></div>
                        </div>
                    </div>

                    {{-- Weekly Completion Rate --}}
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-300">Weekly Growth</span>
                            <span class="font-bold flex items-center {{ ($progressData['completion_rate'] ?? 0) >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                <i class="fas fa-arrow-{{ ($progressData['completion_rate'] ?? 0) >= 0 ? 'up' : 'down' }} mr-1"></i>
                                {{ abs($progressData['completion_rate'] ?? 0) }}%
                            </span>
                        </div>
                    </div>

                    {{-- Study Consistency --}}
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-300">Study Consistency</span>
                            <span class="font-bold text-purple-400">{{ round($progressData['study_consistency'] ?? 0) }}%</span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-2 overflow-hidden">
                            <div class="bg-gradient-to-r from-purple-500 to-pink-500 h-2 rounded-full transition-all duration-1000 animate__animated animate__slideInLeft"
                                 style="width: {{ $progressData['study_consistency'] ?? 0 }}%"></div>
                        </div>
                    </div>

                    {{-- Most Active Course --}}
                    @if(isset($progressData['most_active_course']))
                        <div class="bg-gray-800 rounded-lg p-4 animate__animated animate__fadeIn">
                            <h4 class="font-semibold text-green-400 mb-2">Most Active Course</h4>
                            <p class="text-sm">{{ $progressData['most_active_course']['title'] ?? 'None' }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Leaderboard --}}
            <div class="glass-effect rounded-2xl p-6 animate__animated animate__fadeInRight" style="animation-delay: 0.6s">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold flex items-center">
                        <i class="fas fa-ranking-star text-yellow-400 mr-2"></i>
                        Top Learners
                    </h3>
                    <button wire:click="toggleLeaderboard"
                            class="text-blue-400 hover:text-blue-300 transition-colors">
                        <i class="fas fa-expand-alt"></i>
                    </button>
                </div>
                
                @if(count($leaderboard) > 0)
                    <div class="space-y-3">
                        @foreach($leaderboard->take($showLeaderboard ? 10 : 5) as $leader)
                            <div class="leaderboard-item rounded-lg p-3 transition-all duration-300 hover:scale-105 {{ $leader['is_current_user'] ? 'neon-glow bg-blue-900' : '' }}">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold
                                        @if($leader['rank'] === 1) bg-yellow-500 text-yellow-900
                                        @elseif($leader['rank'] === 2) bg-gray-400 text-gray-900
                                        @elseif($leader['rank'] === 3) bg-yellow-600 text-yellow-100
                                        @else bg-gray-600 text-gray-200 @endif">
                                        {{ $leader['rank'] }}
                                    </div>
                                    
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2">
                                            <h4 class="font-semibold">{{ $leader['user']['name'] ?? 'Anonymous' }}</h4>
                                            <span class="text-xs px-2 py-1 bg-blue-600 rounded-full">L{{ $leader['level'] }}</span>
                                        </div>
                                        <div class="flex items-center space-x-4 text-sm text-gray-300">
                                            <span>{{ number_format($leader['points']) }} XP</span>
                                            <span class="flex items-center">
                                                <i class="fas fa-fire text-red-400 mr-1"></i>
                                                {{ $leader['streak'] }}
                                            </span>
                                            <span class="flex items-center">
                                                <i class="fas fa-medal text-yellow-400 mr-1"></i>
                                                {{ $leader['badges_count'] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Advanced Visualizations --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            {{-- Activity Heatmap --}}
            <div class="glass-effect rounded-2xl p-6 animate__animated animate__fadeInUp" style="animation-delay: 0.7s">
                <h3 class="text-xl font-bold mb-6 flex items-center">
                    <i class="fas fa-calendar-alt text-red-400 mr-2"></i>
                    Activity Heatmap
                </h3>
                
                <div class="grid grid-cols-7 gap-2 mb-4">
                    @if(isset($streakData['recent_activity']))
                        @foreach($streakData['recent_activity'] as $day)
                            <div class="text-center">
                                <div class="text-xs text-gray-400 mb-1">{{ substr($day['day'], 0, 3) }}</div>
                                <div class="w-full h-12 rounded-lg streak-day flex items-center justify-center transition-all duration-300 {{ $day['active'] ? 'active animate__animated animate__pulse' : 'bg-gray-700' }}"
                                     title="{{ $day['date'] }}: {{ $day['active'] ? 'Active' : 'Inactive' }}">
                                    @if($day['active'])
                                        <span class="text-xs font-bold">{{ round($day['intensity'] ?? 0) }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
                
                <div class="flex items-center justify-between text-sm mb-4">
                    <span class="text-gray-400">Less</span>
                    <div class="flex space-x-1">
                        <div class="w-3 h-3 rounded bg-gray-700"></div>
                        <div class="w-3 h-3 rounded bg-red-300"></div>
                        <div class="w-3 h-3 rounded bg-red-500"></div>
                        <div class="w-3 h-3 rounded bg-red-700"></div>
                        <div class="w-3 h-3 rounded bg-red-900"></div>
                    </div>
                    <span class="text-gray-400">More</span>
                </div>

                {{-- Streak Goals --}}
                <div class="p-4 bg-gray-800 rounded-lg">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-gray-300">Next Streak Goal</span>
                        <span class="font-bold text-orange-400">{{ $streakData['streak_goal'] ?? 7 }} days</span>
                    </div>
                    <div class="w-full bg-gray-700 rounded-full h-2 overflow-hidden">
                        <div class="bg-gradient-to-r from-red-500 to-orange-500 h-2 rounded-full transition-all duration-1000 animate__animated animate__slideInLeft"
                             style="width: {{ $streakData['streak_percentage'] ?? 0 }}%"></div>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">{{ $streakData['days_to_goal'] ?? 0 }} days to go</p>
                </div>
            </div>

            {{-- Performance Metrics --}}
            <div class="glass-effect rounded-2xl p-6 animate__animated animate__fadeInUp" style="animation-delay: 0.8s">
                <h3 class="text-xl font-bold mb-6 flex items-center">
                    <i class="fas fa-chart-bar text-green-400 mr-2"></i>
                    Performance Metrics
                </h3>
                
                <div class="space-y-6">
                    {{-- Weekly Study Time --}}
                    <div class="relative">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-300">Weekly Study Time</span>
                            <span class="font-bold text-blue-400">28h</span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-4 relative overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-cyan-500 h-full rounded-full transition-all duration-2000 animate__animated animate__slideInLeft"
                                 style="width: 70%"></div>
                            <div class="absolute inset-0 flex items-center justify-center text-xs font-bold">
                                70%
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Goal: 40 hours/week</p>
                    </div>

                    {{-- Quiz Performance Trend --}}
                    <div class="relative">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-300">Quiz Performance</span>
                            <span class="font-bold text-purple-400">{{ round($stats['average_quiz_score'] ?? 0) }}%</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="flex-1 bg-gray-700 rounded-full h-4 relative overflow-hidden">
                                <div class="absolute inset-0 bg-gradient-to-r from-purple-500 to-pink-500 h-full rounded-full transition-all duration-2000 animate__animated animate__slideInLeft"
                                     style="width: {{ $stats['average_quiz_score'] ?? 0 }}%"></div>
                            </div>
                            @if(($stats['average_quiz_score'] ?? 0) >= 90)
                                <div class="text-lg animate__animated animate__bounceIn">🏆</div>
                            @elseif(($stats['average_quiz_score'] ?? 0) >= 80)
                                <div class="text-lg animate__animated animate__bounceIn">🥇</div>
                            @elseif(($stats['average_quiz_score'] ?? 0) >= 70)
                                <div class="text-lg animate__animated animate__bounceIn">🥈</div>
                            @endif
                        </div>
                    </div>

                    {{-- Learning Velocity Visualization --}}
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-300">Learning Velocity</span>
                            <span class="font-bold text-green-400">3.2/day</span>
                        </div>
                        <div class="grid grid-cols-7 gap-1">
                            @foreach(['20', '85', '60', '90', '75', '40', '95'] as $height)
                                <div class="h-8 rounded bg-gray-700 relative overflow-hidden">
                                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-green-500 to-emerald-400 transition-all duration-1000 animate__animated animate__slideInUp"
                                         style="height: {{ $height }}%; animation-delay: {{ $loop->index * 0.1 }}s"></div>
                                </div>
                            @endforeach
                        </div>
                        <div class="flex justify-between text-xs text-gray-400 mt-1">
                            <span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span><span>Sun</span>
                        </div>
                    </div>

                    {{-- AI Insights --}}
                    <div class="bg-gradient-to-r from-indigo-900 to-purple-900 rounded-lg p-4 animate__animated animate__fadeIn">
                        <div class="flex items-center space-x-2 mb-2">
                            <i class="fas fa-brain text-cyan-400 animate__animated animate__pulse animate__infinite"></i>
                            <span class="font-semibold text-cyan-400">AI Insight</span>
                        </div>
                        <p class="text-sm text-gray-200">Your learning velocity increased 23% this week. JavaScript is your strongest subject with 87% average scores.</p>
                    </div>
                </div>
            </div>

            {{-- Achievement Progress --}}
            <div class="glass-effect rounded-2xl p-6 animate__animated animate__fadeInUp" style="animation-delay: 0.9s">
                <h3 class="text-xl font-bold mb-6 flex items-center">
                    <i class="fas fa-target text-purple-400 mr-2"></i>
                    Achievement Progress
                </h3>
                
                <div class="space-y-6">
                    {{-- Level Progress Detailed --}}
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-300">Level Progress</span>
                            <span class="font-bold text-blue-400">Level {{ $stats['level'] ?? 1 }}</span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-3 overflow-hidden">
                            <div class="bg-gradient-to-r from-blue-500 to-purple-500 h-3 rounded-full transition-all duration-2000 animate__animated animate__slideInLeft"
                                 style="width: {{ $stats['progress_to_next_level'] ?? 0 }}%"></div>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">{{ $stats['progress_to_next_level'] ?? 0 }}% to next level</p>
                    </div>

                    {{-- Badge Categories Grid --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center p-4 bg-gray-800 rounded-lg hover:bg-gray-750 transition-colors duration-300">
                            <div class="text-2xl font-bold text-yellow-400 animate__animated animate__countUp">3</div>
                            <div class="text-sm text-gray-300">Level Badges</div>
                        </div>
                        <div class="text-center p-4 bg-gray-800 rounded-lg hover:bg-gray-750 transition-colors duration-300">
                            <div class="text-2xl font-bold text-red-400 animate__animated animate__countUp">2</div>
                            <div class="text-sm text-gray-300">Streak Badges</div>
                        </div>
                        <div class="text-center p-4 bg-gray-800 rounded-lg hover:bg-gray-750 transition-colors duration-300">
                            <div class="text-2xl font-bold text-green-400 animate__animated animate__countUp">4</div>
                            <div class="text-sm text-gray-300">Course Badges</div>
                        </div>
                        <div class="text-center p-4 bg-gray-800 rounded-lg hover:bg-gray-750 transition-colors duration-300">
                            <div class="text-2xl font-bold text-purple-400 animate__animated animate__countUp">3</div>
                            <div class="text-sm text-gray-300">Quiz Badges</div>
                        </div>
                    </div>

                    {{-- Next Achievement Preview --}}
                    <div class="bg-gradient-to-r from-yellow-900 to-orange-900 rounded-lg p-4 animate__animated animate__fadeIn">
                        <div class="flex items-center space-x-2 mb-2">
                            <i class="fas fa-bullseye text-yellow-400 animate__animated animate__pulse animate__infinite"></i>
                            <span class="font-semibold text-yellow-400">Next Achievement</span>
                        </div>
                        <p class="text-sm font-medium">Two Week Champion</p>
                        <p class="text-xs text-gray-300">Maintain a 14-day learning streak</p>
                        <div class="w-full bg-gray-700 rounded-full h-2 mt-2 overflow-hidden">
                            <div class="bg-gradient-to-r from-yellow-500 to-orange-500 h-2 rounded-full transition-all duration-1000 animate__animated animate__slideInLeft"
                                 style="width: 85%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Smart Recommendations Section --}}
        <div class="glass-effect rounded-2xl p-6 animate__animated animate__fadeInUp" style="animation-delay: 1s">
            <h3 class="text-xl font-bold mb-6 flex items-center">
                <i class="fas fa-lightbulb text-yellow-400 mr-2 animate__animated animate__pulse animate__infinite"></i>
                Smart Recommendations
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Study Recommendation --}}
                <div class="bg-gradient-to-br from-blue-900 to-indigo-900 rounded-lg p-4 hover:scale-105 transition-transform duration-300 animate__animated animate__fadeInLeft">
                    <div class="flex items-center space-x-2 mb-3">
                        <i class="fas fa-book-open text-blue-400"></i>
                        <span class="font-semibold">Study Recommendation</span>
                    </div>
                    <p class="text-sm text-gray-200 mb-3">Focus on JavaScript fundamentals - you're 75% through the course!</p>
                    <button class="w-full bg-blue-600 hover:bg-blue-700 py-2 rounded-lg transition-all duration-300 hover:shadow-lg">
                        Continue Learning
                    </button>
                </div>

                {{-- Challenge Card --}}
                <div class="bg-gradient-to-br from-purple-900 to-pink-900 rounded-lg p-4 hover:scale-105 transition-transform duration-300 animate__animated animate__fadeInUp" style="animation-delay: 0.1s">
                    <div class="flex items-center space-x-2 mb-3">
                        <i class="fas fa-trophy text-purple-400"></i>
                        <span class="font-semibold">Weekly Challenge</span>
                    </div>
                    <p class="text-sm text-gray-200 mb-3">Complete 5 lessons this week to unlock 500 bonus XP!</p>
                    <button class="w-full bg-purple-600 hover:bg-purple-700 py-2 rounded-lg transition-all duration-300 hover:shadow-lg">
                        Accept Challenge
                    </button>
                </div>

                {{-- Reward Available --}}
                <div class="bg-gradient-to-br from-green-900 to-emerald-900 rounded-lg p-4 hover:scale-105 transition-transform duration-300 animate__animated animate__fadeInRight" style="animation-delay: 0.2s">
                    <div class="flex items-center space-x-2 mb-3">
                        <i class="fas fa-gift text-green-400 animate__animated animate__tada animate__infinite"></i>
                        <span class="font-semibold">Reward Available</span>
                    </div>
                    <p class="text-sm text-gray-200 mb-3">Daily quest: Complete any lesson for +50 XP and 10 coins!</p>
                    <button class="w-full bg-green-600 hover:bg-green-700 py-2 rounded-lg transition-all duration-300 hover:shadow-lg">
                        Claim Reward
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Floating Achievement Notification --}}
    @if(session()->has('new_achievement'))
        <div class="fixed top-20 right-6 z-50 bg-gradient-to-r from-yellow-500 to-orange-500 text-white p-4 rounded-lg shadow-2xl max-w-sm animate__animated animate__bounceInRight">
            <div class="flex items-center space-x-3">
                <i class="fas fa-trophy text-2xl animate__animated animate__tada animate__infinite"></i>
                <div>
                    <h4 class="font-bold">New Achievement Unlocked!</h4>
                    <p class="text-sm">{{ session('new_achievement') }}</p>
                </div>
            </div>
            <button onclick="this.parentElement.remove()" class="absolute top-2 right-2 text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    {{-- Loading Overlay --}}
    <div wire:loading.flex wire:target="loadDashboardData,refreshData" 
         class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
        <div class="glass-effect rounded-2xl p-8 text-center">
            <i class="fas fa-spinner fa-spin text-4xl text-blue-400 mb-4"></i>
            <p class="text-lg font-semibold">Updating Analytics...</p>
        </div>
    </div>
    
    @push('scripts')
<script>
    function livewireDashboard() {
        return {
            chart: null,
            
            init() {
                this.initChart();
                this.startRealTimeUpdates();
            },
            
            initChart() {
                const ctx = document.getElementById('analyticsChart');
                if (!ctx) return;
                
                const chartData = @json($chartData);
                
                this.chart = new Chart(ctx, {
                    type: 'line',
                    data: chartData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: '#FFFFFF',
                                bodyColor: '#FFFFFF',
                                borderColor: '#3B82F6',
                                borderWidth: 1,
                                cornerRadius: 8
                            }
                        },
                        scales: {
                            x: {
                                grid: { color: 'rgba(255, 255, 255, 0.1)' },
                                ticks: { color: '#9CA3AF' }
                            },
                            y: {
                                grid: { color: 'rgba(255, 255, 255, 0.1)' },
                                ticks: { color: '#9CA3AF' }
                            }
                        },
                        animation: {
                            duration: 2000,
                            easing: 'easeInOutQuart'
                        }
                    }
                });
            },
            
            startRealTimeUpdates() {
                // Listen for Livewire events
                Livewire.on('data-refreshed', () => {
                    this.updateChart();
                    this.animateCounters();
                });
            },
            
            updateChart() {
                if (this.chart) {
                    const newData = @json($chartData);
                    this.chart.data = newData;
                    this.chart.update('active');
                }
            },
            
            animateCounters() {
                // Animate number counters
                document.querySelectorAll('[data-counter]').forEach(el => {
                    const target = parseInt(el.dataset.counter);
                    const duration = 1000;
                    const start = performance.now();
                    
                    const animate = (currentTime) => {
                        const elapsed = currentTime - start;
                        const progress = Math.min(elapsed / duration, 1);
                        const current = Math.floor(progress * target);
                        
                        el.textContent = current.toLocaleString();
                        
                        if (progress < 1) {
                            requestAnimationFrame(animate);
                        }
                    };
                    
                    requestAnimationFrame(animate);
                });
            }
        }
    }
    
    // Real-time notifications
    window.addEventListener('achievement-earned', event => {
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-gradient-to-r from-yellow-500 to-orange-500 text-white p-4 rounded-lg shadow-2xl z-50 animate__animated animate__bounceInRight';
        notification.innerHTML = `
            <div class="flex items-center space-x-3">
                <i class="fas fa-trophy text-2xl animate__animated animate__tada animate__infinite"></i>
                <div>
                    <h4 class="font-bold">Achievement Unlocked!</h4>
                    <p class="text-sm">${event.detail.name}</p>
                </div>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.remove('animate__bounceInRight');
            notification.classList.add('animate__bounceOutRight');
            setTimeout(() => notification.remove(), 1000);
        }, 5000);
    });
</script>
@endpush

@push('styles')
<style>
    .glass-effect {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .energy-bar {
        background: linear-gradient(90deg, #10B981 0%, #F59E0B 50%, #EF4444 100%);
    }
    
    .level-progress {
        background: linear-gradient(90deg, #3B82F6, #8B5CF6);
    }
    
    .floating {
        animation: float 3s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }
    
    .pulse-slow {
        animation: pulse 3s infinite;
    }
    
    .streak-day.active {
        background: linear-gradient(45deg, #EF4444, #F59E0B);
        box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
    }
    
    .achievement-card {
        background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.1);
    }
    
    .metric-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.2);
    }
    
    .leaderboard-item {
        background: linear-gradient(90deg, rgba(59, 130, 246, 0.1) 0%, rgba(139, 92, 246, 0.1) 100%);
    }
    
    .neon-glow {
        box-shadow: 0 0 20px rgba(59, 130, 246, 0.5);
    }
</style>
@endpush
</div>