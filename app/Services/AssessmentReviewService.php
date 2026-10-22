<?php

namespace App\Services;

use App\Models\Assessment\Assessment;
use App\Models\Assessment\LearningSession;
use App\Models\Assessment\StudentAnswer;
use App\Models\Core\User;
use Illuminate\Support\Facades\DB;

class AssessmentReviewService
{
    public function authorize(User $reviewer, Assessment $assessment): void
    {
        $assessment->refresh();
        $lessonCourse = $assessment->lesson?->section?->course_id ?? $assessment->section?->course_id;
        abort_if($lessonCourse && $assessment->course_id && (int) $lessonCourse !== (int) $assessment->course_id, 403);
        abort_unless($reviewer->hasAnyRole(['super_admin', 'academy_admin', 'instructor']), 403);
        $courseId = $assessment->course_id ?: $assessment->lesson?->section?->course_id ?? $assessment->section?->course_id;
        if ($reviewer->hasAnyRole(['super_admin', 'academy_admin'])) return;
        abort_unless($courseId && (int) \App\Models\Learning\Course::find($courseId)?->instructor_id === (int) $reviewer->id, 403);
    }

    public function grade(User $reviewer, int $sessionId, int $answerId, $points, string $feedback = ''): array
    {
        validator(compact('points', 'feedback'), ['points' => 'required|numeric|min:0', 'feedback' => 'string|max:4000'])->validate();
        $entry = LearningSession::where('activity_type', 'assessment')->findOrFail($sessionId);
        $assessment = Assessment::findOrFail($entry->metadata['assessment_id'] ?? 0);
        $this->authorize($reviewer, $assessment);
        abort_if((int) $reviewer->id === (int) $entry->user_id, 403, 'A reviewer cannot grade their own submission.');
        return DB::transaction(function () use ($reviewer, $entry, $assessment, $answerId, $points, $feedback) {
            $learner = User::whereKey($entry->user_id)->lockForUpdate()->firstOrFail();
            $session = LearningSession::whereKey($entry->id)->lockForUpdate()->firstOrFail();
            $metadata = $session->metadata;
            abort_unless($session->ended_at && isset($metadata['results']), 422, 'The attempt must be submitted first.');
            $answer = StudentAnswer::where('user_id', $learner->id)->where('assessment_id', $assessment->id)->where('attempt_number', $metadata['attempt'])->lockForUpdate()->findOrFail($answerId);
            $snapshot = $metadata['grading'][$answer->question_id] ?? null;
            abort_unless($snapshot && !in_array($snapshot['question_type'], ['multiple_choice', 'true_false', ...StructuredQuestionService::TYPES], true), 422, 'This answer does not require manual grading.');
            validator(['points' => $points], ['points' => 'numeric|between:0,'.(float) $snapshot['points']])->validate();
            if ($answer->graded_at) {
                abort_unless((float) $answer->points_earned === (float) $points && ($answer->feedback ?? '') === $feedback, 409, 'This mark is already recorded. A formal correction is required.');
                return $metadata['results'];
            }
            abort_unless($metadata['results']['pending_review'] ?? false, 409, 'This result is already finalized.');
            $answer->update(['points_earned' => $points, 'is_correct' => (float) $points >= (float) $snapshot['points'], 'graded_by' => $reviewer->id, 'graded_at' => now(), 'feedback' => $feedback]);
            $metadata['review_log'][] = ['answer_id' => $answer->id, 'reviewer_id' => $reviewer->id, 'points' => (float) $points, 'at' => now()->toISOString()];
            $answers = StudentAnswer::where('user_id', $learner->id)->where('assessment_id', $assessment->id)->where('attempt_number', $metadata['attempt'])->get()->keyBy('question_id');
            $pending = false; $earned = $possible = $correct = 0;
            foreach ($metadata['questions'] as $question) {
                $record = $answers->get($question['id']);
                if (!$record || !$record->graded_at) $pending = true;
                $earned += (float) ($record?->points_earned ?? 0);
                $possible += (float) $metadata['grading'][$question['id']]['points'];
                if ($record?->is_correct) $correct++;
            }
            $results = $metadata['results'];
            $results['pending_review'] = $pending;
            if (!$pending) {
                $results['total_points'] = $earned;
                $results['percentage'] = $possible > 0 ? round($earned / $possible * 100, 1) : 0;
                $results['passed'] = $results['percentage'] >= (float) ($metadata['pass_percentage'] ?? $assessment->pass_percentage);
                $results['correct_answers'] = $correct;
                $results['graded_at'] = now()->toISOString();
            }
            $metadata['results'] = $results;
            $session->update(['metadata' => $metadata]);
            if (!$pending) app(GamificationService::class)->handleQuizCompletion($learner, $assessment);
            return $results;
        });
    }
}
