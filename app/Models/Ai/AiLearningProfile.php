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
        'interests',
        'existing_knowledge',
        'starting_level',
        'learning_preferences',
        'onboarding_completed_at',
        'onboarding_skipped_at',
        'skill_diagnosis',
        'adaptive_path',
        'course_recommendations',
        'assessment_feedback',
        'signals',
        'diagnosed_at',
    ];

    protected $casts = [
        'interests' => 'array',
        'existing_knowledge' => 'array',
        'learning_preferences' => 'array',
        'onboarding_completed_at' => 'datetime',
        'onboarding_skipped_at' => 'datetime',
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
