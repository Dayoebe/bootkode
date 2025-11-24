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
        'time_spent_seconds',
        'submitted_at',
        'graded_by',
        'graded_at',
        'feedback',
        'question_order'
    ];
    
    protected $casts = [
        'is_correct' => 'boolean',
        'points_earned' => 'decimal:2',
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
        'question_order' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function grader()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    public function scopeLatestAttempt($query, $userId, $assessmentId)
    {
        return $query->where('user_id', $userId)
            ->where('assessment_id', $assessmentId)
            ->orderBy('attempt_number', 'desc');
    }

    public function scopeCorrect($query)
    {
        return $query->where('is_correct', true);
    }

    public function scopeGraded($query)
    {
        return $query->whereNotNull('graded_at');
    }

    public function scopePendingGrading($query)
    {
        return $query->whereNull('graded_at')
            ->whereHas('question', function ($q) {
                $q->whereIn('question_type', ['essay', 'short_answer']);
            });
    }

    /**
     * FIXED: Auto-grade with proper null/empty answer handling
     */
    public function autoGrade()
    {
        if (!$this->question) {
            \Log::warning('Attempted to grade answer without question', [
                'answer_id' => $this->id,
                'question_id' => $this->question_id
            ]);
            return false;
        }

        // CRITICAL: Check if answer is null, empty, or "null" string
        if ($this->answer === null || $this->answer === '' || $this->answer === 'null') {
            \Log::info('Grading unanswered question as incorrect', [
                'question_id' => $this->question_id,
                'user_id' => $this->user_id
            ]);
            
            // Mark as incorrect with 0 points
            $this->update([
                'is_correct' => false,
                'points_earned' => 0,
                'graded_at' => now()
            ]);
            
            return true;
        }

        // Only auto-grade certain question types
        if (in_array($this->question->question_type, ['essay', 'short_answer'])) {
            return false;
        }

        // Get the user's answer - handle different formats
        $userAnswer = $this->answer;
        
        // If it's stored as JSON string, decode it
        if (is_string($userAnswer) && (strpos($userAnswer, '[') === 0 || strpos($userAnswer, '{') === 0)) {
            $decoded = json_decode($userAnswer, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $userAnswer = $decoded;
            }
        }
        
        // Convert to integer for multiple choice and true/false
        if (in_array($this->question->question_type, ['multiple_choice', 'true_false'])) {
            if (is_array($userAnswer)) {
                $userAnswer = array_map('intval', $userAnswer);
            } else {
                $userAnswer = (int)$userAnswer;
            }
        }

        \Log::info('Auto-grading answer', [
            'question_id' => $this->question_id,
            'user_answer' => $userAnswer,
            'question_type' => $this->question->question_type
        ]);

        // Check if answer is correct
        $isCorrect = $this->question->isCorrectAnswer($userAnswer);
        $pointsEarned = 0;

        if ($isCorrect === true) {
            $pointsEarned = $this->question->points;
        } elseif ($isCorrect === false) {
            $pointsEarned = 0;
        } else {
            // For partial credit
            $pointsEarned = $this->question->calculatePartialCredit($userAnswer);
        }

        $this->update([
            'is_correct' => $isCorrect === true,
            'points_earned' => $pointsEarned,
            'graded_at' => now()
        ]);

        \Log::info('Answer graded', [
            'question_id' => $this->question_id,
            'is_correct' => $isCorrect === true,
            'points_earned' => $pointsEarned
        ]);

        return true;
    }

    public function getFormattedAnswerAttribute()
    {
        // FIXED: Handle null/empty answers
        if ($this->answer === null || $this->answer === '' || $this->answer === 'null') {
            return 'Not Answered';
        }

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

    public function needsManualGrading()
    {
        return is_null($this->graded_at) &&
            $this->question &&
            in_array($this->question->question_type, ['essay', 'short_answer']);
    }

    public function getFormattedTimeSpentAttribute()
    {
        if (!$this->time_spent_seconds) {
            return 'Not recorded';
        }

        $minutes = floor($this->time_spent_seconds / 60);
        $seconds = $this->time_spent_seconds % 60;

        if ($minutes > 0) {
            return $minutes . 'm ' . $seconds . 's';
        }

        return $seconds . 's';
    }

    public function getAccuracyPercentage()
    {
        if (!$this->question || $this->question->points == 0) {
            return 0;
        }

        return round(($this->points_earned / $this->question->points) * 100, 1);
    }
}