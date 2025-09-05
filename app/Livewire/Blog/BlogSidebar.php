<?php

namespace App\Livewire\Blog;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;

class BlogSidebar extends Component
{
    public $currentPost = null;
    public $currentCategory = null;

    public function mount($currentPost = null, $currentCategory = null)
    {
        $this->currentPost = $currentPost;
        $this->currentCategory = $currentCategory;
    }

    public function render()
    {
        // Get categories with post counts
        $categories = Cache::remember('blog_sidebar_categories', 3600, function () {
            return BlogCategory::active()
                ->withCount(['publishedPosts'])
                ->having('published_posts_count', '>', 0)
                ->ordered()
                ->get();
        });

        // Get popular posts
        $popularPosts = Cache::remember('blog_sidebar_popular', 1800, function () {
            return BlogPost::published()
                ->orderByDesc('views_count')
                ->take(5)
                ->get(['id', 'title', 'slug', 'views_count', 'published_at']);
        });

        // Get recent posts
        $recentPosts = Cache::remember('blog_sidebar_recent', 1800, function () {
            return BlogPost::published()
                ->orderByDesc('published_at')
                ->take(5)
                ->get(['id', 'title', 'slug', 'published_at']);
        });

        // Get popular tags
        $popularTags = Cache::remember('blog_sidebar_tags', 3600, function () {
            return BlogPost::published()
                ->whereNotNull('tags')
                ->get()
                ->pluck('tags')
                ->flatten()
                ->countBy()
                ->sortDesc()
                ->take(20)
                ->keys()
                ->toArray();
        });

        return view('livewire.blog.blog-sidebar', [
            'categories' => $categories,
            'popularPosts' => $popularPosts,
            'recentPosts' => $recentPosts,
            'popularTags' => $popularTags,
        ]);
    }
}
