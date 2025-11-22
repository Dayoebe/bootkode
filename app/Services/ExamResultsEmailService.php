<?php

namespace App\Services;

use App\Models\Assessment\Assessment;
use App\Models\Assessment\StudentAnswer;
use App\Models\Core\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\ExamResultsMail;
use Carbon\Carbon;

class ExamResultsEmailService
{
    /**
     * Send exam results email to student
     */
    public function sendResultsEmail(
        User $student,
        Assessment $assessment,
        int $attemptNumber,
        array $results
    ) {
        try {
            // Get detailed results
            $detailedResults = $this->prepareDetailedResults(
                $student,
                $assessment,
                $attemptNumber,
                $results
            );
            
            // Send email
            Mail::to($student->email)->send(
                new ExamResultsMail($detailedResults)
            );
            
            // Log successful send
            \Log::info('Exam results email sent', [
                'student_id' => $student->id,
                'assessment_id' => $assessment->id,
                'attempt' => $attemptNumber
            ]);
            
            return true;
            
        } catch (\Exception $e) {
            \Log::error('Failed to send exam results email', [
                'error' => $e->getMessage(),
                'student_id' => $student->id,
                'assessment_id' => $assessment->id
            ]);
            
            return false;
        }
    }
    
    /**
     * Prepare detailed results for email
     */
    protected function prepareDetailedResults(
        User $student,
        Assessment $assessment,
        int $attemptNumber,
        array $results
    ): array {
        // Get all student answers for this attempt
        $studentAnswers = StudentAnswer::where('user_id', $student->id)
            ->where('assessment_id', $assessment->id)
            ->where('attempt_number', $attemptNumber)
            ->with(['question'])
            ->orderBy('question_id')
            ->get();
        
        // Categorize questions
        $correctAnswers = $studentAnswers->where('is_correct', true);
        $incorrectAnswers = $studentAnswers->where('is_correct', false);
        $unansweredQuestions = $assessment->questions()
            ->whereNotIn('id', $studentAnswers->pluck('question_id'))
            ->get();
        
        // Calculate statistics
        $statistics = $this->calculateStatistics($studentAnswers, $assessment, $results);
        
        // Get performance insights
        $insights = $this->generateInsights($statistics, $results);
        
        // Get recommendations
        $recommendations = $this->generateRecommendations($statistics, $results);
        
        // Format question details
        $questionDetails = $this->formatQuestionDetails($studentAnswers, $unansweredQuestions);
        
        // Generate certificate data if passed
        $certificateData = null;
        if ($results['passed']) {
            $certificateData = $this->generateCertificateData($student, $assessment, $results, $attemptNumber);
        }
        
        return [
            'student' => [
                'name' => $student->name,
                'email' => $student->email,
            ],
            'assessment' => [
                'title' => $assessment->title,
                'description' => $assessment->description,
                'pass_percentage' => $assessment->pass_percentage,
                'max_score' => $assessment->max_score,
                'duration' => $assessment->estimated_duration_minutes,
            ],
            'results' => $results,
            'attempt_number' => $attemptNumber,
            'statistics' => $statistics,
            'insights' => $insights,
            'recommendations' => $recommendations,
            'questions' => $questionDetails,
            'certificate' => $certificateData,
            'submitted_at' => Carbon::now()->format('l, F j, Y \a\t g:i A'),
            'exam_url' => route('cbt.exams'),
            'results_url' => route('cbt.viewer'),
        ];
    }
    
