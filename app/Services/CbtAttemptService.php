<?php

namespace App\Services;

use App\Models\Assessment\Assessment;
use App\Models\Assessment\LearningSession;
use App\Models\Assessment\StudentAnswer;
use App\Models\Core\User;
use Illuminate\Support\Facades\DB;

class CbtAttemptService
{
    public function authorize(Assessment $assessment, User $user): void
    {
        $assessment->refresh();
        $parentCourse = $assessment->lesson?->section?->course ?? $assessment->section?->course;
        if ($parentCourse && $assessment->course_id) abort_unless((int) $parentCourse->id === (int) $assessment->course_id, 403);
        if (!$assessment->course_id && !$parentCourse) {
            abort_unless($assessment->type === 'quiz' && !$assessment->lesson_id && !$assessment->section_id, 403);
            return; // Existing standalone CBT contract.
        }
        $course = $assessment->course ?? $parentCourse;
        abort_unless($course, 404);
        abort_unless($user->hasAnyRole(['super_admin', 'academy_admin']) || (int) $course->instructor_id === (int) $user->id
            || $user->enrollments()->where('course_id', $course->id)->exists() || $user->courses()->where('courses.id', $course->id)->exists(), 403);
    }

    public function questions(Assessment $assessment): array
    {
        return $this->prepareQuestions($assessment)['questions'];
    }

    /** Sanitize retained active snapshots too; old manual options may contain marking notes. */
    public function publicQuestions(LearningSession $session): array
    {
        abort_unless((int) $session->user_id === (int) auth()->id() && $session->activity_type === 'assessment', 403);
        return array_map(function ($question) use ($session) {
            $safe = \Illuminate\Support\Arr::only($question, ['id', 'question_text', 'question_type', 'points', 'time_limit', 'options',
                'display_to_original_map', 'was_shuffled', 'allows_multiple', 'prompts', 'format_version']);
            if (!in_array($question['question_type'], ['multiple_choice', 'true_false', ...StructuredQuestionService::TYPES], true)) {
                $safe['options'] = []; $safe['display_to_original_map'] = []; $safe['allows_multiple'] = false;
                $grading = $session->metadata['grading'][$question['id']] ?? [];
                if (isset($grading['submission_rules'])) {
                    $safe['submission'] = $grading['submission_rules'];
                    if ($safe['submission']['enabled']) $safe['submission']['max_kb'] = min($safe['submission']['max_kb'], app(AssessmentSubmissionService::class)->serverLimitKb());
                    $safe['instructions'] = $grading['instructions'] ?? '';
                }
            }
            return $safe;
        }, $session->metadata['questions'] ?? []);
    }

    private function prepareQuestions(Assessment $assessment): array
    {
        $questions = $assessment->relationLoaded('questions') ? $assessment->questions : $assessment->questions()->get();
        if ($assessment->shuffle_questions) $questions = $questions->shuffle();
        $grading = [];
        $projected = $questions->map(function ($question) use ($assessment, &$grading) {
            $grading[$question->id] = $question->only(['correct_answers', 'points', 'question_type', 'explanation']);
            if (in_array($question->question_type, StructuredQuestionService::TYPES, true)) {
                $projection = app(StructuredQuestionService::class)->project($question);
                $grading[$question->id]['option_map'] = $projection['mapping'];
                unset($projection['mapping']);
                return array_merge($question->only(['id', 'question_text', 'question_type', 'points', 'time_limit']), $projection, ['was_shuffled' => true, 'allows_multiple' => false]);
            }
            if (!in_array($question->question_type, ['multiple_choice', 'true_false'], true)) {
                $public = array_merge($question->only(['id', 'question_text', 'question_type', 'points', 'time_limit']),
                    ['options' => [], 'display_to_original_map' => [], 'was_shuffled' => false, 'allows_multiple' => false]);
                $grading[$question->id]['criteria'] = app(StructuredQuestionService::class)->array($question->options);
                if (in_array($question->question_type, AssessmentSubmissionService::TYPES, true)) {
                    $definition = app(AssessmentSubmissionService::class)->definition($question);
                    $grading[$question->id] = array_merge($grading[$question->id], $definition);
                    $public['submission'] = $definition['submission_rules'];
                    $public['instructions'] = $definition['instructions'];
                }
                return $public;
            }
            $options = $question->options;
            if (is_string($options)) $options = json_decode($options, true);
            if ($question->question_type === 'true_false' && (!is_array($options) || count($options) !== 2)) $options = ['True', 'False'];
            $indexed = collect($options ?? [])->map(fn ($text, $index) => ['index' => $index, 'text' => $text]);
            if ($assessment->shuffle_options && $question->question_type === 'multiple_choice') $indexed = $indexed->shuffle();
            $correct = $question->correct_answers;
            if (is_string($correct)) $correct = json_decode($correct, true);
            return array_merge($question->only(['id', 'question_text', 'question_type', 'points', 'time_limit']), [
                'options' => $indexed->pluck('text')->values()->all(),
                'display_to_original_map' => $indexed->pluck('index')->values()->all(),
                'was_shuffled' => (bool) $assessment->shuffle_options,
                'allows_multiple' => $question->question_type === 'multiple_choice' && count(is_array($correct) ? $correct : []) > 1,
            ]);
        })->values()->all();
        return ['questions' => $projected, 'grading' => $grading];
    }

