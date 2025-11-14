<?php

namespace App\Models\Assessment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Core\User;

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
        'time_spent_seconds', // FIXED: Match database column name
        'submitted_at',
        'graded_by',
        'graded_at',
        'feedback'
    ];
    
    protected $casts = [
        // FIXED: Don't cast as array - store as JSON string or integer
        // 'answer' => 'array',  ❌ REMOVED THIS
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
            ->whereHas('question', function ($q) {
                $q->whereIn('question_type', ['essay', 'short_answer']);
            });
    }

    /**
     * FIXED: Improved auto-grade with proper answer handling
     */
    public function autoGrade()
    {
        if (!$this->question) {
            \Log::error('Cannot auto-grade: Question not found', [
                'student_answer_id' => $this->id,
                'question_id' => $this->question_id
            ]);
            return false;
        }

        // Only auto-grade certain question types
        if (in_array($this->question->question_type, ['essay', 'short_answer'])) {
            \Log::info('Skipping auto-grade for manual grading question', [
                'question_id' => $this->question->id,
                'question_type' => $this->question->question_type
            ]);
            return false; // Requires manual grading
        }

        \Log::info('Starting auto-grade', [
            'student_answer_id' => $this->id,
            'question_id' => $this->question->id,
            'question_type' => $this->question->question_type,
            'user_answer_raw' => $this->answer,
            'user_answer_type' => gettype($this->answer)
        ]);

        // FIXED: Get the user's answer - it's stored as integer or string
        $userAnswer = $this->answer;
        
        // If it's stored as JSON string (like "[0]"), decode it
        if (is_string($userAnswer) && (strpos($userAnswer, '[') === 0 || strpos($userAnswer, '{') === 0)) {
            $decoded = json_decode($userAnswer, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $userAnswer = $decoded;
            }
        }
        
        // Convert to integer for multiple choice and true/false
        if (in_array($this->question->question_type, ['multiple_choice', 'true_false'])) {
            $userAnswer = (int)$userAnswer;
        }

        \Log::info('Processed user answer', [
            'question_id' => $this->question->id,
            'processed_answer' => $userAnswer,
            'processed_type' => gettype($userAnswer)
        ]);

        // Check if answer is correct
        $isCorrect = $this->question->isCorrectAnswer($userAnswer);
        $pointsEarned = 0;

        if ($isCorrect === true) {
            $pointsEarned = $this->question->points;
        } elseif ($isCorrect === false) {
            $pointsEarned = 0;
        } else {
            // For partial credit (e.g., multiple correct answers)
            $pointsEarned = $this->question->calculatePartialCredit($userAnswer);
        }

        \Log::info('Auto-grade result', [
            'student_answer_id' => $this->id,
            'question_id' => $this->question->id,
            'is_correct' => $isCorrect,
            'points_earned' => $pointsEarned,
            'max_points' => $this->question->points
        ]);

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

        $answer = $this->answer;
        
        // If answer is JSON string, decode it
        if (is_string($answer) && (strpos($answer, '[') === 0 || strpos($answer, '{') === 0)) {
            $decoded = json_decode($answer, true);
            if (json_last_error() === JSON_ERROR_NONE && $decoded !== null) {
                $answer = $decoded;
            }
        }

        switch ($this->question->question_type) {
            case 'multiple_choice':
                $options = $this->question->options ?? [];
                
                if (is_array($answer)) {
                    return collect($answer)
                        ->map(function ($index) use ($options) {
                            return isset($options[$index]) 
                                ? chr(65 + $index) . '. ' . strip_tags($options[$index])
                                : "Option " . ($index + 1);
                        })
                        ->join(', ');
                }
                
                $index = (int)$answer;
                return isset($options[$index]) 
                    ? chr(65 + $index) . '. ' . strip_tags($options[$index])
                    : 'Unknown option';

            case 'true_false':
                return ((int)$answer === 0) ? 'True' : 'False';

            case 'short_answer':
            case 'fill_blank':
            case 'essay':
                return is_array($answer) ? implode(' ', $answer) : $answer;

            default:
                return is_array($answer) ? json_encode($answer) : $answer;
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