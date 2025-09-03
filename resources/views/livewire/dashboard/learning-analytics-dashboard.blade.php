{{-- resources/views/livewire/learning-analytics-dashboard.blade.php --}}
<div x-data="livewireDashboard()" 
     x-init="init()" 
     class="min-h-screen bg-gray-50">
     
    {{-- Real-time data binding with Livewire --}}
    <div wire:poll.30s="loadDashboardData" class="hidden"></div>
    
    {{-- Header Section --}}
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="px-6 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-600 flex items-center justify-center">
                        <i class="fas fa-chart-line text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}'s Analytics</h1>
                        <p class="text-gray-600">Level {{ $stats['level'] ?? 1 }} • {{ number_format($stats['total_points'] ?? 0) }} XP</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <input type="checkbox" wire:model="autoRefresh" id="autoRefresh" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="autoRefresh" class="text-sm text-gray-700">Live Updates</label>
                    </div>
                    
                    <button wire:click="refreshData" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200"
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
        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            {{-- Level Progress Card --}}
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-lg bg-blue-50 flex items-center justify-center">
                        <i class="fas fa-star text-blue-600 text-xl"></i>
                    </div>
                    <span class="text-3xl font-bold text-gray-900">{{ $stats['level'] ?? 1 }}</span>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Level Progress</h3>
                <div class="w-full bg-gray-200 rounded-full h-3 mb-2">
                    <div class="bg-blue-600 h-3 rounded-full transition-all duration-1000" 
                         style="width: {{ $stats['progress_to_next_level'] ?? 0 }}%"></div>
                </div>
                <p class="text-sm text-gray-600">{{ number_format($stats['total_points'] ?? 0) }} XP</p>
            </div>

            {{-- Streak Card --}}
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-lg bg-red-50 flex items-center justify-center">
                        <i class="fas fa-fire text-red-500 text-xl"></i>
                    </div>
                    <span class="text-3xl font-bold text-gray-900">{{ $stats['current_streak'] ?? 0 }}</span>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Current Streak</h3>
                <p class="text-sm text-gray-600">Best: {{ $stats['longest_streak'] ?? 0 }} days</p>
                <div class="mt-3 flex items-center space-x-1">
                    @if(isset($streakData['recent_activity']))
                        @foreach(array_slice($streakData['recent_activity'], -7) as $day)
                            <div class="w-4 h-4 rounded-full {{ $day['active'] ? 'bg-red-500' : 'bg-gray-300' }}"
                                 title="{{ $day['day'] }}: {{ $day['active'] ? 'Active' : 'Inactive' }}">
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            {{-- Energy Card --}}
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-lg bg-green-50 flex items-center justify-center">
                        <i class="fas fa-bolt text-green-600 text-xl"></i>
                    </div>
                    <span class="text-3xl font-bold text-gray-900">{{ $stats['energy']['current'] ?? 100 }}</span>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Energy</h3>
                <div class="w-full bg-gray-200 rounded-full h-3 mb-2">
                    <div class="bg-green-600 h-3 rounded-full transition-all duration-1000" 
                         style="width: {{ $stats['energy']['percentage'] ?? 100 }}%"></div>
                </div>
                <p class="text-xs text-gray-600">
                    @if(($stats['energy']['is_full'] ?? false))
                        <span class="text-green-600 font-medium">⚡ Full Energy!</span>
                    @else
                        Next: {{ $stats['energy']['next_energy_in'] ?? 0 }}m
                    @endif
                </p>
            </div>

            {{-- Currency Card --}}
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-lg bg-yellow-50 flex items-center justify-center">
                        <i class="fas fa-coins text-yellow-600 text-xl"></i>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-bold flex items-center justify-end text-gray-900">
                            <i class="fas fa-coins mr-1 text-yellow-600"></i>
                            {{ number_format($stats['coins'] ?? 0) }}
                        </div>
                        <div class="text-sm flex items-center justify-end text-gray-900">
                            <i class="fas fa-gem mr-1 text-blue-600"></i>
                            {{ number_format($stats['gems'] ?? 0) }}
                        </div>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Currency</h3>
                <p class="text-sm text-gray-600">Visit the store</p>
            </div>
        </div>

        {{-- Main Analytics Section --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            {{-- Interactive Chart --}}
            <div class="lg:col-span-2 bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                {{-- Chart Controls --}}
                <div class="flex flex-wrap items-center justify-between mb-6">
                    <div class="flex space-x-2 mb-2 md:mb-0">
                        <button wire:click="updateMetric('overview')" 
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 {{ $selectedMetric === 'overview' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            Overview
                        </button>
                        <button wire:click="updateMetric('points')" 
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 {{ $selectedMetric === 'points' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            Points
                        </button>
                        <button wire:click="updateMetric('learning_time')" 
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 {{ $selectedMetric === 'learning_time' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            Time
                        </button>
                        <button wire:click="updateMetric('assessments')" 
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 {{ $selectedMetric === 'assessments' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            Scores
                        </button>
                    </div>
                    
                    <select wire:model="selectedTimeframe" 
                            class="bg-white border border-gray-300 text-gray-900 px-4 py-2 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="7d">7 Days</option>
                        <option value="30d">30 Days</option>
                        <option value="90d">90 Days</option>
                        <option value="1y">1 Year</option>
                    </select>
                </div>
                
                {{-- Chart Container --}}
                <div class="bg-gray-50 rounded-lg p-4 h-80">
                    <canvas id="analyticsChart" 
                            class="w-full h-full"
                            x-data="chartComponent({{ json_encode($chartData) }})"
                            x-init="initChart()"></canvas>
                </div>
            </div>

            {{-- Sidebar: Stats & Achievements --}}
            <div class="space-y-6">
                {{-- Quick Stats Panel --}}
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                    <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-trophy text-yellow-500 mr-2"></i>
                        Your Stats
                    </h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Global Rank</span>
                            <span class="font-bold text-gray-900">#{{ $stats['rank'] ?? '---' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Courses Done</span>
                            <span class="font-bold text-gray-900">{{ $stats['courses_completed'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Lessons Done</span>
                            <span class="font-bold text-gray-900">{{ $stats['lessons_completed'] ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Avg Quiz Score</span>
                            <span class="font-bold text-gray-900">{{ round($stats['average_quiz_score'] ?? 0) }}%</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Badges Earned</span>
                            <span class="font-bold text-gray-900">{{ $stats['badges_count'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                {{-- Recent Achievements --}}
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-medal text-yellow-500 mr-2"></i>
                            Recent Achievements
                        </h3>
                        <button wire:click="toggleAchievements" 
                                class="text-gray-500 hover:text-gray-700 transition-colors">
                            <i class="fas fa-chevron-down transition-transform duration-200 {{ $showAchievements ? 'rotate-180' : '' }}"></i>
                        </button>
                    </div>
                    
                    @if(count($achievements) > 0)
                        <div class="space-y-3" @if($showAchievements) wire:transition @endif>
                            @foreach(array_slice($achievements, 0, $showAchievements ? count($achievements) : 3) as $index => $achievement)
                                <div class="p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                                             style="background: {{ $achievement['color'] ?? '#3B82F6' }};">
                                            <i class="{{ $achievement['icon'] ?? 'fas fa-trophy' }} text-white"></i>
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-900">{{ $achievement['name'] }}</h4>
                                            <p class="text-sm text-gray-600">{{ $achievement['description'] }}</p>
                                            <div class="flex items-center space-x-2 mt-1">
                                                <span class="text-xs px-2 py-1 rounded-full
                                                    @switch($achievement['rarity'])
                                                        @case('common') bg-gray-200 text-gray-700 @break
                                                        @case('rare') bg-blue-100 text-blue-700 @break
                                                        @case('epic') bg-indigo-100 text-indigo-700 @break
                                                        @case('legendary') bg-yellow-100 text-yellow-700 @break
                                                        @default bg-gray-200 text-gray-700
                                                    @endswitch">
                                                    {{ ucfirst($achievement['rarity'] ?? 'common') }}
                                                </span>
                                                <span class="text-xs text-green-600">+{{ $achievement['points_reward'] ?? 0 }} XP</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
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
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-chart-pie text-blue-600 mr-2"></i>
                    Learning Progress
                </h3>
                
                <div class="space-y-6">
                    {{-- Course Progress --}}
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600">Courses in Progress</span>
                            <span class="font-bold text-gray-900">{{ $progressData['courses_in_progress'] ?? 0 }}</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-1000"
                                 style="width: {{ min(100, ($progressData['courses_in_progress'] ?? 0) * 20) }}%"></div>
                        </div>
                    </div>

                    {{-- Weekly Completion Rate --}}
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600">Weekly Growth</span>
                            <span class="font-bold flex items-center {{ ($progressData['completion_rate'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-500' }}">
                                <i class="fas fa-arrow-{{ ($progressData['completion_rate'] ?? 0) >= 0 ? 'up' : 'down' }} mr-1"></i>
                                {{ abs($progressData['completion_rate'] ?? 0) }}%
                            </span>
                        </div>
                    </div>

                    {{-- Study Consistency --}}
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600">Study Consistency</span>
                            <span class="font-bold text-gray-900">{{ round($progressData['study_consistency'] ?? 0) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full transition-all duration-1000"
                                 style="width: {{ $progressData['study_consistency'] ?? 0 }}%"></div>
                        </div>
                    </div>

                    {{-- Most Active Course --}}
                    @if(isset($progressData['most_active_course']))
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-semibold text-green-600 mb-2">Most Active Course</h4>
                            <p class="text-sm text-gray-700">{{ $progressData['most_active_course']['title'] ?? 'None' }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Leaderboard --}}
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-ranking-star text-yellow-500 mr-2"></i>
                        Top Learners
                    </h3>
                    <button wire:click="toggleLeaderboard"
                            class="text-gray-500 hover:text-gray-700 transition-colors">
                        <i class="fas fa-expand-alt"></i>
                    </button>
                </div>
                
                @if(count($leaderboard) > 0)
                    <div class="space-y-3">
                        @foreach($leaderboard->take($showLeaderboard ? 10 : 5) as $leader)
                            <div class="p-3 rounded-lg transition-colors duration-200 {{ $leader['is_current_user'] ? 'bg-blue-50 border border-blue-200' : 'bg-gray-50 hover:bg-gray-100' }}">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-white
                                        @if($leader['rank'] === 1) bg-yellow-500
                                        @elseif($leader['rank'] === 2) bg-gray-400
                                        @elseif($leader['rank'] === 3) bg-orange-500
                                        @else bg-gray-500 @endif">
                                        {{ $leader['rank'] }}
                                    </div>
                                    
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2">
                                            <h4 class="font-semibold text-gray-900">{{ $leader['user']['name'] ?? 'Anonymous' }}</h4>
                                            <span class="text-xs px-2 py-1 bg-blue-100 text-blue-700 rounded-full">L{{ $leader['level'] }}</span>
                                        </div>
                                        <div class="flex items-center space-x-4 text-sm text-gray-600">
                                            <span>{{ number_format($leader['points']) }} XP</span>
                                            <span class="flex items-center">
                                                <i class="fas fa-fire text-red-500 mr-1"></i>
                                                {{ $leader['streak'] }}
                                            </span>
                                            <span class="flex items-center">
                                                <i class="fas fa-medal text-yellow-500 mr-1"></i>
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

        {{-- Activity & Performance Section --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            {{-- Activity Heatmap --}}
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-calendar-alt text-red-500 mr-2"></i>
                    Activity Heatmap
                </h3>
                
                <div class="grid grid-cols-7 gap-2 mb-4">
                    @if(isset($streakData['recent_activity']))
                        @foreach($streakData['recent_activity'] as $day)
                            <div class="text-center">
                                <div class="text-xs text-gray-500 mb-1">{{ substr($day['day'], 0, 3) }}</div>
                                <div class="w-full h-12 rounded-lg flex items-center justify-center transition-all duration-200 {{ $day['active'] ? 'bg-red-500 text-white' : 'bg-gray-200' }}"
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
                    <span class="text-gray-500">Less</span>
                    <div class="flex space-x-1">
                        <div class="w-3 h-3 rounded bg-gray-200"></div>
                        <div class="w-3 h-3 rounded bg-red-200"></div>
                        <div class="w-3 h-3 rounded bg-red-300"></div>
                        <div class="w-3 h-3 rounded bg-red-400"></div>
                        <div class="w-3 h-3 rounded bg-red-500"></div>
                    </div>
                    <span class="text-gray-500">More</span>
                </div>

                {{-- Streak Goals --}}
                <div class="p-4 bg-gray-50 rounded-lg">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-gray-600">Next Streak Goal</span>
                        <span class="font-bold text-orange-600">{{ $streakData['streak_goal'] ?? 7 }} days</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-orange-500 h-2 rounded-full transition-all duration-1000"
                             style="width: {{ $streakData['streak_percentage'] ?? 0 }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">{{ $streakData['days_to_goal'] ?? 0 }} days to go</p>
                </div>
            </div>

            {{-- Performance Metrics --}}
            <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-chart-bar text-green-600 mr-2"></i>
                    Performance Metrics
                </h3>
                
                <div class="space-y-6">
                    {{-- Weekly Study Time --}}
                    <div class="relative">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600">Weekly Study Time</span>
                            <span class="font-bold text-gray-900">28h</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-4 relative">
                            <div class="absolute inset-0 bg-blue-600 h-full rounded-full transition-all duration-1000"
                                 style="width: 70%"></div>
                            <div class="absolute inset-0 flex items-center justify-center text-xs font-bold text-white">
                                70%
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Goal: 40 hours/week</p>
                    </div>

                    {{-- Quiz Performance Trend --}}
                    <div class="relative">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600">Quiz Performance</span>
                            <span class="font-bold text-gray-900">{{ round($stats['average_quiz_score'] ?? 0) }}%</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="flex-1 bg-gray-200 rounded-full h-4 relative">
                                <div class="absolute inset-0 bg-green-600 h-full rounded-full transition-all duration-1000"
                                     style="width: {{ $stats['average_quiz_score'] ?? 0 }}%"></div>
                            </div>
                            @if(($stats['average_quiz_score'] ?? 0) >= 90)
                                <div class="text-lg">🏆</div>
                            @elseif(($stats['average_quiz_score'] ?? 0) >= 80)
                                <div class="text-lg">🥇</div>
                            @elseif(($stats['average_quiz_score'] ?? 0) >= 70)
                                <div class="text-lg">🥈</div>
                            @endif
                        </div>
                    </div>

                    {{-- Learning Velocity --}}
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-600">Learning Velocity</span>
                            <span class="font-bold text-gray-900">3.2/day</span>
                        </div>
                        <div class="grid grid-cols-7 gap-1">
                            @foreach(['20', '85', '60', '90', '75', '40', '95'] as $height)
                                <div class="h-8 rounded bg-gray-200 relative overflow-hidden">
                                    <div class="absolute bottom-0 left-0 right-0 bg-green-500 transition-all duration-1000"
                                         style="height: {{ $height }}%"></div>
                                </div>
                            @endforeach
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                            <span>Mon</span><span>Tue</span><span>Wed</span><span>Thu</span><span>Fri</span><span>Sat</span><span>Sun</span>
                        </div>
                    </div>

                    {{-- AI Insights --}}
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center space-x-2 mb-2">
                            <i class="fas fa-brain text-blue-600"></i>
                            <span class="font-semibold text-blue-800">AI Insight</span>
                        </div>
                        <p class="text-sm text-blue-700">Your learning velocity increased 23% this week. JavaScript is your strongest subject with 87% average scores.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Smart Recommendations Section --}}
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-200">
            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                Smart Recommendations
            </h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Study Recommendation --}}
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 hover:bg-blue-100 transition-colors duration-200">
                    <div class="flex items-center space-x-2 mb-3">
                        <i class="fas fa-book-open text-blue-600"></i>
                        <span class="font-semibold text-blue-800">Study Recommendation</span>
                    </div>
                    <p class="text-sm text-blue-700 mb-3">Focus on JavaScript fundamentals - you're 75% through the course!</p>
                    <button class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg transition-colors duration-200">
                        Continue Learning
                    </button>
                </div>

                {{-- Challenge Card --}}
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 hover:bg-orange-100 transition-colors duration-200">
                    <div class="flex items-center space-x-2 mb-3">
                        <i class="fas fa-trophy text-orange-600"></i>
                        <span class="font-semibold text-orange-800">Weekly Challenge</span>
                    </div>
                    <p class="text-sm text-orange-700 mb-3">Complete 5 lessons this week to unlock 500 bonus XP!</p>
                    <button class="w-full bg-orange-600 hover:bg-orange-700 text-white py-2 rounded-lg transition-colors duration-200">
                        Accept Challenge
                    </button>
                </div>

                {{-- Reward Available --}}
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 hover:bg-green-100 transition-colors duration-200">
                    <div class="flex items-center space-x-2 mb-3">
                        <i class="fas fa-gift text-green-600"></i>
                        <span class="font-semibold text-green-800">Reward Available</span>
                    </div>
                    <p class="text-sm text-green-700 mb-3">Daily quest: Complete any lesson for +50 XP and 10 coins!</p>
                    <button class="w-full bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg transition-colors duration-200">
                        Claim Reward
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Achievement Notification --}}
    @if(session()->has('new_achievement'))
        <div class="fixed top-20 right-6 z-50 bg-white border border-gray-200 shadow-lg p-4 rounded-lg max-w-sm">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 rounded-lg bg-yellow-500 flex items-center justify-center">
                    <i class="fas fa-trophy text-white text-xl"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900">New Achievement Unlocked!</h4>
                    <p class="text-sm text-gray-600">{{ session('new_achievement') }}</p>
                </div>
            </div>
            <button onclick="this.parentElement.remove()" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    {{-- Loading Overlay --}}
    <div wire:loading.flex wire:target="loadDashboardData,refreshData" 
         class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
        <div class="bg-white rounded-xl p-8 text-center shadow-xl">
            <i class="fas fa-spinner fa-spin text-4xl text-blue-600 mb-4"></i>
            <p class="text-lg font-semibold text-gray-900">Updating Analytics...</p>
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
                                backgroundColor: 'rgba(255, 255, 255, 0.95)',
                                titleColor: '#1F2937',
                                bodyColor: '#1F2937',
                                borderColor: '#E5E7EB',
                                borderWidth: 1,
                                cornerRadius: 8
                            }
                        },
                        scales: {
                            x: {
                                grid: { color: 'rgba(156, 163, 175, 0.2)' },
                                ticks: { color: '#6B7280' }
                            },
                            y: {
                                grid: { color: 'rgba(156, 163, 175, 0.2)' },
                                ticks: { color: '#6B7280' }
                            }
                        },
                        animation: {
                            duration: 1500,
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
        notification.className = 'fixed top-4 right-4 bg-white border border-gray-200 shadow-lg p-4 rounded-lg z-50 transform translate-x-full transition-transform duration-300';
        notification.innerHTML = `
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 rounded-lg bg-yellow-500 flex items-center justify-center">
                    <i class="fas fa-trophy text-white text-xl"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900">Achievement Unlocked!</h4>
                    <p class="text-sm text-gray-600">${event.detail.name}</p>
                </div>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    });
</script>
@endpush

@push('styles')
<style>
    .chart-container {
        background: #f9fafb;
    }
    
    /* Custom scrollbar for webkit browsers */
    .overflow-auto::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    
    .overflow-auto::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }
    
    .overflow-auto::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }
    
    .overflow-auto::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    
    /* Smooth transitions for interactive elements */
    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        transition-duration: 200ms;
    }
    
    /* Focus styles for accessibility */
    button:focus, 
    select:focus, 
    input:focus {
        outline: 2px solid #3B82F6;
        outline-offset: 2px;
    }
</style>
@endpush
</div>