    public function start(User $user, Assessment $assessment, bool $allowWritten = false): LearningSession
    {
        return DB::transaction(function () use ($user, $assessment, $allowWritten) {
            User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            Assessment::whereKey($assessment->id)->lockForUpdate()->firstOrFail();
            $this->authorize($assessment, $user);
            if ($assessment->lesson) $this->authorizeLesson($user, $assessment->lesson);
            $active = LearningSession::where('user_id', $user->id)->where('activity_type', 'assessment')->where('metadata->assessment_id', $assessment->id)->whereNull('ended_at')->lockForUpdate()->latest('id')->first();
            if ($active) {
                abort_if(!$allowWritten && ($active->metadata['allow_written'] ?? false), 422, 'Resume this attempt from the lesson assessments page.');
                return $active;
            }
            abort_if(LearningSession::where('user_id', $user->id)->where('activity_type', 'assessment')->where('metadata->assessment_id', $assessment->id)->where('metadata->results->pending_review', true)->exists(), 403, 'Your submitted attempt is awaiting instructor review.');
            [$eligible, $message] = $assessment->canUserTakeAssessment($user->id);
            abort_unless($eligible, 403, $message);
            abort_unless(is_numeric($assessment->pass_percentage) && $assessment->pass_percentage >= 1 && $assessment->pass_percentage <= 100, 422, 'Your instructor must configure a pass mark before this assessment can start.');
            $assessment->load(['questions' => fn ($query) => $query->lockForUpdate()]);
            $prepared = $this->prepareQuestions($assessment);
            $questions = $prepared['questions'];
            abort_unless(count($questions) > 0, 422, 'No assessment questions are available.');
            $supported = $allowWritten ? ['multiple_choice', 'true_false', 'short_answer', 'essay', 'fill_blank', 'assignment_question', 'project_criteria', 'qna_topic', ...StructuredQuestionService::TYPES] : ['multiple_choice', 'true_false'];
            abort_if(collect($questions)->contains(fn ($question) => !in_array($question['question_type'], $supported, true) || (!$allowWritten && $question['allows_multiple'])), 422, 'This question format requires a different assessment workflow.');
            return LearningSession::create(['user_id' => $user->id, 'course_id' => $assessment->course_id, 'activity_type' => 'assessment', 'started_at' => now(),
                'metadata' => ['assessment_id' => $assessment->id, 'attempt' => $assessment->getNextAttemptNumber($user->id), 'questions' => $questions, 'allow_written' => $allowWritten, 'pass_percentage' => (float) $assessment->pass_percentage,
                    'grading' => $prepared['grading'],
                    'duration_seconds' => max(60, (int) $assessment->estimated_duration_minutes * 60)]]);
        });
    }

