<?php

namespace App\Models\Assessment;

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

        if ($accuracy >= 80) return 'easy';
        if ($accuracy >= 60) return 'medium';
        if ($accuracy >= 40) return 'hard';
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

        $correctAnswers = is_string($this->correct_answers)
            ? json_decode($this->correct_answers, true)
            : $this->correct_answers;

        return is_array($correctAnswers) && count($correctAnswers) > 1;
    }

    /**
     * FIXED: Simplified and more reliable answer checking
     */
    public function isCorrectAnswer($answer)
    {
        // Log for debugging
        \Log::info('Checking answer', [
            'question_id' => $this->id,
            'question_type' => $this->question_type,
            'user_answer' => $answer,
            'correct_answers' => $this->correct_answers,
            'correct_answers_type' => gettype($this->correct_answers)
        ]);

        // Get correct answers as array
        $correctAnswers = $this->correct_answers;
        if (is_string($correctAnswers)) {
            $correctAnswers = json_decode($correctAnswers, true);
        }
        
        if (!is_array($correctAnswers) || empty($correctAnswers)) {
            \Log::warning('No correct answers defined', ['question_id' => $this->id]);
            return false;
        }

        // Normalize correct answers to integers
        $correctAnswers = array_map('intval', $correctAnswers);

        // Handle different question types
        switch ($this->question_type) {
            case 'multiple_choice':
                return $this->checkMultipleChoiceAnswer($answer, $correctAnswers);
            
            case 'true_false':
                return $this->checkTrueFalseAnswer($answer, $correctAnswers);
            
            case 'short_answer':
            case 'fill_blank':
                return $this->checkTextAnswer($answer, $correctAnswers);
            
            default:
                // For other types, convert to int and compare
                $userAnswer = is_array($answer) ? array_map('intval', $answer) : intval($answer);
                
                if (is_array($userAnswer)) {
                    sort($userAnswer);
                    sort($correctAnswers);
                    $result = $userAnswer === $correctAnswers;
                } else {
                    $result = in_array($userAnswer, $correctAnswers);
                }
                
                \Log::info('Answer check result', [
                    'question_id' => $this->id,
                    'result' => $result,
                    'user_answer' => $userAnswer,
                    'correct_answers' => $correctAnswers
                ]);
                
                return $result;
        }
    }

    /**
     * Check multiple choice answer
     */
    protected function checkMultipleChoiceAnswer($answer, $correctAnswers)
    {
        // Handle array of answers (multiple selections)
        if (is_array($answer)) {
            $userAnswers = array_map('intval', $answer);
            sort($userAnswers);
            sort($correctAnswers);
            
            $result = $userAnswers === $correctAnswers;
            
            \Log::info('Multiple choice (multiple) check', [
                'question_id' => $this->id,
                'user_answers' => $userAnswers,
                'correct_answers' => $correctAnswers,
                'result' => $result
            ]);
            
            return $result;
        }
        
        // Handle single answer
        $userAnswer = intval($answer);
        $result = in_array($userAnswer, $correctAnswers);
        
        \Log::info('Multiple choice (single) check', [
            'question_id' => $this->id,
            'user_answer' => $userAnswer,
            'correct_answers' => $correctAnswers,
            'result' => $result
        ]);
        
        return $result;
    }

    /**
     * Check true/false answer
     */
    protected function checkTrueFalseAnswer($answer, $correctAnswers)
    {
        $userAnswer = intval($answer);
        $result = in_array($userAnswer, $correctAnswers);
        
        \Log::info('True/False check', [
            'question_id' => $this->id,
            'user_answer' => $userAnswer,
            'correct_answers' => $correctAnswers,
            'result' => $result
        ]);
        
        return $result;
    }

    /**
     * Check text-based answer
     */
    protected function checkTextAnswer($answer, $correctAnswers)
    {
        $userAnswer = is_array($answer) ? implode(' ', $answer) : $answer;
        $userAnswer = strtolower(trim($userAnswer));
        
        foreach ($correctAnswers as $correctAnswer) {
            $correctAnswer = strtolower(trim($correctAnswer));
            if ($userAnswer === $correctAnswer) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Calculate partial credit for multiple-choice with multiple correct answers
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

            $correctAnswers = array_map('intval', $correctAnswers);
            
            if (!is_array($answer)) {
                $answer = [$answer];
            }
            $answer = array_map('intval', $answer);

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