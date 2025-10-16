<div class="min-h-screen bg-themed-primary transition-colors duration-300 p-4 md:p-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-themed-secondary rounded-2xl shadow-sm border border-themed-primary p-6 relative overflow-hidden transition-colors duration-300">
            <!-- Background decoration -->
            <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-purple-100 to-pink-100 dark:from-purple-900/30 dark:to-pink-900/30 rounded-full -translate-y-12 translate-x-12 opacity-60"></div>
            
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center space-y-4 lg:space-y-0 relative">
                <div>
                    <h1 class="text-3xl font-bold text-themed-primary flex items-center transition-colors duration-300">
                        <i class="fas fa-pen-fancy text-purple-600 dark:text-purple-400 mr-3"></i>
                        Content Editor Dashboard
                    </h1>
                    <p class="text-themed-secondary mt-1 transition-colors duration-300">Welcome back, {{ auth()->user()->name }}! Create and manage compelling content.</p>
                    
                    <div class="flex items-center space-x-4 mt-3">
                        <div class="flex items-center bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300 px-3 py-1 rounded-full text-sm transition-colors duration-300">
                            <i class="fas fa-eye mr-2"></i>
                            {{ number_format($this->overviewStats['total_views']) }} total views
                        </div>
                        <div class="flex items-center bg-pink-100 dark:bg-pink-900/30 text-pink-800 dark:text-pink-300 px-3 py-1 rounded-full text-sm transition-colors duration-300">
                            <i class="fas fa-heart mr-2"></i>
                            {{ number_format($this->overviewStats['total_likes']) }} likes
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-wrap items-center space-x-3">
                    <!-- Content Type Filter -->
                    <select wire:model.live="selectedContentType" class="bg-themed-secondary border border-themed-secondary text-themed-primary px-3 py-2 text-sm rounded-lg focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 focus:border-purple-500 dark:focus:border-purple-400 transition-colors duration-300">
                        <option value="all">All Content</option>
                        <option value="blog_posts">Blog Posts</option>
                        <option value="pages">Pages</option>
                        <option value="faqs">FAQs</option>
                        <option value="announcements">Announcements</option>
                    </select>
                    
                    <!-- Timeframe Selector -->
                    <div class="flex bg-themed-tertiary rounded-lg p-1 transition-colors duration-300">
                        @foreach(['7days' => '7d', '30days' => '30d', '90days' => '90d', '12months' => '12m'] as $value => $label)
                            <button 
                                wire:click="updateTimeframe('{{ $value }}')"
                                class="px-3 py-1 rounded-md text-sm font-medium transition-all duration-200 {{ $selectedTimeframe === $value ? 'bg-themed-secondary text-purple-600 dark:text-purple-400 shadow-sm' : 'text-themed-secondary hover:text-themed-primary' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                    
                    <!-- Quick Actions -->
                    <a href="{{ route('admin.blog.posts.create') }}" class="bg-purple-600 dark:bg-purple-500 hover:bg-purple-700 dark:hover:bg-purple-600 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                        <i class="fas fa-plus mr-2"></i>
                        New Post
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Overview Stats Grid -->
    @if($showWidgets['overview_stats'])
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-6 hover:shadow-md dark:hover:shadow-xl transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-themed-secondary font-medium transition-colors duration-300">Total Posts</p>
                    <h3 class="text-3xl font-bold text-themed-primary mt-1 transition-colors duration-300">{{ $this->overviewStats['total_posts'] }}</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="text-green-600 dark:text-green-400 font-medium transition-colors duration-300">{{ $this->overviewStats['published_posts'] }}</span>
                        <span class="text-themed-tertiary ml-1 transition-colors duration-300">published</span>
                    </div>
                </div>
                <div class="bg-purple-100 dark:bg-purple-900/30 p-3 rounded-full transition-colors duration-300">
                    <i class="fas fa-file-alt text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-6 hover:shadow-md dark:hover:shadow-xl transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-themed-secondary font-medium transition-colors duration-300">Total Views</p>
                    <h3 class="text-3xl font-bold text-themed-primary mt-1 transition-colors duration-300">{{ number_format($this->overviewStats['total_views']) }}</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="text-blue-600 dark:text-blue-400 font-medium transition-colors duration-300">{{ number_format($this->overviewStats['total_views'] / max($this->overviewStats['published_posts'], 1), 0) }}</span>
                        <span class="text-themed-tertiary ml-1 transition-colors duration-300">avg per post</span>
                    </div>
                </div>
                <div class="bg-blue-100 dark:bg-blue-900/30 p-3 rounded-full transition-colors duration-300">
                    <i class="fas fa-eye text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-6 hover:shadow-md dark:hover:shadow-xl transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-themed-secondary font-medium transition-colors duration-300">Engagement</p>
                    <h3 class="text-3xl font-bold text-themed-primary mt-1 transition-colors duration-300">{{ number_format($this->overviewStats['total_likes'] + $this->overviewStats['total_comments']) }}</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="text-pink-600 dark:text-pink-400 font-medium transition-colors duration-300">{{ $this->overviewStats['total_comments'] }}</span>
                        <span class="text-themed-tertiary ml-1 transition-colors duration-300">comments</span>
                    </div>
                </div>
                <div class="bg-pink-100 dark:bg-pink-900/30 p-3 rounded-full transition-colors duration-300">
                    <i class="fas fa-heart text-pink-600 dark:text-pink-400 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-6 hover:shadow-md dark:hover:shadow-xl transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-themed-secondary font-medium transition-colors duration-300">Avg Read Time</p>
                    <h3 class="text-3xl font-bold text-themed-primary mt-1 transition-colors duration-300">{{ number_format($this->overviewStats['avg_read_time'], 1) }}m</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="text-green-600 dark:text-green-400 font-medium transition-colors duration-300">{{ $this->overviewStats['featured_posts'] }}</span>
                        <span class="text-themed-tertiary ml-1 transition-colors duration-300">featured</span>
                    </div>
                </div>
                <div class="bg-green-100 dark:bg-green-900/30 p-3 rounded-full transition-colors duration-300">
                    <i class="fas fa-clock text-green-600 dark:text-green-400 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <!-- Left Column -->
        <div class="xl:col-span-2 space-y-8">
            <!-- Content Analytics -->
            @if($showWidgets['content_analytics'])
            <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                <h2 class="text-xl font-bold text-themed-primary mb-6 transition-colors duration-300">Content Analytics</h2>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Content Trends Chart -->
                    <div>
                        <h3 class="text-sm font-semibold text-themed-primary mb-3 transition-colors duration-300">Content Performance ({{ $selectedTimeframe === '7days' ? 'Last 7 Days' : ($selectedTimeframe === '30days' ? 'Last 30 Days' : ($selectedTimeframe === '90days' ? 'Last 90 Days' : 'Last 12 Months')) }})</h3>
                        <div class="h-48">
                            <canvas id="contentTrendsChart"></canvas>
                        </div>
                    </div>
                    
                    <!-- Category Performance -->
                    <div>
                        <h3 class="text-sm font-semibold text-themed-primary mb-3 transition-colors duration-300">Category Performance</h3>
                        <div class="space-y-3">
                            @forelse($this->contentAnalytics['category_performance']->take(5) as $category)
                            <div class="flex items-center justify-between p-3 bg-themed-tertiary rounded-lg transition-colors duration-300">
                                <div>
                                    <h4 class="font-medium text-themed-primary transition-colors duration-300">{{ $category['category'] }}</h4>
                                    <p class="text-sm text-themed-secondary transition-colors duration-300">{{ $category['post_count'] }} posts</p>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-bold text-purple-600 dark:text-purple-400 transition-colors duration-300">{{ number_format($category['total_views']) }}</div>
                                    <div class="text-xs text-themed-tertiary transition-colors duration-300">views</div>
                                </div>
                            </div>
                            @empty
                            <p class="text-themed-tertiary text-sm transition-colors duration-300">No category data available</p>
                            @endforelse
                        </div>
                        
                        <!-- Top Performing Posts -->
                        <div class="mt-6 pt-4 border-t border-themed-primary transition-colors duration-300">
                            <h4 class="text-sm font-semibold text-themed-primary mb-3 transition-colors duration-300">Top Performing Posts</h4>
                            <div class="space-y-2">
                                @foreach($this->contentAnalytics['top_performing_posts'] as $post)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-themed-secondary truncate transition-colors duration-300">{{ \Str::limit($post['title'], 25) }}</span>
                                    <span class="font-medium text-purple-600 dark:text-purple-400 transition-colors duration-300">{{ number_format($post['views']) }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Recent Posts -->
            @if($showWidgets['recent_posts'])
            <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-themed-primary transition-colors duration-300">Recent Posts</h2>
                    <a href="{{ route('admin.blog.posts.index') }}" class="text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 text-sm font-medium transition-colors duration-300">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                <div class="space-y-4">
                    @forelse($this->recentPosts as $post)
                    <div class="flex items-center justify-between p-4 bg-themed-tertiary rounded-lg hover:bg-themed-secondary transition-colors duration-200">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3">
                                <div>
                                    <h4 class="font-semibold text-themed-primary transition-colors duration-300">{{ \Str::limit($post['title'], 50) }}</h4>
                                    <div class="flex items-center space-x-4 text-sm text-themed-secondary mt-1 transition-colors duration-300">
                                        <span class="flex items-center">
                                            <i class="fas fa-folder mr-1"></i>
                                            {{ $post['category'] }}
                                        </span>
                                        <span class="flex items-center">
                                            <i class="fas fa-eye mr-1"></i>
                                            {{ number_format($post['views']) }}
                                        </span>
                                        <span class="flex items-center">
                                            <i class="fas fa-heart mr-1"></i>
                                            {{ $post['likes'] }}
                                        </span>
                                        <span class="flex items-center">
                                            <i class="fas fa-comment mr-1"></i>
                                            {{ $post['comments'] }}
                                        </span>
                                        <span>{{ $post['read_time'] }}min read</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <!-- Performance Score -->
                            <div class="text-center">
                                <div class="text-lg font-bold text-purple-600 dark:text-purple-400 transition-colors duration-300">{{ number_format($post['performance_score'], 0) }}</div>
                                <div class="text-xs text-themed-tertiary transition-colors duration-300">score</div>
                            </div>
                            
                            <!-- Status Badge -->
                            <span class="px-3 py-1 text-xs rounded-full {{ $post['status'] === 'published' ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300' : ($post['status'] === 'draft' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300' : 'bg-themed-tertiary text-themed-primary') }} transition-colors duration-300">
                                {{ ucfirst($post['status']) }}
                            </span>
                            
                            @if($post['is_featured'])
                            <span class="px-2 py-1 text-xs bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300 rounded-full transition-colors duration-300">
                                Featured
                            </span>
                            @endif
                            
                            <!-- Quick Publish Button -->
                            @if($post['status'] === 'draft')
                            <button wire:click="quickPublish({{ $post['id'] }})" 
                                    class="text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 text-sm transition-colors duration-300">
                                <i class="fas fa-rocket"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <i class="fas fa-pen-alt text-themed-tertiary text-4xl mb-4 transition-colors duration-300"></i>
                        <p class="text-themed-secondary mb-4 transition-colors duration-300">No posts created yet</p>
                        <a href="{{ route('admin.blog.posts.create') }}" class="bg-purple-600 dark:bg-purple-500 hover:bg-purple-700 dark:hover:bg-purple-600 text-white px-4 py-2 rounded-lg transition-colors">
                            Create Your First Post
                        </a>
                    </div>
                    @endforelse
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column -->
        <div class="space-y-8">
            <!-- Pending Content -->
            @if($showWidgets['pending_content'])
            <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                <h2 class="text-xl font-bold text-themed-primary mb-6 transition-colors duration-300">Pending Content</h2>
                
                <div class="space-y-6">
                    <!-- Draft Posts -->
                    <div>
                        <h3 class="text-sm font-semibold text-themed-primary mb-3 flex items-center transition-colors duration-300">
                            <i class="fas fa-edit text-yellow-600 dark:text-yellow-400 mr-2"></i>
                            Draft Posts ({{ count($this->pendingContent['draft_posts']) }})
                        </h3>
                        <div class="space-y-2">
                            @forelse($this->pendingContent['draft_posts'] as $draft)
                            <div class="flex items-center justify-between p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg transition-colors duration-300">
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-themed-primary truncate transition-colors duration-300">{{ $draft['title'] }}</h4>
                                    <div class="flex items-center space-x-2 text-xs text-themed-secondary mt-1 transition-colors duration-300">
                                        <span>{{ $draft['word_count'] }} words</span>
                                        <span>{{ $draft['completion_percentage'] }}% complete</span>
                                    </div>
                                </div>
                                <div class="text-xs text-themed-tertiary transition-colors duration-300">{{ $draft['created_at']->format('M j') }}</div>
                            </div>
                            @empty
                            <p class="text-themed-tertiary text-sm transition-colors duration-300">No draft posts</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Pending Comments -->
                    <div>
                        <h3 class="text-sm font-semibold text-themed-primary mb-3 flex items-center transition-colors duration-300">
                            <i class="fas fa-comments text-blue-600 dark:text-blue-400 mr-2"></i>
                            Pending Comments ({{ count($this->pendingContent['pending_comments']) }})
                        </h3>
                        <div class="space-y-2">
                            @forelse($this->pendingContent['pending_comments'] as $comment)
                            <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg transition-colors duration-300">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-themed-primary transition-colors duration-300">{{ $comment->user->name }}</h4>
                                        <p class="text-xs text-themed-secondary mt-1 transition-colors duration-300">{{ \Str::limit($comment->content, 60) }}</p>
                                        <p class="text-xs text-themed-tertiary mt-1 transition-colors duration-300">On: {{ \Str::limit($comment->post->title, 30) }}</p>
                                    </div>
                                    <div class="flex space-x-1">
                                        <button class="text-green-600 dark:text-green-400 hover:text-green-700 dark:hover:text-green-300 text-xs transition-colors duration-300">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 text-xs transition-colors duration-300">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <p class="text-themed-tertiary text-sm transition-colors duration-300">No pending comments</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Scheduled Posts -->
                    <div>
                        <h3 class="text-sm font-semibold text-themed-primary mb-3 flex items-center transition-colors duration-300">
                            <i class="fas fa-calendar text-purple-600 dark:text-purple-400 mr-2"></i>
                            Scheduled Posts ({{ count($this->pendingContent['scheduled_posts']) }})
                        </h3>
                        <div class="space-y-2">
                            @forelse($this->pendingContent['scheduled_posts'] as $scheduled)
                            <div class="p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg transition-colors duration-300">
                                <h4 class="text-sm font-medium text-themed-primary truncate transition-colors duration-300">{{ $scheduled->title }}</h4>
                                <div class="flex items-center justify-between text-xs text-themed-secondary mt-1 transition-colors duration-300">
                                    <span>{{ $scheduled->category->name ?? 'Uncategorized' }}</span>
                                    <span>{{ $scheduled->published_at->format('M j, Y g:i A') }}</span>
                                </div>
                            </div>
                            @empty
                            <p class="text-themed-tertiary text-sm transition-colors duration-300">No scheduled posts</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- SEO Performance -->
            @if($showWidgets['seo_performance'])
            <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                <h2 class="text-xl font-bold text-themed-primary mb-6 transition-colors duration-300">SEO Performance</h2>
                
                <div class="space-y-4">
                    <!-- SEO Score -->
                    <div class="text-center">
                        <div class="relative inline-flex items-center justify-center w-20 h-20">
                            <svg class="w-20 h-20 transform -rotate-90" viewBox="0 0 36 36">
                                <path class="text-themed-tertiary" stroke="currentColor" stroke-width="2" fill="none" d="M18 2.0845a 15.9155 15.9155 0 0 1 0 31.831a 15.9155 15.9155 0 0 1 0 -31.831"></path>
                                <path class="text-green-500 dark:text-green-400" stroke="currentColor" stroke-width="2" stroke-dasharray="{{ $this->seoPerformance['avg_seo_score'] }}, 100" stroke-linecap="round" fill="none" d="M18 2.0845a 15.9155 15.9155 0 0 1 0 31.831a 15.9155 15.9155 0 0 1 0 -31.831"></path>
                            </svg>
                            <div class="absolute text-sm font-bold text-themed-primary transition-colors duration-300">{{ $this->seoPerformance['avg_seo_score'] }}%</div>
                        </div>
                        <p class="text-sm text-themed-secondary mt-2 transition-colors duration-300">Average SEO Score</p>
                    </div>

                    <!-- SEO Metrics -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg transition-colors duration-300">
                            <div class="text-lg font-bold text-green-600 dark:text-green-400 transition-colors duration-300">{{ $this->seoPerformance['seo_optimized_posts'] }}</div>
                            <div class="text-xs text-green-600 dark:text-green-400 transition-colors duration-300">Optimized Posts</div>
                        </div>
                        <div class="text-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg transition-colors duration-300">
                            <div class="text-lg font-bold text-blue-600 dark:text-blue-400 transition-colors duration-300">{{ $this->seoPerformance['meta_completion_rate'] }}%</div>
                            <div class="text-xs text-blue-600 dark:text-blue-400 transition-colors duration-300">Meta Complete</div>
                        </div>
                    </div>

                    <!-- Content Insights -->
                    <div class="pt-4 border-t border-themed-primary transition-colors duration-300">
                        <h4 class="text-sm font-semibold text-themed-primary mb-3 transition-colors duration-300">Trending Topics</h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach($this->contentInsights['trending_topics'] as $topic)
                            <span class="px-2 py-1 bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300 text-xs rounded-full transition-colors duration-300">{{ $topic }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Content Calendar -->
            @if($showWidgets['content_calendar'])
            <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                <h2 class="text-xl font-bold text-themed-primary mb-6 transition-colors duration-300">Content Calendar</h2>
                
                <div class="space-y-4">
                    <div>
                        <h4 class="text-sm font-semibold text-themed-primary mb-3 transition-colors duration-300">Publishing Insights</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-themed-secondary transition-colors duration-300">Publishing Frequency</span>
                                <span class="font-medium text-themed-primary transition-colors duration-300">{{ $this->contentCalendar['publishing_frequency'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-themed-secondary transition-colors duration-300">Posts This Month</span>
                                <span class="font-medium text-themed-primary transition-colors duration-300">{{ $this->overviewStats['posts_this_month'] }}</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-semibold text-themed-primary mb-3 transition-colors duration-300">Optimal Posting Times</h4>
                        <div class="space-y-1">
                            @foreach($this->contentCalendar['optimal_posting_times'] as $time)
                            <div class="flex items-center text-sm">
                                <i class="fas fa-clock text-themed-tertiary mr-2"></i>
                                <span class="text-themed-secondary transition-colors duration-300">{{ $time }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="pt-4 border-t border-themed-primary transition-colors duration-300">
                        <div class="grid grid-cols-2 gap-2">
                            <a href="{{ route('admin.blog.posts.create') }}" class="block text-center bg-purple-600 dark:bg-purple-500 hover:bg-purple-700 dark:hover:bg-purple-600 text-white py-2 rounded-lg text-sm font-medium transition-colors">
                                New Post
                            </a>
                            <a href="{{ route('admin.blog.settings') }}" class="block text-center bg-themed-tertiary hover:bg-themed-secondary text-themed-primary py-2 rounded-lg text-sm font-medium transition-colors">
                                Settings
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Content Insights -->
            @if($showWidgets['content_insights'])
            <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary p-6 transition-colors duration-300">
                <h2 class="text-xl font-bold text-themed-primary mb-6 transition-colors duration-300">Content Insights</h2>
                
                <div class="space-y-6">
                    <!-- Reading Patterns -->
                    <div>
                        <h4 class="text-sm font-semibold text-themed-primary mb-3 transition-colors duration-300">Reading Patterns</h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div class="p-3 bg-themed-tertiary rounded-lg transition-colors duration-300">
                                <div class="font-bold text-purple-600 dark:text-purple-400 text-lg transition-colors duration-300">65%</div>
                                <div class="text-themed-secondary transition-colors duration-300">Mobile Readers</div>
                            </div>
                            <div class="p-3 bg-themed-tertiary rounded-lg transition-colors duration-300">
                                <div class="font-bold text-blue-600 dark:text-blue-400 text-lg transition-colors duration-300">4.2m</div>
                                <div class="text-themed-secondary transition-colors duration-300">Avg Session</div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Referral Sources -->
                    <div>
                        <h4 class="text-sm font-semibold text-themed-primary mb-3 transition-colors duration-300">Top Traffic Sources</h4>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center space-x-2">
                                    <i class="fab fa-google text-blue-600 dark:text-blue-400"></i>
                                    <span class="text-themed-primary transition-colors duration-300">Google Search</span>
                                </div>
                                <span class="font-medium text-themed-secondary transition-colors duration-300">45.2%</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-share-alt text-green-600 dark:text-green-400"></i>
                                    <span class="text-themed-primary transition-colors duration-300">Social Media</span>
                                </div>
                                <span class="font-medium text-themed-secondary transition-colors duration-300">28.7%</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-link text-purple-600 dark:text-purple-400"></i>
                                    <span class="text-themed-primary transition-colors duration-300">Direct Traffic</span>
                                </div>
                                <span class="font-medium text-themed-secondary transition-colors duration-300">26.1%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Content Suggestions -->
                    <div>
                        <h4 class="text-sm font-semibold text-themed-primary mb-3 transition-colors duration-300">Content Suggestions</h4>
                        <div class="space-y-2">
                            <div class="p-2 bg-yellow-50 dark:bg-yellow-900/20 rounded text-xs text-yellow-800 dark:text-yellow-300 transition-colors duration-300">
                                💡 Consider writing about "Web Development Trends" - high search volume
                            </div>
                            <div class="p-2 bg-blue-50 dark:bg-blue-900/20 rounded text-xs text-blue-800 dark:text-blue-300 transition-colors duration-300">
                                📈 Your tutorials get 3x more engagement than news posts
                            </div>
                            <div class="p-2 bg-green-50 dark:bg-green-900/20 rounded text-xs text-green-800 dark:text-green-300 transition-colors duration-300">
                                🕐 Best posting time: Tuesday 10 AM (based on your audience)
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Widget Toggle Panel -->
    <div class="fixed bottom-6 right-6 z-40">
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" 
                    class="bg-themed-secondary shadow-lg border border-themed-primary rounded-full p-3 hover:shadow-xl transition-all duration-200">
                <i class="fas fa-sliders-h text-themed-secondary"></i>
            </button>
            
            <div x-show="open" 
                 x-transition
                 @click.outside="open = false"
                 class="absolute bottom-full right-0 mb-2 bg-themed-secondary rounded-xl shadow-xl border border-themed-primary p-4 w-64 transition-colors duration-300">
                <h3 class="font-semibold text-themed-primary mb-3 transition-colors duration-300">Dashboard Widgets</h3>
                <div class="space-y-2">
                    @foreach($showWidgets as $widget => $isVisible)
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" 
                               wire:model="showWidgets.{{ $widget }}" 
                               class="rounded border-themed-secondary text-purple-600 dark:text-purple-400 focus:ring-purple-500 dark:focus:ring-purple-400 dark:bg-themed-tertiary transition-colors duration-300">
                        <span class="ml-2 text-sm text-themed-secondary transition-colors duration-300">{{ ucwords(str_replace('_', ' ', $widget)) }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get current theme for chart colors
    const isDark = document.documentElement.classList.contains('dark');
    
    // Chart.js default configuration for dark mode
    Chart.defaults.color = isDark ? '#D1D5DB' : '#374151';
    Chart.defaults.borderColor = isDark ? '#374151' : '#E5E7EB';
    Chart.defaults.backgroundColor = isDark ? '#1F2937' : '#FFFFFF';

    // Content Trends Chart
    const contentData = @json($this->contentAnalytics['content_trends']);
    if (contentData.length > 0) {
        const ctx = document.getElementById('contentTrendsChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: contentData.map(item => item.date),
                datasets: [{
                    label: 'Views',
                    data: contentData.map(item => item.total_views),
                    borderColor: isDark ? 'rgb(168, 85, 247)' : 'rgb(147, 51, 234)',
                    backgroundColor: isDark ? 'rgba(168, 85, 247, 0.1)' : 'rgba(147, 51, 234, 0.1)',
                    fill: true,
                    tension: 0.4,
                }, {
                    label: 'Posts Created',
                    data: contentData.map(item => item.posts_created),
                    borderColor: isDark ? 'rgb(244, 114, 182)' : 'rgb(236, 72, 153)',
                    backgroundColor: isDark ? 'rgba(244, 114, 182, 0.1)' : 'rgba(236, 72, 153, 0.1)',
                    fill: false,
                    tension: 0.4,
                    yAxisID: 'y1',
                }, {
                    label: 'Engagement',
                    data: contentData.map(item => item.engagement),
                    borderColor: isDark ? 'rgb(96, 165, 250)' : 'rgb(59, 130, 246)',
                    backgroundColor: isDark ? 'rgba(96, 165, 250, 0.1)' : 'rgba(59, 130, 246, 0.1)',
                    fill: false,
                    tension: 0.4,
                    yAxisID: 'y1',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: isDark ? '#D1D5DB' : '#374151'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: isDark ? '#9CA3AF' : '#6B7280'
                        },
                        grid: {
                            color: isDark ? 'rgba(55, 65, 81, 0.3)' : 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: true,
                        ticks: {
                            color: isDark ? '#9CA3AF' : '#6B7280'
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                    },
                    x: {
                        ticks: {
                            color: isDark ? '#9CA3AF' : '#6B7280'
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    // Listen for dark mode changes and update charts
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                // Reload charts when dark mode changes
                setTimeout(() => {
                    location.reload();
                }, 100);
            }
        });
    });
    
    observer.observe(document.documentElement, {
        attributes: true,
        attributeFilter: ['class']
    });
});

// Auto-refresh dashboard data
setInterval(() => {
    @this.call('loadAllData');
}, 300000); // 5 minutes
</script>
@endpush