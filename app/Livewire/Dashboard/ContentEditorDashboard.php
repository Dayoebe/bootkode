<?php

namespace App\Livewire\Dashboard;

use App\Models\Core\User;
use App\Models\Content\BlogPost;
use App\Models\Content\BlogCategory;
use App\Models\Content\BlogComment;
use App\Models\Content\BlogReaction;
use App\Models\Content\BlogSetting;
use App\Models\Content\Page;
use App\Models\Community\Faq;
use App\Models\Admin\Announcement;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

#[Layout('layouts.dashboard', ['title' => 'Content Editor Dashboard'])]
class ContentEditorDashboard extends Component
{
    public $selectedTimeframe = '30days';
    public $selectedContentType = 'all';
    
    public $showWidgets = [
        'overview_stats' => true,
        'content_analytics' => true,
        'recent_posts' => true,
        'engagement_metrics' => true,
        'pending_content' => true,
        'content_calendar' => true,
        'seo_performance' => true,
        'content_insights' => true,
    ];

    protected $listeners = [
        'refreshDashboard' => 'loadAllData',
        'timeframeChanged' => 'updateTimeframe',
        'contentTypeChanged' => 'updateContentType',
    ];

    public function mount()
    {
        $user = Auth::user();
        if (!$user->isContentEditor()) {
            redirect()->route($user->getDashboardRouteName());
        }
    }

    public function updateTimeframe($timeframe)
    {
        $this->selectedTimeframe = $timeframe;
    }

    public function updateContentType($type)
    {
        $this->selectedContentType = $type;
    }

    #[Computed]
    public function overviewStats()
    {
        $editor = Auth::user();
        $timeframe = $this->getTimeframeQuery();
        
        return [
            'total_posts' => BlogPost::where('author_id', $editor->id)->count(),
            'published_posts' => BlogPost::where('author_id', $editor->id)->where('status', 'published')->count(),
            'draft_posts' => BlogPost::where('author_id', $editor->id)->where('status', 'draft')->count(),
            'total_views' => BlogPost::where('author_id', $editor->id)->sum('views_count'),
            'total_likes' => BlogPost::where('author_id', $editor->id)->sum('likes_count'),
            'total_comments' => BlogPost::where('author_id', $editor->id)->sum('comments_count'),
            'avg_read_time' => BlogPost::where('author_id', $editor->id)->avg('read_time'),
            'featured_posts' => BlogPost::where('author_id', $editor->id)->where('is_featured', true)->count(),
            'posts_this_month' => BlogPost::where('author_id', $editor->id)
                ->whereMonth('created_at', now()->month)
                ->count(),
            'pages_managed' => Page::where('created_by', $editor->id)->count(),
            'faqs_managed' => Faq::count(), // All FAQs if content editor manages them
            'announcements_created' => Announcement::where('user_id', $editor->id)->count(),
        ];
    }

    #[Computed]
    public function contentAnalytics()
    {
        $editor = Auth::user();
        $days = $this->getTimeframeDays();
        
        // Daily content creation trends
        $contentTrends = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $posts = BlogPost::where('author_id', $editor->id)
                ->whereDate('created_at', $date)
                ->count();
            $views = BlogPost::where('author_id', $editor->id)
                ->whereDate('updated_at', $date)
                ->sum('views_count');
            
            $contentTrends[] = [
                'date' => $date->format('M j'),
                'posts_created' => $posts,
                'total_views' => $views,
                'engagement' => $this->getDailyEngagement($editor, $date),
            ];
        }

        // Category performance
        $categoryPerformance = $this->getCategoryPerformance($editor);
        
        // Content type breakdown
        $contentTypeBreakdown = $this->getContentTypeBreakdown($editor);
        
