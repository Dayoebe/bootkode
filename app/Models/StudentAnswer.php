<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'assessment_id',
        'question_id',
        'attempt_number',
        'answer',
        'points_earned',
        'is_correct',
        'time_spent',
        'submitted_at',
        'graded_by',
        'graded_at',
        'feedback'
    ];

    protected $casts = [
        'answer' => 'array',
        'is_correct' => 'boolean',
        'points_earned' => 'decimal:2',
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime'
    ];

    /**
     * Get the user who submitted the answer
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the assessment this answer belongs to
     */
    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * Get the question this answer is for
     */
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Get the grader (if manually graded)
     */
    public function grader()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    /**
     * Scope for latest attempt
     */
    public function scopeLatestAttempt($query, $userId, $assessmentId)
    {
        return $query->where('user_id', $userId)
                    ->where('assessment_id', $assessmentId)
                    ->orderBy('attempt_number', 'desc');
    }

    /**
     * Scope for correct answers
     */
    public function scopeCorrect($query)
    {
        return $query->where('is_correct', true);
    }

    /**
     * Scope for graded answers
     */
    public function scopeGraded($query)
    {
        return $query->whereNotNull('graded_at');
    }

    /**
     * Scope for pending grading
     */
    public function scopePendingGrading($query)
    {
        return $query->whereNull('graded_at')
                    ->whereHas('question', function($q) {
                        $q->whereIn('question_type', ['essay', 'short_answer']);
                    });
    }

    /**
     * Auto-grade the answer if possible
     */
    public function autoGrade()
    {
        if (!$this->question) {
            return false;
        }

        // Only auto-grade certain question types
        if (in_array($this->question->question_type, ['essay'])) {
            return false; // Requires manual grading
        }

        $isCorrect = $this->question->isCorrectAnswer($this->answer);
        $pointsEarned = 0;

        if ($isCorrect === true) {
            $pointsEarned = $this->question->points;
        } elseif ($isCorrect === false) {
            $pointsEarned = 0;
        } else {
            // For partial credit (e.g., multiple correct answers)
            $pointsEarned = $this->question->calculatePartialCredit($this->answer);
        }

        $this->update([
            'is_correct' => $isCorrect === true,
            'points_earned' => $pointsEarned,
            'graded_at' => now()
        ]);

        return true;
    }

    /**
     * Get formatted answer for display
     */
    public function getFormattedAnswerAttribute()
    {
        if (!$this->question) {
            return $this->answer;
        }

        switch ($this->question->question_type) {
            case 'multiple_choice':
                if (is_array($this->answer)) {
                    $options = $this->question->options ?? [];
                    return collect($this->answer)
                        ->map(function($index) use ($options) {
                            return $options[$index] ?? "Option " . ($index + 1);
                        })
                        ->join(', ');
                }
                return $this->question->options[$this->answer] ?? 'Unknown option';

            case 'true_false':
                $options = $this->question->options ?? ['True', 'False'];
                return $options[$this->answer] ?? 'Unknown';

            case 'short_answer':
            case 'fill_blank':
            case 'essay':
                return is_array($this->answer) ? implode(' ', $this->answer) : $this->answer;

            default:
                return is_array($this->answer) ? json_encode($this->answer) : $this->answer;
        }
    }

    /**
     * Check if answer needs manual grading
     */
    public function needsManualGrading()
    {
        return is_null($this->graded_at) && 
               $this->question && 
               in_array($this->question->question_type, ['essay', 'short_answer']);
    }

    /**
     * Get time spent in human readable format
     */
    public function getFormattedTimeSpentAttribute()
    {
        if (!$this->time_spent) {
            return 'Not recorded';
        }

        $minutes = floor($this->time_spent / 60);
        $seconds = $this->time_spent % 60;

        if ($minutes > 0) {
            return $minutes . 'm ' . $seconds . 's';
        }

        return $seconds . 's';
    }

    /**
     * Calculate accuracy percentage
     */
    public function getAccuracyPercentage()
    {
        if (!$this->question || $this->question->points == 0) {
            return 0;
        }

        return round(($this->points_earned / $this->question->points) * 100, 1);
    }
}