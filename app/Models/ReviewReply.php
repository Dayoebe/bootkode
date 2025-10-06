<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewReply extends Model
{
    use HasFactory;

    protected $fillable = ['review_id', 'user_id', 'reply_text'];

    public function review()
    {
        return $this->belongsTo(CourseReview::class, 'review_id'); // Explicitly set foreign key
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}