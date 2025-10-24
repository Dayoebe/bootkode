<?php

namespace App\Models\Mentorship;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Core\User;

class InterviewQuestionSet extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'created_by',
        'name',
        'description',
        'type',
        'difficulty_level',
        'total_questions',
        'estimated_duration',
        'question_distribution',
        'is_active',
        'is_template',
        'is_public',
    ];

    protected $casts = [
        'question_distribution' => 'array',
        'is_active' => 'boolean',
        'is_template' => 'boolean',
        'is_public' => 'boolean',
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions()
    {
        return $this->belongsToMany(InterviewQuestion::class, 'interview_question_set_items')
            ->withPivot(['order', 'points'])
            ->orderBy('interview_question_set_items.order')
            ->withTimestamps();
    }

    public function mockInterviews()
    {
        return $this->hasMany(MockInterview::class, 'question_set_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeTemplates($query)
    {
        return $query->where('is_template', true);
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    // Methods
    public function addQuestion($questionId, $order = null, $points = null)
    {
        $order = $order ?? ($this->questions()->count() + 1);
        $points = $points ?? 10;

        $this->questions()->attach($questionId, [
            'order' => $order,
            'points' => $points,
        ]);

        $this->updateTotalQuestions();
    }

    public function removeQuestion($questionId)
    {
        $this->questions()->detach($questionId);
        $this->updateTotalQuestions();
    }

    public function updateTotalQuestions()
    {
        $this->update([
            'total_questions' => $this->questions()->count()
        ]);
    }

    // Accessors
    public function getTotalPointsAttribute()
    {
        return $this->questions()->sum('interview_question_set_items.points');
    }

    public function getFormattedDurationAttribute()
    {
        if (!$this->estimated_duration) {
            return 'Variable duration';
        }

        $hours = floor($this->estimated_duration / 60);
        $minutes = $this->estimated_duration % 60;

        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }

        return "{$minutes} minutes";
    }
}