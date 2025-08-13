<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemStatus extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service',
        'status',
        'title',
        'description',
        'severity',
        'started_at',
        'resolved_at',
    ];

    protected $casts = [
        'status' => 'string',
        'severity' => 'string',
        'started_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}