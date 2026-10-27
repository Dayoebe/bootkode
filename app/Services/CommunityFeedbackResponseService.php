<?php

namespace App\Services;

use App\Models\Community\CommunityFeedback;
use App\Models\Core\User;
use Illuminate\Support\Facades\DB;

class CommunityFeedbackResponseService
{
    public function respond(User $actor, int $feedbackId, string $response): CommunityFeedback
    {
        abort_unless(\Illuminate\Support\Facades\Schema::hasColumn('community_feedback', 'responded_by'), 503, 'Community replies are being set up.');
        $response = trim($response);
        validator(compact('response'), ['response' => 'required|string|min:10|max:10000'])->validate();

        return DB::transaction(function () use ($actor, $feedbackId, $response) {
            $actor = User::whereKey($actor->id)->lockForUpdate()->firstOrFail();
            abort_unless($actor->is_active && $actor->email_verified_at && $actor->hasAnyRole(['super_admin', 'academy_admin']), 403);
            $feedback = CommunityFeedback::whereKey($feedbackId)->lockForUpdate()->firstOrFail();
            if ($feedback->admin_response !== null && trim($feedback->admin_response) !== '') {
                abort_unless($feedback->admin_response === $response, 409, 'This feedback already has a recorded reply. Reload to read it.');

                return $feedback;
            }
            abort_if($feedback->status === 'closed', 422, 'This feedback is closed.');
            $feedback->update(['admin_response' => $response, 'responded_at' => now(), 'responded_by' => $actor->id, 'assigned_to' => $feedback->assigned_to ?: $actor->id,
                'status' => $feedback->status === 'open' ? 'in_progress' : $feedback->status]);
            app(PortalNotificationService::class)->record($feedback->user, 'community-feedback:'.$feedback->id,
                ['type' => 'community_feedback_response', 'title' => 'Community feedback reply', 'message' => 'The community team replied to your feedback.',
                    'action_url' => route('community.feedback'), 'icon' => 'fas fa-comment-dots', 'feedback_id' => $feedback->id], 'community_feedback_response');

            return $feedback;
        });
    }
}
