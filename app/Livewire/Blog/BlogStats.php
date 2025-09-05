<?php

namespace App\Livewire\Blog;

use App\Models\BlogPost;
use App\Models\BlogComment;
use App\Models\BlogReaction;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BlogStats extends Component
{
    public $dateRange = '30'; // days
    public $showChart = false;

    public function mount()
    {
        $this->showChart = auth()->user()->canManageCourses();
    }

    public function setDateRange($days)
    {
        $this->dateRange = $days;
    }

    public function render()
    {
        $startDate = Carbon::now()->subDays($this->dateRange);
        
        // Basic stats
        $stats = [
            'total_posts' => BlogPost::count(),
            'published_posts' => BlogPost::published()->count(),
            'draft_posts' => BlogPost::where('status', 'draft')->count(),
            'total_comments' => BlogComment::where('status', 'approved')->count(),
            'pending_comments' => BlogComment::where('status', 'pending')->count(),
            'total_likes' => BlogReaction::where('type', 'like')->count(),
            'total_views' => BlogPost::sum('views_count'),
        ];

        // Recent activity (last X days)
        $recentStats = [
            'new_posts' => BlogPost::where('created_at', '>=', $startDate)->count(),
            'new_comments' => BlogComment::where('created_at', '>=', $startDate)->count(),
            'new_likes' => BlogReaction::where('created_at', '>=', $startDate)
                                   ->where('type', 'like')->count(),
            'total_views_period' => BlogPost::where('updated_at', '>=', $startDate)
                                          ->sum('views_count'),
        ];

        // Top performing posts
        $topPosts = BlogPost::published()
            ->select('id', 'title', 'slug', 'views_count', 'likes_count', 'comments_count')
            ->orderByDesc('views_count')
            ->take(5)
            ->get();

        // Chart data (if enabled)
        $chartData = [];
        if ($this->showChart) {
            $chartData = $this->getChartData($startDate);
        }

        // Recent comments
        $recentComments = BlogComment::with(['post:id,title,slug', 'user:id,name'])
            ->approved()
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        return view('livewire.blog.blog-stats', [
            'stats' => $stats,
            'recentStats' => $recentStats,
            'topPosts' => $topPosts,
            'chartData' => $chartData,
            'recentComments' => $recentComments,
        ]);
    }

    private function getChartData($startDate)
    {
        // Posts published over time
        $postsData = BlogPost::where('published_at', '>=', $startDate)
            ->select(DB::raw('DATE(published_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Comments over time
        $commentsData = BlogComment::where('created_at', '>=', $startDate)
            ->where('status', 'approved')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'posts' => $postsData,
            'comments' => $commentsData,
        ];
    }
}
