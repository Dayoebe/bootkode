<div class="min-h-screen bg-themed-primary">
    <!-- Hero Section -->
    <div class="bg-themed-secondary shadow-lg border-b border-themed-primary">
        <div class="container mx-auto px-6 py-16">
            <div class="text-center mb-12">
                <h1 class="text-5xl font-bold text-themed-primary mb-4">Find Your Dream Job</h1>
                <p class="text-xl text-themed-secondary max-w-2xl mx-auto">Discover opportunities that match your skills and
                    advance your career with top companies</p>
            </div>

            <!-- Quick Search -->
            <div class="max-w-4xl mx-auto">
                <div class="bg-themed-secondary rounded-2xl shadow-2xl p-6 border border-themed-primary">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-2">
                            <div class="relative">
                                <input wire:model.live.debounce.500ms="searchTerm" type="text"
                                    placeholder="Job title, keywords, or company"
                                    class="w-full px-4 py-3 pl-12 border border-themed-primary rounded-xl focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary">
                                <svg class="absolute left-4 top-3.5 h-5 w-5 text-themed-secondary" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <input wire:model.live.debounce.500ms="filterLocation" type="text" placeholder="Location"
                                class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary">
                        </div>
                        <div>
                            <button wire:click="$set('activeTab', 'browse')"
                                class="w-full bg-accent-themed-primary text-white px-6 py-3 rounded-xl hover:bg-accent-themed-secondary transition-all font-semibold">
                                Search Jobs
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-4xl mx-auto mt-12">
                <div class="text-center">
                    <div class="text-3xl font-bold text-themed-primary">{{ number_format($jobStats['total_active'] ?? 0) }}</div>
                    <div class="text-themed-secondary">Active Jobs</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-themed-primary">{{ number_format($jobStats['new_this_week'] ?? 0) }}</div>
                    <div class="text-themed-secondary">New This Week</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-themed-primary">{{ number_format($jobStats['remote_jobs'] ?? 0) }}</div>
                    <div class="text-themed-secondary">Remote Jobs</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-themed-primary">{{ number_format($jobStats['premium_jobs'] ?? 0) }}</div>
                    <div class="text-themed-secondary">Premium Jobs</div>
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
            <button wire:click="$set('activeTab', 'browse')"
                class="pb-4 {{ $activeTab === 'browse' ? 'border-b-2 border-accent-themed-primary text-accent-themed-primary font-semibold' : 'text-themed-secondary hover:text-themed-primary' }}">
                Browse Jobs
            </button>
            @auth
                <button wire:click="$set('activeTab', 'applications')"
                    class="pb-4 {{ $activeTab === 'applications' ? 'border-b-2 border-accent-themed-primary text-accent-themed-primary font-semibold' : 'text-themed-secondary hover:text-themed-primary' }}">
                    My Applications
                    @if(isset($jobStats['user_applications']) && $jobStats['user_applications'] > 0)
                        <span class="ml-2 bg-blue-100 text-blue-600 text-xs px-2 py-1 rounded-full">{{ $jobStats['user_applications'] }}</span>
                    @endif
                </button>
                <button wire:click="$set('activeTab', 'saved')"
                    class="pb-4 {{ $activeTab === 'saved' ? 'border-b-2 border-accent-themed-primary text-accent-themed-primary font-semibold' : 'text-themed-secondary hover:text-themed-primary' }}">
                    Saved Jobs
                    @if(isset($jobStats['user_saved']) && $jobStats['user_saved'] > 0)
                        <span class="ml-2 bg-green-100 text-green-600 text-xs px-2 py-1 rounded-full">{{ $jobStats['user_saved'] }}</span>
                    @endif
                </button>
            @endauth
        </div>

        @if($activeTab === 'browse')
            <!-- Job Browsing Section -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <!-- Filters Sidebar -->
                <div class="lg:col-span-1">
                    <div class="bg-themed-secondary rounded-xl shadow-lg p-6 sticky top-4 border border-themed-primary">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-themed-primary">Filters</h3>
                            <button wire:click="clearFilters" class="text-sm text-accent-themed-primary hover:text-accent-themed-secondary">
                                Clear All
                            </button>
                        </div>

                        <div class="space-y-6">
                            <!-- Category Filter -->
                            <div>
                                <label class="block text-sm font-medium text-themed-primary mb-2">Category</label>
                                <select wire:model.live="filterCategory"
                                    class="w-full px-3 py-2 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-themed-primary text-sm bg-themed-secondary text-themed-primary">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $slug => $name)
                                        <option value="{{ $slug }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Employment Type Filter -->
                            <div>
                                <label class="block text-sm font-medium text-themed-primary mb-2">Employment Type</label>
                                <select wire:model.live="filterEmploymentType"
                                    class="w-full px-3 py-2 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-themed-primary text-sm bg-themed-secondary text-themed-primary">
                                    <option value="">All Types</option>
                                    @foreach($employmentTypes as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Work Type Filter -->
                            <div>
                                <label class="block text-sm font-medium text-themed-primary mb-2">Work Type</label>
                                <select wire:model.live="filterWorkType"
                                    class="w-full px-3 py-2 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-themed-primary text-sm bg-themed-secondary text-themed-primary">
                                    <option value="">All Work Types</option>
                                    @foreach($workTypes as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Experience Level Filter -->
                            <div>
                                <label class="block text-sm font-medium text-themed-primary mb-2">Experience Level</label>
                                <select wire:model.live="filterExperienceLevel"
                                    class="w-full px-3 py-2 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-themed-primary text-sm bg-themed-secondary text-themed-primary">
                                    <option value="">All Levels</option>
                                    @foreach($experienceLevels as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Salary Range Filter -->
                            <div>
                                <label class="block text-sm font-medium text-themed-primary mb-2">Salary Range (NGN)</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <input wire:model.live.debounce.500ms="filterSalaryMin" type="number" placeholder="Min"
                                        class="px-3 py-2 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-themed-primary text-sm bg-themed-secondary text-themed-primary placeholder-themed-tertiary">
                                    <input wire:model.live.debounce.500ms="filterSalaryMax" type="number" placeholder="Max"
                                        class="px-3 py-2 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-themed-primary text-sm bg-themed-secondary text-themed-primary placeholder-themed-tertiary">
                                </div>
                            </div>

                            <!-- Job Alert -->
                            @auth
                                <div class="pt-4 border-t border-themed-primary">
                                    <button wire:click="$set('showJobAlertModal', true)"
                                        class="w-full bg-green-600 text-white px-4 py-3 rounded-lg hover:bg-green-700 transition-all font-medium text-sm">
                                        Create Job Alert
                                    </button>
                                </div>
                            @endauth
                        </div>
                    </div>
                </div>

                <!-- Jobs List -->
                <div class="lg:col-span-3">
                    <!-- Sort and View Controls -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                        <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                            <select wire:model.live="sortBy"
                                class="px-3 py-2 border border-themed-primary rounded-lg focus:ring-2 focus:ring-accent-themed-primary text-sm bg-themed-secondary text-themed-primary">
                                <option value="created_at">Latest Jobs</option>
                                <option value="salary">Salary</option>
                                <option value="views_count">Most Popular</option>
                                <option value="applications_count">Most Applied</option>
                                @if($searchTerm)
                                    <option value="relevance">Relevance</option>
                                @endif
                            </select>

                            <button wire:click="$toggle('sortDirection')"
                                class="p-2 border border-themed-primary rounded-lg hover:bg-themed-tertiary transition-colors">
                                <svg class="w-4 h-4 text-themed-primary {{ $sortDirection === 'desc' ? 'transform rotate-180' : '' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 15l7-7 7 7" />
                                </svg>
                            </button>
                        </div>

                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-themed-secondary">View:</span>
                            <button wire:click="$set('viewMode', 'list')"
                                class="p-2 rounded-lg {{ $viewMode === 'list' ? 'bg-accent-themed-primary text-white' : 'text-themed-secondary hover:text-themed-primary border border-themed-primary' }}">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" />
                                </svg>
                            </button>
                            <button wire:click="$set('viewMode', 'grid')"
                                class="p-2 rounded-lg {{ $viewMode === 'grid' ? 'bg-accent-themed-primary text-white' : 'text-themed-secondary hover:text-themed-primary border border-themed-primary' }}">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Jobs Display -->
                    @if($jobs->count() > 0)
                        <div class="{{ $viewMode === 'grid' ? 'grid grid-cols-1 md:grid-cols-2 gap-6' : 'space-y-6' }}">
                            @foreach($jobs as $job)
                                <div
                                    class="bg-themed-secondary rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden border border-themed-primary hover:border-accent-themed-primary">
                                    <!-- Job Header -->
                                    <div class="p-6">
                                        <div class="flex items-start justify-between mb-4">
                                            <div class="flex items-center space-x-4 flex-1">
                                                <img src="{{ $job->company_logo_url }}" alt="{{ $job->company_name }}"
                                                    class="w-16 h-16 rounded-full border-2 border-themed-primary">
                                                <div class="flex-1">
                                                    <h3 class="text-xl font-bold text-themed-primary hover:text-accent-themed-primary cursor-pointer mb-1"
                                                        wire:click="viewJob({{ $job->id }})">
                                                        {{ $job->title }}
                                                    </h3>
                                                    <p class="text-themed-secondary font-medium">{{ $job->company_name }}</p>
                                                    <div class="flex items-center space-x-4 text-sm text-themed-secondary mt-1">
                                                        <span class="flex items-center">
                                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd"
                                                                    d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" />
                                                            </svg>
                                                            {{ $job->location_formatted }}
                                                        </span>
                                                        <span>{{ $job->created_at->diffForHumans() }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            @auth
                                                <button wire:click="toggleSaveJob({{ $job->id }})"
                                                    class="p-2 rounded-lg hover:bg-themed-tertiary transition-colors {{ $this->isJobSaved($job->id) ? 'text-red-500' : 'text-themed-secondary' }}">
                                                    <svg class="w-6 h-6"
                                                        fill="{{ $this->isJobSaved($job->id) ? 'currentColor' : 'none' }}"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                                    </svg>
                                                </button>
                                            @endauth
                                        </div>

                                        <!-- Job Details -->
                                        <div class="mb-4">
                                            <p class="text-themed-secondary line-clamp-3">{{ Str::limit($job->description, 150) }}</p>
                                        </div>

                                        <!-- Tags -->
                                        <div class="flex flex-wrap gap-2 mb-4">
                                            <span class="bg-blue-100 text-blue-800 text-xs px-3 py-1 rounded-full font-medium">
                                                {{ $job->employment_type_label }}
                                            </span>
                                            <span class="bg-green-100 text-green-800 text-xs px-3 py-1 rounded-full font-medium">
                                                {{ $job->work_type_icon }} {{ $job->work_type_label }}
                                            </span>
                                            <span class="bg-purple-100 text-purple-800 text-xs px-3 py-1 rounded-full font-medium">
                                                {{ $job->experience_level_label }}
                                            </span>
                                            @if($job->is_premium)
                                                <span
                                                    class="bg-gradient-to-r from-yellow-400 to-orange-500 text-white text-xs px-3 py-1 rounded-full font-semibold">
                                                    PREMIUM
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Salary and Skills -->
                                        <div class="space-y-2 mb-4">
                                            @if(!$job->hide_salary)
                                                <div class="flex items-center text-lg font-semibold text-green-600">
                                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" />
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" />
                                                    </svg>
                                                    {{ $job->salary_range_formatted }}
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="flex space-x-3">
                                            <button wire:click="viewJob({{ $job->id }})"
                                                class="flex-1 bg-accent-themed-primary text-white px-4 py-3 rounded-lg hover:bg-accent-themed-secondary transition-colors font-medium">
                                                View Details
                                            </button>

                                            @auth
                                                @if($this->hasAppliedToJob($job->id))
                                                    <button disabled
                                                        class="flex-1 bg-themed-tertiary text-themed-secondary px-4 py-3 rounded-lg cursor-not-allowed font-medium">
                                                        Applied
                                                    </button>
                                                @else
                                                    <button wire:click="openApplicationModal({{ $job->id }})"
                                                        class="flex-1 bg-green-600 text-white px-4 py-3 rounded-lg hover:bg-green-700 transition-colors font-medium">
                                                        Apply Now
                                                    </button>
                                                @endif
                                            @else
                                                <button onclick="window.location.href='{{ route('login') }}'"
                                                    class="flex-1 bg-green-600 text-white px-4 py-3 rounded-lg hover:bg-green-700 transition-colors font-medium">
                                                    Login to Apply
                                                </button>
                                            @endauth
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
                            <div
                                class="mx-auto w-32 h-32 bg-themed-tertiary rounded-full flex items-center justify-center mb-6">
                                <svg class="w-16 h-16 text-accent-themed-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <h3 class="text-2xl font-semibold text-themed-primary mb-2">No jobs found</h3>
                            <p class="text-themed-secondary mb-6">Try adjusting your filters or search terms to find more opportunities.</p>
                            <button wire:click="clearFilters"
                                class="bg-accent-themed-primary text-white px-6 py-3 rounded-lg hover:bg-accent-themed-secondary transition-colors font-medium">
                                Clear Filters
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        @if($activeTab === 'applications')
            <!-- My Applications Section -->
            <div class="bg-themed-secondary rounded-xl shadow-lg p-6 border border-themed-primary">
                <h3 class="text-2xl font-bold text-themed-primary mb-6">My Applications</h3>

                @if(count($userApplications) > 0)
                    <div class="space-y-4">
                        @foreach($userApplications as $application)
                            <div class="border border-themed-primary rounded-lg p-6 hover:border-accent-themed-primary transition-colors bg-themed-secondary">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center space-x-4">
                                        <img src="{{ $application->job->company_logo_url }}"
                                            alt="{{ $application->job->company_name }}" class="w-12 h-12 rounded-full">
                                        <div>
                                            <h4 class="text-lg font-semibold text-themed-primary">{{ $application->job->title }}</h4>
                                            <p class="text-themed-secondary">{{ $application->job->company_name }}</p>
                                            <p class="text-sm text-themed-tertiary">Applied {{ $application->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span
                                            class="bg-{{ $application->status_color }}-100 text-{{ $application->status_color }}-800 text-sm px-3 py-1 rounded-full font-medium">
                                            {{ $application->status_label }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto w-16 h-16 text-themed-secondary mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-themed-secondary">You haven't applied to any jobs yet.</p>
                        <button wire:click="$set('activeTab', 'browse')"
                            class="mt-4 text-accent-themed-primary hover:text-accent-themed-secondary font-medium">
                            Browse Jobs
                        </button>
                    </div>
                @endif
            </div>
        @endif

        @if($activeTab === 'saved')
            <!-- Saved Jobs Section -->
            <div class="bg-themed-secondary rounded-xl shadow-lg p-6 border border-themed-primary">
                <h3 class="text-2xl font-bold text-themed-primary mb-6">Saved Jobs</h3>

                @if(count($savedJobs) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($savedJobs as $savedJob)
                            <div class="border border-themed-primary rounded-lg p-4 hover:border-accent-themed-primary transition-colors bg-themed-secondary">
                                <div class="flex items-start justify-between mb-3">
                                    <img src="{{ $savedJob->job->company_logo_url }}" alt="{{ $savedJob->job->company_name }}"
                                        class="w-10 h-10 rounded-full">
                                    <button wire:click="toggleSaveJob({{ $savedJob->job->id }})"
                                        class="text-red-500 hover:text-red-600">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" />
                                        </svg>
                                    </button>
                                </div>
                                <h4 class="font-semibold text-themed-primary mb-1">{{ $savedJob->job->title }}</h4>
                                <p class="text-sm text-themed-secondary mb-2">{{ $savedJob->job->company_name }}</p>
                                <p class="text-xs text-themed-tertiary mb-3">Saved {{ $savedJob->created_at->diffForHumans() }}</p>

                                <div class="flex space-x-2">
                                    <button wire:click="viewJob({{ $savedJob->job->id }})"
                                        class="flex-1 bg-accent-themed-primary text-white px-3 py-2 rounded-lg hover:bg-accent-themed-secondary transition-colors text-sm">
                                        View
                                    </button>
                                    @if(!$this->hasAppliedToJob($savedJob->job->id))
                                        <button wire:click="openApplicationModal({{ $savedJob->job->id }})"
                                            class="flex-1 bg-green-600 text-white px-3 py-2 rounded-lg hover:bg-green-700 transition-colors text-sm">
                                            Apply
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto w-16 h-16 text-themed-secondary mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        <p class="text-themed-secondary">No saved jobs yet.</p>
                        <button wire:click="$set('activeTab', 'browse')"
                            class="mt-4 text-accent-themed-primary hover:text-accent-themed-secondary font-medium">
                            Browse Jobs
                        </button>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Job Details Modal -->
    @if($showJobDetails && $selectedJob)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 overflow-y-auto">
            <div class="bg-themed-secondary rounded-2xl max-w-5xl w-full max-h-[90vh] overflow-hidden">
                <div class="flex justify-between items-center p-6 border-b border-themed-primary bg-themed-tertiary">
                    <div class="flex items-center space-x-4">
                        <img src="{{ $selectedJob->company_logo_url }}" alt="{{ $selectedJob->company_name }}"
                            class="w-16 h-16 rounded-full">
                        <div>
                            <h2 class="text-2xl font-bold text-themed-primary">{{ $selectedJob->title }}</h2>
                            <p class="text-lg text-themed-secondary">{{ $selectedJob->company_name }}</p>
                        </div>
                    </div>
                    <button wire:click="closeJobDetails" class="text-themed-secondary hover:text-themed-primary">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="overflow-y-auto max-h-[calc(90vh-200px)]">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 p-6">
                        <div class="lg:col-span-2">
                            <!-- Job Description -->
                            <div class="mb-8">
                                <h3 class="text-xl font-semibold text-themed-primary mb-4">Job Description</h3>
                                <div class="prose prose-gray max-w-none text-themed-primary">
                                    {!! nl2br(e($selectedJob->description)) !!}
                                </div>
                            </div>

                            <!-- Requirements -->
                            @if($selectedJob->requirements)
                                <div class="mb-8">
                                    <h3 class="text-xl font-semibold text-themed-primary mb-4">Requirements</h3>
                                    <div class="prose prose-gray max-w-none text-themed-primary">
                                        {!! nl2br(e($selectedJob->requirements)) !!}
                                    </div>
                                </div>
                            @endif

                            <!-- Responsibilities -->
                            @if($selectedJob->responsibilities)
                                <div class="mb-8">
                                    <h3 class="text-xl font-semibold text-themed-primary mb-4">Responsibilities</h3>
                                    <div class="prose prose-gray max-w-none text-themed-primary">
                                        {!! nl2br(e($selectedJob->responsibilities)) !!}
                                    </div>
                                </div>
                            @endif

                            <!-- Skills Required -->
                            @if($selectedJob->skills_required)
                                <div class="mb-8">
                                    <h3 class="text-xl font-semibold text-themed-primary mb-4">Skills Required</h3>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($selectedJob->skills_required as $skill)
                                            <span class="bg-blue-100 text-blue-800 text-sm px-3 py-2 rounded-full font-medium">
                                                {{ $skill }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="lg:col-span-1">
                            <div class="bg-themed-tertiary rounded-xl p-6 sticky top-0">
                                <h3 class="text-lg font-semibold text-themed-primary mb-6">Job Details</h3>

                                <div class="space-y-4">
                                    <div>
                                        <dt class="text-sm font-medium text-themed-secondary">Employment Type</dt>
                                        <dd class="text-lg text-themed-primary">{{ $selectedJob->employment_type_label }}</dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-themed-secondary">Work Type</dt>
                                        <dd class="text-lg text-themed-primary">{{ $selectedJob->work_type_icon }}
                                            {{ $selectedJob->work_type_label }}</dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-themed-secondary">Experience Level</dt>
                                        <dd class="text-lg text-themed-primary">{{ $selectedJob->experience_level_label }}</dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-themed-secondary">Location</dt>
                                        <dd class="text-lg text-themed-primary">{{ $selectedJob->location_formatted }}</dd>
                                    </div>

                                    @if(!$selectedJob->hide_salary)
                                        <div>
                                            <dt class="text-sm font-medium text-themed-secondary">Salary</dt>
                                            <dd class="text-lg font-semibold text-green-600">
                                                {{ $selectedJob->salary_range_formatted }}</dd>
                                        </div>
                                    @endif

                                    <div>
                                        <dt class="text-sm font-medium text-themed-secondary">Posted</dt>
                                        <dd class="text-lg text-themed-primary">{{ $selectedJob->created_at->diffForHumans() }}</dd>
                                    </div>
                                </div>

                                <!-- Quick Actions -->
                                <div class="mt-8 space-y-3">
                                    <button wire:click="saveJob({{ $selectedJob->id }})"
                                        class="w-full flex items-center justify-center px-4 py-3 {{ $selectedJob['is_saved'] ? 'bg-red-100 text-red-700 border border-red-200' : 'bg-themed-secondary text-themed-primary border border-themed-primary' }} rounded-lg hover:bg-opacity-80 transition-all">
                                        <svg class="w-5 h-5 mr-2"
                                            fill="{{ $selectedJob['is_saved'] ? 'currentColor' : 'none' }}"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                        {{ $selectedJob['is_saved'] ? 'Remove from Saved' : 'Save Job' }}
                                    </button>

                                    @if($selectedJob['is_applied'])
                                        <button
                                            class="w-full px-4 py-3 bg-green-100 text-green-700 border border-green-200 rounded-lg cursor-not-allowed">
                                            ✓ Already Applied
                                        </button>
                                    @else
                                        <a href="{{ $selectedJob->redirect_url }}" target="_blank"
                                            wire:click="applyToJob({{ $selectedJob->id }})"
                                            class="w-full block text-center px-4 py-3 bg-accent-themed-primary text-white rounded-lg hover:bg-accent-themed-secondary transition-all font-medium">
                                            Apply Now
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Application Modal -->
    @if($showApplicationModal && $applicationJob)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-themed-secondary rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden">
                <div class="flex justify-between items-center p-6 border-b border-themed-primary bg-themed-tertiary">
                    <div>
                        <h2 class="text-2xl font-bold text-themed-primary">Apply for Job</h2>
                        <p class="text-themed-secondary">{{ $applicationJob->title }} at {{ $applicationJob->company_name }}</p>
                    </div>
                    <button wire:click="closeApplicationModal" class="text-themed-secondary hover:text-themed-primary">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="submitApplication" class="p-6 overflow-y-auto max-h-[calc(90vh-200px)]">
                    <div class="space-y-6">
                        <!-- Cover Letter -->
                        <div>
                            <label class="block text-sm font-semibold text-themed-primary mb-2">Cover Letter *</label>
                            <textarea wire:model="coverLetter" rows="8"
                                placeholder="Write a compelling cover letter explaining why you're the perfect fit for this role..."
                                class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent resize-none bg-themed-secondary text-themed-primary placeholder-themed-tertiary"></textarea>
                            @error('coverLetter') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            <p class="text-sm text-themed-secondary mt-1">{{ strlen($coverLetter) }}/2000 characters</p>
                        </div>

                        <!-- Resume Upload -->
                        <div>
                            <label class="block text-sm font-semibold text-themed-primary mb-2">Resume/CV</label>
                            <input wire:model="resume" type="file" accept=".pdf,.doc,.docx"
                                class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent bg-themed-secondary text-themed-primary">
                            @error('resume') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            <p class="text-sm text-themed-secondary mt-1">PDF, DOC, or DOCX files up to 5MB</p>
                        </div>

                        <!-- Screening Questions -->
                        @if($applicationJob->screening_questions)
                            <div class="pt-6 border-t border-themed-primary">
                                <h3 class="text-lg font-semibold text-themed-primary mb-4">Screening Questions</h3>
                                <div class="space-y-4">
                                    @foreach($applicationJob->screening_questions as $index => $question)
                                        <div>
                                            <label class="block text-sm font-medium text-themed-primary mb-2">
                                                {{ $question['question'] }}
                                                @if($question['required']) <span class="text-red-500">*</span> @endif
                                            </label>

                                            @if($question['type'] === 'text')
                                                <input wire:model="customResponses.{{ $index }}" type="text"
                                                    class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent bg-themed-secondary text-themed-primary">
                                            @elseif($question['type'] === 'textarea')
                                                <textarea wire:model="customResponses.{{ $index }}" rows="3"
                                                    class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent bg-themed-secondary text-themed-primary"></textarea>
                                            @elseif($question['type'] === 'select' && isset($question['options']))
                                                <select wire:model="customResponses.{{ $index }}"
                                                    class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent bg-themed-secondary text-themed-primary">
                                                    <option value="">Select an option</option>
                                                    @foreach($question['options'] as $option)
                                                        <option value="{{ $option }}">{{ $option }}</option>
                                                    @endforeach
                                                </select>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-themed-primary">
                        <button type="button" wire:click="closeApplicationModal"
                            class="px-6 py-3 border border-themed-primary text-themed-primary rounded-xl hover:bg-themed-tertiary transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                            class="bg-green-600 text-white px-8 py-3 rounded-xl hover:bg-green-700 transition-colors font-semibold">
                            <span wire:loading.remove wire:target="submitApplication">Submit Application</span>
                            <span wire:loading wire:target="submitApplication">
                                <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white inline"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Submitting...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Job Alert Modal -->
    @if($showJobAlertModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
            <div class="bg-themed-secondary rounded-2xl max-w-lg w-full">
                <div class="flex justify-between items-center p-6 border-b border-themed-primary">
                    <h2 class="text-2xl font-bold text-themed-primary">Create Job Alert</h2>
                    <button wire:click="$set('showJobAlertModal', false)" class="text-themed-secondary hover:text-themed-primary">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="createJobAlert" class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-themed-primary mb-2">Keywords</label>
                            <input wire:model="alertKeywords" type="text" placeholder="e.g. PHP, Laravel, Developer"
                                class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-themed-primary mb-2">Location</label>
                            <input wire:model="alertLocation" type="text" placeholder="e.g. Lagos, Remote"
                                class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent bg-themed-secondary text-themed-primary placeholder-themed-tertiary">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-themed-primary mb-2">Category</label>
                            <select wire:model="alertCategory"
                                class="w-full px-4 py-3 border border-themed-primary rounded-xl focus:ring-2 focus:ring-accent-themed-primary focus:border-transparent bg-themed-secondary text-themed-primary">
                                <option value="">All Categories</option>
                                @foreach($categories as $slug => $name)
                                    <option value="{{ $slug }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4 mt-6">
                        <button type="button" wire:click="$set('showJobAlertModal', false)"
                            class="px-6 py-3 border border-themed-primary text-themed-primary rounded-xl hover:bg-themed-tertiary transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                            class="bg-green-600 text-white px-8 py-3 rounded-xl hover:bg-green-700 transition-colors font-semibold">
                            Create Alert
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <style>
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

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