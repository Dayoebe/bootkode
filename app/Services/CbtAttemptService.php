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
        if (!$assessment->course_id) return; // Existing standalone CBT contract.
        $course = $assessment->course;
        abort_unless($course, 404);
        abort_unless($user->hasAnyRole(['super_admin', 'academy_admin']) || (int) $course->instructor_id === (int) $user->id
            || $user->enrollments()->where('course_id', $course->id)->exists() || $user->courses()->where('courses.id', $course->id)->exists(), 403);
    }

    public function questions(Assessment $assessment): array
    {
        $questions = $assessment->relationLoaded('questions') ? $assessment->questions : $assessment->questions()->get();
        if ($assessment->shuffle_questions) $questions = $questions->shuffle();
        return $questions->map(function ($question) use ($assessment) {
            $options = $question->options;
            if (is_string($options)) $options = json_decode($options, true);
            if ($question->question_type === 'true_false') $options = ['True', 'False'];
            $indexed = collect($options ?? [])->map(fn ($text, $index) => ['index' => $index, 'text' => $text]);
            if ($assessment->shuffle_options && $question->question_type === 'multiple_choice') $indexed = $indexed->shuffle();
            return array_merge($question->only(['id', 'question_text', 'question_type', 'points', 'time_limit']), [
                'options' => $indexed->pluck('text')->values()->all(),
                'display_to_original_map' => $indexed->pluck('index')->values()->all(),
                'was_shuffled' => (bool) $assessment->shuffle_options,
            ]);
        })->values()->all();
    }

    public function start(User $user, Assessment $assessment): LearningSession
    {
        return DB::transaction(function () use ($user, $assessment) {
            User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            $this->authorize($assessment, $user);
            $active = LearningSession::where('user_id', $user->id)->where('activity_type', 'assessment')->where('metadata->assessment_id', $assessment->id)->whereNull('ended_at')->lockForUpdate()->latest('id')->first();
            if ($active) return $active;
            [$eligible, $message] = $assessment->canUserTakeAssessment($user->id);
            abort_unless($eligible, 403, $message);
            $assessment->load(['questions' => fn ($query) => $query->lockForUpdate()]);
            $questions = $this->questions($assessment);
            abort_unless(count($questions) > 0, 422, 'No assessment questions are available.');
            abort_if(collect($questions)->contains(fn ($question) => !in_array($question['question_type'], ['multiple_choice', 'true_false'], true)), 422, 'This exam requires a grading workflow that is not available in the CBT player.');
            return LearningSession::create(['user_id' => $user->id, 'course_id' => $assessment->course_id, 'activity_type' => 'assessment', 'started_at' => now(),
                'metadata' => ['assessment_id' => $assessment->id, 'attempt' => $assessment->getNextAttemptNumber($user->id), 'questions' => $questions,
                    'grading' => $assessment->questions->mapWithKeys(fn ($question) => [$question->id => $question->only(['correct_answers', 'points', 'question_type'])])->all(),
                    'duration_seconds' => max(60, (int) $assessment->estimated_duration_minutes * 60)]]);
        });
    }

    public function saveAnswer(User $user, Assessment $assessment, int $sessionId, int $questionId, $value): void
    {
        DB::transaction(function () use ($user, $assessment, $sessionId, $questionId, $value) {
            User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            $this->authorize($assessment, $user);
            $session = LearningSession::where('user_id', $user->id)->whereNull('ended_at')->lockForUpdate()->findOrFail($sessionId);
            abort_unless((int) ($session->metadata['assessment_id'] ?? 0) === (int) $assessment->id, 403);
            abort_if($session->started_at->diffInSeconds(now(), false) > $session->metadata['duration_seconds'] + 5, 422, 'The answer deadline has passed.');
            $question = collect($session->metadata['questions'])->firstWhere('id', $questionId);
            abort_unless($question && ($value === null || (filter_var($value, FILTER_VALIDATE_INT) !== false && array_key_exists((int) $value, $question['display_to_original_map']))), 422);
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
            foreach ($questions as $snapshot) {
                $question = $assessment->questions()->findOrFail($snapshot['id']);
                $value = $answers[$question->id] ?? null;
                if ($value !== null && $value !== '') {
                    abort_unless(filter_var($value, FILTER_VALIDATE_INT) !== false && array_key_exists((int) $value, $snapshot['display_to_original_map']), 422, 'Choose a valid answer.');
                    $value = (string) $snapshot['display_to_original_map'][(int) $value];
                    $answered++;
                } else $value = null;
                $answer = StudentAnswer::create(['user_id' => $user->id, 'assessment_id' => $assessment->id, 'question_id' => $question->id,
                    'attempt_number' => $session->metadata['attempt'], 'answer' => $value, 'time_spent_seconds' => min($elapsed, $session->metadata['duration_seconds']), 'submitted_at' => now(),
                    'question_order' => $ids, 'exam_data' => ['was_shuffled' => $snapshot['was_shuffled']]]);
                $grading = $session->metadata['grading'][$question->id] ?? $question->only(['correct_answers', 'points', 'question_type']);
                if ($value !== null && !$answer->autoGrade($grading)) throw new \RuntimeException('Grading failed; no attempt was recorded.');
                $answer->refresh();
                $earned += (float) $answer->points_earned;
                $possible += max(0, (float) $grading['points']);
                if ($answer->is_correct) $correct++;
            }
            $percentage = $possible > 0 ? round($earned / $possible * 100, 1) : 0;
            $results = ['total_questions' => count($questions), 'answered_questions' => $answered, 'correct_answers' => $correct, 'total_points' => $earned, 'max_points' => $possible,
                'percentage' => $percentage, 'passed' => $percentage >= (float) $assessment->pass_percentage, 'attempt_number' => $session->metadata['attempt'], 'time_spent' => min($elapsed, $session->metadata['duration_seconds']), 'expired' => $expired];
            $session->update(['ended_at' => now(), 'duration_minutes' => (int) floor(min($elapsed, $session->metadata['duration_seconds']) / 60), 'metadata' => array_merge($session->metadata, ['results' => $results])]);
            app(GamificationService::class)->handleQuizCompletion($user, $assessment);
            return $results;
        });
    }
}
