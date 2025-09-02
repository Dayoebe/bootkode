<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ForumCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'icon', 'color', 'order', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function threads()
    {
        return $this->hasMany(ForumThread::class, 'category_id');
    }

    public function latestThread()
    {
        return $this->hasOne(ForumThread::class, 'category_id')->latest('last_activity_at');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            $category->slug = Str::slug($category->name);
        });

        static::updating(function ($category) {
            if ($category->isDirty('name')) {
                $category->slug = Str::slug($category->name);
            }
        });
    }
}

class ForumThread extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'user_id', 'title', 'slug', 'content', 
        'is_pinned', 'is_locked', 'views', 'replies_count', 'last_activity_at'
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
        'last_activity_at' => 'datetime',
    ];

    public function category()
    {
        return $this->belongsTo(ForumCategory::class, 'category_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(ForumReply::class, 'thread_id');
    }

    public function latestReply()
    {
        return $this->hasOne(ForumReply::class, 'thread_id')->latest();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($thread) {
            $thread->slug = Str::slug($thread->title);
            $thread->last_activity_at = now();
        });

        static::updating(function ($thread) {
            if ($thread->isDirty('title')) {
                $thread->slug = Str::slug($thread->title);
            }
        });
    }

    public function incrementViews()
    {
        $this->increment('views');
    }

    public function updateActivity()
    {
        $this->update(['last_activity_at' => now()]);
    }
}
