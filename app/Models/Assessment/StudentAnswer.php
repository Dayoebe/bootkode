<?php

namespace App\Models\Assessment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'question_order',
        'graded_by', 'graded_at', 'feedback',
        'exam_data'
    ];

    protected $casts = [
        'exam_data' => 'array',
        'graded_at' => 'datetime',
        'submitted_at' => 'datetime',
        'question_order' => 'array'
    ];

    /**
     * Auto-grade the answer
     * The answer is ALREADY in original position (mapped in CbtExamInterface)
     */
    public function autoGrade(?array $gradingSnapshot = null)
    {
        try {
            $question = $this->question;
            if ($question && $gradingSnapshot) {
                $question = clone $question;
                $question->forceFill(\Illuminate\Support\Arr::only($gradingSnapshot, ['correct_answers', 'points', 'question_type']));
            }
            $userAnswer = $this->answer; // Already in ORIGINAL position

            if (!$question) {
                $this->is_correct = false;
                $this->points_earned = 0;
                $this->save();
                return false;
            }

            // Get original correct answers from database
            $originalCorrectAnswers = $question->correct_answers;
            if (is_string($originalCorrectAnswers)) {
                $originalCorrectAnswers = json_decode($originalCorrectAnswers, true) ?? [];
            }
            if (!is_array($originalCorrectAnswers)) {
                $originalCorrectAnswers = [$originalCorrectAnswers];
            }
            $originalCorrectAnswers = array_map('intval', $originalCorrectAnswers);

            // Grade based on question type
            if ($question->question_type === 'multiple_choice') {
                $this->gradeMultipleChoice($question, $userAnswer, $originalCorrectAnswers);
            } elseif ($question->question_type === 'true_false') {
                $this->gradeTrueFalse($question, $userAnswer, $originalCorrectAnswers);
            } else {
                $this->is_correct = false;
                $this->points_earned = 0;
            }

            $this->save();

            return true;

        } catch (\Exception $e) {
            \Log::error('Auto-grade failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->is_correct = false;
            $this->points_earned = 0;
            $this->save();
            return false;
        }
    }

    /**
     * Grade multiple choice - answer is ALREADY mapped to original position
     */
    protected function gradeMultipleChoice($question, $userAnswer, $originalCorrectAnswers)
    {
        $selected = $userAnswer;
        if (is_string($userAnswer)) {
            $decoded = json_decode($userAnswer, true);
            if (json_last_error() === JSON_ERROR_NONE && $decoded !== null) $selected = $decoded;
        }
        $selected = array_values(array_unique(array_map('intval', is_array($selected) ? $selected : [$selected])));
        $expected = array_values(array_unique(array_map('intval', $originalCorrectAnswers)));
        sort($selected); sort($expected);
        $this->is_correct = $selected === $expected;
        $this->points_earned = $this->is_correct ? $question->points : 0;
    }

    /**
     * Grade true/false
     */
    protected function gradeTrueFalse($question, $userAnswer, $originalCorrectAnswers)
    {
        $correctAnswer = (int) ($originalCorrectAnswers[0] ?? 0);
        $userAnswer = (int) $userAnswer;

        $this->is_correct = $userAnswer === $correctAnswer;
        $this->points_earned = $this->is_correct ? $question->points : 0;

    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(\App\Models\Core\User::class);
    }
    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Format answer for display
     */
    public function getFormattedAnswerAttribute()
    {
        if ($this->question->question_type === 'multiple_choice') {
            $options = ['A', 'B', 'C', 'D', 'E', 'F'];
            $answer = $this->answer;

            if (is_string($answer)) {
                $decoded = json_decode($answer, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $answer = $decoded;
                }
            }

            if (is_array($answer)) {
                $letters = collect($answer)
                    ->map(fn($index) => $options[(int) $index] ?? null)
                    ->filter()
                    ->values();

                return $letters->isNotEmpty() ? $letters->implode(', ') : 'Unknown';
            }

            return $options[(int) $answer] ?? 'Unknown';
        } elseif ($this->question->question_type === 'true_false') {
            return $this->answer == 0 ? 'True' : 'False';
        }
        return $this->answer;
    }
}
