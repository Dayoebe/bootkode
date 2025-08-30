<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50">
    <!-- Header Section -->
    <div class="bg-white shadow-lg border-b">
        <div class="container mx-auto px-6 py-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="mb-6 lg:mb-0">
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">Job Management</h1>
                    <p class="text-xl text-gray-600">Manage job postings and track applications</p>
                </div>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-4 text-white text-center">
                        <div class="text-2xl font-bold">{{ $jobStats['total'] ?? 0 }}</div>
                        <div class="text-sm opacity-90">Total Jobs</div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-4 text-white text-center">
                        <div class="text-2xl font-bold">{{ $jobStats['active'] ?? 0 }}</div>
                        <div class="text-sm opacity-90">Active</div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 text-white text-center">
                        <div class="text-2xl font-bold">{{ $applicationStats['total'] ?? 0 }}</div>
                        <div class="text-sm opacity-90">Applications</div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-4 text-white text-center">
                        <div class="text-2xl font-bold">{{ $jobStats['premium'] ?? 0 }}</div>
                        <div class="text-sm opacity-90">Premium</div>
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

    <div class="px-6 py-8">
        <!-- Navigation Tabs -->
        <div class="flex space-x-8 mb-8 border-b border-gray-200">
            <button wire:click="$set('activeTab', 'overview')" 
                class="pb-4 {{ $activeTab === 'overview' ? 'border-b-2 border-blue-500 text-blue-600 font-semibold' : 'text-gray-500 hover:text-gray-700' }}">
                Overview
            </button>
            <button wire:click="$set('activeTab', 'jobs')" 
                class="pb-4 {{ $activeTab === 'jobs' ? 'border-b-2 border-blue-500 text-blue-600 font-semibold' : 'text-gray-500 hover:text-gray-700' }}">
                All Jobs
            </button>
            <button wire:click="$set('activeTab', 'applications')" 
                class="pb-4 {{ $activeTab === 'applications' ? 'border-b-2 border-blue-500 text-blue-600 font-semibold' : 'text-gray-500 hover:text-gray-700' }}">
                Applications
            </button>
            <button wire:click="$set('activeTab', 'analytics')" 
                class="pb-4 {{ $activeTab === 'analytics' ? 'border-b-2 border-blue-500 text-blue-600 font-semibold' : 'text-gray-500 hover:text-gray-700' }}">
                Analytics
            </button>
        </div>

        @if($activeTab === 'overview')
            <!-- Overview Dashboard -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column - Quick Stats -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Job Status Overview -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Job Status Overview</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <div class="text-center p-4 bg-green-50 rounded-lg">
                                <div class="text-2xl font-bold text-green-600">{{ $jobStats['active'] ?? 0 }}</div>
                                <div class="text-sm text-green-700">Active Jobs</div>
                            </div>
                            <div class="text-center p-4 bg-yellow-50 rounded-lg">
                                <div class="text-2xl font-bold text-yellow-600">{{ $jobStats['draft'] ?? 0 }}</div>
                                <div class="text-sm text-yellow-700">Draft Jobs</div>
                            </div>
                            <div class="text-center p-4 bg-red-50 rounded-lg">
                                <div class="text-2xl font-bold text-red-600">{{ $jobStats['expired'] ?? 0 }}</div>
                                <div class="text-sm text-red-700">Expired</div>
                            </div>
                            <div class="text-center p-4 bg-blue-50 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600">{{ $jobStats['filled'] ?? 0 }}</div>
                                <div class="text-sm text-blue-700">Filled</div>
                            </div>
                            <div class="text-center p-4 bg-purple-50 rounded-lg">
                                <div class="text-2xl font-bold text-purple-600">{{ $jobStats['featured'] ?? 0 }}</div>
                                <div class="text-sm text-purple-700">Featured</div>
                            </div>
                            <div class="text-center p-4 bg-indigo-50 rounded-lg">
                                <div class="text-2xl font-bold text-indigo-600">{{ $jobStats['this_month'] ?? 0 }}</div>
                                <div class="text-sm text-indigo-700">This Month</div>
                            </div>
                        </div>
                    </div>

                    <!-- Application Status Overview -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Application Status Overview</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="text-center p-3 bg-orange-50 rounded-lg">
                                <div class="text-xl font-bold text-orange-600">{{ $applicationStats['pending'] ?? 0 }}</div>
                                <div class="text-xs text-orange-700">Pending</div>
                            </div>
                            <div class="text-center p-3 bg-blue-50 rounded-lg">
                                <div class="text-xl font-bold text-blue-600">{{ $applicationStats['reviewing'] ?? 0 }}</div>
                                <div class="text-xs text-blue-700">Reviewing</div>
                            </div>
                            <div class="text-center p-3 bg-green-50 rounded-lg">
                                <div class="text-xl font-bold text-green-600">{{ $applicationStats['shortlisted'] ?? 0 }}</div>
                                <div class="text-xs text-green-700">Shortlisted</div>
                            </div>
                            <div class="text-center p-3 bg-purple-50 rounded-lg">
                                <div class="text-xl font-bold text-purple-600">{{ $applicationStats['hired'] ?? 0 }}</div>
                                <div class="text-xs text-purple-700">Hired</div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Jobs -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Recently Posted Jobs</h3>
                        <div class="space-y-4">
                            @forelse($recentActivity['recent_jobs'] ?? [] as $job)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div class="flex items-center space-x-4">
                                        <img src="{{ $job->company_logo_url }}" alt="{{ $job->company_name }}" class="w-10 h-10 rounded-full">
                                        <div>
                                            <h4 class="font-semibold text-gray-900">{{ $job->title }}</h4>
                                            <p class="text-sm text-gray-600">{{ $job->company_name }} • {{ $job->location_formatted }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="bg-{{ $job->getStatusColor() }}-100 text-{{ $job->getStatusColor() }}-800 text-xs px-2 py-1 rounded-full">
                                            {{ $job->status_label }}
                                        </span>
                                        <p class="text-xs text-gray-500 mt-1">{{ $job->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-center py-8">No recent jobs posted</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Right Column - Quick Actions & Activity -->
                <div class="space-y-6">
                    <!-- Quick Actions -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Quick Actions</h3>
                        <div class="space-y-3">
                            <button wire:click="$set('showForm', true)" 
                                class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-4 py-3 rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all font-semibold">
                                Post New Job
                            </button>
                            <button wire:click="$set('activeTab', 'applications')" 
                                class="w-full bg-green-100 text-green-700 px-4 py-3 rounded-lg hover:bg-green-200 transition-all font-semibold">
                                Review Applications
                            </button>
                            <button wire:click="$set('activeTab', 'analytics')" 
                                class="w-full bg-purple-100 text-purple-700 px-4 py-3 rounded-lg hover:bg-purple-200 transition-all font-semibold">
                                View Analytics
                            </button>
                        </div>
                    </div>

                    <!-- Jobs Expiring Soon -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Expiring Soon</h3>
                        <div class="space-y-3">
                            @forelse($recentActivity['expiring_soon'] ?? [] as $job)
                                <div class="p-3 bg-red-50 rounded-lg border border-red-200">
                                    <h4 class="font-medium text-gray-900 text-sm">{{ Str::limit($job->title, 30) }}</h4>
                                    <p class="text-xs text-gray-600">{{ $job->company_name }}</p>
                                    <p class="text-xs text-red-600 font-medium mt-1">{{ $job->days_until_deadline }}</p>
                                </div>
                            @empty
                                <p class="text-gray-500 text-sm">No jobs expiring soon</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Top Viewed Jobs -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Top Viewed Jobs</h3>
                        <div class="space-y-3">
                            @forelse($recentActivity['top_viewed_jobs'] ?? [] as $job)
                                <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                                    <div>
                                        <h4 class="font-medium text-gray-900 text-sm">{{ Str::limit($job->title, 25) }}</h4>
                                        <p class="text-xs text-gray-600">{{ $job->company_name }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-semibold text-blue-600">{{ $job->views_count }}</p>
                                        <p class="text-xs text-gray-500">views</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-sm">No view data available</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($activeTab === 'jobs')
            <!-- Jobs Management -->
            <div class="space-y-6">
                <!-- Action Bar -->
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                    <div class="flex flex-wrap items-center gap-4">
                        <button wire:click="$set('showForm', true)" 
                            class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-3 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all font-semibold shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Post New Job
                        </button>

                        @if($showBulkActions)
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-600">{{ count($selectedJobs) }} selected</span>
                                <select wire:change="bulkAction($event.target.value)" class="text-sm border border-gray-300 rounded-lg px-3 py-2">
                                    <option value="">Bulk Actions</option>
                                    <option value="activate">Activate</option>
                                    <option value="pause">Pause</option>
                                    <option value="feature">Feature</option>
                                    <option value="delete">Delete</option>
                                </select>
                            </div>
                        @endif
                    </div>

                    <!-- Search and Filters -->
                    <div class="flex flex-wrap items-center gap-4">
                        <div class="relative">
                            <input wire:model.live.debounce.300ms="searchTerm" type="text" placeholder="Search jobs..."
                                class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent w-64">
                            <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>

                        <select wire:model.live="filterStatus" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">All Status</option>
                            <option value="draft">Draft</option>
                            <option value="active">Active</option>
                            <option value="paused">Paused</option>
                            <option value="expired">Expired</option>
                            <option value="filled">Filled</option>
                        </select>

                        <select wire:model.live="filterCategory" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">All Categories</option>
                            @foreach($categories as $slug => $name)
                                <option value="{{ $slug }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Jobs List -->
                @if($jobs->count() > 0)
                    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                        @foreach($jobs as $job)
                            <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100">
                                <!-- Job Header -->
                                <div class="p-6 pb-4">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center space-x-3">
                                            <img src="{{ $job->company_logo_url }}" alt="{{ $job->company_name }}" class="w-12 h-12 rounded-full">
                                            <div>
                                                <h3 class="font-bold text-lg text-gray-900">{{ Str::limit($job->title, 40) }}</h3>
                                                <p class="text-gray-600 text-sm">{{ $job->company_name }}</p>
                                            </div>
                                        </div>
                                        <input type="checkbox" wire:model="selectedJobs" value="{{ $job->id }}" class="w-5 h-5 text-blue-600">
                                    </div>

                                    <!-- Job Details -->
                                    <div class="space-y-2 mb-4">
                                        <div class="flex items-center text-sm text-gray-600">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"/>
                                            </svg>
                                            {{ $job->location_formatted }}
                                        </div>
                                        <div class="flex items-center text-sm text-gray-600">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"/>
                                            </svg>
                                            Posted {{ $job->created_at->diffForHumans() }}
                                        </div>
                                    </div>

                                    <!-- Tags -->
                                    <div class="flex flex-wrap gap-2 mb-4">
                                        <span class="bg-{{ $job->getStatusColor() }}-100 text-{{ $job->getStatusColor() }}-800 text-xs px-2 py-1 rounded-full">
                                            {{ $job->status_label }}
                                        </span>
                                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                                            {{ $job->employment_type_label }}
                                        </span>
                                        <span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full">
                                            {{ $job->work_type_label }}
                                        </span>
                                        @if($job->is_premium)
                                            <span class="bg-gradient-to-r from-yellow-400 to-orange-500 text-white text-xs px-2 py-1 rounded-full font-semibold">
                                                PREMIUM
                                            </span>
                                        @endif
                                        @if($job->is_featured)
                                            <span class="bg-gradient-to-r from-pink-500 to-rose-500 text-white text-xs px-2 py-1 rounded-full font-semibold">
                                                FEATURED
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Stats -->
                                    <div class="grid grid-cols-3 gap-4 text-center text-sm">
                                        <div>
                                            <div class="font-semibold text-gray-900">{{ $job->views_count }}</div>
                                            <div class="text-gray-500">Views</div>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-900">{{ $job->applications_count }}</div>
                                            <div class="text-gray-500">Applications</div>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-900">{{ $job->positions_available }}</div>
                                            <div class="text-gray-500">Positions</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="bg-gray-50 px-6 py-4 flex space-x-2">
                                    <button wire:click="editJob({{ $job->id }})" class="flex-1 bg-blue-100 text-blue-700 px-3 py-2 rounded-lg hover:bg-blue-200 transition-colors text-sm font-medium">
                                        Edit
                                    </button>
                                    
                                    @if($job->status === 'active')
                                        <button wire:click="changeJobStatus({{ $job->id }}, 'paused')" class="flex-1 bg-yellow-100 text-yellow-700 px-3 py-2 rounded-lg hover:bg-yellow-200 transition-colors text-sm font-medium">
                                            Pause
                                        </button>
                                    @else
                                        <button wire:click="changeJobStatus({{ $job->id }}, 'active')" class="flex-1 bg-green-100 text-green-700 px-3 py-2 rounded-lg hover:bg-green-200 transition-colors text-sm font-medium">
                                            Activate
                                        </button>
                                    @endif

                                    <div class="relative">
                                        <button class="bg-gray-100 text-gray-700 px-3 py-2 rounded-lg hover:bg-gray-200 transition-colors text-sm" onclick="toggleDropdown({{ $job->id }})">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                            </svg>
                                        </button>
                                        <div id="dropdown-{{ $job->id }}" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg z-10">
                                            <button wire:click="duplicateJob({{ $job->id }})" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-t-lg">
                                                Duplicate Job
                                            </button>
                                            <button wire:click="changeJobStatus({{ $job->id }}, 'filled')" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                Mark as Filled
                                            </button>
                                            <button wire:click="deleteJob({{ $job->id }})" 
                                                wire:confirm="Are you sure you want to delete this job?" 
                                                class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 rounded-b-lg">
                                                Delete Job
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $jobs->links() }}
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="text-center py-16">
                        <div class="mx-auto w-32 h-32 bg-gradient-to-br from-blue-100 to-purple-100 rounded-full flex items-center justify-center mb-6">
                            <svg class="w-16 h-16 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.755M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-semibold text-gray-700 mb-2">No jobs found</h3>
                        <p class="text-gray-500 mb-6">Start by posting your first job to attract talented candidates.</p>
                        <button wire:click="$set('showForm', true)" 
                            class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-8 py-3 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all font-semibold shadow-lg hover:shadow-xl">
                            Post Your First Job
                        </button>
                    </div>
                @endif
            </div>
        @endif

        @if($activeTab === 'applications')
            <!-- Applications Management -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Recent Applications</h3>
                
                @if(isset($recentActivity['recent_applications']) && count($recentActivity['recent_applications']) > 0)
                    <div class="space-y-4">
                        @foreach($recentActivity['recent_applications'] as $application)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex items-center space-x-4">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($application->user->name) }}&color=7F9CF5&background=EBF4FF&size=40" 
                                         alt="{{ $application->user->name }}" class="w-10 h-10 rounded-full">
                                    <div>
                                        <h4 class="font-semibold text-gray-900">{{ $application->user->name }}</h4>
                                        <p class="text-sm text-gray-600">Applied for: {{ $application->job->title }}</p>
                                        <p class="text-xs text-gray-500">{{ $application->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="bg-{{ $application->status === 'pending' ? 'yellow' : ($application->status === 'hired' ? 'green' : 'blue') }}-100 text-{{ $application->status === 'pending' ? 'yellow' : ($application->status === 'hired' ? 'green' : 'blue') }}-800 text-xs px-2 py-1 rounded-full">
                                        {{ ucfirst($application->status) }}
                                    </span>
                                    <button wire:click="viewApplication({{ $application->id }})" 
                                        class="bg-blue-100 text-blue-700 px-3 py-2 rounded-lg hover:bg-blue-200 transition-colors text-sm">
                                        Review
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-gray-500">No recent applications to review</p>
                    </div>
                @endif
            </div>
        @endif

        @if($activeTab === 'analytics')
            <!-- Analytics Dashboard -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Performance Metrics -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Performance Metrics</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Total Views</span>
                            <span class="font-semibold text-2xl text-blue-600">{{ number_format($jobStats['total_views'] ?? 0) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Average Applications per Job</span>
                            <span class="font-semibold text-2xl text-green-600">{{ number_format($jobStats['avg_applications'] ?? 0, 1) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Application Conversion Rate</span>
                            <span class="font-semibold text-2xl text-purple-600">{{ $applicationStats['conversion_rate'] ?? 0 }}%</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Jobs This Month</span>
                            <span class="font-semibold text-2xl text-orange-600">{{ $jobStats['this_month'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                <!-- Application Funnel -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Application Funnel</h3>
                    <div class="space-y-3">
                        @php
                            $total = $applicationStats['total'] ?? 1;
                        @endphp
                        
                        <div class="flex items-center space-x-3">
                            <div class="w-full bg-gray-200 rounded-full h-4">
                                <div class="bg-yellow-500 h-4 rounded-full" style="width: {{ $total > 0 ? (($applicationStats['pending'] ?? 0) / $total) * 100 : 0 }}%"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-700 w-20">Pending</span>
                            <span class="text-sm text-gray-500">{{ $applicationStats['pending'] ?? 0 }}</span>
                        </div>

                        <div class="flex items-center space-x-3">
                            <div class="w-full bg-gray-200 rounded-full h-4">
                                <div class="bg-blue-500 h-4 rounded-full" style="width: {{ $total > 0 ? (($applicationStats['shortlisted'] ?? 0) / $total) * 100 : 0 }}%"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-700 w-20">Shortlisted</span>
                            <span class="text-sm text-gray-500">{{ $applicationStats['shortlisted'] ?? 0 }}</span>
                        </div>

                        <div class="flex items-center space-x-3">
                            <div class="w-full bg-gray-200 rounded-full h-4">
                                <div class="bg-purple-500 h-4 rounded-full" style="width: {{ $total > 0 ? (($applicationStats['interviewed'] ?? 0) / $total) * 100 : 0 }}%"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-700 w-20">Interviewed</span>
                            <span class="text-sm text-gray-500">{{ $applicationStats['interviewed'] ?? 0 }}</span>
                        </div>

                        <div class="flex items-center space-x-3">
                            <div class="w-full bg-gray-200 rounded-full h-4">
                                <div class="bg-green-500 h-4 rounded-full" style="width: {{ $total > 0 ? (($applicationStats['hired'] ?? 0) / $total) * 100 : 0 }}%"></div>
                            </div>
                            <span class="text-sm font-medium text-gray-700 w-20">Hired</span>
                            <span class="text-sm text-gray-500">{{ $applicationStats['hired'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Job Form Modal -->
    @if($showForm)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 overflow-y-auto">
            <div class="bg-white rounded-2xl max-w-6xl w-full max-h-[90vh] overflow-hidden">
                <div class="flex justify-between items-center p-6 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                    <h2 class="text-2xl font-bold text-gray-900">
                        {{ $editingJobId ? 'Edit Job' : 'Post New Job' }}
                    </h2>
                    <button wire:click="resetForm" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="saveJob" class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <!-- Basic Information -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-semibold text-gray-900 border-b pb-2">Basic Information</h3>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Job Title *</label>
                                    <input wire:model="title" type="text" placeholder="e.g., Senior Full Stack Developer"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    @error('title') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Job Description *</label>
                                    <textarea wire:model="description" rows="6" placeholder="Describe the role, what they'll be doing, and what makes this opportunity exciting..."
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                                    @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Employment Type *</label>
                                        <select wire:model="employment_type" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                                            @foreach($employmentTypes as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Work Type *</label>
                                        <select wire:model="work_type" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                                            @foreach($workTypes as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Experience Level *</label>
                                        <select wire:model="experience_level" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                                            @foreach($experienceLevels as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Category *</label>
                                        <select wire:model="category" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                                            <option value="">Select Category</option>
                                            @foreach($categories as $slug => $name)
                                                <option value="{{ $slug }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        @error('category') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Skills Required</label>
                                    <input wire:model="skills_required" type="text" placeholder="PHP, Laravel, React, MySQL (comma-separated)"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <p class="text-sm text-gray-500 mt-1">Separate skills with commas</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tags</label>
                                    <input wire:model="tags" type="text" placeholder="startup, fast-paced, remote-friendly (comma-separated)"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>

                            <!-- Location -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-semibold text-gray-900 border-b pb-2">Location</h3>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Location *</label>
                                    <input wire:model="location" type="text" placeholder="e.g., Lagos, Nigeria"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    @error('location') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">State</label>
                                        <input wire:model="state" type="text" placeholder="e.g., Lagos"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">City</label>
                                        <input wire:model="city" type="text" placeholder="e.g., Ikeja"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-6">
                            <!-- Company Information -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-semibold text-gray-900 border-b pb-2">Company Information</h3>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Company Name *</label>
                                    <input wire:model="company_name" type="text" placeholder="e.g., TechCorp Inc."
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    @error('company_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Company Logo</label>
                                    <input wire:model="company_logo" type="file" accept="image/*" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    @error('company_logo') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Company Website</label>
                                        <input wire:model="company_website" type="url" placeholder="https://company.com"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Company Size</label>
                                        <select wire:model="company_size" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                                            <option value="">Select Size</option>
                                            @foreach($companySizes as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Company Industry</label>
                                    <input wire:model="company_industry" type="text" placeholder="e.g., Technology, Healthcare, Finance"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>

                            <!-- Salary Information -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-semibold text-gray-900 border-b pb-2">Salary Information</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Minimum Salary</label>
                                        <input wire:model="salary_min" type="number" step="0.01" placeholder="50000"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Maximum Salary</label>
                                        <input wire:model="salary_max" type="number" step="0.01" placeholder="100000"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Currency</label>
                                        <select wire:model="salary_currency" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                                            @foreach($currencies as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Period</label>
                                        <select wire:model="salary_period" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                                            @foreach($salaryPeriods as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-4">
                                    <label class="flex items-center">
                                        <input wire:model="salary_negotiable" type="checkbox" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-700">Salary is negotiable</span>
                                    </label>

                                    <label class="flex items-center">
                                        <input wire:model="hide_salary" type="checkbox" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-700">Hide salary from listing</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Application Settings -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-semibold text-gray-900 border-b pb-2">Application Settings</h3>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Application Method *</label>
                                    <select wire:model="application_method" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500">
                                        <option value="internal">Internal Application System</option>
                                        <option value="email">Email Application</option>
                                        <option value="external_link">External Link</option>
                                        <option value="phone">Phone Application</option>
                                    </select>
                                </div>

                                @if($application_method === 'email')
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Application Email *</label>
                                        <input wire:model="application_email" type="email" placeholder="hr@company.com"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        @error('application_email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>
                                @endif

                                @if($application_method === 'external_link')
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Application URL *</label>
                                        <input wire:model="application_url" type="url" placeholder="https://company.com/jobs/apply"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        @error('application_url') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>
                                @endif

                                @if($application_method === 'phone')
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Application Phone *</label>
                                        <input wire:model="application_phone" type="tel" placeholder="+234 123 456 7890"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        @error('application_phone') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>
                                @endif

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Application Deadline</label>
                                        <input wire:model="application_deadline" type="datetime-local"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Positions Available *</label>
                                        <input wire:model="positions_available" type="number" min="1" placeholder="1"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        @error('positions_available') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Premium Features Section -->
                    <div class="mt-8 pt-8 border-t border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Premium Features</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input wire:model="enable_ai_screening" type="checkbox" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-3">
                                    <div class="font-medium text-gray-900">AI Screening</div>
                                    <div class="text-sm text-gray-500">Automated screening</div>
                                </span>
                            </label>

                            <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input wire:model="allow_remote_interview" type="checkbox" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-3">
                                    <div class="font-medium text-gray-900">Remote Interviews</div>
                                    <div class="text-sm text-gray-500">Video interviews</div>
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                        <button type="button" wire:click="resetForm"
                            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                            class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-8 py-3 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all font-semibold shadow-lg hover:shadow-xl">
                            <span wire:loading.remove wire:target="saveJob">
                                {{ $editingJobId ? 'Update Job' : 'Post Job' }}
                            </span>
                            <span wire:loading wire:target="saveJob">
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

    <!-- Application Review Modal -->
    @if($showApplicationModal && $selectedApplication)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
                <div class="flex justify-between items-center p-6 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Application Review</h2>
                        <p class="text-gray-600">{{ $selectedApplication->user->name }} - {{ $selectedApplication->job->title }}</p>
                    </div>
                    <button wire:click="closeApplicationModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto max-h-[calc(90vh-200px)]">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <div class="lg:col-span-2">
                            <div class="space-y-6">
                                <div>
                                    <h3 class="font-semibold text-gray-900 mb-2">Cover Letter</h3>
                                    <p class="text-gray-700 bg-gray-50 p-4 rounded-lg">
                                        {{ $selectedApplication->cover_letter ?: 'No cover letter provided.' }}
                                    </p>
                                </div>

                                <div>
                                    <h3 class="font-semibold text-gray-900 mb-2">Application Status</h3>
                                    <select wire:model="applicationStatus" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                        <option value="pending">Pending Review</option>
                                        <option value="reviewing">Under Review</option>
                                        <option value="shortlisted">Shortlisted</option>
                                        <option value="interviewed">Interviewed</option>
                                        <option value="offered">Offered</option>
                                        <option value="hired">Hired</option>
                                        <option value="rejected">Rejected</option>
                                    </select>
                                </div>

                                <div>
                                    <h3 class="font-semibold text-gray-900 mb-2">Feedback/Notes</h3>
                                    <textarea wire:model="applicationFeedback" rows="4" placeholder="Add your feedback or notes about this application..."
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="font-semibold text-gray-900 mb-4">Candidate Information</h3>
                                <div class="space-y-3">
                                    <div>
                                        <span class="text-sm text-gray-600">Name:</span>
                                        <p class="font-medium">{{ $selectedApplication->user->name }}</p>
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-600">Email:</span>
                                        <p class="font-medium">{{ $selectedApplication->user->email }}</p>
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-600">Applied:</span>
                                        <p class="font-medium">{{ $selectedApplication->created_at->format('M d, Y') }}</p>
                                    </div>
                                    @if($selectedApplication->resume_path)
                                        <div>
                                            <a href="{{ Storage::url($selectedApplication->resume_path) }}" target="_blank" 
                                               class="inline-flex items-center px-3 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors">
                                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z"/>
                                                </svg>
                                                Download Resume
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-4 p-6 border-t border-gray-200 bg-gray-50">
                    <button wire:click="closeApplicationModal" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                        Cancel
                    </button>
                    <button wire:click="updateApplicationStatus" 
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Update Status
                    </button>
                </div>
            </div>
        </div>
    @endif

    <script>
        function toggleDropdown(jobId) {
            const dropdown = document.getElementById(`dropdown-${jobId}`);
            dropdown.classList.toggle('hidden');
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            const dropdowns = document.querySelectorAll('[id^="dropdown-"]');
            dropdowns.forEach(dropdown => {
                if (!dropdown.contains(event.target) && !event.target.closest('button')) {
                    dropdown.classList.add('hidden');
                }
            });
        });
    </script>

    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }
    </style>
</div>