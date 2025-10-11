<?php

namespace App\Livewire\Mentorship;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MentorProfile;
use App\Models\Mentorship;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', [
    'title' => 'Mentor Management', 
    'description' => 'Manage Mentors and Applications', 
    'icon' => 'fas fa-user-tie', 
    'active' => 'mentorship'
])]
class MentorManagement extends Component
{
    use WithPagination;

    public $activeTab = 'pending';
    public $searchTerm = '';
    public $statusFilter = '';
    public $experienceFilter = '';
    
    public $selectedMentor = null;
    public $showMentorModal = false;
    public $showRejectionModal = false;
    public $rejectionReason = '';

    protected $queryString = [
        'activeTab' => ['except' => 'pending'],
        'searchTerm' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'experienceFilter' => ['except' => '']
    ];

    public function mount()
    {
        $this->checkAdminAccess();
    }

    private function checkAdminAccess()
    {
        $user = Auth::user();
        if (!$user->isAcademyAdmin() && !$user->isSuperAdmin()) {
            abort(403, 'You do not have permission to access this page.');
        }
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['searchTerm', 'statusFilter', 'experienceFilter'])) {
            $this->resetPage();
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function viewMentor($mentorId)
    {
        $this->selectedMentor = MentorProfile::with(['user'])->find($mentorId);
        $this->showMentorModal = true;
    }

    public function approveMentor($mentorId)
    {
        $profile = MentorProfile::find($mentorId);
        
        if (!$profile) {
            session()->flash('error', 'Mentor profile not found.');
            return;
        }

        $profile->update([
            'is_verified' => true,
            'verified_at' => now(),
            'is_available' => true
        ]);

        // Notify mentor
        $profile->user->notify(new \App\Notifications\MentorApplicationApproved($profile));

        session()->flash('message', 'Mentor approved successfully!');
        $this->closeModal();
    }

    public function rejectMentor($mentorId)
    {
        $this->selectedMentor = MentorProfile::find($mentorId);
        $this->showRejectionModal = true;
    }

    public function submitRejection()
    {
        $this->validate([
            'rejectionReason' => 'required|string|min:20|max:500'
        ]);

        if (!$this->selectedMentor) {
            session()->flash('error', 'Mentor profile not found.');
            return;
        }

        // Update profile metadata
        $this->selectedMentor->update([
            'metadata' => array_merge($this->selectedMentor->metadata ?? [], [
                'rejected_at' => now()->toIso8601String(),
                'rejection_reason' => $this->rejectionReason,
                'rejected_by' => Auth::id()
            ])
        ]);

        // Notify mentor
        $this->selectedMentor->user->notify(
            new \App\Notifications\MentorApplicationRejected($this->selectedMentor, $this->rejectionReason)
        );

        // Delete the profile
        $this->selectedMentor->delete();

        session()->flash('message', 'Mentor application rejected.');
        $this->closeModal();
        $this->rejectionReason = '';
    }

    public function suspendMentor($mentorId)
    {
        $profile = MentorProfile::find($mentorId);
        
        if (!$profile) {
            session()->flash('error', 'Mentor profile not found.');
            return;
        }

        $profile->update([
            'is_available' => false,
            'metadata' => array_merge($profile->metadata ?? [], [
                'suspended_at' => now()->toIso8601String(),
                'suspended_by' => Auth::id()
            ])
        ]);

        // Notify mentor
        $profile->user->notify(new \App\Notifications\MentorSuspended($profile));

        session()->flash('message', 'Mentor suspended successfully.');
    }

    public function reactivateMentor($mentorId)
    {
        $profile = MentorProfile::find($mentorId);
        
        if (!$profile) {
            session()->flash('error', 'Mentor profile not found.');
            return;
        }

        $profile->update([
            'is_available' => true,
            'metadata' => array_merge($profile->metadata ?? [], [
                'reactivated_at' => now()->toIso8601String(),
                'reactivated_by' => Auth::id()
            ])
        ]);

        // Notify mentor
        $profile->user->notify(new \App\Notifications\MentorReactivated($profile));

        session()->flash('message', 'Mentor reactivated successfully.');
    }

    public function closeModal()
    {
        $this->showMentorModal = false;
        $this->showRejectionModal = false;
        $this->selectedMentor = null;
    }

    public function render()
    {
        $query = MentorProfile::with(['user']);

        // Tab filter
        switch($this->activeTab) {
            case 'pending':
                $query->where('is_verified', false);
                break;
            case 'active':
                $query->where('is_verified', true)->where('is_available', true);
                break;
            case 'inactive':
                $query->where('is_verified', true)->where('is_available', false);
                break;
            case 'all':
                // No filter
                break;
        }

        // Search filter
        if ($this->searchTerm) {
            $query->whereHas('user', function($q) {
                $q->where('name', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $this->searchTerm . '%');
            });
        }

        // Experience filter
        if ($this->experienceFilter) {
            $query->where('experience_level', $this->experienceFilter);
        }

        $mentors = $query->orderBy('created_at', 'desc')->paginate(15);

        // Statistics
        $stats = [
            'total' => MentorProfile::count(),
            'pending' => MentorProfile::where('is_verified', false)->count(),
            'active' => MentorProfile::where('is_verified', true)->where('is_available', true)->count(),
            'inactive' => MentorProfile::where('is_verified', true)->where('is_available', false)->count(),
            'total_mentorships' => Mentorship::count(),
            'active_mentorships' => Mentorship::where('status', Mentorship::STATUS_ACTIVE)->count(),
        ];

        return view('livewire.mentorship.mentor-management', [
            'mentors' => $mentors,
            'stats' => $stats
        ]);
    }
}