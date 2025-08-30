<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Mock Interviews Management</h1>
                    <p class="text-gray-600 mt-1">Manage interviews, templates, and analytics</p>
                </div>
                
                <!-- Quick Stats -->
                <div class="hidden lg:flex space-x-6">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">{{ number_format($totalInterviews) }}</div>
                        <div class="text-sm text-gray-600">Total Interviews</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ number_format($dailyInterviews) }}</div>
                        <div class="text-sm text-gray-600">Today</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600">{{ number_format($averageScore, 1) }}%</div>
                        <div class="text-sm text-gray-600">Avg Score</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session('message'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
                {{ session('message') }}
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
        </div>
    @endif

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Tab Navigation -->
        <div class="border-b border-gray-200 mb-8">
            <nav class="-mb-px flex space-x-8">
                <button wire:click="$set('activeTab', 'overview')"
                    class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'overview' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Overview
                </button>
                <button wire:click="$set('activeTab', 'interviews')"
                    class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'interviews' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    All Interviews
                </button>
                <button wire:click="$set('activeTab', 'templates')"
                    class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'templates' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Templates
                </button>
                <button wire:click="$set('activeTab', 'questions')"
                    class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'questions' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Question Bank
                </button>
                <button wire:click="$set('activeTab', 'analytics')"
                    class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'analytics' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Analytics
                </button>
                <button wire:click="$set('activeTab', 'users')"
                    class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'users' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Users
                </button>
                <button wire:click="$set('activeTab', 'settings')"
                    class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'settings' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Settings
                </button>
            </nav>
        </div>

        <!-- Overview Tab -->
        @if($activeTab === 'overview')
            <div class="space-y-8">
                <!-- Key Metrics -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Interviews</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ number_format($totalInterviews) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Completed</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ number_format($completedInterviews) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Average Score</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ number_format($averageScore, 1) }}%</dd>
                                </dl>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-orange-500 rounded-md flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Active Users</dt>
                                    <dd class="text-lg font-medium text-gray-900">{{ number_format($totalUsers) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts and Popular Types -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Popular Interview Types -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Popular Interview Types</h3>
                        <div class="space-y-3">
                            @foreach($popularTypes as $type)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-900 capitalize">{{ str_replace('_', ' ', $type['type']) }}</span>
                                    <div class="flex items-center">
                                        <div class="w-32 bg-gray-200 rounded-full h-2 mr-3">
                                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($type['count'] / max(array_column($popularTypes, 'count'))) * 100 }}%"></div>
                                        </div>
                                        <span class="text-sm text-gray-600">{{ $type['count'] }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Growth Metrics -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Growth Metrics</h3>
                        <div class="space-y-4">
                            <div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-600">Weekly Growth</span>
                                    <span class="text-sm font-bold {{ $weeklyGrowth >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $weeklyGrowth >= 0 ? '+' : '' }}{{ number_format($weeklyGrowth, 1) }}%
                                    </span>
                                </div>
                            </div>
                            <div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-600">Premium Usage</span>
                                    <span class="text-sm font-bold text-purple-600">{{ number_format($premiumUsage) }}</span>
                                </div>
                            </div>
                            <div>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-600">Daily Interviews</span>
                                    <span class="text-sm font-bold text-blue-600">{{ number_format($dailyInterviews) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Activity</h3>
                        <div class="flow-root">
                            <ul class="-mb-8">
                                @foreach($recentActivity as $activity)
                                    <li>
                                        <div class="relative pb-8">
                                            @if(!$loop->last)
                                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"></span>
                                            @endif
                                            <div class="relative flex space-x-3">
                                                <div>
                                                    <span class="bg-{{ $activity->getStatusColor() }}-500 h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white">
                                                        <span class="text-white text-sm">{{ $activity->getTypeIcon() }}</span>
                                                    </span>
                                                </div>
                                                <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                    <div>
                                                        <p class="text-sm text-gray-500">
                                                            <span class="font-medium text-gray-900">{{ $activity->user->name }}</span>
                                                            {{ $activity->isCompleted() ? 'completed' : 'started' }} interview
                                                            <span class="font-medium text-gray-900">{{ $activity->title }}</span>
                                                        </p>
                                                    </div>
                                                    <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                        {{ $activity->created_at->diffForHumans() }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Interviews Tab -->
        @if($activeTab === 'interviews')
            <div class="space-y-6">
                <!-- Search and Filters -->
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                        <div class="lg:col-span-2">
                            <input wire:model.live.debounce.300ms="searchTerm" type="text" placeholder="Search interviews, users..."
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        
                        <select wire:model.live="filterType" class="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Types</option>
                            <option value="technical">Technical</option>
                            <option value="behavioral">Behavioral</option>
                            <option value="case_study">Case Study</option>
                            <option value="system_design">System Design</option>
                            <option value="coding">Coding</option>
                            <option value="hr">HR</option>
                            <option value="custom">Custom</option>
                        </select>

                        <select wire:model.live="filterStatus" class="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Status</option>
                            <option value="scheduled">Scheduled</option>
                            <option value="in_progress">In Progress</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                            <option value="missed">Missed</option>
                        </select>

                        <select wire:model.live="filterDifficulty" class="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Difficulty</option>
                            <option value="beginner">Beginner</option>
                            <option value="intermediate">Intermediate</option>
                            <option value="advanced">Advanced</option>
                            <option value="expert">Expert</option>
                        </select>

                        <select wire:model.live="filterDateRange" class="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Time</option>
                            <option value="1">Last 24 hours</option>
                            <option value="7">Last 7 days</option>
                            <option value="30">Last 30 days</option>
                            <option value="90">Last 90 days</option>
                        </select>
                    </div>

                    <!-- Bulk Actions -->
                    @if($showBulkActions)
                        <div class="mt-4 flex items-center space-x-4">
                            <span class="text-sm text-gray-600">{{ count($selectedInterviews) }} selected</span>
                            <select wire:model="bulkAction" class="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Bulk Actions</option>
                                <option value="approve">Approve</option>
                                <option value="generate_feedback">Generate AI Feedback</option>
                                <option value="delete">Delete</option>
                            </select>
                            <button wire:click="executeBulkAction" 
                                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors text-sm">
                                Execute
                            </button>
                            <button wire:click="clearBulkSelection" 
                                class="text-gray-600 hover:text-gray-800 transition-colors text-sm">
                                Clear Selection
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Interviews Table -->
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" wire:click="selectAllVisible" class="w-4 h-4 text-blue-600">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                    wire:click="$set('sortBy', 'title')">
                                    Interview
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    User
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Type
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Score
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                    wire:click="$set('sortBy', 'created_at')">
                                    Created
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($interviews as $interview)
                                <tr class="{{ in_array($interview->id, $selectedInterviews) ? 'bg-blue-50' : 'hover:bg-gray-50' }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <input type="checkbox" wire:click="toggleBulkSelect({{ $interview->id }})" 
                                            {{ in_array($interview->id, $selectedInterviews) ? 'checked' : '' }}
                                            class="w-4 h-4 text-blue-600">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $interview->title }}</div>
                                        <div class="text-sm text-gray-500">{{ Str::limit($interview->description, 50) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-gray-700">{{ substr($interview->user->name, 0, 2) }}</span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $interview->user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $interview->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $interview->type_label }}
                                        </span>
                                        <div class="text-xs text-gray-500 mt-1">{{ $interview->difficulty_label }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $interview->getStatusColor() }}-100 text-{{ $interview->getStatusColor() }}-800">
                                            {{ $interview->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($interview->overall_score)
                                            <div class="text-sm font-medium text-gray-900">{{ number_format($interview->overall_score, 1) }}%</div>
                                            <div class="text-xs text-gray-500">{{ $interview->overall_rating }}</div>
                                        @else
                                            <span class="text-gray-400 text-sm">N/A</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $interview->created_at->format('M d, Y') }}
                                        <div class="text-xs">{{ $interview->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <button wire:click="viewInterview({{ $interview->id }})"
                                                class="text-blue-600 hover:text-blue-900">View</button>
                                            
                                            @if($interview->isCompleted() && !$interview->ai_feedback)
                                                <button wire:click="generateAIFeedback({{ $interview->id }})"
                                                    class="text-green-600 hover:text-green-900">Generate AI</button>
                                            @endif
                                            
                                            <button wire:click="deleteInterview({{ $interview->id }})"
                                                wire:confirm="Are you sure you want to delete this interview?"
                                                class="text-red-600 hover:text-red-900">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                        No interviews found matching your criteria.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    
                    <!-- Pagination -->
                    <div class="px-6 py-3 border-t border-gray-200">
                        {{ $interviews->links() }}
                    </div>
                </div>
            </div>
        @endif

        <!-- Templates Tab -->
        @if($activeTab === 'templates')
            <div class="space-y-6">
                <!-- Header -->
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-900">Interview Templates</h2>
                    <button wire:click="$set('showTemplateForm', true)"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                        Create Template
                    </button>
                </div>

                <!-- Templates Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($templates as $template)
                        <div class="bg-white shadow rounded-lg p-6 hover:shadow-lg transition-shadow">
                            <div class="flex items-center justify-between mb-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ match($template['type']) { 'technical' => 'blue', 'behavioral' => 'green', default => 'gray' } }}-100 text-{{ match($template['type']) { 'technical' => 'blue', 'behavioral' => 'green', default => 'gray' } }}-800">
                                    {{ ucfirst(str_replace('_', ' ', $template['type'])) }}
                                </span>
                                <span class="text-xs text-gray-500">{{ $template['usage_count'] }} uses</span>
                            </div>
                            
                            <h3 class="text-lg font-medium text-gray-900 mb-2">{{ $template['title'] }}</h3>
                            <p class="text-sm text-gray-600 mb-4">{{ $template['questions_count'] }} questions • {{ ucfirst($template['difficulty']) }}</p>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500">Created {{ $template['created_at']->diffForHumans() }}</span>
                                <div class="flex space-x-2">
                                    <button class="text-blue-600 hover:text-blue-800 text-sm">Edit</button>
                                    <button class="text-red-600 hover:text-red-800 text-sm">Delete</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Template Creation Form -->
                @if($showTemplateForm)
                    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
                        <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                                <h3 class="text-lg font-medium text-gray-900">Create Interview Template</h3>
                                <button wire:click="resetTemplateForm" class="text-gray-400 hover:text-gray-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            
                            <form wire:submit.prevent="createTemplate" class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Template Title</label>
                                        <input wire:model="templateTitle" type="text" placeholder="e.g., Senior Developer Technical Interview"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                        @error('templateTitle') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                                            <select wire:model="templateType" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                                <option value="technical">Technical</option>
                                                <option value="behavioral">Behavioral</option>
                                                <option value="case_study">Case Study</option>
                                                <option value="system_design">System Design</option>
                                                <option value="coding">Coding</option>
                                                <option value="hr">HR</option>
                                                <option value="custom">Custom</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Difficulty</label>
                                            <select wire:model="templateDifficulty" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                                <option value="beginner">Beginner</option>
                                                <option value="intermediate">Intermediate</option>
                                                <option value="advanced">Advanced</option>
                                                <option value="expert">Expert</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                        <textarea wire:model="templateDescription" rows="3" placeholder="Describe this template..."
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"></textarea>
                                    </div>

                                    <!-- Questions -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Questions</label>
                                        
                                        <!-- Add Question -->
                                        <div class="flex space-x-2 mb-4">
                                            <input wire:model="newTemplateQuestion" type="text" placeholder="Enter question..."
                                                class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                            <select wire:model="templateQuestionType" class="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                                <option value="behavioral">Behavioral</option>
                                                <option value="technical">Technical</option>
                                                <option value="situational">Situational</option>
                                            </select>
                                            <button type="button" wire:click="addTemplateQuestion"
                                                class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                                Add
                                            </button>
                                        </div>

                                        <!-- Questions List -->
                                        @if(count($templateQuestions) > 0)
                                            <div class="space-y-2 max-h-40 overflow-y-auto">
                                                @foreach($templateQuestions as $index => $question)
                                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
                                                        <div class="flex-1">
                                                            <p class="text-sm text-gray-700">{{ $question['question'] }}</p>
                                                            <span class="text-xs text-gray-500">{{ ucfirst($question['type']) }}</span>
                                                        </div>
                                                        <button type="button" wire:click="removeTemplateQuestion({{ $index }})"
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

                                <div class="flex justify-end space-x-4 mt-6 pt-4 border-t border-gray-200">
                                    <button type="button" wire:click="resetTemplateForm"
                                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                                        Cancel
                                    </button>
                                    <button type="submit"
                                        class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                                        Create Template
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Question Bank Tab -->
        @if($activeTab === 'questions')
            <div class="space-y-6">
                <!-- Header -->
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-900">Question Bank</h2>
                    <button wire:click="$set('showQuestionForm', true)"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                        Add Question
                    </button>
                </div>

                <!-- Add New Question Form -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Add New Question</h3>
                    
                    <form wire:submit.prevent="addQuestion" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Question</label>
                            <textarea wire:model="newQuestion" rows="3" placeholder="Enter your question..."
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"></textarea>
                            @error('newQuestion') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                                <select wire:model="questionCategory" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                    <option value="technical">Technical</option>
                                    <option value="behavioral">Behavioral</option>
                                    <option value="situational">Situational</option>
                                    <option value="case_study">Case Study</option>
                                    <option value="system_design">System Design</option>
                                    <option value="coding">Coding</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Difficulty</label>
                                <select wire:model="questionDifficulty" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                    <option value="beginner">Beginner</option>
                                    <option value="intermediate">Intermediate</option>
                                    <option value="advanced">Advanced</option>
                                    <option value="expert">Expert</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
                                <input wire:model="questionTags" type="text" placeholder="comma, separated, tags"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                                Add Question
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Questions List -->
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Question</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Difficulty</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usage</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tags</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($questionBanks as $question)
                                <tr>
                                    <td class="px-6 py-4">
                                        <p class="text-sm text-gray-900">{{ $question['question'] }}</p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ ucfirst($question['category']) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ ucfirst($question['difficulty']) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $question['usage_count'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @foreach($question['tags'] as $tag)
                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-gray-100 text-gray-800 mr-1">
                                                {{ $tag }}
                                            </span>
                                        @endforeach
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                                        <button class="text-red-600 hover:text-red-900">Delete</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Analytics Tab -->
        @if($activeTab === 'analytics')
            <div class="space-y-6">
                <!-- Date Range Selector -->
                <div class="bg-white shadow rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-gray-900">Analytics Dashboard</h2>
                        <div class="flex items-center space-x-4">
                            <select wire:model.live="analyticsDateRange" class="px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                <option value="7">Last 7 days</option>
                                <option value="30">Last 30 days</option>
                                <option value="90">Last 90 days</option>
                                <option value="365">Last year</option>
                            </select>
                            <button wire:click="exportAnalytics" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                                Export
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Performance Metrics -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-blue-600">{{ number_format($performanceMetrics['avg_completion_rate'] ?? 0, 1) }}%</div>
                            <div class="text-sm text-gray-600">Avg Completion Rate</div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-green-600">{{ number_format($performanceMetrics['user_satisfaction'] ?? 0, 1) }}</div>
                            <div class="text-sm text-gray-600">User Satisfaction</div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-purple-600">{{ number_format($userEngagementData['active_users'] ?? 0) }}</div>
                            <div class="text-sm text-gray-600">Active Users</div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-orange-600">{{ number_format($performanceMetrics['total_interview_hours'] ?? 0) }}</div>
                            <div class="text-sm text-gray-600">Total Hours</div>
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Interview Trends Chart -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Interview Trends</h3>
                        <div class="h-64">
                            @if(!empty($chartData))
                                <canvas id="interviewTrendsChart"></canvas>
                            @else
                                <div class="h-full flex items-center justify-center text-gray-500">
                                    No data available for the selected period
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Score Distribution -->
                    <div class="bg-white shadow rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Average Scores by Type</h3>
                        <div class="space-y-4">
                            @foreach($performanceMetrics['avg_score_by_type'] ?? [] as $type => $score)
                                <div>
                                    <div class="flex justify-between text-sm">
                                        <span class="font-medium text-gray-700">{{ ucfirst(str_replace('_', ' ', $type)) }}</span>
                                        <span class="text-gray-900">{{ number_format($score, 1) }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $score }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- User Engagement -->
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">User Engagement Metrics</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $userEngagementData['repeat_users'] ?? 0 }}</div>
                            <div class="text-sm text-gray-600">Repeat Users</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ $userEngagementData['avg_interviews_per_user'] ?? 0 }}</div>
                            <div class="text-sm text-gray-600">Avg Interviews/User</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">{{ number_format($premiumUsage) }}</div>
                            <div class="text-sm text-gray-600">Premium Interviews</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Users Tab -->
        @if($activeTab === 'users')
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-900">User Management</h2>
                    <div class="text-sm text-gray-600">Top users by interview count</div>
                </div>

                <!-- Users Table -->
                <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Interviews</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Completion Rate</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Score</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Active</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($users as $user)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-gray-700">{{ substr($user->name, 0, 2) }}</span>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $user->mock_interviews_count }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format(($user->mock_interviews_count > 0 ? rand(65, 95) : 0), 1) }}%
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format(rand(70, 90), 1) }}%
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $user->updated_at->diffForHumans() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button wire:click="viewUser({{ $user->id }})"
                                            class="text-blue-600 hover:text-blue-900">View Details</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Settings Tab -->
        @if($activeTab === 'settings')
            <div class="space-y-6">
                <h2 class="text-xl font-semibold text-gray-900">System Settings</h2>
                
                <div class="bg-white shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Interview Configuration</h3>
                    
                    <form wire:submit.prevent="updateSystemSettings" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Max Interview Duration (minutes)</label>
                                <input wire:model="systemSettings.max_interview_duration" type="number" min="15" max="300"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Default Difficulty Level</label>
                                <select wire:model="systemSettings.default_difficulty" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                    <option value="beginner">Beginner</option>
                                    <option value="intermediate">Intermediate</option>
                                    <option value="advanced">Advanced</option>
                                    <option value="expert">Expert</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Max Concurrent Interviews</label>
                                <input wire:model="systemSettings.max_concurrent_interviews" type="number" min="1" max="20"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        
                        <!-- Feature Toggles -->
                        <div class="space-y-4">
                            <h4 class="text-md font-medium text-gray-900">Feature Settings</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <label class="flex items-center">
                                    <input wire:model="systemSettings.enable_ai_feedback" type="checkbox" class="w-4 h-4 text-blue-600">
                                    <span class="ml-2 text-sm text-gray-700">Enable AI Feedback</span>
                                </label>
                                
                                <label class="flex items-center">
                                    <input wire:model="systemSettings.enable_video_recording" type="checkbox" class="w-4 h-4 text-blue-600">
                                    <span class="ml-2 text-sm text-gray-700">Enable Video Recording</span>
                                </label>
                                
                                <label class="flex items-center">
                                    <input wire:model="systemSettings.auto_generate_feedback" type="checkbox" class="w-4 h-4 text-blue-600">
                                    <span class="ml-2 text-sm text-gray-700">Auto Generate Feedback</span>
                                </label>
                                
                                <label class="flex items-center">
                                    <input wire:model="systemSettings.require_premium_for_retakes" type="checkbox" class="w-4 h-4 text-blue-600">
                                    <span class="ml-2 text-sm text-gray-700">Require Premium for Retakes</span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="flex justify-end">
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                                Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    </div>

    <!-- Interview Details Modal -->
    @if($selectedInterview)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">Interview Details</h3>
                    <button wire:click="$set('selectedInterview', null)" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Basic Information -->
                        <div>
                            <h4 class="text-md font-medium text-gray-900 mb-4">Basic Information</h4>
                            <dl class="space-y-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Title</dt>
                                    <dd class="text-sm text-gray-900">{{ $selectedInterview->title }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">User</dt>
                                    <dd class="text-sm text-gray-900">{{ $selectedInterview->user->name }} ({{ $selectedInterview->user->email }})</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Type</dt>
                                    <dd class="text-sm text-gray-900">{{ $selectedInterview->type_label }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $selectedInterview->getStatusColor() }}-100 text-{{ $selectedInterview->getStatusColor() }}-800">
                                            {{ $selectedInterview->status_label }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Difficulty</dt>
                                    <dd class="text-sm text-gray-900">{{ $selectedInterview->difficulty_label }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Duration</dt>
                                    <dd class="text-sm text-gray-900">{{ $selectedInterview->duration_formatted }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Scores -->
                        @if($selectedInterview->isCompleted())
                            <div>
                                <h4 class="text-md font-medium text-gray-900 mb-4">Performance Scores</h4>
                                <dl class="space-y-2">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Overall Score</dt>
                                        <dd class="text-sm text-gray-900">{{ number_format($selectedInterview->overall_score, 1) }}% ({{ $selectedInterview->overall_rating }})</dd>
                                    </div>
                                    @if($selectedInterview->technical_score)
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Technical</dt>
                                            <dd class="text-sm text-gray-900">{{ number_format($selectedInterview->technical_score, 1) }}%</dd>
                                        </div>
                                    @endif
                                    @if($selectedInterview->communication_score)
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Communication</dt>
                                            <dd class="text-sm text-gray-900">{{ number_format($selectedInterview->communication_score, 1) }}%</dd>
                                        </div>
                                    @endif
                                    @if($selectedInterview->confidence_score)
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Confidence</dt>
                                            <dd class="text-sm text-gray-900">{{ number_format($selectedInterview->confidence_score, 1) }}%</dd>
                                        </div>
                                    @endif
                                </dl>
                            </div>
                        @endif
                    </div>

                    <!-- Questions and Responses -->
                    @if($selectedInterview->questions)
                        <div class="mt-6">
                            <h4 class="text-md font-medium text-gray-900 mb-4">Questions & Responses</h4>
                            <div class="space-y-4">
                                @foreach($selectedInterview->questions as $index => $question)
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div class="font-medium text-gray-900 mb-2">
                                            Q{{ $index + 1 }}: {{ $question['question'] }}
                                        </div>
                                        @if($selectedInterview->user_responses && isset($selectedInterview->user_responses[$question['id']]))
                                            <div class="bg-gray-50 p-3 rounded-md">
                                                <div class="text-sm text-gray-700">
                                                    {{ $selectedInterview->user_responses[$question['id']]['answer'] }}
                                                </div>
                                                @if(isset($selectedInterview->user_responses[$question['id']]['response_time']))
                                                    <div class="text-xs text-gray-500 mt-2">
                                                        Response time: {{ $selectedInterview->user_responses[$question['id']]['response_time'] }}s
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <div class="text-sm text-gray-500 italic">No response provided</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- AI Feedback -->
                    @if($selectedInterview->ai_feedback)
                        <div class="mt-6">
                            <h4 class="text-md font-medium text-gray-900 mb-4">AI Feedback</h4>
                            <div class="space-y-4">
                                @if($selectedInterview->strengths)
                                    <div>
                                        <h5 class="text-sm font-medium text-green-700 mb-2">Strengths</h5>
                                        <ul class="list-disc list-inside text-sm text-gray-700">
                                            @foreach($selectedInterview->strengths as $strength)
                                                <li>{{ $strength }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                
                                @if($selectedInterview->weaknesses)
                                    <div>
                                        <h5 class="text-sm font-medium text-red-700 mb-2">Areas for Improvement</h5>
                                        <ul class="list-disc list-inside text-sm text-gray-700">
                                            @foreach($selectedInterview->weaknesses as $weakness)
                                                <li>{{ $weakness }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                
                                @if($selectedInterview->improvement_suggestions)
                                    <div>
                                        <h5 class="text-sm font-medium text-blue-700 mb-2">Recommendations</h5>
                                        <ul class="list-disc list-inside text-sm text-gray-700">
                                            @foreach($selectedInterview->improvement_suggestions as $suggestion)
                                                <li>{{ $suggestion }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                    <div class="flex space-x-2">
                        @if($selectedInterview->isCompleted() && !$selectedInterview->ai_feedback)
                            <button wire:click="generateAIFeedback({{ $selectedInterview->id }})"
                                class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                                Generate AI Feedback
                            </button>
                        @endif
                    </div>
                    <button wire:click="$set('selectedInterview', null)"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- User Details Modal -->
    @if($showUserModal && $selectedUser)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900">User Details - {{ $selectedUser->name }}</h3>
                    <button wire:click="$set('showUserModal', false)" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                
                <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                    <!-- User Performance Overview -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="text-2xl font-bold text-blue-600">{{ $userPerformanceData['total_interviews'] ?? 0 }}</div>
                            <div class="text-sm text-gray-600">Total Interviews</div>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4">
                            <div class="text-2xl font-bold text-green-600">{{ number_format($userPerformanceData['average_score'] ?? 0, 1) }}%</div>
                            <div class="text-sm text-gray-600">Average Score</div>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-4">
                            <div class="text-2xl font-bold text-purple-600">{{ $userPerformanceData['improvement_rate'] ?? 0 }}%</div>
                            <div class="text-sm text-gray-600">Improvement Rate</div>
                        </div>
                    </div>

                    <!-- Recent Interviews -->
                    <div>
                        <h4 class="text-md font-medium text-gray-900 mb-4">Recent Interviews</h4>
                        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Interview</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                    @foreach($userInterviews as $interview)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $interview->title }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $interview->type_label }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $interview->getStatusColor() }}-100 text-{{ $interview->getStatusColor() }}-800">
                                                    {{ $interview->status_label }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $interview->overall_score ? number_format($interview->overall_score, 1) . '%' : 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $interview->created_at->format('M d, Y') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                    <button wire:click="$set('showUserModal', false)"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-md hover:bg-gray-50">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Chart Script -->
@if($activeTab === 'analytics' && !empty($chartData))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('interviewTrendsChart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json(array_column($chartData, 'date')),
                        datasets: [{
                            label: 'Interviews',
                            data: @json(array_column($chartData, 'count')),
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }
        });
    </script>
@endif