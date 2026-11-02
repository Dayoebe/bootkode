<?php

namespace App\Services;

use App\Models\Assessment\Assessment;
use App\Models\Assessment\LearningSession;
use App\Models\Core\User;

class AssessmentResultService
{
    /** Project only the learner's submitted responses, never marking keys. */
    public function responses(Assessment $assessment, User $learner, int $attempt): array
    {
        abort_unless((int) auth()->id() === (int) $learner->id, 403);
        $session = LearningSession::where('user_id', $learner->id)->where('activity_type', 'assessment')
            ->where('metadata->assessment_id', $assessment->id)->where('metadata->attempt', $attempt)->whereNotNull('ended_at')->latest('id')->first();
        $snapshots = collect($session?->metadata['questions'] ?? [])->keyBy('id');
        return $assessment->studentAnswers()->where('user_id', $learner->id)->where('attempt_number', $attempt)->whereNotNull('submitted_at')
            ->with(['question' => fn ($query) => $query->withTrashed()])->get()->map(function ($answer) use ($snapshots, $session) {
                $question = $snapshots->get($answer->question_id);
                if (!$question) {
                    $question = ['question_text' => $answer->question?->question_text ?? 'Historical question', 'question_type' => $answer->question?->question_type,
                        'options' => $answer->question?->options ?? [], 'points' => $answer->question?->points, 'display_to_original_map' => []];
                }
                $value = $answer->answer;
                if (in_array($question['question_type'], StructuredQuestionService::TYPES, true) && $session) {
                    $value = app(StructuredQuestionService::class)->response($question, $session->metadata['grading'][$answer->question_id] ?? [], $value);
                }
                if ($question['question_type'] === 'qna_topic') $value = app(QnaResponseService::class)->display($value, $answer->exam_data['qna_rules'] ?? null);
                $automatic = in_array($question['question_type'], ['multiple_choice', 'true_false', ...StructuredQuestionService::TYPES], true);
                if (in_array($question['question_type'], ['multiple_choice', 'true_false'], true) && $value !== null && $value !== '') {
                    $decoded = json_decode($value, true);
                    $indices = is_array($decoded) ? $decoded : [$value];
                    $options = $question['options'];
                    if (is_string($options)) $options = json_decode($options, true) ?? [];
                    if ($question['question_type'] === 'true_false' && count($options) !== 2) $options = ['True', 'False'];
                    $map = array_map('intval', $question['display_to_original_map'] ?? []);
                    $value = collect($indices)->map(function ($index) use ($options, $map) {
                        $display = $map ? array_search((int) $index, $map, true) : (int) $index;
                        return $display !== false && isset($options[$display]) ? strip_tags((string) $options[$display]) : 'Selected option '.((int) $index + 1);
                    })->implode('; ');
                }
                $files = $session ? app(AssessmentSubmissionService::class)->projectedFiles($session, $answer->question_id) : [];
                return ['id' => $answer->id, 'question' => strip_tags($question['question_text']), 'response' => $value ?? ($files ? 'No written response.' : 'No answer submitted.'),
                    'points' => $answer->points_earned, 'max_points' => $question['points'], 'pending_review' => !$automatic && !$answer->graded_at,
                    'feedback' => $answer->feedback, 'files' => $files,
                    'can_withdraw_sharing' => in_array($answer->exam_data['qna']['status'] ?? '', ['pending', 'published'], true),
                    'corrections' => collect($session?->metadata['mark_corrections'] ?? [])->where('answer_id', $answer->id)->map(fn ($event) => [
                        'before_points' => $event['before']['points'], 'after_points' => $event['after']['points'],
                        'reason' => $event['reason'], 'at' => $event['at'],
                    ])->values()->all(),
                    'mark_reviews' => collect($session?->metadata['mark_reviews'] ?? [])->where('answer_id', $answer->id)->map(fn ($request) =>
                        \Illuminate\Support\Arr::only($request, ['reason', 'status', 'requested_at', 'decision', 'resolved_at']))->values()->all(),
                    'can_request_review' => !$automatic && (bool) $answer->graded_at && $session && !($session->metadata['results']['pending_review'] ?? true)
                        && isset($session->metadata['grading'][$answer->question_id], $session->metadata['pass_percentage'])
                        && !collect($session->metadata['mark_reviews'] ?? [])->contains(fn ($request) => (int) $request['answer_id'] === (int) $answer->id
                            && (int) $request['mark_version'] === collect($session->metadata['mark_corrections'] ?? [])->where('answer_id', $answer->id)->count()),
                ];
            })->all();
    }
}
