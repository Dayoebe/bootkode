<?php

namespace App\Livewire\Mentorship;

use Livewire\Component;
// use App\Models\MentorProfile;
use App\Models\MentorshipReview;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', [
    'title' => 'Mentor Profile', 
    'description' => 'Manage Your Mentor Profile', 
    'icon' => 'fas fa-user-edit', 
    'active' => 'mentorship'
])]
class MentorProfile extends Component
{
    public $profileId = null;
    public $bio = '';
    public $specializations = [''];
    public $skills = [''];
    public $experienceLevel = '';
    public $yearsExperience = 0;
    public $hourlyRate = 0;
    public $offersFreeSessions = false;
    public $maxMentees = 5;
    public $timezone = 'UTC';
    public $communicationPreferences = [];
    public $mentoringApproach = '';
    public $linkedinProfile = '';
    public $githubProfile = '';
    public $portfolioUrl = '';
    public $isAvailable = true;
    public $languages = [''];
    public $industries = [''];
    public $certifications = [''];
    
    public $showApplicationModal = false;
    public $applicationMessage = '';

    protected $rules = [
        'bio' => 'required|string|min:100|max:2000',
        'specializations' => 'required|array|min:1|max:10',
        'specializations.*' => 'required|string|max:100',
        'skills' => 'nullable|array|max:20',
        'skills.*' => 'nullable|string|max:100',
        'experienceLevel' => 'required|in:junior,mid,senior,expert',
        'yearsExperience' => 'required|integer|min:0|max:50',
        'maxMentees' => 'required|integer|min:1|max:20',
        'timezone' => 'required|string',
        'mentoringApproach' => 'required|string|min:50|max:1000',
        'hourlyRate' => 'nullable|numeric|min:0|max:1000',
    ];

    public function mount()
    {
        $this->checkMentorAccess();
        $this->loadMentorProfile();
    }

    private function checkMentorAccess()
    {
        $user = Auth::user();
        if (!$user->isMentor() && !$user->isAcademyAdmin() && !$user->isSuperAdmin()) {
            session()->flash('error', 'You need to be a mentor to access this page.');
            return redirect()->route('mentorship.hub');
        }
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
            $this->languages = $profile->languages ?? [''];
            $this->industries = $profile->industries ?? [''];
            $this->certifications = $profile->certifications ?? [''];
        } else {
            $this->initializeDefaults();
        }
    }

    private function initializeDefaults()
    {
        $this->specializations = [''];
        $this->skills = [''];
        $this->languages = [''];
        $this->industries = [''];
        $this->certifications = [''];
        $this->timezone = 'UTC';
        $this->experienceLevel = 'mid';
        $this->maxMentees = 5;
    }

    public function applyToBecomeMentor()
    {
        $this->showApplicationModal = true;
    }

    public function submitApplication()
    {
        $this->validate([
            'applicationMessage' => 'required|string|min:100|max:1000',
        ]);

        // Create pending mentor profile
        $profile = MentorProfile::create([
            'user_id' => Auth::id(),
            'bio' => $this->applicationMessage,
            'is_verified' => false,
            'is_available' => false,
            'specializations' => [],
            'skills' => [],
            'experience_level' => 'mid',
            'years_experience' => 0,
            'hourly_rate' => 0,
            'max_mentees' => 5,
            'timezone' => 'UTC',
        ]);

        // Notify admins
        $admins = \App\Models\User::whereIn('role', ['academy_admin', 'super_admin'])->get();
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\NewMentorApplication($profile));
        }

        $this->showApplicationModal = false;
        session()->flash('message', 'Your mentor application has been submitted! You will be notified once approved.');
        
        $this->loadMentorProfile();
    }

    public function saveProfile()
    {
        $this->validate();

        $profileData = [
            'bio' => $this->bio,
            'specializations' => array_filter($this->specializations),
            'skills' => array_filter($this->skills),
            'languages' => array_filter($this->languages),
            'industries' => array_filter($this->industries),
            'certifications' => array_filter($this->certifications),
            'experience_level' => $this->experienceLevel,
            'years_experience' => $this->yearsExperience,
            'hourly_rate' => $this->hourlyRate ?? 0,
            'offers_free_sessions' => $this->offersFreeSessions,
            'max_mentees' => $this->maxMentees,
            'timezone' => $this->timezone,
            'communication_preferences' => $this->communicationPreferences,
            'mentoring_approach' => $this->mentoringApproach,
            'linkedin_profile' => $this->linkedinProfile,
            'github_profile' => $this->githubProfile,
            'portfolio_url' => $this->portfolioUrl,
            'is_available' => $this->isAvailable
        ];

        if ($this->profileId) {
            Auth::user()->mentorProfile->update($profileData);
            session()->flash('message', 'Profile updated successfully!');
        } else {
            $profileData['user_id'] = Auth::id();
            MentorProfile::create($profileData);
            session()->flash('message', 'Profile created successfully!');
        }

        $this->loadMentorProfile();
    }

    public function toggleAvailability()
    {
        if ($this->profileId) {
            Auth::user()->mentorProfile->update(['is_available' => !$this->isAvailable]);
            $this->isAvailable = !$this->isAvailable;
            
            $status = $this->isAvailable ? 'available' : 'unavailable';
            session()->flash('message', "You are now {$status} for new mentorships.");
        }
    }

    // Array management methods
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

    public function addLanguage()
    {
        $this->languages[] = '';
    }

    public function removeLanguage($index)
    {
        unset($this->languages[$index]);
        $this->languages = array_values($this->languages);
    }

    public function addIndustry()
    {
        $this->industries[] = '';
    }

    public function removeIndustry($index)
    {
        unset($this->industries[$index]);
        $this->industries = array_values($this->industries);
    }

    public function addCertification()
    {
        $this->certifications[] = '';
    }

    public function removeCertification($index)
    {
        unset($this->certifications[$index]);
        $this->certifications = array_values($this->certifications);
    }

    public function toggleCommunicationPreference($preference)
    {
        if (in_array($preference, $this->communicationPreferences)) {
            $this->communicationPreferences = array_diff($this->communicationPreferences, [$preference]);
        } else {
            $this->communicationPreferences[] = $preference;
        }
    }

    public function closeModal()
    {
        $this->showApplicationModal = false;
        $this->applicationMessage = '';
    }

    public function render()
    {
        $recentReviews = [];
        
        if ($this->profileId) {
            $recentReviews = MentorshipReview::with(['reviewer', 'mentorship'])
                ->where('reviewee_id', Auth::id())
                ->where('is_public', true)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }

        return view('livewire.mentorship.mentor-profile', [
            'recentReviews' => $recentReviews
        ]);
    }
}