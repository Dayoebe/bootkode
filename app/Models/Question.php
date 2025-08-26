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
        'drag_drop' => 'Drag & Drop',
        'qna_topic' => 'Q&A Topic',
        'project_criteria' => 'Project Criteria',
        'assignment_question' => 'Assignment Question'
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
    /**
     * Check if question has multiple correct answers
     */
    public function hasMultipleCorrectAnswers()
    {
        if ($this->question_type !== 'multiple_choice') {
            return false;
        }

        // Decode correct_answers from JSON if it's a string
        $correctAnswers = is_string($this->correct_answers)
            ? json_decode($this->correct_answers, true)
            : $this->correct_answers;

        return is_array($correctAnswers) && count($correctAnswers) > 1;
    }
    /**
     * Check if an answer is correct, handling both index-based and text-based correct_answers dynamically.
     */
    public function isCorrectAnswer($answer)
    {
        $correctAnswers = is_string($this->correct_answers)
            ? json_decode($this->correct_answers, true)
            : $this->correct_answers;

        if (!is_array($correctAnswers) || empty($correctAnswers)) {
            return false;
        }

        $options = $this->options ?? [];

        // Detect if correct_answers are indices (all numeric) or text values
        $areIndices = true;
        foreach ($correctAnswers as $ca) {
            if (!is_numeric($ca)) {
                $areIndices = false;
                break;
            }
        }

        if ($areIndices) {
            // Index-based comparison (standard case)
            $correctIndices = array_map('intval', $correctAnswers);

            if (is_array($answer)) {
                $userAnswers = array_map('intval', $answer);
                sort($userAnswers);
                sort($correctIndices);
                return $userAnswers === $correctIndices;
            } else {
                return in_array((int) $answer, $correctIndices);
            }
        } else {
            // Text-based comparison (edge case, e.g., manual DB set)
            $correctTexts = array_map(function ($t) {
                return strtolower(trim((string) $t));
            }, $correctAnswers);

            if (is_array($answer)) {
                // Map user answers (assumed indices) to option texts
                $userTexts = array_map(function ($i) use ($options) {
                    return isset($options[$i]) ? strtolower(trim($options[$i])) : '';
                }, $answer);
                // Filter out empty mappings (invalid indices)
                $userTexts = array_filter($userTexts);
                sort($userTexts);
                sort($correctTexts);
                return $userTexts === $correctTexts;
            } else {
                // Single answer: if numeric, map to option text; else use as text
                if (is_numeric($answer)) {
                    $userText = isset($options[(int) $answer]) ? strtolower(trim($options[(int) $answer])) : '';
                } else {
                    $userText = strtolower(trim((string) $answer));
                }
                return in_array($userText, $correctTexts);
            }
        }

        // Log unexpected cases for debugging
        \Illuminate\Support\Facades\Log::warning("Unexpected answer format in Question {$this->id}", [
            'answer' => $answer,
            'correct_answers' => $correctAnswers,
        ]);
        return false;
    }

    /**
     * Calculate partial credit for multiple-choice with multiple correct answers.
     * Updated to handle text-based correct_answers consistently.
     */
    public function calculatePartialCredit($answer)
    {
        if ($this->isCorrectAnswer($answer) === true) {
            return $this->points;
        }

        if ($this->question_type === 'multiple_choice' && $this->hasMultipleCorrectAnswers()) {
            $correctAnswers = is_string($this->correct_answers)
                ? json_decode($this->correct_answers, true)
                : $this->correct_answers;

            // Use same detection as isCorrectAnswer
            $areIndices = true;
            foreach ($correctAnswers as $ca) {
                if (!is_numeric($ca)) {
                    $areIndices = false;
                    break;
                }
            }

            if ($areIndices) {
                $correctAnswers = array_map('intval', $correctAnswers);
                if (!is_array($answer)) {
                    $answer = [$answer];
                }
                $answer = array_map('intval', $answer);
            } else {
                // Text-based: map answer to texts if indices
                $options = $this->options ?? [];
                if (!is_array($answer)) {
                    $answer = [$answer];
                }
                $answerTexts = array_map(function ($a) use ($options) {
                    return is_numeric($a) && isset($options[(int) $a]) ? strtolower(trim($options[(int) $a])) : strtolower(trim((string) $a));
                }, $answer);
                $correctAnswers = array_map(function ($c) {
                    return strtolower(trim((string) $c));
                }, $correctAnswers);
                $answer = $answerTexts; // Override for comparison
            }

            $correctCount = count(array_intersect($answer, $correctAnswers));
            $totalCorrect = count($correctAnswers);
            $incorrectCount = count(array_diff($answer, $correctAnswers));

            // Award partial: correct minus penalties for incorrect
            $score = max(0, ($correctCount - $incorrectCount) / $totalCorrect);
            return $this->points * $score;
        }

        return 0;
    }
}