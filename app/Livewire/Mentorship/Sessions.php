<?php

namespace App\Livewire\Mentorship;

use App\Models\Mentorship\Mentorship;
use App\Models\Mentorship\MentorshipSession;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard', [
    'title' => 'Mentorship Operations',
    'description' => 'Schedule, track notes, and manage mentorship payouts',
    'icon' => 'fas fa-calendar-check',
    'active' => 'mentorship'
])]
class Sessions extends Component
{
    use WithPagination;

    public string $dateFilter = 'upcoming';
    public string $statusFilter = '';
    public bool $showSessionModal = false;
    public bool $showCompletionModal = false;
    public bool $showDetailsModal = false;
    public ?int $editingSessionId = null;
    public ?int $selectedSessionId = null;

    public ?int $mentorshipId = null;
    public string $sessionTitle = '';
    public string $sessionDescription = '';
    public string $sessionType = MentorshipSession::TYPE_GENERAL;
    public string $sessionFormat = 'video';
    public string $scheduledAt = '';
    public int $duration = 60;
    public string $agenda = '';
    public string $meetingLink = '';

    public string $sessionNotes = '';
    public array $actionItems = [''];
    public string $mentorFeedback = '';
    public string $menteeFeedback = '';
    public ?float $mentorRating = null;
    public ?float $menteeRating = null;
    public int $actualDuration = 60;

    protected $queryString = [
        'dateFilter' => ['except' => 'upcoming'],
        'statusFilter' => ['except' => ''],
    ];

    public function updated($propertyName): void
    {
        if (in_array($propertyName, ['dateFilter', 'statusFilter'], true)) {
            $this->resetPage();
        }
    }

    public function createSession(?int $mentorshipId = null): void
    {
        $this->resetSessionForm();

        if ($mentorshipId) {
            $mentorship = $this->findManageableMentorship($mentorshipId);

            if (! $mentorship || ! $mentorship->isActive()) {
                session()->flash('error', 'Cannot schedule a session for this mentorship.');
                return;
            }

            $this->mentorshipId = $mentorship->id;
            $this->duration = 60;
            $this->scheduledAt = $this->suggestedStartFor($mentorship)->format('Y-m-d\TH:i');
            $this->sessionTitle = 'Mentorship session with ' . $mentorship->mentee->name;
        } else {
            $this->scheduledAt = now()->addDay()->setTime(10, 0)->format('Y-m-d\TH:i');
        }

        $this->showSessionModal = true;
    }

    public function editSession(int $sessionId): void
    {
        $session = $this->findManageableSession($sessionId);

        if (! $session || $session->status !== MentorshipSession::STATUS_SCHEDULED) {
            session()->flash('error', 'Only scheduled sessions can be edited.');
            return;
        }

        $this->resetSessionForm();
        $this->editingSessionId = $session->id;
        $this->mentorshipId = $session->mentorship_id;
        $this->sessionTitle = $session->title;
        $this->sessionDescription = $session->description ?? '';
        $this->sessionType = $session->type;
        $this->sessionFormat = $session->format;
        $this->scheduledAt = $session->scheduled_at->format('Y-m-d\TH:i');
        $this->duration = $session->duration_minutes ?? 60;
        $this->agenda = $session->agenda ?? '';
        $this->meetingLink = $session->meeting_link ?? '';
        $this->showSessionModal = true;
    }

