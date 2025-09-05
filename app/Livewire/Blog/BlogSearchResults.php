<?php

namespace App\Livewire\Blog;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use Livewire\Component;
use Livewire\WithPagination;

class BlogSearchResults extends Component
{
    use WithPagination;

    public $query = '';
    public $category = '';
    public $tag = '';
    public $sortBy = 'relevance';
    public $resultsFound = 0;

    protected $queryString = [
        'query' => ['except' => ''],
        'category' => ['except' => ''],
        'tag' => ['except' => ''],
        'sortBy' => ['except' => 'relevance'],
    ];

    public function mount()
    {
        $this->query = request('q', '');
        $this->category = request('category', '');
        $this->tag = request('tag', '');
    }

    public function updatingQuery()
    {
        $this->resetPage();
    }

    public function updatingCategory()
    {
        $this->resetPage();
    }

    public function updatingTag()
    {
        $this->resetPage();
    }

    public function clearSearch()
    {
        $this->reset(['query', 'category', 'tag', 'sortBy']);
        $this->resetPage();
    }

    public function render()
    {
        $posts = collect();
        
        if ($this->query || $this->category || $this->tag) {
            $queryBuilder = BlogPost::with(['author', 'category'])
                ->published();

            // Search by text
            if ($this->query) {
                $queryBuilder->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->query . '%')
                      ->orWhere('excerpt', 'like', '%' . $this->query . '%')
                      ->orWhere('content', 'like', '%' . $this->query . '%')
                      ->orWhereJsonContains('tags', $this->query);
                });
            }

            // Filter by category
            if ($this->category) {
                $queryBuilder->whereHas('category', function ($q) {
                    $q->where('slug', $this->category);
                });
            }

            // Filter by tag
            if ($this->tag) {
                $queryBuilder->whereJsonContains('tags', $this->tag);
            }

            // Apply sorting
            switch ($this->sortBy) {
                case 'latest':
                    $queryBuilder->orderByDesc('published_at');
                    break;
                case 'popular':
                    $queryBuilder->orderByDesc('views_count');
                    break;
                case 'trending':
                    $queryBuilder->orderByDesc('likes_count');
                    break;
                default: // relevance
                    $queryBuilder->orderByDesc('published_at');
            }

            $posts = $queryBuilder->paginate(12);
            $this->resultsFound = $posts->total();
        }

        // Get categories for filter
        $categories = BlogCategory::active()
            ->withCount('publishedPosts')
            ->having('published_posts_count', '>', 0)
            ->ordered()
            ->get();

        return view('livewire.blog.blog-search-results', [
            'posts' => $posts,
            'categories' => $categories,
        ]);
    }
}
