<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InterviewQuestion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'created_by',
        'question',
        'type',
        'difficulty_level',
        'answer_type',
        'options',
        'correct_answer',
        'keywords',
        'max_points',
        'time_limit',
        'category',
        'tags',
        'industry',
        'job_role',
        'sample_answer',
        'evaluation_rubric',
        'is_active',
        'is_approved',
        'approved_by',
        'approved_at',
        'times_used',
        'avg_score',
    ];
    
    protected $casts = [
        'options' => 'array',
        'keywords' => 'array',
        'tags' => 'array',
        'evaluation_rubric' => 'array',
        'is_active' => 'boolean',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
        'avg_score' => 'decimal:2',
    ];
    
    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    
    public function questionSets()
    {
        return $this->belongsToMany(InterviewQuestionSet::class, 'interview_question_set_items')
            ->withPivot(['order', 'points'])
            ->withTimestamps();
    }
    
    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }
    
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
    
    public function scopeByDifficulty($query, $difficulty)
    {
        return $query->where('difficulty_level', $difficulty);
    }
    
    // Methods
    public function approve($userId)
    {
        $this->update([
            'is_approved' => true,
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }
    
    public function incrementUsage()
    {
        $this->increment('times_used');
    }
    
    public function updateAverageScore($newScore)
    {
        $currentAvg = $this->avg_score ?? 0;
        $timesUsed = $this->times_used;
        
        if ($timesUsed > 0) {
            $newAvg = (($currentAvg * $timesUsed) + $newScore) / ($timesUsed + 1);
            $this->update(['avg_score' => $newAvg]);
        }
    }
    
    // Accessors
    public function getTypeLabelAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->type));
    }
    
    public function getDifficultyLabelAttribute()
    {
        return ucfirst($this->difficulty_level);
    }
}