    public function submitSession(): void
    {
        $this->validate($this->sessionRules());

        $mentorship = $this->findManageableMentorship((int) $this->mentorshipId);

        if (! $mentorship || ! $mentorship->isActive()) {
            session()->flash('error', 'Choose an active mentorship before scheduling.');
            return;
        }

        $start = Carbon::parse($this->scheduledAt);

        if (! $this->mentorIsAvailable($mentorship, $start, $this->duration)) {
            session()->flash('error', 'That time is outside the mentor availability window.');
            return;
        }

        if ($this->hasSchedulingConflict($mentorship, $start, $this->duration, $this->editingSessionId)) {
            session()->flash('error', 'That time conflicts with an existing mentorship session.');
            return;
        }

        $sessionData = [
            'mentorship_id' => $mentorship->id,
            'title' => $this->sessionTitle,
            'description' => $this->sessionDescription,
            'type' => $this->sessionType,
            'format' => $this->sessionFormat,
            'status' => MentorshipSession::STATUS_SCHEDULED,
            'scheduled_at' => $start,
            'duration_minutes' => $this->duration,
            'agenda' => $this->agenda,
            'meeting_link' => $this->meetingLink,
            'is_billable' => $mentorship->is_paid,
            'session_cost' => $mentorship->is_paid ? round((float) $mentorship->hourly_rate * ($this->duration / 60), 2) : 0,
            'payment_status' => $mentorship->is_paid ? 'pending' : 'free',
            'metadata' => [
                'scheduled_by' => Auth::id(),
                'scheduled_from' => $this->editingSessionId ? 'reschedule' : 'booking',
            ],
        ];

        if ($this->editingSessionId) {
            $session = $this->findManageableSession($this->editingSessionId);
            $session?->update($sessionData);
            session()->flash('message', 'Session updated successfully.');
        } else {
            MentorshipSession::create($sessionData);
            session()->flash('message', 'Session booked successfully.');
        }

        $this->closeModal();
        $this->dispatch('session-updated');
    }

    public function viewSession(int $sessionId): void
    {
        $session = $this->findViewableSession($sessionId);

        if (! $session) {
            session()->flash('error', 'Session not found.');
            return;
        }

        $this->selectedSessionId = $session->id;
        $this->showDetailsModal = true;
    }

    public function startSession(int $sessionId): void
    {
        $session = $this->findOperableSession($sessionId);

        if (! $session || $session->status !== MentorshipSession::STATUS_SCHEDULED) {
            session()->flash('error', 'This session cannot be started.');
            return;
        }

        $session->start();
        session()->flash('message', 'Session marked as in progress.');
        $this->dispatch('session-updated');
    }

    public function completeSession(int $sessionId): void
    {
        $session = $this->findOperableSession($sessionId);

        if (! $session || ! in_array($session->status, [MentorshipSession::STATUS_SCHEDULED, MentorshipSession::STATUS_IN_PROGRESS], true)) {
            session()->flash('error', 'This session cannot be completed.');
            return;
        }

        $this->resetCompletionForm();
        $this->selectedSessionId = $session->id;
        $this->sessionNotes = $session->session_notes ?? '';
        $this->actionItems = $session->action_items ?: [''];
        $this->mentorFeedback = $session->mentor_feedback ?? '';
        $this->menteeFeedback = $session->mentee_feedback ?? '';
        $this->mentorRating = $session->mentor_rating ? (float) $session->mentor_rating : null;
        $this->menteeRating = $session->mentee_rating ? (float) $session->mentee_rating : null;
        $this->actualDuration = $session->duration_minutes ?? 60;
        $this->showCompletionModal = true;
    }

    public function submitCompletion(): void
    {
        $this->validate([
            'sessionNotes' => 'required|string|min:20|max:5000',
            'actionItems' => 'nullable|array|max:12',
            'actionItems.*' => 'nullable|string|max:255',
            'mentorFeedback' => 'nullable|string|max:3000',
            'menteeFeedback' => 'nullable|string|max:3000',
            'mentorRating' => 'nullable|numeric|min:1|max:5',
            'menteeRating' => 'nullable|numeric|min:1|max:5',
            'actualDuration' => 'required|integer|min:15|max:360',
        ]);

        $session = $this->findOperableSession((int) $this->selectedSessionId);

        if (! $session) {
            session()->flash('error', 'Session not found.');
            return;
        }

        $session->completeWithOperations([
            'duration_minutes' => $this->actualDuration,
            'session_notes' => $this->sessionNotes,
            'action_items' => array_values(array_filter($this->actionItems)),
            'mentor_feedback' => $this->mentorFeedback,
            'mentee_feedback' => $this->menteeFeedback,
            'mentor_rating' => $this->mentorRating,
            'mentee_rating' => $this->menteeRating,
            'metadata' => [
                'completed_by' => Auth::id(),
                'completed_from' => 'mentorship_operations',
            ],
        ]);

        session()->flash('message', $session->is_billable ? 'Session completed and mentor payout was credited.' : 'Session completed successfully.');
        $this->closeModal();
        $this->dispatch('session-completed');
    }

