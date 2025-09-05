<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// Code Challenge Models
class CodeChallenge extends Model
{
    use HasFactory;

    protected $fillable = [
        'creator_id', 'title', 'slug', 'description', 'problem_statement',
        'test_cases', 'sample_inputs', 'sample_outputs', 'difficulty', 
        'tags', 'points', 'starts_at', 'ends_at', 'is_active'
    ];

    protected $casts = [
        'test_cases' => 'array',
        'sample_inputs' => 'array',
        'sample_outputs' => 'array',
        'tags' => 'array',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function submissions()
    {
        return $this->hasMany(CodeSubmission::class, 'challenge_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($challenge) {
            $challenge->slug = Str::slug($challenge->title);
        });
    }
}
