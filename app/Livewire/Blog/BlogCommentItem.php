<?php

namespace App\Livewire\Blog;

use App\Models\Content\BlogComment;
use App\Models\Content\BlogReaction;
use Livewire\Component;

class BlogCommentItem extends Component
{
    public BlogComment $comment;
    public $showReplyForm = false;
    public $replyContent = '';
    public $guestName = '';
    public $guestEmail = '';
    public $userHasLiked = false;

    protected $rules = [
        'replyContent' => 'required|min:5|max:500',
        'guestName' => 'nullable|string|max:100',
        'guestEmail' => 'nullable|email|max:100'
    ];

    public function mount(BlogComment $comment)
    {
        $this->comment = $comment;
        $this->checkUserLike();
    }

    public function checkUserLike()
    {
        if (auth()->check()) {
            $this->userHasLiked = $this->comment->reactions()
                ->where('user_id', auth()->id())
                ->where('type', 'like')
                ->exists();
        } else {
            $ip = request()->ip();
            $this->userHasLiked = $this->comment->reactions()
                ->where('ip_address', $ip)
                ->where('type', 'like')
                ->exists();
        }
    }

    public function toggleLike()
    {
        $userId = auth()->id();
        $ipAddress = request()->ip();
        
        $added = BlogReaction::toggle(
            BlogComment::class,
            $this->comment->id,
            'like',
            $userId,
            $userId ? null : $ipAddress
        );

        // Update counter
        $likesCount = $this->comment->likes()->count();
        $this->comment->update(['likes_count' => $likesCount]);
        
        $this->userHasLiked = $added;
    }

    public function toggleReplyForm()
    {
        $this->showReplyForm = !$this->showReplyForm;
    }

    public function submitReply()
    {
        $this->validate();

        if (!auth()->check() && (!$this->guestName && !$this->guestEmail)) {
            $this->addError('guestInfo', 'Please provide your name or email to reply as a guest.');
            return;
        }

        BlogComment::create([
            'post_id' => $this->comment->post_id,
            'user_id' => auth()->id(),
            'parent_id' => $this->comment->id,
            'author_name' => auth()->check() ? null : $this->guestName,
            'author_email' => auth()->check() ? null : $this->guestEmail,
            'content' => $this->replyContent,
            'status' => 'pending',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Reset form
        $this->reset(['replyContent', 'guestName', 'guestEmail', 'showReplyForm']);

        session()->flash('message', 'Reply submitted for moderation.');
    }

    public function render()
    {
        return view('livewire.blog.blog-comment-item');
    }
}
