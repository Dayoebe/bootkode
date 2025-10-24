<?php 

// app/Models/Community/CommunityFeedback.php
namespace App\Models\Community;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Core\User;

class CommunityFeedback extends Model
{
    use HasFactory;

    protected $table = 'community_feedback';

    protected $fillable = [
        'user_id', 'category', 'subject', 'message', 'priority', 
        'status', 'assigned_to', 'admin_response', 'responded_at'
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    protected $attributes = [
        'status' => 'open',
        'priority' => 'medium',
        'category' => 'general',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status ?? 'open') {
            'open' => 'red',
            'in_progress' => 'yellow', 
            'resolved' => 'green',
            'closed' => 'gray',
            default => 'gray'
        };
    }
}
