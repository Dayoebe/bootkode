<?php

namespace App\Livewire\Mentorship;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Mentorship;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

#[Layout('layouts.dashboard', [
    'title' => 'My Mentorships', 
    'description' => 'Manage Your Mentorships', 
    'icon' => 'fas fa-handshake', 
    'active' => 'mentorship'
])]
class MyMentorships extends Component
{
    use WithPagination;

    public $statusFilter = '';
    public $roleView = 'all'; // all, as_mentor, as_mentee

    protected $queryString = [
        'statusFilter' => ['except' => ''],
        'roleView' => ['except' => 'all']
    ];

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedRoleView()
    {
        $this->resetPage();
    }

    public function acceptMentorship($mentorshipId)
    {
        $mentorship = Mentorship::find($mentorshipId);
        
        if (!$mentorship || $mentorship->mentor_id !== Auth::id()) {
            session()->flash('error', 'Invalid mentorship request.');
            return;
        }

        $profile = Auth::user()->mentorProfile;
        if (!$profile || !$profile->canAcceptMentees()) {
            session()->flash('error', 'You have reached your maximum mentee capacity.');
            return;
        }

        $mentorship->accept();
        $mentorship->mentee->notify(new \App\Notifications\MentorshipAccepted($mentorship));

        session()->flash('message', 'Mentorship request accepted!');
        $this->dispatch('mentorship-updated');
    }

    public function rejectMentorship($mentorshipId, $reason = null)
    {
        $mentorship = Mentorship::find($mentorshipId);
        
        if (!$mentorship || $mentorship->mentor_id !== Auth::id()) {
            session()->flash('error', 'Invalid mentorship request.');
            return;
        }

        $mentorship->reject($reason);
        $mentorship->mentee->notify(new \App\Notifications\MentorshipRejected($mentorship));

        session()->flash('message', 'Mentorship request rejected.');
        $this->dispatch('mentorship-updated');
    }

    public function completeMentorship($mentorshipId)
    {
        $mentorship = Mentorship::find($mentorshipId);
        
        if (!$mentorship || $mentorship->mentor_id !== Auth::id()) {
            session()->flash('error', 'Invalid mentorship.');
            return;
        }

        $mentorship->complete();
        session()->flash('message', 'Mentorship marked as completed!');
        $this->dispatch('mentorship-updated');
    }

    public function cancelMentorship($mentorshipId, $reason = null)
    {
        $mentorship = Mentorship::find($mentorshipId);
        
        $user = Auth::user();
        if (!$mentorship || ($mentorship->mentor_id !== $user->id && $mentorship->mentee_id !== $user->id)) {
            session()->flash('error', 'Invalid mentorship.');
            return;
        }

        $mentorship->update([
            'status' => Mentorship::STATUS_CANCELLED,
            'metadata' => array_merge($mentorship->metadata ?? [], [
                'cancelled_by' => $user->id,
                'cancelled_at' => now()->toIso8601String(),
                'cancellation_reason' => $reason
            ])
        ]);

        // Update mentor's current mentees count
        if ($mentorship->isActive()) {
            $mentorship->mentor->mentorProfile?->decrement('current_mentees');
        }

        // Notify the other party
        if ($user->id === $mentorship->mentor_id) {
            $mentorship->mentee->notify(new \App\Notifications\MentorshipCancelled($mentorship));
        } else {
            $mentorship->mentor->notify(new \App\Notifications\MentorshipCancelled($mentorship));
        }

        session()->flash('message', 'Mentorship cancelled.');
        $this->dispatch('mentorship-updated');
    }

    #[On('mentorship-updated')]
    public function refreshList()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();
        
        $query = Mentorship::with(['mentor.mentorProfile', 'mentee', 'sessions' => function($q) {
            $q->where('scheduled_at', '>', now())->orderBy('scheduled_at')->limit(1);
        }]);

        // Filter by role view
        if ($this->roleView === 'as_mentor') {
            $query->where('mentor_id', $user->id);
        } elseif ($this->roleView === 'as_mentee') {
            $query->where('mentee_id', $user->id);
        } else {
            $query->where(function($q) use ($user) {
                $q->where('mentor_id', $user->id)->orWhere('mentee_id', $user->id);
            });
        }

        // Filter by status
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $mentorships = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.mentorship.my-mentorships', [
            'mentorships' => $mentorships
        ]);
    }
}