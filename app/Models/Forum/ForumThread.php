<?php
// app/Models/Forum/ForumThread.php
namespace App\Models\Forum;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;
use App\Models\Forum\ForumReply;

class ForumThread extends Model
{
    use HasFactory;

    protected $table = 'forum_threads';

    protected $fillable = [
        'category', 'user_id', 'title', 'content', 
        'is_locked', 'is_pinned', 'is_flagged',
        'replies_count', 'views', 'last_activity_at', 'last_reply_user_id'
    ];

    protected $casts = [
        'is_locked' => 'boolean',
        'is_pinned' => 'boolean',
        'is_flagged' => 'boolean',
        'last_activity_at' => 'datetime',
    ];

    protected $attributes = [
        'replies_count' => 0,
        'views' => 0,
        'is_locked' => false,
        'is_pinned' => false,
        'is_flagged' => false,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(ForumReply::class, 'thread_id');
    }

    public function lastReplyUser()
    {
        return $this->belongsTo(User::class, 'last_reply_user_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_locked', false);
    }

    public function updateActivity()
    {
        $this->update([
            'last_activity_at' => now(),
            'last_reply_user_id' => auth()->id(),
        ]);
    }
}