<?php

namespace App\Livewire\Community;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Forum\ForumThread;
use App\Models\Forum\ForumReply;
use App\Models\Community\CommunityActivity;
use App\Models\Community\ActivityParticipant;
use App\Models\Community\CommunityFeedback;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

#[Layout('layouts.dashboard')]
class CommunityCenter extends Component
{
    use WithPagination;

    // ===== TAB & MODAL MANAGEMENT =====
    public $activeTab = 'forums';
    public $activeModal = null;
    public $selectedItem = null;

    // ===== SEARCH & FILTERS =====
    public $search = '';
    public $statusFilter = 'all';
    public $difficultyFilter = 'all';
    public $timeFilter = 'upcoming';
    public $sortBy = 'recent';

    // ===== FORM DATA (Forums) =====
    public $title = '';
    public $content = '';
    public $category = 'general';

    // ===== FORM DATA (Study Groups) =====
    public $groupTitle = '';
    public $groupDescription = '';
    public $groupStartDate = '';
    public $groupEndDate = '';
    public $groupMaxParticipants = '';
    public $groupTags = '';

    // ===== FORM DATA (Code Challenges) =====
    public $challengeTitle = '';
    public $challengeDescription = '';
    public $challengeDifficulty = 'medium';
    public $challengePoints = 100;

    // ===== FORM DATA (Live Events) =====
    public $eventTitle = '';
    public $eventDescription = '';
    public $eventLocation = '';
    public $eventStartDate = '';
    public $eventEndDate = '';
    public $eventMaxParticipants = '';
    public $eventTags = '';

    // ===== FORM DATA (Feedback) =====
    public $feedbackCategory = 'general';
    public $feedbackSubject = '';
    public $feedbackMessage = '';
    public $feedbackPriority = 'medium';
    
    // ===== FORM DATA (Code Submission) =====
    public $code = '';
    public $language = 'javascript';

    // ===== MODERATION PROPERTIES =====
    public $moderationTab = 'reports';
    public $reportStatusFilter = '';
    public $feedbackStatusFilter = '';
    public $userSearch = '';

    // ===== COMMUNITY STATS =====
    public $stats = [
        'totalMembers' => 0,
        'activeStudyGroups' => 0,
        'activeChallenges' => 0,
        'upcomingEvents' => 0,
        'totalThreads' => 0,
        'userLevel' => 1,
        'userStreak' => 0,
    ];

    protected $rules = [
        // Forum rules
        'title' => 'required|min:5|max:255',
        'content' => 'required|min:10',
        'category' => 'required|string',

        // Study group rules
        'groupTitle' => 'required|min:5|max:255',
        'groupDescription' => 'required|min:20',

        // Challenge rules
        'challengeTitle' => 'required|min:5|max:255',
        'challengeDescription' => 'required|min:20',
        'challengeDifficulty' => 'in:easy,medium,hard',

        // Event rules
        'eventTitle' => 'required|min:5|max:255',
        'eventDescription' => 'required|min:20',
        'eventLocation' => 'required|string',
        'eventStartDate' => 'required|date|after:now',

        // Feedback rules
        'feedbackSubject' => 'required|min:5|max:255',
        'feedbackMessage' => 'required|min:10',
        'feedbackPriority' => 'in:low,medium,high',
    ];

    public function mount()
    {
        $this->loadCommunityStats();
    }

    /**
     * Load all statistics for the dashboard
     */
    private function loadCommunityStats()
    {
        $user = auth()->user();

        $this->stats = [
            'totalMembers' => User::where('is_active', true)->count(),
            'activeStudyGroups' => CommunityActivity::studyGroups()->active()->count(),
            'activeChallenges' => CommunityActivity::codeChallenges()->active()->count(),
            'upcomingEvents' => CommunityActivity::liveEvents()
                ->where('start_date', '>', now())
                ->where('start_date', '<=', now()->addDays(30))
                ->count(),
            'totalThreads' => ForumThread::count(),
            'userLevel' => $user->getOrCreateGamificationData()->level ?? 1,
            'userStreak' => $user->getOrCreateGamificationData()->current_streak ?? 0,
        ];
    }

