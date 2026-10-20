<?php

namespace App\Services;

use App\Models\Assessment\Assessment;
use App\Models\Assessment\UserProgress;
use App\Models\Learning\Course;
use App\Models\Learning\Lesson;
use App\Models\Learning\Section;
use Illuminate\Support\Facades\Auth;

class ProgressService
{
    /**
     * Mark a lesson as completed.
     */
    public function completeLesson(Course $course, Lesson $lesson)
    {
        $this->authorizeProgress($course, $lesson->section);
        UserProgress::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'course_id' => $course->id,
                'lesson_id' => $lesson->id,
            ],
            [
                'is_completed' => true,
                'completed_at' => now(),
            ]
        );

    }

    /**
     * Mark an assessment as completed.
     */
    public function completeAssessment(Course $course, Assessment $assessment, bool $passed)
    {
        $this->authorizeProgress($course, $assessment->section);
        UserProgress::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'course_id' => $course->id,
                'assessment_id' => $assessment->id,
            ],
            [
                'is_completed' => $passed,
                'completed_at' => $passed ? now() : null,
            ]
        );

    }

    private function authorizeProgress(Course $course, ?Section $section): void
    {
        abort_unless(Auth::check() && $section && $section->course_id === $course->id, 403);
        abort_unless(Auth::user()->enrollments()->where('course_id', $course->id)->exists()
            || Auth::user()->courses()->where('courses.id', $course->id)->exists(), 403);
        abort_unless($this->canAccessSection($course, $section), 403);
    }

    /**
     * Check if a section is accessible to the user.
     */
    public function canAccessSection(Course $course, Section $section): bool
    {
        if (! Auth::check() || $section->course_id !== $course->id) {
            return false;
        }
        if (! $section->is_locked) {
            return true;
        }

        $previousSections = $course->sections()->where('order', '<', $section->order)->orderBy('order')->get();
        foreach ($previousSections as $previousSection) {

            $previousLessons = $previousSection->lessons()->pluck('id')->toArray();
            $previousAssessments = $previousSection->assessments()->pluck('id')->toArray();
            $completedLessons = UserProgress::where('user_id', Auth::id())
                ->where('course_id', $course->id)
                ->whereIn('lesson_id', $previousLessons)
                ->where('is_completed', true)
                ->count();
            $completedAssessments = UserProgress::where('user_id', Auth::id())
                ->where('course_id', $course->id)
                ->whereIn('assessment_id', $previousAssessments)
                ->where('is_completed', true)
                ->count();

            if ($completedLessons !== count($previousLessons) || $completedAssessments !== count($previousAssessments)) {
                return false;
            }
        }

        return true;
    }
}
