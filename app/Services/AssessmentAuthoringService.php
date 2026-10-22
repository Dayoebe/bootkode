<?php

namespace App\Services;

use App\Models\Assessment\Assessment;
use App\Models\Assessment\Question;
use App\Models\Learning\Lesson;
use Illuminate\Support\Facades\DB;

class AssessmentAuthoringService
{
    private function permutation(array $ids, array $actual): void
    {
        validator(['ids' => $ids], ['ids' => 'present|array', 'ids.*' => 'required|integer|distinct'])->validate();
        abort_unless(array_is_list($ids) && count($ids) === count($actual) && !array_diff($actual, $ids), 422, 'Reorder the complete list from this assessment or lesson.');
    }

    public function reorderQuestions(int $assessmentId, array $ids): void
    {
        $assessment = app(CourseAuthoringAccess::class)->assessment($assessmentId);
        DB::transaction(function () use ($assessment, $ids) {
            Assessment::whereKey($assessment->id)->lockForUpdate()->firstOrFail();
            $this->permutation($ids, $assessment->questions()->pluck('id')->all());
            foreach ($ids as $index => $id) $assessment->questions()->whereKey($id)->update(['order' => $index + 1]);
        });
    }

    public function reorderAssessments(int $lessonId, array $ids): void
    {
        $lesson = app(CourseAuthoringAccess::class)->lesson($lessonId);
        DB::transaction(function () use ($lesson, $ids) {
            Lesson::whereKey($lesson->id)->lockForUpdate()->firstOrFail();
            $this->permutation($ids, $lesson->assessments()->pluck('id')->all());
            foreach ($ids as $index => $id) $lesson->assessments()->whereKey($id)->update(['order' => $index + 1]);
        });
    }

    public function duplicateAssessment(int $lessonId, int $assessmentId): Assessment
    {
        $lesson = app(CourseAuthoringAccess::class)->lesson($lessonId);
        return DB::transaction(function () use ($lesson, $assessmentId) {
            Lesson::whereKey($lesson->id)->lockForUpdate()->firstOrFail();
            $original = $lesson->assessments()->with('questions')->findOrFail($assessmentId);
            $copy = $original->replicate();
            $copy->title .= ' (Copy)'; $copy->order = (int) $lesson->assessments()->max('order') + 1;
            $copy->course_id = $lesson->section->course_id; $copy->section_id = $lesson->section_id;
            $copy->save();
            foreach ($original->questions as $question) {
                $item = $question->replicate(); $item->assessment_id = $copy->id; $item->save();
            }
            return $copy;
        });
    }

    public function duplicateQuestion(int $assessmentId, int $questionId): Question
    {
        $assessment = app(CourseAuthoringAccess::class)->assessment($assessmentId);
        return DB::transaction(function () use ($assessment, $questionId) {
            Assessment::whereKey($assessment->id)->lockForUpdate()->firstOrFail();
            $original = $assessment->questions()->findOrFail($questionId);
            $copy = $original->replicate(); $copy->question_text .= ' (Copy)';
            $copy->order = (int) $assessment->questions()->max('order') + 1; $copy->save();
            return $copy;
        });
    }

    public function moveQuestion(int $assessmentId, int $questionId, int $direction): void
    {
        abort_unless(in_array($direction, [-1, 1], true), 422);
        $assessment = app(CourseAuthoringAccess::class)->assessment($assessmentId);
        DB::transaction(function () use ($assessment, $questionId, $direction) {
            Assessment::whereKey($assessment->id)->lockForUpdate()->firstOrFail();
            $ids = $assessment->questions()->orderBy('order')->orderBy('id')->pluck('id')->all();
            $position = array_search($questionId, $ids, true);
            abort_if($position === false, 404);
            if (!isset($ids[$position + $direction])) return;
            [$ids[$position], $ids[$position + $direction]] = [$ids[$position + $direction], $ids[$position]];
            $this->reorderQuestions($assessment->id, $ids);
        });
    }

    public function saveCriteria(int $assessmentId, string $type, array $attributes, ?int $questionId = null): Question
    {
        $assessment = app(CourseAuthoringAccess::class)->assessment($assessmentId);
        abort_unless(($type === 'project_criteria' && $assessment->type === 'project') || ($type === 'assignment_question' && $assessment->type === 'assignment'), 422);
        return DB::transaction(function () use ($assessment, $type, $attributes, $questionId) {
            Assessment::whereKey($assessment->id)->lockForUpdate()->firstOrFail();
            $question = $questionId ? $assessment->questions()->where('question_type', $type)->findOrFail($questionId) : new Question;
            $question->fill(\Illuminate\Support\Arr::only($attributes, ['question_text', 'points', 'explanation', 'is_required', 'time_limit', 'options']));
            $question->assessment_id = $assessment->id; $question->question_type = $type;
            if (!$question->exists) $question->order = (int) $assessment->questions()->max('order') + 1;
            app(AssessmentSubmissionService::class)->definition($question);
            $question->save();
            return $question;
        });
    }
}