    /**
     * Set active tab and close any open modals
     */
    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->activeModal = null;
        $this->resetPage();
        $this->resetForm();
    }

    /**
     * Open a modal for creation/editing
     */
    public function openModal($modal, $item = null)
    {
        $this->activeModal = $modal;
        $this->selectedItem = $item;
        if ($item) {
            $this->populateFormFromItem($item);
        }
    }

    /**
     * Close the current modal
     */
    public function closeModal()
    {
        $this->activeModal = null;
        $this->selectedItem = null;
        $this->resetForm();
    }

    /**
     * Reset form fields
     */
    private function resetForm()
    {
        $this->reset([
            'title',
            'content',
            'category',
            'groupTitle',
            'groupDescription',
            'groupStartDate',
            'groupEndDate',
            'groupMaxParticipants',
            'groupTags',
            'challengeTitle',
            'challengeDescription',
            'challengeDifficulty',
            'challengePoints',
            'eventTitle',
            'eventDescription',
            'eventLocation',
            'eventStartDate',
            'eventEndDate',
            'eventMaxParticipants',
            'eventTags',
            'feedbackCategory',
            'feedbackSubject',
            'feedbackMessage',
            'feedbackPriority',
            'code',
            'language'
        ]);
    }

    /**
     * Populate form when editing existing item
     */
    private function populateFormFromItem($item)
    {
        if (isset($item['title']))
            $this->title = $item['title'];
        if (isset($item['description']))
            $this->content = $item['description'];
    }

    // ===== FORUM THREAD ACTIONS =====

    public function createThread()
    {
        $this->validate([
            'title' => 'required|min:5|max:255',
            'content' => 'required|min:10',
            'category' => 'required|string',
        ]);

        try {
            ForumThread::create([
                'user_id' => auth()->id(),
                'title' => $this->title,
                'content' => $this->content,
                'category' => $this->category,
                'last_activity_at' => now(),
                'last_reply_user_id' => auth()->id(),
            ]);

            $this->closeModal();
            session()->flash('message', 'Discussion created successfully!');
            $this->loadCommunityStats();
        } catch (\Exception $e) {
            session()->flash('error', 'Error creating discussion: ' . $e->getMessage());
        }
    }

    
