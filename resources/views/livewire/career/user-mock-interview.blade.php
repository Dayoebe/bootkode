<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-cyan-50">
    <!-- Header Section -->
    <div class="bg-white shadow-lg border-b">
        <div class="container mx-auto px-6 py-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="mb-6 lg:mb-0">
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">Mock Interviews</h1>
                    <p class="text-xl text-gray-600">Practice makes perfect - Ace your next interview</p>
                </div>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-2 lg:grid-cols-6 gap-4">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 text-white text-center">
                        <div class="text-2xl font-bold">{{ $totalInterviews }}</div>
                        <div class="text-sm opacity-90">Total</div>
                    </div>
                    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 text-white text-center">
                        <div class="text-2xl font-bold">{{ $completedInterviews }}</div>
                        <div class="text-sm opacity-90">Completed</div>
                    </div>
                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 text-white text-center">
                        <div class="text-2xl font-bold">{{ number_format($averageScore, 1) }}</div>
                        <div class="text-sm opacity-90">Avg Score</div>
                    </div>
                    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-4 text-white text-center">
                        <div class="text-2xl font-bold">{{ $upcomingInterviews }}</div>
                        <div class="text-sm opacity-90">Upcoming</div>
                    </div>
                    <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl p-4 text-white text-center">
                        <div class="text-2xl font-bold">{{ $streakCount }}</div>
                        <div class="text-sm opacity-90">Streak</div>
                    </div>
                    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl p-4 text-white text-center">
                        <div class="text-2xl font-bold">{{ $improvementRate }}%</div>
                        <div class="text-sm opacity-90">Improvement</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session('message'))
        <div class="container mx-auto px-6 pt-4">
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg relative animate-fade-in">
                <span class="block sm:inline">{{ session('message') }}</span>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="container mx-auto px-6 pt-4">
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg relative animate-fade-in">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <div class="container mx-auto px-6 py-8">
        <!-- Tab Navigation -->
        <div class="flex flex-wrap border-b border-gray-200 mb-8">
            <button wire:click="$set('activeTab', 'dashboard')"
                class="px-6 py-3 text-lg font-medium {{ $activeTab === 'dashboard' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700' }} transition-colors">
                Dashboard
            </button>
            <button wire:click="$set('activeTab', 'interviews')"
                class="px-6 py-3 text-lg font-medium {{ $activeTab === 'interviews' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700' }} transition-colors">
                My Interviews
            </button>
            <button wire:click="$set('activeTab', 'practice')"
                class="px-6 py-3 text-lg font-medium {{ $activeTab === 'practice' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700' }} transition-colors">
                Quick Practice
            </button>
            <button wire:click="$set('activeTab', 'analytics')"
                class="px-6 py-3 text-lg font-medium {{ $activeTab === 'analytics' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700' }} transition-colors">
                Performance Analytics
            </button>
        </div>

        <!-- Dashboard Tab -->
        @if($activeTab === 'dashboard')
            <div class="space-y-8">
                <!-- Quick Actions -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-shadow">
                        <div class="flex items-center mb-4">
                            <div class="bg-blue-100 p-3 rounded-full">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">Create Interview</h3>
                                <p class="text-gray-600 text-sm">Set up a new mock interview</p>
                            </div>
                        </div>
                        <button wire:click="$set('showCreateForm', true)"
                            class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                            Create New
                        </button>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-shadow">
                        <div class="flex items-center mb-4">
                            <div class="bg-green-100 p-3 rounded-full">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">Quick Practice</h3>
                                <p class="text-gray-600 text-sm">Start practicing immediately</p>
                            </div>
                        </div>
                        <button wire:click="$set('activeTab', 'practice')"
                            class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors font-medium">
                            Start Practice
                        </button>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-shadow">
                        <div class="flex items-center mb-4">
                            <div class="bg-purple-100 p-3 rounded-full">
                                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">View Analytics</h3>
                                <p class="text-gray-600 text-sm">Track your progress</p>
                            </div>
                        </div>
                        <button wire:click="$set('activeTab', 'analytics')"
                            class="w-full bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors font-medium">
                            View Reports
                        </button>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 hover:shadow-xl transition-shadow">
                        <div class="flex items-center mb-4">
                            <div class="bg-orange-100 p-3 rounded-full">
                                <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-semibold text-gray-900">Premium Features</h3>
                                <p class="text-gray-600 text-sm">Unlock advanced analytics</p>
                            </div>
                        </div>
                        <button class="w-full bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition-colors font-medium">
                            Upgrade Now
                        </button>
                    </div>
                </div>

                <!-- Recent Interviews -->
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900">Recent Interviews</h3>
                        <button wire:click="$set('activeTab', 'interviews')" class="text-blue-600 hover:text-blue-700 font-medium">
                            View All
                        </button>
                    </div>

                    @if(count($mockInterviews) > 0)
                        <div class="space-y-4">
                            @foreach($mockInterviews->take(3) as $interview)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <div class="bg-{{ $interview->getStatusColor() }}-100 p-2 rounded-lg mr-4">
                                            <span class="text-lg">{{ $interview->getTypeIcon() }}</span>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900">{{ $interview->title }}</h4>
                                            <p class="text-sm text-gray-600">
                                                {{ $interview->type_label }} • {{ $interview->difficulty_label }}
                                                @if($interview->overall_score)
                                                    • Score: {{ number_format($interview->overall_score, 1) }}%
                                                @endif

        <!-- Practice Tab -->
        @if($activeTab === 'practice')
            <div class="space-y-8">
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">Quick Practice Session</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="space-y-4">
                            <label class="block text-sm font-semibold text-gray-700">Interview Type</label>
                            <select wire:model.live="type" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="technical">Technical</option>
                                <option value="behavioral">Behavioral</option>
                                <option value="system_design">System Design</option>
                                <option value="coding">Coding</option>
                                <option value="hr">HR</option>
                            </select>
                        </div>
                        
                        <div class="space-y-4">
                            <label class="block text-sm font-semibold text-gray-700">Difficulty Level</label>
                            <select wire:model.live="difficulty_level" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                                <option value="expert">Expert</option>
                            </select>
                        </div>
                        
                        <div class="space-y-4">
                            <label class="block text-sm font-semibold text-gray-700">Format</label>
                            <select wire:model.live="format" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="text">Text Only</option>
                                <option value="voice">Voice Recording</option>
                                <option value="video">Video Recording</option>
                                <option value="mixed">Mixed Format</option>
                            </select>
                        </div>
                    </div>

                    <div class="text-center">
                        <button wire:click="startQuickPractice" 
                            class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-8 py-4 rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all font-semibold text-lg shadow-lg">
                            Start Practice Session
                        </button>
                    </div>
                </div>

                <!-- Sample Questions Preview -->
                <div class="bg-white rounded-2xl shadow-lg p-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Sample Questions for {{ ucfirst($type) }} Interview</h3>
                    <div class="space-y-4">
                        @php
                            $sampleQuestions = match($type) {
                                'technical' => $technicalQuestions,
                                'behavioral' => $behavioralQuestions,
                                'system_design' => $systemDesignQuestions,
                                default => $technicalQuestions
                            };
                        @endphp
                        @foreach(array_slice($sampleQuestions, 0, 5) as $index => $question)
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-start">
                                    <span class="bg-blue-100 text-blue-600 text-sm font-medium px-2 py-1 rounded-full mr-3 mt-0.5">{{ $index + 1 }}</span>
                                    <p class="text-gray-700">{{ $question }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Analytics Tab -->
        @if($activeTab === 'analytics')
            <div class="space-y-8">
                <!-- Performance Overview -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <div class="flex items-center">
                            <div class="bg-blue-100 p-3 rounded-full">
                                <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-600">Overall Performance</p>
                                <p class="text-2xl font-bold text-gray-900">{{ number_format($averageScore, 1) }}%</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <div class="flex items-center">
                            <div class="bg-green-100 p-3 rounded-full">
                                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-600">Improvement Rate</p>
                                <p class="text-2xl font-bold text-gray-900">+{{ $improvementRate }}%</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <div class="flex items-center">
                            <div class="bg-purple-100 p-3 rounded-full">
                                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-600">Avg Response Time</p>
                                <p class="text-2xl font-bold text-gray-900">2.5m</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <div class="flex items-center">
                            <div class="bg-orange-100 p-3 rounded-full">
                                <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm text-gray-600">Current Streak</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $streakCount }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Analytics -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Score Breakdown -->
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">Score Breakdown</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Technical Skills</span>
                                <div class="flex items-center">
                                    <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: 85%"></div>
                                    </div>
                                    <span class="font-semibold text-gray-900">85%</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Communication</span>
                                <div class="flex items-center">
                                    <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                        <div class="bg-green-600 h-2 rounded-full" style="width: 78%"></div>
                                    </div>
                                    <span class="font-semibold text-gray-900">78%</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Problem Solving</span>
                                <div class="flex items-center">
                                    <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                        <div class="bg-purple-600 h-2 rounded-full" style="width: 92%"></div>
                                    </div>
                                    <span class="font-semibold text-gray-900">92%</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Confidence</span>
                                <div class="flex items-center">
                                    <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                        <div class="bg-orange-600 h-2 rounded-full" style="width: 72%"></div>
                                    </div>
                                    <span class="font-semibold text-gray-900">72%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Interview Type Performance -->
                    <div class="bg-white rounded-2xl shadow-lg p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">Performance by Interview Type</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Technical ({{ $mockInterviews->where('type', 'technical')->where('status', 'completed')->count() }})</span>
                                <span class="font-semibold text-blue-600">87%</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Behavioral ({{ $mockInterviews->where('type', 'behavioral')->where('status', 'completed')->count() }})</span>
                                <span class="font-semibold text-green-600">82%</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">System Design ({{ $mockInterviews->where('type', 'system_design')->where('status', 'completed')->count() }})</span>
                                <span class="font-semibold text-purple-600">75%</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Coding ({{ $mockInterviews->where('type', 'coding')->where('status', 'completed')->count() }})</span>
                                <span class="font-semibold text-orange-600">89%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Premium Analytics (if available) -->
                @if($aiAnalysisEnabled)
                    <div class="bg-white rounded-2xl shadow-lg p-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">AI-Powered Insights</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="text-center">
                                <div class="bg-blue-100 p-4 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <h4 class="font-semibold text-gray-900 mb-2">Speech Analysis</h4>
                                <p class="text-sm text-gray-600">Your speaking pace and clarity have improved by 15% in the last month.</p>
                            </div>
                            
                            <div class="text-center">
                                <div class="bg-green-100 p-4 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m-6-4h8m-6-4h8m-6-8h8"/>
                                    </svg>
                                </div>
                                <h4 class="font-semibold text-gray-900 mb-2">Emotion Tracking</h4>
                                <p class="text-sm text-gray-600">Confidence levels are consistently high, with reduced anxiety indicators.</p>
                            </div>
                            
                            <div class="text-center">
                                <div class="bg-purple-100 p-4 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </div>
                                <h4 class="font-semibold text-gray-900 mb-2">Eye Contact</h4>
                                <p class="text-sm text-gray-600">Maintaining good eye contact 78% of the time during video interviews.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Create/Edit Interview Modal -->
    @if($showCreateForm)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 overflow-y-auto">
            <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
                <div class="flex justify-between items-center p-6 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                    <h2 class="text-2xl font-bold text-gray-900">
                        {{ $editingInterviewId ? 'Edit Interview' : 'Create New Interview' }}
                    </h2>
                    <button wire:click="resetForm" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="createInterview" class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <!-- Interview Title -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Interview Title *</label>
                                <input wire:model="title" type="text" placeholder="e.g., Frontend Developer Technical Interview"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('title') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Type & Format -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Interview Type *</label>
                                    <select wire:model.live="type" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="technical">Technical</option>
                                        <option value="behavioral">Behavioral</option>
                                        <option value="case_study">Case Study</option>
                                        <option value="system_design">System Design</option>
                                        <option value="coding">Coding</option>
                                        <option value="hr">HR</option>
                                        <option value="custom">Custom</option>
                                    </select>
                                    @error('type') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Format *</label>
                                    <select wire:model="format" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="text">Text Only</option>
                                        <option value="voice">Voice Recording</option>
                                        <option value="video">Video Recording</option>
                                        <option value="mixed">Mixed Format</option>
                                    </select>
                                    @error('format') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <!-- Difficulty & Duration -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Difficulty Level *</label>
                                    <select wire:model="difficulty_level" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="beginner">Beginner</option>
                                        <option value="intermediate">Intermediate</option>
                                        <option value="advanced">Advanced</option>
                                        <option value="expert">Expert</option>
                                    </select>
                                    @error('difficulty_level') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Duration (minutes) *</label>
                                    <input wire:model="estimated_duration_minutes" type="number" min="15" max="180" step="15"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    @error('estimated_duration_minutes') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <!-- Job Details -->
                            <div class="grid grid-cols-1 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Job Role</label>
                                    <input wire:model="job_role" type="text" placeholder="e.g., Senior Frontend Developer"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    @error('job_role') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Industry</label>
                                        <input wire:model="industry" type="text" placeholder="e.g., Technology"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        @error('industry') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Company Type</label>
                                        <input wire:model="company_type" type="text" placeholder="e.g., Startup"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        @error('company_type') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Scheduling -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Schedule For (Optional)</label>
                                <input wire:model="scheduled_at" type="datetime-local"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('scheduled_at') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-6">
                            <!-- Description -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                                <textarea wire:model="description" rows="4" placeholder="Describe the interview focus and objectives..."
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                                @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            </div>

                            <!-- Course Association -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Associated Course (Optional)</label>
                                <select wire:model="course_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">No Course Association</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Premium Features -->
                            @if($aiAnalysisEnabled)
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-4">Premium Features</label>
                                    <div class="space-y-3">
                                        @foreach(['ai_feedback', 'video_recording', 'detailed_analytics', 'custom_questions', 'unlimited_retakes'] as $feature)
                                            <label class="flex items-center">
                                                <input type="checkbox" wire:click="togglePremiumFeature('{{ $feature }}')"
                                                    {{ in_array($feature, $premium_features) ? 'checked' : '' }}
                                                    class="w-5 h-5 text-blue-600 bg-white border-gray-300 rounded focus:ring-blue-500">
                                                <span class="ml-3 text-sm text-gray-700">
                                                    {{ str_replace('_', ' ', ucwords($feature)) }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Custom Questions -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Custom Questions</label>
                                
                                <!-- Add Question Form -->
                                <div class="flex space-x-2 mb-4">
                                    <input wire:model="newQuestion" type="text" placeholder="Enter your custom question..."
                                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <select wire:model="questionType" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="behavioral">Behavioral</option>
                                        <option value="technical">Technical</option>
                                        <option value="situational">Situational</option>
                                    </select>
                                    <button type="button" wire:click="addCustomQuestion"
                                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                        Add
                                    </button>
                                </div>

                                <!-- Custom Questions List -->
                                @if(count($custom_questions) > 0)
                                    <div class="space-y-2 max-h-40 overflow-y-auto">
                                        @foreach($custom_questions as $index => $question)
                                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                                <div class="flex-1">
                                                    <p class="text-sm text-gray-700">{{ $question['question'] }}</p>
                                                    <span class="text-xs text-gray-500">{{ ucfirst($question['type']) }}</span>
                                                </div>
                                                <button type="button" wire:click="removeCustomQuestion({{ $index }})"
                                                    class="text-red-600 hover:text-red-700 p-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                        <button type="button" wire:click="resetForm"
                            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                            class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-8 py-3 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all font-semibold shadow-lg">
                            <span wire:loading.remove wire:target="createInterview">
                                {{ $editingInterviewId ? 'Update Interview' : 'Create Interview' }}
                            </span>
                            <span wire:loading wire:target="createInterview">
                                <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Saving...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
                
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="px-3 py-1 text-sm rounded-full bg-{{ $interview->getStatusColor() }}-100 text-{{ $interview->getStatusColor() }}-800">
                                            {{ $interview->status_label }}
                                        </span>
                                        @if($interview->isScheduled())
                                            <button wire:click="startInterview({{ $interview->id }})"
                                                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                                Start
                                            </button>
                                        @elseif($interview->isCompleted())
                                            <button wire:click="viewResults({{ $interview->id }})"
                                                class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors text-sm">
                                                Results
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500 mb-4">No interviews yet. Create your first mock interview to get started!</p>
                            <button wire:click="$set('showCreateForm', true)" 
                                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                Create Interview
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <!-- Interviews Tab -->
        @if($activeTab === 'interviews')
            <div class="space-y-6">
                <!-- Action Bar -->
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                    <div class="flex flex-wrap items-center gap-4">
                        <button wire:click="$set('showCreateForm', true)"
                            class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-3 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all font-semibold shadow-lg">
                            Create New Interview
                        </button>
                    </div>

                    <!-- Search and Filters -->
                    <div class="flex flex-wrap items-center gap-4">
                        <input wire:model.live.debounce.300ms="searchTerm" type="text" placeholder="Search interviews..."
                            class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent w-64">
                        
                        <select wire:model.live="filterType" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Types</option>
                            <option value="technical">Technical</option>
                            <option value="behavioral">Behavioral</option>
                            <option value="case_study">Case Study</option>
                            <option value="system_design">System Design</option>
                            <option value="coding">Coding</option>
                            <option value="hr">HR</option>
                        </select>

                        <select wire:model.live="filterStatus" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">All Status</option>
                            <option value="scheduled">Scheduled</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>

                <!-- Interviews Grid -->
                @if(count($mockInterviews) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($mockInterviews as $interview)
                            <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100">
                                <!-- Header -->
                                <div class="p-6 border-b border-gray-100">
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex items-center">
                                            <span class="text-2xl mr-3">{{ $interview->getTypeIcon() }}</span>
                                            <div>
                                                <h3 class="text-lg font-bold text-gray-900 line-clamp-2">{{ $interview->title }}</h3>
                                                <p class="text-sm text-gray-600">{{ $interview->type_label }}</p>
                                            </div>
                                        </div>
                                        <span class="px-3 py-1 text-sm rounded-full bg-{{ $interview->getStatusColor() }}-100 text-{{ $interview->getStatusColor() }}-800">
                                            {{ $interview->status_label }}
                                        </span>
                                    </div>

                                    @if($interview->description)
                                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ $interview->description }}</p>
                                    @endif

                                    <!-- Meta Information -->
                                    <div class="space-y-2 text-sm text-gray-600">
                                        <div class="flex items-center justify-between">
                                            <span>Difficulty:</span>
                                            <span class="font-medium px-2 py-1 rounded bg-{{ $interview->getDifficultyColor() }}-100 text-{{ $interview->getDifficultyColor() }}-800">
                                                {{ $interview->difficulty_label }}
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span>Duration:</span>
                                            <span class="font-medium">{{ $interview->duration_formatted }}</span>
                                        </div>
                                        @if($interview->job_role)
                                            <div class="flex items-center justify-between">
                                                <span>Role:</span>
                                                <span class="font-medium">{{ $interview->job_role }}</span>
                                            </div>
                                        @endif
                                        @if($interview->overall_score)
                                            <div class="flex items-center justify-between">
                                                <span>Score:</span>
                                                <span class="font-bold text-{{ $interview->overall_score >= 80 ? 'green' : ($interview->overall_score >= 60 ? 'yellow' : 'red') }}-600">
                                                    {{ number_format($interview->overall_score, 1) }}%
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="p-6">
                                    <div class="flex space-x-2">
                                        @if($interview->isScheduled())
                                            <button wire:click="startInterview({{ $interview->id }})"
                                                class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                                Start Interview
                                            </button>
                                        @elseif($interview->isCompleted())
                                            <button wire:click="viewResults({{ $interview->id }})"
                                                class="flex-1 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors font-medium">
                                                View Results
                                            </button>
                                            @if($interview->allow_retakes && $interview->retake_count < $interview->max_retakes)
                                                <button wire:click="retakeInterview({{ $interview->id }})"
                                                    class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
                                                    Retake
                                                </button>
                                            @endif
                                        @endif

                                        <div class="flex space-x-1">
                                            <button wire:click="editInterview({{ $interview->id }})"
                                                class="p-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>

                                            <button wire:click="deleteInterview({{ $interview->id }})" 
                                                wire:confirm="Are you sure you want to delete this interview?"
                                                class="p-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-16">
                        <div class="mx-auto w-32 h-32 bg-gradient-to-br from-blue-100 to-purple-100 rounded-full flex items-center justify-center mb-6">
                            <svg class="w-16 h-16 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-semibold text-gray-700 mb-2">No interviews yet</h3>
                        <p class="text-gray-500 mb-6">Create your first mock interview to start practicing</p>
                        <button wire:click="$set('showCreateForm', true)"
                            class="bg-blue-600 text-white px-8 py-3 rounded-xl hover:bg-blue-700 transition-colors font-semibold">
                            Create Your First Interview
                        </button>
                    </div>
                @endif
            </div>
        @endif








































    <!-- Interview Taking Modal -->
    @if($showInterviewModal && $currentInterview)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
            <!-- Interview Header -->
            <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-purple-50">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ $currentInterview->title }}</h2>
                        <p class="text-gray-600">Question {{ $currentQuestionIndex + 1 }} of {{ count($currentInterview->questions) }}</p>
                    </div>
                    <div class="text-right">
                        <div class="bg-blue-100 px-4 py-2 rounded-lg">
                            <span class="text-blue-800 font-semibold" id="timer">{{ $timeRemaining }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Progress Bar -->
                <div class="mt-4">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                             style="width: {{ (($currentQuestionIndex) / count($currentInterview->questions)) * 100 }}%"></div>
                    </div>
                </div>
            </div>

            <div class="p-6 overflow-y-auto max-h-[calc(90vh-200px)]">
                @if($currentQuestionIndex < count($currentInterview->questions))
                    <!-- Current Question -->
                    <div class="mb-8">
                        <div class="bg-gray-50 p-6 rounded-xl mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Question {{ $currentQuestionIndex + 1 }}</h3>
                            <p class="text-gray-700 text-lg">{{ $currentInterview->questions[$currentQuestionIndex]['question'] }}</p>
                        </div>

                        <!-- Answer Input -->
                        <div class="space-y-4">
                            @if($currentInterview->format === 'text')
                                <textarea wire:model="currentAnswer" 
                                    placeholder="Type your answer here..."
                                    class="w-full h-40 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                    autofocus></textarea>
                            @elseif($currentInterview->format === 'voice')
                                <div class="text-center py-8">
                                    <button class="bg-red-600 text-white px-8 py-4 rounded-full hover:bg-red-700 transition-colors">
                                        <svg class="w-8 h-8 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                                        </svg>
                                        Start Recording
                                    </button>
                                    <p class="text-gray-500 mt-4">Click to start voice recording</p>
                                </div>
                            @elseif($currentInterview->format === 'video')
                                <div class="text-center py-8">
                                    <div class="bg-gray-200 rounded-xl p-8 mb-4">
                                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                        <p class="text-gray-600">Video recording will start here</p>
                                    </div>
                                    <button class="bg-red-600 text-white px-8 py-4 rounded-full hover:bg-red-700 transition-colors">
                                        Start Video Recording
                                    </button>
                                </div>
                            @endif

                            <!-- Recording Controls (if applicable) -->
                            @if(in_array($currentInterview->format, ['voice', 'video']))
                                <div class="flex justify-center space-x-4 mt-4">
                                    <button class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                                        Pause
                                    </button>
                                    <button class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                        Resume
                                    </button>
                                    <button class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                        Stop & Continue
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex justify-between pt-6 border-t border-gray-200">
                    <button wire:click="completeInterview" 
                        class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors">
                        End Interview
                    </button>
                    
                    <div class="space-x-4">
                        @if($currentQuestionIndex > 0)
                            <button class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition-colors">
                                Previous
                            </button>
                        @endif
                        
                        <button wire:click="submitAnswer"
                            class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition-colors font-semibold"
                            {{ empty($currentAnswer) && $currentInterview->format === 'text' ? 'disabled' : '' }}>
                            {{ $currentQuestionIndex === count($currentInterview->questions) - 1 ? 'Finish Interview' : 'Next Question' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Results Modal -->
@if($showResultsModal && $currentInterview)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 overflow-y-auto">
        <div class="bg-white rounded-2xl max-w-6xl w-full max-h-[90vh] overflow-hidden">
            <!-- Results Header -->
            <div class="p-6 border-b border-gray-200 bg-gradient-to-r from-green-50 to-emerald-50">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Interview Results</h2>
                        <p class="text-gray-600">{{ $currentInterview->title }}</p>
                    </div>
                    <button wire:click="$set('showResultsModal', false)" 
                        class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="overflow-y-auto max-h-[calc(90vh-120px)]">
                <div class="p-6">
                    <!-- Overall Score -->
                    <div class="text-center mb-8">
                        <div class="inline-flex items-center justify-center w-32 h-32 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full text-white mb-4">
                            <span class="text-3xl font-bold">{{ number_format($currentInterview->overall_score ?? 0, 1) }}%</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $currentInterview->overall_rating }}</h3>
                        <p class="text-gray-600">Overall Performance</p>
                    </div>

                    <!-- Score Breakdown -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <div class="bg-blue-50 rounded-xl p-4 text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ number_format($currentInterview->technical_score ?? 0, 1) }}%</div>
                            <div class="text-sm text-blue-800">Technical Skills</div>
                        </div>
                        <div class="bg-green-50 rounded-xl p-4 text-center">
                            <div class="text-2xl font-bold text-green-600">{{ number_format($currentInterview->communication_score ?? 0, 1) }}%</div>
                            <div class="text-sm text-green-800">Communication</div>
                        </div>
                        <div class="bg-purple-50 rounded-xl p-4 text-center">
                            <div class="text-2xl font-bold text-purple-600">{{ number_format($currentInterview->confidence_score ?? 0, 1) }}%</div>
                            <div class="text-sm text-purple-800">Confidence</div>
                        </div>
                        <div class="bg-orange-50 rounded-xl p-4 text-center">
                            <div class="text-2xl font-bold text-orange-600">{{ number_format($currentInterview->problem_solving_score ?? 0, 1) }}%</div>
                            <div class="text-sm text-orange-800">Problem Solving</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- AI Feedback -->
                        @if($currentInterview->ai_feedback)
                            <div class="bg-white border border-gray-200 rounded-xl p-6">
                                <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                    </svg>
                                    AI-Powered Insights
                                </h4>
                                
                                <div class="space-y-4">
                                    @if(isset($currentInterview->ai_feedback['overall_feedback']))
                                        <div>
                                            <h5 class="font-semibold text-gray-900 mb-2">Overall Feedback</h5>
                                            <p class="text-gray-700 text-sm">{{ $currentInterview->ai_feedback['overall_feedback'] }}</p>
                                        </div>
                                    @endif

                                    @if(isset($currentInterview->ai_feedback['strengths']))
                                        <div>
                                            <h5 class="font-semibold text-green-800 mb-2">Strengths</h5>
                                            <ul class="space-y-1">
                                                @foreach($currentInterview->ai_feedback['strengths'] as $strength)
                                                    <li class="flex items-start">
                                                        <svg class="w-4 h-4 text-green-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                        </svg>
                                                        <span class="text-sm text-gray-700">{{ $strength }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    @if(isset($currentInterview->ai_feedback['areas_for_improvement']))
                                        <div>
                                            <h5 class="font-semibold text-orange-800 mb-2">Areas for Improvement</h5>
                                            <ul class="space-y-1">
                                                @foreach($currentInterview->ai_feedback['areas_for_improvement'] as $improvement)
                                                    <li class="flex items-start">
                                                        <svg class="w-4 h-4 text-orange-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                                        </svg>
                                                        <span class="text-sm text-gray-700">{{ $improvement }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Recommendations -->
                        <div class="bg-white border border-gray-200 rounded-xl p-6">
                            <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                Recommendations
                            </h4>
                            
                            @if($currentInterview->improvement_suggestions)
                                <ul class="space-y-3">
                                    @foreach($currentInterview->improvement_suggestions as $suggestion)
                                        <li class="flex items-start">
                                            <div class="bg-purple-100 p-1 rounded-full mr-3 mt-1">
                                                <svg class="w-3 h-3 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                            <span class="text-sm text-gray-700">{{ $suggestion }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="text-center py-4">
                                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                    </svg>
                                    <p class="text-gray-500 text-sm">Recommendations will be generated with premium AI analysis</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Performance Metrics -->
                    @if($currentInterview->detailed_analytics_enabled)
                        <div class="mt-8 bg-gray-50 rounded-xl p-6">
                            <h4 class="text-lg font-bold text-gray-900 mb-4">Detailed Performance Metrics</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-blue-600">{{ number_format($currentInterview->avg_response_time ?? 0, 1) }}s</div>
                                    <div class="text-sm text-gray-600">Avg Response Time</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-green-600">{{ $currentInterview->completion_rate ?? 0 }}%</div>
                                    <div class="text-sm text-gray-600">Completion Rate</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-purple-600">{{ $currentInterview->pause_count ?? 0 }}</div>
                                    <div class="text-sm text-gray-600">Pause Count</div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="mt-8 flex flex-wrap justify-center gap-4">
                        @if($currentInterview->allow_retakes && $currentInterview->retake_count < $currentInterview->max_retakes)
                            <button wire:click="retakeInterview({{ $currentInterview->id }})"
                                class="bg-orange-600 text-white px-6 py-3 rounded-lg hover:bg-orange-700 transition-colors font-medium">
                                Retake Interview
                            </button>
                        @endif
                        
                        <button class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                            Download Report
                        </button>
                        
                        <button class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors font-medium">
                            Share Results
                        </button>
                        
                        <button wire:click="$set('showResultsModal', false)"
                            class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors font-medium">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<style>
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in { animation: fade-in 0.3s ease-out; }
    .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
</style>

<script>
    // Timer functionality for interview
    let interviewTimer;
    let timeRemaining = 3600; // Default 1 hour

    function startInterviewTimer(duration) {
        timeRemaining = duration;
        interviewTimer = setInterval(function() {
            timeRemaining--;
            updateTimerDisplay();
            
            if (timeRemaining <= 0) {
                clearInterval(interviewTimer);
                // Auto-submit interview
                @this.call('completeInterview');
            }
        }, 1000);
    }

    function updateTimerDisplay() {
        const minutes = Math.floor(timeRemaining / 60);
        const seconds = timeRemaining % 60;
        const display = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        const timerElement = document.getElementById('timer');
        if (timerElement) {
            timerElement.textContent = display;
        }
    }

    // Listen for Livewire events
    document.addEventListener('livewire:init', () => {
        Livewire.on('interview-started', (data) => {
            startInterviewTimer(data.duration * 60);
        });
        
        Livewire.on('interview-completed', () => {
            if (interviewTimer) {
                clearInterval(interviewTimer);
            }
        });
    });
</script>
</div>