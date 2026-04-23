<?php

namespace App\Services;

use App\Models\Learning\Course;
use App\Models\Learning\CourseReview;
use App\Models\Learning\ReviewAnalytics;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReviewAnalyticsService
{
    /**
     * Generate analytics for a specific course and date
     */
    public function generateDailyAnalytics(Course $course, Carbon $date = null)
    {
        $date = $date ?? now();
        
        $reviews = $course->reviews()
            ->where('is_approved', true)
            ->whereDate('created_at', '<=', $date)
            ->get();

        if ($reviews->isEmpty()) {
            return null;
        }

        $analytics = ReviewAnalytics::updateOrCreate(
            [
                'course_id' => $course->id,
                'date' => $date->toDateString()
            ],
            [
                'average_rating' => $reviews->avg('rating'),
                'review_count' => $reviews->count(),
                'response_count' => $reviews->whereNotNull('instructor_reply')->count(),
                'response_rate' => $this->calculateResponseRate($reviews),
                'sentiment_score' => $this->analyzeSentiment($reviews),
                'keyword_frequencies' => $this->extractKeywords($reviews)
            ]
        );

        return $analytics;
    }

    /**
     * Calculate instructor response rate
     */
    private function calculateResponseRate($reviews)
    {
        if ($reviews->isEmpty()) {
            return 0;
        }

        $totalReviews = $reviews->count();
        $repliedReviews = $reviews->whereNotNull('instructor_reply')->count();

        return round(($repliedReviews / $totalReviews) * 100, 2);
    }

    /**
     * Simple sentiment analysis
     */
    private function analyzeSentiment($reviews)
    {
        $positiveWords = ['great', 'excellent', 'amazing', 'wonderful', 'fantastic', 'love', 'best', 'perfect', 'awesome', 'brilliant'];
        $negativeWords = ['bad', 'terrible', 'awful', 'poor', 'worst', 'hate', 'disappointing', 'useless', 'waste', 'boring'];

        $totalScore = 0;
        $count = 0;

        foreach ($reviews as $review) {
            $text = strtolower($review->comment ?? '');
            $positiveCount = 0;
            $negativeCount = 0;

            foreach ($positiveWords as $word) {
                $positiveCount += substr_count($text, $word);
            }

            foreach ($negativeWords as $word) {
                $negativeCount += substr_count($text, $word);
            }

            // Calculate sentiment: -1 (negative) to 1 (positive)
            if ($positiveCount + $negativeCount > 0) {
                $sentiment = ($positiveCount - $negativeCount) / ($positiveCount + $negativeCount);
                $totalScore += $sentiment;
                $count++;
            }
        }

        return $count > 0 ? round($totalScore / $count, 2) : 0;
    }

    /**
     * Extract common keywords from reviews
     */
    private function extractKeywords($reviews)
    {
        $stopWords = ['the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'as', 'by', 'is', 'was', 'are', 'were', 'this', 'that', 'with', 'from', 'it', 'be', 'been', 'has', 'have', 'had'];
        
        $allWords = [];

        foreach ($reviews as $review) {
            $text = strtolower($review->comment ?? '');
            $text = preg_replace('/[^a-z\s]/', '', $text);
            $words = explode(' ', $text);

            foreach ($words as $word) {
                $word = trim($word);
                if (strlen($word) > 3 && !in_array($word, $stopWords)) {
                    if (!isset($allWords[$word])) {
                        $allWords[$word] = 0;
                    }
                    $allWords[$word]++;
                }
            }
        }

        arsort($allWords);
        return array_slice($allWords, 0, 20); // Top 20 keywords
    }

    /**
     * Get rating trends over time
     */
    public function getRatingTrends(Course $course, int $days = 30)
    {
        return ReviewAnalytics::where('course_id', $course->id)
            ->where('date', '>=', now()->subDays($days))
            ->orderBy('date')
            ->get()
            ->map(function ($analytics) {
                return [
                    'date' => $analytics->date->format('M d'),
                    'rating' => $analytics->average_rating,
                    'count' => $analytics->review_count
                ];
            });
    }

    /**
     * Get instructor performance metrics
     */
    public function getInstructorMetrics(Course $course)
    {
        $reviews = $course->reviews()->where('is_approved', true)->get();
        $repliedReviews = $reviews->whereNotNull('instructor_reply');

        $avgResponseTime = null;
        if ($repliedReviews->isNotEmpty()) {
            $responseTimes = $repliedReviews->map(function ($review) {
                return $review->replied_at->diffInHours($review->created_at);
            });
            $avgResponseTime = round($responseTimes->avg(), 1);
        }

        return [
            'total_reviews' => $reviews->count(),
            'replied_reviews' => $repliedReviews->count(),
            'response_rate' => $this->calculateResponseRate($reviews),
            'avg_response_time_hours' => $avgResponseTime,
            'avg_rating' => round($reviews->avg('rating'), 2),
            'rating_distribution' => $course->getRatingDistribution()
        ];
    }

    /**
     * Get student satisfaction trends
     */
    public function getSatisfactionMetrics(Course $course, int $days = 90)
    {
        $analytics = ReviewAnalytics::where('course_id', $course->id)
            ->where('date', '>=', now()->subDays($days))
            ->get();

        return [
            'current_satisfaction' => $analytics->last()?->average_rating ?? 0,
            'trend' => $this->calculateTrend($analytics),
            'sentiment_score' => $analytics->avg('sentiment_score'),
            'top_keywords' => $this->getMergedKeywords($analytics),
        ];
    }

    /**
     * Calculate trend (increasing, decreasing, stable)
     */
    private function calculateTrend($analytics)
    {
        if ($analytics->count() < 2) {
            return 'stable';
        }

        $first = $analytics->take(ceil($analytics->count() / 2))->avg('average_rating');
        $second = $analytics->skip(ceil($analytics->count() / 2))->avg('average_rating');

        $difference = $second - $first;

        if ($difference > 0.3) {
            return 'increasing';
        } elseif ($difference < -0.3) {
            return 'decreasing';
        }

        return 'stable';
    }

    /**
     * Merge and rank keywords from multiple analytics records
     */
    private function getMergedKeywords($analytics)
    {
        $mergedKeywords = [];

        foreach ($analytics as $record) {
            $keywords = $record->keyword_frequencies ?? [];
            foreach ($keywords as $word => $frequency) {
                if (!isset($mergedKeywords[$word])) {
                    $mergedKeywords[$word] = 0;
                }
                $mergedKeywords[$word] += $frequency;
            }
        }

        arsort($mergedKeywords);
        return array_slice($mergedKeywords, 0, 10);
    }
}