    public function payoutSession(int $sessionId): void
    {
        $session = $this->findOperableSession($sessionId);

        if (! $session || $session->status !== MentorshipSession::STATUS_COMPLETED) {
            session()->flash('error', 'Only completed sessions can be paid out.');
            return;
        }

        $transaction = $session->payoutMentorIfNeeded();
        session()->flash('message', $transaction ? 'Mentor payout credited.' : 'No payout was needed for this session.');
        $this->dispatch('session-updated');
    }

    public function cancelSession(int $sessionId): void
    {
        $session = $this->findOperableSession($sessionId);

        if (! $session || $session->status !== MentorshipSession::STATUS_SCHEDULED) {
            session()->flash('error', 'Only scheduled sessions can be cancelled.');
            return;
        }

        $metadata = $session->metadata ?? [];
        data_set($metadata, 'cancelled_by', Auth::id());
        data_set($metadata, 'cancelled_at', now()->toIso8601String());

        $session->update([
            'status' => MentorshipSession::STATUS_CANCELLED,
            'metadata' => $metadata,
        ]);

        session()->flash('message', 'Session cancelled.');
        $this->dispatch('session-updated');
    }

    public function addActionItem(): void
    {
        $this->actionItems[] = '';
    }

    public function removeActionItem(int $index): void
    {
        unset($this->actionItems[$index]);
        $this->actionItems = array_values($this->actionItems ?: ['']);
    }

    public function closeModal(): void
    {
        $this->showSessionModal = false;
        $this->showCompletionModal = false;
        $this->showDetailsModal = false;
        $this->selectedSessionId = null;
        $this->resetSessionForm();
        $this->resetCompletionForm();
    }

    #[On('session-updated')]
    #[On('session-completed')]
    public function refreshSessions(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();
        $sessions = $this->sessionQuery($user)
            ->with(['mentorship.mentor.mentorProfile', 'mentorship.mentee'])
            ->orderByRaw("CASE WHEN status = 'scheduled' AND scheduled_at >= NOW() THEN 0 ELSE 1 END")
            ->orderBy('scheduled_at', $this->dateFilter === 'past' ? 'desc' : 'asc')
            ->paginate(10);

        $activeMentorships = $this->activeMentorships($user);
        $selectedSession = $this->selectedSessionId
            ? MentorshipSession::with(['mentorship.mentor.mentorProfile', 'mentorship.mentee'])->find($this->selectedSessionId)
            : null;

        return view('livewire.mentorship.sessions', [
            'sessions' => $sessions,
            'activeMentorships' => $activeMentorships,
            'selectedSession' => $selectedSession,
            'stats' => $this->operationStats($user),
            'sessionTypes' => $this->sessionTypes(),
            'sessionFormats' => $this->sessionFormats(),
        ]);
    }

    private function sessionRules(): array
    {
        return [
            'mentorshipId' => 'required|integer|exists:mentorships,id',
            'sessionTitle' => 'required|string|max:255',
            'sessionDescription' => 'required|string|min:10|max:2000',
            'sessionType' => 'required|in:general,code_review,project_guidance,career_advice,mock_interview',
            'sessionFormat' => 'required|in:video,audio,screen_share,text',
            'scheduledAt' => 'required|date|after:now',
            'duration' => 'required|integer|min:15|max:240',
            'agenda' => 'nullable|string|max:3000',
            'meetingLink' => 'nullable|url|max:500',
        ];
    }

