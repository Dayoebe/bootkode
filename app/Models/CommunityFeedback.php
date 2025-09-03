<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;


// Community Feedback Model
class CommunityFeedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'category', 'subject', 'message', 'priority', 
        'status', 'assigned_to', 'admin_response', 'responded_at'
    ];

    protected $casts = [
        'responded_at' => 'datetime',
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
        return match($this->status) {
            'open' => 'red',
            'in_progress' => 'yellow',
            'resolved' => 'green',
            'closed' => 'gray',
            default => 'gray'
        };
    }
}