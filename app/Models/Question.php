<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'assessment_id',
        'question_text',
        'question_type',
        'options',
        'correct_answers',
        'points',
        'explanation',
        'is_required',
        'time_limit',
        'order',
        'difficulty_level',
        'tags'
    ];

    protected $casts = [
        'options' => 'array',
        'correct_answers' => 'array',
        'is_required' => 'boolean',
        'tags' => 'array',
        'points' => 'decimal:2'
    ];

    // Question types
    const QUESTION_TYPES = [
        'multiple_choice' => 'Multiple Choice',
        'true_false' => 'True/False',
        'short_answer' => 'Short Answer',
        'essay' => 'Essay',
        'fill_blank' => 'Fill in the Blank',
        'matching' => 'Matching',
        'ordering' => 'Ordering',
        'drag_drop' => 'Drag & Drop'
    ];

    // Difficulty levels
    const DIFFICULTY_LEVELS = [
        'easy' => 'Easy',
        'medium' => 'Medium',
        'hard' => 'Hard',
        'expert' => 'Expert'
    ];

    /**
     * Get the assessment that owns the question.
     */
    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    /**
     * Get the student answers for this question.
     */
    public function studentAnswers()
    {
        return $this->hasMany(StudentAnswer::class);
    }

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($question) {
            if (!$question->order) {
                $maxOrder = static::where('assessment_id', $question->assessment_id)->max('order') ?? 0;
                $question->order = $maxOrder + 1;
            }
        });

        static::deleting(function ($question) {
            // Reorder remaining questions
            static::where('assessment_id', $question->assessment_id)
                ->where('order', '>', $question->order)
                ->decrement('order');
        });
    }

    /**
     * Check if question has multiple correct answers
     */
    public function hasMultipleCorrectAnswers()
    {
        return $this->question_type === 'multiple_choice' &&
            is_array($this->correct_answers) &&
            count($this->correct_answers) > 1;
    }

    /**
     * Get formatted options for display
     */
    public function getFormattedOptionsAttribute()
    {
        if (!is_array($this->options)) {
            return [];
        }

        $formatted = [];
        foreach ($this->options as $index => $option) {
            $formatted[] = [
                'index' => $index,
                'letter' => chr(65 + $index), // A, B, C, D...
                'text' => $option,
                'is_correct' => in_array($index, $this->correct_answers ?? [])
            ];
        }

        return $formatted;
    }

    /**
     * Check if an answer is correct
     */
    public function isCorrectAnswer($answer)
    {
        if (!is_array($this->correct_answers) || empty($this->correct_answers)) {
            return false;
        }

        switch ($this->question_type) {
            case 'multiple_choice':
                // Handle both single and multiple selections
                if (is_array($answer)) {
                    // Multiple selection - check if arrays match exactly
                    sort($answer);
                    $correctAnswers = $this->correct_answers;
                    sort($correctAnswers);
                    return $answer == $correctAnswers;
                } else {
                    // Single selection
                    return in_array((int) $answer, $this->correct_answers);
                }

            case 'true_false':
                return in_array((int) $answer, $this->correct_answers);

            case 'short_answer':
            case 'fill_blank':
                // For text answers, do case-insensitive comparison with all correct answers
                $answer = strtolower(trim($answer));
                foreach ($this->correct_answers as $correctAnswer) {
                    if (strtolower(trim($correctAnswer)) === $answer) {
                        return true;
                    }
                    // Also check for partial matches (contains)
                    if (strpos(strtolower(trim($correctAnswer)), $answer) !== false) {
                        return true;
                    }
                }
                return false;

            case 'essay':
                // Essays require manual grading
                return null;

            default:
                return false;
        }
    }

    /**
     * Calculate partial credit for an answer
     */
    public function calculatePartialCredit($answer)
    {
        if ($this->isCorrectAnswer($answer) === true) {
            return $this->points;
        }

        if ($this->question_type === 'multiple_choice' && $this->hasMultipleCorrectAnswers()) {
            if (!is_array($answer)) {
                $answer = [$answer];
            }

            $answer = array_map('intval', $answer);
            $correctAnswers = array_map('intval', $this->correct_answers);

            $correctCount = count(array_intersect($answer, $correctAnswers));
            $totalCorrect = count($correctAnswers);
            $incorrectCount = count(array_diff($answer, $correctAnswers));

            // Award partial credit based on correct selections minus incorrect selections
            $score = max(0, ($correctCount - $incorrectCount) / $totalCorrect);
            return $this->points * $score;
        }

        return 0;
    }

    /**
     * Get question statistics
     */
    public function getStatsAttribute()
    {
        $totalAnswers = $this->studentAnswers()->count();
        $correctAnswers = $this->studentAnswers()->where('is_correct', true)->count();

        return [
            'total_answers' => $totalAnswers,
            'correct_answers' => $correctAnswers,
            'accuracy_rate' => $totalAnswers > 0 ? round(($correctAnswers / $totalAnswers) * 100, 1) : 0,
            'difficulty_actual' => $this->calculateActualDifficulty()
        ];
    }

    /**
     * Calculate actual difficulty based on student performance
     */
    protected function calculateActualDifficulty()
    {
        $stats = $this->stats;

        if ($stats['total_answers'] < 5) {
            return 'insufficient_data';
        }

        $accuracy = $stats['accuracy_rate'];

        if ($accuracy >= 80)
            return 'easy';
        if ($accuracy >= 60)
            return 'medium';
        if ($accuracy >= 40)
            return 'hard';
        return 'very_hard';
    }

    /**
     * Get the question type label
     */
    public function getQuestionTypeLabelAttribute()
    {
        return self::QUESTION_TYPES[$this->question_type] ?? ucfirst($this->question_type);
    }

    /**
     * Get the difficulty level label
     */
    public function getDifficultyLabelAttribute()
    {
        return self::DIFFICULTY_LEVELS[$this->difficulty_level] ?? ucfirst($this->difficulty_level);
    }
}