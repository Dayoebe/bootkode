<?php

namespace App\Livewire\Career;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\Career\JobCategory;
use App\Models\Career\JobPortal as Job;
use App\Services\JobApplicationService;
use App\Models\Career\JobApplication;
use App\Models\Career\JobSave;
use App\Models\Core\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', [
    'title' => 'Job Portal',
    'description' => 'Find your dream job and advance your career',
    'icon' => 'fas fa-search',
    'active' => 'jobs.portal'
])]

class JobPortal extends Component
{
    use WithFileUploads, WithPagination;

    // Search and Filter Properties
    public $searchTerm = '';
    public $filterCategory = '';
    public $filterLocation = '';
    public $filterEmploymentType = '';
    public $filterWorkType = '';
    public $filterExperienceLevel = '';
    public $filterSalaryMin = '';
    public $filterSalaryMax = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $showAdvancedFilters = false;

    // View Properties
    public $viewMode = 'list'; // list, grid
    public $showJobDetails = false;
    public $selectedJob = null;
    public $activeTab = 'browse'; // browse, applications, saved

    // Application Properties
    public $showApplicationModal = false;
    public $applicationJob = null;
    public $coverLetter = '';
    public $resume = null;
    public $customResponses = [];

    // Job Alerts
    public $showJobAlertModal = false;
    public $alertKeywords = '';
    public $alertLocation = '';
    public $alertCategory = '';

    // User's Data
    public $userApplications = [];
    public $savedJobs = [];
    public $recommendedJobs = [];
    public $jobStats = [];

    // Categories and Options
    public $categories = [];
    public $employmentTypes = [
        'full-time' => 'Full Time',
        'part-time' => 'Part Time',
        'contract' => 'Contract',
        'temporary' => 'Temporary',
        'internship' => 'Internship',
        'freelance' => 'Freelance'
    ];

    public $workTypes = [
        'on-site' => 'On-site',
        'remote' => 'Remote',
        'hybrid' => 'Hybrid'
    ];

    public $experienceLevels = [
        'entry' => 'Entry Level',
        'junior' => 'Junior Level',
        'mid' => 'Mid Level',
        'senior' => 'Senior Level',
        'executive' => 'Executive',
        'director' => 'Director'
    ];

    protected $rules = [
        'coverLetter' => 'required|string|min:50|max:2000',
        'resume' => 'nullable|file|mimes:pdf,doc,docx|max:5120', // 5MB
        'customResponses' => 'array|max:20',
        'customResponses.*' => 'nullable|string|max:2000',
    ];

    protected $messages = [
        'coverLetter.required' => 'Please provide a cover letter for your application.',
        'coverLetter.min' => 'Cover letter should be at least 50 characters long.',
        'coverLetter.max' => 'Cover letter should not exceed 2000 characters.',
        'resume.mimes' => 'Resume must be a PDF or Word document.',
        'resume.max' => 'Resume file size should not exceed 5MB.'
    ];

