<?php

namespace App\Livewire\Mentorship;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Mentorship\MentorProfile;
use App\Models\Mentorship\Mentorship;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', [
    'title' => 'Find a Mentor', 
    'description' => 'Discover Expert Mentors', 
    'icon' => 'fas fa-search', 
    'active' => 'mentorship'
])]
class FindMentor extends Component
{
    use WithPagination;

    public $searchTerm = '';
    public $viewMode = 'grid';
    public $selectedMentor = null;
    public $showMentorModal = false;
    public $showRequestModal = false;

    // Filters
    public $experienceFilter = '';
    public $availabilityFilter = 'available';
    public $ratingFilter = '';
    public $priceRangeFilter = '';
    public $specializationFilter = '';

    // Request form
    public $requestMessage = '';
    public $goals = [''];
    public $expectations = [''];
    public $durationWeeks = 12;

    protected $rules = [
        'requestMessage' => 'required|string|min:50|max:1000',
        'goals' => 'required|array|min:1',
        'goals.*' => 'required|string|max:255',
        'expectations' => 'required|array|min:1',
        'expectations.*' => 'required|string|max:255',
        'durationWeeks' => 'required|integer|min:4|max:52',
    ];

    protected $queryString = [
        'searchTerm' => ['except' => ''],
        'experienceFilter' => ['except' => ''],
        'availabilityFilter' => ['except' => 'available'],
        'ratingFilter' => ['except' => ''],
        'specializationFilter' => ['except' => ''],
        'priceRangeFilter' => ['except' => '']
    ];

    public function updated($propertyName)
    {
        if (in_array($propertyName, [
            'searchTerm',
            'experienceFilter',
            'availabilityFilter',
            'ratingFilter',
            'specializationFilter',
            'priceRangeFilter'
        ])) {
            $this->resetPage();
        }
    }

    public function selectMentor($mentorId)
    {
        $this->selectedMentor = MentorProfile::with('user')->find($mentorId);
        $this->showMentorModal = true;
    }

    public function requestMentorship($mentorId = null)
    {
        if ($mentorId) {
            $this->selectedMentor = MentorProfile::with('user')->find($mentorId);
        }
        
        $this->showRequestModal = true;
        $this->showMentorModal = false;
    }

    public function submitMentorshipRequest()
    {
        $this->validate();

        // Check if already has active/pending mentorship with this mentor
        $existingMentorship = Mentorship::where('mentor_id', $this->selectedMentor->user_id)
            ->where('mentee_id', Auth::id())
            ->whereIn('status', [Mentorship::STATUS_PENDING, Mentorship::STATUS_ACTIVE])
            ->first();

        if ($existingMentorship) {
            session()->flash('error', 'You already have an active or pending mentorship with this mentor.');
            return;
        }

        $mentorship = Mentorship::create([
            'mentor_id' => $this->selectedMentor->user_id,
            'mentee_id' => Auth::id(),
            'status' => Mentorship::STATUS_PENDING,
            'request_message' => $this->requestMessage,
            'goals' => array_filter($this->goals),
            'expectations' => array_filter($this->expectations),
            'duration_weeks' => $this->durationWeeks,
            'is_paid' => !$this->selectedMentor->offers_free_sessions,
            'hourly_rate' => $this->selectedMentor->hourly_rate,
            'requested_at' => now()
        ]);

        // Send notification
        $this->selectedMentor->user->notify(
            new \App\Notifications\MentorshipRequested($mentorship)
        );

        $this->resetRequestForm();
        $this->showRequestModal = false;
        
        session()->flash('message', 'Mentorship request sent successfully! You will be notified once the mentor responds.');
        
        return redirect()->route('mentorship.my-mentorships');
    }

    public function addGoal()
    {
        $this->goals[] = '';
    }

    public function removeGoal($index)
    {
        unset($this->goals[$index]);
        $this->goals = array_values($this->goals);
    }

    public function addExpectation()
    {
        $this->expectations[] = '';
    }

    public function removeExpectation($index)
    {
        unset($this->expectations[$index]);
        $this->expectations = array_values($this->expectations);
    }

    public function resetRequestForm()
    {
        $this->reset(['requestMessage', 'goals', 'expectations', 'durationWeeks']);
        $this->goals = [''];
        $this->expectations = [''];
        $this->durationWeeks = 12;
    }

    public function closeModal()
    {
        $this->showMentorModal = false;
        $this->showRequestModal = false;
        $this->selectedMentor = null;
    }

    public function render()
    {
        $query = MentorProfile::with(['user'])
            ->where('is_verified', true);

        // Apply filters
        if ($this->searchTerm) {
            $query->whereHas('user', function($q) {
                $q->where('name', 'like', '%' . $this->searchTerm . '%');
            })->orWhereJsonContains('specializations', $this->searchTerm)
              ->orWhereJsonContains('skills', $this->searchTerm);
        }

        if ($this->availabilityFilter === 'available') {
            $query->available();
        }

        if ($this->experienceFilter) {
            $query->where('experience_level', $this->experienceFilter);
        }

        if ($this->ratingFilter) {
            $query->where('rating', '>=', $this->ratingFilter);
        }

        if ($this->specializationFilter) {
            $query->whereJsonContains('specializations', $this->specializationFilter);
        }

        if ($this->priceRangeFilter) {
            [$min, $max] = explode('-', $this->priceRangeFilter);
            $query->whereBetween('hourly_rate', [$min, $max]);
        }

        $mentors = $query->orderBy('rating', 'desc')
            ->orderBy('total_reviews', 'desc')
            ->paginate(12);

        return view('livewire.mentorship.find-mentor', [
            'mentors' => $mentors
        ]);
    }
}