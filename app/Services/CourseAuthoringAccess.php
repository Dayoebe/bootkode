<?php

namespace App\Services;

use App\Models\Learning\Course;
use App\Models\Learning\Lesson;
use App\Models\Assessment\Assessment;

class CourseAuthoringAccess
{
    public function authorizeRole(): void
    {
        abort_unless(auth()->user()?->hasAnyRole(['super_admin', 'academy_admin', 'instructor']), 403);
    }

    public function course($id): Course
    {
        $this->authorizeRole();
        $course = Course::findOrFail($id);
        abort_unless(auth()->user()->hasAnyRole(['super_admin', 'academy_admin']) || (int) $course->instructor_id === (int) auth()->id(), 403);
        return $course;
    }

    public function lesson($id): Lesson
    {
        $lesson = Lesson::with('section')->findOrFail($id);
        $this->course($lesson->section->course_id);
        return $lesson;
    }

    public function assessment($id): Assessment
    {
        $assessment = Assessment::findOrFail($id);
        $this->course($assessment->course_id ?: $assessment->lesson->section->course_id);
        return $assessment;
    }
}