    private function sessionQuery($user)
    {
        $query = MentorshipSession::query();

        if (! $this->canManageAll($user)) {
            $query->whereHas('mentorship', fn ($q) => $q
                ->where('mentor_id', $user->id)
                ->orWhere('mentee_id', $user->id));
        }

        $now = now();
        match ($this->dateFilter) {
            'past' => $query->where(function ($q) use ($now) {
                $q->where('scheduled_at', '<', $now)
                    ->orWhereIn('status', [MentorshipSession::STATUS_COMPLETED, MentorshipSession::STATUS_CANCELLED, MentorshipSession::STATUS_MISSED]);
            }),
            'this_week' => $query->whereBetween('scheduled_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()]),
            'this_month' => $query->whereBetween('scheduled_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()]),
            default => $query->where('scheduled_at', '>=', $now->copy()->subHour())
                ->whereIn('status', [MentorshipSession::STATUS_SCHEDULED, MentorshipSession::STATUS_IN_PROGRESS]),
        };

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        return $query;
    }

    private function operationStats($user): array
    {
        $base = MentorshipSession::query();

        if (! $this->canManageAll($user)) {
            $base->whereHas('mentorship', fn ($q) => $q
                ->where('mentor_id', $user->id)
                ->orWhere('mentee_id', $user->id));
        }

        return [
            'upcoming' => (clone $base)->where('status', MentorshipSession::STATUS_SCHEDULED)->where('scheduled_at', '>', now())->count(),
            'needs_notes' => (clone $base)->whereIn('status', [MentorshipSession::STATUS_SCHEDULED, MentorshipSession::STATUS_IN_PROGRESS])->where('scheduled_at', '<', now())->count(),
            'completed' => (clone $base)->where('status', MentorshipSession::STATUS_COMPLETED)->count(),
            'pending_payout' => (clone $base)->where('status', MentorshipSession::STATUS_COMPLETED)->where('is_billable', true)->where(function ($q) {
                $q->whereNull('payment_status')->orWhere('payment_status', '!=', 'paid');
            })->sum('session_cost'),
        ];
    }

    private function activeMentorships($user): Collection
    {
        return Mentorship::with(['mentor.mentorProfile', 'mentee'])
            ->where('status', Mentorship::STATUS_ACTIVE)
            ->when(! $this->canManageAll($user), fn ($q) => $q->where(fn ($nested) => $nested->where('mentor_id', $user->id)->orWhere('mentee_id', $user->id)))
            ->orderBy('started_at', 'desc')
            ->get();
    }

    private function findManageableMentorship(int $mentorshipId): ?Mentorship
    {
        $user = Auth::user();

        return Mentorship::with(['mentor.mentorProfile', 'mentee'])
            ->whereKey($mentorshipId)
            ->when(! $this->canManageAll($user), fn ($q) => $q->where(fn ($nested) => $nested->where('mentor_id', $user->id)->orWhere('mentee_id', $user->id)))
            ->first();
    }

    private function findViewableSession(int $sessionId): ?MentorshipSession
    {
        $user = Auth::user();

        return MentorshipSession::with(['mentorship.mentor.mentorProfile', 'mentorship.mentee'])
            ->whereKey($sessionId)
            ->when(! $this->canManageAll($user), fn ($q) => $q->whereHas('mentorship', fn ($nested) => $nested->where('mentor_id', $user->id)->orWhere('mentee_id', $user->id)))
            ->first();
    }

    private function findManageableSession(int $sessionId): ?MentorshipSession
    {
        return $this->findViewableSession($sessionId);
    }

    private function findOperableSession(int $sessionId): ?MentorshipSession
    {
        $user = Auth::user();

        return MentorshipSession::with(['mentorship.mentor.mentorProfile', 'mentorship.mentee'])
            ->whereKey($sessionId)
            ->when(! $this->canManageAll($user), fn ($q) => $q->whereHas('mentorship', fn ($nested) => $nested->where('mentor_id', $user->id)))
            ->first();
    }

    private function mentorIsAvailable(Mentorship $mentorship, Carbon $start, int $duration): bool
    {
        return $mentorship->mentor->mentorProfile?->isAvailableForSlot($start, $duration) ?? true;
    }

    private function hasSchedulingConflict(Mentorship $mentorship, Carbon $start, int $duration, ?int $exceptSessionId = null): bool
    {
        $end = $start->copy()->addMinutes($duration);

        $sessions = MentorshipSession::with('mentorship')
            ->whereIn('status', [MentorshipSession::STATUS_SCHEDULED, MentorshipSession::STATUS_IN_PROGRESS])
            ->when($exceptSessionId, fn ($q) => $q->whereKeyNot($exceptSessionId))
            ->whereBetween('scheduled_at', [$start->copy()->subDay(), $end->copy()->addDay()])
            ->whereHas('mentorship', function ($q) use ($mentorship) {
                $q->where('mentor_id', $mentorship->mentor_id)
                    ->orWhere('mentee_id', $mentorship->mentor_id)
                    ->orWhere('mentor_id', $mentorship->mentee_id)
                    ->orWhere('mentee_id', $mentorship->mentee_id);
            })
            ->get();

        return $sessions->contains(function (MentorshipSession $session) use ($start, $end) {
            $existingStart = $session->scheduled_at;
            $existingEnd = $existingStart->copy()->addMinutes($session->duration_minutes ?? 60);

            return $start->lt($existingEnd) && $end->gt($existingStart);
        });
    }

    private function suggestedStartFor(Mentorship $mentorship): Carbon
    {
        $schedule = $mentorship->mentor->mentorProfile?->normalizedAvailabilitySchedule() ?? [];

        if (empty($schedule)) {
            return now()->addDay()->setTime(10, 0);
        }

        for ($dayOffset = 0; $dayOffset <= 14; $dayOffset++) {
            $candidateDate = now()->addDays($dayOffset);
            $day = strtolower($candidateDate->format('l'));
            $slot = collect($schedule)->firstWhere('day', $day);

            if ($slot) {
                [$hour, $minute] = array_map('intval', explode(':', $slot['start']));
                $candidate = $candidateDate->copy()->setTime($hour, $minute);

                if ($candidate->isFuture()) {
                    return $candidate;
                }
            }
        }

        return now()->addDay()->setTime(10, 0);
    }

    private function resetSessionForm(): void
    {
        $this->editingSessionId = null;
        $this->mentorshipId = null;
        $this->sessionTitle = '';
        $this->sessionDescription = '';
        $this->sessionType = MentorshipSession::TYPE_GENERAL;
        $this->sessionFormat = 'video';
        $this->scheduledAt = '';
        $this->duration = 60;
        $this->agenda = '';
        $this->meetingLink = '';
    }

    private function resetCompletionForm(): void
    {
        $this->sessionNotes = '';
        $this->actionItems = [''];
        $this->mentorFeedback = '';
        $this->menteeFeedback = '';
        $this->mentorRating = null;
        $this->menteeRating = null;
        $this->actualDuration = 60;
    }

    private function sessionTypes(): array
    {
        return [
            MentorshipSession::TYPE_GENERAL => 'General coaching',
            MentorshipSession::TYPE_CODE_REVIEW => 'Code review',
            MentorshipSession::TYPE_PROJECT_GUIDANCE => 'Project guidance',
            MentorshipSession::TYPE_CAREER_ADVICE => 'Career advice',
            'mock_interview' => 'Mock interview',
        ];
    }

    private function sessionFormats(): array
    {
        return [
            'video' => 'Video call',
            'audio' => 'Audio call',
            'screen_share' => 'Screen share',
            'text' => 'Text chat',
        ];
    }

    private function canManageAll($user): bool
    {
        return $user->isAcademyAdmin() || $user->isSuperAdmin();
    }
}
