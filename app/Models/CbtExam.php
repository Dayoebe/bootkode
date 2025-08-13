<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CbtExam extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'duration_minutes',
        'total_marks',
        'is_active',
        'starts_at',
        'ends_at',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function questions()
    {
        return $this->hasMany(CbtQuestion::class);
    }

    public function answers()
    {
        return $this->hasMany(CbtAnswer::class);
    }

    public function results()
    {
        return $this->hasMany(CbtResult::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function scopeActive($query)
{
    return $query->where('is_active', true)
        ->where(function($q) {
            $q->whereNull('starts_at')
              ->orWhere('starts_at', '<=', now());
        })
        ->where(function($q) {
            $q->whereNull('ends_at')
              ->orWhere('ends_at', '>=', now());
        });
}
}