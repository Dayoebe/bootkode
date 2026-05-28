<?php
// FILE: app/Livewire/Mentorship/Partial/Profile.php

namespace App\Livewire\Mentorship\Partial;

use Livewire\Component;
use App\Models\Mentorship\MentorProfile;
use App\Models\Mentorship\MentorshipReview;
use Illuminate\Support\Facades\Auth;

class Profile extends Component
{
    public $profileId = null;
    public $bio = '';
    public $specializations = [];
    public $skills = [];
    public $experienceLevel = '';
    public $yearsExperience = 0;
    public $hourlyRate = 0;
    public $offersFreeSessions = false;
    public $maxMentees = 5;
    public $timezone = '';
    public $communicationPreferences = [];
    public $mentoringApproach = '';
    public $linkedinProfile = '';
    public $githubProfile = '';
    public $portfolioUrl = '';
    public $isAvailable = true;
    public $availabilitySlots = [];
    
    public $showProfileModal = false;

    protected $rules = [
        'bio' => 'required|string|min:100|max:2000',
        'specializations' => 'required|array|min:1|max:10',
        'experienceLevel' => 'required|in:junior,mid,senior,expert',
        'yearsExperience' => 'required|integer|min:0|max:50',
        'maxMentees' => 'required|integer|min:1|max:20',
        'timezone' => 'required|string',
        'mentoringApproach' => 'required|string|min:50|max:1000',
        'availabilitySlots' => 'nullable|array|max:21',
        'availabilitySlots.*.day' => 'required_with:availabilitySlots|string',
        'availabilitySlots.*.start' => 'required_with:availabilitySlots|date_format:H:i',
        'availabilitySlots.*.end' => 'required_with:availabilitySlots|date_format:H:i',
    ];

    public function mount()
    {
        $this->loadMentorProfile();
    }

    public function loadMentorProfile()
    {
        $profile = Auth::user()->mentorProfile;
        
        if ($profile) {
            $this->profileId = $profile->id;
            $this->bio = $profile->bio ?? '';
            $this->specializations = $profile->specializations ?? [''];
            $this->skills = $profile->skills ?? [''];
            $this->experienceLevel = $profile->experience_level;
            $this->yearsExperience = $profile->years_experience;
            $this->hourlyRate = $profile->hourly_rate;
            $this->offersFreeSessions = $profile->offers_free_sessions;
            $this->maxMentees = $profile->max_mentees;
            $this->timezone = $profile->timezone;
            $this->communicationPreferences = $profile->communication_preferences ?? [];
            $this->mentoringApproach = $profile->mentoring_approach ?? '';
            $this->linkedinProfile = $profile->linkedin_profile ?? '';
            $this->githubProfile = $profile->github_profile ?? '';
            $this->portfolioUrl = $profile->portfolio_url ?? '';
            $this->isAvailable = $profile->is_available;
            $this->availabilitySlots = $profile->normalizedAvailabilitySchedule() ?: $this->defaultAvailabilitySlots();
        } else {
            $this->initializeArrays();
        }
    }

    private function initializeArrays()
    {
        if (empty($this->specializations)) $this->specializations = [''];
        if (empty($this->skills)) $this->skills = [''];
        $this->timezone = $this->timezone ?: 'UTC';
        $this->experienceLevel = $this->experienceLevel ?: 'mid';
        $this->availabilitySlots = $this->availabilitySlots ?: $this->defaultAvailabilitySlots();
    }

    public function editProfile()
    {
        $this->showProfileModal = true;
    }

    public function saveProfile()
    {
        $this->validate();

        $profileData = [
            'bio' => $this->bio,
            'specializations' => array_filter($this->specializations),
            'skills' => array_filter($this->skills),
            'experience_level' => $this->experienceLevel,
            'years_experience' => $this->yearsExperience,
            'hourly_rate' => $this->hourlyRate,
            'offers_free_sessions' => $this->offersFreeSessions,
            'max_mentees' => $this->maxMentees,
            'timezone' => $this->timezone,
            'communication_preferences' => $this->communicationPreferences,
            'mentoring_approach' => $this->mentoringApproach,
            'linkedin_profile' => $this->linkedinProfile,
            'github_profile' => $this->githubProfile,
            'portfolio_url' => $this->portfolioUrl,
            'is_available' => $this->isAvailable,
            'availability_schedule' => $this->cleanAvailabilitySlots(),
        ];

        if ($this->profileId) {
            Auth::user()->mentorProfile->update($profileData);
            session()->flash('message', 'Profile updated successfully!');
        } else {
            $profileData['user_id'] = Auth::id();
            MentorProfile::create($profileData);
            session()->flash('message', 'Profile created successfully!');
        }

        $this->showProfileModal = false;
        $this->loadMentorProfile();
        $this->dispatch('profile-updated');
    }

    public function addSpecialization()
    {
        $this->specializations[] = '';
    }

    public function removeSpecialization($index)
    {
        unset($this->specializations[$index]);
        $this->specializations = array_values($this->specializations);
    }

    public function addSkill()
    {
        $this->skills[] = '';
    }

    public function removeSkill($index)
    {
        unset($this->skills[$index]);
        $this->skills = array_values($this->skills);
    }

    public function toggleCommunicationPreference($preference)
    {
        if (in_array($preference, $this->communicationPreferences)) {
            $this->communicationPreferences = array_diff($this->communicationPreferences, [$preference]);
        } else {
            $this->communicationPreferences[] = $preference;
        }
    }

    public function addAvailabilitySlot()
    {
        $this->availabilitySlots[] = [
            'day' => 'monday',
            'start' => '09:00',
            'end' => '17:00',
        ];
    }

    public function removeAvailabilitySlot($index)
    {
        unset($this->availabilitySlots[$index]);
        $this->availabilitySlots = array_values($this->availabilitySlots ?: []);
    }

    private function cleanAvailabilitySlots(): array
    {
        return collect($this->availabilitySlots)
            ->map(fn ($slot) => [
                'day' => strtolower((string) ($slot['day'] ?? '')),
                'start' => (string) ($slot['start'] ?? ''),
                'end' => (string) ($slot['end'] ?? ''),
            ])
            ->filter(fn ($slot) => $slot['day'] && $slot['start'] && $slot['end'] && $slot['start'] < $slot['end'])
            ->values()
            ->all();
    }

    private function defaultAvailabilitySlots(): array
    {
        return [
            ['day' => 'monday', 'start' => '09:00', 'end' => '17:00'],
            ['day' => 'tuesday', 'start' => '09:00', 'end' => '17:00'],
            ['day' => 'wednesday', 'start' => '09:00', 'end' => '17:00'],
            ['day' => 'thursday', 'start' => '09:00', 'end' => '17:00'],
            ['day' => 'friday', 'start' => '09:00', 'end' => '17:00'],
        ];
    }

    public function closeModal()
    {
        $this->showProfileModal = false;
    }

    public function render()
    {
        $recentReviews = MentorshipReview::with(['reviewer', 'mentorship'])
            ->where('reviewee_id', Auth::id())
            ->where('is_public', true)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('livewire.mentorship.partial.profile', [
            'recentReviews' => $recentReviews
        ]);
    }
}
