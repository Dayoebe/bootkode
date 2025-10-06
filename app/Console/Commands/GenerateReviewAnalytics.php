<?php

namespace App\Console\Commands;

use App\Models\Course;
use App\Services\ReviewAnalyticsService;
use Illuminate\Console\Command;

class GenerateReviewAnalytics extends Command
{
    protected $signature = 'reviews:generate-analytics {--course_id=}';
    protected $description = 'Generate review analytics for courses';

    public function handle(ReviewAnalyticsService $analyticsService)
    {
        $courseId = $this->option('course_id');

        if ($courseId) {
            $course = Course::findOrFail($courseId);
            $analyticsService->generateDailyAnalytics($course);
            $this->info("Analytics generated for course: {$course->title}");
        } else {
            $courses = Course::whereHas('reviews')->get();
            
            foreach ($courses as $course) {
                $analyticsService->generateDailyAnalytics($course);
                $this->info("Analytics generated for: {$course->title}");
            }
        }

        $this->info('Analytics generation complete!');
    }
}