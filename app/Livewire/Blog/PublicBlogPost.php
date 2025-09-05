<?php

namespace App\Livewire\Blog;

use App\Models\BlogPost;
use App\Models\BlogComment;
use App\Models\BlogReaction;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Cache;

class PublicBlogPost extends Component
{
    use WithPagination;

    public BlogPost $post;
    public $newComment = '';
    public $replyTo = null;
    public $guestName = '';
    public $guestEmail = '';
    public $showCommentForm = false;
    public $userHasLiked = false;
    public $userHasBookmarked = false;

    protected $rules = [
        'newComment' => 'required|min:5|max:500',
        'guestName' => 'nullable|string|max:100',
        'guestEmail' => 'nullable|email|max:100'
    ];

    public function mount(BlogPost $post)
    {
        $this->post = $post;
        
        // Increment view count
        $this->incrementPostView();
        
        // Check if user has reacted
        $this->checkUserReactions();
    }

    public function incrementPostView()
    {
        $sessionKey = 'blog_post_viewed_' . $this->post->id;
        
        if (!session()->has($sessionKey)) {
            $this->post->incrementViews();
            session([$sessionKey => true]);
        }
    }

    public function checkUserReactions()
    {
        if (auth()->check()) {
            $this->userHasLiked = $this->post->reactions()
                ->where('user_id', auth()->id())
                ->where('type', 'like')
                ->exists();
                
            $this->userHasBookmarked = $this->post->reactions()
                ->where('user_id', auth()->id())
                ->where('type', 'bookmark')
                ->exists();
        } else {
            $ip = request()->ip();
            $this->userHasLiked = $this->post->reactions()
                ->where('ip_address', $ip)
                ->where('type', 'like')
                ->exists();
        }
    }

    public function toggleReaction($type = 'like')
    {
        $userId = auth()->id();
        $ipAddress = request()->ip();
        
        $added = BlogReaction::toggle(
            BlogPost::class,
            $this->post->id,
            $type,
            $userId,
            $userId ? null : $ipAddress
        );

        // Update counter on post
        if ($type === 'like') {
            $likesCount = $this->post->likes()->count();
            $this->post->update(['likes_count' => $likesCount]);
            $this->userHasLiked = $added;
        } else {
            $this->userHasBookmarked = $added;
        }

        // Show feedback message
        $action = $added ? 'added to' : 'removed from';
        $message = $type === 'like' ? "Post {$action} your likes!" : "Post {$action} your bookmarks!";
        
        session()->flash('message', $message);
    }

    public function toggleCommentForm()
    {
        $this->showCommentForm = !$this->showCommentForm;
        $this->replyTo = null;
    }

    public function replyToComment($commentId)
    {
        $this->replyTo = $commentId;
        $this->showCommentForm = true;
    }

    public function cancelReply()
    {
        $this->replyTo = null;
        $this->showCommentForm = false;
    }

    public function submitComment()
    {
        $this->validate();

        if (!auth()->check() && (!$this->guestName && !$this->guestEmail)) {
            $this->addError('guestInfo', 'Please provide your name or email to comment as a guest.');
            return;
        }

        $comment = BlogComment::create([
            'post_id' => $this->post->id,
            'user_id' => auth()->id(),
            'parent_id' => $this->replyTo,
            'author_name' => auth()->check() ? null : $this->guestName,
            'author_email' => auth()->check() ? null : $this->guestEmail,
            'content' => $this->newComment,
            'status' => 'approved', // Auto-approve for now, you can add moderation later
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Update comment count
        $this->post->increment('comments_count');

        // Reset form
        $this->reset(['newComment', 'guestName', 'guestEmail', 'replyTo', 'showCommentForm']);

        session()->flash('message', 'Comment posted successfully!');
    }

    public function render()
    {
        // Get comments with pagination
        $comments = $this->post->comments()
            ->with(['user', 'replies.user'])
            ->approved()
            ->topLevel()
            ->orderByDesc('created_at')
            ->paginate(10);

        // Get related posts
        $relatedPosts = Cache::remember("related_posts_{$this->post->id}", 3600, function () {
            return BlogPost::published()
                ->where('id', '!=', $this->post->id)
                ->where(function ($query) {
                    if ($this->post->category_id) {
                        $query->where('category_id', $this->post->category_id);
                    }
                    
                    if ($this->post->tags) {
                        foreach ($this->post->tags as $tag) {
                            $query->orWhereJsonContains('tags', $tag);
                        }
                    }
                })
                ->orderByDesc('views_count')
                ->take(4)
                ->get();
        });

        return view('livewire.blog.public-blog-post', [
            'comments' => $comments,
            'relatedPosts' => $relatedPosts
        ])->layout('layouts.blog', [
            'title' => $this->post->title,
            'post' => $this->post
        ]);
    }
}
