<?php

namespace App\Livewire\Mentorship;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\MentorProfile;
use App\Models\Mentorship;
use App\Models\MentorshipSession;
use App\Models\CodeReview;
use App\Models\MentorshipReview;
use App\Models\MentorApplication;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', ['title' => 'Mentor Dashboard', 'description' => 'Manage Your Mentorship Activities', 'icon' => 'fas fa-chalkboard-teacher', 'active' => 'mentor-dashboard'])]

class MentorDashboard extends Component
{
    use WithFileUploads, WithPagination;

    // Profile Management
    public $profileId = null;
    public $bio = '';
    public $specializations = [];
    public $skills = [];
    public $industries = [];
    public $languages = [];
    public $experienceLevel = '';
    public $yearsExperience = 0;
    public $hourlyRate = 0;
    public $offersFreeSessions = false;
    public $maxMentees = 5;
    public $availabilitySchedule = [];
    public $timezone = '';
    public $communicationPreferences = [];
    public $mentoringApproach = '';
    public $certifications = [];
    public $linkedinProfile = '';
    public $githubProfile = '';
    public $portfolioUrl = '';
    public $isAvailable = true;

    // Application Management
    public $motivation = '';
    public $experienceDetails = [];
    public $references = [];
    public $teachingPhilosophy = '';
    public $expectedMentees = 3;
    public $proposedHourlyRate = 0;
    public $additionalInfo = '';
    public $documents = [];

    // Dashboard Stats
    public $totalMentees = 0;
    public $activeMentees = 0;
    public $totalSessions = 0;
    public $completedSessions = 0;
    public $upcomingSessions = 0;
    public $pendingRequests = 0;
    public $pendingCodeReviews = 0;
    public $averageRating = 0;
    public $totalReviews = 0;
    public $monthlyEarnings = 0;
    public $totalEarnings = 0;

    // Collections
    public $mentorships = [];
    public $upcomingSessionsList = [];
    public $recentCodeReviews = [];
    public $recentReviews = [];
    public $pendingRequestsList = [];

    // UI State
    public $activeTab = 'overview';
    public $showProfileModal = false;
    public $showApplicationModal = false;
    public $viewMode = 'cards';
    public $dateFilter = 'this_week';
    public $statusFilter = '';

    protected $rules = [
        'bio' => 'required|string|min:100|max:2000',
        'specializations' => 'required|array|min:1|max:10',
        'experienceLevel' => 'required|in:junior,mid,senior,expert',
        'yearsExperience' => 'required|integer|min:0|max:50',
        'maxMentees' => 'required|integer|min:1|max:20',
        'hourlyRate' => 'nullable|numeric|min:0|max:500',
        'timezone' => 'required|string',
        'mentoringApproach' => 'required|string|min:50|max:1000',
        
        'motivation' => 'required|string|min:100|max:1500',
        'teachingPhilosophy' => 'required|string|min:100|max:1000',
        'expectedMentees' => 'required|integer|min:1|max:10'
    ];

    public function mount()
    {
        $this->loadMentorProfile();
        $this->loadDashboardStats();
        $this->loadDashboardData();
        $this->initializeArrays();
    }

    private function initializeArrays()
    {
        if (empty($this->specializations)) $this->specializations = [''];
        if (empty($this->skills)) $this->skills = [''];
        if (empty($this->industries)) $this->industries = [''];
        if (empty($this->languages)) $this->languages = ['English'];
        if (empty($this->communicationPreferences)) $this->communicationPreferences = [];
        if (empty($this->certifications)) $this->certifications = [''];
        if (empty($this->experienceDetails)) $this->experienceDetails = [''];
        if (empty($this->references)) $this->references = [''];
        
        $this->timezone = $this->timezone ?: 'UTC';
        $this->experienceLevel = $this->experienceLevel ?: 'mid';
    }

