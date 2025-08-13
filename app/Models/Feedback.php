<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'category',
        'message',
        'attachment', // New field
        'rating',
        'status',
        'response',
        'responded_by',
        'responded_at',
    ];

    protected $casts = [
        'status' => 'string',
        'responded_at' => 'datetime',
        'rating' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function responder()
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    public function getAttachmentUrlAttribute()
    {
        return $this->attachment ? asset('storage/' . $this->attachment) : null;
    }
}