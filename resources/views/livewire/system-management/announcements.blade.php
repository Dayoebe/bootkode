<div class="px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-cyan-600 to-blue-700 p-6 rounded-2xl shadow-xl text-white mb-8 animate__animated animate__fadeIn">
        <h1 class="text-3xl font-bold text-white">
            <i class="fas fa-bullhorn mr-2"></i> Announcements
        </h1>
        <p class="text-cyan-100 mt-2">View platform and course announcements</p>
    </div>

    <!-- Filters -->
    <div class="bg-themed-secondary shadow rounded-xl p-6 mb-8 animate__animated animate__fadeInUp border border-themed-primary">
        <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
            <!-- Search -->
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-themed-primary mb-2">Search Announcements</label>
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-3 text-themed-secondary"></i>
                    <input wire:model.live.debounce.300ms="search" type="text" id="search"
                           placeholder="Search announcements..."
                           class="w-full pl-10 pr-4 py-2 border border-themed-primary bg-themed-tertiary rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-themed-primary">
                </div>
            </div>

            <!-- Course Filter -->
            <div class="flex-1">
                <label for="courseFilter" class="block text-sm font-medium text-themed-primary mb-2">Filter by Course</label>
                <select wire:model.live="courseFilter" id="courseFilter"
                        class="w-full px-4 py-2 border border-themed-primary bg-themed-secondary rounded-lg shadow-sm focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-themed-primary">
                    <option value="">All Courses</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div x-data="{ activeTab: @entangle('activeTab') }" class="mb-8">
        <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary overflow-hidden">
            <nav class="flex border-b border-themed-primary" aria-label="Tabs">
                <button @click="activeTab = 'all'"
                        :class="{ 
                            'border-b-2 border-cyan-500 text-cyan-600 dark:text-cyan-400 bg-cyan-100/10 dark:bg-cyan-900/20': activeTab === 'all', 
                            'border-b-2 border-transparent text-themed-secondary hover:text-themed-primary': activeTab !== 'all' 
                        }"
                        class="flex-1 whitespace-nowrap py-4 px-4 font-medium text-sm flex items-center justify-center transition-all duration-300">
                    <i class="fas fa-list mr-2"></i> All Announcements
                </button>
                <button @click="activeTab = 'my_courses'"
                        :class="{ 
                            'border-b-2 border-cyan-500 text-cyan-600 dark:text-cyan-400 bg-cyan-100/10 dark:bg-cyan-900/20': activeTab === 'my_courses', 
                            'border-b-2 border-transparent text-themed-secondary hover:text-themed-primary': activeTab !== 'my_courses' 
                        }"
                        class="flex-1 whitespace-nowrap py-4 px-4 font-medium text-sm flex items-center justify-center transition-all duration-300">
                    <i class="fas fa-graduation-cap mr-2"></i> My Courses
                </button>
            </nav>
        </div>
    </div>

    <!-- Announcements List -->
    <div class="bg-themed-secondary shadow rounded-xl p-6 animate__animated animate__fadeInUp border border-themed-primary">
        <div class="space-y-6">
            @forelse($announcements as $announcement)
                <div class="border border-themed-primary rounded-lg p-6 hover:shadow-lg hover:bg-themed-tertiary transition-all duration-200">
                    <!-- Header -->
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex items-start space-x-3 flex-1">
                            <!-- Icon -->
                            <div class="flex-shrink-0">
                                <div class="flex items-center justify-center h-10 w-10 rounded-lg bg-cyan-100 dark:bg-cyan-900/30">
                                    <i class="fas fa-megaphone text-cyan-600 dark:text-cyan-400"></i>
                                </div>
                            </div>
                            
                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <h3 class="text-lg font-semibold text-themed-primary">{{ $announcement->title }}</h3>
                                <p class="text-sm text-themed-secondary mt-1">
                                    <i class="fas fa-user mr-1"></i>{{ $announcement->user->name }}
                                    <span class="mx-2">•</span>
                                    <i class="fas fa-calendar mr-1"></i>{{ $announcement->published_at->format('M d, Y \a\t g:i A') }}
                                </p>
                                
                                @if($announcement->course)
                                    <div class="mt-2">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-cyan-100 dark:bg-cyan-900/30 text-cyan-700 dark:text-cyan-300">
                                            <i class="fas fa-book mr-1"></i>{{ $announcement->course->title }}
                                        </span>
                                    </div>
                                @else
                                    <div class="mt-2">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                                            <i class="fas fa-globe mr-1"></i>Platform-Wide
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Status Indicator -->
                        <div class="flex-shrink-0 ml-4">
                            @if($announcement->status === 'published')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300">
                                    <i class="fas fa-check-circle mr-1"></i>Published
                                </span>
                            @elseif($announcement->status === 'draft')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300">
                                    <i class="fas fa-edit mr-1"></i>Draft
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                    <i class="fas fa-archive mr-1"></i>Archived
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Content Preview -->
                    <div class="ml-13 border-l-2 border-themed-primary pl-4">
                        <p class="text-themed-secondary text-sm leading-relaxed">
                            {{ Str::limit($announcement->content, 300) }}
                        </p>
                    </div>

                    <!-- Footer with Read More -->
                    <div class="mt-4 pt-4 border-t border-themed-primary flex items-center justify-between">
                        <div class="flex items-center space-x-4 text-xs text-themed-secondary">
                            <span><i class="fas fa-clock mr-1"></i>{{ $announcement->created_at->diffForHumans() }}</span>
                            @if($announcement->updated_at->ne($announcement->created_at))
                                <span><i class="fas fa-pencil-alt mr-1"></i>Updated {{ $announcement->updated_at->diffForHumans() }}</span>
                            @endif
                        </div>
                        
                        @if(strlen($announcement->content) > 300)
                            <a href="#" class="inline-flex items-center text-cyan-600 dark:text-cyan-400 hover:text-cyan-800 dark:hover:text-cyan-300 text-sm font-medium transition-colors">
                                Read More <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <!-- Empty State -->
                <div class="text-center py-16">
                    <div class="bg-themed-tertiary rounded-full p-6 w-24 h-24 mx-auto mb-4 flex items-center justify-center">
                        <i class="fas fa-inbox fa-2x text-themed-secondary"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-themed-primary mb-2">No announcements found</h3>
                    <p class="text-themed-secondary mb-6">There are no announcements to display at the moment.</p>
                    <button wire:click="$set('search', '')" wire:click="$set('courseFilter', '')"
                            class="inline-flex items-center px-4 py-2 bg-cyan-100 dark:bg-cyan-900/30 text-cyan-700 dark:text-cyan-400 rounded-lg hover:bg-cyan-200 dark:hover:bg-cyan-900/50 transition-colors text-sm font-medium">
                        <i class="fas fa-redo mr-2"></i>Clear Filters
                    </button>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($announcements->hasPages())
            <div class="mt-8 pt-6 border-t border-themed-primary flex justify-center">
                {{ $announcements->links() }}
            </div>
        @endif
    </div>

    <!-- Quick Stats -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
        <div class="bg-themed-secondary rounded-lg p-4 border border-themed-primary">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-cyan-100 dark:bg-cyan-900/30">
                    <i class="fas fa-megaphone text-cyan-600 dark:text-cyan-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-themed-secondary">Total Announcements</p>
                    <p class="text-2xl font-bold text-themed-primary">{{ $announcements->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-themed-secondary rounded-lg p-4 border border-themed-primary">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-green-100 dark:bg-green-900/30">
                    <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-themed-secondary">Published</p>
                    <p class="text-2xl font-bold text-themed-primary">{{ $announcements->where('status', 'published')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-themed-secondary rounded-lg p-4 border border-themed-primary">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-blue-100 dark:bg-blue-900/30">
                    <i class="fas fa-book text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-themed-secondary">Courses</p>
                    <p class="text-2xl font-bold text-themed-primary">{{ $courses->count() }}</p>
                </div>
            </div>
        </div>
    </div>
</div>