    public function loadMentorProfile()
    {
        $profile = Auth::user()->mentorProfile;
        
        if ($profile) {
            $this->profileId = $profile->id;
            $this->bio = $profile->bio;
            $this->specializations = $profile->specializations ?? [''];
            $this->skills = $profile->skills ?? [''];
            $this->industries = $profile->industries ?? [''];
            $this->languages = $profile->languages ?? ['English'];
            $this->experienceLevel = $profile->experience_level;
            $this->yearsExperience = $profile->years_experience;
            $this->hourlyRate = $profile->hourly_rate;
            $this->offersFreeSessions = $profile->offers_free_sessions;
            $this->maxMentees = $profile->max_mentees;
            $this->availabilitySchedule = $profile->availability_schedule ?? [];
            $this->timezone = $profile->timezone;
            $this->communicationPreferences = $profile->communication_preferences ?? [];
            $this->mentoringApproach = $profile->mentoring_approach;
            $this->certifications = $profile->certifications ?? [''];
            $this->linkedinProfile = $profile->linkedin_profile;
            $this->githubProfile = $profile->github_profile;
            $this->portfolioUrl = $profile->portfolio_url;
            $this->isAvailable = $profile->is_available;
        }
    }

    public function loadDashboardStats()
    {
        $user = Auth::user();
        $profile = $user->mentorProfile;

        if (!$profile) return;

        // Basic stats
        $this->totalMentees = $profile->total_mentees;
        $this->activeMentees = $profile->current_mentees;
        $this->totalSessions = $profile->total_sessions;
        $this->averageRating = $profile->rating;
        $this->totalReviews = $profile->total_reviews;

        // Active mentorships
        $this->pendingRequests = Mentorship::where('mentor_id', $user->id)
            ->where('status', Mentorship::STATUS_PENDING)
            ->count();

        // Sessions
        $this->completedSessions = MentorshipSession::whereHas('mentorship', function($q) use ($user) {
            $q->where('mentor_id', $user->id);
        })->where('status', MentorshipSession::STATUS_COMPLETED)->count();

        $this->upcomingSessions = MentorshipSession::whereHas('mentorship', function($q) use ($user) {
            $q->where('mentor_id', $user->id)->where('status', Mentorship::STATUS_ACTIVE);
        })->where('status', MentorshipSession::STATUS_SCHEDULED)
          ->where('scheduled_at', '>', now())
          ->count();

        // Code reviews
        $this->pendingCodeReviews = CodeReview::whereHas('mentorship', function($q) use ($user) {
            $q->where('mentor_id', $user->id);
        })->where('status', CodeReview::STATUS_PENDING)->count();

        // Earnings (if applicable)
        $this->calculateEarnings();
    }