    public function authorizeLesson(User $user, \App\Models\Learning\Lesson $lesson): void
    {
        $lesson->refresh()->load('section.course');
        $course = $lesson->section?->course;
        abort_unless($course, 404);
        if ($user->hasAnyRole(['super_admin', 'academy_admin']) || (int) $course->instructor_id === (int) $user->id) return;
        abort_unless(\App\Models\Learning\Lesson::published()->whereKey($lesson->id)->exists(), 403);
        abort_unless($user->enrollments()->where('course_id', $course->id)->exists() || $user->courses()->where('courses.id', $course->id)->exists(), 403);
        $threshold = min(100, max(1, (int) config('course.section_completion_threshold', 80)));
        foreach ($course->sections()->where('order', '<', $lesson->section->order)->get() as $previous) {
            $ids = $previous->lessons()->published()->pluck('id');
            if ($ids->isEmpty()) continue;
            $completed = DB::table('lesson_user')->where('user_id', $user->id)->whereIn('lesson_id', $ids)->whereNotNull('completed_at')->pluck('lesson_id')
                ->merge(DB::table('user_progress')->where('user_id', $user->id)->where('course_id', $course->id)->whereIn('lesson_id', $ids)->where('is_completed', true)->whereNotNull('completed_at')->pluck('lesson_id'))->unique()->count();
            abort_unless($completed >= (int) ceil($ids->count() * $threshold / 100), 403, 'Complete the previous section before opening this lesson.');
        }
    }

    private function validateAnswer(array $question, $value, bool $allowWritten): void
    {
        if ($value === null || $value === '') return;
        if (in_array($question['question_type'], StructuredQuestionService::TYPES, true)) {
            abort_unless($allowWritten, 422);
            app(StructuredQuestionService::class)->validate($question, $value);
        } elseif (in_array($question['question_type'], ['multiple_choice', 'true_false'], true)) {
            $values = is_array($value) ? $value : [$value];
            foreach ($values as $index) abort_unless(is_int($index) || is_string($index), 422, 'Choose valid options.');
            abort_unless(count($values) > 0 && count($values) <= count($question['display_to_original_map']) && count(array_unique($values)) === count($values), 422, 'Choose valid options.');
            abort_if(count($values) > 1 && (!$allowWritten || empty($question['allows_multiple'])), 422, 'Choose one answer.');
            foreach ($values as $index) abort_unless(filter_var($index, FILTER_VALIDATE_INT) !== false && array_key_exists((int) $index, $question['display_to_original_map']), 422, 'Choose a valid answer.');
        } else {
            abort_unless($allowWritten && is_string($value) && mb_strlen($value) <= 10000, 422, 'Write an answer of up to 10,000 characters.');
        }
    }

    public function saveAnswer(User $user, Assessment $assessment, int $sessionId, int $questionId, $value): void
    {
        DB::transaction(function () use ($user, $assessment, $sessionId, $questionId, $value) {
            User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            $this->authorize($assessment, $user);
            if ($assessment->lesson) $this->authorizeLesson($user, $assessment->lesson);
            $session = LearningSession::where('user_id', $user->id)->where('activity_type', 'assessment')->whereNull('ended_at')->lockForUpdate()->findOrFail($sessionId);
            abort_unless((int) ($session->metadata['assessment_id'] ?? 0) === (int) $assessment->id, 403);
            abort_if($session->started_at->diffInSeconds(now(), false) > $session->metadata['duration_seconds'] + 5, 422, 'The answer deadline has passed.');
            $question = collect($session->metadata['questions'])->firstWhere('id', $questionId);
            abort_unless($question, 422);
            $this->validateAnswer($question, $value, (bool) ($session->metadata['allow_written'] ?? false));
            app(AssessmentSubmissionService::class)->validateResponse($session->metadata['grading'][$questionId] ?? [], $value, []);
            $answers = $session->metadata['answers'] ?? [];
            $answers[$questionId] = $value;
            $session->update(['metadata' => array_merge($session->metadata, ['answers' => $answers])]);
        });
    }