    /**
     * Calculate detailed statistics
     */
    protected function calculateStatistics($studentAnswers, $assessment, $results): array
    {
        $totalQuestions = $results['total_questions'];
        $correctCount = $results['correct_answers'];
        $incorrectCount = $totalQuestions - $correctCount;
        
        // Calculate accuracy rate
        $accuracyRate = $totalQuestions > 0 
            ? round(($correctCount / $totalQuestions) * 100, 2) 
            : 0;
        
        // Calculate average time per question
        $avgTimePerQuestion = $totalQuestions > 0 
            ? round($results['time_spent'] / $totalQuestions, 1) 
            : 0;
        
        // Get question type breakdown
        $typeBreakdown = $studentAnswers->groupBy('question.question_type')
            ->map(function ($answers, $type) {
                $correct = $answers->where('is_correct', true)->count();
                $total = $answers->count();
                $accuracy = $total > 0 ? round(($correct / $total) * 100, 1) : 0;
                
                return [
                    'type' => ucfirst(str_replace('_', ' ', $type)),
                    'total' => $total,
                    'correct' => $correct,
                    'incorrect' => $total - $correct,
                    'accuracy' => $accuracy,
                ];
            })->values()->toArray();
        
        // Calculate points breakdown
        $pointsBreakdown = [
            'earned' => $results['total_points'],
            'possible' => $results['max_points'],
            'percentage' => $results['percentage'],
            'difference' => $results['max_points'] - $results['total_points'],
        ];
        
        // Performance metrics
        $performanceMetrics = [
            'speed' => $this->calculateSpeedMetric($results['time_spent'], $assessment->estimated_duration_minutes * 60),
            'completion' => $this->calculateCompletionMetric($correctCount, $totalQuestions),
            'consistency' => $this->calculateConsistencyMetric($studentAnswers),
        ];
        
        return [
            'total_questions' => $totalQuestions,
            'correct_answers' => $correctCount,
            'incorrect_answers' => $incorrectCount,
            'accuracy_rate' => $accuracyRate,
            'time_spent' => $this->formatTime($results['time_spent']),
            'time_spent_seconds' => $results['time_spent'],
            'avg_time_per_question' => $this->formatTime($avgTimePerQuestion),
            'type_breakdown' => $typeBreakdown,
            'points_breakdown' => $pointsBreakdown,
            'performance_metrics' => $performanceMetrics,
        ];
    }
    
    /**
     * Generate performance insights
     */
    protected function generateInsights($statistics, $results): array
    {
        $insights = [];
        
        // Overall performance insight
        if ($results['passed']) {
            if ($results['percentage'] >= 90) {
                $insights[] = [
                    'type' => 'excellent',
                    'icon' => '🏆',
                    'title' => 'Outstanding Performance!',
                    'message' => 'You scored in the top range with ' . $results['percentage'] . '%. Exceptional work!',
                ];
            } elseif ($results['percentage'] >= 80) {
                $insights[] = [
                    'type' => 'great',
                    'icon' => '⭐',
                    'title' => 'Great Job!',
                    'message' => 'You passed with a strong score of ' . $results['percentage'] . '%. Well done!',
                ];
            } else {
                $insights[] = [
                    'type' => 'good',
                    'icon' => '✅',
                    'title' => 'You Passed!',
                    'message' => 'You achieved a passing score of ' . $results['percentage'] . '%. Congratulations!',
                ];
            }
        } else {
            $pointsNeeded = ($results['max_points'] * $results['pass_percentage'] / 100) - $results['total_points'];
            $insights[] = [
                'type' => 'improvement',
                'icon' => '📚',
                'title' => 'Keep Learning!',
                'message' => 'You need ' . round($pointsNeeded, 1) . ' more points to pass. Review the incorrect answers and try again!',
            ];
        }
        
        // Time management insight
        $timeEfficiency = $statistics['performance_metrics']['speed'];
        if ($timeEfficiency < 50) {
            $insights[] = [
                'type' => 'speed',
                'icon' => '⚡',
                'title' => 'Excellent Time Management',
                'message' => 'You completed the exam efficiently, using only ' . $timeEfficiency . '% of the allocated time.',
            ];
        } elseif ($timeEfficiency > 90) {
            $insights[] = [
                'type' => 'speed',
                'icon' => '⏰',
                'title' => 'Time Pressure',
                'message' => 'You used ' . $timeEfficiency . '% of the available time. Consider practicing to improve speed.',
            ];
        }
        
        // Consistency insight
        $consistency = $statistics['performance_metrics']['consistency'];
        if ($consistency >= 80) {
            $insights[] = [
                'type' => 'consistency',
                'icon' => '🎯',
                'title' => 'Consistent Performance',
                'message' => 'You showed consistent understanding across different question types.',
            ];
        }
        
        // Question type insights
        foreach ($statistics['type_breakdown'] as $typeData) {
            if ($typeData['accuracy'] < 50 && $typeData['total'] >= 3) {
                $insights[] = [
                    'type' => 'weakness',
                    'icon' => '📖',
                    'title' => 'Area for Improvement',
                    'message' => 'Focus on ' . $typeData['type'] . ' questions. You got ' . $typeData['correct'] . ' out of ' . $typeData['total'] . ' correct.',
                ];
            } elseif ($typeData['accuracy'] >= 90 && $typeData['total'] >= 3) {
                $insights[] = [
                    'type' => 'strength',
                    'icon' => '💪',
                    'title' => 'Strong Area',
                    'message' => 'You excel at ' . $typeData['type'] . ' questions with ' . $typeData['accuracy'] . '% accuracy!',
                ];
            }
        }
        
        return $insights;
    }
    
