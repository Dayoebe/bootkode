<?php

namespace App\Livewire\Community\Partial;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ForumThread;
use App\Models\ForumReply;
use App\Models\CommunityActivity;
use App\Models\CommunityFeedback;
use App\Models\User;

class Moderation extends Component
{
    use WithPagination;
    
    public $activeTab = 'overview';
    public $showModerationModal = false;
    public $moderationAction = '';
    public $selectedItem = [];
    public $moderationReason = '';
    
    // Pagination properties for different tabs
    public $threadsPerPage = 10;
    public $repliesPerPage = 10;
    public $groupsPerPage = 10;
    public $challengesPerPage = 10;
    public $eventsPerPage = 10;
    public $feedbackPerPage = 10;

    public function mount()
    {
        if (!auth()->user()->canManageUsers()) {
            abort(403, 'Unauthorized access to moderation panel.');
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    // Alias method to match the view
    public function setTab($tab)
    {
        $this->setActiveTab($tab);
    }

    public function moderateItem($type, $id, $action)
    {
        $this->selectedItem = [
            'type' => $type,
            'id' => $id
        ];
        $this->moderationAction = $action;
        $this->showModerationModal = true;
    }

    public function executeModerationAction()
    {
        if (!$this->selectedItem) {
            return;
        }

        $type = $this->selectedItem['type'];
        $id = $this->selectedItem['id'];
        $action = $this->moderationAction;

        try {
            switch ($type) {
                case 'thread':
                    $this->handleThreadAction($id, $action);
                    break;
                case 'reply':
                    $this->handleReplyAction($id, $action);
                    break;
                case 'study_group':
                    $this->handleStudyGroupAction($id, $action);
                    break;
                case 'challenge':
                    $this->handleChallengeAction($id, $action);
                    break;
                case 'event':
                    $this->handleEventAction($id, $action);
                    break;
            }

            $this->reset(['showModerationModal', 'selectedItem', 'moderationAction', 'moderationReason']);
            session()->flash('message', ucfirst($action) . ' action completed successfully.');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to perform moderation action: ' . $e->getMessage());
        }
    }

    private function handleThreadAction($id, $action)
    {
        $thread = ForumThread::find($id);
        if (!$thread) return;

        switch ($action) {
            case 'pin':
                $thread->update(['is_pinned' => true]);
                break;
            case 'unpin':
                $thread->update(['is_pinned' => false]);
                break;
            case 'lock':
                $thread->update(['is_locked' => true]);
                break;
            case 'unlock':
                $thread->update(['is_locked' => false]);
                break;
            case 'delete':
                $thread->delete();
                break;
            case 'view':
                // Redirect to thread view - you might want to implement this
                break;
        }
    }

    private function handleReplyAction($id, $action)
    {
        if ($action === 'delete') {
            ForumReply::find($id)?->delete();
        }
    }

    private function handleStudyGroupAction($id, $action)
    {
        $group = CommunityActivity::studyGroups()->find($id);
        if (!$group) return;

        switch ($action) {
            case 'activate':
                $group->update(['status' => 'active']);
                break;
            case 'deactivate':
                $group->update(['status' => 'inactive']);
                break;
            case 'delete':
                $group->delete();
                break;
            case 'view':
                // Redirect to group view
                break;
        }
    }

    private function handleChallengeAction($id, $action)
    {
        $challenge = CommunityActivity::codeChallenges()->find($id);
        if (!$challenge) return;

        switch ($action) {
            case 'activate':
                $challenge->update(['status' => 'active']);
                break;
            case 'deactivate':
                $challenge->update(['status' => 'inactive']);
                break;
            case 'delete':
                $challenge->delete();
                break;
        }
    }

    private function handleEventAction($id, $action)
    {
        $event = CommunityActivity::liveEvents()->find($id);
        if (!$event) return;

        switch ($action) {
            case 'cancel':
                $event->update(['status' => 'cancelled']);
                break;
            case 'delete':
                $event->delete();
                break;
        }
    }

    public function updateFeedbackStatus($feedbackId, $status)
    {
        $feedback = CommunityFeedback::find($feedbackId);
        if ($feedback && in_array($status, ['open', 'in_progress', 'resolved', 'closed'])) {
            $feedback->update(['status' => $status]);
            session()->flash('message', 'Feedback status updated successfully.');
        }
    }

    public function getStatsProperty()
    {
        return [
            'total_threads' => ForumThread::count(),
            'total_replies' => ForumReply::count(),
            'total_groups' => CommunityActivity::studyGroups()->count(),
            'total_challenges' => CommunityActivity::codeChallenges()->count(),
            'total_events' => CommunityActivity::liveEvents()->count(),
            'pending_feedback' => CommunityFeedback::where('status', 'open')->count(),
        ];
    }

    public function getRecentThreadsProperty()
    {
        return ForumThread::with('user')
            ->latest()
            ->limit(5)
            ->get();
    }

    public function getRecentGroupsProperty()
    {
        return CommunityActivity::studyGroups()
            ->with('creator')
            ->latest()
            ->limit(5)
            ->get();
    }

    public function getPendingFeedbackProperty()
    {
        return CommunityFeedback::with('user')
            ->where('status', 'open')
            ->latest()
            ->limit(5)
            ->get();
    }

    public function getThreadsProperty()
    {
        return ForumThread::with(['user', 'category'])
            ->withCount('replies')
            ->latest()
            ->paginate($this->threadsPerPage);
    }

    public function getRepliesProperty()
    {
        return ForumReply::with(['user', 'thread'])
            ->latest()
            ->paginate($this->repliesPerPage);
    }

    public function getGroupsProperty()
    {
        return CommunityActivity::studyGroups()
            ->with(['creator'])
            ->withCount(['activeParticipants as members_count'])
            ->latest()
            ->paginate($this->groupsPerPage);
    }

    public function getChallengesProperty()
    {
        return CommunityActivity::codeChallenges()
            ->with(['creator'])
            ->withCount(['activeParticipants as submissions_count'])
            ->latest()
            ->paginate($this->challengesPerPage);
    }

    public function getEventsProperty()
    {
        return CommunityActivity::liveEvents()
            ->with(['host' => fn($q) => $q->withTrashed(), 'activeParticipants'])
            ->withCount(['activeParticipants as attendees_count'])
            ->latest()
            ->paginate($this->eventsPerPage);
    }

    public function getFeedbackProperty()
    {
        return CommunityFeedback::with(['user', 'assignedTo'])
            ->latest()
            ->paginate($this->feedbackPerPage);
    }

    public function render()
    {
        return view('livewire.community.partial.moderation', [
            'stats' => $this->stats,
            'recent_threads' => $this->recent_threads,
            'recent_groups' => $this->recent_groups,
            'pending_feedback' => $this->pending_feedback,
            'threads' => $this->threads,
            'replies' => $this->replies,
            'groups' => $this->groups,
            'challenges' => $this->challenges,
            'events' => $this->events,
            'feedback' => $this->feedback,
        ]);
    }
}