    public function mount()
    {
        $this->loadCategories();
        $this->loadUserData();
        $this->loadRecommendations();
        $this->loadJobStats();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, [
            'searchTerm', 'filterCategory', 'filterLocation', 'filterEmploymentType',
            'filterWorkType', 'filterExperienceLevel', 'filterSalaryMin', 'filterSalaryMax',
            'sortBy', 'sortDirection'
        ])) {
            $this->resetPage();
        }
    }

    public function loadCategories()
    {
        $this->categories = JobCategory::where('is_active', true)
            ->orderBy('sort_order')
            ->pluck('name', 'slug')
            ->toArray();
    }

    public function loadUserData()
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            $this->userApplications = JobApplication::where('user_id', $user->id)
                ->with(['job'])
                ->latest()
                ->limit(10)
                ->get();

            $this->savedJobs = JobSave::where('user_id', $user->id)
                ->with(['job'])
                ->latest()
                ->limit(10)
                ->get();
        }
    }

    public function loadRecommendations()
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Use the full model namespace instead of JobPortal (which refers to this Livewire component)
            $this->recommendedJobs = Job::active()
                ->when($user->resume_profile ?? false, function ($query) use ($user) {
                    $profile = $user->resume_profile;
                    $userSkills = collect($profile->skills ?? [])->pluck('name')->toArray();
                    
                    if (!empty($userSkills)) {
                        $query->whereJsonContains('skills_required', $userSkills[0] ?? '');
                    }
                })
                ->limit(6)
                ->get();
        } else {
            // Show popular jobs for guests
            $this->recommendedJobs = Job::active()
                ->orderBy('views_count', 'desc')
                ->limit(6)
                ->get();
        }
    }

    public function loadJobStats()
    {
        $this->jobStats = [
            'total_active' => Job::active()->count(),
            'new_this_week' => Job::active()->where('created_at', '>=', now()->subWeek())->count(),
            'remote_jobs' => Job::active()->where('work_type', 'remote')->count(),
            'premium_jobs' => Job::active()->where('is_premium', true)->count(),
        ];
    
        if (Auth::check()) {
            $user = Auth::user();
            $this->jobStats['user_applications'] = JobApplication::where('user_id', $user->id)->count();
            $this->jobStats['user_saved'] = JobSave::where('user_id', $user->id)->count();
        }
    }

    public function viewJob($jobId)
    {
        $this->selectedJob = Job::active()->with('postedBy:id,name')->findOrFail($jobId);
        $this->selectedJob->incrementViews();
        $this->showJobDetails = true;
    }
    public function closeJobDetails()
    {
        $this->showJobDetails = false;
        $this->selectedJob = null;
    }

    public function openApplicationModal($jobId)
    {
        if (!Auth::check()) {
            session()->flash('error', 'Please login to apply for jobs.');
            return redirect()->route('login');
        }
    
        $this->applicationJob = $this->availableApplicationJob($jobId);
        
        // Check if already applied
        $existingApplication = JobApplication::where('job_id', $jobId)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingApplication) {
            session()->flash('error', 'You have already applied for this job.');
            return;
        }

        // Load screening questions if any
        if ($this->applicationJob->screening_questions) {
            foreach ($this->applicationJob->screening_questions as $index => $question) {
                $this->customResponses[$index] = '';
            }
        }

        $this->showJobDetails = false;
        $this->selectedJob = null;
        $this->showApplicationModal = true;
    }

    public function closeApplicationModal()
    {
        $this->reset(['applicationJob', 'coverLetter', 'resume', 'customResponses']);
        $this->showApplicationModal = false;
    }

    protected function availableApplicationJob($id)
    {
        abort_unless(is_numeric($id), 404);

        return app(JobApplicationService::class)->availableJob((int) $id);
    }

    public function submitApplication()
    {
        abort_unless(Auth::check(), 403);
        $this->validate();
        // Always resolve availability again: a modal may be stale or client-controlled.
        $job = $this->availableApplicationJob($this->applicationJob?->id);
        try {
            app(JobApplicationService::class)->submit(Auth::user(), $job->id,
                $this->coverLetter, $this->customResponses, $this->resume);
            $this->closeApplicationModal();
            $this->loadUserData();
            $this->loadJobStats();
            session()->flash('message', 'Application received. You can track it in My Applications.');
        } catch (\Exception $e) {
            \Log::error('Job application persistence failed', ['exception' => get_class($e)]);
            session()->flash('error', 'The application could not be saved. Please try again.');
        }
    }

    public function toggleSaveJob($jobId)
    {
        if (!Auth::check()) {
            session()->flash('error', 'Please login to save jobs.');
            return;
        }

        Job::active()->findOrFail($jobId);
        $existingSave = JobSave::where('job_id', $jobId)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingSave) {
            $existingSave->delete();
            session()->flash('message', 'Job removed from saved jobs.');
        } else {
            JobSave::create([
                'job_id' => $jobId,
                'user_id' => Auth::id()
            ]);
            session()->flash('message', 'Job saved successfully!');
        }

        $this->loadUserData();
    }

    public function isJobSaved($jobId)
    {
        if (!Auth::check()) {
            return false;
        }

        return JobSave::where('job_id', $jobId)
            ->where('user_id', Auth::id())
            ->exists();
    }

    public function hasAppliedToJob($jobId)
    {
        if (!Auth::check()) {
            return false;
        }

        return JobApplication::where('job_id', $jobId)
            ->where('user_id', Auth::id())
            ->exists();
    }

    public function clearFilters()
    {
        $this->reset([
            'searchTerm', 'filterCategory', 'filterLocation', 'filterEmploymentType',
            'filterWorkType', 'filterExperienceLevel', 'filterSalaryMin', 'filterSalaryMax'
        ]);
        $this->resetPage();
    }

    public function createJobAlert()
    {
        if (!Auth::check()) {
            session()->flash('error', 'Please login to create job alerts.');
            return;
        }

        session()->flash('error', 'Job alerts are not available yet. Use the job search filters to find current openings.');
    }

    public function quickApply($jobId)
    {
        // Reuse the real application form so a cover letter and optional resume are explicit.
        return $this->openApplicationModal($jobId);
    }

    public function render()
    {
        $query = Job::active()->with(['postedBy:id,name']);


        // Apply search
        if ($this->searchTerm) {
            $query->search($this->searchTerm);
        }

        // Apply filters
        if ($this->filterCategory) {
            $query->byCategory($this->filterCategory);
        }

        if ($this->filterLocation) {
            $query->byLocation($this->filterLocation);
        }

        if ($this->filterEmploymentType) {
            $query->byEmploymentType($this->filterEmploymentType);
        }

        if ($this->filterWorkType) {
            $query->where('work_type', $this->filterWorkType);
        }

        if ($this->filterExperienceLevel) {
            $query->byExperience($this->filterExperienceLevel);
        }

        if ($this->filterSalaryMin || $this->filterSalaryMax) {
            $query->bySalaryRange($this->filterSalaryMin, $this->filterSalaryMax);
        }

        // Apply sorting
        if ($this->sortBy === 'salary') {
            $query->orderByRaw('(salary_min + salary_max) / 2 ' . $this->sortDirection);
        } elseif ($this->sortBy === 'relevance' && $this->searchTerm) {
            // Relevance sorting would need full-text search scoring
            $query->orderBy('created_at', 'desc');
        } else {
            $query->orderBy($this->sortBy, $this->sortDirection);
        }

        $jobs = $query->paginate(12);

        return view('livewire.career.job-portal', [
            'jobs' => $jobs
        ]);
    }
}