    /**
     * Generate personalized recommendations
     */
    protected function generateRecommendations($statistics, $results): array
    {
        $recommendations = [];
        
        if (!$results['passed']) {
            $recommendations[] = [
                'priority' => 'high',
                'icon' => '🎯',
                'title' => 'Retake the Exam',
                'description' => 'Review the incorrect answers and explanations below, then attempt the exam again.',
                'action' => 'Retake Now',
            ];
            
            $recommendations[] = [
                'priority' => 'high',
                'icon' => '📚',
                'title' => 'Review Study Materials',
                'description' => 'Go through the course materials related to the questions you missed.',
                'action' => 'View Course',
            ];
        } else {
            if ($results['percentage'] < 80) {
                $recommendations[] = [
                    'priority' => 'medium',
                    'icon' => '📈',
                    'title' => 'Aim for Excellence',
                    'description' => 'You passed! Consider retaking to achieve a higher score and better understanding.',
                    'action' => 'Improve Score',
                ];
            }
        }
        
        // Time management recommendation
        if ($statistics['performance_metrics']['speed'] > 90) {
            $recommendations[] = [
                'priority' => 'medium',
                'icon' => '⏱️',
                'title' => 'Practice Time Management',
                'description' => 'Practice answering similar questions to improve your speed and confidence.',
                'action' => 'Practice Mode',
            ];
        }
        
        // Weak areas recommendation
        foreach ($statistics['type_breakdown'] as $typeData) {
            if ($typeData['accuracy'] < 60 && $typeData['total'] >= 3) {
                $recommendations[] = [
                    'priority' => 'high',
                    'icon' => '🔍',
                    'title' => 'Focus on ' . $typeData['type'],
                    'description' => 'Your accuracy for ' . $typeData['type'] . ' questions is ' . $typeData['accuracy'] . '%. Review these question types.',
                    'action' => 'Study Resources',
                ];
            }
        }
        
        // General recommendations
        $recommendations[] = [
            'priority' => 'low',
            'icon' => '💡',
            'title' => 'Review All Explanations',
            'description' => 'Even for correct answers, reading explanations can deepen your understanding.',
            'action' => 'View Details',
        ];
        
        return $recommendations;
    }
    
    /**
     * Format question details for email
     */
    protected function formatQuestionDetails($studentAnswers, $unansweredQuestions): array
    {
        $formatted = [
            'correct' => [],
            'incorrect' => [],
            'unanswered' => [],
        ];
        
        // Process answered questions
        foreach ($studentAnswers as $answer) {
            $question = $answer->question;
            if (!$question) continue;
            
            $questionData = [
                'number' => $answer->id,
                'text' => strip_tags($question->question_text),
                'type' => ucfirst(str_replace('_', ' ', $question->question_type)),
                'points' => $question->points,
                'points_earned' => $answer->points_earned ?? 0,
                'user_answer' => $this->formatUserAnswer($answer, $question),
                'correct_answer' => $this->formatCorrectAnswer($question),
                'explanation' => $question->explanation ?? 'No explanation provided.',
                'is_correct' => $answer->is_correct,
            ];
            
            if ($answer->is_correct) {
                $formatted['correct'][] = $questionData;
            } else {
                $formatted['incorrect'][] = $questionData;
            }
        }
        
        // Process unanswered questions
        foreach ($unansweredQuestions as $question) {
            $formatted['unanswered'][] = [
                'number' => $question->id,
                'text' => strip_tags($question->question_text),
                'type' => ucfirst(str_replace('_', ' ', $question->question_type)),
                'points' => $question->points,
                'correct_answer' => $this->formatCorrectAnswer($question),
                'explanation' => $question->explanation ?? 'No explanation provided.',
            ];
        }
        
        return $formatted;
    }
    
