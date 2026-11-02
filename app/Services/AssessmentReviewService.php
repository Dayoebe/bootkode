<?php

namespace App\Services;

use App\Models\Assessment\Assessment;
use App\Models\Assessment\LearningSession;
use App\Models\Assessment\StudentAnswer;
use App\Models\Core\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AssessmentReviewService
{
    private const AUTOMATIC_TYPES = ['multiple_choice', 'true_false', ...StructuredQuestionService::TYPES];

    public function authorize(User $reviewer, Assessment $assessment): void
    {
        $reviewer->refresh()->unsetRelation('roles');
        abort_unless($reviewer->is_active && $reviewer->email_verified_at, 403);
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
        validator(compact('points', 'feedback'), ['points' => 'required|numeric|decimal:0,2|min:0', 'feedback' => 'string|max:4000'])->validate();
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
            abort_unless($snapshot && !in_array($snapshot['question_type'], self::AUTOMATIC_TYPES, true), 422, 'This answer does not require manual grading.');
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
            if (!$pending) {
                app(GamificationService::class)->handleQuizCompletion($learner, $assessment);
                app(LearningNotificationService::class)->result($session, 'finalized');
            }
            return $results;
        });
    }

    /** A server-issued optimistic version includes the actual marks, not only the result total. */
    public function revision(LearningSession $session): string
    {
        $metadata = $session->metadata;
        $marks = $this->answers($session)->orderBy('id')->get(['id', 'points_earned', 'is_correct', 'graded_by', 'graded_at', 'feedback'])->toArray();

        return hash('sha256', json_encode([$metadata['results'] ?? [], $metadata['review_log'] ?? [],
            $metadata['mark_corrections'] ?? [], $metadata['mark_reviews'] ?? [], $marks], JSON_THROW_ON_ERROR));
    }

    /** Owners may request a review of their retained result, including after enrollment ends. */
    public function requestMarkReview(User $learner, Assessment $assessment, int $attempt, int $answerId, string $reason): array
    {
        $reason = trim($reason);
        validator(compact('reason'), ['reason' => 'required|string|min:10|max:2000'])->validate();

        return DB::transaction(function () use ($learner, $assessment, $attempt, $answerId, $reason) {
            User::whereKey($learner->id)->lockForUpdate()->firstOrFail();
            $session = LearningSession::where('user_id', $learner->id)->where('activity_type', 'assessment')
                ->where('metadata->assessment_id', $assessment->id)->where('metadata->attempt', $attempt)
                ->whereNotNull('ended_at')->latest('id')->lockForUpdate()->firstOrFail();
            $answers = $this->finalizedAnswers($session);
            $answer = $answers->firstWhere('id', $answerId);
            abort_unless($answer, 404);
            $this->manualSnapshot($session, $answer);
            $metadata = $session->metadata;
            $version = collect($metadata['mark_corrections'] ?? [])->where('answer_id', $answerId)->count();
            foreach ($metadata['mark_reviews'] ?? [] as $request) {
                if ((int) $request['answer_id'] === $answerId && (int) $request['mark_version'] === $version) {
                    if ($request['reason'] === $reason) return $request;
                    throw ValidationException::withMessages(['reason' => 'A review has already been requested for this recorded mark. Its decision remains in your history.']);
                }
            }
            $request = ['id' => (string) Str::uuid(), 'answer_id' => $answerId, 'mark_version' => $version,
                'points' => (float) $answer->points_earned, 'reason' => $reason, 'status' => 'open', 'requested_at' => now()->toISOString()];
            $metadata['mark_reviews'][] = $request;
            $metadata['open_mark_reviews'] = collect($metadata['mark_reviews'])->where('status', 'open')->count();
            $session->update(['metadata' => $metadata]);

            app(LearningNotificationService::class)->reviewRequested($session, $request);
            return $request;
        });
    }

    /** Publish an explicit, replay-safe manual correction and retain both sides of the record. */
    public function correct(User $reviewer, int $sessionId, int $answerId, $points, string $feedback,
        string $reason, string $expectedRevision, string $operationId): array
    {
        $reason = trim($reason);
        $this->validateDecision($reason, $expectedRevision, $operationId);
        validator(compact('points', 'feedback'), ['points' => 'required|numeric|decimal:0,2|min:0', 'feedback' => 'string|max:4000'])->validate();

        return $this->withFinalizedReview($reviewer, $sessionId, function ($session, $answers) use ($reviewer, $answerId, $points, $feedback, $reason, $expectedRevision, $operationId) {
            $metadata = $session->metadata;
            foreach ($metadata['mark_corrections'] ?? [] as $event) {
                if ($event['id'] !== $operationId) continue;
                abort_unless((int) $event['reviewer_id'] === (int) $reviewer->id && (int) $event['answer_id'] === $answerId
                    && (float) $event['after']['points'] === (float) $points && $event['after']['feedback'] === $feedback && $event['reason'] === $reason,
                    409, 'This correction identifier has already been used. Reload the submission.');
                return $metadata['results'];
            }
            $this->assertRevision($session, $expectedRevision);
            $answer = $answers->firstWhere('id', $answerId);
            abort_unless($answer, 404);
            $snapshot = $this->manualSnapshot($session, $answer);
            validator(compact('points'), ['points' => 'numeric|between:0,'.(float) $snapshot['points']])->validate();
            if ((float) $answer->points_earned === (float) $points && ($answer->feedback ?? '') === $feedback) {
                throw ValidationException::withMessages(['points' => 'Change the mark or learner feedback before publishing a correction.']);
            }
            $before = $this->mark($answer);
            $answer->update(['points_earned' => $points, 'is_correct' => (float) $points >= (float) $snapshot['points'],
                'graded_by' => $reviewer->id, 'graded_at' => now(), 'feedback' => $feedback]);
            $result = $metadata['results'];
            $possible = collect($metadata['grading'])->only(collect($metadata['questions'])->pluck('id')->all())->sum('points');
            $result['total_points'] = $answers->sum('points_earned');
            $result['max_points'] = $possible;
            $result['percentage'] = $possible > 0 ? round($result['total_points'] / $possible * 100, 1) : 0;
            $result['passed'] = $result['percentage'] >= (float) $metadata['pass_percentage'];
            $result['correct_answers'] = $answers->where('is_correct', true)->count();
            $result['corrected_at'] = now()->toISOString();
            $result['correction_count'] = count($metadata['mark_corrections'] ?? []) + 1;
            $metadata['mark_corrections'][] = ['id' => $operationId, 'answer_id' => $answerId, 'reviewer_id' => $reviewer->id,
                'reason' => $reason, 'at' => $result['corrected_at'], 'before' => $before, 'after' => $this->mark($answer),
                'before_result' => $metadata['results'], 'after_result' => $result];
            $metadata['results'] = $result;
            foreach ($metadata['mark_reviews'] ?? [] as $index => $request) {
                if ($request['status'] === 'open' && (int) $request['answer_id'] === $answerId) {
                    $metadata['mark_reviews'][$index] = array_merge($request, ['status' => 'corrected', 'decision' => $reason,
                        'reviewer_id' => $reviewer->id, 'resolved_at' => $result['corrected_at'], 'correction_id' => $operationId]);
                }
            }
            $metadata['open_mark_reviews'] = collect($metadata['mark_reviews'] ?? [])->where('status', 'open')->count();
            $session->update(['metadata' => $metadata]);

            app(LearningNotificationService::class)->result($session, 'correction:'.$operationId, 'assessment_review_decision');
            // Corrections change future eligibility. Existing rewards, completions and issued credentials stay recorded.
            return $result;
        });
    }

    public function upholdMark(User $reviewer, int $sessionId, string $requestId, string $reason,
        string $expectedRevision, string $operationId): array
    {
        $reason = trim($reason);
        $this->validateDecision($reason, $expectedRevision, $operationId);

        return $this->withFinalizedReview($reviewer, $sessionId, function ($session) use ($reviewer, $requestId, $reason, $expectedRevision, $operationId) {
            $metadata = $session->metadata;
            $index = collect($metadata['mark_reviews'] ?? [])->search(fn ($request) => $request['id'] === $requestId);
            abort_if($index === false, 404);
            $request = $metadata['mark_reviews'][$index];
            if ($request['status'] !== 'open') {
                abort_unless(($request['operation_id'] ?? null) === $operationId && ($request['reviewer_id'] ?? null) === $reviewer->id
                    && ($request['decision'] ?? null) === $reason && $request['status'] === 'upheld', 409, 'This request has already been resolved. Reload the submission.');
                return $request;
            }
            $this->assertRevision($session, $expectedRevision);
            $metadata['mark_reviews'][$index] = array_merge($request, ['status' => 'upheld', 'decision' => $reason,
                'reviewer_id' => $reviewer->id, 'resolved_at' => now()->toISOString(), 'operation_id' => $operationId]);
            $metadata['open_mark_reviews'] = collect($metadata['mark_reviews'])->where('status', 'open')->count();
            $session->update(['metadata' => $metadata]);

            app(LearningNotificationService::class)->result($session, 'decision:'.$operationId, 'assessment_review_decision', $requestId);
            return $metadata['mark_reviews'][$index];
        });
    }

    private function withFinalizedReview(User $reviewer, int $sessionId, callable $action): array
    {
        $entry = LearningSession::where('activity_type', 'assessment')->findOrFail($sessionId);
        return DB::transaction(function () use ($reviewer, $entry, $action) {
            User::whereKey($entry->user_id)->lockForUpdate()->firstOrFail();
            $session = LearningSession::whereKey($entry->id)->lockForUpdate()->firstOrFail();
            $this->authorize($reviewer, Assessment::findOrFail($session->metadata['assessment_id'] ?? 0));
            abort_if((int) $reviewer->id === (int) $session->user_id, 403, 'A reviewer cannot change their own result.');

            return $action($session, $this->finalizedAnswers($session));
        });
    }

    private function answers(LearningSession $session)
    {
        return StudentAnswer::where('user_id', $session->user_id)->where('assessment_id', $session->metadata['assessment_id'] ?? 0)
            ->where('attempt_number', $session->metadata['attempt'] ?? 0);
    }

    private function finalizedAnswers(LearningSession $session)
    {
        $metadata = $session->metadata;
        abort_unless($session->ended_at && isset($metadata['results']) && ($metadata['results']['pending_review'] ?? true) === false,
            422, 'Every answer must be graded before requesting or correcting a mark.');
        $questions = collect($metadata['questions'] ?? []);
        $answers = $this->answers($session)->lockForUpdate()->get();
        $valid = $questions->isNotEmpty() && $questions->pluck('id')->unique()->count() === $questions->count()
            && $answers->count() === $questions->count() && $answers->pluck('question_id')->unique()->count() === $answers->count()
            && is_numeric($metadata['pass_percentage'] ?? null) && $metadata['pass_percentage'] >= 1 && $metadata['pass_percentage'] <= 100;
        foreach ($questions as $question) {
            $record = $answers->firstWhere('question_id', $question['id']);
            $snapshot = $metadata['grading'][$question['id']] ?? null;
            $valid = $valid && $record?->graded_at && $snapshot && is_numeric($snapshot['points'] ?? null)
                && $record->points_earned >= 0 && $record->points_earned <= $snapshot['points'];
        }
        $valid = $valid && abs($answers->sum('points_earned') - (float) ($metadata['results']['total_points'] ?? -1)) < 0.001;
        abort_unless($valid, 422, 'This historical attempt requires a separate records review; complete grading snapshots are required.');

        return $answers;
    }

    private function manualSnapshot(LearningSession $session, StudentAnswer $answer): array
    {
        $snapshot = $session->metadata['grading'][$answer->question_id] ?? null;
        abort_unless($snapshot && !in_array($snapshot['question_type'] ?? '', self::AUTOMATIC_TYPES, true)
            && (float) $snapshot['points'] > 0, 422, 'Only a reviewed written answer supports this workflow.');
        return $snapshot;
    }

    private function mark(StudentAnswer $answer): array
    {
        return ['points' => (float) $answer->points_earned, 'feedback' => $answer->feedback ?? '',
            'reviewer_id' => $answer->graded_by, 'graded_at' => $answer->graded_at?->toISOString()];
    }

    private function validateDecision(string $reason, string $expectedRevision, string $operationId): void
    {
        validator(compact('reason', 'expectedRevision', 'operationId'), ['reason' => 'required|string|min:10|max:2000',
            'expectedRevision' => 'required|string|size:64', 'operationId' => 'required|uuid'])->validate();
    }

    private function assertRevision(LearningSession $session, string $expectedRevision): void
    {
        abort_unless(hash_equals($this->revision($session), $expectedRevision), 409, 'This submission changed while you were reviewing it. Reload the submission before deciding.');
    }
}