    public function submit(User $user, Assessment $assessment, int $sessionId, array $answers): array
    {
        return DB::transaction(function () use ($user, $assessment, $sessionId, $answers) {
            User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            $this->authorize($assessment, $user);
            if ($assessment->lesson) $this->authorizeLesson($user, $assessment->lesson);
            $session = LearningSession::where('user_id', $user->id)->where('activity_type', 'assessment')->lockForUpdate()->findOrFail($sessionId);
            abort_unless((int) ($session->metadata['assessment_id'] ?? 0) === (int) $assessment->id, 403);
            if ($session->ended_at) return $session->metadata['results'];
            $questions = $session->metadata['questions'];
            $ids = collect($questions)->pluck('id')->all();
            abort_unless(count(array_diff(array_keys($answers), $ids)) === 0, 422, 'An answer does not belong to this attempt.');
            $elapsed = max(0, (int) $session->started_at->diffInSeconds(now(), false));
            $expired = $elapsed > $session->metadata['duration_seconds'] + 5;
            if ($expired) $answers = $session->metadata['answers'] ?? [];
            $earned = $possible = $correct = $answered = 0;
            $pendingReview = false;
            foreach ($questions as $snapshot) {
                $question = $assessment->questions()->findOrFail($snapshot['id']);
                $value = $answers[$question->id] ?? null;
                $this->validateAnswer($snapshot, $value, (bool) ($session->metadata['allow_written'] ?? false));
                $grading = $session->metadata['grading'][$question->id] ?? $question->only(['correct_answers', 'points', 'question_type', 'explanation']);
                $files = $session->metadata['submission_files'][$question->id] ?? [];
                app(AssessmentSubmissionService::class)->validateResponse($grading, $value, $files, true, $expired);
                $structured = in_array($snapshot['question_type'], StructuredQuestionService::TYPES, true);
                $manual = !in_array($snapshot['question_type'], ['multiple_choice', 'true_false', ...StructuredQuestionService::TYPES], true);
                if ($value !== null && $value !== '' && $value !== []) {
                    if ($structured) $value = json_encode(app(StructuredQuestionService::class)->mapAnswer($snapshot, $grading, $value));
                    elseif (!$manual) {
                        $mapped = collect(is_array($value) ? $value : [$value])->map(fn ($index) => (int) $snapshot['display_to_original_map'][(int) $index])->all();
                        $value = count($mapped) === 1 ? (string) $mapped[0] : json_encode($mapped);
                    }
                    $answered++;
                } else {
                    $value = null;
                    if ($files) $answered++;
                }
                $answer = StudentAnswer::create(['user_id' => $user->id, 'assessment_id' => $assessment->id, 'question_id' => $question->id,
                    'attempt_number' => $session->metadata['attempt'], 'answer' => $value, 'time_spent_seconds' => min($elapsed, $session->metadata['duration_seconds']), 'submitted_at' => now(),
                    'question_order' => $ids, 'exam_data' => ['was_shuffled' => $snapshot['was_shuffled'], 'submission_file_ids' => array_column($files, 'id')]]);
                if (!$manual && $value !== null && !$answer->autoGrade($grading)) throw new \RuntimeException('Grading failed; no attempt was recorded.');
                if (!$manual) $answer->forceFill(['graded_at' => now()])->save();
                $pendingReview = $pendingReview || $manual;
                $answer->refresh();
                $earned += (float) $answer->points_earned;
                $possible += max(0, (float) $grading['points']);
                if ($answer->is_correct) $correct++;
            }
            $percentage = $possible > 0 ? round($earned / $possible * 100, 1) : 0;
            $results = ['total_questions' => count($questions), 'answered_questions' => $answered, 'correct_answers' => $correct, 'total_points' => !empty($pendingReview) ? null : $earned, 'max_points' => $possible,
                'percentage' => !empty($pendingReview) ? null : $percentage, 'passed' => empty($pendingReview) && $percentage >= (float) ($session->metadata['pass_percentage'] ?? $assessment->pass_percentage), 'pending_review' => !empty($pendingReview), 'attempt_number' => $session->metadata['attempt'], 'time_spent' => min($elapsed, $session->metadata['duration_seconds']), 'expired' => $expired];
            $session->update(['ended_at' => now(), 'duration_minutes' => (int) floor(min($elapsed, $session->metadata['duration_seconds']) / 60), 'metadata' => array_merge($session->metadata, ['results' => $results])]);
            app(GamificationService::class)->handleQuizCompletion($user, $assessment);
            return $results;
        });
    }
}