    /**
     * Format user's answer
     */
    protected function formatUserAnswer($answer, $question): string
    {
        if ($answer->answer === null || $answer->answer === '') {
            return 'Not answered';
        }
        
        switch ($question->question_type) {
            case 'multiple_choice':
                $options = $question->options ?? [];
                $index = (int)$answer->answer;
                if (isset($options[$index])) {
                    return chr(65 + $index) . '. ' . strip_tags($options[$index]);
                }
                return 'Option ' . chr(65 + $index);
                
            case 'true_false':
                return $answer->answer == 0 ? 'True' : 'False';
                
            default:
                return strip_tags($answer->answer);
        }
    }
    
    /**
     * Format correct answer
     */
    protected function formatCorrectAnswer($question): string
    {
        $correctAnswers = $question->correct_answers ?? [];
        
        switch ($question->question_type) {
            case 'multiple_choice':
                $options = $question->options ?? [];
                $formatted = [];
                foreach ($correctAnswers as $index) {
                    if (isset($options[$index])) {
                        $formatted[] = chr(65 + $index) . '. ' . strip_tags($options[$index]);
                    }
                }
                return implode(', ', $formatted);
                
            case 'true_false':
                return isset($correctAnswers[0]) && $correctAnswers[0] == 0 ? 'True' : 'False';
                
            default:
                return implode(', ', $correctAnswers);
        }
    }
    
    /**
     * Generate certificate data
     */
    protected function generateCertificateData($student, $assessment, $results, $attemptNumber): array
    {
        return [
            'id' => strtoupper(uniqid('CERT-')),
            'student_name' => $student->name,
            'assessment_title' => $assessment->title,
            'score' => $results['percentage'],
            'date' => Carbon::now()->format('F j, Y'),
            'attempt_number' => $attemptNumber,
        ];
    }
    
    /**
     * Calculate speed metric (percentage of time used)
     */
    protected function calculateSpeedMetric($timeSpent, $totalTime): int
    {
        return $totalTime > 0 ? round(($timeSpent / $totalTime) * 100) : 0;
    }
    
    /**
     * Calculate completion metric
     */
    protected function calculateCompletionMetric($correct, $total): int
    {
        return $total > 0 ? round(($correct / $total) * 100) : 0;
    }
    
    /**
     * Calculate consistency metric
     */
    protected function calculateConsistencyMetric($studentAnswers): int
    {
        // Calculate standard deviation of performance
        $scores = $studentAnswers->map(function ($answer) {
            return $answer->is_correct ? 100 : 0;
        })->toArray();
        
        if (count($scores) < 2) return 100;
        
        $mean = array_sum($scores) / count($scores);
        $variance = 0;
        
        foreach ($scores as $score) {
            $variance += pow($score - $mean, 2);
        }
        
        $variance /= count($scores);
        $stdDev = sqrt($variance);
        
        // Convert to consistency score (lower deviation = higher consistency)
        $consistency = max(0, 100 - $stdDev);
        
        return round($consistency);
    }
    
    /**
     * Format time in human-readable format
     */
    protected function formatTime($seconds): string
    {
        $seconds = abs($seconds);
        
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;
        
        if ($hours > 0) {
            return sprintf('%d hour%s %d minute%s', 
                $hours, $hours > 1 ? 's' : '',
                $minutes, $minutes != 1 ? 's' : ''
            );
        } elseif ($minutes > 0) {
            return sprintf('%d minute%s %d second%s', 
                $minutes, $minutes != 1 ? 's' : '',
                $secs, $secs != 1 ? 's' : ''
            );
        } else {
            return sprintf('%d second%s', $secs, $secs != 1 ? 's' : '');
        }
    }
}