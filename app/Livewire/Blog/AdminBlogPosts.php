<?php

namespace App\Livewire\Blog;

use App\Models\Content\BlogPost;
use App\Models\Content\BlogCategory;
use App\Models\Core\User;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Storage;

class AdminBlogPosts extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    public $categoryFilter = 'all';
    public $authorFilter = 'all';
    public $sortBy = 'latest';
    public $showDeleteModal = false;
    public $postToDelete = null;
    public $bulkActions = [];
    public $bulkAction = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'categoryFilter' => ['except' => 'all'],
        'authorFilter' => ['except' => 'all'],
        'sortBy' => ['except' => 'latest'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatingAuthorFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'statusFilter', 'categoryFilter', 'authorFilter', 'sortBy']);
        $this->resetPage();
    }

    public function confirmDelete($postId)
    {
        $this->postToDelete = $postId;
        $this->showDeleteModal = true;
    }

    public function deletePost()
    {
        if ($this->postToDelete) {
            $post = BlogPost::find($this->postToDelete);
            
            if ($post) {
                // Delete featured image if exists
                if ($post->featured_image) {
                    Storage::disk('public')->delete($post->featured_image);
                }
                
                $post->delete();
                session()->flash('message', 'Post deleted successfully!');
            }
        }
        
        $this->showDeleteModal = false;
        $this->postToDelete = null;
    }

    public function togglePostStatus($postId)
    {
        $post = BlogPost::find($postId);
        
        if ($post) {
            $newStatus = $post->status === 'published' ? 'draft' : 'published';
            
            $post->update([
                'status' => $newStatus,
                'published_at' => $newStatus === 'published' && !$post->published_at ? now() : $post->published_at
            ]);
            
            session()->flash('message', "Post {$newStatus} successfully!");
        }
    }

    public function toggleFeatured($postId)
    {
        $post = BlogPost::find($postId);
        
        if ($post) {
            $post->update(['is_featured' => !$post->is_featured]);
            $action = $post->is_featured ? 'featured' : 'unfeatured';
            session()->flash('message', "Post {$action} successfully!");
        }
    }

    public function applyBulkAction()
    {
        if (empty($this->bulkActions) || !$this->bulkAction) {
            return;
        }

        $posts = BlogPost::whereIn('id', $this->bulkActions);

        switch ($this->bulkAction) {
            case 'publish':
                $posts->update([
                    'status' => 'published',
                    'published_at' => now()
                ]);
                session()->flash('message', count($this->bulkActions) . ' posts published successfully!');
                break;
                
            case 'draft':
                $posts->update(['status' => 'draft']);
                session()->flash('message', count($this->bulkActions) . ' posts moved to draft!');
                break;
                
            case 'feature':
                $posts->update(['is_featured' => true]);
                session()->flash('message', count($this->bulkActions) . ' posts featured!');
                break;
                
            case 'unfeature':
                $posts->update(['is_featured' => false]);
                session()->flash('message', count($this->bulkActions) . ' posts unfeatured!');
                break;
                
            case 'delete':
                // Delete featured images
                $postsToDelete = $posts->get();
                foreach ($postsToDelete as $post) {
                    if ($post->featured_image) {
                        Storage::disk('public')->delete($post->featured_image);
                    }
                }
                $posts->delete();
                session()->flash('message', count($this->bulkActions) . ' posts deleted successfully!');
                break;
        }

        $this->reset(['bulkActions', 'bulkAction']);
    }

    public function render()
    {
        $query = BlogPost::with(['author', 'category']);

        // Apply filters
        if ($this->search) {
            $query->where('title', 'like', '%' . $this->search . '%');
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->categoryFilter !== 'all') {
            $query->where('category_id', $this->categoryFilter);
        }

        if ($this->authorFilter !== 'all') {
            $query->where('author_id', $this->authorFilter);
        }

        // Apply sorting
        switch ($this->sortBy) {
            case 'title':
                $query->orderBy('title');
                break;
            case 'views':
                $query->orderByDesc('views_count');
                break;
            case 'likes':
                $query->orderByDesc('likes_count');
                break;
            case 'comments':
                $query->orderByDesc('comments_count');
                break;
            case 'oldest':
                $query->orderBy('created_at');
                break;
            default:
                $query->orderByDesc('created_at');
        }

        $posts = $query->paginate(15);

        // Get filter options
        $categories = BlogCategory::active()->ordered()->get();
        $authors = User::whereIn('id', BlogPost::distinct()->pluck('author_id'))->get();

        return view('livewire.blog.admin-blog-posts', [
            'posts' => $posts,
            'categories' => $categories,
            'authors' => $authors,
        ])->layout('layouts.dashboard');
    }
}
