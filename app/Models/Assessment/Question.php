<?php

namespace App\Models\Assessment;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use \App\Traits\PreservesCourseHistory;

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

    const DIFFICULTY_LEVELS = [
        'easy' => 'Easy',
        'medium' => 'Medium',
        'hard' => 'Hard',
        'expert' => 'Expert'
    ];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function studentAnswers()
    {
        return $this->hasMany(StudentAnswer::class);
    }

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
            static::where('assessment_id', $question->assessment_id)
                ->where('order', '>', $question->order)
                ->decrement('order');
        });
    }

    public function getFormattedOptionsAttribute()
    {
        if (!is_array($this->options)) {
            return [];
        }

        $formatted = [];
        foreach ($this->options as $index => $option) {
            $formatted[] = [
                'index' => $index,
                'letter' => chr(65 + $index),
                'text' => $option,
                'is_correct' => in_array($index, $this->correct_answers ?? [])
            ];
        }

        return $formatted;
    }

    public function getStatsAttribute()
    {
        $totalAnswers = $this->studentAnswers()->count();
        $correctAnswers = $this->studentAnswers()->where('is_correct', true)->count();

        return [
            'total_answers' => $totalAnswers,
            'correct_answers' => $correctAnswers,
            'accuracy_rate' => $totalAnswers > 0 ? round(($correctAnswers / $totalAnswers) * 100, 1) : 0,
            'difficulty_actual' => $this->calculateActualDifficulty($totalAnswers, $totalAnswers > 0 ? $correctAnswers / $totalAnswers * 100 : 0)
        ];
    }

    protected function calculateActualDifficulty(int $totalAnswers, float $accuracy)
    {
        if ($totalAnswers < 5) {
            return 'insufficient_data';
        }

        if ($accuracy >= 80) return 'easy';
        if ($accuracy >= 60) return 'medium';
        if ($accuracy >= 40) return 'hard';
        return 'very_hard';
    }

    public function getQuestionTypeLabelAttribute()
    {
        return self::QUESTION_TYPES[$this->question_type] ?? ucfirst($this->question_type);
    }

    public function getDifficultyLabelAttribute()
    {
        return self::DIFFICULTY_LEVELS[$this->difficulty_level] ?? ucfirst($this->difficulty_level);
    }

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

    /** Grade original option positions only; written work requires review. */
    public function isCorrectAnswer($answer): bool
    {
        $codec = app(\App\Services\StructuredQuestionService::class);
        if (in_array($this->question_type, \App\Services\StructuredQuestionService::TYPES, true)) {
            if (($codec->array($this->options)['version'] ?? null) !== 1) return false;
            return $codec->grade($answer, $codec->array($this->correct_answers));
        }
        if (!in_array($this->question_type, ['multiple_choice', 'true_false'], true) || $answer === null || $answer === '') return false;
        $expected = $codec->array($this->correct_answers);
        if (!$expected) return false;
        if (is_string($answer)) {
            $decoded = json_decode($answer, true);
            if (json_last_error() === JSON_ERROR_NONE && $decoded !== null) $answer = $decoded;
        }
        $selected = is_array($answer) ? $answer : [$answer];
        foreach (array_merge($selected, $expected) as $value) if ((!is_int($value) && !is_string($value)) || filter_var($value, FILTER_VALIDATE_INT) === false) return false;
        $selected = array_map('intval', $selected); $expected = array_map('intval', $expected);
        if (count(array_unique($selected)) !== count($selected)) return false;
        sort($selected); sort($expected);
        return $selected === $expected;
    }

    public function calculatePartialCredit($answer)
    {
        // These formats have an exact-key contract. No unconfigured partial mark.
        return $this->isCorrectAnswer($answer) ? $this->points : 0;
    }
}
