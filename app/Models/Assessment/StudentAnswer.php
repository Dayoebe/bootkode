<?php

namespace App\Models\Assessment;

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
        'time_spent_seconds',
        'submitted_at',
        'question_order',
        'exam_data' // Add this field to store shuffling metadata
    ];

    protected $casts = [
        'exam_data' => 'array',
        'submitted_at' => 'datetime',
        'question_order' => 'array'
    ];

    /**
     * Perfect autoGrade method that handles shuffled options
     */
    public function autoGrade()
    {
        try {
            $question = $this->question;
            $userAnswer = $this->answer;
            
            // Get exam data containing shuffling information
            $examData = $this->exam_data ?? [];
            
            \Log::info('Auto-grading started', [
                'student_answer_id' => $this->id,
                'question_id' => $question->id,
                'question_type' => $question->question_type,
                'user_answer' => $userAnswer,
                'exam_data' => $examData
            ]);

            if ($question->question_type === 'multiple_choice') {
                $this->gradeMultipleChoice($question, $userAnswer, $examData);
                
            } elseif ($question->question_type === 'true_false') {
                $this->gradeTrueFalse($question, $userAnswer);
                
            } else {
                // Default handling for other question types
                $this->is_correct = false;
                $this->points_earned = 0;
            }

            // Save the results
            $this->save();

            \Log::info('Auto-grading completed', [
                'student_answer_id' => $this->id,
                'is_correct' => $this->is_correct,
                'points_earned' => $this->points_earned
            ]);

            return true;

        } catch (\Exception $e) {
            \Log::error('Auto-grading failed', [
                'student_answer_id' => $this->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Default to incorrect on error
            $this->is_correct = false;
            $this->points_earned = 0;
            $this->save();

            return false;
        }
    }

    /**
     * Grade multiple choice questions with shuffling support
     */
    protected function gradeMultipleChoice($question, $userAnswer, $examData)
    {
        // Get correct answers from database (original indices)
        $originalCorrectAnswers = $question->correct_answers;
        
        // Handle string format correct_answers
        if (is_string($originalCorrectAnswers)) {
            $originalCorrectAnswers = json_decode($originalCorrectAnswers, true) ?? [];
        }
        
        // Ensure it's an array
        if (!is_array($originalCorrectAnswers)) {
            $originalCorrectAnswers = [$originalCorrectAnswers];
        }
        
        // Convert to integers
        $originalCorrectAnswers = array_map('intval', $originalCorrectAnswers);

        \Log::debug('Multiple choice grading details', [
            'user_answer' => $userAnswer,
            'original_correct_answers' => $originalCorrectAnswers,
            'was_shuffled' => $examData['was_shuffled'] ?? false,
            'inverse_mapping' => $examData['inverse_mapping'] ?? null
        ]);

        // Handle shuffled options
        if (isset($examData['was_shuffled']) && $examData['was_shuffled'] && isset($examData['inverse_mapping'])) {
            $inverseMapping = $examData['inverse_mapping'];
            
            // User's answer is in shuffled index, map back to original
            if (isset($inverseMapping[$userAnswer])) {
                $userOriginalAnswer = $inverseMapping[$userAnswer];
                $this->is_correct = in_array($userOriginalAnswer, $originalCorrectAnswers);
            } else {
                // Invalid answer (shouldn't happen in normal operation)
                $this->is_correct = false;
            }
        } else {
            // No shuffling - direct comparison
            $this->is_correct = in_array((int)$userAnswer, $originalCorrectAnswers);
        }

        // Calculate points
        if ($this->is_correct) {
            $this->points_earned = $question->points;
        } else {
            $this->points_earned = 0;
        }
    }

    /**
     * Grade true/false questions
     */
    protected function gradeTrueFalse($question, $userAnswer)
    {
        // Get correct answer from database
        $correctAnswer = $question->correct_answers;
        
        // Handle string format
        if (is_string($correctAnswer)) {
            $correctAnswer = json_decode($correctAnswer, true);
        }
        
        // Handle array format
        if (is_array($correctAnswer)) {
            $correctAnswer = $correctAnswer[0] ?? 0;
        }
        
        $correctAnswer = (int)$correctAnswer;
        $userAnswer = (int)$userAnswer;

        \Log::debug('True/False grading details', [
            'user_answer' => $userAnswer,
            'correct_answer' => $correctAnswer
        ]);

        $this->is_correct = $userAnswer === $correctAnswer;
        
        if ($this->is_correct) {
            $this->points_earned = $question->points;
        } else {
            $this->points_earned = 0;
        }
    }

    /**
     * Relationships
     */
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

    /**
     * Format answer for display
     */
    public function getFormattedAnswerAttribute()
    {
        if ($this->question->question_type === 'multiple_choice') {
            $options = ['A', 'B', 'C', 'D', 'E', 'F'];
            return $options[$this->answer] ?? 'Unknown';
        } elseif ($this->question->question_type === 'true_false') {
            return $this->answer == 0 ? 'True' : 'False';
        }
        
        return $this->answer;
    }
}