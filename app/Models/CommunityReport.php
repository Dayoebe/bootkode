<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunityReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'reporter_id',
        'reportable_type',
        'reportable_id',
        'reason',
        'description',
        'status',
        'moderator_id',
        'moderator_notes',
        'resolved_at'
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    public function reportable()
    {
        return $this->morphTo();
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'reviewed' => 'blue',
            'resolved' => 'green',
            'dismissed' => 'gray',
            default => 'gray'
        };
    }

    public function resolve($notes = null)
    {
        $this->update([
            'status' => 'resolved',
            'moderator_notes' => $notes,
            'resolved_at' => now(),
            'moderator_id' => auth()->id(),
        ]);
    }

    public function dismiss($notes = null)
    {
        $this->update([
            'status' => 'dismissed',
            'moderator_notes' => $notes,
            'resolved_at' => now(),
            'moderator_id' => auth()->id(),
        ]);
    }
}