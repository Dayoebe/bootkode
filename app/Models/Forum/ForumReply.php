<?php

// app/Models/Forum/ForumReply.php
namespace App\Models\Forum;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;

class ForumReply extends Model
{
    use HasFactory;

    protected $table = 'forum_replies';

    protected $fillable = [
        'thread_id', 'user_id', 'content', 'parent_id'
    ];

    public function thread()
    {
        return $this->belongsTo(ForumThread::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(ForumReply::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ForumReply::class, 'parent_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($reply) {
            $reply->thread->increment('replies_count');
            $reply->thread->updateActivity();
        });

        static::deleted(function ($reply) {
            $reply->thread->decrement('replies_count');
        });
    }
}
