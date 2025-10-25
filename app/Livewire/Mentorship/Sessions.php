<?php

namespace App\Livewire\Mentorship;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Mentorship\MentorshipSession;
use App\Models\Mentorship\Mentorship;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

#[Layout('layouts.dashboard', [
    'title' => 'Sessions', 
    'description' => 'Manage Your Mentorship Sessions', 
    'icon' => 'fas fa-calendar-check', 
    'active' => 'mentorship'
])]
class Sessions extends Component
{
    use WithPagination;

    public $dateFilter = 'upcoming';
    public $statusFilter = '';
    public $showSessionModal = false;
    public $selectedSession = null;

    // Create session form
    public $mentorshipId = null;
    public $sessionTitle = '';
    public $sessionDescription = '';
    public $sessionType = 'general';
    public $scheduledAt = '';
    public $duration = 60;
    public $agenda = '';
    public $meetingLink = '';

    protected $rules = [
        'sessionTitle' => 'required|string|max:255',
        'sessionDescription' => 'required|string|min:20',
        'sessionType' => 'required|in:general,code_review,project_guidance,career_advice,mock_interview',
        'scheduledAt' => 'required|date|after:now',
        'duration' => 'required|integer|min:15|max:240'
    ];

    protected $queryString = [
        'dateFilter' => ['except' => 'upcoming'],
        'statusFilter' => ['except' => '']
    ];

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['dateFilter', 'statusFilter'])) {
            $this->resetPage();
        }
    }

    public function createSession($mentorshipId)
    {
        $mentorship = Mentorship::find($mentorshipId);
        
        if (!$mentorship || !$mentorship->isActive()) {
            session()->flash('error', 'Cannot create session for inactive mentorship.');
            return;
        }

        $this->mentorshipId = $mentorshipId;
        $this->showSessionModal = true;
    }

    public function submitSession()
    {
        $this->validate();

        $mentorship = Mentorship::find($this->mentorshipId);

        MentorshipSession::create([
            'mentorship_id' => $this->mentorshipId,
            'title' => $this->sessionTitle,
            'description' => $this->sessionDescription,
            'type' => $this->sessionType,
            'format' => 'video',
            'status' => MentorshipSession::STATUS_SCHEDULED,
            'scheduled_at' => $this->scheduledAt,
            'duration_minutes' => $this->duration,
            'agenda' => $this->agenda,
            'meeting_link' => $this->meetingLink,
            'is_billable' => $mentorship->is_paid,
            'session_cost' => $mentorship->is_paid ? ($mentorship->hourly_rate * ($this->duration / 60)) : 0
        ]);

        $this->resetSessionForm();
        $this->showSessionModal = false;
        
        session()->flash('message', 'Session scheduled successfully!');
        $this->dispatch('session-created');
    }

    public function viewSession($sessionId)
    {
        $this->selectedSession = MentorshipSession::with(['mentorship.mentor', 'mentorship.mentee'])->find($sessionId);
        
        if (!$this->selectedSession) {
            session()->flash('error', 'Session not found.');
            return;
        }

        $this->dispatch('open-session-view', sessionId: $sessionId);
    }

    public function completeSession($sessionId)
    {
        $session = MentorshipSession::find($sessionId);
        
        if (!$session || !$this->canManageSession($session)) {
            session()->flash('error', 'You cannot complete this session.');
            return;
        }

        $this->dispatch('open-session-completion', sessionId: $sessionId);
    }

    private function canManageSession($session)
    {
        $mentorship = $session->mentorship;
        $user = Auth::user();
        
        return $user->id === $mentorship->mentor_id || 
               $user->id === $mentorship->mentee_id ||
               $user->isAcademyAdmin() ||
               $user->isSuperAdmin();
    }

    public function resetSessionForm()
    {
        $this->reset([
            'mentorshipId', 'sessionTitle', 'sessionDescription', 
            'sessionType', 'scheduledAt', 'duration', 'agenda', 'meetingLink'
        ]);
        $this->sessionType = 'general';
        $this->duration = 60;
    }

    public function closeModal()
    {
        $this->showSessionModal = false;
        $this->selectedSession = null;
        $this->resetSessionForm();
    }

    #[On('session-created')]
    #[On('session-updated')]
    #[On('session-completed')]
    public function refreshSessions()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();
        
        $query = MentorshipSession::with(['mentorship.mentor', 'mentorship.mentee'])
            ->whereHas('mentorship', function($q) use ($user) {
                $q->where('mentor_id', $user->id)->orWhere('mentee_id', $user->id);
            });

        // Date filter
        $now = now();
        switch($this->dateFilter) {
            case 'upcoming':
                $query->where('scheduled_at', '>', $now)
                      ->where('status', MentorshipSession::STATUS_SCHEDULED);
                break;
            case 'past':
                $query->where(function($q) use ($now) {
                    $q->where('scheduled_at', '<', $now)
                      ->orWhere('status', MentorshipSession::STATUS_COMPLETED);
                });
                break;
            case 'this_week':
                $query->whereBetween('scheduled_at', [$now->startOfWeek(), $now->copy()->endOfWeek()]);
                break;
            case 'this_month':
                $query->whereBetween('scheduled_at', [$now->startOfMonth(), $now->copy()->endOfMonth()]);
                break;
        }

        // Status filter
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $sessions = $query->orderBy('scheduled_at', 'desc')->paginate(10);

        // Get active mentorships for session creation
        $activeMentorships = Mentorship::with(['mentor', 'mentee'])
            ->where(function($q) use ($user) {
                $q->where('mentor_id', $user->id)->orWhere('mentee_id', $user->id);
            })
            ->where('status', Mentorship::STATUS_ACTIVE)
            ->get();

        return view('livewire.mentorship.sessions', [
            'sessions' => $sessions,
            'activeMentorships' => $activeMentorships
        ]);
    }
}