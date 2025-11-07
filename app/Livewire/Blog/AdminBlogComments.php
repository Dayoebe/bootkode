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
    public $selectAll = false; // FIXED: Added missing property

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'postFilter' => ['except' => 'all'],
        'sortBy' => ['except' => 'latest'],
    ];

    // FIXED: Added updatedSelectAll method
    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->bulkActions = $this->getFilteredQuery()->pluck('id')->toArray();
        } else {
            $this->bulkActions = [];
        }
    }

    // FIXED: Added updatedBulkActions to sync with selectAll
    public function updatedBulkActions()
    {
        $allCommentIds = $this->getFilteredQuery()->pluck('id')->toArray();
        $this->selectAll = count($this->bulkActions) === count($allCommentIds) && count($allCommentIds) > 0;
    }

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
        try {
            $comment = BlogComment::find($commentId);
            
            if ($comment) {
                $wasApproved = $comment->status === 'approved';
                $comment->update(['status' => 'approved']);
                
                // Update comment count on post if newly approved
                if (!$wasApproved) {
                    $comment->post->increment('comments_count');
                }
                
                session()->flash('message', 'Comment approved successfully!');
                
                // Close modal if open
                if ($this->showModal && $this->selectedComment && $this->selectedComment->id === $commentId) {
                    $this->closeModal();
                }
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error approving comment: ' . $e->getMessage());
            logger()->error('Comment approval error', ['error' => $e->getMessage()]);
        }
    }

    public function rejectComment($commentId)
    {
        try {
            $comment = BlogComment::find($commentId);
            
            if ($comment) {
                $wasApproved = $comment->status === 'approved';
                $comment->update(['status' => 'rejected']);
                
                // Update comment count on post if was approved
                if ($wasApproved) {
                    $comment->post->decrement('comments_count');
                }
                
                session()->flash('message', 'Comment rejected successfully!');
                
                // Close modal if open
                if ($this->showModal && $this->selectedComment && $this->selectedComment->id === $commentId) {
                    $this->closeModal();
                }
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error rejecting comment: ' . $e->getMessage());
            logger()->error('Comment rejection error', ['error' => $e->getMessage()]);
        }
    }

    public function deleteComment($commentId)
    {
        try {
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
                
                // Close modal if open
                if ($this->showModal) {
                    $this->closeModal();
                }
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting comment: ' . $e->getMessage());
            logger()->error('Comment deletion error', ['error' => $e->getMessage()]);
        }
    }

    public function applyBulkAction()
    {
        if (empty($this->bulkActions) || !$this->bulkAction) {
            session()->flash('error', 'Please select comments and an action.');
            return;
        }

        $comments = BlogComment::whereIn('id', $this->bulkActions);
        $count = count($this->bulkActions);

        try {
            switch ($this->bulkAction) {
                case 'approve':
                    $comments->update(['status' => 'approved']);
                    $this->updatePostCommentCounts();
                    session()->flash('message', "{$count} comment(s) approved!");
                    break;
                    
                case 'reject':
                    $comments->update(['status' => 'rejected']);
                    $this->updatePostCommentCounts();
                    session()->flash('message', "{$count} comment(s) rejected!");
                    break;
                    
                case 'delete':
                    $comments->delete();
                    $this->updatePostCommentCounts();
                    session()->flash('message', "{$count} comment(s) deleted!");
                    break;
                    
                default:
                    session()->flash('error', 'Invalid bulk action.');
                    return;
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error performing bulk action: ' . $e->getMessage());
            logger()->error('Bulk action error', ['error' => $e->getMessage()]);
        }

        $this->reset(['bulkActions', 'bulkAction', 'selectAll']);
    }

    private function updatePostCommentCounts()
    {
        // Update all post comment counts efficiently
        try {
            $postIds = BlogPost::pluck('id');
            
            foreach ($postIds as $postId) {
                $count = BlogComment::where('post_id', $postId)
                    ->where('status', 'approved')
                    ->count();
                    
                BlogPost::where('id', $postId)->update(['comments_count' => $count]);
            }
        } catch (\Exception $e) {
            logger()->error('Error updating comment counts', ['error' => $e->getMessage()]);
        }
    }

    // FIXED: Helper method to get filtered query
    private function getFilteredQuery()
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

        return $query;
    }

    public function render()
    {
        $comments = $this->getFilteredQuery()->paginate(15);

        // Get posts for filter dropdown
        $posts = BlogPost::select('id', 'title')->orderBy('title')->get();

        return view('livewire.blog.admin-blog-comments', [
            'comments' => $comments,
            'posts' => $posts,
        ])->layout('layouts.dashboard');
    }
}