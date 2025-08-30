<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50">
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
        <div class="container mx-auto px-6 py-16">
            <div class="text-center mb-12">
                <h1 class="text-5xl font-bold mb-4">Find Your Dream Job</h1>
                <p class="text-xl opacity-90 max-w-2xl mx-auto">Discover opportunities that match your skills and advance your career with top companies</p>
            </div>

            <!-- Quick Search -->
            <div class="max-w-4xl mx-auto">
                <div class="bg-white rounded-2xl shadow-2xl p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="md:col-span-2">
                            <div class="relative">
                                <input wire:model.live.debounce.500ms="searchTerm" type="text" placeholder="Job title, keywords, or company"
                                    class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900">
                                <svg class="absolute left-4 top-3.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <input wire:model.live.debounce.500ms="filterLocation" type="text" placeholder="Location"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900">
                        </div>
                        <div>
                            <button wire:click="$set('activeTab', 'browse')" class="w-full bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 transition-all font-semibold">
                                Search Jobs
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-4xl mx-auto mt-12">
                <div class="text-center">
                    <div class="text-3xl font-bold">{{ number_format($jobStats['total_active'] ?? 0) }}</div>
                    <div class="text-blue-100">Active Jobs</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold">{{ number_format($jobStats['new_this_week'] ?? 0) }}</div>
                    <div class="text-blue-100">New This Week</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold">{{ number_format($jobStats['remote_jobs'] ?? 0) }}</div>
                    <div class="text-blue-100">Remote Jobs</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold">{{ number_format($jobStats['premium_jobs'] ?? 0) }}</div>
                    <div class="text-blue-100">Premium Jobs</div>
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
            <button wire:click="$set('activeTab', 'browse')" 
                class="pb-4 {{ $activeTab === 'browse' ? 'border-b-2 border-blue-500 text-blue-600 font-semibold' : 'text-gray-500 hover:text-gray-700' }}">
                Browse Jobs
            </button>
            @auth
                <button wire:click="$set('activeTab', 'applications')" 
                    class="pb-4 {{ $activeTab === 'applications' ? 'border-b-2 border-blue-500 text-blue-600 font-semibold' : 'text-gray-500 hover:text-gray-700' }}">
                    My Applications
                    @if(isset($jobStats['user_applications']) && $jobStats['user_applications'] > 0)
                        <span class="ml-2 bg-blue-100 text-blue-600 text-xs px-2 py-1 rounded-full">{{ $jobStats['user_applications'] }}</span>
                    @endif
                </button>
                <button wire:click="$set('activeTab', 'saved')" 
                    class="pb-4 {{ $activeTab === 'saved' ? 'border-b-2 border-blue-500 text-blue-600 font-semibold' : 'text-gray-500 hover:text-gray-700' }}">
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
                    <div class="bg-white rounded-xl shadow-lg p-6 sticky top-4">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900">Filters</h3>
                            <button wire:click="clearFilters" class="text-sm text-blue-600 hover:text-blue-700">
                                Clear All
                            </button>
                        </div>

                        <div class="space-y-6">
                            <!-- Category Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                                <select wire:model.live="filterCategory" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $slug => $name)
                                        <option value="{{ $slug }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Employment Type Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Employment Type</label>
                                <select wire:model.live="filterEmploymentType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                                    <option value="">All Types</option>
                                    @foreach($employmentTypes as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Work Type Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Work Type</label>
                                <select wire:model.live="filterWorkType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                                    <option value="">All Work Types</option>
                                    @foreach($workTypes as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Experience Level Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Experience Level</label>
                                <select wire:model.live="filterExperienceLevel" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                                    <option value="">All Levels</option>
                                    @foreach($experienceLevels as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Salary Range Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Salary Range (NGN)</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <input wire:model.live.debounce.500ms="filterSalaryMin" type="number" placeholder="Min" 
                                        class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                                    <input wire:model.live.debounce.500ms="filterSalaryMax" type="number" placeholder="Max" 
                                        class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                                </div>
                            </div>

                            <!-- Advanced Filters Toggle -->
                            <button wire:click="$toggle('showAdvancedFilters')" 
                                class="w-full text-left text-sm text-blue-600 hover:text-blue-700 font-medium">
                                {{ $showAdvancedFilters ? 'Hide' : 'Show' }} Advanced Filters
                            </button>

                            @if($showAdvancedFilters)
                                <div class="space-y-4 pt-4 border-t border-gray-200">
                                    <!-- Additional filters would go here -->
                                    <div class="text-sm text-gray-500">
                                        More advanced filters coming soon...
                                    </div>
                                </div>
                            @endif

                            <!-- Job Alert -->
                            @auth
                                <div class="pt-4 border-t border-gray-200">
                                    <button wire:click="$set('showJobAlertModal', true)" 
                                        class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white px-4 py-3 rounded-lg hover:from-green-600 hover:to-green-700 transition-all font-medium text-sm">
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
                            <select wire:model.live="sortBy" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
                                <option value="created_at">Latest Jobs</option>
                                <option value="salary">Salary</option>
                                <option value="views_count">Most Popular</option>
                                <option value="applications_count">Most Applied</option>
                                @if($searchTerm)
                                    <option value="relevance">Relevance</option>
                                @endif
                            </select>

                            <button wire:click="$toggle('sortDirection')" 
                                class="p-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4 {{ $sortDirection === 'desc' ? 'transform rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                                </svg>
                            </button>
                        </div>

                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600">View:</span>
                            <button wire:click="$set('viewMode', 'list')" 
                                class="p-2 rounded-lg {{ $viewMode === 'list' ? 'bg-blue-100 text-blue-600' : 'text-gray-400 hover:text-gray-600' }}">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"/>
                                </svg>
                            </button>
                            <button wire:click="$set('viewMode', 'grid')" 
                                class="p-2 rounded-lg {{ $viewMode === 'grid' ? 'bg-blue-100 text-blue-600' : 'text-gray-400 hover:text-gray-600' }}">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Jobs Display -->
                    @if($jobs->count() > 0)
                        <div class="{{ $viewMode === 'grid' ? 'grid grid-cols-1 md:grid-cols-2 gap-6' : 'space-y-6' }}">
                            @foreach($jobs as $job)
                                <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 hover:border-blue-200">
                                    <!-- Job Header -->
                                    <div class="p-6">
                                        <div class="flex items-start justify-between mb-4">
                                            <div class="flex items-center space-x-4 flex-1">
                                                <img src="{{ $job->company_logo_url }}" alt="{{ $job->company_name }}" 
                                                     class="w-16 h-16 rounded-full border-2 border-gray-100">
                                                <div class="flex-1">
                                                    <h3 class="text-xl font-bold text-gray-900 hover:text-blue-600 cursor-pointer mb-1"
                                                        wire:click="viewJob({{ $job->id }})">
                                                        {{ $job->title }}
                                                    </h3>
                                                    <p class="text-gray-600 font-medium">{{ $job->company_name }}</p>
                                                    <div class="flex items-center space-x-4 text-sm text-gray-500 mt-1">
                                                        <span class="flex items-center">
                                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"/>
                                                            </svg>
                                                            {{ $job->location_formatted }}
                                                        </span>
                                                        <span>{{ $job->created_at->diffForHumans() }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            @auth
                                                <button wire:click="toggleSaveJob({{ $job->id }})" 
                                                    class="p-2 rounded-lg hover:bg-gray-100 transition-colors {{ $this->isJobSaved($job->id) ? 'text-red-500' : 'text-gray-400' }}">
                                                    <svg class="w-6 h-6" fill="{{ $this->isJobSaved($job->id) ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                                    </svg>
                                                </button>
                                            @endauth
                                        </div>

                                        <!-- Job Details -->
                                        <div class="mb-4">
                                            <p class="text-gray-700 line-clamp-3">{{ Str::limit($job->description, 150) }}</p>
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
                                                <span class="bg-gradient-to-r from-yellow-400 to-orange-500 text-white text-xs px-3 py-1 rounded-full font-semibold">
                                                    PREMIUM
                                                </span>
                                            @endif
                                            @if($job->is_featured)
                                                <span class="bg-gradient-to-r from-pink-500 to-rose-500 text-white text-xs px-3 py-1 rounded-full font-semibold">
                                                    FEATURED
                                                </span>
                                            @endif
                                            @if($job->is_urgent)
                                                <span class="bg-red-500 text-white text-xs px-3 py-1 rounded-full font-semibold animate-pulse">
                                                    URGENT
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Salary and Skills -->
                                        <div class="space-y-2 mb-4">
                                            @if(!$job->hide_salary)
                                                <div class="flex items-center text-lg font-semibold text-green-600">
                                                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z"/>
                                                    </svg>
                                                    {{ $job->salary_range_formatted }}
                                                </div>
                                            @endif

                                            @if($job->skills_required)
                                                <div class="flex flex-wrap gap-1">
                                                    @foreach(array_slice($job->skills_required, 0, 4) as $skill)
                                                        <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">
                                                            {{ $skill }}
                                                        </span>
                                                    @endforeach
                                                    @if(count($job->skills_required) > 4)
                                                        <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full">
                                                            +{{ count($job->skills_required) - 4 }} more
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Job Stats -->
                                        <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                                            <div class="flex items-center space-x-4">
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"/>
                                                    </svg>
                                                    {{ $job->views_count }} views
                                                </span>
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z"/>
                                                    </svg>
                                                    {{ $job->applications_count }} applied
                                                </span>
                                                @if($job->application_deadline)
                                                    <span class="flex items-center text-orange-600">
                                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"/>
                                                        </svg>
                                                        {{ $job->days_until_deadline }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Action Buttons -->
                                        <div class="flex space-x-3">
                                            <button wire:click="viewJob({{ $job->id }})" 
                                                class="flex-1 bg-blue-600 text-white px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                                View Details
                                            </button>

                                            @auth
                                                @if($this->hasAppliedToJob($job->id))
                                                    <button disabled class="flex-1 bg-gray-300 text-gray-500 px-4 py-3 rounded-lg cursor-not-allowed font-medium">
                                                        Applied
                                                    </button>
                                                @else
                                                    @if($job->application_method === 'internal')
                                                        <button wire:click="openApplicationModal({{ $job->id }})" 
                                                            class="flex-1 bg-green-600 text-white px-4 py-3 rounded-lg hover:bg-green-700 transition-colors font-medium">
                                                            Apply Now
                                                        </button>
                                                    @elseif($job->application_method === 'external_link')
                                                        <a href="{{ $job->application_url }}" target="_blank" 
                                                           class="flex-1 bg-green-600 text-white px-4 py-3 rounded-lg hover:bg-green-700 transition-colors font-medium text-center">
                                                            Apply on Site
                                                        </a>
                                                    @elseif($job->application_method === 'email')
                                                        <a href="mailto:{{ $job->application_email }}?subject=Application for {{ $job->title }}" 
                                                           class="flex-1 bg-green-600 text-white px-4 py-3 rounded-lg hover:bg-green-700 transition-colors font-medium text-center">
                                                            Apply via Email
                                                        </a>
                                                    @endif
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
                            <div class="mx-auto w-32 h-32 bg-gradient-to-br from-blue-100 to-purple-100 rounded-full flex items-center justify-center mb-6">
                                <svg class="w-16 h-16 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-semibold text-gray-700 mb-2">No jobs found</h3>
                            <p class="text-gray-500 mb-6">Try adjusting your filters or search terms to find more opportunities.</p>
                            <button wire:click="clearFilters" 
                                class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                Clear Filters
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        @if($activeTab === 'applications')
            <!-- My Applications Section -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">My Applications</h3>
                
                @if(count($userApplications) > 0)
                    <div class="space-y-4">
                        @foreach($userApplications as $application)
                            <div class="border border-gray-200 rounded-lg p-6 hover:border-blue-200 transition-colors">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-center space-x-4">
                                        <img src="{{ $application->job->company_logo_url }}" alt="{{ $application->job->company_name }}" class="w-12 h-12 rounded-full">
                                        <div>
                                            <h4 class="text-lg font-semibold text-gray-900">{{ $application->job->title }}</h4>
                                            <p class="text-gray-600">{{ $application->job->company_name }}</p>
                                            <p class="text-sm text-gray-500">Applied {{ $application->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="bg-{{ $application->status_color }}-100 text-{{ $application->status_color }}-800 text-sm px-3 py-1 rounded-full font-medium">
                                            {{ $application->status_label }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-gray-500">You haven't applied to any jobs yet.</p>
                        <button wire:click="$set('activeTab', 'browse')" class="mt-4 text-blue-600 hover:text-blue-700 font-medium">
                            Browse Jobs
                        </button>
                    </div>
                @endif
            </div>
        @endif

        @if($activeTab === 'saved')
            <!-- Saved Jobs Section -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">Saved Jobs</h3>
                
                @if(count($savedJobs) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($savedJobs as $savedJob)
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-blue-200 transition-colors">
                                <div class="flex items-start justify-between mb-3">
                                    <img src="{{ $savedJob->job->company_logo_url }}" alt="{{ $savedJob->job->company_name }}" class="w-10 h-10 rounded-full">
                                    <button wire:click="toggleSaveJob({{ $savedJob->job->id }})" class="text-red-500 hover:text-red-600">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z"/>
                                        </svg>
                                    </button>
                                </div>
                                <h4 class="font-semibold text-gray-900 mb-1">{{ $savedJob->job->title }}</h4>
                                <p class="text-sm text-gray-600 mb-2">{{ $savedJob->job->company_name }}</p>
                                <p class="text-xs text-gray-500 mb-3">Saved {{ $savedJob->created_at->diffForHumans() }}</p>
                                
                                <div class="flex space-x-2">
                                    <button wire:click="viewJob({{ $savedJob->job->id }})" 
                                        class="flex-1 bg-blue-100 text-blue-700 px-3 py-2 rounded-lg hover:bg-blue-200 transition-colors text-sm">
                                        View
                                    </button>
                                    @if(!$this->hasAppliedToJob($savedJob->job->id))
                                        <button wire:click="openApplicationModal({{ $savedJob->job->id }})" 
                                            class="flex-1 bg-green-100 text-green-700 px-3 py-2 rounded-lg hover:bg-green-200 transition-colors text-sm">
                                            Apply
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="mx-auto w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        <p class="text-gray-500">No saved jobs yet.</p>
                        <button wire:click="$set('activeTab', 'browse')" class="mt-4 text-blue-600 hover:text-blue-700 font-medium">
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
            <div class="bg-white rounded-2xl max-w-5xl w-full max-h-[90vh] overflow-hidden">
                <div class="flex justify-between items-center p-6 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                    <div class="flex items-center space-x-4">
                        <img src="{{ $selectedJob->company_logo_url }}" alt="{{ $selectedJob->company_name }}" class="w-16 h-16 rounded-full">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">{{ $selectedJob->title }}</h2>
                            <p class="text-lg text-gray-600">{{ $selectedJob->company_name }}</p>
                        </div>
                    </div>
                    <button wire:click="closeJobDetails" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="overflow-y-auto max-h-[calc(90vh-200px)]">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 p-6">
                        <div class="lg:col-span-2">
                            <!-- Job Description -->
                            <div class="mb-8">
                                <h3 class="text-xl font-semibold text-gray-900 mb-4">Job Description</h3>
                                <div class="prose prose-gray max-w-none">
                                    {!! nl2br(e($selectedJob->description)) !!}
                                </div>
                            </div>

                            <!-- Requirements -->
                            @if($selectedJob->requirements)
                                <div class="mb-8">
                                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Requirements</h3>
                                    <div class="prose prose-gray max-w-none">
                                        {!! nl2br(e($selectedJob->requirements)) !!}
                                    </div>
                                </div>
                            @endif

                            <!-- Responsibilities -->
                            @if($selectedJob->responsibilities)
                                <div class="mb-8">
                                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Responsibilities</h3>
                                    <div class="prose prose-gray max-w-none">
                                        {!! nl2br(e($selectedJob->responsibilities)) !!}
                                    </div>
                                </div>
                            @endif

                            <!-- Benefits -->
                            @if($selectedJob->benefits)
                                <div class="mb-8">
                                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Benefits</h3>
                                    <div class="prose prose-gray max-w-none">
                                        {!! nl2br(e($selectedJob->benefits)) !!}
                                    </div>
                                </div>
                            @endif

                            <!-- Skills Required -->
                            @if($selectedJob->skills_required)
                                <div class="mb-8">
                                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Skills Required</h3>
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
                            <div class="bg-gray-50 rounded-xl p-6 sticky top-0">
                                <h3 class="text-lg font-semibold text-gray-900 mb-6">Job Details</h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Employment Type</dt>
                                        <dd class="text-lg text-gray-900">{{ $selectedJob->employment_type_label }}</dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Work Type</dt>
                                        <dd class="text-lg text-gray-900">{{ $selectedJob->work_type_icon }} {{ $selectedJob->work_type_label }}</dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Experience Level</dt>
                                        <dd class="text-lg text-gray-900">{{ $selectedJob->experience_level_label }}</dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Location</dt>
                                        <dd class="text-lg text-gray-900">{{ $selectedJob->location_formatted }}</dd>
                                    </div>

                                    @if(!$selectedJob->hide_salary)
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Salary</dt>
                                            <dd class="text-lg font-semibold text-green-600">{{ $selectedJob->salary_range_formatted }}</dd>
                                        </div>
                                    @endif

                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Positions Available</dt>
                                        <dd class="text-lg text-gray-900">{{ $selectedJob->positions_available }}</dd>
                                    </div>

                                    @if($selectedJob->application_deadline)
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Application Deadline</dt>
                                            <dd class="text-lg text-orange-600 font-medium">{{ $selectedJob->days_until_deadline }}</dd>
                                        </div>
                                    @endif

                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Posted</dt>
                                        <dd class="text-lg text-gray-900">{{ $selectedJob->created_at->diffForHumans() }}</dd>
                                    </div>
                                </div>

                                <!-- Company Info -->
                                <div class="mt-8 pt-6 border-t border-gray-200">
                                    <h4 class="font-semibold text-gray-900 mb-4">About {{ $selectedJob->company_name }}</h4>
                                    
                                    @if($selectedJob->company_description)
                                        <p class="text-gray-700 text-sm mb-4">{{ $selectedJob->company_description }}</p>
                                    @endif

                                    <div class="space-y-2 text-sm">
                                        @if($selectedJob->company_size)
                                            <div class="flex justify-between">
                                                <span class="text-gray-500">Size:</span>
                                                <span class="text-gray-900">{{ $selectedJob->company_size }}</span>
                                            </div>
                                        @endif

                                        @if($selectedJob->company_industry)
                                            <div class="flex justify-between">
                                                <span class="text-gray-500">Industry:</span>
                                                <span class="text-gray-900">{{ $selectedJob->company_industry }}</span>
                                            </div>
                                        @endif

                                        @if($selectedJob->company_website)
                                            <div class="mt-4">
                                                <a href="{{ $selectedJob->company_website }}" target="_blank" 
                                                   class="inline-flex items-center text-blue-600 hover:text-blue-700 text-sm">
                                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4.083 9h1.946c.089-1.546.383-2.97.837-4.118A6.004 6.004 0 004.083 9zM10 2a8 8 0 100 16 8 8 0 000-16zm0 2c-.076 0-.232.032-.465.262-.238.234-.497.623-.737 1.182-.389.907-.673 2.142-.766 3.556h3.936c-.093-1.414-.377-2.649-.766-3.556-.24-.56-.5-.948-.737-1.182C10.232 4.032 10.076 4 10 4zm3.971 5c-.089-1.546-.383-2.97-.837-4.118A6.004 6.004 0 0115.917 9h-1.946zm-2.003 2H8.032c.093 1.414.377 2.649.766 3.556.24.56.5.948.737 1.182.233.23.389.262.465.262.076 0 .232-.032.465-.262.238-.234.498-.623.737-1.182.389-.907.673-2.142.766-3.556zm1.166 4.118c.454-1.147.748-2.572.837-4.118h1.946a6.004 6.004 0 01-2.783 4.118zm-6.268 0C6.412 13.97 6.118 12.546 6.03 11H4.083a6.004 6.004 0 002.783 4.118z"/>
                                                    </svg>
                                                    Visit Website
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="mt-8 space-y-3">
                                    @auth
                                        @if($this->hasAppliedToJob($selectedJob->id))
                                            <button disabled class="w-full bg-gray-300 text-gray-500 px-6 py-3 rounded-lg cursor-not-allowed font-medium">
                                                Already Applied
                                            </button>
                                        @else
                                            @if($selectedJob->application_method === 'internal')
                                                <button wire:click="openApplicationModal({{ $selectedJob->id }})" 
                                                    class="w-full bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors font-medium">
                                                    Apply for this Job
                                                </button>
                                            @elseif($selectedJob->application_method === 'external_link')
                                                <a href="{{ $selectedJob->application_url }}" target="_blank" 
                                                   class="w-full bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors font-medium text-center block">
                                                    Apply on Company Site
                                                </a>
                                            @elseif($selectedJob->application_method === 'email')
                                                <a href="mailto:{{ $selectedJob->application_email }}?subject=Application for {{ $selectedJob->title }}" 
                                                   class="w-full bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors font-medium text-center block">
                                                    Apply via Email
                                                </a>
                                            @endif
                                        @endif

                                        <button wire:click="toggleSaveJob({{ $selectedJob->id }})" 
                                            class="w-full {{ $this->isJobSaved($selectedJob->id) ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-blue-100 text-blue-700 hover:bg-blue-200' }} px-6 py-3 rounded-lg transition-colors font-medium">
                                            {{ $this->isJobSaved($selectedJob->id) ? 'Remove from Saved' : 'Save Job' }}
                                        </button>
                                    @else
                                        <button onclick="window.location.href='{{ route('login') }}'" 
                                            class="w-full bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors font-medium">
                                            Login to Apply
                                        </button>
                                    @endauth
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
            <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden">
                <div class="flex justify-between items-center p-6 border-b border-gray-200 bg-gradient-to-r from-green-50 to-blue-50">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Apply for Job</h2>
                        <p class="text-gray-600">{{ $applicationJob->title }} at {{ $applicationJob->company_name }}</p>
                    </div>
                    <button wire:click="closeApplicationModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="submitApplication" class="p-6 overflow-y-auto max-h-[calc(90vh-200px)]">
                    <div class="space-y-6">
                        <!-- Cover Letter -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Cover Letter *</label>
                            <textarea wire:model="coverLetter" rows="8" 
                                placeholder="Write a compelling cover letter explaining why you're the perfect fit for this role..."
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                            @error('coverLetter') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            <p class="text-sm text-gray-500 mt-1">{{ strlen($coverLetter) }}/2000 characters</p>
                        </div>

                        <!-- Resume Upload -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Resume/CV</label>
                            <input wire:model="resume" type="file" accept=".pdf,.doc,.docx" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            @error('resume') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            <p class="text-sm text-gray-500 mt-1">PDF, DOC, or DOCX files up to 5MB</p>
                        </div>

                        <!-- Screening Questions -->
                        @if($applicationJob->screening_questions)
                            <div class="pt-6 border-t border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Screening Questions</h3>
                                <div class="space-y-4">
                                    @foreach($applicationJob->screening_questions as $index => $question)
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                {{ $question['question'] }}
                                                @if($question['required']) <span class="text-red-500">*</span> @endif
                                            </label>
                                            
                                            @if($question['type'] === 'text')
                                                <input wire:model="customResponses.{{ $index }}" type="text" 
                                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            @elseif($question['type'] === 'textarea')
                                                <textarea wire:model="customResponses.{{ $index }}" rows="3"
                                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                                            @elseif($question['type'] === 'select' && isset($question['options']))
                                                <select wire:model="customResponses.{{ $index }}" 
                                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
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

                    <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                        <button type="button" wire:click="closeApplicationModal" 
                            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors">
                            Cancel
                        </button>
                        <button type="submit" 
                            class="bg-green-600 text-white px-8 py-3 rounded-xl hover:bg-green-700 transition-colors font-semibold">
                            <span wire:loading.remove wire:target="submitApplication">Submit Application</span>
                            <span wire:loading wire:target="submitApplication">
                                <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
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
            <div class="bg-white rounded-2xl max-w-lg w-full">
                <div class="flex justify-between items-center p-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">Create Job Alert</h2>
                    <button wire:click="$set('showJobAlertModal', false)" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="createJobAlert" class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Keywords</label>
                            <input wire:model="alertKeywords" type="text" placeholder="e.g. PHP, Laravel, Developer"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                            <input wire:model="alertLocation" type="text" placeholder="e.g. Lagos, Remote"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <select wire:model="alertCategory" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">All Categories</option>
                                @foreach($categories as $slug => $name)
                                    <option value="{{ $slug }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4 mt-6">
                        <button type="button" wire:click="$set('showJobAlertModal', false)" 
                            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors">
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