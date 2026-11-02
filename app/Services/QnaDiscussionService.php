<?php

namespace App\Services;

use App\Models\Assessment\Assessment;
use App\Models\Assessment\LearningSession;
use App\Models\Assessment\StudentAnswer;
use App\Models\Core\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QnaDiscussionService
{
    public function authorize(User $actor, Assessment $assessment): bool
    {
        $actor->refresh()->unsetRelation('roles');
        abort_unless($actor->is_active && $actor->email_verified_at, 403);
        $assessment->refresh();
        abort_unless($assessment->type === 'qna' && $assessment->lesson_id, 404);
        app(CbtAttemptService::class)->authorize($assessment, $actor);
        app(CbtAttemptService::class)->authorizeLesson($actor, $assessment->lesson);
        return $actor->hasAnyRole(['super_admin', 'academy_admin'])
            || ($actor->hasRole('instructor') && (int) $assessment->course?->instructor_id === (int) $actor->id);
    }

    private function record(Assessment $assessment, int $answerId): StudentAnswer
    {
        $answer = $assessment->studentAnswers()->whereNotNull('submitted_at')->lockForUpdate()->findOrFail($answerId);
        $session = LearningSession::where('user_id', $answer->user_id)->where('activity_type', 'assessment')->whereNotNull('ended_at')
            ->findOrFail($answer->exam_data['qna_session_id'] ?? 0);
        $metadata = $session->metadata;
        $snapshot = $metadata['grading'][$answer->question_id] ?? [];
        abort_unless((int) ($metadata['assessment_id'] ?? 0) === (int) $assessment->id
            && (int) ($metadata['attempt'] ?? 0) === (int) $answer->attempt_number
            && ($snapshot['question_type'] ?? null) === 'qna_topic'
            && ($snapshot['qna_rules'] ?? null) === ($answer->exam_data['qna_rules'] ?? null)
            && ($snapshot['qna_rules']['allow_peer_review'] ?? false), 422, 'Peer participation is not enabled for this recorded response.');
        return $answer;
    }

    private function eligibleQuestions(User $actor, Assessment $assessment): array
    {
        return $assessment->studentAnswers()->where('user_id', $actor->id)->whereNotNull('submitted_at')->whereNotNull('answer')
            ->where('exam_data->qna_rules->allow_peer_review', true)->get()->filter(function ($answer) {
                $responses = json_decode($answer->answer, true);
                if (!is_array($responses) || !count($responses)) return false;
                try { app(QnaResponseService::class)->validate($answer->exam_data['qna_rules'], $responses, true); }
                catch (\Symfony\Component\HttpKernel\Exception\HttpException) { return false; }
                return count(array_filter($responses, fn ($text) => is_string($text) && trim($text) !== '')) >= $answer->exam_data['qna_rules']['min_responses'];
            })->pluck('question_id')->unique()->all();
    }

    private function change(User $actor, Assessment $assessment, int $answerId, callable $action, bool $ownerWithdrawal = false): void
    {
        DB::transaction(function () use ($actor, $assessment, $answerId, $action, $ownerWithdrawal) {
            User::whereKey($actor->id)->lockForUpdate()->firstOrFail();
            if ($ownerWithdrawal) {
                $actor->refresh(); abort_unless($actor->is_active && $actor->email_verified_at, 403); $moderator = false;
            } else $moderator = $this->authorize($actor, $assessment);
            $answer = $this->record($assessment, $answerId);
            $data = $answer->exam_data;
            $state = $data['qna'] ?? ['status' => 'private', 'version' => 0, 'reviews' => [], 'history' => []];
            $action($answer, $state, $moderator);
            $data['qna'] = $state;
            $answer->update(['exam_data' => $data]);
        });
    }

    private function event(array &$state, User $actor, string $action, string $reason = ''): void
    {
        $state['version']++;
        $state['history'][] = ['action' => $action, 'by' => $actor->id, 'reason' => $reason, 'at' => now()->toISOString()];
    }

    public function share(User $actor, Assessment $assessment, int $answerId): void
    {
        $this->change($actor, $assessment, $answerId, function ($answer, &$state) use ($actor) {
            abort_unless((int) $answer->user_id === (int) $actor->id, 403);
            if (in_array($state['status'], ['pending', 'published'], true)) return;
            abort_unless($state['status'] === 'private', 422, 'This sharing request has already been closed. Your original response is retained.');
            app(QnaResponseService::class)->validate($answer->exam_data['qna_rules'], json_decode($answer->answer ?? '', true), true);
            abort_unless(collect(json_decode($answer->answer ?? '', true))->contains(fn ($text) => is_string($text) && trim($text) !== ''), 422, 'There is no submitted response to share.');
            $state['status'] = $answer->exam_data['qna_rules']['moderator_approval'] ? 'pending' : 'published';
            $this->event($state, $actor, 'share');
        });
    }

    public function withdraw(User $actor, Assessment $assessment, int $answerId): void
    {
        $this->change($actor, $assessment, $answerId, function ($answer, &$state) use ($actor) {
            abort_unless((int) $answer->user_id === (int) $actor->id, 403);
            if ($state['status'] === 'withdrawn') return;
            abort_unless(in_array($state['status'], ['pending', 'published'], true), 422);
            $state['status'] = 'withdrawn';
            $this->event($state, $actor, 'withdraw');
        }, true);
    }

    public function moderate(User $actor, Assessment $assessment, int $answerId, int $version, string $decision, string $reason): void
    {
        validator(compact('decision', 'reason'), ['decision' => 'required|in:publish,reject,hide', 'reason' => 'required|string|min:10|max:1000'])->validate();
        $this->change($actor, $assessment, $answerId, function ($answer, &$state, $moderator) use ($actor, $version, $decision, $reason) {
            abort_unless($moderator && (int) $answer->user_id !== (int) $actor->id, 403);
            abort_unless($state['version'] === $version, 409, 'This discussion changed. Reload before deciding.');
            abort_unless(($state['status'] === 'pending' && in_array($decision, ['publish', 'reject'], true)) || ($state['status'] === 'published' && $decision === 'hide'), 422);
            $state['status'] = ['publish' => 'published', 'reject' => 'rejected', 'hide' => 'hidden'][$decision];
            $this->event($state, $actor, $decision, $reason);
        });
    }

    public function review(User $actor, Assessment $assessment, int $answerId, string $text): void
    {
        $text = trim($text);
        validator(compact('text'), ['text' => 'required|string|min:20|max:4000'])->validate();
        $this->change($actor, $assessment, $answerId, function ($answer, &$state, $moderator) use ($actor, $assessment, $text) {
            abort_unless(!$moderator && (int) $answer->user_id !== (int) $actor->id
                && in_array($answer->question_id, $this->eligibleQuestions($actor, $assessment), true), 403, 'Submit your own response to this topic before reviewing a peer.');
            abort_unless($state['status'] === 'published', 403);
            foreach ($state['reviews'] as $review) {
                if ((int) $review['by'] !== (int) $actor->id) continue;
                abort_unless($review['text'] === $text, 422, 'Your feedback is retained. Only one feedback entry is allowed per shared response.');
                return;
            }
            abort_unless(count($state['reviews']) < 100, 422, 'This response has reached its feedback limit.');
            $state['reviews'][] = ['id' => (string) Str::uuid(), 'by' => $actor->id, 'text' => $text,
                'status' => $answer->exam_data['qna_rules']['moderator_approval'] ? 'pending' : 'published', 'at' => now()->toISOString()];
            $this->event($state, $actor, 'peer_feedback');
        });
    }

    public function moderateReview(User $actor, Assessment $assessment, int $answerId, int $version, string $reviewId, string $decision, string $reason): void
    {
        validator(compact('decision', 'reason'), ['decision' => 'required|in:publish,reject,hide', 'reason' => 'required|string|min:10|max:1000'])->validate();
        $this->change($actor, $assessment, $answerId, function ($answer, &$state, $moderator) use ($actor, $version, $reviewId, $decision, $reason) {
            abort_unless($moderator, 403);
            abort_unless($state['version'] === $version, 409, 'This discussion changed. Reload before deciding.');
            $index = array_search($reviewId, array_column($state['reviews'], 'id'), true);
            abort_unless($index !== false, 404);
            $review = &$state['reviews'][$index];
            abort_unless((int) $review['by'] !== (int) $actor->id, 403);
            abort_unless(($review['status'] === 'pending' && in_array($decision, ['publish', 'reject'], true)) || ($review['status'] === 'published' && $decision === 'hide'), 422);
            $review['status'] = ['publish' => 'published', 'reject' => 'rejected', 'hide' => 'hidden'][$decision];
            $this->event($state, $actor, 'feedback_'.$decision, $reason);
        });
    }

    /** Never serialize answer models, private rubrics, grader identities, result data or audit identifiers. */
    public function entries(User $actor, Assessment $assessment)
    {
        $moderator = $this->authorize($actor, $assessment);
        $eligible = $moderator ? [] : $this->eligibleQuestions($actor, $assessment);
        $query = $assessment->studentAnswers()->whereNotNull('submitted_at')->where('exam_data->qna_rules->allow_peer_review', true);
        if (!$moderator) $query->where(fn ($q) => $q->where('user_id', $actor->id)->orWhere(fn ($q) => $q->where('exam_data->qna->status', 'published')->whereIn('question_id', $eligible)));
        $page = $query->orderByDesc('id')->paginate(10);
        $page->through(function ($answer) use ($actor, $moderator, $eligible) {
            $state = $answer->exam_data['qna'] ?? ['status' => 'private', 'version' => 0, 'reviews' => [], 'history' => []];
            $own = (int) $answer->user_id === (int) $actor->id;
            $reviews = collect($state['reviews'])->filter(fn ($review) => $moderator || $review['status'] === 'published' || (int) $review['by'] === (int) $actor->id)
                ->map(fn ($review) => ['id' => $review['id'], 'text' => $review['text'], 'status' => $review['status'], 'own' => (int) $review['by'] === (int) $actor->id])->values()->all();
            return ['id' => $answer->id, 'question_id' => $answer->question_id, 'own' => $own,
                'label' => $own ? 'Your submitted response' : 'Participant '.$answer->id,
                'topic' => $answer->exam_data['qna_rules']['topic_title'] ?? 'Submitted Q&A topic',
                'response' => app(QnaResponseService::class)->display($answer->answer, $answer->exam_data['qna_rules']),
                'status' => $state['status'], 'version' => $state['version'], 'reviews' => $reviews,
                'can_review' => !$moderator && !$own && $state['status'] === 'published' && in_array($answer->question_id, $eligible, true)
                    && !collect($state['reviews'])->contains(fn ($review) => (int) $review['by'] === (int) $actor->id),
                'decisions' => ($own || $moderator) ? collect($state['history'])->filter(fn ($event) => $event['reason'] !== '' && ($moderator || !str_starts_with($event['action'], 'feedback_')))->map(fn ($event) => \Illuminate\Support\Arr::only($event, ['action', 'reason', 'at']))->values()->all() : [],
            ];
        });
        return $page;
    }
}
