<?php

namespace App\Livewire\Blog;

use App\Models\Content\BlogComment;
use App\Models\Content\BlogPost;
use Livewire\Component;
use Livewire\WithPagination;

class AdminBlogComments extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    public $postFilter = 'all';
    public $sortBy = 'latest';
    public $showModal = false;
    public $selectedComment = null;
    public $bulkActions = [];
    public $bulkAction = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'postFilter' => ['except' => 'all'],
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

    public function updatingPostFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'statusFilter', 'postFilter', 'sortBy']);
        $this->resetPage();
    }

    public function viewComment($commentId)
    {
        $this->selectedComment = BlogComment::with(['post', 'user', 'parent'])->find($commentId);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedComment = null;
    }

    public function approveComment($commentId)
    {
        $comment = BlogComment::find($commentId);
        
        if ($comment) {
            $comment->update(['status' => 'approved']);
            
            // Update comment count on post if newly approved
            if ($comment->wasChanged('status')) {
                $comment->post->increment('comments_count');
            }
            
            session()->flash('message', 'Comment approved successfully!');
        }
    }

    public function rejectComment($commentId)
    {
        $comment = BlogComment::find($commentId);
        
        if ($comment) {
            $wasApproved = $comment->status === 'approved';
            $comment->update(['status' => 'rejected']);
            
            // Update comment count on post if was approved
            if ($wasApproved) {
                $comment->post->decrement('comments_count');
            }
            
            session()->flash('message', 'Comment rejected successfully!');
        }
    }

    public function deleteComment($commentId)
    {
        $comment = BlogComment::find($commentId);
        
        if ($comment) {
            $wasApproved = $comment->status === 'approved';
            
            // Delete replies first
            $comment->replies()->delete();
            
            $comment->delete();
            
            // Update comment count on post if was approved
            if ($wasApproved) {
                $comment->post->decrement('comments_count');
            }
            
            session()->flash('message', 'Comment deleted successfully!');
        }
    }

    public function applyBulkAction()
    {
        if (empty($this->bulkActions) || !$this->bulkAction) {
            return;
        }

        $comments = BlogComment::whereIn('id', $this->bulkActions);

        switch ($this->bulkAction) {
            case 'approve':
                $comments->update(['status' => 'approved']);
                // Update post comment counts (simplified)
                $this->updatePostCommentCounts();
                session()->flash('message', count($this->bulkActions) . ' comments approved!');
                break;
                
            case 'reject':
                $comments->update(['status' => 'rejected']);
                $this->updatePostCommentCounts();
                session()->flash('message', count($this->bulkActions) . ' comments rejected!');
                break;
                
            case 'delete':
                $comments->delete();
                $this->updatePostCommentCounts();
                session()->flash('message', count($this->bulkActions) . ' comments deleted!');
                break;
        }

        $this->reset(['bulkActions', 'bulkAction']);
    }

    private function updatePostCommentCounts()
    {
        // Update all post comment counts
        BlogPost::withCount(['comments' => function ($query) {
            $query->where('status', 'approved');
        }])->chunk(100, function ($posts) {
            foreach ($posts as $post) {
                $post->update(['comments_count' => $post->comments_count]);
            }
        });
    }

    public function render()
    {
        $query = BlogComment::with(['post', 'user']);

        // Apply filters
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('content', 'like', '%' . $this->search . '%')
                  ->orWhere('author_name', 'like', '%' . $this->search . '%')
                  ->orWhere('author_email', 'like', '%' . $this->search . '%')
                  ->orWhereHas('user', function ($userQuery) {
                      $userQuery->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        if ($this->postFilter !== 'all') {
            $query->where('post_id', $this->postFilter);
        }

        // Apply sorting
        switch ($this->sortBy) {
            case 'oldest':
                $query->orderBy('created_at');
                break;
            case 'likes':
                $query->orderByDesc('likes_count');
                break;
            default:
                $query->orderByDesc('created_at');
        }

        $comments = $query->paginate(15);

        // Get posts for filter dropdown
        $posts = BlogPost::select('id', 'title')->orderBy('title')->get();

        return view('livewire.blog.admin-blog-comments', [
            'comments' => $comments,
            'posts' => $posts,
        ])->layout('layouts.dashboard');
    }
}