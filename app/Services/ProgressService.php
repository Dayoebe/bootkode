<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Section;
use App\Models\Lesson;
use App\Models\Assessment;
use App\Models\UserProgress;
use Illuminate\Support\Facades\Auth;

class ProgressService
{
    /**
     * Mark a lesson as completed.
     */
    public function completeLesson(Course $course, Lesson $lesson)
    {
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

        $this->checkAndUnlockNextSection($course, $lesson->section);
    }

    /**
     * Mark an assessment as completed.
     */
    public function completeAssessment(Course $course, Assessment $assessment, bool $passed)
    {
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

        if ($passed) {
            $this->checkAndUnlockNextSection($course, $assessment->section);
        }
    }

    /**
     * Check if the section is completed and unlock the next section.
     */
    protected function checkAndUnlockNextSection(Course $course, Section $currentSection)
    {
        $sectionLessons = $currentSection->lessons()->pluck('id')->toArray();
        $sectionAssessments = $currentSection->assessments()->pluck('id')->toArray();
        $completedLessons = UserProgress::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->whereIn('lesson_id', $sectionLessons)
            ->where('is_completed', true)
            ->count();
        $completedAssessments = UserProgress::where('user_id', Auth::id())
            ->where('course_id', $course->id)
            ->whereIn('assessment_id', $sectionAssessments)
            ->where('is_completed', true)
            ->count();

        if ($completedLessons === count($sectionLessons) && $completedAssessments === count($sectionAssessments)) {
            $nextSection = $course->sections()->where('order', '>', $currentSection->order)->first();
            if ($nextSection) {
                $nextSection->update(['is_locked' => false]);
            }
        }
    }

    /**
     * Check if a section is accessible to the user.
     */
    public function canAccessSection(Course $course, Section $section): bool
    {
        if (!$section->is_locked) {
            return true;
        }

        $previousSection = $course->sections()->where('order', '<', $section->order)->orderBy('order', 'desc')->first();
        if (!$previousSection) {
            return true; // First section is always accessible
        }

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

        return $completedLessons === count($previousLessons) && $completedAssessments === count($previousAssessments);
    }
}