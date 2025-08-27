<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-gray-50">
    <!-- Header Section with Enhanced Search -->
    <div class="bg-white shadow-lg border-b">
        <div class="container mx-auto px-6 py-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">Find Your Dream Job</h1>
                    <p class="text-xl text-gray-600">Discover opportunities that match your skills and aspirations</p>
                </div>
                <div class="mt-4 lg:mt-0">
                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                        @if($total_jobs > 0)
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ number_format($total_jobs) }} jobs found
                            </span>
                        @endif
                        @if($search_time)
                            <span>Search completed in {{ $search_time }}ms</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Main Search Bar -->
            <div class="bg-gray-50 rounded-2xl p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-700 mb-2">What job are you looking for?</label>
                        <input wire:model.live.debounce.300ms="sector" 
                               type="text" 
                               placeholder="e.g., Software Developer, Marketing Manager"
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        <svg class="absolute left-3 top-11 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    
                    <div class="relative">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                        <input wire:model.live.debounce.300ms="location" 
                               type="text" 
                               placeholder="e.g., London, Manchester, Remote"
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                        <svg class="absolute left-3 top-11 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                        <select wire:model.live="country_code" 
                                class="w-full py-3 px-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            <option value="gb">🇬🇧 United Kingdom</option>
                            <option value="us">🇺🇸 United States</option>
                            <option value="za">🇿🇦 South Africa</option>
                            <option value="ng">🇳🇬 Nigeria</option>
                            <option value="au">🇦🇺 Australia</option>
                            <option value="ca">🇨🇦 Canada</option>
                            <option value="de">🇩🇪 Germany</option>
                            <option value="fr">🇫🇷 France</option>
                        </select>
                    </div>

                    <div class="flex items-end">
                        <button wire:click="search" 
                                class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-3 rounded-xl hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-300 font-semibold shadow-lg hover:shadow-xl">
                            <span wire:loading.remove wire:target="search">🔍 Search Jobs</span>
                            <span wire:loading wire:target="search">Searching...</span>
                        </button>
                    </div>
                </div>

                <!-- Advanced Filters Toggle -->
                <div class="flex justify-between items-center">
                    <button wire:click="$toggle('show_filters')" 
                            class="text-blue-600 hover:text-blue-700 font-medium transition-colors">
                        {{ $show_filters ? '▲ Hide Advanced Filters' : '▼ Show Advanced Filters' }}
                    </button>
                    
                    @if($sector || $location || $salary_min || $salary_max || $contract_type || $company)
                        <button wire:click="clearFilters" 
                                class="text-red-600 hover:text-red-700 font-medium transition-colors">
                            Clear All Filters
                        </button>
                    @endif
                </div>

                <!-- Advanced Filters -->
                @if($show_filters)
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Salary (£000s)</label>
                                <input wire:model.live.debounce.500ms="salary_min" 
                                       type="number" 
                                       placeholder="e.g., 30"
                                       class="w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Maximum Salary (£000s)</label>
                                <input wire:model.live.debounce.500ms="salary_max" 
                                       type="number" 
                                       placeholder="e.g., 80"
                                       class="w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Contract Type</label>
                                <select wire:model.live="contract_type" 
                                        class="w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">All Types</option>
                                    <option value="permanent">Permanent</option>
                                    <option value="contract">Contract</option>
                                    <option value="full_time">Full Time</option>
                                    <option value="part_time">Part Time</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Company</label>
                                <input wire:model.live.debounce.300ms="company" 
                                       type="text" 
                                       placeholder="e.g., Google, Microsoft"
                                       class="w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Popular Searches -->
            @if(empty($sector) && count($popular_searches) > 0)
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-3">🔥 Popular Searches</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($popular_searches as $search)
                            <button wire:click="usePopularSearch('{{ $search['term'] }}')" 
                                    class="bg-white border border-gray-200 text-gray-700 px-4 py-2 rounded-full hover:bg-blue-50 hover:border-blue-300 transition-all text-sm">
                                {{ $search['term'] }} <span class="text-gray-400 ml-1">({{ $search['count'] }})</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session('success'))
        <div class="container mx-auto px-6 pt-4">
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="container mx-auto px-6 pt-4">
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Main Content Area -->
    <div class="container mx-auto px-6 py-8">
        <!-- Controls Bar -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6">
            <div class="flex items-center space-x-4 mb-4 sm:mb-0">
                <select wire:model.live="sort_by" 
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    <option value="date">Latest Jobs</option>
                    <option value="salary">Highest Salary</option>
                    <option value="relevance">Most Relevant</option>
                </select>
            </div>

            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-600">View:</span>
                <button wire:click="$set('view_mode', 'grid')" 
                        class="p-2 rounded-lg {{ $view_mode === 'grid' ? 'bg-blue-100 text-blue-600' : 'text-gray-400 hover:text-gray-600' }} transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                </button>
                <button wire:click="$set('view_mode', 'list')" 
                        class="p-2 rounded-lg {{ $view_mode === 'list' ? 'bg-blue-100 text-blue-600' : 'text-gray-400 hover:text-gray-600' }} transition-colors">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Loading State -->
        @if ($loading)
            <div class="flex justify-center items-center py-16">
                <div class="text-center">
                    <div class="animate-spin rounded-full h-16 w-16 border-4 border-blue-500 border-t-transparent mx-auto mb-4"></div>
                    <p class="text-gray-600">Finding the perfect jobs for you...</p>
                </div>
            </div>

        <!-- Jobs Display -->
        @elseif (count($jobs) > 0)
            @if($view_mode === 'grid')
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach ($jobs as $job)
                        <div wire:key="{{ $job['id'] }}" class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border border-gray-100 hover:border-blue-200 relative group">
                            <!-- Job Match Score -->
                            @if($job['match_score'] >= 70)
                                <div class="absolute top-4 right-4">
                                    <span class="bg-green-100 text-green-800 text-xs font-semibold px-2 py-1 rounded-full">
                                        {{ $job['match_score'] }}% Match
                                    </span>
                                </div>
                            @endif

                            <!-- Save/Unsave Button -->
                            <button wire:click="saveJob({{ $job['id'] }})" 
                                    class="absolute top-4 left-4 p-2 rounded-full {{ $job['is_saved'] ? 'text-red-500 bg-red-50' : 'text-gray-400 hover:text-red-500 hover:bg-red-50' }} transition-all">
                                <svg class="w-5 h-5" fill="{{ $job['is_saved'] ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                            </button>

                            <div class="mt-8">
                                <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors cursor-pointer line-clamp-2" 
                                    wire:click="viewJobDetails({{ $job['id'] }})">
                                    {{ $job['title'] }}
                                </h3>
                                
                                <div class="space-y-2 mb-4 text-sm text-gray-600">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                            <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z"/>
                                        </svg>
                                        <span class="font-medium">{{ $job['company']['display_name'] ?? 'Company Name' }}</span>
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>{{ $job['location']['display_name'] ?? 'Location' }}</span>
                                    </div>

                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="font-semibold text-green-600">{{ $job['salary_formatted'] }}</span>
                                    </div>

                                    <div class="flex items-center text-gray-500">
                                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>{{ $job['posted_time'] }}</span>
                                    </div>
                                </div>

                                <!-- Job Description Preview -->
                                @if(isset($job['description']))
                                    <p class="text-gray-600 text-sm line-clamp-3 mb-4">
                                        {{ Str::limit(strip_tags($job['description']), 120) }}
                                    </p>
                                @endif

                                <!-- Action Buttons -->
                                <div class="flex space-x-2">
                                    <button wire:click="viewJobDetails({{ $job['id'] }})" 
                                            class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-all text-sm font-medium">
                                        View Details
                                    </button>
                                    
                                    @if($job['is_applied'])
                                        <button class="px-4 py-2 bg-green-100 text-green-700 rounded-lg text-sm font-medium cursor-not-allowed">
                                            Applied ✓
                                        </button>
                                    @else
                                        <a href="{{ $job['redirect_url'] }}" 
                                           target="_blank"
                                           wire:click="applyToJob({{ $job['id'] }})"
                                           class="px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:from-green-700 hover:to-emerald-700 transition-all text-sm font-medium">
                                            Apply
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- List View -->
                <div class="space-y-4">
                    @foreach ($jobs as $job)
                        <div wire:key="{{ $job['id'] }}" class="bg-white rounded-xl shadow-md hover:shadow-lg transition-all duration-300 p-6 border border-gray-100 hover:border-blue-200">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                                <div class="flex-1">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1">
                                            <h3 class="text-xl font-bold text-gray-900 hover:text-blue-600 transition-colors cursor-pointer mb-1"
                                                wire:click="viewJobDetails({{ $job['id'] }})">
                                                {{ $job['title'] }}
                                            </h3>
                                            
                                            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600">
                                                <span class="font-medium">{{ $job['company']['display_name'] ?? 'Company' }}</span>
                                                <span>{{ $job['location']['display_name'] ?? 'Location' }}</span>
                                                <span class="font-semibold text-green-600">{{ $job['salary_formatted'] }}</span>
                                                <span class="text-gray-500">{{ $job['posted_time'] }}</span>
                                            </div>
                                        </div>

                                        <div class="flex items-center space-x-2 ml-4">
                                            @if($job['match_score'] >= 70)
                                                <span class="bg-green-100 text-green-800 text-xs font-semibold px-2 py-1 rounded-full whitespace-nowrap">
                                                    {{ $job['match_score'] }}% Match
                                                </span>
                                            @endif

                                            <button wire:click="saveJob({{ $job['id'] }})" 
                                                    class="p-2 rounded-full {{ $job['is_saved'] ? 'text-red-500 bg-red-50' : 'text-gray-400 hover:text-red-500 hover:bg-red-50' }} transition-all">
                                                <svg class="w-5 h-5" fill="{{ $job['is_saved'] ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    @if(isset($job['description']))
                                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                            {{ Str::limit(strip_tags($job['description']), 200) }}
                                        </p>
                                    @endif
                                </div>

                                <div class="flex space-x-3 mt-4 lg:mt-0 lg:ml-6">
                                    <button wire:click="viewJobDetails({{ $job['id'] }})" 
                                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all font-medium">
                                        View Details
                                    </button>
                                    
                                    @if($job['is_applied'])
                                        <button class="px-6 py-2 bg-green-100 text-green-700 rounded-lg font-medium cursor-not-allowed">
                                            Applied ✓
                                        </button>
                                    @else
                                        <a href="{{ $job['redirect_url'] }}" 
                                           target="_blank"
                                           wire:click="applyToJob({{ $job['id'] }})"
                                           class="px-6 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg hover:from-green-700 hover:to-emerald-700 transition-all font-medium">
                                            Apply Now
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Pagination -->
            <div class="mt-12 flex justify-center">
                <div class="flex items-center space-x-4">
                    <button wire:click="previousPage" 
                            @if($currentPage <= 1) disabled @endif
                            class="px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                        ← Previous
                    </button>
                    
                    <span class="px-4 py-3 bg-blue-600 text-white rounded-lg font-medium">
                        Page {{ $currentPage }}
                    </span>
                    
                    <button wire:click="nextPage" 
                            @if(count($jobs) < 12) disabled @endif
                            class="px-6 py-3 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                        Next →
                    </button>
                </div>
            </div>

        @else
            <!-- No Jobs Found -->
            <div class="text-center py-16">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">No jobs found</h3>
                <p class="text-gray-500 mb-6">Try adjusting your search criteria or check back later for new opportunities.</p>
                <button wire:click="clearFilters" 
                        class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-all font-medium">
                    Clear Filters & Search Again
                </button>
            </div>
        @endif
    </div>

    <!-- Job Details Modal -->
    @if($show_job_modal && $selected_job)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" wire:click="closeJobModal">
            <div class="bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden" wire:click.stop>
                <div class="flex justify-between items-center p-6 border-b border-gray-200">
                    <h2 class="text-2xl font-bold text-gray-900">Job Details</h2>
                    <button wire:click="closeJobModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <div class="lg:col-span-2">
                            <div class="mb-6">
                                <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $selected_job['title'] }}</h3>
                                <div class="flex flex-wrap items-center gap-4 text-gray-600 mb-4">
                                    <span class="font-medium">{{ $selected_job['company']['display_name'] ?? 'Company' }}</span>
                                    <span>{{ $selected_job['location']['display_name'] ?? 'Location' }}</span>
                                    <span class="font-semibold text-green-600">{{ $selected_job['salary_formatted'] }}</span>
                                </div>

                                @if($selected_job['match_score'] >= 70)
                                    <div class="inline-block bg-green-100 text-green-800 text-sm font-semibold px-3 py-1 rounded-full mb-4">
                                        {{ $selected_job['match_score'] }}% Match - Great fit for you!
                                    </div>
                                @endif
                            </div>

                            @if(isset($selected_job['description']))
                                <div class="mb-6">
                                    <h4 class="text-lg font-semibold text-gray-900 mb-3">Job Description</h4>
                                    <div class="prose prose-sm max-w-none text-gray-700">
                                        {!! $selected_job['description'] !!}
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="lg:col-span-1">
                            <div class="bg-gray-50 rounded-xl p-6 sticky top-0">
                                <h4 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h4>
                                
                                <div class="space-y-3 mb-6">
                                    <button wire:click="saveJob({{ $selected_job['id'] }})" 
                                            class="w-full flex items-center justify-center px-4 py-3 {{ $selected_job['is_saved'] ? 'bg-red-100 text-red-700 border border-red-200' : 'bg-white text-gray-700 border border-gray-200' }} rounded-lg hover:bg-opacity-80 transition-all">
                                        <svg class="w-5 h-5 mr-2" fill="{{ $selected_job['is_saved'] ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                        </svg>
                                        {{ $selected_job['is_saved'] ? 'Remove from Saved' : 'Save Job' }}
                                    </button>

                                    @if($selected_job['is_applied'])
                                        <button class="w-full px-4 py-3 bg-green-100 text-green-700 border border-green-200 rounded-lg cursor-not-allowed">
                                            ✓ Already Applied
                                        </button>
                                    @else
                                        <a href="{{ $selected_job['redirect_url'] }}" 
                                           target="_blank"
                                           wire:click="applyToJob({{ $selected_job['id'] }})"
                                           class="w-full block text-center px-4 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all font-medium">
                                            Apply Now
                                        </a>
                                    @endif
                                </div>

                                <div class="border-t border-gray-200 pt-4">
                                    <h5 class="font-medium text-gray-900 mb-3">Job Information</h5>
                                    <div class="space-y-2 text-sm text-gray-600">
                                        <div class="flex justify-between">
                                            <span>Posted:</span>
                                            <span>{{ $selected_job['posted_time'] }}</span>
                                        </div>
                                        @if(isset($selected_job['category']['label']))
                                            <div class="flex justify-between">
                                                <span>Category:</span>
                                                <span>{{ $selected_job['category']['label'] }}</span>
                                            </div>
                                        @endif
                                        <div class="flex justify-between">
                                            <span>Job ID:</span>
                                            <span>{{ $selected_job['id'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    <style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
        </div>