    public function loadDashboardData()
    {
        $user = Auth::user();

        // Load mentorships
        $this->mentorships = Mentorship::with(['mentee', 'sessions' => function($q) {
            $q->where('scheduled_at', '>', now())->orderBy('scheduled_at')->limit(1);
        }])
        ->where('mentor_id', $user->id)
        ->when($this->statusFilter, fn($q) => $q->where('status', $this->statusFilter))
        ->orderBy('created_at', 'desc')
        ->get();

        // Load pending requests
        $this->pendingRequestsList = Mentorship::with('mentee')
            ->where('mentor_id', $user->id)
            ->where('status', Mentorship::STATUS_PENDING)
            ->orderBy('requested_at', 'desc')
            ->get();

        // Load upcoming sessions
        $this->upcomingSessionsList = MentorshipSession::with(['mentorship.mentee'])
            ->whereHas('mentorship', function($q) use ($user) {
                $q->where('mentor_id', $user->id);
            })
            ->where('status', MentorshipSession::STATUS_SCHEDULED)
            ->where('scheduled_at', '>', now())
            ->orderBy('scheduled_at')
            ->limit(10)
            ->get();

        // Load recent code reviews
        $this->recentCodeReviews = CodeReview::with(['mentorship.mentee', 'requester'])
            ->whereHas('mentorship', function($q) use ($user) {
                $q->where('mentor_id', $user->id);
            })
            ->orderBy('requested_at', 'desc')
            ->limit(5)
            ->get();

        // Load recent reviews
        $this->recentReviews = MentorshipReview::with(['reviewer', 'mentorship'])
            ->where('reviewee_id', $user->id)
            ->where('is_public', true)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    private function calculateEarnings()
    {
        $user = Auth::user();
        $thisMonth = now()->startOfMonth();

        // Monthly earnings
        $this->monthlyEarnings = MentorshipSession::whereHas('mentorship', function($q) use ($user) {
            $q->where('mentor_id', $user->id);
        })
        ->where('status', MentorshipSession::STATUS_COMPLETED)
        ->where('is_billable', true)
        ->where('completed_at', '>=', $thisMonth)
        ->sum('session_cost');

        // Total earnings
        $this->totalEarnings = MentorshipSession::whereHas('mentorship', function($q) use ($user) {
            $q->where('mentor_id', $user->id);
        })
        ->where('status', MentorshipSession::STATUS_COMPLETED)
        ->where('is_billable', true)
        ->sum('session_cost');
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        
        if ($tab === 'mentorships') {
            $this->loadDashboardData();
        }
    }

    // Profile Management
    public function editProfile()
    {
        $this->loadMentorProfile();
        $this->showProfileModal = true;
    }

    public function saveProfile()
    {
        $this->validate([
            'bio' => 'required|string|min:100|max:2000',
            'specializations' => 'required|array|min:1|max:10',
            'experienceLevel' => 'required|in:junior,mid,senior,expert',
            'yearsExperience' => 'required|integer|min:0|max:50',
            'maxMentees' => 'required|integer|min:1|max:20',
            'timezone' => 'required|string',
            'mentoringApproach' => 'required|string|min:50|max:1000'
        ]);

        $profileData = [
            'bio' => $this->bio,
            'specializations' => array_filter($this->specializations),
            'skills' => array_filter($this->skills),
            'industries' => array_filter($this->industries),
            'languages' => array_filter($this->languages),
            'experience_level' => $this->experienceLevel,
            'years_experience' => $this->yearsExperience,
            'hourly_rate' => $this->hourlyRate,
            'offers_free_sessions' => $this->offersFreeSessions,
            'max_mentees' => $this->maxMentees,
            'timezone' => $this->timezone,
            'communication_preferences' => $this->communicationPreferences,
            'mentoring_approach' => $this->mentoringApproach,
            'certifications' => array_filter($this->certifications),
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

        $this->showProfileModal = false;
        $this->loadMentorProfile();
        $this->loadDashboardStats();
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

    // Mentorship Management
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
        
        // Send notification
        $mentorship->mentee->notify(new \App\Notifications\MentorshipAccepted($mentorship));

        session()->flash('message', 'Mentorship request accepted!');
        
        $this->loadDashboardData();
        $this->loadDashboardStats();
    }

    public function rejectMentorship($mentorshipId, $reason = null)
    {
        $mentorship = Mentorship::find($mentorshipId);
        
        if (!$mentorship || $mentorship->mentor_id !== Auth::id()) {
            session()->flash('error', 'Invalid mentorship request.');
            return;
        }

        $mentorship->reject($reason);
        
        // Send notification
        $mentorship->mentee->notify(new \App\Notifications\MentorshipRejected($mentorship));

        session()->flash('message', 'Mentorship request rejected.');
        
        $this->loadDashboardData();
        $this->loadDashboardStats();
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
        
        $this->loadDashboardData();
        $this->loadDashboardStats();
    }

    // Application Management (for becoming a mentor)
    public function applyToBecomeMentor()
    {
        if (Auth::user()->mentorProfile) {
            session()->flash('error', 'You already have a mentor profile.');
            return;
        }

        $this->showApplicationModal = true;
    }

    public function submitMentorApplication()
    {
        $this->validate([
            'motivation' => 'required|string|min:100|max:1500',
            'teachingPhilosophy' => 'required|string|min:100|max:1000',
            'expectedMentees' => 'required|integer|min:1|max:10'
        ]);

        $applicationData = [
            'user_id' => Auth::id(),
            'status' => 'pending',
            'motivation' => $this->motivation,
            'experience_details' => array_filter($this->experienceDetails),
            'specializations' => array_filter($this->specializations),
            'linkedin_profile' => $this->linkedinProfile,
            'github_profile' => $this->githubProfile,
            'certifications' => array_filter($this->certifications),
            'references' => array_filter($this->references),
            'teaching_philosophy' => $this->teachingPhilosophy,
            'expected_mentees' => $this->expectedMentees,
            'proposed_hourly_rate' => $this->proposedHourlyRate,
            'additional_info' => $this->additionalInfo,
            'submitted_at' => now()
        ];

        // Handle document uploads
        if ($this->documents) {
            $documentPaths = [];
            foreach ($this->documents as $document) {
                $path = $document->store('mentor-applications/' . Auth::id(), 'private');
                $documentPaths[] = [
                    'name' => $document->getClientOriginalName(),
                    'path' => $path,
                    'size' => $document->getSize(),
                    'type' => $document->getMimeType()
                ];
            }
            $applicationData['documents'] = $documentPaths;
        }

        MentorApplication::create($applicationData);

        // Send notification to admins
        $admins = \App\Models\User::whereHas('roles', function($q) {
            $q->whereIn('name', ['academy_admin', 'super_admin']);
        })->get();

        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\NewMentorApplication(Auth::user()));
        }

        session()->flash('message', 'Your mentor application has been submitted successfully! We will review it and get back to you soon.');
        
        $this->showApplicationModal = false;
        $this->resetApplicationForm();
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

    public function addExperienceDetail()
    {
        $this->experienceDetails[] = '';
    }

    public function removeExperienceDetail($index)
    {
        unset($this->experienceDetails[$index]);
        $this->experienceDetails = array_values($this->experienceDetails);
    }

    public function addReference()
    {
        $this->references[] = '';
    }

    public function removeReference($index)
    {
        unset($this->references[$index]);
        $this->references = array_values($this->references);
    }

    public function toggleCommunicationPreference($preference)
    {
        if (in_array($preference, $this->communicationPreferences)) {
            $this->communicationPreferences = array_diff($this->communicationPreferences, [$preference]);
        } else {
            $this->communicationPreferences[] = $preference;
        }
    }

    public function resetApplicationForm()
    {
        $this->reset([
            'motivation', 'experienceDetails', 'references', 'teachingPhilosophy',
            'expectedMentees', 'proposedHourlyRate', 'additionalInfo', 'documents'
        ]);
        
        $this->experienceDetails = [''];
        $this->references = [''];
        $this->expectedMentees = 3;
    }

    public function closeModal()
    {
        $this->showProfileModal = false;
        $this->showApplicationModal = false;
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'statusFilter') {
            $this->loadDashboardData();
        }
    }

