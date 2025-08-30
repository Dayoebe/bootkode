<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// JobSave Model
class JobSave extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'user_id',
        'notes'
    ];

    protected $casts = [
        'notes' => 'array'
    ];

    public function job()
    {
        return $this->belongsTo(JobPortal::class, 'job_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

