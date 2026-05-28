<?php

namespace App\Models\Ai;

use App\Models\Core\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiLearningProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'goal',
        'skill_diagnosis',
        'adaptive_path',
        'course_recommendations',
        'assessment_feedback',
        'signals',
        'diagnosed_at',
    ];

    protected $casts = [
        'skill_diagnosis' => 'array',
        'adaptive_path' => 'array',
        'course_recommendations' => 'array',
        'assessment_feedback' => 'array',
        'signals' => 'array',
        'diagnosed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
