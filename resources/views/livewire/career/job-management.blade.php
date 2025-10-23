<div class="min-h-screen bg-themed-primary">
    <!-- Header Section -->
    <div class="bg-themed-secondary shadow-lg border-b border-themed-primary">
        <div class="container mx-auto px-6 py-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="mb-6 lg:mb-0">
                    <h1 class="text-4xl font-bold text-themed-primary mb-2">Job Management</h1>
                    <p class="text-xl text-themed-secondary">Manage job postings and track applications</p>
                </div>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-accent-themed-primary rounded-xl p-4 text-white text-center">
                        <div class="text-2xl font-bold">{{ $jobStats['total'] ?? 0 }}</div>
                        <div class="text-sm opacity-90">Total Jobs</div>
                    </div>
                    
                    <div class="bg-green-600 rounded-xl p-4 text-white text-center">
                        <div class="text-2xl font-bold">{{ $jobStats['active'] ?? 0 }}</div>
                        <div class="text-sm opacity-90">Active</div>
                    </div>
                    
                    <div class="bg-purple-600 rounded-xl p-4 text-white text-center">
                        <div class="text-2xl font-bold">{{ $applicationStats['total'] ?? 0 }}</div>
                        <div class="text-sm opacity-90">Applications</div>
                    </div>
                    
                    <div class="bg-orange-600 rounded-xl p-4 text-white text-center">
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
        <div class="flex space-x-8 mb-8 border-b border-themed-primary">
            <button wire:click="$set('activeTab', 'overview')" 
                class="pb-4 {{ $activeTab === 'overview' ? 'border-b-2 border-accent-themed-primary text-accent-themed-primary font-semibold' : 'text-themed-secondary hover:text-themed-primary' }}">
                Overview
            </button>
            <button wire:click="$set('activeTab', 'jobs')" 
                class="pb-4 {{ $activeTab === 'jobs' ? 'border-b-2 border-accent-themed-primary text-accent-themed-primary font-semibold' : 'text-themed-secondary hover:text-themed-primary' }}">
                All Jobs
            </button>
            <button wire:click="$set('activeTab', 'applications')" 
                class="pb-4 {{ $activeTab === 'applications' ? 'border-b-2 border-accent-themed-primary text-accent-themed-primary font-semibold' : 'text-themed-secondary hover:text-themed-primary' }}">
                Applications
            </button>
            <button wire:click="$set('activeTab', 'analytics')" 
                class="pb-4 {{ $activeTab === 'analytics' ? 'border-b-2 border-accent-themed-primary text-accent-themed-primary font-semibold' : 'text-themed-secondary hover:text-themed-primary' }}">
                Analytics
            </button>
        </div>

        @if($activeTab === 'overview')
            <!-- Overview Dashboard -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column - Quick Stats -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Job Status Overview -->
                    <div class="bg-themed-secondary rounded-xl shadow-lg p-6 border border-themed-primary">
                        <h3 class="text-xl font-bold text-themed-primary mb-4">Job Status Overview</h3>
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
                    <div class="bg-themed-secondary rounded-xl shadow-lg p-6 border border-themed-primary">
                        <h3 class="text-xl font-bold text-themed-primary mb-4">Application Status Overview</h3>
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
                    <div class="bg-themed-secondary rounded-xl shadow-lg p-6 border border-themed-primary">
                        <h3 class="text-xl font-bold text-themed-primary mb-4">Recently Posted Jobs</h3>
                        <div class="space-y-4">
                            @forelse($recentActivity['recent_jobs'] ?? [] as $job)
                                <div class="flex items-center justify-between p-4 bg-themed-tertiary rounded-lg">
                                    <div class="flex items-center space-x-4">
                                        <img src="{{ $job->company_logo_url }}" alt="{{ $job->company_name }}" class="w-10 h-10 rounded-full">
                                        <div>
                                            <h4 class="font-semibold text-themed-primary">{{ $job->title }}</h4>
                                            <p class="text-sm text-themed-secondary">{{ $job->company_name }} • {{ $job->location_formatted }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="bg-{{ $job->getStatusColor() }}-100 text-{{ $job->getStatusColor() }}-800 text-xs px-2 py-1 rounded-full">
                                            {{ $job->status_label }}
                                        </span>
                                        <p class="text-xs text-themed-tertiary mt-1">{{ $job->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-themed-secondary text-center py-8">No recent jobs posted</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Right Column - Quick Actions & Activity -->
                <div class="space-y-6">
                    <!-- Quick Actions -->
                    <div class="bg-themed-secondary rounded-xl shadow-lg p-6 border border-themed-primary">
                        <h3 class="text-xl font-bold text-themed-primary mb-4">Quick Actions</h3>
                        <div class="space-y-3">
                            <button wire:click="$set('showForm', true)" 
                                class="w-full bg-accent-themed-primary text-white px-4 py-3 rounded-lg hover:bg-accent-themed-secondary transition-all font-semibold">
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
                    <div class="bg-themed-secondary rounded-xl shadow-lg p-6 border border-themed-primary">
                        <h3 class="text-xl font-bold text-themed-primary mb-4">Expiring Soon</h3>
                        <div class="space-y-3">
                            @forelse($recentActivity['expiring_soon'] ?? [] as $job)
                                <div class="p-3 bg-red-50 rounded-lg border border-red-200">
                                    <h4 class="font-medium text-themed-primary text-sm">{{ Str::limit($job->title, 30) }}</h4>
                                    <p class="text-xs text-themed-secondary">{{ $job->company_name }}</p>
                                    <p class="text-xs text-red-600 font-medium mt-1">{{ $job->days_until_deadline }}</p>
                                </div>
                            @empty
                                <p class="text-themed-secondary text-sm">No jobs expiring soon</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Top Viewed Jobs -->
                    <div class="bg-themed-secondary rounded-xl shadow-lg p-6 border border-themed-primary">
                        <h3 class="text-xl font-bold text-themed-primary mb-4">Top Viewed Jobs</h3>
                        <div class="space-y-3">
                            @forelse($recentActivity['top_viewed_jobs'] ?? [] as $job)
                                <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                                    <div>
                                        <h4 class="font-medium text-themed-primary text-sm">{{ Str::limit($job->title, 25) }}</h4>
                                        <p class="text-xs text-themed-secondary">{{ $job->company_name }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-semibold text-blue-600">{{ $job->views_count }}</p>
                                        <p class="text-xs text-themed-tertiary">views</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-themed-secondary text-sm">No view data available</p>
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
                            class="bg-accent-themed-primary text-white px-6 py-3 rounded-xl hover:bg-accent-themed-secondary transition-all font-semibold shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Post New Job
                        </button>

                        @if($showBulkActions)
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-themed-secondary">{{ count($selectedJobs) }} selected</span>
                                <select wire:change="bulkAction($event.target.value)" class="text-sm border border-themed-primary rounded-lg px-3 py-2 bg-themed-secondary text-themed-primary">
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
                                class="pl-10 pr-4 py-2 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent w-64 bg-themed-secondary text-themed-primary placeholder-themed-tertiary">
                            <svg class="absolute left-3 top-2.5 h-5 w-5 text-themed-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>

                        <select wire:model.live="filterStatus" class="px-4 py-2 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-themed-primary bg-themed-secondary text-themed-primary">
                            <option value="">All Status</option>
                            <option value="draft">Draft</option>
                            <option value="active">Active</option>
                            <option value="paused">Paused</option>
                            <option value="expired">Expired</option>
                            <option value="filled">Filled</option>
                        </select>

                        <select wire:model.live="filterCategory" class="px-4 py-2 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-themed-primary bg-themed-secondary text-themed-primary">
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
                            <div class="bg-themed-secondary rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden border border-themed-primary">
                                <!-- Job Header -->
                                <div class="p-6 pb-4">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center space-x-3">
                                            <img src="{{ $job->company_logo_url }}" alt="{{ $job->company_name }}" class="w-12 h-12 rounded-full">
                                            <div>
                                                <h3 class="font-bold text-lg text-themed-primary">{{ Str::limit($job->title, 40) }}</h3>
                                                <p class="text-themed-secondary text-sm">{{ $job->company_name }}</p>
                                            </div>
                                        </div>
                                        <input type="checkbox" wire:model="selectedJobs" value="{{ $job->id }}" class="w-5 h-5 text-accent-themed-primary">
                                    </div>

                                    <!-- Job Details -->
                                    <div class="space-y-2 mb-4">
                                        <div class="flex items-center text-sm text-themed-secondary">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"/>
                                            </svg>
                                            {{ $job->location_formatted }}
                                        </div>
                                        <div class="flex items-center text-sm text-themed-secondary">
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
                                    </div>

                                    <!-- Stats -->
                                    <div class="grid grid-cols-3 gap-4 text-center text-sm">
                                        <div>
                                            <div class="font-semibold text-themed-primary">{{ $job->views_count }}</div>
                                            <div class="text-themed-secondary">Views</div>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-themed-primary">{{ $job->applications_count }}</div>
                                            <div class="text-themed-secondary">Applications</div>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-themed-primary">{{ $job->positions_available }}</div>
                                            <div class="text-themed-secondary">Positions</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="bg-themed-tertiary px-6 py-4 flex space-x-2">
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
                                        <button class="bg-themed-secondary text-themed-primary px-3 py-2 rounded-lg hover:bg-themed-tertiary transition-colors text-sm" onclick="toggleDropdown({{ $job->id }})">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                            </svg>
                                        </button>
                                        <div id="dropdown-{{ $job->id }}" class="hidden absolute right-0 mt-2 w-48 bg-themed-secondary rounded-lg shadow-lg z-10 border border-themed-primary">
                                            <button wire:click="duplicateJob({{ $job->id }})" class="w-full text-left px-4 py-2 text-sm text-themed-primary hover:bg-themed-tertiary rounded-t-lg">
                                                Duplicate Job
                                            </button>
                                            <button wire:click="changeJobStatus({{ $job->id }}, 'filled')" class="w-full text-left px-4 py-2 text-sm text-themed-primary hover:bg-themed-tertiary">
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
                        <div class="mx-auto w-32 h-32 bg-themed-tertiary rounded-full flex items-center justify-center mb-6">
                            <svg class="w-16 h-16 text-accent-themed-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.755M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-semibold text-themed-primary mb-2">No jobs found</h3>
                        <p class="text-themed-secondary mb-6">Start by posting your first job to attract talented candidates.</p>
                        <button wire:click="$set('showForm', true)" 
                            class="bg-accent-themed-primary text-white px-8 py-3 rounded-xl hover:bg-accent-themed-secondary transition-all font-semibold shadow-lg hover:shadow-xl">
                            Post Your First Job
                        </button>
                    </div>
                @endif
            </div>
        @endif

        @if($activeTab === 'applications')
            <!-- Applications Management -->
            <div class="bg-themed-secondary rounded-xl shadow-lg p-6 border border-themed-primary">
                <h3 class="text-xl font-bold text-themed-primary mb-6">Recent Applications</h3>
                
                @if(isset($recentActivity['recent_applications']) && count($recentActivity['recent_applications']) > 0)
                    <div class="space-y-4">
                        @foreach($recentActivity['recent_applications'] as $application)
                            <div class="flex items-center justify-between p-4 bg-themed-tertiary rounded-lg hover:bg-themed-primary transition-colors">
                                <div class="flex items-center space-x-4">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($application->user->name) }}&color=7F9CF5&background=EBF4FF&size=40" 
                                         alt="{{ $application->user->name }}" class="w-10 h-10 rounded-full">
                                    <div>
                                        <h4 class="font-semibold text-themed-primary">{{ $application->user->name }}</h4>
                                        <p class="text-sm text-themed-secondary">Applied for: {{ $application->job->title }}</p>
                                        <p class="text-xs text-themed-tertiary">{{ $application->created_at->diffForHumans() }}</p>
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
                        <p class="text-themed-secondary">No recent applications to review</p>
                    </div>
                @endif
            </div>
        @endif

        @if($activeTab === 'analytics')
            <!-- Analytics Dashboard -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Performance Metrics -->
                <div class="bg-themed-secondary rounded-xl shadow-lg p-6 border border-themed-primary">
                    <h3 class="text-xl font-bold text-themed-primary mb-6">Performance Metrics</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-themed-secondary">Total Views</span>
                            <span class="font-semibold text-2xl text-blue-600">{{ number_format($jobStats['total_views'] ?? 0) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-themed-secondary">Average Applications per Job</span>
                            <span class="font-semibold text-2xl text-green-600">{{ number_format($jobStats['avg_applications'] ?? 0, 1) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-themed-secondary">Application Conversion Rate</span>
                            <span class="font-semibold text-2xl text-purple-600">{{ $applicationStats['conversion_rate'] ?? 0 }}%</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-themed-secondary">Jobs This Month</span>
                            <span class="font-semibold text-2xl text-orange-600">{{ $jobStats['this_month'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>

                <!-- Application Funnel -->
                <div class="bg-themed-secondary rounded-xl shadow-lg p-6 border border-themed-primary">
                    <h3 class="text-xl font-bold text-themed-primary mb-6">Application Funnel</h3>
                    <div class="space-y-3">
                        @php
                            $total = $applicationStats['total'] ?? 1;
                        @endphp
                        
                        <div class="flex items-center space-x-3">
                            <div class="w-full bg-themed-tertiary rounded-full h-4">
                                <div class="bg-yellow-500 h-4 rounded-full" style="width: {{ $total > 0 ? (($applicationStats['pending'] ?? 0) / $total) * 100 : 0 }}%"></div>
                            </div>
                            <span class="text-sm font-medium text-themed-primary w-20">Pending</span>
                            <span class="text-sm text-themed-secondary">{{ $applicationStats['pending'] ?? 0 }}</span>
                        </div>

                        <div class="flex items-center space-x-3">
                            <div class="w-full bg-themed-tertiary rounded-full h-4">
                                <div class="bg-blue-500 h-4 rounded-full" style="width: {{ $total > 0 ? (($applicationStats['shortlisted'] ?? 0) / $total) * 100 : 0 }}%"></div>
                            </div>
                            <span class="text-sm font-medium text-themed-primary w-20">Shortlisted</span>
                            <span class="text-sm text-themed-secondary">{{ $applicationStats['shortlisted'] ?? 0 }}</span>
                        </div>

                        <div class="flex items-center space-x-3">
                            <div class="w-full bg-themed-tertiary rounded-full h-4">
                                <div class="bg-purple-500 h-4 rounded-full" style="width: {{ $total > 0 ? (($applicationStats['interviewed'] ?? 0) / $total) * 100 : 0 }}%"></div>
                            </div>
                            <span class="text-sm font-medium text-themed-primary w-20">Interviewed</span>
                            <span class="text-sm text-themed-secondary">{{ $applicationStats['interviewed'] ?? 0 }}</span>
                        </div>

                        <div class="flex items-center space-x-3">
                            <div class="w-full bg-themed-tertiary rounded-full h-4">
                                <div class="bg-green-500 h-4 rounded-full" style="width: {{ $total > 0 ? (($applicationStats['hired'] ?? 0) / $total) * 100 : 0 }}%"></div>
                            </div>
                            <span class="text-sm font-medium text-themed-primary w-20">Hired</span>
                            <span class="text-sm text-themed-secondary">{{ $applicationStats['hired'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Job Form Modal -->
    @if($showForm)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 overflow-y-auto">
            <div class="bg-themed-secondary rounded-2xl max-w-6xl w-full max-h-[90vh] overflow-hidden">
                <div class="flex justify-between items-center p-6 border-b border-themed-primary bg-themed-tertiary">
                    <h2 class="text-2xl font-bold text-themed-primary">
                        {{ $editingJobId ? 'Edit Job' : 'Post New Job' }}
                    </h2>
                    <button wire:click="resetForm" class="text-themed-secondary hover:text-themed-primary transition-colors">
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
                                <h3 class="text-lg font-semibold text-themed-primary border-b border-themed-primary pb-2">Basic Information</h3>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-themed-primary mb-2">Job Title *</label>
                                    <input wire:model="title" type="text" placeholder="e.g., Senior Full Stack Developer"
                                        class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary">
                                    @error('title') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-themed-primary mb-2">Job Description *</label>
                                    <textarea wire:model="description" rows="6" placeholder="Describe the role, what they'll be doing, and what makes this opportunity exciting..."
                                        class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary"></textarea>
                                    @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-themed-primary mb-2">Employment Type *</label>
                                        <select wire:model="employment_type" class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-accent-themed-primary bg-themed-secondary text-themed-primary">
                                            @foreach($employmentTypes as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-themed-primary mb-2">Work Type *</label>
                                        <select wire:model="work_type" class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-accent-themed-primary bg-themed-secondary text-themed-primary">
                                            @foreach($workTypes as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-themed-primary mb-2">Experience Level *</label>
                                        <select wire:model="experience_level" class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-accent-themed-primary bg-themed-secondary text-themed-primary">
                                            @foreach($experienceLevels as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-themed-primary mb-2">Category *</label>
                                        <select wire:model="category" class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-accent-themed-primary bg-themed-secondary text-themed-primary">
                                            <option value="">Select Category</option>
                                            @foreach($categories as $slug => $name)
                                                <option value="{{ $slug }}">{{ $name }}</option>
                                            @endforeach
                                        </select>
                                        @error('category') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Location -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-semibold text-themed-primary border-b border-themed-primary pb-2">Location</h3>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-themed-primary mb-2">Location *</label>
                                    <input wire:model="location" type="text" placeholder="e.g., Lagos, Nigeria"
                                        class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary">
                                    @error('location') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-themed-primary mb-2">State</label>
                                        <input wire:model="state" type="text" placeholder="e.g., Lagos"
                                            class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-themed-primary mb-2">City</label>
                                        <input wire:model="city" type="text" placeholder="e.g., Ikeja"
                                            class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-6">
                            <!-- Company Information -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-semibold text-themed-primary border-b border-themed-primary pb-2">Company Information</h3>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-themed-primary mb-2">Company Name *</label>
                                    <input wire:model="company_name" type="text" placeholder="e.g., TechCorp Inc."
                                        class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary">
                                    @error('company_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-themed-primary mb-2">Company Logo</label>
                                    <input wire:model="company_logo" type="file" accept="image/*" 
                                        class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent bg-themed-secondary text-themed-primary">
                                    @error('company_logo') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-themed-primary mb-2">Company Website</label>
                                        <input wire:model="company_website" type="url" placeholder="https://company.com"
                                            class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-themed-primary mb-2">Company Size</label>
                                        <select wire:model="company_size" class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-accent-themed-primary bg-themed-secondary text-themed-primary">
                                            <option value="">Select Size</option>
                                            @foreach($companySizes as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Salary Information -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-semibold text-themed-primary border-b border-themed-primary pb-2">Salary Information</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-themed-primary mb-2">Minimum Salary</label>
                                        <input wire:model="salary_min" type="number" step="0.01" placeholder="50000"
                                            class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-themed-primary mb-2">Maximum Salary</label>
                                        <input wire:model="salary_max" type="number" step="0.01" placeholder="100000"
                                            class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-themed-primary mb-2">Currency</label>
                                        <select wire:model="salary_currency" class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-accent-themed-primary bg-themed-secondary text-themed-primary">
                                            @foreach($currencies as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-themed-primary mb-2">Period</label>
                                        <select wire:model="salary_period" class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-accent-themed-primary bg-themed-secondary text-themed-primary">
                                            @foreach($salaryPeriods as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="flex items-center space-x-4">
                                    <label class="flex items-center">
                                        <input wire:model="salary_negotiable" type="checkbox" class="w-4 h-4 text-accent-themed-primary border-themed-primary rounded focus:ring-accent-themed-primary">
                                        <span class="ml-2 text-sm text-themed-primary">Salary is negotiable</span>
                                    </label>

                                    <label class="flex items-center">
                                        <input wire:model="hide_salary" type="checkbox" class="w-4 h-4 text-accent-themed-primary border-themed-primary rounded focus:ring-accent-themed-primary">
                                        <span class="ml-2 text-sm text-themed-primary">Hide salary from listing</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Application Settings -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-semibold text-themed-primary border-b border-themed-primary pb-2">Application Settings</h3>
                                
                                <div>
                                    <label class="block text-sm font-semibold text-themed-primary mb-2">Application Method *</label>
                                    <select wire:model="application_method" class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-accent-themed-primary bg-themed-secondary text-themed-primary">
                                        <option value="internal">Internal Application System</option>
                                        <option value="email">Email Application</option>
                                        <option value="external_link">External Link</option>
                                        <option value="phone">Phone Application</option>
                                    </select>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-themed-primary mb-2">Application Deadline</label>
                                        <input wire:model="application_deadline" type="datetime-local"
                                            class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent bg-themed-secondary text-themed-primary">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-themed-primary mb-2">Positions Available *</label>
                                        <input wire:model="positions_available" type="number" min="1" placeholder="1"
                                            class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary">
                                        @error('positions_available') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-themed-primary">
                        <button type="button" wire:click="resetForm"
                            class="px-6 py-3 border border-themed-primary text-themed-primary rounded-xl hover:bg-themed-tertiary transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                            class="bg-accent-themed-primary text-white px-8 py-3 rounded-xl hover:bg-accent-themed-secondary transition-all font-semibold shadow-lg hover:shadow-xl">
                            <span wire:loading.remove wire:target="saveJob">
                                {{ $editingJobId ? 'Update Job' : 'Post Job' }}
                            </span>
                            <span wire:loading wire:target="saveJob">
                                <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white inline"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Saving...
                            </span>
                        </button>
                    </div>
                </form>
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