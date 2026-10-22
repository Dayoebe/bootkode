<?php

namespace App\Services;

use App\Models\Core\User;
use App\Models\Learning\Course;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LearningAnalyticsService
{
    public function snapshot(User $user, string $range = 'month', $category = 'all'): array
    {
        validator(compact('range', 'category'), ['range' => 'in:day,week,month,quarter,year', 'category' => ['required', 'regex:/^(all|[1-9][0-9]*)$/']])->validate();
        $days = ['day' => 1, 'week' => 7, 'month' => 30, 'quarter' => 90, 'year' => 365][$range];
        $start = today()->subDays($days - 1);
        $end = now();
        $legacy = DB::table('course_user')->where('user_id', $user->id)->get()->keyBy('course_id');
        $enrollments = $user->enrollments()->get()->keyBy('course_id');
        $ids = $legacy->keys()->merge($enrollments->keys())->unique();
        $courses = Course::whereIn('id', $ids)->when($category !== 'all', fn ($query) => $query->where('category_id', $category))->with('sections.lessons:id,section_id')->get();
        $courseIds = $courses->pluck('id');
        $legacyLessons = DB::table('lesson_user')->join('lessons', 'lessons.id', '=', 'lesson_user.lesson_id')->join('sections', 'sections.id', '=', 'lessons.section_id')
            ->where('lesson_user.user_id', $user->id)->whereIn('sections.course_id', $courseIds)->whereNotNull('lesson_user.completed_at')
            ->get(['lesson_user.lesson_id', 'lesson_user.completed_at', 'lesson_user.time_spent_minutes', 'sections.course_id']);
        $progress = DB::table('user_progress')->where('user_id', $user->id)->whereIn('course_id', $courseIds)->where('is_completed', true)->whereNotNull('completed_at')->whereNotNull('lesson_id')->get();
        $completed = $legacyLessons->map(fn ($entry) => ['lesson_id' => $entry->lesson_id, 'course_id' => $entry->course_id, 'completed_at' => $entry->completed_at])
            ->concat($progress->map(fn ($entry) => ['lesson_id' => $entry->lesson_id, 'course_id' => $entry->course_id, 'completed_at' => $entry->completed_at]))->sortBy('completed_at')->unique('lesson_id')->values();
        $recent = $completed->filter(fn ($entry) => Carbon::parse($entry['completed_at'])->betweenIncluded($start, $end));
        $sessions = DB::table('learning_sessions')->where('user_id', $user->id)->whereIn('course_id', $courseIds)->whereNotNull('ended_at')->whereBetween('started_at', [$start, $end])->get();
        $scores = $this->assessmentScores($user, $courseIds->all(), $start, $end);
        $rows = $courses->map(function ($course) use ($legacy, $enrollments, $completed) {
            $primary = $enrollments->get($course->id);
            $historical = $legacy->get($course->id);
            $done = $completed->where('course_id', $course->id)->count();
            $percent = $primary ? (float) $primary->progress_percentage : (float) ($historical?->progress ?? 0);
            $isComplete = (bool) ($primary?->is_completed || $primary?->completed_at || $historical?->completed_at || $percent >= 100);
            return ['id' => $course->id, 'title' => $course->title, 'slug' => $course->slug, 'lessons' => $course->sections->sum(fn ($section) => $section->lessons->count()), 'completed_lessons' => $done, 'progress' => max(0, min(100, $percent)), 'is_completed' => $isComplete];
        })->values()->all();
        $dailyLessons = $recent->groupBy(fn ($entry) => Carbon::parse($entry['completed_at'])->format('Y-m-d'))->map->count();
        $dailyMinutes = $sessions->groupBy(fn ($entry) => Carbon::parse($entry->started_at)->format('Y-m-d'))->map(fn ($entries) => max(0, (int) $entries->sum('duration_minutes')));
        $activity = [];
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $key = $date->format('Y-m-d');
            $activity[] = ['date' => $key, 'lessons' => $dailyLessons->get($key, 0), 'minutes' => $dailyMinutes->get($key, 0)];
        }
        return [
            'start' => $start->format('Y-m-d'), 'end' => $end->format('Y-m-d'), 'courses' => $rows, 'activity' => $activity,
            'totalCourses' => count($rows), 'completedCourses' => collect($rows)->where('is_completed', true)->count(),
            'totalLessons' => $completed->count(), 'recentLessons' => $recent->count(),
            'sessionMinutes' => max(0, (int) $sessions->sum('duration_minutes')),
            'recordedLessonMinutes' => max(0, (int) $legacyLessons->filter(fn ($entry) => Carbon::parse($entry->completed_at)->betweenIncluded($start, $end))->sum('time_spent_minutes')),
            'averageScore' => $scores->isEmpty() ? null : round($scores->avg('percentage'), 1), 'assessmentAttempts' => $scores->count(),
            'achievements' => $user->badges()->latest()->get(['badge_name', 'badge_description', 'created_at'])->toArray(),
            'goals' => DB::table('learning_goals')->where('user_id', $user->id)->where('is_active', true)->where(fn ($query) => $query->whereNull('end_date')->orWhereDate('end_date', '>=', today()))->orderByDesc('id')->get(),
        ];
    }

    public function assessmentScores(User $user, array $courseIds, Carbon $start, Carbon $end)
    {
        $assessments = \App\Models\Assessment\Assessment::whereIn('course_id', $courseIds)->with('questions')->get();
        $answers = DB::table('student_answers')->where('user_id', $user->id)->whereIn('assessment_id', $assessments->pluck('id'))->whereNotNull('submitted_at')->whereBetween('submitted_at', [$start, $end])->get();
        $scores = collect();
        $recorded = DB::table('learning_sessions')->where('user_id', $user->id)->where('activity_type', 'assessment')->whereIn('course_id', $courseIds)
            ->whereNotNull('ended_at')->whereBetween('ended_at', [$start, $end])->get();
        foreach ($recorded as $session) {
            $metadata = json_decode($session->metadata ?? '{}', true);
            if (!isset($metadata['assessment_id'], $metadata['results']['percentage'])) continue;
            $scores->push(['assessment_id' => (int) $metadata['assessment_id'], 'attempt' => (int) $metadata['results']['attempt_number'], 'percentage' => max(0, min(100, (float) $metadata['results']['percentage']))]);
        }
        foreach ($assessments as $assessment) {
            foreach ($answers->where('assessment_id', $assessment->id)->groupBy('attempt_number') as $attempt => $group) {
                if ($scores->contains(fn ($entry) => $entry['assessment_id'] === (int) $assessment->id && $entry['attempt'] === (int) $attempt)) continue;
                $byQuestion = $group->sortByDesc('id')->unique('question_id')->keyBy('question_id');
                $possible = $earned = 0;
                $complete = $assessment->questions->isNotEmpty();
                foreach ($assessment->questions as $question) {
                    $answer = $byQuestion->get($question->id);
                    if (!$answer || (!in_array($question->question_type, ['multiple_choice', 'true_false'], true) && !$answer->graded_at)) { $complete = false; break; }
                    $possible += max(0, (float) $question->points);
                    $earned += min(max(0, (float) $answer->points_earned), max(0, (float) $question->points));
                }
                if ($complete && $possible > 0) $scores->push(['assessment_id' => $assessment->id, 'attempt' => $attempt, 'percentage' => round($earned / $possible * 100, 1)]);
            }
        }
        return $scores;
    }
}
