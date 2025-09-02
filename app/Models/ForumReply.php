<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ForumReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'thread_id', 'user_id', 'content', 'parent_id'
    ];

    public function thread()
    {
        return $this->belongsTo(ForumThread::class, 'thread_id');
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
            // Update thread reply count and last activity
            $reply->thread->increment('replies_count');
            $reply->thread->updateActivity();
        });

        static::deleted(function ($reply) {
            // Update thread reply count
            $reply->thread->decrement('replies_count');
        });
    }
}
