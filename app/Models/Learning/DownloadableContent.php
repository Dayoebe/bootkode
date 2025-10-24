<?php

namespace App\Models\Learning;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Core\User;

class DownloadableContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'downloaded_at',
        'size_mb',
        'content_types' // JSON field to store types of content (lessons, pdfs, etc.)
    ];

    protected $casts = [
        'downloaded_at' => 'datetime',
        'content_types' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class); 
    }


    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}