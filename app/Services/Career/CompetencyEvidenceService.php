<?php

namespace App\Services\Career;

use App\Models\Assessment\Assessment;
use App\Models\Assessment\LearningSession;
use App\Models\Career\CompetencyEvidence;
use App\Models\Content\Portfolio;
use App\Models\Core\User;
use App\Models\Credentials\Certificate;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CompetencyEvidenceService
{
    public function claim(array $input): CompetencyEvidence
    {
        $user = app(CareerAccess::class)->account();
        $data = validator($input, [
            'kind' => ['required', Rule::in(array_keys(JobRequirementService::KINDS))], 'label' => 'required|string|max:120',
            'source_type' => 'required|in:external,assessment,certificate,portfolio', 'source_id' => 'nullable|integer|min:1',
            'reference' => 'required|string|min:10|max:2000', 'years_experience' => 'nullable|numeric|between:0,80',
        ])->validate();
        $data['label'] = trim($data['label']);
        $data['reference'] = trim($data['reference']);
        $data['source_id'] = $data['source_type'] === 'external' ? null : ($data['source_id'] ?? null);
        $data['years_experience'] = filled($data['years_experience'] ?? null) ? (float) $data['years_experience'] : null;
        abort_if($data['years_experience'] !== null && $data['kind'] !== 'experience', 422, 'Years belong only to experience evidence.');
        abort_unless($data['source_type'] === 'external' || $data['source_id'], 422, 'Choose an owned evidence source.');
        $candidate = new CompetencyEvidence([...$data, 'user_id' => $user->id]);
        abort_unless($data['source_type'] === 'external' || $this->validSource($candidate), 422, 'That source is unavailable, unowned or not a finalized passing result.');
        $data['claim_hash'] = hash('sha256', json_encode([$data['kind'], app(JobRequirementService::class)->normalize($data['label']), $data['source_type'],
            $data['source_id'], $data['reference'], $data['years_experience']]));

        return DB::transaction(function () use ($user, $data) {
            User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            $existing = CompetencyEvidence::where('user_id', $user->id)->where('claim_hash', $data['claim_hash'])->first();
            if ($existing) {
                return $existing;
            }
            abort_if(CompetencyEvidence::where('user_id', $user->id)->where('review_status', 'pending')->count() >= 30, 422, 'You can have up to 30 pending evidence requests.');

            return CompetencyEvidence::firstOrCreate(['user_id' => $user->id, 'claim_hash' => $data['claim_hash']], $data);
        });
    }

    public function review(int $id, string $decision, string $reason, ?string $expiresAt = null): void
    {
        $reviewer = app(CareerAccess::class)->administrator();
        validator(['decision' => $decision, 'reason' => trim($reason), 'expires_at' => $expiresAt], [
            'decision' => 'required|in:verified,rejected,revoked', 'reason' => 'required|string|min:10|max:1000', 'expires_at' => 'nullable|date|after:now',
        ])->validate();
        DB::transaction(function () use ($id, $decision, $reason, $expiresAt, $reviewer) {
            $ownerId = CompetencyEvidence::whereKey($id)->firstOrFail()->user_id;
            User::whereKey($ownerId)->lockForUpdate()->firstOrFail();
            $evidence = CompetencyEvidence::whereKey($id)->lockForUpdate()->firstOrFail();
            abort_if((int) $evidence->user_id === (int) $reviewer->id, 403, 'You cannot verify or review your own evidence.');
            abort_unless(($evidence->review_status === 'pending' && in_array($decision, ['verified', 'rejected']))
                || ($evidence->review_status === 'verified' && $decision === 'revoked'), 409, 'This evidence review changed. Reload it before deciding.');
            abort_if($decision === 'verified' && ! $this->validSource($evidence), 422, 'The evidence source is no longer valid.');
            $history = $evidence->review_history ?? [];
            $history[] = ['by' => $reviewer->id, 'at' => now()->toIso8601String(), 'from' => $evidence->review_status, 'to' => $decision,
                'reason' => trim($reason), 'expires_at' => $expiresAt];
            $evidence->update(['review_status' => $decision, 'reviewed_by' => $reviewer->id, 'reviewed_at' => now(),
                'review_reason' => trim($reason), 'expires_at' => $decision === 'verified' ? $expiresAt : $evidence->expires_at, 'review_history' => $history]);
        });
    }

    public function reassess(int $id): void
    {
        $user = app(CareerAccess::class)->account();
        DB::transaction(function () use ($user, $id) {
            User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            $evidence = CompetencyEvidence::where('user_id', $user->id)->lockForUpdate()->findOrFail($id);
            if ($evidence->review_status === 'pending') {
                return;
            }
            $history = $evidence->review_history ?? [];
            $history[] = ['by' => $user->id, 'at' => now()->toIso8601String(), 'from' => $evidence->review_status, 'to' => 'pending', 'reason' => 'Owner requested reassessment.'];
            $evidence->update(['review_status' => 'pending', 'review_history' => $history]);
        });
    }

    public function usable(CompetencyEvidence $evidence): bool
    {
        return $evidence->review_status === 'verified' && $evidence->reviewed_at !== null
            && (! $evidence->expires_at || $evidence->expires_at->isFuture()) && $this->validSource($evidence);
    }

    public function validSource(CompetencyEvidence $evidence): bool
    {
        if ($evidence->source_type === 'external') {
            return filled($evidence->reference);
        }
        if ($evidence->source_type === 'assessment' && $evidence->kind === 'skill') {
            $session = LearningSession::where('user_id', $evidence->user_id)->where('activity_type', 'assessment')->whereNotNull('ended_at')->find($evidence->source_id);
            $results = $session?->metadata['results'] ?? [];
            $threshold = $session?->metadata['pass_percentage'] ?? null;

            $assessment = Assessment::withTrashed()->find($session?->metadata['assessment_id'] ?? 0);

            return $assessment && (int) $assessment->course_id === (int) $session?->course_id && is_numeric($threshold) && is_numeric($results['percentage'] ?? null)
                && ($results['passed'] ?? false) === true && ($results['pending_review'] ?? true) === false
                && (float) $results['percentage'] >= (float) $threshold;
        }
        if ($evidence->source_type === 'certificate' && $evidence->kind === 'certification') {
            $certificate = Certificate::with('course')->where('user_id', $evidence->user_id)->where('status', Certificate::STATUS_APPROVED)->find($evidence->source_id);

            return $certificate && filled($certificate->verification_code) && $certificate->course
                && app(JobRequirementService::class)->normalize($evidence->label) === app(JobRequirementService::class)->normalize($certificate->course->title);
        }
        if ($evidence->source_type === 'portfolio' && $evidence->kind === 'portfolio') {
            return Portfolio::where('user_id', $evidence->user_id)->where('status', 'completed')->whereKey($evidence->source_id)->exists();
        }

        return false;
    }

    public function sourceSummary(CompetencyEvidence $evidence): array
    {
        app(CareerAccess::class)->administrator();
        if ($evidence->source_type === 'assessment') {
            $session = LearningSession::where('user_id', $evidence->user_id)->find($evidence->source_id);
            $assessment = Assessment::withTrashed()->find($session?->metadata['assessment_id'] ?? 0);
            $results = $session?->metadata['results'] ?? [];

            return ['Assessment' => $assessment?->title ?? 'Source unavailable',
                'Retained result' => ($results['pending_review'] ?? true) || ! is_numeric($results['percentage'] ?? null) ? 'No finalized score' : $results['percentage'].'%',
                'Original pass threshold' => isset($session->metadata['pass_percentage']) ? $session->metadata['pass_percentage'].'%' : 'Unavailable'];
        }
        if ($evidence->source_type === 'certificate') {
            $certificate = Certificate::where('user_id', $evidence->user_id)->with('course:id,title')->find($evidence->source_id);

            return ['Named course credential' => $certificate?->course?->title ?? 'Source unavailable',
                'Recorded certificate status' => $certificate?->status ?? 'Unavailable', 'Certificate identifier' => $certificate?->certificate_number ?? 'Unavailable'];
        }
        if ($evidence->source_type === 'portfolio') {
            $portfolio = Portfolio::where('user_id', $evidence->user_id)->find($evidence->source_id);

            return ['Portfolio title' => $portfolio?->title ?? 'Source unavailable', 'Recorded status' => $portfolio?->status ?? 'Unavailable',
                'Self-reported project link' => $portfolio?->project_url ?? 'Not supplied'];
        }

        return ['External source' => 'Independently check the learner’s reference; no automatic external verification has occurred.'];
    }

    public function owned(User $user)
    {
        app(CareerAccess::class)->owner($user);

        return CompetencyEvidence::where('user_id', $user->id)->latest('id')->get();
    }
}