    // Analytics and Reports
    public function getPerformanceMetrics()
    {
        $user = Auth::user();
        $startDate = match($this->dateFilter) {
            'this_week' => now()->startOfWeek(),
            'this_month' => now()->startOfMonth(),
            'this_quarter' => now()->startOfQuarter(),
            'this_year' => now()->startOfYear(),
            default => now()->startOfMonth()
        };

        return [
            'sessions_conducted' => MentorshipSession::whereHas('mentorship', function($q) use ($user) {
                $q->where('mentor_id', $user->id);
            })
            ->where('status', MentorshipSession::STATUS_COMPLETED)
            ->where('completed_at', '>=', $startDate)
            ->count(),

            'average_session_rating' => MentorshipSession::whereHas('mentorship', function($q) use ($user) {
                $q->where('mentor_id', $user->id);
            })
            ->where('status', MentorshipSession::STATUS_COMPLETED)
            ->where('completed_at', '>=', $startDate)
            ->whereNotNull('mentee_rating')
            ->avg('mentee_rating'),

            'code_reviews_completed' => CodeReview::whereHas('mentorship', function($q) use ($user) {
                $q->where('mentor_id', $user->id);
            })
            ->where('status', CodeReview::STATUS_COMPLETED)
            ->where('completed_at', '>=', $startDate)
            ->count(),

            'response_time_hours' => $this->calculateAverageResponseTime($startDate),
        ];
    }

    private function calculateAverageResponseTime($startDate)
    {
        // Calculate average time between request and start of review/session
        $codeReviews = CodeReview::whereHas('mentorship', function($q) {
            $q->where('mentor_id', Auth::id());
        })
        ->whereNotNull('started_review_at')
        ->where('requested_at', '>=', $startDate)
        ->get();

        if ($codeReviews->isEmpty()) return 0;

        $totalHours = $codeReviews->sum(function($review) {
            return $review->requested_at->diffInHours($review->started_review_at);
        });

        return round($totalHours / $codeReviews->count(), 1);
    }

    public function render()
    {
        $performanceMetrics = $this->getPerformanceMetrics();
        
        return view('livewire.mentorship.mentor-dashboard', compact('performanceMetrics'));
    }
}