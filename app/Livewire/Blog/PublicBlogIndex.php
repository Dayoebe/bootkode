<?php

namespace App\Livewire\Blog;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Cache;

class PublicBlogIndex extends Component
{
    use WithPagination;

    public $category;
    public $tag;
    public $search = '';
    public $sortBy = 'latest';
    public $showFeatured = true;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'latest'],
        'page' => ['except' => 1]
    ];

    public function mount($category = null, $tag = null)
    {
        // Handle category whether it's a string slug or BlogCategory object
        if ($category instanceof BlogCategory) {
            $this->category = $category;
        } elseif (is_string($category)) {
            // Try to find category by slug if string passed
            $this->category = BlogCategory::where('slug', $category)->first();
        } else {
            $this->category = null;
        }
        
        $this->tag = $tag;
        
        if (request('search')) {
            $this->search = request('search');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSortBy()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->category = null;
        $this->tag = null;
        $this->sortBy = 'latest';
        $this->resetPage();
    }

    public function render()
    {
        $query = BlogPost::with(['author', 'category'])
            ->published();

        // Category filter - fixed to handle both string and object
        if ($this->category) {
            // Use slug if it's an object, otherwise use the string directly
            $slug = is_object($this->category) ? $this->category->slug : $this->category;
            $query->byCategory($slug);
        }

        // Tag filter
        if ($this->tag) {
            $query->byTag($this->tag);
        }

        // Search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('excerpt', 'like', '%' . $this->search . '%')
                  ->orWhere('content', 'like', '%' . $this->search . '%');
            });
        }

        // Sorting
        switch ($this->sortBy) {
            case 'popular':
                $query->orderByDesc('views_count');
                break;
            case 'trending':
                $query->orderByDesc('likes_count');
                break;
            case 'oldest':
                $query->orderBy('published_at');
                break;
            default:
                $query->orderByDesc('published_at');
        }

        $posts = $query->paginate(12);

        // Get featured posts (only on first page and when no filters)
        $featuredPosts = collect();
        if ($this->showFeatured && !$this->search && !$this->category && !$this->tag && $posts->currentPage() == 1) {
            $featuredPosts = BlogPost::with(['author', 'category'])
                ->published()
                ->featured()
                ->orderByDesc('published_at')
                ->take(3)
                ->get();
        }

        // Get categories for sidebar
        $categories = Cache::remember('blog_categories_active', 3600, function () {
            return BlogCategory::active()
                ->withCount('publishedPosts')
                ->having('published_posts_count', '>', 0)
                ->ordered()
                ->get();
        });

        // Get popular tags
        $popularTags = Cache::remember('blog_popular_tags', 3600, function () {
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

        // Recent posts for sidebar
        $recentPosts = Cache::remember('blog_recent_posts', 1800, function () {
            return BlogPost::with('author')
                ->published()
                ->orderByDesc('published_at')
                ->take(5)
                ->get();
        });

        return view('livewire.blog.public-blog-index', [
            'posts' => $posts,
            'featuredPosts' => $featuredPosts,
            'categories' => $categories,
            'popularTags' => $popularTags,
            'recentPosts' => $recentPosts,
        ])->layout('layouts.blog', [
            'title' => $this->getPageTitle()
        ]);
    }

    private function getPageTitle()
    {
        if ($this->category) {
            $categoryName = is_object($this->category) ? $this->category->name : $this->category;
            return "Category: {$categoryName}";
        }
        
        if ($this->tag) {
            return "Tag: {$this->tag}";
        }
        
        if ($this->search) {
            return "Search: {$this->search}";
        }
        
        return 'Blog';
    }
}