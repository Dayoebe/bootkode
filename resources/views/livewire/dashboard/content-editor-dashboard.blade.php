<div class="min-h-screen bg-gradient-to-br from-purple-50 to-pink-50 p-4 md:p-6">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 relative overflow-hidden">
            <!-- Background decoration -->
            <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-br from-purple-100 to-pink-100 rounded-full -translate-y-12 translate-x-12 opacity-60"></div>
            
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center space-y-4 lg:space-y-0 relative">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-pen-fancy text-purple-600 mr-3"></i>
                        Content Editor Dashboard
                    </h1>
                    <p class="text-gray-600 mt-1">Welcome back, {{ auth()->user()->name }}! Create and manage compelling content.</p>
                    
                    <div class="flex items-center space-x-4 mt-3">
                        <div class="flex items-center bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm">
                            <i class="fas fa-eye mr-2"></i>
                            {{ number_format($this->overviewStats['total_views']) }} total views
                        </div>
                        <div class="flex items-center bg-pink-100 text-pink-800 px-3 py-1 rounded-full text-sm">
                            <i class="fas fa-heart mr-2"></i>
                            {{ number_format($this->overviewStats['total_likes']) }} likes
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-wrap items-center space-x-3">
                    <!-- Content Type Filter -->
                    <select wire:model.live="selectedContentType" class="bg-white border border-gray-300 rounded-lg px-3 py-2 text-sm">
                        <option value="all">All Content</option>
                        <option value="blog_posts">Blog Posts</option>
                        <option value="pages">Pages</option>
                        <option value="faqs">FAQs</option>
                        <option value="announcements">Announcements</option>
                    </select>
                    
                    <!-- Timeframe Selector -->
                    <div class="flex bg-gray-100 rounded-lg p-1">
                        @foreach(['7days' => '7d', '30days' => '30d', '90days' => '90d', '12months' => '12m'] as $value => $label)
                            <button 
                                wire:click="updateTimeframe('{{ $value }}')"
                                class="px-3 py-1 rounded-md text-sm font-medium transition-all duration-200 {{ $selectedTimeframe === $value ? 'bg-white text-purple-600 shadow-sm' : 'text-gray-600 hover:text-gray-900' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                    
                    <!-- Quick Actions -->
                    <a href="{{ route('admin.blog.posts.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
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
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Posts</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ $this->overviewStats['total_posts'] }}</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="text-green-600 font-medium">{{ $this->overviewStats['published_posts'] }}</span>
                        <span class="text-gray-500 ml-1">published</span>
                    </div>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <i class="fas fa-file-alt text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Total Views</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($this->overviewStats['total_views']) }}</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="text-blue-600 font-medium">{{ number_format($this->overviewStats['total_views'] / max($this->overviewStats['published_posts'], 1), 0) }}</span>
                        <span class="text-gray-500 ml-1">avg per post</span>
                    </div>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-eye text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Engagement</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($this->overviewStats['total_likes'] + $this->overviewStats['total_comments']) }}</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="text-pink-600 font-medium">{{ $this->overviewStats['total_comments'] }}</span>
                        <span class="text-gray-500 ml-1">comments</span>
                    </div>
                </div>
                <div class="bg-pink-100 p-3 rounded-full">
                    <i class="fas fa-heart text-pink-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 font-medium">Avg Read Time</p>
                    <h3 class="text-3xl font-bold text-gray-900 mt-1">{{ number_format($this->overviewStats['avg_read_time'], 1) }}m</h3>
                    <div class="flex items-center mt-2 text-sm">
                        <span class="text-green-600 font-medium">{{ $this->overviewStats['featured_posts'] }}</span>
                        <span class="text-gray-500 ml-1">featured</span>
                    </div>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-clock text-green-600 text-xl"></i>
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
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Content Analytics</h2>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Content Trends Chart -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Content Performance ({{ $selectedTimeframe === '7days' ? 'Last 7 Days' : ($selectedTimeframe === '30days' ? 'Last 30 Days' : ($selectedTimeframe === '90days' ? 'Last 90 Days' : 'Last 12 Months')) }})</h3>
                        <div class="h-48">
                            <canvas id="contentTrendsChart"></canvas>
                        </div>
                    </div>
                    
                    <!-- Category Performance -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Category Performance</h3>
                        <div class="space-y-3">
                            @forelse($this->contentAnalytics['category_performance']->take(5) as $category)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ $category['category'] }}</h4>
                                    <p class="text-sm text-gray-600">{{ $category['post_count'] }} posts</p>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-bold text-purple-600">{{ number_format($category['total_views']) }}</div>
                                    <div class="text-xs text-gray-500">views</div>
                                </div>
                            </div>
                            @empty
                            <p class="text-gray-500 text-sm">No category data available</p>
                            @endforelse
                        </div>
                        
                        <!-- Top Performing Posts -->
                        <div class="mt-6 pt-4 border-t border-gray-200">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Top Performing Posts</h4>
                            <div class="space-y-2">
                                @foreach($this->contentAnalytics['top_performing_posts'] as $post)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-600 truncate">{{ \Str::limit($post['title'], 25) }}</span>
                                    <span class="font-medium text-purple-600">{{ number_format($post['views']) }}</span>
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
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Recent Posts</h2>
                    <a href="{{ route('admin.blog.posts.index') }}" class="text-purple-600 hover:text-purple-700 text-sm font-medium">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                <div class="space-y-4">
                    @forelse($this->recentPosts as $post)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3">
                                <div>
                                    <h4 class="font-semibold text-gray-900">{{ \Str::limit($post['title'], 50) }}</h4>
                                    <div class="flex items-center space-x-4 text-sm text-gray-600 mt-1">
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
                                <div class="text-lg font-bold text-purple-600">{{ number_format($post['performance_score'], 0) }}</div>
                                <div class="text-xs text-gray-500">score</div>
                            </div>
                            
                            <!-- Status Badge -->
                            <span class="px-3 py-1 text-xs rounded-full {{ $post['status'] === 'published' ? 'bg-green-100 text-green-800' : ($post['status'] === 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                {{ ucfirst($post['status']) }}
                            </span>
                            
                            @if($post['is_featured'])
                            <span class="px-2 py-1 text-xs bg-purple-100 text-purple-800 rounded-full">
                                Featured
                            </span>
                            @endif
                            
                            <!-- Quick Publish Button -->
                            @if($post['status'] === 'draft')
                            <button wire:click="quickPublish({{ $post['id'] }})" 
                                    class="text-green-600 hover:text-green-700 text-sm">
                                <i class="fas fa-rocket"></i>
                            </button>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <i class="fas fa-pen-alt text-gray-400 text-4xl mb-4"></i>
                        <p class="text-gray-600 mb-4">No posts created yet</p>
                        <a href="{{ route('admin.blog.posts.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors">
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
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Pending Content</h2>
                
                <div class="space-y-6">
                    <!-- Draft Posts -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <i class="fas fa-edit text-yellow-600 mr-2"></i>
                            Draft Posts ({{ count($this->pendingContent['draft_posts']) }})
                        </h3>
                        <div class="space-y-2">
                            @forelse($this->pendingContent['draft_posts'] as $draft)
                            <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                                <div class="flex-1">
                                    <h4 class="text-sm font-medium text-gray-900 truncate">{{ $draft['title'] }}</h4>
                                    <div class="flex items-center space-x-2 text-xs text-gray-500 mt-1">
                                        <span>{{ $draft['word_count'] }} words</span>
                                        <span>{{ $draft['completion_percentage'] }}% complete</span>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500">{{ $draft['created_at']->format('M j') }}</div>
                            </div>
                            @empty
                            <p class="text-gray-500 text-sm">No draft posts</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Pending Comments -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <i class="fas fa-comments text-blue-600 mr-2"></i>
                            Pending Comments ({{ count($this->pendingContent['pending_comments']) }})
                        </h3>
                        <div class="space-y-2">
                            @forelse($this->pendingContent['pending_comments'] as $comment)
                            <div class="p-3 bg-blue-50 rounded-lg">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h4 class="text-sm font-medium text-gray-900">{{ $comment->user->name }}</h4>
                                        <p class="text-xs text-gray-600 mt-1">{{ \Str::limit($comment->content, 60) }}</p>
                                        <p class="text-xs text-gray-500 mt-1">On: {{ \Str::limit($comment->post->title, 30) }}</p>
                                    </div>
                                    <div class="flex space-x-1">
                                        <button class="text-green-600 hover:text-green-700 text-xs">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="text-red-600 hover:text-red-700 text-xs">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <p class="text-gray-500 text-sm">No pending comments</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Scheduled Posts -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-3 flex items-center">
                            <i class="fas fa-calendar text-purple-600 mr-2"></i>
                            Scheduled Posts ({{ count($this->pendingContent['scheduled_posts']) }})
                        </h3>
                        <div class="space-y-2">
                            @forelse($this->pendingContent['scheduled_posts'] as $scheduled)
                            <div class="p-3 bg-purple-50 rounded-lg">
                                <h4 class="text-sm font-medium text-gray-900 truncate">{{ $scheduled->title }}</h4>
                                <div class="flex items-center justify-between text-xs text-gray-500 mt-1">
                                    <span>{{ $scheduled->category->name ?? 'Uncategorized' }}</span>
                                    <span>{{ $scheduled->published_at->format('M j, Y g:i A') }}</span>
                                </div>
                            </div>
                            @empty
                            <p class="text-gray-500 text-sm">No scheduled posts</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- SEO Performance -->
            @if($showWidgets['seo_performance'])
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">SEO Performance</h2>
                
                <div class="space-y-4">
                    <!-- SEO Score -->
                    <div class="text-center">
                        <div class="relative inline-flex items-center justify-center w-20 h-20">
                            <svg class="w-20 h-20 transform -rotate-90" viewBox="0 0 36 36">
                                <path class="text-gray-300" stroke="currentColor" stroke-width="2" fill="none" d="M18 2.0845a 15.9155 15.9155 0 0 1 0 31.831a 15.9155 15.9155 0 0 1 0 -31.831"></path>
                                <path class="text-green-500" stroke="currentColor" stroke-width="2" stroke-dasharray="{{ $this->seoPerformance['avg_seo_score'] }}, 100" stroke-linecap="round" fill="none" d="M18 2.0845a 15.9155 15.9155 0 0 1 0 31.831a 15.9155 15.9155 0 0 1 0 -31.831"></path>
                            </svg>
                            <div class="absolute text-sm font-bold text-gray-900">{{ $this->seoPerformance['avg_seo_score'] }}%</div>
                        </div>
                        <p class="text-sm text-gray-600 mt-2">Average SEO Score</p>
                    </div>

                    <!-- SEO Metrics -->
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center p-3 bg-green-50 rounded-lg">
                            <div class="text-lg font-bold text-green-600">{{ $this->seoPerformance['seo_optimized_posts'] }}</div>
                            <div class="text-xs text-green-600">Optimized Posts</div>
                        </div>
                        <div class="text-center p-3 bg-blue-50 rounded-lg">
                            <div class="text-lg font-bold text-blue-600">{{ $this->seoPerformance['meta_completion_rate'] }}%</div>
                            <div class="text-xs text-blue-600">Meta Complete</div>
                        </div>
                    </div>

                    <!-- Content Insights -->
                    <div class="pt-4 border-t border-gray-200">
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Trending Topics</h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach($this->contentInsights['trending_topics'] as $topic)
                            <span class="px-2 py-1 bg-purple-100 text-purple-800 text-xs rounded-full">{{ $topic }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Content Calendar -->
            @if($showWidgets['content_calendar'])
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Content Calendar</h2>
                
                <div class="space-y-4">
                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Publishing Insights</h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Publishing Frequency</span>
                                <span class="font-medium">{{ $this->contentCalendar['publishing_frequency'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Posts This Month</span>
                                <span class="font-medium">{{ $this->overviewStats['posts_this_month'] }}</span>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-semibold text-gray-700 mb-3">Optimal Posting Times</h4>
                        <div class="space-y-1">
                            @foreach($this->contentCalendar['optimal_posting_times'] as $time)
                            <div class="flex items-center text-sm">
                                <i class="fas fa-clock text-gray-400 mr-2"></i>
                                <span class="text-gray-600">{{ $time }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="pt-4 border-t border-gray-200">
                        <div class="grid grid-cols-2 gap-2">
                            <a href="{{ route('admin.blog.posts.create') }}" class="block text-center bg-purple-600 hover:bg-purple-700 text-white py-2 rounded-lg text-sm font-medium transition-colors">
                                New Post
                            </a>
                            <a href="{{ route('admin.blog.settings') }}" class="block text-center bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 rounded-lg text-sm font-medium transition-colors">
                                Settings
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
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
                    borderColor: 'rgb(147, 51, 234)',
                    backgroundColor: 'rgba(147, 51, 234, 0.1)',
                    fill: true,
                    tension: 0.4,
                }, {
                    label: 'Posts Created',
                    data: contentData.map(item => item.posts_created),
                    borderColor: 'rgb(236, 72, 153)',
                    backgroundColor: 'rgba(236, 72, 153, 0.1)',
                    fill: false,
                    tension: 0.4,
                    yAxisID: 'y1',
                }, {
                    label: 'Engagement',
                    data: contentData.map(item => item.engagement),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
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
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: true,
                        grid: {
                            drawOnChartArea: false,
                        },
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
});

// Auto-refresh dashboard data
setInterval(() => {
    @this.call('loadAllData');
}, 300000); // 5 minutes
</script>
@endpush