public function openCodeChallenge($challengeId)
{
    $this->selectedItem = ['id' => $challengeId];
    $this->activeModal = 'code-challenge';
    $this->populateFormFromItem($this->selectedItem);
}

    // ===== STUDY GROUP ACTIONS =====

    public function createStudyGroup()
    {
        $this->validate([
            'groupTitle' => 'required|min:5|max:255',
            'groupDescription' => 'required|min:20',
        ]);

        try {
            $tags = $this->groupTags ? array_map('trim', explode(',', $this->groupTags)) : [];

            CommunityActivity::create([
                'creator_id' => auth()->id(),
                'type' => 'study_group',
                'title' => $this->groupTitle,
                'description' => $this->groupDescription,
                'tags' => $tags,
                'max_participants' => $this->groupMaxParticipants ?: null,
                'start_date' => $this->groupStartDate ?: now(),
                'end_date' => $this->groupEndDate ?: null,
                'status' => 'active',
                'participants_count' => 1,
            ]);

            // Add creator as participant
            ActivityParticipant::create([
                'activity_id' => CommunityActivity::latest()->first()->id,
                'user_id' => auth()->id(),
                'status' => 'joined',
                'joined_at' => now(),
            ]);

            $this->closeModal();
            session()->flash('message', 'Study group created successfully!');
            $this->loadCommunityStats();
        } catch (\Exception $e) {
            session()->flash('error', 'Error creating study group: ' . $e->getMessage());
        }
    }

    public function joinStudyGroup($groupId)
    {
        $group = CommunityActivity::findOrFail($groupId);

        if ($group->join(auth()->id())) {
            session()->flash('message', 'Joined group successfully!');
            $this->loadCommunityStats();
        } else {
            session()->flash('error', 'Unable to join group.');
        }
    }

    public function leaveStudyGroup($groupId)
    {
        $group = CommunityActivity::findOrFail($groupId);

        if ($group->leave(auth()->id())) {
            session()->flash('message', 'Left the group.');
            $this->loadCommunityStats();
        }
    }

    // ===== CODE CHALLENGE ACTIONS =====

    public function createChallenge()
    {
        $this->validate([
            'challengeTitle' => 'required|min:5|max:255',
            'challengeDescription' => 'required|min:20',
            'challengeDifficulty' => 'required|in:easy,medium,hard',
        ]);

        try {
            CommunityActivity::create([
                'creator_id' => auth()->id(),
                'type' => 'code_challenge',
                'title' => $this->challengeTitle,
                'description' => $this->challengeDescription,
                'metadata' => [
                    'difficulty' => $this->challengeDifficulty,
                    'points' => $this->challengePoints ?? 100,
                ],
                'status' => 'active',
            ]);

            $this->closeModal();
            session()->flash('message', 'Code challenge created!');
            $this->loadCommunityStats();
        } catch (\Exception $e) {
            session()->flash('error', 'Error creating challenge: ' . $e->getMessage());
        }
    }

    public function submitCodeSolution()
    {
        $this->validate([
            'code' => 'required|min:10',
            'language' => 'required|string',
        ]);

        if (!$this->selectedItem) {
            session()->flash('error', 'No challenge selected.');
            return;
        }

        try {
            $challenge = CommunityActivity::findOrFail($this->selectedItem['id'] ?? null);

            ActivityParticipant::updateOrCreate(
                [
                    'activity_id' => $challenge->id,
                    'user_id' => auth()->id(),
                ],
                [
                    'status' => 'completed',
                    'completed_at' => now(),
                    'score' => 100,
                    'submission_data' => [
                        'language' => $this->language,
                        'code' => $this->code,
                    ],
                ]
            );

            $this->closeModal();
            session()->flash('message', 'Solution submitted!');
            $this->loadCommunityStats();
        } catch (\Exception $e) {
            session()->flash('error', 'Error submitting solution: ' . $e->getMessage());
        }
    }

    // ===== LIVE EVENT ACTIONS =====

    public function createLiveEvent()
    {
        $this->validate([
            'eventTitle' => 'required|min:5|max:255',
            'eventDescription' => 'required|min:20',
            'eventLocation' => 'required|string',
            'eventStartDate' => 'required|date|after:now',
        ]);

        try {
            $tags = $this->eventTags ? array_map('trim', explode(',', $this->eventTags)) : [];

            CommunityActivity::create([
                'creator_id' => auth()->id(),
                'type' => 'live_event',
                'title' => $this->eventTitle,
                'description' => $this->eventDescription,
                'location' => $this->eventLocation,
                'tags' => $tags,
                'start_date' => $this->eventStartDate,
                'end_date' => $this->eventEndDate ?: null,
                'max_participants' => $this->eventMaxParticipants ?: null,
                'status' => 'active',
            ]);

            $this->closeModal();
            session()->flash('message', 'Live event created!');
            $this->loadCommunityStats();
        } catch (\Exception $e) {
            session()->flash('error', 'Error creating event: ' . $e->getMessage());
        }
    }

    public function registerForEvent($eventId)
    {
        $event = CommunityActivity::findOrFail($eventId);

        if ($event->join(auth()->id())) {
            session()->flash('message', 'Registered for event!');
            $this->loadCommunityStats();
        } else {
            session()->flash('error', 'Unable to register for event.');
        }
    }

    public function unregisterFromEvent($eventId)
    {
        $event = CommunityActivity::findOrFail($eventId);

        if ($event->leave(auth()->id())) {
            session()->flash('message', 'Unregistered from event.');
            $this->loadCommunityStats();
        }
    }

    // ===== FEEDBACK ACTIONS =====

    public function submitFeedback()
    {
        $this->validate([
            'feedbackSubject' => 'required|min:5|max:255',
            'feedbackMessage' => 'required|min:10',
            'feedbackCategory' => 'required|string',
            'feedbackPriority' => 'required|in:low,medium,high',
        ]);

        try {
            CommunityFeedback::create([
                'user_id' => auth()->id(),
                'subject' => $this->feedbackSubject,
                'message' => $this->feedbackMessage,
                'category' => $this->feedbackCategory,
                'priority' => $this->feedbackPriority,
                'status' => 'open',
            ]);

            $this->closeModal();
            session()->flash('message', 'Feedback submitted. Thank you!');
        } catch (\Exception $e) {
            session()->flash('error', 'Error submitting feedback: ' . $e->getMessage());
        }
    }

    // ===== DATA RETRIEVAL =====

    public function getThreads()
    {
        return ForumThread::with(['user', 'lastReplyUser'])
            ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->orderBy('created_at', 'desc')
            ->paginate(15);
    }

    public function getStudyGroups()
    {
        return CommunityActivity::studyGroups()
            ->with(['creator', 'activeParticipants.user'])
            ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->statusFilter !== 'all', fn($q) => $q->where('status', $this->statusFilter))
            ->orderBy('created_at', 'desc')
            ->paginate(12);
    }

    public function getChallenges()
    {
        return CommunityActivity::codeChallenges()
            ->with(['creator', 'activeParticipants'])
            ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->difficultyFilter !== 'all', fn($q) => $q->whereJsonContains('metadata->difficulty', $this->difficultyFilter))
            ->orderBy('created_at', 'desc')
            ->paginate(12);
    }

    public function getEvents()
    {
        return CommunityActivity::liveEvents()
            ->with(['creator', 'activeParticipants.user'])
            ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->timeFilter === 'upcoming', fn($q) => $q->where('start_date', '>', now()))
            ->when($this->timeFilter === 'ongoing', fn($q) => $q->where('start_date', '<=', now())->where('end_date', '>=', now()))
            ->when($this->timeFilter === 'past', fn($q) => $q->where('end_date', '<', now()))
            ->orderBy('start_date', 'asc')
            ->paginate(12);
    }

    public function getFeedback()
    {
        return CommunityFeedback::with(['user'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    #[On('refresh-stats')]
    public function refreshStats()
    {
        $this->loadCommunityStats();
    }

    // ===== MODERATION METHODS =====

    public function setModerationTab($tab)
    {
        $this->moderationTab = $tab;
        $this->resetPage();
    }

    public function reviewReport($reportId)
    {
        try {
            $report = \App\Models\Community\CommunityReport::findOrFail($reportId);
            $report->update([
                'status' => 'reviewed',
                'moderator_id' => auth()->id(),
            ]);
            session()->flash('message', 'Report marked as reviewed.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error reviewing report: ' . $e->getMessage());
        }
    }

    public function dismissReport($reportId)
    {
        try {
            $report = \App\Models\Community\CommunityReport::findOrFail($reportId);
            $report->dismiss('Dismissed by moderator');
            session()->flash('message', 'Report dismissed.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error dismissing report: ' . $e->getMessage());
        }
    }

    public function assignFeedback($feedbackId)
    {
        try {
            $feedback = \App\Models\Community\CommunityFeedback::findOrFail($feedbackId);
            $feedback->update([
                'status' => 'in_progress',
                'assigned_to' => auth()->id(),
            ]);
            session()->flash('message', 'Feedback assigned to you.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error assigning feedback: ' . $e->getMessage());
        }
    }

    public function resolveFeedback($feedbackId)
    {
        try {
            $feedback = \App\Models\Community\CommunityFeedback::findOrFail($feedbackId);
            $feedback->update([
                'status' => 'resolved',
                'responded_at' => now(),
            ]);
            session()->flash('message', 'Feedback resolved.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error resolving feedback: ' . $e->getMessage());
        }
    }

    public function respondToFeedback($feedbackId)
    {
        // This would typically open another modal for response
        session()->flash('message', 'Response functionality would be implemented here.');
    }

    public function unflagThread($threadId)
    {
        try {
            $thread = \App\Models\Forum\ForumThread::findOrFail($threadId);
            $thread->update(['is_flagged' => false]);
            session()->flash('message', 'Thread unflagged and approved.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error unflagging thread: ' . $e->getMessage());
        }
    }

    public function removeThread($threadId)
    {
        try {
            $thread = \App\Models\Forum\ForumThread::findOrFail($threadId);
            $thread->delete();
            session()->flash('message', 'Thread removed successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error removing thread: ' . $e->getMessage());
        }
    }

    public function blockUser($userId)
    {
        try {
            $user = \App\Models\User::findOrFail($userId);
            $user->update(['is_active' => false]);
            session()->flash('message', 'User blocked successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error blocking user: ' . $e->getMessage());
        }
    }

    public function unblockUser($userId)
    {
        try {
            $user = \App\Models\User::findOrFail($userId);
            $user->update(['is_active' => true]);
            session()->flash('message', 'User unblocked successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error unblocking user: ' . $e->getMessage());
        }
    }

    // ===== MODERATION DATA RETRIEVAL =====

    public function getCommunityReports()
    {
        return \App\Models\Community\CommunityReport::with(['reporter', 'moderator'])
            ->when($this->reportStatusFilter, fn($q) => $q->where('status', $this->reportStatusFilter))
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function getFeedbackItems()
    {
        return \App\Models\Community\CommunityFeedback::with(['user', 'assignedTo'])
            ->when($this->feedbackStatusFilter, fn($q) => $q->where('status', $this->feedbackStatusFilter))
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function getFlaggedThreads()
    {
        return \App\Models\Forum\ForumThread::with(['user'])
            ->where('is_flagged', true)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function getManagedUsers()
    {
        return \App\Models\User::when($this->userSearch, function ($q) {
            $q->where('name', 'like', "%{$this->userSearch}%")
                ->orWhere('email', 'like', "%{$this->userSearch}%");
        })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }
    public function render()
    {
        $user = auth()->user();

        $data = [
            'stats' => $this->stats,
            'activeTab' => $this->activeTab,
            'activeModal' => $this->activeModal,
            'isAdmin' => $user->isAcademyAdmin() || $user->isSuperAdmin(),
        ];

        // Load data based on active tab
        match ($this->activeTab) {
            'forums' => $data['threads'] = $this->getThreads(),
            'study-groups' => $data['studyGroups'] = $this->getStudyGroups(),
            'code-challenges' => $data['challenges'] = $this->getChallenges(),
            'live-events' => $data['events'] = $this->getEvents(),
            'feedback' => $data['feedback'] = $this->getFeedback(),
            'moderation' => $this->loadModerationData($data),
            default => null,
        };

        return view('livewire.community.community-center', $data);
    }

    private function loadModerationData(&$data)
    {
        $data['moderationTab'] = $this->moderationTab;
        $data['pendingReports'] = \App\Models\Community\CommunityReport::where('status', 'pending')->count();
        $data['pendingFeedback'] = \App\Models\Community\CommunityFeedback::where('status', 'open')->count();
        $data['flaggedThreads'] = \App\Models\Forum\ForumThread::where('is_flagged', true)->count();
        $data['blockedUsers'] = \App\Models\User::where('is_active', false)->count();

        match ($this->moderationTab) {
            'reports' => $data['communityReports'] = $this->getCommunityReports(),
            'feedback' => $data['feedbackItems'] = $this->getFeedbackItems(),
            'flagged' => $data['flaggedThreadsList'] = $this->getFlaggedThreads(),
            'users' => $data['managedUsers'] = $this->getManagedUsers(),
            default => null,
        };
    }
}