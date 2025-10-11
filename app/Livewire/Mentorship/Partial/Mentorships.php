<?php
// FILE: app/Livewire/Mentorship/Partial/Mentorships.php

namespace App\Livewire\Mentorship\Partial;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Mentorship;
use Illuminate\Support\Facades\Auth;

class Mentorships extends Component
{
    use WithPagination;

    public $statusFilter = '';

    public function updatedStatusFilter()
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

    public function render()
    {
        $query = Mentorship::with(['mentee', 'sessions' => function($q) {
            $q->where('scheduled_at', '>', now())->orderBy('scheduled_at')->limit(1);
        }])
        ->where('mentor_id', Auth::id());

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        $mentorships = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.mentorship.partial.mentorships', [
            'mentorships' => $mentorships
        ]);
    }
}