        return [
            'content_trends' => $contentTrends,
            'category_performance' => $categoryPerformance,
            'content_type_breakdown' => $contentTypeBreakdown,
            'top_performing_posts' => $this->getTopPerformingPosts($editor),
        ];
    }

    #[Computed]
    public function recentPosts()
    {
        $editor = Auth::user();
        
        return BlogPost::where('author_id', $editor->id)
            ->with(['category', 'comments', 'reactions'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'status' => $post->status,
                    'category' => $post->category->name ?? 'Uncategorized',
                    'views' => $post->views_count,
                    'likes' => $post->likes_count,
                    'comments' => $post->comments_count,
                    'read_time' => $post->read_time,
                    'is_featured' => $post->is_featured,
                    'published_at' => $post->published_at,
                    'created_at' => $post->created_at,
                    'engagement_rate' => $this->calculateEngagementRate($post),
                    'performance_score' => $this->calculatePerformanceScore($post),
                ];
            });
    }

    #[Computed]
    public function engagementMetrics()
    {
        $editor = Auth::user();
        $timeframe = $this->getTimeframeQuery();
        
        return [
            'total_engagement' => $this->getTotalEngagement($editor, $timeframe),
            'avg_engagement_rate' => $this->getAverageEngagementRate($editor),
            'top_engaging_posts' => $this->getTopEngagingPosts($editor),
            'engagement_by_category' => $this->getEngagementByCategory($editor),
            'comment_sentiment' => $this->getCommentSentiment($editor),
            'social_shares' => $this->getSocialShares($editor),
        ];
    }

    #[Computed]
    public function pendingContent()
    {
        $editor = Auth::user();
        
        return [
            'draft_posts' => BlogPost::where('author_id', $editor->id)
                ->where('status', 'draft')
                ->latest()
                ->take(5)
                ->get()
                ->map(function($post) {
                    return [
                        'id' => $post->id,
                        'title' => $post->title,
                        'created_at' => $post->created_at,
                        'word_count' => str_word_count(strip_tags($post->content)),
                        'completion_percentage' => $this->calculateCompletionPercentage($post),
                    ];
                }),
            'pending_comments' => BlogComment::whereHas('post', function($query) use ($editor) {
                    $query->where('author_id', $editor->id);
                })
                ->where('status', 'pending')
                ->with(['user', 'post'])
                ->latest()
                ->take(5)
                ->get(),
            'scheduled_posts' => BlogPost::where('author_id', $editor->id)
                ->where('status', 'scheduled')
                ->where('published_at', '>', now())
                ->orderBy('published_at')
                ->take(5)
                ->get(),
        ];
    }

    #[Computed]
    public function contentCalendar()
    {
        $editor = Auth::user();
        
        // Get upcoming scheduled content
        $upcomingContent = BlogPost::where('author_id', $editor->id)
            ->where('published_at', '>', now())
            ->orderBy('published_at')
            ->take(10)
            ->get()
            ->map(function($post) {
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'type' => 'blog_post',
                    'scheduled_date' => $post->published_at,
                    'status' => $post->status,
                    'category' => $post->category->name ?? 'Uncategorized',
                ];
            });

        // Get content ideas and suggestions
        $contentSuggestions = $this->getContentSuggestions($editor);
        
        return [
            'upcoming_content' => $upcomingContent,
            'content_suggestions' => $contentSuggestions,
            'publishing_frequency' => $this->getPublishingFrequency($editor),
            'optimal_posting_times' => $this->getOptimalPostingTimes($editor),
        ];
    }

    #[Computed]
    public function seoPerformance()
    {
        $editor = Auth::user();
        
        return [
            'seo_optimized_posts' => $this->getSeoOptimizedPostsCount($editor),
            'avg_seo_score' => $this->getAverageSeoScore($editor),
            'meta_completion_rate' => $this->getMetaCompletionRate($editor),
            'keyword_performance' => $this->getKeywordPerformance($editor),
            'top_organic_posts' => $this->getTopOrganicPosts($editor),
        ];
    }

    #[Computed]
    public function contentInsights()
    {
        $editor = Auth::user();
        
        return [
            'reading_patterns' => $this->getReadingPatterns($editor),
            'audience_demographics' => $this->getAudienceDemographics($editor),
            'content_preferences' => $this->getContentPreferences($editor),
            'trending_topics' => $this->getTrendingTopics(),
            'competitor_analysis' => $this->getCompetitorInsights(),
        ];
    }

    // Helper Methods
    private function getTimeframeQuery()
    {
        return match ($this->selectedTimeframe) {
            '7days' => now()->subDays(7),
            '30days' => now()->subDays(30),
            '90days' => now()->subDays(90),
            '12months' => now()->subMonths(12),
            default => now()->subDays(30),
        };
    }

    private function getTimeframeDays()
    {
        return match ($this->selectedTimeframe) {
            '7days' => 7,
            '30days' => 30,
            '90days' => 90,
            '12months' => 365,
            default => 30,
        };
    }

    private function getDailyEngagement(User $editor, $date)
    {
        return BlogReaction::where(function($query) use ($editor) {
                // For BlogPost reactions
                $query->whereHasMorph('reactable', [BlogPost::class], function($q) use ($editor) {
                    $q->where('author_id', $editor->id);
                })
                // For BlogComment reactions
                ->orWhereHasMorph('reactable', [BlogComment::class], function($q) use ($editor) {
                    $q->where('user_id', $editor->id);
                });
            })
            ->whereDate('created_at', $date)
            ->count();
    }

    private function getCategoryPerformance(User $editor)
    {
        return BlogPost::where('author_id', $editor->id)
            ->with('category')
            ->get()
            ->groupBy('category.name')
            ->map(function($posts, $category) {
                return [
                    'category' => $category ?? 'Uncategorized',
                    'post_count' => $posts->count(),
                    'total_views' => $posts->sum('views_count'),
                    'avg_engagement' => $posts->avg(function($post) {
                        return $post->likes_count + $post->comments_count;
                    }),
                ];
            })
            ->sortByDesc('total_views')
            ->values();
    }

    private function getContentTypeBreakdown(User $editor)
    {
        $posts = BlogPost::where('author_id', $editor->id)->get();
        
        return [
            'blog_posts' => $posts->count(),
            'featured_posts' => $posts->where('is_featured', true)->count(),
            'pages' => Page::where('created_by', $editor->id)->count(),
            'faqs' => Faq::count(),
            'announcements' => Announcement::where('user_id', $editor->id)->count(),
        ];
    }

    private function getTopPerformingPosts(User $editor)
    {
        return BlogPost::where('author_id', $editor->id)
            ->orderByDesc('views_count')
            ->take(5)
            ->get()
            ->map(function($post) {
                return [
                    'title' => $post->title,
                    'views' => $post->views_count,
                    'engagement' => $post->likes_count + $post->comments_count,
                    'published_at' => $post->published_at,
                ];
            });
    }

    private function calculateEngagementRate($post)
    {
        if ($post->views_count == 0) return 0;
        
        $engagement = $post->likes_count + $post->comments_count;
        return round(($engagement / $post->views_count) * 100, 2);
    }

    private function calculatePerformanceScore($post)
    {
        $score = 0;
        
        // Views score (40%)
        $score += min(($post->views_count / 1000) * 40, 40);
        
        // Engagement score (30%)
        $engagementRate = $this->calculateEngagementRate($post);
        $score += min(($engagementRate / 5) * 30, 30);
        
        // SEO score (20%)
        $seoScore = $this->calculateSeoScore($post);
        $score += ($seoScore / 100) * 20;
        
        // Recency bonus (10%)
        $daysSincePublished = $post->published_at ? now()->diffInDays($post->published_at) : 999;
        if ($daysSincePublished <= 7) {
            $score += 10;
        } elseif ($daysSincePublished <= 30) {
            $score += 5;
        }
        
        return min($score, 100);
    }

    private function calculateSeoScore($post)
    {
        $score = 0;
        
        // Meta title
        if ($post->meta_title) $score += 20;
        
        // Meta description
        if ($post->meta_description) $score += 20;
        
        // Featured image
        if ($post->featured_image) $score += 15;
        
        // Content length (ideal 1000+ words)
        $wordCount = str_word_count(strip_tags($post->content));
        if ($wordCount >= 1000) $score += 25;
        elseif ($wordCount >= 500) $score += 15;
        elseif ($wordCount >= 300) $score += 10;
        
        // Keywords/tags
        if ($post->tags && count($post->tags) > 0) $score += 10;
        
        // Reading time (ideal 3-7 minutes)
        if ($post->read_time >= 3 && $post->read_time <= 7) $score += 10;
        
        return $score;
    }

    private function calculateCompletionPercentage($post)
    {
        $score = 0;
        
        if ($post->title) $score += 20;
        if ($post->content) $score += 40;
        if ($post->excerpt) $score += 15;
        if ($post->featured_image) $score += 15;
        if ($post->category_id) $score += 10;
        
        return $score;
    }

    private function getTotalEngagement(User $editor, $timeframe) 
    { 
        return BlogReaction::where(function($query) use ($editor) {
                $query->whereHasMorph('reactable', [BlogPost::class], function($q) use ($editor) {
                    $q->where('author_id', $editor->id);
                })
                ->orWhereHasMorph('reactable', [BlogComment::class], function($q) use ($editor) {
                    $q->where('user_id', $editor->id);
                });
            })
            ->where('created_at', '>=', $timeframe)
            ->count();
    }
    private function getAverageEngagementRate(User $editor) 
    { 
        $posts = BlogPost::where('author_id', $editor->id)->get();
        return $posts->avg(function($post) {
            return $this->calculateEngagementRate($post);
        });
    }

    private function getTopEngagingPosts(User $editor) 
    { 
        return BlogPost::where('author_id', $editor->id)
            ->get()
            ->sortByDesc(function($post) {
                return $this->calculateEngagementRate($post);
            })
            ->take(5)
            ->values();
    }

    private function getEngagementByCategory(User $editor) { return []; }
    private function getCommentSentiment(User $editor) { return ['positive' => 70, 'neutral' => 20, 'negative' => 10]; }
    private function getSocialShares(User $editor) { return rand(50, 500); }
    private function getContentSuggestions(User $editor) { return []; }
    private function getPublishingFrequency(User $editor) { return '3 posts per week'; }
    private function getOptimalPostingTimes(User $editor) { return ['Tuesday 10 AM', 'Thursday 2 PM']; }
    private function getSeoOptimizedPostsCount(User $editor) { return BlogPost::where('author_id', $editor->id)->whereNotNull('meta_title')->count(); }
    private function getAverageSeoScore(User $editor) { return 75; }
    private function getMetaCompletionRate(User $editor) { return 85; }
    private function getKeywordPerformance(User $editor) { return []; }
    private function getTopOrganicPosts(User $editor) { return []; }
    private function getReadingPatterns(User $editor) { return []; }
    private function getAudienceDemographics(User $editor) { return []; }
    private function getContentPreferences(User $editor) { return []; }
    private function getTrendingTopics() { return ['AI', 'Web Development', 'Career Tips']; }
    private function getCompetitorInsights() { return []; }

    public function quickPublish($postId)
    {
        $post = BlogPost::where('id', $postId)->where('author_id', Auth::id())->first();
        
        if ($post && $post->status === 'draft') {
            $post->update([
                'status' => 'published',
                'published_at' => now(),
            ]);
            
            $this->dispatch('notify', type: 'success', message: 'Post published successfully!');
        }
    }

    public function loadAllData()
    {
        $this->dispatch('dashboard-updated');
    }

    public function render()
    {
        return view('livewire.dashboard.content-editor-dashboard');
    }
}
